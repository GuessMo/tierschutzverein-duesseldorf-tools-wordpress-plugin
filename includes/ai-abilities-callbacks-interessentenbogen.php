<?php
// AI Abilities — Interessentenbogen-Formular (Tiervermittlung), angelegt 2026-08-04.
// Bildet den vom Verein bereitgestellten Papier-Interessentenbogen 1:1 als tsvd_form
// ab (~40 Felder, 8 Gruppen). Eigene Datei statt Erweiterung von
// ai-abilities-callbacks.php, da diese bereits die Config-Datei-Zeilenobergrenze
// erreicht (gleiches Muster wie ai-abilities-callbacks-extra.php).

if (!defined('ABSPATH')) exit;

function tsvd_tools_ai_interessentenbogen_groups() {
    return array(
        array('id' => 'kontakt',       'columns' => 1, 'aligns' => array()),
        array('id' => 'wohnsituation', 'columns' => 2, 'aligns' => array()),
        array('id' => 'erfahrung',     'columns' => 1, 'aligns' => array()),
        array('id' => 'wuensche',      'columns' => 2, 'aligns' => array()),
        array('id' => 'aktivitaet',    'columns' => 2, 'aligns' => array()),
        array('id' => 'vorsorge',      'columns' => 2, 'aligns' => array()),
        array('id' => 'sonstiges',     'columns' => 1, 'aligns' => array()),
        array('id' => 'zustimmung',    'columns' => 1, 'aligns' => array()),
    );
}

/**
 * Feld-Reihenfolge = Render-Reihenfolge (forms-render.php gruppiert nach erstem
 * Auftreten im flachen fields-Array, nicht nach der Gruppen-Config-Reihenfolge) —
 * deshalb hier in einem Durchgang in finaler Gruppen-Reihenfolge aufgebaut, nicht
 * nachträglich sortiert.
 */
