<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_at_rest_trash( WP_REST_Request $request ) {
	$post = tsvd_at_request_post( $request );
	if ( ! wp_trash_post( $post->ID ) ) {
		return tsvd_at_error(
			'trash_failed',
			__( 'Das Tier konnte nicht in den Papierkorb gelegt werden.', 'tsv-tools' ),
			500
		);
	}
	return rest_ensure_response( tsvd_at_record( get_post( $post->ID ) ) );
}

function tsvd_at_rest_restore( WP_REST_Request $request ) {
	$post = tsvd_at_request_post( $request );
	if ( 'trash' !== $post->post_status || ! wp_untrash_post( $post->ID ) ) {
		return tsvd_at_error(
			'restore_failed',
			__( 'Das Tier konnte nicht wiederhergestellt werden.', 'tsv-tools' ),
			409
		);
	}
	return rest_ensure_response( tsvd_at_record( get_post( $post->ID ) ) );
}

function tsvd_at_rest_delete( WP_REST_Request $request ) {
	$post = tsvd_at_request_post( $request );
	if ( 'trash' !== $post->post_status ) {
		return tsvd_at_error(
			'not_trashed',
			__( 'Lege das Tier zuerst in den Papierkorb.', 'tsv-tools' ),
			409
		);
	}
	if ( ! wp_delete_post( $post->ID, true ) ) {
		$message = __( 'Löschen ist fehlgeschlagen.', 'tsv-tools' );
		return tsvd_at_error( 'delete_failed', $message, 500 );
	}
	return rest_ensure_response(
		array(
			'deleted' => true,
			'id'      => $post->ID,
		)
	);
}
