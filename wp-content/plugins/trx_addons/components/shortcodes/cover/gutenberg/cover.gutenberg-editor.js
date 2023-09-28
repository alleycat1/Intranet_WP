(function(blocks, i18n, element) {
	
	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Cover
	blocks.registerBlockType(
		'trx-addons/cover',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Cover link', "trx_addons" ),
			description: __( "Add a cover link", "trx_addons" ),
			icon: 'external',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					place: {
						type: 'string',
						default: 'row'
					},
					url: {
						type: 'string',
						default: ''
					},
					// Reload block - hidden option
					reload: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/cover' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select shortcodes's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_cover'] )
								},
								// Place
								{
									'name': 'place',
									'title': __( 'Place', "trx_addons" ),
									'descr': __( "Which object should the link overlap?", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_cover_places'] )
								},
								// URL to navigate
								{
									'name': 'url',
									'title': __( 'URL', "trx_addons" ),
									'descr': __( "URL to navigate", "trx_addons" ),
									'type': 'text',
								}
							], 'trx-addons/cover', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			},
		},
		'trx-addons/cover'
	) );

})( window.wp.blocks, window.wp.i18n, window.wp.element );
