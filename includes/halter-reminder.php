<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_HALTER_REMINDER_DAYS = 28;

add_action( 'init', 'tsvd_halter_schedule_reminders' );
add_action( 'tsvd_halter_send_reminders', 'tsvd_halter_send_reminders' );

function tsvd_halter_schedule_reminders() {
	if ( ! wp_next_scheduled( 'tsvd_halter_send_reminders' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'tsvd_halter_send_reminders' );
	}
}

function tsvd_halter_reminder_candidates() {
	return get_posts( array(
		'post_type'      => 'animals',
		'post_status'    => 'publish',
		'posts_per_page' => 50,
		'fields'         => 'ids',
		'meta_query'     => array(
			'relation' => 'AND',
			array( 'key' => '_tsvd_owner_token', 'compare' => 'EXISTS' ),
			array(
				'relation' => 'OR',
				array(
					'relation' => 'AND',
					array( 'key' => 'animal_mediator', 'value' => 'privat' ),
					array( 'key' => 'animal_adoption_status', 'value' => 'for_adoption' ),
				),
				array( 'key' => 'animal_missing_status', 'value' => 'missing' ),
			),
		),
	) );
}

function tsvd_halter_last_contact( $animal_id ) {
	$stamps = array(
		(string) get_post_meta( $animal_id, '_tsvd_owner_confirmed_at', true ),
		(string) get_post_meta( $animal_id, '_tsvd_owner_reminded_at', true ),
		get_post_field( 'post_date', $animal_id ),
	);
	return max( array_map( 'strtotime', array_filter( $stamps ) ) );
}

function tsvd_halter_reminder_due( $animal_id, $now ) {
	return ( $now - tsvd_halter_last_contact( $animal_id ) ) >= TSVD_HALTER_REMINDER_DAYS * DAY_IN_SECONDS;
}

function tsvd_halter_send_reminders() {
	$now = current_time( 'timestamp' );
	foreach ( tsvd_halter_reminder_candidates() as $animal_id ) {
		if ( ! tsvd_halter_reminder_due( $animal_id, $now ) ) {
			continue;
		}
		if ( tsvd_halter_notify( $animal_id, 'reminder' ) ) {
			update_post_meta( $animal_id, '_tsvd_owner_reminded_at', current_time( 'mysql' ) );
		}
	}
}
