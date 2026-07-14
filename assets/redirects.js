( function () {
    function byId( id ) { return document.getElementById( id ); }

    function fillForm( d ) {
        byId( 'r301-id' ).value = d.id || '';
        byId( 'r301-domain' ).value = d.domain || 'tierheim-duesseldorf.de';
        byId( 'r301-source' ).value = d.source || '';
        byId( 'r301-target' ).value = d.target || '';
        byId( 'r301-status' ).value = d.status || '301';
        byId( 'r301-enabled' ).checked = d.enabled === '1' || d.enabled === true;
        var title = byId( 'r301-form-title' );
        if ( title ) { title.scrollIntoView( { behavior: 'smooth' } ); }
    }

    function sortTable( th ) {
        var table = th.closest( 'table' );
        var tbody = table.tBodies[ 0 ];
        if ( ! tbody ) { return; }
        var index = Array.prototype.indexOf.call( th.parentNode.children, th );
        var numeric = th.getAttribute( 'data-sort' ) === 'num';
        var asc = th.getAttribute( 'data-dir' ) !== 'asc';

        Array.prototype.forEach.call( th.parentNode.children, function ( c ) {
            c.removeAttribute( 'data-dir' );
            c.classList.remove( 'sorted', 'asc', 'desc' );
        } );
        th.setAttribute( 'data-dir', asc ? 'asc' : 'desc' );
        th.classList.add( 'sorted', asc ? 'asc' : 'desc' );

        var rows = Array.prototype.slice.call( tbody.rows );
        rows.sort( function ( a, b ) {
            var x = a.cells[ index ] ? a.cells[ index ].textContent.trim() : '';
            var y = b.cells[ index ] ? b.cells[ index ].textContent.trim() : '';
            if ( numeric ) {
                x = parseFloat( x ) || 0;
                y = parseFloat( y ) || 0;
                return asc ? x - y : y - x;
            }
            return asc ? x.localeCompare( y, 'de' ) : y.localeCompare( x, 'de' );
        } );
        rows.forEach( function ( row ) { tbody.appendChild( row ); } );
    }

    document.addEventListener( 'click', function ( e ) {
        var edit = e.target.closest( '.r301-edit' );
        if ( edit ) { e.preventDefault(); fillForm( edit.dataset ); return; }

        var reset = e.target.closest( '.r301-reset' );
        if ( reset ) {
            e.preventDefault();
            fillForm( { status: '301', enabled: true } );
            byId( 'r301-id' ).value = '';
            return;
        }

        var th = e.target.closest( 'th.r301-sortable' );
        if ( th ) { sortTable( th ); }
    } );
}() );
