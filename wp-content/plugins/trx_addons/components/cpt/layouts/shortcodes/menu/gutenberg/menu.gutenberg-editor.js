(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;
	
	// Register Block - Menu
	blocks.registerBlockType(
		'trx-addons/layouts-menu',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Menu', "trx_addons" ),
			description: __( 'Insert any menu to the custom layout', "trx_addons" ),
			icon: 'menu',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					direction: {
						type: 'string',
						default: 'horizontal'
					},
					location: {
						type: 'string',
						default: 'menu_main'
					},
					menu: {
						type: 'string',
						default: ''
					},
					mobile_menu: {
						type: 'boolean',
						default: false
					},
					mobile_button: {
						type: 'boolean',
						default: false
					},
					animation_in: {
						type: 'string',
						default: ''
					},
					animation_out: {
						type: 'string',
						default: ''
					},
					hover: {
						type: 'string',
						default: 'fade'
					},
					hide_on_mobile: {
						type: 'boolean',
						default: false
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-menu' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select layout's type", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_menu'] ),
								},
								// Direction
								{
									'name': 'direction',
									'title': __( 'Direction', "trx_addons" ),
									'descr': __( "Select direction of the menu items", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_directions'] ),
									'dependency': {
										'type': ['default']
									}
								},
								// Location
								{
									'name': 'location',
									'title': __( 'Location', "trx_addons" ),
									'descr': __( "Select menu location to insert to the layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['menu_locations'] ),
								},
								// Menu
								{
									'name': 'menu',
									'title': __( 'Menu', "trx_addons" ),
									'descr': __( "Select menu to insert to the layout. If empty - use menu assigned in the field 'Location'", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['menus'] ),
									'dependency': {
										'location': ['none']
									}
								},
								// Hover
								{
									'name': 'hover',
									'title': __( 'Hover', "trx_addons" ),
									'descr': __( "Select the menu items hover", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['menu_hover'] ),
									'dependency': {
										'type': ['default']
									}
								},
								// Submenu animation in
								{
									'name': 'animation_in',
									'title': __( 'Submenu animation in', "trx_addons" ),
									'descr': __( "Select animation to show submenu", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['animations_in'] ),
									'dependency': {
										'type': ['default']
									}
								},
								// Submenu animation out
								{
									'name': 'animation_out',
									'title': __( 'Submenu animation out', "trx_addons" ),
									'descr': __( "Select animation to hide submenu", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['animations_out'] ),
									'dependency': {
										'type': ['default']
									}
								},
								// Mobile button
								{
									'name': 'mobile_button',
									'title': __( 'Mobile button', "trx_addons" ),
									'descr': __( "Replace the menu with a menu button on mobile devices. Open the menu when the button is clicked.", "trx_addons" ),
									'type': 'boolean',
								},
								// Add to the mobile menu
								{
									'name': 'mobile_menu',
									'title': __( 'Add to the mobile menu', "trx_addons" ),
									'descr': __( "Use these menu items as a mobile menu (if mobile menu is not selected in the theme).", "trx_addons" ),
									'type': 'boolean',
								},
								// Hide on mobile devices
								{
									'name': 'hide_on_mobile',
									'title': __( 'Hide on mobile devices', "trx_addons" ),
									'descr': __( "Hide this item on mobile devices", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'type': ['default']
									}
								}
							], 'trx-addons/layouts-menu', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// ID, Class, CSS params
							trx_addons_gutenberg_add_param_id( props )
						)
					}, props
				);
			},
			save: function(props) {
				return el( '', null );
			}
		},
		'trx-addons/layouts-menu'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );