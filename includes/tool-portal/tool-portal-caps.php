<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_TOOL_PORTAL_ROLES = array( 'administrator', 'projektleitung' );

add_action( 'init', 'tsvd_tool_portal_grant_caps', 20 );

function tsvd_tool_portal_grant_caps() {
	foreach ( TSVD_TOOL_PORTAL_ROLES as $role_slug ) {
		$role = get_role( $role_slug );
		if ( $role ) {
			tsvd_tool_portal_grant_role_caps( $role );
		}
	}
}

function tsvd_tool_portal_grant_role_caps( WP_Role $role ) {
	$slugs = array_filter( array_keys( tsvd_tool_portal_tools() ), 'tsvd_tool_portal_has_own_cap' );
	foreach ( $slugs as $slug ) {
		$cap = tsvd_tool_portal_cap( $slug );
		if ( ! $role->has_cap( $cap ) ) {
			$role->add_cap( $cap );
		}
	}
}
