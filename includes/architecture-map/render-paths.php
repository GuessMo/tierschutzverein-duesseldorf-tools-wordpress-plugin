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
	$dots  = '';
	foreach ( $path['steps'] as $step ) {
		$node   = $nodes[ $step['node'] ];
		$steps .= '<li class="tsvd-am-step" data-node="' . esc_attr( $step['node'] ) . '">'
			. '<span class="tsvd-am-step__node">' . tsvd_architecture_map_icon( $node['icon'] )
			. esc_html( $node['title'] ) . '</span> '
			. '<span class="tsvd-am-step__text">' . esc_html( $step['text'] ) . '</span></li>';
		$dots  .= '<li class="tsvd-am-path__dot"></li>';
	}
	return '<div class="tsvd-am-path" data-path-panel="' . esc_attr( $path['id'] ) . '" data-index="0">'
		. tsvd_architecture_map_heading( 3, $path['title'], 'tsvd-am-path__title' )
		. '<ol class="tsvd-am-path__steps">' . $steps . '</ol>'
		. tsvd_architecture_map_render_path_nav( $paths, $dots )
		. '</div>';
}

function tsvd_architecture_map_render_path_nav( array $paths, $dots ) {
	return '<div class="tsvd-am-path__nav">'
		. '<button type="button" class="button" data-step="previous">' . esc_html( $paths['previous'] ) . '</button>'
		. '<ol class="tsvd-am-path__dots" aria-hidden="true">' . $dots . '</ol>'
		. '<span class="tsvd-am-path__count" aria-live="polite"></span>'
		. '<button type="button" class="button button-primary" data-step="next">' . esc_html( $paths['next'] ) . '</button>'
		. '</div>';
}
