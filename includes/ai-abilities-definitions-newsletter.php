<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_tools_ai_get_ability_definitions_newsletter() {
	return array(
		'tsv-tools/create-newsletter' => array(
			'label'               => __( 'Newsletter anlegen', 'tsv-tools' ),
			'description'         => __( 'Legt einen Newsletter (tsvd_newsletter) als Entwurf an, mit Bloecken. Blocktypen: rte (data.html), image (data.id, data.alt), website-updates (dynamisch, data leer).', 'tsv-tools' ),
			'category'            => 'tsv-tools-animals',
			'input_schema'        => array(
				'type'                 => 'object',
				'properties'           => array(
					'title'  => array( 'type' => 'string', 'description' => 'Betreff/Titel des Newsletters.' ),
					'blocks' => array(
						'type'        => 'array',
						'description' => 'Geordnete Bloecke, je {type, data}. type: rte|image|website-updates.',
						'items'       => array( 'type' => 'object' ),
					),
				),
				'required'             => array( 'title' ),
				'additionalProperties' => false,
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'id'       => array( 'type' => 'integer' ),
					'edit_url' => array( 'type' => 'string' ),
				),
			),
			'permission_callback' => 'tsvd_tools_ai_can_manage_newsletter',
			'execute_callback'    => 'tsvd_tools_ai_create_newsletter',
			'meta'                => array(
				'mcp'         => array( 'public' => true ),
				'annotations' => array( 'readonly' => false, 'destructive' => false, 'idempotent' => false ),
			),
		),

		'tsv-tools/send-newsletter'   => array(
			'label'               => __( 'Newsletter senden', 'tsv-tools' ),
			'description'         => __( 'Rendert einen Newsletter (tsvd_newsletter) zur E-Mail und versendet ihn. Entweder an konkrete recipients (Test) ODER mit to_subscribers=true an alle internen Abonnenten. Absender: newsletter.intern@tierschutzverein-duesseldorf.de.', 'tsv-tools' ),
			'category'            => 'tsv-tools-animals',
			'input_schema'        => array(
				'type'                 => 'object',
				'properties'           => array(
					'id'             => array( 'type' => 'integer', 'description' => 'Newsletter-Post-ID (tsvd_newsletter).' ),
					'recipients'     => array(
						'type'        => 'array',
						'description' => 'Konkrete Empfaenger-E-Mails (Test). Hat Vorrang vor to_subscribers.',
						'items'       => array( 'type' => 'string' ),
					),
					'to_subscribers' => array( 'type' => 'boolean', 'description' => 'true: an alle internen Abonnenten senden und Sende-Datum setzen.' ),
				),
				'required'             => array( 'id' ),
				'additionalProperties' => false,
			),
			'output_schema'       => array(
				'type'       => 'object',
				'properties' => array(
					'sent'   => array( 'type' => 'integer' ),
					'total'  => array( 'type' => 'integer' ),
					'failed' => array( 'type' => 'array' ),
				),
			),
			'permission_callback' => 'tsvd_tools_ai_can_manage_newsletter',
			'execute_callback'    => 'tsvd_tools_ai_send_newsletter',
			'meta'                => array(
				'mcp'         => array( 'public' => true ),
				'annotations' => array( 'readonly' => false, 'destructive' => false, 'idempotent' => false ),
			),
		),
	);
}
