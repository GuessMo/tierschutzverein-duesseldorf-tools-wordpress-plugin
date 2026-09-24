( function () {
	var MIN_HEIGHT = 480;
	var frame = document.querySelector( '.tsvd-msgr' );
	if ( ! frame ) {
		return;
	}

	function setHeight( px ) {
		frame.style.setProperty( '--tsvd-msgr-height', Math.max( MIN_HEIGHT, Math.round( px ) ) + 'px' );
	}

	function spaceBelow() {
		var content = document.getElementById( 'wpbody-content' );
		if ( ! content ) {
			return 0;
		}
		return content.getBoundingClientRect().bottom - frame.getBoundingClientRect().bottom;
	}

	function fit() {
		var top = frame.getBoundingClientRect().top + window.scrollY;
		setHeight( window.innerHeight - top - spaceBelow() );
	}

	fit();
	window.addEventListener( 'resize', fit );
	document.addEventListener( 'click', function ( event ) {
		if ( event.target.closest( '.notice-dismiss' ) ) {
			window.setTimeout( fit, 50 );
		}
	} );
} )();
