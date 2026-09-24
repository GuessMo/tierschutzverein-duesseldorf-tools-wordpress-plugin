<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ACADEMY_RELATED_LIMIT = 3;

function tsvd_academy_render_lesson( $post ) {
	$toc = tsvd_academy_build_toc( apply_filters( 'the_content', $post->post_content ) );

	echo '<div class="wrap tsvd-ac"><hr class="wp-header-end">';
	tsvd_academy_render_breadcrumb( $post );
	echo '<div class="tsvd-ac-article-layout"><article class="tsvd-ac-article">';
	tsvd_academy_render_article_header( $post );
	echo '<div class="tsvd-ac-prose">' . $toc['html'] . '</div>';
	echo '</article>';
	tsvd_academy_render_toc( $toc['items'] );
	echo '</div>';
	tsvd_academy_render_related( $post );
	echo '</div>';
}

function tsvd_academy_primary_topic( $post ) {
	$topics = tsvd_academy_lesson_topics( $post );
	return $topics ? $topics[0] : null;
}

function tsvd_academy_render_breadcrumb( $post ) {
	$topic = tsvd_academy_primary_topic( $post );

	echo '<nav class="tsvd-ac-breadcrumb" aria-label="' . esc_attr__( 'Brotkrumen', 'tsvd' ) . '"><ol>';
	echo '<li><a href="' . esc_url( tsvd_academy_overview_url() ) . '">' . esc_html__( 'Academy', 'tsvd' ) . '</a></li>';
	if ( $topic ) {
		echo '<li><a href="' . esc_url( tsvd_academy_topic_url( $topic ) ) . '">' . esc_html( $topic->name ) . '</a></li>';
	}
	echo '<li aria-current="page">' . esc_html( get_the_title( $post ) ) . '</li>';
	echo '</ol></nav>';
}

function tsvd_academy_render_article_header( $post ) {
	echo '<header class="tsvd-ac-article__header">';
	echo '<h1 class="tsvd-ac-article__title">' . esc_html( get_the_title( $post ) ) . '</h1>';
	echo '<div class="tsvd-ac-article__meta">';
	tsvd_academy_render_topic_tags( tsvd_academy_lesson_topics( $post ) );
	echo '<span class="tsvd-ac-date">' . esc_html__( 'Aktualisiert am', 'tsvd' ) . ' ';
	tsvd_academy_render_modified_time( $post );
	echo '</span>';
	if ( current_user_can( 'edit_post', $post->ID ) ) {
		echo '<a class="button button-small" href="' . esc_url( get_edit_post_link( $post->ID ) ) . '">'
			. esc_html__( 'Bearbeiten', 'tsvd' ) . '</a>';
	}
	echo '</div></header>';
}

function tsvd_academy_related_lessons( $post ) {
	$topic = tsvd_academy_primary_topic( $post );
	if ( ! $topic ) {
		return array();
	}
	$others = array_filter(
		tsvd_academy_lessons_in_topic( tsvd_academy_visible_lessons(), $topic ),
		function ( $lesson ) use ( $post ) {
			return $lesson->ID !== $post->ID;
		}
	);
	return tsvd_academy_recent_lessons( array_values( $others ), TSVD_ACADEMY_RELATED_LIMIT );
}

function tsvd_academy_render_related( $post ) {
	$related = tsvd_academy_related_lessons( $post );
	if ( ! $related ) {
		return;
	}
	$topic = tsvd_academy_primary_topic( $post );
	echo '<section class="tsvd-ac-section">';
	tsvd_academy_render_section_title( sprintf( __( 'Mehr zum Thema %s', 'tsvd' ), $topic->name ) );
	tsvd_academy_render_lesson_grid( $related );
	echo '</section>';
}
