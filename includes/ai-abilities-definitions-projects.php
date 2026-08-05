<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_get_ability_definitions_projects() {
    return array(
        'tsv-tools/create-project' => array(
            'label'               => __('Projekt anlegen', 'tsv-tools'),
            'description'         => __('Legt ein neues Projekt (Projects CPT) als Entwurf an.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'title'   => array('type' => 'string', 'description' => 'Projekttitel'),
                    'content' => array('type' => 'string', 'description' => 'Beitragsinhalt als HTML (Absätze, Überschriften, Listen).'),
                    'status'  => array('type' => 'string', 'enum' => array('draft', 'publish'), 'description' => 'Standard: draft.'),
                ),
                'required'   => array('title', 'content'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'id'       => array('type' => 'integer'),
                    'status'   => array('type' => 'string'),
                    'edit_url' => array('type' => 'string'),
                    'view_url' => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_projects',
            'execute_callback'    => 'tsvd_tools_ai_create_project',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => false),
            ),
        ),

        'tsv-tools/update-project' => array(
            'label'               => __('Projekt aktualisieren', 'tsv-tools'),
            'description'         => __('Aktualisiert Titel und/oder Inhalt eines bestehenden Projekts (Projects CPT). Lässt Bilder, Meilensteine, Assets und Social-Links unangetastet.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'id'      => array('type' => 'integer', 'description' => 'Projekt-ID'),
                    'title'   => array('type' => 'string', 'description' => 'Neuer Titel (optional).'),
                    'content' => array('type' => 'string', 'description' => 'Neuer Beitragsinhalt als HTML (optional).'),
                ),
                'required'   => array('id'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'id'       => array('type' => 'integer'),
                    'status'   => array('type' => 'string'),
                    'edit_url' => array('type' => 'string'),
                    'view_url' => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_projects',
            'execute_callback'    => 'tsvd_tools_ai_update_project',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
            ),
        ),
    );
}
