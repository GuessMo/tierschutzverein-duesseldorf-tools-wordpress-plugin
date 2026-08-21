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
		'open'     => __( 'Offen', 'tsvd' ),
		'answered' => __( 'Beantwortet', 'tsvd' ),
		'spam'     => __( 'Spam', 'tsvd' ),
	);
}

function tsvd_anfragen_sidebar_pos() {
	$user_id = get_current_user_id();
	if ( isset( $_GET['msgr_side'] ) ) {
		$new = 'left' === $_GET['msgr_side'] ? 'left' : 'right';
		update_user_meta( $user_id, 'tsvd_anfragen_sidebar_pos', $new );
		return $new;
	}
	$pos = get_user_meta( $user_id, 'tsvd_anfragen_sidebar_pos', true );
	return 'left' === $pos ? 'left' : 'right';
}

function tsvd_anfragen_render_page() {
	if ( ! current_user_can( 'manage_tsvd_anfragen' ) ) {
		return;
	}

	$selected = absint( isset( $_GET['view'] ) ? $_GET['view'] : 0 );
	$status   = isset( $_GET['status'] ) ? sanitize_key( $_GET['status'] ) : '';
	$search   = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$pos      = tsvd_anfragen_sidebar_pos();

	echo '<div class="wrap"><h1 class="wp-heading-inline">' . esc_html__( 'Anfragen', 'tsvd' ) . '</h1>';

	if ( isset( $_GET['deleted'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Anfrage endgültig gelöscht.', 'tsvd' ) . '</p></div>';
	}
	if ( isset( $_GET['trashed'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Anfrage in den Papierkorb verschoben.', 'tsvd' ) . '</p></div>';
	}
	if ( isset( $_GET['spammed'] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Anfrage als Spam markiert.', 'tsvd' ) . '</p></div>';
	}

	tsvd_anfragen_messenger_styles();

	echo '<div class="tsvd-msgr tsvd-msgr--' . esc_attr( $pos ) . '">';
	tsvd_anfragen_render_sidebar( $status, $search, $selected, $pos );
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
		. 'height:calc(100vh - 150px);min-height:480px;margin-top:12px;}'
		. '.tsvd-msgr--right{flex-direction:row-reverse;}'
		. '.tsvd-msgr--left{flex-direction:row;}'
		. '.tsvd-msgr__side{width:340px;flex:0 0 340px;display:flex;flex-direction:column;'
		. 'border-right:1px solid var(--tsvd-chrome-border,#c3c4c7);background:var(--tsvd-chrome-canvas,#f0f0f0);}'
		. '.tsvd-msgr--right .tsvd-msgr__side{border-right:0;'
		. 'border-left:1px solid var(--tsvd-chrome-border,#c3c4c7);}'
		. '.tsvd-msgr__side-head{padding:10px;border-bottom:1px solid var(--tsvd-chrome-border,#c3c4c7);}'
		. '.tsvd-msgr__side-bar{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;}'
		. '.tsvd-msgr__side-title{font-weight:600;font-size:11px;text-transform:uppercase;'
		. 'letter-spacing:.03em;color:var(--tsvd-chrome-text-muted,#646970);}'
		. '.tsvd-msgr__pos{display:inline-flex;align-items:center;justify-content:center;width:36px;'
		. 'height:36px;border:1px solid var(--tsvd-chrome-border,#c3c4c7);border-radius:3px;'
		. 'color:var(--tsvd-chrome-text-muted,#646970);text-decoration:none;box-sizing:border-box;}'
		. '.tsvd-msgr__side-head .search-box{display:flex;gap:6px;margin:0;align-items:center;'
		. 'float:none;width:100%;box-sizing:border-box;}'
		. '.tsvd-msgr__side-head .search-box input[type=search]{flex:1;min-width:0;}'
		. '.tsvd-msgr__side-head .tsvd-msgr__search-btn{flex:0 0 auto;display:inline-flex;'
		. 'align-items:center;justify-content:center;width:36px;height:36px;min-height:36px;'
		. 'padding:0;line-height:1;border-radius:3px;box-sizing:border-box;}'
		. '.tsvd-msgr__filters{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;'
		. 'width:100%;box-sizing:border-box;}'
		. '.tsvd-msgr__filters a{display:inline-flex;align-items:center;justify-content:center;'
		. 'width:36px;height:36px;border:1px solid var(--tsvd-chrome-border,#c3c4c7);'
		. 'border-radius:3px;color:var(--tsvd-chrome-text-muted,#646970);text-decoration:none;'
		. 'box-sizing:border-box;}'
		. '.tsvd-msgr__filters a.current{background:var(--wp-admin-theme-color,#2271b1);'
		. 'border-color:var(--wp-admin-theme-color,#2271b1);color:#fff;}'
		. '.tsvd-msgr__filters a.current .dashicons{color:#fff;}'
		. '.tsvd-msgr__filters .dashicons,.tsvd-msgr__pos .dashicons,'
		. '.tsvd-msgr__search-btn .dashicons{display:flex;align-items:center;'
		. 'justify-content:center;width:18px;height:18px;font-size:18px;line-height:1;}'
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
		. 'margin-top:2px;display:flex;justify-content:space-between;gap:8px;align-items:flex-start;}'
		. '.tsvd-msgr__badge{font-size:11px;padding:2px 8px;border-radius:9px;border:0;white-space:nowrap;'
		. 'background:var(--tsvd-chrome-badge-bg,#e5f0fb);color:var(--tsvd-chrome-badge-text,#135e96);}'
		. '.tsvd-msgr__main{flex:1;display:flex;flex-direction:column;overflow-y:auto;padding:16px;min-width:0;}'
		. '.tsvd-msgr__empty{margin:auto;color:var(--tsvd-chrome-text-muted,#646970);}'
		. '.tsvd-msgr__pager{padding:6px;border-top:1px solid var(--tsvd-chrome-border,#c3c4c7);}'
		. '.tsvd-msgr__pager .tablenav{height:auto;margin:0;}'
		. '.tsvd-conv__head{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}'
		. '.tsvd-conv__head h2{margin:0;}'
		. '.tsvd-conv__actions{margin-left:auto;display:flex;gap:8px;align-items:center;}'
		. '.tsvd-conv__actions form{margin:0;display:flex;}'
		. '.tsvd-conv__act-btn{display:inline-flex;align-items:center;justify-content:center;'
		. 'width:36px;height:36px;padding:0;box-sizing:border-box;cursor:pointer;border-radius:3px;'
		. 'border:1px solid var(--tsvd-chrome-border,#c3c4c7);'
		. 'background:var(--tsvd-chrome-surface,#fff);color:var(--tsvd-chrome-text-muted,#646970);}'
		. '.tsvd-conv__act-btn:hover{color:var(--tsvd-chrome-heading,#1d2327);}'
		. '.tsvd-conv__act-btn.is-danger{color:#d63638;border-color:#d63638;}'
		. '.tsvd-conv__act-btn .dashicons{display:flex;align-items:center;justify-content:center;'
		. 'width:18px;height:18px;font-size:18px;line-height:1;}'
		. '.tsvd-conv__assign select{max-width:190px;height:36px;box-sizing:border-box;'
		. 'appearance:none;-webkit-appearance:none;-moz-appearance:none;'
		. 'padding:0 1.75rem 0 0.5rem;'
		. 'background-color:var(--tsvd-chrome-surface,#fff);color:var(--tsvd-chrome-text,#3c434a);'
		. 'border:1px solid var(--tsvd-chrome-border,#c3c4c7);border-radius:3px;'
		. 'background-image:var(--tsvd-select-arrow);background-repeat:no-repeat;'
		. 'background-position:right 0.5rem center;background-size:1rem;}'
		. '.tsvd-conv__assign select:hover{border-color:var(--tsvd-chrome-heading,#1d2327);}'
		. '.tsvd-conv__assign select:focus{color:var(--tsvd-chrome-text,#3c434a);'
		. 'border-color:var(--tsvd-link,#2271b1);outline:none;'
		. 'box-shadow:0 0 0 1px var(--tsvd-link,#2271b1);}'
		. '.tsvd-conv__trash-note{color:var(--tsvd-chrome-text-muted,#646970);font-style:italic;}'
		. '.tsvd-conv__animal{font-weight:400;font-size:14px;color:var(--tsvd-chrome-text-muted,#646970);}'
		. '.tsvd-conv__contact{margin:4px 0 12px;color:var(--tsvd-chrome-text-muted,#646970);}'
		. '.tsvd-conv__details{margin:0 0 14px;}'
		. '.tsvd-conv__details summary{cursor:pointer;color:var(--tsvd-link,#2271b1);}'
		. '.tsvd-conv__details table.widefat{margin-top:8px;background:var(--tsvd-chrome-surface,#fff);'
		. 'border-color:var(--tsvd-chrome-border,#c3c4c7);}'
		. '.tsvd-conv__details table.widefat th,.tsvd-conv__details table.widefat td{'
		. 'color:var(--tsvd-chrome-text,#3c434a);border-color:var(--tsvd-chrome-border,#c3c4c7);}'
		. '.tsvd-conv__details table.striped>tbody>tr:nth-child(odd){'
		. 'background:var(--tsvd-chrome-canvas,#f0f0f0);}'
		. '.tsvd-animal-card{display:flex;gap:12px;align-items:center;padding:10px 12px;'
		. 'margin:0 0 14px;border:1px solid var(--tsvd-chrome-border,#c3c4c7);border-radius:3px;'
		. 'background:var(--tsvd-chrome-canvas,#f0f0f0);}'
		. '.tsvd-animal-card__thumb{flex:0 0 64px;width:64px;height:64px;border-radius:3px;'
		. 'overflow:hidden;display:flex;align-items:center;justify-content:center;'
		. 'background:var(--tsvd-chrome-surface,#fff);border:1px solid var(--tsvd-chrome-border,#c3c4c7);}'
		. '.tsvd-animal-card__thumb img{width:64px;height:64px;object-fit:cover;display:block;}'
		. '.tsvd-animal-card__thumb .dashicons{color:var(--tsvd-chrome-text-muted,#646970);'
		. 'font-size:30px;width:30px;height:30px;}'
		. '.tsvd-animal-card__name{font-weight:600;font-size:14px;}'
		. '.tsvd-animal-card__facts{font-size:12px;color:var(--tsvd-chrome-text-muted,#646970);margin-top:2px;}'
		. '.tsvd-animal-card__links{display:flex;gap:14px;font-size:12px;margin-top:5px;}'
		. '@media(max-width:782px){.tsvd-msgr{flex-direction:column;height:auto;}'
		. '.tsvd-msgr__side{width:auto;flex:none;max-height:320px;}}'
		. '</style>';
}

function tsvd_anfragen_list_where( $status, $search ) {
	global $wpdb;
	$clauses = array();
	if ( 'trash' === $status ) {
		$clauses[] = 'deleted_at IS NOT NULL';
	} elseif ( 'mine' === $status ) {
		$clauses[] = 'deleted_at IS NULL';
		$clauses[] = "status != 'spam'";
		$clauses[] = $wpdb->prepare( 'assigned_user_id = %d', get_current_user_id() );
	} else {
		$clauses[] = 'deleted_at IS NULL';
		if ( $status ) {
			$clauses[] = $wpdb->prepare( 'status = %s', $status );
		} else {
			$clauses[] = "status != 'spam'";
		}
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
	return 'WHERE ' . implode( ' AND ', $clauses );
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
	echo '<button type="submit" class="button tsvd-msgr__search-btn" title="' . esc_attr__( 'Anfragen durchsuchen', 'tsvd' ) . '" aria-label="' . esc_attr__( 'Anfragen durchsuchen', 'tsvd' ) . '"><span class="dashicons dashicons-search"></span></button>';
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

function tsvd_anfragen_render_sidebar( $status, $search, $selected, $pos = 'right' ) {
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

	$keep = array();
	if ( $selected ) {
		$keep['view'] = $selected;
	}
	if ( $status ) {
		$keep['status'] = $status;
	}
	if ( '' !== $search ) {
		$keep['s'] = $search;
	}
	$opposite    = 'right' === $pos ? 'left' : 'right';
	$toggle_url  = add_query_arg( array_merge( $keep, array( 'msgr_side' => $opposite ) ), $base_url );
	$toggle_icon = 'right' === $pos ? 'dashicons-align-pull-left' : 'dashicons-align-pull-right';
	$toggle_lbl  = 'right' === $pos ? __( 'Seitenleiste nach links', 'tsvd' ) : __( 'Seitenleiste nach rechts', 'tsvd' );

	$status_icons = array(
		'open'     => 'dashicons-marker',
		'answered' => 'dashicons-yes',
		'spam'     => 'dashicons-warning',
	);

	echo '<div class="tsvd-msgr__side">';
	echo '<div class="tsvd-msgr__side-head">';

	echo '<div class="tsvd-msgr__side-bar">';
	echo '<span class="tsvd-msgr__side-title">' . esc_html__( 'Konversationen', 'tsvd' ) . '</span>';
	echo '<a class="tsvd-msgr__pos" href="' . esc_url( $toggle_url ) . '" title="' . esc_attr( $toggle_lbl ) . '" aria-label="' . esc_attr( $toggle_lbl ) . '"><span class="dashicons ' . esc_attr( $toggle_icon ) . '"></span></a>';
	echo '</div>';

	tsvd_anfragen_render_search_box( $status, $search );

	echo '<div class="tsvd-msgr__filters">';
	echo '<a href="' . esc_url( add_query_arg( 'status', 'mine', $base_url ) ) . '"' . ( 'mine' === $status ? ' class="current"' : '' ) . ' title="' . esc_attr__( 'Meine Anfragen', 'tsvd' ) . '" aria-label="' . esc_attr__( 'Meine Anfragen', 'tsvd' ) . '"><span class="dashicons dashicons-admin-users"></span></a>';
	echo '<a href="' . esc_url( $base_url ) . '"' . ( '' === $status ? ' class="current"' : '' ) . ' title="' . esc_attr__( 'Alle', 'tsvd' ) . '" aria-label="' . esc_attr__( 'Alle', 'tsvd' ) . '"><span class="dashicons dashicons-menu-alt"></span></a>';
	foreach ( $status_labels as $key => $label ) {
		if ( 'spam' === $key ) {
			continue;
		}
		$icon = isset( $status_icons[ $key ] ) ? $status_icons[ $key ] : 'dashicons-marker';
		echo '<a href="' . esc_url( add_query_arg( 'status', $key, $base_url ) ) . '"' . ( $status === $key ? ' class="current"' : '' ) . ' title="' . esc_attr( $label ) . '" aria-label="' . esc_attr( $label ) . '"><span class="dashicons ' . esc_attr( $icon ) . '"></span></a>';
	}
	echo '<a href="' . esc_url( add_query_arg( 'status', 'spam', $base_url ) ) . '"' . ( 'spam' === $status ? ' class="current"' : '' ) . ' title="' . esc_attr__( 'Spam', 'tsvd' ) . '" aria-label="' . esc_attr__( 'Spam', 'tsvd' ) . '"><span class="dashicons dashicons-warning"></span></a>';
	echo '<a href="' . esc_url( add_query_arg( 'status', 'trash', $base_url ) ) . '"' . ( 'trash' === $status ? ' class="current"' : '' ) . ' title="' . esc_attr__( 'Papierkorb', 'tsvd' ) . '" aria-label="' . esc_attr__( 'Papierkorb', 'tsvd' ) . '"><span class="dashicons dashicons-trash"></span></a>';
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

add_action( 'admin_post_tsvd_anfrage_trash', 'tsvd_anfragen_handle_trash' );
add_action( 'admin_post_tsvd_anfrage_restore', 'tsvd_anfragen_handle_restore' );
add_action( 'admin_post_tsvd_anfrage_delete', 'tsvd_anfragen_handle_delete' );
add_action( 'admin_post_tsvd_anfrage_assign', 'tsvd_anfragen_handle_assign' );
add_action( 'admin_post_tsvd_anfrage_spam', 'tsvd_anfragen_handle_spam' );
add_action( 'admin_post_tsvd_anfrage_unspam', 'tsvd_anfragen_handle_unspam' );

function tsvd_anfragen_check_action( $nonce_prefix ) {
	if ( ! current_user_can( 'manage_tsvd_anfragen' ) ) {
		wp_die( '-1' );
	}
	$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
	check_admin_referer( $nonce_prefix . $id );
	return $id;
}

function tsvd_anfragen_handle_trash() {
	$id = tsvd_anfragen_check_action( 'tsvd_anfrage_trash_' );
	global $wpdb;
	$wpdb->update( tsvd_anfragen_table_name(), array( 'deleted_at' => current_time( 'mysql' ) ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
	wp_safe_redirect( add_query_arg( 'trashed', '1', tsvd_anfragen_list_base_url() ) );
	exit;
}

function tsvd_anfragen_handle_restore() {
	$id = tsvd_anfragen_check_action( 'tsvd_anfrage_restore_' );
	global $wpdb;
	$wpdb->update( tsvd_anfragen_table_name(), array( 'deleted_at' => null ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
	wp_safe_redirect( add_query_arg( 'view', $id, tsvd_anfragen_list_base_url() ) );
	exit;
}

function tsvd_anfragen_handle_delete() {
	$id = tsvd_anfragen_check_action( 'tsvd_anfrage_delete_' );
	global $wpdb;
	$wpdb->delete( tsvd_anfragen_replies_table_name(), array( 'anfrage_id' => $id ), array( '%d' ) );
	$wpdb->delete( tsvd_anfragen_table_name(), array( 'id' => $id ), array( '%d' ) );
	wp_safe_redirect( add_query_arg( array( 'status' => 'trash', 'deleted' => '1' ), tsvd_anfragen_list_base_url() ) );
	exit;
}

function tsvd_anfragen_handle_spam() {
	$id = tsvd_anfragen_check_action( 'tsvd_anfrage_spam_' );
	global $wpdb;
	$wpdb->update( tsvd_anfragen_table_name(), array( 'status' => 'spam', 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $id ), array( '%s', '%s' ), array( '%d' ) );
	wp_safe_redirect( add_query_arg( array( 'status' => 'spam', 'spammed' => '1' ), tsvd_anfragen_list_base_url() ) );
	exit;
}

function tsvd_anfragen_handle_unspam() {
	$id = tsvd_anfragen_check_action( 'tsvd_anfrage_unspam_' );
	global $wpdb;
	$wpdb->update( tsvd_anfragen_table_name(), array( 'status' => 'open', 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $id ), array( '%s', '%s' ), array( '%d' ) );
	wp_safe_redirect( add_query_arg( 'view', $id, tsvd_anfragen_list_base_url() ) );
	exit;
}

function tsvd_anfragen_handle_assign() {
	$id  = tsvd_anfragen_check_action( 'tsvd_anfrage_assign_' );
	$uid = isset( $_POST['assigned_user_id'] ) ? absint( $_POST['assigned_user_id'] ) : 0;
	if ( $uid && ! user_can( $uid, 'manage_tsvd_anfragen' ) ) {
		$uid = 0;
	}
	global $wpdb;
	$wpdb->update( tsvd_anfragen_table_name(), array( 'assigned_user_id' => $uid ?: null ), array( 'id' => $id ), array( '%d' ), array( '%d' ) );
	wp_safe_redirect( add_query_arg( 'view', $id, tsvd_anfragen_list_base_url() ) );
	exit;
}

function tsvd_anfragen_eligible_assignees() {
	return get_users( array(
		'capability' => 'manage_tsvd_anfragen',
		'orderby'    => 'display_name',
		'fields'     => array( 'ID', 'display_name' ),
	) );
}
