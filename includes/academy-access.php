<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_academy_can_manage() {
	return current_user_can( TSVD_ACADEMY_EDIT_CAP );
}

function tsvd_academy_is_admin_only( $post_id ) {
	return has_term( TSVD_ACADEMY_ADMIN_TERM, TSVD_ACADEMY_TAX, $post_id );
}

function tsvd_academy_is_restricted( $post ) {
	return 'publish' !== $post->post_status
		|| '' !== $post->post_password
		|| tsvd_academy_is_admin_only( $post->ID );
}

function tsvd_academy_user_can_view( $post, $user_id ) {
	if ( ! $post instanceof WP_Post || TSVD_ACADEMY_CPT !== $post->post_type ) {
		return false;
	}
	if ( tsvd_academy_is_restricted( $post ) ) {
		return user_can( $user_id, TSVD_ACADEMY_EDIT_CAP );
	}
	return user_can( $user_id, 'read' );
}

function tsvd_academy_can_view( $post ) {
	return tsvd_academy_user_can_view( $post, get_current_user_id() );
}

function tsvd_academy_visible_lessons() {
	$lessons = get_posts(
		array(
			'post_type'      => TSVD_ACADEMY_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	return array_values( array_filter( $lessons, 'tsvd_academy_can_view' ) );
}

function tsvd_academy_visible_topics() {
	$topics = get_terms(
		array(
			'taxonomy'   => TSVD_ACADEMY_TAX,
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $topics ) ) {
		return array();
	}
	if ( tsvd_academy_can_manage() ) {
		return $topics;
	}
	return array_values(
		array_filter(
			$topics,
			function ( $topic ) {
				return TSVD_ACADEMY_ADMIN_TERM !== $topic->slug;
			}
		)
	);
}

function tsvd_academy_lessons_in_topic( $lessons, $topic ) {
	return array_values(
		array_filter(
			$lessons,
			function ( $lesson ) use ( $topic ) {
				return has_term( $topic->term_id, TSVD_ACADEMY_TAX, $lesson );
			}
		)
	);
}

function tsvd_academy_lesson_url( $post_id ) {
	return add_query_arg(
		array(
			'page'   => TSVD_ACADEMY_PAGE,
			'lesson' => (int) $post_id,
		),
		admin_url( 'admin.php' )
	);
}
