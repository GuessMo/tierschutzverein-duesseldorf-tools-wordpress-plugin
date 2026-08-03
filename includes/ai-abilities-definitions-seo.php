<?php
/**
 * AI Abilities — get-seo-score definition (2026-08-03).
 *
 * @package TSVD_Tools
 */

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_get_ability_definitions_seo() {
    $criterion_object = array(
        'type'       => 'object',
        'properties' => array(
            'id'                 => array('type' => 'string'),
            'label'              => array('type' => 'string'),
            'status'             => array('type' => 'string', 'enum' => array('pass', 'warn', 'fail', 'skipped')),
            'message'            => array('type' => 'string'),
            'value'              => array('type' => array('number', 'string', 'null')),
            'score_contribution' => array('type' => array('number', 'null')),
        ),
    );

    return array(
        'tsv-tools/get-seo-score' => array(
            'label'               => __('SEO-Score abrufen', 'tsv-tools'),
            'description'         => __('Regelbasierter SEO-/Lesbarkeits-Score für einen Beitrag oder eine Seite: Satzlänge, Verbindungswörter, Lesbarkeit (Wiener Sachtextformel), Absatzlänge, Zwischenüberschriften, kein Gendern, keine Sie-Anrede, Du-Anrede, Textlänge, Fokus-Keyword-Kriterien und Meta-Description-Länge. Rein lesend, keine Änderungen.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'post_id' => array('type' => 'integer', 'description' => 'ID eines Beitrags oder einer Seite'),
                ),
                'required'             => array('post_id'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'post_id'  => array('type' => 'integer'),
                    'overall'  => array(
                        'type'       => 'object',
                        'properties' => array(
                            'score'             => array('type' => 'integer'),
                            'status'            => array('type' => 'string', 'enum' => array('pass', 'warn', 'fail')),
                            'forced_red_reason' => array('type' => array('string', 'null')),
                        ),
                    ),
                    'criteria' => array(
                        'type'  => 'array',
                        'items' => $criterion_object,
                    ),
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_manage_settings',
            'execute_callback'    => 'tsvd_tools_ai_get_seo_score',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => true, 'destructive' => false, 'idempotent' => true),
            ),
        ),
    );
}
