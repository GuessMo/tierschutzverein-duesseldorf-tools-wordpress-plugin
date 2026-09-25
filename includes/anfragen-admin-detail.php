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
		'not_for_adoption' => __( 'Nicht zu vermitteln', 'tsvd' ),
		'not_adoptable'    => __( 'Nicht vermittelbar', 'tsvd' ),
		'for_adoption'     => __( 'Zu vermitteln', 'tsvd' ),
		'adopted'          => __( 'Vermittelt', 'tsvd' ),
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
	$title     = tsvd_anfragen_animal_name( $animal_id );

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
	$calc_status   = tsvd_anfragen_calc_status( $anfrage );
	$label         = isset( $status_labels[ $calc_status ] ) ? $status_labels[ $calc_status ] : $calc_status;

	echo '<div class="tsvd-conv__head"><h2>' . esc_html( $anfrage['applicant_name'] );
	tsvd_anfragen_help_btn( __( 'Diese Konversation gehört zu einer Person, die Interesse an einem Tier gezeigt hat. Du siehst ihre Kontaktdaten, das Tier und den Status. Rechts legst Du fest, wer zuständig ist. Blockieren, Spam und Papierkorb findest Du unter „Weitere Aktionen“.', 'tsvd' ) );
	echo '</h2>';
	echo '<span class="tsvd-msgr__badge tsvd-msgr__badge--' . esc_attr( $calc_status ) . '">' . esc_html( $label ) . '</span>';
	tsvd_anfragen_render_conversation_actions( $anfrage );
	echo '</div>';

	echo '<p class="tsvd-conv__contact">' . esc_html( $anfrage['applicant_email'] );
	if ( ! empty( $anfrage['applicant_phone'] ) ) {
		echo ' &middot; ' . esc_html( $anfrage['applicant_phone'] );
	}
	echo '</p>';

	$payload = json_decode( $anfrage['payload'], true );
	if ( ! empty( $payload['interesse_tier'] ) ) {
		echo '<p class="tsvd-conv__interest">' . esc_html__( 'Interesse an Tier:', 'tsvd' ) . ' <strong>'
			. esc_html( $payload['interesse_tier'] ) . '</strong></p>';
	}

	tsvd_anfragen_render_animal_card( (int) $anfrage['animal_id'] );

	tsvd_anfragen_render_replies( $wpdb, $replies_table, $anfrage );
	if ( ! empty( $anfrage['deleted_at'] ) ) {
		echo '<p class="tsvd-conv__trash-note">' . esc_html__( 'Diese Anfrage liegt im Papierkorb.', 'tsvd' ) . '</p>';
	} else {
		tsvd_anfragen_render_reply_form( $anfrage );
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

function tsvd_anfragen_payload_facts( $anfrage, $skip = array() ) {
	$payload = json_decode( $anfrage['payload'], true );
	$fields  = tsvd_get_form_fields( (int) $anfrage['form_id'] );
	$facts   = array();
	foreach ( $fields as $field ) {
		$field_id = $field['id'];
		if ( in_array( $field_id, $skip, true ) ) {
			continue;
		}
		if ( ! isset( $payload[ $field_id ] ) || '' === $payload[ $field_id ] ) {
			continue;
		}
		$label = ! empty( $field['label'] ) ? $field['label'] : '';

		if ( '' === $label ) {
			$label_fallbacks = array(
				'zustimmung_datenschutz' => __( 'Datenschutz-Zustimmung', 'tsvd' ),
			);
			$label = isset( $label_fallbacks[ $field_id ] )
				? $label_fallbacks[ $field_id ]
				: $field_id;
		}
		$raw   = $payload[ $field_id ];
		if ( is_array( $raw ) ) {
			$parts = array();
			foreach ( $raw as $item ) {
				$parts[] = tsvd_anfragen_field_value_label( $field, $item );
			}
			$value = implode( ', ', $parts );
		} else {
			$value = tsvd_anfragen_field_value_label( $field, $raw );
		}
		$facts[] = array( 'label' => $label, 'value' => $value );
	}
	return $facts;
}

function tsvd_anfragen_reply_author( $reply, $applicant_name ) {
	$direction = $reply['direction'];
	$is_halter = 'halter' === ( $reply['party'] ?? '' );
	if ( 'in' === $direction ) {
		if ( $is_halter ) {
			return __( 'Halter/Besitzer', 'tsvd' );
		}
		return '' !== $applicant_name ? $applicant_name : __( 'Interessierte Person', 'tsvd' );
	}
	$name = (int) $reply['user_id'] ? tsvd_anfragen_user_label( (int) $reply['user_id'] ) : __( 'System', 'tsvd' );
	if ( $is_halter && 'out' === $direction ) {
		return sprintf( __( '%s → Halter/Besitzer', 'tsvd' ), $name );
	}
	if ( 'note' === $direction ) {
		return sprintf( __( 'Interne Notiz · %s', 'tsvd' ), $name );
	}
	return $name;
}

function tsvd_anfragen_render_replies( $wpdb, $replies_table, $anfrage ) {
	$id             = (int) $anfrage['id'];
	$applicant_name = $anfrage['applicant_name'];
	echo '<h2>' . esc_html__( 'Konversation', 'tsvd' );
	tsvd_anfragen_help_btn( __( 'Der vollständige Verlauf dieser Anfrage: ganz oben die Formularangaben der Person, darunter alle Nachrichten. Blau = Nachrichten vom Verein, weiß = Nachrichten der Person, gestrichelt = interne Notizen. Notizen und geplante Nachrichten kannst Du über die Symbole an der Nachricht bearbeiten oder löschen.', 'tsvd' ) );
	echo '</h2>';
	$replies = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$replies_table} WHERE anfrage_id = %d ORDER BY COALESCE(sent_at, scheduled_at) ASC, id ASC", $id ), ARRAY_A );
	$nonce   = wp_create_nonce( 'tsvd_anfrage_reply_' . $id );

	tsvd_anfragen_render_participants( $replies );

	echo '<div class="tsvd-chat" role="list" aria-label="' . esc_attr__( 'Nachrichtenverlauf', 'tsvd' ) . '" data-anfrage="' . esc_attr( $id ) . '" data-nonce="' . esc_attr( $nonce ) . '">';
	tsvd_anfragen_render_angaben_bubble( $anfrage );
	foreach ( $replies as $reply ) {
		tsvd_anfragen_render_reply_bubble( $reply, $applicant_name );
	}
	echo '</div>';
	tsvd_anfragen_chat_script();
}

