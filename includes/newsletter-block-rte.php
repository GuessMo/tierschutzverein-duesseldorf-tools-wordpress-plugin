<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'tsvd_newsletter_block_types', 'tsvd_newsletter_register_rte_block' );

function tsvd_newsletter_register_rte_block( $types ) {
	$types['rte'] = array(
		'label'    => __( 'Text', 'tsvd' ),
		'dynamic'  => false,
		'sanitize' => 'tsvd_newsletter_rte_sanitize',
		'render'   => 'tsvd_newsletter_rte_render',
	);

	return $types;
}

function tsvd_newsletter_rte_sanitize( $data ) {
	return array( 'html' => wp_kses_post( isset( $data['html'] ) ? $data['html'] : '' ) );
}

function tsvd_newsletter_rte_render( $data ) {
	$html = isset( $data['html'] ) ? $data['html'] : '';
	if ( '' === trim( wp_strip_all_tags( $html ) ) ) {
		return '';
	}

	return '<div style="margin:0 0 20px;font-size:16px;line-height:1.6;color:#3a2f28;">' . $html . '</div>';
}
