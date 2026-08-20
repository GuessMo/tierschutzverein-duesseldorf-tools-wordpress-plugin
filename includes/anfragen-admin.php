<?php
/**
 * Anfragen-Dashboard: Untermenü, Listenansicht mit Status-Filter, Löschen-Aktion.
 *
 * @package TSVD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ANFRAGEN_PER_PAGE = 25;

add_action( 'admin_menu', 'tsvd_anfragen_admin_menu' );

function tsvd_anfragen_admin_menu() {
	$hook = add_submenu_page(
		'edit.php?post_type=animals',
		__( 'Anfragen', 'tsvd' ),
		__( 'Anfragen', 'tsvd' ),
		'manage_tsvd_anfragen',
		'tsvd-anfragen',
		'tsvd_anfragen_render_page'
	);
	add_action( 'admin_print_scripts-' . $hook, function () {
		wp_enqueue_script( 'jquery' );
	} );
}

function tsvd_anfragen_status_labels() {
	return array(
		'new'         => __( 'Neu', 'tsvd' ),
		'in_progress' => __( 'In Bearbeitung', 'tsvd' ),
		'answered'    => __( 'Beantwortet', 'tsvd' ),
		'closed'      => __( 'Geschlossen', 'tsvd' ),
	);
}

function tsvd_anfragen_render_page() {
	if ( ! current_user_can( 'manage_tsvd_anfragen' ) ) {
		return;
	}

	$view_raw = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : '';

	$view = absint( $view_raw );
	if ( $view ) {
		tsvd_anfragen_render_detail( $view );
		return;
	}

	tsvd_anfragen_render_list();
}

function tsvd_anfragen_list_where( $status, $search ) {
	global $wpdb;
	$clauses = array();
	if ( $status ) {
		$clauses[] = $wpdb->prepare( 'status = %s', $status );
	}
	if ( '' !== $search ) {
		$like      = '%' . $wpdb->esc_like( $search ) . '%';
		$clauses[] = $wpdb->prepare(
			'( applicant_name LIKE %s OR applicant_email LIKE %s OR applicant_phone LIKE %s'
			. ' OR animal_id IN ( SELECT ID FROM ' . $wpdb->posts . ' WHERE post_type = %s AND post_title LIKE %s ) )',
			$like,
			$like,
			$like,
			'animals',
			$like
		);
	}
	return $clauses ? ( 'WHERE ' . implode( ' AND ', $clauses ) ) : '';
}

function tsvd_anfragen_list_base_url() {
	return admin_url( 'edit.php?post_type=animals&page=tsvd-anfragen' );
}

function tsvd_anfragen_render_search_box( $status, $search ) {
	echo '<form method="get" class="search-form">';
	echo '<input type="hidden" name="post_type" value="animals" />';
	echo '<input type="hidden" name="page" value="tsvd-anfragen" />';
	if ( $status ) {
		echo '<input type="hidden" name="status" value="' . esc_attr( $status ) . '" />';
	}
	echo '<p class="search-box">';
	echo '<label class="screen-reader-text" for="tsvd-anfrage-search">' . esc_html__( 'Anfragen durchsuchen', 'tsvd' ) . '</label>';
	echo '<input type="search" id="tsvd-anfrage-search" name="s" value="' . esc_attr( $search ) . '" placeholder="' . esc_attr__( 'Name, E-Mail, Telefon, Tier', 'tsvd' ) . '" />';
	echo ' <input type="submit" class="button" value="' . esc_attr__( 'Anfragen durchsuchen', 'tsvd' ) . '" />';
	echo '</p></form>';
}

function tsvd_anfragen_render_pagination( $total, $paged, $status, $search ) {
	$pages = (int) ceil( $total / TSVD_ANFRAGEN_PER_PAGE );
	$args  = array( 'post_type' => 'animals', 'page' => 'tsvd-anfragen' );
	if ( $status ) {
		$args['status'] = $status;
	}
	if ( '' !== $search ) {
		$args['s'] = $search;
	}
	$links = paginate_links( array(
		'base'      => add_query_arg( 'paged', '%#%', add_query_arg( $args, admin_url( 'edit.php' ) ) ),
		'format'    => '',
		'prev_text' => '‹',
		'next_text' => '›',
		'total'     => max( 1, $pages ),
		'current'   => $paged,
	) );

	echo '<div class="tablenav"><div class="tablenav-pages">';
	echo '<span class="displaying-num">' . esc_html( sprintf( _n( '%s Anfrage', '%s Anfragen', $total, 'tsvd' ), number_format_i18n( $total ) ) ) . '</span>';
	if ( $links ) {
		echo '<span class="pagination-links">' . wp_kses_post( $links ) . '</span>';
	}
	echo '</div></div>';
}

function tsvd_anfragen_render_list() {
	global $wpdb;
	$table  = tsvd_anfragen_table_name();
	$status = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '';
	$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

	$where  = tsvd_anfragen_list_where( $status, $search );
	$total  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} {$where}" );
	$pages  = max( 1, (int) ceil( $total / TSVD_ANFRAGEN_PER_PAGE ) );
	$paged  = max( 1, isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1 );
	if ( $paged > $pages ) {
		$paged = $pages;
	}
	$offset = ( $paged - 1 ) * TSVD_ANFRAGEN_PER_PAGE;
	$rows   = $wpdb->get_results(
		$wpdb->prepare( "SELECT * FROM {$table} {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d", TSVD_ANFRAGEN_PER_PAGE, $offset ),
		ARRAY_A
	);

	$base_url = tsvd_anfragen_list_base_url();

	echo '<div class="wrap"><h1>' . esc_html__( 'Anfragen', 'tsvd' ) . '</h1>';

	if ( isset( $_GET['deleted'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Anfrage gelöscht.', 'tsvd' ) . '</p></div>';
	}

	$status_labels = tsvd_anfragen_status_labels();
	$status_count  = count( $status_labels );

	echo '<ul class="subsubsub">';
	echo '<li><a href="' . esc_url( $base_url ) . '"' . ( '' === $status ? ' class="current"' : '' ) . '>' . esc_html__( 'Alle', 'tsvd' ) . '</a> |</li>';
	$i = 0;
	foreach ( $status_labels as $key => $label ) {
		$i++;
		$is_last = ( $i === $status_count );
		echo '<li><a href="' . esc_url( add_query_arg( 'status', $key, $base_url ) ) . '"' . ( $status === $key ? ' class="current"' : '' ) . '>' . esc_html( $label ) . '</a>' . ( $is_last ? '' : ' |' ) . '</li>';
	}
	echo '</ul>';

	tsvd_anfragen_render_search_box( $status, $search );
	echo '<div class="clear"></div>';

	if ( empty( $rows ) ) {
		echo '<p>' . esc_html__( 'Keine Anfragen gefunden.', 'tsvd' ) . '</p></div>';
		return;
	}

	echo '<table class="wp-list-table widefat fixed striped"><thead><tr>'
		. '<th>' . esc_html__( 'Datum', 'tsvd' ) . '</th>'
		. '<th>' . esc_html__( 'Formular', 'tsvd' ) . '</th>'
		. '<th>' . esc_html__( 'Tier', 'tsvd' ) . '</th>'
		. '<th>' . esc_html__( 'Name', 'tsvd' ) . '</th>'
		. '<th>' . esc_html__( 'E-Mail', 'tsvd' ) . '</th>'
		. '<th>' . esc_html__( 'Status', 'tsvd' ) . '</th>'
		. '<th></th></tr></thead><tbody>';

	foreach ( $rows as $row ) {
		$detail_url  = add_query_arg( 'view', $row['id'], $base_url );
		$form_title  = get_the_title( (int) $row['form_id'] );
		$animal_name = $row['animal_id'] ? get_the_title( (int) $row['animal_id'] ) : '';
		$label       = isset( $status_labels[ $row['status'] ] ) ? $status_labels[ $row['status'] ] : $row['status'];

		echo '<tr>'
			. '<td>' . esc_html( get_date_from_gmt( $row['created_at'], 'Y-m-d H:i' ) ) . '</td>'
			. '<td>' . esc_html( $form_title ? $form_title : '#' . $row['form_id'] ) . '</td>'
			. '<td>' . esc_html( $animal_name ) . '</td>'
			. '<td>' . esc_html( $row['applicant_name'] ) . '</td>'
			. '<td>' . esc_html( $row['applicant_email'] ) . '</td>'
			. '<td>' . esc_html( $label ) . '</td>'
			. '<td><a href="' . esc_url( $detail_url ) . '" class="button button-small">' . esc_html__( 'Ansehen', 'tsvd' ) . '</a></td>'
			. '</tr>';
	}

	echo '</tbody></table>';
	tsvd_anfragen_render_pagination( $total, $paged, $status, $search );
	echo '</div>';
}

add_action( 'admin_post_tsvd_anfrage_delete', 'tsvd_anfragen_handle_delete' );

function tsvd_anfragen_handle_delete() {
	if ( ! current_user_can( 'manage_tsvd_anfragen' ) ) {
		wp_die( '-1' );
	}
	$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
	check_admin_referer( 'tsvd_anfrage_delete_' . $id );

	global $wpdb;
	$wpdb->delete( tsvd_anfragen_replies_table_name(), array( 'anfrage_id' => $id ), array( '%d' ) );
	$wpdb->delete( tsvd_anfragen_table_name(), array( 'id' => $id ), array( '%d' ) );

	wp_safe_redirect( add_query_arg( 'deleted', '1', admin_url( 'edit.php?post_type=animals&page=tsvd-anfragen' ) ) );
	exit;
}
