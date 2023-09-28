(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;
	
	// Register Block - Language
	blocks.registerBlockType(
		'trx-addons/layouts-language',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Language', "trx_addons" ),
			description: __( 'Insert WPML Language Selector', "trx_addons" ),
			icon: 'editor-bold',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					flag: {
						type: 'string',
						default: 'both'
					},
					title_link: {
						type: 'string',
						default: 'name'
					},
					title_menu: {
						type: 'string',
						default: 'name'
					}
				},
				trx_addons_gutenberg_get_param_hide(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-language' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select layout's type", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_language'] ),
								},
								// Show flag
								{
									'name': 'flag',
									'title': __( 'Show flag', "trx_addons" ),
									'descr': __( "Where do you want to show flag?", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts_language_positions'] ),
								},
								// Show link's title
								{
									'name': 'title_link',
									'title': __( "Show link's title", "trx_addons" ),
									'descr': __( "Select link's title type", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts_language_parts'] ),
								},
								// Show menu item's title
								{
									'name': 'title_menu',
									'title': __( "Show menu item's title", "trx_addons" ),
									'descr': __( "Select menu item's title type", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts_language_parts'] ),
								}
							], 'trx-addons/layouts-language', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Hide on devices params
							trx_addons_gutenberg_add_param_hide( props ),
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/layouts-language'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
