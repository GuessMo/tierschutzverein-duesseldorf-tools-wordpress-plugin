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
