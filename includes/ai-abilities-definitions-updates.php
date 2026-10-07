<?php

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_update_ability($label, $description, $schema, $callback, $annotations) {
    return array(
        'label'               => $label,
        'description'         => $description,
        'category'            => 'tsv-tools-animals',
        'input_schema'        => $schema,
        'output_schema'       => array('type' => 'object'),
        'permission_callback' => 'tsvd_tools_ai_can_manage_website_updates',
        'execute_callback'    => $callback,
        'meta'                => array(
            'mcp'         => array('public' => true),
            'annotations' => $annotations,
        ),
    );
}

function tsvd_tools_ai_update_id_schema(array $extra = array()) {
    return array(
        'type'       => 'object',
        'properties' => array_merge(
            array('id' => array('type' => 'integer', 'description' => 'tsvd_update-Post-ID.')),
            $extra
        ),
        'required'   => array('id'),
        'additionalProperties' => false,
    );
}

function tsvd_tools_ai_get_ability_definitions_updates() {
    $readonly = array('readonly' => true, 'destructive' => false, 'idempotent' => true);

    return array(
        'tsv-tools/list-updates' => tsvd_tools_ai_update_ability(
            __('Website-Updates auflisten', 'tsv-tools'),
            __('Listet Website-Updates (tsvd_update) mit Nummer, Titel, Status, Datum und in welchem Newsletter sie versendet wurden (sent_in). Neueste Nummer zuerst.', 'tsv-tools'),
            array(
                'type'       => 'object',
                'properties' => array(
                    'status'      => array('type' => 'string', 'enum' => array('any', 'draft', 'publish'), 'description' => 'Standard any.'),
                    'only_unsent' => array('type' => 'boolean', 'description' => 'Nur noch nicht versendete.'),
                    'limit'       => array('type' => 'integer', 'description' => '1-200, Standard 50.'),
                ),
                'additionalProperties' => false,
            ),
            'tsvd_tools_ai_list_website_updates',
            $readonly
        ),

        'tsv-tools/get-update' => tsvd_tools_ai_update_ability(
            __('Website-Update lesen', 'tsv-tools'),
            __('Liefert ein Website-Update inkl. HTML-Inhalt, Nummer, Status und Versand-Historie.', 'tsv-tools'),
            tsvd_tools_ai_update_id_schema(),
            'tsvd_tools_ai_get_website_update',
            $readonly
        ),

        'tsv-tools/delete-update' => tsvd_tools_ai_update_ability(
            __('Website-Update loeschen', 'tsv-tools'),
            __('Verschiebt ein Website-Update in den Papierkorb; force=true loescht endgueltig.', 'tsv-tools'),
            tsvd_tools_ai_update_id_schema(array(
                'force' => array('type' => 'boolean', 'description' => 'Endgueltig loeschen statt Papierkorb.'),
            )),
            'tsvd_tools_ai_delete_website_update',
            array('readonly' => false, 'destructive' => true, 'idempotent' => false)
        ),

        'tsv-tools/mark-updates-sent' => tsvd_tools_ai_update_ability(
            __('Website-Updates als versendet markieren', 'tsv-tools'),
            __('Markiert Website-Updates als versendet, wenn sie ohne Newsletter rausgingen (z. B. per Status-Mail). Legt dafuer einen Platzhalter-Newsletter "<label> (per E-Mail, kein Newsletter-Versand) – TT.MM.JJJJ" an, OHNE Mailversand. ids ODER all_unsent=true.', 'tsv-tools'),
            array(
                'type'       => 'object',
                'properties' => array(
                    'label'      => array('type' => 'string', 'description' => 'Bezeichnung, z. B. "Status-Mail 7".'),
                    'ids'        => array('type' => 'array', 'items' => array('type' => 'integer'), 'description' => 'tsvd_update-Post-IDs.'),
                    'all_unsent' => array('type' => 'boolean', 'description' => 'Alle noch nicht versendeten (Entwurf + veroeffentlicht).'),
                ),
                'required'   => array('label'),
                'additionalProperties' => false,
            ),
            'tsvd_tools_ai_mark_website_updates_sent',
            array('readonly' => false, 'destructive' => false, 'idempotent' => false)
        ),
    );
}
