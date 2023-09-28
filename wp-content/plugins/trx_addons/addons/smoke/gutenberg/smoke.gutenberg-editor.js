(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Smoke
	blocks.registerBlockType(
		'trx-addons/smoke',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Smoke', "trx_addons" ),
			description: __( "Add visual effects 'Smoke', 'Fog' and 'Spots' to this page", "trx_addons" ),
			icon: 'cloud',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params',
				{
					type: {
						type: 'string',
						default: 'smoke'
					},
					bg_color: {
						type: 'string',
						default: '#000000'
					},
					tint_color: {
						type: 'string',
						default: ''
					},
					smoke_curls: {
						type: 'number',
						default: 5
					},
					smoke_density: {
						type: 'number',
						default: 0.97
					},
					smoke_velocity: {
						type: 'number',
						default: 0.98
					},
					smoke_pressure: {
						type: 'number',
						default: 0.8
					},
					smoke_iterations: {
						type: 'number',
						default: 10
					},
					smoke_slap: {
						type: 'number',
						default: 0.6
					},
					use_image: {
						type: 'boolean',
						default: false
					},
					image: {
						type: 'number',
						default: 0
					},
					image_url: {
						type: 'string',
						default: ''
					},
					image_repeat: {
						type: 'number',
						default: 5
					},
					cursor: {
						type: 'number',
						default: 0
					},
					cursor_url: {
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
				}, 'trx-addons/smoke'
			),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'parent': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Type
								{
									'name': 'type',
									'title': __( 'Type', "trx_addons" ),
									'descr': __( "Select an effect", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_smoke_types'] )
								},
								// Background color
								{
									'name': 'bg_color',
									'title': __( 'Bg color', "trx_addons" ),
									'descr': __( "Select a background color", "trx_addons" ),
									'type': 'color'
								},
								// Tint color
								{
									'name': 'tint_color',
									'title': __( 'Tint color', "trx_addons" ),
									'descr': __( "Select a tint color", "trx_addons" ),
									'dependency': {
										'type': ['smoke', 'fog']
									},
									'type': 'color'
								},
								// Smoke curls
								{
									'name': 'smoke_curls',
									'title': __( 'Curls', "trx_addons" ),
									'descr': __( "Specify a number of curls, used for this effect.", "trx_addons" ),
									'dependency': {
										'type': ['smoke']
									},
									'type': 'number',
									'min': 1,
									'max': 20
								},
								// Smoke dencity
								{
									'name': 'smoke_density',
									'title': __( 'Density', "trx_addons" ),
									'descr': __( "Specify a coefficient of the density, used for this effect.", "trx_addons" ),
									'dependency': {
										'type': ['smoke']
									},
									'type': 'number',
									'min': 0.1,
									'max': 1.0,
									'step': 0.01
								},
								// Smoke velocity
								{
									'name': 'smoke_velocity',
									'title': __( 'Velocity', "trx_addons" ),
									'descr': __( "Specify a coefficient of the velocity, used for this effect.", "trx_addons" ),
									'dependency': {
										'type': ['smoke']
									},
									'type': 'number',
									'min': 0.1,
									'max': 1.0,
									'step': 0.01
								},
								// Smoke pressure
								{
									'name': 'smoke_pressure',
									'title': __( 'Pressure', "trx_addons" ),
									'descr': __( "Specify a coefficient of the pressure, used for this effect.", "trx_addons" ),
									'dependency': {
										'type': ['smoke']
									},
									'type': 'number',
									'min': 0.1,
									'max': 1.0,
									'step': 0.01
								},
								// Smoke iterations
								{
									'name': 'smoke_iterations',
									'title': __( 'Iterations', "trx_addons" ),
									'descr': __( "Specify a number of iterations, used for this effect.", "trx_addons" ),
									'dependency': {
										'type': ['smoke']
									},
									'type': 'number',
									'min': 1,
									'max': 20
								},
								// Smoke slap
								{
									'name': 'smoke_slap',
									'title': __( 'Slap', "trx_addons" ),
									'descr': __( "Specify a slap force, used for this effect.", "trx_addons" ),
									'dependency': {
										'type': ['smoke']
									},
									'type': 'number',
									'min': 0.1,
									'max': 1.0,
									'step': 0.01
								},
								// Use image
								{
									'name': 'use_image',
									'title': __( 'Use image', "trx_addons" ),
									'descr': __( "Use an image as a texture for smoke or fog effect.", "trx_addons" ),
									'dependency': {
										'type': ['smoke', 'fog']
									},
									'type': 'boolean'
								},
								// Image
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'Image', "trx_addons" ),
									'descr': __( "Select the image from the library for this effect.", "trx_addons" ),
									'dependency': {
										'type': ['smoke', 'fog'],
										'use_image': true
									},
									'type': 'image'
								},
								// Image repeat
								{
									'name': 'image_repeat',
									'title': __( 'Repeater', "trx_addons" ),
									'descr': __( "Specify the number of repeated images to create a fog.", "trx_addons" ),
									'dependency': {
										'type': ['fog'],
										'use_image': true
									},
									'type': 'number',
									'min': 1,
									'max': 20
								},
								// Cursor image
								{
									'name': 'cursor',
									'name_url': 'cursor_url',
									'title': __( 'Cursor image', "trx_addons" ),
									'descr': __( "Select the image from the library for the cursor.", "trx_addons" ),
									'dependency': {
										'type': ['smoke', 'fog']
									},
									'type': 'image'
								}
							], 'trx-addons/smoke', props ), props )
						),
						'additional_params': null
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
		'trx-addons/smoke'
	) );

	// Register block Spots Item
	blocks.registerBlockType(
		'trx-addons/smoke-item',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Smoke Item', "trx_addons" ),
			description: __( "Insert a single 'Smoke - Spots' item", "trx_addons" ),
			icon: 'cloud',
			category: 'trx-addons-blocks',
			parent: ['trx-addons/smoke'],
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', {
				// A spot attributes
				motion: {
					type: 'number',
					default: 0
				},
				shape: {
					type: 'number',
					default: 1
				},
				pos_x: {
					type: 'number',
					default: 0
				},
				pos_y: {
					type: 'number',
					default: 0
				},
				color_1: {
					type: 'string',
					default: ''
				},
				color_2: {
					type: 'string',
					default: ''
				},
				scale: {
					type: 'number',
					default: 2
				},
				rotation: {
					type: 'number',
					default: 0
				}
			}, 'trx-addons/smoke-item' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'title': __( 'Spot', "trx_addons" ) + ": "
								+ ( props.attributes.motion === 0 ? 'static' : ( props.attributes.motion === 1 ? 'slow' : 'fast' ) )
								+ " " + props.attributes.pos_x + '%, ' + props.attributes.pos_y + '%',
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Motion
								{
									'name': 'motion',
									'title': __( 'Motion', "trx_addons" ),
									'descr': __( "A motion style of the spot", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_smoke_spot_motions'] )
								},
								// Position along X axis
								{
									'name': 'pos_x',
									'title': __( 'Position X', "trx_addons" ),
									'descr': __( "Position of the spot alogn the X axis (in %).", "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 100
								},
								// Position along Y axis
								{
									'name': 'pos_y',
									'title': __( 'Position Y', "trx_addons" ),
									'descr': __( "Position of the spot alogn the Y axis (in %).", "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 100
								},
								// Shape
								{
									'name': 'shape',
									'title': __( 'Shape', "trx_addons" ),
									'descr': __( "A coefficient of the shape deformation. 1 - round spot.", "trx_addons" ),
									'type': 'number',
									'min': 0.1,
									'max': 5,
									'step': 0.1
								},
								// Scale
								{
									'name': 'scale',
									'title': __( 'Scale', "trx_addons" ),
									'descr': __( "A scale factor.", "trx_addons" ),
									'type': 'number',
									'min': 0.5,
									'max': 10,
									'step': 0.1
								},
								// Rotation
								{
									'name': 'rotation',
									'title': __( 'Rotation', "trx_addons" ),
									'descr': __( "A rotation factor.", "trx_addons" ),
									'type': 'number',
									'min': -1,
									'max': 1,
									'step': 0.01
								},
								// Color 1
								{
									'name': 'color_1',
									'title': __( 'Color 1', "trx_addons" ),
									'descr': __( "Select a first color of the spot", "trx_addons" ),
									'type': 'color'
								},
								// Color 2
								{
									'name': 'color_2',
									'title': __( 'Color 2', "trx_addons" ),
									'descr': __( "Select a second color of the spot", "trx_addons" ),
									'type': 'color'
								}
							], 'trx-addons/smoke-item', props ), props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/smoke-item'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
