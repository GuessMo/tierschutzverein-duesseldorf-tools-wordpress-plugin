<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_AT_POST_TYPE = 'animals';

function tsvd_at_error( $code, $message, $status ) {
	return new WP_Error( 'tsvd_at_' . $code, $message, array( 'status' => $status ) );
}

function tsvd_at_forbidden() {
	return tsvd_at_error(
		'forbidden',
		__( 'Dafür fehlt dir die Berechtigung.', 'tsv-tools' ),
		rest_authorization_required_code()
	);
}

function tsvd_at_not_found() {
	return tsvd_at_error( 'not_found', __( 'Das Tier wurde nicht gefunden.', 'tsv-tools' ), 404 );
}

function tsvd_at_can_use() {
	return current_user_can( 'edit_animals' ) ? true : tsvd_at_forbidden();
}

function tsvd_at_request_post( WP_REST_Request $request ) {
	$post = get_post( (int) $request['id'] );
	return ( $post && TSVD_AT_POST_TYPE === $post->post_type ) ? $post : null;
}

function tsvd_at_can_post( WP_REST_Request $request, $meta_cap ) {
	$allowed = tsvd_at_can_use();
	if ( true !== $allowed ) {
		return $allowed;
	}
	$post = tsvd_at_request_post( $request );
	if ( ! $post ) {
		return tsvd_at_not_found();
	}
	return current_user_can( $meta_cap, $post->ID ) ? true : tsvd_at_forbidden();
}

function tsvd_at_can_edit( WP_REST_Request $request ) {
	return tsvd_at_can_post( $request, 'edit_post' );
}

function tsvd_at_can_delete( WP_REST_Request $request ) {
	return tsvd_at_can_post( $request, 'delete_post' );
}

function tsvd_at_can_upload() {
	$allowed = tsvd_at_can_use();
	if ( true !== $allowed ) {
		return $allowed;
	}
	return current_user_can( 'upload_files' ) ? true : tsvd_at_forbidden();
}

function tsvd_at_can_publish() {
	return current_user_can( 'publish_animals' );
}

function tsvd_at_meta( $post_id, $key, $fallback = '' ) {
	$value = get_post_meta( $post_id, $key, true );
	return ( '' === $value || false === $value || null === $value ) ? $fallback : $value;
}

function tsvd_at_ids( $value ) {
	if ( is_string( $value ) ) {
		$value = explode( ',', $value );
	}
	return array_values( array_filter( array_map( 'intval', (array) $value ) ) );
}

function tsvd_at_str( $post_id, $key ) {
	return (string) get_post_meta( $post_id, $key, true );
}

function tsvd_at_id_list( $value ) {
	return implode( ',', tsvd_at_ids( $value ) );
}

function tsvd_at_mediator( $post_id ) {
	return (string) tsvd_at_meta( $post_id, 'animal_mediator', 'tierschutzverein' );
}

function tsvd_at_name( WP_Post $post ) {
	return (string) tsvd_at_meta( $post->ID, 'animal_name', $post->post_title );
}
