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
		wp_enqueue_style( 'tsvd-tools-anfragen', TSVD_TOOLS_URL . 'assets/anfragen-admin.css', array(), TSVD_TOOLS_VERSION );
	} );
}

function tsvd_anfragen_status_labels() {
	return array(
		'open'     => __( 'Offen', 'tsvd' ),
		'answered' => __( 'Beantwortet', 'tsvd' ),
		'spam'     => __( 'Spam', 'tsvd' ),
		'blocked'  => __( 'Blockiert', 'tsvd' ),
	);
}

function tsvd_anfragen_user_settings() {
	$defaults = array(
		'sidebar_pos' => 'right',
		'breed'       => 0,
		'status'      => '',
	);
	$user_id  = get_current_user_id();
	$stored   = get_user_meta( $user_id, 'tsvd_anfragen_user_settings', true );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}
	return array_merge( $defaults, $stored );
}

function tsvd_anfragen_user_setting( $key, $default = null ) {
	$settings = tsvd_anfragen_user_settings();
	return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
}

function tsvd_anfragen_update_user_setting( $key, $value ) {
	$settings = tsvd_anfragen_user_settings();
	$settings[ $key ] = $value;
	update_user_meta( get_current_user_id(), 'tsvd_anfragen_user_settings', $settings );
}

function tsvd_anfragen_sidebar_pos() {
	if ( isset( $_GET['msgr_side'] ) ) {
		$new = 'left' === $_GET['msgr_side'] ? 'left' : 'right';
		tsvd_anfragen_update_user_setting( 'sidebar_pos', $new );
		return $new;
	}
	$pos = tsvd_anfragen_user_setting( 'sidebar_pos', 'right' );
	return 'left' === $pos ? 'left' : 'right';
}

function tsvd_anfragen_render_page() {
	if ( ! current_user_can( 'manage_tsvd_anfragen' ) ) {
		return;
	}

	$selected = absint( isset( $_GET['view'] ) ? $_GET['view'] : 0 );
	$search   = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';

	if ( isset( $_GET['status'] ) ) {
		$status = sanitize_key( $_GET['status'] );
		tsvd_anfragen_update_user_setting( 'status', $status );
	} else {
		$status = (string) tsvd_anfragen_user_setting( 'status', '' );
	}

	if ( isset( $_GET['breed'] ) ) {
		$breed = absint( $_GET['breed'] );
		tsvd_anfragen_update_user_setting( 'breed', $breed );
	} else {
		$breed = (int) tsvd_anfragen_user_setting( 'breed', 0 );
	}

	$pos = tsvd_anfragen_sidebar_pos();

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

	echo '<div class="tsvd-msgr tsvd-msgr--' . esc_attr( $pos ) . '">';
	tsvd_anfragen_render_sidebar( $status, $search, $selected, $pos, $breed );
	echo '<div class="tsvd-msgr__main">';
	if ( $selected ) {
		tsvd_anfragen_render_conversation( $selected );
	} else {
		echo '<div class="tsvd-msgr__empty"><p>' . esc_html__( 'Wähle links eine Konversation aus.', 'tsvd' ) . '</p></div>';
	}
	echo '</div></div></div>';
}

/**
 * Alle animal_breed-Eltern-Terme (Häuser) in fester Reihenfolge.
 *
 * @return WP_Term[]
 */
function tsvd_anfragen_breed_houses() {
	$terms = get_terms(
		array(
			'taxonomy'   => 'animal_breed',
			'parent'     => 0,
			'hide_empty' => false,
		)
	);
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return $terms;
	}

	$order = array( 'Hund', 'Katze', 'Kleintier', 'Vogel', 'Insekt', 'Spinne', 'Amphibie', 'Reptil' );
	$rank  = array_flip( $order );

	usort( $terms, function ( $a, $b ) use ( $rank ) {
		$ra = isset( $rank[ $a->name ] ) ? $rank[ $a->name ] : count( $rank );
		$rb = isset( $rank[ $b->name ] ) ? $rank[ $b->name ] : count( $rank );
		if ( $ra !== $rb ) {
			return $ra <=> $rb;
		}
		return strcmp( $a->name, $b->name );
	} );

	return $terms;
}

/**
 * Nur Häuser mit sichtbaren Anfragen, in fester Reihenfolge.
 * Ein Haus erscheint erst, wenn mindestens eine offene (nicht spam/blocked) Anfrage vorliegt.
 *
 * @return WP_Term[]
 */
