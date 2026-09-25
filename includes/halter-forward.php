<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'tsvd_anfrage_created', 'tsvd_listing_forward_new_inquiry', 20, 4 );
add_filter( 'tsvd_owner_relay_forwarded', 'tsvd_listing_forwarded_in_request', 10, 2 );

const TSVD_LISTING_KINDS = array( 'listing', 'sighting' );

function tsvd_listing_is_inquiry( $anfrage ) {
	return in_array( $anfrage['kind'] ?? '', TSVD_LISTING_KINDS, true );
}

function tsvd_halter_get_anfrage( $anfrage_id ) {
	global $wpdb;
	$table = tsvd_anfragen_table_name();
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $anfrage_id ), ARRAY_A );
}

function tsvd_listing_forward_contact_lines( $anfrage ) {
	$lines = array(
		__( 'Name', 'tsv-tools' ) . ': ' . $anfrage['applicant_name'],
		__( 'E-Mail', 'tsv-tools' ) . ': ' . $anfrage['applicant_email'],
	);
	if ( '' !== trim( (string) $anfrage['applicant_phone'] ) ) {
		$lines[] = __( 'Telefon', 'tsv-tools' ) . ': ' . $anfrage['applicant_phone'];
	}
	return implode( "\n", $lines );
}

function tsvd_listing_forward_body( $anfrage, $submitted, $greeting ) {
	$tier    = tsvd_halter_animal_name( (int) $anfrage['animal_id'] );
	$message = trim( (string) ( $submitted['tsvd_relay_message'] ?? '' ) );
	$intro   = 'sighting' === $anfrage['kind']
		? sprintf( __( 'jemand hat %s möglicherweise gesehen und uns eine Sichtung geschickt:', 'tsv-tools' ), $tier )
		: sprintf( __( 'jemand interessiert sich für %s und hat Dir über unsere Website geschrieben:', 'tsv-tools' ), $tier );
	$parts = array(
		$greeting,
		$intro,
		'' !== $message ? $message : __( '(keine Nachricht)', 'tsv-tools' ),
		__( 'So erreichst Du die Person:', 'tsv-tools' ) . "\n" . tsvd_listing_forward_contact_lines( $anfrage ),
		__( 'Die Person hat zugestimmt, dass wir Dir ihre Kontaktdaten weitergeben. Antworte ihr bitte direkt, Deine eigenen Kontaktdaten haben wir nicht herausgegeben.', 'tsv-tools' ),
		__( 'Viele Grüße', 'tsv-tools' ) . "\n" . get_bloginfo( 'name' ),
	);
	return implode( "\n\n", $parts );
}

function tsvd_listing_forward_new_inquiry( $anfrage_id, $kind, $animal_id, $submitted ) {
	$anfrage = tsvd_halter_get_anfrage( $anfrage_id );
	if ( ! $anfrage || ! tsvd_listing_is_inquiry( $anfrage ) || empty( $submitted['tsvd_relay_consent'] ) ) {
		return;
	}
	$contact = tsvd_halter_contact( (int) $animal_id );
	if ( ! is_email( $contact['email'] ) || ! is_email( $anfrage['applicant_email'] ) ) {
		return;
	}
	$greeting = '' !== $contact['first'] ? sprintf( __( 'Hallo %s,', 'tsv-tools' ), $contact['first'] ) : __( 'Hallo,', 'tsv-tools' );
	$subject  = sprintf( __( 'Anfrage zu %s', 'tsv-tools' ), tsvd_halter_animal_name( (int) $animal_id ) );
	$headers  = array( 'Reply-To: ' . $anfrage['applicant_email'] );
	if ( wp_mail( $contact['email'], $subject, tsvd_listing_forward_body( $anfrage, $submitted, $greeting ), $headers ) ) {
		tsvd_listing_mark_forwarded( $anfrage_id );
		tsvd_listing_forwarded_animals( (int) $animal_id );
	}
}

function tsvd_listing_forwarded_animals( $animal_id = 0 ) {
	static $forwarded = array();
	if ( $animal_id ) {
		$forwarded[ (int) $animal_id ] = true;
	}
	return $forwarded;
}

function tsvd_listing_forwarded_in_request( $forwarded, $animal_id ) {
	return $forwarded || isset( tsvd_listing_forwarded_animals()[ (int) $animal_id ] );
}

function tsvd_listing_mark_forwarded( $anfrage_id ) {
	global $wpdb;
	$wpdb->update(
		tsvd_anfragen_table_name(),
		array( 'status' => 'forwarded', 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => (int) $anfrage_id )
	);
}

function tsvd_anfragen_receive( $anfrage, $party, $body ) {
	global $wpdb;
	$now = current_time( 'mysql' );
	$wpdb->insert( tsvd_anfragen_replies_table_name(), array(
		'anfrage_id' => (int) $anfrage['id'],
		'user_id'    => null,
		'direction'  => 'in',
		'party'      => 'halter' === ( $anfrage['kind'] ?? '' ) ? 'halter' : null,
		'body'       => $body,
		'sent_at'    => $now,
	) );
	$wpdb->update(
		tsvd_anfragen_table_name(),
		array( 'status' => 'open', 'updated_at' => $now ),
		array( 'id' => (int) $anfrage['id'] )
	);
}

function tsvd_anfragen_party_email( $anfrage, $party ) {
	return (string) $anfrage['applicant_email'];
}
