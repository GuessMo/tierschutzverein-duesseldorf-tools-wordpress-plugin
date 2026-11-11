<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_SRC_ID = '/(?P<id>\d+)';

add_action( 'rest_api_init', 'tsvd_src_register_routes' );

function tsvd_src_register_routes() {
	tsvd_src_register_collection_routes();
	tsvd_src_register_taxonomy_routes();
	tsvd_src_register_term_routes();
	tsvd_src_register_entry_routes();
}

function tsvd_src_endpoint( $action, $callback, $args = array() ) {
	$methods = array(
		'list'   => WP_REST_Server::READABLE,
		'create' => WP_REST_Server::CREATABLE,
		'update' => WP_REST_Server::EDITABLE,
		'delete' => WP_REST_Server::DELETABLE,
	);
	return array(
		'methods'  => $methods[ $action ],
		'callback' => $callback,
		'args'     => $args,
	);
}

function tsvd_src_item_routes( $path, $entity, $args ) {
	tsvd_src_route(
		$path . TSVD_SRC_ID,
		array(
			tsvd_src_endpoint( 'update', 'tsvd_src_rest_update_' . $entity, $args ),
			tsvd_src_endpoint( 'delete', 'tsvd_src_rest_delete_' . $entity ),
		)
	);
}

function tsvd_src_register_collection_routes() {
	tsvd_src_route(
		'collections',
		array(
			tsvd_src_endpoint( 'list', 'tsvd_src_rest_list_collections' ),
			tsvd_src_endpoint(
				'create',
				'tsvd_src_rest_create_collection',
				tsvd_src_collection_args( true )
			),
		)
	);
	tsvd_src_route(
		'collections' . TSVD_SRC_ID,
		array( tsvd_src_endpoint( 'list', 'tsvd_src_rest_get_collection' ) )
	);
	tsvd_src_item_routes( 'collections', 'collection', tsvd_src_collection_args( false ) );
}

function tsvd_src_register_taxonomy_routes() {
	tsvd_src_route(
		'collections' . TSVD_SRC_ID . '/taxonomies',
		array(
			tsvd_src_endpoint( 'list', 'tsvd_src_rest_list_taxonomies' ),
			tsvd_src_endpoint(
				'create',
				'tsvd_src_rest_create_taxonomy',
				tsvd_src_taxonomy_args( true )
			),
		)
	);
	tsvd_src_item_routes( 'taxonomies', 'taxonomy', tsvd_src_taxonomy_args( false ) );
}

function tsvd_src_register_term_routes() {
	tsvd_src_route(
		'collections' . TSVD_SRC_ID . '/terms',
		array( tsvd_src_endpoint( 'list', 'tsvd_src_rest_list_terms' ) )
	);
	tsvd_src_route(
		'taxonomies' . TSVD_SRC_ID . '/terms',
		array(
			tsvd_src_endpoint( 'create', 'tsvd_src_rest_create_term', tsvd_src_term_args( true ) ),
		)
	);
	tsvd_src_item_routes( 'terms', 'term', tsvd_src_term_args( false ) );
}

function tsvd_src_register_entry_routes() {
	tsvd_src_route(
		'collections' . TSVD_SRC_ID . '/entries',
		array(
			tsvd_src_endpoint( 'list', 'tsvd_src_rest_list_entries' ),
			tsvd_src_endpoint(
				'create',
				'tsvd_src_rest_create_entry',
				tsvd_src_entry_args( true )
			),
		)
	);
	tsvd_src_item_routes( 'entries', 'entry', tsvd_src_entry_args( false ) );
}
