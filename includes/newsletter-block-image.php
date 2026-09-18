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
		'edit'     => 'tsvd_newsletter_image_edit',
	);

	return $types;
}

function tsvd_newsletter_image_edit( $index, $data ) {
	$id   = absint( isset( $data['id'] ) ? $data['id'] : 0 );
	$alt  = isset( $data['alt'] ) ? $data['alt'] : '';
	$base = 'newsletter_blocks[' . $index . '][data]';

	$preview = $id ? wp_get_attachment_image( $id, 'medium', false, array( 'class' => 'tsvd-nl-image-preview-img' ) ) : '';

	$html  = '<div class="tsvd-nl-image" data-base="' . esc_attr( $base ) . '">';
	$html .= '<input type="hidden" class="tsvd-nl-image-id" name="' . esc_attr( $base ) . '[id]" value="' . esc_attr( $id ) . '">';
	$html .= '<div class="tsvd-nl-image-preview">' . $preview . '</div>';
	$html .= '<p><button type="button" class="button tsvd-nl-image-select">' . esc_html__( 'Bild wählen', 'tsvd' ) . '</button> ';
	$html .= '<button type="button" class="button-link tsvd-nl-image-clear"' . ( $id ? '' : ' hidden' ) . '>' . esc_html__( 'Entfernen', 'tsvd' ) . '</button></p>';
	$html .= '<label class="tsvd-nl-image-alt-label">' . esc_html__( 'Alt-Text', 'tsvd' );
	$html .= '<input type="text" class="regular-text" name="' . esc_attr( $base ) . '[alt]" value="' . esc_attr( $alt ) . '"></label>';
	$html .= '</div>';

	return $html;
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
