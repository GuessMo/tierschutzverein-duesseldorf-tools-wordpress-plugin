<?php
/**
 * Anfragen-Dashboard: Detailansicht (Payload, Verlauf, Antworten, Löschen).
 *
 * @package TSVD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_anfragen_animal_adoption_label( $status ) {
	$map = array(
		'not_for_adoption' => __( 'Not for adoption', 'tsvd' ),
		'not_adoptable'    => __( 'Not adoptable', 'tsvd' ),
		'for_adoption'     => __( 'For adoption', 'tsvd' ),
		'adopted'          => __( 'Adopted', 'tsvd' ),
		'deceased'         => __( 'Verstorben', 'tsvd' ),
	);
	return isset( $map[ $status ] ) ? $map[ $status ] : '';
}

function tsvd_anfragen_render_animal_card( $animal_id ) {
	if ( ! $animal_id || 'animals' !== get_post_type( $animal_id ) ) {
		return;
	}
	$edit_link = get_edit_post_link( $animal_id );
	$view_link = get_permalink( $animal_id );
	$thumb     = get_the_post_thumbnail( $animal_id, array( 64, 64 ) );
	$title     = get_post_meta( $animal_id, 'animal_name', true );
	if ( '' === $title ) {
		$title = get_the_title( $animal_id );
	}

	$facts = array();
	$terms = get_the_terms( $animal_id, 'animal_breed' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		$facts[] = implode( ' · ', array_slice( wp_list_pluck( $terms, 'name' ), 0, 2 ) );
	}
	$adoption = tsvd_anfragen_animal_adoption_label( get_post_meta( $animal_id, 'animal_adoption_status', true ) );
	if ( $adoption ) {
		$facts[] = $adoption;
	}

	echo '<div class="tsvd-animal-card">';
	echo '<div class="tsvd-animal-card__thumb">' . ( $thumb ? $thumb : '<span class="dashicons dashicons-pets"></span>' ) . '</div>';
	echo '<div class="tsvd-animal-card__body">';
	echo '<div class="tsvd-animal-card__name">' . esc_html( $title ) . '</div>';
	if ( $facts ) {
		echo '<div class="tsvd-animal-card__facts">' . esc_html( implode( '  ·  ', $facts ) ) . '</div>';
	}
	echo '<div class="tsvd-animal-card__links">';
	if ( $edit_link ) {
		echo '<a href="' . esc_url( $edit_link ) . '">' . esc_html__( 'Datensatz öffnen', 'tsvd' ) . '</a>';
	}
	if ( $view_link ) {
		echo '<a href="' . esc_url( $view_link ) . '" target="_blank" rel="noopener">' . esc_html__( 'Profil ansehen', 'tsvd' ) . '</a>';
	}
	echo '</div></div></div>';
}

function tsvd_anfragen_render_conversation( $id ) {
	global $wpdb;
	$table         = tsvd_anfragen_table_name();
	$replies_table = tsvd_anfragen_replies_table_name();

	$anfrage = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A );
	if ( ! $anfrage ) {
		echo '<div class="tsvd-msgr__empty"><p>' . esc_html__( 'Anfrage nicht gefunden.', 'tsvd' ) . '</p></div>';
		return;
	}

	if ( isset( $_GET['sent'] ) ) {
		$ok = '1' === $_GET['sent'];
		echo '<div class="notice notice-' . ( $ok ? 'success' : 'error' ) . ' is-dismissible"><p>'
			. esc_html( $ok ? __( 'Antwort gesendet.', 'tsvd' ) : __( 'Antwort konnte nicht gesendet werden.', 'tsvd' ) )
			. '</p></div>';
	}

	$status_labels = tsvd_anfragen_status_labels();
	$label         = isset( $status_labels[ $anfrage['status'] ] ) ? $status_labels[ $anfrage['status'] ] : $anfrage['status'];

	echo '<div class="tsvd-conv__head"><h2>' . esc_html( $anfrage['applicant_name'] ) . '</h2>';
	echo '<span class="tsvd-msgr__badge">' . esc_html( $label ) . '</span>';
	tsvd_anfragen_render_conversation_actions( $anfrage );
	echo '</div>';

	echo '<p class="tsvd-conv__contact">' . esc_html( $anfrage['applicant_email'] );
	if ( ! empty( $anfrage['applicant_phone'] ) ) {
		echo ' &middot; ' . esc_html( $anfrage['applicant_phone'] );
	}
	echo '</p>';

	tsvd_anfragen_render_animal_card( (int) $anfrage['animal_id'] );

	echo '<details class="tsvd-conv__details"><summary>' . esc_html__( 'Angaben aus dem Formular', 'tsvd' ) . '</summary>';
	tsvd_anfragen_render_payload( $anfrage );
	echo '</details>';

	tsvd_anfragen_render_replies( $wpdb, $replies_table, $id, $anfrage['applicant_name'] );
	if ( ! empty( $anfrage['deleted_at'] ) ) {
		echo '<p class="tsvd-conv__trash-note">' . esc_html__( 'Diese Anfrage liegt im Papierkorb.', 'tsvd' ) . '</p>';
	} else {
		tsvd_anfragen_render_reply_form( $id );
	}
}

function tsvd_anfragen_field_value_label( $field, $value ) {
	$options = array();
	if ( ! empty( $field['options'] ) && is_array( $field['options'] ) ) {
		$options = $field['options'];
	} elseif ( function_exists( 'tsvd_get_form_field_types' ) ) {
		$types = tsvd_get_form_field_types();
		$type  = isset( $field['type'] ) ? $field['type'] : '';
		if ( isset( $types[ $type ]['default_options'] ) ) {
			$options = $types[ $type ]['default_options'];
		}
	}
	foreach ( $options as $option ) {
		if ( isset( $option['value'] ) && (string) $option['value'] === (string) $value ) {
			return isset( $option['label'] ) ? $option['label'] : $value;
		}
	}
	return $value;
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
		$raw   = $payload[ $field_id ];
		if ( is_array( $raw ) ) {
			$parts = array();
			foreach ( $raw as $item ) {
				$parts[] = tsvd_anfragen_field_value_label( $field, $item );
			}
			$val = implode( ', ', $parts );
		} else {
			$val = tsvd_anfragen_field_value_label( $field, $raw );
		}
		echo '<tr><th style="width:240px;">' . esc_html( $label ) . '</th><td>' . esc_html( $val ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

function tsvd_anfragen_chat_styles() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done = true;
	echo '<style>'
		. '.tsvd-chat{display:flex;flex-direction:column;gap:10px;width:100%;margin:0 0 16px;}'
		. '.tsvd-chat__msg{display:flex;}'
		. '.tsvd-chat__msg--out{justify-content:flex-end;}'
		. '.tsvd-chat__msg--in{justify-content:flex-start;}'
		. '.tsvd-chat__msg--note{justify-content:center;}'
		. '.tsvd-chat__bubble{position:relative;max-width:78%;padding:8px 12px;border-radius:3px;'
		. 'border:1px solid var(--tsvd-chrome-border,#c3c4c7);'
		. 'background:var(--tsvd-chrome-surface,#fff);color:var(--tsvd-chrome-text,#3c434a);}'
		. '.tsvd-chat__msg--out .tsvd-chat__bubble{background:var(--wp-admin-theme-color,#2271b1);'
		. 'border-color:var(--wp-admin-theme-color,#2271b1);color:#fff;}'
		. '.tsvd-chat__msg--out .tsvd-chat__bubble::after{content:"";position:absolute;bottom:-1px;'
		. 'right:-7px;border:7px solid transparent;border-bottom:0;border-right:0;'
		. 'border-left-color:var(--wp-admin-theme-color,#2271b1);}'
		. '.tsvd-chat__msg--in .tsvd-chat__bubble::after{content:"";position:absolute;bottom:-1px;'
		. 'left:-7px;border:7px solid transparent;border-bottom:0;border-left:0;'
		. 'border-right-color:var(--tsvd-chrome-border,#c3c4c7);}'
		. '.tsvd-chat__msg--note .tsvd-chat__bubble{max-width:90%;background:transparent;'
		. 'border-style:dashed;color:var(--tsvd-chrome-text-muted,#646970);font-style:italic;}'
		. '.tsvd-chat__meta{font-size:11px;opacity:.75;margin-bottom:3px;}'
		. '.tsvd-chat__body{white-space:pre-wrap;word-wrap:break-word;}'
		. '.tsvd-chat__empty{color:var(--tsvd-chrome-text-muted,#646970);}'
		. '</style>';
}

function tsvd_anfragen_reply_author( $reply, $applicant_name ) {
	$direction = $reply['direction'];
	if ( 'in' === $direction ) {
		return '' !== $applicant_name ? $applicant_name : __( 'Interessent:in', 'tsvd' );
	}
	$name = $reply['user_id'] ? get_the_author_meta( 'display_name', $reply['user_id'] ) : __( 'Unbekannt', 'tsvd' );
	if ( 'note' === $direction ) {
		return sprintf( __( 'Interne Notiz · %s', 'tsvd' ), $name );
	}
	return $name;
}

function tsvd_anfragen_render_replies( $wpdb, $replies_table, $id, $applicant_name = '' ) {
	echo '<h2>' . esc_html__( 'Konversation', 'tsvd' ) . '</h2>';
	tsvd_anfragen_chat_styles();
	$replies = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$replies_table} WHERE anfrage_id = %d ORDER BY sent_at ASC", $id ), ARRAY_A );

	echo '<div class="tsvd-chat">';
	if ( empty( $replies ) ) {
		echo '<p class="tsvd-chat__empty">' . esc_html__( 'Noch keine Nachrichten.', 'tsvd' ) . '</p></div>';
		return;
	}

	foreach ( $replies as $reply ) {
		$author = tsvd_anfragen_reply_author( $reply, $applicant_name );
		$css    = 'tsvd-chat__msg tsvd-chat__msg--' . sanitize_html_class( $reply['direction'] );
		echo '<div class="' . esc_attr( $css ) . '"><div class="tsvd-chat__bubble">';
		echo '<div class="tsvd-chat__meta">' . esc_html( $author ) . ' &middot; ' . esc_html( get_date_from_gmt( $reply['sent_at'], 'Y-m-d H:i' ) ) . '</div>';
		echo '<div class="tsvd-chat__body">' . esc_html( $reply['body'] ) . '</div>';
		echo '</div></div>';
	}
	echo '</div>';
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

function tsvd_anfragen_action_form( $id, $action, $nonce_prefix, $label, $icon = '', $classes = '', $confirm = '' ) {
	$onsubmit = '' !== $confirm ? ' onsubmit="return confirm(\'' . esc_js( $confirm ) . '\');"' : '';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"' . $onsubmit . '>';
	echo '<input type="hidden" name="action" value="' . esc_attr( $action ) . '">';
	echo '<input type="hidden" name="id" value="' . (int) $id . '">';
	wp_nonce_field( $nonce_prefix . $id );
	echo '<button type="submit" class="button button-small ' . esc_attr( $classes ) . '">';
	if ( '' !== $icon ) {
		echo '<span class="dashicons ' . esc_attr( $icon ) . '"></span> ';
	}
	echo esc_html( $label ) . '</button></form>';
}

function tsvd_anfragen_render_conversation_actions( $anfrage ) {
	$id = (int) $anfrage['id'];
	echo '<span class="tsvd-conv__actions">';
	if ( ! empty( $anfrage['deleted_at'] ) ) {
		tsvd_anfragen_action_form( $id, 'tsvd_anfrage_restore', 'tsvd_anfrage_restore_', __( 'Wiederherstellen', 'tsvd' ), 'dashicons-undo' );
		tsvd_anfragen_action_form( $id, 'tsvd_anfrage_delete', 'tsvd_anfrage_delete_', __( 'Endgültig löschen', 'tsvd' ), '', 'button-link-delete', __( 'Anfrage endgültig löschen? Dies kann nicht rückgängig gemacht werden.', 'tsvd' ) );
	} else {
		tsvd_anfragen_action_form( $id, 'tsvd_anfrage_trash', 'tsvd_anfrage_trash_', __( 'In den Papierkorb', 'tsvd' ), 'dashicons-trash', '', __( 'Anfrage in den Papierkorb verschieben?', 'tsvd' ) );
	}
	echo '</span>';
}

/**
 * Sendet eine Antwort auf eine Anfrage und protokolliert sie im Verlauf —
 * gemeinsame Logik für die AJAX-Aktion im Dashboard UND die MCP-Ability
 * tsv-tools/reply-to-anfrage (siehe anfragen-mcp-callbacks.php).
 *
 * @param int    $id      Anfragen-ID.
 * @param string $body    Antworttext (bereits sanitiert erwartet).
 * @param int    $user_id WP-Benutzer-ID des Absenders (0 = kein WP-Benutzer, z. B. MCP).
 * @return true|WP_Error
 */
