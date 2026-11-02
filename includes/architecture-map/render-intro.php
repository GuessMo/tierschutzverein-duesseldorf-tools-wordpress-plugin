<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_architecture_map_render_intro( array $intro ) {
	$compare = '';
	foreach ( $intro['compare'] as $side ) {
		$compare .= tsvd_architecture_map_render_compare_side( $side );
	}
	return '<section class="tsvd-am-section tsvd-am-intro">'
		. tsvd_architecture_map_heading( 2, $intro['title'] )
		. '<p class="tsvd-am-lead">' . esc_html( $intro['lead'] ) . '</p>'
		. '<div class="tsvd-am-compare">' . $compare . '</div>'
		. tsvd_architecture_map_render_safety( $intro['safety'] )
		. tsvd_architecture_map_render_standards( $intro['standards'] )
		. '</section>';
}

function tsvd_architecture_map_render_compare_side( array $side ) {
	return '<div class="tsvd-am-compare__side tsvd-am-compare__side--' . esc_attr( $side['tone'] ) . '">'
		. '<h3 class="tsvd-am-compare__title">' . tsvd_architecture_map_icon( $side['icon'] )
		. esc_html( $side['title'] ) . '</h3>'
		. tsvd_architecture_map_list( $side['points'], 'tsvd-am-compare__list' )
		. '</div>';
}

function tsvd_architecture_map_render_safety( array $safety ) {
	$items = '';
	foreach ( $safety['items'] as $item ) {
		$items .= '<li class="tsvd-am-safety__item">' . tsvd_architecture_map_icon( $item['icon'] )
			. '<span class="tsvd-am-safety__title">' . esc_html( $item['title'] ) . '</span>'
			. '<span class="tsvd-am-safety__text">' . esc_html( $item['text'] ) . '</span></li>';
	}
	return '<div class="tsvd-am-safety">'
		. tsvd_architecture_map_heading( 3, $safety['title'], 'tsvd-am-subtitle' )
		. '<ul class="tsvd-am-safety__list">' . $items . '</ul></div>';
}

function tsvd_architecture_map_render_standards( array $standards ) {
	$chips = '';
	foreach ( $standards['chips'] as $chip ) {
		$chips .= '<li>' . esc_html( $chip ) . '</li>';
	}
	return '<div class="tsvd-am-standards">'
		. tsvd_architecture_map_heading( 3, $standards['title'], 'tsvd-am-subtitle' )
		. '<p>' . esc_html( $standards['text'] ) . '</p>'
		. '<ul class="tsvd-am-standards__chips">' . $chips . '</ul></div>';
}
