<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_help_project_boxes() {
	$seo = tsvd_help_seo_texts();
	return array(
		'heading'                   => __( "Was: Hier pflegst Du ein Projekt.\nWarum: Daraus entsteht die Projektseite mit Kopfbild, Text, Meilensteinen, Spendenbox, Impressionen, Social Media, Downloads und Formular – in dieser Reihenfolge.\nWo: Der Projekttext darf höchstens so viele Zeilen haben wie unter Projekte → Einstellungen festgelegt; die Grenze steht unter dem Textfeld. Darüber lässt sich nicht speichern.", 'tsvd' ),
		'submitdiv'                 => tsvd_help_submit_text(),
		'tsvd_seo_score'            => $seo['box'],
		'tsvd_project_images'       => __( "Was: Kopfbild der Projektseite und optional ein Logo.\nWarum: Das Desktop-Bild ist das Gesicht des Projekts in allen Übersichten.\nWo: Kopf der Projektseite, Kachel im Projekt-Archiv und in Teaser-Blöcken. Projekte haben kein eigenes Beitragsbild – beim Teilen in sozialen Netzwerken erscheint deshalb das allgemeine Standardbild.", 'tsvd' ),
		'pageparentdiv'             => __( "Was: Seitenvorlage des Projekts.\nWarum: „Projekt (Vorschau ohne Navigation)“ blendet Menü und Fußbereich aus – für Freigaben vor dem Livegang.\nWo: Inhaltlich gleich wie die Standardvorlage. Eine Reihenfolge-Zahl wirkt bei Projekten nicht; sortiert wird nach Datum.", 'tsvd' ),
		'tsvd_project_social_links' => __( "Was: Links zu den Social-Media-Kanälen des Projekts mit kurzer Beschreibung.\nWarum: Interessierte finden direkt die Kanäle des Projekts.\nWo: Abschnitt „Social Media“ auf der Projektseite nach den Impressionen. Leere Netzwerke fehlen, sind alle leer, fehlt der Abschnitt. Reihenfolge per Ziehpunkt ☰.", 'tsvd' ),
		'tsvd_project_form_link'    => __( "Was: Ein Formular für dieses Projekt, z. B. zum Mitmachen.\nWarum: Interessierte können sich direkt auf der Projektseite melden.\nWo: Letzter Abschnitt der Projektseite. Das Formular muss veröffentlicht sein; Empfänger und Texte stellst Du im Formular selbst ein.", 'tsvd' ),
		'tsvd_project_donations'    => __( "Was: Spendenbox über betterplace.\nWarum: Spenden direkt für dieses Projekt sammeln.\nWo: Karte „Unterstütze dieses Projekt“ nach den Meilensteinen. Sie erscheint nur, wenn Spendenbox und betterplace aktiviert sind und eine betterplace-Projekt-ID eingetragen ist – die Zahl aus der betterplace-Adresse (/projects/12345-…).", 'tsvd' ),
		'tsvd_project_milestones'   => __( "Was: Wichtige Schritte des Projekts mit Titel (Pflicht), Datum, Status, Fortschritt, Text und Bildern.\nWarum: Zeigt, was im Projekt passiert – neueste oben.\nWo: Abschnitt „Meilensteine“ nach dem Projekttext; die Karte zeigt 30 Wörter, der Klick den ganzen Text. „Auch in Aktuelles anzeigen“ bringt den Meilenstein als Kachel in die News. „Veröffentlichen am“ hält ihn bis zu diesem Termin verborgen. „Highlight“ hebt die Karte mit Stern hervor. Ohne Titel wird ein Meilenstein beim Speichern verworfen.", 'tsvd' ),
		'tsvd_project_impressions'  => __( "Was: Bildergalerie mit Eindrücken aus dem Projekt.\nWarum: Bilder machen das Projekt greifbar.\nWo: Abschnitt „Impressionen“ auf der Projektseite; Klick zeigt das Bild groß. Ohne Bilder fehlt der Abschnitt.", 'tsvd' ),
		'tsvd_project_assets'       => __( "Was: Dateien zum Herunterladen oder Links, z. B. Flyer.\nWarum: Material zum Projekt an einer Stelle.\nWo: Abschnitt mit Downloads auf der Projektseite vor dem Formular. Dateien werden direkt heruntergeladen, Links öffnen sich in neuem Tab. Ohne Titel wird der Dateiname gezeigt; Einträge ohne Datei bzw. Adresse werden verworfen.", 'tsvd' ),
		'postexcerpt'               => __( "Was: Kurztext zum Projekt.\nWarum: Ohne Auszug wird der Textanfang abgeschnitten.\nWo: Kachel im Projekt-Archiv und in Teaser-Blöcken (20 Wörter), Beschreibung bei Google und beim Teilen, wenn das SEO-Feld leer ist.", 'tsvd' ),
	);
}

function tsvd_help_project_fields() {
	$seo = tsvd_help_seo_texts();
	return array_merge(
		$seo['fields'],
		array(
			'project_desktop_image'          => __( "Was: Großes Kopfbild im Querformat (empfohlen 1920 × 1080).\nWarum: Ohne Desktop-Bild zeigt die Projektseite gar kein Kopfbild – auch nicht das Handy-Bild.\nWo: Kopf der Projektseite, Kachel im Projekt-Archiv und in Teaser-Blöcken, Ersatzbild für Meilensteine in „Aktuelles“.", 'tsvd' ),
			'project_mobile_image'           => __( "Was: Hochformat-Variante des Kopfbilds für Handys (empfohlen 768 × 1024).\nWarum: Auf schmalen Bildschirmen wirkt ein Hochformat besser.\nWo: Kopf der Projektseite auf Handys. Wirkt nur zusammen mit einem Desktop-Bild.", 'tsvd' ),
			'project_logo_image'             => __( "Was: Logo einer Kampagne oder Marke.\nWarum: Für Projekte ohne passendes Foto.\nWo: Erscheint klein auf der Projektseite – nur wenn weder Desktop- noch Handy-Bild gesetzt ist. Ersatzbild für Meilensteine in „Aktuelles“.", 'tsvd' ),
			'project_form_id'                => __( "Was: Das Formular, das auf der Projektseite erscheint.\nWarum: Damit sich Interessierte direkt melden können.\nWo: Letzter Abschnitt der Projektseite; „Kein Formular“ blendet ihn aus.", 'tsvd' ),
			'project_enable_donations'       => __( "Was: Hauptschalter der Spendenbox.\nWarum: Allein zeigt er noch nichts – zusätzlich braucht es betterplace und die Projekt-ID.\nWo: Karte „Unterstütze dieses Projekt“ auf der Projektseite.", 'tsvd' ),
			'project_betterplace_enabled'    => __( "Was: Spenden über betterplace anbieten.\nWarum: betterplace wickelt die Spende ab.\nWo: Spenden-Widget von betterplace in der Spendenbox der Projektseite.", 'tsvd' ),
			'project_betterplace_project_id' => __( "Was: Die Nummer des Projekts bei betterplace – die Zahl aus der Adresse (/projects/12345-…).\nWarum: Ohne Nummer erscheint keine Spendenbox.\nWo: Lädt das Spenden-Widget dieses betterplace-Projekts.", 'tsvd' ),
		)
	);
}

function tsvd_help_project_targets() {
	return tsvd_help_build_targets( tsvd_help_project_boxes(), tsvd_help_project_fields() );
}
