<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_NEWSLETTER_SENT_IN_META = '_tsvd_newsletter_sent_in';

function tsvd_newsletter_block_sent_items( array $block ) {
	$definition = tsvd_newsletter_block_type( isset( $block['type'] ) ? $block['type'] : '' );
	if ( ! $definition || empty( $definition['sent_items'] ) || ! is_callable( $definition['sent_items'] ) ) {
		return array();
	}

	$data = isset( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();
	$ids  = call_user_func( $definition['sent_items'], $data, $block );

	return is_array( $ids ) ? array_map( 'intval', $ids ) : array();
}

function tsvd_newsletter_collect_sent_items( $newsletter_id ) {
	$ids = array();
	foreach ( tsvd_newsletter_get_blocks( $newsletter_id ) as $block ) {
		$ids = array_merge( $ids, tsvd_newsletter_block_sent_items( $block ) );
	}

	return array_values( array_unique( array_filter( $ids ) ) );
}

function tsvd_newsletter_mark_items_sent( $newsletter_id, array $item_ids ) {
	$newsletter_id = (int) $newsletter_id;
	if ( $newsletter_id <= 0 ) {
		return;
	}

	foreach ( $item_ids as $item_id ) {
		$item_id = (int) $item_id;
		if ( $item_id <= 0 ) {
			continue;
		}

		$sent = get_post_meta( $item_id, TSVD_NEWSLETTER_SENT_IN_META, true );
		$sent = is_array( $sent ) ? array_map( 'intval', $sent ) : array();
		if ( in_array( $newsletter_id, $sent, true ) ) {
			continue;
		}

		$sent[] = $newsletter_id;
		update_post_meta( $item_id, TSVD_NEWSLETTER_SENT_IN_META, $sent );
	}
}

function tsvd_newsletter_item_sent_in( $item_id ) {
	$sent = get_post_meta( (int) $item_id, TSVD_NEWSLETTER_SENT_IN_META, true );
	$sent = is_array( $sent ) ? array_map( 'intval', $sent ) : array();

	$entries = array();
	foreach ( $sent as $newsletter_id ) {
		$exists = ( TSVD_NEWSLETTER_CPT === get_post_type( $newsletter_id ) );

		$entries[] = array(
			'id'       => $newsletter_id,
			'title'    => $exists ? get_the_title( $newsletter_id ) : __( 'Gelöschter Newsletter', 'tsvd' ),
			'edit_url' => $exists ? get_edit_post_link( $newsletter_id, 'raw' ) : '',
			'sent_at'  => $exists ? (int) get_post_meta( $newsletter_id, TSVD_NEWSLETTER_SENT_META, true ) : 0,
		);
	}

	return $entries;
}
