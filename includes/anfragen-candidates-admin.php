<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'tsvd_anfragen_before_messenger', 'tsvd_anfragen_render_candidates' );
add_action( 'admin_post_tsvd_anfrage_candidate_approve', 'tsvd_anfragen_handle_candidate_approve' );
add_action( 'admin_post_tsvd_anfrage_candidate_reject', 'tsvd_anfragen_handle_candidate_reject' );

function tsvd_anfragen_render_candidates() {
	tsvd_anfragen_render_candidate_notice();
	$candidates = current_user_can( 'manage_options' ) ? tsvd_anfragen_candidates_all() : array();
	if ( ! $candidates ) {
		return;
	}
	$count = count( $candidates );
	$title = _n(
		'%d Mail wartet auf Zuordnung',
		'%d Mails warten auf Zuordnung',
		$count,
		'tsv-tools'
	);
	echo '<details class="tsvd-panel tsvd-panel--note tsvd-candidates"><summary><strong>'
		. esc_html( sprintf( $title, $count ) ) . '</strong></summary>';
	echo '<p>' . esc_html__(
		'Diese Mails gehören vermutlich zu einer Anfrage, wurden aber nicht automatisch übernommen: '
		. 'Absender oder Anfragen-Nummer passen nicht genau, oder Gmail hat sie als Spam einsortiert.',
		'tsv-tools'
	) . '</p>';
	echo '<table class="widefat striped"><thead><tr>';
	array_map( 'tsvd_anfragen_render_candidate_heading', tsvd_anfragen_candidate_headings() );
	echo '</tr></thead><tbody>';
	array_map( 'tsvd_anfragen_render_candidate_row', $candidates );
	echo '</tbody></table></details>';
}

function tsvd_anfragen_candidate_headings() {
	return array(
		__( 'Eingang', 'tsv-tools' ),
		__( 'Absender und Betreff', 'tsv-tools' ),
		__( 'Ordner', 'tsv-tools' ),
		__( 'Anfrage', 'tsv-tools' ),
		__( 'Aktionen', 'tsv-tools' ),
	);
}

function tsvd_anfragen_render_candidate_heading( $label ) {
	echo '<th scope="col">' . esc_html( $label ) . '</th>';
}

function tsvd_anfragen_render_candidate_row( $candidate ) {
	$anfrage_id = (int) $candidate['anfrage_id'];
	$is_spam    = tsvd_anfragen_imap_spam_folder() === $candidate['folder'];
	$link       = add_query_arg( 'view', $anfrage_id, tsvd_anfragen_list_base_url() );
	$received   = tsvd_anfragen_format_local( $candidate['received_at'] );
	echo '<tr><td>' . esc_html( $received ) . '</td>';
	echo '<td><strong>' . esc_html( $candidate['sender'] ) . '</strong><br>'
		. esc_html( $candidate['subject'] );
	echo '<p class="description">' . esc_html( wp_trim_words( $candidate['body'], 30 ) ) . '</p></td>';
	$folder     = $is_spam ? __( 'Spam', 'tsv-tools' ) : __( 'Posteingang', 'tsv-tools' );
	echo '<td>' . esc_html( $folder ) . '</td>';
	echo '<td><a href="' . esc_url( $link ) . '">'
		. esc_html( sprintf( __( 'Anfrage #%d', 'tsv-tools' ), $anfrage_id ) ) . '</a></td><td>';
	tsvd_anfragen_render_candidate_actions( (int) $candidate['id'] );
	echo '</td></tr>';
}

function tsvd_anfragen_render_candidate_actions( $id ) {
	tsvd_anfragen_action_form(
		$id, 'tsvd_anfrage_candidate_approve', 'tsvd_anfrage_candidate_approve_',
		__( 'Übernehmen', 'tsv-tools' ), 'dashicons-yes', 'tsvd-anf-btn--primary'
	);
	tsvd_anfragen_action_form(
		$id, 'tsvd_anfrage_candidate_reject', 'tsvd_anfrage_candidate_reject_',
		__( 'Verwerfen', 'tsv-tools' ), 'dashicons-no-alt', 'tsvd-anf-btn--danger',
		__(
			'Mail verwerfen? Sie bleibt im Postfach, wird dort als gelesen markiert und hier gelöscht.',
			'tsv-tools'
		)
	);
}

function tsvd_anfragen_candidate_notices() {
	return array(
		'approved' => array( 'success', __( 'Mail in die Anfrage übernommen.', 'tsv-tools' ) ),
		'rejected' => array( 'success', __( 'Mail verworfen.', 'tsv-tools' ) ),
		'gone'     => array( 'warning', __( 'Diese Mail wurde bereits bearbeitet.', 'tsv-tools' ) ),
		'failed'   => array( 'error', __(
			'Das Postfach war nicht erreichbar. Die Mail wurde nicht verändert, '
			. 'bitte später erneut versuchen.',
			'tsv-tools'
		) ),
	);
}

function tsvd_anfragen_render_candidate_notice() {
	$result  = isset( $_GET['candidate'] ) ? sanitize_key( $_GET['candidate'] ) : '';
	$notices = tsvd_anfragen_candidate_notices();
	if ( ! isset( $notices[ $result ] ) ) {
		return;
	}
	echo '<div class="notice notice-' . esc_attr( $notices[ $result ][0] ) . ' is-dismissible"><p>'
		. esc_html( $notices[ $result ][1] ) . '</p></div>';
}

function tsvd_anfragen_checked_candidate( $nonce_prefix ) {
	$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
	if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( $nonce_prefix . $id ) ) {
		wp_die( esc_html__( 'Keine Berechtigung.', 'tsv-tools' ) );
	}
	$candidate = tsvd_anfragen_candidate_get( $id );
	if ( ! $candidate ) {
		tsvd_anfragen_candidate_redirect( 'gone' );
	}
	return $candidate;
}

function tsvd_anfragen_candidate_redirect( $result, $anfrage_id = 0 ) {
	$args = array( 'candidate' => $result );
	if ( $anfrage_id ) {
		$args['view'] = $anfrage_id;
	}
	wp_safe_redirect( add_query_arg( $args, tsvd_anfragen_list_base_url() ) );
	exit;
}

function tsvd_anfragen_handle_candidate_approve() {
	$candidate = tsvd_anfragen_checked_candidate( 'tsvd_anfrage_candidate_approve_' );
	$anfrage   = tsvd_halter_get_anfrage( (int) $candidate['anfrage_id'] );
	$released  = $anfrage ? tsvd_anfragen_imap_release_candidate( $candidate ) : null;
	if ( ! $anfrage || ! tsvd_anfragen_imap_candidate_done( $released ) ) {
		tsvd_anfragen_candidate_redirect( 'failed' );
	}
	tsvd_anfragen_receive( $anfrage, '', $candidate['body'] );
	tsvd_anfragen_candidate_mark_decided( (int) $candidate['id'] );
	tsvd_anfragen_candidate_redirect( 'approved', (int) $anfrage['id'] );
}

function tsvd_anfragen_handle_candidate_reject() {
	$candidate = tsvd_anfragen_checked_candidate( 'tsvd_anfrage_candidate_reject_' );
	if ( ! tsvd_anfragen_imap_candidate_done( tsvd_anfragen_imap_dismiss_candidate( $candidate ) ) ) {
		tsvd_anfragen_candidate_redirect( 'failed' );
	}
	tsvd_anfragen_candidate_mark_decided( (int) $candidate['id'] );
	tsvd_anfragen_candidate_redirect( 'rejected' );
}
