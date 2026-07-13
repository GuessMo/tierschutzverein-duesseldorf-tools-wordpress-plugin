<?php
/**
 * Admin-Unterseite "Backups": zeigt das vom Server-Cron geschriebene Manifest
 * (DB- und Media-Backups) und bietet 1-Klick-Restore (Dry-Run + echt). Der Restore
 * selbst laeuft server-seitig: WP schreibt eine Anfrage-Datei, ein root-Cron-Worker
 * (restore_worker.sh) zieht die Datei(en) per FTPS von All-Inkl und stellt DB bzw.
 * Medien (Basis + alle Inkremente) wieder her. Status kommt als JSON zurueck.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'TSVD_TOOLS_BACKUP_MANIFEST' ) ) {
    define( 'TSVD_TOOLS_BACKUP_MANIFEST', 'tsv-backups-manifest.json' );
    define( 'TSVD_TOOLS_BACKUP_REQUEST', 'tsv-restore-request.json' );
    define( 'TSVD_TOOLS_BACKUP_STATUS', 'tsv-restore-status.json' );
}

function tsvd_tools_backup_uploads_file( $name ) {
    $uploads = wp_upload_dir();
    return trailingslashit( $uploads['basedir'] ) . $name;
}

function tsvd_tools_backup_read_json( $name ) {
    $path = tsvd_tools_backup_uploads_file( $name );
    if ( ! is_readable( $path ) ) {
        return null;
    }
    $raw  = file_get_contents( $path );
    $data = ( false !== $raw ) ? json_decode( $raw, true ) : null;
    return is_array( $data ) ? $data : null;
}

function tsvd_tools_backup_category_label( $category ) {
    $map = array(
        'db'    => 'Datenbank',
        'media' => 'Medien (Datei-Spiegel)',
    );
    return isset( $map[ $category ] ) ? $map[ $category ] : $category;
}

function tsvd_tools_backup_format_time( $iso_utc ) {
    if ( '' === $iso_utc ) {
        return '—';
    }
    $ts = strtotime( $iso_utc );
    if ( false === $ts ) {
        return $iso_utc;
    }
    return date_i18n( 'Y-m-d H:i', $ts + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
}

add_action( 'admin_menu', 'tsvd_tools_backup_register_page' );
function tsvd_tools_backup_register_page() {
    add_submenu_page(
        'tsvd-tools',
        'Backups',
        'Backups',
        'manage_options',
        'tsvd-tools-backups',
        'tsvd_tools_backup_render_page'
    );
}

function tsvd_tools_backup_restore_form( $kind, $target, $label_real ) {
    $confirm = esc_js( 'Wirklich wiederherstellen? Aktuelle Daten werden ueberschrieben.' );
    $out  = '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline">';
    $out .= '<input type="hidden" name="action" value="tsvd_tools_backup_restore">';
    $out .= '<input type="hidden" name="kind" value="' . esc_attr( $kind ) . '">';
    $out .= '<input type="hidden" name="target" value="' . esc_attr( $target ) . '">';
    $out .= wp_nonce_field( 'tsvd_tools_backup_restore', '_wpnonce', true, false );
    $out .= '<button type="submit" name="dry_run" value="1" class="button">Testlauf</button> ';
    $out .= '<button type="submit" name="dry_run" value="0" class="button button-primary" '
          . 'onclick="return confirm(\'' . $confirm . '\')">' . esc_html( $label_real ) . '</button>';
    $out .= '</form>';
    return $out;
}

function tsvd_tools_backup_render_status() {
    if ( isset( $_GET['sb_restore'] ) ) {
        $ok = 'queued' === $_GET['sb_restore'];
        echo '<div class="notice notice-' . ( $ok ? 'success' : 'error' ) . ' is-dismissible"><p>'
            . esc_html( $ok
                ? 'Restore-Anfrage eingereiht. Der Server verarbeitet sie innerhalb ~1 Minute.'
                : 'Restore-Anfrage konnte nicht geschrieben werden.' )
            . '</p></div>';
    }
    $status = tsvd_tools_backup_read_json( TSVD_TOOLS_BACKUP_STATUS );
    if ( null === $status ) {
        return;
    }
    $state = isset( $status['state'] ) ? $status['state'] : '';
    $cls   = 'running' === $state ? 'notice-info' : ( 'error' === $state ? 'notice-error' : 'notice-success' );
    echo '<div class="notice ' . esc_attr( $cls ) . '"><p><strong>Letzter Restore:</strong> '
        . esc_html( ucfirst( $state ) ) . ' &middot; ' . esc_html( isset( $status['message'] ) ? $status['message'] : '' )
        . ' <em>(' . esc_html( tsvd_tools_backup_format_time( isset( $status['updated_at'] ) ? $status['updated_at'] : '' ) ) . ')</em></p></div>';
}

function tsvd_tools_backup_render_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $manifest = tsvd_tools_backup_read_json( TSVD_TOOLS_BACKUP_MANIFEST );

    echo '<div class="wrap"><h1>Backups</h1>';
    tsvd_tools_backup_render_status();

    if ( null === $manifest ) {
        echo '<div class="notice notice-warning"><p>Noch kein Backup-Manifest gefunden. Es wird nach dem naechtlichen Backup-Lauf erstellt.</p></div></div>';
        return;
    }

    $host  = isset( $manifest['remote_host'] ) ? $manifest['remote_host'] : '';
    $gen   = isset( $manifest['generated_at'] ) ? $manifest['generated_at'] : '';
    $items = isset( $manifest['items'] ) && is_array( $manifest['items'] ) ? $manifest['items'] : array();

    echo '<p class="description">Stand: ' . esc_html( tsvd_tools_backup_format_time( $gen ) )
        . ' &middot; Ziel: All-Inkl (' . esc_html( $host ) . ') &middot; DB: letzte Dumps (Rotation). '
        . 'Medien: Basis + Inkremente (werden beim Restore zusammengesetzt).</p>';

    echo '<h2>Medien wiederherstellen</h2>';
    echo '<p>Kopiert den lokalen Datei-Spiegel (letzter Backup-Stand) ins Upload-Verzeichnis. '
        . 'Neuere Dateien bleiben erhalten.</p>';
    echo '<p>' . tsvd_tools_backup_restore_form( 'media', 'current', 'Medien wiederherstellen' ) . '</p>';

    echo '<h2>Datenbank &amp; Dateien</h2>';
    echo '<table class="wp-list-table widefat fixed striped"><thead><tr>'
        . '<th>Typ</th><th>Datei</th><th>Groesse</th><th>Erstellt</th><th>All-Inkl-Pfad</th><th>Aktion</th>'
        . '</tr></thead><tbody>';

    foreach ( $items as $item ) {
        $name   = isset( $item['name'] ) ? $item['name'] : '';
        $cat    = isset( $item['category'] ) ? $item['category'] : '';
        $size   = isset( $item['size_human'] ) ? $item['size_human'] : '';
        $mtime  = isset( $item['mtime'] ) ? $item['mtime'] : '';
        $remote = isset( $item['remote_path'] ) ? $item['remote_path'] : '';

        $action = ( 'db' === $cat )
            ? tsvd_tools_backup_restore_form( 'db', $name, 'Wiederherstellen' )
            : '<span class="description">siehe „Medien wiederherstellen" oben</span>';

        echo '<tr>'
            . '<td>' . esc_html( tsvd_tools_backup_category_label( $cat ) ) . '</td>'
            . '<td><code>' . esc_html( $name ) . '</code></td>'
            . '<td>' . esc_html( $size ) . '</td>'
            . '<td>' . esc_html( tsvd_tools_backup_format_time( $mtime ) ) . '</td>'
            . '<td><code>' . esc_html( $remote ) . '</code></td>'
            . '<td>' . $action . '</td>'
            . '</tr>';
    }
    echo '</tbody></table></div>';
}

add_action( 'admin_post_tsvd_tools_backup_restore', 'tsvd_tools_backup_handle_restore' );
function tsvd_tools_backup_handle_restore() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( '-1' );
    }
    check_admin_referer( 'tsvd_tools_backup_restore' );

    $kind   = isset( $_POST['kind'] ) ? sanitize_key( $_POST['kind'] ) : '';
    $target = isset( $_POST['target'] ) ? sanitize_text_field( wp_unslash( $_POST['target'] ) ) : '';
    $dry    = ! empty( $_POST['dry_run'] ) && '1' === $_POST['dry_run'];

    $ok = false;
    if ( in_array( $kind, array( 'db', 'media' ), true ) && '' !== $target ) {
        $data = array(
            'request_id'   => uniqid( 'r', true ),
            'kind'         => $kind,
            'target'       => $target,
            'dry_run'      => $dry,
            'requested_by' => wp_get_current_user()->user_login,
            'requested_at' => gmdate( 'Y-m-d\TH:i:s\Z' ),
        );
        $ok = false !== file_put_contents(
            tsvd_tools_backup_uploads_file( TSVD_TOOLS_BACKUP_REQUEST ),
            wp_json_encode( $data )
        );
    }

    wp_safe_redirect( add_query_arg( 'sb_restore', $ok ? 'queued' : 'error', admin_url( 'admin.php?page=tsvd-tools-backups' ) ) );
    exit;
}