function tsvd_anfragen_send_reply( $id, $body, $user_id = 0 ) {
	if ( '' === trim( $body ) ) {
		return new WP_Error( 'empty_body', __( 'Antworttext darf nicht leer sein.', 'tsvd' ) );
	}

	global $wpdb;
	$table   = tsvd_anfragen_table_name();
	$anfrage = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A );
	if ( ! $anfrage ) {
		return new WP_Error( 'not_found', __( 'Anfrage nicht gefunden.', 'tsvd' ) );
	}
	if ( ! is_email( $anfrage['applicant_email'] ) ) {
		return new WP_Error( 'invalid_email', __( 'Keine gültige E-Mail-Adresse hinterlegt.', 'tsvd' ) );
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
		return new WP_Error( 'mail_failed', __( 'E-Mail konnte nicht gesendet werden.', 'tsvd' ) );
	}

	$now = current_time( 'mysql' );
	$wpdb->insert(
		tsvd_anfragen_replies_table_name(),
		array(
			'anfrage_id' => $id,
			'user_id'    => $user_id ?: null,
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

	return true;
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

	$body   = isset( $_POST['body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['body'] ) ) : '';
	$result = tsvd_anfragen_send_reply( $id, $body, get_current_user_id() );

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}

	wp_send_json_success( array( 'message' => __( 'Antwort gesendet.', 'tsvd' ) ) );
}
