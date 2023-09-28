(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Testimonials
	blocks.registerBlockType(
		'trx-addons/testimonials',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Testimonials', "trx_addons" ),
			icon: 'format-status',
			category: 'trx-addons-cpt',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					rating: {
						type: 'boolean',
						default: false
					},
					use_initials: {
						type: 'boolean',
						default: false
					},
					cat: {
						type: 'string',
						default: '0'
					},
					slider_pagination_thumbs: {
						type: 'boolean',
						default: false
					}
				},
				trx_addons_gutenberg_get_param_query(),
				trx_addons_gutenberg_get_param_slider(),
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/testimonials' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['trx_sc_testimonials'] )
								},
								// Show rating
								{
									'name': 'rating',
									'title': __( "Show rating", "trx_addons" ),
									'descr': __( "Display rating stars", "trx_addons" ),
									'type': 'boolean',
								},
								// Use initials
								{
									'name': 'use_initials',
									'title': __( "Use initials", "trx_addons" ),
									'descr': __( "If no avatar is present, the initials derived from the available username will be used.", "trx_addons" ),
									'type': 'boolean',
								},
								// Group
								{
									'name': 'cat',
									'title': __( "Group", "trx_addons" ),
									'descr': __( "Courses group", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_testimonials_cat'] )
								},
								// Slider pagination
								{
									'name': 'slider_pagination_thumbs',
									'title': __( "Slider pagination", "trx_addons" ),
									'descr': __( "Show thumbs as pagination bullets", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'slider_pagination': ['left', 'right', 'bottom', 'bottom_outside']
									}
								}
							], 'trx-addons/testimonials', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Query params
							trx_addons_gutenberg_add_param_query( props ),
							// Title params
							trx_addons_gutenberg_add_param_title( props, true ),
							// Slider params
							trx_addons_gutenberg_add_param_slider( props ),
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
		'trx-addons/testimonials'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
