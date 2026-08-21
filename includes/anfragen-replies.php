<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_send_delay() {
	$delay = (int) get_option( 'tsvd_anfragen_send_delay', 120 );
	return (int) apply_filters( 'tsvd_anfragen_send_delay', max( 0, $delay ) );
}

function tsvd_anfragen_schedule_reply( $id, $body, $user_id = 0 ) {
	if ( '' === trim( $body ) ) {
		return new WP_Error( 'empty_body', __( 'Antworttext darf nicht leer sein.', 'tsvd' ) );
	}
	global $wpdb;
	$table   = tsvd_anfragen_table_name();
	$anfrage = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A );
	if ( ! $anfrage ) {
		return new WP_Error( 'not_found', __( 'Anfrage nicht gefunden.', 'tsvd' ) );
	}
	if ( ! is_email( $anfrage['applicant_email'] ) ) {
		return new WP_Error( 'invalid_email', __( 'Keine gültige E-Mail-Adresse hinterlegt.', 'tsvd' ) );
	}

	$delay = tsvd_anfragen_send_delay();
	if ( $delay <= 0 ) {
		return tsvd_anfragen_send_reply( $id, $body, $user_id );
	}

	$when = time() + $delay;
	$wpdb->insert(
		tsvd_anfragen_replies_table_name(),
		array(
			'anfrage_id'   => $id,
			'user_id'      => $user_id ?: null,
			'direction'    => 'out',
			'body'         => $body,
			'sent_at'      => null,
			'scheduled_at' => gmdate( 'Y-m-d H:i:s', $when ),
		),
		array( '%d', '%d', '%s', '%s', '%s', '%s' )
	);
	wp_schedule_single_event( $when, 'tsvd_anfragen_dispatch', array( (int) $wpdb->insert_id ) );
	return true;
}

add_action( 'tsvd_anfragen_dispatch', 'tsvd_anfragen_dispatch_reply' );

function tsvd_anfragen_dispatch_reply( $reply_id ) {
	global $wpdb;
	$rt    = tsvd_anfragen_replies_table_name();
	$reply = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$rt} WHERE id = %d", $reply_id ), ARRAY_A );
	if ( ! $reply || 'out' !== $reply['direction'] || ! empty( $reply['sent_at'] ) ) {
		return;
	}
	$table   = tsvd_anfragen_table_name();
	$anfrage = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $reply['anfrage_id'] ), ARRAY_A );
	if ( ! $anfrage ) {
		return;
	}
	tsvd_anfragen_mail_reply( $anfrage, $reply['body'] );
	$now = current_time( 'mysql' );
	$wpdb->update( $rt, array( 'sent_at' => $now, 'scheduled_at' => null ), array( 'id' => $reply_id ), array( '%s', '%s' ), array( '%d' ) );
	$wpdb->update( $table, array( 'status' => 'answered', 'updated_at' => $now ), array( 'id' => $anfrage['id'] ), array( '%s', '%s' ), array( '%d' ) );
}

add_action( 'admin_init', 'tsvd_anfragen_flush_due_replies' );

function tsvd_anfragen_flush_due_replies() {
	global $wpdb;
	$rt  = tsvd_anfragen_replies_table_name();
	$ids = $wpdb->get_col( $wpdb->prepare(
		"SELECT id FROM {$rt} WHERE direction = 'out' AND sent_at IS NULL AND scheduled_at IS NOT NULL AND scheduled_at <= %s",
		gmdate( 'Y-m-d H:i:s' )
	) );
	foreach ( $ids as $rid ) {
		tsvd_anfragen_dispatch_reply( (int) $rid );
	}
}

function tsvd_anfragen_edit_reply( $reply_id, $body, $anfrage_id ) {
	if ( '' === trim( $body ) ) {
		return new WP_Error( 'empty_body', __( 'Text darf nicht leer sein.', 'tsvd' ) );
	}
	global $wpdb;
	$rt    = tsvd_anfragen_replies_table_name();
	$reply = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$rt} WHERE id = %d AND anfrage_id = %d", $reply_id, $anfrage_id ), ARRAY_A );
	if ( ! $reply ) {
		return new WP_Error( 'not_found', __( 'Nachricht nicht gefunden.', 'tsvd' ) );
	}
	if ( ! tsvd_anfragen_reply_is_mutable( $reply ) ) {
		return new WP_Error( 'not_editable', __( 'Diese Nachricht kann nicht mehr bearbeitet werden.', 'tsvd' ) );
	}
	$wpdb->update( $rt, array( 'body' => $body, 'edited_at' => current_time( 'mysql' ) ), array( 'id' => $reply_id ), array( '%s', '%s' ), array( '%d' ) );
	return true;
}

