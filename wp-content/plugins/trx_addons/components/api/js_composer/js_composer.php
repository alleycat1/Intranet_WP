<?php
/**
 * Plugin support: WPBakery PageBuilder
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Check if plugin is installed and activated
// Attention! This function is used in many files and was moved to the api.php
/*
if ( !function_exists( 'trx_addons_exists_vc' ) ) {
	function trx_addons_exists_vc() {
		return class_exists('Vc_Manager');
	}
}
*/

if ( ! function_exists( 'trx_addons_vc_is_frontend' ) ) {
	/**
	 * Check if current page is VC frontend editor
	 *
	 * @return boolean
	 */
	function trx_addons_vc_is_frontend() {
		return ( isset( $_GET['vc_editable'] ) && $_GET['vc_editable'] == 'true' )
			|| ( isset( $_GET['vc_action'] ) && $_GET['vc_action'] == 'vc_inline' );
		//return function_exists('vc_is_frontend_editor') && vc_is_frontend_editor();
	}
}

if ( ! function_exists( 'trx_addons_vc_add_param_option' ) ) {
	/**
	 * Add a new param's option to the specified parameters list or modify the existing option
	 *
	 * @param array $params       List of parameters
	 * @param string $param_name  Name of the parameter to add/modify
	 * @param array $option       New option
	 * 
	 * @return array              Modified list of parameters
	 */
	function trx_addons_vc_add_param_option( $params, $param_name, $option ) {
		if ( is_array( $params ) ) {
			foreach( $params as $k => $v ) {
				if ( isset( $v['param_name'] ) && $v['param_name'] == $param_name ) {
					$params[ $k ] = array_merge( $v, $option );
					break;
				}
			}
		}
		return $params;
	}
}

if ( ! function_exists( 'trx_addons_vc_remove_param' ) ) {
	/**
	 * Remove the specified parameter from the list of parameters of the VC shortcode
	 *
	 * @param array $params       List of parameters
	 * @param string $param_name  Name of the parameter to remove
	 * 
	 * @return array              Modified list of parameters
	 */
	function trx_addons_vc_remove_param( $params, $param_name ) {
		if ( is_array( $params ) ) {
			foreach ( $params as $k => $v ) {
				if ( isset( $v['param_name'] ) && $v['param_name'] == $param_name ) {
					unset( $params[ $k ] );
					break;
				}
			}
		}
		return $params;
	}
}

if ( ! function_exists( 'trx_addons_vc_edit_form_start' ) ) {
	add_action( 'wp_ajax_vc_edit_form', 'trx_addons_vc_edit_form_start', 0 );
	/**
	 * Start catch output to add a div before vc_new_row in the vc_edit_form
	 * 
	 * @hook wp_ajax_vc_edit_form
	 */
	function trx_addons_vc_edit_form_start() {
		if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, '6.0.3', '<' ) ) {
			ob_start();
		}
	}
}

if ( ! function_exists( 'trx_addons_vc_edit_form_end' ) ) {
	add_filter( 'vc_edit_form_fields_after_render', 'trx_addons_vc_edit_form_end');
	/**
	 * End catch output and add a div before vc_new_row in the vc_edit_form
	 * 
	 * @hook vc_edit_form_fields_after_render
	 * 
	 * @param string $output  Output of the vc_edit_form
	 * 
	 * @return string         Modified output of the vc_edit_form
	 */
	function trx_addons_vc_edit_form_end( $output = '' ) {
		if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, '6.0.3', '<' ) ) {
			$output = ob_get_contents();
			ob_end_clean();
		}
		$output = preg_replace( '/(<div[^>]*class="[^"]*vc_new_row)/',
								'<div class="vc_new_row_before"></div>$1',
								$output,
								-1,
								$count
							);
		if ( defined( 'WPB_VC_VERSION' ) && version_compare( WPB_VC_VERSION, '6.0.3', '<' ) ) {
			trx_addons_show_layout( $output );
		}
		return $output;
	}
}

if ( ! function_exists( 'trx_addons_get_vc_form_params' ) ) {
	/**
	 * Get VC edit form params (if exists) from GET or POST
	 * 
	 * @param string $sc  Shortcode name
	 * 
	 * @return array      Array with VC form params
	 */
	function trx_addons_get_vc_form_params( $sc ) {
		$vc_edit = is_admin() && trx_addons_get_value_gp('action') == 'vc_edit_form' && trx_addons_get_value_gp('tag') == $sc;
		$vc_params = $vc_edit && isset( $_POST['params'] ) ? $_POST['params'] : array();
		return array( $vc_edit, $vc_params );
	}
}

if ( ! function_exists( 'trx_addons_vc_edit_form_enqueue_script' ) ) {
	add_filter( 'vc_edit_form_enqueue_script', 'trx_addons_vc_edit_form_enqueue_script' );
	/**
	 * Enqueue script to the VC edit form
	 * 
	 * @hook vc_edit_form_enqueue_script
	 * 
	 * @param array $scripts  List of scripts
	 * 
	 * @return array          Modified list of scripts
	 */
	function trx_addons_vc_edit_form_enqueue_script( $scripts ) {
		$scripts[] = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'js_composer/js_composer.edit-form.js') . '?rnd=' . mt_rand();
		return $scripts;
	}
}

if ( ! function_exists( 'trx_addons_vc_load_scripts_admin' ) ) {
	add_action( "trx_addons_action_load_scripts_admin", 'trx_addons_vc_load_scripts_admin' );
	/**
	 * Load required styles and scripts for the VC admin mode
	 * 
	 * @hook trx_addons_action_load_scripts_admin
	 */
	function trx_addons_vc_load_scripts_admin() {
		if ( trx_addons_exists_vc() ) {
			wp_enqueue_style( 'trx_addons-admin-js_composer', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'js_composer/js_composer.admin.css'), array(), null );
			wp_enqueue_script( 'trx_addons-admin-js_composer', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'js_composer/js_composer.admin.js'), array('jquery'), null, true );
		}
	}
}

if ( ! function_exists( 'trx_addons_vc_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_vc_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	/**
	 * Load required styles and scripts for the VC frontend mode (if 'debug_mode' is on)
	 * 
	 * @hook wp_enqueue_scripts
	 */
	function trx_addons_vc_load_scripts_front() {
		if ( trx_addons_exists_vc() && trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
			wp_enqueue_style( 'trx_addons-js_composer', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'js_composer/js_composer.css'), array(), null );
			wp_enqueue_script( 'trx_addons-js_composer', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'js_composer/js_composer.js'), array('jquery'), null, true );
		}
	}
}

if ( ! function_exists( 'trx_addons_vc_load_responsive_styles' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_vc_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	/**
	 * Load responsive styles for the VC frontend mode (if 'debug_mode' is on)
	 * 
	 * @hook wp_enqueue_scripts
	 */
	function trx_addons_vc_load_responsive_styles() {
		if ( trx_addons_exists_vc() && trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
			wp_enqueue_style(
				'trx_addons-js_composer-responsive', 
				trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'js_composer/js_composer.responsive.css'), 
				array(), 
				null, 
				trx_addons_media_for_load_css_responsive( 'vc', 'lg' )
			);
		}
	}
}
	
