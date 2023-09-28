( function( blocks, i18n, element ) {
	"use strict";

	// Set up variables
	var el = element.createElement;


	// Add a param data type and default value
	//------------------------------------------------------------------------------

	/**
	 * Add new params to the JS-list of shortcode's attributes with default values for Gutenberg.
	 * 
	 * Hooks: trx_addons_add_filter( 'trx_addons_gb_map_get_params', '_trx_addons_sc_get_params_gb_map' );
	 * 
	 * @param array atts    An array with types and default values of all supported attrubutes.
	 * @param string sc_gb  The name of the Gutenberg block whose values are being processed.
	 * 
	 * @return  An array with processed attributes.
	 */
	trx_addons_add_filter( 'trx_addons_gb_map_get_params', function( atts, sc_gb ) {
		if ( typeof TRX_ADDONS_SC_ADD_PARAMS == 'object' ) {
			var sc = _trx_addons_sc_add_params_gb_to_sc_name( sc_gb.slice(-5) == '-item' ? sc_gb.slice(0, -5) : sc_gb );
			for ( var i = 0; i < TRX_ADDONS_SC_ADD_PARAMS.length; i++ ) {
				var shortcode = TRX_ADDONS_SC_ADD_PARAMS[i];
				// If we have a new params for the current shortcode
				if ( shortcode['sc'].indexOf( sc ) != -1 && _trx_addons_sc_add_params_check_builder( 'gutenberg', shortcode['builder'] ) ) {
					if ( sc_gb.slice(-5) != '-item' && ( ! shortcode.hasOwnProperty( 'inside' ) || ! shortcode['inside'] )	// To main params
						||																										// or
						sc_gb.slice(-5) == '-item' && shortcode.hasOwnProperty( 'inside' ) && shortcode['inside']			// To repeater param
					) {
						// Append new params at the end of array - for this array, the insertion position of the parameters does not matter
						atts = Object.assign( atts, _trx_addons_sc_add_params_get_gb_params( shortcode ) );
					}
				}
			}
		}
		return atts;
	} );

	/**
	 * Convert a shortcode params to the Gutenberg-compatible format with type and default value of the field.
	 * 
	 * @param array atts  An array with a shortcode attributes in the our internal format to convert it to the Gutenberg-compatible format.
	 * 
	 * @return array  A converted shortcode's attributes in the Gutenberg-compatible format.
	 */
	function _trx_addons_sc_add_params_get_gb_params( shortcode )  {
		var gb_atts = {};
		for ( var param_name in shortcode['params'] ) {
			var params = shortcode['params'][ param_name ];
			var param_type = _trx_addons_sc_add_params_get_gb_param_type( params );
			if ( param_type === '' ) {
				continue;
			}
			var cur_atts = {
				'type': param_type
			};
			// Add default value
			if ( params.hasOwnProperty( 'default' ) ) {
				cur_atts['default'] = ['checkbox', 'switch'].indexOf( params['type'] ) != -1
										? !!params['default']
										: ( ['image', 'audio', 'video', 'media'].indexOf( params['type'] ) != -1
											? 0
											: params['default']
											);
			} else {
				cur_atts['default'] = ['checkbox', 'switch'].indexOf( params['type'] ) != -1 ? false : '';
			}
			gb_atts[ param_name ] = Object.assign( {}, cur_atts );
			// Add extra param with suffix '_url' for all media types
			if ( ['image', 'audio', 'video', 'media'].indexOf( params['type'] ) != -1 ) {
				gb_atts[ param_name + '_url' ] = {
					'type': 'string',
					'default': ''
				};
			}
		}
		return trx_addons_apply_filters( 'trx_addons_filter_sc_add_params_get_gb_params', gb_atts, shortcode );
	}

	/**
	 * Convert a shortcode param's type to the Gutenberg-compatible data type.
	 * 
	 * @param array params  An array with a shortcode attributes.
	 * 
	 * @return string  A Gutenberg-compatible data type.
	 */
	function _trx_addons_sc_add_params_get_gb_param_type( params )  {
		var gb_types = trx_addons_apply_filters( 'trx_addons_filter_gb_param_types', {
			// Gutenberg core types
			'text':      'string',
			'textarea':  'string',
			'checkbox':  'boolean',
			'color':     'string',
			'image':     'number',
			'audio':     'number',
			'video':     'number',
			'media':     'number',
			'icon':      'string',
			'icons':     'string',
			// Substitute our types with core types
			'checklist': 'string',
			'switch':    'boolean',
			'number':    'number',
			'slider':    'number',
			'range':     'number',
			'url':       'string',
			// Custom types
			'radio':     'string',
			'icons':     'string',
			'select':    'string',
			// Types to skip (not supported in Gutenberg)
			'heading':   ''
		} );
		return trx_addons_apply_filters( 'trx_addons_filter_sc_add_params_get_gb_param_type', gb_types.hasOwnProperty( params['type'] ) ? gb_types[ params['type'] ] : params['type'], params );
	}


	// Add a complete field settings
	//--------------------------------------------------------------

	/**
	 * Add new fields to the JS-object of shortcode for Gutenberg.
	 * 
	 * Hooks: trx_addons_add_filter( 'trx_addons_gb_map_add_params', '_trx_addons_sc_add_params_gb_map' );
	 * 
	 * @param array atts     An array with types and default values of all supported attrubutes.
	 * @param string sc_gb   The name of the Gutenberg block whose values are being processed.
	 * @param object props   An object with the block properties.
	 * 
	 * @return  An array with processed fields.
	 */
	trx_addons_add_filter( 'trx_addons_gb_map_add_params', function( atts, sc_gb, props ) {
		var sc = _trx_addons_sc_add_params_gb_to_sc_name( sc_gb.slice(-5) == '-item' ? sc_gb.slice(0, -5) : sc_gb );
		for ( var i = 0; i < TRX_ADDONS_SC_ADD_PARAMS.length; i++ ) {
			var shortcode = TRX_ADDONS_SC_ADD_PARAMS[i];
			// If we have a new params for the current shortcode
			if ( shortcode['sc'].indexOf( sc ) != -1 && _trx_addons_sc_add_params_check_builder( 'gutenberg', shortcode['builder'] ) ) {
				if ( sc_gb.slice(-5) != '-item' && ( ! shortcode.hasOwnProperty( 'inside' ) || ! shortcode['inside'] )	// To main params
					||																										// or
					sc_gb.slice(-5) == '-item' && shortcode.hasOwnProperty( 'inside' ) && shortcode['inside']			// To repeater param
				) {
					// Add new fields to the specified insertion position (before or after the specified parameter)
					var pos = _trx_addons_sc_add_params_get_insert_point( atts, shortcode['before'], shortcode['after'] );
					atts = [].concat( atts.slice( 0, pos ), _trx_addons_sc_add_params_get_gb_fields( shortcode ), atts.slice( pos ) );
				}
			}
		}
		return atts;
	} );

	/**
	 * Add the property 'key' to each field of shortcode for Gutenberg.
	 * 
	 * Hooks: trx_addons_add_filter( 'trx_addons_gb_map_add_params', '_trx_addons_sc_add_params_gb_keys' );
	 * 
	 * @param array atts     An array with types and default values of all supported attrubutes.
	 * @param string sc_gb   The name of the Gutenberg block whose values are being processed.
	 * @param object props   An object with the block properties.
	 * 
	 * @return  An array with processed fields.
	 */
/*
	trx_addons_add_filter( 'trx_addons_gb_map_add_params', function( atts, sc_gb, props ) {
		for ( var i = 0; i < atts.length; i++ ) {
			if ( ! atts[i].hasOwnProperty( 'key' ) ) {
				atts[i]['key'] = atts[i].hasOwnProperty( 'name' ) ? atts[i]['name'] : 'key_' + i;
			}
		}
		return atts;
	} );
*/

	/**
	 * Convert a shortcode params to the Gutenberg-compatible format with a complete field settings.
	 * 
	 * @param array atts  An array with a shortcode attributes in the our internal format to convert it to the Gutenberg-compatible format.
	 * 
	 * @return array  A converted shortcode's attributes in the Gutenberg-compatible format.
	 */
	function _trx_addons_sc_add_params_get_gb_fields( shortcode )  {
		var gb_atts = [];
		for ( var param_name in shortcode['params'] ) {
			var params = shortcode['params'][ param_name ];
			var param_type = _trx_addons_sc_add_params_get_gb_field_type( params );
			if ( param_type === '' ) {
				continue;
			}
			var cur_atts = {
				'name': param_name,
				'type': param_type,
				'title': params.hasOwnProperty( 'title' ) ? params['title'] : '',
				'descr': params.hasOwnProperty( 'description' ) ? params['description'] : '',
			};
			// Name for URL for media types
			if ( ['image', 'audio', 'video', 'media'].indexOf( params['type'] ) != -1 ) {
				cur_atts['name_url'] = param_name + '_url';
			}
			// Prepare option list
			if ( params.hasOwnProperty( 'options' ) ) {
				cur_atts['options'] = trx_addons_gutenberg_get_lists( params['options'] );
			} else if ( ['icon', 'icons'].indexOf( params['type'] ) != -1 ) {
				cur_atts['options'] = trx_addons_gutenberg_get_option_icons_classes();
			}
			// Add specific params for some shortcodes
			if ( params.hasOwnProperty( 'min' ) ) {
				cur_atts['min'] = params['min'];
			}
			if ( params.hasOwnProperty( 'max' ) ) {
				cur_atts['max'] = params['max'];
			}
			if ( params.hasOwnProperty( 'step' ) ) {
				cur_atts['step'] = params['step'];
			}
			// Add specific params for some shortcodes
			if ( params.hasOwnProperty( 'mode' ) ) {
				cur_atts['mode'] = params['mode'];
			}
			if ( params.hasOwnProperty( 'style' ) ) {
				cur_atts['style'] = params['style'];
			}
			if ( params.hasOwnProperty( 'return' ) ) {
				cur_atts['return'] = params['return'];
			}
			if ( params.hasOwnProperty( 'multiple' ) ) {
				cur_atts['multiple'] = params['multiple'];
			}
			if ( params.hasOwnProperty( 'rows' ) ) {
				cur_atts['rows'] = params['rows'];
			}
			// Convert dependencies to Gutenberg-specific format
			if ( params.hasOwnProperty( 'dependency' ) && typeof( params['dependency'] ) == 'object' ) {
				cur_atts['dependency'] = Object.assign( {}, params['dependency'] );
			}
			gb_atts.push( cur_atts );
		}
		return trx_addons_apply_filters( 'trx_addons_filter_sc_add_params_add_gb_params', gb_atts, shortcode );
	}

	/**
	 * Convert a shortcode param's type to the Gutenberg-compatible field type.
	 * 
	 * @param array params  An array with a shortcode attributes.
	 * 
	 * @return string  A Gutenberg-compatible field type.
	 */
	function _trx_addons_sc_add_params_get_gb_field_type( params )  {
		var gb_types = trx_addons_apply_filters( 'trx_addons_filter_gb_field_types', {
			// Gutenberg core types
			'text':      'text',
			'textarea':  'textarea',
			'checkbox':  'boolean',
			'color':     'color',
			'image':     'image',
			'audio':     'text',
			'video':     'text',
			'icon':      'select',
			'icons':     'select',
			'radio':     'radio',
			'select':    'select',
			// Substitute our types with core types
			'checklist': 'select',
			'switch':    'boolean',
			'number':    'number',
			'slider':    'number',
			'range':     'number',
			'url':       'text',
			// Types to skip (not supported in Gutenberg)
			'heading':   ''
		} );
		return trx_addons_apply_filters( 'trx_addons_filter_sc_add_params_get_gb_field_type', gb_types.hasOwnProperty( params['type'] ) ? gb_types[ params['type'] ] : params['type'], params );
	}


	// Utilities
	//---------------------------------------------------------

	/**
	 * Check if a specified builder is one of allowed builders.
	 * 
	 * @param string builder  Name of the builder to check.
	 * @param array  allowed  Array of allowed builders.
	 * 
	 * @return boolean  Return true if a specified builder is one of allowed builders.
	 */
	function _trx_addons_sc_add_params_check_builder( builder, allowed ) {
		return allowed.indexOf( builder ) != -1 || allowed.indexOf( 'all' ) != -1;
	}

	/**
	 * Convert a shortcode name from Gutenberg format to the our internal format.
	 * 
	 * For example: 'trx-addons/actions' -> 'trx_sc_actions'
	 * 
	 *              'trx-addons/layouts-iconed-text' -> 'trx_sc_layouts_iconed_text'
	 * 
	 * @param array atts  An array with types and default values of all supported attrubutes.
	 * @param string sc   The name of the shortcode whose values are being processed.
	 * 
	 * @return  An array with processed attributes.
	 */
	function _trx_addons_sc_add_params_gb_to_sc_name( gb_name )  {
		return trx_addons_apply_filters( 'trx_addons_filter_gb_to_sc_name', gb_name.replace( 'trx-addons/', 'trx_sc_' ).replace( /\-/g, '_' ) );
	}

	/**
	 * Detect an index of the array with shortcode's attributes to insert a new params.
	 * 
	 * @param array atts     An array with a shortcode attributes.
	 * @param string before  A name of the existing attribute to insert a new params before it.
	 * @param string after   A name of the existing attribute to insert a new params after it.
	 *                       If both 'before' and 'after' are empty - return an end of the array index.
	 * 
	 * @return integer  An index of the array with attributes to insert a new params.
	 */
	function _trx_addons_sc_add_params_get_insert_point( atts, before, after )  {
		var i;
		for ( i = 0; i < atts.length; i++ ) {
			if ( before !== '' && atts[ i ][ 'name' ] == before ) {
				break;
			} else if ( after !== '' && atts[ i ][ 'name' ] == after ) {
				i++;
				break;
			}
		}
		return i;
	}

} )( window.wp.blocks, window.wp.i18n, window.wp.element );
