( function ( $ ) {
	'use strict';

	var uid = Date.now();

	function initRte( textarea ) {
		if ( ! textarea || ! window.wp || ! wp.editor || ! wp.editor.initialize ) {
			return;
		}
		wp.editor.initialize( textarea.id, {
			mediaButtons: false,
			quicktags: true,
			tinymce: {
				wpautop: true,
				toolbar1: 'formatselect,bold,italic,bullist,numlist,link,unlink,undo,redo'
			}
		} );
	}

	function removeRte( textarea ) {
		if ( textarea && window.wp && wp.editor && wp.editor.remove ) {
			wp.editor.remove( textarea.id );
		}
	}

	function initImage( scope ) {
		$( scope ).find( '.tsvd-nl-image' ).each( function () {
			var box = $( this );
			if ( box.data( 'bound' ) ) {
				return;
			}
			box.data( 'bound', true );

			box.on( 'click', '.tsvd-nl-image-select', function ( e ) {
				e.preventDefault();
				var frame = wp.media( { multiple: false, library: { type: 'image' } } );
				frame.on( 'select', function () {
					var att = frame.state().get( 'selection' ).first().toJSON();
					var size = ( att.sizes && att.sizes.medium ) ? att.sizes.medium.url : att.url;
					box.find( '.tsvd-nl-image-id' ).val( att.id );
					box.find( '.tsvd-nl-image-preview' ).html(
						'<img class="tsvd-nl-image-preview-img" src="' + size + '" alt="">'
					);
					box.find( '.tsvd-nl-image-clear' ).prop( 'hidden', false );
				} );
				frame.open();
			} );

			box.on( 'click', '.tsvd-nl-image-clear', function ( e ) {
				e.preventDefault();
				box.find( '.tsvd-nl-image-id' ).val( '' );
				box.find( '.tsvd-nl-image-preview' ).empty();
				$( this ).prop( 'hidden', true );
			} );
		} );
	}

	function addBlock( type ) {
		var tpl = $( '.tsvd-nl-tpl[data-type="' + type + '"]' );
		if ( ! tpl.length ) {
			return;
		}
		var html = tpl.html().replace( /\{\{INDEX\}\}/g, 'n' + ( uid++ ) );
		var node = $( html );
		$( '#tsvd-nl-blocks' ).append( node );
		initImage( node );
		node.find( '.tsvd-nl-rte' ).each( function () {
			initRte( this );
		} );
	}

	$( function () {
		var blocks = $( '#tsvd-nl-blocks' );
		if ( ! blocks.length ) {
			return;
		}

		if ( blocks.sortable ) {
			blocks.sortable( { handle: '.tsvd-nl-block-handle', items: '> .tsvd-nl-block', axis: 'y' } );
		}

		blocks.find( '.tsvd-nl-rte' ).each( function () {
			initRte( this );
		} );
		initImage( blocks );

		$( '.tsvd-nl-add' ).on( 'click', function () {
			var picker = $( '.tsvd-nl-picker' );
			var open = ! picker.prop( 'hidden' );
			picker.prop( 'hidden', open );
			$( this ).attr( 'aria-expanded', open ? 'false' : 'true' );
		} );

		$( '.tsvd-nl-picker' ).on( 'click', '.tsvd-nl-pick', function () {
			addBlock( $( this ).data( 'type' ) );
			$( '.tsvd-nl-picker' ).prop( 'hidden', true );
			$( '.tsvd-nl-add' ).attr( 'aria-expanded', 'false' );
		} );

		blocks.on( 'click', '.tsvd-nl-block-remove', function () {
			var block = $( this ).closest( '.tsvd-nl-block' );
			block.find( '.tsvd-nl-rte' ).each( function () {
				removeRte( this );
			} );
			block.remove();
		} );

		$( 'form#post' ).on( 'submit', function () {
			if ( window.tinymce ) {
				window.tinymce.triggerSave();
			}
		} );
	} );
} )( jQuery );
