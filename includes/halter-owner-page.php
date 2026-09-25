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
		'active'  => __( 'Mein Tier ist noch da, Anzeige weiter zeigen', 'tsv-tools' ),
		'paused'  => __( 'Anzeige pausieren', 'tsv-tools' ),
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
	$GLOBALS['tsvd_halter_suppress'] = true;
	if ( 'request' === $action ) {
		$text = isset( $_POST['owner_request'] ) ? sanitize_textarea_field( wp_unslash( $_POST['owner_request'] ) ) : '';
		if ( '' !== trim( $text ) ) {
			tsvd_halter_log( tsvd_halter_conversation_id( $animal_id ), 'in', __( 'Änderungswunsch über „Mein Tier“:', 'tsv-tools' ) . "\n" . $text, 'halter' );
		}
	} elseif ( isset( tsvd_owner_actions( tsvd_halter_case( $animal_id ) )[ $action ] ) ) {
		tsvd_owner_apply_action( $animal_id, $action );
	}
	wp_safe_redirect( add_query_arg( array( 't' => $token, 'done' => $action ), home_url( '/mein-tier/' ) ) );
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

function tsvd_owner_page_render( $animal_id, $token ) {
	get_header();
	echo '<h2 class="headline-1">' . esc_html__( 'Mein Tier', 'tsv-tools' ) . '</h2>';
	echo '<div class="card card-full corner">';
	if ( ! $animal_id ) {
		echo '<p>' . esc_html__( 'Dieser Link ist ungültig oder abgelaufen. Bitte antworte auf eine unserer E-Mails, dann helfen wir Dir weiter.', 'tsv-tools' ) . '</p></div>';
		get_footer();
		return;
	}
	tsvd_owner_page_summary( $animal_id );
	tsvd_owner_page_actions( $animal_id, $token );
	tsvd_owner_page_request_form( $token );
	echo '</div>';
	get_footer();
}

function tsvd_owner_page_summary( $animal_id ) {
	$done    = isset( $_GET['done'] ) ? sanitize_key( $_GET['done'] ) : '';
	$actions = tsvd_owner_actions( tsvd_halter_case( $animal_id ) );
	echo '<h3>' . esc_html( tsvd_halter_animal_name( $animal_id ) ) . '</h3>';
	echo '<p>' . esc_html( sprintf( __( 'Aktueller Stand: %s', 'tsv-tools' ), tsvd_owner_status_label( $animal_id ) ) ) . '</p>';
	if ( isset( $actions[ $done ] ) || 'request' === $done ) {
		echo '<p role="status"><strong>' . esc_html__( 'Danke, wir haben Deine Rückmeldung erhalten.', 'tsv-tools' ) . '</strong></p>';
	}
}

function tsvd_owner_page_actions( $animal_id, $token ) {
	$preset = isset( $_GET['a'] ) ? sanitize_key( $_GET['a'] ) : '';
	foreach ( tsvd_owner_actions( tsvd_halter_case( $animal_id ) ) as $key => $label ) {
		$class = $key === $preset ? 'button' : 'button-text';
		echo '<form method="post" class="mb-24">';
		wp_nonce_field( 'tsvd_owner_' . $token );
		echo '<input type="hidden" name="t" value="' . esc_attr( $token ) . '" />';
		echo '<input type="hidden" name="owner_action" value="' . esc_attr( $key ) . '" />';
		echo '<button type="submit" class="' . esc_attr( $class ) . '">' . esc_html( $label ) . '</button>';
		echo '</form>';
	}
}

function tsvd_owner_page_request_form( $token ) {
	echo '<form method="post" class="form">';
	wp_nonce_field( 'tsvd_owner_' . $token );
	echo '<input type="hidden" name="t" value="' . esc_attr( $token ) . '" />';
	echo '<input type="hidden" name="owner_action" value="request" />';
	echo '<div class="form-field"><label for="owner-request">' . esc_html__( 'Etwas an der Anzeige ändern? Schreib uns, was angepasst werden soll.', 'tsv-tools' ) . '</label>';
	echo '<textarea id="owner-request" name="owner_request" class="form-field-input" rows="4"></textarea></div>';
	echo '<button type="submit" class="button">' . esc_html__( 'Änderungswunsch senden', 'tsv-tools' ) . '</button>';
	echo '</form>';
}
