<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_RELAYED_STATES = array(
	'forwarded' => array( 'success', 'An Halter weitergeleitet' ),
	'spam'      => array( 'danger', 'Spam' ),
	'blocked'   => array( 'danger', 'Blockiert' ),
);

function tsvd_anfragen_rest_relayed( WP_REST_Request $request ) {
	$filters = tsvd_listing_filters(
		array(
			'kind'  => $request->get_param( 'kind' ),
			's'     => $request->get_param( 'search' ),
			'm'     => $request->get_param( 'month' ),
			'paged' => $request->get_param( 'page' ),
		)
	);
	$result  = tsvd_listing_query( $filters );
	return rest_ensure_response(
		array(
			'items'         => array_map( 'tsvd_anfragen_rest_relayed_item', $result['rows'] ),
			'total'         => $result['total'],
			'page'          => $filters['paged'],
			'pages'         => max( 1, (int) ceil( $result['total'] / TSVD_LISTING_PER_PAGE ) ),
			'months'        => array_map( 'tsvd_anfragen_rest_relayed_month', tsvd_listing_months() ),
			'retentionDays' => tsvd_listing_retention_days(),
		)
	);
}

function tsvd_anfragen_rest_relayed_month( $month ) {
	return array(
		'value' => $month,
		'label' => date_i18n( 'F Y', strtotime( $month . '-01' ) ),
	);
}

function tsvd_anfragen_rest_relayed_item( $row ) {
	$labels = tsvd_listing_kind_labels();
	$state  = TSVD_RELAYED_STATES[ $row['status'] ] ?? array( 'warning', 'Nicht weitergeleitet' );
	return array(
		'id'           => (int) $row['id'],
		'createdLabel' => date_i18n( 'd.m.Y H:i', strtotime( $row['created_at'] ) ),
		'kind'         => $row['kind'],
		'kindLabel'    => $labels[ $row['kind'] ] ?? $row['kind'],
		'name'         => (string) $row['applicant_name'],
		'email'        => (string) $row['applicant_email'],
		'phone'        => (string) $row['applicant_phone'],
		'message'      => tsvd_listing_message( $row ),
		'stateLevel'   => $state[0],
		'stateLabel'   => $state[1],
		'animal'       => tsvd_anfragen_rest_animal( (int) $row['animal_id'] ),
	);
}
