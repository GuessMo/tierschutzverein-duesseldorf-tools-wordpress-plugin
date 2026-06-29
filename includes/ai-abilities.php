<?php
// AI Abilities — registers WordPress Abilities (WP 6.9+) for the animals CPT
// so MCP clients can list, read, create and update animals.
// Requires the WordPress MCP Adapter plugin to expose them over MCP.

if (!defined('ABSPATH')) exit;

require_once TSVD_TOOLS_DIR . 'includes/ai-abilities-callbacks.php';

add_action('wp_abilities_api_categories_init', 'tsvd_tools_ai_register_category');
function tsvd_tools_ai_register_category() {
    if (!function_exists('wp_register_ability_category')) {
        return;
    }
    wp_register_ability_category('tsv-tools-animals', array(
        'label'       => __('TSV Tiere', 'tsv-tools'),
        'description' => __('Lesen und Verwalten von Tieren des Tierschutzvereins.', 'tsv-tools'),
    ));
}

add_action('wp_abilities_api_init', 'tsvd_tools_ai_register_abilities');
function tsvd_tools_ai_register_abilities() {
    if (!function_exists('wp_register_ability')) {
        return;
    }

    $animal_object = array(
        'type'       => 'object',
        'properties' => array(
            'id'              => array('type' => 'integer'),
            'title'           => array('type' => 'string'),
            'status'          => array('type' => 'string'),
            'name'            => array('type' => 'string'),
            'adoption_status' => array('type' => 'string'),
            'mediator'        => array('type' => array('string', 'null')),
            'missing_status'  => array('type' => array('string', 'null')),
            'edit_link'       => array('type' => 'string'),
        ),
    );

    wp_register_ability('tsv-tools/list-animals', array(
        'label'               => __('Tiere auflisten', 'tsv-tools'),
        'description'         => __('Listet Tiere mit optionalen Filtern (Vermittlungsstatus, Vermittler, Vermisst-Status, Suchbegriff).', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array(
                'adoption_status' => array('type' => 'string', 'description' => 'z. B. for_adoption, not_for_adoption, adopted'),
                'mediator'        => array('type' => 'string', 'description' => 'tierschutzverein oder privat'),
                'missing_status'  => array('type' => 'string', 'description' => 'missing, found oder reunited'),
                'search'          => array('type' => 'string'),
                'per_page'        => array('type' => 'integer', 'default' => 20),
            ),
            'additionalProperties' => false,
        ),
        'output_schema'       => array(
            'type'       => 'object',
            'properties' => array(
                'animals' => array('type' => 'array', 'items' => $animal_object),
                'total'   => array('type' => 'integer'),
            ),
        ),
        'permission_callback' => 'tsvd_tools_ai_can_manage_animals',
        'execute_callback'    => 'tsvd_tools_ai_list_animals',
        'meta'                => array('mcp' => array('public' => true)),
    ));

    wp_register_ability('tsv-tools/get-animal', array(
        'label'               => __('Tier abrufen', 'tsv-tools'),
        'description'         => __('Gibt ein einzelnes Tier mit seinen Feldern zurück.', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array('id' => array('type' => 'integer')),
            'required'   => array('id'),
            'additionalProperties' => false,
        ),
        'output_schema'       => $animal_object,
        'permission_callback' => 'tsvd_tools_ai_can_manage_animals',
        'execute_callback'    => 'tsvd_tools_ai_get_animal',
        'meta'                => array('mcp' => array('public' => true)),
    ));

    wp_register_ability('tsv-tools/create-animal', array(
        'label'               => __('Tier anlegen', 'tsv-tools'),
        'description'         => __('Legt ein neues Tier als Entwurf an.', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array(
                'name'            => array('type' => 'string'),
                'description'     => array('type' => 'string'),
                'adoption_status' => array('type' => 'string', 'default' => 'not_for_adoption'),
                'mediator'        => array('type' => 'string'),
                'missing_status'  => array('type' => 'string'),
            ),
            'required'   => array('name'),
            'additionalProperties' => false,
        ),
        'output_schema'       => $animal_object,
        'permission_callback' => 'tsvd_tools_ai_can_manage_animals',
        'execute_callback'    => 'tsvd_tools_ai_create_animal',
        'meta'                => array('mcp' => array('public' => true)),
    ));

    wp_register_ability('tsv-tools/update-animal', array(
        'label'               => __('Tier aktualisieren', 'tsv-tools'),
        'description'         => __('Aktualisiert Felder eines bestehenden Tieres.', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array(
                'id'              => array('type' => 'integer'),
                'name'            => array('type' => 'string'),
                'description'     => array('type' => 'string'),
                'adoption_status' => array('type' => 'string'),
                'mediator'        => array('type' => 'string'),
                'missing_status'  => array('type' => 'string'),
            ),
            'required'   => array('id'),
            'additionalProperties' => false,
        ),
        'output_schema'       => $animal_object,
        'permission_callback' => 'tsvd_tools_ai_can_manage_animals',
        'execute_callback'    => 'tsvd_tools_ai_update_animal',
        'meta'                => array('mcp' => array('public' => true)),
    ));
}
