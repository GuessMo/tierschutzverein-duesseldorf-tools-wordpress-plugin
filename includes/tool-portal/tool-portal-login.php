<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'login_redirect', 'tsvd_tool_portal_login_redirect', 20, 3 );

function tsvd_tool_portal_login_redirect( $redirect_to, $requested, $user ) {
	if ( ! $user instanceof WP_User || ! tsvd_tools_theme_active() ) {
		return $redirect_to;
	}
	if ( ! tsvd_tool_portal_is_default_target( (string) $requested ) ) {
		return $redirect_to;
	}
	return tsvd_tool_portal_start_url();
}

function tsvd_tool_portal_is_default_target( $url ) {
	if ( '' === $url ) {
		return true;
	}
	return untrailingslashit( $url ) === untrailingslashit( admin_url() );
}
