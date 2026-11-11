<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_AT_HELP_ALIASES = array(
	'animal_breeds'         => 'animal_breed_selection',
	'animal_featured_image' => 'postimagediv',
	'animal_images'         => 'animal_additional_images',
	'animal_record_type'    => 'tsvd_record_type_ui',
	'animal_status'         => 'submitdiv',
	'animal_tdw_image'      => 'animal_tdw_article_image',
	'animal_owner_photos'   => 'animal_adopted_owner_photos',
	'animal_owner_text'     => 'animal_adopted_owner_text',
	'animal_interest_seed'  => 'tsvd_interest_seed',
	'animal_species'        => 'animal_breed_selection',
	'animal_nori'           => 'animal_participates_in_nori',
	'animal_contact_firstname' => 'animal_private_contact_firstname',
	'animal_contact_lastname'  => 'animal_private_contact_lastname',
	'animal_contact_address'   => 'animal_private_contact_address',
	'animal_contact_email'     => 'animal_private_contact_email',
);

function tsvd_at_help_texts() {
	if ( ! function_exists( 'tsvd_help_animal_data_field_texts' ) ) {
		return array();
	}
	$texts = array_merge(
		tsvd_help_animal_box_texts(),
		tsvd_help_animal_data_field_texts(),
		tsvd_help_animal_status_field_texts()
	);
	foreach ( TSVD_AT_HELP_ALIASES as $alias => $key ) {
		if ( isset( $texts[ $key ] ) ) {
			$texts[ $alias ] = $texts[ $key ];
		}
	}
	return $texts;
}
