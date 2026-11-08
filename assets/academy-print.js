( function () {
	const button = document.querySelector( '.tsvd-ac-print' );
	const title = document.querySelector( '.tsvd-ac-article__title' );
	if ( ! button || ! title ) {
		return;
	}

	const documentTitle = document.title;
	let restoreTitle = false;

	button.addEventListener( 'click', function () {
		document.title = title.textContent.trim();
		restoreTitle = true;
		window.print();
	} );

	window.addEventListener( 'afterprint', function () {
		if ( restoreTitle ) {
			document.title = documentTitle;
			restoreTitle = false;
		}
	} );
} )();
