<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_academy_topic_icon( $topic ) {
	$icons = array(
		TSVD_ACADEMY_ADMIN_TERM => 'dashicons-shield',
		'anwender'              => 'dashicons-groups',
		'google-workspace'      => 'dashicons-cloud',
		'mails'                 => 'dashicons-email-alt',
	);
	return $icons[ $topic->slug ] ?? 'dashicons-category';
}

function tsvd_academy_topic_hint( $topic ) {
	if ( TSVD_ACADEMY_ADMIN_TERM === $topic->slug ) {
		return __( 'Nur für Admins sichtbar.', 'tsvd' );
	}
	return $topic->description;
}

function tsvd_academy_lesson_count_label( $count ) {
	return sprintf( _n( '%d Schulung', '%d Schulungen', $count, 'tsvd' ), $count );
}

function tsvd_academy_render_lesson_grid( $lessons ) {
	echo '<div class="tsvd-ac-grid">';
	foreach ( $lessons as $lesson ) {
		tsvd_academy_render_lesson_card( $lesson );
	}
	echo '</div>';
}

function tsvd_academy_render_lesson_card( $lesson ) {
	echo '<article class="tsvd-ac-card">';
	echo '<h3 class="tsvd-ac-card__title"><a class="tsvd-ac-card__link" href="'
		. esc_url( tsvd_academy_lesson_url( $lesson->ID ) ) . '">'
		. esc_html( get_the_title( $lesson ) ) . '</a></h3>';
	if ( has_excerpt( $lesson ) ) {
		echo '<p class="tsvd-ac-card__excerpt">' . esc_html( get_the_excerpt( $lesson ) ) . '</p>';
	}
	echo '<footer class="tsvd-ac-card__meta">';
	tsvd_academy_render_topic_tags( tsvd_academy_lesson_topics( $lesson ) );
	tsvd_academy_render_modified_time( $lesson );
	echo '</footer></article>';
}

function tsvd_academy_render_modified_time( $lesson ) {
	echo '<time class="tsvd-ac-date" datetime="' . esc_attr( get_post_modified_time( 'c', true, $lesson ) ) . '">'
		. esc_html( get_the_modified_date( 'j. M Y', $lesson ) ) . '</time>';
}

function tsvd_academy_render_topic_tags( $topics ) {
	if ( ! $topics ) {
		return;
	}
	echo '<span class="tsvd-ac-tags">';
	foreach ( $topics as $topic ) {
		echo '<span class="tsvd-ac-tag">' . esc_html( $topic->name ) . '</span>';
	}
	echo '</span>';
}

function tsvd_academy_render_topic_grid( $topics, $lessons ) {
	echo '<div class="tsvd-ac-topics">';
	foreach ( $topics as $topic ) {
		tsvd_academy_render_topic_card( $topic, count( tsvd_academy_lessons_in_topic( $lessons, $topic ) ) );
	}
	echo '</div>';
}

function tsvd_academy_render_topic_card( $topic, $count ) {
	$hint = tsvd_academy_topic_hint( $topic );

	echo '<article class="tsvd-ac-topic">';
	echo '<span class="tsvd-ac-topic__icon dashicons ' . esc_attr( tsvd_academy_topic_icon( $topic ) ) . '" aria-hidden="true"></span>';
	echo '<div class="tsvd-ac-topic__body">';
	echo '<h3 class="tsvd-ac-topic__title"><a class="tsvd-ac-card__link" href="'
		. esc_url( tsvd_academy_topic_url( $topic ) ) . '">' . esc_html( $topic->name ) . '</a></h3>';
	echo '<p class="tsvd-ac-topic__count">' . esc_html( tsvd_academy_lesson_count_label( $count ) ) . '</p>';
	if ( $hint ) {
		echo '<p class="tsvd-ac-topic__hint">' . esc_html( $hint ) . '</p>';
	}
	echo '</div></article>';
}

function tsvd_academy_render_filter( $topics, $active_slug ) {
	echo '<nav class="tsvd-ac-filter" aria-label="' . esc_attr__( 'Nach Thema filtern', 'tsvd' ) . '">';
	tsvd_academy_render_chip( __( 'Alle', 'tsvd' ), tsvd_academy_overview_url(), '' === $active_slug );
	foreach ( $topics as $topic ) {
		tsvd_academy_render_chip( $topic->name, tsvd_academy_topic_url( $topic ), $topic->slug === $active_slug );
	}
	echo '</nav>';
}

function tsvd_academy_render_chip( $label, $url, $is_active ) {
	echo '<a class="tsvd-ac-chip" href="' . esc_url( $url ) . '"' . ( $is_active ? ' aria-current="page"' : '' ) . '>'
		. esc_html( $label ) . '</a>';
}

function tsvd_academy_render_empty( $message, $hint ) {
	echo '<div class="tsvd-ac-empty"><p class="tsvd-ac-empty__message">' . esc_html( $message ) . '</p>';
	echo '<p class="tsvd-ac-empty__hint">' . esc_html( $hint ) . '</p></div>';
}
