<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_help_display_fields() {
	return array(
		'tsvd_show_author_frontend' => __( "Was: Karten der Autorin bzw. des Autors und der Mitwirkenden unter dem Inhalt.\nWarum: Zeigt, wer den Beitrag geschrieben hat – pro Eintrag abschaltbar.\nWo: Unter dem Inhalt mit Name, Position und Link zum Team-Profil. Ist die Anzeige im Customizer global aus, erscheint nichts.", 'tsvd' ),
		'tsvd_show_post_title'      => __( "Was: Titel als Überschrift über dem Inhalt anzeigen.\nWarum: Ausschalten, wenn der Inhalt selbst schon mit einer Überschrift beginnt – sonst steht sie doppelt da.\nWo: Nur die Überschrift auf der Seite. Browser-Tab, Google und Link-Vorschau nutzen den Titel immer.", 'tsvd' ),
	);
}

function tsvd_help_display_boxes() {
	$seo = tsvd_help_seo_texts();
	return array(
		'tsvd_seo_score'             => $seo['box'],
		'tsvd_author_display_meta'   => __( "Was: Anzeige der Autorin bzw. des Autors.\nWarum: Namensnennung pro Eintrag steuern.\nWo: Karten unter dem Inhalt.", 'tsvd' ),
		'tsvd_show_post_title'       => __( "Was: Anzeigeoptionen für die Überschrift.\nWarum: Doppelte Überschriften vermeiden.\nWo: Überschrift über dem Inhalt.", 'tsvd' ),
		'tsvd_contributors_meta_box' => __( "Was: Wer außer der Autorin bzw. dem Autor mitgearbeitet hat, und wie viele Versionen es gibt.\nWarum: Nur zur Information, hier wird nichts gespeichert.\nWo: Mit „Autor im Frontend anzeigen“ erscheinen diese Personen auch als Karten unter dem Inhalt.", 'tsvd' ),
	);
}

function tsvd_help_post_targets() {
	$seo = tsvd_help_seo_texts();
	return tsvd_help_build_targets( tsvd_help_display_boxes(), array_merge( $seo['fields'], tsvd_help_display_fields() ) );
}

function tsvd_help_page_targets() {
	$seo   = tsvd_help_seo_texts();
	$boxes = array_merge(
		tsvd_help_display_boxes(),
		array( 'tsvd_page_image' => __( "Was: Großes Kopfbild über dem Seitentitel.\nWarum: Gibt der Seite ein Gesicht.\nWo: Kopf der Seite. Ohne eigenes Beitragsbild auch Bild beim Teilen in sozialen Netzwerken und auf Kacheln. Die Vorlage „Übersichtsseite“ zeigt kein Kopfbild.", 'tsvd' ) )
	);
	return tsvd_help_build_targets( $boxes, array_merge( $seo['fields'], tsvd_help_display_fields() ) );
}

