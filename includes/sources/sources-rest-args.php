<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_src_schema_arg(
	$schema,
	$sanitize = 'rest_sanitize_request_arg',
	$validate = 'rest_validate_request_arg'
) {
	return array_merge(
		$schema,
		array(
			'validate_callback' => $validate,
			'sanitize_callback' => $sanitize,
		)
	);
}

function tsvd_src_text_arg( $max_length, $sanitize = 'sanitize_text_field' ) {
	return tsvd_src_schema_arg(
		array(
			'type'      => 'string',
			'maxLength' => $max_length,
		),
		$sanitize
	);
}

function tsvd_src_require_args( $args, $required ) {
	foreach ( $required as $key ) {
		$args[ $key ]['required'] = true;
	}
	return $args;
}

function tsvd_src_collection_args( $is_create ) {
	$args = array(
		'name'        => tsvd_src_text_arg( 190 ),
		'description' => tsvd_src_text_arg( 2000, 'sanitize_textarea_field' ),
		'shared_with' => tsvd_src_schema_arg(
			array(
				'type'     => 'array',
				'maxItems' => 50,
				'items'    => array(
					'type'      => 'string',
					'maxLength' => 100,
				),
			),
			'tsvd_src_sanitize_keys'
		),
	);
	return tsvd_src_require_args( $args, $is_create ? array( 'name' ) : array() );
}

function tsvd_src_taxonomy_args( $is_create ) {
	$args = array(
		'name'            => tsvd_src_text_arg( 190 ),
		'is_hierarchical' => tsvd_src_schema_arg( array( 'type' => 'boolean' ) ),
	);
	return tsvd_src_require_args( $args, $is_create ? array( 'name' ) : array() );
}

function tsvd_src_term_args( $is_create ) {
	$args = array(
		'name'      => tsvd_src_text_arg( 190 ),
		'parent_id' => tsvd_src_schema_arg(
			array(
				'type'    => array( 'integer', 'null' ),
				'minimum' => 1,
			)
		),
	);
	return tsvd_src_require_args( $args, $is_create ? array( 'name' ) : array() );
}

function tsvd_src_sanitize_keys( $value ) {
	return array_values( array_filter( array_map( 'sanitize_key', (array) $value ) ) );
}
