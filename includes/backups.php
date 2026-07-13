<?php
/**
 * Admin-Unterseite "Backups": zeigt das vom Server-Cron geschriebene Manifest
 * (DB- und Media-Backups) mit Zeitpunkt, Groesse und All-Inkl-Pfad. Reines Lesen;
 * die Backups erstellt der Server per Cron und spiegelt sie per FTPS zu All-Inkl.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'TSVD_TOOLS_BACKUP_MANIFEST' ) ) {
    define( 'TSVD_TOOLS_BACKUP_MANIFEST', 'tsv-backups-manifest.json' );
}

function tsvd_tools_backup_manifest_path() {
    $uploads = wp_upload_dir();
    return trailingslashit( $uploads['basedir'] ) . TSVD_TOOLS_BACKUP_MANIFEST;
}

function tsvd_tools_backup_read_manifest() {
    $path = tsvd_tools_backup_manifest_path();
    if ( ! is_readable( $path ) ) {
        return null;
    }
    $raw = file_get_contents( $path );
    if ( false === $raw ) {
        return null;
    }
    $data = json_decode( $raw, true );
    return is_array( $data ) ? $data : null;
}

function tsvd_tools_backup_category_label( $category ) {
    $map = array(
        'db'         => 'Datenbank',
        'media-full' => 'Medien (Voll-Archiv)',
        'media-inc'  => 'Medien (Inkrement)',
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

function tsvd_tools_backup_render_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    $manifest = tsvd_tools_backup_read_manifest();

    echo '<div class="wrap"><h1>Backups</h1>';

    if ( null === $manifest ) {
        echo '<div class="notice notice-warning"><p>'
            . 'Noch kein Backup-Manifest gefunden. Es wird nach dem naechtlichen Backup-Lauf erstellt.'
            . '</p></div></div>';
        return;
    }

    $generated = isset( $manifest['generated_at'] ) ? $manifest['generated_at'] : '';
    $host      = isset( $manifest['remote_host'] ) ? $manifest['remote_host'] : '';
    $items     = isset( $manifest['items'] ) && is_array( $manifest['items'] ) ? $manifest['items'] : array();

    echo '<p class="description">Stand: ' . esc_html( tsvd_tools_backup_format_time( $generated ) )
        . ' &middot; Ziel: All-Inkl (' . esc_html( $host ) . ')'
        . ' &middot; Rotation: 3 Versionen (DB) bzw. 3 Wochen-Zyklen (Medien).</p>';

    if ( empty( $items ) ) {
        echo '<p>Keine Backup-Dateien im Manifest.</p></div>';
        return;
    }

    echo '<table class="wp-list-table widefat fixed striped"><thead><tr>'
        . '<th>Typ</th><th>Datei</th><th>Groesse</th><th>Erstellt</th><th>All-Inkl-Pfad</th>'
        . '</tr></thead><tbody>';

    foreach ( $items as $item ) {
        $name   = isset( $item['name'] ) ? $item['name'] : '';
        $cat    = isset( $item['category'] ) ? $item['category'] : '';
        $size   = isset( $item['size_human'] ) ? $item['size_human'] : '';
        $mtime  = isset( $item['mtime'] ) ? $item['mtime'] : '';
        $remote = isset( $item['remote_path'] ) ? $item['remote_path'] : '';

        echo '<tr>'
            . '<td>' . esc_html( tsvd_tools_backup_category_label( $cat ) ) . '</td>'
            . '<td><code>' . esc_html( $name ) . '</code></td>'
            . '<td>' . esc_html( $size ) . '</td>'
            . '<td>' . esc_html( tsvd_tools_backup_format_time( $mtime ) ) . '</td>'
            . '<td><code>' . esc_html( $remote ) . '</code></td>'
            . '</tr>';
    }

    echo '</tbody></table></div>';
}
