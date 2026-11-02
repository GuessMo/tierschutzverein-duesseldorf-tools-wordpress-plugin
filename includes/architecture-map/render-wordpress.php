<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_architecture_map_render_wordpress( array $texts ) {
	$groups = tsvd_architecture_map_wp_group( $texts['versions'], tsvd_architecture_map_render_versions() )
		. tsvd_architecture_map_wp_group(
			$texts['theme'],
			'<p>' . esc_html( tsvd_architecture_map_theme_label() ) . '</p>'
		)
		. tsvd_architecture_map_wp_group(
			$texts['plugins'],
			tsvd_architecture_map_render_plugins( $texts['plugin_texts'] ),
			'tsvd-am-wp__group--wide'
		)
		. tsvd_architecture_map_wp_group(
			$texts['public_types'],
			tsvd_architecture_map_list( tsvd_architecture_map_post_type_labels( true ), 'tsvd-am-wp__list' )
		)
		. tsvd_architecture_map_wp_group(
			$texts['internal_types'],
			tsvd_architecture_map_list( tsvd_architecture_map_post_type_labels( false ), 'tsvd-am-wp__list' )
		)
		. tsvd_architecture_map_wp_group(
			$texts['roles'],
			tsvd_architecture_map_list( tsvd_architecture_map_role_labels(), 'tsvd-am-wp__list' )
		);
	return tsvd_architecture_map_section(
		$texts['title'],
		$texts['lead'],
		'<div class="tsvd-am-wp">' . $groups . '</div>',
		'tsvd-am-wordpress'
	);
}

function tsvd_architecture_map_wp_group( $title, $body, $modifier = '' ) {
	return '<div class="tsvd-am-wp__group ' . esc_attr( $modifier ) . '">'
		. tsvd_architecture_map_heading( 3, $title, 'tsvd-am-wp__title' )
		. $body . '</div>';
}

function tsvd_architecture_map_render_versions() {
	$rows = '';
	foreach ( tsvd_architecture_map_versions() as $label => $version ) {
		$rows .= '<div><dt>' . esc_html( $label ) . '</dt><dd>' . esc_html( $version ) . '</dd></div>';
	}
	return '<dl class="tsvd-am-wp__versions">' . $rows . '</dl>';
}

function tsvd_architecture_map_render_plugins( array $plugin_texts ) {
	$items = '';
	foreach ( tsvd_architecture_map_active_plugins() as $plugin ) {
		$name        = $plugin['Name'];
		$description = $plugin_texts[ $name ] ?? wp_strip_all_tags( $plugin['Description'] );
		$items      .= '<li><span class="tsvd-am-wp__plugin">' . esc_html( $name )
			. ' <span class="tsvd-am-wp__version">' . esc_html( $plugin['Version'] ) . '</span></span>'
			. '<span class="tsvd-am-wp__desc">' . esc_html( $description ) . '</span></li>';
	}
	return '<ul class="tsvd-am-wp__plugins">' . $items . '</ul>';
}
