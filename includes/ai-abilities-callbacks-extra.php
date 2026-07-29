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

function tsvd_tools_ai_regenerate_attachment($attachment_id) {
    $attachment_id = (int) $attachment_id;
    if (!$attachment_id) {
        return null;
    }
    $file = get_attached_file($attachment_id);
    if (!$file || !file_exists($file)) {
        return array('image_id' => $attachment_id, 'status' => 'file_missing');
    }
    if (!function_exists('wp_generate_attachment_metadata')) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }
    $metadata = wp_generate_attachment_metadata($attachment_id, $file);
    wp_update_attachment_metadata($attachment_id, $metadata);
    return array('image_id' => $attachment_id, 'status' => 'regenerated');
}

function tsvd_tools_ai_regenerate_project_images($input) {
    $projects = get_posts(array(
        'post_type'      => 'projects',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ));

    $results = array();
    foreach ($projects as $pid) {
        $desktop = tsvd_tools_ai_regenerate_attachment(get_post_meta($pid, 'project_desktop_image', true));
        if ($desktop) {
            $results[] = array_merge(array('project_id' => $pid, 'field' => 'project_desktop_image'), $desktop);
        }
        $mobile = tsvd_tools_ai_regenerate_attachment(get_post_meta($pid, 'project_mobile_image', true));
        if ($mobile) {
            $results[] = array_merge(array('project_id' => $pid, 'field' => 'project_mobile_image'), $mobile);
        }
        $impressions = (string) get_post_meta($pid, 'project_impression_images', true);
        foreach (array_filter(array_map('absint', explode(',', $impressions))) as $img_id) {
            $r = tsvd_tools_ai_regenerate_attachment($img_id);
            if ($r) {
                $results[] = array_merge(array('project_id' => $pid, 'field' => 'project_impression_images'), $r);
            }
        }
    }

    return array('total' => count($results), 'results' => $results);
}

function tsvd_tools_ai_import_animal($input) {
    if (empty($input['name'])) {
        return new WP_Error('tsvd_tools_ai_missing_name', __('Name ist erforderlich.', 'tsv-tools'));
    }

    $postarr = array(
        'post_type'    => 'animals',
        'post_status'  => !empty($input['post_status']) ? sanitize_key($input['post_status']) : 'draft',
        'post_title'   => sanitize_text_field($input['name']),
        'post_content' => isset($input['description']) ? wp_kses_post($input['description']) : '',
    );
    if (!empty($input['post_date'])) {
        $postarr['post_date'] = sanitize_text_field($input['post_date']);
    }

    $post_id = wp_insert_post($postarr, true);
    if (is_wp_error($post_id)) {
        return $post_id;
    }

    if (!isset($input['adoption_status'])) {
        $input['adoption_status'] = 'for_adoption';
    }
    if (!isset($input['mediator'])) {
        $input['mediator'] = 'tierschutzverein';
    }
    tsvd_tools_ai_apply_animal_meta($post_id, $input);

    if (isset($input['teo_id']) && $input['teo_id'] !== '') {
        update_post_meta($post_id, 'animal_teo_id', sanitize_text_field($input['teo_id']));
    }
    if (isset($input['life_stage'])) {
        $life_stage = sanitize_key($input['life_stage']);
        if (in_array($life_stage, array('young', 'adult', 'senior', 'undefined'), true)) {
            update_post_meta($post_id, 'animal_life_stage', $life_stage);
        }
    }
    if (isset($input['castrated'])) {
        $castrated = sanitize_key($input['castrated']);
        if (in_array($castrated, array('yes', 'no', 'unknown'), true)) {
            update_post_meta($post_id, 'animal_castrated', $castrated);
        }
    }
    if (!empty($input['birthday'])) {
        update_post_meta($post_id, 'animal_birthday', sanitize_text_field($input['birthday']));
    }
    if (isset($input['birthday_status'])) {
        $birthday_status = sanitize_key($input['birthday_status']);
        if (in_array($birthday_status, array('known', 'estimated', 'unknown'), true)) {
            update_post_meta($post_id, 'animal_birthday_status', $birthday_status);
        }
    }

    if (!empty($input['image_ids']) && is_array($input['image_ids'])) {
        $image_ids = array_values(array_filter(array_map('absint', $input['image_ids'])));
        if ($image_ids) {
            update_post_meta($post_id, 'animal_images', $image_ids);
            set_post_thumbnail($post_id, $image_ids[0]);
        }
    }

    if (!empty($input['companion_ids']) && is_array($input['companion_ids'])) {
        $companion_ids = array_values(array_unique(array_filter(array_map('absint', $input['companion_ids']))));
        if ($companion_ids) {
            update_post_meta($post_id, 'animal_companion_animals', $companion_ids);
            foreach ($companion_ids as $companion_id) {
                $existing = get_post_meta($companion_id, 'animal_companion_animals', true);
                $existing = is_array($existing) ? $existing : array();
                if (!in_array($post_id, $existing, true)) {
                    $existing[] = $post_id;
                    update_post_meta($companion_id, 'animal_companion_animals', array_values(array_unique($existing)));
                }
            }
        }
    }

    if (function_exists('tsvd_regenerate_animal_title')) {
        tsvd_regenerate_animal_title($post_id, sanitize_text_field($input['name']));
    }

    return tsvd_tools_ai_format_animal($post_id);
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
        case 'adoption_source':
            $rows = function_exists('tsvd_stats_count_by_adoption_source') ? tsvd_stats_count_by_adoption_source() : array();
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
