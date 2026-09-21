<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'add_meta_boxes_' . TSVD_NEWSLETTER_CPT, 'tsvd_newsletter_editor_metabox' );

function tsvd_newsletter_editor_metabox() {
	add_meta_box(
		'tsvd-newsletter-blocks',
		__( 'Inhalt (Blöcke)', 'tsvd' ),
		'tsvd_newsletter_editor_render',
		TSVD_NEWSLETTER_CPT,
		'normal',
		'high'
	);
}

function tsvd_newsletter_editor_block_body( $index, $type, array $data ) {
	$definition = tsvd_newsletter_block_type( $type );
	if ( isset( $definition['edit'] ) && is_callable( $definition['edit'] ) ) {
		return call_user_func( $definition['edit'], $index, $data );
	}

	return '<p class="description">' . esc_html__( 'Wird automatisch aus Daten befüllt – keine Bearbeitung nötig.', 'tsvd' ) . '</p>';
}

function tsvd_newsletter_editor_row( $index, $type, array $data ) {
	$definition = tsvd_newsletter_block_type( $type );
	$label      = $definition ? $definition['label'] : $type;

	$html  = '<div class="tsvd-nl-block" data-type="' . esc_attr( $type ) . '">';
	$html .= '<div class="tsvd-nl-block-head">';
	$html .= '<span class="tsvd-nl-block-handle dashicons dashicons-menu" aria-hidden="true"></span>';
	$html .= '<span class="tsvd-nl-block-label">' . esc_html( $label ) . '</span>';
	$html .= '<button type="button" class="button-link tsvd-nl-block-remove" aria-label="'
		. esc_attr__( 'Block entfernen', 'tsvd' ) . '"><span class="dashicons dashicons-trash"></span></button>';
	$html .= '</div>';
	$html .= '<div class="tsvd-nl-block-body">';
	$html .= '<input type="hidden" class="tsvd-nl-block-type" name="newsletter_blocks[' . esc_attr( $index ) . '][type]" value="' . esc_attr( $type ) . '">';
	$html .= tsvd_newsletter_editor_block_body( $index, $type, $data );
	$html .= '</div></div>';

	return $html;
}

function tsvd_newsletter_picker_group( $heading, array $group ) {
	if ( empty( $group ) ) {
		return '';
	}

	$html  = '<div class="tsvd-nl-picker-group">';
	$html .= '<span class="tsvd-nl-picker-heading">' . esc_html( $heading ) . '</span>';
	$html .= '<div class="tsvd-nl-picker-items">';
	foreach ( $group as $key => $definition ) {
		$html .= '<button type="button" class="button tsvd-nl-pick" data-type="' . esc_attr( $key ) . '">'
			. esc_html( $definition['label'] ) . '</button>';
	}
	$html .= '</div></div>';

	return $html;
}

function tsvd_newsletter_editor_render( $post ) {
	wp_nonce_field( 'tsvd_newsletter_blocks_save', 'tsvd_newsletter_blocks_nonce' );

	$blocks = tsvd_newsletter_get_blocks( $post->ID );
	$types  = tsvd_newsletter_block_types();

	echo '<div class="tsvd-nl-editor">';

	echo '<div class="tsvd-nl-blocks" id="tsvd-nl-blocks">';
	foreach ( $blocks as $offset => $block ) {
		$data = isset( $block['data'] ) && is_array( $block['data'] ) ? $block['data'] : array();
		echo tsvd_newsletter_editor_row( (int) $offset, isset( $block['type'] ) ? $block['type'] : '', $data );
	}
	echo '</div>';

	echo '<div class="tsvd-nl-inserter">';
	echo '<button type="button" class="button button-secondary tsvd-nl-add" aria-expanded="false">'
		. '<span class="dashicons dashicons-plus-alt2"></span> ' . esc_html__( 'Block hinzufügen', 'tsvd' ) . '</button>';
	$static_types  = array();
	$dynamic_types = array();
	foreach ( $types as $key => $definition ) {
		if ( ! empty( $definition['dynamic'] ) ) {
			$dynamic_types[ $key ] = $definition;
		} else {
			$static_types[ $key ] = $definition;
		}
	}

	echo '<div class="tsvd-nl-picker" hidden>';
	echo tsvd_newsletter_picker_group( __( 'Inhaltsblöcke', 'tsvd' ), $static_types );
	echo tsvd_newsletter_picker_group( __( 'Dynamische Blöcke', 'tsvd' ), $dynamic_types );
	echo '</div></div>';

	foreach ( $types as $key => $definition ) {
		echo '<script type="text/template" class="tsvd-nl-tpl" data-type="' . esc_attr( $key ) . '">';
		echo tsvd_newsletter_editor_row( '{{INDEX}}', $key, array() );
		echo '</script>';
	}

	echo '</div>';
}

add_action( 'admin_enqueue_scripts', 'tsvd_newsletter_editor_assets' );

function tsvd_newsletter_editor_assets( $hook ) {
	if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || TSVD_NEWSLETTER_CPT !== $screen->post_type ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_editor();
	wp_enqueue_script( 'jquery-ui-sortable' );

	$js = TSVD_TOOLS_DIR . 'assets/newsletter-editor.js';
	wp_enqueue_script(
		'tsvd-newsletter-editor',
		TSVD_TOOLS_URL . 'assets/newsletter-editor.js',
		array( 'jquery', 'jquery-ui-sortable', 'wp-util' ),
		file_exists( $js ) ? (string) filemtime( $js ) : TSVD_TOOLS_VERSION,
		true
	);

	$iframe_css = TSVD_TOOLS_DIR . 'assets/dark-mode-editor-iframe.css';
	wp_localize_script(
		'tsvd-newsletter-editor',
		'tsvdNlEditor',
		array(
			'iframeCss' => TSVD_TOOLS_URL . 'assets/dark-mode-editor-iframe.css?ver='
				. ( file_exists( $iframe_css ) ? (string) filemtime( $iframe_css ) : TSVD_TOOLS_VERSION ),
		)
	);

	$css = TSVD_TOOLS_DIR . 'assets/newsletter-editor.css';
	wp_enqueue_style(
		'tsvd-newsletter-editor',
		TSVD_TOOLS_URL . 'assets/newsletter-editor.css',
		array(),
		file_exists( $css ) ? (string) filemtime( $css ) : TSVD_TOOLS_VERSION
	);
}

add_action( 'save_post_' . TSVD_NEWSLETTER_CPT, 'tsvd_newsletter_editor_save' );

function tsvd_newsletter_editor_save( $post_id ) {
	if ( ! isset( $_POST['tsvd_newsletter_blocks_nonce'] )
		|| ! wp_verify_nonce( $_POST['tsvd_newsletter_blocks_nonce'], 'tsvd_newsletter_blocks_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$raw = isset( $_POST['newsletter_blocks'] ) && is_array( $_POST['newsletter_blocks'] )
		? wp_unslash( $_POST['newsletter_blocks'] )
		: array();

	tsvd_newsletter_save_blocks( $post_id, array_values( $raw ) );
}
