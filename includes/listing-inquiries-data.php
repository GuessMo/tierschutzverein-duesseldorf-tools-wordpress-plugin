<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_LISTING_PER_PAGE = 20;

function tsvd_listing_filters_from_request() {
	$kind = isset( $_GET['kind'] ) ? sanitize_key( $_GET['kind'] ) : '';
	return array(
		'kind'   => in_array( $kind, TSVD_LISTING_KINDS, true ) ? $kind : '',
		'search' => isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '',
		'month'  => isset( $_GET['m'] ) && preg_match( '/^\d{4}-\d{2}$/', $_GET['m'] ) ? $_GET['m'] : '',
		'paged'  => max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 ),
	);
}

function tsvd_listing_where( $filters ) {
	global $wpdb;
	$clauses = array( "a.kind IN ( 'listing', 'sighting' )", 'a.deleted_at IS NULL' );
	if ( '' !== $filters['kind'] ) {
		$clauses[] = $wpdb->prepare( 'a.kind = %s', $filters['kind'] );
	}
	if ( '' !== $filters['month'] ) {
		$clauses[] = $wpdb->prepare( "DATE_FORMAT( a.created_at, '%%Y-%%m' ) = %s", $filters['month'] );
	}
	if ( '' !== $filters['search'] ) {
		$like      = '%' . $wpdb->esc_like( $filters['search'] ) . '%';
		$clauses[] = $wpdb->prepare(
			"( a.applicant_name LIKE %s OR a.applicant_email LIKE %s OR a.animal_id IN ( SELECT ID FROM {$wpdb->posts} WHERE post_type = 'animals' AND post_title LIKE %s ) )",
			$like,
			$like,
			$like
		);
	}
	return 'WHERE ' . implode( ' AND ', $clauses );
}

function tsvd_listing_query( $filters ) {
	global $wpdb;
	$table  = tsvd_anfragen_table_name();
	$where  = tsvd_listing_where( $filters );
	$offset = ( $filters['paged'] - 1 ) * TSVD_LISTING_PER_PAGE;
	return array(
		'total' => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} a {$where}" ),
		'rows'  => $wpdb->get_results(
			$wpdb->prepare( "SELECT a.* FROM {$table} a {$where} ORDER BY a.created_at DESC LIMIT %d OFFSET %d", TSVD_LISTING_PER_PAGE, $offset ),
			ARRAY_A
		),
	);
}

function tsvd_listing_months() {
	global $wpdb;
	$table = tsvd_anfragen_table_name();
	return $wpdb->get_col( "SELECT DISTINCT DATE_FORMAT( created_at, '%Y-%m' ) m FROM {$table} WHERE kind IN ( 'listing', 'sighting' ) AND deleted_at IS NULL ORDER BY m DESC" );
}

function tsvd_listing_message( $anfrage ) {
	$payload = json_decode( (string) $anfrage['payload'], true );
	return is_array( $payload ) ? trim( (string) ( $payload['tsvd_relay_message'] ?? '' ) ) : '';
}

function tsvd_listing_retention_days() {
	return max( 1, (int) get_option( 'tsvd_listing_inquiry_retention_days', 90 ) );
}
