(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Courses
	blocks.registerBlockType(
		'trx-addons/courses',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Courses', "trx_addons" ),
			icon: 'welcome-learn-more',
			category: 'trx-addons-cpt',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					pagination: {
						type: 'string',
						default: 'none'
					},
					no_margin: {
						type: 'boolean',
						default: false
					},
					more_text: {
						type: 'string',
						default: __( 'Read more', "trx_addons" ),
					},
					past: {
						type: 'boolean',
						default: false
					},
					cat: {
						type: 'string',
						default: '0'
					}
				},
				trx_addons_gutenberg_get_param_query(),
				trx_addons_gutenberg_get_param_slider(),
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/courses' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['trx_sc_courses'] )
								},
								// Remove margin
								{
									'name': 'no_margin',
									'title': __( "Remove margin", "trx_addons" ),
									'descr': __( "Check if you want remove spaces between columns", "trx_addons" ),
									'type': 'boolean',
								},
								// Pagination
								{
									'name': 'pagination',
									'title': __( 'Pagination', "trx_addons" ),
									'descr': __( "Add pagination links after posts. Attention! Pagination is not allowed if the slider layout is used.", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_paginations'] )
								},
								// 'More' text
								{
									'name': 'more_text',
									'title': __( "'More' text", "trx_addons" ),
									'descr': __( "Specify caption of the 'Read more' button. If empty - hide button", "trx_addons" ),
									'type': 'text',
								},
								// Past courses
								{
									'name': 'past',
									'title': __( "Past courses", "trx_addons" ),
									'descr': __( "Show the past courses if checked, otherwise - show upcoming courses.", "trx_addons" ),
									'type': 'boolean',
								},
								// Group
								{
									'name': 'cat',
									'title': __( "Group", "trx_addons" ),
									'descr': __( "Courses group", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_courses_cat'] )
								}
							], 'trx-addons/courses', props ), props )
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
		'trx-addons/courses'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
