<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_init', 'tsvd_remove_welcome_panel' );

function tsvd_remove_welcome_panel() {
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
}
