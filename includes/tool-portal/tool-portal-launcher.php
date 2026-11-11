<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_TOOL_PORTAL_WEBSITE_CAP = 'edit_posts';

function tsvd_tool_portal_launcher_items() {
	$items = array();
	if ( current_user_can( TSVD_TOOL_PORTAL_WEBSITE_CAP ) ) {
		$items[] = tsvd_tool_portal_website_item();
	}
	foreach ( tsvd_tool_portal_tools() as $slug => $tool ) {
		if ( current_user_can( tsvd_tool_portal_cap( $slug ) ) ) {
			$items[] = tsvd_tool_portal_launcher_item( $slug, $tool );
		}
	}
	return $items;
}

function tsvd_tool_portal_website_item() {
	return array(
		'slug'   => 'website',
		'title'  => __( 'Website', 'tsvd' ),
		'url'    => admin_url(),
		'symbol' => 'world',
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
