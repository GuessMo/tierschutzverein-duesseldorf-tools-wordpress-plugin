<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'tsvd_anfragen_after_animal_card', 'tsvd_owner_render_pending_approval' );
add_action( 'admin_post_tsvd_owner_approve', 'tsvd_owner_handle_approve' );

function tsvd_owner_needs_approval( $animal_id, $action ) {
	return 'active' === $action && 'for_adoption' !== get_post_meta( $animal_id, 'animal_adoption_status', true );
}

function tsvd_owner_pending_action( $animal_id ) {
	return (string) get_post_meta( $animal_id, '_tsvd_owner_pending_action', true );
}

function tsvd_owner_request_approval( $animal_id, $action ) {
	update_post_meta( $animal_id, '_tsvd_owner_pending_action', $action );
	update_post_meta( $animal_id, '_tsvd_owner_confirmed_at', current_time( 'mysql' ) );
	tsvd_halter_log( tsvd_halter_conversation_id( $animal_id ), 'in', __( 'Über „Mein Tier“ angefragt: Die Anzeige soll wieder online gehen. Bitte prüfen und freigeben.', 'tsv-tools' ), 'halter' );
	tsvd_halter_mail( $animal_id, __( 'Danke für Deine Rückmeldung', 'tsv-tools' ), sprintf( __( "Du möchtest Deine Anzeige wieder zeigen. Wir prüfen sie kurz und schalten sie dann frei.\n\nDein Tier verwalten: %1\$s\n\nViele Grüße\n%2\$s", 'tsv-tools' ), tsvd_halter_owner_url( $animal_id ), get_bloginfo( 'name' ) ) );
}

function tsvd_owner_render_pending_approval( $anfrage ) {
	$animal_id = (int) $anfrage['animal_id'];
	if ( 'halter' !== $anfrage['kind'] || 'active' !== tsvd_owner_pending_action( $animal_id ) ) {
		return;
	}
	echo '<div class="tsvd-panel tsvd-panel--note"><p>' . esc_html__( 'Der Halter möchte die Anzeige wieder online stellen.', 'tsv-tools' ) . '</p>';
	tsvd_anfragen_action_form( (int) $anfrage['id'], 'tsvd_owner_approve', 'tsvd_owner_approve_', __( 'Anzeige wieder freigeben', 'tsv-tools' ), 'dashicons-yes', 'tsvd-anf-btn--primary' );
	echo '</div>';
}

function tsvd_owner_handle_approve() {
	$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
	if ( ! current_user_can( 'manage_tsvd_anfragen' ) || ! check_admin_referer( 'tsvd_owner_approve_' . $id ) ) {
		wp_die( esc_html__( 'Keine Berechtigung.', 'tsv-tools' ) );
	}
	$anfrage   = tsvd_halter_get_anfrage( $id );
	$animal_id = $anfrage ? (int) $anfrage['animal_id'] : 0;
	if ( $animal_id && 'active' === tsvd_owner_pending_action( $animal_id ) ) {
		delete_post_meta( $animal_id, '_tsvd_owner_pending_action' );
		update_post_meta( $animal_id, 'animal_adoption_status', 'for_adoption' );
		tsvd_halter_log( $id, 'out', __( 'Anzeige wieder freigegeben.', 'tsv-tools' ), 'halter', get_current_user_id() );
		tsvd_halter_mail( $animal_id, sprintf( __( '%s ist wieder online', 'tsv-tools' ), tsvd_halter_animal_name( $animal_id ) ), sprintf( __( "Deine Anzeige ist wieder online:\n%1\$s\n\nViele Grüße\n%2\$s", 'tsv-tools' ), get_permalink( $animal_id ), get_bloginfo( 'name' ) ) );
	}
	wp_safe_redirect( wp_get_referer() ?: admin_url() );
	exit;
}
