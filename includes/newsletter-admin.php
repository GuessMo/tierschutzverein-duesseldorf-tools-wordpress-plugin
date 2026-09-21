<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_NEWSLETTER_CAP = 'manage_options';

add_action( 'admin_menu', 'tsvd_newsletter_admin_menu' );

function tsvd_newsletter_admin_menu() {
	add_menu_page(
		__( 'Newsletter', 'tsvd' ),
		__( 'Newsletter', 'tsvd' ),
		TSVD_NEWSLETTER_CAP,
		'tsvd-newsletter',
		'tsvd_newsletter_render_subscribers_page',
		'dashicons-email-alt',
		59
	);

	add_submenu_page(
		'tsvd-newsletter',
		__( 'Abonnenten (intern)', 'tsvd' ),
		__( 'Abonnenten (intern)', 'tsvd' ),
		TSVD_NEWSLETTER_CAP,
		'tsvd-newsletter',
		'tsvd_newsletter_render_subscribers_page'
	);
}

function tsvd_newsletter_handle_save() {
	if ( ! isset( $_POST['tsvd_newsletter_subscribers_nonce'] ) ) {
		return null;
	}
	if ( ! wp_verify_nonce( $_POST['tsvd_newsletter_subscribers_nonce'], 'tsvd_newsletter_subscribers' ) ) {
		return null;
	}
	if ( ! current_user_can( TSVD_NEWSLETTER_CAP ) ) {
		return null;
	}

	$ids = isset( $_POST['tsvd_newsletter_subscribers'] ) && is_array( $_POST['tsvd_newsletter_subscribers'] )
		? $_POST['tsvd_newsletter_subscribers']
		: array();

	return tsvd_newsletter_set_subscribers( $ids );
}

function tsvd_newsletter_user_label( WP_User $user ) {
	$name = trim( $user->first_name . ' ' . $user->last_name );
	if ( '' === $name ) {
		$name = $user->display_name;
	}

	return $name;
}

function tsvd_newsletter_render_subscribers_page() {
	if ( ! current_user_can( TSVD_NEWSLETTER_CAP ) ) {
		return;
	}

	$result         = tsvd_newsletter_handle_save();
	$subscriber_ids = tsvd_newsletter_subscriber_ids();
	$users          = get_users( array( 'orderby' => 'display_name', 'order' => 'ASC' ) );

	echo '<div class="wrap">';
	echo '<h1>' . esc_html__( 'Newsletter – Abonnenten (intern)', 'tsvd' ) . '</h1>';

	if ( is_array( $result ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>';
		printf(
			esc_html__( 'Abonnenten aktualisiert: %1$d hinzugefügt, %2$d entfernt.', 'tsvd' ),
			(int) $result['added'],
			(int) $result['removed']
		);
		echo '</p></div>';
		$subscriber_ids = tsvd_newsletter_subscriber_ids();
	}

	echo '<p>' . esc_html__(
		'Interne Empfänger des Newsletters. Wähle die WP-/Mitarbeiter-Accounts aus, die den internen Newsletter erhalten sollen.',
		'tsvd'
	) . '</p>';

	tsvd_newsletter_render_subscribers_form( $users, $subscriber_ids );
	tsvd_newsletter_render_current_list( $subscriber_ids );

	echo '</div>';
}

function tsvd_newsletter_render_subscribers_form( array $users, array $subscriber_ids ) {
	echo '<form method="post">';
	wp_nonce_field( 'tsvd_newsletter_subscribers', 'tsvd_newsletter_subscribers_nonce' );

	echo '<input type="text" id="tsvd-newsletter-user-filter" placeholder="'
		. esc_attr__( 'Benutzer suchen…', 'tsvd' )
		. '" style="width:100%;max-width:480px;margin:8px 0;">';

	echo '<div class="tabs-panel" style="max-width:480px;max-height:320px;overflow:auto;margin-bottom:8px;">';
	echo '<ul class="categorychecklist form-no-clear" id="tsvd-newsletter-user-list">';
	foreach ( $users as $user ) {
		$checked = in_array( (int) $user->ID, $subscriber_ids, true ) ? ' checked' : '';
		echo '<li class="tsvd-newsletter-user"><label class="selectit">'
			. '<input type="checkbox" name="tsvd_newsletter_subscribers[]" value="' . esc_attr( $user->ID ) . '"' . $checked . '> '
			. esc_html( tsvd_newsletter_user_label( $user ) . ' (' . $user->user_email . ')' )
			. '</label></li>';
	}
	echo '</ul></div>';

	echo '<p class="description">'
		. esc_html__( 'Empfänger ankreuzen. Filter oben durchsucht die Liste.', 'tsvd' )
		. '</p>';

	submit_button( __( 'Abonnenten speichern', 'tsvd' ) );
	echo '</form>';

	tsvd_newsletter_filter_script();
}

function tsvd_newsletter_render_current_list( array $subscriber_ids ) {
	echo '<h2>' . esc_html__( 'Aktuelle Abonnenten', 'tsvd' ) . '</h2>';

	if ( empty( $subscriber_ids ) ) {
		echo '<p>' . esc_html__( 'Noch keine Abonnenten ausgewählt.', 'tsvd' ) . '</p>';
		return;
	}

	echo '<table class="wp-list-table widefat striped" style="max-width:640px;"><thead><tr>';
	echo '<th>' . esc_html__( 'Name', 'tsvd' ) . '</th>';
	echo '<th>' . esc_html__( 'E-Mail', 'tsvd' ) . '</th>';
	echo '</tr></thead><tbody>';
	foreach ( get_users( array( 'include' => $subscriber_ids, 'orderby' => 'display_name' ) ) as $user ) {
		echo '<tr><td>' . esc_html( tsvd_newsletter_user_label( $user ) ) . '</td>';
		echo '<td>' . esc_html( $user->user_email ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

function tsvd_newsletter_filter_script() {
	?>
	<script>
	(function () {
		var filter = document.getElementById('tsvd-newsletter-user-filter');
		var list = document.getElementById('tsvd-newsletter-user-list');
		if (!filter || !list) {
			return;
		}
		var items = Array.prototype.slice.call(list.querySelectorAll('li'));
		filter.addEventListener('input', function () {
			var query = this.value.toLowerCase().trim();
			items.forEach(function (item) {
				var match = !query || item.textContent.toLowerCase().indexOf(query) !== -1;
				item.style.display = match ? '' : 'none';
			});
		});
	})();
	</script>
	<?php
}
