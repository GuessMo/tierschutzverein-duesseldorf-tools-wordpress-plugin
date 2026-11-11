<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_AT_NAMESPACE = 'tsvd-tools/v1';
const TSVD_AT_ID        = '/(?P<id>\d+)';

add_action( 'rest_api_init', 'tsvd_at_register_routes' );

function tsvd_at_register_routes() {
	tsvd_at_register_collection_routes();
	tsvd_at_register_item_routes();
	tsvd_at_register_action_routes();
	tsvd_at_register_media_routes();
}

function tsvd_at_endpoint( $methods, $callback, $permission ) {
	return array(
		'methods'             => $methods,
		'callback'            => $callback,
		'permission_callback' => $permission,
	);
}

function tsvd_at_route( $path, array $endpoints ) {
	register_rest_route( TSVD_AT_NAMESPACE, '/tiere' . $path, $endpoints );
}

function tsvd_at_register_collection_routes() {
	tsvd_at_route(
		'',
		array(
			tsvd_at_endpoint( WP_REST_Server::READABLE, 'tsvd_at_rest_list', 'tsvd_at_can_use' ),
			tsvd_at_endpoint( WP_REST_Server::CREATABLE, 'tsvd_at_rest_create', 'tsvd_at_can_use' ),
		)
	);
	tsvd_at_route(
		'/options',
		array(
			tsvd_at_endpoint( WP_REST_Server::READABLE, 'tsvd_at_rest_options', 'tsvd_at_can_use' ),
		)
	);
}

function tsvd_at_register_item_routes() {
	tsvd_at_route(
		TSVD_AT_ID,
		array(
			tsvd_at_endpoint( WP_REST_Server::READABLE, 'tsvd_at_rest_get', 'tsvd_at_can_edit' ),
			tsvd_at_endpoint( WP_REST_Server::EDITABLE, 'tsvd_at_rest_update', 'tsvd_at_can_edit' ),
			tsvd_at_endpoint(
				WP_REST_Server::DELETABLE,
				'tsvd_at_rest_delete',
				'tsvd_at_can_delete'
			),
		)
	);
}

function tsvd_at_register_action_routes() {
	$actions = array(
		'trash'         => array( 'tsvd_at_rest_trash', 'tsvd_at_can_delete' ),
		'restore'       => array( 'tsvd_at_rest_restore', 'tsvd_at_can_delete' ),
		'owner-approve' => array( 'tsvd_at_rest_owner_approve', 'tsvd_at_can_edit' ),
		'owner-mail'    => array( 'tsvd_at_rest_owner_mail', 'tsvd_at_can_edit' ),
	);
	foreach ( $actions as $action => $handlers ) {
		tsvd_at_route(
			TSVD_AT_ID . '/' . $action,
			array( tsvd_at_endpoint( WP_REST_Server::CREATABLE, $handlers[0], $handlers[1] ) )
		);
	}
}

function tsvd_at_register_media_routes() {
	tsvd_at_route(
		'/media',
		array(
			tsvd_at_endpoint( WP_REST_Server::CREATABLE, 'tsvd_at_rest_upload', 'tsvd_at_can_upload' ),
		)
	);
	tsvd_at_route(
		'/media' . TSVD_AT_ID . '/focus',
		array(
			tsvd_at_endpoint(
				WP_REST_Server::CREATABLE,
				'tsvd_at_rest_focus',
				'tsvd_at_can_edit_media'
			),
		)
	);
}
