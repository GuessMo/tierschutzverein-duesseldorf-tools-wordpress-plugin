<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_TOOL_PORTAL_MENU_POSITION = 4;

add_action( 'admin_menu', 'tsvd_tool_portal_admin_menu' );
add_action( 'admin_enqueue_scripts', 'tsvd_tool_portal_enqueue_menu_script' );
add_filter( 'tsvd_role_manager_always_allowed_menus', 'tsvd_tool_portal_allow_menus' );

function tsvd_tool_portal_admin_menu() {
	if ( ! tsvd_tools_theme_active() ) {
		return;
	}
	foreach ( tsvd_tool_portal_tools() as $slug => $tool ) {
		add_menu_page(
			$tool['title'],
			esc_html( $tool['title'] ) . tsvd_tool_portal_new_tab_hint(),
			tsvd_tool_portal_cap( $slug ),
			tsvd_tool_portal_menu_slug( $slug ),
			'',
			$tool['icon'],
			TSVD_TOOL_PORTAL_MENU_POSITION
		);
	}
}

function tsvd_tool_portal_menu_slug( $slug ) {
	return untrailingslashit( tsvd_tool_portal_url( $slug ) );
}

function tsvd_tool_portal_new_tab_hint() {
	$hint = esc_html__( '(öffnet in neuem Tab)', 'tsvd' );
	return '<span class="screen-reader-text"> ' . $hint . '</span>';
}

function tsvd_tool_portal_allow_menus( $menus ) {
	foreach ( array_keys( tsvd_tool_portal_tools() ) as $slug ) {
		$menus[] = tsvd_tool_portal_menu_slug( $slug );
	}
	return $menus;
}

function tsvd_tool_portal_enqueue_menu_script() {
	if ( ! tsvd_tools_theme_active() ) {
		return;
	}
	wp_enqueue_script(
		'tsvd-tool-portal-menu',
		TSVD_TOOLS_URL . 'assets/tool-portal-menu.js',
		array(),
		TSVD_TOOLS_ASSET_VERSION,
		array( 'in_footer' => true )
	);
	wp_localize_script(
		'tsvd-tool-portal-menu',
		'tsvdToolPortalMenu',
		array( 'base' => home_url( '/' . TSVD_TOOL_PORTAL_BASE . '/' ) )
	);
}
