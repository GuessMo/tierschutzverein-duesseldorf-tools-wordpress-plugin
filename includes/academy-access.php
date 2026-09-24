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

function tsvd_academy_find_topic( $slug ) {
	foreach ( tsvd_academy_visible_topics() as $topic ) {
		if ( $topic->slug === $slug ) {
			return $topic;
		}
	}
	return null;
}

function tsvd_academy_lesson_topics( $post ) {
	$terms = get_the_terms( $post, TSVD_ACADEMY_TAX );
	return is_array( $terms ) ? $terms : array();
}

function tsvd_academy_recent_lessons( $lessons, $limit ) {
	usort(
		$lessons,
		function ( $a, $b ) {
			return strcmp( $b->post_modified_gmt, $a->post_modified_gmt );
		}
	);
	return array_slice( $lessons, 0, $limit );
}

function tsvd_academy_overview_url( $args = array() ) {
	$args = array_merge( array( 'page' => TSVD_ACADEMY_PAGE ), $args );
	return add_query_arg( $args, admin_url( 'admin.php' ) );
}

function tsvd_academy_topic_url( $topic ) {
	return tsvd_academy_overview_url( array( 'topic' => $topic->slug ) );
}

function tsvd_academy_lesson_url( $post_id ) {
	return tsvd_academy_overview_url( array( 'lesson' => (int) $post_id ) );
}
