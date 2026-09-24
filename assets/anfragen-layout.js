( function () {
	var MIN_HEIGHT = 480;
	var frame = document.querySelector( '.tsvd-msgr' );
	if ( ! frame ) {
		return;
	}

	function setHeight( px ) {
		frame.style.setProperty( '--tsvd-msgr-height', Math.max( MIN_HEIGHT, Math.round( px ) ) + 'px' );
	}

	function fit() {
		var top = frame.getBoundingClientRect().top + window.scrollY;
		setHeight( window.innerHeight - top );
		var overflow = document.documentElement.scrollHeight - window.innerHeight;
		if ( overflow > 0 ) {
			setHeight( frame.offsetHeight - overflow );
		}
	}

	fit();
	window.addEventListener( 'resize', fit );
	document.addEventListener( 'click', function ( event ) {
		if ( event.target.closest( '.notice-dismiss' ) ) {
			window.setTimeout( fit, 50 );
		}
	} );
} )();
