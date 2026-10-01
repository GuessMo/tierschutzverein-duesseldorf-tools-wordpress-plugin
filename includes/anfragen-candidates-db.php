<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ANFRAGEN_CANDIDATE_TOMBSTONE_DAYS = 14;

function tsvd_anfragen_candidates_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'tsvd_anfragen_mail_candidates';
}

function tsvd_anfragen_create_candidates_table( $charset_collate ) {
	$table = tsvd_anfragen_candidates_table_name();
	dbDelta( "CREATE TABLE {$table} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		anfrage_id BIGINT UNSIGNED NOT NULL,
		folder VARCHAR(191) NOT NULL DEFAULT '',
		message_uid BIGINT UNSIGNED NOT NULL,
		message_key VARCHAR(191) NOT NULL DEFAULT '',
		sender VARCHAR(255) NOT NULL DEFAULT '',
		subject VARCHAR(255) NOT NULL DEFAULT '',
		body LONGTEXT NOT NULL,
		received_at DATETIME NULL,
		created_at DATETIME NOT NULL,
		decided_at DATETIME NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY message_key (message_key)
	) {$charset_collate};" );
}

function tsvd_anfragen_candidate_exists( $message_key ) {
	global $wpdb;
	$table = tsvd_anfragen_candidates_table_name();
	$sql   = "SELECT id FROM {$table} WHERE message_key = %s";
	return (bool) $wpdb->get_var( $wpdb->prepare( $sql, $message_key ) );
}

function tsvd_anfragen_candidate_insert( array $candidate ) {
	global $wpdb;
	if ( tsvd_anfragen_candidate_exists( $candidate['message_key'] ) ) {
		return;
	}
	$candidate['created_at'] = current_time( 'mysql' );
	$wpdb->insert( tsvd_anfragen_candidates_table_name(), $candidate );
}

function tsvd_anfragen_candidate_get( $id ) {
	global $wpdb;
	$table = tsvd_anfragen_candidates_table_name();
	$sql   = "SELECT * FROM {$table} WHERE id = %d AND decided_at IS NULL";
	return $wpdb->get_row( $wpdb->prepare( $sql, $id ), ARRAY_A );
}

function tsvd_anfragen_candidates_all() {
	global $wpdb;
	$table = tsvd_anfragen_candidates_table_name();
	return $wpdb->get_results(
		"SELECT * FROM {$table} WHERE decided_at IS NULL ORDER BY received_at DESC, id DESC",
		ARRAY_A
	);
}

function tsvd_anfragen_candidate_mark_decided( $id ) {
	global $wpdb;
	$wpdb->update(
		tsvd_anfragen_candidates_table_name(),
		array(
			'sender'     => '',
			'subject'    => '',
			'body'       => '',
			'decided_at' => current_time( 'mysql' ),
		),
		array( 'id' => (int) $id )
	);
}

function tsvd_anfragen_candidates_purge_decided() {
	global $wpdb;
	$table  = tsvd_anfragen_candidates_table_name();
	$keep   = TSVD_ANFRAGEN_CANDIDATE_TOMBSTONE_DAYS * DAY_IN_SECONDS;
	$cutoff = wp_date( 'Y-m-d H:i:s', time() - $keep );
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE decided_at < %s", $cutoff ) );
}
