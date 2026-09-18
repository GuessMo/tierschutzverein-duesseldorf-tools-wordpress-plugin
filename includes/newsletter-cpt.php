<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_NEWSLETTER_CPT       = 'tsvd_newsletter';
const TSVD_NEWSLETTER_SENT_META = '_tsvd_newsletter_sent_at';

add_action( 'init', 'tsvd_newsletter_register_cpt' );

function tsvd_newsletter_register_cpt() {
	register_post_type(
		TSVD_NEWSLETTER_CPT,
		array(
			'labels'          => array(
				'name'          => __( 'Newsletter', 'tsvd' ),
				'singular_name' => __( 'Newsletter', 'tsvd' ),
				'add_new'       => __( 'Neu erstellen', 'tsvd' ),
				'add_new_item'  => __( 'Neuen Newsletter erstellen', 'tsvd' ),
				'edit_item'     => __( 'Newsletter bearbeiten', 'tsvd' ),
				'new_item'      => __( 'Neuer Newsletter', 'tsvd' ),
				'all_items'     => __( 'Alle Newsletter', 'tsvd' ),
				'menu_name'     => __( 'Newsletter', 'tsvd' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'tsvd-newsletter',
			'show_in_rest'    => false,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'menu_position'   => null,
			'rewrite'         => false,
			'query_var'       => false,
		)
	);
}

function tsvd_newsletter_is_sent( $post_id ) {
	return '' !== (string) get_post_meta( $post_id, TSVD_NEWSLETTER_SENT_META, true );
}

add_filter( 'manage_' . TSVD_NEWSLETTER_CPT . '_posts_columns', 'tsvd_newsletter_admin_columns' );

function tsvd_newsletter_admin_columns( $columns ) {
	$ordered = array();
	foreach ( $columns as $key => $label ) {
		$ordered[ $key ] = $label;
		if ( 'title' === $key ) {
			$ordered['tsvd_newsletter_status']     = __( 'Status', 'tsvd' );
			$ordered['tsvd_newsletter_recipients'] = __( 'Empfänger', 'tsvd' );
		}
	}

	return $ordered;
}

add_action( 'manage_' . TSVD_NEWSLETTER_CPT . '_posts_custom_column', 'tsvd_newsletter_admin_column_value', 10, 2 );

function tsvd_newsletter_admin_column_value( $column, $post_id ) {
	if ( 'tsvd_newsletter_status' === $column ) {
		$sent_at = (int) get_post_meta( $post_id, TSVD_NEWSLETTER_SENT_META, true );
		if ( $sent_at > 0 ) {
			echo esc_html( sprintf( __( 'Gesendet am %s', 'tsvd' ), date_i18n( 'd.m.Y H:i', $sent_at ) ) );
		} else {
			echo esc_html__( 'Entwurf', 'tsvd' );
		}
		return;
	}

	if ( 'tsvd_newsletter_recipients' === $column ) {
		echo (int) count( tsvd_newsletter_subscriber_ids() );
	}
}
