<?php
/**
 * AI Abilities — get-seo-score (2026-08-03).
 *
 * Read-only SEO/readability score for a post or page. The scoring engine
 * itself lives in the theme (inc/seo/seo-scoring.php) since it owns the
 * criteria/thresholds — this is a thin wrapper, same pattern as
 * regenerate-focus-crops in ai-abilities-callbacks-focus-crops.php.
 *
 * @package TSVD_Tools
 */

if (!defined('ABSPATH')) exit;

/**
 * @param array $input Ability input, { post_id: int }.
 * @return array|WP_Error
 */
function tsvd_tools_ai_get_seo_score($input) {
    if (!function_exists('tsvd_get_seo_score_for_post')) {
        return new WP_Error(
            'tsvd_tools_theme_missing',
            __('Theme-Funktion für den SEO-Score nicht geladen.', 'tsv-tools')
        );
    }

    $post_id = isset($input['post_id']) ? absint($input['post_id']) : 0;
    if (!$post_id || !in_array(get_post_type($post_id), array('post', 'page'), true)) {
        return new WP_Error(
            'tsvd_tools_ai_not_found',
            __('Beitrag oder Seite nicht gefunden.', 'tsv-tools')
        );
    }

    return tsvd_get_seo_score_for_post($post_id);
}

function tsvd_tools_ai_set_seo_meta($input) {
    $post_id = isset($input['post_id']) ? absint($input['post_id']) : 0;
    if (!$post_id || !in_array(get_post_type($post_id), array('post', 'page', 'projects'), true)) {
        return new WP_Error(
            'tsvd_tools_ai_not_found',
            __('Beitrag oder Seite nicht gefunden.', 'tsv-tools')
        );
    }

    $updated = array();

    if (array_key_exists('focus_keyword', $input)) {
        update_post_meta($post_id, '_tsvd_seo_focus_keyword', sanitize_text_field((string) $input['focus_keyword']));
        $updated[] = 'focus_keyword';
    }

    if (array_key_exists('meta_description', $input)) {
        update_post_meta($post_id, '_tsvd_seo_meta_description', sanitize_textarea_field((string) $input['meta_description']));
        $updated[] = 'meta_description';
    }

    return array(
        'post_id'          => $post_id,
        'focus_keyword'    => (string) get_post_meta($post_id, '_tsvd_seo_focus_keyword', true),
        'meta_description' => (string) get_post_meta($post_id, '_tsvd_seo_meta_description', true),
        'updated'          => $updated,
    );
}
