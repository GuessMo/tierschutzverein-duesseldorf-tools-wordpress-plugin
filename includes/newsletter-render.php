<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tsvd_newsletter_render_email( $post_id ) {
	$post    = get_post( $post_id );
	$subject = $post ? $post->post_title : '';
	$blocks  = tsvd_newsletter_render_blocks( $post_id );

	return tsvd_newsletter_email_document( $subject, $blocks );
}

function tsvd_newsletter_email_document( $subject, $inner_html ) {
	$title = esc_html( $subject );

	$site      = get_bloginfo( 'name' );
	$logo_id   = get_theme_mod( 'custom_logo' );
	$logo_url  = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';

	if ( $logo_url ) {
		$logo_html = '<img src="' . esc_url( $logo_url ) . '" alt="' . esc_attr( $site )
			. '" style="max-height:72px;width:auto;display:inline-block;">';
	} else {
		$logo_html = '<img src="' . esc_url( TSVD_TOOLS_URL . 'assets/newsletter-logo.png' )
			. '" alt="' . esc_attr( $site )
			. '" width="72" height="72" style="width:72px;height:72px;max-width:72px;display:inline-block;">';
	}

	$head  = '<!DOCTYPE html><html lang="de" xmlns="http://www.w3.org/1999/xhtml">';
	$head .= '<head><meta charset="utf-8">';
	$head .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
	$head .= '<meta name="color-scheme" content="light dark">';
	$head .= '<meta name="supported-color-schemes" content="light dark">';
	$head .= '<title>' . $title . '</title>';
	$head .= '<style>@media (prefers-color-scheme: dark){'
		. '.tsvd-mail-bg{background-color:#1d1e20 !important;}'
		. '.tsvd-mail-card,.tsvd-mail-header{background-color:#26272b !important;}'
		. '.tsvd-mail-header{border-bottom-color:#3c3f43 !important;}'
		. '.tsvd-mail-content,.tsvd-mail-content *{color:#e3e5e8 !important;}'
		. '.tsvd-mail-content a{color:#8aa8ff !important;}'
		. '.tsvd-mail-footer{color:#9a9ea6 !important;}'
		. '}</style>';
	$head .= '</head>';

	$body_open  = '<body class="tsvd-mail-bg" style="margin:0;padding:0;background-color:#f5ede5;">';
	$body_open .= '<table role="presentation" class="tsvd-mail-bg" width="100%" cellpadding="0" cellspacing="0" border="0" '
		. 'style="background-color:#f5ede5;"><tr><td align="center" style="padding:24px 12px;">';
	$body_open .= '<table role="presentation" class="tsvd-mail-card" width="600" cellpadding="0" cellspacing="0" border="0" '
		. 'style="width:600px;max-width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;">';
	$body_open .= '<tr><td align="center" class="tsvd-mail-header" style="padding:24px 28px 16px;text-align:center;'
		. 'background-color:#ffffff;border-bottom:1px solid #ebdfd4;">' . $logo_html . '</td></tr>';
	$body_open .= '<tr><td class="tsvd-mail-content" style="padding:24px 28px 8px;font-family:Arial,Helvetica,sans-serif;'
		. 'font-size:16px;line-height:1.6;color:#3a2f28;">';

	$body_close  = '</td></tr></table>';
	$body_close .= '<table role="presentation" class="tsvd-mail-bg" width="600" cellpadding="0" cellspacing="0" border="0" '
		. 'style="width:600px;max-width:100%;"><tr><td class="tsvd-mail-footer" style="padding:16px 28px;'
		. 'font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.5;color:#8a7a6d;text-align:center;">';
	$body_close .= esc_html__( 'Interner Newsletter – Tierschutzverein Düsseldorf und Umgebung e.V. 1873', 'tsvd' );
	$body_close .= '</td></tr></table>';
	$body_close .= '</td></tr></table></body></html>';

	return $head . $body_open . $inner_html . $body_close;
}
