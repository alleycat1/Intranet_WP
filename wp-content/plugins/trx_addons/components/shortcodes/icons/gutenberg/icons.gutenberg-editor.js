(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Icons
	blocks.registerBlockType(
		'trx-addons/icons',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Icons', "trx_addons" ),
			description: __( "Insert icons or images with title and description", "trx_addons" ),
			icon: 'carrot',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					align: {
						type: 'string',
						default: 'center'
					},
					size: {
						type: 'string',
						default: 'medium'
					},
					color: {
						type: 'string',
						default: ''
					},
					item_title_color: {
						type: 'string',
						default: ''
					},
					item_text_color: {
						type: 'string',
						default: ''
					},
					columns: {
						type: 'number',
						default: 1
					},
					icons_animation: {
						type: 'boolean',
						default: false
					},
					icons: {
						type: 'string',
						default: ''
					},
					// Reload block - hidden option
					reload: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_slider(),
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/icons' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_icons'] )
								},
								// Align
								{
									'name': 'align',
									'title': __( 'Align', "trx_addons" ),
									'descr': __( "Select alignment of this item", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
								},
								// Icon size
								{
									'name': 'size',
									'title': __( 'Icon size', "trx_addons" ),
									'descr': __( "Select icon's size", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_icon_sizes'] )
								},
								// Icon color
								{
									'name': 'color',
									'title': __( 'Icon color', "trx_addons" ),
									'descr': __( "Select custom color for item icons", "trx_addons" ),
									'type': 'color',
								},
								// Title color
								{
									'name': 'item_title_color',
									'title': __( 'Title color', "trx_addons" ),
									'descr': __( "Select custom color for item titles", "trx_addons" ),
									'type': 'color',
								},
								// Text (description) color
								{
									'name': 'item_text_color',
									'title': __( 'Text color', "trx_addons" ),
									'descr': __( "Select custom color for item descriptions", "trx_addons" ),
									'type': 'color',
								},
								// Columns
								{
									'name': 'columns',
									'title': __( 'Columns', "trx_addons" ),
									'descr': __( "Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': 4
								},
								// Animation
								{
									'name': 'icons_animation',
									'title': __( 'Animation', "trx_addons" ),
									'descr': __( "Toggle on if you want to animate icons. Attention! Animation is enabled only if there is an .SVG  icon in your theme with the same name as the selected icon.", "trx_addons" ),
									'type': 'boolean'
								}
							], 'trx-addons/icons', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Title params
							trx_addons_gutenberg_add_param_title( props, true ),
							// Slider params
							trx_addons_gutenberg_add_param_slider( props ),
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				// Get child block values of attributes
				if ( props.hasOwnProperty( 'innerBlocks' ) ) {	// && props.innerBlocks.length
					props.attributes.icons = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/icons'
	) );

	// Register block Icons Item
	blocks.registerBlockType(
		'trx-addons/icons-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Icons Item', "trx_addons" ),
			description: __( "elect icons, specify title and/or description for each item", "trx_addons" ),
			icon: 'carrot',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/icons'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				title: {
					type: 'string',
					default: __( 'One', "trx_addons" )
				},
				link: {
					type: 'string',
					default: ''
				},
				description: {
					type: 'string',
					default: ''
				},
				color: {
					type: 'string',
					default: ''
				},
				item_title_color: {
					type: 'string',
					default: ''
				},
				item_text_color: {
					type: 'string',
					default: ''
				},
				char: {
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
				icon: {
					type: 'string',
					default: ''
				},
				className: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/icons-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Icons item', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'descr': __( "Select icon from library", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes()
								},
								// Char
								{
									'name': 'char',
									'title': __( 'or character', "trx_addons" ),
									'descr': __( "Single character instaed image or icon", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'icon': ['', 'none']
									}
								},
								// Image
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'or image', "trx_addons" ),
									'descr': __( "Select or upload image or specify URL from other site to use it as icon", "trx_addons" ),
									'type': 'image',
									'dependency': {
										'icon': ['', 'none'],
										'char': ''
									}
								},
								// Icon color
								{
									'name': 'color',
									'title': __( 'Icon color', "trx_addons" ),
									'descr': __( "Select a custom color of the icon", "trx_addons" ),
									'type': 'color'
								},
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Enter title of the item", "trx_addons" ),
									'type': 'text'
								},
								// Title color
								{
									'name': 'item_title_color',
									'title': __( 'Title color', "trx_addons" ),
									'descr': __( "Select a custom color of the title", "trx_addons" ),
									'type': 'color'
								},
								// Link
								{
									'name': 'link',
									'title': __( 'Link', "trx_addons" ),
									'descr': __( "URL to link this block", "trx_addons" ),
									'type': 'text'
								},
								// Description
								{
									'name': 'description',
									'title': __( 'Description', "trx_addons" ),
									'descr': __( "Enter short description for this item", "trx_addons" ),
									'type': 'textarea'
								},
								// Text (description) color
								{
									'name': 'item_text_color',
									'title': __( 'Description color', "trx_addons" ),
									'descr': __( "Select a custom color of the description", "trx_addons" ),
									'type': 'color'
								},
							], 'trx-addons/icons-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/icons-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
