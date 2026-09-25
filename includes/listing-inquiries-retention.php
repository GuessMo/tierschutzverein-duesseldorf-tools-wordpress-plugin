<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'tsvd_listing_schedule_retention' );
add_action( 'tsvd_listing_purge_inquiries', 'tsvd_listing_purge_inquiries' );

function tsvd_listing_schedule_retention() {
	if ( ! wp_next_scheduled( 'tsvd_listing_purge_inquiries' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'tsvd_listing_purge_inquiries' );
	}
}

function tsvd_listing_purge_inquiries() {
	global $wpdb;
	$table   = tsvd_anfragen_table_name();
	$replies = tsvd_anfragen_replies_table_name();
	$cutoff  = gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) - tsvd_listing_retention_days() * DAY_IN_SECONDS );
	$ids     = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$table} WHERE kind IN ( 'listing', 'sighting' ) AND created_at < %s", $cutoff ) );
	if ( ! $ids ) {
		return 0;
	}
	$in = implode( ',', array_map( 'absint', $ids ) );
	$wpdb->query( "DELETE FROM {$replies} WHERE anfrage_id IN ( {$in} )" );
	$wpdb->query( "DELETE FROM {$table} WHERE id IN ( {$in} )" );
	return count( $ids );
}
