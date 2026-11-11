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
		'/academy/(?P<id>\d+)',
		array(
			'methods'             => 'GET',
			'callback'            => 'tsvd_act_rest_lesson',
			'permission_callback' => $permission,
		)
	);
}

function tsvd_act_rest_index() {
	return rest_ensure_response(
		array(
			'topics'    => array_map( 'tsvd_act_topic', tsvd_academy_visible_topics() ),
			'lessons'   => array_map( 'tsvd_act_lesson_summary', tsvd_academy_visible_lessons() ),
			'canManage' => tsvd_academy_can_manage(),
			'newUrl'    => tsvd_act_new_url(),
		)
	);
}

function tsvd_act_rest_lesson( WP_REST_Request $request ) {
	$post = get_post( (int) $request['id'] );
	if ( ! $post || ! tsvd_academy_can_view( $post ) ) {
		return new WP_Error(
			'tsvd_act_not_found',
			__( 'Diese Anleitung gibt es nicht oder du darfst sie nicht sehen.', 'tsvd' ),
			array( 'status' => 404 )
		);
	}
	$toc = tsvd_academy_build_toc( apply_filters( 'the_content', $post->post_content ) );
	return rest_ensure_response(
		array_merge(
			tsvd_act_lesson_summary( $post ),
			array(
				'html'    => $toc['html'],
				'toc'     => $toc['items'],
				'editUrl' => tsvd_academy_can_manage() ? (string) get_edit_post_link( $post->ID, 'raw' ) : '',
			)
		)
	);
}

function tsvd_act_new_url() {
	if ( ! tsvd_academy_can_manage() ) {
		return '';
	}
	return admin_url( 'post-new.php?post_type=' . TSVD_ACADEMY_CPT );
}

function tsvd_act_topic( WP_Term $term ) {
	return array(
		'slug' => $term->slug,
		'name' => $term->name,
	);
}

function tsvd_act_lesson_summary( WP_Post $post ) {
	return array(
		'id'       => $post->ID,
		'title'    => get_the_title( $post ),
		'excerpt'  => wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post ) ), 30 ),
		'topics'   => array_map( 'tsvd_act_topic', tsvd_academy_lesson_topics( $post ) ),
		'modified' => mysql_to_rfc3339( $post->post_modified ),
	);
}