function tsvd_help_staff_targets() {
	$boxes  = array(
		'heading'                => __( "Was: Ein Eintrag fürs Team.\nWarum: Daraus entstehen die Karten in der Mitarbeiter-Liste, das Profil und die Autorenkarten unter Beiträgen.\nWo: Der Titel entsteht automatisch aus Vor- und Nachname. Der Text im Editor ist die Bio auf dem Profil.", 'tsvd' ),
		'submitdiv'              => tsvd_help_submit_text(),
		'pageparentdiv'          => __( "Was: Reihenfolge in der Mitarbeiter-Liste.\nWarum: Legt fest, wer zuerst erscheint.\nWo: Block „Mitarbeiter-Liste“: kleinste Zahl zuerst, bei Gleichstand nach Name.", 'tsvd' ),
		'tsvd_staff_linked_user' => __( "Was: Das Website-Konto (oder mehrere), das zu dieser Person gehört.\nWarum: Beiträge dieses Kontos zeigen dann dieses Profil statt des Kontos.\nWo: Autorenkarten unter Beiträgen, „von …“ auf Beitragskarten, Abschnitt „Aktivität“ auf dem Profil. Fehlen Bio oder Bild, werden die des Kontos genommen.", 'tsvd' ),
		'tsvd_staff_dept_roles'  => __( "Was: Abteilung und Position, je Zeile ein Paar.\nWarum: Ordnet die Person im Team ein.\nWo: Karten der Mitarbeiter-Liste und deren Filter, Profil, Autorenkarten. Mit ↑/↓ änderst Du nur die Reihenfolge der Zeilen bei dieser Person. Zeilen ohne Abteilung werden verworfen; die Position „Teammitglied“ wird nicht angezeigt.", 'tsvd' ),
		'postimagediv'           => __( "Was: Profilfoto.\nWarum: Ohne Foto erscheint eine Silhouette oder das Bild des verknüpften Kontos.\nWo: Mitarbeiter-Liste, Teaser, Profil, Autorenkarten.", 'tsvd' ),
		'tagsdiv-staff_chapter'  => __( "Was: Kapitel, z. B. ein Bereich oder Team.\nWarum: Gruppiert Personen.\nWo: Filter und Kapitelzeile in der Mitarbeiter-Liste, auf Wunsch im Teaser-Block.", 'tsvd' ),
	);
	$fields = array(
		'tsvd_staff_first_name' => __( "Was: Vorname.\nWarum: Ergibt zusammen mit dem Nachnamen Titel und Adresse des Profils.\nWo: Überall, wo die Person genannt wird: Liste, Profil, Autorenkarten, „von …“ auf Beiträgen.", 'tsvd' ),
		'tsvd_staff_last_name'  => __( "Was: Nachname.\nWarum: Ergibt zusammen mit dem Vornamen Titel und Adresse des Profils.\nWo: Wie der Vorname; die Admin-Liste sortiert nach Nachname.", 'tsvd' ),
		'tsvd_staff_entry_date' => __( "Was: Eintrittsdatum.\nWarum: Nur zur internen Dokumentation.\nWo: Wird auf der Website nicht angezeigt.", 'tsvd' ),
		'tsvd_staff_exit_date'  => __( "Was: Austrittsdatum.\nWarum: Ehemalige werden automatisch ausgeblendet – ab dem Tag nach diesem Datum.\nWo: Verschwindet aus der Mitarbeiter-Liste; Autorenkarten werden grau mit „Bis …“ und verlinken nicht mehr. Teaser-Block und Profilseite bleiben erreichbar.", 'tsvd' ),
	);
	return tsvd_help_build_targets( $boxes, $fields );
}

function tsvd_help_pdf_fields( $kind ) {
	return array(
		'tsvd_article_image' => 'tierschutzbrief' === $kind
			? __( "Was: Titelbild der Ausgabe.\nWarum: Es ist das Gesicht der Ausgabe.\nWo: Großes Bild auf der Ausgabenseite (mit Eselsohr zum PDF) und Kachel im Archiv. Ist zusätzlich ein Beitragsbild gesetzt, nutzen Teaser-Blöcke und Link-Vorschau das Beitragsbild.", 'tsvd' )
			: __( "Was: Titelbild des Materials.\nWarum: Es ist das Gesicht des Eintrags.\nWo: Bild auf der Einzelseite und Hintergrund der Kachel im Archiv. Ist zusätzlich ein Beitragsbild gesetzt, nutzen Teaser-Blöcke und Link-Vorschau das Beitragsbild.", 'tsvd' ),
		'tsvd_article_pdf'   => __( "Was: Die PDF-Datei zum Herunterladen.\nWarum: Ohne PDF gibt es keinen Download.\nWo: Button „PDF herunterladen“ auf der Einzelseite; mit Titelbild ist zusätzlich die Bildecke verlinkt.", 'tsvd' ),
	);
}

