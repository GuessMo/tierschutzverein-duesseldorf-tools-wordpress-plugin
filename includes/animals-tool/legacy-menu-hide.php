<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_AT_LEGACY_PARENT = 'edit.php?post_type=animals';

add_action( 'admin_menu', 'tsvd_at_hide_legacy_menu', 999 );

function tsvd_at_hide_legacy_menu() {
	if ( ! tsvd_tools_theme_active() ) {
		return;
	}
	remove_submenu_page( TSVD_AT_LEGACY_PARENT, TSVD_AT_LEGACY_PARENT );
	remove_submenu_page( TSVD_AT_LEGACY_PARENT, 'post-new.php?post_type=animals' );
	global $submenu;
	if ( empty( $submenu[ TSVD_AT_LEGACY_PARENT ] ) ) {
		remove_menu_page( TSVD_AT_LEGACY_PARENT );
	}
}
