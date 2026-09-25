<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ANFRAGEN_WAIT_WARN_DEFAULT  = 48;
const TSVD_ANFRAGEN_WAIT_ALERT_DEFAULT = 96;

function tsvd_anfragen_wait_thresholds() {
	$warn  = max( 1, (int) get_option( 'tsvd_anfragen_wait_warn_hours', TSVD_ANFRAGEN_WAIT_WARN_DEFAULT ) );
	$alert = max( $warn, (int) get_option( 'tsvd_anfragen_wait_alert_hours', TSVD_ANFRAGEN_WAIT_ALERT_DEFAULT ) );
	return array(
		'warn'  => $warn * HOUR_IN_SECONDS,
		'alert' => $alert * HOUR_IN_SECONDS,
	);
}

function tsvd_anfragen_counted_reply_sql( $reply_alias, $anfrage_alias ) {
	return "( ( {$reply_alias}.direction = 'in' AND NOT ( {$reply_alias}.party IS NOT NULL AND {$anfrage_alias}.kind IN ( 'inquiry', 'sighting' ) ) )
		OR ( {$reply_alias}.direction = 'out' AND NOT ( {$reply_alias}.user_id IS NULL AND {$anfrage_alias}.kind = 'halter' ) ) )";
}

function tsvd_anfragen_waiting_since_sql( $alias = 'a' ) {
	$replies = tsvd_anfragen_replies_table_name();
	return "COALESCE(
		(SELECT MIN(i.sent_at) FROM {$replies} i
		 WHERE i.anfrage_id = {$alias}.id AND i.direction = 'in' AND " . tsvd_anfragen_counted_reply_sql( 'i', $alias ) . "
		 AND i.sent_at > COALESCE(
			(SELECT MAX(o.sent_at) FROM {$replies} o WHERE o.anfrage_id = {$alias}.id AND o.direction = 'out' AND " . tsvd_anfragen_counted_reply_sql( 'o', $alias ) . "),
			'1970-01-01 00:00:00')),
		{$alias}.created_at)";
}

function tsvd_anfragen_last_activity_sql( $alias = 'a' ) {
	$replies = tsvd_anfragen_replies_table_name();
	return "COALESCE(
		(SELECT MAX(l.sent_at) FROM {$replies} l
		 WHERE l.anfrage_id = {$alias}.id AND l.direction IN ( 'in', 'out' ) AND l.sent_at IS NOT NULL),
		{$alias}.created_at)";
}

function tsvd_anfragen_wait_seconds( $since_local ) {
	return max( 0, current_time( 'timestamp' ) - strtotime( $since_local ) );
}

function tsvd_anfragen_wait_level( $seconds ) {
	$limits = tsvd_anfragen_wait_thresholds();
	if ( $seconds >= $limits['alert'] ) {
		return 'alert';
	}
	return $seconds >= $limits['warn'] ? 'warn' : '';
}

function tsvd_anfragen_wait_label( $seconds ) {
	if ( $seconds < HOUR_IN_SECONDS ) {
		return sprintf( __( 'seit %d Min.', 'tsvd' ), max( 1, (int) floor( $seconds / MINUTE_IN_SECONDS ) ) );
	}
	if ( $seconds < DAY_IN_SECONDS ) {
		return sprintf( __( 'seit %d Std.', 'tsvd' ), (int) floor( $seconds / HOUR_IN_SECONDS ) );
	}
	$days = (int) floor( $seconds / DAY_IN_SECONDS );
	return sprintf( _n( 'seit %d Tag', 'seit %d Tagen', $days, 'tsvd' ), $days );
}

function tsvd_anfragen_render_wait_time( $since_local ) {
	$seconds = tsvd_anfragen_wait_seconds( $since_local );
	$level   = tsvd_anfragen_wait_level( $seconds );
	$class   = 'tsvd-msgr__time tsvd-msgr__wait' . ( $level ? ' tsvd-msgr__wait--' . $level : '' );
	$title   = sprintf( __( 'Wartet auf Antwort seit %s', 'tsvd' ), tsvd_anfragen_format_local( $since_local ) );

	echo '<span class="' . esc_attr( $class ) . '" title="' . esc_attr( $title ) . '">';
	if ( $level ) {
		echo '<span class="dashicons dashicons-clock" aria-hidden="true"></span>';
	}
	echo '<span class="screen-reader-text">' . esc_html__( 'Wartet', 'tsvd' ) . ' </span>' . esc_html( tsvd_anfragen_wait_label( $seconds ) ) . '</span>';
}

function tsvd_anfragen_save_wait_settings() {
	$warn  = max( 1, absint( $_POST['tsvd_anfragen_wait_warn_hours'] ?? TSVD_ANFRAGEN_WAIT_WARN_DEFAULT ) );
	$alert = max( $warn, absint( $_POST['tsvd_anfragen_wait_alert_hours'] ?? TSVD_ANFRAGEN_WAIT_ALERT_DEFAULT ) );
	update_option( 'tsvd_anfragen_wait_warn_hours', $warn );
	update_option( 'tsvd_anfragen_wait_alert_hours', $alert );
}

function tsvd_anfragen_render_wait_settings_rows() {
	$rows = array(
		'tsvd_anfragen_wait_warn_hours'  => array( __( 'Wartezeit gelb ab (Stunden)', 'tsv-tools' ), TSVD_ANFRAGEN_WAIT_WARN_DEFAULT, __( 'Offene Anfragen, die länger ohne Antwort sind, werden in der Liste gelb markiert. Standard: 48.', 'tsv-tools' ) ),
		'tsvd_anfragen_wait_alert_hours' => array( __( 'Wartezeit rot ab (Stunden)', 'tsv-tools' ), TSVD_ANFRAGEN_WAIT_ALERT_DEFAULT, __( 'Ab dieser Wartezeit wird die Anfrage rot markiert. Standard: 96 (4 Tage).', 'tsv-tools' ) ),
	);
	foreach ( $rows as $name => $row ) {
		echo '<tr><th><label for="' . esc_attr( $name ) . '">' . esc_html( $row[0] ) . '</label></th><td>';
		echo '<input type="number" min="1" step="1" id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '" value="'
			. esc_attr( (int) get_option( $name, $row[1] ) ) . '" />';
		echo '<p class="description">' . esc_html( $row[2] ) . '</p></td></tr>';
	}
}
