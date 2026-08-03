<?php
/**
 * Automatically creates a Route301 redirect when an already-published
 * post/page's own slug changes, so the old URL doesn't silently 404. Reuses
 * tsvd_r301_request() (redirects-api.php) — created redirects show up in the
 * same Route301 list as manually-created ones (redirects.php admin page).
 *
 * Deliberately uses the CURRENT site's own domain (home_url()), NOT the
 * 'tierheim-duesseldorf.de' default every migrated redirect uses — those are
 * old-domain-to-new-site migration redirects, matched against the OLD
 * domain's Host header. A same-site slug change must redirect requests
 * arriving under the site's actual current domain
 * (tierschutzverein-duesseldorf.de), or it would silently never match.
 *
 * @package TSVD_Tools
 */

if (!defined('ABSPATH')) exit;

add_action('post_updated', 'tsvd_r301_auto_redirect_on_slug_change', 10, 3);

/**
 * @param int     $post_id     Post ID.
 * @param WP_Post $post_after  Post object reflecting the just-saved state.
 * @param WP_Post $post_before Post object as it was before this save.
 */
function tsvd_r301_auto_redirect_on_slug_change($post_id, $post_after, $post_before) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if ($post_before->post_name === $post_after->post_name) {
        return;
    }
    if ('publish' !== $post_before->post_status || 'publish' !== $post_after->post_status) {
        return; // only same-site slug changes on content that stayed live throughout
    }
    if (!tsvd_r301_configured()) {
        return;
    }

    $old_url = get_permalink($post_before);
    $new_url = get_permalink($post_after);
    if (!$old_url || !$new_url || $old_url === $new_url) {
        return;
    }

    $domain      = (string) wp_parse_url(home_url(), PHP_URL_HOST);
    $source_path = (string) wp_parse_url($old_url, PHP_URL_PATH);
    if ('' === $source_path) {
        return;
    }

    $existing = tsvd_r301_get_redirects($domain);
    if (tsvd_r301_find_match($domain, $source_path, $existing)) {
        return; // a redirect for this exact path already exists, don't duplicate
    }

    tsvd_r301_request('POST', '/api/redirects', array(
        'domain'     => $domain,
        'sourcePath' => $source_path,
        'target'     => $new_url,
        'statusCode' => 301,
        'enabled'    => true,
    ));
}
