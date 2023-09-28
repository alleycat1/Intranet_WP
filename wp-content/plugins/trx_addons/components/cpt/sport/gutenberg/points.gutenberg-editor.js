(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Points
	blocks.registerBlockType(
		'trx-addons/points',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Points', "trx_addons" ),
			icon: 'universal-access',
			category: 'trx-addons-cpt',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					sport: {
						type: 'string',
						default:  TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_sport_default']
					},
					competition: {
						type: 'string',
						default: '0'
					},
					logo: {
						type: 'boolean',
						default: false
					},
					accented_top: {
						type: 'number',
						default: 3
					},
					accented_bottom: {
						type: 'number',
						default: 3
					}
				},
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/points' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['trx_sc_points'] )
								},
								// Sport
								{
									'name': 'sport',
									'title': __( 'Sport', "trx_addons" ),
									'descr': __( "Select Sport to display matches", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_sports_list'] ),
								},
								// Competition
								{
									'name': 'competition',
									'title': __( 'Competition', "trx_addons" ),
									'descr': __( "Select competition to display matches", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_sport_competitions_list'][props.attributes.sport], true ),
								},
								// Logo
								{
									'name': 'logo',
									'title': __( "Logo", "trx_addons" ),
									'descr': __( "Show logo (players photo) in the table", "trx_addons" ),
									'type': 'boolean',
								},
								// Accented top
								{
									'name': 'accented_top',
									'title': __( "Accented top", "trx_addons" ),
									'descr': __( "How many rows should be accented at the top of the table?", "trx_addons" ),
									'type': 'number',
									'min': 0
								},
								// Accented bottom
								{
									'name': 'accented_bottom',
									'title': __( "Accented bottom", "trx_addons" ),
									'descr': __( "How many rows should be accented at the bottom of the table?", "trx_addons" ),
									'type': 'number',
									'min': 0
								}
							], 'trx-addons/points', props ), props )
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
		'trx-addons/points'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
