<?php
/**
 * ThemeREX Shortcodes additional params
 *
 * @package ThemeREX Addons
 * @since v2.12.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// A global array with additional types (layouts) for shortcodes output
//------------------------------------------------------------------------------
global $trx_addons_sc_add_types;
$trx_addons_sc_add_types = array();

if ( ! function_exists( 'trx_addons_sc_add_types' ) ) {
	/**
	 * Add a new types (layouts) of our shortcodes/blocks to the global list.
	 * 
	 * @param array $types  An array with additional types in the format:
	 *                      array(
	 *                          'sc1' => array(
	 *                                     'type_slug1' => 'Type 1 Name'
	 *                                     'type_slug2' => 'Type 2 Name'
	 *                                   ),
	 *                          'sc2' => array(
	 *                                      'type_slug3' => 'Type 3 Name'
	 *                                      'type_slug4' => 'Type 4 Name'
	 *                                   ),
	 *                          ...
	 *                      )
	 */
	function trx_addons_sc_add_types( $types ) {
		global $trx_addons_sc_add_types;
		if ( is_array( $types ) ) {
			foreach( $types as $sc => $list ) {
				if ( ! isset( $trx_addons_sc_add_types[ $sc ] ) ) {
					$trx_addons_sc_add_types[ $sc ] = array();
				}
				$trx_addons_sc_add_types[ $sc ] = trx_addons_array_merge( $trx_addons_sc_add_types[ $sc ], $list );
			}
		}
	}
}

if ( ! function_exists( '_trx_addons_sc_add_types_to_list' ) ) {
	add_filter( 'trx_addons_sc_type', '_trx_addons_sc_add_types_to_list', 10, 2 );
	/**
	 * Add new output types (layouts) to the type list for each supported shortcode.
	 * 
	 * Hooks: add_filter( 'trx_addons_sc_type', '_trx_addons_sc_add_types_to_list', 10, 2 );
	 * 
	 * @param array $list  An array with types of the specified shortcode.
	 * @param string $sc   The name of the processed shortcode.
	 * 
	 * @return  A modified array with types (layouts).
	 */
	function _trx_addons_sc_add_types_to_list( $list, $sc ) {
		global $trx_addons_sc_add_types;
		if ( ! empty( $trx_addons_sc_add_types[ $sc ] ) ) {
			$list = trx_addons_array_merge( $list, $trx_addons_sc_add_types[ $sc ] );
		}
		return $list;
	}
}


// A global array with additional parameters for our shortcodes
//---------------------------------------------------------------------
global $trx_addons_sc_add_params;
$trx_addons_sc_add_params = array();

