<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_at_post_terms( $post_id, $taxonomy ) {
	$terms = get_the_terms( $post_id, $taxonomy );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms : array();
}

function tsvd_at_term_ids( $post_id, $taxonomy ) {
	$terms = tsvd_at_post_terms( $post_id, $taxonomy );
	return array_map( 'intval', wp_list_pluck( $terms, 'term_id' ) );
}

function tsvd_at_species_ids( $post_id ) {
	$ids = array();
	foreach ( tsvd_at_post_terms( $post_id, 'animal_breed' ) as $term ) {
		$ids[] = $term->parent ? (int) $term->parent : (int) $term->term_id;
	}
	return array_values( array_unique( $ids ) );
}

function tsvd_at_breed_label( $post_id ) {
	$names = wp_list_pluck( tsvd_at_post_terms( $post_id, 'animal_breed' ), 'name' );
	return implode( ', ', $names );
}

function tsvd_at_species_label( $post_id ) {
	$names = array();
	foreach ( tsvd_at_species_ids( $post_id ) as $id ) {
		$term = get_term( $id, 'animal_breed' );
		if ( $term && ! is_wp_error( $term ) ) {
			$names[] = $term->name;
		}
	}
	return implode( ', ', $names );
}

function tsvd_at_color_id( $post_id ) {
	$ids = tsvd_at_term_ids( $post_id, 'animal_color' );
	return $ids ? $ids[0] : null;
}

function tsvd_at_color_label( $post_id ) {
	$terms = tsvd_at_post_terms( $post_id, 'animal_color' );
	return $terms ? $terms[0]->name : '';
}

function tsvd_at_age( $post_id ) {
	$birthday = tsvd_at_str( $post_id, 'animal_birthday' );
	if ( '' === $birthday || ! function_exists( 'tsvd_calculate_age' ) ) {
		return '';
	}
	return (string) tsvd_calculate_age( $birthday );
}
