(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Contacts
	blocks.registerBlockType(
		'trx-addons/contacts',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Contacts', "trx_addons" ),
			description: __( "Insert widget with logo, short description and contacts", "trx_addons" ),
			icon: 'admin-home',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					title: {
						type: 'string',
						default: __( 'Contacts', "trx_addons" )
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
					description: {
						type: 'string',
						default: ''
					},
					map: {
						type: 'boolean',
						default: false
					},
					map_height: {
						type: 'number',
						default: 140
					},
					map_position: {
						type: 'string',
						default: 'top'
					},
					address: {
						type: 'string',
						default: ''
					},
					phone: {
						type: 'string',
						default: ''
					},
					email: {
						type: 'string',
						default: ''
					},
					columns: {
						type: 'boolean',
						default: false
					},
					socials: {
						type: 'boolean',
						default: false
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/contacts' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Widget title
								{
									'name': 'title',
									'title': __( 'Widget title', "trx_addons" ),
									'descr': __( "Title of the widget", "trx_addons" ),
									'type': 'text',
								},
								// Logo
								{
									'name': 'logo',
									'name_url': 'logo_url',
									'title': __( 'Logo', "trx_addons" ),
									'descr': __( "Select or upload image or write URL from other site for site's logo.", "trx_addons" ),
									'type': 'image',
								},
								// Logo Retina
								{
									'name': 'logo_retina',
									'name_url': 'logo_retina_url',
									'title': __( 'Logo Retina', "trx_addons" ),
									'descr': __( "Select or upload image or write URL from other site: site's logo for the Retina display.", "trx_addons" ),
									'type': 'image',
								},
								// Description
								{
									'name': 'description',
									'title': __( 'Description', "trx_addons" ),
									'descr': __( "Short description about user. If empty - get description of the first registered blog user", "trx_addons" ),
									'type': 'textarea',
								},
								// Address
								{
									'name': 'address',
									'title': __( 'Address', "trx_addons" ),
									'descr': __( "Address string. Use '|' to split this string on two parts", "trx_addons" ),
									'type': 'text',
								},
								// Phone
								{
									'name': 'phone',
									'title': __( 'Phone', "trx_addons" ),
									'descr': __( "Your phone", "trx_addons" ),
									'type': 'text',
								},
								// E-mail
								{
									'name': 'email',
									'title': __( 'E-mail', "trx_addons" ),
									'descr': __( "Your e-mail address", "trx_addons" ),
									'type': 'text',
								},
								// Break into columns
								{
									'name': 'columns',
									'title': __( 'Break into columns', "trx_addons" ),
									'descr': __( "Break contact information into two columns with the address being displayed on the left hand side and phone/email - on the right.", "trx_addons" ),
									'type': 'boolean',
								},
								// Show map
								{
									'name': 'map',
									'title': __( 'Show map', "trx_addons" ),
									'descr': __( "Do you want to display map with address above", "trx_addons" ),
									'type': 'boolean',
								},
								// Map height
								{
									'name': 'map_height',
									'title': __( 'Map height', "trx_addons" ),
									'descr': __( "Height of the map", "trx_addons" ),
									'type': 'number',
									'min': 100,
									'dependency': {
										'map': [true]
									}
								},
								// Map position
								{
									'name': 'map_position',
									'title': __( 'Map position', "trx_addons" ),
									'descr': __( "Select position of the map", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists({
										'top': __( 'Top', "trx_addons" ),
										'left': __( 'Left', "trx_addons" ),
										'right': __( 'Right', "trx_addons" ),
									}),
									'dependency': {
										'map': [true]
									}
								},
								// Show Social Icons
								{
									'name': 'socials',
									'title': __( 'Show Social Icons', "trx_addons" ),
									'descr': __( "Do you want to display icons with links on your profiles in the Social networks?", "trx_addons" ),
									'type': 'boolean'
								}
							], 'trx-addons/contacts', props ), props )
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
		'trx-addons/contacts'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
