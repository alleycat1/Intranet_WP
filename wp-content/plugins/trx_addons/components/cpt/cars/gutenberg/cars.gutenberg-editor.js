(function(blocks, i18n, element) {

	// Set up variables
	var el = element.createElement,
		__ = i18n.__;

	// Register Block - Cars
	blocks.registerBlockType(
		'trx-addons/cars',
		trx_addons_apply_filters( 'trx_addons_gb_map', {
			title: __( 'Cars', "trx_addons" ),
			icon: 'format-aside',
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
					more_text: {
						type: 'string',
						default: __( 'Read more', "trx_addons" ),
					},
					cars_type: {
						type: 'string',
						default: '0'
					},
					cars_maker: {
						type: 'string',
						default: '0'
					},
					cars_model: {
						type: 'string',
						default: '0'
					},
					cars_status: {
						type: 'string',
						default: '0'
					},
					cars_labels: {
						type: 'string',
						default: '0'
					},
					cars_city: {
						type: 'string',
						default: '0'
					},
					cars_transmission: {
						type: 'string',
						default: ''
					},
					cars_type_drive: {
						type: 'string',
						default: ''
					},
					cars_fuel: {
						type: 'string',
						default: ''
					}
				},
				trx_addons_gutenberg_get_param_query(),
				trx_addons_gutenberg_get_param_slider(),
				trx_addons_gutenberg_get_param_title(),
				trx_addons_gutenberg_get_param_button(),
				trx_addons_gutenberg_get_param_id()
			), 'trx-addons/cars' ),
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
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_layouts']['trx_sc_cars'] )
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
								// Type
								{
									'name': 'cars_type',
									'title': __( 'Type', "trx_addons" ),
									'descr': __( "Select the type to show cars that have it!", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_cars_type'] )
								},
								// Manufacturer
								{
									'name': 'cars_maker',
									'title': __( 'Manufacturer', "trx_addons" ),
									'descr': __( "Select the car's manufacturer", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_cars_maker'] )
								},
								// Model
								{
									'name': 'cars_model',
									'title': __( 'Model', "trx_addons" ),
									'descr': __( "Select the car's model", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_cars_model'] )
								},
								// Status
								{
									'name': 'cars_status',
									'title': __( 'Status', "trx_addons" ),
									'descr': __( "Select the status to show cars that have it", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_cars_status'] )
								},
								// Label
								{
									'name': 'cars_labels',
									'title': __( 'Label', "trx_addons" ),
									'descr': __( "Select the label to show cars that have it", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_cars_labels'] )
								},
								// City
								{
									'name': 'cars_city',
									'title': __( 'City', "trx_addons" ),
									'descr': __( "Select the city to show cars from it", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_cars_city'] )
								},
								// Transmission
								{
									'name': 'cars_transmission',
									'title': __( 'Transmission', "trx_addons" ),
									'descr': __( "Select type of the transmission", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_cars_transmission'] )
								},
								// Type of drive
								{
									'name': 'cars_type_drive',
									'title': __( 'Type of drive', "trx_addons" ),
									'descr': __( "Select type of drive", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_cars_type_drive'] )
								},
								// Fuel
								{
									'name': 'cars_fuel',
									'title': __( 'Fuel', "trx_addons" ),
									'descr': __( "Select type of the fuel", "trx_addons" ),
									'type': 'select',
									'options': trx_addons_gutenberg_get_lists( TRX_ADDONS_STORAGE['gutenberg_sc_params']['sc_cars_fuel'] )
								}
							], 'trx-addons/cars', props ), props )
						),
						'additional_params': el( wp.element.Fragment, { key: props.name + '-additional-params' },
							// Query params
							trx_addons_gutenberg_add_param_query( props ),
							// Title & Button params
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
		'trx-addons/cars'
	) );
})( window.wp.blocks, window.wp.i18n, window.wp.element );
