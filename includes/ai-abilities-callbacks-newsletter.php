<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_tools_ai_can_manage_newsletter() {
	return current_user_can( 'manage_options' );
}

function tsvd_tools_ai_create_newsletter( $input ) {
	$title = isset( $input['title'] ) ? sanitize_text_field( $input['title'] ) : '';
	if ( '' === $title ) {
		return new WP_Error( 'missing_title', __( 'Titel erforderlich.', 'tsv-tools' ) );
	}

	$id = wp_insert_post(
		array(
			'post_type'   => TSVD_NEWSLETTER_CPT,
			'post_title'  => $title,
			'post_status' => 'draft',
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return $id;
	}

	$blocks = isset( $input['blocks'] ) && is_array( $input['blocks'] ) ? $input['blocks'] : array();
	if ( ! empty( $blocks ) ) {
		tsvd_newsletter_save_blocks( $id, $blocks );
	}

	return array(
		'id'       => (int) $id,
		'edit_url' => get_edit_post_link( $id, 'raw' ),
	);
}

function tsvd_tools_ai_send_newsletter( $input ) {
	$id = isset( $input['id'] ) ? absint( $input['id'] ) : 0;
	if ( ! $id || TSVD_NEWSLETTER_CPT !== get_post_type( $id ) ) {
		return new WP_Error( 'bad_id', __( 'Kein gueltiger Newsletter.', 'tsv-tools' ) );
	}

	$subject = get_the_title( $id );
	$body    = tsvd_newsletter_render_email( $id );

	if ( ! empty( $input['recipients'] ) && is_array( $input['recipients'] ) ) {
		return tsvd_newsletter_send_html( $input['recipients'], $subject, $body );
	}

	if ( ! empty( $input['to_subscribers'] ) ) {
		$emails = wp_list_pluck( tsvd_newsletter_subscribers(), 'user_email' );
		$result = tsvd_newsletter_send_html( $emails, $subject, $body );
		if ( $result['sent'] > 0 ) {
			update_post_meta( $id, TSVD_NEWSLETTER_SENT_META, time() );
		}
		return $result;
	}

	return new WP_Error( 'no_target', __( 'recipients oder to_subscribers erforderlich.', 'tsv-tools' ) );
}
