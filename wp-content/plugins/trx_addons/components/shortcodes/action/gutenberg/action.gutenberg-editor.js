(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Action
	blocks.registerBlockType(
		'trx-addons/action',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Action', "trx_addons" ),
			description: __( "Insert 'Call to action' or custom Events as slider or columns layout", "trx_addons" ),
			icon: 'align-right',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					columns: {
						type: 'number',
						default: 1
					},
					full_height: {
						type: 'boolean',
						default: false
					},
					min_height: {
						type: 'string',
						default: ''
					},
					actions: {
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
			), 'trx-addons/action' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_action'] )
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
								// Full Height
								{
									'name': 'full_height',
									'title': __( 'Full Height', "trx_addons" ),
									'descr': __( "Stretch the height of the element to the full screen's height", "trx_addons" ),
									'type': 'boolean'
								},
								// Height
								{
									'name': 'min_height',
									'title': __( 'Height', "trx_addons" ),
									'descr': __( "Specify the height of items. If left empty or assigned the value '0' - height is auto", "trx_addons" ),
									'type': 'text'
								}
							], 'trx-addons/action', props ), props )
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
					props.attributes.actions = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/action'
	) );

	// Register block Action Item
	blocks.registerBlockType(
		'trx-addons/action-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Action Item', "trx_addons" ),
			description: __( "Insert 'Call to action' item", "trx_addons" ),
			icon: 'align-right',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/action'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				// Action Item attributes
				position: {
					type: 'string',
					default: 'mc'
				},
				title: {
					type: 'string',
					default: __( 'One', "trx_addons" )
				},
				subtitle: {
					type: 'string',
					default: ''
				},
				date: {
					type: 'string',
					default: ''
				},
				info: {
					type: 'string',
					default: ''
				},
				description: {
					type: 'string',
					default: ''
				},
				link: {
					type: 'string',
					default: ''
				},
				link_text: {
					type: 'string',
					default: ''
				},
				color: {
					type: 'string',
					default: ''
				},
				bg_color: {
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
				bg_image: {
					type: 'number',
					default: 0
				},
				bg_image_url: {
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
			}, 'trx-addons/action-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Action item', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Position
								{
									'name': 'position',
									'title': __( 'Position', "trx_addons" ),
									'descr': __( "Text position inside item", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_positions'] )
								},
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Enter title of the item", "trx_addons" ),
									'type': 'text'
								},
								// Subtitle
								{
									'name': 'subtitle',
									'title': __( 'Subtitle', "trx_addons" ),
									'descr': __( "Enter subtitle of the item", "trx_addons" ),
									'type': 'text'
								},
								// Date
								{
									'name': 'date',
									'title': __( 'Date', "trx_addons" ),
									'descr': __( "Specify date (and/or time) of this event", "trx_addons" ),
									'type': 'text'
								},
								// Brief info
								{
									'name': 'info',
									'title': __( 'Brief info', "trx_addons" ),
									'descr': __( "Additional info for this item", "trx_addons" ),
									'type': 'text'
								},
								// Description
								{
									'name': 'description',
									'title': __( 'Description', "trx_addons" ),
									'descr': __( "Enter short description of the item", "trx_addons" ),
									'type': 'textarea'
								},
								// Link
								{
									'name': 'link',
									'title': __( 'Link', "trx_addons" ),
									'descr': __( "URL to link this item", "trx_addons" ),
									'type': 'text'
								},
								// Link Text
								{
									'name': 'link_text',
									'title': __( 'Link Text', "trx_addons" ),
									'descr': __( "Caption of the link", "trx_addons" ),
									'type': 'text'
								},
								// Color
								{
									'name': 'color',
									'title': __( 'Color', "trx_addons" ),
									'descr': __( "Select custom color of this item", "trx_addons" ),
									'type': 'color'
								},
								// Background Color
								{
									'name': 'bg_color',
									'title': __( 'Background color', "trx_addons" ),
									'descr': __( "Select custom background color of this item", "trx_addons" ),
									'type': 'color'
								},
								// Image
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'Image', "trx_addons" ),
									'descr': __( "Select or upload image or specify URL from other site to use it as icon", "trx_addons" ),
									'type': 'image'
								},
								// Background Image
								{
									'name': 'bg_image',
									'name_url': 'bg_image_url',
									'title': __( 'Background image', "trx_addons" ),
									'descr': __( "Select or upload image or specify URL from other site to use it as background of this item", "trx_addons" ),
									'type': 'image'
								},
								// Icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'descr': __( "Select icon from library", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes()
								}
							], 'trx-addons/action-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/action-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
