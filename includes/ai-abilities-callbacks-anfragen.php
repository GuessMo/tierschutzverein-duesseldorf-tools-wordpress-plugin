<?php
// AI Abilities — Anfragen-Dashboard per MCP verwalten (list/get/update-status/
// delete/reply), 2026-08-04. Spiegelt die wp-admin-Aktionen aus anfragen-admin.php
// / anfragen-admin-detail.php, damit Anfragen auch ohne wp-admin-Session bearbeitet
// werden können.

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_format_anfrage_row($row) {
    $form_title = get_the_title((int) $row['form_id']);
    return array(
        'id'              => (int) $row['id'],
        'form_id'         => (int) $row['form_id'],
        'form_title'      => $form_title ?: null,
        'animal_id'       => $row['animal_id'] ? (int) $row['animal_id'] : null,
        'applicant_name'  => $row['applicant_name'],
        'applicant_email' => $row['applicant_email'],
        'applicant_phone' => $row['applicant_phone'],
        'status'          => $row['status'],
        'created_at'      => $row['created_at'],
        'updated_at'      => $row['updated_at'],
    );
}

function tsvd_tools_ai_list_anfragen($input) {
    global $wpdb;
    $table  = tsvd_anfragen_table_name();
    $status = isset($input['status']) ? sanitize_key($input['status']) : '';
    $limit  = isset($input['limit']) ? min(200, max(1, absint($input['limit']))) : 50;

    if ($status !== '') {
        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} WHERE status = %s ORDER BY created_at DESC LIMIT %d", $status, $limit), ARRAY_A);
    } else {
        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d", $limit), ARRAY_A);
    }

    return array(
        'count'    => count($rows),
        'anfragen' => array_map('tsvd_tools_ai_format_anfrage_row', $rows),
    );
}

function tsvd_tools_ai_get_anfrage($input) {
    $id = absint($input['id'] ?? 0);
    if (!$id) {
        return new WP_Error('missing_id', __('id ist erforderlich.', 'tsv-tools'));
    }

    global $wpdb;
    $table = tsvd_anfragen_table_name();
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d", $id), ARRAY_A);
    if (!$row) {
        return new WP_Error('not_found', __('Anfrage nicht gefunden.', 'tsv-tools'));
    }

    $replies_table = tsvd_anfragen_replies_table_name();
    $replies = $wpdb->get_results($wpdb->prepare("SELECT id, user_id, direction, body, sent_at FROM {$replies_table} WHERE anfrage_id = %d ORDER BY sent_at ASC", $id), ARRAY_A);

    $payload = json_decode($row['payload'], true);
    $fields = function_exists('tsvd_get_form_fields') ? tsvd_get_form_fields((int) $row['form_id']) : array();
    $angaben = array();
    foreach ($fields as $field) {
        $field_id = $field['id'];
        if (!isset($payload[$field_id]) || $payload[$field_id] === '') {
            continue;
        }
        $val = $payload[$field_id];
        if (is_array($val)) {
            $val = implode(', ', $val);
        }
        $angaben[] = array(
            'label' => !empty($field['label']) ? $field['label'] : $field_id,
            'value' => $val,
        );
    }

    $formatted = tsvd_tools_ai_format_anfrage_row($row);
    $formatted['angaben'] = $angaben;
    $formatted['replies'] = array_map(function ($r) {
        return array(
            'id'        => (int) $r['id'],
            'direction' => $r['direction'],
            'author'    => $r['user_id'] ? get_the_author_meta('display_name', $r['user_id']) : null,
            'body'      => $r['body'],
            'sent_at'   => $r['sent_at'],
        );
    }, $replies);

    return $formatted;
}

function tsvd_tools_ai_update_anfrage_status($input) {
    $id     = absint($input['id'] ?? 0);
    $status = sanitize_key($input['status'] ?? '');
    $valid  = array('new', 'in_progress', 'answered', 'closed');

    if (!$id) {
        return new WP_Error('missing_id', __('id ist erforderlich.', 'tsv-tools'));
    }
    if (!in_array($status, $valid, true)) {
        return new WP_Error('invalid_status', sprintf(__('status muss einer von: %s sein.', 'tsv-tools'), implode(', ', $valid)));
    }

    global $wpdb;
    $table = tsvd_anfragen_table_name();
    $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id));
    if (!$existing) {
        return new WP_Error('not_found', __('Anfrage nicht gefunden.', 'tsv-tools'));
    }

    $wpdb->update(
        $table,
        array('status' => $status, 'updated_at' => current_time('mysql')),
        array('id' => $id),
        array('%s', '%s'),
        array('%d')
    );

    return array('updated' => true, 'id' => $id, 'status' => $status);
}

