<?php
/**
 * Seed-Skript für das Anfragen-Dashboard (nur Dev/Staging).
 *
 * Idempotent: leert beim Lauf beide Anfragen-Tabellen und baut exakt
 * 14 Anfragen (open 6, answered 4, spam 2, blocked 1, trash 1) mit
 * 46 Replies auf. Nur in der development-Umgebung ausführbar — sonst
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
);

// [status, tier_slug, name, email, phone, assigned_login, days_ago, extra_payload, replies]
$conversations = array(
	array( 'open', 'balu-katze-20-07-2026', 'Anna Schmidt', 'anna.schmidt@example.de', '0171 234 56 78', 'f.steller', 2, array( 's1_wohnform' => 'Wohnung', 's1_wohnflaeche_etage' => '72 qm, 3. Etage', 's3_tierart' => 'Katze', 's6_motivation' => 'Balu würde perfekt zu uns passen, wir haben schon einen gesicherten Balkon.' ), array(
		array( 'in', '', 'Guten Tag, wir interessieren uns sehr für Balu!', 2, '09:15' ),
		array( 'out', 'f.steller', 'Hallo Frau Schmidt, vielen Dank für Ihre Nachricht. Ich melde mich mit ein paar Rückfragen.', 2, '11:40' ),
		array( 'in', '', 'Sehr gerne, ich beantworte alles. Unsere Wohnung hat 72 qm.', 2, '14:05' ),
	) ),
	array( 'open', 'luna-katze-08-11-2023', 'Mehmet Yilmaz', 'mehmet.yilmaz@example.de', '0152 987 65 43', '', 1, array( 's1_wohnform' => 'Haus', 's1_garten' => 'mit Garten', 's1_garten_eingezaunt' => 'Garten ist sicher eingezäunt', 's6_motivation' => 'Wir suchen eine Katze für unseren eingezäunten Garten.' ), array(
		array( 'in', '', 'Hallo, ich habe Luna auf Ihrer Seite gesehen und wäre interessiert.', 1, '10:00' ),
		array( 'out', 'f.steller', 'Guten Tag Herr Yilmaz, danke für Ihr Interesse. Kurze Frage: Ist Luna als Freigängerin vorgesehen?', 1, '12:30' ),
		array( 'in', '', 'Ja, wir haben einen sicheren Garten und ruhige Lage.', 1, '16:45' ),
	) ),
	array( 'open', 'fufu-kleintier-20-07-2026', 'Sabine Meier', 'sabine.meier@example.de', '0160 111 22 33', 'corinna.hoeppner', 0, array( 's3_tierart' => 'Kleintier', 's1_anzahl_personen' => '2', 's6_anmerkungen' => 'Wir hatten schon früher Kaninchen.' ), array(
		array( 'in', '', 'Für Fufu würde ich mich gerne bewerben.', 0, '08:30' ),
		array( 'out', 'corinna.hoeppner', 'Hallo Frau Meier, schön! Ich sende Ihnen den Interessentenbogen und melde mich.', 0, '09:45' ),
		array( 'in', '', 'Vielen Dank, ich habe den Bogen schon ausgefüllt.', 0, '11:20' ),
	) ),
	array( 'open', 'athena-kleintier-20-07-2026', 'Claudia Roth', 'claudia.roth@example.de', '0178 919 88 77', '', 1, array( 's3_tierart' => 'Vogel', 's1_wohnform' => 'Wohnung', 's1_kinder' => 'Keine Kinder', 's6_motivation' => 'Athena würde in unsere ruhige, vogelsichere Wohnung passen.' ), array(
		array( 'in', '', 'Guten Tag, ich interessiere mich sehr für Athena.', 1, '09:40' ),
		array( 'out', 'katrin.haas', 'Hallo Frau Roth, danke für Ihr Interesse. Athena ist eine Papageien-Dame — haben Sie Erfahrung mit Vögeln?', 0, '11:00' ),
	) ),
	array( 'open', 'mimi-katze-20-07-2026', 'Jonas Weber', 'jonas.weber@example.de', '0177 555 44 33', '', 3, array( 's3_tierart' => 'Katze', 's1_kinder' => 'Keine Kinder', 's6_motivation' => 'Mimi sieht lieb aus, wir möchten sie kennenlernen.' ), array(
		array( 'in', '', 'Hallo, ist Mimi noch verfügbar?', 3, '13:10' ),
		array( 'out', 'corinna.hoeppner', 'Ja, Mimi ist noch da. Gerne vereinbaren wir einen Kennenlerntermin.', 3, '15:00' ),
		array( 'in', '', 'Super, nächste Woche Mittwoch würde passen.', 3, '17:25' ),
		array( 'out', 'corinna.hoeppner', 'Ich trage den Termin ein und melde mich mit der Bestätigung.', 0, '10:00', 6 ),
	) ),
	array( 'open', 'atze-hund-20-07-2026', 'Petra Klein', 'petra.klein@example.de', '0151 222 33 44', '', 5, array( 's3_tierart' => 'Hund', 's1_garten' => 'mit Garten', 's1_garten_groesse_zaun' => '500 qm, 1,80 m Zaun', 's6_motivation' => 'Atze wäre der ideale Familienhund für uns.' ), array(
		array( 'in', '', 'Guten Tag, wir interessieren uns für Atze.', 5, '09:00' ),
		array( 'out', 'f.steller', 'Guten Tag, vielen Dank für Ihr Interesse. Ich leite Ihre Anfrage an die Vermittlung weiter.', 5, '10:15' ),
	) ),
	array( 'answered', 'rocky-hund-20-07-2026', 'Michael Braun', 'michael.braun@example.de', '0176 333 44 55', 'f.steller', 8, array( 's3_tierart' => 'Hund', 's1_wohnform' => 'Wohnung', 's1_hundeschule' => 'Ja', 's6_motivation' => 'Ich möchte Rocky ein Zuhause geben.' ), array(
		array( 'in', '', 'Ich würde Rocky gerne adoptieren. Ich wohne in einer 80 qm Wohnung.', 8, '10:00' ),
		array( 'out', 'f.steller', 'Guten Tag Herr Braun, herzlichen Dank für Ihre Bewerbung. Wir prüfen die Unterlagen.', 8, '11:30' ),
		array( 'in', '', 'Ich habe den Bogen ausgefüllt und sende ihn angehängt.', 8, '13:00' ),
		array( 'out', 'f.steller', 'Die Unterlagen sind vollständig. Wir melden uns nach der Prüfung.', 7, '09:00' ),
		array( 'in', '', 'Gibt es Neuigkeiten?', 4, '16:20' ),
		array( 'out', 'f.steller', 'Gute Nachricht: Die Vorkontrolle ist positiv. Wir vereinbaren einen Termin.', 3, '09:45' ),
		array( 'in', '', 'Das freut mich sehr! Wann wäre es möglich?', 3, '11:10' ),
	) ),
	array( 'answered', 'sammy-katze-mischling-20-07-2026', 'Laura Hoffmann', 'laura.hoffmann@example.de', '0170 444 55 66', 'corinna.hoeppner', 6, array( 's3_tierart' => 'Katze', 's1_freigang' => 'Freigang (bei Katzen) möglich', 's6_motivation' => 'Sammy gefällt uns unglaublich gut.' ), array(
		array( 'in', '', 'Hallo, ich habe eine Frage zu Sammy.', 6, '14:00' ),
		array( 'out', 'corinna.hoeppner', 'Gern, bitte stellen Sie Ihre Frage.', 6, '15:30' ),
		array( 'in', '', 'Verträgt sich Sammy mit anderen Katzen?', 6, '16:00' ),
		array( 'out', 'corinna.hoeppner', 'Ja, Sammy ist sehr sozial und verträgt sich gut mit Artgenossen.', 5, '09:00' ),
		array( 'in', '', 'Dann bewerbe ich mich!', 5, '10:30' ),
		array( 'out', 'corinna.hoeppner', 'Wir freuen uns! Unterlagen sind eingegangen, wir melden uns.', 2, '13:00' ),
		array( 'in', '', 'Vielen Dank, ich warte auf Ihre Rückmeldung.', 2, '15:45' ),
	) ),
	array( 'answered', 'molly-katze-20-07-2026', 'Karin Vogel', 'karin.vogel@example.de', '0157 666 77 88', 'f.steller', 10, array( 's3_tierart' => 'Katze', 's1_wohnlage' => 'Ländlich', 's6_motivation' => 'Molly ist genau die Katze, die wir suchen.' ), array(
		array( 'in', '', 'Guten Tag, wir möchten Molly adoptieren.', 10, '09:30' ),
		array( 'out', 'f.steller', 'Vielen Dank für Ihre Anfrage, Frau Vogel. Der Bogen liegt bei.', 10, '11:00' ),
		array( 'in', '', 'Erledigt, ich habe alles ausgefüllt.', 9, '14:00' ),
		array( 'out', 'f.steller', 'Vielen Dank, wir prüfen Ihre Unterlagen.', 9, '15:20' ),
		array( 'in', '', 'Konnten Sie schon schauen?', 6, '10:00' ),
		array( 'out', 'f.steller', 'Ja, alles sieht gut aus. Wir bereiten den Vertrag vor.', 5, '11:40' ),
		array( 'in', '', 'Perfekt, vielen Dank für die tolle Betreuung!', 5, '12:10' ),
	) ),
	array( 'answered', 'mailo-hund-20-07-2026', 'Thomas Fuchs', 'thomas.fuchs@example.de', '0172 888 99 00', '', 7, array( 's3_tierart' => 'Hund', 's1_berufstaetigkeit' => 'Vollzeit mit Homeoffice', 's6_motivation' => 'Mailo soll ein neues Zuhause bei mir finden.' ), array(
		array( 'in', '', 'Hallo, ich interessiere mich für Mailo.', 7, '10:15' ),
		array( 'out', 'f.steller', 'Guten Tag Herr Fuchs, gerne. Haben Sie bereits Erfahrung mit Hunden?', 7, '12:00' ),
		array( 'in', '', 'Ja, ich hatte 8 Jahre einen Mischling.', 7, '13:30' ),
	) ),
	array( 'spam', 'ginny-hund-20-07-2026', 'Werbe Agentur', 'info@werbung-xyz.example', '0211 000 00 00', '', 4, array( 's6_anmerkungen' => 'Wir bieten SEO-Dienstleistungen für Ihre Website an.' ), array(
		array( 'in', '', 'Hallo, wir helfen Ihnen, mehr Reichweite zu bekommen. Schreiben Sie uns!', 4, '09:00' ),
	) ),
	array( 'spam', 'simba-katze-main-coon-06-07-2026', 'Karina Wolf', 'karina.wolf@example.de', '0157 222 33 44', '', 2, array( 's6_motivation' => 'Simba ist die schönste Katze überhaupt!' ), array(
		array( 'in', '', 'Simba ist hübsch. Habe ich leider keine Fragen.', 2, '12:00' ),
	) ),
	array( 'blocked', 'eddie-hund-20-07-2026', 'Blockierter Absender', 'blocked@example.de', '', '', 2, array(), array(
		array( 'in', '', 'Testnachricht eines blockierten Absenders.', 2, '07:00' ),
	) ),
	array( 'trash', 'mailo-hund-20-07-2026', 'Duplikat Anfrage', 'thomas.fuchs@example.de', '0172 888 99 00', '', 3, array( 's6_anmerkungen' => 'Doppelte Anfrage, bitte löschen.' ), array(
		array( 'in', '', 'Nochmal eine Anfrage von mir, bitte entschuldigen Sie die Dopplung.', 3, '18:00' ),
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
		$sent_at = gmdate( 'Y-m-d H:i:s', time() - $r_days_ago * DAY_IN_SECONDS );

		$wpdb->insert(
			$replies_table,
			array(
				'anfrage_id'    => $anfrage_id,
				'user_id'       => $user_login ? tsvd_seed_user_id( $user_login ) : null,
				'direction'     => $direction,
				'body'          => $body,
				'sent_at'       => $scheduled_hours ? null : $sent_at,
				'scheduled_at'  => $scheduled_hours ? gmdate( 'Y-m-d H:i:s', time() + $scheduled_hours * HOUR_IN_SECONDS ) : null,
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