<?php
/**
 * Captures form submissions into wp_tsvd_anfragen: forms that opt in via
 * "_tsvd_form_persist_inquiry", the private-adoption and missing-animal forms, and every
 * submission about an animal owned by a private person (relay via the association).
 *
 * @package TSVD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'tsvd_form_submitted', 'tsvd_anfragen_capture_submission', 10, 4 );

/**
 * Art der Unterhaltung für eine Einsendung.
 *
 * @param int $form_id   Formular-ID.
 * @param int $animal_id Tier-ID aus dem Kontext.
 * @return string 'halter', 'sighting', 'inquiry' oder '' (nicht speichern).
 */
function tsvd_anfragen_submission_kind( $form_id, $animal_id ) {
	$owner_forms = array( (int) get_option( 'tsvd_private_adoption_form', 0 ), (int) get_option( 'tsvd_missing_animals_form', 0 ) );
	if ( in_array( (int) $form_id, $owner_forms, true ) ) {
		return 'halter';
	}
	if ( (int) $form_id === (int) get_option( 'tsvd_missing_sighting_form', 0 ) ) {
		return 'sighting';
	}
	if ( function_exists( 'tsvd_halter_case' ) && '' !== tsvd_halter_case( (int) $animal_id ) ) {
		return 'inquiry';
	}
	return get_post_meta( $form_id, '_tsvd_form_persist_inquiry', true ) ? 'inquiry' : '';
}

/**
 * Name, E-Mail und Telefon der Person, mit der die Unterhaltung geführt wird.
 *
 * @param string $kind           Art der Unterhaltung.
 * @param array  $submitted_data Einsendung nach Feld-ID.
 * @param array  $normalized     Einsendung nach Feldtyp.
 * @return array{name:string,email:string,phone:string}
 */
function tsvd_anfragen_submission_person( $kind, $submitted_data, $normalized ) {
	if ( 'halter' === $kind ) {
		$name = trim( ( $normalized['animal_private_contact_firstname'] ?? '' ) . ' ' . ( $normalized['animal_private_contact_lastname'] ?? '' ) );
		return array( 'name' => $name, 'email' => (string) ( $normalized['animal_private_contact_email'] ?? '' ), 'phone' => '' );
	}
	$person = array(
		'name'  => (string) ( $normalized['applicant_name'] ?? '' ),
		'email' => (string) ( $normalized['email'] ?? '' ),
		'phone' => (string) ( $normalized['tel'] ?? '' ),
	);
	return tsvd_anfragen_person_from_ids( $person, $submitted_data );
}

/**
 * Ergänzt Name/E-Mail aus Feld-IDs, wenn Formulare generische „text“-Felder nutzen.
 *
 * @param array $person         Bisher erkannte Angaben.
 * @param array $submitted_data Einsendung nach Feld-ID.
 * @return array
 */
function tsvd_anfragen_person_from_ids( array $person, array $submitted_data ) {
	$first = '';
	$last  = '';
	foreach ( $submitted_data as $key => $value ) {
		if ( ! is_string( $value ) ) {
			continue;
		}
		if ( '' === $person['email'] && is_email( $value ) ) {
			$person['email'] = $value;
		} elseif ( false !== strpos( $key, 'vorname' ) ) {
			$first = $value;
		} elseif ( false !== strpos( $key, 'nachname' ) ) {
			$last = $value;
		}
	}
	if ( '' === $person['name'] ) {
		$person['name'] = trim( $first . ' ' . $last );
	}
	return $person;
}

/**
 * Speichert die Einsendung als Anfrage und meldet sie an Folge-Module.
 *
 * @param int   $form_id           Formular-ID.
 * @param array $submitted_data    Einsendung nach Feld-ID.
 * @param array $normalized_data   Einsendung nach Feldtyp.
 * @param int   $context_animal_id Tier-ID.
 */
function tsvd_anfragen_capture_submission( $form_id, $submitted_data, $normalized_data, $context_animal_id ) {
	$kind = tsvd_anfragen_submission_kind( $form_id, $context_animal_id );
	if ( '' === $kind ) {
		return;
	}

	global $wpdb;
	$now    = current_time( 'mysql' );
	$person = tsvd_anfragen_submission_person( $kind, (array) $submitted_data, (array) $normalized_data );
	$email  = sanitize_email( $person['email'] );
	$status = tsvd_anfragen_is_blacklisted( $email ) ? 'blocked' : 'open';

	$wpdb->insert(
		tsvd_anfragen_table_name(),
		array(
			'form_id'          => absint( $form_id ),
			'animal_id'        => $context_animal_id ? absint( $context_animal_id ) : null,
			'applicant_name'   => sanitize_text_field( $person['name'] ),
			'applicant_email'  => $email,
			'applicant_phone'  => sanitize_text_field( $person['phone'] ),
			'payload'          => wp_json_encode( $submitted_data ),
			'status'           => $status,
			'kind'             => $kind,
			'assigned_user_id' => function_exists( 'tsvd_halter_responsible_user' ) ? tsvd_halter_responsible_user( (int) $context_animal_id ) : null,
			'created_at'       => $now,
			'updated_at'       => $now,
		),
		array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s' )
	);

	if ( 'blocked' !== $status && $wpdb->insert_id ) {
		do_action( 'tsvd_anfrage_created', (int) $wpdb->insert_id, $kind, (int) $context_animal_id, (array) $submitted_data );
	}
}
