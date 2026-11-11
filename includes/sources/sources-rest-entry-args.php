<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_src_entry_args( $is_create ) {
	$args = array(
		'title'       => tsvd_src_text_arg( 255 ),
		'body'        => tsvd_src_text_arg( 20000, 'sanitize_textarea_field' ),
		'url'         => tsvd_src_schema_arg(
			array(
				'type'      => 'string',
				'maxLength' => 2048,
			),
			'esc_url_raw',
			'tsvd_src_validate_url'
		),
		'publisher'   => tsvd_src_text_arg( 190 ),
		'year'        => tsvd_src_schema_arg( tsvd_src_year_schema() ),
		'kind'        => tsvd_src_schema_arg(
			array(
				'type' => 'string',
				'enum' => TSVD_SRC_KINDS,
			)
		),
		'accessed_at' => tsvd_src_schema_arg(
			array( 'type' => array( 'string', 'null' ) ),
			'rest_sanitize_request_arg',
			'tsvd_src_validate_date'
		),
		'term_ids'    => tsvd_src_schema_arg( tsvd_src_term_ids_schema() ),
	);
	return tsvd_src_require_args( $args, $is_create ? array( 'title' ) : array() );
}

function tsvd_src_year_schema() {
	return array(
		'type'    => array( 'integer', 'null' ),
		'minimum' => 1000,
		'maximum' => 2100,
	);
}

function tsvd_src_term_ids_schema() {
	return array(
		'type'     => 'array',
		'maxItems' => 200,
		'items'    => array(
			'type'    => 'integer',
			'minimum' => 1,
		),
	);
}

function tsvd_src_validate_url( $value, $request, $param ) {
	$valid = rest_validate_request_arg( $value, $request, $param );
	if ( true !== $valid || '' === $value ) {
		return $valid;
	}
	$scheme = wp_parse_url( (string) $value, PHP_URL_SCHEME );
	$is_web = in_array( $scheme, array( 'http', 'https' ), true );
	if ( $is_web && false !== filter_var( $value, FILTER_VALIDATE_URL ) ) {
		return true;
	}
	return tsvd_src_invalid( __( 'Der Link muss mit http:// oder https:// beginnen.', 'tsvd' ) );
}

function tsvd_src_validate_date( $value, $request, $param ) {
	$valid = rest_validate_request_arg( $value, $request, $param );
	if ( true !== $valid || null === $value || '' === $value ) {
		return $valid;
	}
	$parts = array_map( 'intval', explode( '-', (string) $value ) );
	$shape = preg_match( '/^\d{4}-\d{2}-\d{2}$/', (string) $value );
	$ok    = $shape && checkdate( $parts[1], $parts[2], $parts[0] );
	return $ok ? true : tsvd_src_invalid( __( 'Das Abrufdatum ist ungültig.', 'tsvd' ) );
}
