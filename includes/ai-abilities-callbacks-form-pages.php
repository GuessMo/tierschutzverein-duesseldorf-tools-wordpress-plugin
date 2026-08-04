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

/**
 * Anfragen-Dashboard: Status abfragen und dabei die lazy auf admin_init
 * gehookten Einmal-Migrationen (DB-Tabellen, Capability-Grant) auslösen.
 * Ein MCP-/REST-Aufruf durchläuft admin_init NICHT von selbst (das feuert nur
 * bei einem echten wp-admin-Request) — ohne diesen manuellen Trigger blieben
 * Tabellen/Capability bis zum ersten echten Admin-Besuch ungesetzt.
 *
 * Ruft dafür gezielt NUR die beiden bekannten Migrations-Funktionen auf, statt
 * pauschal do_action('admin_init') zu feuern — Letzteres löst auch fremde
 * admin_init-Hooks aus (z. B. Settings-API-Aufrufe wie add_settings_section()),
 * die außerhalb eines echten wp-admin-Bootstraps fatal fehlschlagen.
 */
function tsvd_tools_ai_anfragen_dashboard_status($input) {
    if (function_exists('tsvd_anfragen_maybe_upgrade_db')) {
        tsvd_anfragen_maybe_upgrade_db();
    }
    if (function_exists('tsvd_role_manager_maybe_grant_anfragen_cap')) {
        tsvd_role_manager_maybe_grant_anfragen_cap();
    }

    global $wpdb;
    $table_exists = function_exists('tsvd_anfragen_table_name')
        ? (bool) $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', tsvd_anfragen_table_name()))
        : false;
    $replies_table_exists = function_exists('tsvd_anfragen_replies_table_name')
        ? (bool) $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', tsvd_anfragen_replies_table_name()))
        : false;

    $anfragen_count = ($table_exists && function_exists('tsvd_anfragen_table_name'))
        ? (int) $wpdb->get_var('SELECT COUNT(*) FROM ' . tsvd_anfragen_table_name())
        : null;

    $admin_role = get_role('administrator');
    $admin_has_cap = $admin_role ? $admin_role->has_cap('manage_tsvd_anfragen') : false;

    $form_id = (int) get_option('tsvd_interessentenbogen_form', 0);
    $persist_enabled = $form_id ? (bool) get_post_meta($form_id, '_tsvd_form_persist_inquiry', true) : false;

    return array(
        'tables_exist'                 => $table_exists && $replies_table_exists,
        'anfragen_count'               => $anfragen_count,
        'administrator_has_capability' => $admin_has_cap,
        'interessentenbogen_form_id'   => $form_id,
        'persist_inquiry_enabled'      => $persist_enabled,
        'dashboard_url'                => admin_url('edit.php?post_type=animals&page=tsvd-anfragen'),
    );
}
