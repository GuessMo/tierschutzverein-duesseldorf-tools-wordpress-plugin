<?php
/**
 * AI Abilities — ai-optimize-seo-text definition (2026-08-03).
 *
 * @package TSVD_Tools
 */

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_get_ability_definitions_ai_optimize() {
    $suggestion_object = array(
        'type'       => 'object',
        'properties' => array(
            'content'          => array('type' => 'string'),
            'focus_keyword'    => array('type' => 'string'),
            'meta_description' => array('type' => 'string'),
        ),
    );

    return array(
        'tsv-tools/ai-optimize-seo-text' => array(
            'label'               => __('SEO-Text KI-optimieren', 'tsv-tools'),
            'description'         => __('Lässt Gemini Inhalt, Fokus-Keyword und Meta-Description eines Beitrags oder einer Seite anhand der aktuell fehlschlagenden SEO-/Lesbarkeits-Kriterien überarbeiten. Standard ist ein Dry-Run: erst mit apply=true wird geschrieben (Beitragsinhalt + Meta-Felder). Nutzt den Gemini-API-Key des aufrufenden Nutzers.', 'tsv-tools'),
            'category'            => 'tsv-tools-animals',
            'input_schema'        => array(
                'type'       => 'object',
                'properties' => array(
                    'post_id' => array('type' => 'integer', 'description' => 'ID eines Beitrags oder einer Seite'),
                    'apply'   => array('type' => 'boolean', 'default' => false, 'description' => 'false = Dry-Run, es wird nichts geschrieben'),
                ),
                'required'             => array('post_id'),
                'additionalProperties' => false,
            ),
            'output_schema'       => array(
                'type'       => 'object',
                'properties' => array(
                    'post_id'    => array('type' => 'integer'),
                    'applied'    => array('type' => 'boolean'),
                    'suggestion' => $suggestion_object,
                ),
            ),
            'permission_callback' => 'tsvd_tools_ai_can_ai_optimize_seo',
            'execute_callback'    => 'tsvd_tools_ai_optimize_seo_text',
            'meta'                => array(
                'mcp'         => array('public' => true),
                'annotations' => array('readonly' => false, 'destructive' => true, 'idempotent' => false),
            ),
        ),
    );
}
