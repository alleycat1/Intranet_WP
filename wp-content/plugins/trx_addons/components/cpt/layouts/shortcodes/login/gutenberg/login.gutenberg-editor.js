(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;
	
	// Register Block - Login link
	blocks.registerBlockType(
		'trx-addons/layouts-login',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Login link', "trx_addons" ),
			description: __( 'Insert Login/Logout link to the custom layout', "trx_addons" ),
			icon: 'admin-users',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					user_menu: {
						type: 'boolean',
						default: false
					},
					text_login: {
						type: 'string',
						default: ''
					},
					text_logout: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_hide(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-login' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_login'] ),
								},
								// User menu
								{
									'name': 'user_menu',
									'title': __( 'User menu', "trx_addons" ),
									'descr': __( "Show user menu on mouse hover", "trx_addons" ),
									'type': 'boolean',
								},
								// Login text
								{
									'name': 'text_login',
									'title': __( 'Login text', "trx_addons" ),
									'descr': __( "Text of the Login link", "trx_addons" ),
									'type': 'text',
								},
								// Logout text
								{
									'name': 'text_logout',
									'title': __( 'Logout text', "trx_addons" ),
									'descr': __( "Text of the Logout link", "trx_addons" ),
									'type': 'text',
								}
							], 'trx-addons/layouts-login', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Hide on devices params
							trx_addons_gutenberg_add_param_hide( props ),
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
		'trx-addons/layouts-login'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );