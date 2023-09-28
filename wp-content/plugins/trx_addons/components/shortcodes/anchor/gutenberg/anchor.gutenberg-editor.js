(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Anchor
	blocks.registerBlockType(
		'trx-addons/anchor',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Anchor', "trx_addons" ),
			description: __( "Insert anchor for the inner page navigation", "trx_addons" ),
			icon: 'sticky',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				id: {
					type: 'string',
					default: ''
				},
				title: {
					type: 'string',
					default: __( 'Anchor', "trx_addons" )
				},
				url: {
					type: 'string',
					default: ''
				},
				icon: {
					type: 'string',
					default: ''
				},
				className: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/anchor' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'general_params': el(
							'div', {}, trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Anchor ID
								{
									'name': 'id',
									'title': __( 'Anchor ID', "trx_addons" ),
									'descr': __( "ID of this anchor", "trx_addons" ),
									'type': 'text',
								},
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Anchor title", "trx_addons" ),
									'type': 'text',
								},
								// URL to navigate
								{
									'name': 'url',
									'title': __( 'URL to navigate', "trx_addons" ),
									'descr': __( "URL to navigate. If empty - use id to create anchor", "trx_addons" ),
									'type': 'text',
								},
								// Icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'descr': __( "Select icon from library", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes()
								}
							], 'trx-addons/anchor', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			},
		},
		'trx-addons/anchor'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