function tsvd_anfragen_breed_houses_visible() {
	$houses = tsvd_anfragen_breed_houses();
	if ( is_wp_error( $houses ) || empty( $houses ) ) {
		return $houses;
	}

	global $wpdb;
	$table = tsvd_anfragen_table_name();
	$ids   = array_map( 'absint', wp_list_pluck( $houses, 'term_id' ) );
	$in    = implode( ',', $ids );

	$has_requests = $wpdb->get_col(
		"SELECT DISTINCT CASE WHEN tt.parent = 0 THEN tt.term_id ELSE tt.parent END
		 FROM {$table} a
		 INNER JOIN {$wpdb->posts} p ON p.ID = a.animal_id AND p.post_type = 'animals'
		 INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
		 INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
		 WHERE tt.taxonomy = 'animal_breed'
		 AND ( tt.term_id IN ( {$in} ) OR tt.parent IN ( {$in} ) )
		 AND a.deleted_at IS NULL AND a.status NOT IN ( 'spam', 'blocked' )"
	);
	$visible = array_flip( array_map( 'intval', $has_requests ) );

	return array_values( array_filter( $houses, function ( $term ) use ( $visible ) {
		return isset( $visible[ (int) $term->term_id ] );
	} ) );
}

function tsvd_anfragen_breed_icon( $term_id ) {
	$name = get_term_field( 'name', $term_id, 'animal_breed' );
	$map  = array(
		'Hund'     => 'dog',
		'Katze'    => 'cat',
		'Kleintier' => 'rabbit',
		'Vogel'    => 'canary',
		'Insekt'   => 'bug',
		'Spinne'   => 'spider',
		'Amphibie' => 'frog',
	);
	return isset( $map[ $name ] ) ? $map[ $name ] : 'paw';
}

