<?php
/**
 * Elementor extension: Fixes for compatibility with new versions
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Convert args of controls with a type ::REPEATER
//-------------------------------------------------------

if ( ! function_exists( 'trx_addons_elm_get_repeater_controls' ) ) {
	add_filter( 'trx_addons_sc_param_group_params', 'trx_addons_elm_get_repeater_controls', 999, 2 );
	/**
	 * Convert args of fields for the type ::REPEATER for the new Elementor version.
	 * Make an associative array from a list by key 'name'.
	 * After the update Elementor 3.1.0+ (or near) internal structure of field type ::REPEATER was changed
	 * (fields list was converted to the associative array) and as result js-errors appears in the Elementor Editor:
	 * "Cannot read property 'global' of undefined".
	 * "TypeError: undefined is not an object (evaluating 't[o].global')".
	 *
	 * @param array $args  Array with a group of fields
	 * @param string $sc   Shortcode slug
	 * 
	 * @return array     Array with a group of fields for the type ::REPEATER for the new Elementor version
	 */
	function trx_addons_elm_get_repeater_controls( $args, $sc = '' ) {
		if ( trx_addons_exists_elementor() && ! empty( $args[0]['name'] ) && ! isset( $args['_id'] ) ) {
			$repeater = new \Elementor\Repeater();
			$tab = '';
			if ( is_array( $args ) ) {
				foreach ( $args as $k => $v ) {
					if ( ! empty( $v['name'] ) ) {
						$k = $v['name'];
					}
					if ( empty( $tab ) && ! empty( $v['tab'] ) ) {
						$tab = $v['tab'];
					}
					if ( ! empty( $v['responsive'] ) ) {
						unset( $v['responsive'] );
						$repeater->add_responsive_control( $k, $v );
					} else {
						$repeater->add_control( $k, $v );
					}
				}
			}
			$controls = $repeater->get_controls();
			if ( ! empty( $tab ) && ! empty( $controls['_id']['tab'] ) && $controls['_id']['tab'] != $tab ) {
				$controls['_id']['tab'] = $tab;
			}
			return $controls;
		}
		return $args;
	}
}


// Prepare global atts
//-----------------------------------------------

if ( ! function_exists( 'trx_addons_elm_prepare_global_params' ) ) {
	add_filter( 'trx_addons_filter_sc_prepare_atts', 'trx_addons_elm_prepare_global_params', 10, 2 );
	/**
	 * Prepare global atts for the new Elementor version: add array keys by 'name' from __globals__
	 * After the update Elementor 3.0+ (or later) for settings with type ::COLOR global selector appears.
	 * Color value from this selects is not placed to the appropriate settings.
	 * 
	 * @hooked trx_addons_sc_prepare_atts
	 * 
	 * @trigger trx_addons_filter_prepare_global_param
	 *
	 * @param array $args  Array with atts
	 * @param string $sc   Shortcode slug
	 * 
	 * @return array     Array with atts
	 */
	function trx_addons_elm_prepare_global_params( $args, $sc = '' ) {
		foreach ( $args as $k => $v ) {
			if ( is_array( $v ) ) {
				if ( is_string( $k ) && $k == '__globals__' ) {
					foreach ( $v as $k1 => $v1 ) {
						if ( ! empty( $v1 ) ) {
							$args[ $k1 ] = apply_filters( 'trx_addons_filter_prepare_global_param', $v1, $k1 );
						}
					}
				} else {
					$args[ $k ] = trx_addons_elm_prepare_global_params( $v, $sc );
				}
			}
		}
		return $args;
	}
}

