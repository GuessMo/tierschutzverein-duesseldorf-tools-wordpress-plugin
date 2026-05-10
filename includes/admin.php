<?php
if (!defined('ABSPATH')) exit;

define('TSVD_TOOLS_LOG_FILE', WP_CONTENT_DIR . '/tsvd-tools-sync.log');

function tsvd_tools_log($message) {
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] $message\n";
    @file_put_contents(TSVD_TOOLS_LOG_FILE, $entry, FILE_APPEND);
}

add_action('admin_menu', 'tsvd_tools_register_admin_page');
function tsvd_tools_register_admin_page() {
    add_menu_page(
        'TSV Tools',
        'TSV Tools',
        'manage_options',
        'tsvd-tools',
        'tsvd_tools_render_admin_page',
        'dashicons-admin-tools',
        81
    );
}

add_action('admin_enqueue_scripts', 'tsvd_tools_enqueue_assets');
function tsvd_tools_enqueue_assets($hook) {
    if ($hook !== 'toplevel_page_tsvd-tools') return;
    wp_enqueue_style('tsvd-tools-admin', TSVD_TOOLS_URL . 'assets/admin.css', array(), TSVD_TOOLS_VERSION);
    wp_enqueue_script('tsvd-tools-admin', TSVD_TOOLS_URL . 'assets/admin.js', array(), TSVD_TOOLS_VERSION, true);

    wp_localize_script('tsvd-tools-admin', 'tsvdTools', array(
        'nonce'   => wp_create_nonce('tsvd_tools_nonce'),
        'ajaxUrl' => admin_url('admin-ajax.php'),
    ));
}

function tsvd_tools_render_admin_page() {
    $theme_dir = get_template_directory();
    $breeds_file = $theme_dir . '/data/animal-breeds.json';
    $colors_file = $theme_dir . '/data/animal-colors.json';

    $breeds_modified = file_exists($breeds_file) ? date('d.m.Y H:i', filemtime($breeds_file)) : 'nicht gefunden';
    $colors_modified = file_exists($colors_file) ? date('d.m.Y H:i', filemtime($colors_file)) : 'nicht gefunden';
    $breeds_last_sync = get_option('tsvd_tools_breeds_last_sync', 'nie');
    $colors_last_sync = get_option('tsvd_tools_colors_last_sync', 'nie');
    $breeds_count = wp_count_terms('animal_breed', array('hide_empty' => false));
    $colors_count = wp_count_terms('animal_color', array('hide_empty' => false));
    $breeds_count = is_int($breeds_count) ? $breeds_count : 0;
    $colors_count = is_int($colors_count) ? $colors_count : 0;
    ?>
    <div class="wrap tsvd-tools-wrap">
        <h1>TSV Tools</h1>

        <h2>Tier-Sync: Rassen & Farben aus JSON</h2>
        <p>Synchronisiert Tier-Rassen und Farben aus den JSON-Dateien im Theme-Verzeichnis in die WordPress-Taxonomien.</p>

        <div class="tsvd-tools-info" style="margin:15px 0;padding:15px;background:#f8f8f8;border:1px solid #ddd;">
            <p><strong>Rassen-JSON:</strong> <?php echo $breeds_modified; ?> (<?php echo $breeds_count; ?> Rassen in DB) — Letzter Sync: <?php echo esc_html($breeds_last_sync); ?></p>
            <p><strong>Farben-JSON:</strong> <?php echo $colors_modified; ?> (<?php echo $colors_count; ?> Farben in DB) — Letzter Sync: <?php echo esc_html($colors_last_sync); ?></p>
        </div>

        <div style="margin:20px 0;">
            <p style="margin-bottom:10px;">
                <button type="button" id="tsvd-tools-sync-breeds" class="button button-primary">Rassen synchronisieren</button>
                <button type="button" id="tsvd-tools-sync-colors" class="button button-primary">Farben synchronisieren</button>
                <button type="button" id="tsvd-tools-sync-both" class="button button-primary">Beides synchronisieren</button>
            </p>
            <p><label><input type="checkbox" id="tsvd-tools-force-sync"> <strong>Force Sync</strong> (alle bestehenden Einträge löschen und neu importieren)</label></p>
        </div>

        <div style="margin:20px 0;padding:15px;background:#fffbe6;border:1px solid #ddd;">
            <h3>Auto-Sync Webhook (für GitHub Actions)</h3>
            <p>Endpoint: <code id="tsvd-tools-endpoint"><?php echo rest_url('tsvd-tools/v1/sync'); ?></code></p>
            <p>
                <label>Sync-Token:</label>
                <input type="text" id="tsvd-tools-token" value="<?php echo esc_attr(get_option('tsvd_tools_sync_token', '')); ?>" style="width:300px;">
                <button type="button" id="tsvd-tools-save-token" class="button">Speichern</button>
            </p>
            <p class="description">Token wird für REST-API-Authentifizierung benötigt. Er muss in GitHub Actions als Secret hinterlegt werden.</p>
        </div>

        <div style="margin:20px 0;">
            <h3>Log</h3>
            <div id="tsvd-tools-log" style="background:#1e1e1e;color:#00ff00;font-family:monospace;font-size:12px;padding:15px;height:300px;overflow-y:auto;border:1px solid #333;white-space:pre-wrap;"></div>
        </div>
    </div>
    <?php
}

