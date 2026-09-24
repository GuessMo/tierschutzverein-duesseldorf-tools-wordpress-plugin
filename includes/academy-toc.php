<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ACADEMY_TOC_MIN_ITEMS = 2;

function tsvd_academy_build_toc( $html ) {
	$items = array();
	$html  = preg_replace_callback(
		'/<h2([^>]*)>(.*?)<\/h2>/is',
		function ( $match ) use ( &$items ) {
			$text    = wp_strip_all_tags( $match[2] );
			$id      = 'abschnitt-' . ( count( $items ) + 1 ) . '-' . sanitize_title( $text );
			$items[] = array(
				'id'   => $id,
				'text' => $text,
			);
			$attrs = preg_replace( '/\sid="[^"]*"/i', '', $match[1] );
			return '<h2' . $attrs . ' id="' . esc_attr( $id ) . '">' . $match[2] . '</h2>';
		},
		$html
	);
	return array(
		'html'  => $html,
		'items' => $items,
	);
}

function tsvd_academy_render_toc( $items ) {
	if ( count( $items ) < TSVD_ACADEMY_TOC_MIN_ITEMS ) {
		return;
	}
	echo '<aside class="tsvd-ac-toc" aria-labelledby="tsvd-ac-toc-title">';
	echo '<h2 id="tsvd-ac-toc-title" class="tsvd-ac-toc__title">' . esc_html__( 'Auf dieser Seite', 'tsvd' ) . '</h2>';
	echo '<ol class="tsvd-ac-toc__list">';
	foreach ( $items as $item ) {
		echo '<li><a href="#' . esc_attr( $item['id'] ) . '">' . esc_html( $item['text'] ) . '</a></li>';
	}
	echo '</ol></aside>';
}
