<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_AT_OPTION_SET_KEYS = array(
	'life_stage',
	'gender',
	'birthday_status',
	'ternary',
	'size',
);

const TSVD_AT_NOTICE_TONES = array(
	'notice-success' => 'success',
	'notice-warning' => 'warning',
	'notice-error'   => 'danger',
	'notice-info'    => 'info',
);

function tsvd_at_option_sets() {
	$exists = function_exists( 'tsvd_get_animal_option_sets' );
	$sets   = $exists ? tsvd_get_animal_option_sets() : array();
	$result = array();
	foreach ( TSVD_AT_OPTION_SET_KEYS as $key ) {
		$result[ $key ] = array_values( $sets[ $key ]['options'] ?? array() );
	}
	return $result;
}

function tsvd_at_tone( $notice_class ) {
	return TSVD_AT_NOTICE_TONES[ $notice_class ] ?? 'info';
}

function tsvd_at_adoption_statuses() {
	if ( ! function_exists( 'tsvd_get_animal_adoption_status_options' ) ) {
		return array();
	}
	$statuses = array();
	foreach ( tsvd_get_animal_adoption_status_options() as $value => $option ) {
		$statuses[] = array(
			'value' => $value,
			'label' => $option['label'],
			'text'  => $option['text'],
			'tone'  => tsvd_at_tone( $option['notice_class'] ),
		);
	}
	return $statuses;
}

function tsvd_at_missing_statuses() {
	$labels  = array(
		'missing'  => __( 'Vermisst', 'tsvd' ),
		'found'    => __( 'Gefunden', 'tsvd' ),
		'reunited' => __( 'Wieder zuhause', 'tsvd' ),
	);
	$banners = function_exists( 'tsvd_get_animal_missing_status_banners' )
		? tsvd_get_animal_missing_status_banners() : array();
	$result  = array();
	foreach ( $labels as $value => $label ) {
		$result[] = array(
			'value' => $value,
			'label' => $label,
			'text'  => $banners[ $value ]['text'] ?? '',
			'tone'  => tsvd_at_tone( $banners[ $value ]['notice_class'] ?? '' ),
		);
	}
	return $result;
}

function tsvd_at_missing_fields() {
	if ( ! function_exists( 'tsvd_get_animal_missing_field_definitions' ) ) {
		return array();
	}
	$fields = array();
	foreach ( tsvd_get_animal_missing_field_definitions() as $key => $definition ) {
		list( $label, $type, $states, $readonly_states ) = $definition;
		$fields[] = array(
			'key'             => $key,
			'label'           => $label,
			'type'            => $type,
			'states'          => $states,
			'readonly_states' => $readonly_states,
		);
	}
	return $fields;
}

function tsvd_at_adoption_sources() {
	return tsvd_at_value_labels(
		array(
			''              => __( 'Keine Angabe', 'tsvd' ),
			'social_media'  => __( 'Social Media', 'tsvd' ),
			'website'       => __( 'Website', 'tsvd' ),
			'shelter_visit' => __( 'Besuch im Tierheim', 'tsvd' ),
			'referral'      => __( 'Weiterempfehlung/Bekannte', 'tsvd' ),
			'other'         => __( 'Andere', 'tsvd' ),
		)
	);
}
