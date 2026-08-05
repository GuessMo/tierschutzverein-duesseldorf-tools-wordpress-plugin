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
            'description'         => __('Aktualisiert Titel, Inhalt und/oder Bilder eines bestehenden Projekts (Projects CPT). Lässt Meilensteine, Assets und Social-Links unangetastet.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'id'             => array('type' => 'integer', 'description' => 'Projekt-ID'),
                    'title'          => array('type' => 'string', 'description' => 'Neuer Titel (optional).'),
                    'content'        => array('type' => 'string', 'description' => 'Beitragsinhalt als HTML (optional). Wird ersetzt, ausser append=true.'),
                    'append'         => array('type' => 'boolean', 'description' => 'true: content ans bestehende Ende anhaengen statt ersetzen (fuer sehr lange Texte in mehreren Aufrufen).'),
                    'desktop_image'  => array('type' => 'integer', 'description' => 'Attachment-ID fuer das Hero-Desktop-Bild (optional). 0 entfernt es.'),
                    'mobile_image'   => array('type' => 'integer', 'description' => 'Attachment-ID fuer das Hero-Mobile-Bild (optional). 0 entfernt es.'),
                    'logo_image'     => array('type' => 'integer', 'description' => 'Attachment-ID fuer das kleine Logo-Bild (optional, Alternative zum Hero-Bild). 0 entfernt es.'),
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

        'tsv-tools/set-projects-max-lines' => array(
            'label'               => __('Zeilenlimit für Projekt-Beschreibungen setzen', 'tsv-tools'),
            'description'         => __('Setzt das globale Zeilenlimit (tsvd_projects_description_max_lines), ab dem das Beschreibungsfeld der Projects CPT beim Speichern gekürzt wird.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'max_lines' => array('type' => 'integer', 'description' => 'Neues Zeilenlimit (> 0).'),
                ),
                'required'   => array('max_lines'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'max_lines' => array('type' => 'integer'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_projects_settings',
            'execute_callback'    => 'tsvd_tools_ai_set_projects_max_lines',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
            ),
        ),

        'tsv-tools/add-project-milestone' => array(
            'label'               => __('Projekt-Meilenstein hinzufügen', 'tsv-tools'),
            'description'         => __('Fügt einem bestehenden Projekt einen neuen Meilenstein an (Repeater-Feld).', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'id'           => array('type' => 'integer', 'description' => 'Projekt-ID'),
                    'title'        => array('type' => 'string', 'description' => 'Titel des Meilensteins'),
                    'description'  => array('type' => 'string', 'description' => 'Beschreibungstext (Klartext, kein HTML). Wird auf der Karte per wpautop in Absaetze umgewandelt.'),
                    'date'         => array('type' => 'string', 'description' => 'Datum im Format YYYY-MM-DD.'),
                    'status'       => array('type' => 'string', 'enum' => array('pending', 'in_progress', 'completed'), 'description' => 'Standard: pending.'),
                    'progress'     => array('type' => 'integer', 'description' => 'Fortschritt in Prozent (0-100). 0 blendet die Fortschrittsanzeige aus.'),
                    'image'        => array('type' => 'integer', 'description' => 'Attachment-ID fuer das Meilenstein-Bild (optional).'),
                    'publish_at'   => array('type' => 'string', 'description' => 'Optionaler Zeitplan (YYYY-MM-DDTHH:MM). Leer = sofort sichtbar.'),
                    'show_in_news' => array('type' => 'boolean', 'description' => 'true: Meilenstein erscheint zusaetzlich als eigene Karte im Aktuelles-Slider (verlinkt zur Projektseite, kein eigener Beitrag).'),
                ),
                'required'   => array('id', 'title'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'id'              => array('type' => 'integer'),
                    'milestone_count' => array('type' => 'integer'),
                    'edit_url'        => array('type' => 'string'),
                    'view_url'        => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_projects',
            'execute_callback'    => 'tsvd_tools_ai_add_project_milestone',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => false),
            ),
        ),

        'tsv-tools/update-project-milestone' => array(
            'label'               => __('Projekt-Meilenstein aktualisieren', 'tsv-tools'),
            'description'         => __('Aktualisiert einzelne Felder eines bestehenden Meilensteins, gefunden per exaktem Titel-Match.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'id'           => array('type' => 'integer', 'description' => 'Projekt-ID'),
                    'match_title'  => array('type' => 'string', 'description' => 'Exakter aktueller Titel des zu aendernden Meilensteins.'),
                    'title'        => array('type' => 'string', 'description' => 'Neuer Titel (optional).'),
                    'description'  => array('type' => 'string', 'description' => 'Neue Beschreibung (optional, Klartext).'),
                    'date'         => array('type' => 'string', 'description' => 'Neues Datum im Format YYYY-MM-DD (optional).'),
                    'status'       => array('type' => 'string', 'enum' => array('pending', 'in_progress', 'completed'), 'description' => 'Neuer Status (optional).'),
                    'progress'     => array('type' => 'integer', 'description' => 'Neuer Fortschritt in Prozent (optional).'),
                    'image'        => array('type' => 'integer', 'description' => 'Neue Attachment-ID fuer das Bild (optional).'),
                    'publish_at'   => array('type' => 'string', 'description' => 'Neuer Zeitplan (optional).'),
                    'show_in_news' => array('type' => 'boolean', 'description' => 'Neuer Aktuelles-Sync-Status (optional).'),
                ),
                'required'   => array('id', 'match_title'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'id'       => array('type' => 'integer'),
                    'index'    => array('type' => 'integer'),
                    'edit_url' => array('type' => 'string'),
                    'view_url' => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_projects',
            'execute_callback'    => 'tsvd_tools_ai_update_project_milestone',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
            ),
        ),
    );
}
