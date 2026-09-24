<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

const TSVD_SSO_JWKS_URI = 'https://www.googleapis.com/oauth2/v3/certs';
const TSVD_SSO_ISSUERS  = array( 'https://accounts.google.com', 'accounts.google.com' );

function tsvd_sso_jwks() {
	$cached = get_transient( 'tsvd_sso_jwks' );
	if ( is_array( $cached ) && $cached ) {
		return $cached;
	}
	$resp = wp_remote_get( TSVD_SSO_JWKS_URI, array( 'timeout' => 15 ) );
	if ( is_wp_error( $resp ) || 200 !== wp_remote_retrieve_response_code( $resp ) ) {
		return array();
	}
	$jwks = json_decode( wp_remote_retrieve_body( $resp ), true );
	if ( ! is_array( $jwks ) || empty( $jwks['keys'] ) ) {
		return array();
	}
	set_transient( 'tsvd_sso_jwks', $jwks, HOUR_IN_SECONDS );
	return $jwks;
}

function tsvd_sso_verify_id_token( $id_token, $expected_nonce ) {
	$jwks = tsvd_sso_jwks();
	if ( ! $jwks ) {
		return null;
	}
	try {
		$claims = (array) JWT::decode( $id_token, JWK::parseKeySet( $jwks ) );
	} catch ( \Throwable $e ) {
		return null;
	}
	if ( ! tsvd_sso_claims_valid( $claims, $expected_nonce ) ) {
		return null;
	}
	return $claims;
}

function tsvd_sso_claims_valid( array $claims, $expected_nonce ) {
	if ( empty( $claims['iss'] ) || ! in_array( $claims['iss'], TSVD_SSO_ISSUERS, true ) ) {
		return false;
	}
	if ( empty( $claims['aud'] ) || ! hash_equals( (string) TSVD_SSO_GOOGLE_CLIENT_ID, (string) $claims['aud'] ) ) {
		return false;
	}
	if ( empty( $claims['nonce'] ) || ! hash_equals( (string) $expected_nonce, (string) $claims['nonce'] ) ) {
		return false;
	}
	if ( empty( $claims['hd'] ) || $claims['hd'] !== tsvd_sso_hosted_domain() ) {
		return false;
	}
	return ! empty( $claims['email_verified'] ) && ! empty( $claims['sub'] );
}
