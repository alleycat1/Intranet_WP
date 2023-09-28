(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Button
	blocks.registerBlockType(
		'trx-addons/button',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Buttons', "trx_addons" ),
			description: __( "Insert set of buttons", "trx_addons" ),
			icon: 'video-alt3',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					// Button attributes
					align: {
						type: 'string',
						default: 'none'
					},
					buttons: {
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
			), 'trx-addons/button' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'parent': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Button alignment
								{
									'name': 'align',
									'title': __( 'Button alignment', "trx_addons" ),
									'descr': __( "Select button alignment", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
								}
							], 'trx-addons/button', props ), props )
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
					props.attributes.buttons = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/button'
	) );

	// Register block Button Item
	blocks.registerBlockType(
		'trx-addons/button-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Single button', "trx_addons" ),
			description: __( "Insert single button", "trx_addons" ),
			icon: 'video-alt3',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/button'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				// Button attributes
				type: {
					type: 'string',
					default: 'default'
				},
				size: {
					type: 'string',
					default: 'normal'
				},
				link: {
					type: 'string',
					default: '#'
				},
				new_window: {
					type: 'boolean',
					default: false
				},
				title: {
					type: 'string',
					default: __( "Button", "trx_addons" )
				},
				subtitle: {
					type: 'string',
					default: ''
				},
				text_align: {
					type: 'string',
					default: 'none'
				},
				back_image: {
					type: 'number',
					default: 0
				},
				back_image_url: {
					type: 'string',
					default: ''
				},
				icon: {
					type: 'string',
					default: ''
				},
				icon_position: {
					type: 'string',
					default: 'left'
				},
				image: {
					type: 'number',
					default: 0
				},
				image_url: {
					type: 'string',
					default: ''
				},
				// ID, Class, CSS attributes
				item_id: {	// 'id' not work in Elementor
					type: 'string',
					default: ''
				},
				class: {
					type: 'string',
					default: ''
				},
				className: {
					type: 'string',
					default: ''
				},
				css: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/button-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Button', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select shortcodes's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_button'] )
								},
								// Size
								{
									'name': 'size',
									'title': __( 'Size', "trx_addons" ),
									'descr': __( "Size of the button", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_button_sizes'] )
								},
								// Button URL
								{
									'name': 'link',
									'title': __( 'Button URL', "trx_addons" ),
									'descr': __( "Link URL for the button", "trx_addons" ),
									'type': 'text'
								},
								// Open in the new tab
								{
									'name': 'new_window',
									'title': __( 'Open in the new tab', "trx_addons" ),
									'descr': __( "Open this link in the new browser's tab", "trx_addons" ),
									'type': 'boolean'
								},
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Title of the button", "trx_addons" ),
									'type': 'text'
								},
								// Subtitle
								{
									'name': 'subtitle',
									'title': __( 'Subtitle', "trx_addons" ),
									'descr': __( "Subtitle for the button", "trx_addons" ),
									'type': 'text'
								},
								// Text alignment
								{
									'name': 'text_align',
									'title': __( 'Text alignment', "trx_addons" ),
									'descr': __( "Select text alignment", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
								},
								// Button's background image
								{
									'name': 'back_image',
									'name_url': 'back_image_url',
									'title': __( "Button's background image", "trx_addons" ),
									'descr': __( "Select the image from the library for this button's background", "trx_addons" ),
									'type': 'image'
								},
								// Icon
								{
									'name': 'icon',
									'title': __( "Icon", "trx_addons" ),
									'descr': __( "Select icon from library", "trx_addons" ),
									'type': 'select',									
									'options': trx_addons_gutenberg_get_option_icons_classes()
								},
								// Icon position
								{
									'name': 'icon_position',
									'title': __( "Icon position", "trx_addons" ),
									'descr': __( "Place the icon (image) to the left or to the right or to the top side of the button", "trx_addons" ),
									'type': 'select',									
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_icon_positions'] )
								},
								// or select an image
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( "or select an image", "trx_addons" ),
									'descr': __( "Select the image instead the icon (if need)", "trx_addons" ),
									'type': 'image'
								}
							], 'trx-addons/button-item', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props, 'item_id' )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/button-item'
	) );

})( window.wp.blocks, window.wp.i18n, window.wp.element );