function tsvd_anfragen_breed_icon_svg( $icon ) {
	$paths = array(
		'dog'    => '<path d="M11 5h2" /><path d="M19 12c-.667 5.333 -2.333 8 -5 8h-4c-2.667 0 -4.333 -2.667 -5 -8" /><path d="M11 16c0 .667 .333 1 1 1s1 -.333 1 -1h-2" /><path d="M12 18v2" /><path d="M10 11v.01" /><path d="M14 11v.01" /><path d="M5 4l6 .97l-6.238 6.688a1.021 1.021 0 0 1 -1.41 .111a.953 .953 0 0 1 -.327 -.954l1.975 -6.815" /><path d="M19 4l-6 .97l6.238 6.688c.358 .408 .989 .458 1.41 .111a.953 .953 0 0 0 .327 -.954l-1.975 -6.815" />',
		'cat'    => '<path d="M20 3v10a8 8 0 1 1 -16 0v-10l3.432 3.432a7.963 7.963 0 0 1 4.568 -1.432c1.769 0 3.403 .574 4.728 1.546l3.272 -3.546" /><path d="M2 16h5l-4 4" /><path d="M22 16h-5l4 4" /><path d="M11 16a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M9 11v.01" /><path d="M15 11v.01" />',
		'paw'    => '<path d="M14.7 13.5c-1.1 -2 -1.441 -2.5 -2.7 -2.5c-1.259 0 -1.736 .755 -2.836 2.747c-.942 1.703 -2.846 1.845 -3.321 3.291c-.097 .265 -.145 .677 -.143 .962c0 1.176 .787 2 1.8 2c1.259 0 3 -1 4.5 -1s3.241 1 4.5 1c1.013 0 1.8 -.823 1.8 -2c0 -.285 -.049 -.697 -.146 -.962c-.475 -1.451 -2.512 -1.835 -3.454 -3.538" /><path d="M20.188 8.082a1.039 1.039 0 0 0 -.406 -.082h-.015c-.735 .012 -1.56 .75 -1.993 1.866c-.519 1.335 -.28 2.7 .538 3.052c.129 .055 .267 .082 .406 .082c.739 0 1.575 -.742 2.011 -1.866c.516 -1.335 .273 -2.7 -.54 -3.052l-.001 0" /><path d="M9.474 9c.055 0 .109 0 .163 -.011c.944 -.128 1.533 -1.346 1.32 -2.722c-.203 -1.297 -1.047 -2.267 -1.932 -2.267c-.055 0 -.109 0 -.163 .011c-.944 .128 -1.533 1.346 -1.32 2.722c.204 1.293 1.048 2.267 1.933 2.267" /><path d="M16.456 6.733c.214 -1.376 -.375 -2.594 -1.32 -2.722a1.164 1.164 0 0 0 -.162 -.011c-.885 0 -1.728 .97 -1.93 2.267c-.214 1.376 .375 2.594 1.32 2.722c.054 .007 .108 .011 .162 .011c.885 0 1.73 -.974 1.93 -2.267" /><path d="M5.69 12.918c.816 -.352 1.054 -1.719 .536 -3.052c-.436 -1.124 -1.271 -1.866 -2.009 -1.866c-.14 0 -.277 .027 -.407 .082c-.816 .352 -1.054 1.719 -.536 3.052c.436 1.124 1.271 1.866 2.009 1.866c.14 0 .277 -.027 .407 -.082" />',
		'canary' => '<path d="M12 20v-2" /><path d="M15 8.01v.01" /><path d="M3 17l8 -8v-1a4 4 0 1 1 8 0h2l-2 2v1a7 7 0 0 1 -13.215 3.223" />',
		'bug'    => '<path d="M9 9v-1a3 3 0 0 1 6 0v1" /><path d="M8 9h8a6 6 0 0 1 1 3v3a5 5 0 0 1 -10 0v-3a6 6 0 0 1 1 -3" /><path d="M3 13l4 0" /><path d="M17 13l4 0" /><path d="M12 20l0 -6" /><path d="M4 19l3.35 -2" /><path d="M20 19l-3.35 -2" /><path d="M4 7l3.75 2.4" /><path d="M20 7l-3.75 2.4" />',
		'spider' => '<path d="M5 4v2l5 5" /><path d="M2.5 9.5l1.5 1.5h6" /><path d="M4 19v-2l6 -6" /><path d="M19 4v2l-5 5" /><path d="M21.5 9.5l-1.5 1.5h-6" /><path d="M20 19v-2l-6 -6" /><path d="M8 15a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" /><path d="M10 9a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />',
		'rabbit' => '<path d="M13 16a3 3 0 0 1 2.24 5" /><path d="M18 12h.01" /><path d="M18 21h-8a4 4 0 0 1-4-4 7 7 0 0 1 7-7h.2L9.6 6.4a1 1 0 1 1 2.8-2.8L15.8 7h.2c3.3 0 6 2.7 6 6v1a2 2 0 0 1-2 2h-1a3 3 0 0 0-3 3" /><path d="M20 8.54V4a2 2 0 1 0-4 0v3" /><path d="M7.612 12.524a3 3 0 1 0-1.6 4.3" />',
		'frog' => '<circle cx="8" cy="6.5" r="2" /><circle cx="16" cy="6.5" r="2" /><path d="M4 10a8 4.5 0 0 1 16 0v1.5a3.5 3.5 0 0 1 -3.5 3.5h-9A3.5 3.5 0 0 1 4 11.5z" /><path d="M9.5 13.5c1.2 1.6 3.8 1.6 5 0" />',
	);
	$body = isset( $paths[ $icon ] ) ? $paths[ $icon ] : $paths['paw'];
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"'
		. ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $body . '</svg>';
}


