(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Cart
	blocks.registerBlockType(
		'trx-addons/layouts-cart',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Cart', "trx_addons" ),
			description: __( 'Insert cart with items number and totals to the custom layout', "trx_addons" ),
			icon: 'cart',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					market: {
						type: 'string',
						default: 'woocommerce'
					},
					text: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_hide(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-cart' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_cart'] ),
								},
								// Market
								{
									'name': 'market',
									'title': __( 'Market', "trx_addons" ),
									'descr': __( "Select e-commerce plugin to show cart", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_cart_market'] ),
								},
								// Cart text
								{
									'name': 'text',
									'title': __( 'Cart text', "trx_addons" ),
									'descr': __( "Text before cart", "trx_addons" ),
									'type': 'text',
								}
							], 'trx-addons/layouts-cart', props ), props )
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
		'trx-addons/layouts-cart'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
