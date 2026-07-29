<?php
// AI Abilities — execute callbacks for the animals CPT.

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_can_manage_animals() {
    return current_user_can('edit_animals') || current_user_can('edit_posts');
}

function tsvd_tools_ai_format_animal($post_id) {
    $name = get_post_meta($post_id, 'animal_name', true);
    $breeds = get_the_terms($post_id, 'animal_breed');
    $breed_names = ($breeds && !is_wp_error($breeds)) ? wp_list_pluck($breeds, 'name') : array();
    $companions = get_post_meta($post_id, 'animal_companion_animals', true);
    $images = get_post_meta($post_id, 'animal_images', true);

    return array(
        'id'              => (int) $post_id,
        'title'           => get_the_title($post_id),
        'status'          => get_post_status($post_id),
        'name'            => $name !== '' ? $name : get_the_title($post_id),
        'adoption_status' => (string) get_post_meta($post_id, 'animal_adoption_status', true),
        'mediator'        => get_post_meta($post_id, 'animal_mediator', true) ?: null,
        'missing_status'  => get_post_meta($post_id, 'animal_missing_status', true) ?: null,
        'edit_link'       => (string) get_edit_post_link($post_id, 'raw'),
        'post_date'       => get_the_date('Y-m-d H:i:s', $post_id),
        'teo_id'          => get_post_meta($post_id, 'animal_teo_id', true) ?: null,
        'gender'          => get_post_meta($post_id, 'animal_gender', true) ?: null,
        'size'            => get_post_meta($post_id, 'animal_size_cm', true) ?: null,
        'life_stage'      => get_post_meta($post_id, 'animal_life_stage', true) ?: null,
        'castrated'       => get_post_meta($post_id, 'animal_castrated', true) ?: null,
        'birthday'        => get_post_meta($post_id, 'animal_birthday', true) ?: null,
        'birthday_status' => get_post_meta($post_id, 'animal_birthday_status', true) ?: null,
        'breed'           => $breed_names,
        'companion_ids'   => is_array($companions) ? array_map('intval', $companions) : array(),
        'image_count'     => is_array($images) ? count($images) : 0,
    );
}

function tsvd_tools_ai_build_animal_meta_query($input) {
    $meta_query = array();
    if (!empty($input['adoption_status'])) {
        $meta_query[] = array('key' => 'animal_adoption_status', 'value' => sanitize_text_field($input['adoption_status']));
    }
    if (!empty($input['mediator'])) {
        $meta_query[] = array('key' => 'animal_mediator', 'value' => sanitize_text_field($input['mediator']));
    }
    if (!empty($input['missing_status'])) {
        $meta_query[] = array('key' => 'animal_missing_status', 'value' => sanitize_text_field($input['missing_status']));
    }
    return $meta_query;
}

function tsvd_tools_ai_list_animals($input) {
    $per_page = isset($input['per_page']) ? max(1, min(100, (int) $input['per_page'])) : 20;
    $args = array(
        'post_type'      => 'animals',
        'post_status'    => array('publish', 'draft', 'pending'),
        'posts_per_page' => $per_page,
    );
    $meta_query = tsvd_tools_ai_build_animal_meta_query($input);
    if (!empty($meta_query)) {
        $args['meta_query'] = $meta_query;
    }
    if (!empty($input['search'])) {
        $args['s'] = sanitize_text_field($input['search']);
    }

    $query = new WP_Query($args);
    $animals = array();
    foreach ($query->posts as $post) {
        $animals[] = tsvd_tools_ai_format_animal($post->ID);
    }

    return array(
        'animals' => $animals,
        'total'   => (int) $query->found_posts,
    );
}

function tsvd_tools_ai_get_animal($input) {
    $id = isset($input['id']) ? (int) $input['id'] : 0;
    if (!$id || get_post_type($id) !== 'animals') {
        return new WP_Error('tsvd_tools_ai_not_found', __('Tier nicht gefunden.', 'tsv-tools'));
    }
    return tsvd_tools_ai_format_animal($id);
}

