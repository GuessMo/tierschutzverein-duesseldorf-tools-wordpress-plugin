<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'manage_' . TSVD_UPDATE_CPT . '_posts_columns', 'tsvd_newsletter_sent_in_column' );

function tsvd_newsletter_sent_in_column( $columns ) {
	$columns['tsvd_newsletter_sent_in'] = __( 'Versendet in', 'tsvd' );

	return $columns;
}

add_action( 'manage_' . TSVD_UPDATE_CPT . '_posts_custom_column', 'tsvd_newsletter_sent_in_column_value', 10, 2 );

function tsvd_newsletter_sent_in_column_value( $column, $post_id ) {
	if ( 'tsvd_newsletter_sent_in' === $column ) {
		echo wp_kses_post( tsvd_newsletter_sent_in_markup( $post_id, false ) );
	}
}

add_action( 'add_meta_boxes_' . TSVD_UPDATE_CPT, 'tsvd_newsletter_sent_in_metabox' );

function tsvd_newsletter_sent_in_metabox() {
	add_meta_box(
		'tsvd-update-sent-in',
		__( 'Newsletter-Versand', 'tsvd' ),
		'tsvd_newsletter_sent_in_metabox_render',
		TSVD_UPDATE_CPT,
		'side'
	);
}

function tsvd_newsletter_sent_in_metabox_render( $post ) {
	echo wp_kses_post( tsvd_newsletter_sent_in_markup( $post->ID, true ) );
}

function tsvd_newsletter_sent_in_markup( $post_id, $with_date ) {
	$entries = tsvd_newsletter_item_sent_in( $post_id );
	if ( empty( $entries ) ) {
		return '<span style="color:var(--tsvd-chrome-text-muted,#646970);">' . esc_html__( 'Noch nicht versendet', 'tsvd' ) . '</span>';
	}

	$items = array();
	foreach ( $entries as $entry ) {
		$label = esc_html( $entry['title'] );
		if ( $entry['edit_url'] ) {
			$label = '<a href="' . esc_url( $entry['edit_url'] ) . '">' . $label . '</a>';
		}
		if ( $with_date && $entry['sent_at'] > 0 ) {
			$label .= ' <span style="color:var(--tsvd-chrome-text-muted,#646970);">(' . esc_html( date_i18n( 'd.m.Y', $entry['sent_at'] ) ) . ')</span>';
		}
		$items[] = $label;
	}

	return implode( $with_date ? '<br>' : ', ', $items );
}
