<?php
/**
 * Captures form submissions into wp_tsvd_anfragen when a form opts in via the
 * "_tsvd_form_persist_inquiry" checkbox (theme: form-settings-metabox.php).
 *
 * @package TSVD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'tsvd_form_submitted', 'tsvd_anfragen_capture_submission', 10, 4 );

function tsvd_anfragen_capture_submission( $form_id, $submitted_data, $normalized_data, $context_animal_id ) {
	if ( ! get_post_meta( $form_id, '_tsvd_form_persist_inquiry', true ) ) {
		return;
	}

	global $wpdb;
	$now = current_time( 'mysql' );

	$wpdb->insert(
		tsvd_anfragen_table_name(),
		array(
			'form_id'         => absint( $form_id ),
			'animal_id'       => $context_animal_id ? absint( $context_animal_id ) : null,
			'applicant_name'  => isset( $normalized_data['applicant_name'] ) ? sanitize_text_field( $normalized_data['applicant_name'] ) : '',
			'applicant_email' => isset( $normalized_data['email'] ) ? sanitize_email( $normalized_data['email'] ) : '',
			'applicant_phone' => isset( $normalized_data['tel'] ) ? sanitize_text_field( $normalized_data['tel'] ) : '',
			'payload'         => wp_json_encode( $submitted_data ),
			'status'          => 'open',
			'created_at'      => $now,
			'updated_at'      => $now,
		),
		array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
	);
}
