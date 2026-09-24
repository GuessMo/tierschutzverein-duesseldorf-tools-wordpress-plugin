<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_primary_statuses() {
	return array(
		'open'     => __( 'Offen', 'tsvd' ),
		'answered' => __( 'Beantwortet', 'tsvd' ),
		''         => __( 'Alle', 'tsvd' ),
	);
}

function tsvd_anfragen_more_statuses() {
	return array(
		'spam'    => __( 'Spam', 'tsvd' ),
		'blocked' => __( 'Blockiert', 'tsvd' ),
		'trash'   => __( 'Papierkorb', 'tsvd' ),
	);
}

function tsvd_anfragen_render_filters( $status, $search, $breed ) {
	tsvd_anfragen_render_breed_chips( $status, $search, $breed );
	tsvd_anfragen_render_status_tabs( $status, $search, $breed );
	echo '<div class="tsvd-anf-row">';
	tsvd_anfragen_render_mine_chip( $status, $search, $breed );
	tsvd_anfragen_render_more_menu( $status, $search, $breed );
	echo '</div>';
	tsvd_anfragen_render_filter_state( $status, $search, $breed );
}

function tsvd_anfragen_current_attr( $is_current ) {
	return $is_current ? ' aria-current="true"' : '';
}

function tsvd_anfragen_render_breed_chips( $status, $search, $breed ) {
	$houses = tsvd_anfragen_breed_houses_visible();
	echo '<div class="tsvd-anf-chips" role="group" aria-label="' . esc_attr__( 'Nach Tierart filtern', 'tsvd' ) . '">';
	tsvd_anfragen_render_breed_chip( __( 'Alle Tiere', 'tsvd' ), 'paw', 0, 0 === $breed, $status, $search );
	if ( ! is_wp_error( $houses ) ) {
		foreach ( $houses as $term ) {
			$active = $breed === (int) $term->term_id;
			$target = $active ? 0 : (int) $term->term_id;
			tsvd_anfragen_render_breed_chip( $term->name, tsvd_anfragen_breed_icon( $term->term_id ), $target, $active, $status, $search );
		}
	}
	echo '</div>';
}

function tsvd_anfragen_render_breed_chip( $label, $icon, $target, $active, $status, $search ) {
	$url = tsvd_anfragen_filter_url(
		array(
			'status' => $status,
			's'      => '' !== $search ? rawurlencode( $search ) : null,
			'breed'  => $target,
		)
	);
	echo '<a class="tsvd-anf-btn" href="' . esc_url( $url ) . '"' . tsvd_anfragen_current_attr( $active ) . '>'
		. tsvd_anfragen_breed_icon_svg( $icon ) . '<span>' . esc_html( $label ) . '</span></a>';
}

function tsvd_anfragen_render_status_tabs( $status, $search, $breed ) {
	echo '<nav class="tsvd-anf-tabs" aria-label="' . esc_attr__( 'Nach Status filtern', 'tsvd' ) . '">';
	foreach ( tsvd_anfragen_primary_statuses() as $key => $label ) {
		$count = tsvd_anfragen_count( $key, $search, $breed );
		tsvd_anfragen_render_status_link( 'tsvd-anf-btn', $key, $label, $count, $status, $search, $breed );
	}
	echo '</nav>';
}

function tsvd_anfragen_render_status_link( $class, $key, $label, $count, $status, $search, $breed ) {
	$url = tsvd_anfragen_filter_url(
		array(
			'status' => $key,
			's'      => '' !== $search ? rawurlencode( $search ) : null,
			'breed'  => $breed,
		)
	);
	echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $url ) . '"' . tsvd_anfragen_current_attr( $status === $key ) . '>'
		. '<span>' . esc_html( $label ) . '</span> <span class="tsvd-anf-count">' . (int) $count . '</span></a>';
}

function tsvd_anfragen_render_mine_chip( $status, $search, $breed ) {
	$is_mine = 'mine' === $status;
	$count   = tsvd_anfragen_count( 'mine', $search, $breed );
	$url     = tsvd_anfragen_filter_url(
		array(
			'status' => $is_mine ? '' : 'mine',
			's'      => '' !== $search ? rawurlencode( $search ) : null,
			'breed'  => $breed,
		)
	);
	echo '<a class="tsvd-anf-btn" href="' . esc_url( $url ) . '"' . tsvd_anfragen_current_attr( $is_mine ) . '>'
		. '<span class="dashicons dashicons-admin-users" aria-hidden="true"></span>'
		. '<span>' . esc_html__( 'Mir zugewiesen', 'tsvd' ) . '</span> <span class="tsvd-anf-count">' . (int) $count . '</span></a>';
}

function tsvd_anfragen_render_more_menu( $status, $search, $breed ) {
	$more   = tsvd_anfragen_more_statuses();
	$active = isset( $more[ $status ] );
	$label  = $active ? $more[ $status ] : __( 'Mehr', 'tsvd' );

	echo '<details class="tsvd-anf-more"' . ( $active ? ' data-active="1"' : '' ) . '>';
	tsvd_anfragen_disclosure_summary( $label, $active );
	echo '<div class="tsvd-anf-more__menu">';
	foreach ( $more as $key => $item_label ) {
		$count = tsvd_anfragen_count( $key, $search, $breed );
		tsvd_anfragen_render_status_link( 'tsvd-anf-btn tsvd-anf-btn--menu-item', $key, $item_label, $count, $status, $search, $breed );
	}
	echo '</div></details>';
}

function tsvd_anfragen_filter_is_active( $status, $search, $breed ) {
	return '' !== $status || '' !== $search || $breed > 0;
}

function tsvd_anfragen_render_filter_state( $status, $search, $breed ) {
	if ( ! tsvd_anfragen_filter_is_active( $status, $search, $breed ) ) {
		return;
	}
	echo '<p class="tsvd-anf-filter-state">' . esc_html__( 'Filter aktiv.', 'tsvd' )
		. ' <a href="' . esc_url( tsvd_anfragen_reset_url() ) . '">' . esc_html__( 'Alle Filter zurücksetzen', 'tsvd' ) . '</a></p>';
}

function tsvd_anfragen_render_list_empty( $status, $search, $breed ) {
	echo '<div class="tsvd-msgr__list-empty"><p>' . esc_html__( 'Keine Anfragen für diese Auswahl.', 'tsvd' ) . '</p>';
	if ( tsvd_anfragen_filter_is_active( $status, $search, $breed ) ) {
		echo '<p><a href="' . esc_url( tsvd_anfragen_reset_url() ) . '">' . esc_html__( 'Alle Filter zurücksetzen', 'tsvd' ) . '</a></p>';
	}
	echo '</div>';
}

function tsvd_anfragen_disclosure_summary( $label, $is_current = false ) {
	echo '<summary class="tsvd-anf-btn"' . tsvd_anfragen_current_attr( $is_current ) . '><span>' . esc_html( $label ) . '</span>'
		. '<span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></summary>';
}
