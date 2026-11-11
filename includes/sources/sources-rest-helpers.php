<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_SRC_REST_NAMESPACE = 'tsvd-tools/v1';

function tsvd_src_route( $path, $endpoints ) {
	foreach ( $endpoints as $index => $endpoint ) {
		$endpoints[ $index ]['permission_callback'] = 'tsvd_src_can_use';
	}
	register_rest_route( TSVD_SRC_REST_NAMESPACE, '/sources/' . $path, $endpoints );
}

function tsvd_src_pick( WP_REST_Request $request, $keys ) {
	$fields = array();
	foreach ( $keys as $key ) {
		if ( $request->has_param( $key ) ) {
			$fields[ $key ] = $request->get_param( $key );
		}
	}
	return $fields;
}

function tsvd_src_created( $data ) {
	return new WP_REST_Response( $data, 201 );
}

function tsvd_src_deleted( $id ) {
	return rest_ensure_response(
		array(
			'deleted' => true,
			'id'      => (int) $id,
		)
	);
}

function tsvd_src_check_name( $fields, $key, $label ) {
	if ( ! array_key_exists( $key, $fields ) ) {
		return null;
	}
	return tsvd_src_require_name( $fields[ $key ], $label );
}
