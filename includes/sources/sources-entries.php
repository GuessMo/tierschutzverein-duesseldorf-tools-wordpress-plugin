<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_SRC_KINDS = array( 'study', 'law', 'statement', 'press', 'book', 'other' );

function tsvd_src_entries_for( $collection_id ) {
	global $wpdb;
	$table = tsvd_src_table( 'entries' );
	$rows  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE collection_id = %d ORDER BY updated_at DESC",
			$collection_id
		),
		ARRAY_A
	);
	$rows  = $rows ? $rows : array();
	$links = tsvd_src_entry_term_map( array_column( $rows, 'id' ) );
	return array_map(
		function ( $row ) use ( $links ) {
			return tsvd_src_format_entry( $row, $links[ (int) $row['id'] ] ?? array() );
		},
		$rows
	);
}

function tsvd_src_entry_row( $id ) {
	global $wpdb;
	$table = tsvd_src_table( 'entries' );
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A );
}

function tsvd_src_entry_get( $id ) {
	$row = tsvd_src_entry_row( $id );
	if ( ! $row ) {
		return null;
	}
	$links = tsvd_src_entry_term_map( array( $id ) );
	return tsvd_src_format_entry( $row, $links[ (int) $id ] ?? array() );
}

function tsvd_src_format_entry( $row, $term_ids ) {
	return array(
		'id'            => (int) $row['id'],
		'collection_id' => (int) $row['collection_id'],
		'title'         => $row['title'],
		'body'          => $row['body'],
		'url'           => $row['url'],
		'publisher'     => $row['publisher'],
		'year'          => null === $row['year'] ? null : (int) $row['year'],
		'kind'          => $row['kind'],
		'accessed_at'   => $row['accessed_at'],
		'term_ids'      => $term_ids,
		'updated_at'    => $row['updated_at'],
	);
}

function tsvd_src_entry_insert( $fields ) {
	global $wpdb;
	$now  = tsvd_src_now();
	$data = array_merge(
		array( 'body' => '' ),
		$fields,
		array(
			'created_by' => get_current_user_id(),
			'created_at' => $now,
			'updated_at' => $now,
		)
	);
	return $wpdb->insert( tsvd_src_table( 'entries' ), $data ) ? (int) $wpdb->insert_id : 0;
}

function tsvd_src_entry_update( $id, $fields ) {
	global $wpdb;
	$fields['updated_at'] = tsvd_src_now();
	return false !== $wpdb->update( tsvd_src_table( 'entries' ), $fields, array( 'id' => $id ) );
}

function tsvd_src_entry_delete( $id ) {
	global $wpdb;
	$wpdb->delete( tsvd_src_table( 'entry_terms' ), array( 'entry_id' => $id ) );
	return false !== $wpdb->delete( tsvd_src_table( 'entries' ), array( 'id' => $id ) );
}
