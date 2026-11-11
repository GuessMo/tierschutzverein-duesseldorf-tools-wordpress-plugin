<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_update_row( $id, $fields ) {
	global $wpdb;
	return false !== $wpdb->update( tsvd_anfragen_table_name(), $fields, array( 'id' => (int) $id ) );
}

function tsvd_anfragen_set_status( $id, $status ) {
	return tsvd_anfragen_update_row(
		$id,
		array(
			'status'     => $status,
			'updated_at' => current_time( 'mysql' ),
		)
	);
}

function tsvd_anfragen_trash( $id ) {
	return tsvd_anfragen_update_row( $id, array( 'deleted_at' => current_time( 'mysql' ) ) );
}

function tsvd_anfragen_restore( $id ) {
	return tsvd_anfragen_update_row( $id, array( 'deleted_at' => null ) );
}

function tsvd_anfragen_delete( $id ) {
	global $wpdb;
	$wpdb->delete( tsvd_anfragen_replies_table_name(), array( 'anfrage_id' => (int) $id ), array( '%d' ) );
	return (bool) $wpdb->delete( tsvd_anfragen_table_name(), array( 'id' => (int) $id ), array( '%d' ) );
}

function tsvd_anfragen_mark_spam( $id ) {
	return tsvd_anfragen_set_status( $id, 'spam' );
}

function tsvd_anfragen_unmark_spam( $id ) {
	return tsvd_anfragen_set_status( $id, 'open' );
}

function tsvd_anfragen_assign( $id, $user_id ) {
	$user_id = absint( $user_id );
	if ( $user_id && ! user_can( $user_id, 'manage_tsvd_anfragen' ) ) {
		$user_id = 0;
	}
	return tsvd_anfragen_update_row( $id, array( 'assigned_user_id' => $user_id ?: null ) );
}

function tsvd_anfragen_applicant_email( $id ) {
	global $wpdb;
	$table = tsvd_anfragen_table_name();
	$sql   = "SELECT applicant_email FROM {$table} WHERE id = %d";
	return (string) $wpdb->get_var( $wpdb->prepare( $sql, $id ) );
}

function tsvd_anfragen_block( $id ) {
	$email = tsvd_anfragen_applicant_email( $id );
	if ( '' === $email ) {
		return false;
	}
	tsvd_anfragen_blacklist_add( $email );
	return tsvd_anfragen_set_status( $id, 'blocked' );
}

function tsvd_anfragen_unblock( $id ) {
	$email = tsvd_anfragen_applicant_email( $id );
	if ( '' === $email ) {
		return false;
	}
	tsvd_anfragen_blacklist_remove( $email );
	return tsvd_anfragen_set_status( $id, 'open' );
}
