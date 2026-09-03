<?php
/**
 * Anfragen-Dashboard: IMAP-Einstellungen für den Antwort-Rückkanal.
 *
 * Speichert Zugangsdaten für eine Mailbox (z. B. Google Workspace), aus der
 * eingehende Antworten der Interessent:innen per Cron abgeholt und der
 * passenden Anfrage zugeordnet werden (siehe anfragen-imap-poll.php).
 *
 * Render/Save sind bewusst als Tab-Sektion in den Animals-Settings verankert
 * (tsvd_render_anfragen_imap_settings_tab()/tsvd_anfragen_imap_save_settings(),
 * aufgerufen aus animals-admin.php) statt als eigene Anfragen-Unterseite —
 * gehört fachlich zur Tiervermittlungs-Konfiguration, nicht zur Anfragen-Liste.
 *
 * @package TSVD_Tools
 */

if (!defined('ABSPATH')) exit;

function tsvd_anfragen_imap_get_settings() {
    return array(
        'enabled'  => (bool) get_option('tsvd_anfragen_imap_enabled', false),
        'host'     => get_option('tsvd_anfragen_imap_host', 'imap.gmail.com'),
        'port'     => (int) get_option('tsvd_anfragen_imap_port', 993),
        'username' => get_option('tsvd_anfragen_imap_username', ''),
        'password' => get_option('tsvd_anfragen_imap_password', ''),
        'folder'   => get_option('tsvd_anfragen_imap_folder', 'INBOX'),
    );
}

/**
 * Speichert die IMAP-Felder — aufgerufen innerhalb des bereits per
 * tsvd_animal_settings_nonce verifizierten Save-Blocks in animals-admin.php,
 * daher keine eigene Nonce-Prüfung hier (analog zu den Sibling-Funktionen
 * tsvd_save_private_adoption_settings()/tsvd_save_missing_animals_settings()).
 */
function tsvd_anfragen_imap_save_settings() {
    if (!current_user_can('manage_tsvd_anfragen')) return;

    update_option('tsvd_anfragen_imap_enabled', isset($_POST['tsvd_anfragen_imap_enabled']) ? 1 : 0);
    update_option('tsvd_anfragen_imap_host', sanitize_text_field($_POST['tsvd_anfragen_imap_host'] ?? ''));
    update_option('tsvd_anfragen_imap_port', absint($_POST['tsvd_anfragen_imap_port'] ?? 993));
    update_option('tsvd_anfragen_imap_username', sanitize_text_field($_POST['tsvd_anfragen_imap_username'] ?? ''));
    update_option('tsvd_anfragen_imap_folder', sanitize_text_field($_POST['tsvd_anfragen_imap_folder'] ?? 'INBOX'));
    update_option('tsvd_anfragen_send_delay', absint($_POST['tsvd_anfragen_send_delay'] ?? 120));
    update_option('tsvd_anfragen_signature', sanitize_textarea_field(wp_unslash($_POST['tsvd_anfragen_signature'] ?? '')));
    // Passwort nur überschreiben, wenn tatsächlich ein neuer Wert eingegeben wurde
    // (Feld wird beim Anzeigen nie mit dem echten Wert vorbefüllt).
    if (!empty($_POST['tsvd_anfragen_imap_password'])) {
        update_option('tsvd_anfragen_imap_password', (string) $_POST['tsvd_anfragen_imap_password']);
    }
}

/**
 * Rendert die IMAP-Felder als Sektion innerhalb des Animals-Settings-Formulars
 * (kein eigenes <form>, kein eigener Submit-Button — teilt sich den "Save"-
 * Button der Elternseite). Verbindungstest läuft per AJAX, nicht als
 * verschachteltes <form>, das innerhalb eines <form> ungültiges HTML wäre.
 */
