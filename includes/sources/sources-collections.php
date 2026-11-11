<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_src_collections_all() {
	global $wpdb;
	$collections = tsvd_src_table( 'collections' );
	$entries     = tsvd_src_table( 'entries' );
	$rows        = $wpdb->get_results(
		"SELECT c.*,
		 ( SELECT COUNT(*) FROM {$entries} e WHERE e.collection_id = c.id ) AS entry_count
		 FROM {$collections} c ORDER BY c.name ASC",
		ARRAY_A
	);
	return array_map( 'tsvd_src_format_collection', $rows ? $rows : array() );
}

function tsvd_src_collection_row( $id ) {
	global $wpdb;
	$table = tsvd_src_table( 'collections' );
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A );
}

function tsvd_src_format_collection( $row ) {
	$shared = json_decode( (string) $row['shared_with'], true );
	return array(
		'id'          => (int) $row['id'],
		'name'        => $row['name'],
		'description' => $row['description'],
		'shared_with' => is_array( $shared ) ? $shared : array(),
		'entry_count' => isset( $row['entry_count'] ) ? (int) $row['entry_count'] : 0,
		'created_at'  => $row['created_at'],
		'updated_at'  => $row['updated_at'],
	);
}

function tsvd_src_collection_insert( $fields ) {
	global $wpdb;
	$now  = tsvd_src_now();
	$data = array_merge(
		array(
			'description' => '',
			'shared_with' => '[]',
		),
		$fields,
		array(
			'created_by' => get_current_user_id(),
			'created_at' => $now,
			'updated_at' => $now,
		)
	);
	return $wpdb->insert( tsvd_src_table( 'collections' ), $data ) ? (int) $wpdb->insert_id : 0;
}

function tsvd_src_collection_update( $id, $fields ) {
	global $wpdb;
	$fields['updated_at'] = tsvd_src_now();
	$table = tsvd_src_table( 'collections' );
	return false !== $wpdb->update( $table, $fields, array( 'id' => $id ) );
}

function tsvd_src_collection_delete( $id ) {
	global $wpdb;
	foreach ( tsvd_src_taxonomy_ids( $id ) as $taxonomy_id ) {
		tsvd_src_taxonomy_delete( $taxonomy_id );
	}
	$entries = tsvd_src_table( 'entries' );
	$links   = tsvd_src_table( 'entry_terms' );
	$wpdb->query(
		$wpdb->prepare(
			"DELETE l FROM {$links} l INNER JOIN {$entries} e ON e.id = l.entry_id
			 WHERE e.collection_id = %d",
			$id
		)
	);
	$wpdb->delete( $entries, array( 'collection_id' => $id ) );
	return false !== $wpdb->delete( tsvd_src_table( 'collections' ), array( 'id' => $id ) );
}
