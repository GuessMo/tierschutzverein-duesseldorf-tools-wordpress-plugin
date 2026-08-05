<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_can_manage_projects() {
    return current_user_can('edit_projects') || current_user_can('edit_posts');
}

function tsvd_tools_ai_normalize_milestone_progress($status, $progress) {
    if ('completed' === $status) {
        return 100;
    }
    if ('in_progress' === $status && $progress < 10) {
        return 10;
    }
    return $progress;
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

function tsvd_tools_ai_can_manage_projects_settings() {
    return current_user_can('manage_options');
}

function tsvd_tools_ai_set_projects_max_lines($input) {
    $max_lines = absint($input['max_lines'] ?? 0);
    if ($max_lines < 1) {
        return new WP_Error('tsvd_tools_ai_invalid_max_lines', __('max_lines muss größer als 0 sein.', 'tsv-tools'));
    }

    update_option('tsvd_projects_description_max_lines', $max_lines);

    return array('max_lines' => $max_lines);
}

function tsvd_tools_ai_update_project($input) {
    $post_id = absint($input['id'] ?? 0);
    $post    = $post_id ? get_post($post_id) : null;

    if (!$post || 'projects' !== $post->post_type) {
        return new WP_Error('tsvd_tools_ai_project_not_found', __('Projekt nicht gefunden.', 'tsv-tools'));
    }

    $postarr = array('ID' => $post_id);
    if (isset($input['title'])) {
        $postarr['post_title'] = sanitize_text_field($input['title']);
    }
    if (isset($input['content'])) {
        $new_content = wp_kses_post($input['content']);
        $postarr['post_content'] = !empty($input['append'])
            ? $post->post_content . $new_content
            : $new_content;
    }

    $result = wp_update_post($postarr, true);
    if (is_wp_error($result)) {
        return $result;
    }

    $image_fields = array(
        'desktop_image' => 'project_desktop_image',
        'mobile_image'  => 'project_mobile_image',
        'logo_image'    => 'project_logo_image',
    );
    foreach ($image_fields as $input_key => $meta_key) {
        if (!isset($input[$input_key])) {
            continue;
        }
        $attachment_id = absint($input[$input_key]);
        if ($attachment_id > 0) {
            update_post_meta($post_id, $meta_key, $attachment_id);
        } else {
            delete_post_meta($post_id, $meta_key);
        }
    }

    return array(
        'id'       => $post_id,
        'status'   => get_post_status($post_id),
        'edit_url' => get_edit_post_link($post_id, 'raw'),
        'view_url' => get_permalink($post_id),
    );
}

function tsvd_tools_ai_add_project_milestone($input) {
    $post_id = absint($input['id'] ?? 0);
    $post    = $post_id ? get_post($post_id) : null;

    if (!$post || 'projects' !== $post->post_type) {
        return new WP_Error('tsvd_tools_ai_project_not_found', __('Projekt nicht gefunden.', 'tsv-tools'));
    }
    if (empty($input['title'])) {
        return new WP_Error('tsvd_tools_ai_missing_milestone_title', __('Titel ist erforderlich.', 'tsv-tools'));
    }

    $status = in_array($input['status'] ?? 'pending', array('pending', 'in_progress', 'completed'), true)
        ? $input['status']
        : 'pending';

    $milestones   = get_post_meta($post_id, 'project_milestones', true);
    $milestones   = is_array($milestones) ? $milestones : array();
    $milestones[] = array(
        'title'        => sanitize_text_field($input['title']),
        'description'  => sanitize_textarea_field($input['description'] ?? ''),
        'date'         => sanitize_text_field($input['date'] ?? ''),
        'progress'     => tsvd_tools_ai_normalize_milestone_progress($status, min(100, absint($input['progress'] ?? 0))),
        'status'       => $status,
        'image'        => absint($input['image'] ?? 0),
        'publish_at'   => sanitize_text_field($input['publish_at'] ?? ''),
        'show_in_news' => !empty($input['show_in_news']),
    );

    update_post_meta($post_id, 'project_milestones', $milestones);

    return array(
        'id'              => $post_id,
        'milestone_count' => count($milestones),
        'edit_url'        => get_edit_post_link($post_id, 'raw'),
        'view_url'        => get_permalink($post_id),
    );
}

function tsvd_tools_ai_update_project_milestone($input) {
    $post_id = absint($input['id'] ?? 0);
    $post    = $post_id ? get_post($post_id) : null;

    if (!$post || 'projects' !== $post->post_type) {
        return new WP_Error('tsvd_tools_ai_project_not_found', __('Projekt nicht gefunden.', 'tsv-tools'));
    }
    if (empty($input['match_title'])) {
        return new WP_Error('tsvd_tools_ai_missing_match_title', __('match_title ist erforderlich.', 'tsv-tools'));
    }

    $milestones = get_post_meta($post_id, 'project_milestones', true);
    $milestones = is_array($milestones) ? $milestones : array();

    $index = null;
    foreach ($milestones as $i => $milestone) {
        if (($milestone['title'] ?? '') === $input['match_title']) {
            $index = $i;
            break;
        }
    }

    if (null === $index) {
        return new WP_Error('tsvd_tools_ai_milestone_not_found', __('Kein Meilenstein mit diesem Titel gefunden.', 'tsv-tools'));
    }

    if (isset($input['title'])) {
        $milestones[$index]['title'] = sanitize_text_field($input['title']);
    }
    if (isset($input['description'])) {
        $milestones[$index]['description'] = sanitize_textarea_field($input['description']);
    }
    if (isset($input['date'])) {
        $milestones[$index]['date'] = sanitize_text_field($input['date']);
    }
    if (isset($input['progress'])) {
        $milestones[$index]['progress'] = min(100, absint($input['progress']));
    }
    if (isset($input['status']) && in_array($input['status'], array('pending', 'in_progress', 'completed'), true)) {
        $milestones[$index]['status'] = $input['status'];
    }
    if (isset($input['image'])) {
        $milestones[$index]['image'] = absint($input['image']);
    }
    if (isset($input['publish_at'])) {
        $milestones[$index]['publish_at'] = sanitize_text_field($input['publish_at']);
    }
    if (isset($input['show_in_news'])) {
        $milestones[$index]['show_in_news'] = !empty($input['show_in_news']);
    }

    $milestones[$index]['progress'] = tsvd_tools_ai_normalize_milestone_progress(
        $milestones[$index]['status'] ?? 'pending',
        absint($milestones[$index]['progress'] ?? 0)
    );

    update_post_meta($post_id, 'project_milestones', $milestones);

    return array(
        'id'         => $post_id,
        'index'      => $index,
        'edit_url'   => get_edit_post_link($post_id, 'raw'),
        'view_url'   => get_permalink($post_id),
    );
}
