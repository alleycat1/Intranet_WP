(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;
	
	// Register Block - Layouts
	blocks.registerBlockType(
		'trx-addons/layouts',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Layouts', "trx_addons" ),
			description: __( 'Display previously created custom layouts', "trx_addons" ),
			icon: 'admin-plugins',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					popup_id: {
						type: 'string',
						default: ''
					},
					layout: {
						type: 'string',
						default: ''
					},
					position: {
						type: 'string',
						default: 'right'
					},
					effect: {
						type: 'string',
						default: 'slide'
					},
					size: {
						type: 'number',
						default: 300
					},
					modal: {
						type: 'boolean',
						default: false
					},
					shift_page: {
						type: 'boolean',
						default: false
					},
					show_on: {
						type: 'string',
						default: 'none'
					},
					show_delay: {
						type: 'number',
						default: 0
					},
					content: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Type
								{
									'name': 'type',
									'title': __( 'Type', "trx_addons" ),
									'descr': __( "Select shortcodes's type", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_layouts'] ),
								},
								// Popup (panel) ID
								{
									'name': 'popup_id',
									'title': __( 'Popup (panel) ID', "trx_addons" ),
									'descr': __( "Popup (panel) ID is required!", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'type': ['popup', 'panel']
									}									
								},
								// Layout
								{
									'name': 'layout',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select any previously created layout to insert to this page", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts_layouts'] ),
								},
								// Panel position
								{
									'name': 'position',
									'title': __( 'Panel position', "trx_addons" ),
									'descr': __( "Dock the panel to the specified side of the window", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts_panel_positions'] ),
									'dependency': {
										'type': ['panel']
									}
								},
								// Display effect
								{
									'name': 'effect',
									'title': __( 'Display effect', "trx_addons" ),
									'descr': __( "Effect to display this panel", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts_panel_effects'] ),
									'dependency': {
										'type': ['panel']
									}
								},
								// Size of the panel
								{
									'name': 'size',
									'title': __( 'Size of the panel', "trx_addons" ),
									'descr': __( 'Size (width or height) of the panel', "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 600,
									'dependency': {
										'type': ['panel']
									}
								},
								// Modal
								{
									'name': 'modal',
									'title': __( 'Modal', "trx_addons" ),
									'descr': __( 'Disable clicks on the rest window area', "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'type': ['panel']
									}
								},
								// Shift page
								{
									'name': 'shift_page',
									'title': __( 'Shift page', "trx_addons" ),
									'descr': __( 'Shift page content when panel is opened', "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'type': ['panel']
									}
								},
								// Show on page load
								{
									'name': 'show_on',
									'title': __( 'Show on', "trx_addons" ),
									'descr': __( "The event on which to display the popup", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts_show_on'] ),
								},
								// Show delay
								{
									'name': 'show_delay',
									'title': __( 'Show delay', "trx_addons" ),
									'descr': __( 'How many seconds to wait before the popup appears', "trx_addons" ),
									'type': 'number',
									'min': 0,
									'max': 120,
									'dependency': {
										'type': ['popup', 'panel'],
										'show_on': ['on_page_load', 'on_page_load_once']
									}
								},
								// Content
								{
									'name': 'content',
									'title': __( 'Content', "trx_addons" ),
									'descr': __( "Alternative content to be used instead of the selected layout", "trx_addons" ),
									'type': 'textarea',
								}
							], 'trx-addons/layouts', props ), props )
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
		'trx-addons/layouts'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
