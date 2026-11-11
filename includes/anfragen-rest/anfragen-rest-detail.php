<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ANFRAGEN_REST_CONTACT_FIELDS = array(
	'bewerber_name',
	'bewerber_telefon',
	'bewerber_email',
	'interesse_tier',
);

function tsvd_anfragen_rest_detail( WP_REST_Request $request ) {
	$anfrage = tsvd_anfragen_rest_anfrage( $request );
	if ( ! $anfrage ) {
		return tsvd_anfragen_rest_not_found();
	}
	$payload = json_decode( $anfrage['payload'], true );
	$replies = tsvd_anfragen_replies_for( (int) $anfrage['id'] );
	return rest_ensure_response(
		array(
			'id'              => (int) $anfrage['id'],
			'name'            => $anfrage['applicant_name'],
			'email'           => $anfrage['applicant_email'],
			'phone'           => $anfrage['applicant_phone'],
			'interest'        => is_array( $payload ) ? (string) ( $payload['interesse_tier'] ?? '' ) : '',
			'status'          => $anfrage['status'],
			'calcStatus'      => tsvd_anfragen_calc_status( $anfrage ),
			'isTrashed'       => ! empty( $anfrage['deleted_at'] ),
			'assignedUserId'  => (int) $anfrage['assigned_user_id'],
			'animal'          => tsvd_anfragen_rest_animal( (int) $anfrage['animal_id'] ),
			'createdLabel'    => tsvd_anfragen_format_local( $anfrage['created_at'] ),
			'facts'           => tsvd_anfragen_payload_facts( $anfrage, TSVD_ANFRAGEN_REST_CONTACT_FIELDS ),
			'participants'    => tsvd_anfragen_participant_names( $replies ),
			'messages'        => tsvd_anfragen_rest_messages( $replies, $anfrage['applicant_name'] ),
			'composer'        => tsvd_anfragen_composer_texts( $anfrage ),
			'pendingApproval' => tsvd_owner_has_pending_approval( $anfrage ),
		)
	);
}

function tsvd_anfragen_rest_animal( $animal_id ) {
	if ( ! $animal_id || 'animals' !== get_post_type( $animal_id ) ) {
		return null;
	}
	return array(
		'id'       => $animal_id,
		'name'     => tsvd_anfragen_animal_name( $animal_id ),
		'thumbUrl' => (string) get_the_post_thumbnail_url( $animal_id, 'thumbnail' ),
		'facts'    => tsvd_anfragen_rest_animal_facts( $animal_id ),
		'editUrl'  => (string) get_edit_post_link( $animal_id, 'raw' ),
		'viewUrl'  => (string) get_permalink( $animal_id ),
	);
}

function tsvd_anfragen_rest_animal_facts( $animal_id ) {
	$facts = array();
	$terms = get_the_terms( $animal_id, 'animal_breed' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$facts[] = implode( ' · ', array_slice( wp_list_pluck( $terms, 'name' ), 0, 2 ) );
	}
	$status   = get_post_meta( $animal_id, 'animal_adoption_status', true );
	$adoption = tsvd_anfragen_animal_adoption_label( $status );
	if ( $adoption ) {
		$facts[] = $adoption;
	}
	return $facts;
}

function tsvd_anfragen_rest_messages( $replies, $applicant_name ) {
	$messages = array();
	foreach ( $replies as $reply ) {
		$is_pending = tsvd_anfragen_reply_is_pending( $reply );
		$messages[] = array(
			'id'          => (int) $reply['id'],
			'direction'   => $reply['direction'],
			'author'      => tsvd_anfragen_reply_author( $reply, $applicant_name ),
			'stamp'       => tsvd_anfragen_format_local( $reply['sent_at'] ),
			'isEdited'    => ! empty( $reply['edited_at'] ),
			'body'        => $reply['body'],
			'isPending'   => $is_pending,
			'remaining'   => $is_pending ? tsvd_anfragen_reply_remaining( $reply ) : 0,
			'isMutable'   => tsvd_anfragen_reply_is_mutable( $reply ),
		);
	}
	return $messages;
}
