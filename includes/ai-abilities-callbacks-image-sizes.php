<?php
/**
 * AI Abilities — Reparatur der Bildgrößen bearbeiteter Bilder (2026-07-29).
 *
 * WordPress 7.0 erzeugt beim Zuschneiden/Skalieren die registrierten Größen nicht neu,
 * solange die Speicher-Anfrage kein `target=all` mitbringt. Das Theme korrigiert das im
 * Filter `wp_update_attachment_metadata` (`inc/media-image-edit-subsizes.php`). Bilder, die
 * VOR dieser Korrektur bearbeitet wurden, tragen weiterhin veraltete Größen-Einträge — und
 * `create-image-subsizes` hilft dort nicht, weil WordPress die Einträge für vollständig
 * hält (`missing_image_sizes` ist leer).
 *
 * Diese Ability stößt für solche Attachments eine Metadaten-Aktualisierung an, wodurch der
 * Theme-Filter greift.
 *
 * @package TSVD_Tools
 */

if (!defined('ABSPATH')) exit;

/**
 * @param string $file Dateiname oder Pfad.
 * @return string Edit-Marker (`e<timestamp>`) oder leerer String.
 */
function tsvd_tools_get_image_edit_marker($file) {
    if (preg_match('/-(e\d{10,})\./', wp_basename((string) $file), $matches)) {
        return $matches[1];
    }

    return '';
}

/**
 * Alle Attachments, deren Datei einen Edit-Marker trägt.
 *
 * @return int[]
 */
function tsvd_tools_find_edited_attachments() {
    global $wpdb;

    $ids = $wpdb->get_col(
        "SELECT post_id FROM {$wpdb->postmeta}
         WHERE meta_key = '_wp_attached_file'
           AND meta_value REGEXP '-e[0-9]{10,}\\\\.'"
    );

    return array_map('intval', $ids);
}

/**
 * @param array  $sizes  Größen-Einträge der Metadaten.
 * @param string $marker Erwarteter Edit-Marker.
 * @return string[] Namen der Größen, die noch auf eine Datei vor der Bearbeitung zeigen.
 */
function tsvd_tools_find_stale_sizes($sizes, $marker) {
    $stale = array();

    foreach ((array) $sizes as $name => $size) {
        if (empty($size['file'])) {
            continue;
        }
        if (false === strpos($size['file'], $marker)) {
            $stale[] = (string) $name;
        }
    }

    return $stale;
}

/**
 * Ability-Callback: veraltete Größen bearbeiteter Bilder neu erzeugen lassen.
 *
 * @param array $input Ability-Input.
 * @return array
 */
function tsvd_tools_ai_repair_edited_image_sizes($input) {
    $ids = array();
    if (!empty($input['attachment_ids']) && is_array($input['attachment_ids'])) {
        $ids = array_values(array_filter(array_map('absint', $input['attachment_ids'])));
    }
    if (!$ids) {
        $ids = tsvd_tools_find_edited_attachments();
    }

    $apply  = !empty($input['apply']);
    $result = array(
        'applied'          => $apply,
        'checked'          => count($ids),
        'affected'         => array(),
        'repaired'         => 0,
        'still_stale'      => array(),
    );

    foreach ($ids as $attachment_id) {
        $meta = wp_get_attachment_metadata($attachment_id);
        if (!is_array($meta) || empty($meta['file'])) {
            continue;
        }

        $marker = tsvd_tools_get_image_edit_marker($meta['file']);
        if ('' === $marker) {
            continue;
        }

        $stale = tsvd_tools_find_stale_sizes(isset($meta['sizes']) ? $meta['sizes'] : array(), $marker);
        if (!$stale) {
            continue;
        }

        $result['affected'][] = array(
            'id'    => $attachment_id,
            'file'  => wp_basename($meta['file']),
            'stale' => $stale,
        );

        if (!$apply) {
            continue;
        }

        wp_update_attachment_metadata($attachment_id, $meta);

        $after       = wp_get_attachment_metadata($attachment_id);
        $after_stale = tsvd_tools_find_stale_sizes(
            isset($after['sizes']) ? $after['sizes'] : array(),
            $marker
        );

        if ($after_stale) {
            $result['still_stale'][] = array('id' => $attachment_id, 'stale' => $after_stale);
            continue;
        }

        $result['repaired']++;
    }

    return $result;
}
