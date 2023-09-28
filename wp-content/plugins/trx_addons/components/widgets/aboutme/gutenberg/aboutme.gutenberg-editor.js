(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - About me
	blocks.registerBlockType(
		'trx-addons/aboutme',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: About me', "trx_addons" ),
			description: __( "About me - photo and short description about the blog author", "trx_addons" ),
			icon: 'admin-users',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					title: {
						type: 'string',
						default: __( 'About me', "trx_addons" )
					},
					avatar: {
						type: 'number',
						default: 0
					},
					avatar_url: {
						type: 'string',
						default: ''
					},
					username: {
						type: 'string',
						default: ''
					},
					description: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/aboutme' ),
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
									'type': 'text'
								},
								// Avatar
								{
									'name': 'avatar',
									'name_url': 'avatar_url',
									'title': __( 'Avatar', "trx_addons" ),
									'descr': __( 'Avatar (if empty - get gravatar by admin email)', "trx_addons" ),
									'type': 'image'
								},
								// User name
								{
									'name': 'username',
									'title': __( 'User name', "trx_addons" ),
									'descr': __( 'User name (if equal to # - not show)', "trx_addons" ),
									'type': 'text'
								},
								// Description
								{
									'name': 'description',
									'title': __( 'Description', "trx_addons" ),
									'descr': __( 'Short description about user (if equal to # - not show)', "trx_addons" ),
									'type': 'textarea'
								}
							], 'trx-addons/aboutme', props ), props )
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
		'trx-addons/aboutme'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
