(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;
	
	// Register Block - Iconed text
	blocks.registerBlockType(
		'trx-addons/layouts-iconed-text',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Iconed text', "trx_addons" ),
			description: __( 'Insert icon with two text lines to the custom layout', "trx_addons" ),
			icon: 'phone',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					icon: {
						type: 'string',
						default: 'icon-phone'
					},
					text1: {
						type: 'string',
						default: __( 'Line 1', "trx_addons" )
					},
					text2: {
						type: 'string',
						default: __( 'Line 2', "trx_addons" )
					},
					link: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_hide(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-iconed-text' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_iconed_text'] ),
								},
								// Icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes()
								},
								// Text line 1
								{
									'name': 'text1',
									'title': __( 'Text line 1', "trx_addons" ),
									'descr': __( "Text in the first line.", "trx_addons" ),
									'type': 'text',
								},
								// Text line 2
								{
									'name': 'text2',
									'title': __( 'Text line 2', "trx_addons" ),
									'descr': __( "Text in the second line.", "trx_addons" ),
									'type': 'text',
								},
								// Link URL
								{
									'name': 'link',
									'title': __( 'Link URL', "trx_addons" ),
									'descr': __( "Specify link URL. If empty - show plain text without link", "trx_addons" ),
									'type': 'text',
								}
							], 'trx-addons/layouts-iconed-text', props ), props )
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
		'trx-addons/layouts-iconed-text'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
