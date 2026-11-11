<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_src_terms_for_collection( $collection_id ) {
	global $wpdb;
	$terms      = tsvd_src_table( 'terms' );
	$taxonomies = tsvd_src_table( 'taxonomies' );
	$rows       = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT t.* FROM {$terms} t INNER JOIN {$taxonomies} x ON x.id = t.taxonomy_id
			 WHERE x.collection_id = %d ORDER BY t.name ASC",
			$collection_id
		),
		ARRAY_A
	);
	return array_map( 'tsvd_src_format_term', $rows ? $rows : array() );
}

function tsvd_src_term_row( $id ) {
	global $wpdb;
	$table = tsvd_src_table( 'terms' );
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A );
}

function tsvd_src_format_term( $row ) {
	return array(
		'id'          => (int) $row['id'],
		'taxonomy_id' => (int) $row['taxonomy_id'],
		'parent_id'   => null === $row['parent_id'] ? null : (int) $row['parent_id'],
		'name'        => $row['name'],
	);
}

function tsvd_src_term_insert( $fields ) {
	global $wpdb;
	$fields['created_at'] = tsvd_src_now();
	return $wpdb->insert( tsvd_src_table( 'terms' ), $fields ) ? (int) $wpdb->insert_id : 0;
}

function tsvd_src_term_update( $id, $fields ) {
	global $wpdb;
	if ( ! $fields ) {
		return true;
	}
	return false !== $wpdb->update( tsvd_src_table( 'terms' ), $fields, array( 'id' => $id ) );
}

function tsvd_src_term_delete( $id ) {
	global $wpdb;
	$row = tsvd_src_term_row( $id );
	if ( ! $row ) {
		return false;
	}
	$terms = tsvd_src_table( 'terms' );
	$wpdb->update( $terms, array( 'parent_id' => $row['parent_id'] ), array( 'parent_id' => $id ) );
	$wpdb->delete( tsvd_src_table( 'entry_terms' ), array( 'term_id' => $id ) );
	return false !== $wpdb->delete( $terms, array( 'id' => $id ) );
}

function tsvd_src_term_is_descendant( $candidate_id, $ancestor_id ) {
	$current = $candidate_id;
	$guard   = 0;
	while ( $current && $guard < 100 ) {
		if ( (int) $current === (int) $ancestor_id ) {
			return true;
		}
		$row     = tsvd_src_term_row( $current );
		$current = $row ? $row['parent_id'] : null;
		++$guard;
	}
	return false;
}
