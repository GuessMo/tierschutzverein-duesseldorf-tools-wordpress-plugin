<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_src_validate_parent( $taxonomy, $parent_id, $term_id ) {
	if ( null === $parent_id ) {
		return null;
	}
	if ( ! (int) $taxonomy['is_hierarchical'] ) {
		return tsvd_src_invalid( __( 'Diese Taxonomie hat keine Hierarchie.', 'tsvd' ) );
	}
	$parent = tsvd_src_term_row( $parent_id );
	if ( ! $parent || (int) $parent['taxonomy_id'] !== (int) $taxonomy['id'] ) {
		$message = __( 'Der übergeordnete Begriff gehört nicht zu dieser Taxonomie.', 'tsvd' );
		return tsvd_src_invalid( $message );
	}
	if ( $term_id && tsvd_src_term_is_descendant( $parent_id, $term_id ) ) {
		return tsvd_src_invalid( __( 'Ein Begriff kann nicht unter sich selbst liegen.', 'tsvd' ) );
	}
	return null;
}

function tsvd_src_term_fields_error( $fields, $taxonomy, $term_id ) {
	$error = tsvd_src_check_name( $fields, 'name', __( 'Der Name des Begriffs', 'tsvd' ) );
	if ( $error || ! array_key_exists( 'parent_id', $fields ) ) {
		return $error;
	}
	return tsvd_src_validate_parent( $taxonomy, $fields['parent_id'], $term_id );
}

function tsvd_src_rest_list_terms( WP_REST_Request $request ) {
	$collection_id = (int) $request['id'];
	if ( ! tsvd_src_collection_row( $collection_id ) ) {
		return tsvd_src_not_found();
	}
	return rest_ensure_response( tsvd_src_terms_for_collection( $collection_id ) );
}

function tsvd_src_rest_create_term( WP_REST_Request $request ) {
	$taxonomy = tsvd_src_taxonomy_row( (int) $request['id'] );
	if ( ! $taxonomy ) {
		return tsvd_src_not_found();
	}
	$fields = tsvd_src_pick( $request, array( 'name', 'parent_id' ) );
	$error  = tsvd_src_term_fields_error( $fields, $taxonomy, 0 );
	if ( $error ) {
		return $error;
	}
	$fields['taxonomy_id'] = (int) $taxonomy['id'];
	$id                    = tsvd_src_term_insert( $fields );
	if ( ! $id ) {
		return tsvd_src_db_error();
	}
	return tsvd_src_created( tsvd_src_format_term( tsvd_src_term_row( $id ) ) );
}

function tsvd_src_rest_update_term( WP_REST_Request $request ) {
	$id   = (int) $request['id'];
	$term = tsvd_src_term_row( $id );
	if ( ! $term ) {
		return tsvd_src_not_found();
	}
	$fields = tsvd_src_pick( $request, array( 'name', 'parent_id' ) );
	$taxonomy = tsvd_src_taxonomy_row( (int) $term['taxonomy_id'] );
	$error    = tsvd_src_term_fields_error( $fields, $taxonomy, $id );
	if ( $error ) {
		return $error;
	}
	if ( ! tsvd_src_term_update( $id, $fields ) ) {
		return tsvd_src_db_error();
	}
	return rest_ensure_response( tsvd_src_format_term( tsvd_src_term_row( $id ) ) );
}

function tsvd_src_rest_delete_term( WP_REST_Request $request ) {
	$id = (int) $request['id'];
	if ( ! tsvd_src_term_row( $id ) ) {
		return tsvd_src_not_found();
	}
	return tsvd_src_term_delete( $id ) ? tsvd_src_deleted( $id ) : tsvd_src_db_error();
}
