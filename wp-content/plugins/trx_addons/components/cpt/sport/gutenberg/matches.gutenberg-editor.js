(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Matches
	blocks.registerBlockType(
		'trx-addons/matches',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Matches', "trx_addons" ),
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
					round: {
						type: 'string',
						default: '0'
					},
					main_matches: {
						type: 'boolean',
						default: false
					},
					position: {
						type: 'string',
						default: 'top'
					},
					slider: {
						type: 'boolean',
						default: false
					}
				},
				trx_addons_gutenberg_get_param_query(),
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/matches' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['trx_sc_matches'] )
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
								// Round
								{
									'name': 'round',
									'title': __( 'Round', "trx_addons" ),
									'descr': __( "Select round to display matches", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_sport_rounds_list'][props.attributes.competition], true )
								},
								// Main matches
								{
									'name': 'main_matches',
									'title': __( 'Main matches', "trx_addons" ),
									'descr': __( "Show large items marked as main match of the round", "trx_addons" ),
									'type': 'boolean'
								},
								// Position of the matches list
								{
									'name': 'position',
									'title': __( 'Position of the matches list', "trx_addons" ),
									'descr': __( "Select the position of the matches list", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_sport_positions'] ),
									'dependency': {
										'main_matches': [true]
									}
								},
								// Slider
								{
									'name': 'slider',
									'title': __( "Slider", "trx_addons" ),
									'descr': __( "Show main matches as slider (if two and more)", "trx_addons" ),
									'type': 'boolean',
									'dependency': {
										'main_matches': [true]
									}
								}
							], 'trx-addons/matches', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Query params
							trx_addons_gutenberg_add_param_query( props ),
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
		'trx-addons/matches'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
