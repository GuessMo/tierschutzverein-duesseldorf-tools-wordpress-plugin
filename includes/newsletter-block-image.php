<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'tsvd_newsletter_block_types', 'tsvd_newsletter_register_image_block' );

function tsvd_newsletter_register_image_block( $types ) {
	$types['image'] = array(
		'label'    => __( 'Bild', 'tsvd' ),
		'dynamic'  => false,
		'sanitize' => 'tsvd_newsletter_image_sanitize',
		'render'   => 'tsvd_newsletter_image_render',
	);

	return $types;
}

function tsvd_newsletter_image_sanitize( $data ) {
	return array(
		'id'  => absint( isset( $data['id'] ) ? $data['id'] : 0 ),
		'alt' => sanitize_text_field( isset( $data['alt'] ) ? $data['alt'] : '' ),
	);
}

function tsvd_newsletter_image_render( $data ) {
	$id = absint( isset( $data['id'] ) ? $data['id'] : 0 );
	if ( ! $id ) {
		return '';
	}

	$url = wp_get_attachment_image_url( $id, 'large' );
	if ( ! $url ) {
		return '';
	}

	$alt = isset( $data['alt'] ) ? $data['alt'] : '';

	return '<div style="margin:0 0 20px;"><img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt )
		. '" style="max-width:100%;height:auto;display:block;border-radius:8px;"></div>';
}
