<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'tsvd_anfrage_created', 'tsvd_halter_on_submission', 20, 2 );
add_action( 'transition_post_status', 'tsvd_halter_on_publish', 10, 3 );
add_filter( 'update_post_metadata', 'tsvd_halter_on_status_meta', 10, 4 );

function tsvd_halter_links( $animal_id ) {
	return array(
		'owner'         => tsvd_halter_owner_url( $animal_id ),
		'adopted'       => tsvd_halter_owner_url( $animal_id, 'adopted' ),
		'paused'        => tsvd_halter_owner_url( $animal_id, 'paused' ),
		'active'        => tsvd_halter_owner_url( $animal_id, 'active' ),
		'reunited'      => tsvd_halter_owner_url( $animal_id, 'reunited' ),
		'still_missing' => tsvd_halter_owner_url( $animal_id, 'still_missing' ),
	);
}

function tsvd_halter_template_texts( $event, $case, $tier, $links, $permalink ) {
	$manage  = "\n\n" . sprintf( __( 'Dein Tier verwalten: %s', 'tsv-tools' ), $links['owner'] );
	$private = array(
		'received' => array( __( 'Wir haben Deine Anzeige erhalten', 'tsv-tools' ), sprintf( __( "danke, dass Du %s bei uns eingestellt hast. Wir prüfen Deine Angaben und melden uns, sobald die Anzeige online ist.", 'tsv-tools' ), $tier ) . $manage ),
		'approved' => array( sprintf( __( '%s ist jetzt online', 'tsv-tools' ), $tier ), sprintf( __( "%1\$s ist jetzt auf unserer Website zu sehen:\n%2\$s\n\nAnfragen leiten wir Dir per Mail weiter. Antworte einfach darauf, wir leiten Deine Antwort weiter. Deine E-Mail-Adresse bleibt verborgen.\n\n%1\$s ist vermittelt? %3\$s\nAnzeige pausieren: %4\$s", 'tsv-tools' ), $tier, $permalink, $links['adopted'], $links['paused'] ) ),
		'adopted'  => array( sprintf( __( '%s ist vermittelt', 'tsv-tools' ), $tier ), sprintf( __( 'wie schön, dass %s ein neues Zuhause gefunden hat. Die Anzeige ist jetzt offline. Danke, dass Du uns Bescheid gegeben hast.', 'tsv-tools' ), $tier ) ),
		'paused'   => array( sprintf( __( 'Anzeige für %s pausiert', 'tsv-tools' ), $tier ), sprintf( __( "die Anzeige für %1\$s ist pausiert und nicht mehr zu sehen.\n\nWieder anzeigen: %2\$s", 'tsv-tools' ), $tier, $links['active'] ) ),
		'reminder' => array( sprintf( __( 'Ist %s noch zu vermitteln?', 'tsv-tools' ), $tier ), sprintf( __( "ist %1\$s noch zu vermitteln? Bitte gib uns kurz Bescheid, damit die Anzeige aktuell bleibt.\n\nJa, weiter anzeigen: %2\$s\nVermittelt: %3\$s\nAnzeige pausieren: %4\$s", 'tsv-tools' ), $tier, $links['active'], $links['adopted'], $links['paused'] ) ),
	);
	$missing = array(
		'received'  => array( __( 'Wir haben Deine Vermisstmeldung erhalten', 'tsv-tools' ), sprintf( __( "danke für Deine Meldung zu %s. Wir prüfen die Angaben und melden uns, sobald sie online ist. Wir drücken die Daumen.", 'tsv-tools' ), $tier ) . $manage ),
		'published' => array( sprintf( __( 'Vermisstmeldung für %s ist online', 'tsv-tools' ), $tier ), sprintf( __( "die Vermisstmeldung für %1\$s ist jetzt auf unserer Website:\n%2\$s\n\nSichtungen leiten wir Dir per Mail weiter. Antworte einfach darauf, wir leiten Deine Antwort weiter. Deine E-Mail-Adresse bleibt verborgen.\n\n%1\$s ist wieder da? %3\$s", 'tsv-tools' ), $tier, $permalink, $links['reunited'] ) ),
		'found'     => array( sprintf( __( '%s wurde gefunden', 'tsv-tools' ), $tier ), sprintf( __( 'gute Nachricht: %s wurde gefunden. Bitte melde Dich bei uns, antworte einfach auf diese Mail.', 'tsv-tools' ), $tier ) ),
		'reunited'  => array( sprintf( __( '%s ist wieder zuhause', 'tsv-tools' ), $tier ), sprintf( __( 'wie schön, dass %s wieder zuhause ist. Die Vermisstmeldung ist beendet.', 'tsv-tools' ), $tier ) ),
		'reminder'  => array( sprintf( __( 'Wird %s noch vermisst?', 'tsv-tools' ), $tier ), sprintf( __( "wird %1\$s noch vermisst? Bitte gib uns kurz Bescheid, damit die Meldung aktuell bleibt.\n\nJa, noch vermisst: %2\$s\nWieder zuhause: %3\$s", 'tsv-tools' ), $tier, $links['still_missing'], $links['reunited'] ) ),
	);
	$set = 'missing' === $case ? $missing : $private;
	return isset( $set[ $event ] ) ? $set[ $event ] : null;
}

