( function () {
	var KEY = 'tsvdAnfragenScroll';
	var AREAS = [
		{ selector: '.tsvd-anf-chips', axis: 'scrollLeft' },
		{ selector: '.tsvd-msgr__list', axis: 'scrollTop' }
	];

	function save() {
		var state = {};
		AREAS.forEach( function ( area ) {
			var el = document.querySelector( area.selector );
			if ( el ) {
				state[ area.selector ] = el[ area.axis ];
			}
		} );
		try {
			sessionStorage.setItem( KEY, JSON.stringify( state ) );
		} catch ( e ) {
			return;
		}
	}

	function readState() {
		try {
			var raw = sessionStorage.getItem( KEY );
			sessionStorage.removeItem( KEY );
			return raw ? JSON.parse( raw ) : null;
		} catch ( e ) {
			return null;
		}
	}

	function revealActiveChip() {
		var chip = document.querySelector( '.tsvd-anf-chips [aria-current="true"]' );
		if ( chip ) {
			chip.scrollIntoView( { block: 'nearest', inline: 'nearest' } );
		}
	}

	function restore() {
		var state = readState();
		if ( ! state ) {
			revealActiveChip();
			return;
		}
		AREAS.forEach( function ( area ) {
			var el = document.querySelector( area.selector );
			if ( el && 'number' === typeof state[ area.selector ] ) {
				el[ area.axis ] = state[ area.selector ];
			}
		} );
	}

	document.addEventListener( 'click', function ( event ) {
		if ( event.target.closest( '.tsvd-msgr a[href]' ) ) {
			save();
		}
	} );
	restore();
} )();