add_action('wp_ajax_tsvd_tools_get_status', 'tsvd_tools_ajax_get_status');
function tsvd_tools_ajax_get_status() {
    check_ajax_referer('tsvd_tools_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Nicht autorisiert.'), 403);
    }

    $theme_dir = get_template_directory();
    $breeds_file = $theme_dir . '/data/animal-breeds.json';
    $colors_file = $theme_dir . '/data/animal-colors.json';

    $breeds_modified = file_exists($breeds_file) ? date('d.m.Y H:i', filemtime($breeds_file)) : 'nicht gefunden';
    $colors_modified = file_exists($colors_file) ? date('d.m.Y H:i', filemtime($colors_file)) : 'nicht gefunden';
    $breeds_last_sync = get_option('tsvd_tools_breeds_last_sync', 'nie');
    $colors_last_sync = get_option('tsvd_tools_colors_last_sync', 'nie');
    $breeds_count = wp_count_terms('animal_breed', array('hide_empty' => false));
    $colors_count = wp_count_terms('animal_color', array('hide_empty' => false));

    wp_send_json_success(array(
        'breeds_file' => $breeds_modified,
        'colors_file' => $colors_modified,
        'breeds_last_sync' => $breeds_last_sync,
        'colors_last_sync' => $colors_last_sync,
        'breeds_count' => is_int($breeds_count) ? $breeds_count : 0,
        'colors_count' => is_int($colors_count) ? $colors_count : 0,
    ));
}

add_action('rest_api_init', function() {
    register_rest_route('tsvd-tools/v1', '/sync', array(
        'methods' => 'POST',
        'callback' => function($request) {
            $token = $request->get_header('x-sync-token');
            $expected = get_option('tsvd_tools_sync_token', '');
            if (empty($expected) || $token !== $expected) {
                return new WP_Error('unauthorized', 'Invalid or missing sync token', array('status' => 401));
            }

            $type = $request->get_param('type') ?: 'both';
            $results = array();

            if ($type === 'breeds' || $type === 'both') {
                $results['breeds'] = tsvd_tools_import_breeds(true);
                tsvd_tools_log("REST API: breeds sync done");
            }
            if ($type === 'colors' || $type === 'both') {
                $results['colors'] = tsvd_tools_import_colors(true);
                tsvd_tools_log("REST API: colors sync done");
            }

            return new WP_REST_Response($results, 200);
        },
        'permission_callback' => '__return_true',
    ));
});

