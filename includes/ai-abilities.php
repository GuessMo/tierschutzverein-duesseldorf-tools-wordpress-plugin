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
                'last_seen_date'     => array('type' => 'string', 'description' => 'Vermisst: zuletzt gesehen (Datum)'),
                'last_seen_location' => array('type' => 'string', 'description' => 'Vermisst: zuletzt gesehen (Ort)'),
                'chip_number'        => array('type' => 'string', 'description' => 'Vermisst: Chipnummer'),
                'reward'             => array('type' => 'string', 'description' => 'Vermisst: Belohnung'),
                'image_url'          => array('type' => 'string', 'description' => 'Bild-URL, wird als Beitragsbild importiert'),
                'species'            => array('type' => 'string', 'description' => 'Tierart (animal_breed Elternterm, z.B. Katze) – wird bei Bedarf angelegt'),
                'breed'              => array('type' => 'string', 'description' => 'Rasse (animal_breed Kindterm, z.B. EKH) – wird bei Bedarf unter der Art angelegt'),
                'color'              => array('type' => 'string', 'description' => 'Farbe (animal_color) – wird bei Bedarf angelegt'),
                'gender'             => array('type' => 'string', 'description' => 'Geschlecht: female/male/unknown (weiblich/männlich werden gemappt)'),
                'size'               => array('type' => 'string', 'description' => 'Größe: small/middle/large (klein/mittel/groß werden gemappt)'),
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
                'last_seen_date'     => array('type' => 'string', 'description' => 'Vermisst: zuletzt gesehen (Datum)'),
                'last_seen_location' => array('type' => 'string', 'description' => 'Vermisst: zuletzt gesehen (Ort)'),
                'chip_number'        => array('type' => 'string', 'description' => 'Vermisst: Chipnummer'),
                'reward'             => array('type' => 'string', 'description' => 'Vermisst: Belohnung'),
                'image_url'          => array('type' => 'string', 'description' => 'Bild-URL, wird als Beitragsbild importiert'),
                'species'            => array('type' => 'string', 'description' => 'Tierart (animal_breed Elternterm, z.B. Katze) – wird bei Bedarf angelegt'),
                'breed'              => array('type' => 'string', 'description' => 'Rasse (animal_breed Kindterm, z.B. EKH) – wird bei Bedarf unter der Art angelegt'),
                'color'              => array('type' => 'string', 'description' => 'Farbe (animal_color) – wird bei Bedarf angelegt'),
                'gender'             => array('type' => 'string', 'description' => 'Geschlecht: female/male/unknown (weiblich/männlich werden gemappt)'),
                'size'               => array('type' => 'string', 'description' => 'Größe: small/middle/large (klein/mittel/groß werden gemappt)'),
            ),
            'required'   => array('id'),
            'additionalProperties' => false,
        ),
        'output_schema'       => $animal_object,
        'permission_callback' => 'tsvd_tools_ai_can_manage_animals',
        'execute_callback'    => 'tsvd_tools_ai_update_animal',
        'meta'                => array('mcp' => array('public' => true)),
    ));

    wp_register_ability('tsv-tools/create-missing-form', array(
        'label'               => __('Vermisst-Formular anlegen', 'tsv-tools'),
        'description'         => __('Legt das Meldeformular für vermisste Tiere (tsvd_form) mit den passenden Feldern an, aktiviert das Feature und verknüpft Formular + Seite in den Einstellungen.', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array(
                'recipient_email' => array('type' => 'string', 'description' => 'E-Mail für Benachrichtigungen (Default: admin_email)'),
                'create_page'     => array('type' => 'boolean', 'default' => true, 'description' => 'Seite mit [missing_animals] anlegen und verknüpfen'),
                'force'           => array('type' => 'boolean', 'default' => false, 'description' => 'Neu anlegen, auch wenn bereits ein Formular konfiguriert ist'),
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
                'page_id'        => array('type' => 'integer'),
                'page_url'       => array('type' => 'string'),
                'message'        => array('type' => 'string'),
            ),
        ),
        'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
        'execute_callback'    => 'tsvd_tools_ai_create_missing_form',
        'meta'                => array('mcp' => array('public' => true)),
    ));

    wp_register_ability('tsv-tools/set-missing-page', array(
        'label'               => __('Vermisst-Seite umhängen', 'tsv-tools'),
        'description'         => __('Setzt die Seite für vermisste Tiere in den Einstellungen auf eine bestehende Seite, stellt den [missing_animals]-Inhalt sicher und aktualisiert die Rewrite-Regeln.', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array(
                'page_id'         => array('type' => 'integer', 'description' => 'ID der bestehenden Seite (Post-Type page)'),
                'ensure_content'  => array('type' => 'boolean', 'default' => true, 'description' => '[missing_animals] in den Seiteninhalt einfügen, falls nicht vorhanden'),
            ),
            'required'             => array('page_id'),
            'additionalProperties' => false,
        ),
        'output_schema'       => array(
            'type'       => 'object',
            'properties' => array(
                'ok'       => array('type' => 'boolean'),
                'page_id'  => array('type' => 'integer'),
                'page_url' => array('type' => 'string'),
                'message'  => array('type' => 'string'),
            ),
        ),
        'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
        'execute_callback'    => 'tsvd_tools_ai_set_missing_page',
        'meta'                => array('mcp' => array('public' => true)),
    ));
}
