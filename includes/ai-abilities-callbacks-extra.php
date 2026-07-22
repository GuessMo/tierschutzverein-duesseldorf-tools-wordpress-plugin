<?php
// AI Abilities — execute callbacks added 2026-07-15: delete-animal, list-redirects,
// get-form-stats. Kept separate from ai-abilities-callbacks.php to avoid growing
// that file further (already a god-file candidate).

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_can_delete_animal($input) {
    $id = isset($input['id']) ? (int) $input['id'] : 0;
    return $id > 0 && current_user_can('delete_post', $id);
}

function tsvd_tools_ai_delete_animal($input) {
    $id = isset($input['id']) ? (int) $input['id'] : 0;
    if (!$id || get_post_type($id) !== 'animals') {
        return new WP_Error('tsvd_tools_ai_not_found', __('Tier nicht gefunden.', 'tsv-tools'));
    }
    $permanent = !empty($input['permanent']);
    $deleted = wp_delete_post($id, $permanent);
    if (!$deleted) {
        return new WP_Error('tsvd_tools_ai_delete_failed', __('Löschen fehlgeschlagen.', 'tsv-tools'));
    }
    return array(
        'id'        => $id,
        'permanent' => $permanent,
        'status'    => $permanent ? 'deleted' : 'trashed',
    );
}

function tsvd_tools_ai_list_redirects($input) {
    if (!function_exists('tsvd_r301_configured') || !tsvd_r301_configured()) {
        return new WP_Error('tsvd_tools_ai_route301_unavailable', __('Route301 ist nicht konfiguriert.', 'tsv-tools'));
    }
    $domain = !empty($input['domain']) ? sanitize_text_field($input['domain']) : 'tierheim-duesseldorf.de';
    $redirects = tsvd_r301_get_redirects($domain);
    return array(
        'redirects' => is_array($redirects) ? $redirects : array(),
        'total'     => is_array($redirects) ? count($redirects) : 0,
    );
}

function tsvd_tools_ai_reset_form_stats($input) {
    if (empty($input['confirm'])) {
        return new WP_Error('tsvd_tools_ai_confirm_required', __('confirm muss true sein.', 'tsv-tools'));
    }
    if (!function_exists('tsvd_stats_table_name')) {
        return new WP_Error('no_stats', __('Statistik-Funktionen (Theme) nicht geladen.', 'tsv-tools'));
    }
    global $wpdb;
    $wpdb->query('TRUNCATE TABLE ' . tsvd_stats_table_name());
    $n = function_exists('tsvd_interest_reset_form_data') ? tsvd_interest_reset_form_data() : 0;
    return array('reset_animals' => (int) $n);
}

function tsvd_tools_ai_get_form_stats($input) {
    $dimension = isset($input['dimension']) ? sanitize_key($input['dimension']) : 'request_type';

    switch ($dimension) {
        case 'request_type':
        case 'residence':
        case 'housing':
        case 'outdoor':
            $rows = function_exists('tsvd_stats_count_by') ? tsvd_stats_count_by($dimension) : array();
            break;
        case 'period':
            $period = isset($input['period']) && in_array($input['period'], array('day', 'month', 'year'), true)
                ? $input['period'] : 'month';
            $rows = function_exists('tsvd_stats_count_by_period') ? tsvd_stats_count_by_period($period) : array();
            break;
        case 'top_animals':
            $limit = isset($input['limit']) ? max(1, min(50, (int) $input['limit'])) : 10;
            $rows = function_exists('tsvd_stats_top_animals') ? tsvd_stats_top_animals($limit) : array();
            break;
        case 'species_term':
        case 'breed_term':
            $rows = function_exists('tsvd_stats_interest_by_term') ? tsvd_stats_interest_by_term($dimension) : array();
            break;
        case 'adoption_status':
            $rows = function_exists('tsvd_stats_count_by_adoption_status') ? tsvd_stats_count_by_adoption_status() : array();
            break;
        case 'adoption_status_by_month':
            $limit = isset($input['limit']) ? max(1, min(60, (int) $input['limit'])) : 12;
            $rows = function_exists('tsvd_stats_adopted_by_month') ? tsvd_stats_adopted_by_month($limit) : array();
            break;
        default:
            return new WP_Error('tsvd_tools_ai_invalid_dimension', __('Unbekannte Dimension.', 'tsv-tools'));
    }

    return array('dimension' => $dimension, 'rows' => $rows);
}
