<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_AT_IMAGE_MIMES = array( 'image/jpeg', 'image/png', 'image/webp', 'image/gif' );
const TSVD_AT_FOCUS_KEYS  = array(
	'tsvd_focus_x',
	'tsvd_focus_y',
	'tsvd_focus_w',
	'tsvd_focus_h',
	'tsvd_focal_x',
	'tsvd_focal_y',
);

function tsvd_at_rest_upload( WP_REST_Request $request ) {
	$files = $request->get_file_params();
	$type  = $files['file']['type'] ?? '';
	if ( ! in_array( $type, TSVD_AT_IMAGE_MIMES, true ) ) {
		return tsvd_at_error(
			'invalid_file',
			__( 'Lade ein Bild im Format JPG, PNG, WebP oder GIF hoch.', 'tsv-tools' ),
			400
		);
	}
	tsvd_at_require_media_admin();
	$parent = tsvd_at_upload_parent( (int) $request->get_param( 'animal_id' ) );
	$id     = media_handle_upload( 'file', $parent );
	if ( is_wp_error( $id ) ) {
		return tsvd_at_error( 'upload_failed', $id->get_error_message(), 500 );
	}
	return new WP_REST_Response( tsvd_at_image( $id ), 201 );
}

function tsvd_at_require_media_admin() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
}

function tsvd_at_upload_parent( $animal_id ) {
	if ( $animal_id && TSVD_AT_POST_TYPE === get_post_type( $animal_id )
		&& current_user_can( 'edit_post', $animal_id ) ) {
		return $animal_id;
	}
	return 0;
}

function tsvd_at_can_edit_media( WP_REST_Request $request ) {
	$allowed = tsvd_at_can_upload();
	if ( true !== $allowed ) {
		return $allowed;
	}
	$id = (int) $request['id'];
	if ( ! wp_attachment_is_image( $id ) ) {
		$message = __( 'Das Bild wurde nicht gefunden.', 'tsv-tools' );
		return tsvd_at_error( 'not_found', $message, 404 );
	}
	return current_user_can( 'edit_post', $id ) ? true : tsvd_at_forbidden();
}

function tsvd_at_rest_focus( WP_REST_Request $request ) {
	if ( ! function_exists( 'tsvd_save_focus_region_attachment_field' ) ) {
		$message = __( 'Der Bildfokus ist nicht verfügbar.', 'tsv-tools' );
		return tsvd_at_error( 'no_focus', $message, 501 );
	}
	$id     = (int) $request['id'];
	$fields = tsvd_at_focus_fields( (array) $request->get_param( 'region' ) );
	$post   = array( 'ID' => $id );
	tsvd_save_focus_region_attachment_field( $post, $fields );
	tsvd_save_focal_point_attachment_field( $post, $fields );
	return rest_ensure_response( tsvd_at_image( $id ) );
}

function tsvd_at_focus_fields( array $region ) {
	if ( ! isset( $region['x'], $region['y'], $region['w'], $region['h'] ) ) {
		return array_fill_keys( TSVD_AT_FOCUS_KEYS, '' );
	}
	$values = array_map( 'floatval', $region );
	return array(
		'tsvd_focus_x' => (string) $values['x'],
		'tsvd_focus_y' => (string) $values['y'],
		'tsvd_focus_w' => (string) $values['w'],
		'tsvd_focus_h' => (string) $values['h'],
		'tsvd_focal_x' => (string) round( $values['x'] + $values['w'] / 2, 2 ),
		'tsvd_focal_y' => (string) round( $values['y'] + $values['h'] / 2, 2 ),
	);
}
