(function () {
	var config = window.tsvdToolPortalMenu;
	if (!config || !config.urls) {
		return;
	}
	config.urls.forEach(function (url) {
		var href = url.replace(/\/$/, '');
		document.querySelectorAll('#adminmenu a[href="' + href + '"]').forEach(function (link) {
			link.target = '_blank';
			link.rel = 'noopener';
		});
	});
})();
