<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/sso-verify.php';
require_once __DIR__ . '/sso-user.php';
require_once __DIR__ . '/sso-flow.php';

function tsvd_sso_is_configured() {
	return defined( 'TSVD_SSO_GOOGLE_CLIENT_ID' )
		&& defined( 'TSVD_SSO_GOOGLE_CLIENT_SECRET' )
		&& '' !== (string) TSVD_SSO_GOOGLE_CLIENT_ID
		&& '' !== (string) TSVD_SSO_GOOGLE_CLIENT_SECRET;
}

const TSVD_SSO_DEFAULT_DOMAINS = array(
	'tierschutzverein-duesseldorf.de',
	'tierheim-duesseldorf.de',
);

function tsvd_sso_hosted_domains() {
	$configured = defined( 'TSVD_SSO_GOOGLE_HD' )
		? explode( ',', (string) TSVD_SSO_GOOGLE_HD )
		: array();
	$domains    = array_map( 'strtolower', array_map( 'trim', $configured ) );
	$merged     = array_merge( TSVD_SSO_DEFAULT_DOMAINS, $domains );
	return array_values( array_unique( array_filter( $merged ) ) );
}

add_action(
	'init',
	function () {
		if ( ! get_role( 'mitarbeiter' ) ) {
			add_role( 'mitarbeiter', 'Mitarbeiter', array( 'read' => true ) );
		}
	}
);

add_action(
	'login_init',
	function () {
		if ( ! tsvd_sso_is_configured() ) {
			return;
		}
		$action = isset( $_GET['tsvd_sso'] ) ? sanitize_key( wp_unslash( $_GET['tsvd_sso'] ) ) : '';
		if ( 'start' === $action ) {
			tsvd_sso_start();
		} elseif ( 'callback' === $action ) {
			tsvd_sso_callback();
		}
	}
);

add_action(
	'login_form',
	function () {
		if ( ! tsvd_sso_is_configured() ) {
			return;
		}
		printf(
			'<p class="tsvd-sso-login"><a class="button button-large" href="%s">%s</a></p>',
			esc_url( tsvd_sso_start_url() ),
			esc_html__( 'Mit Google anmelden', 'tsvd' )
		);
	}
);

add_action(
	'login_enqueue_scripts',
	function () {
		if ( ! tsvd_sso_is_configured() ) {
			return;
		}
		wp_enqueue_style(
			'tsvd-sso-login',
			TSVD_TOOLS_URL . 'assets/sso-login.css',
			array(),
			TSVD_TOOLS_ASSET_VERSION
		);
	}
);

add_action(
	'template_redirect',
	function () {
		$uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
		$path = trim( (string) wp_parse_url( $uri, PHP_URL_PATH ), '/' );
		if ( 'login' !== $path ) {
			return;
		}
		if ( is_user_logged_in() ) {
			wp_safe_redirect( admin_url() );
			exit;
		}
		wp_safe_redirect( tsvd_sso_is_configured() ? tsvd_sso_start_url() : wp_login_url() );
		exit;
	},
	1
);