function tsvd_tools_ai_interessentenbogen_fields() {
    $heading = function ( $id, $text ) {
        return array(
            'id'               => $id,
            'type'             => 'description',
            'label'            => '',
            'description_text' => $text,
        );
    };

    $fields = array(
        // Kontaktblock
        array('id' => 'bewerber_name', 'type' => 'applicant_name', 'label' => __('Name, Vorname (aller Halter)', 'tsv-tools'), 'required' => true),
        array('id' => 'bewerber_geburtsdatum', 'type' => 'text', 'label' => __('Geburtsdatum (aller Halter)', 'tsv-tools'), 'required' => false),
        array('id' => 'bewerber_anschrift', 'type' => 'textarea', 'label' => __('Anschrift', 'tsv-tools'), 'required' => true, 'rows' => 2),
        array('id' => 'bewerber_telefon', 'type' => 'tel', 'label' => __('Telefon', 'tsv-tools'), 'required' => true),
        array('id' => 'bewerber_email', 'type' => 'email', 'label' => __('E-Mail', 'tsv-tools'), 'required' => true),
        array('id' => 'interesse_tier', 'type' => 'text', 'label' => __('Interesse an Tier (Name/ID, falls bekannt)', 'tsv-tools'), 'required' => false),
        array('id' => 'bewerbung_datum', 'type' => 'date', 'label' => __('Datum', 'tsv-tools'), 'required' => false),

        // 1. Wohnsituation & Haushalt
        $heading('s1_heading', '<h3>' . __('1. Wohnsituation &amp; Haushalt', 'tsv-tools') . '</h3>'),
        array('id' => 's1_wohnform', 'type' => 'radio', 'label' => __('Wohnform', 'tsv-tools'), 'options' => array(
            array('value' => 'wohnung', 'label' => __('Wohnung', 'tsv-tools')),
            array('value' => 'haus', 'label' => __('Haus', 'tsv-tools')),
        )),
        array('id' => 's1_garten', 'type' => 'checkbox', 'label' => __('mit Garten', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's1_garten_eingezaunt', 'type' => 'checkbox', 'label' => __('Garten ist sicher eingezäunt', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's1_miete_eigentum', 'type' => 'radio', 'label' => __('Wohnverhältnis', 'tsv-tools'), 'options' => array(
            array('value' => 'miete', 'label' => __('zur Miete', 'tsv-tools')),
            array('value' => 'eigentum', 'label' => __('Eigentum', 'tsv-tools')),
        )),
        array('id' => 's1_wohnflaeche_etage', 'type' => 'text', 'label' => __('Wohnfläche (qm) und Etage', 'tsv-tools')),
        array('id' => 's1_aufzug', 'type' => 'radio', 'label' => __('Aufzug', 'tsv-tools'), 'options' => array(
            array('value' => 'ja', 'label' => __('Aufzug vorhanden', 'tsv-tools')),
            array('value' => 'nein', 'label' => __('kein Aufzug', 'tsv-tools')),
        )),
        array('id' => 's1_wohnlage', 'type' => 'radio', 'label' => __('Wohnlage', 'tsv-tools'), 'options' => array(
            array('value' => 'laendlich', 'label' => __('ländlich', 'tsv-tools')),
            array('value' => 'stadtrand', 'label' => __('Stadtrand', 'tsv-tools')),
            array('value' => 'innenstadt', 'label' => __('Innenstadt', 'tsv-tools')),
        )),
        array('id' => 's1_garten_groesse_zaun', 'type' => 'text', 'label' => __('Falls Garten vorhanden: Größe (qm) und Höhe des Zauns', 'tsv-tools')),
        array('id' => 's1_freigang', 'type' => 'checkbox', 'label' => __('Freigang (bei Katzen) möglich', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's1_balkon', 'type' => 'checkbox', 'label' => __('gesicherter Balkon vorhanden', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's1_haustierhaltung', 'type' => 'radio', 'label' => __('Haustierhaltung', 'tsv-tools'), 'options' => array(
            array('value' => 'erlaubt', 'label' => __('vom Vermieter/von der Eigentümergemeinschaft erlaubt (Nachweis vorhanden)', 'tsv-tools')),
            array('value' => 'unklar', 'label' => __('noch zu klären', 'tsv-tools')),
        )),
        array('id' => 's1_anzahl_personen', 'type' => 'number', 'label' => __('Anzahl Personen im Haushalt', 'tsv-tools'), 'min' => 1),
        array('id' => 's1_kinder', 'type' => 'radio', 'label' => __('Kinder im Haushalt', 'tsv-tools'), 'options' => array(
            array('value' => 'ja', 'label' => __('Kinder im Haushalt', 'tsv-tools')),
            array('value' => 'nein', 'label' => __('keine Kinder im Haushalt', 'tsv-tools')),
        )),
        array('id' => 's1_kinder_alter', 'type' => 'text', 'label' => __('Alter der Kinder (falls vorhanden)', 'tsv-tools')),
        array('id' => 's1_weitere_haushaltsmitglieder_alter', 'type' => 'text', 'label' => __('Alter der weiteren Haushaltsmitglieder', 'tsv-tools')),
        array('id' => 's1_weitere_kinder_geplant', 'type' => 'radio', 'label' => __('Weitere Kinder geplant?', 'tsv-tools'), 'options' => array(
            array('value' => 'ja', 'label' => __('weitere Kinder geplant', 'tsv-tools')),
            array('value' => 'nein', 'label' => __('keine weiteren Kinder geplant', 'tsv-tools')),
        )),
        array('id' => 's1_besuch_kinder', 'type' => 'radio', 'label' => __('Regelmäßiger Besuch von Kindern/Erwachsenen', 'tsv-tools'), 'options' => array(
            array('value' => 'regelmaessig', 'label' => __('regelmäßig Besuch von Kindern und/oder Erwachsenen', 'tsv-tools')),
            array('value' => 'selten', 'label' => __('selten/kein regelmäßiger Besuch', 'tsv-tools')),
        )),
        array('id' => 's1_berufstaetigkeit', 'type' => 'radio', 'label' => __('Berufstätigkeit & Zeit für das Tier', 'tsv-tools'), 'options' => array(
            array('value' => 'vollzeit', 'label' => __('Vollzeit berufstätig', 'tsv-tools')),
            array('value' => 'teilzeit', 'label' => __('Teilzeit berufstätig', 'tsv-tools')),
            array('value' => 'homeoffice', 'label' => __('überwiegend Homeoffice', 'tsv-tools')),
            array('value' => 'nicht_berufstaetig', 'label' => __('nicht berufstätig / Rente', 'tsv-tools')),
        )),
        array('id' => 's1_alleinbleibzeit', 'type' => 'text', 'label' => __('Voraussichtliche durchschnittliche Alleinbleibzeit des Tieres pro Tag', 'tsv-tools')),

        // 2. Erfahrung mit Tieren
        $heading('s2_heading', '<h3>' . __('2. Erfahrung mit Tieren', 'tsv-tools') . '</h3>'),
        array('id' => 's2_erfahrung_haustier', 'type' => 'radio', 'label' => __('Erfahrung mit Haustieren', 'tsv-tools'), 'options' => array(
            array('value' => 'erstes', 'label' => __('erstes eigenes Haustier', 'tsv-tools')),
            array('value' => 'erfahren', 'label' => __('bereits Erfahrung mit Hunden/Katzen', 'tsv-tools')),
        )),
        array('id' => 's2_weitere_tiere', 'type' => 'checkbox', 'label' => __('aktuell weitere Tiere im Haushalt', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's2_rasseerfahrung', 'type' => 'checkbox', 'label' => __('Erfahrung mit bestimmten Rassen/Rassegruppen', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's2_weitere_tiere_details', 'type' => 'textarea', 'label' => __('Falls weitere Tiere im Haushalt: welche Art, Anzahl, Geschlecht?', 'tsv-tools'), 'rows' => 2),
        array('id' => 's2_erfahrung_details', 'type' => 'textarea', 'label' => __('Falls vorhanden: welche konkrete Erfahrung (Rassen, Problemverhalten, Krankheiten)?', 'tsv-tools'), 'rows' => 2),
        array('id' => 's2_problemverhalten', 'type' => 'checkbox', 'label' => __('Erfahrung mit Problemverhalten (z. B. Angst, Leinenaggression)', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's2_kranke_tiere', 'type' => 'checkbox', 'label' => __('Erfahrung mit kranken/alten/pflegebedürftigen Tieren', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's2_hundeschule', 'type' => 'radio', 'label' => __('Bereitschaft zu Hundeschule/Training', 'tsv-tools'), 'options' => array(
            array('value' => 'ja', 'label' => __('ja', 'tsv-tools')),
            array('value' => 'eventuell', 'label' => __('eventuell', 'tsv-tools')),
        )),

        // 3. Wünsche zum Tier
        $heading('s3_heading', '<h3>' . __('3. Wünsche zum Tier', 'tsv-tools') . '</h3>'),
        array('id' => 's3_tierart', 'type' => 'radio', 'label' => __('Gesucht wird', 'tsv-tools'), 'options' => array(
            array('value' => 'hund', 'label' => __('Hund', 'tsv-tools')),
            array('value' => 'katze', 'label' => __('Katze', 'tsv-tools')),
            array('value' => 'kleintier', 'label' => __('Kleintier', 'tsv-tools')),
            array('value' => 'andere', 'label' => __('andere Tierart', 'tsv-tools')),
        )),
        array('id' => 's3_andere_tierart_text', 'type' => 'text', 'label' => __('Andere Tierart (falls ausgewählt)', 'tsv-tools')),
        array('id' => 's3_alter', 'type' => 'radio', 'label' => __('Gewünschtes Alter', 'tsv-tools'), 'options' => array(
            array('value' => 'welpe', 'label' => __('Welpe/Jungtier', 'tsv-tools')),
            array('value' => 'erwachsen', 'label' => __('erwachsen', 'tsv-tools')),
            array('value' => 'senior', 'label' => __('Senior', 'tsv-tools')),
            array('value' => 'egal', 'label' => __('egal', 'tsv-tools')),
        )),
        array('id' => 's3_gesundheitl_einschraenkung', 'type' => 'checkbox', 'label' => __('offen für Tier mit gesundheitlichen Einschränkungen', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's3_verhaltensbesonderheit', 'type' => 'checkbox', 'label' => __('offen für Tier mit Verhaltensbesonderheiten', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's3_zweittier', 'type' => 'checkbox', 'label' => __('offen für Zweithund/-katze zu vorhandenem Tier', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's3_keine_besonderheiten', 'type' => 'checkbox', 'label' => __('kein Interesse an besonderen Bedürfnissen', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),

        // 4. Aktivität & Alltag
        $heading('s4_heading', '<h3>' . __('4. Aktivität &amp; Alltag', 'tsv-tools') . '</h3>'),
        array('id' => 's4_aktivitaetslevel', 'type' => 'radio', 'label' => __('Gewünschtes Aktivitätslevel des Tieres', 'tsv-tools'), 'options' => array(
            array('value' => 'ruhig', 'label' => __('ruhig', 'tsv-tools')),
            array('value' => 'mittel', 'label' => __('mittel', 'tsv-tools')),
            array('value' => 'sehr_aktiv', 'label' => __('sehr aktiv / sportlich', 'tsv-tools')),
        )),
        array('id' => 's4_zeit_beschaeftigung', 'type' => 'radio', 'label' => __('Zeit für Beschäftigung/Training', 'tsv-tools'), 'options' => array(
            array('value' => 'wenig', 'label' => __('wenig', 'tsv-tools')),
            array('value' => 'mittel', 'label' => __('mittel', 'tsv-tools')),
            array('value' => 'viel', 'label' => __('viel', 'tsv-tools')),
        )),

        // 5. Vorsorge & Absicherung
        $heading('s5_heading', '<h3>' . __('5. Vorsorge &amp; Absicherung', 'tsv-tools') . '</h3>'),
        array('id' => 's5_urlaub', 'type' => 'radio', 'label' => __('Urlaub: Ist die Versorgung des Tieres geregelt?', 'tsv-tools'), 'options' => array(
            array('value' => 'familie', 'label' => __('ja, durch Familie/Freunde', 'tsv-tools')),
            array('value' => 'tiersitter', 'label' => __('ja, durch Tiersitter/Tierpension', 'tsv-tools')),
            array('value' => 'unklar', 'label' => __('noch nicht geklärt', 'tsv-tools')),
        )),
        array('id' => 's5_urlaub_wer', 'type' => 'text', 'label' => __('Falls geklärt: durch wen konkret?', 'tsv-tools')),
        array('id' => 's5_erkrankung', 'type' => 'radio', 'label' => __('Eigene Erkrankung: Ist die Versorgung des Tieres geregelt?', 'tsv-tools'), 'options' => array(
            array('value' => 'geregelt', 'label' => __('ja, geregelt', 'tsv-tools')),
            array('value' => 'unklar', 'label' => __('noch nicht geklärt', 'tsv-tools')),
        )),
        array('id' => 's5_erkrankung_wer', 'type' => 'text', 'label' => __('Falls geklärt: durch wen konkret?', 'tsv-tools')),
        array('id' => 's5_finanzen_ruecklagen', 'type' => 'checkbox', 'label' => __('Rücklagen vorhanden', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's5_finanzen_versicherung', 'type' => 'checkbox', 'label' => __('Tierkrankenversicherung vorhanden/geplant', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),
        array('id' => 's5_finanzen_nicht_abgesichert', 'type' => 'checkbox', 'label' => __('derzeit nicht abgesichert', 'tsv-tools'), 'options' => array(array('value' => 'ja', 'label' => __('trifft zu', 'tsv-tools')))),

        // 6. Sonstige Angaben
        $heading('s6_heading', '<h3>' . __('6. Sonstige Angaben', 'tsv-tools') . '</h3>'),
        array('id' => 's6_motivation', 'type' => 'textarea', 'label' => __('Motivation für die Adoption / Warum dieses Tier?', 'tsv-tools'), 'rows' => 3),
        array('id' => 's6_anmerkungen', 'type' => 'textarea', 'label' => __('Sonstige Anmerkungen (z. B. geplanter Umzug, weitere Haustiere geplant, Allergien im Haushalt)', 'tsv-tools'), 'rows' => 3),

        // Zustimmung
        array(
            'id'                => 'zustimmung_datenschutz',
            'type'              => 'description',
            'label'             => '',
            'description_text'  => __('Die Angaben in diesem Formular werden ausschließlich zur Bearbeitung Ihrer Vermittlungsanfrage verwendet.', 'tsv-tools'),
            'show_acceptance'   => true,
            'acceptance_label'  => __('Ich habe die Datenschutzhinweise gelesen und bin mit der Verarbeitung meiner Daten zur Bearbeitung dieser Anfrage einverstanden.', 'tsv-tools'),
            'required'          => true,
        ),
    );

    $group_map = array(
        'bewerber_' => 'kontakt', 'interesse_tier' => 'kontakt', 'bewerbung_datum' => 'kontakt',
        's1_' => 'wohnsituation', 's2_' => 'erfahrung', 's3_' => 'wuensche',
        's4_' => 'aktivitaet', 's5_' => 'vorsorge', 's6_' => 'sonstiges',
        'zustimmung_' => 'zustimmung',
    );

    foreach ( $fields as &$field ) {
        $group_id = 'kontakt';
        foreach ( $group_map as $prefix => $gid ) {
            if ( strpos( $field['id'], $prefix ) === 0 ) {
                $group_id = $gid;
                break;
            }
        }
        $field['group_id'] = $group_id;
        $field['group_column'] = 0;
        if ( ! isset( $field['required'] ) ) {
            $field['required'] = false;
        }
    }
    unset( $field );

    return $fields;
}

function tsvd_tools_ai_write_interessentenbogen_fields( $form_id ) {
    update_post_meta( $form_id, '_tsvd_form_fields', tsvd_tools_ai_interessentenbogen_fields() );
    update_post_meta( $form_id, '_tsvd_form_groups_config', tsvd_tools_ai_interessentenbogen_groups() );
}

function tsvd_tools_ai_create_interessentenbogen_form( $input ) {
    $force = ! empty( $input['force'] );
    $existing = (int) get_option( 'tsvd_interessentenbogen_form', 0 );
    if ( $existing && get_post_type( $existing ) === 'tsvd_form' && ! $force ) {
        tsvd_tools_ai_write_interessentenbogen_fields( $existing );
        update_post_meta( $existing, '_tsvd_form_persist_inquiry', '1' );
        return array(
            'created'        => false,
            'repaired'       => true,
            'form_id'        => $existing,
            'form_edit_link' => (string) get_edit_post_link( $existing, 'raw' ),
            'message'        => __( 'Formular bestand bereits; Felder und Gruppierung wurden neu geschrieben.', 'tsv-tools' ),
        );
    }

    $title = isset( $input['title'] ) && $input['title'] !== '' ? sanitize_text_field( $input['title'] ) : __( 'Interessentenbogen für die Tiervermittlung', 'tsv-tools' );
    $form_id = wp_insert_post( array(
        'post_type'   => 'tsvd_form',
        'post_status' => 'publish',
        'post_title'  => $title,
    ), true );
    if ( is_wp_error( $form_id ) ) {
        return $form_id;
    }

    tsvd_tools_ai_write_interessentenbogen_fields( $form_id );

    $recipient = isset( $input['recipient_email'] ) && is_email( $input['recipient_email'] ) ? sanitize_email( $input['recipient_email'] ) : get_option( 'admin_email' );
    update_post_meta( $form_id, '_tsvd_form_recipient', $recipient );
    update_post_meta( $form_id, '_tsvd_form_subject', __( 'Neue Anfrage: Interessentenbogen Tiervermittlung', 'tsv-tools' ) );
    update_post_meta( $form_id, '_tsvd_form_success_message', __( 'Vielen Dank für Ihr Interesse! Ihre Angaben sind bei uns eingegangen und werden geprüft.', 'tsv-tools' ) );
    update_post_meta( $form_id, '_tsvd_form_show_title', '1' );
    // Feed the Anfragen-Dashboard (siehe anfragen-listener.php) — dieses Formular
    // ersetzt keinen bestehenden Kanal, sondern ist von Anfang an Teil davon.
    update_post_meta( $form_id, '_tsvd_form_persist_inquiry', '1' );

    update_option( 'tsvd_interessentenbogen_form', $form_id );

    return array(
        'created'        => true,
        'form_id'        => (int) $form_id,
        'form_edit_link' => (string) get_edit_post_link( $form_id, 'raw' ),
        'message'        => __( 'Interessentenbogen-Formular angelegt und in den Einstellungen verknüpft.', 'tsv-tools' ),
    );
}
