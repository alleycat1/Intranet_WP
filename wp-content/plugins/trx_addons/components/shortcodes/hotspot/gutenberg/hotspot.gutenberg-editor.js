(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Hotspot
	blocks.registerBlockType(
		'trx-addons/hotspot',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Hotspot', "trx_addons" ),
			description: __( "Insert image with hotspots", "trx_addons" ),
			icon: 'location-alt',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					image: {
						type: 'number',
						default: 0
					},
					image_url: {
						type: 'string',
						default: ''
					},
					image_link: {
						type: 'string',
						default: ''
					},
					spots: {
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
			), 'trx-addons/hotspot' ),
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
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_hotspot'] )
								},
								// Image
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'Image', "trx_addons" ),
									'type': 'image'
								},
								// Link
								{
									'name': 'image_link',
									'title': __( 'Image link', "trx_addons" ),
									'type': 'text'
								},
							], 'trx-addons/hotspot', props ), props )
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
					props.attributes.spots = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/hotspot'
	) );

	// Register block Hotspot Item
	blocks.registerBlockType(
		'trx-addons/hotspot-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Hotspot Item', "trx_addons" ),
			description: __( "Insert a single hotspot", "trx_addons" ),
			icon: 'sticky',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/hotspot'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				// Action Item attributes
				spot_visible: {
					type: 'boolean',
					default: true
				},
				spot_x: {
					type: 'number',
					default: 0
				},
				spot_y: {
					type: 'number',
					default: 0
				},
				spot_symbol: {
					type: 'string',
					default: 'none'
				},
				icon: {
					type: 'string',
					default: 'none'
				},
				spot_image: {
					type: 'number',
					default: 0
				},
				spot_char: {
					type: 'string',
					default: ''
				},
				spot_color: {
					type: 'string',
					default: ''
				},
				spot_bg_color: {
					type: 'string',
					default: ''
				},
				spot_sonar_color: {
					type: 'string',
					default: ''
				},
				position: {
					type: 'string',
					default: 'bc'
				},
				align: {
					type: 'string',
					default: 'center'
				},
				open: {
					type: 'boolean',
					default: true
				},
				opened: {
					type: 'boolean',
					default: false
				},
				source: {
					type: 'string',
					default: 'custom'
				},
				post_parts: {
					type: 'string',
					default: 'image,title,category,price'
				},
				post: {
					type: 'number',
					default: 0
				},
				image: {
					type: 'number',
					default: 0
				},
				image_url: {
					type: 'string',
					default: ''
				},
				title: {
					type: 'string',
					default: __( 'One', "trx_addons" )
				},
				subtitle: {
					type: 'string',
					default: ''
				},
				price: {
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
				className: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/hotspot-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Hotspot item', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Spot visible
								{
									'name': 'spot_visible',
									'title': __( 'Always visible', "trx_addons" ),
									'type': 'boolean'
								},
								// X position
								{
									'name': 'spot_x',
									'title': __( 'X position (in %)', "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 100,
									'step': 0.1
								},
								// Y position
								{
									'name': 'spot_y',
									'title': __( 'Y position (in %)', "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 100,
									'step': 0.1
								},
								// Spot symbol
								{
									'name': 'spot_symbol',
									'title': __( 'Spot symbol', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_hotspot_symbols'] )
								},
								// Spot image
								{
									'name': 'spot_image',
									'name_url': 'spot_image_url',
									'title': __( 'Image', "trx_addons" ),
									'type': 'image',
									'dependency': {
										'spot_symbol': ['image']
									}
								},
								// Spot icon
								{
									'name': 'icon',
									'title': __( 'Icon', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_option_icons_classes(),
									'dependency': {
										'spot_symbol': [ 'icon' ]
									}
								},
								// Spot caption
								{
									'name': 'spot_char',
									'title': __( 'Caption', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'spot_symbol': [ 'custom' ]
									}
								},
								// Spot color
								{
									'name': 'spot_color',
									'title': __( 'Spot color', "trx_addons" ),
									'type': 'color',
									'dependency': {
										'spot_symbol': [ '^none' ]
									}
								},
								// Background color
								{
									'name': 'spot_bg_color',
									'title': __( 'Spot bg color', "trx_addons" ),
									'type': 'color'
								},
								// Sonar color
								{
									'name': 'spot_sonar_color',
									'title': __( 'Spot sonar color', "trx_addons" ),
									'type': 'color'
								},
								// Popup position
								{
									'name': 'position',
									'title': __( 'Popup position', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_positions'] )
								},
								// Popup alignment
								{
									'name': 'align',
									'title': __( 'Popup alignment', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
								},
								// Popup open on click/hover
								{
									'name': 'open',
									'title': __( 'Open on click', "trx_addons" ),
									'type': 'boolean'
								},
								// Popup opened on page load
								{
									'name': 'opened',
									'title': __( 'Open on load', "trx_addons" ),
									'type': 'boolean'
								},
								// Source
								{
									'name': 'source',
									'title': __( 'Data source', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_hotspot_sources'] )
								},
								// Post parts
								{
									'name': 'post_parts',
									'title': __( 'Show parts', "trx_addons" ),
									'type': 'select',
									'multiple': true,
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_hotspot_post_parts'] ),
									'dependency': {
										'source': [ 'post' ]
									}
								},
								// Post
								{
									'name': 'post',
									'title': __( 'Data from post', "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_hotspot_posts'] ),
									'dependency': {
										'source': ['post']
									}
								},
								// Image
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'Image', "trx_addons" ),
									'type': 'image',
									'dependency': {
										'source': ['custom']
									}
								},
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'source': ['custom']
									}
								},
								// Subtitle
								{
									'name': 'subtitle',
									'title': __( 'Subtitle', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'source': ['custom']
									}
								},
								// Price
								{
									'name': 'price',
									'title': __( 'Price', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'source': ['custom']
									}
								},
								// Description
								{
									'name': 'description',
									'title': __( 'Description', "trx_addons" ),
									'type': 'textarea',
									'dependency': {
										'source': ['custom']
									}
								},
								// Link
								{
									'name': 'link',
									'title': __( 'Link', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'source': ['custom']
									}
								},
								// Link Text
								{
									'name': 'link_text',
									'title': __( 'Link Text', "trx_addons" ),
									'type': 'text'
								}
							], 'trx-addons/hotspot-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/hotspot-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
