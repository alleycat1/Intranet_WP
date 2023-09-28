(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Calendar
	blocks.registerBlockType(
		'trx-addons/calendar',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Widget: Calendar', "trx_addons" ),
			description: __( "Insert standard WP Calendar, but allow user select week day's captions", "trx_addons" ),
			icon: 'calendar-alt',
			category: 'trx-addons-widgets',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					title: {
						type: 'string',
						default: __( 'Calendar', "trx_addons" )
					},
					weekdays: {
						type: 'string',
						default: "short"
					}
				},
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/calendar' ),
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
								// Week days
								{
									'name': 'weekdays',
									'title': __( 'Week days', "trx_addons" ),
									'descr': __( "Show captions for the week days as three letters (Sun, Mon, etc.) or as one initial letter (S, M, etc.)", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists({
										'short': __( 'Short', "trx_addons" ),
										'initial': __( 'Initial', "trx_addons" ),
									} )
								}
							], 'trx-addons/calendar', props ), props )
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
		'trx-addons/calendar'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
