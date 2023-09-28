(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;
	
	// Register Block - Search form
	blocks.registerBlockType(
		'trx-addons/layouts-search',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Search form', "trx_addons" ),
			description: __( 'Insert search form to the custom layout', "trx_addons" ),
			icon: 'search',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					style: {
						type: 'string',
						default: 'normal'
					},
					ajax: {
						type: 'boolean',
						default: false
					},
					post_types: {
						type: 'string',
						default: 'normal'
					}
				},
				trx_addons_gutenberg_get_param_hide(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-search' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_search'] ),
								},
								// Style
								{
									'name': 'style',
									'title': __( 'Style', "trx_addons" ),
									'descr': __( "Select form's style", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts_search'] ),
								},
								// AJAX search
								{
									'name': 'ajax',
									'title': __( 'AJAX search', "trx_addons" ),
									'descr': __( "Use incremental AJAX search", "trx_addons" ),
									'type': 'boolean',
								},
								// Post types
								{
									'name': 'post_types',
									'title': __( 'Post types', "trx_addons" ),
									'descr': __( "Select the types of posts you want to search", "trx_addons" ),
									'type': 'select',
									'multiple': true,
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['posts_types'] ),
								}
							], 'trx-addons/layouts-search', props ), props )
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
		'trx-addons/layouts-search'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );