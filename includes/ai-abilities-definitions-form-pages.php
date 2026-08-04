<?php
// AI Abilities — Definitionen für Formular-Seiten, 2026-08-04.

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_get_ability_definitions_form_pages() {
    return array(
        'tsv-tools/create-form-page' => array(
            'label'               => __('Formular-Seite anlegen', 'tsv-tools'),
            'description'         => __('Legt eine öffentliche /tiere/<slug>/-Seite an, die statt eines Tierprofils ein bestehendes Formular zeigt (z. B. für Aktionen vor Livegang der neuen Website).', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'title'   => array('type' => 'string', 'description' => 'Seitentitel'),
                    'slug'    => array('type' => 'string', 'description' => 'URL-Teil unter /tiere/'),
                    'form_id' => array('type' => 'integer', 'description' => 'ID eines bestehenden tsvd_form'),
                ),
                'required'   => array('title', 'slug', 'form_id'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'created'   => array('type' => 'boolean'),
                    'animal_id' => array('type' => 'integer'),
                    'page_url'  => array('type' => 'string'),
                    'message'   => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_create_form_page',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => false),
            ),
        ),

        'tsv-tools/add-maintenance-allowed-path' => array(
            'label'               => __('Wartungsmodus-Ausnahme hinzufügen', 'tsv-tools'),
            'description'         => __('Fügt einen Pfad zur Wartungsmodus-Ausnahmeliste hinzu (additiv, idempotent) — diese URL bleibt dann für anonyme Besucher erreichbar, auch wenn der Rest der Website im Wartungsmodus ist.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'path' => array('type' => 'string', 'description' => 'Relativer Pfad, z. B. /tiere/interessentenbogen-tiervermittlung'),
                ),
                'required'   => array('path'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'added'   => array('type' => 'boolean'),
                    'paths'   => array('type' => 'array'),
                    'message' => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_add_maintenance_allowed_path',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
            ),
        ),
    );
}
