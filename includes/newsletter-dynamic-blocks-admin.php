<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'tsvd_newsletter_dynamic_blocks_menu', 20 );

function tsvd_newsletter_dynamic_blocks_menu() {
	add_submenu_page(
		'tsvd-newsletter',
		__( 'Dynamische Blöcke', 'tsvd' ),
		__( 'Dynamische Blöcke', 'tsvd' ),
		'manage_options',
		'tsvd-dynamic-blocks',
		'tsvd_newsletter_dynamic_blocks_page'
	);
}

function tsvd_newsletter_dynamic_blocks_page() {
	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Dynamische Newsletter-Blöcke', 'tsvd' ) . '</h1>';
	echo '<p class="description">' . esc_html__( 'Diese Blöcke befüllen sich beim Versand automatisch aus Inhalten im Hintergrund. Hier verwaltest du die zugrunde liegenden Daten.', 'tsvd' ) . '</p>';

	echo '<table class="wp-list-table widefat striped"><thead><tr>';
	echo '<th>' . esc_html__( 'Block', 'tsvd' ) . '</th>';
	echo '<th>' . esc_html__( 'Beschreibung', 'tsvd' ) . '</th>';
	echo '<th>' . esc_html__( 'Status', 'tsvd' ) . '</th>';
	echo '<th>' . esc_html__( 'Verwalten', 'tsvd' ) . '</th>';
	echo '</tr></thead><tbody>';

	$rendered = 0;
	foreach ( tsvd_newsletter_block_types() as $definition ) {
		if ( empty( $definition['dynamic'] ) ) {
			continue;
		}
		$rendered++;
		echo tsvd_newsletter_dynamic_block_row( $definition );
	}

	if ( 0 === $rendered ) {
		echo '<tr><td colspan="4">' . esc_html__( 'Keine dynamischen Blöcke registriert.', 'tsvd' ) . '</td></tr>';
	}

	echo '</tbody></table></div>';
}

function tsvd_newsletter_dynamic_block_row( array $definition ) {
	$admin       = isset( $definition['admin'] ) && is_array( $definition['admin'] ) ? $definition['admin'] : array();
	$label       = isset( $definition['label'] ) ? $definition['label'] : '';
	$description = isset( $admin['description'] ) ? $admin['description'] : '';
	$status      = ( isset( $admin['status'] ) && is_callable( $admin['status'] ) ) ? (string) call_user_func( $admin['status'] ) : '';
	$manage_url  = ( isset( $admin['manage_url'] ) && is_callable( $admin['manage_url'] ) ) ? (string) call_user_func( $admin['manage_url'] ) : '';

	$html  = '<tr>';
	$html .= '<td><strong>' . esc_html( $label ) . '</strong></td>';
	$html .= '<td>' . esc_html( $description ) . '</td>';
	$html .= '<td>' . esc_html( '' !== $status ? $status : __( 'automatisch', 'tsvd' ) ) . '</td>';
	$html .= '<td>';
	if ( '' !== $manage_url ) {
		$html .= '<a class="button" href="' . esc_url( $manage_url ) . '">' . esc_html__( 'Verwalten', 'tsvd' ) . '</a>';
	} else {
		$html .= '<span style="color:var(--tsvd-chrome-text-muted,#646970);">' . esc_html__( 'keine Pflege nötig', 'tsvd' ) . '</span>';
	}
	$html .= '</td></tr>';

	return $html;
}
