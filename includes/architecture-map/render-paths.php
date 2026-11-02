<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_architecture_map_render_paths( array $paths, array $nodes ) {
	$picker = '';
	$panels = '';
	foreach ( $paths['items'] as $path ) {
		$picker .= '<button type="button" class="tsvd-am-chip" data-path="' . esc_attr( $path['id'] ) . '"'
			. ' aria-pressed="false">' . esc_html( $path['title'] ) . '</button>';
		$panels .= tsvd_architecture_map_render_path( $path, $paths, $nodes );
	}
	$body = '<div class="tsvd-am-paths" data-step-label="' . esc_attr( $paths['step'] ) . '">'
		. '<div class="tsvd-am-paths__picker" role="group" aria-label="' . esc_attr( $paths['title'] ) . '">'
		. $picker . '</div>' . $panels . '</div>';
	return tsvd_architecture_map_section( $paths['title'], $paths['hint'], $body, 'tsvd-am-walks' );
}

function tsvd_architecture_map_render_path( array $path, array $paths, array $nodes ) {
	$steps = '';
	foreach ( $path['steps'] as $step ) {
		$steps .= '<li class="tsvd-am-step" data-node="' . esc_attr( $step['node'] ) . '">'
			. '<span class="tsvd-am-step__node">' . esc_html( $nodes[ $step['node'] ]['title'] ) . '</span> '
			. '<span class="tsvd-am-step__text">' . esc_html( $step['text'] ) . '</span></li>';
	}
	return '<div class="tsvd-am-path" data-path-panel="' . esc_attr( $path['id'] ) . '" data-index="0">'
		. tsvd_architecture_map_heading( 3, $path['title'], 'tsvd-am-path__title' )
		. '<ol class="tsvd-am-path__steps">' . $steps . '</ol>'
		. tsvd_architecture_map_render_path_nav( $paths )
		. '</div>';
}

function tsvd_architecture_map_render_path_nav( array $paths ) {
	return '<div class="tsvd-am-path__nav">'
		. '<button type="button" class="button" data-step="previous">' . esc_html( $paths['previous'] ) . '</button>'
		. '<span class="tsvd-am-path__count" aria-live="polite"></span>'
		. '<button type="button" class="button button-primary" data-step="next">' . esc_html( $paths['next'] ) . '</button>'
		. '</div>';
}