function tsvd_tools_ai_apply_animal_meta($post_id, $input) {
    if (isset($input['name'])) {
        update_post_meta($post_id, 'animal_name', sanitize_text_field($input['name']));
    }
    if (isset($input['adoption_status'])) {
        update_post_meta($post_id, 'animal_adoption_status', sanitize_text_field($input['adoption_status']));
    }
    if (isset($input['mediator'])) {
        update_post_meta($post_id, 'animal_mediator', sanitize_text_field($input['mediator']));
    }
    if (isset($input['missing_status'])) {
        $missing = sanitize_text_field($input['missing_status']);
        if (in_array($missing, array('missing', 'found', 'reunited'), true)) {
            update_post_meta($post_id, 'animal_missing_status', $missing);
        }
    }

    $missing_meta = array(
        'last_seen_date'     => 'animal_missing_last_seen_date',
        'last_seen_location' => 'animal_missing_last_seen_location',
        'chip_number'        => 'animal_missing_chip_number',
        'reward'             => 'animal_missing_reward',
    );
    foreach ($missing_meta as $key => $meta_key) {
        if (isset($input[$key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($input[$key]));
        }
    }

    if (isset($input['gender'])) {
        update_post_meta($post_id, 'animal_gender', tsvd_tools_ai_normalize_option($input['gender'], array(
            'weiblich' => 'female', 'männlich' => 'male', 'maennlich' => 'male', 'unbekannt' => 'unknown',
        )));
    }
    if (isset($input['size'])) {
        update_post_meta($post_id, 'animal_size_cm', tsvd_tools_ai_normalize_option($input['size'], array(
            'klein' => 'small', 'mittel' => 'middle', 'groß' => 'large', 'gross' => 'large',
        )));
    }

    tsvd_tools_ai_assign_breed($post_id, $input['species'] ?? '', $input['breed'] ?? '');
    if (isset($input['color']) && $input['color'] !== '') {
        $color_id = tsvd_tools_ai_ensure_term($input['color'], 'animal_color', 0);
        if ($color_id) {
            wp_set_post_terms($post_id, array($color_id), 'animal_color', false);
        }
    }

    if (isset($input['description'])) {
        update_post_meta($post_id, 'animal_description', sanitize_textarea_field($input['description']));
    }

    $contact_meta = array(
        'contact_firstname' => 'animal_private_contact_firstname',
        'contact_lastname'  => 'animal_private_contact_lastname',
        'contact_address'   => 'animal_private_contact_address',
    );
    foreach ($contact_meta as $key => $meta_key) {
        if (isset($input[$key])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($input[$key]));
        }
    }
    if (isset($input['contact_email'])) {
        update_post_meta($post_id, 'animal_private_contact_email', sanitize_email($input['contact_email']));
    }

    if (isset($input['image_url']) && $input['image_url'] !== '') {
        tsvd_tools_ai_set_animal_image($post_id, $input['image_url']);
    }
}

function tsvd_tools_ai_normalize_option($value, array $map) {
    $value = sanitize_text_field($value);
    $key = mb_strtolower(trim($value));
    return isset($map[$key]) ? $map[$key] : $value;
}

function tsvd_tools_ai_ensure_term($name, $taxonomy, $parent = 0) {
    $name = sanitize_text_field($name);
    if ($name === '') {
        return 0;
    }
    $term = get_term_by('name', $name, $taxonomy);
    if (! $term) {
        $term = get_term_by('slug', sanitize_title($name), $taxonomy);
    }
    if ($term) {
        return (int) $term->term_id;
    }
    $created = wp_insert_term($name, $taxonomy, array('parent' => (int) $parent));
    if (is_wp_error($created)) {
        return 0;
    }
    return (int) $created['term_id'];
}

function tsvd_tools_ai_assign_breed($post_id, $species, $breed) {
    $species = sanitize_text_field($species);
    $breed = sanitize_text_field($breed);
    if ($species === '' && $breed === '') {
        return;
    }
    $term_ids = array();
    $parent_id = 0;
    if ($species !== '') {
        $parent_id = tsvd_tools_ai_ensure_term($species, 'animal_breed', 0);
        if ($parent_id) {
            $term_ids[] = $parent_id;
        }
    }
    if ($breed !== '') {
        $breed_id = tsvd_tools_ai_ensure_term($breed, 'animal_breed', $parent_id);
        if ($breed_id) {
            $term_ids[] = $breed_id;
        }
    }
    if ($term_ids) {
        wp_set_post_terms($post_id, $term_ids, 'animal_breed', false);
    }
}

function tsvd_tools_ai_set_animal_image($post_id, $url) {
    $url = esc_url_raw($url);
    if (! $url) {
        return;
    }
    $parts = wp_parse_url($url);
    if (empty($parts['scheme']) || ! in_array($parts['scheme'], array('http', 'https'), true)) {
        return;
    }
    $host = isset($parts['host']) ? $parts['host'] : '';
    $ip   = $host ? gethostbyname($host) : '';
    if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return;
    }
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attachment_id = media_sideload_image($url, $post_id, null, 'id');
    if (! is_wp_error($attachment_id)) {
        set_post_thumbnail($post_id, (int) $attachment_id);
    }
}