function tsvd_anfragen_render_participants( $replies ) {
	$names = array();
	foreach ( $replies as $reply ) {
		if ( 'in' === $reply['direction'] ) {
			continue;
		}
		$uid = (int) $reply['user_id'];
		$key = $uid ? 'u' . $uid : 'system';
		if ( isset( $names[ $key ] ) ) {
			continue;
		}
		$names[ $key ] = $uid ? tsvd_anfragen_user_label( $uid ) : __( 'System', 'tsvd' );
	}
	if ( empty( $names ) ) {
		return;
	}
	echo '<div class="tsvd-conv__participants"><span class="tsvd-conv__participants-label">'
		. esc_html__( 'Beteiligt', 'tsvd' ) . ':</span> '
		. esc_html( implode( ' → ', $names ) ) . '</div>';
}

function tsvd_anfragen_render_angaben_bubble( $anfrage ) {
	$facts = tsvd_anfragen_payload_facts(
		$anfrage,
		array( 'bewerber_name', 'bewerber_telefon', 'bewerber_email', 'interesse_tier' )
	);
	if ( empty( $facts ) ) {
		return;
	}
	$name  = '' !== $anfrage['applicant_name'] ? $anfrage['applicant_name'] : __( 'Interessierte Person', 'tsvd' );
	$stamp = tsvd_anfragen_format_local( $anfrage['created_at'] );

	echo '<div class="tsvd-chat__msg tsvd-chat__msg--in" role="listitem">';
	echo '<div class="tsvd-chat__bubble">';
	echo '<div class="tsvd-chat__meta"><span class="tsvd-chat__author">' . esc_html( $name )
		. ' &middot; ' . esc_html( $stamp ) . '</span></div>';
	echo '<dl class="tsvd-chat__facts">';
	foreach ( $facts as $fact ) {
		echo '<div class="tsvd-chat__fact"><dt>' . esc_html( $fact['label'] ) . '</dt>'
			. '<dd>' . esc_html( $fact['value'] ) . '</dd></div>';
	}
	echo '</dl>';
	echo '</div></div>';
}

