<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_SRC_TOOL_SLUG = 'quellen';

function tsvd_src_error( $code, $message, $status ) {
	return new WP_Error( 'tsvd_src_' . $code, $message, array( 'status' => $status ) );
}

function tsvd_src_not_found() {
	return tsvd_src_error( 'not_found', __( 'Der Eintrag wurde nicht gefunden.', 'tsvd' ), 404 );
}

function tsvd_src_db_error() {
	$message = __( 'Speichern in der Datenbank ist fehlgeschlagen.', 'tsvd' );
	return tsvd_src_error( 'db_error', $message, 500 );
}

function tsvd_src_invalid( $message ) {
	return tsvd_src_error( 'invalid', $message, 400 );
}

function tsvd_src_can_use() {
	if ( current_user_can( tsvd_tool_portal_cap( TSVD_SRC_TOOL_SLUG ) ) ) {
		return true;
	}
	return tsvd_src_error(
		'forbidden',
		__( 'Du hast keinen Zugriff auf das Quellen-Werkzeug.', 'tsvd' ),
		rest_authorization_required_code()
	);
}

function tsvd_src_now() {
	return current_time( 'mysql', true );
}

function tsvd_src_require_name( $name, $label ) {
	if ( '' !== trim( (string) $name ) ) {
		return null;
	}
	return tsvd_src_invalid( sprintf( __( '%s darf nicht leer sein.', 'tsvd' ), $label ) );
}
