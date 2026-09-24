<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_academy_rest_forbidden() {
	if ( current_user_can( TSVD_ACADEMY_EDIT_CAP ) ) {
		return null;
	}
	return new WP_Error(
		'rest_forbidden',
		__( 'Academy-Inhalte sind nur im wp-admin lesbar.', 'tsvd' ),
		array( 'status' => rest_authorization_required_code() )
	);
}

class TSVD_Academy_REST_Controller extends WP_REST_Posts_Controller {

	public function get_items_permissions_check( $request ) {
		return tsvd_academy_rest_forbidden() ?? parent::get_items_permissions_check( $request );
	}

	public function get_item_permissions_check( $request ) {
		return tsvd_academy_rest_forbidden() ?? parent::get_item_permissions_check( $request );
	}
}

class TSVD_Academy_Terms_REST_Controller extends WP_REST_Terms_Controller {

	public function get_items_permissions_check( $request ) {
		return tsvd_academy_rest_forbidden() ?? parent::get_items_permissions_check( $request );
	}

	public function get_item_permissions_check( $request ) {
		return tsvd_academy_rest_forbidden() ?? parent::get_item_permissions_check( $request );
	}
}
