<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_SRC_DB_VERSION = '1';

add_action( 'init', 'tsvd_src_maybe_upgrade_db' );

function tsvd_src_table( $name ) {
	global $wpdb;
	return $wpdb->prefix . 'tsvd_src_' . $name;
}

function tsvd_src_maybe_upgrade_db() {
	if ( get_option( 'tsvd_src_db_version' ) === TSVD_SRC_DB_VERSION ) {
		return;
	}
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	foreach ( tsvd_src_table_definitions() as $name => $columns ) {
		tsvd_src_create_table( $name, $columns );
	}
	update_option( 'tsvd_src_db_version', TSVD_SRC_DB_VERSION );
}

function tsvd_src_create_table( $name, $columns ) {
	global $wpdb;
	$table = tsvd_src_table( $name );
	dbDelta( "CREATE TABLE {$table} (\n{$columns}\n) {$wpdb->get_charset_collate()};" );
}

function tsvd_src_table_definitions() {
	return array(
		'collections' => tsvd_src_collections_columns(),
		'taxonomies'  => "id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		collection_id BIGINT UNSIGNED NOT NULL,
		name VARCHAR(190) NOT NULL DEFAULT '',
		is_hierarchical TINYINT(1) NOT NULL DEFAULT 0,
		created_at DATETIME NOT NULL,
		PRIMARY KEY  (id),
		KEY collection_id (collection_id)",
		'terms'       => "id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		taxonomy_id BIGINT UNSIGNED NOT NULL,
		parent_id BIGINT UNSIGNED NULL,
		name VARCHAR(190) NOT NULL DEFAULT '',
		created_at DATETIME NOT NULL,
		PRIMARY KEY  (id),
		KEY taxonomy_id (taxonomy_id)",
		'entries'     => tsvd_src_entries_columns(),
		'entry_terms' => "entry_id BIGINT UNSIGNED NOT NULL,
		term_id BIGINT UNSIGNED NOT NULL,
		PRIMARY KEY  (entry_id,term_id),
		KEY term_id (term_id)",
	);
}

function tsvd_src_collections_columns() {
	return "id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		name VARCHAR(190) NOT NULL DEFAULT '',
		description TEXT NOT NULL,
		shared_with TEXT NOT NULL,
		created_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
		created_at DATETIME NOT NULL,
		updated_at DATETIME NOT NULL,
		PRIMARY KEY  (id)";
}

function tsvd_src_entries_columns() {
	return "id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		collection_id BIGINT UNSIGNED NOT NULL,
		title VARCHAR(255) NOT NULL DEFAULT '',
		body LONGTEXT NOT NULL,
		url VARCHAR(2048) NOT NULL DEFAULT '',
		publisher VARCHAR(190) NOT NULL DEFAULT '',
		year SMALLINT UNSIGNED NULL,
		kind VARCHAR(20) NOT NULL DEFAULT 'other',
		accessed_at DATE NULL,
		created_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
		created_at DATETIME NOT NULL,
		updated_at DATETIME NOT NULL,
		PRIMARY KEY  (id),
		KEY collection_id (collection_id)";
}
