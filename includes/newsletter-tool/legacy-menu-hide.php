<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'tsvd_nlt_hide_legacy_menu', 999 );

function tsvd_nlt_hide_legacy_menu() {
	if ( ! tsvd_tools_theme_active() ) {
		return;
	}
	remove_menu_page( 'tsvd-newsletter' );
	remove_menu_page( 'edit.php?post_type=' . TSVD_NEWSLETTER_CPT );
}
