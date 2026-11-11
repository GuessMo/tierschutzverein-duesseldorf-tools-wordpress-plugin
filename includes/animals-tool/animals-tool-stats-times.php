<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_at_stats_response_times() {
	if ( ! function_exists( 'tsvd_anfragen_stats_durations' ) || ! current_user_can( 'manage_tsvd_anfragen' ) ) {
		return null;
	}
	$durations = tsvd_anfragen_stats_durations();
	$months    = tsvd_anfragen_stats_months( TSVD_ANFRAGEN_STATS_MONTHS );
	return array(
		'kpis'    => tsvd_at_stats_time_kpis( $durations ),
		'first'   => tsvd_at_stats_time_first( $durations, $months ),
		'compare' => tsvd_at_stats_time_compare( $durations, $months ),
	);
}

function tsvd_at_stats_time_kpi( array $entries, $label ) {
	$hours = tsvd_anfragen_stats_hours( $entries );
	return array(
		'label' => $label,
		'value' => tsvd_anfragen_stats_format_hours( tsvd_anfragen_stats_median( $hours ) ),
		'sub'   => sprintf(
			__( 'Ø %1$s · %2$d Antworten', 'tsvd' ),
			tsvd_anfragen_stats_format_hours( tsvd_anfragen_stats_average( $hours ) ),
			count( $hours )
		),
	);
}

function tsvd_at_stats_time_kpis( array $durations ) {
	$warn  = tsvd_anfragen_wait_thresholds()['warn'] / HOUR_IN_SECONDS;
	$share = tsvd_anfragen_stats_share_within( $durations['first'], $warn );
	return array(
		tsvd_at_stats_time_kpi( $durations['first'], __( 'Erste Antwort vom Verein (Median)', 'tsvd' ) ),
		tsvd_at_stats_time_kpi( $durations['verein'], __( 'Antwortzeit Verein (Median)', 'tsvd' ) ),
		tsvd_at_stats_time_kpi( $durations['person'], __( 'Antwortzeit Interessierte (Median)', 'tsvd' ) ),
		array(
			'label' => sprintf( __( 'Erstantworten innerhalb %d Std.', 'tsvd' ), $warn ),
			'value' => null === $share ? '–' : number_format_i18n( $share * 100 ) . ' %',
			'sub'   => __( 'Schwelle „gelb“ aus den Anfragen-Einstellungen', 'tsvd' ),
		),
	);
}

function tsvd_at_stats_time_first( array $durations, array $months ) {
	$rows = array();
	foreach ( tsvd_anfragen_stats_monthly_median( $durations['first'], $months ) as $month => $median ) {
		$rows[] = array(
			'label' => (string) $month,
			'total' => null === $median ? 0 : (int) round( $median ),
		);
	}
	return $rows;
}

function tsvd_at_stats_time_compare( array $durations, array $months ) {
	$verein = tsvd_anfragen_stats_monthly_median( $durations['verein'], $months );
	$person = tsvd_anfragen_stats_monthly_median( $durations['person'], $months );
	$rows   = array();
	foreach ( $months as $month ) {
		$rows[] = array(
			'label'  => (string) $month,
			'verein' => (int) round( (float) $verein[ $month ] ),
			'person' => (int) round( (float) $person[ $month ] ),
		);
	}
	return $rows;
}
