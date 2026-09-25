<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'tsvd_anfrage_created', 'tsvd_halter_relay_new_inquiry', 20, 4 );

function tsvd_halter_relay_anfrage( $anfrage ) {
	if ( ! in_array( $anfrage['kind'] ?? '', array( 'inquiry', 'sighting' ), true ) ) {
		return false;
	}
	$animal_id = (int) $anfrage['animal_id'];
	return $animal_id && '' !== tsvd_halter_case( $animal_id ) && is_email( tsvd_halter_contact( $animal_id )['email'] );
}

function tsvd_halter_first_name( $name ) {
	$parts = preg_split( '/\s+/', trim( (string) $name ) );
	return '' !== $parts[0] ? $parts[0] : __( 'Eine interessierte Person', 'tsv-tools' );
}

function tsvd_halter_relay_intro( $anfrage, $submitted ) {
	$tier    = tsvd_halter_animal_name( (int) $anfrage['animal_id'] );
	$first   = tsvd_halter_first_name( $anfrage['applicant_name'] );
	$message = trim( (string) ( $submitted['tsvd_relay_message'] ?? '' ) );
	$intro   = 'sighting' === $anfrage['kind']
		? sprintf( __( '%1$s hat %2$s möglicherweise gesehen und schreibt:', 'tsv-tools' ), $first, $tier )
		: sprintf( __( '%1$s interessiert sich für %2$s und schreibt:', 'tsv-tools' ), $first, $tier );
	return $intro . "\n\n" . ( '' !== $message ? $message : __( '(keine Nachricht)', 'tsv-tools' ) );
}

function tsvd_halter_relay_footer() {
	return "\n\n" . __( 'Antworte einfach auf diese Mail. Wir leiten Deine Antwort weiter, Eure E-Mail-Adressen bleiben verborgen.', 'tsv-tools' )
		. "\n\n" . __( 'Viele Grüße', 'tsv-tools' ) . "\n" . get_bloginfo( 'name' );
}

function tsvd_halter_get_anfrage( $anfrage_id ) {
	global $wpdb;
	$table = tsvd_anfragen_table_name();
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $anfrage_id ), ARRAY_A );
}

function tsvd_halter_relay_new_inquiry( $anfrage_id, $kind, $animal_id, $submitted ) {
	$anfrage = tsvd_halter_get_anfrage( $anfrage_id );
	if ( ! $anfrage || ! tsvd_halter_relay_anfrage( $anfrage ) || empty( $submitted['tsvd_relay_consent'] ) ) {
		return;
	}
	$contact  = tsvd_halter_contact( (int) $animal_id );
	$greeting = '' !== $contact['first'] ? sprintf( __( 'Hallo %s,', 'tsv-tools' ), $contact['first'] ) : __( 'Hallo,', 'tsv-tools' );
	$body     = $greeting . "\n\n" . tsvd_halter_relay_intro( $anfrage, $submitted ) . tsvd_halter_relay_footer();
	$subject  = sprintf( __( 'Nachricht zu %s', 'tsv-tools' ), tsvd_halter_animal_name( (int) $animal_id ) );
	if ( tsvd_halter_send( $contact['email'], $subject, $body, $anfrage_id ) ) {
		tsvd_halter_log( $anfrage_id, 'out', $body, 'halter' );
	}
}

function tsvd_halter_resolve_party( $anfrage, $sender ) {
	$sender = strtolower( trim( (string) $sender ) );
	if ( '' === $sender ) {
		return '';
	}
	if ( strtolower( (string) $anfrage['applicant_email'] ) === $sender ) {
		return 'applicant';
	}
	if ( tsvd_halter_relay_anfrage( $anfrage ) && strtolower( tsvd_halter_contact( (int) $anfrage['animal_id'] )['email'] ) === $sender ) {
		return 'halter';
	}
	return '';
}

function tsvd_halter_relay_incoming( $anfrage, $party, $body ) {
	if ( ! tsvd_halter_relay_anfrage( $anfrage ) ) {
		return false;
	}
	$animal_id = (int) $anfrage['animal_id'];
	$tier      = tsvd_halter_animal_name( $animal_id );
	if ( 'applicant' === $party ) {
		$to    = tsvd_halter_contact( $animal_id )['email'];
		$intro = sprintf( __( '%1$s hat zu %2$s geantwortet:', 'tsv-tools' ), tsvd_halter_first_name( $anfrage['applicant_name'] ), $tier );
	} else {
		$to    = $anfrage['applicant_email'];
		$intro = sprintf( __( 'Antwort zu %s:', 'tsv-tools' ), $tier );
	}
	$mail = $intro . "\n\n" . $body . tsvd_halter_relay_footer();
	return tsvd_halter_send( $to, sprintf( __( 'Nachricht zu %s', 'tsv-tools' ), $tier ), $mail, (int) $anfrage['id'] );
}

function tsvd_anfragen_receive( $anfrage, $party, $body ) {
	global $wpdb;
	$now     = current_time( 'mysql' );
	$relayed = tsvd_halter_relay_incoming( $anfrage, $party, $body );
	$wpdb->insert( tsvd_anfragen_replies_table_name(), array(
		'anfrage_id' => (int) $anfrage['id'],
		'user_id'    => null,
		'direction'  => 'in',
		'party'      => $relayed || 'halter' === $party ? $party : null,
		'body'       => $body,
		'sent_at'    => $now,
	) );
	$wpdb->update(
		tsvd_anfragen_table_name(),
		array( 'status' => $relayed ? 'answered' : 'open', 'updated_at' => $now ),
		array( 'id' => (int) $anfrage['id'] )
	);
	return $relayed;
}

function tsvd_anfragen_party_email( $anfrage, $party ) {
	if ( 'halter' === $party && ! empty( $anfrage['animal_id'] ) ) {
		return tsvd_halter_contact( (int) $anfrage['animal_id'] )['email'];
	}
	return (string) $anfrage['applicant_email'];
}

function tsvd_halter_mail_from_team( $anfrage, $body ) {
	$animal_id = (int) $anfrage['animal_id'];
	$to        = tsvd_halter_contact( $animal_id )['email'];
	$signature = (string) get_option( 'tsvd_anfragen_signature', '' );
	$mail      = '' !== trim( $signature ) ? $body . "\n\n" . $signature : $body;
	return tsvd_halter_send( $to, sprintf( __( 'Nachricht zu %s', 'tsv-tools' ), tsvd_halter_animal_name( $animal_id ) ), $mail, (int) $anfrage['id'] );
}
