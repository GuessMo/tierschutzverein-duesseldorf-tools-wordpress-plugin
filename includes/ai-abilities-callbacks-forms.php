<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_sanitize_form_fields($fields) {
    if (!is_array($fields)) {
        return array();
    }
    $out = array();
    foreach ($fields as $field) {
        if (!is_array($field) || empty($field['id']) || empty($field['type'])) {
            continue;
        }
        if (function_exists('tsvd_sanitize_form_field')) {
            $out[] = tsvd_sanitize_form_field($field);
        } else {
            $field['id'] = sanitize_key($field['id']);
            $field['type'] = sanitize_key($field['type']);
            $out[] = $field;
        }
    }
    return $out;
}

function tsvd_tools_ai_apply_form_meta($form_id, $input, $is_create) {
    if (isset($input['fields'])) {
        update_post_meta($form_id, '_tsvd_form_fields', tsvd_tools_ai_sanitize_form_fields($input['fields']));
    }
    if (isset($input['groups_config']) && is_array($input['groups_config'])) {
        update_post_meta($form_id, '_tsvd_form_groups_config', $input['groups_config']);
    }
    if (isset($input['recipient_email'])) {
        $recipient = is_email($input['recipient_email']) ? sanitize_email($input['recipient_email']) : get_option('admin_email');
        update_post_meta($form_id, '_tsvd_form_recipient', $recipient);
    } elseif ($is_create) {
        update_post_meta($form_id, '_tsvd_form_recipient', get_option('admin_email'));
    }
    if (isset($input['subject'])) {
        update_post_meta($form_id, '_tsvd_form_subject', sanitize_text_field($input['subject']));
    }
    if (isset($input['success_message'])) {
        update_post_meta($form_id, '_tsvd_form_success_message', wp_kses_post($input['success_message']));
    }
    if (isset($input['show_title'])) {
        update_post_meta($form_id, '_tsvd_form_show_title', !empty($input['show_title']) ? '1' : '0');
    } elseif ($is_create) {
        update_post_meta($form_id, '_tsvd_form_show_title', '1');
    }
    if (isset($input['persist_inquiry'])) {
        update_post_meta($form_id, '_tsvd_form_persist_inquiry', !empty($input['persist_inquiry']) ? '1' : '0');
    }
}

function tsvd_tools_ai_list_forms($input) {
    $args = array(
        'post_type'      => 'tsvd_form',
        'post_status'    => array('publish', 'draft'),
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );
    if (!empty($input['search'])) {
        $args['s'] = sanitize_text_field($input['search']);
    }
    $forms = array();
    foreach (get_posts($args) as $post) {
        $fields = get_post_meta($post->ID, '_tsvd_form_fields', true);
        $forms[] = array(
            'form_id'     => $post->ID,
            'title'       => $post->post_title,
            'status'      => $post->post_status,
            'field_count' => is_array($fields) ? count($fields) : 0,
            'recipient'   => (string) get_post_meta($post->ID, '_tsvd_form_recipient', true),
            'shortcode'   => '[tsvd_form id="' . $post->ID . '"]',
        );
    }
    return array('count' => count($forms), 'forms' => $forms);
}

function tsvd_tools_ai_get_form($input) {
    $form_id = isset($input['form_id']) ? (int) $input['form_id'] : 0;
    if (!$form_id || get_post_type($form_id) !== 'tsvd_form') {
        return new WP_Error('not_found', __('Formular nicht gefunden.', 'tsv-tools'));
    }
    $fields = get_post_meta($form_id, '_tsvd_form_fields', true);
    return array(
        'form_id'         => $form_id,
        'title'           => get_the_title($form_id),
        'status'          => get_post_status($form_id),
        'fields'          => is_array($fields) ? $fields : array(),
        'groups_config'   => (array) get_post_meta($form_id, '_tsvd_form_groups_config', true),
        'recipient'       => (string) get_post_meta($form_id, '_tsvd_form_recipient', true),
        'subject'         => (string) get_post_meta($form_id, '_tsvd_form_subject', true),
        'success_message' => (string) get_post_meta($form_id, '_tsvd_form_success_message', true),
        'show_title'      => get_post_meta($form_id, '_tsvd_form_show_title', true) === '1',
        'persist_inquiry' => get_post_meta($form_id, '_tsvd_form_persist_inquiry', true) === '1',
        'shortcode'       => '[tsvd_form id="' . $form_id . '"]',
    );
}

function tsvd_tools_ai_create_form($input) {
    if (empty($input['title']) || !isset($input['fields'])) {
        return new WP_Error('missing_params', __('title und fields sind erforderlich.', 'tsv-tools'));
    }
    $form_id = wp_insert_post(array(
        'post_type'   => 'tsvd_form',
        'post_status' => 'publish',
        'post_title'  => sanitize_text_field($input['title']),
    ), true);
    if (is_wp_error($form_id)) {
        return $form_id;
    }
    tsvd_tools_ai_apply_form_meta($form_id, $input, true);
    return array(
        'created'        => true,
        'form_id'        => (int) $form_id,
        'shortcode'      => '[tsvd_form id="' . (int) $form_id . '"]',
        'form_edit_link' => (string) get_edit_post_link($form_id, 'raw'),
    );
}

function tsvd_tools_ai_update_form($input) {
    $form_id = isset($input['form_id']) ? (int) $input['form_id'] : 0;
    if (!$form_id || get_post_type($form_id) !== 'tsvd_form') {
        return new WP_Error('not_found', __('Formular nicht gefunden.', 'tsv-tools'));
    }
    if (isset($input['title'])) {
        wp_update_post(array('ID' => $form_id, 'post_title' => sanitize_text_field($input['title'])));
    }
    tsvd_tools_ai_apply_form_meta($form_id, $input, false);
    return array(
        'updated'        => true,
        'form_id'        => $form_id,
        'form_edit_link' => (string) get_edit_post_link($form_id, 'raw'),
    );
}

function tsvd_tools_ai_delete_form($input) {
    $form_id = isset($input['form_id']) ? (int) $input['form_id'] : 0;
    if (!$form_id || get_post_type($form_id) !== 'tsvd_form') {
        return new WP_Error('not_found', __('Formular nicht gefunden.', 'tsv-tools'));
    }
    $force = !empty($input['force']);
    $result = wp_delete_post($form_id, $force);
    return array(
        'deleted'   => (bool) $result,
        'form_id'   => $form_id,
        'permanent' => $force,
    );
}
