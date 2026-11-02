(({ blocks, element, blockEditor, serverSideRender: ServerSideRender }) => {
	const BLOCK_NAME = 'tsvd/architecture-map';

	const Edit = () =>
		element.createElement(
			'div',
			blockEditor.useBlockProps(),
			element.createElement(ServerSideRender, { block: BLOCK_NAME })
		);

	blocks.registerBlockType(BLOCK_NAME, {
		apiVersion: 3,
		title: 'Aufbau-Plan',
		description: 'Klickbare Übersicht, wie Website, Server und Dienste zusammenhängen.',
		icon: 'networking',
		category: 'widgets',
		supports: { html: false, multiple: false },
		edit: Edit,
		save: () => null,
	});
})(window.wp);
