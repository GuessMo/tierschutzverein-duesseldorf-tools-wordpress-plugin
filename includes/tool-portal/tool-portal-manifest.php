<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_tool_portal_read_manifest( $slug ) {
	$path = TSVD_TOOLS_DIR . tsvd_tool_portal_app_dir( $slug ) . '.vite/manifest.json';
	if ( ! is_readable( $path ) ) {
		return array();
	}
	$manifest = json_decode( (string) file_get_contents( $path ), true );
	return is_array( $manifest ) ? $manifest : array();
}

function tsvd_tool_portal_entry_assets( $slug ) {
	foreach ( tsvd_tool_portal_read_manifest( $slug ) as $chunk ) {
		if ( ! empty( $chunk['isEntry'] ) && ! empty( $chunk['file'] ) ) {
			return tsvd_tool_portal_chunk_urls( $slug, $chunk );
		}
	}
	return null;
}

function tsvd_tool_portal_chunk_urls( $slug, $chunk ) {
	$base = TSVD_TOOLS_URL . tsvd_tool_portal_app_dir( $slug );
	return array(
		'script' => $base . $chunk['file'],
		'styles' => array_map(
			function ( $file ) use ( $base ) {
				return $base . $file;
			},
			$chunk['css'] ?? array()
		),
	);
}

function tsvd_tool_portal_theme_init_script( $slug ) {
	$path = TSVD_TOOLS_DIR . tsvd_tool_portal_app_dir( $slug ) . 'theme-init.js';
	return is_readable( $path ) ? (string) file_get_contents( $path ) : '';
}
