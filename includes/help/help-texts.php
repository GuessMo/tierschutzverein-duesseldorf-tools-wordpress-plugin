<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_help_shared_box_texts() {
	return array(
		'submitdiv'                  => __( 'Hier speicherst Du den Eintrag. „Aktualisieren“ bzw. „Veröffentlichen“ macht ihn auf der Website sichtbar, „Vorschau“ zeigt ihn vorher an. Unter Status kannst Du ihn als Entwurf zurückhalten.', 'tsvd' ),
		'postimagediv'               => __( 'Das Hauptbild dieses Eintrags. Es erscheint in Übersichten und Kacheln auf der Website. Klicke auf das Bild, um es zu tauschen oder den Bildfokus zu setzen.', 'tsvd' ),
		'postexcerpt'                => __( 'Optionaler Kurztext zum Eintrag. Leer lassen, wenn der Anfang des Textes reicht.', 'tsvd' ),
		'categorydiv'                => __( 'Ordnet den Eintrag einer oder mehreren Kategorien zu. Darüber wird er auf der Website gruppiert und gefiltert.', 'tsvd' ),
		'pageparentdiv'              => __( 'Reihenfolge und Zuordnung des Eintrags. Eine kleinere Zahl bei „Reihenfolge“ steht weiter vorn.', 'tsvd' ),
		'tsvd_seo_score'             => __( 'Hilft, dass der Eintrag bei Google gut gefunden wird. Trage ein Fokus-Keyword (Suchbegriff) und eine Meta-Beschreibung (Text unter dem Link bei Google) ein. Der Score zeigt, was sich noch verbessern lässt.', 'tsvd' ),
		'tsvd_author_display_meta'   => __( 'Legt fest, ob der Name der Autorin oder des Autors auf der Website unter dem Eintrag steht.', 'tsvd' ),
		'tsvd_show_post_title'       => __( 'Legt fest, ob der Titel auf der Website als Überschrift angezeigt wird. Ausschalten, wenn der Inhalt selbst schon eine Überschrift hat.', 'tsvd' ),
		'tsvd_contributors_meta_box' => __( 'Zeigt, wer an diesem Eintrag mitgearbeitet hat. Die Liste entsteht automatisch aus den gespeicherten Versionen.', 'tsvd' ),
		'tsvd_page_image'            => __( 'Großes Kopfbild dieser Seite auf der Website.', 'tsvd' ),
	);
}

