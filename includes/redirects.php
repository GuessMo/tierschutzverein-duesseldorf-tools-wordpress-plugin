<?php
/**
 * Admin-Seite "Redirects" (TSV Tools) im WP-CPT-Standardfluss: Listen-Ansicht mit
 * "Hinzufuegen"-Button + separate Edit-Ansicht pro Redirect. Zeigt zusaetzlich das
 * 404-Log. API-Client + Schreib-Handler: redirects-api.php.
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

function tsvd_r301_list_url( $extra = array() ) {
    return add_query_arg( array_merge( array( 'page' => 'tsvd-tools-redirects' ), $extra ), admin_url( 'admin.php' ) );
}

function tsvd_r301_edit_url( $id = 0, $extra = array() ) {
    return tsvd_r301_list_url( array_merge( array( 'view' => 'edit', 'id' => (int) $id ), $extra ) );
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

function tsvd_r301_render_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    if ( ! tsvd_r301_configured() ) {
        echo '<div class="wrap"><h1>Redirects</h1><div class="notice notice-error"><p>Route301-API nicht konfiguriert (ROUTE301_API_* fehlen im mu-plugin).</p></div></div>';
        return;
    }
    $view = isset( $_GET['view'] ) ? sanitize_key( $_GET['view'] ) : 'list';
    if ( 'edit' === $view ) {
        tsvd_r301_render_edit();
    } else {
        tsvd_r301_render_list();
    }
}

function tsvd_r301_status_options( $selected ) {
    $opts = array(
        301 => '301 (permanent)',
        302 => '302 (temporaer)',
        307 => '307 (temporaer)',
        308 => '308 (permanent)',
        410 => '410 (entfernt)',
    );
    $out = '';
    foreach ( $opts as $v => $label ) {
        $out .= '<option value="' . $v . '" ' . selected( $selected, $v, false ) . '>' . esc_html( $label ) . '</option>';
    }
    return $out;
}

function tsvd_r301_render_edit() {
    $id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
    $r  = array( 'domain' => 'tierheim-duesseldorf.de', 'sourcePath' => '', 'target' => '', 'statusCode' => 301, 'enabled' => true );
    if ( $id ) {
        $res = tsvd_r301_request( 'GET', '/api/redirects/' . $id );
        if ( is_array( $res['data'] ) ) {
            $r = array_merge( $r, $res['data'] );
        }
    } else {
        if ( isset( $_GET['source'] ) ) {
            $r['sourcePath'] = sanitize_text_field( wp_unslash( $_GET['source'] ) );
        }
        if ( isset( $_GET['domain'] ) ) {
            $r['domain'] = sanitize_text_field( wp_unslash( $_GET['domain'] ) );
        }
    }

    echo '<div class="wrap"><h1>' . ( $id ? 'Redirect bearbeiten' : 'Redirect hinzufuegen' ) . '</h1>';
    tsvd_r301_notice();
    echo '<p><a href="' . esc_url( tsvd_r301_list_url() ) . '">&larr; Zurueck zur Liste</a></p>';
    echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">'
        . '<input type="hidden" name="action" value="tsvd_r301_save">'
        . wp_nonce_field( 'tsvd_r301_save', '_wpnonce', true, false )
        . '<input type="hidden" name="id" value="' . (int) $id . '">'
        . '<table class="form-table"><tbody>'
        . '<tr><th><label for="r301-domain">Domain</label></th><td><input type="text" name="domain" id="r301-domain" class="regular-text" value="' . esc_attr( $r['domain'] ) . '" required></td></tr>'
        . '<tr><th><label for="r301-source">Quelle (Pfad)</label></th><td><input type="text" name="sourcePath" id="r301-source" class="regular-text" value="' . esc_attr( $r['sourcePath'] ) . '" placeholder="/de/alte-url" required></td></tr>'
        . '<tr><th><label for="r301-target">Ziel</label></th><td><input type="text" name="target" id="r301-target" class="regular-text" value="' . esc_attr( $r['target'] ) . '" placeholder="https://tierschutzverein-duesseldorf.de/neue-url/"><p class="description">Bei 410 ohne Ziel.</p></td></tr>'
        . '<tr><th><label for="r301-status">Status</label></th><td><select name="statusCode" id="r301-status" class="regular-text">' . tsvd_r301_status_options( (int) $r['statusCode'] ) . '</select></td></tr>'
        . '<tr><th>Aktiv</th><td><label><input type="checkbox" name="enabled" value="1" ' . checked( ! empty( $r['enabled'] ), true, false ) . '> aktiviert</label></td></tr>'
        . '</tbody></table>'
        . '<p><button type="submit" class="button button-primary">Speichern</button> '
        . '<a href="' . esc_url( tsvd_r301_list_url() ) . '" class="button">Abbrechen</a></p>'
        . '</form></div>';
}

function tsvd_r301_render_list() {
    echo '<div class="wrap"><h1 class="wp-heading-inline">Redirects</h1> '
        . '<a href="' . esc_url( tsvd_r301_edit_url( 0 ) ) . '" class="page-title-action">Hinzufuegen</a>'
        . '<hr class="wp-header-end">';
    tsvd_r301_notice();

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
    echo '<p class="description">Domain <code>tierheim-duesseldorf.de</code> &middot; ' . count( $redirects ) . ' Redirects. '
        . '301: ' . $n301 . ' (erreichbar: ' . $nReach . ', fehlt: ' . $nMiss . ') &middot; 410: ' . $n410 . '. '
        . '<a href="' . tsvd_r301_open_url( '/' ) . '" target="_blank" rel="noopener">Route301 oeffnen &#8599;</a></p>';

    echo '<table class="wp-list-table widefat fixed striped"><thead><tr>'
        . '<th class="r301-sortable">Quelle</th><th class="r301-sortable">Ziel</th>'
        . '<th class="r301-sortable" data-sort="num">Status</th><th class="r301-sortable">Aktiv</th>'
        . '<th class="r301-sortable">Erreichbar</th><th class="r301-sortable" data-sort="num">Hits</th><th>Aktion</th>'
        . '</tr></thead><tbody>';
    foreach ( $redirects as $r ) {
        $reach     = tsvd_r301_target_reachable( $r['statusCode'], $r['target'] );
        $reachCell = 'yes' === $reach ? '<span style="color:#227122">&#10003;</span>'
            : ( 'no' === $reach ? '<span style="color:#b32d2e" title="Ziel nicht auf der neuen Seite gefunden">&#10007;</span>'
            : ( 'gone' === $reach ? '<span class="description">&ndash; (410)</span>' : '?' ) );
        $del = '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline" onsubmit="return confirm(\'Redirect loeschen?\')">'
            . '<input type="hidden" name="action" value="tsvd_r301_delete">'
            . wp_nonce_field( 'tsvd_r301_delete', '_wpnonce', true, false )
            . '<input type="hidden" name="id" value="' . (int) $r['id'] . '">'
            . '<button class="button-link delete" style="color:#b32d2e">Loeschen</button></form>';
        echo '<tr>'
            . '<td><a href="' . esc_url( tsvd_r301_edit_url( (int) $r['id'] ) ) . '"><code>' . esc_html( $r['sourcePath'] ) . '</code></a></td>'
            . '<td><code>' . esc_html( $r['target'] ) . '</code></td>'
            . '<td>' . (int) $r['statusCode'] . '</td>'
            . '<td>' . ( ! empty( $r['enabled'] ) ? '&#10003;' : '&ndash;' ) . '</td>'
            . '<td>' . $reachCell . '</td>'
            . '<td>' . (int) $r['hitCount'] . '</td>'
            . '<td><a href="' . esc_url( tsvd_r301_edit_url( (int) $r['id'] ) ) . '" class="button button-small">Bearbeiten</a> ' . $del . '</td>'
            . '</tr>';
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
        . '<th class="r301-sortable">Host</th><th class="r301-sortable">Pfad</th>'
        . '<th class="r301-sortable" data-sort="num">Hits</th><th class="r301-sortable">Zuletzt</th><th>Redirect</th><th>Aktion</th>'
        . '</tr></thead><tbody>';
    foreach ( $nf as $l ) {
        $m = tsvd_r301_find_match( $l['host'], $l['path'], $redirects );
        if ( $m ) {
            $badge  = ! empty( $m['enabled'] ) ? '' : ' <span style="color:#b32d2e">(inaktiv)</span>';
            $status = '&#8594; <code>' . esc_html( $m['target'] ) . '</code> (' . (int) $m['statusCode'] . ')' . $badge;
            $action = '<a href="' . esc_url( tsvd_r301_edit_url( (int) $m['id'] ) ) . '" class="button button-small">Bearbeiten</a>';
        } else {
            $status = '<span style="color:#b32d2e">offen</span>';
            $action = '<a href="' . esc_url( tsvd_r301_edit_url( 0, array( 'source' => $l['path'], 'domain' => $l['host'] ) ) ) . '" class="button button-small button-primary">Redirect anlegen</a>';
        }
        echo '<tr><td>' . esc_html( $l['host'] ) . '</td>'
            . '<td><code>' . esc_html( $l['path'] ) . '</code></td>'
            . '<td>' . (int) $l['hitCount'] . '</td>'
            . '<td>' . esc_html( isset( $l['lastHitAt'] ) ? substr( (string) $l['lastHitAt'], 0, 16 ) : '' ) . '</td>'
            . '<td>' . $status . '</td>'
            . '<td>' . $action . '</td></tr>';
    }
    echo '</tbody></table></div>';
}
