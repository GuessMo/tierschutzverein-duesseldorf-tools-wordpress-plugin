<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_UPDATE_CPT         = 'tsvd_update';
const TSVD_UPDATE_NUMBER_META = '_tsvd_update_number';

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
			'show_in_menu'    => false,
			'supports'        => array( 'title', 'editor' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
			'rewrite'         => false,
			'query_var'       => false,
		)
	);
}

function tsvd_update_number( $post_id ) {
	return (int) get_post_meta( $post_id, TSVD_UPDATE_NUMBER_META, true );
}

function tsvd_update_next_number() {
	$latest = get_posts(
		array(
			'post_type'      => TSVD_UPDATE_CPT,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'meta_key'       => TSVD_UPDATE_NUMBER_META,
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
			'fields'         => 'ids',
		)
	);

	return ( $latest ? tsvd_update_number( $latest[0] ) : 0 ) + 1;
}

add_action( 'save_post_' . TSVD_UPDATE_CPT, 'tsvd_update_assign_number', 5 );

function tsvd_update_assign_number( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( 'auto-draft' === get_post_status( $post_id ) ) {
		return;
	}
	if ( tsvd_update_number( $post_id ) > 0 ) {
		return;
	}
	update_post_meta( $post_id, TSVD_UPDATE_NUMBER_META, tsvd_update_next_number() );
}

add_action( 'admin_init', 'tsvd_update_backfill_numbers' );

function tsvd_update_backfill_numbers() {
	if ( get_option( 'tsvd_update_numbers_backfilled' ) ) {
		return;
	}
	$posts = get_posts(
		array(
			'post_type'      => TSVD_UPDATE_CPT,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'ASC',
			'fields'         => 'ids',
		)
	);
	$next = tsvd_update_next_number();
	foreach ( $posts as $id ) {
		if ( tsvd_update_number( $id ) > 0 ) {
			continue;
		}
		update_post_meta( $id, TSVD_UPDATE_NUMBER_META, $next );
		$next++;
	}
	update_option( 'tsvd_update_numbers_backfilled', 1 );
}

add_action( 'admin_init', 'tsvd_update_cleanup_visibility_meta' );

function tsvd_update_cleanup_visibility_meta() {
	if ( get_option( 'tsvd_update_visibility_removed' ) ) {
		return;
	}
	$posts = get_posts(
		array(
			'post_type'      => TSVD_UPDATE_CPT,
			'post_status'    => 'any',
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	foreach ( $posts as $id ) {
		delete_post_meta( $id, '_tsvd_update_visibility' );
	}
	update_option( 'tsvd_update_visibility_removed', 1 );
}

function tsvd_update_recent( $limit = 5, $only_public = false, $only_unsent = false ) {
	$meta_query = array(
		'number_clause' => array(
			'key'     => TSVD_UPDATE_NUMBER_META,
			'type'    => 'NUMERIC',
			'compare' => 'EXISTS',
		),
	);
	if ( $only_unsent ) {
		$meta_query[] = array(
			'key'     => TSVD_NEWSLETTER_SENT_IN_META,
			'compare' => 'NOT EXISTS',
		);
	}

	return get_posts(
		array(
			'post_type'      => TSVD_UPDATE_CPT,
			'post_status'    => 'publish',
			'posts_per_page' => (int) $limit,
			'orderby'        => array( 'number_clause' => 'DESC' ),
			'meta_query'     => $meta_query,
		)
	);
}

add_action( 'pre_get_posts', 'tsvd_update_admin_order' );

function tsvd_update_admin_order( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	if ( TSVD_UPDATE_CPT !== $query->get( 'post_type' ) ) {
		return;
	}
	if ( $query->get( 'orderby' ) ) {
		return;
	}
	$query->set( 'meta_key', TSVD_UPDATE_NUMBER_META );
	$query->set( 'orderby', 'meta_value_num' );
	$query->set( 'order', 'DESC' );
}

add_filter( 'manage_' . TSVD_UPDATE_CPT . '_posts_columns', 'tsvd_update_columns' );

function tsvd_update_columns( $columns ) {
	$ordered = array();
	foreach ( $columns as $key => $label ) {
		if ( 'date' === $key ) {
			continue;
		}
		if ( 'title' === $key ) {
			$ordered['tsvd_update_number'] = __( 'Nr.', 'tsvd' );
		}
		$ordered[ $key ] = $label;
	}

	return $ordered;
}

add_action( 'manage_' . TSVD_UPDATE_CPT . '_posts_custom_column', 'tsvd_update_column_value', 10, 2 );

function tsvd_update_column_value( $column, $post_id ) {
	if ( 'tsvd_update_number' === $column ) {
		$number = tsvd_update_number( $post_id );
		echo $number > 0 ? esc_html( '#' . $number ) : '—';
	}
}