function tsvd_help_tierschutzbrief_targets() {
	$boxes  = array(
		'heading'                      => __( "Was: Eine Ausgabe des Tierschutzbriefs.\nWarum: Ausgaben erscheinen im Archiv nach Jahr und Nummer.\nWo: Archiv /tierschutzbrief/, neueste zuerst, und in den News.", 'tsvd' ),
		'submitdiv'                    => tsvd_help_submit_text(),
		'categorydiv'                  => __( "Was: Kategorien der Ausgabe.\nWarum: Die Kategorie „Tierschutzbrief“ wird beim Speichern automatisch gesetzt.\nWo: Kategorie-Abzeichen auf den Kacheln der Teaser-Blöcke.", 'tsvd' ),
		'tsvd_tierschutzbrief_slug'    => __( "Was: Die Adresse der Ausgabe.\nWarum: Entsteht automatisch aus Jahr und Nummer; nur bei Bedarf überschreiben.\nWo: Adresse der Ausgabenseite. Alte Adressen /tierschutzbriefe/… leiten automatisch weiter.", 'tsvd' ),
		'tsvd_tierschutzbrief_details' => __( "Was: Jahr, Ausgabenummer, Titelbild und PDF.\nWarum: Aus Jahr und Nummer entsteht der Titel „Tierschutzbrief JJJJ Ausgabe N“ – nur wenn beides gesetzt ist.\nWo: Ausgabenseite und Archiv.", 'tsvd' ),
		'postimagediv'                 => __( "Was: Beitragsbild.\nWarum: Optional – ohne Beitragsbild wird überall das Titelbild genommen.\nWo: Ist es gesetzt, nutzen Teaser-Blöcke, Blog-Übersicht und Link-Vorschau dieses Bild statt des Titelbilds.", 'tsvd' ),
		'postexcerpt'                  => __( "Was: Kurztext zur Ausgabe.\nWarum: Ohne Auszug bleibt die Kachel ohne Text.\nWo: Kachel im Archiv und in Teaser-Blöcken (20 Wörter), Beschreibung bei Google.", 'tsvd' ),
	);
	$fields = array_merge(
		array(
			'tsvd_tierschutzbrief_year'   => __( "Was: Jahrgang der Ausgabe.\nWarum: Teil des automatischen Titels und Grundlage der Gruppierung im Archiv.\nWo: Archiv, nach Jahr gruppiert.", 'tsvd' ),
			'tsvd_tierschutzbrief_number' => __( "Was: Nummer der Ausgabe im Jahr.\nWarum: Teil des automatischen Titels.\nWo: Reihenfolge im Archiv innerhalb des Jahres.", 'tsvd' ),
		),
		tsvd_help_pdf_fields( 'tierschutzbrief' )
	);
	return tsvd_help_build_targets( $boxes, $fields );
}

function tsvd_help_infomaterial_targets() {
	$boxes = array(
		'heading'                           => __( "Was: Ein Informationsblatt zum Herunterladen.\nWarum: Interessierte finden Material gesammelt an einer Stelle.\nWo: Archiv /informationsmaterial/ mit Kacheln und eigene Einzelseite.", 'tsvd' ),
		'submitdiv'                         => tsvd_help_submit_text(),
		'tsvd_informationsmaterial_slug'    => __( "Was: Die Adresse des Eintrags.\nWarum: Nur bei Bedarf setzen – leer bleibt die Adresse aus dem Titel.\nWo: Adresse der Einzelseite.", 'tsvd' ),
		'tsvd_informationsmaterial_details' => __( "Was: Titelbild und PDF.\nWarum: Ohne beides wirkt der Eintrag leer.\nWo: Einzelseite und Kachel im Archiv.", 'tsvd' ),
		'postimagediv'                      => __( "Was: Beitragsbild.\nWarum: Optional – ohne Beitragsbild wird das Titelbild genommen.\nWo: Ist es gesetzt, nutzen Teaser-Blöcke und Link-Vorschau dieses Bild.", 'tsvd' ),
		'postexcerpt'                       => __( "Was: Kurztext zum Material.\nWarum: Erscheint auf der Kachel.\nWo: Kachel im Archiv (20 Wörter), Beschreibung bei Google.", 'tsvd' ),
	);
	return tsvd_help_build_targets( $boxes, tsvd_help_pdf_fields( 'informationsmaterial' ) );
}
