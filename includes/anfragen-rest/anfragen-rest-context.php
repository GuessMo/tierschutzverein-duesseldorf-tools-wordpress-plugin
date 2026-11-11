<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_rest_context() {
	$assignees = array();
	foreach ( tsvd_anfragen_assign_options() as $id => $label ) {
		$assignees[] = array(
			'id'    => (int) $id,
			'label' => $label,
		);
	}
	return rest_ensure_response(
		array(
			'settings'            => tsvd_anfragen_rest_settings_payload(),
			'assignees'           => $assignees,
			'canManageCandidates' => tsvd_anfragen_rest_can_manage_candidates(),
			'openCount'           => tsvd_anfragen_count( 'open', '', 0 ),
			'oldestOpenId'        => tsvd_anfragen_oldest_open_id(),
		)
	);
}

function tsvd_anfragen_rest_settings_payload() {
	$settings = tsvd_anfragen_user_settings();
	return array(
		'sidebarPos' => 'left' === $settings['sidebar_pos'] ? 'left' : 'right',
		'status'     => (string) $settings['status'],
		'breed'      => (int) $settings['breed'],
	);
}

function tsvd_anfragen_rest_update_settings( WP_REST_Request $request ) {
	if ( $request->has_param( 'sidebarPos' ) ) {
		$position = 'left' === $request->get_param( 'sidebarPos' ) ? 'left' : 'right';
		tsvd_anfragen_update_user_setting( 'sidebar_pos', $position );
	}
	if ( $request->has_param( 'status' ) ) {
		$filter = tsvd_anfragen_rest_filter( $request );
		tsvd_anfragen_update_user_setting( 'status', $filter['status'] );
	}
	if ( $request->has_param( 'breed' ) ) {
		tsvd_anfragen_update_user_setting( 'breed', absint( $request->get_param( 'breed' ) ) );
	}
	return rest_ensure_response( tsvd_anfragen_rest_settings_payload() );
}
