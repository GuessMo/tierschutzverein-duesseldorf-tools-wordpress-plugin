<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_AT_SAVE_STATUSES = array( 'draft', 'pending', 'publish' );

function tsvd_at_rest_create( WP_REST_Request $request ) {
	$data   = (array) $request->get_json_params();
	$status = tsvd_at_validate( $data, 'draft' );
	if ( is_wp_error( $status ) ) {
		return $status;
	}
	$id = wp_insert_post(
		array(
			'post_type'   => TSVD_AT_POST_TYPE,
			'post_status' => 'draft',
			'post_title'  => sanitize_text_field( tsvd_at_text( $data, 'name' ) ),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return $id;
	}
	$saved = tsvd_at_save( get_post( $id ), $data, $status );
	if ( is_wp_error( $saved ) ) {
		return $saved;
	}
	return new WP_REST_Response( tsvd_at_record( get_post( $id ) ), 201 );
}

function tsvd_at_rest_update( WP_REST_Request $request ) {
	$post   = tsvd_at_request_post( $request );
	$data   = (array) $request->get_json_params();
	$status = tsvd_at_validate( $data, $post->post_status );
	if ( is_wp_error( $status ) ) {
		return $status;
	}
	$saved = tsvd_at_save( $post, $data, $status );
	if ( is_wp_error( $saved ) ) {
		return $saved;
	}
	return rest_ensure_response( tsvd_at_record( get_post( $post->ID ) ) );
}

function tsvd_at_target_status( array $data, $current ) {
	$status = tsvd_at_text( $data, 'status' );
	if ( '' === $status || $status === $current ) {
		return $current;
	}
	if ( ! in_array( $status, TSVD_AT_SAVE_STATUSES, true ) ) {
		$message = __( 'Dieser Status ist nicht erlaubt.', 'tsv-tools' );
		return tsvd_at_error( 'invalid_status', $message, 400 );
	}
	if ( 'publish' === $status && ! tsvd_at_can_publish() ) {
		return tsvd_at_forbidden();
	}
	return $status;
}

function tsvd_at_validate( array $data, $current ) {
	if ( '' === trim( tsvd_at_text( $data, 'name' ) ) ) {
		return tsvd_at_error( 'invalid_name', __( 'Gib einen Tiernamen ein.', 'tsv-tools' ), 400 );
	}
	return tsvd_at_target_status( $data, $current );
}

function tsvd_at_save( WP_Post $post, array $data, $status ) {
	tsvd_at_unlink_companions( $post->ID, $data );
	tsvd_at_apply_breeds( $post->ID, $data );
	tsvd_at_apply_thumbnail( $post->ID, $data );
	return tsvd_at_run_legacy_save( $post->ID, tsvd_at_legacy_fields( $data ), $status );
}

function tsvd_at_run_legacy_save( $post_id, array $fields, $status ) {
	$backup = $_POST;
	$_POST  = wp_slash( $fields );
	$result = wp_update_post(
		array(
			'ID'          => $post_id,
			'post_status' => $status,
		),
		true
	);
	$_POST  = $backup;
	return $result;
}

function tsvd_at_apply_breeds( $post_id, array $data ) {
	$valid = array();
	foreach ( tsvd_at_ids( $data['breeds'] ?? array() ) as $term_id ) {
		if ( term_exists( $term_id, 'animal_breed' ) ) {
			$valid[] = $term_id;
		}
	}
	wp_set_post_terms( $post_id, $valid, 'animal_breed', false );
}

function tsvd_at_apply_thumbnail( $post_id, array $data ) {
	$image_id = absint( $data['featured_image_id'] ?? 0 );
	if ( $image_id && wp_attachment_is_image( $image_id ) ) {
		set_post_thumbnail( $post_id, $image_id );
		return;
	}
	delete_post_thumbnail( $post_id );
}

function tsvd_at_unlink_companions( $post_id, array $data ) {
	$kept    = tsvd_at_ids( wp_list_pluck( (array) ( $data['companions'] ?? array() ), 'id' ) );
	$current = tsvd_at_ids( get_post_meta( $post_id, 'animal_companion_animals', true ) );
	foreach ( array_diff( $current, $kept ) as $companion_id ) {
		$list = tsvd_at_ids( get_post_meta( $companion_id, 'animal_companion_animals', true ) );
		$list = array_values( array_diff( $list, array( (int) $post_id ) ) );
		update_post_meta( $companion_id, 'animal_companion_animals', $list );
	}
}
