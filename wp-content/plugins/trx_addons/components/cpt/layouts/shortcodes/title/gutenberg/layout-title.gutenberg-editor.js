(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;
	
	// Register Block - Title and Breadcrumbs
	blocks.registerBlockType(
		'trx-addons/layouts-title',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Title and Breadcrumbs', "trx_addons" ),
			description: __( 'Insert post meta and/or title and/or breadcrumbs', "trx_addons" ),
			icon: 'editor-textcolor',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					image: {
						type: 'number',
						default: 0
					},
					image_url: {
						type: 'string',
						default: ''
					},
					use_featured_image: {
						type: 'boolean',
						default: false
					},
					height: {
						type: 'string',
						default: ''
					},
					align: {
						type: 'string',
						default: ''
					},
					meta: {
						type: 'boolean',
						default: false
					},
					title: {
						type: 'boolean',
						default: false
					},
					breadcrumbs: {
						type: 'boolean',
						default: false
					}
				},
				trx_addons_gutenberg_get_param_hide(true),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-title' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_layouts_title'] ),
								},
								// Alignment
								{
									'name': 'align',
									'title': __( 'Alignment', "trx_addons" ),
									'descr': __( "Select alignment of the inner content in this block", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] ),
								},
								// Show post title
								{
									'name': 'title',
									'title': __( 'Show post title', "trx_addons" ),
									'descr': __( "Show post/page title", "trx_addons" ),
									'type': 'boolean',
								},
								// Show post meta
								{
									'name': 'meta',
									'title': __( 'Show post meta', "trx_addons" ),
									'descr': __( "Show post meta: date, author, categories list, etc.", "trx_addons" ),
									'type': 'boolean',
								},
								// Show breadcrumbs
								{
									'name': 'breadcrumbs',
									'title': __( 'Show breadcrumbs', "trx_addons" ),
									'descr': __( "Show breadcrumbs under the title", "trx_addons" ),
									'type': 'boolean',
								},
								// Background image
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'Background image', "trx_addons" ),
									'descr': __( "Background image of the block", "trx_addons" ),
									'type': 'image',
								},
								// Post featured image
								{
									'name': 'use_featured_image',
									'title': __( 'Post featured image', "trx_addons" ),
									'descr': __( "Use post's featured image as background of the block instead image above (if present)", "trx_addons" ),
									'type': 'boolean',
								},
								// Height of the block
								{
									'name': 'height',
									'title': __( 'Height of the block', "trx_addons" ),
									'descr': __( "Specify height of this block. If empty - use default height", "trx_addons" ),
									'type': 'text',
								}
							], 'trx-addons/layouts-title', props ), props )
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
				return el( '', null );
			}
		},
		'trx-addons/layouts-title'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
