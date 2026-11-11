<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_src_taxonomy_fields( WP_REST_Request $request ) {
	$fields = tsvd_src_pick( $request, array( 'name' ) );
	if ( $request->has_param( 'is_hierarchical' ) ) {
		$fields['is_hierarchical'] = $request->get_param( 'is_hierarchical' ) ? 1 : 0;
	}
	return $fields;
}

function tsvd_src_rest_list_taxonomies( WP_REST_Request $request ) {
	$collection_id = (int) $request['id'];
	if ( ! tsvd_src_collection_row( $collection_id ) ) {
		return tsvd_src_not_found();
	}
	return rest_ensure_response( tsvd_src_taxonomies_for( $collection_id ) );
}

function tsvd_src_rest_create_taxonomy( WP_REST_Request $request ) {
	$collection_id = (int) $request['id'];
	if ( ! tsvd_src_collection_row( $collection_id ) ) {
		return tsvd_src_not_found();
	}
	$fields = tsvd_src_taxonomy_fields( $request );
	$error  = tsvd_src_check_name( $fields, 'name', __( 'Der Name der Taxonomie', 'tsvd' ) );
	if ( $error ) {
		return $error;
	}
	$fields['collection_id'] = $collection_id;
	$id                      = tsvd_src_taxonomy_insert( $fields );
	if ( ! $id ) {
		return tsvd_src_db_error();
	}
	return tsvd_src_created( tsvd_src_format_taxonomy( tsvd_src_taxonomy_row( $id ) ) );
}

function tsvd_src_rest_update_taxonomy( WP_REST_Request $request ) {
	$id = (int) $request['id'];
	if ( ! tsvd_src_taxonomy_row( $id ) ) {
		return tsvd_src_not_found();
	}
	$fields = tsvd_src_taxonomy_fields( $request );
	$error  = tsvd_src_check_name( $fields, 'name', __( 'Der Name der Taxonomie', 'tsvd' ) );
	if ( $error ) {
		return $error;
	}
	if ( ! tsvd_src_taxonomy_update( $id, $fields ) ) {
		return tsvd_src_db_error();
	}
	return rest_ensure_response( tsvd_src_format_taxonomy( tsvd_src_taxonomy_row( $id ) ) );
}

function tsvd_src_rest_delete_taxonomy( WP_REST_Request $request ) {
	$id = (int) $request['id'];
	if ( ! tsvd_src_taxonomy_row( $id ) ) {
		return tsvd_src_not_found();
	}
	return tsvd_src_taxonomy_delete( $id ) ? tsvd_src_deleted( $id ) : tsvd_src_db_error();
}
