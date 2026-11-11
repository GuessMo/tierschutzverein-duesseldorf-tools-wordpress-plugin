<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_src_entry_term_map( $entry_ids ) {
	global $wpdb;
	$ids = array_values( array_filter( array_map( 'intval', $entry_ids ) ) );
	if ( ! $ids ) {
		return array();
	}
	$table = tsvd_src_table( 'entry_terms' );
	$list  = implode( ',', $ids );
	$sql   = "SELECT entry_id, term_id FROM {$table} WHERE entry_id IN ({$list})";
	$rows  = $wpdb->get_results( $sql, ARRAY_A );
	$map   = array();
	foreach ( $rows ? $rows : array() as $row ) {
		$map[ (int) $row['entry_id'] ][] = (int) $row['term_id'];
	}
	return $map;
}

function tsvd_src_entry_set_terms( $entry_id, $term_ids ) {
	global $wpdb;
	$table = tsvd_src_table( 'entry_terms' );
	$wpdb->delete( $table, array( 'entry_id' => $entry_id ) );
	foreach ( array_unique( $term_ids ) as $term_id ) {
		$wpdb->insert(
			$table,
			array(
				'entry_id' => $entry_id,
				'term_id'  => $term_id,
			)
		);
	}
}

function tsvd_src_terms_outside_collection( $term_ids, $collection_id ) {
	$valid = array_column( tsvd_src_terms_for_collection( $collection_id ), 'id' );
	return array_values( array_diff( array_map( 'intval', $term_ids ), $valid ) );
}