function tsvd_tools_ai_create_animal($input) {
    if (empty($input['name'])) {
        return new WP_Error('tsvd_tools_ai_missing_name', __('Name ist erforderlich.', 'tsv-tools'));
    }
    $post_id = wp_insert_post(array(
        'post_type'    => 'animals',
        'post_status'  => 'draft',
        'post_title'   => sanitize_text_field($input['name']),
        'post_content' => isset($input['description']) ? wp_kses_post($input['description']) : '',
    ), true);

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    if (!isset($input['adoption_status'])) {
        $input['adoption_status'] = 'not_for_adoption';
    }
    tsvd_tools_ai_apply_animal_meta($post_id, $input);

    if (function_exists('tsvd_regenerate_animal_title')) {
        tsvd_regenerate_animal_title($post_id, sanitize_text_field($input['name']));
    }

    return tsvd_tools_ai_format_animal($post_id);
}

function tsvd_tools_ai_create_animals_bulk($input) {
    $items = isset($input['animals']) && is_array($input['animals']) ? $input['animals'] : array();
    if (empty($items)) {
        return new WP_Error('tsvd_tools_ai_no_animals', __('Keine Tiere übergeben.', 'tsv-tools'));
    }
    if (count($items) > 200) {
        return new WP_Error('tsvd_tools_ai_too_many', __('Maximal 200 Tiere pro Aufruf.', 'tsv-tools'));
    }
    $created = array();
    $errors = array();
    foreach ($items as $i => $item) {
        if (! is_array($item)) {
            $errors[] = array('index' => (int) $i, 'message' => 'Ungültiger Eintrag.');
            continue;
        }
        $res = tsvd_tools_ai_create_animal($item);
        if (is_wp_error($res)) {
            $errors[] = array('index' => (int) $i, 'name' => isset($item['name']) ? (string) $item['name'] : '', 'message' => $res->get_error_message());
        } else {
            $created[] = array('id' => (int) $res['id'], 'name' => (string) $res['name']);
        }
    }
    return array('count' => count($created), 'created' => $created, 'errors' => $errors);
}

function tsvd_tools_ai_update_animal($input) {
    $id = isset($input['id']) ? (int) $input['id'] : 0;
    if (!$id || get_post_type($id) !== 'animals') {
        return new WP_Error('tsvd_tools_ai_not_found', __('Tier nicht gefunden.', 'tsv-tools'));
    }

    $postarr = array('ID' => $id);
    if (isset($input['name'])) {
        $postarr['post_title'] = sanitize_text_field($input['name']);
    }
    if (isset($input['description'])) {
        $postarr['post_content'] = wp_kses_post($input['description']);
    }
    if (count($postarr) > 1) {
        $result = wp_update_post($postarr, true);
        if (is_wp_error($result)) {
            return $result;
        }
    }

    tsvd_tools_ai_apply_animal_meta($id, $input);

    return tsvd_tools_ai_format_animal($id);
}

function tsvd_tools_ai_can_manage_settings() {
    return current_user_can('manage_options');
}

function tsvd_tools_ai_missing_form_group_id() {
    return 'vermisst';
}

function tsvd_tools_ai_missing_form_groups() {
    return array(
        array('id' => tsvd_tools_ai_missing_form_group_id(), 'columns' => 1, 'aligns' => array()),
    );
}

