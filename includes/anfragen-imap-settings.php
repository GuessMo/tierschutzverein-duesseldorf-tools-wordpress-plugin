<?php
/**
 * Anfragen-Dashboard: IMAP-Einstellungen für den Antwort-Rückkanal.
 *
 * Speichert Zugangsdaten für eine Mailbox (z. B. Google Workspace), aus der
 * eingehende Antworten der Interessent:innen per Cron abgeholt und der
 * passenden Anfrage zugeordnet werden (siehe anfragen-imap-poll.php).
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

function tsvd_anfragen_render_settings_view() {
    if (!current_user_can('manage_tsvd_anfragen')) return;

    $list_url = admin_url('edit.php?post_type=animals&page=tsvd-anfragen');

    if (isset($_POST['tsvd_anfragen_imap_nonce']) && wp_verify_nonce($_POST['tsvd_anfragen_imap_nonce'], 'tsvd_anfragen_imap_save')) {
        update_option('tsvd_anfragen_imap_enabled', isset($_POST['tsvd_anfragen_imap_enabled']) ? 1 : 0);
        update_option('tsvd_anfragen_imap_host', sanitize_text_field($_POST['tsvd_anfragen_imap_host'] ?? ''));
        update_option('tsvd_anfragen_imap_port', absint($_POST['tsvd_anfragen_imap_port'] ?? 993));
        update_option('tsvd_anfragen_imap_username', sanitize_text_field($_POST['tsvd_anfragen_imap_username'] ?? ''));
        update_option('tsvd_anfragen_imap_folder', sanitize_text_field($_POST['tsvd_anfragen_imap_folder'] ?? 'INBOX'));
        // Passwort nur überschreiben, wenn tatsächlich ein neuer Wert eingegeben wurde
        // (Feld wird beim Anzeigen nie mit dem echten Wert vorbefüllt).
        if (!empty($_POST['tsvd_anfragen_imap_password'])) {
            update_option('tsvd_anfragen_imap_password', (string) $_POST['tsvd_anfragen_imap_password']);
        }
        echo '<div class="updated"><p>' . esc_html__('Einstellungen gespeichert.', 'tsv-tools') . '</p></div>';
    }

    if (isset($_POST['tsvd_anfragen_imap_test_nonce']) && wp_verify_nonce($_POST['tsvd_anfragen_imap_test_nonce'], 'tsvd_anfragen_imap_test')) {
        $result = function_exists('tsvd_anfragen_imap_test_connection') ? tsvd_anfragen_imap_test_connection() : new WP_Error('missing', 'Poller nicht geladen.');
        if (is_wp_error($result)) {
            echo '<div class="error"><p>' . esc_html($result->get_error_message()) . '</p></div>';
        } else {
            echo '<div class="updated"><p>' . sprintf(esc_html__('Verbindung erfolgreich, %d ungelesene Nachrichten im Postfach.', 'tsv-tools'), $result) . '</p></div>';
        }
    }

    $s = tsvd_anfragen_imap_get_settings();
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Anfragen — IMAP-Einstellungen', 'tsv-tools'); ?></h1>
        <p><a href="<?php echo esc_url($list_url); ?>">&laquo; <?php esc_html_e('Zurück zur Liste', 'tsv-tools'); ?></a></p>
        <p><?php esc_html_e('Holt Antworten von Interessent:innen aus einer Mailbox (z. B. Google Workspace) automatisch per Cron ab und ordnet sie der passenden Anfrage zu.', 'tsv-tools'); ?></p>

        <form method="post">
            <?php wp_nonce_field('tsvd_anfragen_imap_save', 'tsvd_anfragen_imap_nonce'); ?>
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
                    <td><input type="email" id="tsvd_anfragen_imap_username" name="tsvd_anfragen_imap_username" class="regular-text" value="<?php echo esc_attr($s['username']); ?>" /></td>
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
            </table>
            <button type="submit" class="button button-primary"><?php esc_html_e('Speichern', 'tsv-tools'); ?></button>
        </form>

        <form method="post" style="margin-top:1.5em;">
            <?php wp_nonce_field('tsvd_anfragen_imap_test', 'tsvd_anfragen_imap_test_nonce'); ?>
            <button type="submit" class="button"><?php esc_html_e('Verbindung testen', 'tsv-tools'); ?></button>
        </form>
    </div>
    <?php
}
