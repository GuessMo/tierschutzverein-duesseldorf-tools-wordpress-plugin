<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ANFRAGEN_STAMP_FORMAT = 'd.m.Y, H:i';

function tsvd_anfragen_format_local( $mysql, $format = TSVD_ANFRAGEN_STAMP_FORMAT ) {
	if ( empty( $mysql ) ) {
		return '';
	}
	return mysql2date( $format, $mysql );
}

function tsvd_anfragen_animal_name( $animal_id ) {
	if ( ! $animal_id ) {
		return '';
	}
	$name = get_post_meta( $animal_id, 'animal_name', true );
	return '' !== $name ? $name : get_the_title( $animal_id );
}

function tsvd_anfragen_user_label( $user_id ) {
	$user = $user_id ? get_userdata( $user_id ) : false;
	if ( ! $user ) {
		return __( 'Unbekannt', 'tsvd' );
	}
	$full = trim( $user->first_name . ' ' . $user->last_name );
	return '' !== $full ? $full : $user->display_name;
}

function tsvd_anfragen_first_name( $full_name ) {
	$parts = preg_split( '/\s+/', trim( (string) $full_name ) );
	return $parts && '' !== $parts[0] ? $parts[0] : __( 'die interessierte Person', 'tsvd' );
}

function tsvd_anfragen_count( $status, $search, $breed ) {
	global $wpdb;
	$table = tsvd_anfragen_table_name();
	$where = tsvd_anfragen_list_where( $status, $search, $breed );
	return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} a {$where}" );
}

function tsvd_anfragen_oldest_open_id() {
	global $wpdb;
	$table = tsvd_anfragen_table_name();
	$where = tsvd_anfragen_list_where( 'open', '', 0 );
	return (int) $wpdb->get_var( "SELECT a.id FROM {$table} a {$where} ORDER BY a.created_at ASC LIMIT 1" );
}

function tsvd_anfragen_filter_url( $args ) {
	$args = array_filter(
		$args,
		function ( $value ) {
			return null !== $value;
		}
	);
	return add_query_arg( $args, tsvd_anfragen_list_base_url() );
}

function tsvd_anfragen_reset_url() {
	return tsvd_anfragen_filter_url(
		array(
			'status' => '',
			'breed'  => 0,
		)
	);
}

function tsvd_anfragen_render_main_empty() {
	$open = tsvd_anfragen_count( 'open', '', 0 );
	echo '<div class="tsvd-msgr__empty">';
	echo '<p class="tsvd-msgr__empty-title">' . esc_html__( 'Wähle eine Konversation aus der Liste.', 'tsvd' ) . '</p>';
	if ( ! $open ) {
		echo '<p>' . esc_html__( 'Alle Anfragen sind beantwortet.', 'tsvd' ) . '</p></div>';
		return;
	}
	$label = sprintf( _n( '%d offene Anfrage wartet auf eine Antwort.', '%d offene Anfragen warten auf eine Antwort.', $open, 'tsvd' ), $open );
	$url   = tsvd_anfragen_filter_url(
		array(
			'status' => '',
			'breed'  => 0,
			'view'   => tsvd_anfragen_oldest_open_id(),
		)
	);
	echo '<p>' . esc_html( $label ) . '</p>';
	echo '<p><a class="tsvd-anf-btn tsvd-anf-btn--primary" href="' . esc_url( $url ) . '">' . esc_html__( 'Älteste offene Anfrage öffnen', 'tsvd' ) . '</a></p>';
	echo '</div>';
}
