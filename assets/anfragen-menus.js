( function () {
	var SELECTOR = 'details.tsvd-anf-more';

	function closeAll( except ) {
		document.querySelectorAll( SELECTOR + '[open]' ).forEach( function ( menu ) {
			if ( menu !== except ) {
				menu.removeAttribute( 'open' );
			}
		} );
	}

	function boundaryOf( menu ) {
		var frame = menu.closest( '.tsvd-msgr' );
		var rect = frame ? frame.getBoundingClientRect() : null;
		return {
			left: rect ? Math.max( rect.left, 0 ) : 0,
			right: rect ? Math.min( rect.right, window.innerWidth ) : window.innerWidth,
			bottom: window.innerHeight
		};
	}

	function place( details ) {
		var panel = details.querySelector( '.tsvd-anf-more__menu' );
		if ( ! panel ) {
			return;
		}
		details.classList.remove( 'is-align-end', 'is-align-start', 'is-drop-up' );
		var limit = boundaryOf( details );
		var rect = panel.getBoundingClientRect();
		if ( rect.right > limit.right ) {
			details.classList.add( 'is-align-end' );
		} else if ( rect.left < limit.left ) {
			details.classList.add( 'is-align-start' );
		}
		var trigger = details.querySelector( 'summary' ).getBoundingClientRect();
		if ( rect.bottom > limit.bottom && trigger.top > limit.bottom - trigger.bottom ) {
			details.classList.add( 'is-drop-up' );
		}
	}

	document.addEventListener( 'toggle', function ( event ) {
		if ( event.target.matches && event.target.matches( SELECTOR ) && event.target.open ) {
			place( event.target );
		}
	}, true );

	document.addEventListener( 'click', function ( event ) {
		closeAll( event.target.closest( SELECTOR ) );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' !== event.key ) {
			return;
		}
		var open = document.querySelector( SELECTOR + '[open]' );
		if ( ! open ) {
			return;
		}
		open.removeAttribute( 'open' );
		open.querySelector( 'summary' ).focus();
	} );
} )();
