<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/help-texts.php';

function tsvd_help_editor_enqueue( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || ! $screen->post_type ) {
		return;
	}
	$targets = tsvd_help_editor_targets( $screen->post_type );
	if ( ! $targets ) {
		return;
	}
	tsvd_help_enqueue( $targets );
}
add_action( 'admin_enqueue_scripts', 'tsvd_help_editor_enqueue' );

function tsvd_help_box( $box_id, $text ) {
	return array(
		'selector' => '#' . $box_id . ' > .postbox-header h2',
		'text'     => $text,
	);
}

function tsvd_help_heading( $text ) {
	return array(
		'selector' => '.wrap > h1.wp-heading-inline',
		'text'     => $text,
	);
}

function tsvd_help_label( $selector, $label, $text ) {
	return array(
		'selector' => $selector,
		'label'    => $label,
		'text'     => $text,
	);
}
