<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_help_animal_box_texts() {
	return array(
		'heading'                => __( "Was: Hier pflegst Du ein Tier – ein Vermittlungstier oder ein vermisstes Tier.\nWarum: Aus diesen Angaben entstehen Tierseite, Kacheln, Filter, Erfolgsgeschichten, Tier der Woche und die Mails an private Halter.\nWo: Unter „Tierdaten“ stehen Beschreibung und Merkmale, unter „Status“ Vermittlung, Halterdaten, Erfolgsgeschichte und Tier der Woche.", 'tsvd' ),
		'submitdiv'              => __( "Was: Speichern und Veröffentlichen.\nWarum: Erst „Veröffentlichen“ macht das Tier auf der Website sichtbar. Bei Vermisstmeldungen geht beim Veröffentlichen eine Mail an die Halterin oder den Halter.\nWo: Mit „Vorschau“ siehst Du die Tierseite vorher.", 'tsvd' ),
		'postimagediv'           => __( "Was: Das Hauptfoto des Tiers.\nWarum: Ohne Foto erscheint überall nur eine Pfoten-Grafik.\nWo: Großes Bild auf der Tierseite, Kacheln in allen Listen, Vorschau beim Teilen in sozialen Netzwerken, Tierkarte in den Anfragen und Ersatzbild für Tier der Woche.", 'tsvd' ),
		'animal_breed_selection' => __( "Was: Tierart und Rasse, auch mehrere.\nWarum: Die Tierart entscheidet, in welchem Bereich das Tier erscheint.\nWo: Rassenzeile auf Kachel und Tierseite, Filter „Tierart“, „Diese Tiere warten auch …“ (gleiche Rasse), automatischer Titel, Tier der Woche, Statistik und Anfragen. Nur bei Hunden erscheinen „Voraussetzungen“ und „Nori-Projekt“.", 'tsvd' ),
		'animal_title_permalink' => __( "Was: Titel und Link des Tiers, automatisch aus Name, Tierart, Rasse und Datum.\nWarum: Der Titel erscheint im Browser-Tab und bei Google.\nWo: Wird bei jedem Speichern neu erzeugt. Ändert sich der Link eines veröffentlichten Tiers, leitet die alte Adresse automatisch auf die neue weiter.", 'tsvd' ),
		'postexcerpt'            => __( "Was: Kurzer Text, der das Tier zusammenfasst.\nWarum: Ohne Auszug zeigt Google nur die allgemeine Seitenbeschreibung – mit Auszug einen passenden Text zu genau diesem Tier.\nWo: Beschreibung bei Google und beim Teilen in sozialen Netzwerken sowie in der Website-Suche.", 'tsvd' ),
		'animal_additional_images' => __( "Was: Weitere Fotos des Tiers.\nWarum: Mehrere Bilder zeigen das Tier in verschiedenen Situationen.\nWo: Galerie unter der Beschreibung auf der Tierseite (Klick öffnet die Großansicht), Ersatzbild für Tier der Woche.", 'tsvd' ),
	);
}

