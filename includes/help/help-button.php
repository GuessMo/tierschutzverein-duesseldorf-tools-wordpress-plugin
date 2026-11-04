<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_help_register_assets() {
	wp_register_style( 'tsvd-help', TSVD_TOOLS_URL . 'assets/help.css', array( 'dashicons' ), TSVD_TOOLS_ASSET_VERSION );
	wp_register_script( 'tsvd-help', TSVD_TOOLS_URL . 'assets/help.js', array(), TSVD_TOOLS_ASSET_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'tsvd_help_register_assets', 1 );

function tsvd_help_enqueue( array $targets = array() ) {
	static $is_enqueued = false;
	if ( $is_enqueued ) {
		return;
	}
	$is_enqueued = true;
	wp_enqueue_style( 'tsvd-help' );
	wp_enqueue_script( 'tsvd-help' );
	$config = array(
		'label'   => __( 'Hilfe', 'tsvd' ),
		'targets' => array_values( $targets ),
	);
	wp_add_inline_script( 'tsvd-help', 'window.tsvdHelp = ' . wp_json_encode( $config ) . ';', 'before' );
}

function tsvd_help_btn_html( $text ) {
	static $index = 0;
	$pop_id = 'tsvd-help-pop-' . ( ++$index );
	return '<span class="tsvd-help-wrap">'
		. '<button type="button" class="tsvd-help-doc" data-help="' . esc_attr( $text ) . '"'
		. ' aria-label="' . esc_attr__( 'Hilfe', 'tsvd' ) . '" aria-expanded="false"'
		. ' aria-controls="' . esc_attr( $pop_id ) . '">'
		. '<span class="dashicons dashicons-editor-help" aria-hidden="true"></span></button>'
		. '<span class="tsvd-help-pop" id="' . esc_attr( $pop_id ) . '" role="note" hidden></span>'
		. '</span>';
}
