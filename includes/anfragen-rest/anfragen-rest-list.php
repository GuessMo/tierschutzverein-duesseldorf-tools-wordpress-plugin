<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ANFRAGEN_REST_STATUSES = array( '', 'open', 'answered', 'mine', 'spam', 'blocked', 'trash' );

function tsvd_anfragen_rest_filter( WP_REST_Request $request ) {
	$status = sanitize_key( (string) $request->get_param( 'status' ) );
	return array(
		'status' => in_array( $status, TSVD_ANFRAGEN_REST_STATUSES, true ) ? $status : '',
		'search' => sanitize_text_field( (string) $request->get_param( 'search' ) ),
		'breed'  => absint( $request->get_param( 'breed' ) ),
		'animal' => absint( $request->get_param( 'animal' ) ),
	);
}

function tsvd_anfragen_rest_list( WP_REST_Request $request ) {
	$filter = tsvd_anfragen_rest_filter( $request );
	$list   = tsvd_anfragen_list_rows(
		$filter['status'],
		$filter['search'],
		$filter['breed'],
		max( 1, absint( $request->get_param( 'page' ) ) ),
		$filter['animal']
	);
	return rest_ensure_response(
		array(
			'items'  => array_map( 'tsvd_anfragen_rest_list_item', $list['rows'] ),
			'total'  => $list['total'],
			'pages'  => $list['pages'],
			'page'   => $list['paged'],
			'counts' => tsvd_anfragen_rest_counts( $filter['search'], $filter['breed'], $filter['animal'] ),
			'animal' => $filter['animal'] ? tsvd_anfragen_animal_name( $filter['animal'] ) : '',
			'houses' => tsvd_anfragen_rest_houses(),
		)
	);
}

function tsvd_anfragen_rest_list_item( $row ) {
	$is_flagged = in_array( $row['status'], array( 'spam', 'blocked' ), true );
	$is_open    = 'open' === $row['calc_status'] && ! $is_flagged;
	$seconds    = tsvd_anfragen_wait_seconds( $row['waiting_since'] );
	return array(
		'id'          => (int) $row['id'],
		'name'        => $row['applicant_name'],
		'animal'      => tsvd_anfragen_animal_name( (int) $row['animal_id'] ),
		'status'      => $row['status'],
		'calcStatus'  => $row['calc_status'],
		'isOpen'      => $is_open,
		'waitLabel'   => $is_open ? tsvd_anfragen_wait_label( $seconds ) : '',
		'waitLevel'   => $is_open ? tsvd_anfragen_wait_level( $seconds ) : '',
		'waitTitle'   => tsvd_anfragen_format_local( $row['waiting_since'] ),
		'activityDay' => tsvd_anfragen_format_local( $row['last_activity'], 'd.m.Y' ),
	);
}

function tsvd_anfragen_rest_counts( $search, $breed, $animal = 0 ) {
	$counts = array();
	foreach ( TSVD_ANFRAGEN_REST_STATUSES as $status ) {
		$counts[ '' === $status ? 'all' : $status ] = tsvd_anfragen_count( $status, $search, $breed, $animal );
	}
	return $counts;
}

function tsvd_anfragen_rest_houses() {
	$houses = tsvd_anfragen_breed_houses_visible();
	if ( is_wp_error( $houses ) || empty( $houses ) ) {
		return array();
	}
	return array_map(
		function ( $term ) {
			return array(
				'id'   => (int) $term->term_id,
				'name' => $term->name,
				'icon' => tsvd_anfragen_breed_icon( $term->term_id ),
			);
		},
		$houses
	);
}
