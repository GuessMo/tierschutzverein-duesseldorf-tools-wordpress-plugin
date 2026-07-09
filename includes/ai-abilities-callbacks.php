<?php
// AI Abilities — execute callbacks for the animals CPT.

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_can_manage_animals() {
    return current_user_can('edit_animals') || current_user_can('edit_posts');
}

function tsvd_tools_ai_format_animal($post_id) {
    $name = get_post_meta($post_id, 'animal_name', true);
    return array(
        'id'              => (int) $post_id,
        'title'           => get_the_title($post_id),
        'status'          => get_post_status($post_id),
        'name'            => $name !== '' ? $name : get_the_title($post_id),
        'adoption_status' => (string) get_post_meta($post_id, 'animal_adoption_status', true),
        'mediator'        => get_post_meta($post_id, 'animal_mediator', true) ?: null,
        'missing_status'  => get_post_meta($post_id, 'animal_missing_status', true) ?: null,
        'edit_link'       => (string) get_edit_post_link($post_id, 'raw'),
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
        'applicant_housing'   => array('label' => __('Unterkunft', 'tsv-tools'), 'required' => false),
        'applicant_outdoor'   => array('label' => __('Außenbereich', 'tsv-tools'), 'required' => false),
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

    $existing_types = array();
    foreach ($fields as $field) {
        if (isset($field['type'])) {
            $existing_types[$field['type']] = true;
        }
    }

    $added = array();
    foreach (tsvd_tools_ai_applicant_field_defs($target) as $type => $def) {
        if (isset($existing_types[$type])) {
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

    update_post_meta($form_id, '_tsvd_form_fields', $fields);
    update_post_meta($form_id, '_tsvd_form_groups_config', $groups);

    $all_types = array_keys(tsvd_tools_ai_applicant_field_defs($target));

    return array(
        'form_id'          => $form_id,
        'added'            => $added,
        'skipped_existing' => array_values(array_diff($all_types, $added)),
        'form_edit_link'   => (string) get_edit_post_link($form_id, 'raw'),
    );
}
