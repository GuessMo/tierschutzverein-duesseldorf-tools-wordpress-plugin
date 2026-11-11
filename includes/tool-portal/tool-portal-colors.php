<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_TOOL_PORTAL_HUES = array(
	'start'    => 172,
	'newsletter' => 15,
	'tiere'    => 45,
	'quellen'  => 85,
	'layout'   => 130,
	'skylark'  => 215,
	'academy'  => 255,
	'anfragen' => 300,
	'website'  => 350,
);

function tsvd_tool_portal_hue( $slug ) {
	return TSVD_TOOL_PORTAL_HUES[ $slug ] ?? TSVD_TOOL_PORTAL_HUES['start'];
}

function tsvd_tool_portal_oklch( $lightness, $chroma, $hue ) {
	return sprintf( 'oklch(%s %s %d)', $lightness, $chroma, $hue );
}

function tsvd_tool_portal_light_vars( $hue ) {
	$primary = tsvd_tool_portal_oklch( 0.52, 0.1, $hue );
	return array(
		'--primary'                    => $primary,
		'--primary-foreground'         => 'oklch(0.985 0 0)',
		'--primary-hover'              => tsvd_tool_portal_oklch( 0.46, 0.1, $hue ),
		'--primary-active'             => tsvd_tool_portal_oklch( 0.42, 0.1, $hue ),
		'--primary-subtle'             => tsvd_tool_portal_oklch( 0.94, 0.03, $hue ),
		'--primary-subtle-foreground'  => tsvd_tool_portal_oklch( 0.3, 0.08, $hue ),
		'--ring'                       => $primary,
		'--sidebar-primary'            => $primary,
		'--sidebar-primary-foreground' => 'oklch(0.985 0 0)',
		'--sidebar-ring'               => $primary,
	);
}

function tsvd_tool_portal_dark_vars( $hue ) {
	$primary    = tsvd_tool_portal_oklch( 0.74, 0.1, $hue );
	$foreground = tsvd_tool_portal_oklch( 0.18, 0.02, $hue );
	return array(
		'--primary'                    => $primary,
		'--primary-foreground'         => $foreground,
		'--primary-hover'              => tsvd_tool_portal_oklch( 0.8, 0.1, $hue ),
		'--primary-active'             => tsvd_tool_portal_oklch( 0.84, 0.09, $hue ),
		'--primary-subtle'             => tsvd_tool_portal_oklch( 0.32, 0.05, $hue ),
		'--primary-subtle-foreground'  => tsvd_tool_portal_oklch( 0.95, 0.02, $hue ),
		'--ring'                       => $primary,
		'--sidebar-primary'            => $primary,
		'--sidebar-primary-foreground' => $foreground,
		'--sidebar-ring'               => $primary,
	);
}

function tsvd_tool_portal_icon_vars( $lightness ) {
	$vars = array();
	foreach ( TSVD_TOOL_PORTAL_HUES as $slug => $hue ) {
		$vars[ '--tool-color-' . $slug ] = tsvd_tool_portal_oklch( $lightness, 0.1, $hue );
	}
	return $vars;
}

function tsvd_tool_portal_declarations( array $vars ) {
	$out = '';
	foreach ( $vars as $name => $value ) {
		$out .= $name . ':' . $value . ';';
	}
	return $out;
}

function tsvd_tool_portal_color_css( $slug ) {
	$hue   = tsvd_tool_portal_hue( $slug );
	$light = tsvd_tool_portal_light_vars( $hue ) + tsvd_tool_portal_icon_vars( 0.52 );
	$dark  = tsvd_tool_portal_dark_vars( $hue ) + tsvd_tool_portal_icon_vars( 0.74 );
	return 'html:root{' . tsvd_tool_portal_declarations( $light ) . '}'
		. 'html:root.dark{' . tsvd_tool_portal_declarations( $dark ) . '}';
}

function tsvd_tool_portal_icon_color( $slug ) {
	return 'var(--tool-color-' . $slug . ')';
}
