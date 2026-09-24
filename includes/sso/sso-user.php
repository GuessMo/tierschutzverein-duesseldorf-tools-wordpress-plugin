<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_sso_provision_user( array $claims ) {
	$sub   = (string) $claims['sub'];
	$email = isset( $claims['email'] ) ? sanitize_email( (string) $claims['email'] ) : '';
	$name  = isset( $claims['name'] ) ? sanitize_text_field( (string) $claims['name'] ) : $email;

	$user = tsvd_sso_find_user_by_sub( $sub );
	if ( ! $user && $email ) {
		$found = get_user_by( 'email', $email );
		$user  = $found ? $found : null;
	}
	if ( ! $user ) {
		$user = tsvd_sso_create_user( $claims, $email, $name );
	}
	if ( ! $user ) {
		return 0;
	}

	update_user_meta( $user->ID, 'tsvd_google_sub', $sub );
	$roles = (array) $user->roles;
	if ( ! in_array( 'administrator', $roles, true ) && ! in_array( 'mitarbeiter', $roles, true ) ) {
		$user->add_role( 'mitarbeiter' );
	}
	return (int) $user->ID;
}

function tsvd_sso_find_user_by_sub( $sub ) {
	$users = get_users(
		array(
			'meta_key'   => 'tsvd_google_sub',
			'meta_value' => $sub,
			'number'     => 1,
		)
	);
	return $users ? $users[0] : null;
}

function tsvd_sso_login_base( array $claims, $email ) {
	$given  = isset( $claims['given_name'] ) ? (string) $claims['given_name'] : '';
	$family = isset( $claims['family_name'] ) ? (string) $claims['family_name'] : '';
	$base   = trim( $given . '.' . $family, '.' );
	if ( '' === $base ) {
		$base = (string) current( explode( '@', $email ) );
	}
	$base = sanitize_user( strtolower( remove_accents( $base ) ), true );
	$base = preg_replace( '/[^a-z0-9._-]/', '', $base );
	return '' !== $base ? $base : 'mitarbeiter';
}

function tsvd_sso_unique_login( array $claims, $email ) {
	$base  = tsvd_sso_login_base( $claims, $email );
	$login = $base;
	$n     = 2;
	while ( username_exists( $login ) ) {
		$login = $base . $n;
		++$n;
	}
	return $login;
}

function tsvd_sso_create_user( array $claims, $email, $name ) {
	if ( ! $email ) {
		return null;
	}
	$id = wp_insert_user(
		array(
			'user_login'   => tsvd_sso_unique_login( $claims, $email ),
			'user_email'   => $email,
			'display_name' => $name ? $name : $email,
			'user_pass'    => wp_generate_password( 32, true, true ),
			'role'         => 'mitarbeiter',
		)
	);
	if ( is_wp_error( $id ) ) {
		return null;
	}
	return get_user_by( 'id', $id );
}
