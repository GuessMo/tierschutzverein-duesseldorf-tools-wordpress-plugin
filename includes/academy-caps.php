<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'map_meta_cap', 'tsvd_academy_map_read_post', 10, 4 );
add_filter( 'map_meta_cap', 'tsvd_academy_protect_admin_term', 10, 4 );
add_filter( 'wp_update_term_data', 'tsvd_academy_keep_admin_slug', 10, 3 );
add_filter( 'wp_insert_attachment_data', 'tsvd_academy_detach_uploads' );

function tsvd_academy_map_read_post( $caps, $cap, $user_id, $args ) {
	if ( 'read_post' !== $cap || empty( $args[0] ) ) {
		return $caps;
	}
	$post = get_post( $args[0] );
	if ( ! $post || TSVD_ACADEMY_CPT !== $post->post_type ) {
		return $caps;
	}
	return tsvd_academy_user_can_view( $post, $user_id ) ? $caps : array( 'do_not_allow' );
}

function tsvd_academy_is_admin_term( $term_id ) {
	$term = get_term( (int) $term_id, TSVD_ACADEMY_TAX );
	return $term instanceof WP_Term && TSVD_ACADEMY_ADMIN_TERM === $term->slug;
}

function tsvd_academy_protect_admin_term( $caps, $cap, $user_id, $args ) {
	if ( 'delete_term' !== $cap || empty( $args[0] ) ) {
		return $caps;
	}
	return tsvd_academy_is_admin_term( $args[0] ) ? array( 'do_not_allow' ) : $caps;
}

function tsvd_academy_keep_admin_slug( $data, $term_id, $taxonomy ) {
	if ( TSVD_ACADEMY_TAX === $taxonomy && tsvd_academy_is_admin_term( $term_id ) ) {
		$data['slug'] = TSVD_ACADEMY_ADMIN_TERM;
	}
	return $data;
}

function tsvd_academy_detach_uploads( $data ) {
	if ( ! empty( $data['post_parent'] ) && TSVD_ACADEMY_CPT === get_post_type( $data['post_parent'] ) ) {
		$data['post_parent'] = 0;
	}
	return $data;
}
