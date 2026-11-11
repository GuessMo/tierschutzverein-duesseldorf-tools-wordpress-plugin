<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'template_redirect', 'tsvd_tool_portal_handle_request', 0 );

function tsvd_tool_portal_request_path() {
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$path = trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );
	$home = trim( (string) wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
	if ( '' !== $home && str_starts_with( $path, $home . '/' ) ) {
		$path = substr( $path, strlen( $home ) + 1 );
	}
	return $path;
}

function tsvd_tool_portal_requested_slug() {
	$pattern = '#^' . TSVD_TOOL_PORTAL_BASE . '/([a-z0-9-]+)$#';
	if ( ! preg_match( $pattern, tsvd_tool_portal_request_path(), $matches ) ) {
		return null;
	}
	return tsvd_tool_portal_tool( $matches[1] ) ? $matches[1] : null;
}

function tsvd_tool_portal_handle_request() {
	$slug = tsvd_tool_portal_requested_slug();
	if ( null === $slug || ! tsvd_tools_theme_active() ) {
		return;
	}
	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( wp_login_url( tsvd_tool_portal_url( $slug ) ) );
		exit;
	}
	tsvd_tool_portal_guard_access( $slug );
	$assets = tsvd_tool_portal_entry_assets( $slug );
	if ( ! $assets ) {
		wp_die(
			esc_html__( 'Dieses Werkzeug ist noch nicht verfügbar.', 'tsvd' ),
			'',
			array( 'response' => 503 )
		);
	}
	tsvd_tool_portal_render_shell( $slug, $assets );
	exit;
}

function tsvd_tool_portal_guard_access( $slug ) {
	if ( current_user_can( tsvd_tool_portal_cap( $slug ) ) ) {
		return;
	}
	wp_die(
		esc_html__( 'Du hast keinen Zugriff auf dieses Werkzeug.', 'tsvd' ),
		esc_html__( 'Kein Zugriff', 'tsvd' ),
		array( 'response' => 403 )
	);
}
