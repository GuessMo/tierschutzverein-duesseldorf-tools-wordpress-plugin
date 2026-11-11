(function () {
	var config = window.tsvdToolPortalMenu;
	if (!config || !config.base) {
		return;
	}
	document.querySelectorAll('#adminmenu a[href^="' + config.base + '"]').forEach(function (link) {
		link.target = '_blank';
		link.rel = 'noopener';
	});
})();
