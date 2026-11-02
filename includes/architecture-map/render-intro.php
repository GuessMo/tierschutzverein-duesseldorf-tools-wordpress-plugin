<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_architecture_map_render_intro( array $intro ) {
	$cards = '';
	foreach ( $intro['cards'] as $card ) {
		$cards .= tsvd_architecture_map_render_compare_card( $card );
	}
	return '<section class="tsvd-am-section tsvd-am-intro">'
		. tsvd_architecture_map_heading( 2, $intro['title'] )
		. '<p class="tsvd-am-lead">' . esc_html( $intro['lead'] ) . '</p>'
		. '<div class="tsvd-am-compare">' . $cards . '</div>'
		. tsvd_architecture_map_render_standards( $intro['standards'] )
		. '</section>';
}

function tsvd_architecture_map_render_compare_card( array $card ) {
	return '<div class="tsvd-am-compare__card tsvd-am-compare__card--' . esc_attr( $card['tone'] ) . '">'
		. '<h3 class="tsvd-am-compare__title">' . tsvd_architecture_map_icon( $card['icon'] )
		. esc_html( $card['title'] ) . '</h3>'
		. tsvd_architecture_map_list( $card['points'], 'tsvd-am-compare__list' )
		. '</div>';
}

function tsvd_architecture_map_render_standards( array $standards ) {
	return '<div class="tsvd-am-standards">'
		. '<h3 class="tsvd-am-standards__title">' . tsvd_architecture_map_icon( 'admin-site-alt3' )
		. esc_html( $standards['title'] ) . '</h3>'
		. '<p>' . esc_html( $standards['text'] ) . '</p>'
		. tsvd_architecture_map_list( $standards['items'], 'tsvd-am-standards__list' )
		. '</div>';
}