function tsvd_help_animal_data_field_texts() {
	return array(
		'animal_name'            => __( "Was: Der Rufname des Tiers.\nWarum: Er erscheint überall, wo Besucher, Halter und Team das Tier sehen.\nWo: Überschrift der Tierseite, Kacheln, Teilen-Button, Titel und Link der Seite, Tier der Woche, Mails an private Halter und Anfragen.", 'tsvd' ),
		'tsvd_record_type_ui'    => __( "Was: Vermittlungstier oder vermisstes Tier.\nWarum: Legt fest, welche Felder gelten und in welchen Listen das Tier erscheint.\nWo: Vermisste Tiere erscheinen nur auf „Vermisste Tiere“, nicht in der Vermittlung. Beim Wechsel auf „Vermisst“ wird „Zu vermitteln“ automatisch aufgehoben.", 'tsvd' ),
		'animal_teo_id'          => __( "Was: Die Nummer des Tiers in der Tierheim-Software.\nWarum: Damit lässt sich der Eintrag eindeutig dem Tier im Tierheim zuordnen.\nWo: Nur intern (Spalte in der Tierliste), nicht auf der Website.", 'tsvd' ),
		'animal_description'     => __( "Was: Text über Wesen, Geschichte und Bedürfnisse des Tiers.\nWarum: Das ist der wichtigste Text für Interessenten.\nWo: Abschnitt „Beschreibung“ auf der Tierseite und gekürzt (20 Wörter) auf jeder Kachel. Leer = kein Beschreibungsabschnitt.", 'tsvd' ),
		'animal_birthday_status' => __( "Was: Ist das Geburtsdatum bekannt, geschätzt oder unbekannt?\nWarum: Daraus wird das Alter berechnet.\nWo: „Alter“ und „Geburtstag“ auf der Tierseite; bei „Geschätzt“ nur das Alter mit Zusatz „(Geschätzt)“, bei „Unbekannt“ steht dort „Unbekannt“.", 'tsvd' ),
		'animal_life_stage'      => __( "Was: Jungtier, Erwachsen oder Senior.\nWarum: Besucher filtern die Vermittlung danach – ohne Angabe fällt das Tier bei gesetztem Filter heraus.\nWo: Filter „Lebensabschnitt“, Symbol auf der Kachel, Zeile auf der Tierseite. „Nicht definiert“ zeigt nichts an.", 'tsvd' ),
		'animal_color'           => __( "Was: Fellfarbe.\nWarum: Nur für die interne Übersicht.\nWo: Spalte in der Tierliste. Nicht auf der Website sichtbar.", 'tsvd' ),
		'animal_size_cm'         => __( "Was: Klein, Mittel oder Groß.\nWarum: Gibt Interessenten eine schnelle Einschätzung.\nWo: Symbol auf der Kachel und Zeile „Größe“ auf der Tierseite. Leer = „unbekannt“.", 'tsvd' ),
		'animal_gender'          => __( "Was: Weiblich, Männlich oder Nicht bekannt.\nWarum: Besucher filtern danach.\nWo: Filter „Geschlecht“, Symbol auf der Kachel, Zeile auf der Tierseite, Kacheln von Partnertieren. „Nicht bekannt“ zeigt kein Symbol.", 'tsvd' ),
		'animal_castrated'       => __( "Was: Ist das Tier kastriert?\nWarum: Eine der häufigsten Fragen von Interessenten.\nWo: Datenliste auf der Tierseite. Leer = „Unbekannt“.", 'tsvd' ),
		'animal_vaccinated'      => __( "Was: Ist das Tier geimpft?\nWarum: Eine der häufigsten Fragen von Interessenten.\nWo: Datenliste auf der Tierseite. Leer = „Unbekannt“.", 'tsvd' ),
		'animal_chipped'         => __( "Was: Ist das Tier gechippt?\nWarum: Eine der häufigsten Fragen von Interessenten.\nWo: Datenliste auf der Tierseite. Leer = „Unbekannt“.", 'tsvd' ),
		'animal_companion_animals' => __( "Was: Tiere, die nur gemeinsam mit diesem vermittelt werden.\nWarum: Verhindert, dass jemand ein Tier ohne seinen Partner anfragt.\nWo: Block „Wird nur vermittelt mit“ auf der Tierseite. Die Verknüpfung gilt automatisch in beide Richtungen. Ist ein Partner nicht mehr „Zu vermitteln“, verschwindet auch dieses Tier aus der Vermittlung.", 'tsvd' ),
	);
}

