<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_nlt_issue_summary( WP_Post $post ) {
	$sent_at = (int) get_post_meta( $post->ID, TSVD_NEWSLETTER_SENT_META, true );
	return array(
		'id'         => $post->ID,
		'title'      => $post->post_title,
		'sentAt'     => $sent_at ? gmdate( 'c', $sent_at ) : '',
		'modified'   => mysql_to_rfc3339( $post->post_modified ),
		'blockCount' => count( tsvd_newsletter_get_blocks( $post->ID ) ),
	);
}

function tsvd_nlt_block_payload( array $block ) {
	$data = $block['data'] ?? array();
	if ( 'image' === $block['type'] && ! empty( $data['id'] ) ) {
		$data['url'] = (string) wp_get_attachment_image_url( (int) $data['id'], 'medium_large' );
	}
	return array(
		'id'   => (string) $block['id'],
		'type' => (string) $block['type'],
		'data' => (object) $data,
	);
}

function tsvd_nlt_issue_detail( WP_Post $post ) {
	return tsvd_nlt_issue_summary( $post ) + array(
		'blocks' => array_map( 'tsvd_nlt_block_payload', tsvd_newsletter_get_blocks( $post->ID ) ),
	);
}

function tsvd_nlt_call( $callback ) {
	return is_callable( $callback ) ? (string) call_user_func( $callback ) : '';
}

function tsvd_nlt_block_types() {
	$out = array();
	foreach ( tsvd_newsletter_block_types() as $type => $definition ) {
		$admin = $definition['admin'] ?? array();
		$out[] = array(
			'type'        => $type,
			'label'       => (string) $definition['label'],
			'dynamic'     => ! empty( $definition['dynamic'] ),
			'description' => (string) ( $admin['description'] ?? '' ),
			'status'      => tsvd_nlt_call( $admin['status'] ?? null ),
			'manageUrl'   => tsvd_nlt_call( $admin['manage_url'] ?? null ),
		);
	}
	return $out;
}

function tsvd_nlt_rest_list() {
	$posts = get_posts(
		array(
			'post_type'      => TSVD_NEWSLETTER_CPT,
			'post_status'    => array( 'publish', 'draft', 'private', 'future', 'pending' ),
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	return rest_ensure_response(
		array(
			'issues'          => array_map( 'tsvd_nlt_issue_summary', $posts ),
			'subscriberCount' => count( tsvd_newsletter_subscriber_ids() ),
			'blockTypes'      => tsvd_nlt_block_types(),
		)
	);
}

function tsvd_nlt_rest_create( WP_REST_Request $request ) {
	$title = sanitize_text_field( (string) $request->get_param( 'title' ) );
	$id    = wp_insert_post(
		array(
			'post_type'   => TSVD_NEWSLETTER_CPT,
			'post_status' => 'draft',
			'post_title'  => '' !== $title ? $title : __( 'Neuer Newsletter', 'tsvd' ),
		),
		true
	);
	if ( is_wp_error( $id ) ) {
		return tsvd_nlt_error( 'create_failed', $id->get_error_message(), 500 );
	}
	return rest_ensure_response( tsvd_nlt_issue_detail( get_post( $id ) ) );
}

function tsvd_nlt_rest_get( WP_REST_Request $request ) {
	$post = tsvd_nlt_issue_post( $request );
	return is_wp_error( $post ) ? $post : rest_ensure_response( tsvd_nlt_issue_detail( $post ) );
}

function tsvd_nlt_rest_update( WP_REST_Request $request ) {
	$post = tsvd_nlt_issue_post( $request );
	if ( is_wp_error( $post ) ) {
		return $post;
	}
	$title = $request->get_param( 'title' );
	if ( null !== $title ) {
		wp_update_post(
			array(
				'ID'         => $post->ID,
				'post_title' => sanitize_text_field( (string) $title ),
			)
		);
	}
	$blocks = $request->get_param( 'blocks' );
	if ( is_array( $blocks ) ) {
		tsvd_newsletter_save_blocks( $post->ID, array_values( $blocks ) );
	}
	return rest_ensure_response( tsvd_nlt_issue_detail( get_post( $post->ID ) ) );
}

function tsvd_nlt_rest_delete( WP_REST_Request $request ) {
	$post = tsvd_nlt_issue_post( $request );
	if ( is_wp_error( $post ) ) {
		return $post;
	}
	wp_trash_post( $post->ID );
	return rest_ensure_response( array( 'deleted' => true ) );
}
