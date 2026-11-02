<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_architecture_map_icon( $name ) {
	return '<span class="tsvd-am-icon dashicons dashicons-' . esc_attr( $name ) . '" aria-hidden="true"></span>';
}

function tsvd_architecture_map_fill( $text ) {
	return str_replace( '{php}', PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION, $text );
}

function tsvd_architecture_map_list( array $items, $class ) {
	$html = '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $items as $item ) {
		$html .= '<li>' . esc_html( tsvd_architecture_map_fill( $item ) ) . '</li>';
	}
	return $html . '</ul>';
}

function tsvd_architecture_map_heading( $level, $text, $class = '' ) {
	$tag   = 'h' . (int) $level;
	$attrs = '' === $class ? '' : ' class="' . esc_attr( $class ) . '"';
	return '<' . $tag . $attrs . '>' . esc_html( $text ) . '</' . $tag . '>';
}

function tsvd_architecture_map_section( $title, $hint, $body, $class ) {
	return '<section class="tsvd-am-section ' . esc_attr( $class ) . '">'
		. tsvd_architecture_map_heading( 2, $title )
		. '<p class="tsvd-am-hint">' . esc_html( $hint ) . '</p>'
		. $body
		. '</section>';
}
