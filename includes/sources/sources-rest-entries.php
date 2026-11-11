<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_SRC_ENTRY_FIELDS = array(
	'title',
	'body',
	'url',
	'publisher',
	'year',
	'kind',
	'accessed_at',
);

function tsvd_src_entry_fields( WP_REST_Request $request ) {
	$fields = tsvd_src_pick( $request, TSVD_SRC_ENTRY_FIELDS );
	if ( array_key_exists( 'accessed_at', $fields ) && '' === $fields['accessed_at'] ) {
		$fields['accessed_at'] = null;
	}
	return $fields;
}

function tsvd_src_entry_input_error( WP_REST_Request $request, $fields, $collection_id ) {
	$error = tsvd_src_check_name( $fields, 'title', __( 'Der Titel', 'tsvd' ) );
	if ( $error || ! $request->has_param( 'term_ids' ) ) {
		return $error;
	}
	$term_ids = $request->get_param( 'term_ids' );
	if ( ! tsvd_src_terms_outside_collection( $term_ids, $collection_id ) ) {
		return null;
	}
	$message = __( 'Mindestens ein Begriff gehört nicht zu dieser Sammlung.', 'tsvd' );
	return tsvd_src_invalid( $message );
}

function tsvd_src_entry_save_terms( WP_REST_Request $request, $entry_id ) {
	if ( $request->has_param( 'term_ids' ) ) {
		$term_ids = array_map( 'intval', $request->get_param( 'term_ids' ) );
		tsvd_src_entry_set_terms( $entry_id, $term_ids );
	}
}

function tsvd_src_rest_list_entries( WP_REST_Request $request ) {
	$collection_id = (int) $request['id'];
	if ( ! tsvd_src_collection_row( $collection_id ) ) {
		return tsvd_src_not_found();
	}
	return rest_ensure_response( tsvd_src_entries_for( $collection_id ) );
}

function tsvd_src_rest_create_entry( WP_REST_Request $request ) {
	$collection_id = (int) $request['id'];
	if ( ! tsvd_src_collection_row( $collection_id ) ) {
		return tsvd_src_not_found();
	}
	$fields = tsvd_src_entry_fields( $request );
	$error  = tsvd_src_entry_input_error( $request, $fields, $collection_id );
	if ( $error ) {
		return $error;
	}
	$fields['collection_id'] = $collection_id;
	$id                      = tsvd_src_entry_insert( $fields );
	if ( ! $id ) {
		return tsvd_src_db_error();
	}
	tsvd_src_entry_save_terms( $request, $id );
	return tsvd_src_created( tsvd_src_entry_get( $id ) );
}

function tsvd_src_rest_update_entry( WP_REST_Request $request ) {
	$id  = (int) $request['id'];
	$row = tsvd_src_entry_row( $id );
	if ( ! $row ) {
		return tsvd_src_not_found();
	}
	$fields = tsvd_src_entry_fields( $request );
	$error  = tsvd_src_entry_input_error( $request, $fields, (int) $row['collection_id'] );
	if ( $error ) {
		return $error;
	}
	if ( ! tsvd_src_entry_update( $id, $fields ) ) {
		return tsvd_src_db_error();
	}
	tsvd_src_entry_save_terms( $request, $id );
	return rest_ensure_response( tsvd_src_entry_get( $id ) );
}

function tsvd_src_rest_delete_entry( WP_REST_Request $request ) {
	$id = (int) $request['id'];
	if ( ! tsvd_src_entry_row( $id ) ) {
		return tsvd_src_not_found();
	}
	return tsvd_src_entry_delete( $id ) ? tsvd_src_deleted( $id ) : tsvd_src_db_error();
}