if ( ! function_exists( 'trx_addons_vc_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_vc_merge_styles' );
	/**
	 * Merge VC-specific styles to the single stylesheet to increase page upload speed
	 * 
	 * @hook trx_addons_filter_merge_styles
	 * 
	 * @param array $list  List of stylesheets to merge
	 * 
	 * @return array      Modified list of stylesheets
	 */
	function trx_addons_vc_merge_styles( $list ) {
		if ( trx_addons_exists_vc() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'js_composer/js_composer.css' ] = true;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_vc_merge_styles_responsive' ) ) {
	add_filter( "trx_addons_filter_merge_styles_responsive", 'trx_addons_vc_merge_styles_responsive' );
	/**
	 * Merge VC-specific responsive styles to the single stylesheet to increase page upload speed
	 * 
	 * @hook trx_addons_filter_merge_styles_responsive
	 * 
	 * @param array $list  List of stylesheets to merge
	 * 
	 * @return array      Modified list of stylesheets
	 */
	function trx_addons_vc_merge_styles_responsive( $list ) {
		if ( trx_addons_exists_vc() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'js_composer/js_composer.responsive.css' ] = true;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_vc_merge_scripts' ) ) {
	add_action( "trx_addons_filter_merge_scripts", 'trx_addons_vc_merge_scripts' );
	/**
	 * Merge VC-specific scripts to the single file to increase page upload speed
	 * 
	 * @hook trx_addons_filter_merge_scripts
	 * 
	 * @param array $list  List of scripts to merge
	 * 
	 * @return array      Modified list of scripts
	 */
	function trx_addons_vc_merge_scripts( $list ) {
		if ( trx_addons_exists_vc() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'js_composer/js_composer.js' ] = true;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_vc_sass_responsive' ) ) {
	add_filter( "trx_addons_filter_responsive_sizes", 'trx_addons_vc_sass_responsive', 11 );
	/**
	 * Add responsive sizes to the list of @media for CSS and SASS
	 * 
	 * @hook trx_addons_filter_responsive_sizes
	 * 
	 * @param array $list  List of responsive sizes
	 * 
	 * @return array       Modified list of responsive sizes
	 */
	function trx_addons_vc_sass_responsive( $list ) {
		if ( ! isset( $list['md_lg'] ) ) {
			$list['md_lg'] = array(
									'min' => $list['sm']['max'] + 1,
									'max' => $list['lg']['max']
									);
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_vc_edit_post_link' ) ) {
	add_filter( "edit_post_link", 'trx_addons_vc_edit_post_link', 100, 3 );
	/**
	 * Add classes 'post_meta' and 'post_meta_edit' to the VC inline link in the post meta
	 * 
	 * @hook edit_post_link
	 * 
	 * @param string $link  Link to the post edit
	 * @param int $id       Post ID
	 * @param string $text  Link text
	 * 
	 * @return string       Modified link
	 */
	function trx_addons_vc_edit_post_link( $link, $id = 0, $text = '' ) {
		return str_replace( 'vc_inline-link', 'post_meta_item post_meta_edit vc_inline-link', $link );
	}
}

if ( ! function_exists( 'trx_addons_vc_disable_load_animation' ) ) {
	add_filter( "trx_addons_filter_disable_load_animation", 'trx_addons_vc_disable_load_animation', 10, 1 );
	/**
	 * Disable load animations if VC is in the frontend editor mode
	 * 
	 * @hook trx_addons_filter_disable_load_animation
	 * 
	 * @param boolean $disable  Disable load animation or not
	 * 
	 * @return boolean          Modified value
	 */
	function trx_addons_vc_disable_load_animation( $disable = false ) {
		if ( trx_addons_exists_vc() && trx_addons_vc_is_frontend() ) {
			$disable = true;
		}
		return $disable;
	}
}



// Modify standard VC shortcodes params
//------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_vc_add_params' ) ) {
	add_action( 'init', 'trx_addons_vc_add_params' );
	/**
	 * Add new parameters to the standard VC shortcodes
	 * 
	 * @hook init
	 */
	function trx_addons_vc_add_params() {

		if ( trx_addons_exists_vc() ) {

			// Alter height for Empty Space
			vc_add_param("vc_empty_space", array(
				"param_name" => "alter_height",
				"heading" => esc_html__("Alter height", 'trx_addons'),
				"description" => wp_kses_data( __("Select alternative height instead value from the field above", 'trx_addons') ),
				"admin_label" => true,
				"value" => array_flip(trx_addons_get_list_sc_empty_space_heights()),
				"type" => "dropdown"
			));

			// Add Narrow style to the Progress bars
			vc_add_param("vc_progress_bar", array(
				"param_name" => "narrow",
				"heading" => esc_html__("Narrow", 'trx_addons'),
				"description" => wp_kses_data( __("Use narrow style for the progress bar", 'trx_addons') ),
				"std" => 0,
				"value" => array(esc_html__("Narrow style", 'trx_addons') => "1" ),
				"type" => "checkbox"
			));
			
			// Add param 'Closeable' to the Message Box
			vc_add_param("vc_message", array(
				"param_name" => "closeable",
				"heading" => esc_html__("Closeable", 'trx_addons'),
				"description" => wp_kses_data( __("Add 'Close' button to the message box", 'trx_addons') ),
				"std" => 0,
				"value" => array(esc_html__("Closeable", 'trx_addons') => "1" ),
				"type" => "checkbox"
			));

			// Add 'Hide on xxx'
			$sc_list = apply_filters('trx_addons_filter_add_hide_in_vc', array('vc_empty_space'));
			foreach ($sc_list as $sc) {
				$params = trx_addons_vc_add_hide_param();
				foreach ($params as $param)
					vc_add_param($sc, $param);
			}

			// Add 'Fix column' to the columns
			$param = array(
				"param_name" => "fix_column",
				"heading" => esc_html__("Fix column", 'trx_addons'),
				"description" => wp_kses_data( __("Fix this column when page scrolling. Attention! At least one column in the row must have a greater height than this column", 'trx_addons') ),
				"group" => esc_html__('Effects', 'trx_addons'),
				'edit_field_class' => 'vc_col-sm-4',
				"std" => "0",
				"value" => array(esc_html__("Fix column", 'trx_addons') => "1" ),
				"type" => "checkbox"
			);
			vc_add_param('vc_column', $param);
			vc_add_param('vc_column_inner', $param);

			// Add 'Shift X' to the columns
			$param = array(
				"param_name" => "shift_x",
				"heading" => esc_html__("The X-axis shift", 'trx_addons'),
				"description" => wp_kses_data( __("Shift this column along the X-axis", 'trx_addons') ),
				"group" => esc_html__('Effects', 'trx_addons'),
				'dependency' => array(
					'element' => 'fix_column',
					'is_empty' => true
				),
				'edit_field_class' => 'vc_col-sm-4',
				"value" => array_flip(trx_addons_get_list_sc_content_shift()),
				"std" => "none",
				"type" => "dropdown"
			);
			vc_add_param('vc_column', $param);
			vc_add_param('vc_column_inner', $param);

			// Add 'Shift Y' to the columns
			$param = array(
				"param_name" => "shift_y",
				"heading" => esc_html__("The Y-axis shift", 'trx_addons'),
				"description" => wp_kses_data( __("Shift this column along the Y-axis", 'trx_addons') ),
				"group" => esc_html__('Effects', 'trx_addons'),
				'dependency' => array(
					'element' => 'fix_column',
					'is_empty' => true
				),
				'edit_field_class' => 'vc_col-sm-4',
				"value" => array_flip(trx_addons_get_list_sc_content_shift()),
				"std" => "none",
				"type" => "dropdown"
			);
			vc_add_param('vc_column', $param);
			vc_add_param('vc_column_inner', $param);

			// Add 'Extra bg' to the columns and Text Block
			$param = array(
				"param_name" => "extra_bg",
				"heading" => esc_html__("Extend background", 'trx_addons'),
				"description" => wp_kses_data( __("Extend background color of the column", 'trx_addons') ),
				"group" => esc_html__('Design Options', 'trx_addons'),
				'edit_field_class' => 'vc_col-sm-6',
				"value" => array_flip(trx_addons_get_list_sc_content_extra_bg()),
				"std" => "none",
				"type" => "dropdown"
			);
			vc_add_param('vc_column', $param);
			vc_add_param('vc_column_inner', $param);
			vc_add_param('vc_column_text', $param);

			// Add 'Bg mask' to the rows and columns and Text Block
			$param = array(
				"param_name" => "extra_bg_mask",
				"heading" => esc_html__("Background mask", 'trx_addons'),
				"description" => wp_kses_data( __("Specify opacity of the background color to use it as mask for the background image", 'trx_addons') ),
				"group" => esc_html__('Design Options', 'trx_addons'),
				'edit_field_class' => 'vc_col-sm-6',
				"value" => array_flip(trx_addons_get_list_sc_content_extra_bg_mask()),
				"std" => "none",
				"type" => "dropdown"
			);
			vc_add_param('vc_row', $param);
			vc_add_param('vc_row_inner', $param);
			vc_add_param('vc_column', $param);
			vc_add_param('vc_column_inner', $param);
			vc_add_param('vc_column_text', $param);

			// Add 'Hide bg image on XXX' to the rows
			$param = array(
				"param_name" => "hide_bg_image_on_tablet",
				"heading" => esc_html__("Hide bg image on tablet", 'trx_addons'),
				"description" => wp_kses_data( __("Hide background image on the tablets", 'trx_addons') ),
				"group" => esc_html__('Design Options', 'trx_addons'),
				'edit_field_class' => 'vc_col-sm-3',
				"std" => "0",
				"value" => array(esc_html__("Hide bg image on tablet", 'trx_addons') => "1" ),
				"type" => "checkbox"
			);
			vc_add_param('vc_row', $param);
			vc_add_param('vc_row_inner', $param);
			vc_add_param('vc_column', $param);
			vc_add_param('vc_column_inner', $param);

			$param = array(
				"param_name" => "hide_bg_image_on_mobile",
				"heading" => esc_html__("Hide bg image on mobile", 'trx_addons'),
				"description" => wp_kses_data( __("Hide background image on the mobile devices", 'trx_addons') ),
				"group" => esc_html__('Design Options', 'trx_addons'),
				'edit_field_class' => 'vc_col-sm-3',
				"std" => "0",
				"value" => array(esc_html__("Hide bg image on mobile", 'trx_addons') => "1" ),
				"type" => "checkbox"
			);
			vc_add_param('vc_row', $param);
			vc_add_param('vc_row_inner', $param);
			vc_add_param('vc_column', $param);
			vc_add_param('vc_column_inner', $param);

			// Add 'Shape Divider' params to the rows
			global $TRX_ADDONS_STORAGE;
			if ( ! empty( $TRX_ADDONS_STORAGE['shapes_list'] ) ) {
				$shapes = array(
					'none' => trx_addons_get_not_selected_text( esc_html__( 'Not selected', 'trx_addons' ) )
				);
				foreach ( $TRX_ADDONS_STORAGE['shapes_list'] as $shape ) {
					$shape_name = pathinfo( $shape, PATHINFO_FILENAME );
					$shapes[ $shape_name ] = ucfirst( str_replace( '_', ' ', $shape_name ) );
				}
				foreach ( array('top', 'bottom') as $side ) {

					// Shape
					$param = array(
						"param_name" => "shape_divider_{$side}",
						"heading" => sprintf(__("Shape Divider %s", 'trx_addons'), ucfirst($side)),
						"description" => wp_kses_data( sprintf(__("Select shape to use it as divider at the %s of this row", 'trx_addons'), $side) ),
						"group" => esc_html__('Shapes', 'trx_addons'),
						"value" => array_flip($shapes),
						"std" => "none",
						"type" => "dropdown"
					);
					vc_add_param('vc_row', $param);
					vc_add_param('vc_row_inner', $param);

					// Bring to Front
					$param = array(
						"param_name" => "shape_divider_{$side}_front",
						"heading" => esc_html__("Bring to Front", 'trx_addons'),
						"description" => wp_kses_data( __("Check to place this shape over of the row. By default, shape is under the row", 'trx_addons') ),
						"group" => esc_html__('Shapes', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => 0,
						"value" => array(esc_html__("Bring to Front", 'trx_addons') => 1 ),
						"type" => "checkbox"
					);
					vc_add_param('vc_row', $param);
					vc_add_param('vc_row_inner', $param);

					// Color
					$param = array(
						"param_name" => "shape_divider_{$side}_color",
						"heading" => esc_html__("Color", 'trx_addons'),
						"description" => wp_kses_data( __("Specify color for all filled areas in the shape. Default color is white", 'trx_addons') ),
						"group" => esc_html__('Shapes', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "",
						"type" => "colorpicker"
					);
					vc_add_param('vc_row', $param);
					vc_add_param('vc_row_inner', $param);

					// Height
					$param = array(
						"param_name" => "shape_divider_{$side}_height",
						"heading" => esc_html__("Height", 'trx_addons'),
						"description" => wp_kses_data( __("Specify height of the shape. If empty - use default height", 'trx_addons') ),
						"group" => esc_html__('Shapes', 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "",
						"type" => "textfield"
					);
					vc_add_param('vc_row', $param);
					vc_add_param('vc_row_inner', $param);
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_vc_add_params_classes' ) ) {
	if ( trx_addons_exists_vc() ) {
		add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'trx_addons_vc_add_params_classes', 10, 3 );
	}
	/**
	 * Add classes to the standard VC shortcodes
	 * 
	 * @hooked VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG
	 * 
	 * @trigger trx_addons_filter_add_hide_in_vc
	 *
	 * @param string $classes  CSS classes
	 * @param string $sc       Shortcode name
	 * @param array $atts      Shortcode attributes
	 * 
	 * @return string          Modified CSS classes
	 */
	function trx_addons_vc_add_params_classes( $classes, $sc, $atts ) {

		// Add 'hide_on_xxx'
		if ( in_array( $sc, apply_filters( 'trx_addons_filter_add_hide_in_vc', array( 'vc_empty_space' ) ) ) ) {
			if ( ! empty( $atts['hide_on_wide'] ) )		$classes .= ( $classes ? ' ' : '' ) . 'hide_on_wide';
			if ( ! empty( $atts['hide_on_desktop'] ) )	$classes .= ( $classes ? ' ' : '' ) . 'hide_on_desktop';
			if ( ! empty( $atts['hide_on_notebook'] ) )	$classes .= ( $classes ? ' ' : '' ) . 'hide_on_notebook';
			if ( ! empty( $atts['hide_on_tablet'] ) )	$classes .= ( $classes ? ' ' : '' ) . 'hide_on_tablet';
			if ( ! empty( $atts['hide_on_mobile'] ) )	$classes .= ( $classes ? ' ' : '' ) . 'hide_on_mobile';
		}

		// Add other specific classes
		if ( in_array( $sc, array( 'vc_empty_space' ) ) ) {
			if ( ! empty( $atts['alter_height'] ) && ! trx_addons_is_off( $atts['alter_height'] ) ) {
				$classes .= ( $classes ? ' ' : '' ) . 'sc_height_' . $atts['alter_height'];
			}
		} else if ( in_array( $sc, array( 'vc_progress_bar' ) ) ) {
			if ( ! empty( $atts['narrow'] ) && (int) $atts['narrow'] == 1 ) {
				$classes .= ( $classes ? ' ' : '' ) . 'vc_progress_bar_narrow';
			}
		} else if ( in_array( $sc, array( 'vc_message' ) ) ) {
			if ( ! empty( $atts['closeable'] ) && (int) $atts['closeable'] == 1 ) {
				$classes .= ( $classes ? ' ' : '' ) . 'vc_message_box_closeable';
			}
		}

		// Add 'sc_column_fixed' and 'shift_x/y'
		if ( in_array( $sc, array( 'vc_column', 'vc_column_inner' ) ) ) {
			if ( ! empty( $atts['fix_column'] ) ) {
				$classes .= ( $classes ? ' ' : '' ) . 'sc_column_fixed';
			}
			if ( empty( $atts['fix_column'] ) && ! empty( $atts['shift_x'] ) && $atts['shift_x'] != 'none' ) { 
				$classes .= ( $classes ? ' ' : '' ) . 'sc_shift_x_' . esc_attr( $atts['shift_x'] );
			}
			if ( empty( $atts['fix_column'] ) && ! empty( $atts['shift_y'] ) && $atts['shift_y'] != 'none' ) { 
				$classes .= ( $classes ? ' ' : '' ) . 'sc_shift_y_' . esc_attr( $atts['shift_y'] );
			}
		}

		// Add 'extra_bg'
		if ( in_array( $sc, array( 'vc_column', 'vc_column_inner', 'vc_column_text' ) ) ) {
			if ( ! empty( $atts['extra_bg'] ) && $atts['extra_bg'] != 'none' ) { 
				$classes .= ( $classes ? ' ' : '' ) . 'sc_extra_bg_' . esc_attr( $atts['extra_bg'] );
			}
		}

		// Add 'bg_mask'
		if ( in_array( $sc, array( 'vc_row', 'vc_row_inner', 'vc_column', 'vc_column_inner', 'vc_column_text' ) ) ) {
			if ( ! empty( $atts['extra_bg_mask'] ) && $atts['extra_bg_mask'] != 'none' ) { 
				$classes .= ( $classes ? ' ' : '' ) . 'sc_bg_mask_' . esc_attr( $atts['extra_bg_mask'] );
			}
		}

		// Add 'Hide bg image on XXX'
		if ( in_array( $sc, array( 'vc_row', 'vc_row_inner', 'vc_column', 'vc_column_inner' ) ) ) {
			if ( ! empty( $atts['hide_bg_image_on_tablet'] ) ) { 
				$classes .= ( $classes ? ' ' : '' ) . 'hide_bg_image_on_tablet';
			}
			if ( ! empty( $atts['hide_bg_image_on_mobile'] ) ) {
				$classes .= ( $classes ? ' ' : '' ) . 'hide_bg_image_on_mobile';
			}
		}

		// Add 'Shape Divider'
		if ( in_array( $sc, array( 'vc_row', 'vc_row_inner' ) ) ) {
			foreach ( array( 'top', 'bottom' ) as $side ) {
				if ( ! empty( $atts["shape_divider_{$side}"] ) ) {
					$classes .= ( $classes ? ' ' : '' ) . "shape_divider_{$side}-" . esc_attr( $atts["shape_divider_{$side}"] );
				}
				if ( ! empty( $atts["shape_divider_{$side}_front"] ) && $atts["shape_divider_{$side}_front"] > 0 ) {
					$classes .= ( $classes ? ' ' : '' ) . "shape_divider_{$side}_front";
				}
				if ( ! empty( $atts["shape_divider_{$side}_color"] ) ) {
					$classes .= ( $classes ? ' ' : '' ) . trx_addons_add_inline_css_class( sprintf( 'fill:%s !important', esc_attr( $atts["shape_divider_{$side}_color"] ) ), '.vc-shape-fill' );
				}
				if ( ! empty( $atts["shape_divider_{$side}_height"] ) ) {
					$classes .= ( $classes ? ' ' : '' ) . trx_addons_add_inline_css_class( sprintf( 'height:%s;', esc_attr( trx_addons_prepare_css_value( $atts["shape_divider_{$side}_height"] ) ) ), 'svg' );
				}
			}
		}

		return $classes;
	}
}

if ( ! function_exists( 'trx_addons_vc_content_filter_after' ) ) {
	add_filter( 'vc_shortcode_content_filter_after', 'trx_addons_vc_content_filter_after', 10, 2 );
	/**
	 * Prepare VC shortcodes html output: add shapes to the VC rows 
	 *
	 * @param string $html  Shortcode html output
	 * @param string $sc    Shortcode name
	 * 
	 * @return string       Modified html output
	 */
	function trx_addons_vc_content_filter_after( $html, $sc ) {
		if ( in_array( $sc, array( 'vc_row', 'vc_row_inner' ) ) ) {
			$shapes_path = apply_filters( 'trx_addons_filter_shapes_path', 'css/shapes' );
			foreach ( array('top', 'bottom') as $side ) {
				$reg_exp = '~(<div[^>]*class="vc_row[\s][^"]*shape_divider_'.$side.'-([^\s"]+)[\s"]+.*>)~U';
				if ( preg_match( $reg_exp, $html, $matches ) && ! empty( $matches[2] ) && ! trx_addons_is_off( $matches[2] ) ) {
					$shape_name = trx_addons_esc( $matches[2] );
					$shape_dir = trx_addons_get_file_dir( "{$shapes_path}/{$shape_name}.svg" );
					if ( ! empty( $shape_dir ) ) {
						$html = preg_replace( $reg_exp,
											'$1'
											. '<div class="vc_shape_divider vc_shape_divider_'.esc_attr($side).' vc_shape_divider_name_'.esc_attr($shape_name).'">'
												. strip_tags( trx_addons_fgc( $shape_dir ), '<svg><path>' )
											. '</div>',
											$html );
					}
				}
			}
		}
		return $html;
	}
}

if ( ! function_exists( 'trx_addons_vc_css_editor' ) ) {
	add_filter( 'vc_css_editor', 'trx_addons_vc_css_editor' );
	/**
	 * Add new parameter 'Background position' to the VC CSS-editor
	 *
	 * @param string $output  VC CSS editor output
	 * 
	 * @return string         Modified VC CSS editor output
	 */
	function trx_addons_vc_css_editor( $output ) {
		return str_replace(
			'<div class="vc_background-style">',
			'<div class="vc_background-position">'
				. '<select name="background_position" class="vc_background-position">'
					. '<option value="top left">' . esc_html__('Top Left', 'trx_addons') . '</option>'
					. '<option value="top center">' . esc_html__('Top Center', 'trx_addons') . '</option>'
					. '<option value="top right">' . esc_html__('Top Right', 'trx_addons') . '</option>'
					. '<option value="center left">' . esc_html__('Center Left', 'trx_addons') . '</option>'
					. '<option value="center">' . esc_html__('Center', 'trx_addons') . '</option>'
					. '<option value="center right">' . esc_html__('Center Right', 'trx_addons') . '</option>'
					. '<option value="bottom left">' . esc_html__('Bottom Left', 'trx_addons') . '</option>'
					. '<option value="bottom center">' . esc_html__('Bottom Center', 'trx_addons') . '</option>'
					. '<option value="bottom right">' . esc_html__('Bottom Right', 'trx_addons') . '</option>'
				. '</select>'
			. '</div>'
			. '<div class="vc_background-style">',
			$output );
	}
}

if ( ! function_exists('trx_addons_vc_prepare_atts' ) ) {
	add_filter( 'trx_addons_filter_sc_prepare_atts', 'trx_addons_vc_prepare_atts', 10, 2 );
	/**
	 * Prepare VC shortcode attributes: add custom CSS class and unsafe item description
	 *
	 * @param array  $atts  Shortcode attributes
	 * @param string $sc    Shortcode name
	 * 
	 * @return array        Modified attributes
	 */
	function trx_addons_vc_prepare_atts( $atts, $sc ) {
		// Unsafe item description
		if ( ! empty( $atts['description'] ) && function_exists( 'vc_value_from_safe' ) ) {
			$atts['description'] = trim( vc_value_from_safe( $atts['description'] ) );
		}
		// Add custom CSS class
		if ( ! empty( $atts['css'] )
			&& ( trx_addons_sc_stack_check( 'show_layout_vc' ) || strpos( $atts['css'], '.vc_custom_' ) !== false )
			&& defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' )
			&& function_exists( 'vc_shortcode_custom_css_class' )
		) {
			$atts['class'] = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG,
											( ! empty($atts['class'] ) ? $atts['class'] . ' ' : '' ) . vc_shortcode_custom_css_class( $atts['css'], ' ' ),
											$sc,
											$atts
										);
			$atts['css'] = '';
		}
		return $atts;
	}
}



// Shortcode's common params for WPBakery PageBuilder
//---------------------------------------------------------

if ( ! function_exists( 'trx_addons_vc_add_id_param' ) ) {
	/**
	 * Return ID, Class, CSS params for VC
	 * 
	 * @trigger trx_addons_filter_vc_add_id_param
	 * 
	 * @param boolean|string $group  Name of the group to add params.
	 * 								 If false - use default name 'ID & Class'.
	 * 								 If empty string - add params without group
	 */
	function trx_addons_vc_add_id_param( $group = false ) {
		$params = array(
					array(
						"param_name" => "id",
						"heading" => esc_html__("Element ID", 'trx_addons'),
						"description" => wp_kses_data( __("ID for current element", 'trx_addons') ),
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						"param_name" => "class",
						"heading" => esc_html__("Element CSS class", 'trx_addons'),
						"description" => wp_kses_data( __("CSS class for current element", 'trx_addons') ),
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						'param_name' => 'css',
						'heading' => __( 'CSS box', 'trx_addons' ),
						'group' => __( 'Design Options', 'trx_addons' ),
						'type' => 'css_editor'
					)
				);

		// Add param 'group' if not empty
		if ( $group === false ) {
			$group = esc_html__('ID &amp; Class', 'trx_addons');
		}
		
		if ( ! empty( $group ) ) {
			$params[0]['group'] = $group;
			$params[1]['group'] = $group;
		}

		return apply_filters( 'trx_addons_filter_vc_add_id_param', $params, $group );
	}
}

if ( ! function_exists( 'trx_addons_vc_add_slider_param' ) ) {
	/**
	 * Return Slider param for VC
	 * 
	 * @trigger trx_addons_filter_vc_add_slider_param
	 * 
	 * @param boolean|string $group  Name of the group to add params.
	 * 								 If false - use default name 'Slider'.
	 * 								 If empty string - add params without group
	 */
	function trx_addons_vc_add_slider_param( $group = false ) {
		$params = array(
					array(
						"param_name" => "slider",
						"heading" => esc_html__("Slider", 'trx_addons'),
						"description" => wp_kses_data( __("Show items as slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"admin_label" => true,
						"std" => "0",
						"value" => array(esc_html__("Slider", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "slider_effect",
						"heading" => esc_html__("Effect", 'trx_addons'),
						"description" => wp_kses_data( __("Select slides effect of the Swiper slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"value" => array_flip(trx_addons_get_list_sc_slider_effects()),
						"std" => "slide",
				        'save_always' => true,
						"type" => "dropdown"
					),
					array(
						"param_name" => "slides_space",
						"heading" => esc_html__("Space", 'trx_addons'),
						"description" => wp_kses_data( __("Space between slides", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'slider',
							'value' => '1'
						),
						"std" => "",
						"type" => "textfield"
					),
					array(
						"param_name" => "slides_centered",
						"heading" => esc_html__("Slides centered", 'trx_addons'),
						"description" => wp_kses_data( __("Center active slide", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'slider',
							'value' => '1'
						),
						"std" => "0",
						"value" => array(esc_html__("Centered", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "slides_overflow",
						"heading" => esc_html__("Slides overflow visible", 'trx_addons'),
						"description" => wp_kses_data( __("Don't hide slides outside the borders of the viewport", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'slider',
							'value' => '1'
						),
						"std" => "0",
						"value" => array(esc_html__("Overflow visible", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "slider_mouse_wheel",
						"heading" => esc_html__("Enable mouse wheel", 'trx_addons'),
						"description" => wp_kses_data( __("Enable mouse wheel to control slides", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'slider',
							'value' => '1'
						),
						"std" => "0",
						"value" => array(esc_html__("Enable", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "slider_autoplay",
						"heading" => esc_html__("Enable autoplay", 'trx_addons'),
						"description" => wp_kses_data( __("Enable autoplay for this slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'slider',
							'value' => '1'
						),
						"std" => "1",
						"value" => array(esc_html__("Enable autoplay", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "slider_loop",
						"heading" => esc_html__("Enable loop mode", 'trx_addons'),
						"description" => wp_kses_data( __("Allow infinite loop slides", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'slider',
							'value' => '1'
						),
						"std" => "1",
						"value" => array(esc_html__("Enable", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "slider_free_mode",
						"heading" => esc_html__("Enable free mode", 'trx_addons'),
						"description" => wp_kses_data( __("Free mode - slides will not have fixed positions", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'slider',
							'value' => '1'
						),
						"std" => "0",
						"value" => array(esc_html__("Enable", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "slider_controls",
						"heading" => esc_html__("Slider controls", 'trx_addons'),
						"description" => wp_kses_data( __("Show arrows in the slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'slider',
							'value' => '1'
						),
						"std" => "none",
						"value" => array_flip(trx_addons_get_list_sc_slider_controls()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slider_pagination",
						"heading" => esc_html__("Slider pagination", 'trx_addons'),
						"description" => wp_kses_data( __("Show pagination in the slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'slider',
							'value' => '1'
						),
						"std" => "none",
						"value" => array_flip(trx_addons_get_list_sc_slider_paginations()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slider_pagination_type",
						"heading" => esc_html__("Slider pagination type", 'trx_addons'),
						"description" => wp_kses_data( __("Select type of the pagination in the slider", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'slider_pagination',
							'not_equal' => 'none'
						),
						"std" => "bullets",
						"value" => array_flip(trx_addons_get_list_sc_slider_paginations_types()),
						"type" => "dropdown"
					)
				);

		// Add param 'group' if not empty
		if ( $group === false ) {
			$group = esc_html__('Slider', 'trx_addons');
		}
		if ( ! empty( $group ) ) {
			foreach ( $params as $k => $v ) {
				$params[$k]['group'] = $group;
			}
		}

		return apply_filters( 'trx_addons_filter_vc_add_slider_param', $params, $group );
	}
}

if ( ! function_exists( 'trx_addons_vc_add_title_param' ) ) {
	/**
	 * Add a group with 'Title' parameters to the VC shortcodes
	 * 
	 * @trigger trx_addons_filter_vc_add_title_param
	 *
	 * @param boolean|string $group  Name of the group to add params.
	 * 								 If false - use default name 'Titles'.
	 * 								 If empty string - add params without group
	 * @param boolean $button        Add button or not
	 * 
	 * @return array                 Array of the params
	 */
	function trx_addons_vc_add_title_param( $group = false, $button = true ) {
		$params = array(
					array(
						"param_name" => "title_style",
						"heading" => esc_html__("Title style", 'trx_addons'),
						"description" => wp_kses_data( __("Select style of the title and subtitle", 'trx_addons') ),
						"admin_label" => true,
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "default",
				        'save_always' => true,
						"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'title'), 'trx_sc_title')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "title_tag",
						"heading" => esc_html__("Title tag", 'trx_addons'),
						"description" => wp_kses_data( __("Select tag (level) of the title", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						"std" => "none",
						"value" => array_flip(trx_addons_get_list_sc_title_tags()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "title_align",
						"heading" => esc_html__("Title alignment", 'trx_addons'),
						"description" => wp_kses_data( __("Select alignment of the title, subtitle and description", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "none",
						"value" => array_flip(trx_addons_get_list_sc_aligns()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", 'trx_addons'),
						"description" => wp_kses_data( __("Title of the block. Enclose any words in {{ and }} to make them italic or in (( and )) to make them bold. If title style is 'accent' - bolded element styled as shadow, italic - as a filled circle", 'trx_addons') ),
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						'param_name' => 'title_color',
						'heading' => esc_html__( 'Color', 'trx_addons' ),
						'description' => esc_html__( 'Title custom color', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						'std' => '',
						'type' => 'colorpicker'
					),
					array(
						'param_name' => 'title_color2',
						'heading' => esc_html__( 'Color 2', 'trx_addons' ),
						'description' => esc_html__( 'Used for gradient.', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4',
						'std' => '',
						'dependency' => array(
							'element' => 'title_style',
							'value' => array('gradient')
						),
						'type' => 'colorpicker'
					),
					array(
						"param_name" => "gradient_fill",
						"heading" => esc_html__("Gradient fill", 'trx_addons'),
						"description" => wp_kses_data( __("Select a gradient fill type", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "block",
						"value" => array_flip(trx_addons_get_list_sc_title_gradient_fills()),
						"type" => "dropdown"
					),
					array(
						'param_name' => 'gradient_direction',
						'heading' => esc_html__( 'Gradient direction', 'trx_addons' ),
						'description' => esc_html__( 'Gradient direction in degress (0 - 360)', 'trx_addons' ),
						'admin_label' => true,
						'edit_field_class' => 'vc_col-sm-4',
						'std' => '0',
						'dependency' => array(
							'element' => 'title_style',
							'value' => array('gradient')
						),
						'type' => 'textfield',
					),
					array(
						'param_name' => 'title_border_color',
						'heading' => esc_html__( 'Border color', 'trx_addons' ),
						'description' => esc_html__( 'Title border color', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4',
						'std' => '',
						'type' => 'colorpicker'
					),
					array(
						'param_name' => 'title_border_width',
						'heading' => esc_html__( 'Border width', 'trx_addons' ),
						'description' => esc_html__( 'Title border width (in px)', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4',
						'std' => '',
						'type' => 'textfield',
					),
					array(
						"param_name" => "title_bg_image",
						"heading" => esc_html__("Background image", 'trx_addons'),
						"description" => wp_kses_data( __("Select or upload image to use it as a text background", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "attach_image"
					),
					// Dual title
					array(
						"param_name" => "title2",
						"heading" => esc_html__("Title part 2", 'trx_addons'),
						"description" => wp_kses_data( __("Use this parameter if you want to separate title parts with different color, border or background", 'trx_addons') ),
						"type" => "textfield"
					),
					array(
						'param_name' => 'title2_color',
						'heading' => esc_html__( 'Color', 'trx_addons' ),
						'description' => esc_html__( 'Title 2 custom color', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						'std' => '',
						'type' => 'colorpicker'
					),
					array(
						'param_name' => 'title2_color2',
						'heading' => esc_html__( 'Color 2', 'trx_addons' ),
						'description' => esc_html__( 'Used for gradient.', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4',
						'std' => '',
						'dependency' => array(
							'element' => 'title_style',
							'value' => array('gradient')
						),
						'type' => 'colorpicker'
					),
					array(
						"param_name" => "gradient_fill2",
						"heading" => esc_html__("Gradient fill", 'trx_addons'),
						"description" => wp_kses_data( __("Select a gradient fill type", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "block",
						"value" => array_flip(trx_addons_get_list_sc_title_gradient_fills()),
						"type" => "dropdown"
					),
					array(
						'param_name' => 'gradient_direction2',
						'heading' => esc_html__( 'Gradient direction', 'trx_addons' ),
						'description' => esc_html__( 'Gradient direction in degress (0 - 360)', 'trx_addons' ),
						'admin_label' => true,
						'edit_field_class' => 'vc_col-sm-4',
						'std' => '0',
						'dependency' => array(
							'element' => 'title_style',
							'value' => array('gradient')
						),
						'type' => 'textfield',
					),
					array(
						'param_name' => 'title2_border_color',
						'heading' => esc_html__( 'Border color', 'trx_addons' ),
						'description' => esc_html__( 'Title 2 border color', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						'std' => '',
						'type' => 'colorpicker'
					),
					array(
						'param_name' => 'title2_border_width',
						'heading' => esc_html__( 'Border width', 'trx_addons' ),
						'description' => esc_html__( 'Title 2 border width (in px)', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4',
						'std' => '',
						'type' => 'textfield',
					),
					array(
						"param_name" => "title2_bg_image",
						"heading" => esc_html__("Background image", 'trx_addons'),
						"description" => wp_kses_data( __("Select or upload image to use it as a text background", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "attach_image"
					),
					array(
						"param_name" => "typed",
						"heading" => esc_html__("Use autotype", 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"admin_label" => true,
						'dependency' => array(
							'element' => 'title',
							'not_empty' => true
						),
						"std" => "0",
						"value" => array(esc_html__("Use autotype", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "typed_loop",
						"heading" => esc_html__("Autotype loop", 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'typed',
							'value' => '1'
						),
						"std" => "1",
						"value" => array(esc_html__("Loop typing", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "typed_cursor",
						"heading" => esc_html__("Autotype cursor", 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'typed',
							'value' => '1'
						),
						"std" => "1",
						"value" => array(esc_html__("Display cursor", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "typed_strings",
						"heading" => esc_html__("Alternative strings", 'trx_addons'),
						'description' => __( "Alternative strings to type. Attention! First string must be equal of the part of the title.", 'trx_addons' ),
						'dependency' => array(
							'element' => 'typed',
							'value' => '1'
						),
						"std" => "",
						"rows" => 5,
						"type" => "textarea"
					),
					array(
						'param_name' => 'typed_color',
						'heading' => esc_html__( 'Autotype color', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						'dependency' => array(
							'element' => 'typed',
							'value' => '1'
						),
						'std' => '',
						'type' => 'colorpicker'
					),
					array(
						'param_name' => 'typed_speed',
						'heading' => esc_html__( 'Autotype speed', 'trx_addons' ),
						'description' => __( "Typing speed from 1 (min) to 10 (max)", 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'typed',
							'value' => '1'
						),
						'std' => '6',
						'type' => 'textfield'
					),
					array(
						'param_name' => 'typed_delay',
						'heading' => esc_html__( 'Autotype delay (in sec.)', 'trx_addons' ),
						'description' => __( "Delay before erase text", 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'typed',
							'value' => '1'
						),
						'std' => '1',
						'type' => 'textfield'
					),
					array(
						"param_name" => "subtitle",
						"heading" => esc_html__("Subtitle", 'trx_addons'),
						"description" => wp_kses_data( __("Subtitle of the block", 'trx_addons') ),
						"type" => "textfield"
					),
					array(
						"param_name" => "subtitle_align",
						"heading" => esc_html__("Subtitle alignment", 'trx_addons'),
						"description" => wp_kses_data( __("Select alignment of the subtitle", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "none",
						"value" => array_flip(trx_addons_get_list_sc_aligns()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "subtitle_position",
						"heading" => esc_html__("Subtitle position", 'trx_addons'),
						"description" => wp_kses_data( __("Select position of the subtitle", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => trx_addons_get_setting('subtitle_above_title') ? 'above' : 'below',
						"value" => array_flip(trx_addons_get_list_sc_subtitle_positions()),
						"type" => "dropdown"
					),
					array(
						'param_name' => 'subtitle_color',
						'heading' => esc_html__( 'Subtitle color', 'trx_addons' ),
						'description' => esc_html__( 'Subtitle custom color', 'trx_addons' ),
						'edit_field_class' => 'vc_col-sm-4',
						'std' => '',
						'type' => 'colorpicker'
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", 'trx_addons'),
						"description" => wp_kses_data( __("Description of the block", 'trx_addons') ),
						"type" => "textarea_safe"
					),
					array(
						'param_name' => 'description_color',
						'heading' => esc_html__( 'Description color', 'trx_addons' ),
						'description' => esc_html__( 'Description custom color', 'trx_addons' ),
						'std' => '',
						'type' => 'colorpicker'
					),
				);
		
		// Add button's params
		if ( $button ) {
			$params = array_merge( $params, array(
					array(
						"param_name" => "link",
						"heading" => esc_html__("Button's URL", 'trx_addons'),
						"description" => wp_kses_data( __("Link URL for the button at the bottom of the block", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "textfield"
					),
					array(
						"param_name" => "link_text",
						"heading" => esc_html__("Button's text", 'trx_addons'),
						"description" => wp_kses_data( __("Caption for the button at the bottom of the block", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "textfield"
					),
					array(
						"param_name" => "link_size",
						"heading" => esc_html__("Button's size", 'trx_addons'),
						"description" => wp_kses_data( __("Select the size of the button", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
				        'save_always' => true,
						"std" => "normal",
						"value" => array_flip(trx_addons_get_list_sc_button_sizes()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "link_style",
						"heading" => esc_html__("Button's style", 'trx_addons'),
						"description" => wp_kses_data( __("Select the style (layout) of the button", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
				        'save_always' => true,
						"std" => "default",
						"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'button'), 'trx_sc_button')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "link_image",
						"heading" => esc_html__("Button's image", 'trx_addons'),
						"description" => wp_kses_data( __("Select the promo image from the library for this button", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "attach_image"
					),
					array(
						"param_name" => "new_window",
						"heading" => esc_html__("Open in the new window", 'trx_addons'),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "0",
						"value" => array(esc_html__("Open in the new window", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
			) );
		}

		// Add param 'group' if not empty
		if ( $group === false ) {
			$group = esc_html__('Titles', 'trx_addons');
		}
		if ( ! empty( $group ) ) {
			foreach ( $params as $k => $v ) {
				$params[$k]['group'] = $group;
			}
		}

		return apply_filters('trx_addons_filter_vc_add_title_param', $params, $group, $button);
	}
}

if ( ! function_exists( 'trx_addons_vc_add_query_param' ) ) {
	/**
	 * Add query params to VC shortcodes
	 *
	 * @trigger trx_addons_filter_vc_add_query_param
	 *
	 * @param boolean|string $group  Name of the group to add params.
	 * 								 If false - use default name 'Query'.
	 * 								 If empty string - add params without group
	 * 
	 * @return array                 List of params
	 */
	function trx_addons_vc_add_query_param( $group = false ) {
		$params = array(
					array(
						"param_name" => "ids",
						"heading" => esc_html__("IDs to show", 'trx_addons'),
						"description" => wp_kses_data( __("Comma separated list of IDs to display. If not empty, parameters 'cat', 'offset' and 'count' are ignored!", 'trx_addons') ),
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						"param_name" => "count",
						"heading" => esc_html__("Count", 'trx_addons'),
						"description" => wp_kses_data( __("The number of displayed posts. If IDs are used, this parameter is ignored.", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'ids',
							'is_empty' => true
						),
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						"param_name" => "columns",
						"heading" => esc_html__("Columns", 'trx_addons'),
						"description" => wp_kses_data( __("Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						"param_name" => "offset",
						"heading" => esc_html__("Offset", 'trx_addons'),
						"description" => wp_kses_data( __("Specify the number of items to be skipped before the displayed items.", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						'dependency' => array(
							'element' => 'ids',
							'is_empty' => true
						),
						"admin_label" => true,
						"type" => "textfield"
					),
					array(
						"param_name" => "orderby",
						"heading" => esc_html__("Order by", 'trx_addons'),
						"description" => wp_kses_data( __("Select how to sort the posts", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-6 vc_new_row',
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_sc_query_orderby()),
						"std" => "none",
						"type" => "dropdown"
					),
					array(
						"param_name" => "order",
						"heading" => esc_html__("Order", 'trx_addons'),
						"description" => wp_kses_data( __("Select sort order", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-6',
						"value" => array_flip(trx_addons_get_list_sc_query_orders()),
				        'save_always' => true,
						"std" => "asc",
						"type" => "dropdown"
					)
				);

		// Add param 'group' if not empty
		if ( $group === false ) {
			$group = esc_html__('Query', 'trx_addons');
		}
		if ( ! empty( $group ) ) {
			foreach ( $params as $k => $v ) {
				$params[$k]['group'] = $group;
			}
		}

		return apply_filters( 'trx_addons_filter_vc_add_query_param', $params, $group );
	}
}

if ( ! function_exists( 'trx_addons_vc_add_hide_param' ) ) {
	/**
	 * Add 'Hide on XXX' params to VC shortcodes
	 *
	 * @trigger trx_addons_filter_vc_add_hide_param
	 *
	 * @param boolean|string $group  Name of the group to add params.
	 * 								 If false - add params without group
	 * @param boolean $hide_on_frontpage  Add param 'Hide on frontpage'
	 * 
	 * @return array                 List of params
	 */
	function trx_addons_vc_add_hide_param( $group = false, $hide_on_frontpage = false ) {
		$params = array(
					array(
						"param_name" => "hide_on_wide",
						"heading" => esc_html__("Hide on wide", 'trx_addons'),
						"description" => wp_kses_data( __("Hide this item on wide screens", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"admin_label" => true,
						"std" => "0",
						"value" => array(esc_html__("Hide on wide", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "hide_on_desktop",
						"heading" => esc_html__("Hide on desktops", 'trx_addons'),
						"description" => wp_kses_data( __("Hide this item on desktops", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						"std" => "0",
						"value" => array(esc_html__("Hide on desktops", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "hide_on_notebook",
						"heading" => esc_html__("Hide on notebooks", 'trx_addons'),
						"description" => wp_kses_data( __("Hide this item on notebooks", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						"std" => "0",
						"value" => array(esc_html__("Hide on notebooks", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "hide_on_tablet",
						"heading" => esc_html__("Hide on tablets", 'trx_addons'),
						"description" => wp_kses_data( __("Hide this item on tablets", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						"std" => "0",
						"value" => array(esc_html__("Hide on tablets", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "hide_on_mobile",
						"heading" => esc_html__("Hide on mobile devices", 'trx_addons'),
						"description" => wp_kses_data( __("Hide this item on mobile devices", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						"std" => "0",
						"value" => array(esc_html__("Hide on mobile devices", 'trx_addons') => "1"),
						"type" => "checkbox"
					)
				);
		if ( $hide_on_frontpage ) {
			$params[] = array(
				"param_name" => "hide_on_frontpage",
				"heading" => esc_html__("Hide on Frontpage", 'trx_addons'),
				"description" => wp_kses_data( __("Hide this item on the Frontpage", 'trx_addons') ),
				'edit_field_class' => 'vc_col-sm-4 vc_new_row',
				"std" => "0",
				"value" => array(esc_html__("Hide on Frontpage", 'trx_addons') => "1" ),
				"type" => "checkbox"
			);
			$params[] = array(
				"param_name" => "hide_on_singular",
				"heading" => esc_html__("Hide on single posts and pages", 'trx_addons'),
				"description" => wp_kses_data( __("Hide this item on single posts and pages", 'trx_addons') ),
				'edit_field_class' => 'vc_col-sm-4',
				"std" => "0",
				"value" => array(esc_html__("Hide on single posts and pages", 'trx_addons') => "1" ),
				"type" => "checkbox"
			);
			$params[] = array(
				"param_name" => "hide_on_other",
				"heading" => esc_html__("Hide on other pages", 'trx_addons'),
				"description" => wp_kses_data( __("Hide this item on other pages (posts archive, category or taxonomy posts, author's posts, etc.)", 'trx_addons') ),
				'edit_field_class' => 'vc_col-sm-4',
				"std" => "0",
				"value" => array(esc_html__("Hide on other pages", 'trx_addons') => "1" ),
				"type" => "checkbox"
			);
		}

		// Add param 'group' if not empty
		if ( ! empty( $group ) ) {
			foreach ( $params as $k => $v ) {
				$params[$k]['group'] = $group;
			}
		}

		return apply_filters( 'trx_addons_filter_vc_add_hide_param', $params, $group );
	}
}

if ( ! function_exists( 'trx_addons_vc_add_icon_param' ) ) {
	/**
	 * Add param 'icon' to the VC shortcodes.
	 * If an internal setting 'icons_selector' is 'internal' - add internal popup with icons list.
	 * Otherwise - add param 'icon' with type 'iconpicker' of VC
	 * 
	 * @trigger trx_addons_filter_vc_add_icon_param
	 *
	 * @param boolean|string $group  Name of the group to add params.
	 * 								 If false - use default name 'Icons'.
	 * 								 If empty string - add params without group
	 * @param bool $only_socials     Add only socials icons
	 * @param string $style          Icons style
	 * 
	 * @return array                 List of the params
	 */
	function trx_addons_vc_add_icon_param( $group = false, $only_socials = false, $style = '' ) {
		if ( trx_addons_get_setting('icons_selector') == 'internal' ) {

			// Internal popup with icons list
			if ( empty( $style ) ) {
				$style = $only_socials ? trx_addons_get_setting('socials_type') : trx_addons_get_setting('icons_type');
			}
			$params = array(
				array(
					"param_name" => "icon",
					"heading" => esc_html__("Icon", 'trx_addons'),
					"description" => wp_kses_data( __("Select icon", 'trx_addons') ),
					"value" => trx_addons_get_list_icons($style),
					"std" => "",
					"style" => $style,
					"type" => "icons"
				)
			);

		} else {
			
			// Standard VC icons selector
			$params = array(
						array(
							'type' => 'dropdown',
							'heading' => __( 'Icon library', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							'value' => array(
								__( 'Font Awesome', 'trx_addons' ) => 'fontawesome',
	/*
								__( 'Open Iconic', 'trx_addons' ) => 'openiconic',
								__( 'Typicons', 'trx_addons' ) => 'typicons',
								__( 'Entypo', 'trx_addons' ) => 'entypo',
								__( 'Linecons', 'trx_addons' ) => 'linecons'
	*/
							),
							'std' => 'fontswesome',
							'param_name' => 'icon_type',
							'description' => __( 'Select icon library.', 'trx_addons' ),
						),
						array(
							'type' => 'iconpicker',
							'heading' => esc_html__( 'Icon', 'trx_addons' ),
							'description' => esc_html__( 'Select icon from library.', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-8',
							'param_name' => 'icon_fontawesome',
							'value' => '',
							'settings' => array(
								'emptyIcon' => true,						// default true, display an "EMPTY" icon?
								'iconsPerPage' => 4000,						// default 100, how many icons per/page to display
								'type' => 'fontawesome'
	
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'fontawesome',
							),
						),
	/*
						array(
							'type' => 'iconpicker',
							'heading' => esc_html__( 'Icon', 'trx_addons' ),
							'description' => esc_html__( 'Select icon from library.', 'trx_addons' ),
							'param_name' => 'icon_openiconic',
							'value' => '',
							'settings' => array(
								'emptyIcon' => true,						// default true, display an "EMPTY" icon?
								'iconsPerPage' => 4000,						// default 100, how many icons per/page to display
								'type' => 'openiconic'
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'openiconic',
							),
						),
						array(
							'type' => 'iconpicker',
							'heading' => esc_html__( 'Icon', 'trx_addons' ),
							'description' => esc_html__( 'Select icon from library.', 'trx_addons' ),
							'param_name' => 'icon_typicons',
							'value' => '',
							'settings' => array(
								'emptyIcon' => true,						// default true, display an "EMPTY" icon?
								'iconsPerPage' => 4000,						// default 100, how many icons per/page to display
								'type' => 'typicons',
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'typicons',
							),
						),
						array(
							'type' => 'iconpicker',
							'heading' => esc_html__( 'Icon', 'trx_addons' ),
							'description' => esc_html__( 'Select icon from library.', 'trx_addons' ),
							'param_name' => 'icon_entypo',
							'value' => '',
							'settings' => array(
								'emptyIcon' => true,						// default true, display an "EMPTY" icon?
								'iconsPerPage' => 4000,						// default 100, how many icons per/page to display
								'type' => 'entypo',
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'entypo',
							),
						),
						array(
							'type' => 'iconpicker',
							'heading' => esc_html__( 'Icon', 'trx_addons' ),
							'description' => esc_html__( 'Select icon from library.', 'trx_addons' ),
							'param_name' => 'icon_linecons',
							'value' => '',
							'settings' => array(
								'emptyIcon' => true,						// default true, display an "EMPTY" icon?
								'iconsPerPage' => 4000,						// default 100, how many icons per/page to display
								'type' => 'linecons',
							),
							'dependency' => array(
								'element' => 'icon_type',
								'value' => 'linecons',
							),
						)
	*/					
					);

		}
		
		// Add param 'group' if not empty
		if ( $group === false ) {
			$group = esc_html__('Icons', 'trx_addons');
		}
		if ( ! empty( $group ) ) {
			foreach ( $params as $k => $v ) {
				$params[$k]['group'] = $group;
			}
		}

		return apply_filters( 'trx_addons_filter_vc_add_icon_param', $params, $group, $only_socials );
	}
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'js_composer/js_composer-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_vc() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'js_composer/js_composer-demo-ocdi.php';
}


// Custom param's types for VC
//------------------------------------------------------------------------
if ( trx_addons_exists_vc() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'js_composer/params/select/select.php';
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'js_composer/params/radio/radio.php';
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'js_composer/params/icons/icons.php';
}
