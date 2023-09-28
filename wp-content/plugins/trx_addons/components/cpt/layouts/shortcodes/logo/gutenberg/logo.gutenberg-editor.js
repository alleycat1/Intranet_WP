(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;
	
	// Register Block - Logo
	blocks.registerBlockType(
		'trx-addons/layouts-logo',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Logo', "trx_addons" ),
			description: __( 'Insert the site logo to the custom layout', "trx_addons" ),
			icon: 'admin-appearance',
			category: 'trx-addons-layouts',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					logo_height: {
						type: 'string',
						default: ''
					},
					logo: {
						type: 'number',
						default: 0
					},
					logo_url: {
						type: 'string',
						default: ''
					},
					logo_retina: {
						type: 'number',
						default: 0
					},
					logo_retina_url: {
						type: 'string',
						default: ''
					},
					logo_text: {
						type: 'string',
						default: ''
					},
					logo_slogan: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_hide(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/layouts-logo' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_logo'] ),
								},
								// Max height
								{
									'name': 'logo_height',
									'title': __( 'Max height', "trx_addons" ),
									'descr': __( "Max height of the logo image. If empty - theme default value is used", "trx_addons" ),
									'type': 'text',
								},
								// Logo
								{
									'name': 'logo',
									'name_url': 'logo_url',
									'title': __( 'Logo', "trx_addons" ),
									'descr': __( "Select or upload image for site's logo. If empty - theme-specific logo is used", "trx_addons" ),
									'type': 'image',
								},
								// Logo Retina
								{
									'name': 'logo_retina',
									'name_url': 'logo_retina_url',
									'title': __( 'Logo Retin', "trx_addons" ),
									'descr': __( "Select or upload image for site's logo on the Retina displays", "trx_addons" ),
									'type': 'image',
								},
								// Logo text
								{
									'name': 'logo_text',
									'title': __( 'Logo text', "trx_addons" ),
									'descr': __( "Site name (used as logo if image is empty or as alt text if image is selected). If not specified - use blog name", "trx_addons" ),
									'type': 'text',
								},
								// Logo slogan
								{
									'name': 'logo_slogan',
									'title': __( 'Logo slogan', "trx_addons" ),
									'descr': __( "Slogan or description below site name (used if logo is empty). If not specified - use blog description", "trx_addons" ),
									'type': 'textarea',
								}
							], 'trx-addons/layouts-logo', props ), props )
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
		'trx-addons/layouts-logo'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );