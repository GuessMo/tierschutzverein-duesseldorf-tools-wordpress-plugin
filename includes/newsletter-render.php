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

	$head  = '<!DOCTYPE html><html lang="de" xmlns="http://www.w3.org/1999/xhtml">';
	$head .= '<head><meta charset="utf-8">';
	$head .= '<meta name="viewport" content="width=device-width, initial-scale=1">';
	$head .= '<meta name="color-scheme" content="light dark">';
	$head .= '<meta name="supported-color-schemes" content="light dark">';
	$head .= '<title>' . $title . '</title></head>';

	$body_open  = '<body style="margin:0;padding:0;background-color:#f5ede5;">';
	$body_open .= '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" '
		. 'style="background-color:#f5ede5;"><tr><td align="center" style="padding:24px 12px;">';
	$body_open .= '<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" '
		. 'style="width:600px;max-width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;">';
	$body_open .= '<tr><td style="padding:28px 28px 8px;font-family:Arial,Helvetica,sans-serif;'
		. 'font-size:16px;line-height:1.6;color:#3a2f28;">';

	$body_close  = '</td></tr></table>';
	$body_close .= '<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" '
		. 'style="width:600px;max-width:100%;"><tr><td style="padding:16px 28px;'
		. 'font-family:Arial,Helvetica,sans-serif;font-size:12px;line-height:1.5;color:#8a7a6d;text-align:center;">';
	$body_close .= esc_html__( 'Interner Newsletter – Tierschutzverein Düsseldorf und Umgebung e.V. 1873', 'tsvd' );
	$body_close .= '</td></tr></table>';
	$body_close .= '</td></tr></table></body></html>';

	return $head . $body_open . $inner_html . $body_close;
}
