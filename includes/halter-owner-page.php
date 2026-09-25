<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'template_redirect', 'tsvd_owner_page_route', 1 );

function tsvd_owner_actions( $case ) {
	if ( 'missing' === $case ) {
		return array(
			'reunited'      => __( 'Mein Tier ist wieder zuhause', 'tsv-tools' ),
			'still_missing' => __( 'Mein Tier wird noch vermisst', 'tsv-tools' ),
		);
	}
	return array(
		'adopted' => __( 'Mein Tier ist vermittelt', 'tsv-tools' ),
		'active'  => __( 'Mein Tier ist noch da, die Anzeige bleibt online', 'tsv-tools' ),
		'paused'  => __( 'Anzeige vorerst pausieren', 'tsv-tools' ),
	);
}

function tsvd_owner_page_route() {
	$path = rtrim( (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );
	if ( '/mein-tier' !== $path ) {
		return;
	}
	nocache_headers();
	header( 'X-Robots-Tag: noindex, nofollow' );
	$token     = isset( $_REQUEST['t'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['t'] ) ) : '';
	$animal_id = tsvd_halter_animal_by_token( $token );
	if ( $animal_id && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
		tsvd_owner_page_handle_post( $animal_id, $token );
	}
	status_header( $animal_id ? 200 : 404 );
	tsvd_owner_page_render( $animal_id, $token );
	exit;
}

function tsvd_owner_page_handle_post( $animal_id, $token ) {
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'tsvd_owner_' . $token ) ) {
		return;
	}
	$action = isset( $_POST['owner_action'] ) ? sanitize_key( $_POST['owner_action'] ) : '';
	$text   = isset( $_POST['owner_request'] ) ? trim( sanitize_textarea_field( wp_unslash( $_POST['owner_request'] ) ) ) : '';
	$GLOBALS['tsvd_halter_suppress'] = true;
	if ( '' !== $text ) {
		tsvd_halter_log( tsvd_halter_conversation_id( $animal_id ), 'in', __( 'Änderungswunsch über „Mein Tier“:', 'tsv-tools' ) . "\n" . $text, 'halter' );
	}
	if ( isset( tsvd_owner_actions( tsvd_halter_case( $animal_id ) )[ $action ] ) ) {
		tsvd_owner_apply_action( $animal_id, $action );
	}
	wp_safe_redirect( add_query_arg( array( 't' => $token, 'done' => 1 ), home_url( '/mein-tier/' ) ) );
	exit;
}

function tsvd_owner_apply_action( $animal_id, $action ) {
	$label = tsvd_owner_actions( tsvd_halter_case( $animal_id ) )[ $action ];
	$today = current_time( 'Y-m-d' );
	$map   = array(
		'adopted'       => array( 'animal_adoption_status' => 'adopted' ),
		'paused'        => array( 'animal_adoption_status' => 'not_for_adoption' ),
		'active'        => array( 'animal_adoption_status' => 'for_adoption' ),
		'reunited'      => array( 'animal_missing_status' => 'reunited', 'animal_missing_reunited_date' => $today ),
		'still_missing' => array(),
	);
	foreach ( $map[ $action ] as $key => $value ) {
		update_post_meta( $animal_id, $key, $value );
	}
	update_post_meta( $animal_id, '_tsvd_owner_confirmed_at', current_time( 'mysql' ) );
	tsvd_halter_log( tsvd_halter_conversation_id( $animal_id ), 'in', sprintf( __( 'Über „Mein Tier“ gemeldet: %s', 'tsv-tools' ), $label ), 'halter' );
	tsvd_halter_mail( $animal_id, __( 'Danke für Deine Rückmeldung', 'tsv-tools' ), sprintf( __( "Wir haben Deine Rückmeldung erhalten: %1\$s.\n\nDein Tier verwalten: %2\$s\n\nViele Grüße\n%3\$s", 'tsv-tools' ), $label, tsvd_halter_owner_url( $animal_id ), get_bloginfo( 'name' ) ) );
}

function tsvd_owner_status_label( $animal_id ) {
	if ( 'missing' === tsvd_halter_case( $animal_id ) ) {
		$labels = array( 'missing' => __( 'vermisst', 'tsv-tools' ), 'found' => __( 'gefunden', 'tsv-tools' ), 'reunited' => __( 'wieder zuhause', 'tsv-tools' ) );
		$status = get_post_meta( $animal_id, 'animal_missing_status', true );
	} else {
		$labels = array( 'for_adoption' => __( 'wird vermittelt', 'tsv-tools' ), 'adopted' => __( 'vermittelt', 'tsv-tools' ), 'not_for_adoption' => __( 'pausiert', 'tsv-tools' ) );
		$status = get_post_meta( $animal_id, 'animal_adoption_status', true );
	}
	$label = isset( $labels[ $status ] ) ? $labels[ $status ] : __( 'in Prüfung', 'tsv-tools' );
	return 'publish' === get_post_status( $animal_id ) ? $label : __( 'in Prüfung', 'tsv-tools' );
}

