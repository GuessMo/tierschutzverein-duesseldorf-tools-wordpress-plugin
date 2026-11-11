<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ANFRAGEN_REST_NAMESPACE = 'tsvd-tools/v1';
const TSVD_ANFRAGEN_REST_ID        = '(?P<id>\d+)';

function tsvd_anfragen_rest_can_use() {
	return current_user_can( 'manage_tsvd_anfragen' );
}

function tsvd_anfragen_rest_can_manage_candidates() {
	return current_user_can( 'manage_options' );
}

function tsvd_anfragen_rest_route( $path, $method, $callback, $permission = '' ) {
	register_rest_route(
		TSVD_ANFRAGEN_REST_NAMESPACE,
		'/anfragen' . $path,
		array(
			'methods'             => $method,
			'callback'            => $callback,
			'permission_callback' => $permission ? $permission : 'tsvd_anfragen_rest_can_use',
		)
	);
}

function tsvd_anfragen_rest_not_found() {
	return new WP_Error(
		'tsvd_anfragen_not_found',
		__( 'Die Anfrage wurde nicht gefunden.', 'tsvd' ),
		array( 'status' => 404 )
	);
}

function tsvd_anfragen_rest_failed() {
	return new WP_Error(
		'tsvd_anfragen_failed',
		__( 'Die Änderung konnte nicht gespeichert werden.', 'tsvd' ),
		array( 'status' => 500 )
	);
}

function tsvd_anfragen_rest_error( WP_Error $error ) {
	$status = 'not_found' === $error->get_error_code() ? 404 : 400;
	return new WP_Error(
		'tsvd_anfragen_' . $error->get_error_code(),
		$error->get_error_message(),
		array( 'status' => $status )
	);
}

function tsvd_anfragen_rest_message( $message ) {
	return rest_ensure_response( array( 'message' => $message ) );
}

function tsvd_anfragen_rest_anfrage( WP_REST_Request $request ) {
	return tsvd_halter_get_anfrage( (int) $request['id'] );
}

function tsvd_anfragen_rest_text( WP_REST_Request $request, $key ) {
	return sanitize_textarea_field( (string) $request->get_param( $key ) );
}
