<?php
// AI Abilities — Formular-Seiten (Tiervermittlung), angelegt 2026-08-04.
// Eigenständige öffentliche /tiere/<slug>/-Seiten, die statt eines
// Tierprofils ein Formular zeigen (siehe Theme:
// inc/animals/form-pages/shortcode-admin.php + single-animals.php-Zweig).
// Self-contained statt Aufruf der Theme-Funktionen: die Theme-Seite lädt
// dieses Modul nur bei is_admin(), ein MCP-/REST-Aufruf läuft aber nicht
// zwingend als Admin-Request.

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_create_form_page($input) {
    $title = sanitize_text_field($input['title'] ?? '');
    $slug = sanitize_title($input['slug'] ?? '');
    $form_id = absint($input['form_id'] ?? 0);

    if ($title === '' || $slug === '') {
        return new WP_Error('missing_fields', __('Titel und Slug sind erforderlich.', 'tsv-tools'));
    }
    if (!$form_id || get_post_type($form_id) !== 'tsvd_form') {
        return new WP_Error('invalid_form', __('form_id verweist auf kein bestehendes tsvd_form.', 'tsv-tools'));
    }

    $animal_id = wp_insert_post(array(
        'post_type'   => 'animals',
        'post_status' => 'publish',
        'post_title'  => $title,
        'post_name'   => $slug,
    ), true);
    if (is_wp_error($animal_id)) {
        return $animal_id;
    }

    update_post_meta($animal_id, 'animal_adoption_status', 'not_for_adoption');
    update_post_meta($animal_id, '_tsvd_animal_is_form_page', '1');
    update_post_meta($animal_id, '_tsvd_animal_form_id', $form_id);

    $pages = get_option('tsvd_animal_form_pages', array());
    if (!is_array($pages)) {
        $pages = array();
    }
    $pages[] = array(
        'animal_id' => (int) $animal_id,
        'form_id'   => $form_id,
        'title'     => $title,
        'slug'      => $slug,
    );
    update_option('tsvd_animal_form_pages', $pages);

    flush_rewrite_rules();

    return array(
        'created'   => true,
        'animal_id' => (int) $animal_id,
        'page_url'  => (string) get_permalink($animal_id),
        'message'   => __('Formular-Seite angelegt.', 'tsv-tools'),
    );
}

/**
 * Fügt einen Pfad zur Wartungsmodus-Ausnahmeliste hinzu (additiv, idempotent),
 * damit eine Formular-Seite für anonyme Besucher erreichbar bleibt, während
 * der Rest der Website noch hinter dem Wartungsmodus-Plugin liegt.
 */
function tsvd_tools_ai_add_maintenance_allowed_path($input) {
    $path = trim((string) ($input['path'] ?? ''));
    if ($path === '') {
        return new WP_Error('missing_path', __('path ist erforderlich.', 'tsv-tools'));
    }
    $path = '/' . trim($path, '/');

    $existing = get_option('tsvd_maintenance_allowed_paths', '');
    $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $existing)));
    $normalized = array_map(function ($p) {
        return '/' . trim($p, '/');
    }, $lines);

    if (in_array($path, $normalized, true)) {
        return array(
            'added'   => false,
            'paths'   => array_values($normalized),
            'message' => __('Pfad war bereits in der Ausnahmeliste.', 'tsv-tools'),
        );
    }

    $normalized[] = $path;
    update_option('tsvd_maintenance_allowed_paths', implode("\n", $normalized));

    return array(
        'added'   => true,
        'paths'   => array_values($normalized),
        'message' => __('Pfad zur Wartungsmodus-Ausnahmeliste hinzugefügt.', 'tsv-tools'),
    );
}
