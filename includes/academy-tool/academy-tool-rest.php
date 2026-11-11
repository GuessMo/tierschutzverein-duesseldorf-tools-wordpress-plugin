<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'rest_api_init', 'tsvd_act_register_routes' );

function tsvd_act_register_routes() {
	$permission = function () {
		return is_user_logged_in() && current_user_can( 'read' );
	};
	register_rest_route(
		'tsvd-tools/v1',
		'/academy',
		array(
			'methods'             => 'GET',
			'callback'            => 'tsvd_act_rest_index',
			'permission_callback' => $permission,
		)
	);
	register_rest_route(
		'tsvd-tools/v1',
		'/academy/(?P<slug>[a-z0-9-]+)',
		array(
			'methods'             => 'GET',
			'callback'            => 'tsvd_act_rest_lesson',
			'permission_callback' => $permission,
		)
	);
}

function tsvd_act_visible_lessons() {
	return array_values( array_filter( tsvd_act_md_lessons(), 'tsvd_act_md_can_view' ) );
}

function tsvd_act_visible_topics() {
	$is_manager = current_user_can( TSVD_ACADEMY_EDIT_CAP );
	return array_values(
		array_filter(
			tsvd_act_md_topics(),
			function ( $topic ) use ( $is_manager ) {
				return $is_manager || TSVD_ACADEMY_ADMIN_TERM !== $topic['slug'];
			}
		)
	);
}

function tsvd_act_rest_index() {
	return rest_ensure_response(
		array(
			'topics'  => tsvd_act_visible_topics(),
			'lessons' => array_map( 'tsvd_act_lesson_summary', tsvd_act_visible_lessons() ),
		)
	);
}

function tsvd_act_rest_lesson( WP_REST_Request $request ) {
	$lessons = tsvd_act_md_lessons();
	$lesson  = $lessons[ (string) $request['slug'] ] ?? null;
	if ( ! $lesson || ! tsvd_act_md_can_view( $lesson ) ) {
		return new WP_Error(
			'tsvd_act_not_found',
			__( 'Diese Anleitung gibt es nicht oder du darfst sie nicht sehen.', 'tsvd' ),
			array( 'status' => 404 )
		);
	}
	return rest_ensure_response(
		array_merge(
			tsvd_act_lesson_summary( $lesson ),
			array(
				'markdown' => $lesson['body'],
				'blocks'   => tsvd_act_lesson_blocks( $lesson['body'] ),
			)
		)
	);
}

function tsvd_act_lesson_blocks( $body ) {
	$blocks = array();
	if ( str_contains( $body, ':::architecture-map' ) ) {
		$blocks['architecture-map'] = render_block(
			array(
				'blockName' => 'tsvd/architecture-map',
				'attrs'     => array(),
			)
		);
	}
	return $blocks;
}

function tsvd_act_topic_lookup() {
	$map = array();
	foreach ( tsvd_act_md_topics() as $topic ) {
		$map[ $topic['slug'] ] = $topic;
	}
	return $map;
}

function tsvd_act_lesson_summary( array $lesson ) {
	$lookup = tsvd_act_topic_lookup();
	$topics = array();
	foreach ( $lesson['topics'] as $slug ) {
		$topics[] = $lookup[ $slug ] ?? array(
			'slug' => $slug,
			'name' => $slug,
		);
	}
	return array(
		'slug'    => $lesson['slug'],
		'title'   => $lesson['title'],
		'excerpt' => tsvd_act_md_excerpt( $lesson['body'] ),
		'topics'  => $topics,
		'updated' => $lesson['updated'],
	);
}
