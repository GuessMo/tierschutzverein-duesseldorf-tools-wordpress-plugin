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

    return tsvd_tools_ai_format_animal($post_id);
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

function tsvd_tools_ai_missing_form_fields() {
    return array(
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
}

function tsvd_tools_ai_create_missing_form($input) {
    $force = ! empty($input['force']);
    $existing = (int) get_option('tsvd_missing_animals_form', 0);
    if ($existing && get_post_type($existing) === 'tsvd_form' && ! $force) {
        return array(
            'created'        => false,
            'form_id'        => $existing,
            'form_edit_link' => (string) get_edit_post_link($existing, 'raw'),
            'page_id'        => (int) get_option('tsvd_missing_animals_page', 0),
            'page_url'       => '',
            'message'        => __('Formular bereits konfiguriert (force=true zum Neuanlegen).', 'tsv-tools'),
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

    update_post_meta($form_id, '_tsvd_form_fields', tsvd_tools_ai_missing_form_fields());
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