if ( ! function_exists( 'trx_addons_elm_prepare_global_color' ) ) {
	add_filter( 'trx_addons_filter_prepare_global_param', 'trx_addons_elm_prepare_global_color', 10, 2 );
	/**
	 * Return a CSS-var from global color key, i.e. 'globals/colors?id=1855627f'
	 * 
	 * @hooked trx_addons_elm_prepare_global_params
	 *
	 * @param string $value  Value of the setting
	 * @param string $key    Key of the setting
	 * 
	 * @return string     Value of the setting
	 */
	function trx_addons_elm_prepare_global_color( $value, $key ) {
		$prefix = 'globals/colors?id=';
		if ( strpos( $value, $prefix ) === 0 ) {
			$id = str_replace( $prefix, '', $value );
			$value = "var(--e-global-color-{$id})";
		}
		return $value;
	}
}


// Conditions with unavailable characters
//-------------------------------------------------

if ( false && ! function_exists('trx_addons_elm_remove_unavailable_conditions') ) {
	add_action( 'elementor/element/after_section_end', 'trx_addons_elm_remove_unavailable_conditions', 9999, 3 );
	/**
	 * Remove conditions where key contain unavailable characters.
	 * After the update Elementor 3.4.1 js-errors appears in the console and the Editor stop loading
	 * if the condition of any option contains a key with characters outside the range a-z 0-9 - _ [ ] !
	 * a mask '/([a-z_\-0-9]+)(?:\[([a-z_]+)])?(!?)$/i' is used in the editor.js and controls-stack.php
	 * This issue is resolved in Elementor 3.4.2 (according to it author)
	 * I leave this code commented for future cases (if appears)
	 * 
	 * @hooked elementor/element/after_section_end
	 *
	 * @param object $element  Elementor element object
	 * @param string $section_id  Section ID
	 * @param array $args  Array with additional arguments
	 */
	function trx_addons_elm_remove_unavailable_conditions( $element, $section_id='', $args='' ) {
		if ( ! is_object( $element ) ) {
			return;
		}
		$controls = $element->get_controls();
		if ( is_array( $controls ) ) {
			foreach( $controls as $k => $v ) {
				if ( ! empty( $v['condition'] ) && is_array( $v['condition'] ) ) {
					$chg = false;
					$condition = array();
					foreach( $v['condition'] as $k1 => $v1 ) {
						// If current condition contains a selector to the field  "Page template" - replace it with 'template'
						if ( strpos( $k1, '.editor-page-attributes__template' ) !== false || strpos( $k1, '#page_template' ) !== false ) {
							$condition['template'] = $v1;
							$chg = true;
						// Else if current condition contains any other selector - remove it
						} else if ( strpos( $k1, ' ' ) !== false || strpos( $k1, '.' ) !== false || strpos( $k1, '#' ) !== false ) {
							$chg = true;
						// Else - leave all other conditions unchanged
						} else {
							$condition[ $k1 ] = $v1;
						}
					}
					// Update 'condition' in the current control if changed
					if ( $chg ) {
						$element->update_control( $k, array(
										'condition' => $condition
									) );
					}
				}
			}
		}
	}
}


// Column paddings
//------------------------------------

