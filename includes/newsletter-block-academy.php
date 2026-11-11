<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_NEWSLETTER_ACADEMY_SENT_OPTION = 'tsvd_newsletter_academy_sent';

add_filter( 'tsvd_newsletter_block_types', 'tsvd_newsletter_register_academy_block' );
add_action( 'tsvd_newsletter_sent', 'tsvd_newsletter_academy_record_sent' );

function tsvd_newsletter_register_academy_block( $types ) {
	$types['academy-updates'] = array(
		'label'   => __( 'Academy-Updates', 'tsvd' ),
		'dynamic' => true,
		'render'  => 'tsvd_newsletter_academy_render',
		'admin'   => array(
			'description' => __(
				'Zeigt Academy-Anleitungen, die noch in keinem Newsletter waren. Anleitungen nur für Admins bleiben draußen.',
				'tsvd'
			),
			'status'      => 'tsvd_newsletter_academy_admin_status',
			'manage_url'  => 'tsvd_newsletter_academy_manage_url',
		),
	);
	return $types;
}

function tsvd_newsletter_academy_manage_url() {
	return tsvd_tool_portal_url( 'academy' );
}

function tsvd_newsletter_academy_admin_status() {
	$count = count( tsvd_newsletter_academy_items() );
	return sprintf( __( '%d noch nicht versendet', 'tsvd' ), $count );
}

function tsvd_newsletter_academy_legacy_sent() {
	$sent = array();
	if ( ! defined( 'TSVD_ACADEMY_CPT' ) ) {
		return $sent;
	}
	$posts = get_posts(
		array(
			'post_type'      => TSVD_ACADEMY_CPT,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'meta_key'       => TSVD_NEWSLETTER_SENT_IN_META,
		)
	);
	foreach ( $posts as $post ) {
		$ids = (array) get_post_meta( $post->ID, TSVD_NEWSLETTER_SENT_IN_META, true );

		$sent[ $post->post_name ] = array_map( 'intval', $ids );
	}
	return $sent;
}

function tsvd_newsletter_academy_sent_map() {
	$sent = get_option( TSVD_NEWSLETTER_ACADEMY_SENT_OPTION, null );
	if ( is_array( $sent ) ) {
		return $sent;
	}
	$sent = tsvd_newsletter_academy_legacy_sent();
	update_option( TSVD_NEWSLETTER_ACADEMY_SENT_OPTION, $sent, false );
	return $sent;
}

function tsvd_newsletter_academy_items() {
	if ( ! function_exists( 'tsvd_act_md_lessons' ) ) {
		return array();
	}
	$sent = tsvd_newsletter_academy_sent_map();
	return array_values(
		array_filter(
			tsvd_act_md_lessons(),
			function ( $lesson ) use ( $sent ) {
				return 'all' === $lesson['audience'] && empty( $sent[ $lesson['slug'] ] );
			}
		)
	);
}

function tsvd_newsletter_academy_has_block( $newsletter_id ) {
	foreach ( tsvd_newsletter_get_blocks( $newsletter_id ) as $block ) {
		if ( 'academy-updates' === ( $block['type'] ?? '' ) ) {
			return true;
		}
	}
	return false;
}

function tsvd_newsletter_academy_record_sent( $newsletter_id ) {
	if ( ! tsvd_newsletter_academy_has_block( $newsletter_id ) ) {
		return;
	}
	$sent = tsvd_newsletter_academy_sent_map();
	foreach ( tsvd_newsletter_academy_items() as $lesson ) {
		$sent[ $lesson['slug'] ] = array( (int) $newsletter_id );
	}
	update_option( TSVD_NEWSLETTER_ACADEMY_SENT_OPTION, $sent, false );
}

function tsvd_newsletter_academy_render_item( array $lesson ) {
	$url  = tsvd_tool_portal_url( 'academy' ) . '#lesson/' . $lesson['slug'];
	$text = tsvd_act_md_excerpt( $lesson['body'] );
	$html = '<div style="margin:0 0 14px;padding:0 0 14px;border-bottom:1px solid #ebdfd4;">';
	$html .= '<p style="margin:0 0 4px;font-weight:700;font-size:16px;">'
		. '<a href="' . esc_url( $url ) . '" style="color:#007a63;text-decoration:underline;">'
		. esc_html( $lesson['title'] ) . '</a></p>';
	if ( '' !== $text ) {
		$html .= '<p style="margin:0;font-size:15px;line-height:1.5;color:#3a2f28;">'
			. esc_html( $text ) . '</p>';
	}
	return $html . '</div>';
}

function tsvd_newsletter_academy_render( $data ) {
	$lessons = tsvd_newsletter_academy_items();
	if ( empty( $lessons ) ) {
		return '';
	}
	$html = '<div style="margin:0 0 20px;">';
	$html .= '<h2 style="margin:0 0 12px;font-size:20px;color:#009879;">'
		. esc_html__( 'Neue Anleitungen in der Academy', 'tsvd' ) . '</h2>';
	foreach ( $lessons as $lesson ) {
		$html .= tsvd_newsletter_academy_render_item( $lesson );
	}
	$html .= '<p style="margin:0;font-size:13px;color:#6f6157;">'
		. esc_html__( 'Die Links öffnen sich nach der Anmeldung in der Academy.', 'tsvd' ) . '</p>';
	return $html . '</div>';
}
