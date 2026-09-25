<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_halter_responsible_options() {
	return array(
		'tsvd_anfragen_responsible_private' => __( 'Zuständig für private Vermittlung', 'tsv-tools' ),
		'tsvd_anfragen_responsible_missing' => __( 'Zuständig für vermisste Tiere', 'tsv-tools' ),
	);
}

function tsvd_halter_responsible_user( $animal_id ) {
	$case = function_exists( 'tsvd_halter_case' ) ? tsvd_halter_case( (int) $animal_id ) : '';
	if ( '' === $case ) {
		return null;
	}
	$user_id = (int) get_option( 'missing' === $case ? 'tsvd_anfragen_responsible_missing' : 'tsvd_anfragen_responsible_private', 0 );
	return $user_id && user_can( $user_id, 'manage_tsvd_anfragen' ) ? $user_id : null;
}

function tsvd_halter_save_responsible_settings() {
	foreach ( array_keys( tsvd_halter_responsible_options() ) as $option ) {
		update_option( $option, absint( $_POST[ $option ] ?? 0 ) );
	}
}

function tsvd_halter_render_responsible_rows() {
	$users = get_users( array( 'capability' => 'manage_tsvd_anfragen', 'orderby' => 'display_name', 'fields' => array( 'ID', 'display_name' ) ) );
	foreach ( tsvd_halter_responsible_options() as $option => $label ) {
		$current = (int) get_option( $option, 0 );
		echo '<tr><th><label for="' . esc_attr( $option ) . '">' . esc_html( $label ) . '</label></th><td>';
		echo '<select id="' . esc_attr( $option ) . '" name="' . esc_attr( $option ) . '"><option value="0">' . esc_html__( 'Niemand (manuell zuweisen)', 'tsv-tools' ) . '</option>';
		foreach ( $users as $user ) {
			echo '<option value="' . esc_attr( $user->ID ) . '"' . selected( $current, (int) $user->ID, false ) . '>' . esc_html( $user->display_name ) . '</option>';
		}
		echo '</select><p class="description">' . esc_html__( 'Neue Meldungen, Anfragen und Sichtungen zu diesen Tieren werden automatisch zugewiesen.', 'tsv-tools' ) . '</p></td></tr>';
	}
}
