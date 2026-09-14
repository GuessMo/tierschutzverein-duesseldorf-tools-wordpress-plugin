<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_sync_default_types() {
    return array('projects', 'tsvd_form', 'page', 'post', 'animals');
}

function tsvd_tools_ai_export_content_delta($input) {
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
    $posts = array();
    foreach ($query->posts as $post) {
        $posts[] = tsvd_tools_ai_sync_export_post($post, $with_media);
    }

    return array(
        'exported_at' => gmdate('c'),
        'source_url'  => site_url(),
        'since'       => $since,
        'post_types'  => $types,
        'count'       => count($posts),
        'posts'       => $posts,
        'options'     => tsvd_tools_ai_sync_export_options(),
    );
}

function tsvd_tools_ai_sync_export_post($post, $with_media) {
    $terms = array();
    foreach (get_object_taxonomies($post->post_type) as $taxonomy) {
        $objects = wp_get_object_terms($post->ID, $taxonomy);
        if (is_wp_error($objects) || empty($objects)) {
            continue;
        }
        $terms[$taxonomy] = array_map(function ($term) {
            return array('term_id' => $term->term_id, 'name' => $term->name, 'slug' => $term->slug);
        }, $objects);
    }

    return array(
        'ID'            => $post->ID,
        'post_type'     => $post->post_type,
        'post_name'     => $post->post_name,
        'post_title'    => $post->post_title,
        'post_content'  => $post->post_content,
        'post_excerpt'  => $post->post_excerpt,
        'post_status'   => $post->post_status,
        'post_date'     => $post->post_date,
        'post_modified' => $post->post_modified,
        'meta'          => get_post_meta($post->ID),
        'terms'         => $terms,
        'attachments'   => $with_media ? tsvd_tools_ai_sync_collect_attachments($post) : array(),
    );
}

function tsvd_tools_ai_sync_gather_ids($value, &$ids) {
    if (is_numeric($value)) {
        $ids[] = (int) $value;
        return;
    }
    if (is_array($value)) {
        foreach ($value as $item) {
            tsvd_tools_ai_sync_gather_ids($item, $ids);
        }
    }
}

function tsvd_tools_ai_sync_collect_attachments($post) {
    $ids = array();

    $thumb = (int) get_post_thumbnail_id($post->ID);
    if ($thumb) {
        $ids[] = $thumb;
    }
    $children = get_children(array(
        'post_parent' => $post->ID,
        'post_type'   => 'attachment',
        'numberposts' => -1,
        'fields'      => 'ids',
    ));
    foreach ((array) $children as $child_id) {
        $ids[] = (int) $child_id;
    }
    foreach (get_post_meta($post->ID) as $values) {
        foreach ((array) $values as $raw) {
            tsvd_tools_ai_sync_gather_ids(maybe_unserialize($raw), $ids);
        }
    }
    if (preg_match_all('/\[gallery[^\]]*ids=.([0-9,]+)./', $post->post_content, $matches)) {
        foreach ($matches[1] as $list) {
            foreach (explode(',', $list) as $gid) {
                $ids[] = (int) $gid;
            }
        }
    }

    $out = array();
    foreach (array_unique(array_filter($ids)) as $id) {
        if (get_post_type($id) !== 'attachment' || ! wp_attachment_is_image($id)) {
            continue;
        }
        $relative = get_post_meta($id, '_wp_attached_file', true);
        $url = wp_get_attachment_url($id);
        if (! $relative || ! $url) {
            continue;
        }
        $out[] = array(
            'id'       => (int) $id,
            'relative' => $relative,
            'url'      => $url,
            'filename' => basename($relative),
        );
    }
    return $out;
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
