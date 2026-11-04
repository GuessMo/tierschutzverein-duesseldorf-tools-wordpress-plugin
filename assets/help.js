( function () {
	var pop = null;
	var counter = 0;
	var config = window.tsvdHelp || { label: 'Hilfe', targets: [] };

	function position( btn ) {
		var rect = btn.closest( '.tsvd-help-wrap' ).getBoundingClientRect();
		var width = pop.offsetWidth;
		var height = pop.offsetHeight;
		var viewport = document.documentElement;
		var left = Math.min( rect.left, viewport.clientWidth - width - 12 );
		var below = rect.bottom + 6;
		pop.style.left = Math.max( 10, left ) + 'px';
		pop.style.top = ( below + height > viewport.clientHeight ? rect.top - height - 6 : below ) + 'px';
	}

	function close() {
		if ( ! pop ) {
			return;
		}
		pop.hidden = true;
		var open = document.querySelector( '.tsvd-help-doc.is-open' );
		if ( open ) {
			open.classList.remove( 'is-open' );
			open.setAttribute( 'aria-expanded', 'false' );
		}
	}

	function toggle( btn ) {
		var wrap = btn.closest( '.tsvd-help-wrap' );
		var target = wrap.querySelector( '.tsvd-help-pop' );
		var wasOpen = ! target.hidden;
		close();
		if ( wasOpen ) {
			return;
		}
		pop = target;
		pop.textContent = btn.getAttribute( 'data-help' );
		pop.hidden = false;
		btn.classList.add( 'is-open' );
		btn.setAttribute( 'aria-expanded', 'true' );
		position( btn );
	}

	function buildButton( text ) {
		var popId = 'tsvd-help-js-' + ( ++counter );
		var wrap = document.createElement( 'span' );
		var btn = document.createElement( 'button' );
		var icon = document.createElement( 'span' );
		var popover = document.createElement( 'span' );
		wrap.className = 'tsvd-help-wrap';
		btn.type = 'button';
		btn.className = 'tsvd-help-doc';
		btn.setAttribute( 'data-help', text );
		btn.setAttribute( 'aria-label', config.label );
		btn.setAttribute( 'aria-expanded', 'false' );
		btn.setAttribute( 'aria-controls', popId );
		icon.className = 'dashicons dashicons-editor-help';
		icon.setAttribute( 'aria-hidden', 'true' );
		popover.className = 'tsvd-help-pop';
		popover.id = popId;
		popover.setAttribute( 'role', 'note' );
		popover.hidden = true;
		btn.appendChild( icon );
		wrap.appendChild( btn );
		wrap.appendChild( popover );
		return wrap;
	}

	function matchesLabel( element, label ) {
		if ( ! label ) {
			return true;
		}
		var own = Array.prototype.filter.call( element.childNodes, function ( node ) {
			return 3 === node.nodeType;
		} ).map( function ( node ) {
			return node.textContent;
		} ).join( '' ).trim();
		return own === label || element.textContent.trim() === label;
	}

	function isHeading( node ) {
		return node && /^(LEGEND|LABEL|STRONG|H3|H4|SPAN)$/.test( node.tagName )
			&& ! node.querySelector( 'input, select, textarea' ) && '' !== node.textContent.trim();
	}

	function headingBefore( field ) {
		var node = field;
		for ( var depth = 0; node && depth < 5; depth++ ) {
			for ( var sibling = node.previousElementSibling; sibling; sibling = sibling.previousElementSibling ) {
				if ( isHeading( sibling ) ) {
					return sibling;
				}
				var inner = 'P' === sibling.tagName ? sibling.querySelector( ':scope > strong' ) : null;
				if ( inner && isHeading( inner ) && ! sibling.querySelector( 'input, select, textarea' ) ) {
					return inner;
				}
			}
			node = node.parentElement;
			if ( node && 'FIELDSET' === node.tagName ) {
				return node.querySelector( 'legend' );
			}
		}
		return null;
	}

	function pickField( name ) {
		var scopes = [ '#post-body', '.edit-post-meta-boxes-area' ];
		var selector = scopes.map( function ( scope ) {
			return scope + ' [name="' + name + '"], ' + scope + ' [name="' + name + '[]"]';
		} ).join( ', ' );
		var fields = document.querySelectorAll( selector );
		var visible = Array.prototype.filter.call( fields, function ( field ) {
			return 'hidden' !== field.type;
		} );
		return { field: visible[ 0 ] || fields[ 0 ], isGroup: fields.length > 1 };
	}

	function fieldHost( name ) {
		var picked = pickField( name );
		var field = picked.field;
		if ( ! field ) {
			return null;
		}
		var isOption = picked.isGroup || 'radio' === field.type;
		var label = field.id ? document.querySelector( 'label[for="' + field.id + '"]' ) : null;
		if ( label && ! isOption ) {
			return label;
		}
		if ( 'checkbox' === field.type && ! isOption && field.closest( 'label' ) ) {
			return field.closest( 'label' );
		}
		var start = isOption && label ? label : field.closest( 'label' ) || field;
		return headingBefore( start );
	}

	function targetHosts( target ) {
		if ( target.field ) {
			var host = fieldHost( target.field );
			return host ? [ host ] : [];
		}
		return document.querySelectorAll( target.selector );
	}

	function inject() {
		config.targets.forEach( function ( target ) {
			Array.prototype.forEach.call( targetHosts( target ), function ( element ) {
				if ( element.querySelector( '.tsvd-help-wrap' ) || ! matchesLabel( element, target.label ) ) {
					return;
				}
				element.appendChild( buildButton( target.text ) );
			} );
		} );
	}

	document.addEventListener( 'click', function ( event ) {
		var btn = event.target.closest( '.tsvd-help-doc' );
		if ( btn ) {
			event.preventDefault();
			event.stopPropagation();
			toggle( btn );
			return;
		}
		if ( ! event.target.closest( '.tsvd-help-pop' ) ) {
			close();
		}
	}, true );

	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key ) {
			close();
		}
	} );

	window.addEventListener( 'resize', function () {
		if ( pop && ! pop.hidden ) {
			position( pop.closest( '.tsvd-help-wrap' ).querySelector( '.tsvd-help-doc' ) );
		}
	} );

	if ( config.targets.length ) {
		inject();
		new MutationObserver( inject ).observe( document.body, { childList: true, subtree: true } );
	}
}() );
