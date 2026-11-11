<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_nlt_rest_preview( WP_REST_Request $request ) {
	$post = tsvd_nlt_issue_post( $request );
	if ( is_wp_error( $post ) ) {
		return $post;
	}
	return rest_ensure_response( array( 'html' => tsvd_newsletter_render_email( $post->ID ) ) );
}

function tsvd_nlt_rest_send( WP_REST_Request $request ) {
	$post = tsvd_nlt_issue_post( $request );
	if ( is_wp_error( $post ) ) {
		return $post;
	}
	if ( '' === trim( $post->post_title ) ) {
		return tsvd_nlt_error( 'no_subject', __( 'Gib zuerst einen Betreff ein.', 'tsvd' ), 400 );
	}
	$mode   = 'all' === $request->get_param( 'mode' ) ? 'all' : 'test';
	$result = tsvd_newsletter_send_issue( $post->ID, $mode );
	if ( 'all' === $mode && $result['sent'] > 0 && 'publish' !== $post->post_status ) {
		wp_update_post( array( 'ID' => $post->ID, 'post_status' => 'publish' ) );
	}
	return rest_ensure_response(
		array(
			'mode'   => $mode,
			'sent'   => (int) $result['sent'],
			'total'  => (int) $result['total'],
			'failed' => count( $result['failed'] ),
			'issue'  => tsvd_nlt_issue_summary( get_post( $post->ID ) ),
		)
	);
}

function tsvd_nlt_rest_media() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	if ( empty( $_FILES['file'] ) ) {
		return tsvd_nlt_error( 'no_file', __( 'Es wurde keine Datei übertragen.', 'tsvd' ), 400 );
	}
	$id = media_handle_upload( 'file', 0 );
	if ( is_wp_error( $id ) ) {
		return tsvd_nlt_error( 'upload_failed', $id->get_error_message(), 500 );
	}
	return rest_ensure_response(
		array(
			'id'  => (int) $id,
			'url' => (string) wp_get_attachment_image_url( $id, 'medium_large' ),
		)
	);
}
