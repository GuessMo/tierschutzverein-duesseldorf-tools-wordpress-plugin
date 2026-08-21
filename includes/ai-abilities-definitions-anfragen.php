<?php
// AI Abilities — Definitionen für die Anfragen-Verwaltung per MCP, 2026-08-04.

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_can_manage_anfragen() {
    return current_user_can('manage_tsvd_anfragen');
}

function tsvd_tools_ai_get_ability_definitions_anfragen() {
    $anfrage_object = array(
        'type'       => 'object',
        'properties' => array(
            'id'              => array('type' => 'integer'),
            'form_id'         => array('type' => 'integer'),
            'form_title'      => array('type' => array('string', 'null')),
            'animal_id'       => array('type' => array('integer', 'null')),
            'applicant_name'  => array('type' => 'string'),
            'applicant_email' => array('type' => 'string'),
            'applicant_phone' => array('type' => 'string'),
            'status'          => array('type' => 'string'),
            'created_at'      => array('type' => 'string'),
            'updated_at'      => array('type' => 'string'),
        ),
    );

    return array(
        'tsv-tools/list-anfragen' => array(
            'label'               => __('Anfragen auflisten', 'tsv-tools'),
            'description'         => __('Listet Anfragen aus dem Anfragen-Dashboard, optional gefiltert nach Status (open|answered|spam).', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'status' => array('type' => 'string', 'description' => 'open|answered|spam, leer = alle'),
                    'limit'  => array('type' => 'integer', 'default' => 50, 'description' => 'max. 200'),
                ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'count'    => array('type' => 'integer'),
                    'anfragen' => array('type' => 'array', 'items' => $anfrage_object),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_anfragen',
            'execute_callback'    => 'tsvd_tools_ai_list_anfragen',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => true, 'destructive' => false, 'idempotent' => true),
            ),
        ),

        'tsv-tools/get-anfrage' => array(
            'label'               => __('Anfrage abrufen', 'tsv-tools'),
            'description'         => __('Gibt eine einzelne Anfrage mit vollständigen Angaben und Antwort-Verlauf zurück.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array('id' => array('type' => 'integer')),
                'required'   => array('id'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array('type' => 'object'),
            'permission_callback' => 'tsvd_tools_ai_can_manage_anfragen',
            'execute_callback'    => 'tsvd_tools_ai_get_anfrage',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => true, 'destructive' => false, 'idempotent' => true),
            ),
        ),

        'tsv-tools/update-anfrage-status' => array(
            'label'               => __('Anfragen-Status ändern', 'tsv-tools'),
            'description'         => __('Setzt den Status einer Anfrage (open|answered|spam).', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'id'     => array('type' => 'integer'),
                    'status' => array('type' => 'string', 'enum' => array('open', 'answered', 'spam')),
                ),
                'required'   => array('id', 'status'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'updated' => array('type' => 'boolean'),
                    'id'      => array('type' => 'integer'),
                    'status'  => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_anfragen',
            'execute_callback'    => 'tsvd_tools_ai_update_anfrage_status',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
            ),
        ),

        'tsv-tools/delete-anfrage' => array(
            'label'               => __('Anfrage löschen', 'tsv-tools'),
            'description'         => __('Löscht eine Anfrage inkl. ihres Antwort-Verlaufs endgültig (kein Papierkorb).', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array('id' => array('type' => 'integer')),
                'required'   => array('id'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'deleted' => array('type' => 'boolean'),
                    'id'      => array('type' => 'integer'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_anfragen',
            'execute_callback'    => 'tsvd_tools_ai_delete_anfrage',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => true, 'idempotent' => true),
            ),
        ),

        'tsv-tools/set-form-mail-recipient' => array(
            'label'               => __('Formular-Mail-Empfänger setzen', 'tsv-tools'),
            'description'         => __('Setzt den Mail-Empfänger (_tsvd_form_recipient) eines bestehenden tsvd_form — entweder per form_id oder per exaktem Formular-Titel.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'form_id'   => array('type' => 'integer', 'description' => 'ID eines bestehenden tsvd_form'),
                    'title'     => array('type' => 'string', 'description' => 'Alternative zu form_id: exakter Formular-Titel'),
                    'recipient' => array('type' => 'string', 'description' => 'Neue Empfänger-Mailadresse'),
                ),
                'required'   => array('recipient'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'updated'   => array('type' => 'boolean'),
                    'form_id'   => array('type' => 'integer'),
                    'title'     => array('type' => 'string'),
                    'recipient' => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_anfragen',
            'execute_callback'    => 'tsvd_tools_ai_set_form_mail_recipient',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
            ),
        ),

        'tsv-tools/set-anfragen-imap-settings' => array(
            'label'               => __('Anfragen-IMAP-Einstellungen setzen', 'tsv-tools'),
            'description'         => __('Aktualisiert die IMAP-Einstellungen des Anfragen-Rückkanals (Host/Port/Mailbox-Adresse/Ordner/Aktiv) — bewusst OHNE Passwort-Feld, das App-Passwort muss weiterhin manuell in wp-admin hinterlegt werden. Nicht übergebene Felder bleiben unverändert.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'enabled'  => array('type' => 'boolean'),
                    'host'     => array('type' => 'string'),
                    'port'     => array('type' => 'integer'),
                    'username' => array('type' => 'string', 'description' => 'Mailbox-Adresse, z.B. tieranfragen@tierschutzverein-duesseldorf.de'),
                    'folder'   => array('type' => 'string'),
                ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'updated'  => array('type' => 'boolean'),
                    'enabled'  => array('type' => 'boolean'),
                    'host'     => array('type' => 'string'),
                    'port'     => array('type' => 'integer'),
                    'username' => array('type' => 'string'),
                    'folder'   => array('type' => 'string'),
                    'password_set' => array('type' => 'boolean', 'description' => 'Ob bereits ein App-Passwort hinterlegt ist (Wert selbst wird nie zurückgegeben)'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_anfragen',
            'execute_callback'    => 'tsvd_tools_ai_set_anfragen_imap_settings',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
            ),
        ),

        'tsv-tools/reply-to-anfrage' => array(
            'label'               => __('Auf Anfrage antworten', 'tsv-tools'),
            'description'         => __('Sendet eine Antwort-Mail an die interessierte Person (Reply-To = Formular-Empfänger, Betreff mit Anfragen-Nummer) und speichert sie im Verlauf; setzt Status auf "answered".', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'id'   => array('type' => 'integer'),
                    'body' => array('type' => 'string'),
                ),
                'required'   => array('id', 'body'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'sent' => array('type' => 'boolean'),
                    'id'   => array('type' => 'integer'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_anfragen',
            'execute_callback'    => 'tsvd_tools_ai_reply_to_anfrage',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => false),
            ),
        ),
    );
}