function tsvd_anfragen_render_reply_bubble( $reply, $applicant_name ) {
	$dir     = $reply['direction'];
	$rid     = (int) $reply['id'];
	$pending = ( 'out' === $dir && empty( $reply['sent_at'] ) && ! empty( $reply['scheduled_at'] ) );
	$author  = tsvd_anfragen_reply_author( $reply, $applicant_name );
	$stamp   = tsvd_anfragen_format_local( $reply['sent_at'] );
	$css     = 'tsvd-chat__msg tsvd-chat__msg--' . sanitize_html_class( $dir ) . ( $pending ? ' is-pending' : '' );

	echo '<div class="' . esc_attr( $css ) . '" role="listitem" data-reply="' . $rid . '">';
	echo '<div class="tsvd-chat__bubble">';
	$icon    = 'note' === $dir ? '<span class="dashicons dashicons-lock" aria-hidden="true"></span>' : '';
	echo '<div class="tsvd-chat__meta"><span class="tsvd-chat__author">' . $icon . esc_html( $author );
	if ( '' !== $stamp ) {
		echo ' &middot; ' . esc_html( $stamp );
	}
	if ( ! empty( $reply['edited_at'] ) ) {
		echo ' &middot; <em>' . esc_html__( 'bearbeitet', 'tsvd' ) . '</em>';
	}
	echo '</span>' . tsvd_anfragen_reply_tools( $reply, $pending ) . '</div>';
	echo '<div class="tsvd-chat__body">' . esc_html( $reply['body'] ) . '</div>';
	if ( $pending ) {
		$remaining = strtotime( $reply['scheduled_at'] . ' UTC' ) - time();
		echo '<div class="tsvd-chat__timer" data-remaining="' . max( 0, (int) $remaining ) . '">';
		echo '<span class="dashicons dashicons-clock"></span> <span class="tsvd-chat__timer-text"></span>';
		echo '</div>';
	}
	echo '</div></div>';
}

function tsvd_anfragen_reply_tools( $reply, $pending ) {
	if ( 'note' !== $reply['direction'] && ! $pending ) {
		return '';
	}
	$html = '<span class="tsvd-chat__tools">';
	if ( $pending ) {
		$html .= tsvd_anfragen_chat_tool( 'sendnow', __( 'Jetzt senden', 'tsvd' ), 'dashicons-yes-alt' );
	}
	$html .= tsvd_anfragen_chat_tool( 'edit', __( 'Bearbeiten', 'tsvd' ), 'dashicons-edit' );
	$html .= tsvd_anfragen_chat_tool( 'delete', $pending ? __( 'Abbrechen', 'tsvd' ) : __( 'Löschen', 'tsvd' ), 'dashicons-trash' );
	return $html . '</span>';
}

function tsvd_anfragen_chat_tool( $act, $label, $icon ) {
	return '<button type="button" class="tsvd-chat__tool" data-act="' . esc_attr( $act ) . '" title="' . esc_attr( $label ) . '" aria-label="' . esc_attr( $label ) . '">'
		. '<span class="dashicons ' . esc_attr( $icon ) . '" aria-hidden="true"></span></button>';
}

