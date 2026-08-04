<?php
/**
 * Anfragen-Dashboard: Detailansicht (Payload, Verlauf, Antworten, Löschen).
 *
 * @package TSVD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_render_detail( $id ) {
	global $wpdb;
	$table         = tsvd_anfragen_table_name();
	$replies_table = tsvd_anfragen_replies_table_name();
	$list_url      = admin_url( 'edit.php?post_type=animals&page=tsvd-anfragen' );

	$anfrage = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A );

	echo '<div class="wrap"><h1>' . esc_html__( 'Anfrage', 'tsvd' ) . '</h1>';
	echo '<p><a href="' . esc_url( $list_url ) . '">&laquo; ' . esc_html__( 'Zurück zur Liste', 'tsvd' ) . '</a></p>';

	if ( ! $anfrage ) {
		echo '<p>' . esc_html__( 'Anfrage nicht gefunden.', 'tsvd' ) . '</p></div>';
		return;
	}

	if ( isset( $_GET['sent'] ) ) {
		$ok = '1' === $_GET['sent'];
		echo '<div class="notice notice-' . ( $ok ? 'success' : 'error' ) . ' is-dismissible"><p>'
			. esc_html( $ok ? __( 'Antwort gesendet.', 'tsvd' ) : __( 'Antwort konnte nicht gesendet werden.', 'tsvd' ) )
			. '</p></div>';
	}

	tsvd_anfragen_render_payload( $anfrage );
	tsvd_anfragen_render_replies( $wpdb, $replies_table, $id, $anfrage['applicant_name'] );
	tsvd_anfragen_render_reply_form( $id );
	tsvd_anfragen_render_delete_form( $id );

	echo '</div>';
}

function tsvd_anfragen_render_payload( $anfrage ) {
	// Label:Wert-Aufbereitung analog zur Mail-Body-Erstellung in forms-ajax.php.
	$payload = json_decode( $anfrage['payload'], true );
	$fields  = tsvd_get_form_fields( (int) $anfrage['form_id'] );

	echo '<h2>' . esc_html__( 'Angaben', 'tsvd' ) . '</h2><table class="widefat striped"><tbody>';
	foreach ( $fields as $field ) {
		$field_id = $field['id'];
		if ( ! isset( $payload[ $field_id ] ) || '' === $payload[ $field_id ] ) {
			continue;
		}
		$label = ! empty( $field['label'] ) ? $field['label'] : $field_id;
		$val   = $payload[ $field_id ];
		if ( is_array( $val ) ) {
			$val = implode( ', ', $val );
		}
		echo '<tr><th style="width:240px;">' . esc_html( $label ) . '</th><td>' . esc_html( $val ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

function tsvd_anfragen_render_replies( $wpdb, $replies_table, $id, $applicant_name = '' ) {
	echo '<h2>' . esc_html__( 'Verlauf', 'tsvd' ) . '</h2>';
	$replies = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$replies_table} WHERE anfrage_id = %d ORDER BY sent_at ASC", $id ), ARRAY_A );

	if ( empty( $replies ) ) {
		echo '<p>' . esc_html__( 'Noch keine Antworten.', 'tsvd' ) . '</p>';
		return;
	}

	foreach ( $replies as $reply ) {
		if ( 'in' === $reply['direction'] ) {
			// Per IMAP-Abruf eingegangene Antwort der interessierten Person (siehe
			// anfragen-imap-poll.php) — kein WP-Benutzer, applicant_name statt Autor.
			$author = $applicant_name !== '' ? $applicant_name : __( 'Interessent:in', 'tsvd' );
		} else {
			$author = $reply['user_id'] ? get_the_author_meta( 'display_name', $reply['user_id'] ) : __( 'Unbekannt', 'tsvd' );
		}
		echo '<div style="border:1px solid #ccd0d4;padding:10px 14px;margin-bottom:10px;background:#fff;' . ( 'in' === $reply['direction'] ? 'border-left:4px solid #2271b1;' : '' ) . '">';
		echo '<p style="margin:0 0 6px;color:#666;font-size:12px;">' . esc_html( $author ) . ' &middot; ' . esc_html( get_date_from_gmt( $reply['sent_at'], 'Y-m-d H:i' ) ) . '</p>';
		echo '<p style="margin:0;white-space:pre-wrap;">' . esc_html( $reply['body'] ) . '</p>';
		echo '</div>';
	}
}

function tsvd_anfragen_render_reply_form( $id ) {
	$nonce = wp_create_nonce( 'tsvd_anfrage_reply_' . $id );
	?>
	<h2><?php esc_html_e( 'Antworten', 'tsvd' ); ?></h2>
	<textarea id="tsvd-anfrage-reply-body" class="widefat" rows="6"></textarea>
	<p>
		<button type="button" class="button button-primary" id="tsvd-anfrage-reply-send" data-id="<?php echo esc_attr( $id ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>">
			<?php esc_html_e( 'Antwort senden', 'tsvd' ); ?>
		</button>
		<span id="tsvd-anfrage-reply-result" style="margin-left:8px;"></span>
	</p>
	<script>
	(function () {
		var btn = document.getElementById( 'tsvd-anfrage-reply-send' );
		var result = document.getElementById( 'tsvd-anfrage-reply-result' );
		var body = document.getElementById( 'tsvd-anfrage-reply-body' );
		if ( ! btn ) return;
		btn.addEventListener( 'click', function () {
			btn.disabled = true;
			result.textContent = '...';
			result.style.color = '#666';
			jQuery.post( ajaxurl, {
				action: 'tsvd_anfrage_reply',
				id: btn.getAttribute( 'data-id' ),
				nonce: btn.getAttribute( 'data-nonce' ),
				body: body.value
			}, function ( response ) {
				btn.disabled = false;
				if ( response.success ) {
					window.location.reload();
				} else {
					result.textContent = response.data.message || '<?php echo esc_js( __( 'Fehler', 'tsvd' ) ); ?>';
					result.style.color = '#c00';
				}
			} ).fail( function () {
				btn.disabled = false;
				result.textContent = '<?php echo esc_js( __( 'Serverfehler', 'tsvd' ) ); ?>';
				result.style.color = '#c00';
			} );
		} );
	})();
	</script>
	<?php
}

function tsvd_anfragen_render_delete_form( $id ) {
	?>
	<p style="margin-top:2rem;">
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Anfrage wirklich löschen?', 'tsvd' ) ); ?>');">
			<input type="hidden" name="action" value="tsvd_anfrage_delete">
			<input type="hidden" name="id" value="<?php echo (int) $id; ?>">
			<?php wp_nonce_field( 'tsvd_anfrage_delete_' . $id ); ?>
			<button type="submit" class="button button-link-delete"><?php esc_html_e( 'Anfrage löschen', 'tsvd' ); ?></button>
		</form>
	</p>
	<?php
}

add_action( 'wp_ajax_tsvd_anfrage_reply', 'tsvd_ajax_anfrage_reply' );

function tsvd_ajax_anfrage_reply() {
	$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
	if ( ! $id || ! current_user_can( 'manage_tsvd_anfragen' ) ) {
		wp_send_json_error( array( 'message' => __( 'Keine Berechtigung.', 'tsvd' ) ) );
	}
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'tsvd_anfrage_reply_' . $id ) ) {
		wp_send_json_error( array( 'message' => __( 'Sicherheitsfehler.', 'tsvd' ) ) );
	}

	$body = isset( $_POST['body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['body'] ) ) : '';
	if ( '' === trim( $body ) ) {
		wp_send_json_error( array( 'message' => __( 'Antworttext darf nicht leer sein.', 'tsvd' ) ) );
	}

	global $wpdb;
	$table   = tsvd_anfragen_table_name();
	$anfrage = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A );
	if ( ! $anfrage ) {
		wp_send_json_error( array( 'message' => __( 'Anfrage nicht gefunden.', 'tsvd' ) ) );
	}
	if ( ! is_email( $anfrage['applicant_email'] ) ) {
		wp_send_json_error( array( 'message' => __( 'Keine gültige E-Mail-Adresse hinterlegt.', 'tsvd' ) ) );
	}

	// Reply-To = derselbe Empfänger, der im Formular für neue Anfragen hinterlegt ist
	// (_tsvd_form_recipient) — Antworten der interessierten Person landen dadurch in
	// derselben Mailbox, die auch die ursprüngliche Anfrage bekommen hat, statt im
	// technischen WordPress-Standardabsender zu verschwinden.
	$reply_to = get_post_meta( (int) $anfrage['form_id'], '_tsvd_form_recipient', true );
	$reply_to = is_email( $reply_to ) ? $reply_to : get_option( 'admin_email' );
	$headers  = array( 'Reply-To: ' . $reply_to );

	// Anfragen-Nummer im Betreff, damit ein späterer IMAP-Abruf die Antwort der
	// richtigen Anfrage zuordnen kann (Referenz-Token).
	$subject = sprintf( __( 'Antwort auf Ihre Anfrage #%d', 'tsvd' ), $id );

	$sent = wp_mail( $anfrage['applicant_email'], $subject, $body, $headers );
	if ( ! $sent ) {
		wp_send_json_error( array( 'message' => __( 'E-Mail konnte nicht gesendet werden.', 'tsvd' ) ) );
	}

	$now = current_time( 'mysql' );
	$wpdb->insert(
		tsvd_anfragen_replies_table_name(),
		array(
			'anfrage_id' => $id,
			'user_id'    => get_current_user_id(),
			'direction'  => 'out',
			'body'       => $body,
			'sent_at'    => $now,
		),
		array( '%d', '%d', '%s', '%s', '%s' )
	);

	$wpdb->update(
		$table,
		array( 'status' => 'answered', 'updated_at' => $now ),
		array( 'id' => $id ),
		array( '%s', '%s' ),
		array( '%d' )
	);

	wp_send_json_success( array( 'message' => __( 'Antwort gesendet.', 'tsvd' ) ) );
}
