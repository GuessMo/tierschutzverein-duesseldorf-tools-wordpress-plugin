<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_TOOL_PORTAL_START = 'start';

function tsvd_tool_portal_tools() {
	return array(
		'tiere'    => array(
			'title'  => __( 'Tierverwaltung', 'tsvd' ),
			'path'   => 'tierverwaltung',
			'icon'   => 'dashicons-pets',
			'symbol' => 'paw',
			'cap'    => 'edit_animals',
		),
		'anfragen' => array(
			'title'  => __( 'Anfragen', 'tsvd' ),
			'path'   => 'anfragen',
			'icon'   => 'dashicons-format-chat',
			'symbol' => 'messages',
			'cap'    => 'manage_tsvd_anfragen',
		),
		'quellen'  => array(
			'title'  => __( 'Quellen', 'tsvd' ),
			'path'   => 'quellen',
			'icon'   => 'dashicons-book-alt',
			'symbol' => 'books',
		),
		'academy'  => array(
			'title'  => __( 'Academy', 'tsvd' ),
			'path'   => 'academy',
			'icon'   => 'dashicons-welcome-learn-more',
			'symbol' => 'school',
			'cap'    => 'read',
		),
		'layout'   => array(
			'title'  => __( 'Layout', 'tsvd' ),
			'path'   => 'layout',
			'icon'   => 'dashicons-layout',
			'symbol' => 'layout',
			'cap'    => 'edit_posts',
		),
	) + tsvd_tool_portal_skylark_tool();
}

function tsvd_tool_portal_skylark_tool() {
	if ( ! function_exists( 'skylark_register_data_route' ) ) {
		return array();
	}
	return array(
		'skylark' => array(
			'title'  => __( 'Skylark', 'tsvd' ),
			'path'   => 'skylark',
			'icon'   => 'dashicons-chart-area',
			'symbol' => 'chart',
			'cap'    => 'manage_options',
		),
	);
}

function tsvd_tool_portal_start_app() {
	return array(
		'title' => __( 'Dashboard', 'tsvd' ),
		'path'  => 'dashboard',
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

function tsvd_tool_portal_apps() {
	return array( TSVD_TOOL_PORTAL_START => tsvd_tool_portal_start_app() ) + tsvd_tool_portal_tools();
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

function tsvd_tool_portal_url( $slug ) {
	$app = tsvd_tool_portal_app( $slug );
	return home_url( '/' . $app['path'] . '/' );
}

function tsvd_tool_portal_start_url() {
	return tsvd_tool_portal_url( TSVD_TOOL_PORTAL_START );
}

function tsvd_tool_portal_app_dir( $slug ) {
	return 'apps/' . $slug . '/dist/';
}
