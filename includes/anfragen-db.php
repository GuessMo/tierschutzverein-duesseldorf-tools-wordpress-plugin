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

const TSVD_ANFRAGEN_DB_VERSION = '2';

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
	update_option( 'tsvd_anfragen_db_version', TSVD_ANFRAGEN_DB_VERSION );
}
add_action( 'admin_init', 'tsvd_anfragen_maybe_upgrade_db' );

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
		status VARCHAR(20) NOT NULL DEFAULT 'new',
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
		body LONGTEXT NOT NULL,
		sent_at DATETIME NOT NULL,
		PRIMARY KEY  (id),
		KEY anfrage_id (anfrage_id)
	) {$charset_collate};" );
}
