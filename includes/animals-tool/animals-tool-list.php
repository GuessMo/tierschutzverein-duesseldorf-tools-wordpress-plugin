<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_AT_LIST_STATUSES = array( 'publish', 'future', 'draft', 'pending', 'private', 'trash' );
const TSVD_AT_MISSING_STATES = array( 'missing', 'found', 'reunited' );

function tsvd_at_rest_list() {
	$posts = get_posts(
		array(
			'post_type'      => TSVD_AT_POST_TYPE,
			'post_status'    => TSVD_AT_LIST_STATUSES,
			'posts_per_page' => -1,
			'orderby'        => 'modified',
			'order'          => 'DESC',
		)
	);
	update_postmeta_cache( wp_list_pluck( $posts, 'ID' ) );
	return rest_ensure_response( array_map( 'tsvd_at_list_item', $posts ) );
}

function tsvd_at_missing_status( $post_id ) {
	$status = tsvd_at_str( $post_id, 'animal_missing_status' );
	return in_array( $status, TSVD_AT_MISSING_STATES, true ) ? $status : '';
}

function tsvd_at_list_item( WP_Post $post ) {
	$id = $post->ID;
	return array_merge(
		array(
			'id'       => $id,
			'status'   => $post->post_status,
			'title'    => $post->post_title,
			'name'     => (string) tsvd_at_meta( $id, 'animal_name', $post->post_title ),
			'thumb'    => (string) get_the_post_thumbnail_url( $id, 'thumbnail' ),
			'focus'    => tsvd_at_has_focus( $id ),
			'modified' => mysql_to_rfc3339( $post->post_modified ),
		),
		tsvd_at_list_profile( $id ),
		tsvd_at_list_state( $id )
	);
}

function tsvd_at_list_profile( $post_id ) {
	return array(
		'species'     => tsvd_at_species_label( $post_id ),
		'species_ids' => tsvd_at_species_ids( $post_id ),
		'breed'       => tsvd_at_breed_label( $post_id ),
		'color'       => tsvd_at_color_label( $post_id ),
		'age'         => tsvd_at_age( $post_id ),
		'gender'      => tsvd_at_str( $post_id, 'animal_gender' ),
		'teo_id'      => tsvd_at_str( $post_id, 'animal_teo_id' ),
		'internal_id' => tsvd_at_str( $post_id, 'animal_internal_id' ),
	);
}

function tsvd_at_list_state( $post_id ) {
	$missing = tsvd_at_missing_status( $post_id );
	return array(
		'record_type'          => '' === $missing ? 'vermittlung' : 'vermisst',
		'missing_status'       => $missing,
		'mediator'             => tsvd_at_mediator( $post_id ),
		'adoption_status'      => (string) tsvd_at_meta(
			$post_id,
			'animal_adoption_status',
			'not_for_adoption'
		),
		'successfully_adopted' => 'successfully_adopted' === get_post_meta(
			$post_id,
			'animal_internal_adoption_status',
			true
		),
		'owner_feedback_date'  => tsvd_at_str( $post_id, 'animal_owner_feedback_date' ),
		'owner_pending'        => '' !== tsvd_at_str( $post_id, '_tsvd_owner_pending_action' ),
	);
}
