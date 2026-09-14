<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_form_field_schema() {
    return array(
        'type'        => 'array',
        'description' => 'Formularfelder in Anzeige-Reihenfolge. Jedes Feld ist ein Objekt.',
        'items'       => array(
            'type'       => 'object',
            'properties' => array(
                'id'               => array('type' => 'string', 'description' => 'Eindeutige Feld-ID (a-z0-9_).'),
                'type'             => array('type' => 'string', 'description' => 'Feldtyp: text, email, tel, number, date, textarea, select, checkbox, radio, description (Info-/Überschriftsblock) u.a.'),
                'label'            => array('type' => 'string'),
                'placeholder'      => array('type' => 'string'),
                'required'         => array('type' => 'boolean', 'default' => false),
                'rows'             => array('type' => 'integer'),
                'min'              => array('type' => 'integer'),
                'max'              => array('type' => 'integer'),
                'maxlength'        => array('type' => 'integer'),
                'options'          => array(
                    'type'  => 'array',
                    'items' => array(
                        'type'       => 'object',
                        'properties' => array(
                            'value' => array('type' => 'string'),
                            'label' => array('type' => 'string'),
                        ),
                    ),
                    'description' => 'Optionen für select/checkbox/radio.',
                ),
                'group_id'         => array('type' => 'string', 'description' => 'Layout-Gruppe (Spalten-Container), optional.'),
                'group_column'     => array('type' => 'integer', 'default' => 0),
                'description_text'  => array('type' => 'string', 'description' => 'HTML-Text für type=description (z.B. <h3>Abschnitt</h3>).'),
                'show_acceptance'  => array('type' => 'boolean', 'description' => 'Bei type=description: Zustimmungs-Checkbox anzeigen.'),
                'acceptance_label' => array('type' => 'string'),
            ),
            'required' => array('id', 'type'),
        ),
    );
}

function tsvd_tools_ai_form_meta_properties() {
    return array(
        'recipient_email' => array('type' => 'string', 'description' => 'Mail-Empfänger für Einsendungen (Default: admin_email).'),
        'subject'         => array('type' => 'string', 'description' => 'Betreff der Benachrichtigungsmail.'),
        'success_message' => array('type' => 'string'),
        'show_title'      => array('type' => 'boolean', 'default' => true),
        'persist_inquiry' => array('type' => 'boolean', 'default' => false, 'description' => 'Einsendungen im Anfragen-Dashboard speichern.'),
        'groups_config'   => array(
            'type'        => 'array',
            'description' => 'Layout-Gruppen: [{id, columns, aligns}]. Optional.',
            'items'       => array('type' => 'object'),
        ),
    );
}

function tsvd_tools_ai_get_ability_definitions_forms() {
    $field_schema = tsvd_tools_ai_form_field_schema();
    $meta_props   = tsvd_tools_ai_form_meta_properties();

    return array(
        'tsv-tools/list-forms' => array(
            'label'               => __('Formulare auflisten', 'tsv-tools'),
            'description'         => __('Listet alle tsvd_form-Formulare (ID, Titel, Feldanzahl, Empfänger).', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'search' => array('type' => 'string', 'description' => 'Titel-Suchbegriff (optional).'),
                ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array('type' => 'object'),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_list_forms',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => true, 'destructive' => false, 'idempotent' => true),
            ),
        ),
        'tsv-tools/get-form' => array(
            'label'               => __('Formular abrufen', 'tsv-tools'),
            'description'         => __('Gibt ein Formular mit allen Feldern und Einstellungen zurück (per form_id).', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'form_id' => array('type' => 'integer'),
                ),
                'required'             => array('form_id'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array('type' => 'object'),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_get_form',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => true, 'destructive' => false, 'idempotent' => true),
            ),
        ),
        'tsv-tools/create-form' => array(
            'label'               => __('Formular anlegen', 'tsv-tools'),
            'description'         => __('Legt ein beliebiges Formular (tsvd_form) mit den übergebenen Feldern und Einstellungen an.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array_merge(
                    array(
                        'title'  => array('type' => 'string'),
                        'fields' => $field_schema,
                    ),
                    $meta_props
                ),
                'required'             => array('title', 'fields'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array('type' => 'object'),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_create_form',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => false),
            ),
        ),
        'tsv-tools/update-form' => array(
            'label'               => __('Formular bearbeiten', 'tsv-tools'),
            'description'         => __('Aktualisiert ein bestehendes Formular. Nur übergebene Felder/Einstellungen werden geändert.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array_merge(
                    array(
                        'form_id' => array('type' => 'integer'),
                        'title'   => array('type' => 'string'),
                        'fields'  => $field_schema,
                    ),
                    $meta_props
                ),
                'required'             => array('form_id'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array('type' => 'object'),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_update_form',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
            ),
        ),
        'tsv-tools/delete-form' => array(
            'label'               => __('Formular löschen', 'tsv-tools'),
            'description'         => __('Löscht ein Formular (Standard: Papierkorb, mit force=true endgültig).', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'form_id' => array('type' => 'integer'),
                    'force'   => array('type' => 'boolean', 'default' => false),
                ),
                'required'             => array('form_id'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array('type' => 'object'),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_delete_form',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => true, 'idempotent' => false),
            ),
        ),
    );
}