add_action('wp_ajax_tsvd_tools_save_token', function() {
    check_ajax_referer('tsvd_tools_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Nicht autorisiert.'));
    }
    $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
    update_option('tsvd_tools_sync_token', $token);
    wp_send_json_success(array('message' => 'Token gespeichert.'));
});

add_action('wp_ajax_tsvd_tools_sync_breeds', 'tsvd_tools_ajax_sync_breeds');
function tsvd_tools_ajax_sync_breeds() {
    check_ajax_referer('tsvd_tools_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Nicht autorisiert.'), 403);
    }

    $force = isset($_POST['force']) && $_POST['force'] === 'true';
    $result = tsvd_tools_import_breeds($force);

    if ($result['success']) {
        update_option('tsvd_tools_breeds_last_sync', date('d.m.Y H:i'));
        wp_send_json_success(array(
            'message' => $result['message'],
            'imported' => $result['imported'],
            'updated' => $result['updated'],
            'deleted' => $result['deleted'],
        ));
    } else {
        wp_send_json_error(array('message' => $result['message']));
    }
}

add_action('wp_ajax_tsvd_tools_sync_colors', 'tsvd_tools_ajax_sync_colors');
function tsvd_tools_ajax_sync_colors() {
    check_ajax_referer('tsvd_tools_nonce', 'nonce');
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Nicht autorisiert.'), 403);
    }

    $force = isset($_POST['force']) && $_POST['force'] === 'true';
    $result = tsvd_tools_import_colors($force);

    if ($result['success']) {
        update_option('tsvd_tools_colors_last_sync', date('d.m.Y H:i'));
        wp_send_json_success(array(
            'message' => $result['message'],
            'imported' => $result['imported'],
            'updated' => $result['updated'],
            'deleted' => $result['deleted'],
        ));
    } else {
        wp_send_json_error(array('message' => $result['message']));
    }
}

function tsvd_tools_import_breeds($force_update = false) {
    tsvd_tools_log('BREEDS SYNC START (force=' . ($force_update ? 'true' : 'false') . ')');

    $json_file = get_template_directory() . '/data/animal-breeds.json';

    if (!file_exists($json_file)) {
        return array('success' => false, 'message' => 'JSON-Datei nicht gefunden: ' . $json_file);
    }

    $json_content = file_get_contents($json_file);
    $data = json_decode($json_content, true);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($data['species'])) {
        return array('success' => false, 'message' => 'Ungültiges JSON-Format.');
    }

    if (!$force_update && get_option('tsvd_tools_breeds_imported')) {
        return array('success' => false, 'message' => 'Bereits importiert. Nutze Force Sync für erneuten Import.');
    }

    $current_locale = get_locale();
    $is_german = (strpos($current_locale, 'de') === 0);
    $imported = 0;
    $updated = 0;
    $deleted = 0;

    $valid_slugs = array();
    foreach ($data['species'] as $sp) {
        $valid_slugs[] = $sp['slug'];
        if (isset($sp['breeds']) && is_array($sp['breeds'])) {
            foreach ($sp['breeds'] as $br) {
                $valid_slugs[] = $br['slug'];
            }
        }
    }

    if ($force_update) {
        $all_breeds = get_terms(array('taxonomy' => 'animal_breed', 'hide_empty' => false));
        if (!is_wp_error($all_breeds) && !empty($all_breeds)) {
            foreach ($all_breeds as $term) {
                if (!in_array($term->slug, $valid_slugs)) {
                    $r = wp_delete_term($term->term_id, 'animal_breed');
                    if ($r && !is_wp_error($r)) { $deleted++; }
                }
            }
        }
    }

    foreach ($data['species'] as $sp) {
        $sp_name = $is_german ? $sp['name_de'] : $sp['name_en'];
        $sp_slug = $sp['slug'];

        $exists = term_exists($sp_slug, 'animal_breed');
        if (!$exists || $exists === 0) {
            $new = wp_insert_term($sp_name, 'animal_breed', array('slug' => $sp_slug));
            if (!is_wp_error($new)) { $imported++; }
            $sp_id = $new['term_id'];
        } else {
            $sp_id = (int) $exists;
        }

        if (isset($sp['breeds']) && is_array($sp['breeds'])) {
            foreach ($sp['breeds'] as $br) {
                $br_name = $is_german ? $br['name_de'] : $br['name_en'];
                $br_slug = $br['slug'];

                $br_exists = term_exists($br_slug, 'animal_breed');
                if (!$br_exists || $br_exists === 0) {
                    $new = wp_insert_term($br_name, 'animal_breed', array('slug' => $br_slug, 'parent' => $sp_id));
                    if (!is_wp_error($new)) { $imported++; }
                } else {
                    wp_update_term($br_exists, 'animal_breed', array('name' => $br_name, 'parent' => $sp_id));
                    $updated++;
                }
            }
        }
    }

    update_option('tsvd_tools_breeds_imported', true);
    tsvd_tools_log("BREEDS SYNC DONE: imported=$imported updated=$updated deleted=$deleted");
    return array(
        'success' => true,
        'message' => sprintf('Rassen importiert: %d neu, %d aktualisiert, %d gelöscht.', $imported, $updated, $deleted),
        'imported' => $imported, 'updated' => $updated, 'deleted' => $deleted
    );
}

