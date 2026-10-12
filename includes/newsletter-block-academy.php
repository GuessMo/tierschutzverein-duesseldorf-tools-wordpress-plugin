<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_NEWSLETTER_ACADEMY_EXCERPT_WORDS = 30;

add_filter( 'tsvd_newsletter_block_types', 'tsvd_newsletter_register_academy_block' );

function tsvd_newsletter_register_academy_block( $types ) {
	$types['academy-updates'] = array(
		'label'      => __( 'Academy-Updates', 'tsvd' ),
		'dynamic'    => true,
		'render'     => 'tsvd_newsletter_academy_render',
		'sent_items' => 'tsvd_newsletter_academy_sent_items',
		'admin'      => array(
			'description' => __( 'Zeigt veröffentlichte Academy-Anleitungen, die noch in keinem Newsletter waren. Anleitungen mit dem Thema „Admin“ bleiben draußen.', 'tsvd' ),
			'status'      => 'tsvd_newsletter_academy_admin_status',
			'manage_url'  => 'tsvd_newsletter_academy_manage_url',
		),
	);

	return $types;
}

function tsvd_newsletter_academy_manage_url() {
	return admin_url( 'edit.php?post_type=' . TSVD_ACADEMY_CPT );
}

function tsvd_newsletter_academy_admin_status() {
	return sprintf(
		__( '%d noch nicht versendet', 'tsvd' ),
		count( tsvd_newsletter_academy_items() )
	);
}

function tsvd_newsletter_academy_items() {
	if ( ! defined( 'TSVD_ACADEMY_CPT' ) ) {
		return array();
	}

	return get_posts(
		array(
			'post_type'      => TSVD_ACADEMY_CPT,
			'post_status'    => 'publish',
			'has_password'   => false,
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'meta_query'     => array(
				array(
					'key'     => TSVD_NEWSLETTER_SENT_IN_META,
					'compare' => 'NOT EXISTS',
				),
			),
			'tax_query'      => array(
				array(
					'taxonomy' => TSVD_ACADEMY_TAX,
					'field'    => 'slug',
					'terms'    => array( TSVD_ACADEMY_ADMIN_TERM ),
					'operator' => 'NOT IN',
				),
			),
		)
	);
}

function tsvd_newsletter_academy_sent_items( $data ) {
	return wp_list_pluck( tsvd_newsletter_academy_items(), 'ID' );
}

function tsvd_newsletter_academy_excerpt( $lesson ) {
	$source = has_excerpt( $lesson ) ? $lesson->post_excerpt : $lesson->post_content;

	return wp_trim_words( wp_strip_all_tags( $source ), TSVD_NEWSLETTER_ACADEMY_EXCERPT_WORDS, '…' );
}

function tsvd_newsletter_academy_render_item( $lesson ) {
	$url  = tsvd_academy_lesson_url( $lesson->ID );
	$text = tsvd_newsletter_academy_excerpt( $lesson );

	$html  = '<div style="margin:0 0 14px;padding:0 0 14px;border-bottom:1px solid #ebdfd4;">';
	$html .= '<p style="margin:0 0 4px;font-weight:700;font-size:16px;">'
		. '<a href="' . esc_url( $url ) . '" style="color:#007a63;text-decoration:underline;">'
		. esc_html( get_the_title( $lesson ) ) . '</a></p>';
	if ( '' !== $text ) {
		$html .= '<p style="margin:0;font-size:15px;line-height:1.5;color:#3a2f28;">' . esc_html( $text ) . '</p>';
	}
	$html .= '</div>';

	return $html;
}

function tsvd_newsletter_academy_render( $data ) {
	$lessons = tsvd_newsletter_academy_items();
	if ( empty( $lessons ) ) {
		return '';
	}

	$html  = '<div style="margin:0 0 20px;">';
	$html .= '<h2 style="margin:0 0 12px;font-size:20px;color:#009879;">'
		. esc_html__( 'Neue Anleitungen in der Academy', 'tsvd' ) . '</h2>';
	foreach ( $lessons as $lesson ) {
		$html .= tsvd_newsletter_academy_render_item( $lesson );
	}
	$html .= '<p style="margin:0;font-size:13px;color:#6f6157;">'
		. esc_html__( 'Die Links öffnen sich nach der Anmeldung im WordPress-Backend.', 'tsvd' ) . '</p>';
	$html .= '</div>';

	return $html;
}
