(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Images Compare
	blocks.registerBlockType(
		'trx-addons/icompare',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Images Compare', "trx_addons" ),
			description: __( "Insert images and compare states Before and After", "trx_addons" ),
			icon: 'format-gallery',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					image1: {
						type: 'number',
						default: 0
					},
					image1_url: {
						type: 'string',
						default: ''
					},
					image2: {
						type: 'number',
						default: 0
					},
					image2_url: {
						type: 'string',
						default: ''
					},
					direction: {
						type: 'string',
						default: 'vertical'
					},
					event: {
						type: 'string',
						default: 'drag'
					},
					handler: {
						type: 'string',
						default: 'round'
					},
					handler_separator: {
						type: 'boolean',
						default: true
					},
					handler_pos: {
						type: 'number',
						default: 50
					},
					icon: {
						type: 'string',
						default: ''
					},
					handler_image: {
						type: 'number',
						default: 0
					},
					handler_image_url: {
						type: 'string',
						default: ''
					},
					before_text: {
						type: 'string',
						default: ''
					},
					before_pos: {
						type: 'string',
						default: 'tl'
					},
					after_text: {
						type: 'string',
						default: ''
					},
					after_pos: {
						type: 'string',
						default: 'br'
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
			), 'trx-addons/icompare' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'parent': false,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_icompare'] ),
									'type': 'select'
								},
								// Image 1 (before)
								{
									'name': 'image1',
									'name_url': 'image1_url',
									'title': __( 'Image 1 (before)', "trx_addons" ),
									'type': 'image'
								},
								// Image 2 (after)
								{
									'name': 'image2',
									'name_url': 'image2_url',
									'title': __( 'Image 2 (after)', "trx_addons" ),
									'type': 'image'
								},
								// Direction
								{
									'name': 'direction',
									'title': __( 'Direction', "trx_addons" ),
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_icompare_directions'] ),
									'type': 'select'
								},
								// Event
								{
									'name': 'event',
									'title': __( 'Move on', "trx_addons" ),
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_icompare_events'] ),
									'type': 'select'
								},
								// Handler
								{
									'name': 'handler',
									'title': __( 'Handler style', "trx_addons" ),
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_icompare_handlers'] ),
									'type': 'select'
								},
								// Handler separator
								{
									'name': 'handler_separator',
									'title': __( 'Show separator', "trx_addons" ),
									'type': 'boolean',
								},
								// Handler pos
								{
									'name': 'handler_pos',
									'title': __( 'Handler position', "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 100,
									'step': 0.1
								},
								// Icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'options': trx_addons_gutenberg_get_option_icons_classes(),
									'type': 'select'
								},
								// Handler image
								{
									'name': 'handler_image',
									'name_url': 'handler_image_url',
									'title': __( 'Handler image', "trx_addons" ),
									'type': 'image'
								},
								// Before text
								{
									'name': 'before_text',
									'title': __( 'Text "Before"', "trx_addons" ),
									'type': 'text'
								},
								// Position "Before"
								{
									'name': 'before_pos',
									'title': __( 'Position "Before"', "trx_addons" ),
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_icompare_positions'] ),
									'type': 'select'
								},
								// After text
								{
									'name': 'after_text',
									'title': __( 'Text "After"', "trx_addons" ),
									'type': 'text'
								},
								// Position "After"
								{
									'name': 'after_pos',
									'title': __( 'Position "After"', "trx_addons" ),
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_icompare_positions'] ),
									'type': 'select'
								},
							], 'trx-addons/icompare', props ), props )
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
				return el( '', null );
			},
		},
		'trx-addons/icompare'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
