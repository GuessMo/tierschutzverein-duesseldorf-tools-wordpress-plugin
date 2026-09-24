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

function tsvd_academy_render_search_form( $term, $topic ) {
	$placeholder = __( 'Wonach suchst Du?', 'tsvd' );

	echo '<form class="tsvd-ac-search" role="search" method="get" action="' . esc_url( admin_url( 'admin.php' ) ) . '">';
	echo '<input type="hidden" name="page" value="' . esc_attr( TSVD_ACADEMY_PAGE ) . '">';
	if ( $topic ) {
		echo '<input type="hidden" name="topic" value="' . esc_attr( $topic->slug ) . '">';
	}
	echo '<label class="screen-reader-text" for="tsvd-ac-search-input">' . esc_html__( 'Academy durchsuchen', 'tsvd' ) . '</label>';
	echo '<div class="tsvd-ac-search__field">';
	echo '<span class="tsvd-ac-search__icon dashicons dashicons-search" aria-hidden="true"></span>';
	echo '<input type="search" id="tsvd-ac-search-input" class="tsvd-ac-search__input" name="s" value="'
		. esc_attr( $term ) . '" placeholder="' . esc_attr( $placeholder ) . '">';
	echo '</div>';
	echo '<button type="submit" class="button button-primary tsvd-ac-search__submit">' . esc_html__( 'Suchen', 'tsvd' ) . '</button>';
	echo '</form>';
}

function tsvd_academy_search_heading( $term, $count, $topic ) {
	if ( $topic ) {
		return sprintf( _n( '%1$d Treffer für „%2$s“ in %3$s', '%1$d Treffer für „%2$s“ in %3$s', $count, 'tsvd' ), $count, $term, $topic->name );
	}
	return sprintf( _n( '%1$d Treffer für „%2$s“', '%1$d Treffer für „%2$s“', $count, 'tsvd' ), $count, $term );
}

function tsvd_academy_render_search_results( $term, $topic ) {
	$results = tsvd_academy_search_lessons( $term );
	if ( $topic ) {
		$results = tsvd_academy_lessons_in_topic( $results, $topic );
	}
	$reset = $topic ? tsvd_academy_topic_url( $topic ) : tsvd_academy_overview_url();

	echo '<section class="tsvd-ac-section">';
	tsvd_academy_render_section_title( tsvd_academy_search_heading( $term, count( $results ), $topic ) );
	if ( $results ) {
		tsvd_academy_render_lesson_grid( $results );
	} else {
		tsvd_academy_render_empty(
			sprintf( __( 'Keine Schulung zu „%s“ gefunden.', 'tsvd' ), $term ),
			__( 'Prüfe die Schreibweise, versuche ein anderes Wort oder wähle oben ein Thema.', 'tsvd' )
		);
	}
	echo '<p class="tsvd-ac-reset"><a href="' . esc_url( $reset ) . '">' . esc_html__( 'Suche zurücksetzen', 'tsvd' ) . '</a></p>';
	echo '</section>';
}
