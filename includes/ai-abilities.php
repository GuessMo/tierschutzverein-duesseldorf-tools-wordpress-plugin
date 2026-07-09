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
                'contact_firstname'  => array('type' => 'string', 'description' => 'Halter/Melder Vorname'),
                'contact_lastname'   => array('type' => 'string', 'description' => 'Halter/Melder Nachname'),
                'contact_email'      => array('type' => 'string', 'description' => 'Halter/Melder E-Mail'),
                'contact_address'    => array('type' => 'string', 'description' => 'Halter/Melder Anschrift'),
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
                'contact_firstname'  => array('type' => 'string', 'description' => 'Halter/Melder Vorname'),
                'contact_lastname'   => array('type' => 'string', 'description' => 'Halter/Melder Nachname'),
                'contact_email'      => array('type' => 'string', 'description' => 'Halter/Melder E-Mail'),
                'contact_address'    => array('type' => 'string', 'description' => 'Halter/Melder Anschrift'),
            ),
            'required'   => array('id'),
            'additionalProperties' => false,
        ),
        'output_schema'       => $animal_object,
        'permission_callback' => 'tsvd_tools_ai_can_manage_animals',
        'execute_callback'    => 'tsvd_tools_ai_update_animal',
        'meta'                => array('mcp' => array('public' => true)),
    ));

    wp_register_ability('tsv-tools/create-animals-bulk', array(
        'label'               => __('Tiere im Bulk anlegen', 'tsv-tools'),
        'description'         => __('Legt mehrere Tiere als Entwurf an (Array von Tier-Objekten wie create-animal).', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array(
                'animals' => array(
                    'type'  => 'array',
                    'items' => array('type' => 'object'),
                    'description' => 'Liste von Tier-Objekten (Felder wie bei create-animal).',
                ),
            ),
            'required'   => array('animals'),
            'additionalProperties' => false,
        ),
        'output_schema'       => array(
            'type'       => 'object',
            'properties' => array(
                'count'   => array('type' => 'integer'),
                'created' => array('type' => 'array'),
                'errors'  => array('type' => 'array'),
            ),
        ),
        'permission_callback' => 'tsvd_tools_ai_can_manage_animals',
        'execute_callback'    => 'tsvd_tools_ai_create_animals_bulk',
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

    wp_register_ability('tsv-tools/set-animal-interest-seed', array(
        'label'               => __('Interesse-Basiswert setzen', 'tsv-tools'),
        'description'         => __('Setzt den redaktionellen Interesse-Basiswert (animal_interest_seed) für ein oder mehrere Tiere und berechnet den Gesamtwert neu.', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array(
                'items' => array(
                    'type'        => 'array',
                    'description' => 'Liste von {id, seed}.',
                    'items'       => array(
                        'type'       => 'object',
                        'properties' => array(
                            'id'   => array('type' => 'integer'),
                            'seed' => array('type' => 'integer'),
                        ),
                        'required'   => array('id', 'seed'),
                    ),
                ),
            ),
            'required'             => array('items'),
            'additionalProperties' => false,
        ),
        'output_schema'       => array(
            'type'       => 'object',
            'properties' => array(
                'updated' => array('type' => 'integer'),
                'results' => array('type' => 'array'),
            ),
        ),
        'permission_callback' => 'tsvd_tools_ai_can_manage_animals',
        'execute_callback'    => 'tsvd_tools_ai_set_animal_interest_seed',
        'meta'                => array('mcp' => array('public' => true)),
    ));

    wp_register_ability('tsv-tools/add-applicant-fields-to-form', array(
        'label'               => __('Bewerber-Felder zum Formular hinzufügen', 'tsv-tools'),
        'description'         => __('Fügt die Bewerber-Felder (Wohnort/Unterkunft/Außenbereich) gruppen-sicher in ein Formular ein. target: interest|private|missing (missing = nur Wohnort). Idempotent.', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array(
                'target'  => array('type' => 'string', 'description' => 'interest | private | missing'),
                'form_id' => array('type' => 'integer', 'description' => 'Alternativ zur target-Auflösung: direkte tsvd_form-ID'),
            ),
            'additionalProperties' => false,
        ),
        'output_schema'       => array(
            'type'       => 'object',
            'properties' => array(
                'form_id'        => array('type' => 'integer'),
                'added'          => array('type' => 'array'),
                'updated'        => array('type' => 'array'),
                'order'          => array('type' => 'array'),
                'form_edit_link' => array('type' => 'string'),
            ),
        ),
        'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
        'execute_callback'    => 'tsvd_tools_ai_add_applicant_fields',
        'meta'                => array('mcp' => array('public' => true)),
    ));

    wp_register_ability('tsv-tools/get-page', array(
        'label'               => __('Seite abrufen', 'tsv-tools'),
        'description'         => __('Gibt eine WordPress-Seite (Post-Type page) mit Inhalt zurück. Auflösung per page_id oder slug.', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array(
                'page_id' => array('type' => 'integer'),
                'slug'    => array('type' => 'string', 'description' => 'Seiten-Slug (Pfad), alternativ zu page_id'),
            ),
            'additionalProperties' => false,
        ),
        'output_schema'       => array(
            'type'       => 'object',
            'properties' => array(
                'id'        => array('type' => 'integer'),
                'title'     => array('type' => 'string'),
                'slug'      => array('type' => 'string'),
                'status'    => array('type' => 'string'),
                'content'   => array('type' => 'string'),
                'url'       => array('type' => 'string'),
                'edit_link' => array('type' => 'string'),
            ),
        ),
        'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
        'execute_callback'    => 'tsvd_tools_ai_get_page',
        'meta'                => array('mcp' => array('public' => true)),
    ));

    wp_register_ability('tsv-tools/update-page', array(
        'label'               => __('Seite bearbeiten', 'tsv-tools'),
        'description'         => __('Bearbeitet eine WordPress-Seite. mode: append (anhängen), replace (kompletten Inhalt ersetzen), section (idempotenter Block via section_id ersetzen/anlegen). Auflösung per page_id oder slug; optional anlegen.', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array(
                'page_id'           => array('type' => 'integer'),
                'slug'              => array('type' => 'string'),
                'content'           => array('type' => 'string', 'description' => 'HTML-Inhalt'),
                'mode'              => array('type' => 'string', 'description' => 'append | replace | section (Default: append)'),
                'section_id'        => array('type' => 'string', 'description' => 'Nur mode=section: Kennung des idempotenten Blocks'),
                'create_if_missing' => array('type' => 'boolean', 'default' => false),
                'title'             => array('type' => 'string', 'description' => 'Titel für neue Seite (bei create_if_missing)'),
            ),
            'required'             => array('content'),
            'additionalProperties' => false,
        ),
        'output_schema'       => array(
            'type'       => 'object',
            'properties' => array(
                'id'        => array('type' => 'integer'),
                'url'       => array('type' => 'string'),
                'edit_link' => array('type' => 'string'),
                'mode'      => array('type' => 'string'),
                'action'    => array('type' => 'string'),
            ),
        ),
        'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
        'execute_callback'    => 'tsvd_tools_ai_update_page',
        'meta'                => array('mcp' => array('public' => true)),
    ));

    wp_register_ability('tsv-tools/rest-request', array(
        'label'               => __('REST-API-Aufruf', 'tsv-tools'),
        'description'         => __('Führt einen beliebigen WordPress-REST-API-Aufruf aus (CRUD auf Beiträge, Seiten, Taxonomien, Medien, Nutzer, Kommentare, Einstellungen). method=GET|POST|PUT|PATCH|DELETE, route=z.B. /wp/v2/posts oder /wp/v2/settings, params=Objekt. Läuft mit den Rechten des angemeldeten Nutzers (Endpoint-Permissions greifen).', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array(
                'method' => array('type' => 'string', 'description' => 'GET|POST|PUT|PATCH|DELETE (Default GET)'),
                'route'  => array('type' => 'string', 'description' => 'REST-Route, z.B. /wp/v2/posts/123'),
                'params' => array('type' => 'object', 'description' => 'Query- bzw. Body-Parameter'),
            ),
            'required'             => array('route'),
            'additionalProperties' => false,
        ),
        'output_schema'       => array(
            'type'       => 'object',
            'properties' => array(
                'status' => array('type' => 'integer'),
                'body'   => array('type' => array('object', 'array', 'string', 'null')),
            ),
        ),
        'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
        'execute_callback'    => 'tsvd_tools_ai_rest_request',
        'meta'                => array('mcp' => array('public' => true)),
    ));

    wp_register_ability('tsv-tools/rest-list-routes', array(
        'label'               => __('REST-Routen auflisten', 'tsv-tools'),
        'description'         => __('Listet registrierte REST-API-Routen (optional gefiltert per contains), zur Entdeckung verfügbarer Endpoints.', 'tsv-tools'),
        'category'            => 'tsv-tools-animals',
        'input_schema'        => array(
            'type'       => 'object',
            'properties' => array(
                'contains' => array('type' => 'string', 'description' => 'Nur Routen, die diesen Teilstring enthalten'),
            ),
            'additionalProperties' => false,
        ),
        'output_schema'       => array(
            'type'       => 'object',
            'properties' => array(
                'count'  => array('type' => 'integer'),
                'routes' => array('type' => 'array'),
            ),
        ),
        'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
        'execute_callback'    => 'tsvd_tools_ai_rest_list_routes',
        'meta'                => array('mcp' => array('public' => true)),
    ));
}