function tsvd_help_animal_status_field_texts() {
	return array(
		'animal_mediator'            => __( "Was: Wer vermittelt – der Tierschutzverein oder eine Privatperson.\nWarum: Bei „Privat“ gehen Anfragen per Mail direkt an die Halterin oder den Halter, und die Tierseite zeigt einen Hinweis zur Privatvermittlung.\nWo: Abzeichen „Vereins-“ bzw. „Privatvermittlung“, Filter „Vermittler“, Liste der Privatvermittlungen, Reihenfolge in der Vermittlung (Verein und Privat gemischt).", 'tsvd' ),
		'animal_adoption_status'     => __( "Was: Wo das Tier gerade steht.\nWarum: Nur „Zu vermitteln“ zeigt das Tier in der Vermittlung, mit Interesse-Anzeige und Interessensformular. „Vermittelt“ öffnet die Felder für die Erfolgsgeschichte.\nWo: Listen, Abzeichen auf der Tierseite, Statistik. Bei privaten Tieren lösen „Zu vermitteln“, „Vermittelt“ und das Pausieren (von „Zu vermitteln“ auf „Nicht in der Vermittlung“) jeweils eine Mail an die Halterin oder den Halter aus.", 'tsvd' ),
		'animal_participates_in_nori' => __( "Was: Das Tier ist Teil des Nori-Projekts.\nWarum: Nori-Tiere erscheinen in der Vermittlung wie „Zu vermitteln“.\nWo: Abzeichen „Nori-Projekt“ auf Kachel und Tierseite mit Link zur Nori-Seite. Nur bei Hunden.", 'tsvd' ),
		'animal_missing_status'      => __( "Was: Vermisst, gefunden oder wieder zuhause.\nWarum: Steuert die Seite „Vermisste Tiere“ und die Mails an die Halterin oder den Halter.\nWo: „Vermisst“ und „Gefunden“ erscheinen auf „Vermisste Tiere“, „Wieder zuhause“ nicht mehr. Nur bei „Vermisst“ gibt es das Sichtungsformular. „Gefunden“ und „Wieder zuhause“ lösen je eine Mail aus; solange „Vermisst“ gilt, kommt alle 28 Tage eine Erinnerung.", 'tsvd' ),
		'animal_missing_last_seen_date' => __( "Was: Wann das Tier zuletzt gesehen wurde.\nWarum: Hilft Suchenden einzuschätzen, ob eine Sichtung passt.\nWo: Vermisst-Box auf der Tierseite, nur wenn ausgefüllt.", 'tsvd' ),
		'animal_missing_last_seen_location' => __( "Was: Wo das Tier zuletzt gesehen wurde.\nWarum: Der wichtigste Hinweis für Menschen in der Nähe.\nWo: Vermisst-Box auf der Tierseite, nur wenn ausgefüllt.", 'tsvd' ),
		'animal_missing_chip_number' => __( "Was: Chipnummer des vermissten Tiers.\nWarum: Tierärzte und Tierheime können ein gefundenes Tier damit eindeutig zuordnen.\nWo: Vermisst-Box auf der Tierseite, nur wenn ausgefüllt.", 'tsvd' ),
		'animal_missing_reward'      => __( "Was: Ausgesetzte Belohnung.\nWarum: Kann zusätzliche Aufmerksamkeit bringen.\nWo: Vermisst-Box auf der Tierseite, nur bei „Vermisst“.", 'tsvd' ),
		'animal_missing_found_date'  => __( "Was: Wann das Tier gefunden wurde.\nWarum: Zur internen Dokumentation.\nWo: Nur hier im Editor, nicht auf der Website.", 'tsvd' ),
		'animal_missing_found_location' => __( "Was: Wo das Tier gefunden wurde.\nWarum: Zur internen Dokumentation.\nWo: Nur hier im Editor, nicht auf der Website.", 'tsvd' ),
		'animal_missing_reunited_date' => __( "Was: Wann das Tier wieder bei der Familie war.\nWarum: Zur internen Dokumentation.\nWo: Nur hier im Editor. Setzt die Halterin oder der Halter den Status selbst über den „Mein Tier“-Link, wird das Datum automatisch eingetragen.", 'tsvd' ),
		'animal_private_contact_firstname' => __( "Was: Vorname der Halterin oder des Halters.\nWarum: Persönliche Anrede in allen Mails.\nWo: Nur in Mails, nie auf der Website.", 'tsvd' ),
		'animal_private_contact_lastname' => __( "Was: Nachname der Halterin oder des Halters.\nWarum: Damit das Team weiß, wer das Tier betreut.\nWo: Nur intern, nie auf der Website.", 'tsvd' ),
		'animal_private_contact_address' => __( "Was: Anschrift der Halterin oder des Halters.\nWarum: Nur zur internen Information.\nWo: Wird nicht verwendet und nie auf der Website gezeigt.", 'tsvd' ),
		'animal_private_contact_email' => __( "Was: E-Mail-Adresse der Halterin oder des Halters.\nWarum: Ohne gültige Adresse gehen keine Mails raus und Anfragen werden nicht weitergeleitet.\nWo: Empfänger aller Halter-Mails (erhalten, online, vermittelt, pausiert, gefunden, wieder zuhause, Erinnerung) und der weitergeleiteten Besucher-Anfragen. Nie auf der Website sichtbar.", 'tsvd' ),
		'animal_adoption_requirements' => __( "Was: Was ein neues Zuhause erfüllen muss.\nWarum: Interessenten sehen vorab, ob sie passen – das spart unpassende Anfragen.\nWo: Box „Voraussetzungen für die Vermittlung“ auf der Tierseite und Abzeichen „Spez. Anforderungen“ auf der Kachel. Nur bei Hunden.", 'tsvd' ),
		'animal_adoption_date'       => __( "Was: Wann das Tier vermittelt wurde.\nWarum: Für Erfolgsgeschichte und Statistik.\nWo: „Vermittelt am“ in der Erfolgsgeschichte auf der Tierseite und Statistik „Vermittlungen pro Monat“. Wird beim Umschalten auf „Vermittelt“ auf heute gesetzt.", 'tsvd' ),
		'animal_owner_feedback_date' => __( "Was: Wann die neuen Besitzer sich gemeldet haben.\nWarum: Zeigt, wie aktuell die Erfolgsgeschichte ist.\nWo: „Rückmeldung vom“ in der Erfolgsgeschichte und Spalte in der Tierliste.", 'tsvd' ),
		'animal_adoption_source'     => __( "Was: Wie die neuen Besitzer auf das Tier aufmerksam wurden.\nWarum: Zeigt, über welche Wege Vermittlungen zustande kommen.\nWo: Statistik „Vermittelt nach Quelle“.", 'tsvd' ),
		'animal_show_in_frontend'    => __( "Was: Das Tier als Erfolgsgeschichte zeigen.\nWarum: Erfolgsgeschichten zeigen, dass Vermittlung gelingt.\nWo: Die Erfolgsgeschichte erscheint nur, wenn dieser Haken gesetzt ist, der Status „Vermittelt“ ist und ein Text der neuen Besitzer vorliegt. Dann steht das Tier unter „Erfolgreich vermittelte Tiere“ und die Tierseite zeigt „Glücklich vermittelt!“.", 'tsvd' ),
		'animal_adopted_owner_photos' => __( "Was: Fotos aus dem neuen Zuhause.\nWarum: Machen die Erfolgsgeschichte anschaulich.\nWo: „Fotos vom neuen Zuhause“ in der Erfolgsgeschichte. Fotos allein reichen nicht – es braucht auch den Text.", 'tsvd' ),
		'animal_adopted_owner_text'  => __( "Was: Rückmeldung oder Geschichte der neuen Besitzer.\nWarum: Pflicht für die Erfolgsgeschichte – ohne Text erscheint das Tier nicht unter „Erfolgreich vermittelte Tiere“.\nWo: Box „Glücklich vermittelt!“ auf der Tierseite.", 'tsvd' ),
		'animal_tdw_enabled'         => __( "Was: Das Tier als „Tier der Woche“ vorstellen.\nWarum: Ein eigener News-Beitrag bringt dem Tier zusätzliche Aufmerksamkeit.\nWo: Beim Speichern entsteht ein Beitrag „… ist unser Tier der Woche“ mit Text und Tierkachel. Ausschalten macht einen geplanten Beitrag wieder zum Entwurf; bereits veröffentlichte bleiben stehen.", 'tsvd' ),
		'animal_tdw_year'            => __( "Was: Jahr der Tier-der-Woche-Woche.\nWarum: Ohne Jahr und Woche entsteht kein Beitrag.\nWo: Liegt die Woche in der Zukunft, wird der Beitrag für Montag 0 Uhr geplant, sonst sofort veröffentlicht.", 'tsvd' ),
		'animal_tdw_week'            => __( "Was: Kalenderwoche für das Tier der Woche.\nWarum: Ohne Jahr und Woche entsteht kein Beitrag.\nWo: Liegt die Woche in der Zukunft, wird der Beitrag für Montag 0 Uhr geplant, sonst sofort veröffentlicht.", 'tsvd' ),
		'animal_tdw_article_image'   => __( "Was: Eigenes Bild für den Tier-der-Woche-Beitrag.\nWarum: Optional – ohne Auswahl wird das Profilbild genommen, sonst das erste Galeriebild.\nWo: Beitragsbild des Tier-der-Woche-Beitrags in den News.", 'tsvd' ),
		'animal_tdw_text'            => __( "Was: Text für den Tier-der-Woche-Beitrag.\nWarum: Stellt das Tier persönlich vor.\nWo: Steht im News-Beitrag vor der Tierkachel.", 'tsvd' ),
		'tsvd_interest_seed'         => __( "Was: Startwert für das Interesse am Tier.\nWarum: Neue Tiere haben noch keine Anfragen – der Startwert gibt ihnen eine Ausgangslage.\nWo: Interesse = Startwert + Anzahl der Anfragen. Daraus entsteht die Interesse-Anzeige auf der Tierseite und die Reihenfolge in der Vermittlung (standardmäßig wenig gefragte Tiere zuerst). Bleibt beim Zurücksetzen der Statistik erhalten.", 'tsvd' ),
	);
}

function tsvd_help_animal_targets() {
	$targets = array();
	foreach ( tsvd_help_animal_box_texts() as $key => $text ) {
		$targets[] = 'heading' === $key ? tsvd_help_heading( $text ) : tsvd_help_box( $key, $text );
	}
	$fields = array_merge( tsvd_help_animal_data_field_texts(), tsvd_help_animal_status_field_texts() );
	foreach ( $fields as $name => $text ) {
		$targets[] = tsvd_help_field( $name, $text );
	}
	return $targets;
}
