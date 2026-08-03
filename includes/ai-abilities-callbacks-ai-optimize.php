<?php
/**
 * AI Abilities — ai-optimize-seo-text (2026-08-03).
 *
 * Calls Gemini (via the theme's tsvd_get_ai_seo_suggestion()) to rewrite a
 * post/page's content, focus keyword and meta description. Restricted to
 * the same admin allow-list as the AI/MCP admin pages
 * (tsvd_tools_ai_user_allowed(), ai-admin.php) — the FIRST ability to use
 * that gate as a permission_callback, deliberately narrower than the
 * broader edit_posts default every other ability uses. Dry-run/apply
 * pattern mirrors cleanup-animal-image-artifacts
 * (ai-abilities-callbacks-maintenance.php).
 *
 * @package TSVD_Tools
 */

if (!defined('ABSPATH')) exit;

/**
 * @return bool Whether the current user may call the AI-optimize ability.
 */
function tsvd_tools_ai_can_ai_optimize_seo() {
    return tsvd_tools_ai_user_allowed();
}

/**
 * @param array $input Ability input, { post_id: int, apply: bool }.
 * @return array|WP_Error
 */
function tsvd_tools_ai_optimize_seo_text($input) {
    if (!function_exists('tsvd_get_ai_seo_suggestion')) {
        return new WP_Error(
            'tsvd_tools_theme_missing',
            __('Theme-Funktion für die KI-Optimierung nicht geladen.', 'tsv-tools')
        );
    }

    $post_id = isset($input['post_id']) ? absint($input['post_id']) : 0;
    if (!$post_id || !in_array(get_post_type($post_id), array('post', 'page'), true)) {
        return new WP_Error(
            'tsvd_tools_ai_not_found',
            __('Beitrag oder Seite nicht gefunden.', 'tsv-tools')
        );
    }

    $suggestion = tsvd_get_ai_seo_suggestion($post_id, get_current_user_id());
    if (is_wp_error($suggestion)) {
        return $suggestion;
    }

    $apply = !empty($input['apply']);

    if ($apply) {
        wp_update_post(array(
            'ID'           => $post_id,
            'post_content' => $suggestion['content'],
        ));
        update_post_meta($post_id, '_tsvd_seo_focus_keyword', $suggestion['focus_keyword']);
        update_post_meta($post_id, '_tsvd_seo_meta_description', $suggestion['meta_description']);
    }

    return array(
        'post_id'    => $post_id,
        'applied'    => $apply,
        'suggestion' => $suggestion,
    );
}
