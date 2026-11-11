<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_act_md_dir() {
	return TSVD_TOOLS_DIR . 'academy/';
}

function tsvd_act_md_value( $raw ) {
	$raw = trim( $raw );
	if ( str_starts_with( $raw, '[' ) && str_ends_with( $raw, ']' ) ) {
		$items = array_map( 'trim', explode( ',', substr( $raw, 1, -1 ) ) );
		return array_values( array_filter( $items, 'strlen' ) );
	}
	if ( str_starts_with( $raw, '"' ) ) {
		$decoded = json_decode( $raw );
		return is_string( $decoded ) ? $decoded : trim( $raw, '"' );
	}
	return $raw;
}

function tsvd_act_md_parse( $path ) {
	$text = (string) file_get_contents( $path );
	if ( ! preg_match( '/\A---\R(.*?)\R---\R?(.*)\z/s', $text, $parts ) ) {
		return null;
	}
	$meta = array();
	foreach ( preg_split( '/\R/', $parts[1] ) as $line ) {
		if ( preg_match( '/^([a-z_]+):\s*(.*)$/', $line, $match ) ) {
			$meta[ $match[1] ] = tsvd_act_md_value( $match[2] );
		}
	}
	return array(
		'slug'     => basename( $path, '.md' ),
		'title'    => (string) ( $meta['title'] ?? basename( $path, '.md' ) ),
		'topics'   => (array) ( $meta['topics'] ?? array() ),
		'audience' => 'admin' === ( $meta['audience'] ?? 'all' ) ? 'admin' : 'all',
		'updated'  => (string) ( $meta['updated'] ?? '' ),
		'body'     => ltrim( $parts[2] ),
	);
}

function tsvd_act_md_lessons() {
	static $lessons = null;
	if ( null !== $lessons ) {
		return $lessons;
	}
	$lessons = array();
	foreach ( glob( tsvd_act_md_dir() . 'lessons/*.md' ) ?: array() as $path ) {
		$lesson = tsvd_act_md_parse( $path );
		if ( $lesson ) {
			$lessons[ $lesson['slug'] ] = $lesson;
		}
	}
	uasort(
		$lessons,
		function ( $a, $b ) {
			return strcasecmp( $a['title'], $b['title'] );
		}
	);
	return $lessons;
}

function tsvd_act_md_topics() {
	$path = tsvd_act_md_dir() . 'topics.json';
	if ( ! is_readable( $path ) ) {
		return array();
	}
	$all = json_decode( (string) file_get_contents( $path ), true );
	return is_array( $all ) ? $all : array();
}

function tsvd_act_md_can_view( array $lesson ) {
	if ( 'admin' === $lesson['audience'] ) {
		return current_user_can( TSVD_ACADEMY_EDIT_CAP );
	}
	return current_user_can( 'read' );
}

function tsvd_act_md_excerpt( $body ) {
	foreach ( preg_split( '/\R{2,}/', $body ) as $block ) {
		$block = trim( $block );
		if ( '' === $block || preg_match( '/^(#|:::|!\[|```|-|\d+\.)/', $block ) ) {
			continue;
		}
		$plain = preg_replace( '/[*_`\[\]]|\(https?:[^)]*\)/', '', $block );
		return html_entity_decode( wp_trim_words( $plain, 30 ), ENT_QUOTES );
	}
	return '';
}
