<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_update_summary($post) {
    return array(
        'id'      => (int) $post->ID,
        'number'  => tsvd_update_number($post->ID),
        'title'   => get_the_title($post),
        'status'  => $post->post_status,
        'date'    => get_post_time('c', false, $post),
        'sent_in' => tsvd_newsletter_item_sent_in($post->ID),
    );
}

function tsvd_tools_ai_get_valid_update($input) {
    $id = isset($input['id']) ? absint($input['id']) : 0;
    if (!$id || TSVD_UPDATE_CPT !== get_post_type($id)) {
        return new WP_Error('bad_id', __('Kein gueltiges Website-Update.', 'tsv-tools'));
    }

    return get_post($id);
}

function tsvd_tools_ai_query_updates($status, $only_unsent, $limit) {
    $args = array(
        'post_type'      => TSVD_UPDATE_CPT,
        'post_status'    => 'any' === $status ? array('draft', 'publish') : $status,
        'posts_per_page' => $limit,
        'meta_key'       => TSVD_UPDATE_NUMBER_META,
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    );
    if ($only_unsent) {
        $args['meta_query'] = array(
            array('key' => TSVD_NEWSLETTER_SENT_IN_META, 'compare' => 'NOT EXISTS'),
        );
    }

    return get_posts($args);
}

function tsvd_tools_ai_list_website_updates($input) {
    $allowed = array('any', 'draft', 'publish');
    $status  = (isset($input['status']) && in_array($input['status'], $allowed, true))
        ? $input['status']
        : 'any';
    $limit   = isset($input['limit']) ? max(1, min(200, absint($input['limit']))) : 50;
    $unsent  = !empty($input['only_unsent']);

    $posts = tsvd_tools_ai_query_updates($status, $unsent, $limit);

    return array(
        'count'   => count($posts),
        'updates' => array_map('tsvd_tools_ai_update_summary', $posts),
    );
}

function tsvd_tools_ai_get_website_update($input) {
    $post = tsvd_tools_ai_get_valid_update($input);
    if (is_wp_error($post)) {
        return $post;
    }

    return array_merge(
        tsvd_tools_ai_update_summary($post),
        array(
            'content'  => $post->post_content,
            'edit_url' => get_edit_post_link($post->ID, 'raw'),
        )
    );
}

function tsvd_tools_ai_delete_website_update($input) {
    $post = tsvd_tools_ai_get_valid_update($input);
    if (is_wp_error($post)) {
        return $post;
    }

    $force  = !empty($input['force']);
    $result = $force ? wp_delete_post($post->ID, true) : wp_trash_post($post->ID);
    if (!$result) {
        return new WP_Error('delete_failed', __('Loeschen fehlgeschlagen.', 'tsv-tools'));
    }

    return array('id' => (int) $post->ID, 'deleted' => $force, 'trashed' => !$force);
}

function tsvd_tools_ai_resolve_update_ids($input) {
    if (!empty($input['all_unsent'])) {
        return wp_list_pluck(tsvd_tools_ai_query_updates('any', true, -1), 'ID');
    }
    $ids = isset($input['ids']) ? array_map('absint', (array) $input['ids']) : array();

    return array_values(array_filter($ids, function ($id) {
        return TSVD_UPDATE_CPT === get_post_type($id);
    }));
}

function tsvd_tools_ai_create_sent_placeholder($label) {
    $title = sprintf(
        __('%1$s (per E-Mail, kein Newsletter-Versand) – %2$s', 'tsv-tools'),
        $label,
        wp_date('d.m.Y')
    );
    $id = wp_insert_post(array(
        'post_type'   => TSVD_NEWSLETTER_CPT,
        'post_title'  => $title,
        'post_status' => 'publish',
    ), true);
    if (!is_wp_error($id)) {
        update_post_meta($id, TSVD_NEWSLETTER_SENT_META, time());
    }

    return $id;
}

function tsvd_tools_ai_mark_website_updates_sent($input) {
    $label = isset($input['label']) ? sanitize_text_field($input['label']) : '';
    if ('' === $label) {
        return new WP_Error('missing_label', __('Bezeichnung erforderlich.', 'tsv-tools'));
    }
    $ids = tsvd_tools_ai_resolve_update_ids($input);
    if (!$ids) {
        return new WP_Error('no_updates', __('Keine passenden Website-Updates.', 'tsv-tools'));
    }

    $newsletter_id = tsvd_tools_ai_create_sent_placeholder($label);
    if (is_wp_error($newsletter_id)) {
        return $newsletter_id;
    }
    tsvd_newsletter_mark_items_sent($newsletter_id, $ids);

    return array(
        'newsletter_id'    => (int) $newsletter_id,
        'newsletter_title' => get_the_title($newsletter_id),
        'marked'           => array_map('intval', $ids),
    );
}
