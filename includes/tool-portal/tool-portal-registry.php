<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_TOOL_PORTAL_BASE = 'tools';

function tsvd_tool_portal_tools() {
	return array(
		'quellen' => array(
			'title' => __( 'Quellen', 'tsvd' ),
			'icon'  => 'dashicons-book-alt',
		),
	);
}

function tsvd_tool_portal_tool( $slug ) {
	$tools = tsvd_tool_portal_tools();
	return $tools[ $slug ] ?? null;
}

function tsvd_tool_portal_cap( $slug ) {
	return 'tsvd_use_tool_' . $slug;
}

function tsvd_tool_portal_url( $slug ) {
	return home_url( '/' . TSVD_TOOL_PORTAL_BASE . '/' . $slug . '/' );
}

function tsvd_tool_portal_app_dir( $slug ) {
	return 'apps/' . $slug . '/dist/';
}
