<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_mail_header( $message, $name ) {
	$attribute = $message->get( $name );
	return $attribute ? $attribute->first() : null;
}

function tsvd_anfragen_mail_sender( $message ) {
	$address = tsvd_anfragen_mail_header( $message, 'from' );
	return $address ? strtolower( (string) $address->mail ) : '';
}

function tsvd_anfragen_mail_token_id( $subject ) {
	return preg_match( '/#(\d+)/', (string) $subject, $match ) ? (int) $match[1] : 0;
}

function tsvd_anfragen_latest_by_email( $email ) {
	global $wpdb;
	if ( '' === $email ) {
		return null;
	}
	$table = tsvd_anfragen_table_name();
	return $wpdb->get_row( $wpdb->prepare(
		"SELECT * FROM {$table} WHERE LOWER(applicant_email) = %s AND deleted_at IS NULL
		 ORDER BY created_at DESC LIMIT 1",
		$email
	), ARRAY_A );
}

function tsvd_anfragen_match_mail( $message ) {
	$sender  = tsvd_anfragen_mail_sender( $message );
	$token   = tsvd_anfragen_mail_token_id( tsvd_anfragen_mail_header( $message, 'subject' ) );
	$anfrage = $token ? tsvd_halter_get_anfrage( $token ) : null;
	if ( $anfrage ) {
		$is_sender = '' !== $sender && strtolower( (string) $anfrage['applicant_email'] ) === $sender;
		return array(
			'anfrage'         => $anfrage,
			'is_exact'        => $is_sender,
			'is_sender_match' => $is_sender,
		);
	}
	$anfrage = tsvd_anfragen_latest_by_email( $sender );
	return array(
		'anfrage'         => $anfrage,
		'is_exact'        => false,
		'is_sender_match' => (bool) $anfrage,
	);
}

function tsvd_anfragen_mail_body( $message ) {
	$body = trim( (string) $message->getTextBody() );
	if ( '' === $body ) {
		$body = trim( wp_strip_all_tags( (string) $message->getHTMLBody() ) );
	}
	$body = tsvd_anfragen_imap_strip_quote( $body );
	$empty = __( '(Leerer Antworttext, siehe Original-Mail im Postfach)', 'tsv-tools' );
	return '' !== $body ? $body : $empty;
}

function tsvd_anfragen_mail_received_at( $message ) {
	$date = tsvd_anfragen_mail_header( $message, 'date' );
	if ( ! $date instanceof DateTimeInterface ) {
		return current_time( 'mysql' );
	}
	return wp_date( 'Y-m-d H:i:s', $date->getTimestamp() );
}

function tsvd_anfragen_mail_key( $message, $folder_path ) {
	$message_id = (string) tsvd_anfragen_mail_header( $message, 'message_id' );
	return '' !== $message_id ? $message_id : $folder_path . ':' . (int) $message->uid;
}

function tsvd_anfragen_candidate_from_mail( $message, $anfrage, $folder_path ) {
	return array(
		'anfrage_id'  => (int) $anfrage['id'],
		'folder'      => $folder_path,
		'message_uid' => (int) $message->uid,
		'message_key' => substr( tsvd_anfragen_mail_key( $message, $folder_path ), 0, 191 ),
		'sender'      => tsvd_anfragen_mail_sender( $message ),
		'subject'     => substr( (string) tsvd_anfragen_mail_header( $message, 'subject' ), 0, 255 ),
		'body'        => tsvd_anfragen_mail_body( $message ),
		'received_at' => tsvd_anfragen_mail_received_at( $message ),
	);
}
