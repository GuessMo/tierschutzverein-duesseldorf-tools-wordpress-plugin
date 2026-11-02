<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ARCHITECTURE_MAP_QUESTION_KEYS = array( 'what', 'why', 'where', 'outage' );

function tsvd_architecture_map_render_map( array $content ) {
	$zones = '';
	foreach ( $content['zones'] as $zone ) {
		$zones .= tsvd_architecture_map_render_zone( $zone, $content['nodes'] );
	}
	$body = '<div class="tsvd-am-map">' . $zones . '</div>'
		. tsvd_architecture_map_render_panels( $content['nodes'], $content['questions'] );
	return tsvd_architecture_map_section(
		$content['map']['title'],
		$content['map']['hint'],
		$body,
		'tsvd-am-overview'
	);
}

function tsvd_architecture_map_render_zone( array $zone, array $nodes ) {
	$title_id = 'tsvd-am-zone-' . $zone['id'];
	$items    = '';
	foreach ( $zone['nodes'] as $node_id ) {
		$items .= '<li>' . tsvd_architecture_map_render_node( $node_id, $nodes[ $node_id ] ) . '</li>';
	}
	return '<div class="tsvd-am-zone tsvd-am-zone--' . esc_attr( $zone['id'] ) . '" role="group"'
		. ' aria-labelledby="' . esc_attr( $title_id ) . '">'
		. '<h3 class="tsvd-am-zone__title" id="' . esc_attr( $title_id ) . '">' . esc_html( $zone['title'] ) . '</h3>'
		. '<ul class="tsvd-am-zone__nodes">' . $items . '</ul>'
		. '</div>';
}

function tsvd_architecture_map_render_node( $node_id, array $node ) {
	return '<button type="button" class="tsvd-am-node" data-node="' . esc_attr( $node_id ) . '"'
		. ' aria-controls="tsvd-am-panel-' . esc_attr( $node_id ) . '" aria-expanded="false">'
		. tsvd_architecture_map_icon( $node['icon'] )
		. '<span class="tsvd-am-node__text">'
		. '<span class="tsvd-am-node__title">' . esc_html( $node['title'] ) . '</span>'
		. '<span class="tsvd-am-node__short">' . esc_html( $node['short'] ) . '</span>'
		. '</span></button>';
}

function tsvd_architecture_map_render_panels( array $nodes, array $questions ) {
	$panels = '';
	foreach ( $nodes as $node_id => $node ) {
		$panels .= tsvd_architecture_map_render_panel( $node_id, $node, $questions );
	}
	return '<div class="tsvd-am-panels">' . $panels . '</div>';
}

function tsvd_architecture_map_render_panel( $node_id, array $node, array $questions ) {
	$facts = '';
	foreach ( TSVD_ARCHITECTURE_MAP_QUESTION_KEYS as $key ) {
		$facts .= '<div class="tsvd-am-fact"><dt>' . esc_html( $questions[ $key ] ) . '</dt>'
			. '<dd>' . esc_html( $node[ $key ] ) . '</dd></div>';
	}
	return '<section class="tsvd-am-panel" id="tsvd-am-panel-' . esc_attr( $node_id ) . '"'
		. ' data-panel="' . esc_attr( $node_id ) . '">'
		. '<h3 class="tsvd-am-panel__title">' . tsvd_architecture_map_icon( $node['icon'] )
		. esc_html( $node['title'] ) . '</h3>'
		. '<dl class="tsvd-am-panel__facts">' . $facts . '</dl>'
		. '</section>';
}
