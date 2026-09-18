<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_UPDATE_CPT             = 'tsvd_update';
const TSVD_UPDATE_VISIBILITY_META = '_tsvd_update_visibility';

add_action( 'init', 'tsvd_update_register_cpt' );

function tsvd_update_register_cpt() {
	register_post_type(
		TSVD_UPDATE_CPT,
		array(
			'labels'          => array(
				'name'          => __( 'Website-Updates', 'tsvd' ),
				'singular_name' => __( 'Website-Update', 'tsvd' ),
				'add_new'       => __( 'Neu', 'tsvd' ),
				'add_new_item'  => __( 'Neues Update', 'tsvd' ),
				'edit_item'     => __( 'Update bearbeiten', 'tsvd' ),
				'all_items'     => __( 'Website-Updates (Changelog)', 'tsvd' ),
				'menu_name'     => __( 'Website-Updates', 'tsvd' ),
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => 'tsvd-newsletter',
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'rewrite'         => false,
			'query_var'       => false,
		)
	);
}

function tsvd_update_visibility( $post_id ) {
	$value = get_post_meta( $post_id, TSVD_UPDATE_VISIBILITY_META, true );

	return 'extern' === $value ? 'extern' : 'intern';
}

add_action( 'add_meta_boxes_' . TSVD_UPDATE_CPT, 'tsvd_update_visibility_metabox' );

function tsvd_update_visibility_metabox() {
	add_meta_box(
		'tsvd-update-visibility',
		__( 'Sichtbarkeit', 'tsvd' ),
		'tsvd_update_visibility_render',
		TSVD_UPDATE_CPT,
		'side'
	);
}

function tsvd_update_visibility_render( $post ) {
	wp_nonce_field( 'tsvd_update_visibility_save', 'tsvd_update_visibility_nonce' );
	$current = tsvd_update_visibility( $post->ID );

	echo '<p><label><input type="radio" name="tsvd_update_visibility" value="intern"'
		. checked( $current, 'intern', false ) . '> ' . esc_html__( 'Intern (nur Newsletter/Changelog)', 'tsvd' ) . '</label></p>';
	echo '<p><label><input type="radio" name="tsvd_update_visibility" value="extern"'
		. checked( $current, 'extern', false ) . '> ' . esc_html__( 'Extern (auch Startseite)', 'tsvd' ) . '</label></p>';
}

add_action( 'save_post_' . TSVD_UPDATE_CPT, 'tsvd_update_save_visibility' );

function tsvd_update_save_visibility( $post_id ) {
	if ( ! isset( $_POST['tsvd_update_visibility_nonce'] )
		|| ! wp_verify_nonce( $_POST['tsvd_update_visibility_nonce'], 'tsvd_update_visibility_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$value = ( isset( $_POST['tsvd_update_visibility'] ) && 'extern' === $_POST['tsvd_update_visibility'] ) ? 'extern' : 'intern';
	update_post_meta( $post_id, TSVD_UPDATE_VISIBILITY_META, $value );
}

function tsvd_update_recent( $limit = 5, $only_public = false ) {
	$args = array(
		'post_type'      => TSVD_UPDATE_CPT,
		'post_status'    => 'publish',
		'posts_per_page' => (int) $limit,
		'orderby'        => 'date',
		'order'          => 'DESC',
	);

	if ( $only_public ) {
		$args['meta_query'] = array(
			array(
				'key'   => TSVD_UPDATE_VISIBILITY_META,
				'value' => 'extern',
			),
		);
	}

	return get_posts( $args );
}

add_filter( 'manage_' . TSVD_UPDATE_CPT . '_posts_columns', 'tsvd_update_columns' );

function tsvd_update_columns( $columns ) {
	$ordered = array();
	foreach ( $columns as $key => $label ) {
		$ordered[ $key ] = $label;
		if ( 'title' === $key ) {
			$ordered['tsvd_update_visibility'] = __( 'Sichtbarkeit', 'tsvd' );
		}
	}

	return $ordered;
}

add_action( 'manage_' . TSVD_UPDATE_CPT . '_posts_custom_column', 'tsvd_update_column_value', 10, 2 );

function tsvd_update_column_value( $column, $post_id ) {
	if ( 'tsvd_update_visibility' === $column ) {
		echo 'extern' === tsvd_update_visibility( $post_id )
			? esc_html__( 'Extern', 'tsvd' )
			: esc_html__( 'Intern', 'tsvd' );
	}
}
