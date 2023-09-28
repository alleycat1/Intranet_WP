(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Properties
	blocks.registerBlockType(
		'trx-addons/properties',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Properties', "trx_addons" ),
			icon: 'admin-multisite',
			category: 'trx-addons-cpt',
			attributes: trx_addons_apply_filters( 'trx_addons_gb_map_get_params', trx_addons_object_merge(
				{
					type: {
						type: 'string',
						default: 'default'
					},
					more_text: {
						type: 'string',
						default: __( 'Read more', "trx_addons" ),
					},
					pagination: {
						type: 'string',
						default: 'none'
					},
					map_height: {
						type: 'number',
						default: 350
					},
					properties_type: {
						type: 'string',
						default: '0'
					},
					properties_status: {
						type: 'string',
						default: '0'
					},
					properties_labels: {
						type: 'string',
						default: '0'
					},
					properties_country: {
						type: 'string',
						default: '0'
					},
					properties_city: {
						type: 'string',
						default: '0'
					},
					properties_neighborhood: {
						type: 'string',
						default: '0'
					}
				},
				trx_addons_gutenberg_get_param_query(),
				trx_addons_gutenberg_get_param_slider(),
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/properties' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['trx_sc_properties'] )
								},
								// 'More' text
								{
									'name': 'more_text',
									'title': __( "'More' text", "trx_addons" ),
									'descr': __( "Specify caption of the 'Read more' button. If empty - hide button", "trx_addons" ),
									'type': 'text',
								},
								// Pagination
								{
									'name': 'pagination',
									'title': __( 'Pagination', "trx_addons" ),
									'descr': __( "Add pagination links after posts. Attention! Pagination is not allowed if the slider layout is used.", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_paginations'] )
								},
								//  Map height
								{
									'name': 'map_height',
									'title': __( "Map height", "trx_addons" ),
									'descr': __( "Specify height of the map with properties", "trx_addons" ),
									'type': 'number',
									'min': 100,
									'dependency': {
										'type': ['map']
									}
								},
								// Type
								{
									'name': 'properties_type',
									'title': __( 'Type', "trx_addons" ),
									'descr': __( "Select the type to show properties that have it!", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_properties_type'] )
								},
								// Status
								{
									'name': 'properties_status',
									'title': __( 'Status', "trx_addons" ),
									'descr': __( "Select the status to show properties that have it", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_properties_status'] )
								},
								// Label
								{
									'name': 'properties_labels',
									'title': __( 'Label', "trx_addons" ),
									'descr': __( "Select the label to show properties that have it", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_properties_labels'] )
								},
								// Country
								{
									'name': 'properties_country',
									'title': __( 'Country', "trx_addons" ),
									'descr': __( "Select the country to show properties from", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_properties_country'] )
								},
								// State
								{
									'name': 'properties_state',
									'title': __( 'State', "trx_addons" ),
									'descr': __( "Select the county/state to show properties from", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_properties_states'] )
								},
								// City
								{
									'name': 'properties_city',
									'title': __( 'City', "trx_addons" ),
									'descr': __( "Select the city to show properties from it", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_properties_cities'] )
								},
								// Neighborhood
								{
									'name': 'properties_neighborhood',
									'title': __( 'Neighborhood', "trx_addons" ),
									'descr': __( "Select the neighborhood to show properties from", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_properties_neighborhoods'] )
								}
							], 'trx-addons/properties', props ), props )
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
		'trx-addons/properties'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
