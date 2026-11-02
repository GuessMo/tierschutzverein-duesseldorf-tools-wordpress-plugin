<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_architecture_map_versions() {
	global $wpdb;
	return array(
		'WordPress' => get_bloginfo( 'version' ),
		'PHP'       => PHP_VERSION,
		'Datenbank' => $wpdb->db_version(),
	);
}

function tsvd_architecture_map_theme_label() {
	$theme = wp_get_theme();
	return trim( $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ) );
}

function tsvd_architecture_map_active_plugins() {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$active = array_flip( (array) get_option( 'active_plugins', array() ) );
	return array_values( array_intersect_key( get_plugins(), $active ) );
}

function tsvd_architecture_map_post_type_labels( $is_public ) {
	$args = $is_public
		? array( 'public' => true )
		: array(
			'public'   => false,
			'_builtin' => false,
			'show_ui'  => true,
		);
	$labels = array();
	foreach ( get_post_types( $args, 'objects' ) as $name => $type ) {
		if ( 'attachment' !== $name ) {
			$labels[] = $type->labels->name;
		}
	}
	return $labels;
}

function tsvd_architecture_map_role_labels() {
	$labels = array_map( 'translate_user_role', wp_roles()->role_names );
	$counts = array_count_values( $labels );
	$result = array();
	foreach ( $labels as $slug => $label ) {
		$result[] = $counts[ $label ] > 1 ? $label . ' (' . $slug . ')' : $label;
	}
	return $result;
}