function tsvd_tools_ai_missing_form_fields() {
    $fields = array(
        array('id' => 'animal_name', 'type' => 'animal_name', 'label' => __('Name des Tieres', 'tsv-tools'), 'required' => true),
        array('id' => 'animal_breed', 'type' => 'animal_breed', 'label' => __('Tierart / Rasse', 'tsv-tools'), 'required' => true),
        array('id' => 'animal_missing_last_seen_date', 'type' => 'animal_missing_last_seen_date', 'label' => __('Zuletzt gesehen (Datum)', 'tsv-tools'), 'required' => true),
        array('id' => 'animal_missing_last_seen_location', 'type' => 'animal_missing_last_seen_location', 'label' => __('Zuletzt gesehen (Ort)', 'tsv-tools'), 'required' => true),
        array('id' => 'animal_missing_chip_number', 'type' => 'animal_missing_chip_number', 'label' => __('Chipnummer', 'tsv-tools'), 'required' => false),
        array('id' => 'animal_missing_reward', 'type' => 'animal_missing_reward', 'label' => __('Belohnung', 'tsv-tools'), 'required' => false),
        array('id' => 'animal_profile_image', 'type' => 'animal_profile_image', 'label' => __('Foto', 'tsv-tools'), 'required' => false),
        array('id' => 'animal_description', 'type' => 'animal_description', 'label' => __('Beschreibung', 'tsv-tools'), 'required' => false),
        array('id' => 'melder_vorname', 'type' => 'animal_private_contact_firstname', 'label' => __('Dein Vorname', 'tsv-tools'), 'required' => true),
        array('id' => 'melder_nachname', 'type' => 'animal_private_contact_lastname', 'label' => __('Dein Nachname', 'tsv-tools'), 'required' => true),
        array('id' => 'melder_email', 'type' => 'animal_private_contact_email', 'label' => __('Deine E-Mail', 'tsv-tools'), 'required' => true),
        array('id' => 'melder_anschrift', 'type' => 'animal_private_contact_address', 'label' => __('Deine Anschrift', 'tsv-tools'), 'required' => false),
    );

    $group_id = tsvd_tools_ai_missing_form_group_id();
    foreach ($fields as &$field) {
        $field['group_id'] = $group_id;
        $field['group_column'] = 0;
    }
    unset($field);

    return $fields;
}

function tsvd_tools_ai_write_missing_form_fields($form_id) {
    update_post_meta($form_id, '_tsvd_form_fields', tsvd_tools_ai_missing_form_fields());
    update_post_meta($form_id, '_tsvd_form_groups_config', tsvd_tools_ai_missing_form_groups());
}

function tsvd_tools_ai_create_missing_form($input) {
    $force = ! empty($input['force']);
    $existing = (int) get_option('tsvd_missing_animals_form', 0);
    if ($existing && get_post_type($existing) === 'tsvd_form' && ! $force) {
        tsvd_tools_ai_write_missing_form_fields($existing);
        update_option('tsvd_enable_missing_animals', 1);
        return array(
            'created'        => false,
            'repaired'       => true,
            'form_id'        => $existing,
            'form_edit_link' => (string) get_edit_post_link($existing, 'raw'),
            'page_id'        => (int) get_option('tsvd_missing_animals_page', 0),
            'page_url'       => '',
            'message'        => __('Formular bestand bereits; Felder und Gruppierung wurden neu geschrieben.', 'tsv-tools'),
        );
    }

    $title = isset($input['title']) && $input['title'] !== '' ? sanitize_text_field($input['title']) : __('Vermisstes Tier melden', 'tsv-tools');
    $form_id = wp_insert_post(array(
        'post_type'   => 'tsvd_form',
        'post_status' => 'publish',
        'post_title'  => $title,
    ), true);
    if (is_wp_error($form_id)) {
        return $form_id;
    }

    tsvd_tools_ai_write_missing_form_fields($form_id);
    $recipient = isset($input['recipient_email']) && is_email($input['recipient_email']) ? sanitize_email($input['recipient_email']) : get_option('admin_email');
    update_post_meta($form_id, '_tsvd_form_recipient', $recipient);
    update_post_meta($form_id, '_tsvd_form_subject', __('Neue Meldung: Vermisstes Tier', 'tsv-tools'));
    update_post_meta($form_id, '_tsvd_form_success_message', __('Danke, deine Meldung wurde übermittelt und wird geprüft.', 'tsv-tools'));
    update_post_meta($form_id, '_tsvd_form_show_title', '1');

    update_option('tsvd_enable_missing_animals', 1);
    update_option('tsvd_missing_animals_form', $form_id);

    $page_id = (int) get_option('tsvd_missing_animals_page', 0);
    $create_page = ! isset($input['create_page']) || ! empty($input['create_page']);
    if ($create_page && (! $page_id || get_post_type($page_id) !== 'page')) {
        $new_page = wp_insert_post(array(
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_title'   => __('Vermisste Tiere', 'tsv-tools'),
            'post_name'    => 'vermisste-tiere',
            'post_content' => '[missing_animals]',
        ), true);
        if (! is_wp_error($new_page)) {
            $page_id = (int) $new_page;
            update_option('tsvd_missing_animals_page', $page_id);
        }
    }

    flush_rewrite_rules();

    return array(
        'created'        => true,
        'form_id'        => (int) $form_id,
        'form_edit_link' => (string) get_edit_post_link($form_id, 'raw'),
        'page_id'        => (int) $page_id,
        'page_url'       => $page_id ? (string) get_permalink($page_id) : '',
        'message'        => __('Vermisst-Formular angelegt und in den Einstellungen verknüpft.', 'tsv-tools'),
    );
}

