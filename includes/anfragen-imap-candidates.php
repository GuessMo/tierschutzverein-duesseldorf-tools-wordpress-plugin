<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Webklex\PHPIMAP\Exceptions\MessageHeaderFetchingException;
use Webklex\PHPIMAP\Exceptions\MessageNotFoundException;

const TSVD_ANFRAGEN_IMAP_SPAM_DEFAULT    = '[Gmail]/Spam';
const TSVD_ANFRAGEN_IMAP_SPAM_SCAN_LIMIT = 100;

function tsvd_anfragen_imap_spam_folder() {
	return (string) get_option( 'tsvd_anfragen_imap_spam_folder', TSVD_ANFRAGEN_IMAP_SPAM_DEFAULT );
}

function tsvd_anfragen_imap_collect_spam( $client ) {
	$path = tsvd_anfragen_imap_spam_folder();
	if ( '' === $path ) {
		return;
	}
	$folder = $client->getFolderByPath( $path, false, true );
	if ( ! $folder ) {
		error_log( 'TSVD Anfragen IMAP: Spam-Ordner nicht gefunden: ' . $path );
		return;
	}
	$headers = $folder->query()->whereUnseen()->setFetchBody( false )
		->limit( TSVD_ANFRAGEN_IMAP_SPAM_SCAN_LIMIT )->get();
	foreach ( $headers as $header ) {
		tsvd_anfragen_imap_collect_spam_mail( $folder, $header );
	}
}

function tsvd_anfragen_imap_collect_spam_mail( $folder, $header ) {
	try {
		$match = tsvd_anfragen_match_mail( $header );
		if ( ! $match['anfrage'] || ! $match['is_sender_match'] ) {
			return;
		}
		$message = $folder->query()->getMessageByUid( (int) $header->uid );
		tsvd_anfragen_candidate_insert(
			tsvd_anfragen_candidate_from_mail( $message, $match['anfrage'], $folder->path )
		);
	} catch ( \Throwable $e ) {
		error_log( 'TSVD Anfragen IMAP: Spam-Mail übersprungen: ' . $e->getMessage() );
	}
}

function tsvd_anfragen_imap_with_candidate_mail( $candidate, callable $action ) {
	$client = tsvd_anfragen_imap_make_client();
	if ( is_wp_error( $client ) ) {
		return $client;
	}
	try {
		$folder = $client->getFolderByPath( $candidate['folder'], false, true );
		if ( ! $folder ) {
			return tsvd_anfragen_imap_mail_gone();
		}
		$action( $folder->query()->getMessageByUid( (int) $candidate['message_uid'] ) );
		return true;
	} catch ( MessageHeaderFetchingException | MessageNotFoundException $e ) {
		return tsvd_anfragen_imap_mail_gone();
	} catch ( \Throwable $e ) {
		return new WP_Error( 'imap_error', $e->getMessage() );
	} finally {
		$client->disconnect();
	}
}

function tsvd_anfragen_imap_mail_gone() {
	$text = __( 'Die Mail ist im Postfach nicht mehr vorhanden.', 'tsv-tools' );
	return new WP_Error( 'mail_not_found', $text );
}

function tsvd_anfragen_imap_release_candidate( $candidate ) {
	$inbox = tsvd_anfragen_imap_get_settings()['folder'];
	return tsvd_anfragen_imap_with_candidate_mail(
		$candidate,
		function ( $message ) use ( $candidate, $inbox ) {
			$message->setFlag( 'Seen' );
			if ( $candidate['folder'] !== $inbox ) {
				tsvd_anfragen_imap_move_quietly( $message, $inbox );
			}
		}
	);
}

function tsvd_anfragen_imap_move_quietly( $message, $folder_path ) {
	try {
		$message->move( $folder_path );
	} catch ( \Throwable $e ) {
		error_log( 'TSVD Anfragen IMAP: Verschieben fehlgeschlagen: ' . $e->getMessage() );
	}
}

function tsvd_anfragen_imap_dismiss_candidate( $candidate ) {
	return tsvd_anfragen_imap_with_candidate_mail(
		$candidate,
		function ( $message ) {
			$message->setFlag( 'Seen' );
		}
	);
}

function tsvd_anfragen_imap_candidate_done( $result ) {
	return ! is_wp_error( $result ) || 'mail_not_found' === $result->get_error_code();
}
