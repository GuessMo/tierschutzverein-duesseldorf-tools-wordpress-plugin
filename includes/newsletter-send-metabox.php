<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes_' . TSVD_NEWSLETTER_CPT, 'tsvd_newsletter_send_metabox' );

function tsvd_newsletter_send_metabox() {
	add_meta_box(
		'tsvd-newsletter-send',
		__( 'Versand & Vorschau', 'tsvd' ),
		'tsvd_newsletter_send_metabox_render',
		TSVD_NEWSLETTER_CPT,
		'side',
		'high'
	);
}

function tsvd_newsletter_send_metabox_render( $post ) {
	$count   = count( tsvd_newsletter_subscriber_ids() );
	$sent_at = (int) get_post_meta( $post->ID, TSVD_NEWSLETTER_SENT_META, true );
	$preview = tsvd_newsletter_render_email( $post->ID );

	echo '<p>';
	printf(
		esc_html( _n( '%d interner Abonnent.', '%d interne Abonnenten.', $count, 'tsvd' ) ),
		(int) $count
	);
	echo ' <a href="' . esc_url( admin_url( 'admin.php?page=tsvd-newsletter' ) ) . '">'
		. esc_html__( 'verwalten', 'tsvd' ) . '</a></p>';

	if ( $sent_at > 0 ) {
		echo '<p><strong>' . esc_html( sprintf( __( 'Gesendet am %s', 'tsvd' ), date_i18n( 'd.m.Y H:i', $sent_at ) ) ) . '</strong></p>';
	}

	echo '<p class="description">' . esc_html__( 'Vorschau zeigt den gespeicherten Stand. Vor dem Senden speichern.', 'tsvd' ) . '</p>';
	echo '<button type="button" class="button button-large tsvd-nl-btn-full tsvd-nl-preview-open">'
		. '<span class="dashicons dashicons-visibility"></span>' . esc_html__( 'Vorschau anzeigen', 'tsvd' ) . '</button>';
	echo '<hr class="tsvd-nl-divider">';

	echo '<dialog class="tsvd-nl-preview-dialog">';
	echo '<div class="tsvd-nl-preview-dialog-head"><strong>' . esc_html__( 'Newsletter-Vorschau', 'tsvd' ) . '</strong>';
	echo '<button type="button" class="button-link tsvd-nl-preview-close" aria-label="' . esc_attr__( 'Schließen', 'tsvd' )
		. '"><span class="dashicons dashicons-no-alt"></span></button></div>';
	echo '<iframe class="tsvd-nl-preview-frame" srcdoc="' . esc_attr( $preview ) . '"></iframe>';
	echo '</dialog>';

	echo '<script>(function(){var b=document.querySelector(".tsvd-nl-preview-open"),d=document.querySelector(".tsvd-nl-preview-dialog");'
		. 'if(!b||!d){return;}b.addEventListener("click",function(){if(d.showModal){d.showModal();}});'
		. 'd.addEventListener("click",function(e){if(e.target===d){d.close();}});'
		. 'var c=d.querySelector(".tsvd-nl-preview-close");if(c){c.addEventListener("click",function(){d.close();});}})();</script>';

	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="margin-top:12px;">';
	echo '<input type="hidden" name="action" value="tsvd_newsletter_send">';
	echo '<input type="hidden" name="post_id" value="' . esc_attr( $post->ID ) . '">';
	wp_nonce_field( 'tsvd_newsletter_send_' . $post->ID );

	echo '<div class="tsvd-nl-send-actions">';
	echo '<button type="submit" name="tsvd_send_mode" value="test" class="button tsvd-nl-btn-full">'
		. esc_html__( 'Testmail an mich', 'tsvd' ) . '</button>';

	$disabled = 0 === $count ? ' disabled' : '';
	echo '<button type="submit" name="tsvd_send_mode" value="all" class="button button-primary tsvd-nl-btn-full"' . $disabled . '>'
		. esc_html( sprintf( __( 'An %d senden', 'tsvd' ), (int) $count ) ) . '</button>';
	echo '</div>';
	echo '</form>';
}

add_action( 'admin_post_tsvd_newsletter_send', 'tsvd_newsletter_handle_send_action' );

function tsvd_newsletter_handle_send_action() {
	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		wp_die( esc_html__( 'Keine Berechtigung.', 'tsvd' ) );
	}
	check_admin_referer( 'tsvd_newsletter_send_' . $post_id );

	$mode    = ( isset( $_POST['tsvd_send_mode'] ) && 'all' === $_POST['tsvd_send_mode'] ) ? 'all' : 'test';
	$subject = get_the_title( $post_id );
	$body    = tsvd_newsletter_render_email( $post_id );

	if ( 'all' === $mode ) {
		$emails = wp_list_pluck( tsvd_newsletter_subscribers(), 'user_email' );
		$result = tsvd_newsletter_send_html( $emails, $subject, $body );
		if ( $result['sent'] > 0 ) {
			update_post_meta( $post_id, TSVD_NEWSLETTER_SENT_META, time() );
		}
	} else {
		$me     = wp_get_current_user();
		$result = tsvd_newsletter_send_html( array( $me->user_email ), '[Test] ' . $subject, $body );
	}

	$redirect = add_query_arg(
		array(
			'tsvd_nl_sent'  => (int) $result['sent'],
			'tsvd_nl_total' => (int) $result['total'],
			'tsvd_nl_mode'  => $mode,
		),
		get_edit_post_link( $post_id, 'raw' )
	);
	wp_safe_redirect( $redirect );
	exit;
}

add_action( 'admin_notices', 'tsvd_newsletter_send_notice' );

function tsvd_newsletter_send_notice() {
	if ( ! isset( $_GET['tsvd_nl_sent'], $_GET['tsvd_nl_total'] ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || TSVD_NEWSLETTER_CPT !== $screen->post_type ) {
		return;
	}

	$sent  = absint( $_GET['tsvd_nl_sent'] );
	$total = absint( $_GET['tsvd_nl_total'] );
	$mode  = isset( $_GET['tsvd_nl_mode'] ) && 'all' === $_GET['tsvd_nl_mode'] ? 'all' : 'test';
	$class = ( $sent === $total && $total > 0 ) ? 'notice-success' : 'notice-warning';

	echo '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><p>';
	if ( 'all' === $mode ) {
		printf( esc_html__( 'Newsletter versendet: %1$d von %2$d Abonnenten.', 'tsvd' ), $sent, $total );
	} else {
		printf( esc_html__( 'Testmail: %1$d von %2$d versendet.', 'tsvd' ), $sent, $total );
	}
	echo '</p></div>';
}