function tsvd_tools_ai_set_missing_page($input) {
    $page_id = isset($input['page_id']) ? (int) $input['page_id'] : 0;
    if (! $page_id || get_post_type($page_id) !== 'page' || get_post_status($page_id) === false) {
        return new WP_Error('invalid_page', __('Keine gültige Seite (Post-Type page) mit dieser ID gefunden.', 'tsv-tools'));
    }

    $ensure_content = ! isset($input['ensure_content']) || ! empty($input['ensure_content']);
    if ($ensure_content && ! has_shortcode((string) get_post_field('post_content', $page_id), 'missing_animals')) {
        $updated = wp_update_post(array(
            'ID'           => $page_id,
            'post_content' => "<!-- wp:shortcode -->[missing_animals]<!-- /wp:shortcode -->",
        ), true);
        if (is_wp_error($updated)) {
            return $updated;
        }
    }

    update_option('tsvd_missing_animals_page', $page_id);
    update_option('tsvd_enable_missing_animals', 1);
    flush_rewrite_rules();

    return array(
        'ok'       => true,
        'page_id'  => $page_id,
        'page_url' => (string) get_permalink($page_id),
        'message'  => __('Vermisst-Seite in den Einstellungen verknüpft und Rewrite-Regeln aktualisiert.', 'tsv-tools'),
    );
}

function tsvd_tools_ai_set_animal_interest_seed($input) {
    $items = isset($input['items']) && is_array($input['items']) ? $input['items'] : array();
    $results = array();
    $updated = 0;

    foreach ($items as $item) {
        $id   = isset($item['id']) ? (int) $item['id'] : 0;
        $seed = isset($item['seed']) ? max(0, (int) $item['seed']) : 0;

        if (! $id || get_post_type($id) !== 'animals') {
            $results[] = array('id' => $id, 'ok' => false, 'error' => 'invalid_animal');
            continue;
        }

        if (function_exists('tsvd_interest_set_seed')) {
            $total = tsvd_interest_set_seed($id, $seed);
        } else {
            update_post_meta($id, 'animal_interest_seed', $seed);
            $form  = (int) get_post_meta($id, 'animal_interest_form_count', true);
            $total = $seed + $form;
            update_post_meta($id, 'animal_interest_total', $total);
        }

        $updated++;
        $results[] = array('id' => $id, 'ok' => true, 'seed' => $seed, 'total' => (int) $total);
    }

    return array('updated' => $updated, 'results' => $results);
}

function tsvd_tools_ai_applicant_field_defs($target) {
    $all = array(
        'applicant_residence' => array('label' => __('Wohnort', 'tsv-tools'), 'required' => true),
        'applicant_housing'   => array('label' => __('Unterkunft', 'tsv-tools'), 'required' => true),
        'applicant_outdoor'   => array('label' => __('Außenbereich', 'tsv-tools'), 'required' => true),
    );
    if ('missing' === $target) {
        return array('applicant_residence' => $all['applicant_residence']);
    }
    return $all;
}

