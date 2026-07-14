<?php
/**
 * Admin-Seite "Redirects" (TSV Tools): zeigt Route301-Redirects + 404-Log und erlaubt
 * Anlegen/Bearbeiten/Loeschen ueber die Route301-API. API-Client: redirects-api.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_menu', 'tsvd_r301_register_page' );
function tsvd_r301_register_page() {
    add_submenu_page(
        'tsvd-tools',
        'Redirects',
        'Redirects',
        'manage_options',
        'tsvd-tools-redirects',
        'tsvd_r301_render_page'
    );
}

function tsvd_r301_open_url( $path ) {
    return esc_url( rtrim( ROUTE301_API_URL, '/' ) . $path );
}

function tsvd_r301_notice() {
    if ( ! isset( $_GET['r301'] ) ) {
        return;
    }
    $ok  = 'ok' === $_GET['r301'];
    $msg = isset( $_GET['r301msg'] ) ? sanitize_text_field( wp_unslash( $_GET['r301msg'] ) ) : '';
    echo '<div class="notice notice-' . ( $ok ? 'success' : 'error' ) . ' is-dismissible"><p>' . esc_html( $msg ) . '</p></div>';
}

function tsvd_r301_form() {
    $nonce = wp_nonce_field( 'tsvd_r301_save', '_wpnonce', true, false );
    $url   = esc_url( admin_url( 'admin-post.php' ) );
    echo '<h2 id="r301-form-title">Redirect anlegen / bearbeiten</h2>'
        . '<form method="post" action="' . $url . '" style="margin-bottom:20px">'
        . '<input type="hidden" name="action" value="tsvd_r301_save">' . $nonce
        . '<input type="hidden" name="id" id="r301-id" value="">'
        . '<table class="form-table"><tbody>'
        . '<tr><th><label for="r301-domain">Domain</label></th><td><input name="domain" id="r301-domain" class="regular-text" placeholder="tierheim-duesseldorf.de" required></td></tr>'
        . '<tr><th><label for="r301-source">Quelle (Pfad)</label></th><td><input name="sourcePath" id="r301-source" class="regular-text" placeholder="/alte-url/" required></td></tr>'
        . '<tr><th><label for="r301-target">Ziel</label></th><td><input name="target" id="r301-target" class="regular-text" placeholder="/neue-url/" required></td></tr>'
        . '<tr><th><label for="r301-status">Status</label></th><td><select name="statusCode" id="r301-status"><option value="301">301 (permanent)</option><option value="302">302 (temporaer)</option><option value="410">410 (entfernt)</option></select></td></tr>'
        . '<tr><th>Aktiv</th><td><label><input type="checkbox" name="enabled" id="r301-enabled" value="1" checked> aktiviert</label></td></tr>'
        . '</tbody></table>'
        . '<p><button type="submit" class="button button-primary">Speichern</button> '
        . '<button type="button" class="button" onclick="r301Reset()">Zuruecksetzen</button></p>'
        . '</form>';
}

function tsvd_r301_render_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    echo '<div class="wrap"><h1>Redirects</h1>';
    if ( ! tsvd_r301_configured() ) {
        echo '<div class="notice notice-error"><p>Route301-API nicht konfiguriert (ROUTE301_API_* fehlen im mu-plugin).</p></div></div>';
        return;
    }
    tsvd_r301_notice();
    echo '<p class="description">Verwaltung der Weiterleitungen im Route301-Tool. '
        . '<a href="' . tsvd_r301_open_url( '/' ) . '" target="_blank" rel="noopener">Route301 oeffnen &#8599;</a></p>';

    tsvd_r301_form();

    $redirects = tsvd_r301_get_redirects();
    echo '<h2>Redirects (' . count( $redirects ) . ')</h2>';
    echo '<table class="wp-list-table widefat fixed striped"><thead><tr>'
        . '<th>Domain</th><th>Quelle</th><th>Ziel</th><th>Status</th><th>Aktiv</th><th>Hits</th><th>Aktion</th>'
        . '</tr></thead><tbody>';
    foreach ( $redirects as $r ) {
        $del = '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline" onsubmit="return confirm(\'Redirect loeschen?\')">'
            . '<input type="hidden" name="action" value="tsvd_r301_delete">'
            . wp_nonce_field( 'tsvd_r301_delete', '_wpnonce', true, false )
            . '<input type="hidden" name="id" value="' . (int) $r['id'] . '">'
            . '<button class="button-link delete" style="color:#b32d2e">Loeschen</button></form>';
        $edit = '<button type="button" class="button button-small" onclick=\'r301Edit(' . wp_json_encode( array(
            'id'         => (int) $r['id'],
            'domain'     => $r['domain'],
            'sourcePath' => $r['sourcePath'],
            'target'     => $r['target'],
            'statusCode' => (int) $r['statusCode'],
            'enabled'    => (bool) $r['enabled'],
        ) ) . '\')>Bearbeiten</button> ';
        echo '<tr><td>' . esc_html( $r['domain'] ) . '</td>'
            . '<td><code>' . esc_html( $r['sourcePath'] ) . '</code></td>'
            . '<td><code>' . esc_html( $r['target'] ) . '</code></td>'
            . '<td>' . (int) $r['statusCode'] . '</td>'
            . '<td>' . ( ! empty( $r['enabled'] ) ? '&#10003;' : '&ndash;' ) . '</td>'
            . '<td>' . (int) $r['hitCount'] . '</td>'
            . '<td>' . $edit . $del . '</td></tr>';
    }
    echo '</tbody></table>';

    $nf   = tsvd_r301_get_not_found();
    $open = 0;
    foreach ( $nf as $l ) {
        if ( null === tsvd_r301_find_match( $l['host'], $l['path'], $redirects ) ) {
            $open++;
        }
    }
    echo '<h2>404-Log (' . count( $nf ) . ', davon ' . $open . ' ohne Redirect)</h2>';
    echo '<table class="wp-list-table widefat fixed striped"><thead><tr>'
        . '<th>Host</th><th>Pfad</th><th>Hits</th><th>Zuletzt</th><th>Redirect</th><th>Aktion</th></tr></thead><tbody>';
    foreach ( $nf as $l ) {
        $m = tsvd_r301_find_match( $l['host'], $l['path'], $redirects );
        if ( $m ) {
            $badge  = ! empty( $m['enabled'] ) ? '' : ' <span style="color:#b32d2e">(inaktiv)</span>';
            $status = '&#8594; <code>' . esc_html( $m['target'] ) . '</code> (' . (int) $m['statusCode'] . ')' . $badge;
            $action = '<button type="button" class="button button-small" onclick=\'r301Edit(' . wp_json_encode( array(
                'id'         => (int) $m['id'],
                'domain'     => $m['domain'],
                'sourcePath' => $m['sourcePath'],
                'target'     => $m['target'],
                'statusCode' => (int) $m['statusCode'],
                'enabled'    => (bool) $m['enabled'],
            ) ) . '\')>Bearbeiten</button>';
        } else {
            $status = '<span style="color:#b32d2e">offen</span>';
            $action = '<button type="button" class="button button-small button-primary" onclick=\'r301Edit(' . wp_json_encode( array(
                'id'         => 0,
                'domain'     => $l['host'],
                'sourcePath' => $l['path'],
                'target'     => '',
                'statusCode' => 301,
                'enabled'    => true,
            ) ) . '\')>Redirect anlegen</button>';
        }
        echo '<tr><td>' . esc_html( $l['host'] ) . '</td>'
            . '<td><code>' . esc_html( $l['path'] ) . '</code></td>'
            . '<td>' . (int) $l['hitCount'] . '</td>'
            . '<td>' . esc_html( isset( $l['lastHitAt'] ) ? substr( (string) $l['lastHitAt'], 0, 16 ) : '' ) . '</td>'
            . '<td>' . $status . '</td>'
            . '<td>' . $action . '</td></tr>';
    }
    echo '</tbody></table>';

    echo '<script>
function r301Edit(d){document.getElementById("r301-id").value=d.id||"";
document.getElementById("r301-domain").value=d.domain||"";
document.getElementById("r301-source").value=d.sourcePath||"";
document.getElementById("r301-target").value=d.target||"";
document.getElementById("r301-status").value=d.statusCode||301;
document.getElementById("r301-enabled").checked=!!d.enabled;
document.getElementById("r301-form-title").scrollIntoView({behavior:"smooth"});}
function r301Reset(){r301Edit({statusCode:301,enabled:true});document.getElementById("r301-id").value="";}
</script></div>';
}
