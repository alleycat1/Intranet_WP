(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;
	
	// Register Block - Single Post Meta
	blocks.registerBlockType(
		'trx-addons/layouts-meta',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Single Post Meta', "trx_addons" ),
			description: __( 'Add post meta', "trx_addons" ),
			icon: 'info',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					components: {
						type: 'string',
						default: 'date'
					},
					share_type: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-meta' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_meta'] ),
								},
								// Choose components
								{
									'name': 'components',
									'name_arr': 'components_arr',
									'title': __( 'Choose components', "trx_addons" ),
									'descr': __( "Display specified post meta elements", "trx_addons" ),
									'type': 'select',
									'multiple': true,
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_meta_components'] ),
								},
								// Share type
								{
									'name': 'share_type',
									'title': __( 'Share_type', "trx_addons" ),
									'descr': __( "Display share links", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_share_types'] ),
								}
							], 'trx-addons/layouts-meta', props ), props )
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
			}
		},
		'trx-addons/layouts-meta'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );