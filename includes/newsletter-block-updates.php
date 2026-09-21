<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'tsvd_newsletter_block_types', 'tsvd_newsletter_register_updates_block' );

function tsvd_newsletter_register_updates_block( $types ) {
	$types['website-updates'] = array(
		'label'      => __( 'Website-Updates', 'tsvd' ),
		'dynamic'    => true,
		'render'     => 'tsvd_newsletter_updates_render',
		'sent_items' => 'tsvd_newsletter_updates_sent_items',
		'admin'      => array(
			'description' => __( 'Zeigt die neuesten noch nicht versendeten Website-Updates (Changelog).', 'tsvd' ),
			'status'      => 'tsvd_newsletter_updates_admin_status',
			'manage_url'  => 'tsvd_newsletter_updates_manage_url',
		),
	);

	return $types;
}

function tsvd_newsletter_updates_manage_url() {
	return admin_url( 'edit.php?post_type=' . TSVD_UPDATE_CPT );
}

function tsvd_newsletter_updates_admin_status() {
	$counts    = wp_count_posts( TSVD_UPDATE_CPT );
	$published = isset( $counts->publish ) ? (int) $counts->publish : 0;
	$draft     = isset( $counts->draft ) ? (int) $counts->draft : 0;

	return sprintf(
		__( '%1$d veröffentlicht · %2$d Entwürfe', 'tsvd' ),
		$published,
		$draft
	);
}

function tsvd_newsletter_updates_items() {
	if ( ! function_exists( 'tsvd_update_recent' ) ) {
		return array();
	}

	return tsvd_update_recent( -1, false, true );
}

function tsvd_newsletter_updates_sent_items( $data ) {
	return wp_list_pluck( tsvd_newsletter_updates_items(), 'ID' );
}

function tsvd_newsletter_updates_render( $data ) {
	$updates = tsvd_newsletter_updates_items();
	if ( empty( $updates ) ) {
		return '';
	}

	$html  = '<div style="margin:0 0 20px;">';
	$html .= '<h2 style="margin:0 0 12px;font-size:20px;color:#009879;">'
		. esc_html__( 'Neues von der Website', 'tsvd' ) . '</h2>';

	foreach ( $updates as $update ) {
		$title = get_the_title( $update );
		$date  = get_the_date( 'd.m.Y', $update );
		$text  = wp_trim_words( wp_strip_all_tags( $update->post_content ), 40, '…' );

		$html .= '<div style="margin:0 0 14px;padding:0 0 14px;border-bottom:1px solid #ebdfd4;">';
		$html .= '<p style="margin:0 0 4px;font-weight:700;font-size:16px;color:#3a2f28;">' . esc_html( $title ) . '</p>';
		$html .= '<p style="margin:0 0 4px;font-size:12px;color:#8a7a6d;">' . esc_html( $date ) . '</p>';
		if ( '' !== $text ) {
			$html .= '<p style="margin:0;font-size:15px;line-height:1.5;color:#3a2f28;">' . esc_html( $text ) . '</p>';
		}
		$html .= '</div>';
	}

	$html .= '</div>';

	return $html;
}
