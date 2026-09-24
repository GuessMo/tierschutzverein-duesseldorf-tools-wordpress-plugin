<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ACADEMY_PAGE = 'tsvd-academy';

add_action( 'admin_menu', 'tsvd_academy_admin_menu' );
add_filter( 'parent_file', 'tsvd_academy_parent_file' );
add_filter( 'tsvd_role_manager_always_allowed_menus', 'tsvd_academy_allow_menu' );

function tsvd_academy_admin_menu() {
	if ( ! tsvd_tools_theme_active() ) {
		return;
	}
	$hook = add_menu_page(
		__( 'Academy', 'tsvd' ),
		__( 'Academy', 'tsvd' ),
		'read',
		TSVD_ACADEMY_PAGE,
		'tsvd_academy_render_page',
		'dashicons-welcome-learn-more',
		3
	);
	add_submenu_page(
		TSVD_ACADEMY_PAGE,
		__( 'Übersicht', 'tsvd' ),
		__( 'Übersicht', 'tsvd' ),
		'read',
		TSVD_ACADEMY_PAGE,
		'tsvd_academy_render_page'
	);
	tsvd_academy_add_manage_submenus();
	add_action( 'load-' . $hook, 'tsvd_academy_guard_lesson' );
	add_action( 'admin_print_styles-' . $hook, 'tsvd_academy_enqueue_assets' );
}

function tsvd_academy_add_manage_submenus() {
	$topics_slug = 'edit-tags.php?taxonomy=' . TSVD_ACADEMY_TAX . '&post_type=' . TSVD_ACADEMY_CPT;
	$items       = array(
		'edit.php?post_type=' . TSVD_ACADEMY_CPT     => __( 'Alle Schulungen', 'tsvd' ),
		'post-new.php?post_type=' . TSVD_ACADEMY_CPT => __( 'Neue Schulung', 'tsvd' ),
		$topics_slug                                 => __( 'Themen', 'tsvd' ),
	);
	foreach ( $items as $slug => $label ) {
		add_submenu_page( TSVD_ACADEMY_PAGE, $label, $label, TSVD_ACADEMY_EDIT_CAP, $slug );
	}
}

function tsvd_academy_parent_file( $parent_file ) {
	$screen = get_current_screen();
	if ( $screen && TSVD_ACADEMY_CPT === $screen->post_type ) {
		return TSVD_ACADEMY_PAGE;
	}
	return $parent_file;
}

function tsvd_academy_allow_menu( $slugs ) {
	$slugs[] = TSVD_ACADEMY_PAGE;
	return $slugs;
}

function tsvd_academy_requested_lesson_id() {
	return isset( $_GET['lesson'] ) ? absint( $_GET['lesson'] ) : 0;
}

function tsvd_academy_guard_lesson() {
	$lesson_id = tsvd_academy_requested_lesson_id();
	if ( ! $lesson_id || tsvd_academy_can_view( get_post( $lesson_id ) ) ) {
		return;
	}
	wp_die(
		esc_html__( 'Diese Schulung ist nicht verfügbar.', 'tsvd' ),
		'',
		array(
			'response'  => 403,
			'back_link' => true,
		)
	);
}

function tsvd_academy_enqueue_assets() {
	wp_enqueue_style( 'tsvd-dashboards' );
	if ( tsvd_academy_requested_lesson_id() ) {
		wp_enqueue_style( 'wp-block-library' );
	}
}

function tsvd_academy_render_page() {
	$lesson_id = tsvd_academy_requested_lesson_id();
	if ( $lesson_id ) {
		tsvd_academy_render_lesson( get_post( $lesson_id ) );
		return;
	}
	tsvd_academy_render_dashboard();
}
