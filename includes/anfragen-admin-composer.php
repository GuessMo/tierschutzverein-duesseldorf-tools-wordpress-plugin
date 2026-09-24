<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_composer_texts( $anfrage ) {
	$first = tsvd_anfragen_first_name( $anfrage['applicant_name'] );
	$email = $anfrage['applicant_email'];
	$delay = (int) ceil( tsvd_anfragen_send_delay() / 60 );
	$hint  = $delay > 0
		? sprintf( _n( 'Geht per E-Mail an %1$s, Signatur wird angehängt. Versand nach %2$d Minute, bis dahin kannst Du abbrechen.', 'Geht per E-Mail an %1$s, Signatur wird angehängt. Versand nach %2$d Minuten, bis dahin kannst Du abbrechen.', $delay, 'tsvd' ), $email, $delay )
		: sprintf( __( 'Geht sofort per E-Mail an %s, Signatur wird angehängt.', 'tsvd' ), $email );
	return array(
		'reply' => array(
			'tab'    => sprintf( __( 'Antwort an %s', 'tsvd' ), $first ),
			'label'  => sprintf( __( 'Antwort an %s', 'tsvd' ), $anfrage['applicant_name'] ),
			'button' => sprintf( __( 'An %s senden', 'tsvd' ), $first ),
			'hint'   => $hint,
		),
		'note'  => array(
			'tab'    => __( 'Interne Notiz', 'tsvd' ),
			'label'  => __( 'Interne Notiz (nur im System sichtbar)', 'tsvd' ),
			'button' => __( 'Notiz speichern', 'tsvd' ),
			'hint'   => __( 'Nur für das Team sichtbar. Geht nicht an die interessierte Person.', 'tsvd' ),
		),
	);
}

function tsvd_anfragen_render_reply_form( $anfrage ) {
	$id    = (int) $anfrage['id'];
	$texts = tsvd_anfragen_composer_texts( $anfrage );
	$nonce = wp_create_nonce( 'tsvd_anfrage_reply_' . $id );

	echo '<section class="tsvd-composer" id="tsvd-composer" data-mode="reply">';
	echo '<h2>' . esc_html__( 'Nachricht', 'tsvd' );
	tsvd_anfragen_help_btn( __( 'Wähle oben, ob Du der interessierten Person antwortest oder eine interne Notiz fürs Team schreibst. Antworten gehen per E-Mail raus, Notizen bleiben im System. Mit Strg+Enter (Mac: Cmd+Enter) sendest Du direkt aus dem Textfeld.', 'tsvd' ) );
	echo '</h2>';
	echo '<div class="tsvd-composer__modes" role="group" aria-label="' . esc_attr__( 'Art der Nachricht', 'tsvd' ) . '">';
	foreach ( $texts as $mode => $text ) {
		$pressed = 'reply' === $mode ? 'true' : 'false';
		echo '<button type="button" class="tsvd-anf-btn tsvd-composer__mode" data-mode="' . esc_attr( $mode ) . '" aria-pressed="' . esc_attr( $pressed ) . '">'
			. '<span class="dashicons ' . ( 'reply' === $mode ? 'dashicons-email-alt' : 'dashicons-lock' ) . '" aria-hidden="true"></span>'
			. esc_html( $text['tab'] ) . '</button>';
	}
	echo '</div>';
	echo '<label class="tsvd-composer__label" for="tsvd-anfrage-reply-body" id="tsvd-anfrage-label">' . esc_html( $texts['reply']['label'] ) . '</label>';
	echo '<textarea id="tsvd-anfrage-reply-body" class="widefat tsvd-composer__input" rows="6" aria-describedby="tsvd-anfrage-hint"></textarea>';
	echo '<p class="tsvd-composer__hint" id="tsvd-anfrage-hint">' . esc_html( $texts['reply']['hint'] ) . '</p>';
	echo '<p class="tsvd-composer__bar">';
	echo '<button type="button" class="tsvd-anf-btn tsvd-anf-btn--primary" id="tsvd-anfrage-reply-send" data-id="' . esc_attr( $id ) . '" data-nonce="' . esc_attr( $nonce ) . '">'
		. esc_html( $texts['reply']['button'] ) . '</button>';
	echo '<span id="tsvd-anfrage-reply-result" class="tsvd-composer__result" role="status" aria-live="polite"></span>';
	echo '</p></section>';
	tsvd_anfragen_composer_script( $texts );
}

function tsvd_anfragen_composer_script( $texts ) {
	$strings = array(
		'texts'   => $texts,
		'sending' => __( 'Wird gespeichert …', 'tsvd' ),
		'error'   => __( 'Fehler', 'tsvd' ),
		'server'  => __( 'Serverfehler. Bitte versuche es erneut.', 'tsvd' ),
	);
	wp_enqueue_script( 'tsvd-anfragen-composer', TSVD_TOOLS_URL . 'assets/anfragen-composer.js', array( 'jquery' ), TSVD_TOOLS_ASSET_VERSION, true );
	wp_localize_script( 'tsvd-anfragen-composer', 'tsvdComposer', $strings );
}
