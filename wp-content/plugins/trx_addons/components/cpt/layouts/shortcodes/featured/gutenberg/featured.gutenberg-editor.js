(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;
	
	// Register Block - Featured image
	blocks.registerBlockType(
		'trx-addons/layouts-featured',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Featured image', "trx_addons" ),
			description: __( 'Insert featured with items number and totals to the custom layout', "trx_addons" ),
			icon: 'format-image',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					height: {
						type: 'string',
						default: ''
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
			), 'trx-addons/layouts-featured' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_featured'] ),
								},
								// Height of the block
								{
									'name': 'height',
									'title': __( 'Height of the block', "trx_addons" ),
									'descr': __( "Specify height of this block. If empty - use default height", "trx_addons" ),
									'type': 'text',
								},
								// Content alignment
								{
									'name': 'align',
									'title': __( 'Content alignment', "trx_addons" ),
									'descr': __( "Select alignment of the inner content in this block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] ),
								}
							], 'trx-addons/layouts-featured', props ), props )
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
		'trx-addons/layouts-featured'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );