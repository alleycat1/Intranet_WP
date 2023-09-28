(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Flickr photos
	blocks.registerBlockType(
		'trx-addons/flickr',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Flickr', "trx_addons" ),
			description: __( "Display the latest photos from Flickr account", "trx_addons" ),
			icon: 'format-gallery',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{				
					title: {
						type: 'string',
						default: __( 'Flickr photos', "trx_addons" )
					},
					flickr_api_key: {
						type: 'string',
						default: ''
					},
					flickr_username: {
						type: 'string',
						default: ''
					},
					flickr_count: {
						type: 'number',
						default: 8
					},
					flickr_columns: {
						type: 'number',
						default: 4
					},
					flickr_columns_gap: {
						type: 'number',
						default: 0
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/flickr' ),
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
								// Flickr API key
								{
									'name': 'flickr_api_key',
									'title': __( 'Flickr API key', "trx_addons" ),
									'descr': __( "Specify API key from your Flickr application", "trx_addons" ),
									'type': 'text',
								},
								// Flickr username
								{
									'name': 'flickr_username',
									'title': __( 'Flickr username', "trx_addons" ),
									'descr': __( "Your Flickr username", "trx_addons" ),
									'type': 'text',
								},
								// Number of photos
								{
									'name': 'flickr_count',
									'title': __( 'Number of photos', "trx_addons" ),
									'descr': __( "How many photos to be displayed?", "trx_addons" ),
									'type': 'number',
									'min': 1
								},
								// Columns
								{
									'name': 'flickr_columns',
									'title': __( 'Columns', "trx_addons" ),
									'descr': __( "Columns number", "trx_addons" ),
									'type': 'number',
									'min': 1
								},
								// Columns gap
								{
									'name': 'flickr_columns_gap',
									'title': __( 'Columns gap', "trx_addons" ),
									'descr': __( "Gap between images", "trx_addons" ),
									'type': 'number',
									'min': 0
								}
							], 'trx-addons/flickr', props ), props )
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
		'trx-addons/flickr'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
