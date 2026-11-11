<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_src_collection_fields( WP_REST_Request $request ) {
	$fields = tsvd_src_pick( $request, array( 'name', 'description' ) );
	if ( $request->has_param( 'shared_with' ) ) {
		$fields['shared_with'] = (string) wp_json_encode( $request->get_param( 'shared_with' ) );
	}
	return $fields;
}

function tsvd_src_rest_list_collections() {
	return rest_ensure_response( tsvd_src_collections_all() );
}

function tsvd_src_rest_get_collection( WP_REST_Request $request ) {
	$row = tsvd_src_collection_row( (int) $request['id'] );
	return $row ? rest_ensure_response( tsvd_src_format_collection( $row ) ) : tsvd_src_not_found();
}

function tsvd_src_rest_create_collection( WP_REST_Request $request ) {
	$fields = tsvd_src_collection_fields( $request );
	$error  = tsvd_src_check_name( $fields, 'name', __( 'Der Name der Sammlung', 'tsvd' ) );
	if ( $error ) {
		return $error;
	}
	$id = tsvd_src_collection_insert( $fields );
	if ( ! $id ) {
		return tsvd_src_db_error();
	}
	return tsvd_src_created( tsvd_src_format_collection( tsvd_src_collection_row( $id ) ) );
}

function tsvd_src_rest_update_collection( WP_REST_Request $request ) {
	$id = (int) $request['id'];
	if ( ! tsvd_src_collection_row( $id ) ) {
		return tsvd_src_not_found();
	}
	$fields = tsvd_src_collection_fields( $request );
	$error  = tsvd_src_check_name( $fields, 'name', __( 'Der Name der Sammlung', 'tsvd' ) );
	if ( $error ) {
		return $error;
	}
	if ( ! tsvd_src_collection_update( $id, $fields ) ) {
		return tsvd_src_db_error();
	}
	return rest_ensure_response( tsvd_src_format_collection( tsvd_src_collection_row( $id ) ) );
}

function tsvd_src_rest_delete_collection( WP_REST_Request $request ) {
	$id = (int) $request['id'];
	if ( ! tsvd_src_collection_row( $id ) ) {
		return tsvd_src_not_found();
	}
	return tsvd_src_collection_delete( $id ) ? tsvd_src_deleted( $id ) : tsvd_src_db_error();
}