function tsvd_halter_notify( $animal_id, $event ) {
	$case = tsvd_halter_case( $animal_id );
	if ( '' === $case || ! empty( $GLOBALS['tsvd_halter_suppress'] ) ) {
		return false;
	}
	$contact = tsvd_halter_contact( $animal_id );
	if ( ! is_email( $contact['email'] ) ) {
		return false;
	}
	$texts = tsvd_halter_template_texts( $event, $case, tsvd_halter_animal_name( $animal_id ), tsvd_halter_links( $animal_id ), get_permalink( $animal_id ) );
	if ( ! $texts ) {
		return false;
	}
	$greeting = '' !== $contact['first'] ? sprintf( __( 'Hallo %s,', 'tsv-tools' ), $contact['first'] ) : __( 'Hallo,', 'tsv-tools' );
	$body     = $greeting . "\n\n" . $texts[1] . "\n\n" . __( 'Viele Grüße', 'tsv-tools' ) . "\n" . get_bloginfo( 'name' );
	return tsvd_halter_mail( $animal_id, $texts[0], $body );
}

function tsvd_halter_on_submission( $anfrage_id, $kind ) {
	global $wpdb;
	if ( 'halter' !== $kind ) {
		return;
	}
	$table     = tsvd_anfragen_table_name();
	$animal_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT animal_id FROM {$table} WHERE id = %d", $anfrage_id ) );
	if ( $animal_id ) {
		tsvd_halter_notify( $animal_id, 'received' );
	}
}

function tsvd_halter_on_publish( $new_status, $old_status, $post ) {
	if ( 'publish' !== $new_status || 'publish' === $old_status || 'animals' !== $post->post_type ) {
		return;
	}
	if ( 'missing' === get_post_meta( $post->ID, 'animal_missing_status', true ) ) {
		tsvd_halter_notify( $post->ID, 'published' );
	}
}

function tsvd_halter_status_event( $key, $old, $new ) {
	if ( 'animal_missing_status' === $key && in_array( $new, array( 'found', 'reunited' ), true ) ) {
		return $new;
	}
	if ( 'animal_adoption_status' !== $key ) {
		return '';
	}
	if ( 'adopted' === $new ) {
		return 'adopted';
	}
	return ( 'for_adoption' === $old && 'not_for_adoption' === $new ) ? 'paused' : '';
}

function tsvd_halter_on_status_meta( $check, $post_id, $key, $value ) {
	if ( ! in_array( $key, array( 'animal_adoption_status', 'animal_missing_status' ), true ) || 'animals' !== get_post_type( $post_id ) ) {
		return $check;
	}
	$old = (string) get_post_meta( $post_id, $key, true );
	if ( $old === (string) $value ) {
		return $check;
	}
	$event = tsvd_halter_status_event( $key, $old, (string) $value );
	if ( '' !== $event ) {
		add_action( 'shutdown', function () use ( $post_id, $event ) {
			tsvd_halter_notify( $post_id, $event );
		} );
	}
	return $check;
}