if ( ! function_exists( 'trx_addons_elm_move_paddings_to_column_wrap' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_move_paddings_to_column_wrap', 10, 3 );
	/**
	 * Move a column paddings from the .elementor-element-wrap to the .elementor-column-wrap to compatibility with old themes
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element  Elementor element object
	 * @param string $section_id  Section ID
	 * @param array $args  Array with additional arguments
	 */
	function trx_addons_elm_move_paddings_to_column_wrap( $element, $section_id, $args ) {
		if ( is_object( $element ) ) {
			$el_name = $element->get_name();
			if ( 'column' == $el_name && 'section_advanced' == $section_id ) {
				$element->update_responsive_control( 'padding', array(
											'selectors' => array(
												'{{WRAPPER}} > .elementor-element-populated.elementor-column-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',	// Elm 2.9- (or DOM Optimization == Inactive)
												'{{WRAPPER}} > .elementor-element-populated.elementor-widget-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',	// Elm 3.0+
											)
										) );
			}
		}
	}
}


// Widgets and controls registration
//--------------------------------------------------

if ( ! function_exists( 'trx_addons_elm_register_widget' ) ) {
	/**
	 * Wrapper for widgets registration in Elementor according to the version of the plugin to prevent a deprecation warning.
	 * In Elementor 3.5.0 the method Plugin::$instance->widgets_manager->register_widget_type() is soft deprecated and
	 * replaced with Plugin::$instance->widgets_manager->register().
	 *
	 * @param string $widget_class  Widget class name
	 */
	function trx_addons_elm_register_widget( $widget_class ) {
		if ( class_exists( $widget_class ) ) {
			if ( method_exists( \Elementor\Plugin::instance()->widgets_manager, 'register' ) ) {
				\Elementor\Plugin::instance()->widgets_manager->register( new $widget_class() );
			} else {
				\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new $widget_class() );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elementor_get_action_for_controls_registration' ) ) {
	/**
	 * Return an action name for controls registration in Elementor according to the version of the plugin
	 * to prevent a deprecation warning.
	 * In Elementor 3.5.0 the action 'elementor/controls/controls_registered' is soft deprecated
	 * and replaced with 'elementor/controls/register'.
	 *
	 * @return string  Action name
	 */
	function trx_addons_elementor_get_action_for_controls_registration() {
		return defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' )
					? 'elementor/controls/register'
					: 'elementor/controls/controls_registered';
	}
}

if ( ! function_exists( 'trx_addons_elementor_get_action_for_widgets_registration' ) ) {
	/**
	 * Return an action name for widgets registration in Elementor according to the version of the plugin
	 * to prevent a deprecation warning.
	 * In Elementor 3.5.0 the action 'elementor/widgets/widgets_registered' is soft deprecated
	 * and replaced with 'elementor/widgets/register'.
	 *
	 * @return string  Action name
	 */
	function trx_addons_elementor_get_action_for_widgets_registration() {
		return defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' )
					? 'elementor/widgets/register'
					: 'elementor/widgets/widgets_registered';
	}
}


// Elementor Pro
//------------------------------------------
if ( ! function_exists( 'trx_addons_elm_pro_woocommerce_wordpress_widget_css_class' ) ) {
	add_filter( 'elementor/widgets/wordpress/widget_args', 'trx_addons_elm_pro_woocommerce_wordpress_widget_css_class', 11, 2 );
	/**
	 * Fix for Elementor Pro 3.5.0+ - prevent to cross the WooCommerce widget's wrapper
	 * 
	 * @hook elementor/widgets/wordpress/widget_args
	 */
	function trx_addons_elm_pro_woocommerce_wordpress_widget_css_class( $default_widget_args, $widget ) {
		if ( is_object( $widget ) ) {
			$widget_instance = $widget->get_widget_instance();
			if ( ! empty( $widget_instance->widget_cssclass ) ) {
				$open_tag = sprintf( '<div class="%s">', $widget_instance->widget_cssclass );
				if ( substr( $default_widget_args['before_widget'], -strlen( $open_tag ) ) == $open_tag
					&& $default_widget_args['after_widget'] == '</aside></div>'
				) {
					$default_widget_args['after_widget'] = '</div></aside>';
				}
			}
		}
		return $default_widget_args;
	}
}


// Other fixes
//------------------------------------------

if ( ! function_exists( 'trx_addons_elm_prevent_redirect_to_wizard_after_activation' ) ) {
	add_action( 'admin_init', 'trx_addons_elm_prevent_redirect_to_wizard_after_activation', 1 );
	/**
	 * Fix for Elementor 3.6.8+ - prevent to redirect to the Elementor's setup wizard after the plugin activation
	 * until theme-specific plugins are installed and activated completely
	 * 
	 * @hook admin_init, 1
	 */
	function trx_addons_elm_prevent_redirect_to_wizard_after_activation() {
		if ( trx_addons_get_value_gp( 'page' ) == 'trx_addons_theme_panel' && get_transient( 'elementor_activation_redirect' ) ) {
			delete_transient( 'elementor_activation_redirect' );
		}
	}
}
