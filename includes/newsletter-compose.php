<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'tsvd_newsletter_compose_menu', 11 );

function tsvd_newsletter_compose_menu() {
	add_submenu_page(
		'tsvd-newsletter',
		__( 'Newsletter erstellen', 'tsvd' ),
		__( 'Erstellen & senden', 'tsvd' ),
		TSVD_NEWSLETTER_CAP,
		'tsvd-newsletter-compose',
		'tsvd_newsletter_render_compose_page'
	);
}

function tsvd_newsletter_handle_compose() {
	if ( ! isset( $_POST['tsvd_newsletter_compose_nonce'] ) ) {
		return null;
	}
	if ( ! wp_verify_nonce( $_POST['tsvd_newsletter_compose_nonce'], 'tsvd_newsletter_compose' ) ) {
		return null;
	}
	if ( ! current_user_can( TSVD_NEWSLETTER_CAP ) ) {
		return null;
	}

	$subject = isset( $_POST['tsvd_newsletter_subject'] )
		? sanitize_text_field( wp_unslash( $_POST['tsvd_newsletter_subject'] ) )
		: '';
	$content = isset( $_POST['tsvd_newsletter_content'] )
		? wp_kses_post( wp_unslash( $_POST['tsvd_newsletter_content'] ) )
		: '';

	if ( '' === $subject || '' === trim( wp_strip_all_tags( $content ) ) ) {
		return array( 'error' => __( 'Betreff und Inhalt sind erforderlich.', 'tsvd' ) );
	}

	$is_send = isset( $_POST['tsvd_newsletter_do_send'] );

	if ( ! $is_send ) {
		$me     = wp_get_current_user();
		$result = tsvd_newsletter_send_html( array( $me->user_email ), '[Test] ' . $subject, $content );

		return array( 'mode' => 'test', 'result' => $result );
	}

	$emails = wp_list_pluck( tsvd_newsletter_subscribers(), 'user_email' );
	$result = tsvd_newsletter_send_html( $emails, $subject, $content );

	return array( 'mode' => 'send', 'result' => $result );
}

function tsvd_newsletter_render_compose_notice( array $outcome ) {
	if ( isset( $outcome['error'] ) ) {
		echo '<div class="notice notice-error"><p>' . esc_html( $outcome['error'] ) . '</p></div>';
		return;
	}

	$result = $outcome['result'];
	$class  = empty( $result['failed'] ) ? 'notice-success' : 'notice-warning';

	echo '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><p>';
	if ( 'test' === $outcome['mode'] ) {
		printf(
			esc_html__( 'Testmail versendet: %1$d von %2$d.', 'tsvd' ),
			(int) $result['sent'],
			(int) $result['total']
		);
	} else {
		printf(
			esc_html__( 'Newsletter versendet: %1$d von %2$d Abonnenten.', 'tsvd' ),
			(int) $result['sent'],
			(int) $result['total']
		);
	}
	if ( ! empty( $result['failed'] ) ) {
		echo ' ' . esc_html( sprintf( __( 'Fehlgeschlagen: %s', 'tsvd' ), implode( ', ', $result['failed'] ) ) );
	}
	echo '</p></div>';
}

function tsvd_newsletter_render_compose_page() {
	if ( ! current_user_can( TSVD_NEWSLETTER_CAP ) ) {
		return;
	}

	$outcome          = tsvd_newsletter_handle_compose();
	$subscriber_count = count( tsvd_newsletter_subscriber_ids() );
	$keep_input       = is_array( $outcome ) && isset( $outcome['error'] );

	$subject = $keep_input && isset( $_POST['tsvd_newsletter_subject'] )
		? sanitize_text_field( wp_unslash( $_POST['tsvd_newsletter_subject'] ) )
		: '';
	$content = $keep_input && isset( $_POST['tsvd_newsletter_content'] )
		? wp_kses_post( wp_unslash( $_POST['tsvd_newsletter_content'] ) )
		: '';

	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Newsletter erstellen & senden', 'tsvd' ) . '</h1>';

	if ( is_array( $outcome ) ) {
		tsvd_newsletter_render_compose_notice( $outcome );
	}

	echo '<p>';
	printf(
		esc_html( _n( 'Aktuell %d interner Abonnent.', 'Aktuell %d interne Abonnenten.', $subscriber_count, 'tsvd' ) ),
		(int) $subscriber_count
	);
	echo ' <a href="' . esc_url( admin_url( 'admin.php?page=tsvd-newsletter' ) ) . '">'
		. esc_html__( 'Abonnenten verwalten', 'tsvd' ) . '</a></p>';

	echo '<form method="post">';
	wp_nonce_field( 'tsvd_newsletter_compose', 'tsvd_newsletter_compose_nonce' );

	echo '<table class="form-table" role="presentation"><tbody><tr>';
	echo '<th scope="row"><label for="tsvd_newsletter_subject">' . esc_html__( 'Betreff', 'tsvd' ) . '</label></th>';
	echo '<td><input name="tsvd_newsletter_subject" id="tsvd_newsletter_subject" type="text" class="regular-text" '
		. 'value="' . esc_attr( $subject ) . '" required></td>';
	echo '</tr></tbody></table>';

	wp_editor(
		$content,
		'tsvd_newsletter_content',
		array(
			'textarea_rows' => 14,
			'media_buttons' => false,
			'teeny'         => true,
		)
	);

	echo '<p class="submit">';
	submit_button( __( 'Testmail an mich senden', 'tsvd' ), 'secondary', 'tsvd_newsletter_do_test', false );
	echo ' ';
	$send_label = sprintf( __( 'An %d Abonnenten senden', 'tsvd' ), (int) $subscriber_count );
	$disabled   = 0 === $subscriber_count ? array( 'disabled' => 'disabled' ) : array();
	submit_button( $send_label, 'primary', 'tsvd_newsletter_do_send', false, $disabled );
	echo '</p>';

	echo '</form></div>';
}
