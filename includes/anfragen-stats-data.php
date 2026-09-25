<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_stats_timelines() {
	global $wpdb;
	$table   = tsvd_anfragen_table_name();
	$replies = tsvd_anfragen_replies_table_name();
	$rows    = $wpdb->get_results(
		"SELECT a.id, a.created_at, r.direction, r.sent_at
		 FROM {$table} a
		 LEFT JOIN {$replies} r ON r.anfrage_id = a.id AND " . tsvd_anfragen_counted_reply_sql( 'r', 'a' ) . " AND r.sent_at IS NOT NULL
		 WHERE a.deleted_at IS NULL AND a.status NOT IN ( 'spam', 'blocked' ) AND " . tsvd_anfragen_messenger_sql( 'a' ) . "
		 ORDER BY a.id ASC, r.sent_at ASC, r.id ASC",
		ARRAY_A
	);
	$timelines = array();
	foreach ( $rows as $row ) {
		$id = (int) $row['id'];
		if ( ! isset( $timelines[ $id ] ) ) {
			$timelines[ $id ] = array( array( 'in', $row['created_at'] ) );
		}
		if ( $row['direction'] ) {
			$timelines[ $id ][] = array( $row['direction'], $row['sent_at'] );
		}
	}
	return $timelines;
}

function tsvd_anfragen_stats_turns( $events ) {
	$turns = array();
	$start = null;
	$side  = null;
	foreach ( $events as $event ) {
		list( $direction, $stamp ) = $event;
		if ( null === $side ) {
			$side  = $direction;
			$start = $stamp;
			continue;
		}
		if ( $direction === $side ) {
			continue;
		}
		$turns[] = array(
			'responder' => 'in' === $side ? 'verein' : 'person',
			'start'     => $start,
			'hours'     => max( 0, strtotime( $stamp ) - strtotime( $start ) ) / HOUR_IN_SECONDS,
		);
		$side  = $direction;
		$start = $stamp;
	}
	return $turns;
}

function tsvd_anfragen_stats_durations() {
	$result = array(
		'first'  => array(),
		'verein' => array(),
		'person' => array(),
	);
	foreach ( tsvd_anfragen_stats_timelines() as $events ) {
		$turns = tsvd_anfragen_stats_turns( $events );
		foreach ( $turns as $index => $turn ) {
			$entry = array(
				'month' => substr( $turn['start'], 0, 7 ),
				'hours' => $turn['hours'],
			);
			$result[ $turn['responder'] ][] = $entry;
			if ( 0 === $index ) {
				$result['first'][] = $entry;
			}
		}
	}
	return $result;
}

function tsvd_anfragen_stats_median( $values ) {
	if ( ! $values ) {
		return null;
	}
	sort( $values );
	$count  = count( $values );
	$middle = (int) floor( $count / 2 );
	return 0 === $count % 2 ? ( $values[ $middle - 1 ] + $values[ $middle ] ) / 2 : $values[ $middle ];
}

function tsvd_anfragen_stats_average( $values ) {
	return $values ? array_sum( $values ) / count( $values ) : null;
}

function tsvd_anfragen_stats_hours( $entries ) {
	return wp_list_pluck( $entries, 'hours' );
}

function tsvd_anfragen_stats_months( $count ) {
	$months = array();
	for ( $offset = $count - 1; $offset >= 0; $offset-- ) {
		$months[] = gmdate( 'Y-m', strtotime( current_time( 'Y-m' ) . '-01 -' . $offset . ' month' ) );
	}
	return $months;
}

function tsvd_anfragen_stats_monthly_median( $entries, $months ) {
	$by_month = array_fill_keys( $months, array() );
	foreach ( $entries as $entry ) {
		if ( isset( $by_month[ $entry['month'] ] ) ) {
			$by_month[ $entry['month'] ][] = $entry['hours'];
		}
	}
	return array_map( 'tsvd_anfragen_stats_median', $by_month );
}

function tsvd_anfragen_stats_share_within( $entries, $hours ) {
	if ( ! $entries ) {
		return null;
	}
	$within = array_filter(
		tsvd_anfragen_stats_hours( $entries ),
		function ( $value ) use ( $hours ) {
			return $value <= $hours;
		}
	);
	return count( $within ) / count( $entries );
}
