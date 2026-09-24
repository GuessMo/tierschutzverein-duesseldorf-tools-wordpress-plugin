<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_academy_render_lesson( $post ) {
	$back_url = admin_url( 'admin.php?page=' . TSVD_ACADEMY_PAGE );

	echo '<div class="wrap tsvd-dash">';
	echo '<p><a href="' . esc_url( $back_url ) . '">&larr; ' . esc_html__( 'Zur Übersicht', 'tsvd' ) . '</a></p>';
	echo '<h1 class="wp-heading-inline">' . esc_html( get_the_title( $post ) ) . '</h1>';
	if ( current_user_can( 'edit_post', $post->ID ) ) {
		echo ' <a href="' . esc_url( get_edit_post_link( $post->ID ) ) . '" class="page-title-action">'
			. esc_html__( 'Bearbeiten', 'tsvd' ) . '</a>';
	}
	echo '<hr class="wp-header-end">';
	echo '<p class="tsvd-dash-hint">' . esc_html( tsvd_academy_lesson_meta_line( $post ) ) . '</p>';
	echo '<div class="tsvd-card tsvd-card--full tsvd-academy-lesson">';
	echo apply_filters( 'the_content', $post->post_content );
	echo '</div></div>';
}

function tsvd_academy_lesson_meta_line( $post ) {
	$names = wp_get_post_terms( $post->ID, TSVD_ACADEMY_TAX, array( 'fields' => 'names' ) );
	$parts = array();
	if ( ! is_wp_error( $names ) && $names ) {
		$parts[] = sprintf( __( 'Themen: %s', 'tsvd' ), implode( ', ', $names ) );
	}
	$parts[] = sprintf(
		__( 'Aktualisiert: %s', 'tsvd' ),
		get_the_modified_date( get_option( 'date_format' ), $post )
	);
	return implode( ' · ', $parts );
}
