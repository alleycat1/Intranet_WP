(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Super Title
	blocks.registerBlockType(
		'trx-addons/supertitle',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Super Title', "trx_addons" ),
			description: __( "Insert 'Super Title' element", "trx_addons" ),
			icon: 'editor-bold',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					icon_column: {
						type: 'number',
						default: 1
					},
					header_column: {
						type: 'number',
						default: 8
					},
					image: {
						type: 'number',
						default: 0
					},
					icon: {
						type: 'string',
						default: ''
					},
					icon_color: {
						type: 'string',
						default: ''
					},
					icon_bg_color: {
						type: 'string',
						default: ''
					},
					icon_size: {
						type: 'string',
						default: ''
					},
					image: {
						type: 'number',
						default: 0
					},
					image_url: {
						type: 'string',
						default: ''
					},
					items: {
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
			), 'trx-addons/supertitle' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_supertitle'] )
								},
								// Icon column size
								{
									'name': 'icon_column',
									'title': __( 'Icon column size', "trx_addons" ),
									'descr': __( "Specify the width of the icon (left) column from 0 (no left column) to 6.", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': 6
								},
								// Left column size
								{
									'name': 'header_column',
									'title': __( 'Left column size', "trx_addons" ),
									'descr': __( "Specify the width of the main (middle) column from 0 (no middle column) to 12. Attention! The sum of values for the two columns (Icon and Main) must not exceed 12.", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': 12
								},
								// Choose media
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'Choose media', "trx_addons" ),
									'descr': __( "Select or upload image or specify URL from other site to use it as icon", "trx_addons" ),
									'type': 'image'
								},
								// Icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'descr': __( "Select icon from library", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes()
								},
								// Color
								{
									'name': 'icon_color',
									'title': __( 'Color', "trx_addons" ),
									'descr': __( "Selected color will be applied to the Super Title icon or border (if no icon selected)", "trx_addons" ),
									'type': 'color',
								},
								// Background color
								{
									'name': 'icon_bg_color',
									'title': __( 'Background color', "trx_addons" ),
									'descr': __( "Selected background color will be applied to the Super Title icon", "trx_addons" ),
									'type': 'color',
								},			
								// Icon size or image width
								{
									'name': 'icon_size',
									'title': __( 'Icon size or image width', "trx_addons" ),
									'descr': __( "For example, use 14px or 1em.", "trx_addons" ),
									'type': 'text',
								}
							], 'trx-addons/supertitle', props ), props )
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
					props.attributes.items = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/supertitle'
	) );

	// Register block Supertitle Item
	blocks.registerBlockType(
		'trx-addons/supertitle-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Super Title Item', "trx_addons" ),
			description: __( "Select icons, specify title and/or description for each item", "trx_addons" ),
			icon: 'editor-bold',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/supertitle'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				item_type: {
					type: 'string',
					default: 'text'
				},
				text: {
					type: 'string',
					default: ''
				},
				link: {
					type: 'string',
					default: ''
				},
				new_window: {
					type: 'boolean',
					default: false
				},
				tag: {
					type: 'string',
					default: ''
				},
				media: {
					type: 'number',
					default: 0
				},
				media_url: {
					type: 'string',
					default: ''
				},
				item_icon: {
					type: 'string',
					default: ''
				},
				size: {
					type: 'string',
					default: ''
				},
				float_position: {
					type: 'string',
					default: ''
				},
				align: {
					type: 'string',
					default: 'left'
				},
				inline: {
					type: 'boolean',
					default: false
				},
				color: {
					type: 'string',
					default: ''
				},
				color2: {
					type: 'string',
					default: ''
				},
				gradient_direction: {
					type: 'number',
					default: 0
				},
				className: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/supertitle-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Title', "trx_addons" ) + (props.attributes.item_type ? ': ' + props.attributes.item_type : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Item Type
								{
									'name': 'item_type',
									'title': __( 'Item Type', "trx_addons" ),
									'descr': __( "Select type of the item", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_supertitle_item_types'] )
								},
								// Text
								{
									'name': 'text',
									'title': __( 'Text', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'item_type': ['text']
									}
								},
								// Link text
								{
									'name': 'link',
									'title': __( 'Link text', "trx_addons" ),
									'descr': __( "Specify link for the text", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'item_type': ['text']
									}
								},
								// Open in the new tab
								{
									'name': 'new_window',
									'title': __( 'Open in the new tab', "trx_addons" ),
									'descr': __( "Open this link in the new browser's tab", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'item_type': ['text']
									}
								},
								// HTML Tag
								{
									'name': 'tag',
									'title': __( 'HTML Tag', "trx_addons" ),
									'descr': __( "Select HTML wrapper of the item", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_title_tags'] ),
									'dependency': {
										'item_type': ['text']
									}
								},
								// Choose media
								{
									'name': 'media',
									'name_url': 'media_url',
									'title': __( 'Choose media', "trx_addons" ),
									'descr': __( "Select or upload image or specify URL from other site to use it as icon", "trx_addons" ),
									'type': 'image',
									'dependency': {
										'item_type': ['media']
									}
								},
								// Icon
								{
									'name': 'item_icon',
									'title': __( 'Icon', "trx_addons" ),
									'descr': __( "Select icon from library", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes(),
									'dependency': {
										'item_type': ['icon']
									}
								},						
								// Size
								{
									'name': 'size',
									'title': __( 'Size', "trx_addons" ),
									'descr': __( "For example, use 14px or 1em.", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'item_type': ['icon']
									}
								},
								// Float
								{
									'name': 'float_position',
									'title': __( 'Position', "trx_addons" ),
									'descr': __( "Select position of the item - in the left or right column", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] ),
									'dependency': {
										'item_type': ['icon', 'media']
									}
								},
								// Alignment
								{
									'name': 'align',
									'title': __( 'Alignment', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists({
										'left': __( 'Left', "trx_addons" ),
										'right': __( 'Right', "trx_addons" ),
									})
								},
								// Inline
								{
									'name': 'inline',
									'title': __( 'Inline', "trx_addons" ),
									'descr': __( "Make it inline", "trx_addons" ),
									'type': 'boolean',
								},
								// Color
								{
									'name': 'color',
									'title': __( 'Color', "trx_addons" ),
									'descr': __( "Selected color will also be applied to the subtitle", "trx_addons" ),
									'type': 'color',
								},
								// Color 2
								{
									'name': 'color2',
									'title': __( 'Color 2', "trx_addons" ),
									'descr': __( "'If not empty - used for gradient.", "trx_addons" ),
									'type': 'color',
									'dependency': {
										'item_type': ['text']
									}
								},
								// Gradient direction
								{
									'name': 'gradient_direction',
									'title': __( 'Gradient direction', "trx_addons" ),
									'descr': __( "Gradient direction in degress (0 - 360)", "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 360,
									'step': 1
								}
							], 'trx-addons/supertitle-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/supertitle-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
