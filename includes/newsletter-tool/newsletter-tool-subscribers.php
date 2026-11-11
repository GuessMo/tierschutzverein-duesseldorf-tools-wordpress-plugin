<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_nlt_subscriber_row( WP_User $user, array $subscribed ) {
	return array(
		'id'           => $user->ID,
		'name'         => tsvd_newsletter_user_label( $user ),
		'email'        => $user->user_email,
		'isSubscribed' => in_array( $user->ID, $subscribed, true ),
	);
}

function tsvd_nlt_rest_subscribers() {
	$subscribed = tsvd_newsletter_subscriber_ids();
	$users      = get_users(
		array(
			'orderby' => 'display_name',
			'order'   => 'ASC',
		)
	);
	return rest_ensure_response(
		array_map(
			function ( $user ) use ( $subscribed ) {
				return tsvd_nlt_subscriber_row( $user, $subscribed );
			},
			$users
		)
	);
}

function tsvd_nlt_rest_save_subscribers( WP_REST_Request $request ) {
	$ids    = (array) $request->get_param( 'ids' );
	$result = tsvd_newsletter_set_subscribers( $ids );
	$count = count( tsvd_newsletter_subscriber_ids() );
	return rest_ensure_response( $result + array( 'count' => $count ) );
}
