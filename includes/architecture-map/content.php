<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ARCHITECTURE_MAP_CONTENT_FILE = __DIR__ . '/content.json';

function tsvd_architecture_map_content() {
	static $content = null;
	if ( null !== $content ) {
		return $content;
	}
	$raw     = file_get_contents( TSVD_ARCHITECTURE_MAP_CONTENT_FILE );
	$decoded = false === $raw ? null : json_decode( $raw, true );
	if ( ! is_array( $decoded ) ) {
		trigger_error( 'TSV Tools: Inhalt des Aufbau-Plans (content.json) ist nicht lesbar.', E_USER_WARNING );
		$decoded = array();
	}
	$content = $decoded;
	return $content;
}
