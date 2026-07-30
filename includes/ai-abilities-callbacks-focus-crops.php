<?php
/**
 * AI Abilities — regenerate-focus-crops (2026-07-30).
 *
 * Stage 3 of the Bildfokus plan: animal-card and tsvd-card are hard-crop sizes, so a
 * saved focus region (stage 2, theme) needs the files themselves regenerated — the
 * actual crop math lives in the theme (inc/media-focus-crop.php) since it depends on
 * the theme's registered image sizes and focus meta. This ability just runs it over
 * the existing backlog of attachments that already carry a focus region.
 *
 * @package TSVD_Tools
 */

if (!defined('ABSPATH')) exit;

/**
 * @return int[] Attachment IDs that carry a focus region.
 */
function tsvd_tools_find_attachments_with_focus_region() {
    global $wpdb;

    $ids = $wpdb->get_col(
        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_tsvd_focus_region'"
    );

    return array_map('intval', $ids);
}

/**
 * @param array $input Ability input, optionally { attachment_ids: int[] }.
 * @return array|WP_Error
 */
function tsvd_tools_ai_regenerate_focus_crops($input) {
    if (!function_exists('tsvd_regenerate_focus_crops_for_attachment')) {
        return new WP_Error(
            'tsvd_tools_theme_missing',
            __('Theme-Funktion für Fokus-Zuschnitte nicht geladen.', 'tsv-tools')
        );
    }

    $ids = array();
    if (!empty($input['attachment_ids']) && is_array($input['attachment_ids'])) {
        $ids = array_values(array_filter(array_map('absint', $input['attachment_ids'])));
    }
    if (!$ids) {
        $ids = tsvd_tools_find_attachments_with_focus_region();
    }

    $results = array();
    foreach ($ids as $attachment_id) {
        $results[] = array_merge(
            array('id' => $attachment_id),
            tsvd_regenerate_focus_crops_for_attachment($attachment_id)
        );
    }

    return array('total' => count($results), 'results' => $results);
}
