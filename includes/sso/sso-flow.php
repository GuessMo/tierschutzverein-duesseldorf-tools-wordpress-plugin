<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_SSO_AUTH_ENDPOINT  = 'https://accounts.google.com/o/oauth2/v2/auth';
const TSVD_SSO_TOKEN_ENDPOINT = 'https://oauth2.googleapis.com/token';
const TSVD_SSO_COOKIE         = 'tsvd_sso_state';

function tsvd_sso_b64url( $bin ) {
	return rtrim( strtr( base64_encode( $bin ), '+/', '-_' ), '=' );
}

function tsvd_sso_redirect_uri() {
	$base = wp_login_url();
	return $base . ( false === strpos( $base, '?' ) ? '?' : '&' ) . 'tsvd_sso=callback';
}

function tsvd_sso_start_url() {
	$base = wp_login_url();
	return $base . ( false === strpos( $base, '?' ) ? '?' : '&' ) . 'tsvd_sso=start';
}

function tsvd_sso_start() {
	$state     = tsvd_sso_b64url( random_bytes( 24 ) );
	$nonce     = tsvd_sso_b64url( random_bytes( 24 ) );
	$verifier  = tsvd_sso_b64url( random_bytes( 48 ) );
	$challenge = tsvd_sso_b64url( hash( 'sha256', $verifier, true ) );
	$key       = tsvd_sso_b64url( random_bytes( 24 ) );

	set_transient(
		'tsvd_sso_' . $key,
		array(
			'state'    => $state,
			'nonce'    => $nonce,
			'verifier' => $verifier,
		),
		10 * MINUTE_IN_SECONDS
	);
	setcookie(
		TSVD_SSO_COOKIE,
		$key,
		array(
			'expires'  => time() + 600,
			'path'     => COOKIEPATH ? COOKIEPATH : '/',
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		)
	);

	$args = array(
		'client_id'             => TSVD_SSO_GOOGLE_CLIENT_ID,
		'redirect_uri'          => tsvd_sso_redirect_uri(),
		'response_type'         => 'code',
		'scope'                 => 'openid email profile',
		'state'                 => $state,
		'nonce'                 => $nonce,
		'code_challenge'        => $challenge,
		'code_challenge_method' => 'S256',
		'prompt'                => 'select_account',
	);
	wp_redirect( TSVD_SSO_AUTH_ENDPOINT . '?' . http_build_query( $args ) );
	exit;
}

function tsvd_sso_callback() {
	$key  = isset( $_COOKIE[ TSVD_SSO_COOKIE ] ) ? sanitize_text_field( wp_unslash( $_COOKIE[ TSVD_SSO_COOKIE ] ) ) : '';
	$data = $key ? get_transient( 'tsvd_sso_' . $key ) : false;
	if ( $key ) {
		delete_transient( 'tsvd_sso_' . $key );
	}
	setcookie( TSVD_SSO_COOKIE, '', time() - 3600, COOKIEPATH ? COOKIEPATH : '/' );

	$state = isset( $_GET['state'] ) ? sanitize_text_field( wp_unslash( $_GET['state'] ) ) : '';
	$code  = isset( $_GET['code'] ) ? sanitize_text_field( wp_unslash( $_GET['code'] ) ) : '';
	if ( ! is_array( $data ) || '' === $code || ! hash_equals( (string) $data['state'], $state ) ) {
		tsvd_sso_fail( 'state' );
	}

	$token = tsvd_sso_exchange_code( $code, $data['verifier'] );
	if ( empty( $token['id_token'] ) ) {
		tsvd_sso_fail( 'token' );
	}

	$claims = tsvd_sso_verify_id_token( $token['id_token'], $data['nonce'] );
	if ( ! $claims ) {
		tsvd_sso_fail( 'verify' );
	}

	$user_id = tsvd_sso_provision_user( $claims );
	if ( ! $user_id ) {
		tsvd_sso_fail( 'provision' );
	}

	wp_set_auth_cookie( $user_id, true );
	wp_safe_redirect( admin_url() );
	exit;
}

function tsvd_sso_exchange_code( $code, $verifier ) {
	$resp = wp_remote_post(
		TSVD_SSO_TOKEN_ENDPOINT,
		array(
			'timeout' => 15,
			'body'    => array(
				'code'          => $code,
				'client_id'     => TSVD_SSO_GOOGLE_CLIENT_ID,
				'client_secret' => TSVD_SSO_GOOGLE_CLIENT_SECRET,
				'redirect_uri'  => tsvd_sso_redirect_uri(),
				'grant_type'    => 'authorization_code',
				'code_verifier' => $verifier,
			),
		)
	);
	if ( is_wp_error( $resp ) || 200 !== wp_remote_retrieve_response_code( $resp ) ) {
		return array();
	}
	$body = json_decode( wp_remote_retrieve_body( $resp ), true );
	return is_array( $body ) ? $body : array();
}

function tsvd_sso_fail( $reason ) {
	wp_die(
		esc_html__( 'Anmeldung mit Google fehlgeschlagen.', 'tsvd' ) . ' (' . esc_html( $reason ) . ')',
		esc_html__( 'Anmeldung fehlgeschlagen', 'tsvd' ),
		array( 'response' => 403 )
	);
}
