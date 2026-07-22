<?php
// AI Abilities — definitions added 2026-07-15: delete-animal, list-redirects,
// get-form-stats. Kept separate from ai-abilities-definitions.php so that file
// stays within the config-file line limit.

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_get_ability_definitions_extra() {
    return array(
        'tsv-tools/delete-animal' => array(
            'label'               => __('Tier löschen', 'tsv-tools'),
            'description'         => __('Löscht ein Tier (Standard: in den Papierkorb, optional endgültig).', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'id'        => array('type' => 'integer'),
                    'permanent' => array('type' => 'boolean', 'default' => false, 'description' => 'true = endgültig löschen statt Papierkorb'),
                ),
                'required'   => array('id'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'id'        => array('type' => 'integer'),
                    'permanent' => array('type' => 'boolean'),
                    'status'    => array('type' => 'string'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_delete_animal',
            'execute_callback'    => 'tsvd_tools_ai_delete_animal',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => true, 'idempotent' => true),
            ),
        ),

        'tsv-tools/list-redirects' => array(
            'label'               => __('Redirects auflisten', 'tsv-tools'),
            'description'         => __('Listet die über Route301 konfigurierten Redirects für eine Domain (Default tierheim-duesseldorf.de).', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'domain' => array('type' => 'string', 'description' => 'Default: tierheim-duesseldorf.de'),
                ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'redirects' => array('type' => 'array'),
                    'total'     => array('type' => 'integer'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_list_redirects',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => true, 'destructive' => false, 'idempotent' => true),
            ),
        ),

        'tsv-tools/get-form-stats' => array(
            'label'               => __('Formular-Statistik abfragen', 'tsv-tools'),
            'description'         => __('Fragt die echte (anonyme) Formular-/Interesse-Statistik ab. dimension: request_type|residence|housing|outdoor|period|top_animals|species_term|breed_term|adoption_status|adoption_status_by_month|adoption_source.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'dimension' => array('type' => 'string', 'default' => 'request_type'),
                    'period'    => array('type' => 'string', 'description' => 'nur bei dimension=period: day|month|year'),
                    'limit'     => array('type' => 'integer', 'description' => 'nur bei dimension=top_animals oder adoption_status_by_month, Default 10 bzw. 12'),
                ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'dimension' => array('type' => 'string'),
                    'rows'      => array('type' => 'array'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_animals',
            'execute_callback'    => 'tsvd_tools_ai_get_form_stats',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => true, 'destructive' => false, 'idempotent' => true),
            ),
        ),

        'tsv-tools/reset-form-stats' => array(
            'label'               => __('Formular-Statistik zurücksetzen', 'tsv-tools'),
            'description'         => __('Setzt die Anfragen-/Interesse-Statistik zurück (TRUNCATE Zähler-Tabelle + animal_interest_form_count auf 0 bei allen Tieren). Betrifft NICHT den Vermittlungsstatus/animal_adoption_status echter Tiere. Nicht rückgängig zu machen — Bestätigungsfeld erforderlich.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'confirm' => array('type' => 'boolean', 'description' => 'Muss true sein, sonst Fehler.'),
                ),
                'required'             => array('confirm'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'reset_animals' => array('type' => 'integer'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_reset_form_stats',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => true, 'idempotent' => true),
            ),
        ),

        'tsv-tools/regenerate-project-images' => array(
            'label'               => __('Projekt-Bilder neu generieren', 'tsv-tools'),
            'description'         => __('Regeneriert alle Bildgrößen (u.a. tsvd-card) für die Desktop-/Mobil-/Impressionsbilder aller Projekte, damit ein geänderter add_image_size-Zuschnitt auch für bereits hochgeladene Bilder greift.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'                 => 'object',
                'properties'           => array(),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'total'   => array('type' => 'integer'),
                    'results' => array('type' => 'array'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_regenerate_project_images',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
            ),
        ),
    );
}
