<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_NEWSLETTER_BLOCKS_META = '_tsvd_newsletter_blocks';

function tsvd_newsletter_block_types() {
	return apply_filters( 'tsvd_newsletter_block_types', array() );
}

function tsvd_newsletter_block_type( $type ) {
	$types = tsvd_newsletter_block_types();

	return isset( $types[ $type ] ) ? $types[ $type ] : null;
}

function tsvd_newsletter_block_is_dynamic( $type ) {
	$definition = tsvd_newsletter_block_type( $type );

	return $definition && ! empty( $definition['dynamic'] );
}

function tsvd_newsletter_get_blocks( $post_id ) {
	$blocks = get_post_meta( $post_id, TSVD_NEWSLETTER_BLOCKS_META, true );

	return is_array( $blocks ) ? $blocks : array();
}

function tsvd_newsletter_save_blocks( $post_id, array $raw_blocks ) {
	$clean = array();

	foreach ( $raw_blocks as $block ) {
		$type = isset( $block['type'] ) ? sanitize_key( $block['type'] ) : '';
		$definition = tsvd_newsletter_block_type( $type );
		if ( ! $definition ) {
			continue;
		}

		$data = isset( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();
		if ( isset( $definition['sanitize'] ) && is_callable( $definition['sanitize'] ) ) {
			$data = (array) call_user_func( $definition['sanitize'], $data );
		}

		$clean[] = array(
			'id'   => ! empty( $block['id'] ) ? sanitize_key( $block['id'] ) : uniqid( 'blk_' ),
			'type' => $type,
			'data' => $data,
		);
	}

	update_post_meta( $post_id, TSVD_NEWSLETTER_BLOCKS_META, $clean );

	return $clean;
}

function tsvd_newsletter_render_block( array $block ) {
	$definition = tsvd_newsletter_block_type( isset( $block['type'] ) ? $block['type'] : '' );
	if ( ! $definition || ! is_callable( $definition['render'] ) ) {
		return '';
	}

	$data = isset( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();

	return (string) call_user_func( $definition['render'], $data, $block );
}

function tsvd_newsletter_render_blocks( $post_id ) {
	$html = '';
	foreach ( tsvd_newsletter_get_blocks( $post_id ) as $block ) {
		$html .= tsvd_newsletter_render_block( $block );
	}

	return $html;
}
