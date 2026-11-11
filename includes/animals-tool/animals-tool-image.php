<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_at_image( $attachment_id ) {
	$attachment_id = (int) $attachment_id;
	if ( $attachment_id < 1 || ! wp_attachment_is_image( $attachment_id ) ) {
		return null;
	}
	return array(
		'id'     => $attachment_id,
		'thumb'  => (string) wp_get_attachment_image_url( $attachment_id, 'thumbnail' ),
		'large'  => (string) wp_get_attachment_image_url( $attachment_id, 'large' ),
		'alt'    => tsvd_at_str( $attachment_id, '_wp_attachment_image_alt' ),
		'focus'  => tsvd_at_focus_region( $attachment_id ),
		'labels' => tsvd_at_focus_violations( $attachment_id ),
	);
}

function tsvd_at_images( $ids ) {
	return array_values( array_filter( array_map( 'tsvd_at_image', tsvd_at_ids( $ids ) ) ) );
}

function tsvd_at_focus_region( $attachment_id ) {
	if ( ! function_exists( 'tsvd_get_focus_region' ) ) {
		return null;
	}
	$region = tsvd_get_focus_region( (int) $attachment_id );
	return $region ? $region : null;
}

function tsvd_at_focus_violations( $attachment_id ) {
	if ( ! function_exists( 'tsvd_get_focus_region_violations' ) ) {
		return array();
	}
	return tsvd_get_focus_region_violations( (int) $attachment_id );
}

function tsvd_at_has_focus( $post_id ) {
	$thumb_id = (int) get_post_thumbnail_id( $post_id );
	if ( ! $thumb_id ) {
		return 'no_image';
	}
	return tsvd_at_focus_region( $thumb_id ) ? 'set' : 'missing';
}

function tsvd_at_meta_images( $post_id, $key ) {
	return tsvd_at_images( get_post_meta( $post_id, $key, true ) );
}
