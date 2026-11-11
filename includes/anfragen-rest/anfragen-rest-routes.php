<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'tsvd_anfragen_rest_register_routes' );

function tsvd_anfragen_rest_register_routes() {
	tsvd_anfragen_rest_register_list_routes();
	tsvd_anfragen_rest_register_item_routes();
	tsvd_anfragen_rest_register_message_routes();
	tsvd_anfragen_rest_register_candidate_routes();
}

function tsvd_anfragen_rest_register_list_routes() {
	$read  = WP_REST_Server::READABLE;
	$write = WP_REST_Server::EDITABLE;
	tsvd_anfragen_rest_route( '', $read, 'tsvd_anfragen_rest_list' );
	tsvd_anfragen_rest_route( '/context', $read, 'tsvd_anfragen_rest_context' );
	tsvd_anfragen_rest_route( '/relayed', $read, 'tsvd_anfragen_rest_relayed' );
	tsvd_anfragen_rest_route( '/settings', $write, 'tsvd_anfragen_rest_update_settings' );
}

function tsvd_anfragen_rest_register_item_routes() {
	$item = '/' . TSVD_ANFRAGEN_REST_ID;
	tsvd_anfragen_rest_route( $item, WP_REST_Server::READABLE, 'tsvd_anfragen_rest_detail' );
	tsvd_anfragen_rest_route( $item, WP_REST_Server::DELETABLE, 'tsvd_anfragen_rest_delete' );
	tsvd_anfragen_rest_route(
		$item . '/assign',
		WP_REST_Server::CREATABLE,
		'tsvd_anfragen_rest_assign'
	);
	tsvd_anfragen_rest_route(
		$item . '/actions/(?P<action>[a-z-]+)',
		WP_REST_Server::CREATABLE,
		'tsvd_anfragen_rest_run_action'
	);
}

function tsvd_anfragen_rest_register_message_routes() {
	$messages = '/' . TSVD_ANFRAGEN_REST_ID . '/messages';
	$message  = $messages . '/(?P<reply>\d+)';
	tsvd_anfragen_rest_route(
		$messages,
		WP_REST_Server::CREATABLE,
		'tsvd_anfragen_rest_create_message'
	);
	tsvd_anfragen_rest_route(
		$message,
		WP_REST_Server::EDITABLE,
		'tsvd_anfragen_rest_update_message'
	);
	tsvd_anfragen_rest_route(
		$message,
		WP_REST_Server::DELETABLE,
		'tsvd_anfragen_rest_delete_message'
	);
	tsvd_anfragen_rest_route(
		$message . '/send',
		WP_REST_Server::CREATABLE,
		'tsvd_anfragen_rest_send_message'
	);
}

function tsvd_anfragen_rest_register_candidate_routes() {
	$permission = 'tsvd_anfragen_rest_can_manage_candidates';
	tsvd_anfragen_rest_route(
		'/candidates',
		WP_REST_Server::READABLE,
		'tsvd_anfragen_rest_candidates',
		$permission
	);
	tsvd_anfragen_rest_route(
		'/candidates/' . TSVD_ANFRAGEN_REST_ID . '/(?P<decision>approve|reject)',
		WP_REST_Server::CREATABLE,
		'tsvd_anfragen_rest_decide_candidate',
		$permission
	);
}
