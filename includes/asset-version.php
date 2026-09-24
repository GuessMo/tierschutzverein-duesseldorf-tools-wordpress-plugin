<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_tools_latest_asset_mtime() {
	$files = glob( TSVD_TOOLS_DIR . 'assets/*.{css,js}', GLOB_BRACE );
	return $files ? max( array_map( 'filemtime', $files ) ) : 0;
}

define(
	'TSVD_TOOLS_ASSET_VERSION',
	'local' === wp_get_environment_type()
		? TSVD_TOOLS_VERSION . '-' . tsvd_tools_latest_asset_mtime()
		: TSVD_TOOLS_VERSION
);
