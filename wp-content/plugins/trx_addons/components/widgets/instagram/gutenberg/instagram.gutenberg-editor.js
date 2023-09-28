(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Instagram feed
	blocks.registerBlockType(
		'trx-addons/instagram',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Instagram', "trx_addons" ),
			description: __( "Display the latest photos from instagram account by hashtag", "trx_addons" ),
			icon: 'images-alt2',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{				
					title: {
						type: 'string',
						default: __( 'Instagram feed', "trx_addons" )
					},
					type: {
						type: 'string',
						default: 'default'
					},
					demo: {
						type: 'boolean',
						default: false
					},
					demo_thumb_size: {
						type: 'string',
						default: TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_instagram_demo_thumb_size']	//'trx_addons-thumb-avatar'
					},
					demo_files: {
						type: 'string',
						default: ''
					},
					count: {
						type: 'number',
						default: 8
					},
					columns: {
						type: 'number',
						default: 4
					},
					columns_gap: {
						type: 'number',
						default: 0
					},
					hashtag: {
						type: 'string',
						default: ''
					},
					links: {
						type: 'string',
						default: 'instagram'
					},
					ratio: {
						type: 'string',
						default: 'none'
					},
					follow: {
						type: 'boolean',
						default: false
					},
					follow_link: {
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
			), 'trx-addons/instagram' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'parent': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Widget title
								{
									'name': 'title',
									'title': __( 'Widget title', "trx_addons" ),
									'descr': __( "Title of the widget", "trx_addons" ),
									'type': 'text',
								},
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select shortcodes's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_instagram'] )
								},
								// Demo mode
								{
									'name': 'demo',
									'title': __( 'Demo mode', "trx_addons" ),
									'descr': __( 'Show demo images', "trx_addons" ),
									'type': 'boolean',
								},
								// Demo thumb size
								{
									'name': 'demo_thumb_size',
									'title': __( 'Thumb size', "trx_addons" ),
									'descr': __( "Select a thumb size to show images", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_instagram_thumb_sizes'] ),
									'dependency': {
										'demo': [true]
									}
								},
								// Hashtag
								{
									'name': 'hashtag',
									'title': __( 'Hashtag or Username', "trx_addons" ),
									'descr': __( "Hashtag (start with #) or Username to filter your photos", "trx_addons" ),
									'dependency': {
										'demo': [false]
									},
									'type': 'text',
								},
								// Number of photos
								{
									'name': 'count',
									'title': __( 'Number of photos', "trx_addons" ),
									'descr': __( "How many photos to be displayed?", "trx_addons" ),
									'dependency': {
										'demo': [false]
									},
									'type': 'number',
									'min': 1
								},
								// Columns
								{
									'name': 'columns',
									'title': __( 'Columns', "trx_addons" ),
									'descr': __( "Columns number", "trx_addons" ),
									'type': 'number',
									'min': 1
								},
								// Columns gap
								{
									'name': 'columns_gap',
									'title': __( 'Columns gap', "trx_addons" ),
									'descr': __( "Gap between images", "trx_addons" ),
									'type': 'number',
									'min': 0
								},
								// Link images to
								{
									'name': 'links',
									'title': __( 'Link images to', "trx_addons" ),
									'descr': __( "Where to send a visitor after clicking on the picture", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_instagram_redirects'] )
								},
								// Image ratio
								{
									'name': 'ratio',
									'title': __( 'Image ratio', "trx_addons" ),
									'descr': __( "Select a ratio to show images. Default leave original ratio for each image", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_instagram_image_ratio'] )
								},
								// Show button "Follow me"
								{
									'name': 'follow',
									'title': __( 'Show button "Follow me"', "trx_addons" ),
									'descr': __( 'Add button "Follow me" after images', "trx_addons" ),
									'type': 'boolean',
								},
								// Foolow link
								{
									'name': 'follow_link',
									'title': __( 'Follow link', "trx_addons" ),
									'descr': __( "URL for the Follow link", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'follow': [true]
									}
								}
							], 'trx-addons/instagram', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				// Get child block values of attributes
				if ( props.hasOwnProperty( 'innerBlocks' ) ) {	// && props.innerBlocks.length
					props.attributes.demo_files = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			}
		},
		'trx-addons/instagram'
	) );

	// Register block Instagram Item
	blocks.registerBlockType(
		'trx-addons/instagram-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Instagram demo image', "trx_addons" ),
			description: __( "Insert an image or a video for demo mode", "trx_addons" ),
			icon: 'images-alt',
			category: 'trx-addons-widgets',
			parent: ['trx-addons/instagram'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				// Media attributes
				image: {
					type: 'number',
					default: 0
				},
				image_url: {
					type: 'string',
					default: ''
				},
				video: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/instagram-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Demo media item', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Image
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'Image', "trx_addons" ),
									'descr': __( "Select or upload image or specify URL from other site to use it as icon", "trx_addons" ),
									'type': 'image'
								},
								// Video URL
								{
									'name': 'video',
									'title': __( 'Video URL', "trx_addons" ),
									'descr': __( "Enter link to the video (Note: read more about available formats at WordPress Codex page)", "trx_addons" ),
									'type': 'text'
								}
							], 'trx-addons/instagram-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/instagram-item'
	) );

})( window.wp.blocks, window.wp.i18n, window.wp.element );
