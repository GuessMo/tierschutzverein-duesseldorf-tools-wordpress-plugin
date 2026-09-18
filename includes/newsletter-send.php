<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TSVD_NEWSLETTER_FROM_EMAIL = 'newsletter.intern@tierschutzverein-duesseldorf.de';
const TSVD_NEWSLETTER_FROM_NAME  = 'Tierschutzverein Düsseldorf – Interner Newsletter';

function tsvd_newsletter_from_email_override() {
	return TSVD_NEWSLETTER_FROM_EMAIL;
}

function tsvd_newsletter_from_name_override() {
	return TSVD_NEWSLETTER_FROM_NAME;
}

function tsvd_newsletter_content_type_html() {
	return 'text/html';
}

function tsvd_newsletter_send_html( array $recipient_emails, $subject, $html ) {
	$recipient_emails = array_values( array_unique( array_filter( array_map( 'sanitize_email', $recipient_emails ) ) ) );

	$result = array(
		'sent'   => 0,
		'failed' => array(),
		'total'  => count( $recipient_emails ),
	);

	if ( empty( $recipient_emails ) || '' === trim( (string) $subject ) ) {
		return $result;
	}

	add_filter( 'wp_mail_from', 'tsvd_newsletter_from_email_override', 99 );
	add_filter( 'wp_mail_from_name', 'tsvd_newsletter_from_name_override', 99 );
	add_filter( 'wp_mail_content_type', 'tsvd_newsletter_content_type_html', 99 );

	$body = tsvd_newsletter_wrap_html( $subject, $html );

	foreach ( $recipient_emails as $email ) {
		if ( wp_mail( $email, $subject, $body ) ) {
			$result['sent']++;
		} else {
			$result['failed'][] = $email;
		}
	}

	remove_filter( 'wp_mail_from', 'tsvd_newsletter_from_email_override', 99 );
	remove_filter( 'wp_mail_from_name', 'tsvd_newsletter_from_name_override', 99 );
	remove_filter( 'wp_mail_content_type', 'tsvd_newsletter_content_type_html', 99 );

	return $result;
}

function tsvd_newsletter_wrap_html( $subject, $html ) {
	$title = esc_html( $subject );

	$open  = '<!DOCTYPE html><html lang="de"><head><meta charset="utf-8">';
	$open .= '<meta name="viewport" content="width=device-width, initial-scale=1"><title>' . $title . '</title></head>';
	$open .= '<body style="margin:0;padding:0;background:#f5ede5;">';
	$open .= '<div style="max-width:640px;margin:0 auto;padding:24px;font-family:Arial,Helvetica,sans-serif;';
	$open .= 'color:#3a2f28;line-height:1.6;">';

	$close = '</div></body></html>';

	return $open . $html . $close;
}
