<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_can_manage_projects() {
    return current_user_can('edit_projects') || current_user_can('edit_posts');
}

function tsvd_tools_ai_create_project($input) {
    if (empty($input['title'])) {
        return new WP_Error('tsvd_tools_ai_missing_title', __('Titel ist erforderlich.', 'tsv-tools'));
    }
    if (empty($input['content'])) {
        return new WP_Error('tsvd_tools_ai_missing_content', __('Inhalt ist erforderlich.', 'tsv-tools'));
    }

    $status = in_array($input['status'] ?? 'draft', array('draft', 'publish'), true) ? $input['status'] : 'draft';

    $post_id = wp_insert_post(array(
        'post_type'    => 'projects',
        'post_status'  => $status,
        'post_title'   => sanitize_text_field($input['title']),
        'post_content' => wp_kses_post($input['content']),
    ), true);

    if (is_wp_error($post_id)) {
        return $post_id;
    }

    return array(
        'id'       => $post_id,
        'status'   => get_post_status($post_id),
        'edit_url' => get_edit_post_link($post_id, 'raw'),
        'view_url' => get_permalink($post_id),
    );
}
