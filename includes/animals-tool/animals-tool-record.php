<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_at_rest_get( WP_REST_Request $request ) {
	return rest_ensure_response( tsvd_at_record( tsvd_at_request_post( $request ) ) );
}

function tsvd_at_record( WP_Post $post ) {
	$id = $post->ID;
	return array_merge(
		tsvd_at_record_post( $post ),
		tsvd_at_record_profile( $id, $post->post_title ),
		tsvd_at_record_status( $id ),
		tsvd_at_record_story( $id ),
		array(
			'tdw'      => tsvd_at_record_tdw( $id ),
			'interest' => tsvd_at_record_interest( $id ),
		)
	);
}

function tsvd_at_record_post( WP_Post $post ) {
	$id = $post->ID;
	return array(
		'id'              => $id,
		'status'          => $post->post_status,
		'title'           => $post->post_title,
		'modified'        => mysql_to_rfc3339( $post->post_modified ),
		'permalink'       => 'publish' === $post->post_status ? get_permalink( $post ) : '',
		'preview_url'     => (string) get_preview_post_link( $post ),
		'legacy_edit_url' => admin_url( 'post.php?post=' . $id . '&action=edit' ),
		'internal_id'     => function_exists( 'tsvd_get_animal_internal_id' )
			? (string) tsvd_get_animal_internal_id( $id ) : '',
		'featured_image'  => tsvd_at_image( get_post_thumbnail_id( $id ) ),
		'images'          => tsvd_at_meta_images( $id, 'animal_images' ),
		'can_delete'      => current_user_can( 'delete_post', $id ),
	);
}

function tsvd_at_option_default( $set, $fallback ) {
	if ( ! function_exists( 'tsvd_get_animal_option_sets' ) ) {
		return $fallback;
	}
	$sets = tsvd_get_animal_option_sets();
	return $sets[ $set ]['default'] ?? $fallback;
}

function tsvd_at_record_profile( $id, $title ) {
	$ternary = tsvd_at_option_default( 'ternary', 'unknown' );
	return array(
		'name'            => (string) tsvd_at_meta( $id, 'animal_name', $title ),
		'teo_id'          => tsvd_at_str( $id, 'animal_teo_id' ),
		'description'     => tsvd_at_str( $id, 'animal_description' ),
		'birthday_status' => (string) tsvd_at_meta(
			$id,
			'animal_birthday_status',
			tsvd_at_option_default( 'birthday_status', 'unknown' )
		),
		'birthday'        => tsvd_at_str( $id, 'animal_birthday' ),
		'age'             => tsvd_at_age( $id ),
		'life_stage'      => (string) tsvd_at_meta(
			$id,
			'animal_life_stage',
			tsvd_at_option_default( 'life_stage', 'undefined' )
		),
		'color'           => tsvd_at_color_id( $id ),
		'size_cm'         => tsvd_at_option_meta( $id, 'size_cm', 'size', 'small' ),
		'gender'          => tsvd_at_option_meta( $id, 'gender', 'gender', 'unknown' ),
		'castrated'       => (string) tsvd_at_meta( $id, 'animal_castrated', $ternary ),
		'vaccinated'      => (string) tsvd_at_meta( $id, 'animal_vaccinated', $ternary ),
		'chipped'         => (string) tsvd_at_meta( $id, 'animal_chipped', $ternary ),
		'breeds'          => tsvd_at_term_ids( $id, 'animal_breed' ),
		'companions'      => tsvd_at_companions( $id ),
	);
}

function tsvd_at_companions( $id ) {
	$items = array();
	$ids   = tsvd_at_ids( get_post_meta( $id, 'animal_companion_animals', true ) );
	foreach ( $ids as $companion_id ) {
		$companion = get_post( $companion_id );
		if ( $companion && TSVD_AT_POST_TYPE === $companion->post_type ) {
			$items[] = tsvd_at_companion( $companion );
		}
	}
	return $items;
}

function tsvd_at_companion( WP_Post $companion ) {
	return array(
		'id'          => $companion->ID,
		'title'       => tsvd_at_name( $companion ),
		'internal_id' => tsvd_at_str( $companion->ID, 'animal_internal_id' ),
		'breed'       => tsvd_at_breed_label( $companion->ID ),
		'thumb'       => (string) get_the_post_thumbnail_url( $companion->ID, 'thumbnail' ),
	);
}

function tsvd_at_option_meta( $id, $field, $set, $fallback ) {
	$default = tsvd_at_option_default( $set, $fallback );
	return (string) tsvd_at_meta( $id, 'animal_' . $field, $default );
}
