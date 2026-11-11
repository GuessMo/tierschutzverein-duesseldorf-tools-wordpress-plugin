<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_at_rest_stats() {
	if ( ! function_exists( 'tsvd_stats_count_by' ) ) {
		return new WP_Error( 'tsvd_at_stats_missing', __( 'Die Statistik ist ohne das TSVD-Theme nicht verfügbar.', 'tsvd' ), array( 'status' => 501 ) );
	}
	return rest_ensure_response(
		array(
			'requests'      => tsvd_at_stats_requests(),
			'stock'         => tsvd_at_stats_stock(),
			'responseTimes' => tsvd_at_stats_response_times(),
		)
	);
}

function tsvd_at_stats_rows( array $rows, $label_cb = null ) {
	$out = array();
	foreach ( $rows as $row ) {
		$out[] = array(
			'label' => (string) ( $label_cb ? $label_cb( $row ) : $row['label'] ),
			'total' => (int) $row['total'],
		);
	}
	return $out;
}

function tsvd_at_stats_enum_rows( array $rows ) {
	return tsvd_at_stats_rows(
		$rows,
		function ( $row ) {
			return tsvd_stats_enum_label( $row['label'] );
		}
	);
}

function tsvd_at_stats_term_rows( $column ) {
	return tsvd_at_stats_rows(
		tsvd_stats_interest_by_term( $column ),
		function ( $row ) {
			return tsvd_stats_term_label( $row['term_id'] );
		}
	);
}

function tsvd_at_stats_months( $count ) {
	$values = array();
	foreach ( tsvd_stats_count_by_period( 'month' ) as $row ) {
		$values[ $row['label'] ] = (int) $row['total'];
	}
	$current = current_time( 'Y-m' );
	$rows    = array();
	for ( $offset = $count - 1; $offset >= 0; $offset-- ) {
		$month  = gmdate( 'Y-m', strtotime( $current . '-01 -' . $offset . ' month' ) );
		$rows[] = array(
			'label' => $month,
			'total' => $values[ $month ] ?? 0,
		);
	}
	return $rows;
}

function tsvd_at_stats_type_totals() {
	$totals = array();
	foreach ( tsvd_stats_count_by( 'request_type' ) as $row ) {
		$totals[ $row['label'] ] = (int) $row['total'];
	}
	return $totals;
}

function tsvd_at_stats_trend( array $months ) {
	$recent = array_slice( $months, -4 );
	$current = (int) ( end( $recent )['total'] ?? 0 );
	$before = array_slice( $recent, 0, 3 );
	$avg    = $before ? array_sum( array_column( $before, 'total' ) ) / count( $before ) : 0;
	return array(
		'thisMonth' => $current,
		'average'   => round( $avg, 1 ),
		'delta'     => $avg > 0 ? (int) round( ( $current - $avg ) / $avg * 100 ) : null,
	);
}

function tsvd_at_stats_listed_vs_requests() {
	$requests = array();
	foreach ( tsvd_stats_interest_by_term( 'species_term' ) as $row ) {
		$requests[ (int) $row['term_id'] ] = (int) $row['total'];
	}
	$out = array();
	foreach ( tsvd_stats_listed_by_species() as $row ) {
		$count = $requests[ (int) $row['term_id'] ] ?? 0;
		if ( 0 === (int) $row['listed'] && 0 === $count ) {
			continue;
		}
		$out[] = array(
			'label'    => (string) $row['name'],
			'listed'   => (int) $row['listed'],
			'requests' => $count,
		);
	}
	return $out;
}

function tsvd_at_stats_top_animals() {
	return tsvd_at_stats_rows(
		tsvd_stats_top_animals( 10 ),
		function ( $row ) {
			return get_the_title( (int) $row['animal_id'] ) ?: '#' . $row['animal_id'];
		}
	);
}

function tsvd_at_stats_requests() {
	$types  = tsvd_at_stats_type_totals();
	$months = tsvd_at_stats_months( 12 );
	return array(
		'total'      => array_sum( $types ),
		'trend'      => tsvd_at_stats_trend( $months ),
		'interest'   => $types['interest'] ?? 0,
		'private'    => $types['private_placement'] ?? 0,
		'missing'    => $types['missing'] ?? 0,
		'byType'     => tsvd_at_stats_enum_rows( tsvd_stats_count_by( 'request_type' ) ),
		'months'     => $months,
		'residence'  => tsvd_at_stats_enum_rows( tsvd_stats_count_by( 'residence' ) ),
		'housing'    => tsvd_at_stats_enum_rows( tsvd_stats_count_by( 'housing' ) ),
		'outdoor'    => tsvd_at_stats_enum_rows( tsvd_stats_count_by( 'outdoor' ) ),
		'species'    => tsvd_at_stats_term_rows( 'species_term' ),
		'breed'      => tsvd_at_stats_term_rows( 'breed_term' ),
		'topAnimals' => tsvd_at_stats_top_animals(),
		'listedVs'   => tsvd_at_stats_listed_vs_requests(),
	);
}

function tsvd_at_stats_stock() {
	$sources = tsvd_at_stats_rows(
		tsvd_stats_count_by_adoption_source(),
		function ( $row ) {
			return '' === $row['label'] ? __( 'Keine Angabe', 'tsvd' ) : tsvd_stats_enum_label( $row['label'] );
		}
	);
	return array(
		'byStatus' => tsvd_at_stats_enum_rows( tsvd_stats_count_by_adoption_status() ),
		'adopted'  => tsvd_at_stats_rows( array_reverse( tsvd_stats_adopted_by_month( 12 ) ) ),
		'sources'  => $sources,
	);
}
