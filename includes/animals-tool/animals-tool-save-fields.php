<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_AT_TEXT_FIELDS = array(
	'name',
	'teo_id',
	'description',
	'birthday_status',
	'birthday',
	'life_stage',
	'size_cm',
	'gender',
	'castrated',
	'vaccinated',
	'chipped',
	'mediator',
	'adoption_status',
	'adoption_date',
	'owner_feedback_date',
	'adoption_source',
	'adoption_source_other',
);

function tsvd_at_text( $data, $key ) {
	$value = $data[ $key ] ?? '';
	return is_scalar( $value ) ? (string) $value : '';
}

function tsvd_at_flag( $data, $key ) {
	return ! empty( $data[ $key ] );
}

function tsvd_at_legacy_fields( array $data ) {
	return array_merge(
		array( 'tsvd_animal_meta_nonce' => wp_create_nonce( 'tsvd_save_animal_meta' ) ),
		tsvd_at_text_fields( $data ),
		tsvd_at_relation_fields( $data ),
		tsvd_at_status_fields( $data ),
		tsvd_at_story_fields( $data ),
		tsvd_at_tdw_fields( (array) ( $data['tdw'] ?? array() ) ),
		tsvd_at_interest_fields( $data )
	);
}

function tsvd_at_text_fields( array $data ) {
	$fields = array();
	foreach ( TSVD_AT_TEXT_FIELDS as $key ) {
		$fields[ 'animal_' . $key ] = tsvd_at_text( $data, $key );
	}
	$color                  = absint( $data['color'] ?? 0 );
	$fields['animal_color'] = $color ? (string) $color : '';
	return $fields;
}

function tsvd_at_relation_fields( array $data ) {
	$companions = wp_list_pluck( (array) ( $data['companions'] ?? array() ), 'id' );
	return array(
		'animal_images'            => tsvd_at_id_list( $data['image_ids'] ?? array() ),
		'animal_companion_animals' => tsvd_at_id_list( $companions ),
	);
}

function tsvd_at_status_fields( array $data ) {
	$is_missing = 'vermisst' === tsvd_at_text( $data, 'record_type' );
	$fields     = array(
		'animal_missing_status' => $is_missing ? tsvd_at_text( $data, 'missing_status' ) : '',
	);
	$missing    = (array) ( $data['missing'] ?? array() );
	foreach ( tsvd_at_missing_field_keys() as $key ) {
		$fields[ 'animal_missing_' . $key ] = tsvd_at_text( $missing, $key );
	}
	foreach ( TSVD_AT_CONTACT_FIELDS as $key ) {
		$contact = (array) ( $data['private_contact'] ?? array() );
		$fields[ 'animal_private_contact_' . $key ] = tsvd_at_text( $contact, $key );
	}
	if ( tsvd_at_flag( $data, 'participates_in_nori' ) ) {
		$fields['animal_participates_in_nori'] = '1';
	}
	$requirements = array_map( 'strval', (array) ( $data['adoption_requirements'] ?? array() ) );
	if ( $requirements ) {
		$fields['animal_adoption_requirements'] = $requirements;
	}
	return $fields;
}

function tsvd_at_story_fields( array $data ) {
	$fields = array(
		'animal_adopted_owner_photos' => tsvd_at_id_list( $data['owner_photo_ids'] ?? array() ),
		'animal_adopted_owner_text'   => tsvd_at_text( $data, 'owner_text' ),
	);
	if ( tsvd_at_flag( $data, 'show_in_frontend' ) ) {
		$fields['animal_show_in_frontend'] = '1';
	}
	return $fields;
}

function tsvd_at_tdw_fields( array $tdw ) {
	$fields = array(
		'animal_tdw_year'          => (string) absint( $tdw['year'] ?? 0 ),
		'animal_tdw_week'          => (string) absint( $tdw['week'] ?? 0 ),
		'animal_tdw_text'          => tsvd_at_text( $tdw, 'text' ),
		'animal_tdw_article_image' => (string) absint( $tdw['article_image_id'] ?? 0 ),
	);
	if ( tsvd_at_flag( $tdw, 'enabled' ) ) {
		$fields['animal_tdw_enabled'] = '1';
	}
	return $fields;
}

function tsvd_at_interest_fields( array $data ) {
	if ( ! isset( $data['interest_seed'] ) || ! is_numeric( $data['interest_seed'] ) ) {
		return array();
	}
	return array(
		'tsvd_interest_nonce' => wp_create_nonce( 'tsvd_interest_save' ),
		'tsvd_interest_seed'  => (string) (int) $data['interest_seed'],
	);
}
