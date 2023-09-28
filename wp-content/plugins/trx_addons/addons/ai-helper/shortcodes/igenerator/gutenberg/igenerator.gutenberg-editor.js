(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Image Generator
	blocks.registerBlockType(
		'trx-addons/igenerator',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'AI Helper Image Generator', "trx_addons" ),
			description: __( "AI Helper Image Generator form for frontend", "trx_addons" ),
			icon: 'images-alt2',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					prompt: {
						type: 'string',
						default: ''
					},
					show_prompt_translated: {
						type: 'boolean',
						default: true
					},
					prompt_width: {
						type: 'number',
						default: 100
					},
					button_text: {
						type: 'string',
						default: ''
					},
					align: {
						type: 'string',
						default: ''
					},
					tags_label: {
						type: 'string',
						default: __( 'Popular Tags', "trx_addons" )
					},
					tags: {
						type: 'string',
						default: ''
					},
					premium: {
						type: 'boolean',
						default: false
					},
					model: {
						type: 'string',
						default: 'openai/default'
					},
					show_settings: {
						type: 'boolean',
						default: false
					},
					show_settings_size: {
						type: 'boolean',
						default: false
					},
					show_limits: {
						type: 'boolean',
						default: false
					},
					show_download: {
						type: 'boolean',
						default: false
					},
					show_popup: {
						type: 'boolean',
						default: false
					},
					number: {
						type: 'number',
						default: 3
					},
					columns: {
						type: 'number',
						default: 3
					},
					size: {
						type: 'string',
						default: TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_default_image_size']
					},
					width: {
						type: 'number',
						default: 0
					},
					height: {
						type: 'number',
						default: 0
					},
					demo_thumb_size: {
						type: 'string',
						default: TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_demo_thumb_size']	//'trx_addons-thumb-avatar'
					},
					demo_images: {
						type: 'string',
						default: ''
					},
					demo_images_url: {
						type: 'string',
						default: ''
					},
					// Reload block - hidden option
					reload: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/igenerator' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'parent': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select shortcodes's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_igenerator'] )
								},
								// Default prompt
								{
									'name': 'prompt',
									'title': __( 'Default prompt', "trx_addons" ),
									'type': 'text'
								},
								// Show "Prompt translated"
								{
									'name': 'show_prompt_translated',
									'title': __( 'Show "Prompt translated"', "trx_addons" ),
									'descr': __( "Display the message 'Prompt is translated into English'", "trx_addons" ),
									'type': 'boolean'
								},
								// Prompt width
								{
									'name': 'prompt_width',
									'title': __( 'Prompt field width', "trx_addons" ),
									'descr': __( "Specify a width of the prompt field (in %)", "trx_addons" ),
									'type': 'number',
									'min': 50,
									'max': 100
								},
								// Align
								{
									'name': 'align',
									'title': __( 'Alignment', "trx_addons" ),
									'descr': __( "Alignment of the prompt field and tags", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_aligns'] )
								},
								// Button text
								{
									'name': 'button_text',
									'title': __( 'Caption for "Generate"', "trx_addons" ),
									'type': 'text'
								},
								// Tags label
								{
									'name': 'tags_label',
									'title': __( 'Tags label', "trx_addons" ),
									'type': 'text'
								},
								// Premium Mode
								{
									'name': 'premium',
									'title': __( 'Premium Mode', "trx_addons" ),
									'descr': __( "Enables you to set a broader range of limits for image generation, which can be used for a paid image generation service. The limits are configured in the global settings.", "trx_addons" ),
									'type': 'boolean'
								},
								// Model
								{
									'name': 'model',
									'title': __( 'Default model', "trx_addons" ),
									'descr': __( "Select a default model for generation images", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_models'] )
								},
								// Show button "Settings"
								{
									'name': 'show_settings',
									'title': __( 'Show button "Settings"', "trx_addons" ),
									'descr': __( "Show a button to open a model selector", "trx_addons" ),
									'type': 'boolean'
								},
								// Allow visitors to change size of generated images
								{
									'name': 'show_settings_size',
									'title': __( 'Image dimensions picker', "trx_addons" ),
									'descr': __( "Allow visitors to change size of generated images", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'show_settings': [true]
									}
								},
								// Show "Limits" info
								{
									'name': 'show_limits',
									'title': __( 'Show limits', "trx_addons" ),
									'descr': __( "Show a message with available limits for generation", "trx_addons" ),
									'type': 'boolean'
								},
								// Show button "Download"
								{
									'name': 'show_download',
									'title': __( 'Show button "Download"', "trx_addons" ),
									'descr': __( "Show a button Download after each generated image", "trx_addons" ),
									'type': 'boolean'
								},
								// Open in the popup
								{
									'name': 'show_popup',
									'title': __( 'Open images in the popup', "trx_addons" ),
									'descr': __( "Open generated images in the popup on click", "trx_addons" ),
									'type': 'boolean'
								},
								// Number
								{
									'name': 'number',
									'title': __( 'Generate at once', "trx_addons" ),
									'descr': __( "Specify the number of images to be generated at once (from 1 to 10)", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': 10
								},
								// Columns
								{
									'name': 'columns',
									'title': __( 'Columns', "trx_addons" ),
									'descr': __( "Specify the number of columns to show images (from 1 to 12)", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': 12
								},
								// Size
								{
									'name': 'size',
									'title': __( 'Image size', "trx_addons" ),
									'descr': __( "Select the size of generated images.", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_image_sizes'] )
								},
								// Width
								{
									'name': 'width',
									'title': __( 'Image width', "trx_addons" ),
									'descr': __( "Specify the image width for Stable Diffusion models only. If 0 or empty - a size from the field above will be used.", "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 1024,
									'step': 8,
									'dependency': {
										'model': TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_models_sd'],
										'size': ['custom']
									}
								},
								// Height
								{
									'name': 'height',
									'title': __( 'Image height', "trx_addons" ),
									'descr': __( "Specify the image height for Stable Diffusion models only. If 0 or empty - a size from the field above will be used.", "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 1024,
									'step': 8,
									'dependency': {
										'model': TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_models_sd'],
										'size': ['custom']
									}
								},
								// Demo Image
								{
									'name': 'demo_images',
									'name_url': 'demo_images_url',
									'title': __( 'Demo images', "trx_addons" ),
									'descr': __( "Selected images will be used instead of the image generator as a demo mode when limits are reached", "trx_addons" ),
									'type': 'image',
									'multiple': true
								},
								// Demo thumb size
								{
									'name': 'demo_thumb_size',
									'title': __( 'Thumb size', "trx_addons" ),
									'descr': __( "Select a thumb size to show images", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_igenerator_thumb_sizes'] )
								},
							], 'trx-addons/igenerator', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Title params
							trx_addons_gutenberg_add_param_title( props, true ),
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				// Get child block values of attributes
				if ( props.hasOwnProperty( 'innerBlocks' ) ) {	// && props.innerBlocks.length
					props.attributes.tags = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/igenerator'
	) );

	// Register block Tag Item
	blocks.registerBlockType(
		'trx-addons/igenerator-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Tag Item', "trx_addons" ),
			description: __( "Insert a tag for Image Generator", "trx_addons" ),
			icon: 'tag',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/igenerator'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				// Tag Item attributes
				title: {
					type: 'string',
					default: __( 'One', "trx_addons" )
				},
				prompt: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/igenerator-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Tag', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Enter title of the tag", "trx_addons" ),
									'type': 'text'
								},
								// Prompt
								{
									'name': 'prompt',
									'title': __( 'Prompt', "trx_addons" ),
									'descr': __( "Enter a prompt associated with a tag", "trx_addons" ),
									'type': 'text'
								}
							], 'trx-addons/igenerator-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/igenerator-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
