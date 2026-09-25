<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'tsvd_listing_admin_menu' );

function tsvd_listing_admin_menu() {
	if ( ! tsvd_tools_theme_active() ) {
		return;
	}
	add_submenu_page(
		'edit.php?post_type=animals',
		__( 'Anfragen zu Anzeigen', 'tsv-tools' ),
		__( 'Anfragen zu Anzeigen', 'tsv-tools' ),
		'manage_tsvd_anfragen',
		'tsvd-listing-inquiries',
		'tsvd_listing_render_page'
	);
}

function tsvd_listing_base_url() {
	return admin_url( 'edit.php?post_type=animals&page=tsvd-listing-inquiries' );
}

function tsvd_listing_kind_labels() {
	return array(
		'listing'  => __( 'Private Vermittlung', 'tsv-tools' ),
		'sighting' => __( 'Sichtung', 'tsv-tools' ),
	);
}

function tsvd_listing_render_page() {
	$filters = tsvd_listing_filters_from_request();
	$result  = tsvd_listing_query( $filters );
	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Anfragen zu Anzeigen', 'tsv-tools' ) . '</h1>';
	echo '<p class="description">' . esc_html( sprintf( __( 'Anfragen und Sichtungen zu privaten Anzeigen und Vermisstmeldungen. Wir leiten sie einmal mit den Kontaktdaten an den Halter weiter, danach klären beide alles direkt. Einträge werden nach %d Tagen automatisch gelöscht.', 'tsv-tools' ), tsvd_listing_retention_days() ) ) . '</p>';
	tsvd_listing_render_views( $filters );
	tsvd_listing_render_filters( $filters );
	tsvd_listing_render_table( $result['rows'] );
	tsvd_listing_render_pagination( $filters, $result['total'] );
	echo '</div>';
}

function tsvd_listing_render_views( $filters ) {
	$views = array( '' => __( 'Alle', 'tsv-tools' ) ) + tsvd_listing_kind_labels();
	$links = array();
	foreach ( $views as $kind => $label ) {
		$url     = '' === $kind ? tsvd_listing_base_url() : add_query_arg( 'kind', $kind, tsvd_listing_base_url() );
		$current = $filters['kind'] === $kind ? ' class="current" aria-current="page"' : '';
		$links[] = '<li><a href="' . esc_url( $url ) . '"' . $current . '>' . esc_html( $label ) . '</a></li>';
	}
	echo '<ul class="subsubsub">' . implode( ' | ', $links ) . '</ul>';
}

function tsvd_listing_render_filters( $filters ) {
	echo '<form method="get" class="tsvd-listing-filters"><p class="search-box">';
	echo '<input type="hidden" name="post_type" value="animals" /><input type="hidden" name="page" value="tsvd-listing-inquiries" />';
	if ( '' !== $filters['kind'] ) {
		echo '<input type="hidden" name="kind" value="' . esc_attr( $filters['kind'] ) . '" />';
	}
	echo '<label class="screen-reader-text" for="tsvd-listing-month">' . esc_html__( 'Monat', 'tsv-tools' ) . '</label>';
	echo '<select id="tsvd-listing-month" name="m"><option value="">' . esc_html__( 'Alle Monate', 'tsv-tools' ) . '</option>';
	foreach ( tsvd_listing_months() as $month ) {
		echo '<option value="' . esc_attr( $month ) . '"' . selected( $filters['month'], $month, false ) . '>' . esc_html( date_i18n( 'F Y', strtotime( $month . '-01' ) ) ) . '</option>';
	}
	echo '</select> ';
	echo '<label class="screen-reader-text" for="tsvd-listing-search">' . esc_html__( 'Suche', 'tsv-tools' ) . '</label>';
	echo '<input type="search" id="tsvd-listing-search" name="s" value="' . esc_attr( $filters['search'] ) . '" placeholder="' . esc_attr__( 'Tier, Name oder E-Mail', 'tsv-tools' ) . '" /> ';
	submit_button( __( 'Filtern', 'tsv-tools' ), '', '', false );
	echo '</p></form>';
}

function tsvd_listing_render_table( $rows ) {
	$labels = tsvd_listing_kind_labels();
	echo '<table class="wp-list-table widefat fixed striped"><thead><tr>';
	foreach ( array( __( 'Eingang', 'tsv-tools' ), __( 'Tier', 'tsv-tools' ), __( 'Art', 'tsv-tools' ), __( 'Person', 'tsv-tools' ), __( 'Nachricht', 'tsv-tools' ), __( 'Weiterleitung', 'tsv-tools' ) ) as $head ) {
		echo '<th scope="col">' . esc_html( $head ) . '</th>';
	}
	echo '</tr></thead><tbody>';
	if ( ! $rows ) {
		echo '<tr><td colspan="6">' . esc_html__( 'Keine Anfragen gefunden.', 'tsv-tools' ) . '</td></tr>';
	}
	foreach ( $rows as $row ) {
		tsvd_listing_render_row( $row, $labels );
	}
	echo '</tbody></table>';
}

function tsvd_listing_render_row( $row, $labels ) {
	$animal_id = (int) $row['animal_id'];
	$message   = tsvd_listing_message( $row );
	echo '<tr>';
	echo '<td>' . esc_html( date_i18n( 'd.m.Y H:i', strtotime( $row['created_at'] ) ) ) . '</td>';
	echo '<td><a href="' . esc_url( get_edit_post_link( $animal_id ) ) . '">' . esc_html( tsvd_halter_animal_name( $animal_id ) ) . '</a></td>';
	echo '<td>' . esc_html( $labels[ $row['kind'] ] ?? $row['kind'] ) . '</td>';
	echo '<td>' . esc_html( $row['applicant_name'] ) . '<br /><a href="mailto:' . esc_attr( $row['applicant_email'] ) . '">' . esc_html( $row['applicant_email'] ) . '</a>';
	echo '' !== trim( (string) $row['applicant_phone'] ) ? '<br />' . esc_html( $row['applicant_phone'] ) : '';
	echo '</td><td>' . ( '' !== $message ? nl2br( esc_html( $message ) ) : '—' ) . '</td>';
	echo '<td>' . tsvd_listing_forward_state( $row ) . '</td>';
	echo '</tr>';
}

function tsvd_listing_forward_state( $row ) {
	$states = array(
		'forwarded' => array( 'success', __( 'An Halter weitergeleitet', 'tsv-tools' ) ),
		'spam'      => array( 'danger', __( 'Spam', 'tsv-tools' ) ),
		'blocked'   => array( 'danger', __( 'Blockiert', 'tsv-tools' ) ),
	);
	$state = $states[ $row['status'] ] ?? array( 'warning', __( 'Nicht weitergeleitet', 'tsv-tools' ) );
	return '<span class="tsvd-text-' . $state[0] . '">' . esc_html( $state[1] ) . '</span>';
}

function tsvd_listing_render_pagination( $filters, $total ) {
	$pages = (int) ceil( $total / TSVD_LISTING_PER_PAGE );
	if ( $pages < 2 ) {
		return;
	}
	echo '<div class="tablenav bottom"><div class="tablenav-pages">' . paginate_links( array(
		'base'    => add_query_arg( 'paged', '%#%' ),
		'format'  => '',
		'current' => $filters['paged'],
		'total'   => $pages,
	) ) . '</div></div>';
}
