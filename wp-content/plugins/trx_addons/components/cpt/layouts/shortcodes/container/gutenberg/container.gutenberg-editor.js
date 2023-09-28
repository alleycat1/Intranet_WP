(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Container
	blocks.registerBlockType(
		'trx-addons/layouts-container',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Container', "trx_addons" ),
			description: __( 'Container for other blocks in layouts', "trx_addons" ),
			icon: 'schedule',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					align: {
						type: 'string',
						default: ''
					},
					content: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_hide(true),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-container' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'parent': true,
						'allowedblocks': TRX_ADDONS_STORAGE['gutenberg_allowed_blocks'],
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
								// Content alignment
								{
									'name': 'align',
									'title': __( 'Content alignment', "trx_addons" ),
									'descr': __( "Select alignment of the inner content in this block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( {
										'inherit': __( 'Inherit', "trx_addons" ),
										'left': __( 'Left', "trx_addons" ),
										'center': __( 'Center', "trx_addons" ),
										'right': __( 'Right', "trx_addons" ),
									} )
								}
							], 'trx-addons/layouts-container', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Hide on devices params
							trx_addons_gutenberg_add_param_hide( props, true ),
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			}
		},
		'trx-addons/layouts-container'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
