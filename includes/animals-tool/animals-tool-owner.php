<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_at_rest_owner_approve( WP_REST_Request $request ) {
	if ( ! function_exists( 'tsvd_owner_approve' ) || ! current_user_can( 'manage_tsvd_anfragen' ) ) {
		return tsvd_at_forbidden();
	}
	$post       = tsvd_at_request_post( $request );
	$anfrage_id = tsvd_halter_find_conversation( $post->ID );
	if ( ! $anfrage_id || ! tsvd_owner_approve( $anfrage_id ) ) {
		return tsvd_at_error(
			'no_pending_approval',
			__( 'Für dieses Tier liegt keine Freigabe-Anfrage vor.', 'tsv-tools' ),
			409
		);
	}
	return rest_ensure_response( tsvd_at_record( get_post( $post->ID ) ) );
}

function tsvd_at_rest_owner_mail( WP_REST_Request $request ) {
	$post  = tsvd_at_request_post( $request );
	$email = tsvd_at_str( $post->ID, 'animal_private_contact_email' );
	if ( ! is_email( $email ) ) {
		return tsvd_at_error(
			'no_email',
			__( 'Keine gültige E-Mail-Adresse hinterlegt.', 'tsv-tools' ),
			400
		);
	}
	$sent = function_exists( 'tsvd_halter_notify' )
		? tsvd_halter_notify( $post->ID, 'invite' )
		: tsvd_notify_private_halter_animal_approved( $post->ID );
	if ( false === $sent ) {
		$message = __( 'Die Mail wurde nicht gesendet.', 'tsv-tools' );
		return tsvd_at_error( 'mail_failed', $message, 500 );
	}
	return rest_ensure_response( array( 'sent' => true ) );
}
