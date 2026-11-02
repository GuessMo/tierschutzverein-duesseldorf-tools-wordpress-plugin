(() => {
	const selectNode = (root, nodeId) => {
		root.querySelectorAll('.tsvd-am-node').forEach((button) => {
			const isActive = button.dataset.node === nodeId;
			button.classList.toggle('is-active', isActive);
			button.setAttribute('aria-expanded', String(isActive));
		});
		root.querySelectorAll('.tsvd-am-panel').forEach((panel) => {
			panel.hidden = panel.dataset.panel !== nodeId;
		});
	};

	const markPath = (root, path) => {
		const nodeIds = Array.from(path.querySelectorAll('.tsvd-am-step'), (step) => step.dataset.node);
		root.querySelectorAll('.tsvd-am-node').forEach((button) => {
			button.classList.toggle('is-on-path', nodeIds.includes(button.dataset.node));
		});
	};

	const updateNav = (root, path, index, total) => {
		const label = root.querySelector('.tsvd-am-paths').dataset.stepLabel;
		path.querySelector('[data-step="previous"]').disabled = index === 0;
		path.querySelector('[data-step="next"]').disabled = index === total - 1;
		path.querySelector('.tsvd-am-path__count').textContent = label
			.replace('%1$d', String(index + 1))
			.replace('%2$d', String(total));
	};

	const showStep = (root, path, requested, shouldSelect) => {
		const steps = path.querySelectorAll('.tsvd-am-step');
		const index = Math.max(0, Math.min(requested, steps.length - 1));
		steps.forEach((step, position) => {
			step.classList.toggle('is-current', position === index);
			step.classList.toggle('is-done', position < index);
			step.toggleAttribute('aria-current', position === index);
		});
		path.dataset.index = String(index);
		updateNav(root, path, index, steps.length);
		if (shouldSelect) {
			markPath(root, path);
			selectNode(root, steps[index].dataset.node);
		}
	};

	const choosePath = (root, pathId, shouldSelect) => {
		root.querySelectorAll('[data-path]').forEach((button) => {
			button.setAttribute('aria-pressed', String(button.dataset.path === pathId));
		});
		root.querySelectorAll('.tsvd-am-path').forEach((path) => {
			path.hidden = path.dataset.pathPanel !== pathId;
		});
		const path = root.querySelector(`[data-path-panel="${pathId}"]`);
		showStep(root, path, 0, shouldSelect);
	};

	const moveStep = (root, button) => {
		const path = button.closest('.tsvd-am-path');
		const offset = button.dataset.step === 'next' ? 1 : -1;
		showStep(root, path, Number(path.dataset.index) + offset, true);
	};

	const handleClick = (root, event) => {
		const node = event.target.closest('.tsvd-am-node');
		if (node) {
			selectNode(root, node.dataset.node);
			return;
		}
		const picker = event.target.closest('[data-path]');
		if (picker) {
			choosePath(root, picker.dataset.path, true);
			return;
		}
		const stepButton = event.target.closest('[data-step]');
		if (stepButton) {
			moveStep(root, stepButton);
		}
	};

	const init = (root) => {
		root.classList.add('is-enhanced');
		root.addEventListener('click', (event) => handleClick(root, event));
		const firstNode = root.querySelector('.tsvd-am-node');
		const firstPath = root.querySelector('[data-path]');
		if (firstNode) {
			selectNode(root, firstNode.dataset.node);
		}
		if (firstPath) {
			choosePath(root, firstPath.dataset.path, false);
		}
	};

	document.querySelectorAll('[data-tsvd-am]').forEach(init);
})();