function tsvd_tools_ai_delete_anfrage($input) {
    $id = absint($input['id'] ?? 0);
    if (!$id) {
        return new WP_Error('missing_id', __('id ist erforderlich.', 'tsv-tools'));
    }

    global $wpdb;
    $table = tsvd_anfragen_table_name();
    $existing = $wpdb->get_var($wpdb->prepare("SELECT id FROM {$table} WHERE id = %d", $id));
    if (!$existing) {
        return new WP_Error('not_found', __('Anfrage nicht gefunden.', 'tsv-tools'));
    }

    $wpdb->delete(tsvd_anfragen_replies_table_name(), array('anfrage_id' => $id), array('%d'));
    $wpdb->delete($table, array('id' => $id), array('%d'));

    return array('deleted' => true, 'id' => $id);
}

function tsvd_tools_ai_set_form_mail_recipient($input) {
    $recipient = sanitize_email($input['recipient'] ?? '');
    if (!is_email($recipient)) {
        return new WP_Error('invalid_recipient', __('recipient ist keine gültige Mailadresse.', 'tsv-tools'));
    }

    $form_id = absint($input['form_id'] ?? 0);
    $title   = sanitize_text_field($input['title'] ?? '');

    if (!$form_id && $title !== '') {
        $found = get_posts(array(
            'post_type'      => 'tsvd_form',
            'title'          => $title,
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ));
        $form_id = $found ? (int) $found[0] : 0;
    }

    if (!$form_id) {
        return new WP_Error('missing_form', __('form_id oder ein bestehender title ist erforderlich.', 'tsv-tools'));
    }
    if (get_post_type($form_id) !== 'tsvd_form') {
        return new WP_Error('invalid_form', __('form_id verweist auf kein bestehendes tsvd_form.', 'tsv-tools'));
    }

    update_post_meta($form_id, '_tsvd_form_recipient', $recipient);

    return array(
        'updated'   => true,
        'form_id'   => $form_id,
        'title'     => get_the_title($form_id),
        'recipient' => $recipient,
    );
}

function tsvd_tools_ai_set_anfragen_imap_settings($input) {
    if (isset($input['enabled'])) {
        update_option('tsvd_anfragen_imap_enabled', (bool) $input['enabled'] ? 1 : 0);
    }
    if (isset($input['host'])) {
        update_option('tsvd_anfragen_imap_host', sanitize_text_field($input['host']));
    }
    if (isset($input['port'])) {
        update_option('tsvd_anfragen_imap_port', absint($input['port']));
    }
    if (isset($input['username'])) {
        $username = sanitize_email($input['username']);
        if (!is_email($username)) {
            return new WP_Error('invalid_username', __('username ist keine gültige Mailadresse.', 'tsv-tools'));
        }
        update_option('tsvd_anfragen_imap_username', $username);
    }
    if (isset($input['folder'])) {
        update_option('tsvd_anfragen_imap_folder', sanitize_text_field($input['folder']));
    }

    $s = function_exists('tsvd_anfragen_imap_get_settings') ? tsvd_anfragen_imap_get_settings() : array();

    return array(
        'updated'      => true,
        'enabled'      => (bool) ($s['enabled'] ?? false),
        'host'         => (string) ($s['host'] ?? ''),
        'port'         => (int) ($s['port'] ?? 0),
        'username'     => (string) ($s['username'] ?? ''),
        'folder'       => (string) ($s['folder'] ?? ''),
        'password_set' => '' !== ($s['password'] ?? ''),
    );
}

function tsvd_tools_ai_reply_to_anfrage($input) {
    $id   = absint($input['id'] ?? 0);
    $body = isset($input['body']) ? sanitize_textarea_field($input['body']) : '';

    if (!$id) {
        return new WP_Error('missing_id', __('id ist erforderlich.', 'tsv-tools'));
    }
    if (!function_exists('tsvd_anfragen_send_reply')) {
        return new WP_Error('missing_function', __('Reply-Funktion nicht geladen.', 'tsv-tools'));
    }

    // user_id 0 = kein WP-Benutzer (MCP-Aufruf), im Verlauf als "Unbekannt"/System
    // sichtbar statt einem echten Redakteur zugeschrieben zu werden.
    $result = tsvd_anfragen_send_reply($id, $body, 0);
    if (is_wp_error($result)) {
        return $result;
    }

    return array('sent' => true, 'id' => $id);
}
