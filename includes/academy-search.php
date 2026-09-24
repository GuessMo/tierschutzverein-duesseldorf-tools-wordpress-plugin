<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_academy_search_term() {
	return isset( $_GET['s'] ) ? trim( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) : '';
}

function tsvd_academy_search_lessons( $term ) {
	$lessons = get_posts(
		array(
			'post_type'      => TSVD_ACADEMY_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			's'              => $term,
		)
	);
	return array_values( array_filter( $lessons, 'tsvd_academy_can_view' ) );
}

function tsvd_academy_render_search_form( $term ) {
	echo '<form method="get" action="' . esc_url( admin_url( 'admin.php' ) ) . '" class="search-box">';
	echo '<input type="hidden" name="page" value="' . esc_attr( TSVD_ACADEMY_PAGE ) . '">';
	echo '<label class="screen-reader-text" for="tsvd-academy-search">'
		. esc_html__( 'Schulungen durchsuchen', 'tsvd' ) . '</label>';
	echo '<input type="search" id="tsvd-academy-search" name="s" value="' . esc_attr( $term ) . '"'
		. ' placeholder="' . esc_attr__( 'Schulungen durchsuchen …', 'tsvd' ) . '">';
	echo ' <input type="submit" class="button" value="' . esc_attr__( 'Suchen', 'tsvd' ) . '">';
	echo '</form>';
}

function tsvd_academy_render_search_results( $term ) {
	$results = tsvd_academy_search_lessons( $term );
	$title   = sprintf( __( 'Suchergebnisse für „%s“', 'tsvd' ), $term );
	$reset   = admin_url( 'admin.php?page=' . TSVD_ACADEMY_PAGE );

	echo '<div class="tsvd-grid"><div class="tsvd-card tsvd-card--full">';
	echo '<h3>' . esc_html( $title ) . ' (' . count( $results ) . ')</h3>';
	tsvd_academy_render_lesson_list( $results );
	echo '<p><a href="' . esc_url( $reset ) . '">' . esc_html__( 'Suche zurücksetzen', 'tsvd' ) . '</a></p>';
	echo '</div></div>';
}
