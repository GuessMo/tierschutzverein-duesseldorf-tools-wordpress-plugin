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

add_action( 'admin_enqueue_scripts', 'tsvd_r301_enqueue' );
function tsvd_r301_enqueue( $hook ) {
    if ( false === strpos( (string) $hook, 'tsvd-tools-redirects' ) ) {
        return;
    }
    wp_enqueue_script( 'tsvd-r301', TSVD_TOOLS_URL . 'assets/redirects.js', array(), TSVD_TOOLS_VERSION, true );
    wp_add_inline_style( 'wp-admin', 'th.r301-sortable{cursor:pointer;white-space:nowrap} th.r301-sortable.sorted.asc::after{content:" \\2191"} th.r301-sortable.sorted.desc::after{content:" \\2193"}' );
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
        . '<button type="button" class="button r301-reset">Zuruecksetzen</button></p>'
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
    $n301 = $n410 = $nReach = $nMiss = 0;
    foreach ( $redirects as $r ) {
        if ( 410 === (int) $r['statusCode'] ) {
            $n410++;
        } else {
            $n301++;
            'yes' === tsvd_r301_target_reachable( $r['statusCode'], $r['target'] ) ? $nReach++ : $nMiss++;
        }
    }
    echo '<h2>Redirects (' . count( $redirects ) . ')</h2>';
    echo '<p class="description">Nur Domain <code>tierheim-duesseldorf.de</code>. '
        . '301: ' . $n301 . ' (Ziel erreichbar: ' . $nReach . ', fehlt: ' . $nMiss . ') &middot; 410: ' . $n410 . '. '
        . '„Erreichbar" prüft, ob das Ziel auf der neuen Seite existiert (funktioniert auch im Wartungsmodus).</p>';
    echo '<table class="wp-list-table widefat fixed striped"><thead><tr>'
        . '<th class="r301-sortable">Quelle</th><th class="r301-sortable">Ziel</th>'
        . '<th class="r301-sortable" data-sort="num">Status</th><th class="r301-sortable">Aktiv</th>'
        . '<th class="r301-sortable">Erreichbar</th><th class="r301-sortable" data-sort="num">Hits</th><th>Aktion</th>'
        . '</tr></thead><tbody>';
    foreach ( $redirects as $r ) {
        $del = '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline" onsubmit="return confirm(\'Redirect loeschen?\')">'
            . '<input type="hidden" name="action" value="tsvd_r301_delete">'
            . wp_nonce_field( 'tsvd_r301_delete', '_wpnonce', true, false )
            . '<input type="hidden" name="id" value="' . (int) $r['id'] . '">'
            . '<button class="button-link delete" style="color:#b32d2e">Loeschen</button></form>';
        $edit = '<button type="button" class="button button-small r301-edit"'
            . ' data-id="' . (int) $r['id'] . '"'
            . ' data-domain="' . esc_attr( $r['domain'] ) . '"'
            . ' data-source="' . esc_attr( $r['sourcePath'] ) . '"'
            . ' data-target="' . esc_attr( $r['target'] ) . '"'
            . ' data-status="' . (int) $r['statusCode'] . '"'
            . ' data-enabled="' . ( ! empty( $r['enabled'] ) ? '1' : '0' ) . '">Bearbeiten</button> ';
        $reach = tsvd_r301_target_reachable( $r['statusCode'], $r['target'] );
        if ( 'yes' === $reach ) {
            $reachCell = '<span style="color:#227122">&#10003;</span>';
        } elseif ( 'no' === $reach ) {
            $reachCell = '<span style="color:#b32d2e" title="Ziel nicht auf der neuen Seite gefunden">&#10007;</span>';
        } elseif ( 'gone' === $reach ) {
            $reachCell = '<span class="description">&ndash; (410)</span>';
        } else {
            $reachCell = '?';
        }
        echo '<tr><td><code>' . esc_html( $r['sourcePath'] ) . '</code></td>'
            . '<td><code>' . esc_html( $r['target'] ) . '</code></td>'
            . '<td>' . (int) $r['statusCode'] . '</td>'
            . '<td>' . ( ! empty( $r['enabled'] ) ? '&#10003;' : '&ndash;' ) . '</td>'
            . '<td>' . $reachCell . '</td>'
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
            $action = '<button type="button" class="button button-small r301-edit"'
                . ' data-id="' . (int) $m['id'] . '"'
                . ' data-domain="' . esc_attr( $m['domain'] ) . '"'
                . ' data-source="' . esc_attr( $m['sourcePath'] ) . '"'
                . ' data-target="' . esc_attr( $m['target'] ) . '"'
                . ' data-status="' . (int) $m['statusCode'] . '"'
                . ' data-enabled="' . ( ! empty( $m['enabled'] ) ? '1' : '0' ) . '">Bearbeiten</button>';
        } else {
            $status = '<span style="color:#b32d2e">offen</span>';
            $action = '<button type="button" class="button button-small button-primary r301-edit"'
                . ' data-id="0"'
                . ' data-domain="' . esc_attr( $l['host'] ) . '"'
                . ' data-source="' . esc_attr( $l['path'] ) . '"'
                . ' data-target="" data-status="301" data-enabled="1">Redirect anlegen</button>';
        }
        echo '<tr><td>' . esc_html( $l['host'] ) . '</td>'
            . '<td><code>' . esc_html( $l['path'] ) . '</code></td>'
            . '<td>' . (int) $l['hitCount'] . '</td>'
            . '<td>' . esc_html( isset( $l['lastHitAt'] ) ? substr( (string) $l['lastHitAt'], 0, 16 ) : '' ) . '</td>'
            . '<td>' . $status . '</td>'
            . '<td>' . $action . '</td></tr>';
    }
    echo '</tbody></table>';

    echo '</div>';
}
