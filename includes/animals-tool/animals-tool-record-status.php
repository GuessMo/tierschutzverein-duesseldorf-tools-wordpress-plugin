<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_AT_CONTACT_FIELDS = array( 'firstname', 'lastname', 'address', 'email' );

function tsvd_at_missing_field_keys() {
	if ( ! function_exists( 'tsvd_get_animal_missing_field_definitions' ) ) {
		return array();
	}
	return array_keys( tsvd_get_animal_missing_field_definitions() );
}

function tsvd_at_record_status( $id ) {
	$missing = tsvd_at_missing_status( $id );
	return array(
		'record_type'              => '' === $missing ? 'vermittlung' : 'vermisst',
		'mediator'                 => tsvd_at_mediator( $id ),
		'adoption_status'          => (string) tsvd_at_meta(
			$id,
			'animal_adoption_status',
			'not_for_adoption'
		),
		'participates_in_nori'     => '1' === tsvd_at_str( $id, 'animal_participates_in_nori' ),
		'missing_status'           => '' === $missing ? 'missing' : $missing,
		'missing'                  => tsvd_at_prefixed_meta(
			$id,
			'animal_missing_',
			tsvd_at_missing_field_keys()
		),
		'private_contact'          => tsvd_at_prefixed_meta(
			$id,
			'animal_private_contact_',
			TSVD_AT_CONTACT_FIELDS
		),
		'adoption_requirements'    => array_values(
			(array) tsvd_at_meta( $id, 'animal_adoption_requirements', array() )
		),
		'internal_adoption_status' => tsvd_at_str( $id, 'animal_internal_adoption_status' ),
		'owner_pending_action'     => tsvd_at_str( $id, '_tsvd_owner_pending_action' ),
	);
}

function tsvd_at_prefixed_meta( $id, $prefix, $keys ) {
	$values = array();
	foreach ( $keys as $key ) {
		$values[ $key ] = (string) get_post_meta( $id, $prefix . $key, true );
	}
	return (object) $values;
}

function tsvd_at_record_story( $id ) {
	return array(
		'adoption_date'         => tsvd_at_str( $id, 'animal_adoption_date' ),
		'owner_feedback_date'   => tsvd_at_str( $id, 'animal_owner_feedback_date' ),
		'adoption_source'       => tsvd_at_str( $id, 'animal_adoption_source' ),
		'adoption_source_other' => tsvd_at_str( $id, 'animal_adoption_source_other' ),
		'show_in_frontend'      => '0' !== tsvd_at_str( $id, 'animal_show_in_frontend' ),
		'owner_photos'          => tsvd_at_meta_images( $id, 'animal_adopted_owner_photos' ),
		'owner_text'            => tsvd_at_str( $id, 'animal_adopted_owner_text' ),
	);
}

function tsvd_at_record_tdw( $id ) {
	return array(
		'enabled'       => '1' === get_post_meta( $id, 'animal_tdw_enabled', true ),
		'year'          => (int) tsvd_at_meta( $id, 'animal_tdw_year', (int) gmdate( 'Y' ) ),
		'week'          => (int) tsvd_at_meta( $id, 'animal_tdw_week', (int) gmdate( 'W' ) ),
		'text'          => tsvd_at_str( $id, 'animal_tdw_text' ),
		'article_image' => tsvd_at_image( (int) tsvd_at_str( $id, 'animal_tdw_article_image' ) ),
	);
}

function tsvd_at_record_interest( $id ) {
	if ( ! function_exists( 'tsvd_interest_get_seed' ) ) {
		return null;
	}
	return array(
		'seed'  => (int) tsvd_interest_get_seed( $id ),
		'form'  => (int) tsvd_interest_get_form_count( $id ),
		'total' => (int) tsvd_interest_get_total( $id ),
	);
}
