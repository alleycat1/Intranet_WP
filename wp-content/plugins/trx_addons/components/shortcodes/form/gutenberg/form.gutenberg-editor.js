(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Form
	blocks.registerBlockType(
		'trx-addons/form',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Form', "trx_addons" ),
			description: __( "Insert simple or detailed form", "trx_addons" ),
			icon: 'email-alt',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					style: {
						type: 'string',
						default: 'inherit'
					},
					align: {
						type: 'string',
						default: 'default'
					},
					email: {
						type: 'string',
						default: ''
					},
					phone: {
						type: 'string',
						default: ''
					},
					address: {
						type: 'string',
						default: ''
					},
					button_caption: {
						type: 'string',
						default: ''
					},
					labels: {
						type: 'boolean',
						default: false
					}
				},
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/form' ),
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
									'descr': __( "Select shortcodes's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_form'] )
								},
								// Style
								{
									'name': 'style',
									'title': __( 'Style', "trx_addons" ),
									'descr': __( "Select input's style", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['input_hover'] )
								},
								// Fields alignment
								{
									'name': 'align',
									'title': __( 'Fields alignment', "trx_addons" ),
									'descr': __( "Select alignment of the field's text", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
								},
								// Your E-mail
								{
									'name': 'email',
									'title': __( 'Your E-mail', "trx_addons" ),
									'descr': __( "Specify your E-mail for the detailed form. This address will be used to send you filled form data. If empty - admin e-mail will be used", "trx_addons" ),
									'type': 'text'
								},
								// Your phone
								{
									'name': 'phone',
									'title': __( 'Your phone', "trx_addons" ),
									'descr': __( "Specify your phone for the detailed form", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'type': ['modern', 'detailed']
									}
								},
								// Your address
								{
									'name': 'address',
									'title': __( 'Your address', "trx_addons" ),
									'descr': __( "Specify your address for the detailed form", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'type1': ['modern', 'detailed']
									}
								},
								// Button caption
								{
									'name': 'button_caption',
									'title': __( 'Button caption', "trx_addons" ),
									'descr': __( 'Caption of the "Send" button', "trx_addons" ),
									'type': 'text'
								},
								// Field labels
								{
									'name': 'labels',
									'title': __( 'Field labels', "trx_addons" ),
									'descr': __( "Show field's labels", "trx_addons" ),
									'type': 'boolean'
								}
							], 'trx-addons/form', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Title params
							trx_addons_gutenberg_add_param_title( props, false ),
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
		'trx-addons/form'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
