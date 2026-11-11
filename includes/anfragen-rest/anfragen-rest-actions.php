<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_rest_actions() {
	return array(
		'trash'         => array( 'tsvd_anfragen_trash', __( 'In den Papierkorb verschoben.', 'tsvd' ) ),
		'restore'       => array( 'tsvd_anfragen_restore', __( 'Anfrage wiederhergestellt.', 'tsvd' ) ),
		'spam'          => array( 'tsvd_anfragen_mark_spam', __( 'Als Spam markiert.', 'tsvd' ) ),
		'unspam'        => array( 'tsvd_anfragen_unmark_spam', __( 'Nicht mehr als Spam markiert.', 'tsvd' ) ),
		'block'         => array( 'tsvd_anfragen_block', __( 'Absender blockiert.', 'tsvd' ) ),
		'unblock'       => array( 'tsvd_anfragen_unblock', __( 'Blockierung aufgehoben.', 'tsvd' ) ),
		'approve-owner' => array( 'tsvd_owner_approve', __( 'Anzeige wieder freigegeben.', 'tsvd' ) ),
	);
}

function tsvd_anfragen_rest_run_action( WP_REST_Request $request ) {
	$actions = tsvd_anfragen_rest_actions();
	$action  = (string) $request['action'];
	if ( ! isset( $actions[ $action ] ) || ! tsvd_anfragen_rest_anfrage( $request ) ) {
		return tsvd_anfragen_rest_not_found();
	}
	if ( ! call_user_func( $actions[ $action ][0], (int) $request['id'] ) ) {
		return tsvd_anfragen_rest_failed();
	}
	return tsvd_anfragen_rest_message( $actions[ $action ][1] );
}

function tsvd_anfragen_rest_delete( WP_REST_Request $request ) {
	if ( ! tsvd_anfragen_rest_anfrage( $request ) ) {
		return tsvd_anfragen_rest_not_found();
	}
	tsvd_anfragen_delete( (int) $request['id'] );
	return tsvd_anfragen_rest_message( __( 'Anfrage endgültig gelöscht.', 'tsvd' ) );
}

function tsvd_anfragen_rest_assign( WP_REST_Request $request ) {
	if ( ! tsvd_anfragen_rest_anfrage( $request ) ) {
		return tsvd_anfragen_rest_not_found();
	}
	tsvd_anfragen_assign( (int) $request['id'], $request->get_param( 'userId' ) );
	return tsvd_anfragen_rest_message( __( 'Zuständigkeit gespeichert.', 'tsvd' ) );
}

function tsvd_anfragen_rest_create_message( WP_REST_Request $request ) {
	$mode   = sanitize_key( (string) $request->get_param( 'mode' ) );
	$body   = tsvd_anfragen_rest_text( $request, 'body' );
	$result = tsvd_anfragen_submit_message( (int) $request['id'], $mode, $body );
	return is_wp_error( $result )
		? tsvd_anfragen_rest_error( $result )
		: tsvd_anfragen_rest_message( $result );
}

function tsvd_anfragen_rest_update_message( WP_REST_Request $request ) {
	$body   = tsvd_anfragen_rest_text( $request, 'body' );
	$result = tsvd_anfragen_edit_reply( (int) $request['reply'], $body, (int) $request['id'] );
	return is_wp_error( $result )
		? tsvd_anfragen_rest_error( $result )
		: tsvd_anfragen_rest_message( __( 'Nachricht gespeichert.', 'tsvd' ) );
}

function tsvd_anfragen_rest_delete_message( WP_REST_Request $request ) {
	$result = tsvd_anfragen_delete_reply( (int) $request['reply'], (int) $request['id'] );
	return is_wp_error( $result )
		? tsvd_anfragen_rest_error( $result )
		: tsvd_anfragen_rest_message( __( 'Nachricht gelöscht.', 'tsvd' ) );
}

function tsvd_anfragen_rest_send_message( WP_REST_Request $request ) {
	$result = tsvd_anfragen_send_now( (int) $request['reply'], (int) $request['id'] );
	return is_wp_error( $result )
		? tsvd_anfragen_rest_error( $result )
		: tsvd_anfragen_rest_message( __( 'Antwort gesendet.', 'tsvd' ) );
}
