<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_TOOL_PORTAL_BASE = 'tools';

function tsvd_tool_portal_tools() {
	return array(
		'tiere'    => array(
			'title' => __( 'Tierverwaltung', 'tsvd' ),
			'icon'  => 'dashicons-pets',
			'cap'   => 'edit_animals',
		),
		'anfragen' => array(
			'title' => __( 'Anfragen', 'tsvd' ),
			'icon'  => 'dashicons-format-chat',
			'cap'   => 'manage_tsvd_anfragen',
		),
		'quellen'  => array(
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
	$tool = tsvd_tool_portal_tool( $slug );
	return $tool['cap'] ?? tsvd_tool_portal_own_cap( $slug );
}

function tsvd_tool_portal_own_cap( $slug ) {
	return 'tsvd_use_tool_' . $slug;
}

function tsvd_tool_portal_has_own_cap( $slug ) {
	return tsvd_tool_portal_cap( $slug ) === tsvd_tool_portal_own_cap( $slug );
}

function tsvd_tool_portal_url( $slug ) {
	return home_url( '/' . TSVD_TOOL_PORTAL_BASE . '/' . $slug . '/' );
}

function tsvd_tool_portal_app_dir( $slug ) {
	return 'apps/' . $slug . '/dist/';
}
