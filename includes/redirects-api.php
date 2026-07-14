<?php
/**
 * Route301 API-Client + Schreib-Handler. Spricht die Route301-REST-API per HTTP-Basic
 * an (Credentials aus mu-plugin-Konstanten ROUTE301_API_*). Anzeige/Menu: redirects.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tsvd_r301_configured() {
    return defined( 'ROUTE301_API_URL' ) && defined( 'ROUTE301_API_USER' ) && defined( 'ROUTE301_API_PASS' );
}

function tsvd_r301_request( $method, $path, $body = null ) {
    if ( ! tsvd_r301_configured() ) {
        return array( 'code' => 0, 'data' => null, 'error' => 'ROUTE301_API_* nicht konfiguriert' );
    }
    $args = array(
        'method'  => $method,
        'timeout' => 15,
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode( ROUTE301_API_USER . ':' . ROUTE301_API_PASS ),
            'Accept'        => 'application/json',
        ),
    );
    if ( null !== $body ) {
        $args['headers']['Content-Type'] = 'application/json';
        $args['body']                    = wp_json_encode( $body );
    }
    $res = wp_remote_request( rtrim( ROUTE301_API_URL, '/' ) . $path, $args );
    if ( is_wp_error( $res ) ) {
        return array( 'code' => 0, 'data' => null, 'error' => $res->get_error_message() );
    }
    $code = (int) wp_remote_retrieve_response_code( $res );
    $data = json_decode( wp_remote_retrieve_body( $res ), true );
    return array( 'code' => $code, 'data' => $data, 'error' => ( $code >= 400 ? 'HTTP ' . $code : '' ) );
}

function tsvd_r301_get_redirects() {
    $r = tsvd_r301_request( 'GET', '/api/redirects' );
    return is_array( $r['data'] ) ? $r['data'] : array();
}

function tsvd_r301_get_not_found( $limit = 200 ) {
    $r = tsvd_r301_request( 'GET', '/api/not-found?limit=' . (int) $limit );
    return is_array( $r['data'] ) ? $r['data'] : array();
}

function tsvd_r301_find_match( $host, $path, $redirects ) {
    $path = (string) $path;
    $norm = rtrim( $path, '/' );
    foreach ( $redirects as $r ) {
        if ( (string) ( $r['domain'] ?? '' ) !== (string) $host ) {
            continue;
        }
        if ( ! empty( $r['isRegex'] ) ) {
            continue; // Regex-Semantik von route301 nicht raten -> nicht auto-matchen
        }
        $src = (string) ( $r['sourcePath'] ?? '' );
        if ( $src === $path || rtrim( $src, '/' ) === $norm ) {
            return $r;
        }
    }
    return null;
}

function tsvd_r301_redirect_back( $ok, $msg ) {
    $url = add_query_arg(
        array( 'r301' => $ok ? 'ok' : 'err', 'r301msg' => rawurlencode( $msg ) ),
        admin_url( 'admin.php?page=tsvd-tools-redirects' )
    );
    wp_safe_redirect( $url );
    exit;
}

add_action( 'admin_post_tsvd_r301_save', 'tsvd_r301_handle_save' );
function tsvd_r301_handle_save() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( '-1' );
    }
    check_admin_referer( 'tsvd_r301_save' );

    $id   = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    $body = array(
        'domain'     => sanitize_text_field( wp_unslash( $_POST['domain'] ?? '' ) ),
        'sourcePath' => sanitize_text_field( wp_unslash( $_POST['sourcePath'] ?? '' ) ),
        'target'     => sanitize_text_field( wp_unslash( $_POST['target'] ?? '' ) ),
        'statusCode' => (int) ( $_POST['statusCode'] ?? 301 ),
        'enabled'    => ! empty( $_POST['enabled'] ),
    );
    if ( '' === $body['sourcePath'] || '' === $body['target'] || '' === $body['domain'] ) {
        tsvd_r301_redirect_back( false, 'Domain, Quelle und Ziel sind Pflicht.' );
    }

    $r = $id
        ? tsvd_r301_request( 'PATCH', '/api/redirects/' . $id, $body )
        : tsvd_r301_request( 'POST', '/api/redirects', $body );

    $ok = $r['code'] >= 200 && $r['code'] < 300;
    tsvd_r301_redirect_back( $ok, $ok ? ( $id ? 'Redirect aktualisiert.' : 'Redirect angelegt.' ) : ( 'Fehler: ' . $r['error'] ) );
}

add_action( 'admin_post_tsvd_r301_delete', 'tsvd_r301_handle_delete' );
function tsvd_r301_handle_delete() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( '-1' );
    }
    check_admin_referer( 'tsvd_r301_delete' );
    $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
    $r  = tsvd_r301_request( 'DELETE', '/api/redirects/' . $id );
    $ok = $r['code'] >= 200 && $r['code'] < 300;
    tsvd_r301_redirect_back( $ok, $ok ? 'Redirect geloescht.' : ( 'Fehler: ' . $r['error'] ) );
}
