<?php
/**
 * AI Abilities — Wartungs-Callbacks (2026-07-29).
 *
 * Bereinigt Import-Artefakte in Tier-Bildern: Attachments, die ein Migrations-
 * Import pro Tier erneut heruntergeladen hat (z. B. eine Teaser-Grafik der
 * Quellseite), aus `animal_images` und dem Beitragsbild entfernen.
 *
 * @package TSVD_Tools
 */

if (!defined('ABSPATH')) exit;

/**
 * Attachments über ein MySQL-REGEXP auf den Titel finden.
 *
 * @param string $pattern REGEXP für post_title.
 * @return int[]
 */
function tsvd_tools_ai_find_attachments_by_title_pattern($pattern) {
    global $wpdb;

    $ids = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_title REGEXP %s",
            $pattern
        )
    );

    return array_map('intval', $ids);
}

/**
 * Entfernt die Attachment-IDs aus Galerie und Beitragsbild eines Tieres.
 *
 * @param int   $animal_id      Tier-Post-ID.
 * @param int[] $attachment_ids Zu entfernende Attachment-IDs.
 * @param bool  $apply          false = nur melden, nichts schreiben.
 * @return array{gallery: ?array, thumbnail: ?array}
 */
function tsvd_tools_ai_strip_images_from_animal($animal_id, $attachment_ids, $apply) {
    $out    = array('gallery' => null, 'thumbnail' => null);
    $slug   = get_post_field('post_name', $animal_id);
    $images = get_post_meta($animal_id, 'animal_images', true);

    if (is_array($images)) {
        $images = array_map('intval', $images);
        $kept   = array_values(array_diff($images, $attachment_ids));
        if (count($kept) !== count($images)) {
            $out['gallery'] = array(
                'id'     => (int) $animal_id,
                'slug'   => $slug,
                'before' => count($images),
                'after'  => count($kept),
            );
            if ($apply && $kept) {
                update_post_meta($animal_id, 'animal_images', $kept);
            } elseif ($apply) {
                delete_post_meta($animal_id, 'animal_images');
            }
        }
    }

    $thumbnail_id = (int) get_post_thumbnail_id($animal_id);
    if ($thumbnail_id && in_array($thumbnail_id, $attachment_ids, true)) {
        $out['thumbnail'] = array(
            'id'            => (int) $animal_id,
            'slug'          => $slug,
            'attachment_id' => $thumbnail_id,
        );
        if ($apply) {
            delete_post_thumbnail($animal_id);
        }
    }

    return $out;
}

/**
 * Ability-Callback: Import-Artefakte aus Tier-Bildern entfernen.
 *
 * @param array $input Ability-Input.
 * @return array|WP_Error
 */
function tsvd_tools_ai_cleanup_animal_image_artifacts($input) {
    $pattern = isset($input['title_pattern']) ? trim((string) $input['title_pattern']) : '';
    if (strlen($pattern) < 8) {
        return new WP_Error(
            'tsvd_tools_pattern_too_broad',
            __('title_pattern muss mindestens 8 Zeichen lang sein, damit nicht die halbe Mediathek getroffen wird.', 'tsv-tools')
        );
    }

    $attachment_ids = tsvd_tools_ai_find_attachments_by_title_pattern($pattern);
    $apply          = !empty($input['apply']);
    $delete         = !empty($input['delete_attachments']);

    $result = array(
        'applied'            => $apply,
        'attachment_count'   => count($attachment_ids),
        'attachment_ids'     => $attachment_ids,
        'gallery_changed'    => array(),
        'thumbnail_cleared'  => array(),
        'attachments_deleted' => 0,
    );

    if (!$attachment_ids) {
        return $result;
    }

    $animals = get_posts(
        array(
            'post_type'      => 'animals',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        )
    );

    foreach ($animals as $animal_id) {
        $stripped = tsvd_tools_ai_strip_images_from_animal($animal_id, $attachment_ids, $apply);
        if ($stripped['gallery']) {
            $result['gallery_changed'][] = $stripped['gallery'];
        }
        if ($stripped['thumbnail']) {
            $result['thumbnail_cleared'][] = $stripped['thumbnail'];
        }
    }

    if ($apply && $delete) {
        foreach ($attachment_ids as $attachment_id) {
            if (wp_delete_attachment($attachment_id, true)) {
                $result['attachments_deleted']++;
            }
        }
    }

    return $result;
}
