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

	$selected = absint( isset( $_GET['view'] ) ? $_GET['view'] : 0 );
	$status   = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '';
	$search   = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

	echo '<div class="wrap"><h1 class="wp-heading-inline">' . esc_html__( 'Anfragen', 'tsvd' ) . '</h1>';

	if ( isset( $_GET['deleted'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Anfrage gelöscht.', 'tsvd' ) . '</p></div>';
	}

	tsvd_anfragen_messenger_styles();

	echo '<div class="tsvd-msgr">';
	tsvd_anfragen_render_sidebar( $status, $search, $selected );
	echo '<div class="tsvd-msgr__main">';
	if ( $selected ) {
		tsvd_anfragen_render_conversation( $selected );
	} else {
		echo '<div class="tsvd-msgr__empty"><p>' . esc_html__( 'Wähle links eine Konversation aus.', 'tsvd' ) . '</p></div>';
	}
	echo '</div></div></div>';
}

function tsvd_anfragen_messenger_styles() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;
	echo '<style>'
		. '.tsvd-msgr{display:flex;border:1px solid var(--tsvd-chrome-border,#c3c4c7);'
		. 'background:var(--tsvd-chrome-surface,#fff);border-radius:4px;overflow:hidden;'
		. 'height:calc(100vh - 200px);min-height:440px;margin-top:12px;}'
		. '.tsvd-msgr__side{width:340px;flex:0 0 340px;display:flex;flex-direction:column;'
		. 'border-right:1px solid var(--tsvd-chrome-border,#c3c4c7);background:var(--tsvd-chrome-canvas,#f0f0f0);}'
		. '.tsvd-msgr__side-head{padding:10px;border-bottom:1px solid var(--tsvd-chrome-border,#c3c4c7);}'
		. '.tsvd-msgr__side-head .search-box{display:flex;gap:6px;margin:0;}'
		. '.tsvd-msgr__side-head input[type=search]{flex:1;}'
		. '.tsvd-msgr__filters{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;font-size:12px;}'
		. '.tsvd-msgr__filters a{text-decoration:none;padding:2px 8px;border-radius:10px;'
		. 'color:var(--tsvd-chrome-text-muted,#646970);}'
		. '.tsvd-msgr__filters a.current{background:var(--wp-admin-theme-color,#2271b1);color:#fff;}'
		. '.tsvd-msgr__list{overflow-y:auto;flex:1;}'
		. '.tsvd-msgr__item{display:block;text-decoration:none;padding:10px 12px;'
		. 'border-bottom:1px solid var(--tsvd-chrome-border,#c3c4c7);color:var(--tsvd-chrome-text,#3c434a);}'
		. '.tsvd-msgr__item:hover{background:var(--tsvd-chrome-surface,#fff);}'
		. '.tsvd-msgr__item.is-active{background:var(--tsvd-chrome-surface,#fff);'
		. 'box-shadow:inset 3px 0 0 var(--wp-admin-theme-color,#2271b1);}'
		. '.tsvd-msgr__item-top{display:flex;justify-content:space-between;gap:8px;}'
		. '.tsvd-msgr__name{font-weight:600;}'
		. '.tsvd-msgr__time{font-size:11px;color:var(--tsvd-chrome-text-muted,#646970);white-space:nowrap;}'
		. '.tsvd-msgr__sub{font-size:12px;color:var(--tsvd-chrome-text-muted,#646970);'
		. 'margin-top:2px;display:flex;justify-content:space-between;gap:8px;}'
		. '.tsvd-msgr__badge{font-size:11px;padding:1px 7px;border-radius:9px;'
		. 'background:var(--tsvd-chrome-canvas,#f0f0f0);border:1px solid var(--tsvd-chrome-border,#c3c4c7);}'
		. '.tsvd-msgr__main{flex:1;display:flex;flex-direction:column;overflow-y:auto;padding:16px;min-width:0;}'
		. '.tsvd-msgr__empty{margin:auto;color:var(--tsvd-chrome-text-muted,#646970);}'
		. '.tsvd-msgr__pager{padding:6px;border-top:1px solid var(--tsvd-chrome-border,#c3c4c7);}'
		. '.tsvd-msgr__pager .tablenav{height:auto;margin:0;}'
		. '.tsvd-conv__head{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}'
		. '.tsvd-conv__head h2{margin:0;}'
		. '.tsvd-conv__animal{font-weight:400;font-size:14px;color:var(--tsvd-chrome-text-muted,#646970);}'
		. '.tsvd-conv__contact{margin:4px 0 12px;color:var(--tsvd-chrome-text-muted,#646970);}'
		. '.tsvd-conv__details{margin:0 0 14px;}'
		. '.tsvd-conv__details summary{cursor:pointer;color:var(--wp-admin-theme-color,#2271b1);}'
		. '@media(max-width:782px){.tsvd-msgr{flex-direction:column;height:auto;}'
		. '.tsvd-msgr__side{width:auto;flex:none;max-height:320px;}}'
		. '</style>';
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
	echo ' <input type="submit" class="button" value="' . esc_attr__( 'Suchen', 'tsvd' ) . '" />';
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

function tsvd_anfragen_render_sidebar( $status, $search, $selected ) {
	global $wpdb;
	$table  = tsvd_anfragen_table_name();
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

	$base_url      = tsvd_anfragen_list_base_url();
	$status_labels = tsvd_anfragen_status_labels();

	echo '<div class="tsvd-msgr__side">';
	echo '<div class="tsvd-msgr__side-head">';
	tsvd_anfragen_render_search_box( $status, $search );
	echo '<div class="tsvd-msgr__filters">';
	echo '<a href="' . esc_url( $base_url ) . '"' . ( '' === $status ? ' class="current"' : '' ) . '>' . esc_html__( 'Alle', 'tsvd' ) . '</a>';
	foreach ( $status_labels as $key => $label ) {
		echo '<a href="' . esc_url( add_query_arg( 'status', $key, $base_url ) ) . '"' . ( $status === $key ? ' class="current"' : '' ) . '>' . esc_html( $label ) . '</a>';
	}
	echo '</div></div>';

	echo '<div class="tsvd-msgr__list">';
	if ( empty( $rows ) ) {
		echo '<p class="tsvd-msgr__empty" style="padding:12px;">' . esc_html__( 'Keine Anfragen gefunden.', 'tsvd' ) . '</p>';
	} else {
		foreach ( $rows as $row ) {
			$args = array( 'view' => (int) $row['id'] );
			if ( $status ) {
				$args['status'] = $status;
			}
			if ( '' !== $search ) {
				$args['s'] = $search;
			}
			$url    = add_query_arg( $args, $base_url );
			$active = ( (int) $row['id'] === $selected ) ? ' is-active' : '';
			$animal = $row['animal_id'] ? get_the_title( (int) $row['animal_id'] ) : '';
			$label  = isset( $status_labels[ $row['status'] ] ) ? $status_labels[ $row['status'] ] : $row['status'];

			echo '<a class="tsvd-msgr__item' . $active . '" href="' . esc_url( $url ) . '">';
			echo '<div class="tsvd-msgr__item-top"><span class="tsvd-msgr__name">' . esc_html( $row['applicant_name'] ) . '</span>';
			echo '<span class="tsvd-msgr__time">' . esc_html( get_date_from_gmt( $row['created_at'], 'd.m.Y' ) ) . '</span></div>';
			echo '<div class="tsvd-msgr__sub"><span>' . esc_html( $animal ? $animal : '—' ) . '</span>';
			echo '<span class="tsvd-msgr__badge">' . esc_html( $label ) . '</span></div>';
			echo '</a>';
		}
	}
	echo '</div>';

	if ( $pages > 1 ) {
		echo '<div class="tsvd-msgr__pager">';
		tsvd_anfragen_render_pagination( $total, $paged, $status, $search );
		echo '</div>';
	}
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
