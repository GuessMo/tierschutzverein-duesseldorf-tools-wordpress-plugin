<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_NEWSLETTER_SUBSCRIBER_META = '_tsvd_newsletter_intern';

function tsvd_newsletter_subscriber_ids() {
	$ids = get_users( array(
		'meta_key'   => TSVD_NEWSLETTER_SUBSCRIBER_META,
		'meta_value' => '1',
		'fields'     => 'ID',
		'orderby'    => 'display_name',
		'order'      => 'ASC',
	) );

	return array_map( 'intval', $ids );
}

function tsvd_newsletter_subscribers() {
	$ids = tsvd_newsletter_subscriber_ids();
	if ( empty( $ids ) ) {
		return array();
	}

	return get_users( array(
		'include' => $ids,
		'orderby' => 'display_name',
		'order'   => 'ASC',
	) );
}

function tsvd_newsletter_set_subscribers( array $user_ids ) {
	$target = array_filter(
		array_map( 'intval', $user_ids ),
		function ( $id ) {
			return $id > 0;
		}
	);
	$target  = array_values( array_unique( $target ) );
	$current = tsvd_newsletter_subscriber_ids();

	$added = 0;
	foreach ( array_diff( $target, $current ) as $id ) {
		if ( get_userdata( $id ) ) {
			update_user_meta( $id, TSVD_NEWSLETTER_SUBSCRIBER_META, '1' );
			$added++;
		}
	}

	$removed = 0;
	foreach ( array_diff( $current, $target ) as $id ) {
		delete_user_meta( $id, TSVD_NEWSLETTER_SUBSCRIBER_META );
		$removed++;
	}

	return array(
		'added'   => $added,
		'removed' => $removed,
	);
}
