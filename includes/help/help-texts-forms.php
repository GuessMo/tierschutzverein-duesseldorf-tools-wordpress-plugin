<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_help_form_targets() {
	$boxes  = array(
		'heading'            => __( "Was: Ein Formular der Website.\nWarum: Besucher schicken darüber Anfragen, Meldungen oder Anmeldungen.\nWo: Einbinden per Block „Formular“, Shortcode, im Projekt oder auf Formularseiten. Nur veröffentlichte Formulare erscheinen.", 'tsvd' ),
		'submitdiv'          => tsvd_help_submit_text(),
		'tsvd_form_settings' => __( "Was: Empfänger, Betreff, Dankestext und Überschrift.\nWarum: Legt fest, wohin Einsendungen gehen und was Absender danach sehen.\nWo: Mail an den Empfänger und Meldung nach dem Absenden. Formulare für Privatvermittlung, Vermisstmeldung und Sichtung haben eigene, feste Betreffe und Texte.", 'tsvd' ),
		'tsvd_form_usage'    => __( "Was: So kommt das Formular auf eine Seite.\nWarum: Ein Formular erscheint nur dort, wo es eingebunden ist.\nWo: Block „Formular“ im Editor oder den Shortcode in eine Seite kopieren.", 'tsvd' ),
		'tsvd_form_fields'   => __( "Was: Die Felder des Formulars, in Gruppen mit 1 oder mehr Spalten.\nWarum: Pflichtfelder werden geprüft, bevor etwas verschickt wird. Ein Feld „Abschnitt / Schritt“ macht das Formular mehrstufig.\nWo: Formular auf der Website und Mail an den Empfänger (Feldname und Wert). Felder ohne Bezeichnung werden verworfen.", 'tsvd' ),
	);
	$fields = array(
		'form_show_title'      => __( "Was: Überschrift über dem Formular anzeigen.\nWarum: Ohne Überschrift steht das Formular für sich, z. B. unter einem eigenen Seitentitel.\nWo: Formular in Seiten und auf Tierseiten. Im Pop-up und bei mehrstufigen Formularen erscheint der Titel immer.", 'tsvd' ),
		'form_display_title'   => __( "Was: Text der sichtbaren Überschrift.\nWarum: Der interne Formularname passt nicht immer für Besucher.\nWo: Überschrift über dem Formular. Leer = Formularname.", 'tsvd' ),
		'form_recipient'       => __( "Was: E-Mail-Adresse, an die Einsendungen gehen.\nWarum: Ohne eigene Adresse gehen sie an die allgemeine Admin-Adresse.\nWo: Mail bei jeder Einsendung; auch Antwortadresse für Antworten aus den Anfragen. Ungültige Adressen werden nicht gespeichert.", 'tsvd' ),
		'form_subject'         => __( "Was: Betreff der Mail.\nWarum: Hilft, Einsendungen im Postfach zuzuordnen.\nWo: Betreff der Mail an den Empfänger. Leer = „Neue Formular-Einreichung“. Ein Feld „Tier – E-Mail Betreff“ im Formular ersetzt ihn.", 'tsvd' ),
		'form_success_message' => __( "Was: Dankestext nach dem Absenden.\nWarum: Bestätigt, dass die Nachricht angekommen ist.\nWo: Erscheint statt des Formulars. Leer = Standardtext. Bei Anfragen zu privaten oder vermissten Tieren erscheint ein Hinweis, dass die Nachricht an die Halterin bzw. den Halter weitergeleitet wurde.", 'tsvd' ),
		'form_persist_inquiry' => __( "Was: Einsendungen zusätzlich in den Anfragen speichern.\nWarum: Dann kann das Team sie dort beantworten, zuweisen und nachverfolgen.\nWo: Anfragen-Verwaltung. Privatvermittlung, Vermisstmeldung, Sichtungen und Anfragen zu privaten oder vermissten Tieren landen dort immer.", 'tsvd' ),
	);
	return tsvd_help_build_targets( $boxes, $fields );
}

function tsvd_help_newsletter_targets() {
	return tsvd_help_build_targets(
		array(
			'heading'                => __( "Was: Der interne Newsletter an die Mitarbeitenden.\nWarum: Informiert das Team über Neues – der Titel ist der Betreff der Mail.\nWo: Geht an alle Benutzer, die unter Newsletter → Abonnenten (intern) angehakt sind; Absender ist der interne Newsletter.", 'tsvd' ),
			'submitdiv'              => tsvd_help_submit_text(),
			'tsvd-newsletter-send'   => __( "Was: Vorschau, PDF, Testmail und Versand.\nWarum: Vor dem Versand prüfen – die Vorschau zeigt den gespeicherten Stand, also erst speichern.\nWo: „Testmail an mich“ geht nur an Dich. „An N senden“ schickt an alle Abonnenten und markiert enthaltene Website-Updates und Anleitungen als versendet. Es gibt keine Sperre gegen doppeltes Senden.", 'tsvd' ),
			'tsvd-newsletter-blocks' => __( "Was: Inhalt aus Blöcken: Text, Bild (mit Alt-Text), Website-Updates, Academy-Anleitungen.\nWarum: Website-Updates und Academy-Anleitungen füllen sich automatisch mit allem, was veröffentlicht und noch nicht versendet ist.\nWo: Inhalt der Mail. Leere Blöcke werden ausgelassen.", 'tsvd' ),
		)
	);
}

function tsvd_help_update_targets() {
	return tsvd_help_build_targets(
		array(
			'heading'   => __( "Was: Ein Eintrag „Neues von der Website“.\nWarum: Hält das Team über Änderungen an der Website auf dem Laufenden.\nWo: Erscheint im nächsten internen Newsletter (Titel, Datum, Text gekürzt auf 40 Wörter) – nur wenn veröffentlicht. Die Nummer wird beim ersten Speichern automatisch vergeben.", 'tsvd' ),
			'submitdiv' => tsvd_help_submit_text(),
		)
	);
}