function tsvd_tools_import_colors($force_update = false) {
    tsvd_tools_log('COLORS SYNC START (force=' . ($force_update ? 'true' : 'false') . ')');

    $json_file = get_template_directory() . '/data/animal-colors.json';

    if (!file_exists($json_file)) {
        return array('success' => false, 'message' => 'JSON-Datei nicht gefunden: ' . $json_file);
    }

    $json_content = file_get_contents($json_file);
    $data = json_decode($json_content, true);

    if (json_last_error() !== JSON_ERROR_NONE || !isset($data['colors'])) {
        return array('success' => false, 'message' => 'Ungültiges JSON-Format.');
    }

    if (!$force_update && get_option('tsvd_tools_colors_imported')) {
        return array('success' => false, 'message' => 'Bereits importiert. Nutze Force Sync für erneuten Import.');
    }

    $current_locale = get_locale();
    $is_german = (strpos($current_locale, 'de') === 0);
    $imported = 0;
    $updated = 0;
    $deleted = 0;

    $valid_slugs = array();
    foreach ($data['colors'] as $c) {
        $valid_slugs[] = $c['slug'];
    }

    if ($force_update) {
        $all_colors = get_terms(array('taxonomy' => 'animal_color', 'hide_empty' => false));
        if (!is_wp_error($all_colors) && !empty($all_colors)) {
            foreach ($all_colors as $term) {
                if (!in_array($term->slug, $valid_slugs)) {
                    $r = wp_delete_term($term->term_id, 'animal_color');
                    if ($r && !is_wp_error($r)) { $deleted++; }
                }
            }
        }
    }

    foreach ($data['colors'] as $c) {
        $name = $is_german ? $c['name_de'] : $c['name_en'];
        $slug = $c['slug'];

        $exists = term_exists($slug, 'animal_color');
        if (!$exists || $exists === 0) {
            $new = wp_insert_term($name, 'animal_color', array('slug' => $slug));
            if (!is_wp_error($new)) { $imported++; }
        } else {
            wp_update_term($exists, 'animal_color', array('name' => $name));
            $updated++;
        }
    }

    update_option('tsvd_tools_colors_imported', true);
    tsvd_tools_log("COLORS SYNC DONE: imported=$imported updated=$updated deleted=$deleted");
    return array(
        'success' => true,
        'message' => sprintf('Farben importiert: %d neu, %d aktualisiert, %d gelöscht.', $imported, $updated, $deleted),
        'imported' => $imported, 'updated' => $updated, 'deleted' => $deleted
    );
}