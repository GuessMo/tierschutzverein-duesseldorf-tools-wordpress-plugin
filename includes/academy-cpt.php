<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ACADEMY_CPT          = 'tsvd_academy';
const TSVD_ACADEMY_TAX          = 'tsvd_academy_topic';
const TSVD_ACADEMY_EDIT_CAP     = 'manage_options';
const TSVD_ACADEMY_ADMIN_TERM   = 'admin';
const TSVD_ACADEMY_SEED_VERSION = 1;

add_action( 'init', 'tsvd_academy_register_cpt' );
add_action( 'init', 'tsvd_academy_register_taxonomy' );
add_action( 'admin_init', 'tsvd_academy_seed_topics' );

function tsvd_academy_edit_caps() {
	$keys = array(
		'edit_posts',
		'edit_others_posts',
		'edit_private_posts',
		'edit_published_posts',
		'publish_posts',
		'read_private_posts',
		'delete_posts',
		'delete_others_posts',
		'delete_private_posts',
		'delete_published_posts',
		'create_posts',
	);
	return array_fill_keys( $keys, TSVD_ACADEMY_EDIT_CAP );
}

function tsvd_academy_register_cpt() {
	register_post_type(
		TSVD_ACADEMY_CPT,
		array(
			'labels'                => array(
				'name'          => __( 'Schulungen', 'tsvd' ),
				'singular_name' => __( 'Schulung', 'tsvd' ),
				'add_new'       => __( 'Neu', 'tsvd' ),
				'add_new_item'  => __( 'Neue Schulung', 'tsvd' ),
				'edit_item'     => __( 'Schulung bearbeiten', 'tsvd' ),
				'all_items'     => __( 'Alle Schulungen', 'tsvd' ),
				'menu_name'     => __( 'Academy', 'tsvd' ),
			),
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => false,
			'show_in_rest'          => true,
			'rest_controller_class' => 'TSVD_Academy_REST_Controller',
			'supports'              => array( 'title', 'editor', 'excerpt', 'revisions' ),
			'capabilities'          => tsvd_academy_edit_caps(),
			'map_meta_cap'          => true,
			'rewrite'               => false,
			'query_var'             => false,
		)
	);
}

function tsvd_academy_register_taxonomy() {
	register_taxonomy(
		TSVD_ACADEMY_TAX,
		TSVD_ACADEMY_CPT,
		array(
			'labels'                => array(
				'name'          => __( 'Themen', 'tsvd' ),
				'singular_name' => __( 'Thema', 'tsvd' ),
				'add_new_item'  => __( 'Neues Thema', 'tsvd' ),
				'edit_item'     => __( 'Thema bearbeiten', 'tsvd' ),
			),
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => false,
			'show_in_rest'          => true,
			'rest_controller_class' => 'TSVD_Academy_Terms_REST_Controller',
			'show_admin_column'     => true,
			'hierarchical'          => false,
			'rewrite'               => false,
			'query_var'             => false,
			'capabilities'          => array(
				'manage_terms' => TSVD_ACADEMY_EDIT_CAP,
				'edit_terms'   => TSVD_ACADEMY_EDIT_CAP,
				'delete_terms' => TSVD_ACADEMY_EDIT_CAP,
				'assign_terms' => TSVD_ACADEMY_EDIT_CAP,
			),
		)
	);
}

function tsvd_academy_default_topics() {
	return array(
		TSVD_ACADEMY_ADMIN_TERM => __( 'Admin', 'tsvd' ),
		'anwender'              => __( 'Anwender', 'tsvd' ),
		'google-workspace'      => __( 'Google Workspace', 'tsvd' ),
		'mails'                 => __( 'Mails', 'tsvd' ),
	);
}

function tsvd_academy_seed_topics() {
	if ( (int) get_option( 'tsvd_academy_seed_version', 0 ) >= TSVD_ACADEMY_SEED_VERSION ) {
		return;
	}
	foreach ( tsvd_academy_default_topics() as $slug => $name ) {
		if ( ! term_exists( $slug, TSVD_ACADEMY_TAX ) ) {
			wp_insert_term( $name, TSVD_ACADEMY_TAX, array( 'slug' => $slug ) );
		}
	}
	update_option( 'tsvd_academy_seed_version', TSVD_ACADEMY_SEED_VERSION );
}
