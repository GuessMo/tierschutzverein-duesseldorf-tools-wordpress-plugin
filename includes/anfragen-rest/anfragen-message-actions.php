<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_submit_message( $id, $mode, $body ) {
	$user_id = get_current_user_id();
	if ( 'note' === $mode ) {
		$result = tsvd_anfragen_add_note( $id, $body, $user_id );
		return is_wp_error( $result ) ? $result : __( 'Notiz gespeichert.', 'tsvd' );
	}
	$party  = 'halter' === $mode ? 'halter' : 'applicant';
	$result = tsvd_anfragen_schedule_reply( $id, $body, $user_id, $party );
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	return tsvd_anfragen_send_delay() > 0
		? __( 'Antwort geplant.', 'tsvd' )
		: __( 'Antwort gesendet.', 'tsvd' );
}

function tsvd_anfragen_send_now( $reply_id, $anfrage_id ) {
	global $wpdb;
	$table = tsvd_anfragen_replies_table_name();
	$sql   = "SELECT id FROM {$table} WHERE id = %d AND anfrage_id = %d"
		. " AND direction = 'out' AND sent_at IS NULL";
	if ( ! $wpdb->get_var( $wpdb->prepare( $sql, $reply_id, $anfrage_id ) ) ) {
		return new WP_Error(
			'not_found',
			__( 'Nachricht nicht gefunden oder bereits gesendet.', 'tsvd' )
		);
	}
	wp_clear_scheduled_hook( 'tsvd_anfragen_dispatch', array( (int) $reply_id ) );
	tsvd_anfragen_dispatch_reply( (int) $reply_id );
	return true;
}

function tsvd_anfragen_reply_is_pending( $reply ) {
	return 'out' === $reply['direction'] && empty( $reply['sent_at'] )
		&& ! empty( $reply['scheduled_at'] );
}

function tsvd_anfragen_reply_remaining( $reply ) {
	return max( 0, (int) ( strtotime( $reply['scheduled_at'] . ' UTC' ) - time() ) );
}

function tsvd_anfragen_replies_for( $id ) {
	global $wpdb;
	$table = tsvd_anfragen_replies_table_name();
	$sql   = "SELECT * FROM {$table} WHERE anfrage_id = %d"
		. ' ORDER BY COALESCE(sent_at, scheduled_at) ASC, id ASC';
	return $wpdb->get_results( $wpdb->prepare( $sql, $id ), ARRAY_A );
}

function tsvd_anfragen_participant_names( $replies ) {
	$names = array();
	foreach ( $replies as $reply ) {
		$user_id = (int) $reply['user_id'];
		$key     = $user_id ? 'u' . $user_id : 'system';
		if ( 'in' === $reply['direction'] || isset( $names[ $key ] ) ) {
			continue;
		}
		$names[ $key ] = $user_id ? tsvd_anfragen_user_label( $user_id ) : __( 'System', 'tsvd' );
	}
	return array_values( $names );
}
