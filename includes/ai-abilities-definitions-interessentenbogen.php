<?php
// AI Abilities — Definitionen für das Interessentenbogen-Formular, 2026-08-04.
// Eigene Datei aus demselben Grund wie ai-abilities-definitions-extra.php
// (Config-Datei-Zeilenobergrenze der Haupt-Datei).

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_get_ability_definitions_interessentenbogen() {
    return array(
        'tsv-tools/create-interessentenbogen-form' => array(
            'label'               => __('Interessentenbogen-Formular anlegen', 'tsv-tools'),
            'description'         => __('Legt das ausführliche Interessentenbogen-Formular (tsvd_form, ~40 Felder) für die Tiervermittlung an, verknüpft es in den Einstellungen und aktiviert die Anfragen-Speicherung.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'recipient_email' => array('type' => 'string', 'description' => 'E-Mail für Benachrichtigungen (Default: admin_email)'),
                    'force'           => array('type' => 'boolean', 'default' => false, 'description' => 'Felder neu schreiben, auch wenn bereits ein Formular konfiguriert ist'),
                    'title'           => array('type' => 'string'),
                ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'created'        => array('type' => 'boolean'),
                    'form_id'        => array('type' => 'integer'),
                    'form_edit_link' => array('type' => 'string'),
                    'message'        => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_create_interessentenbogen_form',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
            ),
        ),
    );
}