function tsvd_tools_ai_add_applicant_fields($input) {
    $target = isset($input['target']) ? sanitize_key($input['target']) : '';
    $map    = array(
        'interest' => 'tsvd_animal_interest_form',
        'private'  => 'tsvd_private_adoption_form',
        'missing'  => 'tsvd_missing_animals_form',
    );

    $form_id = isset($input['form_id']) ? (int) $input['form_id'] : 0;
    if (! $form_id && isset($map[$target])) {
        $form_id = (int) get_option($map[$target], 0);
    }
    if (! $form_id || get_post_type($form_id) !== 'tsvd_form') {
        return new WP_Error('invalid_form', __('Kein gültiges Formular gefunden (target oder form_id prüfen).', 'tsv-tools'));
    }

    $fields = get_post_meta($form_id, '_tsvd_form_fields', true);
    if (! is_array($fields)) {
        $fields = array();
    }
    $groups = get_post_meta($form_id, '_tsvd_form_groups_config', true);
    if (! is_array($groups)) {
        $groups = array();
    }

    $group_id  = 'bewerber';
    $has_group = false;
    foreach ($groups as $group) {
        if (isset($group['id']) && $group['id'] === $group_id) {
            $has_group = true;
            break;
        }
    }
    if (! $has_group) {
        $groups[] = array('id' => $group_id, 'columns' => 1, 'aligns' => array());
    }

    $index_by_type = array();
    foreach ($fields as $i => $field) {
        if (isset($field['type'])) {
            $index_by_type[$field['type']] = $i;
        }
    }

    $added   = array();
    $updated = array();
    foreach (tsvd_tools_ai_applicant_field_defs($target) as $type => $def) {
        if (isset($index_by_type[$type])) {
            $i = $index_by_type[$type];
            $fields[$i]['label']    = $def['label'];
            $fields[$i]['required'] = (bool) $def['required'];
            if (empty($fields[$i]['group_id'])) {
                $fields[$i]['group_id'] = $group_id;
            }
            if (! isset($fields[$i]['group_column'])) {
                $fields[$i]['group_column'] = 0;
            }
            $updated[] = $type;
            continue;
        }
        $fields[] = array(
            'id'           => $type,
            'type'         => $type,
            'label'        => $def['label'],
            'required'     => (bool) $def['required'],
            'options'      => array(),
            'group_id'     => $group_id,
            'group_column' => 0,
        );
        $added[] = $type;
    }

    // Consent/acceptance must render last. The renderer orders by group (first
    // appearance of group_id in the field array), so isolate consent in its own
    // group placed at the very end.
    $consent_types = array('description', 'checkbox');
    $consent_group = 'zustimmung';
    $head = array();
    $tail = array();
    foreach ($fields as $field) {
        if (isset($field['type']) && in_array($field['type'], $consent_types, true)) {
            $field['group_id']     = $consent_group;
            $field['group_column'] = 0;
            $tail[] = $field;
        } else {
            $head[] = $field;
        }
    }
    $fields = array_merge($head, $tail);

    if (! empty($tail)) {
        $has_consent_group = false;
        foreach ($groups as $group) {
            if (isset($group['id']) && $group['id'] === $consent_group) {
                $has_consent_group = true;
                break;
            }
        }
        if (! $has_consent_group) {
            $groups[] = array('id' => $consent_group, 'columns' => 1, 'aligns' => array());
        }
    }

    update_post_meta($form_id, '_tsvd_form_fields', $fields);
    update_post_meta($form_id, '_tsvd_form_groups_config', $groups);

    return array(
        'form_id'        => $form_id,
        'added'          => $added,
        'updated'        => $updated,
        'order'          => array_values(wp_list_pluck($fields, 'type')),
        'form_edit_link' => (string) get_edit_post_link($form_id, 'raw'),
    );
}

function tsvd_tools_ai_resolve_page($input) {
    $page_id = isset($input['page_id']) ? (int) $input['page_id'] : 0;
    if (! $page_id && ! empty($input['slug'])) {
        $page = get_page_by_path(sanitize_title($input['slug']), OBJECT, 'page');
        if ($page) {
            $page_id = (int) $page->ID;
        }
    }
    if ($page_id && get_post_type($page_id) === 'page') {
        return $page_id;
    }
    return 0;
}

function tsvd_tools_ai_get_page($input) {
    $page_id = tsvd_tools_ai_resolve_page($input);
    if (! $page_id) {
        return new WP_Error('page_not_found', __('Keine Seite (Post-Type page) gefunden (page_id oder slug prüfen).', 'tsv-tools'));
    }
    $post = get_post($page_id);
    return array(
        'id'        => (int) $post->ID,
        'title'     => (string) $post->post_title,
        'slug'      => (string) $post->post_name,
        'status'    => (string) $post->post_status,
        'content'   => (string) $post->post_content,
        'url'       => (string) get_permalink($post),
        'edit_link' => (string) get_edit_post_link($post->ID, 'raw'),
    );
}

