<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_get_ability_definitions_sync() {
    return array(
        'tsv-tools/export-content-delta' => array(
            'label'               => __('Inhalte exportieren (Delta)', 'tsv-tools'),
            'description'         => __('Exportiert Inhalte als Manifest für den Live→Lokal-Abgleich: gewählte Post-Types (Default projects, tsvd_form, page, post, animals, tsvd_newsletter, tsvd_update) mit Meta, Taxonomien und Medien-Referenzen (inkl. URL). Inkrementell per since (post_modified_gmt). KEINE Personendaten (Anfragen/User/Newsletter-Abonnenten ausgeschlossen). Medien-Dateien werden per URL referenziert, nicht mitgeliefert.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'since'      => array('type' => 'string', 'description' => 'ISO-8601-Zeitstempel (GMT); nur seither geänderte Inhalte. Leer = alles.'),
                    'post_types' => array(
                        'type'  => 'array',
                        'items' => array('type' => 'string'),
                        'description' => 'Zu exportierende Post-Types. Default: projects, tsvd_form, page, post, animals. tsvd_anfragen/attachment/revision werden immer ausgeschlossen.',
                    ),
                    'with_media' => array('type' => 'boolean', 'default' => true),
                    'limit'      => array('type' => 'integer', 'default' => 300, 'description' => 'Max. Posts pro Aufruf (1-500).'),
                ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array('type' => 'object'),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_export_content_delta',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => true, 'destructive' => false, 'idempotent' => true),
            ),
        ),
    );
}
