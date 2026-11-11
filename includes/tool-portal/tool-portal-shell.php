<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_tool_portal_render_shell( $slug, $assets ) {
	status_header( 200 );
	nocache_headers();
	header( 'Content-Type: text/html; charset=utf-8' );
	echo '<!doctype html><html lang="de"><head>';
	tsvd_tool_portal_print_head( $slug, $assets );
	echo '</head><body><div id="root"></div>';
	echo '<script type="module" src="' . esc_url( $assets['script'] ) . '"></script>';
	echo '</body></html>';
}

function tsvd_tool_portal_print_head( $slug, $assets ) {
	$tool = tsvd_tool_portal_app( $slug );
	echo '<meta charset="utf-8">';
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
	echo '<meta name="robots" content="noindex, nofollow">';
	echo '<title>' . esc_html( $tool['title'] . ' · ' . get_bloginfo( 'name' ) ) . '</title>';
	echo '<script>' . tsvd_tool_portal_theme_init_script( $slug ) . '</script>';
	foreach ( $assets['styles'] as $style ) {
		echo '<link rel="stylesheet" href="' . esc_url( $style ) . '">';
	}
	$bootstrap = tsvd_tool_portal_bootstrap_json( $slug );
	echo '<script>window.tsvdToolBootstrap = ' . $bootstrap . ';</script>';
}

function tsvd_tool_portal_bootstrap_json( $slug ) {
	$user = wp_get_current_user();
	$data = array(
		'restUrl'      => esc_url_raw( rest_url( 'tsvd-tools/v1/' ) ),
		'restRoot'     => esc_url_raw( rest_url() ),
		'nonce'        => wp_create_nonce( 'wp_rest' ),
		'adminUrl'     => admin_url(),
		'userName'     => $user->display_name,
		'userInitials' => tsvd_tool_portal_initials( $user->display_name ),
		'toolUrl'      => tsvd_tool_portal_url( $slug ),
		'startUrl'     => tsvd_tool_portal_start_url(),
		'logoutUrl'    => wp_logout_url( wp_login_url() ),
		'tools'        => tsvd_tool_portal_launcher_items(),
	);
	return (string) wp_json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES );
}