if ( ! function_exists( 'trx_addons_sc_add_params' ) ) {
	/**
	 * Add a new params of the our shortcodes/blocks to the global list.
	 * 
	 * @param array $params  An array with additional params in the format:
	 *                       'sc'      Comma separated list or array with name of the shortcodes to add new params in.
	 *                       'builder' Comma separated list or array with name of builders.
	 *                                 Default is 'all' - new parameters should be added to all supported builders.
	 *                                 Allowed values are: 'all' | omitted, 'elementor' | 'elm', 'gutenberg' | 'gb', 'js_composer' | 'vc'
	 *                       'inside'  Name of the existing repeating parameter to insert new params inside it.
	 *                       'before'  Name of the existing parameter to insert new params before it.
	 *                       'after'   Name of the existing parameter to insert new params after it.
	 *                                 If 'before' and 'after' are empty - new parameters should be inserted to the end of the array with parameters.
	 *                       'group'   Name of the existing group parameter (for VC) to insert new params inside it 
	 *                                 (before or after the key, specified in parameters 'before' or 'after').
	 *                       'section' Name of the Elementor's section to add a new params in.
	 *                                 If a section's name start with 'new:' - a new section should be added.
	 *                       'tab'     Name of the tab to add a new section (only for Elementor).
	 *                       'params'  An array with new parameters in the format:
	 *                                 array(
	 *                                     'param1_name' => array(
	 *                                                        'title'       => 'Title of the parameter',
	 *                                                        'description' => 'Description of the parameter',
	 *                                                        'default'     => 'Default value of the parameter',
	 *                                                        'options'     => 'An array with options for parameters with type select, radio, etc.',
	 *                                                        'type'        => 'Type of the parameter'
	 *                                                        'min', 'max', 'units', 'return_value', 'edit_field_class' ... => A builder-specific parameters
	 *                                                      ),
	 *                                     'param2_name' => array(),
	 *                                     ...
	 *                                 )
	 */
	function trx_addons_sc_add_params( $params ) {
		global $trx_addons_sc_add_params;
		if ( is_array( $params ) ) {
			$params['sc'] = array_map( 'trim', is_array( $params['sc'] ) ? $params['sc'] : explode( ',', $params['sc'] ) );
			if ( ! isset( $params['before'] ) ) {
				$params['before'] = '';
			}
			if ( ! isset( $params['after'] ) ) {
				$params['after'] = '';
			}
			if ( empty( $params['builder'] ) ) {
				$params['builder'] = 'all';
			}
			$params['builder'] = array_map(
									'_trx_addons_sc_add_params_prepare_builder',
									is_array( $params['builder'] )
										? $params['builder']
										: array_map( 'trim', explode( ',', $params['builder'] ) )
								);
			$trx_addons_sc_add_params[] = $params;
		}
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_prepare_builder' ) ) {
	/**
	 * Prepare a standard builder name for one of allowed builders.
	 * 
	 * @param string $builder  Name of the builder to check.
	 * 
	 * @return string  An allowed builder name or empty string.
	 */
	function _trx_addons_sc_add_params_prepare_builder( $builder ) {
		if ( in_array( $builder, array( 'elm', 'elementor' ) ) ) {
			$builder = 'elementor';
		} else if ( in_array( $builder, array( 'gb', 'gutenberg' ) ) ) {
			$builder = 'gutenberg';
		} else if ( in_array( $builder, array( 'vc', 'js_composer' ) ) ) {
			$builder = 'vc';
		} else {
			$builder = 'all';
		}
		return $builder;
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_check_builder' ) ) {
	/**
	 * Check if a specified builder is one of allowed builders.
	 * 
	 * @param string $builder  Name of the builder to check.
	 * @param array  $allowed  Array of allowed builders.
	 * 
	 * @return boolean  Return true if a specified builder is one of allowed builders.
	 */
	function _trx_addons_sc_add_params_check_builder( $builder, $allowed ) {
		return in_array( $builder, $allowed ) || in_array( 'all', $allowed );
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_sc_atts' ) ) {
	add_filter( 'trx_addons_sc_atts', '_trx_addons_sc_add_params_sc_atts', 10, 2 );
	/**
	 * Add new default values to the main shortcode's attributes.
	 * 
	 * Hooks: add_filter( 'trx_addons_sc_atts', '_trx_addons_sc_add_params_sc_atts', 10, 2 );
	 * 
	 * @param array $atts         An array with the default values of all supported attrubutes.
	 * @param string | array $sc  The name of the shortcode whose values are being processed.
	 * 
	 * @return  An array with processed default values.
	 */
	function _trx_addons_sc_add_params_sc_atts( $atts, $sc )  {
		global $trx_addons_sc_add_params;
		foreach ( $trx_addons_sc_add_params as $shortcode ) {
			if ( in_array( $sc, $shortcode['sc'] ) ) {
				foreach( $shortcode['params'] as $param => $settings ) {
					$atts[ $param ] = ! empty( $settings['default'] ) ? $settings['default'] : '';
				}
			}
		}
		return $atts;
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_sc_common_atts' ) ) {
	add_filter( 'trx_addons_filter_sc_common_atts', '_trx_addons_sc_add_params_sc_common_atts', 10, 2 );
	/**
	 * Add new default values to the common shortcode's attributes.
	 * 
	 * Hooks: add_filter( 'trx_addons_filter_sc_common_atts', '_trx_addons_sc_add_params_sc_common_atts', 10, 2 );
	 * 
	 * @param array $atts         An array with the default values of common attrubutes.
	 * @param string | array $sc  The name of the shortcode whose values are being processed.
	 * 
	 * @return  An array with processed default values of common attributes.
	 */
	function _trx_addons_sc_add_params_sc_common_atts( $atts, $common )  {
		global $trx_addons_sc_add_params;
		foreach ( $trx_addons_sc_add_params as $shortcode ) {
			foreach ( $common as $sc ) {
				if ( in_array( "common/{$sc}", $shortcode['sc'] ) ) {
					foreach( $shortcode['params'] as $param => $settings ) {
						$atts[ $param ] = ! empty( $settings['default'] ) ? $settings['default'] : '';
					}
				}
			}
		}
		return $atts;
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_sc_prepare_atts' ) ) {
	add_filter( 'trx_addons_filter_sc_prepare_atts', '_trx_addons_sc_add_params_sc_prepare_atts', 10, 2 );
	/**
	 * Add a class with name "${prefix_class}${value}" to the shortcodes attribute 'class' if a new param has option 'prefix_class'.
	 * 
	 * Hooks: add_filter( 'trx_addons_filter_sc_prepare_atts', '_trx_addons_sc_add_params_sc_prepare_atts', 10, 2 );
	 * 
	 * @param array $atts         An array with the shortcode values of all supported attrubutes.
	 * @param string | array $sc  The name of the shortcode whose values are being processed.
	 * 
	 * @return  An array with processed values.
	 */
	function _trx_addons_sc_add_params_sc_prepare_atts( $atts, $sc )  {
		global $trx_addons_sc_add_params;
		foreach ( $trx_addons_sc_add_params as $shortcode ) {
			if ( in_array( $sc, $shortcode['sc'] ) && empty( $shortcode['inside'] ) ) {
				foreach( $shortcode['params'] as $param => $settings ) {
					if ( ! empty( $settings['prefix_class'] ) && ! empty( $atts[ $param ] ) ) {
						$atts['class'] = ( ! empty( $atts['class'] ) ? ' ' : '' ) . sprintf( '%1$s%2$s', $settings['prefix_class'], $atts[ $param ] );
					}
				}
			}
		}
		return $atts;
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_sc_param_group_value' ) ) {
	add_filter( 'trx_addons_sc_param_group_value', '_trx_addons_sc_add_params_sc_param_group_value', 10, 2 );
	/**
	 * Add new default values to the repeater parameter.
	 * 
	 * Hooks: add_filter( 'trx_addons_sc_param_group_value', '_trx_addons_sc_add_params_sc_param_group_value', 10, 2 );
	 * 
	 * @param array $atts         An array with the default values of the repeater field.
	 * @param string | array $sc  The name of the shortcode whose values are being processed.
	 * 
	 * @return  An array with processed default values.
	 */
	function _trx_addons_sc_add_params_sc_param_group_value( $atts, $sc )  {
		global $trx_addons_sc_add_params;
		foreach ( $trx_addons_sc_add_params as $shortcode ) {
			// If we have a new params for the current shortcode
			if ( in_array( $sc, $shortcode['sc'] ) ) {
				// If a new params should be inserted inside a repeater param
				if ( ! empty( $shortcode['inside'] ) ) {
					for ( $i = 0; $i < count( $atts ); $i++ ) {
						foreach ( $shortcode['params'] as $param => $settings ) {
							$atts[ $i ][ $param ] = ! empty( $settings['default'] ) ? $settings['default'] : '';
						}
					}
				}
			}
		}
		return $atts;
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_sc_param_group_params' ) ) {
	add_filter( 'trx_addons_sc_param_group_params', '_trx_addons_sc_add_params_sc_param_group_params', 10, 2 );
	/**
	 * Add new fields to the repeater parameter.
	 * 
	 * Hooks: add_filter( 'trx_addons_sc_param_group_params', '_trx_addons_sc_add_params_sc_param_group_params', 10, 2 );
	 * 
	 * @param array $atts         An array with the repeater fields.
	 * @param string | array $sc  The name of the shortcode whose values are being processed.
	 * 
	 * @return  An array with processed repeater fields.
	 */
	function _trx_addons_sc_add_params_sc_param_group_params( $atts, $sc )  {
		global $trx_addons_sc_add_params;
		$cur_builder = isset( $atts[0]['label'] ) ? 'elementor' : 'vc';
		foreach ( $trx_addons_sc_add_params as $shortcode ) {
			// If we have a new params for the current shortcode
			if ( in_array( $sc, $shortcode['sc'] ) && _trx_addons_sc_add_params_check_builder( $cur_builder, $shortcode['builder'] ) ) {
				// If a new params should be inserted inside a repeater param
				if ( ! empty( $shortcode['inside'] ) ) {
					$pos = _trx_addons_sc_add_params_get_insert_point( $atts, $shortcode['before'], $shortcode['after'] );
					array_splice(
						$atts,
						$pos,
						0,
						isset( $atts[0]['param_name'] )
							? _trx_addons_sc_add_params_get_vc_params( $shortcode )
							: ( isset( $atts[0]['label'] )
								? _trx_addons_sc_add_params_get_elementor_params( $shortcode )
								: _trx_addons_sc_add_params_get_gb_params( $shortcode )
								)
					);
				}
			}
		}
		return $atts;
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_get_insert_point' ) ) {
	/**
	 * Detect an index of the array with shortcode's attributes to insert a new params.
	 * 
	 * @param array $atts     An array with a shortcode attributes.
	 * @param string $before  A name of the existing attribute to insert a new params before it.
	 * @param string $after   A name of the existing attribute to insert a new params after it.
	 *                        If both 'before' and 'after' are empty - return an end of the array index.
	 * 
	 * @return integer  An index of the array with attributes to insert a new params.
	 */
	function _trx_addons_sc_add_params_get_insert_point( $atts, $before, $after )  {
		$builder = isset( $atts[ 0 ]['param_name'] )
						? 'vc'
						: ( isset( $atts[ 0 ]['name'] )
							? 'elementor'
							: 'gutenberg'
							);
		if ( $builder == 'gutenberg' ) {
			$i = 0;
			foreach ( $atts as $name => $params ) {
				if ( ! empty( $before ) && $name == $before ) {
					break;
				} else if ( ! empty( $after ) && $name == $after ) {
					$i++;
					break;
				}
				$i++;
			}
		} else {
			$name_key = $builder == 'vc' ? 'param_name' : 'name';
			for ( $i = 0; $i < count( $atts ); $i++ ) {
				if ( ! empty( $before ) && $atts[ $i ][ $name_key ] == $before ) {
					break;
				} else if ( ! empty( $after ) && $atts[ $i ][ $name_key ] == $after ) {
					$i++;
					break;
				}
			}
		}
		return $i;
	}
}


// Elementor-specific utilities to add new params
//---------------------------------------------------------------------

if ( ! function_exists( '_trx_addons_sc_add_params_to_section' ) ) {
	add_action( 'elementor/element/before_section_end', '_trx_addons_sc_add_params_to_section', 10, 3 );
	add_action( 'elementor/element/before_section_start', '_trx_addons_sc_add_params_to_section', 10, 3 );
	add_action( 'elementor/element/after_section_end', '_trx_addons_sc_add_params_to_section', 10, 3 );
	/**
	 * Add new controls to the specified section for Elementor's module.
	 * 
	 * Hooks: add_filter( 'elementor/element/before_section_end', '_trx_addons_sc_add_params_to_section', 10, 3 );
	 * 
	 *        add_filter( 'elementor/element/before_section_start', '_trx_addons_sc_add_params_to_section', 10, 3 );
	 * 
	 *        add_action( 'elementor/element/after_section_end', '_trx_addons_sc_add_params_to_section', 10, 3 );
	 * 
	 * @param object $element     Current module.
	 * @param string $section_id  ID of the current section.
	 * @param string $args        Additional arguments to create section.
	 */
	function _trx_addons_sc_add_params_to_section( $element, $section_id, $args ) {
		global $trx_addons_sc_add_params;
		if ( is_object( $element ) ) {
			$el_name = $element->get_name();
			foreach ( $trx_addons_sc_add_params as $shortcode ) {
				// If we have a new params for the current shortcode
				if ( _trx_addons_sc_add_params_check_builder( 'elementor', $shortcode['builder'] ) 
					&& in_array( $el_name, $shortcode['sc'] )
					&& ! empty( $shortcode['section'] )
				) {
					if ( current_action() == 'elementor/element/before_section_end' && empty( $shortcode['section_before'] ) && empty( $shortcode['section_after'] ) && $section_id == $shortcode['section']
						||
						current_action() == 'elementor/element/before_section_start' && ! empty( $shortcode['section_before'] ) && $section_id == $shortcode['section_before']
						||
						current_action() == 'elementor/element/after_section_end' && ! empty( $shortcode['section_after'] ) && $section_id == $shortcode['section_after']
					) {
						// If a new params should be inserted to the main params list (not inside repeater)
						if ( empty( $shortcode['inside'] ) ) {
							// Open a new section
							if ( current_action() == 'elementor/element/before_section_start' && ! empty( $shortcode['section_before'] ) && $section_id == $shortcode['section_before']
								||
								current_action() == 'elementor/element/after_section_end' && ! empty( $shortcode['section_after'] ) && $section_id == $shortcode['section_after']
							) {
								$element->start_controls_section(
									$shortcode['section'],
									array(
										'label' => ! empty( $shortcode['section_title'] ) ? $shortcode['section_title'] : esc_html__( 'Additional options', 'trx_addons' ),
										'description' => ! empty( $shortcode['section_description'] ) ? $shortcode['section_description'] : '',
										'tab' => ! empty( $shortcode['section_tab'] ) ? $shortcode['section_tab'] : ''
									)
								);
							}
							// Set plain params
							if ( ! empty( $shortcode['plain'] ) && is_array( $shortcode['plain'] ) ) {
								$element->add_plain_params( $shortcode['plain'] );
							}
							// Add new params
							$new_params = _trx_addons_sc_add_params_get_elementor_params( $shortcode );
							if ( is_array( $new_params ) ) {
								foreach ( $new_params as $param_name => $param_settings ) {
									if ( ! empty( $param_settings['responsive'] ) ) {
										$element->add_responsive_control( $param_name, $param_settings );
									} else {
										$element->add_control( $param_name, $param_settings );
									}
								}
							}
							// Close a new section
							if ( current_action() == 'elementor/element/before_section_start' && ! empty( $shortcode['section_before'] ) && $section_id == $shortcode['section_before']
								||
								current_action() == 'elementor/element/after_section_end' && ! empty( $shortcode['section_after'] ) && $section_id == $shortcode['section_after']
							) {
								$element->end_controls_section();
							}
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_get_elementor_params' ) ) {
	/**
	 * Convert a shortcode params to the Elementor-compatible format
	 * 
	 * @param array $shortcode  An array with a shortcode attributes with parameters in the our internal format
	 *                          to convert it to the Elementor-compatible format.
	 * 
	 * @return array  A converted shortcode's parameters in the Elementor-compatible format.
	 */
	function _trx_addons_sc_add_params_get_elementor_params( $shortcode )  {
		$elm_atts = array();
		foreach( $shortcode['params'] as $param_name => $params ) {
			$cur_atts = array(
				'type'        => _trx_addons_sc_add_params_get_elementor_param_type( $params ),
				'label'       => ! empty( $params['title'] ) ? $params['title'] : '',
				'label_block' => isset( $params['label_block'] ) ? $params['label_block'] : in_array( $params['type'], array( 'heading', 'textarea', 'media', 'image', 'audio', 'video' ) ),
				'description' => ! empty( $params['description'] ) ? $params['description'] : '',
			);
			// Add default value
			if ( isset( $params['default'] ) ) {
				$cur_atts['default'] = $params['default'];
			} else if ( $params['type'] != 'heading' ) {
				$cur_atts['default'] = '';
			}
			// Change default value for 'url', 'media', etc.
			if ( in_array( $params['type'], array( 'image', 'audio', 'video', 'media', 'url' ) ) ) {
				$cur_atts['default'] = array( 'url' => $cur_atts['default'] );
			} else if ( in_array( $params['type'], array( 'number', 'slider', 'range' ) ) ) {
				$cur_atts['default'] = array( 'size' => $cur_atts['default'], 'unit' => ! empty( $params['unit'] ) ? $params['unit'] : 'px' );
			}
			// Prepare option list
			if ( ! empty( $params['options'] ) ) {
				$cur_atts['options'] = $params['options'];
			}
			// Get attributes for the type 'icons'
			if ( $params['type'] == 'icons' ) {
				if ( empty( $params['style'] ) ) {
					$params['style'] = trx_addons_get_setting('icons_type');
				}
				$cur_atts = trx_addons_array_get_first_value( trx_addons_get_icon_param( $param_name, false, $params['style'] ) );
			}
			// Add 'media_type' for 'audio' and 'video'
			if ( in_array( $params['type'], array( 'audio', 'video' ) ) ) {
				$cur_atts['media_type'] = $params['type'];
			}
			// Add Elementor-specific options
			if ( isset( $params['range'] ) ) {
				$cur_atts['range'] = $params['range'];
			}
			if ( isset( $params['size_units'] ) ) {
				$cur_atts['size_units'] = $params['size_units'];
			} else if ( isset( $params['units'] ) ) {
				$cur_atts['size_units'] = $params['units'];
			}
			if ( isset( $params['return_value'] ) ) {
				$cur_atts['return_value'] = $params['return_value'];
			}
			if ( isset( $params['rows'] ) ) {
				$cur_atts['rows'] = $params['rows'];
			}
			if ( isset( $params['media_type'] ) ) {
				$cur_atts['media_type'] = $params['media_type'];
			}
			if ( isset( $params['multiple'] ) ) {
				$cur_atts['multiple'] = $params['multiple'];
			}
			if ( isset( $params['separator'] ) ) {
				$cur_atts['separator'] = $params['separator'];
			}
			if ( isset( $params['placeholder'] ) ) {
				$cur_atts['placeholder'] = $params['placeholder'];
			}
			if ( isset( $params['show_label'] ) ) {
				$cur_atts['show_label'] = $params['show_label'];
			}
			if ( isset( $params['label_block'] ) ) {
				$cur_atts['label_block'] = $params['label_block'];
			}
			if ( isset( $params['label_on'] ) ) {
				$cur_atts['label_on'] = $params['label_on'];
			}
			if ( isset( $params['label_off'] ) ) {
				$cur_atts['label_off'] = $params['label_off'];
			}
			if ( isset( $params['responsive'] ) ) {
				$cur_atts['responsive'] = $params['responsive'];
			}
			if ( isset( $params['prefix_class'] ) ) {
				$cur_atts['prefix_class'] = $params['prefix_class'];
			}
			// Add specific params for some shortcodes
			if ( isset( $params['mode'] ) ) {
				$cur_atts['mode'] = $params['mode'];
			}
			if ( isset( $params['return'] ) ) {
				$cur_atts['return'] = $params['return'];
			}
			// Convert dependencies to Elementor-specific format
			if ( isset( $params['dependency'] ) && is_array( $params['dependency'] ) ) {
				$cur_atts['condition'] = array();
				foreach( $params['dependency'] as $k => $v ) {
					if ( ! is_array( $v ) ) {
						$v = array( $v );
					}
					$cond = array(
						$k => array(),
						"{$k}!" => array()
					);
					foreach( $v as $value ) {
						if ( strval( $value )[0] === '^' ) {
							$cond["{$k}!"][] = substr( $value, 1 );
						} else if ( $value === 'not_empty' ) {
							$cond["{$k}!"][] = '';
						} else {
							$cond[$k][] = $value;
						}
					}
					if ( count( $cond[ $k ] ) > 0 ) {
						$cur_atts['condition'][ $k ] = count( $cond[ $k ] ) > 1 ? $cond[ $k ] : $cond[ $k ][0];
					}
					if ( count( $cond["{$k}!"] ) > 0 ) {
						$cur_atts['condition']["{$k}!"] = count( $cond["{$k}!"] ) > 1 ? $cond["{$k}!"] : $cond["{$k}!"][0];
					}
				}
			}
			// Add name of the param to use it inside repeater
			if ( ! empty( $shortcode['inside'] ) ) {
				$cur_atts['name'] = $param_name;
				$elm_atts[] = $cur_atts;
			} else {
				$elm_atts[ $param_name ] = $cur_atts;
			}
		}
		return apply_filters( 'trx_addons_filter_sc_add_params_get_elementor_params', $elm_atts, $shortcode );
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_get_elementor_param_type' ) ) {
	/**
	 * Convert a shortcode params to the Elementor-compatible format
	 * 
	 * @param array $atts  An array with a shortcode attributes in the our internal format to convert it to the Elementor-compatible format.
	 * 
	 * @return array  A converted shortcode's attributes in the Elementor-compatible format.
	 */
	function _trx_addons_sc_add_params_get_elementor_param_type( $params )  {
		if ( trx_addons_exists_elementor() ) {
			$elementor_types = apply_filters( 'trx_addons_filter_elementor_param_types', array(
				// Elementor core types
				'text'      => \Elementor\Controls_Manager::TEXT,
				'textarea'  => \Elementor\Controls_Manager::TEXTAREA,
				'checkbox'  => \Elementor\Controls_Manager::SWITCHER,
				'color'     => \Elementor\Controls_Manager::COLOR,
				'image'     => \Elementor\Controls_Manager::MEDIA,
				'audio'     => \Elementor\Controls_Manager::MEDIA,
				'video'     => \Elementor\Controls_Manager::MEDIA,
				'icon'      => \Elementor\Controls_Manager::ICON,
				'heading'   => \Elementor\Controls_Manager::HEADING,
				// Substitute our types with core types
				'checklist' => \Elementor\Controls_Manager::SELECT2,
				'switch'    => \Elementor\Controls_Manager::SWITCHER,
				'number'    => \Elementor\Controls_Manager::SLIDER,
				'slider'    => \Elementor\Controls_Manager::SLIDER,
				'range'     => \Elementor\Controls_Manager::SLIDER,
				'url'       => \Elementor\Controls_Manager::URL,
				// Custom types
				'radio'     => \Elementor\Controls_Manager::SELECT,
				'icons'     => 'trx_icons',
				'select'    => \Elementor\Controls_Manager::SELECT,
				'select2'   => \Elementor\Controls_Manager::SELECT2,
				// Types to skip (not supported in Elementor)
			) );
			return apply_filters( 'trx_addons_filter_sc_add_params_get_elementor_param_type', isset( $elementor_types[ $params['type'] ] ) ? $elementor_types[ $params['type'] ] : $params['type'], $params );
		}
		return apply_filters( 'trx_addons_filter_sc_add_params_get_elementor_param_type', $params['type'], $params );
	}
}


// VC-specific utilities to add new params
//---------------------------------------------------------------------

if ( ! function_exists( '_trx_addons_sc_add_params_sc_map' ) ) {
	add_filter( 'trx_addons_sc_map', '_trx_addons_sc_add_params_sc_map', 10, 2 );
	/**
	 * Add new params to the list of shortcode's attributes for VC.
	 * 
	 * Hooks: add_filter( 'trx_addons_sc_map', '_trx_addons_sc_add_params_sc_map', 10, 2 );
	 * 
	 * @param array $atts  An array with the default values of all supported attrubutes.
	 * @param string $sc   The name of the shortcode whose values are being processed.
	 * 
	 * @return  An array with processed attributes.
	 */
	function _trx_addons_sc_add_params_sc_map( $atts, $sc )  {
		global $trx_addons_sc_add_params;
		foreach ( $trx_addons_sc_add_params as $shortcode ) {
			// If we have a new params for the current shortcode
			if ( in_array( $sc, $shortcode['sc'] ) && _trx_addons_sc_add_params_check_builder( 'vc', $shortcode['builder'] ) ) {
				// If a new params should be inserted to the main params list (not inside repeater)
				if ( empty( $shortcode['inside'] ) ) {
					$pos = _trx_addons_sc_add_params_get_insert_point( $atts['params'], $shortcode['before'], $shortcode['after'] );
					array_splice( $atts['params'], $pos, 0, _trx_addons_sc_add_params_get_vc_params( $shortcode ) );
				}
			}
		}
		return $atts;
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_get_vc_params' ) ) {
	/**
	 * Convert a shortcode params to the VC-compatible format
	 * 
	 * @param array $shortcode  An array with a shortcode attributes with parameters in the our internal format
	 *                          to convert it to the VC-compatible format.
	 * 
	 * @return array  A converted shortcode's parameters in the VC-compatible format.
	 */
	function _trx_addons_sc_add_params_get_vc_params( $shortcode )  {
		$vc_atts = array();
		foreach( $shortcode['params'] as $param_name => $params ) {
			$param_type = _trx_addons_sc_add_params_get_vc_param_type( $params );
			if ( empty( $param_type ) ) {
				continue;
			}
			$cur_atts = array(
				'param_name'  => $param_name,
				'type'        => $param_type,
				'heading'     => ! empty( $params['title'] ) ? $params['title'] : '',
				'description' => ! empty( $params['description'] ) ? $params['description'] : '',
			);
			// Add default value
			if ( isset( $params['default'] ) ) {
				$cur_atts['std'] = in_array( $params['type'], array( 'checkbox', 'switch' ) )  ? strval( $params['default'] ) : $params['default'];
			} else {
				$cur_atts['std'] = '';
			}
			// Prepare option list
			if ( in_array( $params['type'], array( 'checkbox', 'switch' ) ) ) {
				$cur_atts['value'] = array( ( ! empty( $params['title'] ) ? $params['title'] : '' ) => isset( $params['return_value'] )
																											? $params['return_value']
																											: '1'
											);
			} else if ( in_array( $params['type'], array( 'select', 'radio' ) ) ) {
				$cur_atts['value'] = array_flip( $params['options'] );
			} else if ( $params['type'] == 'icons' && ! empty( $params['options'] ) ) {
				$cur_atts['value'] = $params['options'];
			}
			// Replace type 'checklist' to the 'select' with 'multiple'
			if ( $params['type'] == 'checklist' ) {
				$cur_atts['type'] = 'select';
				$cur_atts['multiple'] = true;
			}
			// Add icons list to the type 'icons'
			if ( $params['type'] == 'icons' && empty( $params['options'] ) ) {
				if ( empty( $params['style'] ) ) {
					$params['style'] = trx_addons_get_setting('icons_type');
				}
				$cur_atts['value'] = trx_addons_get_list_icons( $params['style']);
			}
			// Add VC-specific options
			if ( isset( $shortcode['group'] ) ) {
				$cur_atts['group'] = $shortcode['group'];
			}
			if ( isset( $params['admin_label'] ) ) {
				$cur_atts['admin_label'] = $params['admin_label'];
			}
			if ( isset( $params['save_always'] ) ) {
				$cur_atts['save_always'] = $params['save_always'];
			}
			if ( isset( $params['edit_field_class'] ) ) {
				$cur_atts['edit_field_class'] = $params['edit_field_class'];
			}
			// Add specific params for some shortcodes
			if ( isset( $params['mode'] ) ) {
				$cur_atts['mode'] = $params['mode'];
			}
			if ( isset( $params['return'] ) ) {
				$cur_atts['return'] = $params['return'];
			}
			if ( isset( $params['style'] ) ) {
				$cur_atts['style'] = $params['style'];
			}
			if ( isset( $params['multiple'] ) ) {
				$cur_atts['multiple'] = $params['multiple'];
			}
			if ( isset( $params['rows'] ) ) {
				$cur_atts['rows'] = $params['rows'];
			}
			// Convert dependencies to VC-specific format
			if ( isset( $params['dependency'] ) && is_array( $params['dependency'] ) ) {
				$cur_atts['dependency'] = array();
				foreach( $params['dependency'] as $k => $v ) {
					$cur_atts['dependency'][ 'element' ] = $k;
					if ( ! is_array( $v ) ) {
						$v = array( $v );
					}
					foreach( $v as $value ) {
						if ( $value === '' ) {
							$cur_atts['dependency'][ 'is_empty' ] = true;
						} else if ( $value === 'not_empty' ) {
							$cur_atts['dependency'][ 'not_empty' ] = true;
						} else if ( strval( $value )[0] === '^' ) {
							if ( ! isset( $cur_atts['dependency'][ 'value_not_equal_to' ] ) ) {
								$cur_atts['dependency'][ 'value_not_equal_to' ] = array();
							}
							$cur_atts['dependency'][ 'value_not_equal_to' ][] = substr( $value, 1 );
						} else {
							if ( ! isset( $cur_atts['dependency'][ 'value' ] ) ) {
								$cur_atts['dependency'][ 'value' ] = array();
							}
							$cur_atts['dependency'][ 'value' ][] = in_array( $params['type'], array( 'checkbox', 'switch' ) ) ? strval( $value ) : $value;
						}
					}
					break;	// VC is support only one dependency element
				}
			}
			$vc_atts[] = $cur_atts;
		}
		return apply_filters( 'trx_addons_filter_sc_add_params_get_vc_params', $vc_atts, $shortcode );
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_get_vc_param_type' ) ) {
	/**
	 * Convert a shortcode params to the VC-compatible format
	 * 
	 * @param array $atts  An array with a shortcode attributes in the our internal format to convert it to the VC-compatible format.
	 * 
	 * @return array  A converted shortcode's attributes in the VC-compatible format.
	 */
	function _trx_addons_sc_add_params_get_vc_param_type( $params )  {
		$vc_types = apply_filters( 'trx_addons_filter_vc_param_types', array(
			// VC core types
			'text'      => 'textfield',
			'textarea'  => 'textarea_safe',
			'checkbox'  => 'checkbox',
			'color'     => 'colorpicker',
			'image'     => 'attach_image',
			'audio'     => 'attach_image',
			'video'     => 'attach_image',
			'icon'      => 'icons',
			'icons'     => 'icons',
			// Substitute our types with core types
			'checklist' => 'select',
			'switch'    => 'checkbox',
			'number'    => 'textfield',
			'slider'    => 'textfield',
			'range'     => 'textfield',
			'url'       => 'textfield',
			// Custom types
			'radio'     => 'radio',
			'icons'     => 'icons',
			'select'    => ! empty( $params['multiple'] ) ? 'select' : 'dropdown',
			// Types to skip (not supported in VC)
			'heading'   => ''
		) );
		return apply_filters( 'trx_addons_filter_sc_add_params_get_vc_param_type', isset( $vc_types[ $params['type'] ] ) ? $vc_types[ $params['type'] ] : $params['type'], $params );
	}
}


// Gutenberg-specific utilities to add new params
//---------------------------------------------------------------------

if ( ! function_exists( '_trx_addons_sc_add_params_gb_map' ) ) {
	add_filter( 'trx_addons_gb_map', '_trx_addons_sc_add_params_gb_map', 10, 2 );
	/**
	 * Add new params to the PHP-list of shortcode's attributes with default values for Gutenberg.
	 * 
	 * Hooks: add_filter( 'trx_addons_gb_map', '_trx_addons_sc_add_params_gb_map', 10, 2 );
	 * 
	 * @param array $atts     An array with types and default values of all supported attrubutes.
	 * @param string $sc_gb   The name of the Gutenberg block whose values are being processed.
	 * 
	 * @return  An array with processed attributes.
	 */
	function _trx_addons_sc_add_params_gb_map( $atts, $sc_gb )  {
		global $trx_addons_sc_add_params;
		$sc = _trx_addons_sc_add_params_gb_to_sc_name( $sc_gb );
		foreach ( $trx_addons_sc_add_params as $shortcode ) {
			// If we have a new params for the current shortcode
			if ( in_array( $sc, $shortcode['sc'] ) && _trx_addons_sc_add_params_check_builder( 'gutenberg', $shortcode['builder'] ) ) {
				// If a new params should be inserted to the main params list (not inside repeater)
				if ( empty( $shortcode['inside'] ) ) {
					// Append new params at the end of array - for this array, the insertion position of the parameters does not matter
					if ( strpos( $sc_gb, 'common/' ) === 0 ) {
						$atts = array_merge( $atts, _trx_addons_sc_add_params_get_gb_params( $shortcode ) );
					} else {
						$atts['attributes'] = array_merge( $atts['attributes'], _trx_addons_sc_add_params_get_gb_params( $shortcode ) );
					}
				}
			}
		}
		return $atts;
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_gb_to_sc_name' ) ) {
	/**
	 * Convert a shortcode name from Gutenberg format to the our internal format.
	 * 
	 * For example: 'trx-addons/actions' -> 'trx_sc_actions'
	 * 
	 *              'trx-addons/layouts-iconed-text' -> 'trx_sc_layouts_iconed_text'
	 * 
	 * @param array $atts  An array with types and default values of all supported attrubutes.
	 * @param string $sc   The name of the shortcode whose values are being processed.
	 * 
	 * @return  An array with processed attributes.
	 */
	function _trx_addons_sc_add_params_gb_to_sc_name( $gb_name )  {
		return apply_filters( 'trx_addons_filter_gb_to_sc_name', str_replace( '-', '_', str_replace( 'trx-addons/', 'trx_sc_', $gb_name ) ) );
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_get_gb_params' ) ) {
	/**
	 * Convert a shortcode params to the Gutenberg-compatible format
	 * 
	 * @param array $shortcode  An array with a shortcode attributes with parameters in the our internal format
	 *                          to convert it to the Gutenberg-compatible format.
	 * 
	 * @return array  A converted shortcode's parameters in the Gutenberg-compatible format.
	 */
	function _trx_addons_sc_add_params_get_gb_params( $shortcode )  {
		$gb_atts = array();
		foreach( $shortcode['params'] as $param_name => $params ) {
			$param_type = _trx_addons_sc_add_params_get_gb_param_type( $params );
			if ( empty( $param_type ) ) {
				continue;
			}
			$cur_atts = array(
				'type' => $param_type,
			);
			// Add default value
			if ( isset( $params['default'] ) ) {
				$cur_atts['default'] = in_array( $params['type'], array( 'checkbox', 'switch' ) )
										? (bool)$params['default']
										: ( in_array( $params['type'], array( 'image', 'audio', 'video', 'media' ) )
											? 0
											: $params['default']
											);
			} else {
				$cur_atts['default'] = in_array( $params['type'], array( 'checkbox', 'switch' ) ) ? false : '';
			}
			$gb_atts[ $param_name ] = $cur_atts;
			// Add extra param with suffix '_url' for all media types
			if ( in_array( $params['type'], array( 'image', 'audio', 'video', 'media' ) ) ) {
				$gb_atts[ $param_name . '_url' ] = array(
					'type' => 'string',
					'default' => ''
				);
			}
		}
		return apply_filters( 'trx_addons_filter_sc_add_params_get_gb_params', $gb_atts, $shortcode );
	}
}

if ( ! function_exists( '_trx_addons_sc_add_params_get_gb_param_type' ) ) {
	/**
	 * Convert a shortcode param's type to the Gutenberg-compatible data type.
	 * 
	 * @param array $params  An array with a shortcode attributes.
	 * 
	 * @return string  A Gutenberg-compatible data type.
	 */
	function _trx_addons_sc_add_params_get_gb_param_type( $params )  {
		$gb_types = apply_filters( 'trx_addons_filter_gb_param_types', array(
			// Gutenberg core types
			'text'      => 'string',
			'textarea'  => 'string',
			'checkbox'  => 'boolean',
			'color'     => 'string',
			'image'     => 'number',
			'audio'     => 'number',
			'video'     => 'number',
			'media'     => 'number',
			'icon'      => 'string',
			'icons'     => 'string',
			'radio'     => 'string',
			'select'    => 'string',
			// Substitute our types with core types
			'checklist' => 'string',
			'switch'    => 'boolean',
			'number'    => 'number',
			'slider'    => 'number',
			'range'     => 'number',
			'url'       => 'string',
			// Types to skip (not supported in VC)
			'heading'   => ''
		) );
		return apply_filters( 'trx_addons_filter_sc_add_params_get_gb_param_type', isset( $gb_types[ $params['type'] ] ) ? $gb_types[ $params['type'] ] : $params['type'], $params );
	}
}

// Add scripts and styles for the editor
if ( ! function_exists( '_trx_addons_sc_add_params_gb_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', '_trx_addons_sc_add_params_gb_editor_assets', 8 );
	/**
	 * Add scripts to add new params for the Gutenberg editor.
	 * Use the priority 8 to add filters that add params before all other blocks are registered
	 * 
	 * Hooks: add_filter( 'enqueue_block_editor_assets', '_trx_addons_sc_add_params_gb_editor_assets', 8 );
	 */
	function _trx_addons_sc_add_params_gb_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-sc-add-params',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'shortcodes-params.js' ),
				function_exists( 'trx_addons_block_editor_dependencis' ) ? trx_addons_block_editor_dependencis() : array(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'shortcodes-params.js' ) ),
				true
			);
			global $trx_addons_sc_add_params;
			wp_localize_script( 'trx-addons-gutenberg-editor-block-sc-add-params', 'TRX_ADDONS_SC_ADD_PARAMS', apply_filters( 'trx_addons_filter_sc_add_params_gb_editor_localize', $trx_addons_sc_add_params ) );
		}
	}
}
