(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Countdown
	blocks.registerBlockType(
		'trx-addons/countdown',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Countdown', "trx_addons" ),
			description: __( "Put the countdown to the specified date and time", "trx_addons" ),
			icon: 'clock',
			category: 'trx-addons-blocks',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					align: {
						type: 'string',
						default: 'none'
					},
					count_restart: {
						type: 'boolean',
						default: false
					},
					count_to: {
						type: 'boolean',
						default: true
					},
					date: {
						type: 'string',
						default: ''
					},
					time: {
						type: 'string',
						default: ''
					},
					date_time_restart: {
						type: 'string',
						default: ''
					},
					// Reload block - hidden option
					reload: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/countdown' ),
			edit: function(props) {
				return trx_addons_gutenberg_block_params(
					{
						'render': true,
						'render_button': true,
						'general_params': el( wp.element.Fragment, {},
							trx_addons_gutenberg_add_params( trx_addons_apply_filters( 'trx_addons_gb_map_add_params', [
								// Layout
								{
									'name': 'type',
									'title': __( 'Layout', "trx_addons" ),
									'descr': __( "Select shortcodes's layout", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['sc_countdown'] )
								},
								// Alignment
								{
									'name': 'align',
									'title': __( 'Alignment', "trx_addons" ),
									'descr': __( "Select alignment of the countdown", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_aligns'] )
								},
								// Restart counter
								{
									'name': 'count_restart',
									'title': __( 'Restart counter', "trx_addons" ),
									'descr': __( "If checked - restart count from/to time on each page loading", "trx_addons" ),
									'type': 'boolean'
								},
								// Count to
								{
									'name': 'count_to',
									'title': __( 'Count to', "trx_addons" ),
									'descr': __( "If checked - date above is a finish date, else - is a start date", "trx_addons" ),
									'type': 'boolean'
								},
								// Date
								{
									'name': 'date',
									'title': __( 'Date', "trx_addons" ),
									'descr': __( "Target date. Attention! Write the date in the format 'yyyy-mm-dd'", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'count_restart': [ false ]
									}
								},
								// Time
								{
									'name': 'time',
									'title': __( 'Time', "trx_addons" ),
									'descr': __( "Target time. Attention! Put the time in the 24-hours format 'hh:mm:ss'", "trx_addons" ),
									'type': 'text',
									'dependency': {
										'count_restart': [ false ]
									}
								},
								// Time
								{
									'name': 'date_time_restart',
									'title': __( 'Time to restart', "trx_addons" ),
									'descr': __( 'Specify start value of timer with format "[DD:]HH:MM[:SS]"', "trx_addons" ),
									'type': 'text',
									'dependency': {
										'count_restart': [ true ]
									}
								}
							], 'trx-addons/countdown', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Title params
							trx_addons_gutenberg_add_param_title( props, true ),
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
		'trx-addons/countdown'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
