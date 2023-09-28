(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Socials
	blocks.registerBlockType(
		'trx-addons/widget-socials',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Socials', "trx_addons" ),
			description: __( "Socials - show links to the profiles in your favorites social networks", "trx_addons" ),
			icon: 'facebook-alt',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					title: {
						type: 'string',
						default: ''
					},
					description: {
						type: 'string',
						default: ''
					},
					align: {
						type: 'string',
						default: 'left'
					},
					type: {
						type: 'string',
						default: 'socials'
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/widget-socials' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Widget title
								{
									'name': 'title',
									'title': __( 'Widget title', "trx_addons" ),
									'descr': __( "Title of the widget", "trx_addons" ),
									'type': 'text',
								},
								// Type
								{
									'name': 'type',
									'title': __( 'Icons type', "trx_addons" ),
									'descr': __( "Select type of icons: links to the social profiles or share links", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_socials_types'] )
								},
								// Align
								{
									'name': 'align',
									'title': __( 'Align', "trx_addons" ),
									'descr': __( "Select alignment of this item", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
								},
								// Description
								{
									'name': 'description',
									'title': __( 'Description', "trx_addons" ),
									'descr': __( "Short description about user", "trx_addons" ),
									'type': 'textarea',
								}
							], 'trx-addons/widget-socials', props ), props )
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
		'trx-addons/widget-socials'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