function tsvd_anfragen_delete_reply( $reply_id, $anfrage_id ) {
	global $wpdb;
	$rt    = tsvd_anfragen_replies_table_name();
	$reply = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$rt} WHERE id = %d AND anfrage_id = %d", $reply_id, $anfrage_id ), ARRAY_A );
	if ( ! $reply ) {
		return new WP_Error( 'not_found', __( 'Nachricht nicht gefunden.', 'tsvd' ) );
	}
	if ( ! tsvd_anfragen_reply_is_mutable( $reply ) ) {
		return new WP_Error( 'not_deletable', __( 'Diese Nachricht kann nicht gelöscht werden.', 'tsvd' ) );
	}
	if ( 'out' === $reply['direction'] && empty( $reply['sent_at'] ) ) {
		wp_clear_scheduled_hook( 'tsvd_anfragen_dispatch', array( (int) $reply_id ) );
	}
	$wpdb->delete( $rt, array( 'id' => $reply_id ), array( '%d' ) );
	return true;
}

function tsvd_anfragen_reply_is_mutable( $reply ) {
	if ( 'note' === $reply['direction'] ) {
		return true;
	}
	return ( 'out' === $reply['direction'] && empty( $reply['sent_at'] ) );
}

function tsvd_anfragen_reply_ajax_guard() {
	$anfrage = isset( $_POST['anfrage'] ) ? absint( $_POST['anfrage'] ) : 0;
	if ( ! $anfrage || ! current_user_can( 'manage_tsvd_anfragen' ) ) {
		wp_send_json_error( array( 'message' => __( 'Keine Berechtigung.', 'tsvd' ) ) );
	}
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'tsvd_anfrage_reply_' . $anfrage ) ) {
		wp_send_json_error( array( 'message' => __( 'Sicherheitsfehler.', 'tsvd' ) ) );
	}
	return $anfrage;
}

add_action( 'wp_ajax_tsvd_anfrage_reply_edit', 'tsvd_ajax_anfrage_reply_edit' );
function tsvd_ajax_anfrage_reply_edit() {
	$anfrage = tsvd_anfragen_reply_ajax_guard();
	$rid     = isset( $_POST['reply'] ) ? absint( $_POST['reply'] ) : 0;
	$body    = isset( $_POST['body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['body'] ) ) : '';
	$result  = tsvd_anfragen_edit_reply( $rid, $body, $anfrage );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}
	wp_send_json_success();
}

add_action( 'wp_ajax_tsvd_anfrage_reply_delete', 'tsvd_ajax_anfrage_reply_delete' );
function tsvd_ajax_anfrage_reply_delete() {
	$anfrage = tsvd_anfragen_reply_ajax_guard();
	$rid     = isset( $_POST['reply'] ) ? absint( $_POST['reply'] ) : 0;
	$result  = tsvd_anfragen_delete_reply( $rid, $anfrage );
	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}
	wp_send_json_success();
}

add_action( 'wp_ajax_tsvd_anfrage_reply_sendnow', 'tsvd_ajax_anfrage_reply_sendnow' );
function tsvd_ajax_anfrage_reply_sendnow() {
	$anfrage = tsvd_anfragen_reply_ajax_guard();
	$rid     = isset( $_POST['reply'] ) ? absint( $_POST['reply'] ) : 0;
	global $wpdb;
	$rt = tsvd_anfragen_replies_table_name();
	$ok = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$rt} WHERE id = %d AND anfrage_id = %d AND direction = 'out' AND sent_at IS NULL", $rid, $anfrage ) );
	if ( ! $ok ) {
		wp_send_json_error( array( 'message' => __( 'Nachricht nicht gefunden oder bereits gesendet.', 'tsvd' ) ) );
	}
	wp_clear_scheduled_hook( 'tsvd_anfragen_dispatch', array( (int) $rid ) );
	tsvd_anfragen_dispatch_reply( (int) $rid );
	wp_send_json_success();
}
