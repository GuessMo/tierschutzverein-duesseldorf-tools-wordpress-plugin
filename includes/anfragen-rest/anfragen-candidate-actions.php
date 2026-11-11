<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_candidate_approve( $candidate ) {
	$anfrage  = tsvd_halter_get_anfrage( (int) $candidate['anfrage_id'] );
	$released = $anfrage ? tsvd_anfragen_imap_release_candidate( $candidate ) : null;
	if ( ! $anfrage || ! tsvd_anfragen_imap_candidate_done( $released ) ) {
		return 'failed';
	}
	tsvd_anfragen_receive( $anfrage, '', $candidate['body'] );
	tsvd_anfragen_candidate_mark_decided( (int) $candidate['id'] );
	return 'approved';
}

function tsvd_anfragen_candidate_reject( $candidate ) {
	$dismissed = tsvd_anfragen_imap_dismiss_candidate( $candidate );
	if ( ! tsvd_anfragen_imap_candidate_done( $dismissed ) ) {
		return 'failed';
	}
	tsvd_anfragen_candidate_mark_decided( (int) $candidate['id'] );
	return 'rejected';
}
