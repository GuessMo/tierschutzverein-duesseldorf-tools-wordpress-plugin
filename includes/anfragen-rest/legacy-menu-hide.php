<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'tsvd_anfragen_hide_legacy_menu', 999 );

function tsvd_anfragen_hide_legacy_menu() {
	remove_submenu_page( 'edit.php?post_type=animals', 'tsvd-anfragen' );
}