function tsvd_tools_ai_update_page($input) {
    $content = isset($input['content']) ? (string) $input['content'] : '';
    if (! current_user_can('unfiltered_html')) {
        $content = wp_kses_post($content);
    }
    $mode    = isset($input['mode']) ? sanitize_key($input['mode']) : 'append';
    $page_id = tsvd_tools_ai_resolve_page($input);
    $action  = 'updated';

    if (! $page_id) {
        if (empty($input['create_if_missing'])) {
            return new WP_Error('page_not_found', __('Seite nicht gefunden. Zum Anlegen create_if_missing=true setzen.', 'tsv-tools'));
        }
        $title   = isset($input['title']) && $input['title'] !== '' ? sanitize_text_field($input['title']) : __('Neue Seite', 'tsv-tools');
        $page_id = wp_insert_post(array(
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_title'   => $title,
            'post_content' => '',
        ), true);
        if (is_wp_error($page_id)) {
            return $page_id;
        }
        $page_id = (int) $page_id;
        $action  = 'created';
    }

    $existing = (string) get_post_field('post_content', $page_id);

    if ('replace' === $mode) {
        $new_content = $content;
    } elseif ('section' === $mode) {
        $sid   = ! empty($input['section_id']) ? sanitize_key($input['section_id']) : 'tsvd';
        $start = '<!-- tsvd-section:' . $sid . ' -->';
        $end   = '<!-- /tsvd-section:' . $sid . ' -->';
        $block = $start . "\n" . $content . "\n" . $end;
        $s     = strpos($existing, $start);
        $e     = strpos($existing, $end);
        if (false !== $s && false !== $e && $e > $s) {
            $new_content = substr($existing, 0, $s) . $block . substr($existing, $e + strlen($end));
            $action      = 'section_replaced';
        } else {
            $new_content = '' === trim($existing) ? $block : $existing . "\n\n" . $block;
            $action      = 'section_added';
        }
    } else {
        $new_content = '' === trim($existing) ? $content : $existing . "\n\n" . $content;
        $mode        = 'append';
    }

    $updated = wp_update_post(array('ID' => $page_id, 'post_content' => $new_content), true);
    if (is_wp_error($updated)) {
        return $updated;
    }

    return array(
        'id'        => $page_id,
        'url'       => (string) get_permalink($page_id),
        'edit_link' => (string) get_edit_post_link($page_id, 'raw'),
        'mode'      => $mode,
        'action'    => $action,
    );
}

function tsvd_tools_ai_rest_request($input) {
    $allowed_methods = array('GET', 'POST', 'PUT', 'PATCH', 'DELETE');
    $method = isset($input['method']) ? strtoupper(sanitize_text_field($input['method'])) : 'GET';
    if (! in_array($method, $allowed_methods, true)) {
        return new WP_Error('invalid_method', __('Ungültige HTTP-Methode.', 'tsv-tools'));
    }
    $route = isset($input['route']) ? '/' . ltrim((string) $input['route'], '/') : '';
    if ('' === $route || '/' === $route) {
        return new WP_Error('invalid_route', __('Keine gültige REST-Route angegeben.', 'tsv-tools'));
    }

    // Allowlist: only content/config namespaces. Excludes SSRF proxies
    // (/oembed/1.0/proxy) and plugin/theme management by omission.
    $allowed_prefixes = apply_filters('tsvd_rest_request_allowed_prefixes', array(
        '/wp/v2/posts', '/wp/v2/pages', '/wp/v2/media', '/wp/v2/blocks',
        '/wp/v2/categories', '/wp/v2/tags', '/wp/v2/taxonomies', '/wp/v2/types',
        '/wp/v2/comments', '/wp/v2/users', '/wp/v2/settings', '/wp/v2/search',
        '/wp/v2/animals', '/tsv-tools/',
    ));
    $route_path = strtok($route, '?');
    $allowed = false;
    foreach ($allowed_prefixes as $prefix) {
        if (0 === strpos($route_path, $prefix)) {
            $allowed = true;
            break;
        }
    }
    if (! $allowed) {
        return new WP_Error(
            'route_forbidden',
            __('Route nicht in der Allowlist. Erlaubt: Beiträge, Seiten, Medien, Taxonomien, Kommentare, Nutzer, Einstellungen, tsv-tools.', 'tsv-tools'),
            array('allowed_prefixes' => $allowed_prefixes)
        );
    }

    $params = isset($input['params']) && is_array($input['params']) ? $input['params'] : array();

    $request = new WP_REST_Request($method, $route);
    foreach ($params as $key => $value) {
        $request->set_param($key, $value);
    }

    $server   = rest_get_server();
    $response = rest_do_request($request);
    $data     = $server->response_to_data($response, false);

    return array(
        'status' => (int) $response->get_status(),
        'body'   => $data,
    );
}

function tsvd_tools_ai_rest_list_routes($input) {
    $contains = isset($input['contains']) ? (string) $input['contains'] : '';
    $routes   = array_keys(rest_get_server()->get_routes());
    if ('' !== $contains) {
        $routes = array_values(array_filter($routes, function ($route) use ($contains) {
            return false !== strpos($route, $contains);
        }));
    }
    sort($routes);
    return array(
        'count'  => count($routes),
        'routes' => $routes,
    );
}

