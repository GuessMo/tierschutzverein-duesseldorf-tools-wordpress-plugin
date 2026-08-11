( function () {
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
            var x = a.cells[ index ] ? ( a.cells[ index ].getAttribute( 'data-sortv' ) || a.cells[ index ].textContent.trim() ) : '';
            var y = b.cells[ index ] ? ( b.cells[ index ].getAttribute( 'data-sortv' ) || b.cells[ index ].textContent.trim() ) : '';
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
        var th = e.target.closest( 'th.r301-sortable' );
        if ( th ) { sortTable( th ); }
    } );

    document.addEventListener( 'DOMContentLoaded', function () {
        if ( document.querySelector( 'th.sorted' ) ) { return; }
        Array.prototype.forEach.call( document.querySelectorAll( 'table' ), function ( table ) {
            if ( ! table.querySelector( 'th.r301-sortable' ) ) { return; }
            var hitsTh = null;
            Array.prototype.forEach.call( table.querySelectorAll( 'th.r301-sortable' ), function ( th ) {
                var text = th.textContent.trim();
                if ( text === 'Hits' || /Hits$/.test( text ) ) { hitsTh = th; }
            } );
            if ( hitsTh ) {
                hitsTh.setAttribute( 'data-dir', 'asc' );
                sortTable( hitsTh );
            }
        } );
    } );
}() );
