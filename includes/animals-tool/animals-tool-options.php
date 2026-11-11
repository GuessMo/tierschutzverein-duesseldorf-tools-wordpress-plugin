<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_at_rest_options() {
	return rest_ensure_response(
		array(
			'species'           => tsvd_at_species_tree(),
			'colors'            => tsvd_at_color_options(),
			'option_sets'       => tsvd_at_option_sets(),
			'adoption_statuses' => tsvd_at_adoption_statuses(),
			'missing_statuses'  => tsvd_at_missing_statuses(),
			'missing_fields'    => tsvd_at_missing_fields(),
			'requirement_sets'  => tsvd_at_requirement_sets(),
			'adoption_sources'  => tsvd_at_adoption_sources(),
			'caps'              => array(
				'publish' => tsvd_at_can_publish(),
				'upload'  => current_user_can( 'upload_files' ),
				'approve' => current_user_can( 'manage_tsvd_anfragen' ),
			),
		)
	);
}

function tsvd_at_all_terms( $taxonomy ) {
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'orderby'    => 'name',
		)
	);
	return is_wp_error( $terms ) ? array() : $terms;
}

function tsvd_at_term_option( WP_Term $term ) {
	return array(
		'id'   => (int) $term->term_id,
		'name' => $term->name,
		'slug' => $term->slug,
	);
}

function tsvd_at_species_tree() {
	$terms    = tsvd_at_all_terms( 'animal_breed' );
	$children = array();
	foreach ( $terms as $term ) {
		if ( $term->parent ) {
			$children[ $term->parent ][] = tsvd_at_term_option( $term );
		}
	}
	$species = array();
	foreach ( $terms as $term ) {
		if ( ! $term->parent ) {
			$species[] = tsvd_at_term_option( $term ) + array(
				'breeds' => $children[ $term->term_id ] ?? array(),
			);
		}
	}
	return $species;
}

function tsvd_at_color_options() {
	return array_map( 'tsvd_at_term_option', tsvd_at_all_terms( 'animal_color' ) );
}

function tsvd_at_requirement_sets() {
	if ( ! function_exists( 'tsvd_get_breed_requirements_config' ) ) {
		return (object) array();
	}
	$sets = array();
	foreach ( tsvd_at_all_terms( 'animal_breed' ) as $term ) {
		$config = tsvd_get_breed_requirements_config( $term->slug );
		if ( $config ) {
			$sets[ $term->slug ] = array(
				'label'   => $config['label'],
				'options' => tsvd_at_value_labels( $config['options'] ),
			);
		}
	}
	return (object) $sets;
}

function tsvd_at_value_labels( $map ) {
	$options = array();
	foreach ( $map as $value => $label ) {
		$options[] = array(
			'value' => (string) $value,
			'label' => (string) $label,
		);
	}
	return $options;
}