function tsvd_render_anfragen_imap_settings_tab() {
    if (!current_user_can('manage_tsvd_anfragen')) return;

    $s = tsvd_anfragen_imap_get_settings();
    ?>
    <div id="tab-anfragen" class="tsvd-settings-tab" style="display:none;">

    <h2><?php esc_html_e('Anfragen — IMAP-Einstellungen (Antwort-Rückkanal)', 'tsv-tools'); ?></h2>
    <p><?php esc_html_e('Holt Antworten von Interessent:innen aus einer Mailbox (z. B. Google Workspace) automatisch per Cron ab und ordnet sie der passenden Anfrage zu.', 'tsv-tools'); ?></p>

    <table class="form-table">
        <tr>
            <th><label for="tsvd_anfragen_imap_enabled"><?php esc_html_e('Aktiv', 'tsv-tools'); ?></label></th>
            <td><input type="checkbox" id="tsvd_anfragen_imap_enabled" name="tsvd_anfragen_imap_enabled" value="1" <?php checked($s['enabled']); ?> /></td>
        </tr>
        <tr>
            <th><label for="tsvd_anfragen_imap_host"><?php esc_html_e('IMAP-Host', 'tsv-tools'); ?></label></th>
            <td><input type="text" id="tsvd_anfragen_imap_host" name="tsvd_anfragen_imap_host" class="regular-text" value="<?php echo esc_attr($s['host']); ?>" /></td>
        </tr>
        <tr>
            <th><label for="tsvd_anfragen_imap_port"><?php esc_html_e('Port', 'tsv-tools'); ?></label></th>
            <td><input type="number" id="tsvd_anfragen_imap_port" name="tsvd_anfragen_imap_port" value="<?php echo esc_attr($s['port']); ?>" /></td>
        </tr>
        <tr>
            <th><label for="tsvd_anfragen_imap_username"><?php esc_html_e('Mailbox-Adresse', 'tsv-tools'); ?></label></th>
            <td>
                <input type="text" id="tsvd_anfragen_imap_username" name="tsvd_anfragen_imap_username" class="regular-text" value="<?php echo esc_attr($s['username']); ?>" />
                <p class="description"><?php esc_html_e('Benutzername für die Mailbox. In der lokalen Entwicklung (Mailpit) reicht eine beliebige Adresse wie tieranfragen@localhost:8080, weil keine echten Mails verschickt werden. In Produktion hier die echte Mailbox-Adresse eintragen.', 'tsv-tools'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="tsvd_anfragen_imap_password"><?php esc_html_e('App-Passwort', 'tsv-tools'); ?></label></th>
            <td>
                <input type="password" id="tsvd_anfragen_imap_password" name="tsvd_anfragen_imap_password" class="regular-text" autocomplete="new-password" placeholder="<?php echo $s['password'] !== '' ? esc_attr__('•••••••• (unverändert lassen für keine Änderung)', 'tsv-tools') : ''; ?>" />
                <p class="description"><?php esc_html_e('Google-App-Passwort (nicht das normale Konto-Passwort), benötigt aktive Bestätigung in zwei Schritten. Bleibt leer = wird nicht geändert.', 'tsv-tools'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="tsvd_anfragen_imap_folder"><?php esc_html_e('Ordner', 'tsv-tools'); ?></label></th>
            <td><input type="text" id="tsvd_anfragen_imap_folder" name="tsvd_anfragen_imap_folder" value="<?php echo esc_attr($s['folder']); ?>" /></td>
        </tr>
        <tr>
            <th><label for="tsvd_anfragen_send_delay"><?php esc_html_e('Verzögerung Mailversand (Sekunden)', 'tsv-tools'); ?></label></th>
            <td>
                <input type="number" min="0" step="10" id="tsvd_anfragen_send_delay" name="tsvd_anfragen_send_delay" value="<?php echo esc_attr((int) get_option('tsvd_anfragen_send_delay', 120)); ?>" />
                <p class="description"><?php esc_html_e('Zeitfenster, in dem eine gesendete Antwort noch bearbeitet oder abgebrochen werden kann, bevor die Mail wirklich rausgeht. 0 = sofort senden. Empfohlen: 120.', 'tsv-tools'); ?></p>
            </td>
        </tr>
        <tr>
            <th><label for="tsvd_anfragen_signature"><?php esc_html_e('Signatur für Antwort-Mails', 'tsv-tools'); ?></label></th>
            <td>
                <textarea id="tsvd_anfragen_signature" name="tsvd_anfragen_signature" class="large-text" rows="4"><?php echo esc_textarea(get_option('tsvd_anfragen_signature', '')); ?></textarea>
                <p class="description"><?php esc_html_e('Wird automatisch unter jede Antwort an Interessent:innen angehängt.', 'tsv-tools'); ?></p>
            </td>
        </tr>
    </table>

    <p>
        <button type="button" class="button" id="tsvd-anfragen-imap-test" data-nonce="<?php echo esc_attr(wp_create_nonce('tsvd_anfragen_imap_test')); ?>">
            <?php esc_html_e('Verbindung testen', 'tsv-tools'); ?>
        </button>
        <span id="tsvd-anfragen-imap-test-result" style="margin-left:8px;"></span>
    </p>
    <script>
    (function () {
        var btn = document.getElementById('tsvd-anfragen-imap-test');
        var result = document.getElementById('tsvd-anfragen-imap-test-result');
        if (!btn) return;
        btn.addEventListener('click', function () {
            btn.disabled = true;
            result.textContent = '...';
            result.style.color = '#666';
            jQuery.post(ajaxurl, {
                action: 'tsvd_anfragen_imap_test',
                nonce: btn.getAttribute('data-nonce')
            }, function (response) {
                btn.disabled = false;
                if (response.success) {
                    result.textContent = response.data.message;
                    result.style.color = '#008a20';
                } else {
                    result.textContent = response.data.message || '<?php echo esc_js(__('Fehler', 'tsv-tools')); ?>';
                    result.style.color = '#c00';
                }
            }).fail(function () {
                btn.disabled = false;
                result.textContent = '<?php echo esc_js(__('Serverfehler', 'tsv-tools')); ?>';
                result.style.color = '#c00';
            });
        });
    })();
    </script>
    </div>
    <?php
}

add_action('wp_ajax_tsvd_anfragen_imap_test', 'tsvd_ajax_anfragen_imap_test');

function tsvd_ajax_anfragen_imap_test() {
    if (!current_user_can('manage_tsvd_anfragen')) {
        wp_send_json_error(array('message' => __('Keine Berechtigung.', 'tsv-tools')));
    }
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tsvd_anfragen_imap_test')) {
        wp_send_json_error(array('message' => __('Sicherheitsfehler.', 'tsv-tools')));
    }

    $result = tsvd_anfragen_imap_test_connection();
    if (is_wp_error($result)) {
        wp_send_json_error(array('message' => $result->get_error_message()));
    }

    wp_send_json_success(array(
        'message' => sprintf(__('Verbindung erfolgreich, %d ungelesene Nachrichten im Postfach.', 'tsv-tools'), $result),
    ));
}
