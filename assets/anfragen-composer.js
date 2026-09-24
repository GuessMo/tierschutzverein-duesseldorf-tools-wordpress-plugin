( function () {
	var FLASH_KEY = 'tsvdComposerFlash';
	var composer = document.getElementById( 'tsvd-composer' );
	if ( ! composer || ! window.tsvdComposer ) {
		return;
	}
	var cfg = window.tsvdComposer;
	var btn = document.getElementById( 'tsvd-anfrage-reply-send' );
	var result = document.getElementById( 'tsvd-anfrage-reply-result' );
	var body = document.getElementById( 'tsvd-anfrage-reply-body' );
	var label = document.getElementById( 'tsvd-anfrage-label' );
	var hint = document.getElementById( 'tsvd-anfrage-hint' );
	var modes = composer.querySelectorAll( '.tsvd-composer__mode' );
	var mode = 'reply';

	function showResult( text, isError ) {
		result.textContent = text;
		result.classList.toggle( 'is-error', !! isError );
	}

	function setMode( next ) {
		mode = next;
		composer.setAttribute( 'data-mode', next );
		modes.forEach( function ( button ) {
			button.setAttribute( 'aria-pressed', button.getAttribute( 'data-mode' ) === next ? 'true' : 'false' );
		} );
		label.textContent = cfg.texts[ next ].label;
		hint.textContent = cfg.texts[ next ].hint;
		btn.textContent = cfg.texts[ next ].button;
	}

	function onSuccess( response ) {
		try {
			sessionStorage.setItem( FLASH_KEY, response.data && response.data.message ? response.data.message : '' );
		} catch ( e ) {
			showResult( response.data.message, false );
		}
		window.location.reload();
	}

	function onFailure( response ) {
		btn.disabled = false;
		var message = response && response.data && response.data.message ? response.data.message : cfg.error;
		showResult( message, true );
	}

	function send() {
		btn.disabled = true;
		showResult( cfg.sending, false );
		jQuery.post( ajaxurl, {
			action: 'tsvd_anfrage_reply',
			id: btn.getAttribute( 'data-id' ),
			nonce: btn.getAttribute( 'data-nonce' ),
			mode: mode,
			body: body.value
		}, function ( response ) {
			if ( response.success ) {
				onSuccess( response );
				return;
			}
			onFailure( response );
		} ).fail( function () {
			btn.disabled = false;
			showResult( cfg.server, true );
		} );
	}

	function restoreFlash() {
		var message = '';
		try {
			message = sessionStorage.getItem( FLASH_KEY );
			sessionStorage.removeItem( FLASH_KEY );
		} catch ( e ) {
			return;
		}
		if ( null === message ) {
			return;
		}
		showResult( message, false );
		composer.scrollIntoView( { block: 'end' } );
		body.focus();
	}

	modes.forEach( function ( button ) {
		button.addEventListener( 'click', function () {
			setMode( button.getAttribute( 'data-mode' ) );
		} );
	} );
	btn.addEventListener( 'click', send );
	body.addEventListener( 'keydown', function ( event ) {
		if ( 'Enter' === event.key && ( event.metaKey || event.ctrlKey ) && ! btn.disabled ) {
			event.preventDefault();
			send();
		}
	} );
	restoreFlash();
} )();
