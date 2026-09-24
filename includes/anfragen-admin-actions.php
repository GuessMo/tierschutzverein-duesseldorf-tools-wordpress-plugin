<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_action_form( $id, $action, $nonce_prefix, $label, $icon, $classes = '', $confirm = '' ) {
	$onsubmit = '' !== $confirm ? ' onsubmit="return confirm(\'' . esc_js( $confirm ) . '\');"' : '';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"' . $onsubmit . '>';
	echo '<input type="hidden" name="action" value="' . esc_attr( $action ) . '">';
	echo '<input type="hidden" name="id" value="' . (int) $id . '">';
	wp_nonce_field( $nonce_prefix . $id );
	echo '<button type="submit" class="tsvd-anf-btn ' . esc_attr( $classes ) . '">'
		. '<span class="dashicons ' . esc_attr( $icon ) . '" aria-hidden="true"></span>'
		. '<span>' . esc_html( $label ) . '</span></button></form>';
}

function tsvd_anfragen_render_conversation_actions( $anfrage ) {
	echo '<div class="tsvd-conv__actions">';
	if ( ! empty( $anfrage['deleted_at'] ) ) {
		tsvd_anfragen_render_trash_actions( (int) $anfrage['id'] );
	} else {
		tsvd_anfragen_render_assign_control( $anfrage );
		tsvd_anfragen_render_more_actions( $anfrage );
	}
	echo '</div>';
}

function tsvd_anfragen_render_trash_actions( $id ) {
	tsvd_anfragen_action_form( $id, 'tsvd_anfrage_restore', 'tsvd_anfrage_restore_', __( 'Wiederherstellen', 'tsvd' ), 'dashicons-undo', 'tsvd-anf-btn--primary' );
	tsvd_anfragen_action_form( $id, 'tsvd_anfrage_delete', 'tsvd_anfrage_delete_', __( 'Endgültig löschen', 'tsvd' ), 'dashicons-trash', 'tsvd-anf-btn--danger', __( 'Anfrage endgültig löschen? Das kann nicht rückgängig gemacht werden.', 'tsvd' ) );
}

function tsvd_anfragen_render_more_actions( $anfrage ) {
	$id = (int) $anfrage['id'];
	echo '<details class="tsvd-anf-more tsvd-anf-more--end">';
	tsvd_anfragen_overflow_summary( __( 'Weitere Aktionen', 'tsvd' ) );
	echo '<div class="tsvd-anf-more__menu">';
	if ( 'blocked' === $anfrage['status'] ) {
		tsvd_anfragen_action_form( $id, 'tsvd_anfrage_unblock', 'tsvd_anfrage_unblock_', __( 'Blockierung aufheben', 'tsvd' ), 'dashicons-shield', 'tsvd-anf-btn--menu-item', __( 'Blockierung aufheben und wieder als offen markieren?', 'tsvd' ) );
	} else {
		tsvd_anfragen_action_form( $id, 'tsvd_anfrage_block', 'tsvd_anfrage_block_', __( 'Absender blockieren', 'tsvd' ), 'dashicons-shield-alt', 'tsvd-anf-btn--menu-item', __( 'Absender blockieren? Weitere Anfragen dieser E-Mail-Adresse werden automatisch blockiert. Die Person wird nicht benachrichtigt.', 'tsvd' ) );
	}
	if ( 'spam' === $anfrage['status'] ) {
		tsvd_anfragen_action_form( $id, 'tsvd_anfrage_unspam', 'tsvd_anfrage_unspam_', __( 'Kein Spam', 'tsvd' ), 'dashicons-email', 'tsvd-anf-btn--menu-item' );
	} else {
		tsvd_anfragen_action_form( $id, 'tsvd_anfrage_spam', 'tsvd_anfrage_spam_', __( 'Als Spam markieren', 'tsvd' ), 'dashicons-warning', 'tsvd-anf-btn--menu-item', __( 'Anfrage als Spam markieren?', 'tsvd' ) );
	}
	tsvd_anfragen_action_form( $id, 'tsvd_anfrage_trash', 'tsvd_anfrage_trash_', __( 'In den Papierkorb', 'tsvd' ), 'dashicons-trash', 'tsvd-anf-btn--menu-item tsvd-anf-btn--danger', __( 'Anfrage in den Papierkorb verschieben?', 'tsvd' ) );
	echo '</div></details>';
}

function tsvd_anfragen_assign_options() {
	$options = array( 0 => __( 'Niemand', 'tsvd' ) );
	foreach ( tsvd_anfragen_eligible_assignees() as $user ) {
		$options[ (int) $user->ID ] = tsvd_anfragen_user_label( (int) $user->ID );
	}
	return $options;
}

function tsvd_anfragen_render_assign_control( $anfrage ) {
	$id      = (int) $anfrage['id'];
	$current = isset( $anfrage['assigned_user_id'] ) ? (int) $anfrage['assigned_user_id'] : 0;
	$options = tsvd_anfragen_assign_options();
	$label   = isset( $options[ $current ] ) ? $options[ $current ] : $options[0];

	echo '<details class="tsvd-anf-more">';
	tsvd_anfragen_disclosure_summary( sprintf( __( 'Zuständig: %s', 'tsvd' ), $label ) );
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="tsvd-anf-more__menu">';
	echo '<input type="hidden" name="action" value="tsvd_anfrage_assign">';
	echo '<input type="hidden" name="id" value="' . $id . '">';
	wp_nonce_field( 'tsvd_anfrage_assign_' . $id );
	foreach ( $options as $user_id => $name ) {
		echo '<button type="submit" class="tsvd-anf-btn tsvd-anf-btn--menu-item" name="assigned_user_id" value="' . (int) $user_id . '"'
			. tsvd_anfragen_current_attr( $current === $user_id ) . '><span>' . esc_html( $name ) . '</span></button>';
	}
	echo '</form></details>';
}
