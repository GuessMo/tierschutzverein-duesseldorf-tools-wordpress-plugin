<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ARCHITECTURE_MAP_BLOCK      = 'tsvd/architecture-map';
const TSVD_ARCHITECTURE_MAP_CAPABILITY = 'manage_options';

add_action( 'init', 'tsvd_architecture_map_register_block' );

function tsvd_architecture_map_register_assets() {
	wp_register_style(
		'tsvd-architecture-map',
		TSVD_TOOLS_URL . 'assets/architecture-map.css',
		array( 'dashicons' ),
		TSVD_TOOLS_ASSET_VERSION
	);
	wp_register_style(
		'tsvd-architecture-map-details',
		TSVD_TOOLS_URL . 'assets/architecture-map-details.css',
		array( 'tsvd-architecture-map' ),
		TSVD_TOOLS_ASSET_VERSION
	);
	wp_register_script(
		'tsvd-architecture-map',
		TSVD_TOOLS_URL . 'assets/architecture-map.js',
		array(),
		TSVD_TOOLS_ASSET_VERSION,
		true
	);
	wp_register_script(
		'tsvd-architecture-map-editor',
		TSVD_TOOLS_URL . 'assets/architecture-map-editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-server-side-render' ),
		TSVD_TOOLS_ASSET_VERSION,
		true
	);
}

function tsvd_architecture_map_register_block() {
	tsvd_architecture_map_register_assets();
	register_block_type(
		TSVD_ARCHITECTURE_MAP_BLOCK,
		array(
			'api_version'          => 3,
			'title'                => __( 'Aufbau-Plan', 'tsvd' ),
			'editor_script_handles' => array( 'tsvd-architecture-map-editor' ),
			'editor_style_handles' => array( 'tsvd-architecture-map-details' ),
			'render_callback'      => 'tsvd_architecture_map_render',
		)
	);
}

function tsvd_architecture_map_render() {
	$content = tsvd_architecture_map_content();
	if ( ! $content || ! current_user_can( TSVD_ARCHITECTURE_MAP_CAPABILITY ) ) {
		return '';
	}
	wp_enqueue_style( 'tsvd-architecture-map-details' );
	wp_enqueue_script( 'tsvd-architecture-map' );
	return '<div class="tsvd-am" data-tsvd-am>'
		. tsvd_architecture_map_render_intro( $content['intro'] )
		. tsvd_architecture_map_render_map( $content )
		. tsvd_architecture_map_render_paths( $content['paths'], $content['nodes'] )
		. tsvd_architecture_map_render_wordpress( $content['wordpress'] )
		. '</div>';
}
