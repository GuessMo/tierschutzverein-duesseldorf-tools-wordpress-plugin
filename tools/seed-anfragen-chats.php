<?php
/**
 * Seed-Skript für das Anfragen-Dashboard (nur Dev/Staging).
 *
 * Idempotent: leert beim Lauf beide Anfragen-Tabellen und baut exakt
 * 19 Anfragen (open 11, answered 4, spam 2, blocked 1, trash 1) mit
 * Replies auf. Nur in der development-Umgebung ausführbar — sonst
 * bricht das Skript ab, es sei denn TSVD_SEED=1 ist gesetzt.
 *
 * Aufruf: wp eval-file wp-content/plugins/tsv-tools/tools/seed-anfragen-chats.php
 *
 * @package TSVD_Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! in_array( wp_get_environment_type(), array( 'development', 'local' ), true ) && ! getenv( 'TSVD_SEED' ) ) {
	wp_die( 'Seed-Skript nur in development/local oder mit TSVD_SEED=1.' );
}

require_once __DIR__ . '/../includes/anfragen-db.php';

global $wpdb;

$anfragen_table  = tsvd_anfragen_table_name();
$replies_table   = tsvd_anfragen_replies_table_name();
$loan_form_id    = 5586; // Interessentenbogen für die Tiervermittlung (Publish).

// Bestand komplett zurücksetzen — idempotenter Lauf.
$wpdb->query( "DELETE FROM {$replies_table}" );
$wpdb->query( "DELETE FROM {$anfragen_table}" );

function tsvd_seed_animal_id( $slug ) {
	$posts = get_posts(
		array(
			'post_type'   => 'animals',
			'name'        => $slug,
			'numberposts' => 1,
			'post_status' => 'any',
		)
	);
	return $posts ? (int) $posts[0]->ID : 0;
}

function tsvd_seed_user_id( $login ) {
	$user = get_user_by( 'login', $login );
	return $user ? (int) $user->ID : 0;
}

function tsvd_seed_payload( $name, $email, $phone, $tier_name, $extra = array() ) {
	return wp_json_encode(
		array_merge(
			array(
				'bewerber_name'      => $name,
				'bewerber_email'     => $email,
				'bewerber_telefon'   => $phone,
				'interesse_tier'     => $tier_name,
				'seed'               => true,
				'zustimmung_datenschutz' => 'Ja',
			),
			$extra
		)
	);
}

$tiers = array(
	'balu-katze-20-07-2026'        => 'Balu',
	'luna-katze-08-11-2023'        => 'Luna',
	'fufu-kleintier-20-07-2026'    => 'Fufu',
	'mimi-katze-20-07-2026'        => 'Mimi',
	'atze-hund-20-07-2026'         => 'Atze',
	'rocky-hund-20-07-2026'        => 'Rocky',
	'sammy-katze-mischling-20-07-2026' => 'Sammy',
	'molly-katze-20-07-2026'       => 'Molly',
	'mailo-hund-20-07-2026'        => 'Mailo',
	'eddie-hund-20-07-2026'        => 'Eddie',
	'capone-hund-20-07-2026'       => 'Capone',
	'ginny-hund-20-07-2026'        => 'Ginny',
	'simba-katze-main-coon-06-07-2026' => 'Simba',
	'athena-kleintier-20-07-2026'      => 'Athena',
	'pallas-kleintier-20-07-2026'      => 'Pallas',
	'kalli-vogel-wellensittich-06-07-2026' => 'Kalli',
	'hertha-vogel-kanarienvogel-06-07-2026' => 'Hertha',
	'frodo-vogel-gelbwangenamazone-06-07-2026' => 'Frodo',
	'caruso-vogel-kanarienvogel-06-07-2026' => 'Caruso',
	'archibald-vogel-graupapagei-08-05-2026' => 'Archibald',
);

// [status, tier_slug, name, email, phone, assigned_login, days_ago, extra_payload, replies]
$conversations = array(
	array( 'open', 'balu-katze-20-07-2026', 'Anna Schmidt', 'anna.schmidt@example.de', '0171 234 56 78', 'f.steller', 2, array( 's1_wohnform' => 'wohnung', 's1_wohnflaeche_etage' => '72 qm, 3. Etage', 's3_tierart' => 'katze', 's6_motivation' => 'Balu würde perfekt zu uns passen, wir haben schon einen gesicherten Balkon.' ), array(
		array( 'out', 'f.steller', 'Hallo Frau Schmidt, vielen Dank für Ihre Nachricht. Ich melde mich mit ein paar Rückfragen.', 2, '11:40' ),
		array( 'in', '', 'Sehr gerne, ich beantworte alles. Unsere Wohnung hat 72 qm.', 2, '14:05' ),
	) ),
	array( 'open', 'luna-katze-08-11-2023', 'Mehmet Yilmaz', 'mehmet.yilmaz@example.de', '0152 987 65 43', '', 1, array( 's1_wohnform' => 'haus', 's1_garten' => 'ja', 's1_garten_eingezaunt' => 'ja', 's3_tierart' => 'katze', 's6_motivation' => 'Wir suchen eine Katze für unseren eingezäunten Garten.' ), array(
		array( 'out', 'f.steller', 'Guten Tag Herr Yilmaz, danke für Ihr Interesse. Kurze Frage: Ist Luna als Freigängerin vorgesehen?', 1, '12:30' ),
		array( 'in', '', 'Ja, wir haben einen sicheren Garten und ruhige Lage.', 1, '16:45' ),
	) ),
	array( 'open', 'fufu-kleintier-20-07-2026', 'Sabine Meier', 'sabine.meier@example.de', '0160 111 22 33', 'corinna.hoeppner', 0, array( 's3_tierart' => 'kleintier', 's1_anzahl_personen' => '2', 's6_anmerkungen' => 'Wir hatten schon früher Kaninchen.' ), array(
		array( 'out', 'corinna.hoeppner', 'Hallo Frau Meier, vielen Dank für Ihren Interessentenbogen. Wir melden uns, sobald wir alles geprüft haben.', 0, '09:45' ),
		array( 'in', '', 'Vielen Dank, ich freue mich auf Ihre Rückmeldung.', 0, '11:20' ),
	) ),
	array( 'open', 'athena-kleintier-20-07-2026', 'Silke Warnke', 'silke.warnke@example.de', '0163 55 11 22', '', 1, array( 's3_tierart' => 'kleintier', 's1_anzahl_personen' => '2', 's2_erfahrung_haustier' => 'erfahren', 's6_motivation' => 'Wir haben schon Degus gehalten und kennen die Haltung gut.' ), array(
		array( 'out', 'katrin.haas', 'Hallo Frau Warnke, danke für Ihre Nachricht. Wie viele Tiere haben Sie aktuell?', 1, '12:00' ),
		array( 'in', '', 'Aktuell einen Degu, gut vergesellschaftet.', 1, '14:30' ),
	) ),
	array( 'open', 'mimi-katze-20-07-2026', 'Jonas Weber', 'jonas.weber@example.de', '0177 555 44 33', '', 3, array( 's3_tierart' => 'katze', 's1_kinder' => 'nein', 's6_motivation' => 'Mimi sieht lieb aus, wir möchten sie kennenlernen.' ), array(
		array( 'out', 'corinna.hoeppner', 'Ja, Mimi ist noch da. Gerne vereinbaren wir einen Kennenlerntermin.', 3, '15:00' ),
		array( 'in', '', 'Super, nächste Woche Mittwoch würde passen.', 3, '17:25' ),
		array( 'out', 'corinna.hoeppner', 'Ich trage den Termin ein und melde mich mit der Bestätigung.', 0, '10:00', 6 ),
	) ),
	array( 'open', 'atze-hund-20-07-2026', 'Petra Klein', 'petra.klein@example.de', '0151 222 33 44', '', 5, array( 's3_tierart' => 'hund', 's1_garten' => 'ja', 's1_garten_groesse_zaun' => '500 qm, 1,80 m Zaun', 's6_motivation' => 'Atze wäre der ideale Familienhund für uns.' ), array(
		array( 'out', 'f.steller', 'Guten Tag, vielen Dank für Ihr Interesse. Ich leite Ihre Anfrage an die Vermittlung weiter.', 5, '10:15' ),
	) ),
	array( 'open', 'kalli-vogel-wellensittich-06-07-2026', 'Nadine Berger', 'nadine.berger@example.de', '0159 77 88 99', 'katrin.haas', 2, array( 's3_tierart' => 'andere', 's3_andere_tierart_text' => 'Wellensittich', 's2_erfahrung_haustier' => 'erfahren', 's2_weitere_tiere' => 'ja', 's6_motivation' => 'Kalli soll zu unserem Wellensittich-Pärchen ziehen.' ), array(
		array( 'out', 'katrin.haas', 'Guten Tag Frau Berger, ja, Kalli wartet noch auf ein Zuhause. Wie groß ist Ihre Voliere?', 2, '11:15' ),
		array( 'in', '', 'Wir haben eine Zimmervoliere mit 2,5 m Breite.', 2, '13:40' ),
	) ),
	array( 'open', 'hertha-vogel-kanarienvogel-06-07-2026', 'Gisela Brandt', 'gisela.brandt@example.de', '0157 33 44 55', 'katrin.haas', 3, array( 's3_tierart' => 'andere', 's3_andere_tierart_text' => 'Kanarienvogel', 's1_wohnform' => 'wohnung', 's5_urlaub' => 'familie', 's6_motivation' => 'Ich bin Rentnerin und habe viel Zeit für Hertha.' ), array(
		array( 'out', 'katrin.haas', 'Guten Tag Frau Brandt, das freut uns. Darf ich fragen, ob Sie schon einmal Kanarienvögel gehalten haben?', 3, '11:20' ),
		array( 'in', '', 'Ja, viele Jahre. Leider ist mein letzter Vogel vor einem Jahr verstorben.', 3, '15:05' ),
	) ),
	array( 'open', 'frodo-vogel-gelbwangenamazone-06-07-2026', 'Daniel Weber', 'daniel.weber@example.de', '0173 66 77 88', '', 4, array( 's3_tierart' => 'andere', 's3_andere_tierart_text' => 'Gelbwangenamazone', 's1_wohnlage' => 'laendlich', 's2_erfahrung_haustier' => 'erfahren', 's6_motivation' => 'Frodo passt zu unserer ruhigen ländlichen Lage mit großem Vogelzimmer.' ), array(
		array( 'out', 'f.steller', 'Guten Tag Herr Weber, danke für Ihr Interesse an Frodo. Wie viel Erfahrung haben Sie mit Amazonen?', 4, '10:45' ),
		array( 'in', '', 'Seit zehn Jahren halten wir einen Senegalpapagei. Amazonen sind neu für uns.', 4, '12:20' ),
	) ),
	array( 'open', 'caruso-vogel-kanarienvogel-06-07-2026', 'Ruth Hübner', 'ruth.huebner@example.de', '0160 99 00 11', 'katrin.haas', 1, array( 's3_tierart' => 'andere', 's3_andere_tierart_text' => 'Kanarienvogel', 's1_aufzug' => 'ja', 's5_erkrankung' => 'geregelt', 's6_anmerkungen' => 'Ich wohne im Erdgeschoss mit Terrasse.' ), array(
		array( 'out', 'katrin.haas', 'Hallo Frau Hübner, schön, dass Sie sich melden. Haben Sie schon einmal Kanarienvögel gepflegt?', 1, '10:00' ),
		array( 'in', '', 'Ja, früher hatte ich mehrere. Ich freue mich auf ein neues Tier.', 1, '12:30' ),
	) ),
	array( 'open', 'archibald-vogel-graupapagei-08-05-2026', 'Claudia Roth', 'claudia.roth@example.de', '0178 919 88 77', '', 1, array( 's3_tierart' => 'andere', 's3_andere_tierart_text' => 'Graupapagei', 's1_wohnform' => 'wohnung', 's1_kinder' => 'nein', 's2_erfahrung_haustier' => 'erfahren', 's6_motivation' => 'Archibald würde in unsere ruhige, vogelsichere Wohnung passen.' ), array(
		array( 'out', 'katrin.haas', 'Hallo Frau Roth, danke für Ihr Interesse. Archibald ist ein Graupapagei — haben Sie Erfahrung mit Papageien?', 1, '11:00' ),
		array( 'in', '', 'Ja, ich habe 15 Jahre einen Graupapagei betreut.', 0, '09:15' ),
	) ),
	array( 'answered', 'rocky-hund-20-07-2026', 'Michael Braun', 'michael.braun@example.de', '0176 333 44 55', 'f.steller', 8, array( 's3_tierart' => 'hund', 's1_wohnform' => 'wohnung', 's2_hundeschule' => 'ja', 's6_motivation' => 'Ich möchte Rocky ein Zuhause geben.' ), array(
		array( 'out', 'f.steller', 'Guten Tag Herr Braun, herzlichen Dank für Ihre Bewerbung. Wir prüfen die Unterlagen.', 8, '11:30' ),
		array( 'in', '', 'Ich habe alle Unterlagen ergänzt, die Anlagen sind dabei.', 8, '13:00' ),
		array( 'out', 'f.steller', 'Die Unterlagen sind vollständig. Wir melden uns nach der Prüfung.', 7, '09:00' ),
		array( 'in', '', 'Gibt es Neuigkeiten?', 4, '16:20' ),
		array( 'out', 'f.steller', 'Gute Nachricht: Die Vorkontrolle ist positiv. Wir vereinbaren einen Termin.', 3, '09:45' ),
		array( 'in', '', 'Das freut mich sehr! Wann wäre es möglich?', 3, '11:10' ),
	) ),
	array( 'answered', 'sammy-katze-mischling-20-07-2026', 'Laura Hoffmann', 'laura.hoffmann@example.de', '0170 444 55 66', 'corinna.hoeppner', 6, array( 's3_tierart' => 'katze', 's1_freigang' => 'ja', 's6_motivation' => 'Sammy gefällt uns unglaublich gut.' ), array(
		array( 'out', 'corinna.hoeppner', 'Gern, bitte stellen Sie Ihre Frage.', 6, '15:30' ),
		array( 'in', '', 'Verträgt sich Sammy mit anderen Katzen?', 6, '16:00' ),
		array( 'out', 'corinna.hoeppner', 'Ja, Sammy ist sehr sozial und verträgt sich gut mit Artgenossen.', 5, '09:00' ),
		array( 'in', '', 'Dann bewerbe ich mich!', 5, '10:30' ),
		array( 'out', 'corinna.hoeppner', 'Wir freuen uns! Unterlagen sind eingegangen, wir melden uns.', 2, '13:00' ),
		array( 'in', '', 'Vielen Dank, ich warte auf Ihre Rückmeldung.', 2, '15:45' ),
	) ),
	array( 'answered', 'molly-katze-20-07-2026', 'Karin Vogel', 'karin.vogel@example.de', '0157 666 77 88', 'f.steller', 10, array( 's3_tierart' => 'katze', 's1_wohnlage' => 'laendlich', 's6_motivation' => 'Molly ist genau die Katze, die wir suchen.' ), array(
		array( 'out', 'f.steller', 'Vielen Dank für Ihren Interessentenbogen, Frau Vogel. Wir prüfen alles und melden uns.', 10, '11:00' ),
		array( 'in', '', 'Vielen Dank, ich warte auf Ihre Rückmeldung.', 9, '14:00' ),
		array( 'out', 'f.steller', 'Vielen Dank, wir prüfen Ihre Unterlagen.', 9, '15:20' ),
		array( 'in', '', 'Konnten Sie schon schauen?', 6, '10:00' ),
		array( 'out', 'f.steller', 'Ja, alles sieht gut aus. Wir bereiten den Vertrag vor.', 5, '11:40' ),
		array( 'in', '', 'Perfekt, vielen Dank für die tolle Betreuung!', 5, '12:10' ),
	) ),
	array( 'answered', 'mailo-hund-20-07-2026', 'Thomas Fuchs', 'thomas.fuchs@example.de', '0172 888 99 00', '', 7, array( 's3_tierart' => 'hund', 's1_berufstaetigkeit' => 'homeoffice', 's6_motivation' => 'Mailo soll ein neues Zuhause bei mir finden.' ), array(
		array( 'out', 'f.steller', 'Guten Tag Herr Fuchs, gerne. Haben Sie bereits Erfahrung mit Hunden?', 7, '12:00' ),
		array( 'in', '', 'Ja, ich hatte 8 Jahre einen Mischling.', 7, '13:30' ),
	) ),
	array( 'spam', 'ginny-hund-20-07-2026', 'Werbe Agentur', 'info@werbung-xyz.example', '0211 000 00 00', '', 4, array( 's6_anmerkungen' => 'Wir bieten SEO-Dienstleistungen für Ihre Website an.' ), array() ),
	array( 'spam', 'simba-katze-main-coon-06-07-2026', 'Karina Wolf', 'karina.wolf@example.de', '0157 222 33 44', '', 2, array( 's6_motivation' => 'Simba ist die schönste Katze überhaupt!' ), array() ),
	array( 'blocked', 'eddie-hund-20-07-2026', 'Blockierter Absender', 'blocked@example.de', '', '', 2, array(), array(
		array( 'in', '', 'Testnachricht eines blockierten Absenders.', 2, '07:00' ),
	) ),
	array( 'trash', 'mailo-hund-20-07-2026', 'Duplikat Anfrage', 'thomas.fuchs@example.de', '0172 888 99 00', '', 3, array( 's6_anmerkungen' => 'Doppelte Anfrage, bitte löschen.' ), array(
		array( 'out', 'f.steller', 'Danke, wir behalten die erste Anfrage.', 3, '19:00' ),
	) ),
);

$created = 0;
$replied = 0;

foreach ( $conversations as $conv ) {
	list( $status, $tier_slug, $name, $email, $phone, $assigned_login, $days_ago, $extra, $replies ) = $conv;

	$animal_id = tsvd_seed_animal_id( $tier_slug );
	$assignee  = $assigned_login ? tsvd_seed_user_id( $assigned_login ) : 0;
	$tier_name = isset( $tiers[ $tier_slug ] ) ? $tiers[ $tier_slug ] : $tier_slug;

	$created_at = gmdate( 'Y-m-d H:i:s', time() - $days_ago * DAY_IN_SECONDS );
	$is_trash   = 'trash' === $status;

	$wpdb->insert(
		$anfragen_table,
		array(
			'form_id'          => $loan_form_id,
			'animal_id'        => $animal_id ?: null,
			'applicant_name'   => $name,
			'applicant_email'  => $email,
			'applicant_phone'  => $phone,
			'payload'          => tsvd_seed_payload( $name, $email, $phone, $tier_name, $extra ),
			'status'           => $status,
			'assigned_user_id' => $assignee ?: null,
			'created_at'       => $created_at,
			'updated_at'       => $created_at,
			'deleted_at'       => $is_trash ? $created_at : null,
		),
		array( '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s' )
	);
	$anfrage_id = (int) $wpdb->insert_id;
	$created++;

	foreach ( $replies as $reply ) {
		list( $direction, $user_login, $body, $r_days_ago, $time ) = $reply;
		$scheduled_hours = isset( $reply[5] ) ? (int) $reply[5] : 0;

		$sent_at      = null;
		$scheduled_at = null;
		if ( $scheduled_hours ) {
			$scheduled_at = get_gmt_from_date( wp_date( 'Y-m-d H:i:s', time() + $scheduled_hours * HOUR_IN_SECONDS ) );
		} else {
			$local_stamp = wp_date( 'Y-m-d', time() - $r_days_ago * DAY_IN_SECONDS )
				. ' ' . ( $time ?: '09:00' ) . ':00';
			$sent_at = get_gmt_from_date( $local_stamp );
		}

		$wpdb->insert(
			$replies_table,
			array(
				'anfrage_id'    => $anfrage_id,
				'user_id'       => $user_login ? tsvd_seed_user_id( $user_login ) : null,
				'direction'     => $direction,
				'body'          => $body,
				'sent_at'       => $sent_at,
				'scheduled_at'  => $scheduled_at,
				'edited_at'     => null,
			),
			array( '%d', '%d', '%s', '%s', '%s', '%s', '%s' )
		);
		$replied++;
	}
}

$wpdb->query( "UPDATE {$anfragen_table} SET deleted_at = NULL WHERE status != 'trash'" );

echo "Seed fertig: {$created} Anfragen, {$replied} Replies.\n";
echo 'Status: ';
foreach ( $wpdb->get_results( "SELECT status, COUNT(*) c FROM {$anfragen_table} GROUP BY status" ) as $r ) {
	echo "{$r->status}={$r->c} ";
}
echo "\n";