<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ANFRAGEN_STATS_MONTHS = 6;

add_action( 'tsvd_stats_dashboard_sections', 'tsvd_anfragen_stats_render_section' );

function tsvd_anfragen_stats_format_hours( $hours ) {
	if ( null === $hours ) {
		return '–';
	}
	if ( $hours < 1 ) {
		return sprintf( __( '%d Min.', 'tsvd' ), max( 1, (int) round( $hours * 60 ) ) );
	}
	if ( $hours < 48 ) {
		return sprintf( __( '%s Std.', 'tsvd' ), number_format_i18n( $hours, 1 ) );
	}
	return sprintf( __( '%s Tage', 'tsvd' ), number_format_i18n( $hours / 24, 1 ) );
}

function tsvd_anfragen_stats_kpi( $value, $label, $sub ) {
	echo '<div class="tsvd-kpi"><div class="tsvd-kpi__val">' . esc_html( $value ) . '</div>'
		. '<div class="tsvd-kpi__lbl">' . esc_html( $label ) . '</div>'
		. '<div class="tsvd-kpi__sub">' . esc_html( $sub ) . '</div></div>';
}

function tsvd_anfragen_stats_render_kpis( $durations ) {
	$warn_hours = tsvd_anfragen_wait_thresholds()['warn'] / HOUR_IN_SECONDS;
	$share      = tsvd_anfragen_stats_share_within( $durations['first'], $warn_hours );
	$items      = array(
		array( 'first', __( 'Erste Antwort vom Verein (Median)', 'tsvd' ) ),
		array( 'verein', __( 'Antwortzeit Verein (Median)', 'tsvd' ) ),
		array( 'person', __( 'Antwortzeit Interessierte (Median)', 'tsvd' ) ),
	);
	echo '<div class="tsvd-kpis">';
	foreach ( $items as $item ) {
		$hours = tsvd_anfragen_stats_hours( $durations[ $item[0] ] );
		tsvd_anfragen_stats_kpi(
			tsvd_anfragen_stats_format_hours( tsvd_anfragen_stats_median( $hours ) ),
			$item[1],
			sprintf( __( 'Ø %1$s · %2$d Antworten', 'tsvd' ), tsvd_anfragen_stats_format_hours( tsvd_anfragen_stats_average( $hours ) ), count( $hours ) )
		);
	}
	tsvd_anfragen_stats_kpi(
		null === $share ? '–' : number_format_i18n( $share * 100 ) . ' %',
		sprintf( __( 'Erstantworten innerhalb %d Std.', 'tsvd' ), $warn_hours ),
		__( 'Schwelle „gelb“ aus den Anfragen-Einstellungen', 'tsvd' )
	);
	echo '</div>';
}

function tsvd_anfragen_stats_empty_note() {
	return '<p class="tsvd-empty">' . esc_html__( 'Für einen Verlauf braucht es Antworten aus mindestens zwei Monaten.', 'tsvd' ) . '</p>';
}

function tsvd_anfragen_stats_first_line( $durations, $months ) {
	$medians = array_filter(
		tsvd_anfragen_stats_monthly_median( $durations['first'], $months ),
		function ( $median ) {
			return null !== $median;
		}
	);
	if ( count( $medians ) < 2 ) {
		return tsvd_anfragen_stats_empty_note();
	}
	$rows = array();
	foreach ( $medians as $month => $median ) {
		$rows[] = array(
			'label' => $month,
			'total' => (int) round( $median ),
		);
	}
	return tsvd_stats_line(
		$rows,
		function ( $row ) {
			return $row['label'];
		}
	);
}

function tsvd_anfragen_stats_compare_bars( $durations, $months ) {
	$verein = tsvd_anfragen_stats_monthly_median( $durations['verein'], $months );
	$person = tsvd_anfragen_stats_monthly_median( $durations['person'], $months );
	$groups = array();
	foreach ( $months as $month ) {
		if ( null === $verein[ $month ] && null === $person[ $month ] ) {
			continue;
		}
		$groups[] = array(
			'name' => $month,
			'a'    => (int) round( (float) $verein[ $month ] ),
			'b'    => (int) round( (float) $person[ $month ] ),
		);
	}
	return tsvd_stats_grouped_bars( $groups, array( __( 'Verein (Std.)', 'tsvd' ), __( 'Interessierte (Std.)', 'tsvd' ) ) );
}

function tsvd_anfragen_stats_render_section() {
	if ( ! function_exists( 'tsvd_stats_card' ) || ! function_exists( 'tsvd_stats_grouped_bars' ) ) {
		return;
	}
	$durations = tsvd_anfragen_stats_durations();
	$months    = tsvd_anfragen_stats_months( TSVD_ANFRAGEN_STATS_MONTHS );

	echo '<h2>' . esc_html__( 'Auswertung: Antwortzeiten', 'tsvd' ) . '</h2>';
	echo '<p class="tsvd-dash-hint">' . esc_html__( 'Zeit zwischen Nachrichten in den Anfragen. Interne Notizen, Spam, Blockierte und Papierkorb zählen nicht. Median = typischer Wert, Ø = Durchschnitt.', 'tsvd' ) . '</p>';
	tsvd_anfragen_stats_render_kpis( $durations );
	echo '<div class="tsvd-grid">';
	tsvd_stats_card( __( 'Erste Antwort pro Monat (Median, Stunden)', 'tsvd' ), tsvd_anfragen_stats_first_line( $durations, $months ), true );
	tsvd_stats_card( __( 'Antwortzeit pro Monat: Verein vs. Interessierte (Median, Stunden)', 'tsvd' ), tsvd_anfragen_stats_compare_bars( $durations, $months ), true );
	echo '</div>';
}
