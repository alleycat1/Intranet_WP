(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - OpenStreet Map
	blocks.registerBlockType(
		'trx-addons/osmap',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'OpenStreet Map', "trx_addons" ),
			description: __( "OpenStreet map with custom styles and several markers", "trx_addons" ),
			icon: 'admin-site',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					style: {
						type: 'string',
						default: TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_osmap_style_default']
					},
					zoom: {
						type: 'string',
						default: '16'
					},
					center: {
						type: 'string',
						default: ''
					},
					width: {
						type: 'string',
						default: '100%'
					},
					height: {
						type: 'string',
						default: '350'
					},
					cluster: {
						type: 'number',
						default: ''
					},
					cluster_url: {
						type: 'string',
						default: ''
					},
					prevent_scroll: {
						type: 'boolean',
						default: false
					},
					address: {
						type: 'string',
						default: ''
					},
					markers: {
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
			), 'trx-addons/osmap' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_osmap'] )
								},
								// Style
								{
									'name': 'style',
									'title': __( 'Style', "trx_addons" ),
									'descr': __( "Map's custom style", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_osmap_styles'] )
								},
								// Zoom
								{
									'name': 'zoom',
									'title': __( 'Zoom', "trx_addons" ),
									'descr': __( "Map zoom factor on a scale from 1 to 20. If assigned the value '0' or left empty, fit the bounds to markers.", "trx_addons" ),
									'type': 'text',
								},
								// Center
								{
									'name': 'center',
									'title': __( 'Center', "trx_addons" ),
									'descr': __( "Comma separated coordinates of the map's center. If left empty, the coordinates of the first marker will be used.", "trx_addons" ),
									'type': 'text',
								},
								// Width
								{
									'name': 'width',
									'title': __( 'Width', "trx_addons" ),
									'descr': __( "Width of the element", "trx_addons" ),
									'type': 'text',
								},
								// Height
								{
									'name': 'height',
									'title': __( 'Height', "trx_addons" ),
									'descr': __( "Height of the element", "trx_addons" ),
									'type': 'text',
								},
								// Cluster icon
								{
									'name': 'cluster',
									'name_url': 'cluster_url',
									'title': __( 'Cluster icon', "trx_addons" ),
									'descr': __( "Select or upload image for markers clusterer", "trx_addons" ),
									'type': 'image'
								},
								// Prevent_scroll
								{
									'name': 'prevent_scroll',
									'title': __( 'Prevent_scroll', "trx_addons" ),
									'descr': __( "Disallow scrolling of the map", "trx_addons" ),
									'type': 'boolean'
								},
								// Address
								{
									'name': 'address',
									'title': __( 'Address or Lat,Lng', "trx_addons" ),
									'descr': __( "Specify the address (or comma separated coordinates) if you don't need a unique marker, title or LatLng coordinates. Otherwise, leave this field empty and specify the markers below.", "trx_addons" ),
									'type': 'text',
								}
							], 'trx-addons/osmap', props ), props )
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
					props.attributes.markers = trx_addons_gutenberg_get_child_attr( props );
				}
				return el( trx_addons_get_wp_editor().InnerBlocks.Content, {} );
			},
		},
		'trx-addons/osmap'
	) );

	// Register block Markers
	blocks.registerBlockType(
		'trx-addons/osmap-markers',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Markers', "trx_addons" ),
			description: __( "Add markers to the map", "trx_addons" ),
			icon: 'admin-site',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/osmap'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				title: {
					type: 'string',
					default: __( 'One', "trx_addons" )
				},
				address: {
					type: 'string',
					default: ''
				},
				icon: {
					type: 'number',
					default: ''
				},
				icon_url: {
					type: 'string',
					default: ''
				},
				icon_retina: {
					type: 'number',
					default: ''
				},
				icon_retina_url: {
					type: 'string',
					default: ''
				},
				icon_width: {
					type: 'string',
					default: ''
				},
				icon_height: {
					type: 'string',
					default: ''
				},
				description: {
					type: 'string',
					default: ''
				},
				className: {
					type: 'string',
					default: ''
				}
			}, 'trx-addons/osmap-markers' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Marker', "trx_addons" ) + (props.attributes.title ? ': ' + props.attributes.title : ''),
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'descr': __( "Title of the marker", "trx_addons" ),
									'type': 'text'
								},
								// Address
								{
									'name': 'address',
									'title': __( 'Address or Lat,Lng', "trx_addons" ),
									'descr': __( "Address or comma separated coorditanes of this marker", "trx_addons" ),
									'type': 'text'
								},
								// Marker image
								{
									'name': 'icon',
									'name_url': 'icon_url',
									'title': __( 'Marker image', "trx_addons" ),
									'descr': __( "Select or upload image of this marker", "trx_addons" ),
									'type': 'image',
								},
								// Marker for Retina
								{
									'name': 'icon_retina',
									'name_url': 'icon_retina_url',
									'title': __( 'Marker for Retina', "trx_addons" ),
									'descr': __( "Select or upload image of this marker for Retina device", "trx_addons" ),
									'type': 'image',
								},
								// Width
								{
									'name': 'icon_width',
									'title': __( 'Width', "trx_addons" ),
									'descr': __( "Width of this marker. If empty - use original size", "trx_addons" ),
									'type': 'text'
								},
								// Height
								{
									'name': 'icon_height',
									'title': __( 'Height', "trx_addons" ),
									'descr': __( "Height of this marker. If empty - use original size", "trx_addons" ),
									'type': 'text'
								},
								// Description
								{
									'name': 'description',
									'title': __( 'Description', "trx_addons" ),
									'descr': __( "Description of the marker", "trx_addons" ),
									'type': 'textarea'
								}
							], 'trx-addons/osmap-markers', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/osmap-markers'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