function tsvd_anfragen_list_where( $status, $search, $breed = 0 ) {
	global $wpdb;
	$clauses = array();
	if ( 'trash' === $status ) {
		$clauses[] = 'deleted_at IS NOT NULL';
	} elseif ( 'mine' === $status ) {
		$clauses[] = 'deleted_at IS NULL';
		$clauses[] = "status NOT IN ( 'spam', 'blocked' )";
		$clauses[] = $wpdb->prepare( 'assigned_user_id = %d', get_current_user_id() );
	} else {
		$clauses[] = 'deleted_at IS NULL';
		if ( $status ) {
			$clauses[] = $wpdb->prepare( 'status = %s', $status );
		} else {
			$clauses[] = "status NOT IN ( 'spam', 'blocked' )";
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
	if ( $breed ) {
		$clauses[] = $wpdb->prepare(
			'animal_id IN ( SELECT object_id FROM ' . $wpdb->term_relationships . ' WHERE term_taxonomy_id = %d )',
			$breed
		);
	}
	return 'WHERE ' . implode( ' AND ', $clauses );
}

function tsvd_anfragen_list_base_url() {
	return admin_url( 'edit.php?post_type=animals&page=tsvd-anfragen' );
}

function tsvd_anfragen_render_search_box( $status, $search, $breed = 0 ) {
	echo '<form method="get" class="search-form">';
	echo '<input type="hidden" name="post_type" value="animals" />';
	echo '<input type="hidden" name="page" value="tsvd-anfragen" />';
	if ( $status ) {
		echo '<input type="hidden" name="status" value="' . esc_attr( $status ) . '" />';
	}
	if ( $breed ) {
		echo '<input type="hidden" name="breed" value="' . esc_attr( $breed ) . '" />';
	}
	echo '<p class="search-box">';
	echo '<label class="screen-reader-text" for="tsvd-anfrage-search">' . esc_html__( 'Anfragen durchsuchen', 'tsvd' ) . '</label>';
	echo '<input type="search" id="tsvd-anfrage-search" name="s" value="' . esc_attr( $search ) . '" placeholder="' . esc_attr__( 'Name, E-Mail, Telefon, Tier', 'tsvd' ) . '" />';
	echo '<button type="submit" class="button tsvd-msgr__search-btn" title="' . esc_attr__( 'Anfragen durchsuchen', 'tsvd' ) . '" aria-label="' . esc_attr__( 'Anfragen durchsuchen', 'tsvd' ) . '"><span class="dashicons dashicons-search"></span></button>';
	echo '</p></form>';
}

function tsvd_anfragen_render_pagination( $total, $paged, $status, $search, $breed = 0 ) {
	$pages = (int) ceil( $total / TSVD_ANFRAGEN_PER_PAGE );
	$args  = array( 'post_type' => 'animals', 'page' => 'tsvd-anfragen' );
	if ( $status ) {
		$args['status'] = $status;
	}
	if ( '' !== $search ) {
		$args['s'] = $search;
	}
	if ( $breed ) {
		$args['breed'] = $breed;
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

function tsvd_anfragen_render_sidebar( $status, $search, $selected, $pos = 'right', $breed = 0 ) {
	global $wpdb;
	$table = tsvd_anfragen_table_name();
	$where = tsvd_anfragen_list_where( $status, $search, $breed );
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
	if ( $breed ) {
		$keep['breed'] = $breed;
	}
	$breed_args = $breed ? array( 'breed' => $breed ) : array();
	$opposite   = 'right' === $pos ? 'left' : 'right';
	$toggle_url  = add_query_arg( array_merge( $keep, array( 'msgr_side' => $opposite ) ), $base_url );
	$toggle_icon = 'right' === $pos ? 'dashicons-align-pull-left' : 'dashicons-align-pull-right';
	$toggle_lbl  = 'right' === $pos ? __( 'Seitenleiste nach links', 'tsvd' ) : __( 'Seitenleiste nach rechts', 'tsvd' );

	$status_icons = array(
		'open'     => 'dashicons-marker',
		'answered' => 'dashicons-yes',
		'spam'     => 'dashicons-warning',
		'blocked'  => 'dashicons-shield-alt',
	);

	echo '<div class="tsvd-msgr__side">';
	echo '<div class="tsvd-msgr__side-head">';

	echo '<div class="tsvd-msgr__side-bar">';
	echo '<span class="tsvd-msgr__side-title">' . esc_html__( 'Konversationen', 'tsvd' ) . '</span>';
	echo '<a class="tsvd-msgr__pos" href="' . esc_url( $toggle_url ) . '" title="' . esc_attr( $toggle_lbl ) . '" aria-label="' . esc_attr( $toggle_lbl ) . '"><span class="dashicons ' . esc_attr( $toggle_icon ) . '"></span></a>';
	echo '</div>';

	tsvd_anfragen_render_search_box( $status, $search, $breed );

	$status_args = array();
	if ( $status ) {
		$status_args['status'] = $status;
	}
	if ( '' !== $search ) {
		$status_args['s'] = $search;
	}

	$houses = tsvd_anfragen_breed_houses_visible();
	echo '<div class="tsvd-msgr__filters tsvd-msgr__filters--breed">';
	echo '<a class="tsvd-msgr__filter-btn' . ( $breed ? '' : ' is-current' ) . '" href="' . esc_url( add_query_arg( array_merge( $status_args, array( 'breed' => 0 ) ), $base_url ) ) . '" title="' . esc_attr__( 'Alle Tierarten', 'tsvd' ) . '" aria-label="' . esc_attr__( 'Alle Tierarten', 'tsvd' ) . '">' . tsvd_anfragen_breed_icon_svg( 'paw' ) . '</a>';
	if ( ! is_wp_error( $houses ) ) {
		foreach ( $houses as $term ) {
			$icon   = tsvd_anfragen_breed_icon( $term->term_id );
			$active = $breed === (int) $term->term_id;
			$href   = add_query_arg( array_merge( $status_args, array( 'breed' => $active ? 0 : $term->term_id ) ), $base_url );
			echo '<a class="tsvd-msgr__filter-btn' . ( $active ? ' is-current' : '' ) . '" href="' . esc_url( $href ) . '" title="' . esc_attr( $term->name ) . '" aria-label="' . esc_attr( $term->name ) . '">' . tsvd_anfragen_breed_icon_svg( $icon ) . '</a>';
		}
	}
	echo '</div>';

	echo '<div class="tsvd-msgr__filters">';
	echo '<a class="tsvd-msgr__filter-btn' . ( 'mine' === $status ? ' is-current' : '' ) . '" href="' . esc_url( add_query_arg( array_merge( array( 'status' => 'mine' ), $breed_args ), $base_url ) ) . '" title="' . esc_attr__( 'Meine Anfragen', 'tsvd' ) . '" aria-label="' . esc_attr__( 'Meine Anfragen', 'tsvd' ) . '"><span class="dashicons dashicons-admin-users"></span></a>';
	echo '<a class="tsvd-msgr__filter-btn' . ( '' === $status ? ' is-current' : '' ) . '" href="' . esc_url( add_query_arg( array_merge( array( 'status' => '' ), $breed_args ), $base_url ) ) . '" title="' . esc_attr__( 'Alle', 'tsvd' ) . '" aria-label="' . esc_attr__( 'Alle', 'tsvd' ) . '"><span class="dashicons dashicons-menu-alt"></span></a>';
	foreach ( $status_labels as $key => $label ) {
		if ( 'spam' === $key ) {
			continue;
		}
		$icon = isset( $status_icons[ $key ] ) ? $status_icons[ $key ] : 'dashicons-marker';
		echo '<a class="tsvd-msgr__filter-btn' . ( $status === $key ? ' is-current' : '' ) . '" href="' . esc_url( add_query_arg( array_merge( array( 'status' => $key ), $breed_args ), $base_url ) ) . '" title="' . esc_attr( $label ) . '" aria-label="' . esc_attr( $label ) . '"><span class="dashicons ' . esc_attr( $icon ) . '"></span></a>';
	}
	echo '<a class="tsvd-msgr__filter-btn' . ( 'spam' === $status ? ' is-current' : '' ) . '" href="' . esc_url( add_query_arg( array_merge( array( 'status' => 'spam' ), $breed_args ), $base_url ) ) . '" title="' . esc_attr__( 'Spam', 'tsvd' ) . '" aria-label="' . esc_attr__( 'Spam', 'tsvd' ) . '"><span class="dashicons dashicons-warning"></span></a>';
	echo '<a class="tsvd-msgr__filter-btn' . ( 'trash' === $status ? ' is-current' : '' ) . '" href="' . esc_url( add_query_arg( array_merge( array( 'status' => 'trash' ), $breed_args ), $base_url ) ) . '" title="' . esc_attr__( 'Papierkorb', 'tsvd' ) . '" aria-label="' . esc_attr__( 'Papierkorb', 'tsvd' ) . '"><span class="dashicons dashicons-trash"></span></a>';
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
			if ( $breed ) {
				$args['breed'] = $breed;
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
		tsvd_anfragen_render_pagination( $total, $paged, $status, $search, $breed );
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
add_action( 'admin_post_tsvd_anfrage_block', 'tsvd_anfragen_handle_block' );
add_action( 'admin_post_tsvd_anfrage_unblock', 'tsvd_anfragen_handle_unblock' );

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

function tsvd_anfragen_handle_block() {
	$id = tsvd_anfragen_check_action( 'tsvd_anfrage_block_' );
	global $wpdb;
	$table = tsvd_anfragen_table_name();
	$email = $wpdb->get_var( $wpdb->prepare( "SELECT applicant_email FROM {$table} WHERE id = %d", $id ) );
	if ( $email ) {
		tsvd_anfragen_blacklist_add( $email );
		$wpdb->update( $table, array( 'status' => 'blocked', 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $id ), array( '%s', '%s' ), array( '%d' ) );
	}
	wp_safe_redirect( add_query_arg( 'view', $id, tsvd_anfragen_list_base_url() ) );
	exit;
}

function tsvd_anfragen_handle_unblock() {
	$id = tsvd_anfragen_check_action( 'tsvd_anfrage_unblock_' );
	global $wpdb;
	$table = tsvd_anfragen_table_name();
	$email = $wpdb->get_var( $wpdb->prepare( "SELECT applicant_email FROM {$table} WHERE id = %d", $id ) );
	if ( $email ) {
		tsvd_anfragen_blacklist_remove( $email );
		$wpdb->update( $table, array( 'status' => 'open', 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $id ), array( '%s', '%s' ), array( '%d' ) );
	}
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
