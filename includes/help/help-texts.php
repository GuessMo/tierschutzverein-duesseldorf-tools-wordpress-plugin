<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/help-texts-animals.php';
require_once __DIR__ . '/help-texts-projects.php';
require_once __DIR__ . '/help-texts-content.php';
require_once __DIR__ . '/help-texts-forms.php';

function tsvd_help_build_targets( array $boxes, array $fields = array() ) {
	$targets = array();
	foreach ( $boxes as $key => $text ) {
		$targets[] = 'heading' === $key ? tsvd_help_heading( $text ) : tsvd_help_box( $key, $text );
	}
	foreach ( $fields as $name => $text ) {
		$targets[] = tsvd_help_field( $name, $text );
	}
	return $targets;
}

function tsvd_help_submit_text() {
	return __( "Was: Speichern und Veröffentlichen.\nWarum: Erst „Veröffentlichen“ macht den Eintrag auf der Website sichtbar.\nWo: Mit „Vorschau“ siehst Du die Seite vorher. Unter „Status“ hältst Du ihn als Entwurf zurück.", 'tsvd' );
}

function tsvd_help_seo_texts() {
	return array(
		'box'    => __( "Was: Bewertung, wie gut der Eintrag bei Google gefunden wird und wie gut er sich liest.\nWarum: Der Score (0–100, ab 80 grün, ab 50 gelb) zeigt, was sich noch verbessern lässt. Gendern oder die Sie-Anrede machen die Ampel immer rot.\nWo: Nur hier im Editor. „KI optimieren“ braucht einen eigenen Gemini-Schlüssel im Profil, „Prompt kopieren“ funktioniert ohne.", 'tsvd' ),
		'fields' => array(
			'tsvd_seo_exclude'          => __( "Was: Nimmt den Eintrag aus der SEO-Bewertung.\nWarum: Für Seiten, die nicht über Google gefunden werden müssen, z. B. Formular- oder Rechtsseiten.\nWo: Keyword und Meta-Description werden ausgeblendet und nicht mehr bewertet; Lesbarkeit wird weiter geprüft. Die Seite bleibt trotzdem für Google sichtbar.", 'tsvd' ),
			'tsvd_seo_focus_keyword'    => __( "Was: Der Suchbegriff, unter dem der Eintrag gefunden werden soll.\nWarum: Die Bewertung prüft, ob er in Titel, Meta-Description, erstem Absatz, einer Überschrift und der Adresse vorkommt.\nWo: Nur für die Bewertung – er erscheint nirgends auf der Website.", 'tsvd' ),
			'tsvd_seo_meta_description' => __( "Was: Der Text unter dem Link bei Google und in Link-Vorschauen (Facebook, WhatsApp …).\nWarum: Ein guter Text bringt mehr Klicks. Ideal sind 120–156 Zeichen.\nWo: Bleibt das Feld leer, wird der Auszug genommen, sonst der Textanfang, sonst die allgemeine Seitenbeschreibung.", 'tsvd' ),
		),
	);
}

function tsvd_help_post_type_targets() {
	return array(
		'animals'              => 'tsvd_help_animal_targets',
		'projects'             => 'tsvd_help_project_targets',
		'post'                 => 'tsvd_help_post_targets',
		'page'                 => 'tsvd_help_page_targets',
		'staff'                => 'tsvd_help_staff_targets',
		'tierschutzbrief'      => 'tsvd_help_tierschutzbrief_targets',
		'informationsmaterial' => 'tsvd_help_infomaterial_targets',
		'tsvd_form'            => 'tsvd_help_form_targets',
		'tsvd_newsletter'      => 'tsvd_help_newsletter_targets',
		'tsvd_update'          => 'tsvd_help_update_targets',
	);
}

function tsvd_help_editor_targets( $post_type ) {
	$map = tsvd_help_post_type_targets();
	return isset( $map[ $post_type ] ) ? call_user_func( $map[ $post_type ] ) : array();
}
