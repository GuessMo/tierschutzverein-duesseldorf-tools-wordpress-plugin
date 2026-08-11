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

        'tsv-tools/update-redirects' => array(
            'label'               => __('Redirects aktualisieren', 'tsv-tools'),
            'description'         => __('Ergänzt Redirect-Ziele (Route301) idempotent um einen Route-Code (?route=<code>).', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'domain'    => array('type' => 'string', 'description' => 'Default: tierheim-duesseldorf.de'),
                    'redirects' => array(
                        'type'  => 'array',
                        'items' => array(
                            'type'       => 'object',
                            'properties' => array(
                                'id'         => array('type' => 'integer'),
                                'route_code' => array('type' => 'string', 'description' => 'Route-Code, z.B. thd oder tms'),
                                'target'     => array('type' => 'string', 'description' => 'Optional: ersetzt das Ziel vor dem Anhängen des Route-Codes'),
                            ),
                            'required'   => array('id'),
                            'additionalProperties' => false,
                        ),
                    ),
                ),
                'required'   => array('redirects'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'updated' => array('type' => 'integer'),
                    'skipped' => array('type' => 'integer'),
                    'failed'  => array('type' => 'array'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_update_redirects',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
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

        'tsv-tools/import-animal' => array(
            'label'               => __('Tier historisch importieren', 'tsv-tools'),
            'description'         => __('Legt ein Tier mit vollem Feldumfang an (Migration/Bulk-Import): TEO-ID, Lebensabschnitt, Kastriert-Status, Geburtsdatum, mehrere Bilder, frei wählbares Anlegedatum und Status (Standard: draft), Companion-Verknüpfung.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'name'            => array('type' => 'string'),
                    'description'     => array('type' => 'string'),
                    'teo_id'          => array('type' => 'string'),
                    'species'         => array('type' => 'string', 'description' => 'animal_breed Elternterm, z.B. Hund'),
                    'breed'           => array('type' => 'string', 'description' => 'animal_breed Kindterm'),
                    'gender'          => array('type' => 'string', 'description' => 'female/male/unknown'),
                    'size'            => array('type' => 'string', 'description' => 'small/middle/large'),
                    'life_stage'      => array('type' => 'string', 'description' => 'young/adult/senior/undefined'),
                    'castrated'       => array('type' => 'string', 'description' => 'yes/no/unknown'),
                    'birthday'        => array('type' => 'string', 'description' => 'YYYY-MM-DD'),
                    'birthday_status' => array('type' => 'string', 'description' => 'known/estimated/unknown'),
                    'adoption_status' => array('type' => 'string', 'description' => 'Default: for_adoption'),
                    'mediator'        => array('type' => 'string', 'description' => 'Default: tierschutzverein'),
                    'post_status'     => array('type' => 'string', 'description' => 'Default: draft'),
                    'post_date'       => array('type' => 'string', 'description' => 'Y-m-d H:i:s, Anlegedatum (historisch)'),
                    'image_ids'       => array('type' => 'array', 'items' => array('type' => 'integer'), 'description' => 'Bereits hochgeladene Attachment-IDs; erste = Beitragsbild'),
                    'companion_ids'   => array('type' => 'array', 'items' => array('type' => 'integer'), 'description' => 'Bidirektional zu verknüpfende Tier-Post-IDs'),
                ),
                'required'   => array('name'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
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
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_animals',
            'execute_callback'    => 'tsvd_tools_ai_import_animal',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => false),
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

        'tsv-tools/cleanup-animal-image-artifacts' => array(
            'label'               => __('Import-Artefakte aus Tier-Bildern entfernen', 'tsv-tools'),
            'description'         => __('Entfernt Attachments, deren Titel auf ein REGEXP passt, aus animal_images und dem Beitragsbild aller Tiere — für Import-Artefakte wie eine pro Tier mitheruntergeladene Teaser-Grafik der Quellseite. Standard ist ein Dry-Run: erst mit apply=true wird geschrieben, Attachments werden nur mit delete_attachments=true endgültig gelöscht.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'title_pattern'      => array('type' => 'string', 'description' => 'MySQL-REGEXP auf den Attachment-Titel, mind. 8 Zeichen, z.B. ^[0-9]{2}_teaser-3-e41c3afd'),
                    'apply'              => array('type' => 'boolean', 'default' => false, 'description' => 'false = Dry-Run, es wird nichts geschrieben'),
                    'delete_attachments' => array('type' => 'boolean', 'default' => false, 'description' => 'true = gefundene Attachments samt Dateien löschen (nur zusammen mit apply)'),
                ),
                'required'   => array('title_pattern'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'applied'             => array('type' => 'boolean'),
                    'attachment_count'    => array('type' => 'integer'),
                    'attachment_ids'      => array('type' => 'array'),
                    'gallery_changed'     => array('type' => 'array'),
                    'thumbnail_cleared'   => array('type' => 'array'),
                    'attachments_deleted' => array('type' => 'integer'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_animals',
            'execute_callback'    => 'tsvd_tools_ai_cleanup_animal_image_artifacts',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => true, 'idempotent' => true),
            ),
        ),

        'tsv-tools/repair-edited-image-sizes' => array(
            'label'               => __('Bildgrößen bearbeiteter Bilder reparieren', 'tsv-tools'),
            'description'         => __('Findet Attachments, die im Bildeditor bearbeitet wurden, deren registrierte Größen aber noch auf die Datei vor der Bearbeitung zeigen — der Grund, warum das Frontend das alte Bild zeigt. Stößt für diese eine Metadaten-Aktualisierung an, wodurch die Größen neu erzeugt werden. Standard ist ein Dry-Run: erst mit apply=true wird geschrieben. Ohne attachment_ids werden alle bearbeiteten Bilder geprüft.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'attachment_ids' => array(
                        'type'        => 'array',
                        'items'       => array('type' => 'integer'),
                        'description' => 'Optional: nur diese Attachments prüfen.',
                    ),
                    'apply'          => array('type' => 'boolean', 'default' => false, 'description' => 'false = Dry-Run, es wird nichts geschrieben'),
                ),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'applied'     => array('type' => 'boolean'),
                    'checked'     => array('type' => 'integer'),
                    'affected'    => array('type' => 'array'),
                    'repaired'    => array('type' => 'integer'),
                    'still_stale' => array('type' => 'array'),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_animals',
            'execute_callback'    => 'tsvd_tools_ai_repair_edited_image_sizes',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => true, 'idempotent' => true),
            ),
        ),

        'tsv-tools/regenerate-focus-crops' => array(
            'label'               => __('Fokus-Zuschnitte neu generieren', 'tsv-tools'),
            'description'         => __('Schneidet die im Theme konfigurierten Bildgrößen (Filter tsvd_focus_crop_sizes, Standard: thumbnail) fokus-bewusst neu zu (statt WordPress-Standard-Mittenzuschnitt) für alle Attachments mit gesetzter Fokus-Region (Bildfokus Stufe 2). Ohne attachment_ids laufen alle Attachments mit Fokus-Region durch.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'attachment_ids' => array(
                        'type'        => 'array',
                        'items'       => array('type' => 'integer'),
                        'description' => 'Optional: nur diese Attachment-IDs verarbeiten statt aller mit Fokus-Region.',
                    ),
                ),
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
            'execute_callback'    => 'tsvd_tools_ai_regenerate_focus_crops',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => false, 'idempotent' => true),
            ),
        ),
    );
}
