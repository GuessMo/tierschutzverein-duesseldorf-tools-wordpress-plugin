(() => {
	const ACTIVATION_OFFSET = 96;
	const BOTTOM_TOLERANCE = 2;
	const toc = document.querySelector('.tsvd-ac-toc');
	if (!toc) {
		return;
	}

	const entries = Array.from(toc.querySelectorAll('a[href^="#"]'))
		.map((link) => ({ link, heading: document.getElementById(decodeURIComponent(link.hash.slice(1))) }))
		.filter((entry) => entry.heading);
	if (!entries.length) {
		return;
	}

	const isAtBottom = () =>
		window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - BOTTOM_TOLERANCE;

	const activeIndex = () => {
		if (isAtBottom()) {
			return entries.length - 1;
		}
		return entries.reduce(
			(current, entry, index) =>
				entry.heading.getBoundingClientRect().top <= ACTIVATION_OFFSET ? index : current,
			0
		);
	};

	let isScheduled = false;

	const update = () => {
		isScheduled = false;
		const index = activeIndex();
		entries.forEach((entry, position) => {
			if (position === index) {
				entry.link.setAttribute('aria-current', 'location');
				return;
			}
			entry.link.removeAttribute('aria-current');
		});
	};

	const schedule = () => {
		if (isScheduled) {
			return;
		}
		isScheduled = true;
		window.requestAnimationFrame(update);
	};

	window.addEventListener('scroll', schedule, { passive: true });
	window.addEventListener('resize', schedule);
	update();
})();
