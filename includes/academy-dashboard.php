<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ACADEMY_RECENT_LIMIT = 3;

function tsvd_academy_requested_topic_slug() {
	return isset( $_GET['topic'] ) ? sanitize_title( wp_unslash( $_GET['topic'] ) ) : '';
}

function tsvd_academy_render_dashboard() {
	$term    = tsvd_academy_search_term();
	$slug    = tsvd_academy_requested_topic_slug();
	$topic   = $slug ? tsvd_academy_find_topic( $slug ) : null;
	$lessons = tsvd_academy_visible_lessons();
	$topics  = tsvd_academy_visible_topics();

	echo '<div class="wrap tsvd-ac"><hr class="wp-header-end">';
	tsvd_academy_render_header();
	tsvd_academy_render_search_form( $term, $topic );
	tsvd_academy_render_filter( $topics, $topic ? $topic->slug : '' );
	if ( '' !== $term ) {
		tsvd_academy_render_search_results( $term, $topic );
	} elseif ( $topic ) {
		tsvd_academy_render_topic_view( $topic, $lessons );
	} else {
		tsvd_academy_render_home( $lessons, $topics );
	}
	echo '</div>';
}

function tsvd_academy_render_header() {
	echo '<header class="tsvd-ac-header"><div>';
	echo '<h1 class="tsvd-ac-title">' . esc_html__( 'Academy', 'tsvd' ) . '</h1>';
	echo '<p class="tsvd-ac-lead">' . esc_html__( 'Anleitungen und Schulungen für alle im Verein.', 'tsvd' ) . '</p>';
	echo '</div>';
	if ( tsvd_academy_can_manage() ) {
		tsvd_academy_render_manage_actions();
	}
	echo '</header>';
}

function tsvd_academy_render_manage_actions() {
	$list_url = admin_url( 'edit.php?post_type=' . TSVD_ACADEMY_CPT );
	$new_url  = admin_url( 'post-new.php?post_type=' . TSVD_ACADEMY_CPT );
	echo '<div class="tsvd-ac-actions">';
	echo '<a class="button" href="' . esc_url( $list_url ) . '">' . esc_html__( 'Verwalten', 'tsvd' ) . '</a>';
	echo '<a class="button button-primary" href="' . esc_url( $new_url ) . '">' . esc_html__( 'Neue Schulung', 'tsvd' ) . '</a>';
	echo '</div>';
}

function tsvd_academy_render_section_title( $title, $count = null ) {
	echo '<h2 class="tsvd-ac-section__title">' . esc_html( $title );
	if ( null !== $count ) {
		echo ' <span class="tsvd-ac-count">' . (int) $count . '</span>';
	}
	echo '</h2>';
}

function tsvd_academy_render_home( $lessons, $topics ) {
	$recent = tsvd_academy_recent_lessons( $lessons, TSVD_ACADEMY_RECENT_LIMIT );

	echo '<section class="tsvd-ac-section">';
	tsvd_academy_render_section_title( __( 'Zuletzt aktualisiert', 'tsvd' ) );
	if ( $recent ) {
		tsvd_academy_render_lesson_grid( $recent );
	} else {
		tsvd_academy_render_empty(
			__( 'Noch keine Schulungen veröffentlicht.', 'tsvd' ),
			__( 'Sobald eine Schulung erscheint, findest Du sie hier.', 'tsvd' )
		);
	}
	echo '</section><section class="tsvd-ac-section">';
	tsvd_academy_render_section_title( __( 'Themen', 'tsvd' ) );
	tsvd_academy_render_topic_grid( $topics, $lessons );
	echo '</section>';
}

function tsvd_academy_render_topic_view( $topic, $lessons ) {
	$items = tsvd_academy_lessons_in_topic( $lessons, $topic );
	$hint  = tsvd_academy_topic_hint( $topic );

	echo '<section class="tsvd-ac-section">';
	tsvd_academy_render_section_title( $topic->name, count( $items ) );
	if ( $hint ) {
		echo '<p class="tsvd-ac-section__hint">' . esc_html( $hint ) . '</p>';
	}
	if ( $items ) {
		tsvd_academy_render_lesson_grid( $items );
	} else {
		tsvd_academy_render_empty(
			__( 'In diesem Thema gibt es noch keine Schulung.', 'tsvd' ),
			__( 'Wähle oben ein anderes Thema oder nutze die Suche.', 'tsvd' )
		);
	}
	echo '</section>';
}
