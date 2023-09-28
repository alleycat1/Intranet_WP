(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Price
	blocks.registerBlockType(
		'trx-addons/price',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Price', "trx_addons" ),
			description: __( "Add block with prices", "trx_addons" ),
			icon: 'admin-page',
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
					prices: {
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
			), 'trx-addons/price' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_price'] )
								},
								// Columns
								{
									'name': 'columns',
									'title': __( 'Columns', "trx_addons" ),
									'descr': __( "Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", "trx_addons" ),
									'type': 'number',
									'min': 1,
									'max': 4
								}
							], 'trx-addons/price', props ), props )
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
					props.attributes.prices = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/price'
	) );

	// Register block Action Item
	blocks.registerBlockType(
		'trx-addons/price-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Price Item', "trx_addons" ),
			description: __( "Select icon, specify price, title and/or description for each itemm", "trx_addons" ),
			icon: 'admin-page',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/price'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				title: {
					type: 'string',
					default: __( 'One', "trx_addons" )
				},
				subtitle: {
					type: 'string',
					default: ''
				},
				label: {
					type: 'string',
					default: ''
				},
				description: {
					type: 'string',
					default: ''
				},
				before_price: {
					type: 'string',
					default: ''
				},
				price: {
					type: 'string',
					default: ''
				},
				after_price: {
					type: 'string',
					default: ''
				},
				details: {
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
				new_window: {
					type: 'boolean',
					default: false
				},
				icon: {
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
				bg_color: {
					type: 'string',
					default: ''
				},
				className: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/price-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Price item', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
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
								// Label
								{
									'name': 'label',
									'title': __( 'Label', "trx_addons" ),
									'descr': __( "If not empty, a colored band with this text is shown at the top corner of the price block.", "trx_addons" ),
									'type': 'text'
								},
								// Description
								{
									'name': 'description',
									'title': __( 'Description', "trx_addons" ),
									'descr': __( "Enter short description of the item", "trx_addons" ),
									'type': 'textarea'
								},
								// Before price
								{
									'name': 'before_price',
									'title': __( 'Before price', "trx_addons" ),
									'descr': __( "Any text before the price value", "trx_addons" ),
									'type': 'text'
								},
								// Price
								{
									'name': 'price',
									'title': __( 'Price', "trx_addons" ),
									'descr': __( "Price valuee", "trx_addons" ),
									'type': 'text'
								},
								// After price
								{
									'name': 'after_price',
									'title': __( 'After price', "trx_addons" ),
									'descr': __( "Any text after the price value", "trx_addons" ),
									'type': 'text'
								},
								// Details
								{
									'name': 'details',
									'title': __( 'Details', "trx_addons" ),
									'descr': __( "Price details", "trx_addons" ),
									'type': 'text'
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
								// Open in the new tab
								{
									'name': 'new_window',
									'title': __( 'Open in the new tab', "trx_addons" ),
									'descr': __( "Open this link in the new browser's tab", "trx_addons" ),
									'type': 'boolean'
								},
								// Icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'descr': __( "Select icon from library", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes()
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
								// Background Color
								{
									'name': 'bg_color',
									'title': __( 'Background color', "trx_addons" ),
									'descr': __( "Select custom background color of this item", "trx_addons" ),
									'type': 'color'
								}
							], 'trx-addons/price-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/price-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
