<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_sync_load_backup_functions() {
    if (function_exists('sb_build_manifest')) {
        return true;
    }
    $base = WP_PLUGIN_DIR . '/site-backup-wordpress-plugin/includes/';
    foreach (array('media.php', 'export.php') as $file) {
        if (!file_exists($base . $file)) {
            return false;
        }
        require_once $base . $file;
    }
    return function_exists('sb_build_manifest');
}

function tsvd_tools_ai_sync_default_types() {
    return array('projects', 'tsvd_form', 'page', 'post', 'animals');
}

function tsvd_tools_ai_export_content_delta($input) {
    if (!tsvd_tools_ai_sync_load_backup_functions()) {
        return new WP_Error('site_backup_missing', __('site-backup-Plugin/Funktionen nicht verfügbar.', 'tsv-tools'));
    }

    $blocked = array('tsvd_anfragen', 'attachment', 'revision', 'nav_menu_item');
    $types = ! empty($input['post_types']) && is_array($input['post_types'])
        ? array_map('sanitize_key', $input['post_types'])
        : tsvd_tools_ai_sync_default_types();
    $types = array_values(array_diff($types, $blocked));

    $since = ! empty($input['since']) ? sanitize_text_field($input['since']) : '';
    $with_media = ! isset($input['with_media']) || ! empty($input['with_media']);
    $limit = isset($input['limit']) ? max(1, min(500, (int) $input['limit'])) : 300;

    $args = array(
        'post_type'      => $types,
        'post_status'    => array('publish', 'draft'),
        'posts_per_page' => $limit,
        'orderby'        => 'modified',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    );
    if ($since) {
        $args['date_query'] = array(
            array('column' => 'post_modified_gmt', 'after' => $since, 'inclusive' => false),
        );
    }

    $query = new WP_Query($args);
    $manifest = sb_build_manifest($query->posts, $with_media ? '1' : '0');

    if (! empty($manifest['posts'])) {
        foreach ($manifest['posts'] as &$post) {
            if (empty($post['attachments'])) {
                continue;
            }
            foreach ($post['attachments'] as &$attachment) {
                unset($attachment['file']);
                $attachment['url'] = wp_get_attachment_url($attachment['id']);
            }
            unset($attachment);
        }
        unset($post);
    }

    return array(
        'server_time' => gmdate('c'),
        'source_url'  => site_url(),
        'since'       => $since,
        'post_types'  => $types,
        'count'       => isset($manifest['posts']) ? count($manifest['posts']) : 0,
        'manifest'    => $manifest,
        'options'     => tsvd_tools_ai_sync_export_options(),
    );
}

function tsvd_tools_ai_sync_export_options() {
    global $wpdb;
    $options = array();
    $names = $wpdb->get_col(
        "SELECT option_name FROM {$wpdb->options}
         WHERE option_name LIKE 'tsvd\\_%' AND option_name NOT LIKE '\\_transient\\_%'"
    );
    foreach ((array) $names as $name) {
        if (in_array($name, array('siteurl', 'home'), true)) {
            continue;
        }
        $options[$name] = get_option($name);
    }
    return $options;
}
