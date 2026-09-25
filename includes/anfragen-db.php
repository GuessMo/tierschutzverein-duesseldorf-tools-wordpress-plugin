<?php
/**
 * Anfragen (inquiry) storage schema — dbDelta-managed tables, versioned via option.
 *
 * DSGVO-Hinweise (bewusste Entscheidungen in diesem Durchgang, offene Folgepunkte):
 * - Erste PII-tragende Tabelle im Projekt (Name, E-Mail, Telefon, Freitext-Payload).
 * - Keine automatische Lösch-/Anonymisierungsfrist — Löschung erfolgt manuell pro
 *   Anfrage über das Dashboard (bestätigte Nutzer-Entscheidung, kein Versehen).
 * - Kein Audit-Log, wer eine Anfrage eingesehen hat.
 * - Kein Anschluss an WordPress' wp_privacy_personal_data_exporters/erasers.
 *
 * @package TSVD
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_ANFRAGEN_DB_VERSION = '6';

function tsvd_anfragen_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'tsvd_anfragen';
}

function tsvd_anfragen_replies_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'tsvd_anfragen_replies';
}

function tsvd_anfragen_maybe_upgrade_db() {
	if ( get_option( 'tsvd_anfragen_db_version' ) === TSVD_ANFRAGEN_DB_VERSION ) {
		return;
	}
	tsvd_anfragen_create_tables();
	tsvd_anfragen_migrate_listing_kind();
	update_option( 'tsvd_anfragen_db_version', TSVD_ANFRAGEN_DB_VERSION );
}

function tsvd_anfragen_messenger_sql( $alias = 'a' ) {
	return "{$alias}.kind NOT IN ( 'listing', 'sighting' )";
}

function tsvd_anfragen_migrate_listing_kind() {
	global $wpdb;
	if ( ! function_exists( 'tsvd_halter_case' ) ) {
		return;
	}
	$table   = tsvd_anfragen_table_name();
	$replies = tsvd_anfragen_replies_table_name();
	$rows    = $wpdb->get_results(
		"SELECT a.id, a.animal_id FROM {$table} a WHERE a.kind = 'inquiry' AND a.animal_id IS NOT NULL
		 AND NOT EXISTS ( SELECT 1 FROM {$replies} r WHERE r.anfrage_id = a.id AND r.user_id IS NOT NULL )",
		ARRAY_A
	);
	foreach ( $rows as $row ) {
		if ( '' !== tsvd_halter_case( (int) $row['animal_id'] ) ) {
			$wpdb->update( $table, array( 'kind' => 'listing' ), array( 'id' => (int) $row['id'] ) );
		}
	}
	$wpdb->query(
		"UPDATE {$table} a SET a.status = 'forwarded'
		 WHERE a.kind IN ( 'listing', 'sighting' ) AND a.status IN ( 'open', 'answered' )
		 AND EXISTS ( SELECT 1 FROM {$replies} r WHERE r.anfrage_id = a.id AND r.direction = 'out' AND r.party = 'halter' )"
	);
}
add_action( 'admin_init', 'tsvd_anfragen_maybe_upgrade_db' );
add_action( 'init', 'tsvd_anfragen_maybe_upgrade_db' );

function tsvd_anfragen_create_tables() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$charset_collate = $wpdb->get_charset_collate();

	$table = tsvd_anfragen_table_name();
	dbDelta( "CREATE TABLE {$table} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		form_id BIGINT UNSIGNED NOT NULL,
		animal_id BIGINT UNSIGNED NULL,
		applicant_name VARCHAR(255) NOT NULL DEFAULT '',
		applicant_email VARCHAR(255) NOT NULL DEFAULT '',
		applicant_phone VARCHAR(64) NOT NULL DEFAULT '',
		payload LONGTEXT NOT NULL,
		status VARCHAR(20) NOT NULL DEFAULT 'open',
		kind VARCHAR(20) NOT NULL DEFAULT 'inquiry',
		assigned_user_id BIGINT UNSIGNED NULL,
		created_at DATETIME NOT NULL,
		updated_at DATETIME NOT NULL,
		deleted_at DATETIME NULL,
		PRIMARY KEY  (id),
		KEY status_created (status,created_at)
	) {$charset_collate};" );

	$replies_table = tsvd_anfragen_replies_table_name();
	dbDelta( "CREATE TABLE {$replies_table} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		anfrage_id BIGINT UNSIGNED NOT NULL,
		user_id BIGINT UNSIGNED NULL,
		direction VARCHAR(10) NOT NULL DEFAULT 'out',
		party VARCHAR(20) NULL,
		body LONGTEXT NOT NULL,
		sent_at DATETIME NULL,
		scheduled_at DATETIME NULL,
		edited_at DATETIME NULL,
		PRIMARY KEY  (id),
		KEY anfrage_id (anfrage_id)
	) {$charset_collate};" );

	$wpdb->query( "ALTER TABLE {$replies_table} MODIFY sent_at DATETIME NULL" );

	tsvd_anfragen_migrate_status_values( $table );
}

function tsvd_anfragen_migrate_status_values( $table ) {
	global $wpdb;
	$wpdb->query( "UPDATE {$table} SET status = 'open' WHERE status IN ( 'new', 'in_progress' )" );
	$wpdb->query( "UPDATE {$table} SET status = 'answered' WHERE status = 'closed'" );
}

function tsvd_anfragen_blacklist() {
	$list = get_option( 'tsvd_anfragen_blacklist', array() );
	return is_array( $list ) ? $list : array();
}

function tsvd_anfragen_is_blacklisted( $email ) {
	$email = strtolower( trim( sanitize_email( $email ) ) );
	return $email && in_array( $email, tsvd_anfragen_blacklist(), true );
}

function tsvd_anfragen_blacklist_add( $email ) {
	$email = strtolower( trim( sanitize_email( $email ) ) );
	if ( ! $email ) {
		return;
	}
	$list   = tsvd_anfragen_blacklist();
	$list[] = $email;
	$list   = array_values( array_unique( $list ) );
	update_option( 'tsvd_anfragen_blacklist', $list );
}

function tsvd_anfragen_blacklist_remove( $email ) {
	$email = strtolower( trim( sanitize_email( $email ) ) );
	if ( ! $email ) {
		return;
	}
	$list   = tsvd_anfragen_blacklist();
	$list   = array_values( array_diff( $list, array( $email ) ) );
	update_option( 'tsvd_anfragen_blacklist', $list );
}
