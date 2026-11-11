<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_src_taxonomies_for( $collection_id ) {
	global $wpdb;
	$table = tsvd_src_table( 'taxonomies' );
	$rows  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE collection_id = %d ORDER BY name ASC",
			$collection_id
		),
		ARRAY_A
	);
	return array_map( 'tsvd_src_format_taxonomy', $rows ? $rows : array() );
}

function tsvd_src_taxonomy_ids( $collection_id ) {
	return array_column( tsvd_src_taxonomies_for( $collection_id ), 'id' );
}

function tsvd_src_taxonomy_row( $id ) {
	global $wpdb;
	$table = tsvd_src_table( 'taxonomies' );
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A );
}

function tsvd_src_format_taxonomy( $row ) {
	return array(
		'id'              => (int) $row['id'],
		'collection_id'   => (int) $row['collection_id'],
		'name'            => $row['name'],
		'is_hierarchical' => (bool) (int) $row['is_hierarchical'],
	);
}

function tsvd_src_taxonomy_insert( $fields ) {
	global $wpdb;
	$fields['created_at'] = tsvd_src_now();
	return $wpdb->insert( tsvd_src_table( 'taxonomies' ), $fields ) ? (int) $wpdb->insert_id : 0;
}

function tsvd_src_taxonomy_update( $id, $fields ) {
	global $wpdb;
	if ( ! $fields ) {
		return true;
	}
	if ( isset( $fields['is_hierarchical'] ) && ! $fields['is_hierarchical'] ) {
		$terms = tsvd_src_table( 'terms' );
		$wpdb->update( $terms, array( 'parent_id' => null ), array( 'taxonomy_id' => $id ) );
	}
	return false !== $wpdb->update( tsvd_src_table( 'taxonomies' ), $fields, array( 'id' => $id ) );
}

function tsvd_src_taxonomy_delete( $id ) {
	global $wpdb;
	$terms = tsvd_src_table( 'terms' );
	$links = tsvd_src_table( 'entry_terms' );
	$wpdb->query(
		$wpdb->prepare(
			"DELETE l FROM {$links} l INNER JOIN {$terms} t ON t.id = l.term_id
			 WHERE t.taxonomy_id = %d",
			$id
		)
	);
	$wpdb->delete( $terms, array( 'taxonomy_id' => $id ) );
	return false !== $wpdb->delete( tsvd_src_table( 'taxonomies' ), array( 'id' => $id ) );
}
