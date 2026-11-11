<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_NLT_NAMESPACE = 'tsvd-tools/v1';
const TSVD_NLT_ID        = '/(?P<id>\d+)';

add_action( 'rest_api_init', 'tsvd_nlt_register_routes' );

function tsvd_nlt_can_use() {
	return current_user_can( TSVD_NEWSLETTER_CAP );
}

function tsvd_nlt_route( $path, $methods, $callback ) {
	register_rest_route(
		TSVD_NLT_NAMESPACE,
		'/newsletter' . $path,
		array(
			'methods'             => $methods,
			'callback'            => $callback,
			'permission_callback' => 'tsvd_nlt_can_use',
		)
	);
}

function tsvd_nlt_register_routes() {
	tsvd_nlt_route( '', WP_REST_Server::READABLE, 'tsvd_nlt_rest_list' );
	tsvd_nlt_route( '', WP_REST_Server::CREATABLE, 'tsvd_nlt_rest_create' );
	tsvd_nlt_route( '/subscribers', WP_REST_Server::READABLE, 'tsvd_nlt_rest_subscribers' );
	tsvd_nlt_route( '/subscribers', WP_REST_Server::EDITABLE, 'tsvd_nlt_rest_save_subscribers' );
	tsvd_nlt_route( '/media', WP_REST_Server::CREATABLE, 'tsvd_nlt_rest_media' );
	tsvd_nlt_route( TSVD_NLT_ID, WP_REST_Server::READABLE, 'tsvd_nlt_rest_get' );
	tsvd_nlt_route( TSVD_NLT_ID, WP_REST_Server::EDITABLE, 'tsvd_nlt_rest_update' );
	tsvd_nlt_route( TSVD_NLT_ID, WP_REST_Server::DELETABLE, 'tsvd_nlt_rest_delete' );
	tsvd_nlt_route( TSVD_NLT_ID . '/preview', WP_REST_Server::READABLE, 'tsvd_nlt_rest_preview' );
	tsvd_nlt_route( TSVD_NLT_ID . '/send', WP_REST_Server::CREATABLE, 'tsvd_nlt_rest_send' );
}

function tsvd_nlt_error( $code, $message, $status ) {
	return new WP_Error( 'tsvd_nlt_' . $code, $message, array( 'status' => $status ) );
}

function tsvd_nlt_issue_post( WP_REST_Request $request ) {
	$post = get_post( (int) $request['id'] );
	if ( ! $post || TSVD_NEWSLETTER_CPT !== $post->post_type || 'trash' === $post->post_status ) {
		return tsvd_nlt_error( 'not_found', __( 'Diesen Newsletter gibt es nicht.', 'tsvd' ), 404 );
	}
	return $post;
}
