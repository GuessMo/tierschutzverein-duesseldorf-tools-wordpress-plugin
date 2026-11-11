<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_TOOL_PORTAL_BASE  = 'tools';
const TSVD_TOOL_PORTAL_START = 'start';

function tsvd_tool_portal_tools() {
	return array(
		'tiere'    => array(
			'title'  => __( 'Tierverwaltung', 'tsvd' ),
			'icon'   => 'dashicons-pets',
			'symbol' => 'paw',
			'cap'    => 'edit_animals',
		),
		'anfragen' => array(
			'title'  => __( 'Anfragen', 'tsvd' ),
			'icon'   => 'dashicons-format-chat',
			'symbol' => 'messages',
			'cap'    => 'manage_tsvd_anfragen',
		),
		'quellen'  => array(
			'title'  => __( 'Quellen', 'tsvd' ),
			'icon'   => 'dashicons-book-alt',
			'symbol' => 'books',
		),
	);
}

function tsvd_tool_portal_start_app() {
	return array(
		'title' => __( 'Dashboard', 'tsvd' ),
		'cap'   => 'read',
	);
}

function tsvd_tool_portal_tool( $slug ) {
	$tools = tsvd_tool_portal_tools();
	return $tools[ $slug ] ?? null;
}

function tsvd_tool_portal_app( $slug ) {
	return TSVD_TOOL_PORTAL_START === $slug ? tsvd_tool_portal_start_app() : tsvd_tool_portal_tool( $slug );
}

function tsvd_tool_portal_cap( $slug ) {
	$app = tsvd_tool_portal_app( $slug );
	return $app['cap'] ?? tsvd_tool_portal_own_cap( $slug );
}

function tsvd_tool_portal_own_cap( $slug ) {
	return 'tsvd_use_tool_' . $slug;
}

function tsvd_tool_portal_has_own_cap( $slug ) {
	return tsvd_tool_portal_cap( $slug ) === tsvd_tool_portal_own_cap( $slug );
}

function tsvd_tool_portal_start_url() {
	return home_url( '/' . TSVD_TOOL_PORTAL_BASE . '/' );
}

function tsvd_tool_portal_url( $slug ) {
	if ( TSVD_TOOL_PORTAL_START === $slug ) {
		return tsvd_tool_portal_start_url();
	}
	return home_url( '/' . TSVD_TOOL_PORTAL_BASE . '/' . $slug . '/' );
}

function tsvd_tool_portal_app_dir( $slug ) {
	return 'apps/' . $slug . '/dist/';
}