function tsvd_anfragen_chat_script() {
	static $done = false;
	if ( $done ) {
		return;
	}
	$done    = true;
	$strings = array(
		'confirmDelete' => __( 'Diese Nachricht löschen?', 'tsvd' ),
		'confirmCancel' => __( 'Geplanten Versand abbrechen und Entwurf löschen?', 'tsvd' ),
		'save'          => __( 'Speichern', 'tsvd' ),
		'cancel'        => __( 'Abbrechen', 'tsvd' ),
		'sendsIn'       => __( 'wird in %s gesendet', 'tsvd' ),
		'sendingNow'    => __( 'wird gesendet …', 'tsvd' ),
		'error'         => __( 'Fehler', 'tsvd' ),
	);
	?>
	<script>
	(function () {
		var chat = document.querySelector( '.tsvd-chat' );
		if ( ! chat ) return;
		var S = <?php echo wp_json_encode( $strings ); ?>;
		var anfrage = chat.getAttribute( 'data-anfrage' );
		var nonce = chat.getAttribute( 'data-nonce' );

		function post( action, data ) {
			data.action = action; data.anfrage = anfrage; data.nonce = nonce;
			jQuery.post( ajaxurl, data, function () { location.reload(); } ).fail( function () { alert( S.error ); } );
		}

		chat.addEventListener( 'click', function ( e ) {
			var tool = e.target.closest( '.tsvd-chat__tool' );
			if ( ! tool ) return;
			var msg = tool.closest( '.tsvd-chat__msg' );
			var rid = msg.getAttribute( 'data-reply' );
			var act = tool.getAttribute( 'data-act' );
			if ( 'delete' === act ) {
				var isPending = msg.classList.contains( 'is-pending' );
				if ( confirm( isPending ? S.confirmCancel : S.confirmDelete ) ) { post( 'tsvd_anfrage_reply_delete', { reply: rid } ); }
			} else if ( 'sendnow' === act ) {
				post( 'tsvd_anfrage_reply_sendnow', { reply: rid } );
			} else if ( 'edit' === act ) {
				startEdit( msg, rid );
			}
		} );

		function startEdit( msg, rid ) {
			if ( msg.querySelector( '.tsvd-chat__edit' ) ) return;
			var bodyEl = msg.querySelector( '.tsvd-chat__body' );
			var box = document.createElement( 'div' );
			box.className = 'tsvd-chat__edit';
			var ta = document.createElement( 'textarea' );
			ta.className = 'widefat'; ta.rows = 4; ta.value = bodyEl.textContent;
			var save = document.createElement( 'button' ); save.className = 'button button-primary button-small'; save.textContent = S.save;
			var cancel = document.createElement( 'button' ); cancel.className = 'button button-small'; cancel.textContent = S.cancel;
			var bar = document.createElement( 'p' ); bar.appendChild( save ); bar.appendChild( document.createTextNode( ' ' ) ); bar.appendChild( cancel );
			box.appendChild( ta ); box.appendChild( bar );
			bodyEl.style.display = 'none';
			bodyEl.parentNode.insertBefore( box, bodyEl.nextSibling );
			ta.focus();
			cancel.addEventListener( 'click', function () { box.remove(); bodyEl.style.display = ''; } );
			save.addEventListener( 'click', function () { save.disabled = true; post( 'tsvd_anfrage_reply_edit', { reply: rid, body: ta.value } ); } );
		}

		function fmt( s ) {
			if ( s <= 0 ) return null;
			var m = Math.floor( s / 60 ), sec = s % 60;
			return m + ':' + ( sec < 10 ? '0' : '' ) + sec;
		}
		chat.querySelectorAll( '.tsvd-chat__timer' ).forEach( function ( t ) {
			var rem = parseInt( t.getAttribute( 'data-remaining' ), 10 ) || 0;
			var txt = t.querySelector( '.tsvd-chat__timer-text' );
			var iv = setInterval( tick, 1000 );
			function tick() {
				var f = fmt( rem );
				txt.textContent = f ? S.sendsIn.replace( '%s', f ) : S.sendingNow;
				if ( rem <= 0 ) { clearInterval( iv ); return; }
				rem--;
			}
			tick();
		} );
	})();
	</script>
	<?php
}

/**
 * Versendet die Antwort-Mail an die interessierte Person (Reply-To =
 * Formular-Empfänger, Betreff mit Anfragen-Nummer). Reine Mail-Logik ohne
 * Verlauf-/Status-Update — genutzt von send_reply und dispatch_reply.
 *
 * @param array  $anfrage Anfragen-Datensatz (ARRAY_A).
 * @param string $body    Antworttext (bereits sanitiert erwartet).
 * @return bool Ob wp_mail den Versand angenommen hat.
 */
