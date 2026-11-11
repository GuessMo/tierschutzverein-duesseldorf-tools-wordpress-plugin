<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_rest_candidates() {
	return rest_ensure_response(
		array_map( 'tsvd_anfragen_rest_candidate_item', tsvd_anfragen_candidates_all() )
	);
}

function tsvd_anfragen_rest_candidate_item( $candidate ) {
	return array(
		'id'            => (int) $candidate['id'],
		'anfrageId'     => (int) $candidate['anfrage_id'],
		'receivedLabel' => tsvd_anfragen_format_local( $candidate['received_at'] ),
		'sender'        => $candidate['sender'],
		'subject'       => $candidate['subject'],
		'excerpt'       => wp_trim_words( $candidate['body'], 30 ),
		'isSpamFolder'  => tsvd_anfragen_imap_spam_folder() === $candidate['folder'],
	);
}

function tsvd_anfragen_rest_decide_candidate( WP_REST_Request $request ) {
	$candidate = tsvd_anfragen_candidate_get( (int) $request['id'] );
	if ( ! $candidate ) {
		return new WP_Error(
			'tsvd_anfragen_candidate_gone',
			__( 'Diese Mail wurde bereits bearbeitet.', 'tsv-tools' ),
			array( 'status' => 409 )
		);
	}
	$result = 'approve' === $request['decision']
		? tsvd_anfragen_candidate_approve( $candidate )
		: tsvd_anfragen_candidate_reject( $candidate );
	$notices = tsvd_anfragen_candidate_notices();
	if ( 'failed' === $result ) {
		return new WP_Error(
			'tsvd_anfragen_candidate_failed',
			$notices['failed'][1],
			array( 'status' => 502 )
		);
	}
	return tsvd_anfragen_rest_message( $notices[ $result ][1] );
}
