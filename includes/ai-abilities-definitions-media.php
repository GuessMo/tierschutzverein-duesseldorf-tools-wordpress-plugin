<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_get_ability_definitions_media() {
    return array(
        'tsv-tools/upload-media' => array(
            'label'               => __('Medium hochladen', 'tsv-tools'),
            'description'         => __('Laedt eine Datei (Base64-kodiert) in die Medienbibliothek hoch und registriert sie als vollstaendiges Attachment (inkl. Bildgroessen).', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'filename'    => array('type' => 'string', 'description' => 'Dateiname inkl. Endung, z.B. logo.png'),
                    'file_base64' => array('type' => 'string', 'description' => 'Dateiinhalt Base64-kodiert (ohne data:-Praefix).'),
                    'title'       => array('type' => 'string', 'description' => 'Titel des Attachments (optional, Standard: Dateiname).'),
                    'alt_text'    => array('type' => 'string', 'description' => 'Alt-Text fuer Bilder (optional).'),
                ),
                'required'   => array('filename', 'file_base64'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'id'  => array('type' => 'integer'),
                    'url' => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_upload_media',
            'execute_callback'    => 'tsvd_tools_ai_upload_media',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => false),
            ),
        ),
    );
}
