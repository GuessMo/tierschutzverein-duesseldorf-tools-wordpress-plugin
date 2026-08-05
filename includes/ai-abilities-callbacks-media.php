<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_can_upload_media() {
    return current_user_can('upload_files');
}

function tsvd_tools_ai_upload_media($input) {
    if (empty($input['filename'])) {
        return new WP_Error('tsvd_tools_ai_missing_filename', __('filename ist erforderlich.', 'tsv-tools'));
    }
    if (empty($input['file_base64'])) {
        return new WP_Error('tsvd_tools_ai_missing_file', __('file_base64 ist erforderlich.', 'tsv-tools'));
    }

    $transient_key = 'tsvd_media_chunks_' . md5(sanitize_file_name($input['filename']));
    $total_chunks  = isset($input['total_chunks']) ? absint($input['total_chunks']) : 1;

    if ($total_chunks > 1) {
        $chunks   = get_transient($transient_key);
        $chunks   = is_array($chunks) ? $chunks : array();
        $chunks[(int) ($input['chunk_index'] ?? 0)] = $input['file_base64'];
        set_transient($transient_key, $chunks, 10 * MINUTE_IN_SECONDS);

        if (count($chunks) < $total_chunks) {
            return array('chunks_received' => count($chunks), 'total_chunks' => $total_chunks);
        }

        ksort($chunks);
        $full_base64 = implode('', $chunks);
        delete_transient($transient_key);
    } else {
        $full_base64 = $input['file_base64'];
    }

    $decoded = base64_decode($full_base64, true);
    if (false === $decoded) {
        return new WP_Error('tsvd_tools_ai_invalid_base64', __('file_base64 ist kein gueltiges Base64.', 'tsv-tools'));
    }

    $filename = sanitize_file_name($input['filename']);
    $filetype = wp_check_filetype($filename);
    if (empty($filetype['type'])) {
        return new WP_Error('tsvd_tools_ai_unsupported_filetype', __('Dateityp wird nicht unterstuetzt.', 'tsv-tools'));
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $upload = wp_upload_bits($filename, null, $decoded);
    if (!empty($upload['error'])) {
        return new WP_Error('tsvd_tools_ai_upload_failed', $upload['error']);
    }

    $attachment_id = wp_insert_attachment(array(
        'post_mime_type' => $filetype['type'],
        'post_title'     => sanitize_text_field($input['title'] ?? pathinfo($filename, PATHINFO_FILENAME)),
        'post_status'    => 'inherit',
    ), $upload['file']);

    if (is_wp_error($attachment_id)) {
        return $attachment_id;
    }

    $metadata = wp_generate_attachment_metadata($attachment_id, $upload['file']);
    wp_update_attachment_metadata($attachment_id, $metadata);

    if (!empty($input['alt_text'])) {
        update_post_meta($attachment_id, '_wp_attachment_image_alt', sanitize_text_field($input['alt_text']));
    }

    return array(
        'id'  => $attachment_id,
        'url' => wp_get_attachment_url($attachment_id),
    );
}