function tsvd_anfragen_mail_reply( $anfrage, $body, $party = 'applicant' ) {
	if ( 'halter' === $party ) {
		return tsvd_halter_mail_from_team( $anfrage, $body );
	}
	$reply_to = get_post_meta( (int) $anfrage['form_id'], '_tsvd_form_recipient', true );
	$reply_to = is_email( $reply_to ) ? $reply_to : get_option( 'admin_email' );
	$headers  = array( 'Reply-To: ' . $reply_to );
	$subject  = sprintf( __( 'Antwort auf deine Anfrage #%d', 'tsvd' ), (int) $anfrage['id'] );
	$signature = get_option( 'tsvd_anfragen_signature', '' );
	if ( '' !== trim( $signature ) ) {
		$body .= "\n\n" . $signature;
	}
	return wp_mail( $anfrage['applicant_email'], $subject, $body, $headers );
}

function tsvd_anfragen_send_reply( $id, $body, $user_id = 0, $party = 'applicant' ) {
	if ( '' === trim( $body ) ) {
		return new WP_Error( 'empty_body', __( 'Antworttext darf nicht leer sein.', 'tsvd' ) );
	}

	global $wpdb;
	$table   = tsvd_anfragen_table_name();
	$anfrage = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $id ), ARRAY_A );
	if ( ! $anfrage ) {
		return new WP_Error( 'not_found', __( 'Anfrage nicht gefunden.', 'tsvd' ) );
	}
	if ( ! is_email( tsvd_anfragen_party_email( $anfrage, $party ) ) ) {
		return new WP_Error( 'invalid_email', __( 'Keine gültige E-Mail-Adresse hinterlegt.', 'tsvd' ) );
	}

	if ( ! tsvd_anfragen_mail_reply( $anfrage, $body, $party ) ) {
		return new WP_Error( 'mail_failed', __( 'E-Mail konnte nicht gesendet werden.', 'tsvd' ) );
	}

	$now = current_time( 'mysql' );
	$wpdb->insert(
		tsvd_anfragen_replies_table_name(),
		array(
			'anfrage_id' => $id,
			'user_id'    => $user_id ?: null,
			'direction'  => 'out',
			'party'      => $party,
			'body'       => $body,
			'sent_at'    => $now,
		),
		array( '%d', '%d', '%s', '%s', '%s', '%s' )
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

	$body = isset( $_POST['body'] ) ? sanitize_textarea_field( wp_unslash( $_POST['body'] ) ) : '';
	$mode = isset( $_POST['mode'] ) && in_array( $_POST['mode'], array( 'note', 'halter' ), true ) ? $_POST['mode'] : 'reply';

	if ( 'note' === $mode ) {
		$result  = tsvd_anfragen_add_note( $id, $body, get_current_user_id() );
		$success = __( 'Notiz gespeichert.', 'tsvd' );
	} else {
		$result  = tsvd_anfragen_schedule_reply( $id, $body, get_current_user_id(), 'halter' === $mode ? 'halter' : 'applicant' );
		$success = tsvd_anfragen_send_delay() > 0 ? __( 'Antwort geplant.', 'tsvd' ) : __( 'Antwort gesendet.', 'tsvd' );
	}

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( array( 'message' => $result->get_error_message() ) );
	}

	wp_send_json_success( array( 'message' => $success ) );
}

function tsvd_anfragen_add_note( $id, $body, $user_id = 0 ) {
	if ( '' === trim( $body ) ) {
		return new WP_Error( 'empty_body', __( 'Notiz darf nicht leer sein.', 'tsvd' ) );
	}
	global $wpdb;
	$exists = $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM ' . tsvd_anfragen_table_name() . ' WHERE id = %d', $id ) );
	if ( ! $exists ) {
		return new WP_Error( 'not_found', __( 'Anfrage nicht gefunden.', 'tsvd' ) );
	}
	$wpdb->insert(
		tsvd_anfragen_replies_table_name(),
		array(
			'anfrage_id' => $id,
			'user_id'    => $user_id ?: null,
			'direction'  => 'note',
			'body'       => $body,
			'sent_at'    => current_time( 'mysql' ),
		),
		array( '%d', '%d', '%s', '%s', '%s' )
	);
	return true;
}
