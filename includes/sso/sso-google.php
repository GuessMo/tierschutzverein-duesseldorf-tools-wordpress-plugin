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

function tsvd_sso_hosted_domain() {
	return defined( 'TSVD_SSO_GOOGLE_HD' ) && TSVD_SSO_GOOGLE_HD
		? (string) TSVD_SSO_GOOGLE_HD
		: 'tierschutzverein-duesseldorf.de';
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
			'<p style="margin-bottom:16px"><a class="button button-large" style="width:100%%;text-align:center" href="%s">%s</a></p>',
			esc_url( tsvd_sso_start_url() ),
			esc_html__( 'Mit Google anmelden', 'tsvd' )
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
	}
);