function tsvd_owner_page_title() {
	return __( 'Mein Tier', 'tsv-tools' ) . ' – ' . get_bloginfo( 'name' );
}

function tsvd_owner_page_render( $animal_id, $token ) {
	add_filter( 'pre_get_document_title', 'tsvd_owner_page_title' );
	get_header();
	echo '<h2 class="headline-1">' . esc_html__( 'Mein Tier', 'tsv-tools' ) . '</h2>';
	echo '<div class="card card-static corner owner-page">';
	if ( ! $animal_id ) {
		echo '<p>' . esc_html__( 'Dieser Link ist ungültig oder abgelaufen. Bitte antworte auf eine unserer E-Mails, dann helfen wir Dir weiter.', 'tsv-tools' ) . '</p></div>';
		get_footer();
		return;
	}
	tsvd_owner_page_summary( $animal_id );
	tsvd_owner_page_done_notice();
	tsvd_owner_page_form( $animal_id, $token );
	echo '</div>';
	get_footer();
}

function tsvd_owner_page_summary( $animal_id ) {
	echo '<div class="owner-page__head">';
	if ( has_post_thumbnail( $animal_id ) ) {
		echo get_the_post_thumbnail( $animal_id, 'thumbnail', array( 'class' => 'owner-page__image', 'alt' => '' ) );
	}
	echo '<div><h3 class="owner-page__name">' . esc_html( tsvd_halter_animal_name( $animal_id ) ) . '</h3>';
	echo '<p class="owner-page__status">' . esc_html__( 'Aktueller Stand:', 'tsv-tools' ) . ' <strong>' . esc_html( tsvd_owner_status_label( $animal_id ) ) . '</strong></p>';
	if ( 'publish' === get_post_status( $animal_id ) ) {
		echo '<a href="' . esc_url( get_permalink( $animal_id ) ) . '">' . esc_html__( 'Anzeige ansehen', 'tsv-tools' ) . '</a>';
	}
	echo '</div></div>';
}

function tsvd_owner_page_done_notice() {
	if ( empty( $_GET['done'] ) ) {
		return;
	}
	echo '<div class="info-box info-box--success" role="status"><div class="info-box__text"><p>' . esc_html__( 'Danke, wir haben Deine Rückmeldung erhalten.', 'tsv-tools' ) . '</p></div></div>';
}

function tsvd_owner_page_current_action( $animal_id ) {
	$map = array(
		'for_adoption'     => 'active',
		'not_for_adoption' => 'paused',
		'adopted'          => 'adopted',
		'missing'          => 'still_missing',
		'found'            => 'still_missing',
		'reunited'         => 'reunited',
	);
	$key    = 'missing' === tsvd_halter_case( $animal_id ) ? 'animal_missing_status' : 'animal_adoption_status';
	$status = (string) get_post_meta( $animal_id, $key, true );
	return isset( $map[ $status ] ) ? $map[ $status ] : '';
}

function tsvd_owner_page_preset( $animal_id, $actions ) {
	$preset = isset( $_GET['a'] ) ? sanitize_key( $_GET['a'] ) : '';
	return isset( $actions[ $preset ] ) ? $preset : tsvd_owner_page_current_action( $animal_id );
}

function tsvd_owner_page_form( $animal_id, $token ) {
	echo '<form method="post" class="form owner-page__form">';
	wp_nonce_field( 'tsvd_owner_' . $token );
	echo '<input type="hidden" name="t" value="' . esc_attr( $token ) . '" />';
	tsvd_owner_page_status_field( $animal_id );
	echo '<div class="form-field form-field-textarea"><div class="form-field-input-wrapper">';
	echo '<label for="owner-request">' . esc_html__( 'Möchtest Du etwas an der Anzeige ändern? (optional)', 'tsv-tools' ) . '</label>';
	echo '<textarea id="owner-request" name="owner_request" rows="4"></textarea></div></div>';
	echo '<div class="form-submit"><div class="form-submit-action"><button type="submit" class="button">' . esc_html__( 'Rückmeldung senden', 'tsv-tools' ) . '</button></div></div>';
	echo '</form>';
}

function tsvd_owner_page_status_field( $animal_id ) {
	$actions = tsvd_owner_actions( tsvd_halter_case( $animal_id ) );
	$checked = tsvd_owner_page_preset( $animal_id, $actions );
	echo '<div class="form-field form-field-radio"><fieldset class="form-radio-group">';
	echo '<legend>' . esc_html__( 'Wie ist der Stand?', 'tsv-tools' ) . '</legend>';
	foreach ( $actions as $key => $label ) {
		echo '<label class="form-radio-label"><input type="radio" name="owner_action" value="' . esc_attr( $key ) . '"' . checked( $checked, $key, false ) . ' /> ' . esc_html( $label ) . '</label>';
	}
	echo '</fieldset></div>';
}
