<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_tool_portal_admin_links() {
	return array(
		'website' => array(
			'title'  => __( 'Website', 'tsvd' ),
			'page'   => '',
			'symbol' => 'world',
			'cap'    => 'edit_posts',
		),
		'skylark' => array(
			'title'  => __( 'Skylark', 'tsvd' ),
			'page'   => 'skylark-analytics',
			'symbol' => 'chart',
			'cap'    => 'manage_options',
		),
		'academy' => array(
			'title'  => __( 'Academy', 'tsvd' ),
			'page'   => 'tsvd-academy',
			'symbol' => 'school',
			'cap'    => 'read',
		),
	);
}

function tsvd_tool_portal_launcher_items() {
	$items = array();
	foreach ( tsvd_tool_portal_admin_links() as $slug => $link ) {
		if ( current_user_can( $link['cap'] ) ) {
			$items[] = tsvd_tool_portal_admin_link_item( $slug, $link );
		}
	}
	foreach ( tsvd_tool_portal_tools() as $slug => $tool ) {
		if ( current_user_can( tsvd_tool_portal_cap( $slug ) ) ) {
			$items[] = tsvd_tool_portal_launcher_item( $slug, $tool );
		}
	}
	return $items;
}

function tsvd_tool_portal_admin_link_item( $slug, $link ) {
	$url = '' === $link['page'] ? admin_url() : admin_url( 'admin.php?page=' . $link['page'] );
	return array(
		'slug'   => $slug,
		'title'  => $link['title'],
		'url'    => $url,
		'symbol' => $link['symbol'],
	);
}

function tsvd_tool_portal_launcher_item( $slug, $tool ) {
	return array(
		'slug'   => $slug,
		'title'  => $tool['title'],
		'url'    => tsvd_tool_portal_url( $slug ),
		'symbol' => $tool['symbol'] ?? 'apps',
	);
}

function tsvd_tool_portal_initials( $name ) {
	$parts = preg_split( '/\s+/', trim( (string) $name ) ) ?: array();
	$first = mb_substr( $parts[0] ?? '', 0, 1 );
	$last  = count( $parts ) > 1 ? mb_substr( (string) end( $parts ), 0, 1 ) : '';
	return mb_strtoupper( $first . $last );
}