function tsvd_help_post_type_texts() {
	return array(
		'post'                 => array(),
		'page'                 => array(),
		'staff'                => array(
			'heading'                => __( 'Ein Eintrag für die Seite „Team“. Name, Foto, Abteilung und Position erscheinen in den Mitarbeiter-Listen der Website.', 'tsvd' ),
			'tsvd_staff_linked_user' => __( 'Verknüpft diesen Eintrag mit einem Benutzerkonto der Website. Dann erscheint dieses Profil als Autorin oder Autor unter Beiträgen dieser Person.', 'tsvd' ),
			'tsvd_staff_dept_roles'  => __( 'Abteilungen und Positionen dieser Person. Je Zeile eine Abteilung mit Position. „Sort.“ legt die Reihenfolge innerhalb der Abteilung fest.', 'tsvd' ),
			'tagsdiv-staff_chapter'  => __( 'Kapitel gruppieren Personen in der Mitarbeiter-Liste, z. B. nach Bereich. Mehrere Kapitel mit Kommas trennen.', 'tsvd' ),
		),
		'projects'             => array(
			'heading'                    => __( 'Hier bearbeitest Du ein Projekt. Text und Titel stehen oben, die Kästen darunter steuern Bilder, Meilensteine, Spenden, Downloads und Formular der Projektseite.', 'tsvd' ),
			'tsvd_project_images'        => __( 'Kopfbild der Projektseite, getrennt für große Bildschirme und Handys. Ein Logo nutzt Du statt eines Fotos für Kampagnen; es wird klein angezeigt.', 'tsvd' ),
			'tsvd_project_social_links'  => __( 'Links zu Social-Media-Kanälen des Projekts. Leere Felder werden nicht angezeigt, die Reihenfolge änderst Du am Ziehpunkt.', 'tsvd' ),
			'tsvd_project_form_link'     => __( 'Formular, das auf der Projektseite angeboten wird, z. B. zum Mitmachen. „Kein Formular“ blendet es aus.', 'tsvd' ),
			'tsvd_project_milestones'    => __( 'Wichtige Schritte des Projekts, angezeigt auf der Projektseite. Mit „+ Meilenstein hinzufügen“ legst Du einen neuen an.', 'tsvd' ),
			'tsvd_project_donations'     => __( 'Schaltet die Spendenbox auf der Projektseite ein. Optional lässt sich die Box mit betterplace verbinden.', 'tsvd' ),
			'tsvd_project_impressions'   => __( 'Bildergalerie mit Eindrücken aus dem Projekt.', 'tsvd' ),
			'tsvd_project_assets'        => __( 'Dateien, die Besucherinnen und Besucher auf der Projektseite herunterladen können, z. B. Flyer oder PDFs.', 'tsvd' ),
		),
		'tierschutzbrief'      => array(
			'heading'                      => __( 'Eine Ausgabe des Tierschutzbriefs. Jahr und Ausgabenummer erzeugen den Titel automatisch.', 'tsvd' ),
			'tsvd_tierschutzbrief_slug'    => __( 'Die Adresse der Ausgabe auf der Website. Sie entsteht automatisch und muss normalerweise nicht geändert werden.', 'tsvd' ),
			'tsvd_tierschutzbrief_details' => __( 'Jahr und Ausgabenummer. Daraus entsteht der Titel automatisch.', 'tsvd' ),
		),
		'informationsmaterial' => array(
			'heading'                           => __( 'Ein Informationsblatt zum Herunterladen. Titel, Vorschaubild und PDF erscheinen im Bereich Informationsmaterial der Website.', 'tsvd' ),
			'tsvd_informationsmaterial_slug'    => __( 'Die Adresse des Eintrags auf der Website. Sie entsteht automatisch und muss normalerweise nicht geändert werden.', 'tsvd' ),
			'tsvd_informationsmaterial_details' => __( 'Vorschaubild für Archiv und Artikel sowie die PDF-Datei, die heruntergeladen wird.', 'tsvd' ),
		),
		'tsvd_newsletter'      => array(
			'heading'                => __( 'Interner Newsletter an die Mitarbeitenden. Du baust ihn aus Blöcken zusammen, speicherst, prüfst die Vorschau und versendest ihn rechts.', 'tsvd' ),
			'tsvd-newsletter-send'   => __( 'Vorschau, PDF, Testmail an Dich und Versand an alle Abonnenten. Die Vorschau zeigt den gespeicherten Stand, also vorher speichern.', 'tsvd' ),
			'tsvd-newsletter-blocks' => __( 'Der Inhalt des Newsletters aus Text- und Bildblöcken. Der Block „Website-Updates“ füllt sich automatisch.', 'tsvd' ),
		),
		'tsvd_update'          => array(
			'heading' => __( 'Ein Eintrag für die Website-Updates im internen Newsletter. Kurz beschreiben, was sich auf der Website geändert hat.', 'tsvd' ),
		),
		'tsvd_form'            => array(
			'heading'            => __( 'Ein Formular der Website. Du legst Felder, Empfänger und Erfolgsmeldung fest und bindest es per Block oder Shortcode in Seiten ein.', 'tsvd' ),
			'tsvd_form_settings' => __( 'Wer die Einsendungen per E-Mail bekommt, mit welchem Betreff, und welche Meldung nach dem Absenden erscheint. Auf Wunsch landen Einsendungen zusätzlich in den Anfragen.', 'tsvd' ),
			'tsvd_form_usage'    => __( 'So bindest Du das Formular ein: Block „Formular“ im Editor oder den Shortcode in eine Seite kopieren.', 'tsvd' ),
			'tsvd_form_fields'   => __( 'Die Felder des Formulars. Mit + fügst Du Felder hinzu, am Ziehpunkt änderst Du die Reihenfolge.', 'tsvd' ),
		),
	);
}

function tsvd_help_editor_targets( $post_type ) {
	if ( 'animals' === $post_type ) {
		return tsvd_help_animal_targets();
	}
	$all = tsvd_help_post_type_texts();
	if ( ! isset( $all[ $post_type ] ) ) {
		return array();
	}
	$texts   = array_merge( tsvd_help_shared_box_texts(), $all[ $post_type ] );
	$targets = array();
	foreach ( $texts as $key => $text ) {
		$targets[] = 'heading' === $key ? tsvd_help_heading( $text ) : tsvd_help_box( $key, $text );
	}
	return $targets;
}
