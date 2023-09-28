(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Banner
	blocks.registerBlockType(
		'trx-addons/banner',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Banner', "trx_addons" ),
			description: __( "Banner with image and/or any html and js code", "trx_addons" ),
			icon: 'format-image',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					title: {
						type: 'string',
						default: ''
					},
					fullwidth: {
						type: 'boolean',
						default: false
					},
					show: {
						type: 'string',
						default: 'permanent'
					},
					image: {
						type: 'number',
						default: 0
					},
					image_url: {
						type: 'string',
						default: ''
					},
					link: {
						type: 'string',
						default: ''
					},
					code: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/banner' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Title
								{
									'name': 'title',
									'title': __( 'Title', "trx_addons" ),
									'type': 'text',
								},
								// Widget size
								{
									'name': 'fullwidth',
									'title': __( 'Widget size:', "trx_addons" ),
									'descr': __( "Stretch the width of the element to the full screen's width", "trx_addons" ),
									'type': 'boolean'
								},
								// Show on
								{
									'name': 'show',
									'title': __( 'Show on:', "trx_addons" ),
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_banner_show_on'] ),
									'type': 'select',
								},
								// Image source URL
								{
									'name': 'image',
									'name_url': 'image_url',
									'title': __( 'Image source URL:', "trx_addons" ),
									'type': 'image'
								},
								// Image link URL
								{
									'name': 'link',
									'title': __( 'Image link URL:', "trx_addons" ),
									'type': 'text',
								},
								// Paste HTML Code
								{
									'name': 'code',
									'title': __( 'Paste HTML Code:', "trx_addons" ),
									'type': 'textarea',
								}
							], 'trx-addons/banner', props ), props )
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
		'trx-addons/banner'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