function tsvd_tools_ai_seed_demo_stats( $input ) {
    if ( ! function_exists( 'tsvd_stats_table_name' ) ) {
        return new WP_Error( 'no_stats', __( 'Statistik-Funktionen (Theme) nicht geladen.', 'tsv-tools' ) );
    }
    global $wpdb;
    $table = tsvd_stats_table_name();

    $reset = ! isset( $input['reset'] ) || ! empty( $input['reset'] );
    if ( $reset ) {
        $wpdb->query( 'TRUNCATE TABLE ' . $table );
        if ( function_exists( 'tsvd_interest_reset_form_data' ) ) {
            tsvd_interest_reset_form_data();
        }
    }

    $interest = ( isset( $input['interest'] ) && is_array( $input['interest'] ) && $input['interest'] )
        ? $input['interest']
        : array( 3970 => 8, 3972 => 6, 3932 => 5, 3919 => 4, 3936 => 3, 3934 => 2, 3986 => 2, 3978 => 1, 3980 => 1 );
    $priv = isset( $input['private_placement'] ) ? max( 0, (int) $input['private_placement'] ) : 5;
    $miss = isset( $input['missing'] ) ? max( 0, (int) $input['missing'] ) : 4;

    $inc = function ( $metric, $bucket, $n ) use ( $wpdb, $table ) {
        $n = (int) $n;
        if ( $n <= 0 || null === $bucket || '' === $bucket ) {
            return;
        }
        $wpdb->query( $wpdb->prepare(
            "INSERT INTO {$table} (metric,bucket,cnt) VALUES (%s,%s,%d) ON DUPLICATE KEY UPDATE cnt = cnt + %d",
            $metric, (string) $bucket, $n, $n
        ) );
    };

    $total_interest = 0;
    $animals        = array();
    foreach ( $interest as $aid => $n ) {
        $aid = (int) $aid;
        $n   = (int) $n;
        if ( get_post_type( $aid ) !== 'animals' || get_post_status( $aid ) !== 'publish' ) {
            $animals[] = array( 'id' => $aid, 'skipped' => 'not_published_animal' );
            continue;
        }
        update_post_meta( $aid, 'animal_interest_form_count', $n );
        if ( function_exists( 'tsvd_interest_recompute_total' ) ) {
            tsvd_interest_recompute_total( $aid );
        }
        if ( function_exists( 'tsvd_stats_species_breed_terms' ) ) {
            $terms = tsvd_stats_species_breed_terms( $aid );
            $inc( 'species', $terms['species'], $n );
            $inc( 'breed', $terms['breed'], $n );
        }
        $inc( 'request_type', 'interest', $n );
        $total_interest += $n;
        $animals[] = array( 'id' => $aid, 'interest' => $n );
    }

    $inc( 'request_type', 'private_placement', $priv );
    $inc( 'request_type', 'missing', $miss );

    $grand    = $total_interest + $priv + $miss;
    $with_hou = $total_interest + $priv;

    $inc( 'residence', 'duesseldorf', round( $grand * 0.6 ) );
    $inc( 'residence', 'outside', $grand - round( $grand * 0.6 ) );
    $inc( 'housing', 'apartment', round( $with_hou * 0.55 ) );
    $inc( 'housing', 'house', $with_hou - round( $with_hou * 0.55 ) );
    $g = round( $with_hou * 0.4 );
    $b = round( $with_hou * 0.35 );
    $inc( 'outdoor', 'garden', $g );
    $inc( 'outdoor', 'balcony', $b );
    $inc( 'outdoor', 'none', $with_hou - $g - $b );

    $m0 = current_time( 'Y-m' );
    $m1 = gmdate( 'Y-m', strtotime( $m0 . '-01 -1 month' ) );
    $m2 = gmdate( 'Y-m', strtotime( $m0 . '-01 -2 month' ) );
    $p0 = round( $grand * 0.4 );
    $p1 = round( $grand * 0.35 );
    $inc( 'period', $m0, $p0 );
    $inc( 'period', $m1, $p1 );
    $inc( 'period', $m2, $grand - $p0 - $p1 );

    $counters = $wpdb->get_results( "SELECT metric, bucket, cnt FROM {$table} ORDER BY metric, cnt DESC", ARRAY_A );

    return array(
        'reset'          => $reset,
        'total_interest' => $total_interest,
        'grand_total'    => $grand,
        'animals'        => $animals,
        'counters'       => $counters ?: array(),
    );
}
