<?php
/**
 * Anfragen-Dashboard: IMAP-Abruf für eingehende Antworten.
 *
 * Holt per Cron ungelesene Nachrichten aus der in anfragen-imap-settings.php
 * konfigurierten Mailbox, ordnet sie anhand der Anfragen-Nummer im Betreff
 * (siehe Referenz-Token "#<id>" in anfragen-admin-detail.php) der passenden
 * Anfrage zu und speichert sie als eingehende Antwort (direction='in').
 *
 * Nutzt webklex/php-imap (Composer), da PHPs native ext-imap XOAUTH2 nicht
 * unterstützt und auf vielen Hosts nicht kompiliert ist — die Bibliothek
 * implementiert das Protokoll selbst, keine Abhängigkeit von ext-imap nötig.
 *
 * @package TSVD_Tools
 */

if (!defined('ABSPATH')) exit;

add_action('init', 'tsvd_anfragen_imap_maybe_schedule_cron');
function tsvd_anfragen_imap_maybe_schedule_cron() {
    $settings = tsvd_anfragen_imap_get_settings();
    $scheduled = wp_next_scheduled('tsvd_anfragen_check_imap');
    if ($settings['enabled'] && !$scheduled) {
        wp_schedule_event(time(), 'hourly', 'tsvd_anfragen_check_imap');
    } elseif (!$settings['enabled'] && $scheduled) {
        wp_unschedule_event($scheduled, 'tsvd_anfragen_check_imap');
    }
}

/**
 * Baut einen verbundenen IMAP-Client aus den gespeicherten Einstellungen.
 *
 * @return \Webklex\PHPIMAP\Client|WP_Error
 */
function tsvd_anfragen_imap_make_client() {
    $s = tsvd_anfragen_imap_get_settings();
    if (empty($s['username']) || empty($s['password'])) {
        return new WP_Error('missing_config', __('IMAP-Zugangsdaten sind nicht vollständig hinterlegt.', 'tsv-tools'));
    }
    if (!class_exists('\Webklex\PHPIMAP\ClientManager')) {
        return new WP_Error('missing_library', __('webklex/php-imap ist nicht geladen (Composer-Autoload fehlt).', 'tsv-tools'));
    }

    try {
        $cm = new \Webklex\PHPIMAP\ClientManager();
        $client = $cm->make(array(
            'host'           => $s['host'],
            'port'           => $s['port'],
            'protocol'       => 'imap',
            'encryption'     => 'ssl',
            'validate_cert'  => true,
            'username'       => $s['username'],
            'password'       => $s['password'],
            'authentication' => null,
        ));
        $client->connect();
        return $client;
    } catch (\Throwable $e) {
        return new WP_Error('imap_connect_failed', $e->getMessage());
    }
}

/**
 * Verbindungstest für die Einstellungsseite: gibt die Anzahl ungelesener
 * Nachrichten zurück oder einen WP_Error.
 *
 * @return int|WP_Error
 */
function tsvd_anfragen_imap_test_connection() {
    $client = tsvd_anfragen_imap_make_client();
    if (is_wp_error($client)) {
        return $client;
    }
    try {
        $s = tsvd_anfragen_imap_get_settings();
        $folder = $client->getFolder($s['folder']);
        $count = $folder->messages()->whereUnseen()->get()->count();
        $client->disconnect();
        return $count;
    } catch (\Throwable $e) {
        return new WP_Error('imap_error', $e->getMessage());
    }
}

/**
 * Entfernt zitierten Text ab der ersten erkannten Zitat-Kopfzeile
 * ("Am ... schrieb ...:", "On ... wrote:", "-----Ursprüngliche Nachricht-----").
 * Bewusst simple Heuristik, kein Anspruch auf 100% saubere Trennung.
 */
function tsvd_anfragen_imap_strip_quote($body) {
    $patterns = array(
        '/^Am .{0,80} schrieb .{0,120}:$/mu',
        '/^On .{0,80} wrote:$/mu',
        '/^-{2,}\s*(Ursprüngliche Nachricht|Original Message)\s*-{2,}$/mu',
        '/^>.*$/mu',
    );
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $body, $m, PREG_OFFSET_CAPTURE)) {
            $body = substr($body, 0, $m[0][1]);
        }
    }
    return trim($body);
}

add_action('tsvd_anfragen_check_imap', 'tsvd_anfragen_imap_poll');

function tsvd_anfragen_imap_poll() {
    $s = tsvd_anfragen_imap_get_settings();
    if (!$s['enabled']) {
        return;
    }

    $client = tsvd_anfragen_imap_make_client();
    if (is_wp_error($client)) {
        error_log('TSVD Anfragen IMAP: ' . $client->get_error_message());
        return;
    }

    global $wpdb;
    $table = tsvd_anfragen_table_name();

    try {
        $folder = $client->getFolder($s['folder']);
        $messages = $folder->messages()->whereUnseen()->get();

        foreach ($messages as $message) {
            $subject = (string) $message->subject->first();

            // Referenz-Token "#<id>" aus dem Betreff der Antwort-Mail
            // (siehe tsvd_ajax_anfrage_reply()) — ohne erkennbaren Bezug bleibt
            // die Nachricht ungelesen, für manuelle Sichtung im echten Postfach.
            if (!preg_match('/#(\d+)/', $subject, $m)) {
                continue;
            }
            $anfrage_id = (int) $m[1];

            $anfrage = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $anfrage_id), ARRAY_A);
            if (!$anfrage) {
                continue;
            }

            // Nur akzeptieren, wenn der Absender zur hinterlegten Anfrage passt —
            // verhindert, dass eine beliebige Mail mit passender Betreffzeile
            // fälschlich einer fremden Anfrage zugeordnet wird.
            $sender_address = $message->from->first();
            $sender = $sender_address ? strtolower((string) $sender_address->mail) : '';
            if ($sender === '' || $sender !== strtolower((string) $anfrage['applicant_email'])) {
                continue;
            }

            $body = trim((string) $message->getTextBody());
            if ($body === '') {
                $body = trim(wp_strip_all_tags((string) $message->getHTMLBody()));
            }
            $body = tsvd_anfragen_imap_strip_quote($body);
            if ($body === '') {
                $body = __('(Leerer Antworttext, siehe Original-Mail im Postfach)', 'tsv-tools');
            }

            $now = current_time('mysql');
            $wpdb->insert(
                tsvd_anfragen_replies_table_name(),
                array(
                    'anfrage_id' => $anfrage_id,
                    'user_id'    => null,
                    'direction'  => 'in',
                    'body'       => $body,
                    'sent_at'    => $now,
                ),
                array('%d', '%d', '%s', '%s', '%s')
            );

            $wpdb->update(
                $table,
                array('status' => 'in_progress', 'updated_at' => $now),
                array('id' => $anfrage_id),
                array('%s', '%s'),
                array('%d')
            );

            $message->setFlag('Seen');
        }

        $client->disconnect();
    } catch (\Throwable $e) {
        error_log('TSVD Anfragen IMAP poll error: ' . $e->getMessage());
    }
}
