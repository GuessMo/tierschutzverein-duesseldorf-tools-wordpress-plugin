<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_can_manage_website_updates() {
    return current_user_can('manage_options');
}

function tsvd_tools_ai_create_website_update($input) {
    $title = isset($input['title']) ? sanitize_text_field($input['title']) : '';
    if ('' === $title) {
        return new WP_Error('missing_title', __('Titel erforderlich.', 'tsv-tools'));
    }

    $status  = (isset($input['status']) && 'publish' === $input['status']) ? 'publish' : 'draft';
    $content = isset($input['content']) ? wp_kses_post($input['content']) : '';

    $id = wp_insert_post(array(
        'post_type'    => TSVD_UPDATE_CPT,
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => $status,
    ), true);
    if (is_wp_error($id)) {
        return $id;
    }

    $visibility = (isset($input['visibility']) && 'extern' === $input['visibility']) ? 'extern' : 'intern';
    update_post_meta($id, TSVD_UPDATE_VISIBILITY_META, $visibility);

    return array(
        'id'       => (int) $id,
        'status'   => $status,
        'edit_url' => get_edit_post_link($id, 'raw'),
    );
}

function tsvd_tools_ai_set_project_form($input) {
    $id = isset($input['id']) ? absint($input['id']) : 0;
    if (!$id || 'projects' !== get_post_type($id)) {
        return new WP_Error('bad_id', __('Kein gueltiges Projekt.', 'tsv-tools'));
    }

    $form_id = isset($input['form_id']) ? absint($input['form_id']) : 0;
    if ($form_id > 0) {
        $form = get_post($form_id);
        if (!$form || 'tsvd_form' !== $form->post_type || 'publish' !== $form->post_status) {
            return new WP_Error('bad_form', __('Kein gueltiges veroeffentlichtes Formular.', 'tsv-tools'));
        }
        update_post_meta($id, 'project_form_id', $form_id);
    } else {
        delete_post_meta($id, 'project_form_id');
    }

    return array(
        'id'       => $id,
        'form_id'  => $form_id,
        'view_url' => get_permalink($id),
    );
}
