<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_halter_case( $animal_id ) {
	if ( ! $animal_id || 'animals' !== get_post_type( $animal_id ) ) {
		return '';
	}
	$missing = get_post_meta( $animal_id, 'animal_missing_status', true );
	if ( in_array( $missing, array( 'missing', 'found', 'reunited' ), true ) ) {
		return 'missing';
	}
	return 'privat' === get_post_meta( $animal_id, 'animal_mediator', true ) ? 'private' : '';
}

function tsvd_halter_contact( $animal_id ) {
	$first = (string) get_post_meta( $animal_id, 'animal_private_contact_firstname', true );
	$last  = (string) get_post_meta( $animal_id, 'animal_private_contact_lastname', true );
	return array(
		'email' => (string) get_post_meta( $animal_id, 'animal_private_contact_email', true ),
		'first' => $first,
		'name'  => trim( $first . ' ' . $last ),
	);
}

function tsvd_halter_animal_name( $animal_id ) {
	$name = (string) get_post_meta( $animal_id, 'animal_name', true );
	return '' !== $name ? $name : get_the_title( $animal_id );
}

function tsvd_halter_relay_address() {
	$imap = (string) get_option( 'tsvd_anfragen_imap_username', '' );
	return is_email( $imap ) ? $imap : (string) get_option( 'admin_email' );
}

function tsvd_halter_token( $animal_id ) {
	$token = (string) get_post_meta( $animal_id, '_tsvd_owner_token', true );
	if ( '' === $token ) {
		$token = wp_generate_password( 32, false, false );
		update_post_meta( $animal_id, '_tsvd_owner_token', $token );
	}
	return $token;
}

function tsvd_halter_animal_by_token( $token ) {
	$token = preg_replace( '/[^A-Za-z0-9]/', '', (string) $token );
	if ( strlen( $token ) !== 32 ) {
		return 0;
	}
	$ids = get_posts( array(
		'post_type'      => 'animals',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_key'       => '_tsvd_owner_token',
		'meta_value'     => $token,
	) );
	return $ids ? (int) $ids[0] : 0;
}

function tsvd_halter_owner_url( $animal_id, $action = '' ) {
	$args = array( 't' => tsvd_halter_token( $animal_id ) );
	if ( '' !== $action ) {
		$args['a'] = $action;
	}
	return add_query_arg( $args, home_url( '/mein-tier/' ) );
}

function tsvd_halter_find_conversation( $animal_id ) {
	global $wpdb;
	$table = tsvd_anfragen_table_name();
	return (int) $wpdb->get_var( $wpdb->prepare(
		"SELECT id FROM {$table} WHERE animal_id = %d AND kind = 'halter' AND deleted_at IS NULL ORDER BY id ASC LIMIT 1",
		$animal_id
	) );
}

function tsvd_halter_conversation_id( $animal_id ) {
	$id = tsvd_halter_find_conversation( $animal_id );
	if ( $id ) {
		return $id;
	}
	$contact = tsvd_halter_contact( $animal_id );
	if ( ! is_email( $contact['email'] ) ) {
		return 0;
	}
	global $wpdb;
	$now      = current_time( 'mysql' );
	$form_opt = 'missing' === tsvd_halter_case( $animal_id ) ? 'tsvd_missing_animals_form' : 'tsvd_private_adoption_form';
	$wpdb->insert( tsvd_anfragen_table_name(), array(
		'form_id'          => (int) get_option( $form_opt, 0 ),
		'animal_id'        => $animal_id,
		'applicant_name'   => $contact['name'],
		'applicant_email'  => $contact['email'],
		'payload'          => '{}',
		'status'           => 'answered',
		'kind'             => 'halter',
		'assigned_user_id' => tsvd_halter_responsible_user( $animal_id ),
		'created_at'       => $now,
		'updated_at'       => $now,
	) );
	return (int) $wpdb->insert_id;
}

function tsvd_halter_log( $anfrage_id, $direction, $body, $party = 'halter', $user_id = 0 ) {
	if ( ! $anfrage_id ) {
		return;
	}
	global $wpdb;
	$now = current_time( 'mysql' );
	$wpdb->insert( tsvd_anfragen_replies_table_name(), array(
		'anfrage_id' => $anfrage_id,
		'user_id'    => $user_id ? $user_id : null,
		'direction'  => $direction,
		'party'      => $party,
		'body'       => $body,
		'sent_at'    => $now,
	) );
	$fields = array( 'updated_at' => $now );
	if ( 'in' === $direction ) {
		$fields['status'] = 'open';
	} elseif ( $user_id ) {
		$fields['status'] = 'answered';
	}
	$wpdb->update( tsvd_anfragen_table_name(), $fields, array( 'id' => $anfrage_id ) );
}

function tsvd_halter_send( $to, $subject, $body, $anfrage_id ) {
	$full_subject = $anfrage_id ? sprintf( '%s #%d', $subject, $anfrage_id ) : $subject;
	return wp_mail( $to, $full_subject, $body, array( 'Reply-To: ' . tsvd_halter_relay_address() ) );
}

function tsvd_halter_mail( $animal_id, $subject, $body ) {
	$contact = tsvd_halter_contact( $animal_id );
	if ( ! is_email( $contact['email'] ) ) {
		return false;
	}
	$conversation = tsvd_halter_conversation_id( $animal_id );
	$sent         = tsvd_halter_send( $contact['email'], $subject, $body, $conversation );
	if ( $sent ) {
		tsvd_halter_log( $conversation, 'out', $body, 'halter' );
	}
	return $sent;
}
