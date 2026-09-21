<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_get_ability_definitions_content() {
    return array(
        'tsv-tools/create-update' => array(
            'label'               => __('Website-Update anlegen', 'tsv-tools'),
            'description'         => __('Legt einen Website-Update-Eintrag (tsvd_update) an — interner Changelog/Dashboard und Quelle des dynamischen Newsletter-Blocks. Eintraege sind immer intern und werden fortlaufend nummeriert. status draft|publish.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'title'   => array('type' => 'string', 'description' => 'Titel des Updates.'),
                    'content' => array('type' => 'string', 'description' => 'Inhalt als HTML.'),
                    'status'  => array('type' => 'string', 'enum' => array('draft', 'publish'), 'description' => 'Standard draft.'),
                ),
                'required'   => array('title'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'id'       => array('type' => 'integer'),
                    'status'   => array('type' => 'string'),
                    'edit_url' => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_website_updates',
            'execute_callback'    => 'tsvd_tools_ai_create_website_update',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => false),
            ),
        ),

        'tsv-tools/set-project-form' => array(
            'label'               => __('Projekt-Formular zuordnen', 'tsv-tools'),
            'description'         => __('Setzt das am Ende der Projektseite angezeigte Formular (project_form_id) eines Projekts (Projects CPT). form_id 0 entfernt die Zuordnung.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'id'      => array('type' => 'integer', 'description' => 'Projekt-ID (Projects CPT).'),
                    'form_id' => array('type' => 'integer', 'description' => 'tsvd_form-ID; 0 entfernt die Zuordnung.'),
                ),
                'required'   => array('id', 'form_id'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'id'       => array('type' => 'integer'),
                    'form_id'  => array('type' => 'integer'),
                    'view_url' => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_projects',
            'execute_callback'    => 'tsvd_tools_ai_set_project_form',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
            ),
        ),
    );
}
