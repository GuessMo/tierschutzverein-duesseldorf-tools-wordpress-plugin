<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ACADEMY_RECENT_LIMIT = 5;

function tsvd_academy_render_dashboard() {
	$lessons = tsvd_academy_visible_lessons();
	$topics  = tsvd_academy_visible_topics();
	$hint    = __( 'Schulungen und Anleitungen für alle Mitarbeitenden. Wähle ein Thema und öffne eine Schulung.', 'tsvd' );

	echo '<div class="wrap tsvd-dash">';
	tsvd_academy_render_heading( __( 'Academy', 'tsvd' ) );
	echo '<p class="tsvd-dash-hint">' . esc_html( $hint ) . '</p>';
	tsvd_academy_render_kpis( count( $lessons ), count( $topics ) );
	echo '<div class="tsvd-grid">';
	tsvd_academy_render_recent_card( $lessons );
	foreach ( $topics as $topic ) {
		tsvd_academy_render_topic_card( $topic, tsvd_academy_lessons_in_topic( $lessons, $topic ) );
	}
	echo '</div></div>';
}

function tsvd_academy_render_heading( $title ) {
	echo '<h1 class="wp-heading-inline">' . esc_html( $title ) . '</h1>';
	if ( tsvd_academy_can_manage() ) {
		$url = admin_url( 'post-new.php?post_type=' . TSVD_ACADEMY_CPT );
		echo ' <a href="' . esc_url( $url ) . '" class="page-title-action">'
			. esc_html__( 'Neue Schulung', 'tsvd' ) . '</a>';
	}
	echo '<hr class="wp-header-end">';
}

function tsvd_academy_render_kpis( $lesson_count, $topic_count ) {
	$kpis = array(
		array( $lesson_count, __( 'Schulungen', 'tsvd' ) ),
		array( $topic_count, __( 'Themen', 'tsvd' ) ),
	);
	echo '<div class="tsvd-kpis">';
	foreach ( $kpis as $kpi ) {
		echo '<div class="tsvd-kpi"><div class="tsvd-kpi__val">' . (int) $kpi[0] . '</div>'
			. '<div class="tsvd-kpi__lbl">' . esc_html( $kpi[1] ) . '</div></div>';
	}
	echo '</div>';
}

function tsvd_academy_render_recent_card( $lessons ) {
	usort(
		$lessons,
		function ( $a, $b ) {
			return strcmp( $b->post_modified_gmt, $a->post_modified_gmt );
		}
	);
	$recent = array_slice( $lessons, 0, TSVD_ACADEMY_RECENT_LIMIT );
	tsvd_academy_render_card( __( 'Zuletzt aktualisiert', 'tsvd' ), '', $recent );
}

function tsvd_academy_render_topic_card( $topic, $lessons ) {
	$hint = TSVD_ACADEMY_ADMIN_TERM === $topic->slug
		? __( 'Nur für Admins sichtbar.', 'tsvd' )
		: $topic->description;
	tsvd_academy_render_card( $topic->name, $hint, $lessons );
}

function tsvd_academy_render_card( $title, $hint, $lessons ) {
	echo '<div class="tsvd-card"><h3>' . esc_html( $title ) . ' (' . count( $lessons ) . ')</h3>';
	if ( $hint ) {
		echo '<p class="description">' . esc_html( $hint ) . '</p>';
	}
	tsvd_academy_render_lesson_list( $lessons );
	echo '</div>';
}

function tsvd_academy_render_lesson_list( $lessons ) {
	if ( ! $lessons ) {
		echo '<p class="tsvd-empty">' . esc_html__( 'Noch keine Schulungen.', 'tsvd' ) . '</p>';
		return;
	}
	echo '<ul>';
	foreach ( $lessons as $lesson ) {
		echo '<li><a href="' . esc_url( tsvd_academy_lesson_url( $lesson->ID ) ) . '"><strong>'
			. esc_html( get_the_title( $lesson ) ) . '</strong></a>';
		if ( has_excerpt( $lesson ) ) {
			echo '<br><span class="description">' . esc_html( get_the_excerpt( $lesson ) ) . '</span>';
		}
		echo '</li>';
	}
	echo '</ul>';
}
