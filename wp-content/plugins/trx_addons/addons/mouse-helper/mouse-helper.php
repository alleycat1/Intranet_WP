<?php
/**
 * Mouse helper
 *
 * @addon mouse-helper
 * @version 1.9
 *
 * @package ThemeREX Addons
 * @since v1.84.0
 */


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_mouse_helper_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_mouse_helper_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_mouse_helper_load_scripts_front', 10, 1 );
	function trx_addons_mouse_helper_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'mouse_helper', $force, array(
			'css'  => array(
				'trx_addons-mouse-helper' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'mouse-helper/mouse-helper.css' ),
			),
			'js' => array(
				'trx_addons-mouse-helper' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'mouse-helper/mouse-helper.js', 'deps' => 'jquery' ),
			),
			'need' => (int) trx_addons_get_option('mouse_helper') > 0
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_mouse_helper_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_mouse_helper_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_mouse_helper', 'trx_addons_mouse_helper_load_scripts_front_responsive', 10, 1 );
	function trx_addons_mouse_helper_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'mouse_helper', $force, array(
			'css'  => array(
				'trx_addons-mouse-helper-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_ADDONS . 'mouse-helper/mouse-helper.responsive.css',
					'media' => 'lg'
				),
			),
		) );
	}
}

	
// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_mouse_helper_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_mouse_helper_merge_styles');
	function trx_addons_mouse_helper_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'mouse-helper/mouse-helper.css' ] = false;
		return $list;
	}
}

// Merge styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_mouse_helper_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_mouse_helper_merge_styles_responsive');
	function trx_addons_mouse_helper_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'mouse-helper/mouse-helper.responsive.css' ] = false;
		return $list;
	}
}

	
// Merge specific scripts to the single file
if ( !function_exists( 'trx_addons_mouse_helper_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_mouse_helper_merge_scripts');
	function trx_addons_mouse_helper_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'mouse-helper/mouse-helper.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( !function_exists( 'trx_addons_mouse_helper_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_mouse_helper_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_mouse_helper_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_mouse_helper_check_in_html_output', 10, 1 );
	function trx_addons_mouse_helper_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'trx_addons_mouse_helper'
			)
		);
		if ( trx_addons_check_in_html_output( 'mouse_helper', $content, $args ) ) {
			trx_addons_mouse_helper_load_scripts_front( true );
		}
		return $content;
	}
}


// Add mouse_helper to the list with JS vars
if ( !function_exists( 'trx_addons_mouse_helper_localize_script' ) ) {
	add_filter( 'trx_addons_filter_localize_script', 'trx_addons_mouse_helper_localize_script' );
	function trx_addons_mouse_helper_localize_script( $vars ) {
		$vars['mouse_helper']            = (int) trx_addons_get_option('mouse_helper');
		$vars['mouse_helper_delay']      = max( 1, min( 20, (int) trx_addons_get_option('mouse_helper_delay') ) );
		$vars['mouse_helper_centered']   = in_array( trx_addons_get_option('mouse_helper_style', 'default'), apply_filters( 'trx_addons_filter_mouse_helper_force_centered', array( 'aim', 'pointer' ) ) )
												? 1
												: (int) trx_addons_get_option('mouse_helper_centered');
		$vars['msg_mouse_helper_anchor'] = (int) trx_addons_get_option('mouse_helper') > 0 ? esc_attr__( 'Scroll to', 'trx_addons' ) : '';
		return $vars;
	}
}



//========================================================================
//  Add params to the ThemeREX Addons Options and layout to the page
//========================================================================

// Add params to the ThemeREX Addons Options.
if ( ! function_exists( 'trx_addons_mouse_helper_add_options' ) ) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_mouse_helper_add_options' );
	function trx_addons_mouse_helper_add_options( $options ) {
		trx_addons_array_insert_before($options, 'sc_section', apply_filters( 'trx_addons_filter_options_mouse_helper', array(
			'mouse_helper_section' => array(
				"title" => esc_html__('Mouse helper', 'trx_addons'),
				'icon' => 'trx_addons_icon-mouse',
				"type" => "section"
			),
			'mouse_helper_info' => array(
				"title" => esc_html__('Mouse helper', 'trx_addons'),
				"desc" => wp_kses_data( __("Settings of the mouse helper", 'trx_addons') ),
				"type" => "info"
			),
			'mouse_helper_replace_cursor' => array(
				"title" => esc_html__('System cursor', 'trx_addons'),
				"desc" => wp_kses_data( __('Replace system cursor with custom image', 'trx_addons') ),
				"options" => array(
					"0"    => esc_html__( 'Default', 'trx_addons' ),
					"1"    => esc_html__( 'Replace', 'trx_addons' ),
					"hide" => esc_html__( 'Hide', 'trx_addons' ),
				),
				"std" => "default",
				"type" => "radio"
			),
			'mouse_helper_replace_cursor_default' => array(
				"title" => esc_html__('Default cursor image',  'trx_addons'),
				"desc" => wp_kses_data( __('Select or upload image to use it as default cursor. If you select animated cursor .ANI - select alternative cursor for not supported browsers in the next field', 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"dependency" => array(
					'mouse_helper_replace_cursor' => array( 1 )
				),
				"std" => "",
				"type" => "image"
			),
			'mouse_helper_replace_cursor_default2' => array(
				"title" => esc_html__('Default cursor image (alternative)',  'trx_addons'),
				"desc" => wp_kses_data( __('Select or upload image to use it as alternative cursor.', 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"dependency" => array(
					'mouse_helper_replace_cursor' => array( 1 ),
					'mouse_helper_replace_cursor_default' => array( 'not_empty' )
				),
				"std" => "",
				"type" => "image"
			),
			'mouse_helper_replace_cursor_links' => array(
				"title" => esc_html__('Cursor image over links',  'trx_addons'),
				"desc" => wp_kses_data( __('Select or upload image to use it as cursor over links and buttons.  If you select animated cursor .ANI - select alternative cursor for not supported browsers in the next field', 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"dependency" => array(
					'mouse_helper_replace_cursor' => array( 1 )
				),
				"std" => "",
				"type" => "image"
			),
			'mouse_helper_replace_cursor_links2' => array(
				"title" => esc_html__('Cursor image over links (alternative)',  'trx_addons'),
				"desc" => wp_kses_data( __('Select or upload image to use it as alternative cursor.', 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"dependency" => array(
					'mouse_helper_replace_cursor' => array( 1 ),
					'mouse_helper_replace_cursor_links' => array( 'not_empty' )
				),
				"std" => "",
				"type" => "image"
			),
			'mouse_helper' => array(
				"title" => esc_html__('Show mouse helper', 'trx_addons'),
				"desc" => wp_kses_data( __('Display animated helper near the mouse cursor on desktop and notebooks', 'trx_addons') ),
				"std" => "0",
				"type" => "switch"
			),
			'mouse_helper_style' => array(
				"title" => esc_html__('Style', 'trx_addons'),
				"desc" => wp_kses_data( __('Select a style of the mouse helper', 'trx_addons') ),
				"dependency" => array(
					'mouse_helper' => array( 1 )
				),
				"options" => apply_filters( 'trx_addons_filter_mouse_helper_list_styles', array(
					"default" => esc_html__( 'Default', 'trx_addons' ),
					"dot"     => esc_html__( 'Large dot', 'trx_addons' ),
					"pointer" => esc_html__( 'Pointer', 'trx_addons' ),
					"aim"     => esc_html__( 'Aim', 'trx_addons' ),
				) ),
				"std" => "default",
				"type" => "select"
			),
			'mouse_helper_permanent' => array(
				"title" => esc_html__('Always visible', 'trx_addons'),
				"desc" => wp_kses_data( __('Display the mouse helper permanently or only when hovering over the corresponding object', 'trx_addons') ),
				"dependency" => array(
					'mouse_helper' => array( 1 )
				),
				"std" => "0",
				"type" => "switch"
			),
			'mouse_helper_centered' => array(
				"title" => esc_html__('Centered', 'trx_addons'),
				"desc" => wp_kses_data( __('Place the center of the helper in the cursor position', 'trx_addons') ),
				"dependency" => array(
					'mouse_helper' => array( 1 )
				),
				"std" => "0",
				"type" => "switch"
			),
			'mouse_helper_smooth' => array(
				"title" => esc_html__('Smooth change states', 'trx_addons'),
				"desc" => wp_kses_data( __('Smooth transition between helper states (to a picture, with text, with an icon) or abrupt state switching', 'trx_addons') ),
				"dependency" => array(
					'mouse_helper' => array( 1 )
				),
				"std" => "1",
				"type" => "switch"
			),
			'mouse_helper_delay' => array(
				"title" => esc_html__('Delay', 'trx_addons'),
				"desc" => wp_kses_data( __('The coefficient of lag between the helper and the cursor (1 - the helper moves with the cursor)', 'trx_addons') ),
				"dependency" => array(
					'mouse_helper' => array( 1 )
				),
				"std" => 10,
				"min" => 1,
				"max" => 20,
				"type" => "slider"
			),
		)));
		return $options;
	}
}


// Add mouse helper to the page
if ( !function_exists( 'trx_addons_mouse_helper_add_to_html' ) ) {
	add_action('wp_footer', 'trx_addons_mouse_helper_add_to_html');
	function trx_addons_mouse_helper_add_to_html() {
		if ( (int) trx_addons_get_option( 'mouse_helper' ) > 0 ) {
			// Add mouse helper layout
			$style = trx_addons_get_option( 'mouse_helper_style', 'default' );
			ob_start();
			// Default layout for all styles
			?><div class="<?php
				echo esc_attr( apply_filters( 'trx_addons_filter_mouse_helper_classes',
												'trx_addons_mouse_helper trx_addons_mouse_helper_base'
												. ' trx_addons_mouse_helper_style_' . esc_attr( $style )
												. ( (int) trx_addons_get_option( 'mouse_helper_smooth' ) > 0
														? ' trx_addons_mouse_helper_smooth'
														: ' trx_addons_mouse_helper_abrupt'
														)
												. ( (int) trx_addons_get_option( 'mouse_helper_permanent' ) > 0
														? ' trx_addons_mouse_helper_permanent'
														: ''
														)
												. ( (int) trx_addons_get_option( 'mouse_helper_centered' ) > 0
													|| in_array( $style, apply_filters( 'trx_addons_filter_mouse_helper_force_centered', array( 'aim', 'pointer' ) ) )
														? ' trx_addons_mouse_helper_centered'
														: ''
														),
												'base'
											)
							);
				?>"
				<?php
				do_action( 'trx_addons_action_mouse_helper_attributes', 'base' );
			?>><?php
				do_action( 'trx_addons_action_mouse_helper_layout', 'base' );
			?></div><?php
			// Additional layouts for some styles
			if ( in_array( $style, array( 'aim', 'pointer' ) ) ) {
				?><div class="<?php
					echo esc_attr( apply_filters( 'trx_addons_filter_mouse_helper_classes',
													'trx_addons_mouse_helper trx_addons_mouse_helper_outer'
													. ' trx_addons_mouse_helper_style_' . esc_attr( $style )
													. ( (int) trx_addons_get_option( 'mouse_helper_permanent' ) > 0
															? ' trx_addons_mouse_helper_permanent'
															: ''
															)
													. ' trx_addons_mouse_helper_centered',
													'outer'
												)
								);
					?>"
					data-delay="<?php echo esc_attr( apply_filters( 'trx_addons_filter_mouse_helper_delay', 3, 'outer' ) ); ?>"
					<?php
					do_action( 'trx_addons_action_mouse_helper_attributes', 'outer' );
				?>><?php
					do_action( 'trx_addons_action_mouse_helper_layout', 'outer' );
				?></div><?php
			}
			$output = ob_get_contents();
			ob_end_clean();
			trx_addons_show_layout( apply_filters( 'trx_addons_filter_mouse_helper_layout', $output ) );
			// Load addon-specific scripts and styles
			trx_addons_mouse_helper_load_scripts_front( true );
		}
	}
}

// Replace system cursor
if ( !function_exists( 'trx_addons_mouse_helper_replace_system_cursor' ) ) {
	add_filter('body_class', 'trx_addons_mouse_helper_replace_system_cursor');
	function trx_addons_mouse_helper_replace_system_cursor( $classes ) {
		if ( (int) trx_addons_get_option( 'mouse_helper_replace_cursor' ) == 1 ) {
			$classes[]  = 'trx_addons_custom_cursor';
			$cur_defa   = trx_addons_get_option( 'mouse_helper_replace_cursor_default' );
			$cur_defa2  = trx_addons_get_option( 'mouse_helper_replace_cursor_default2' );
			$cur_links  = trx_addons_get_option( 'mouse_helper_replace_cursor_links' );
			$cur_links2 = trx_addons_get_option( 'mouse_helper_replace_cursor_links2' );
			if ( ! empty( $cur_defa ) ) {
				trx_addons_add_inline_css(
					join( ',', apply_filters( 'trx_addons_filter_custom_cursor_default', array(
						'body',
						'body *'
					) ) )
					. ' { cursor: '
						. 'url(' . esc_url($cur_defa) . ')'
						. ( ! empty($cur_defa2) ? ',url(' . esc_url($cur_defa2) . ')' : '' )
						. ', auto !important; }'
					);
			}
			if ( ! empty( $cur_links ) ) {
				trx_addons_add_inline_css(
					join( ',', apply_filters( 'trx_addons_filter_custom_cursor_links', array(
						'body a',
						'body a *',
						'body button',
						'body input[type="submit"]',
						'body input[type="button"]',
						'body input[type="reset"]'
					) ) )
					. ' { cursor: '
						. 'url(' . esc_url($cur_links) . ')'
						. ( ! empty($cur_links2) ? ',url(' . esc_url($cur_links2) . ')' : '' )
						. ', pointer !important; }'
					);
			}
		} else if ( in_array( trx_addons_get_option( 'mouse_helper_replace_cursor' ), array( '2', 'hide' ) ) ) {
			if ( ! trx_addons_is_preview() ) {
				$classes[]  = 'trx_addons_hide_cursor';
			}
		}
		return $classes;
	}
}



//========================================================================
//  Highlight on mouse hover for Title
//========================================================================

// Add 'mouse_helper_highlight' to the 'Title' params in Elementor
if ( !function_exists( 'trx_addons_mouse_helper_highlight_add_title_param_in_elementor' ) ) {
	add_filter( 'trx_addons_filter_elementor_add_title_param', 'trx_addons_mouse_helper_highlight_add_title_param_in_elementor' );
	function trx_addons_mouse_helper_highlight_add_title_param_in_elementor( $params ) {
		if ( is_array( $params ) ) {
			foreach( $params as $k => $v ) {
				if ( $v['name'] == 'typed' ) {
					$params = array_merge(
								array_slice( $params, 0, $k, true),
								array(
									array(
										'name' => "mouse_helper_highlight",
										'type' => \Elementor\Controls_Manager::SWITCHER,
										'label' => __("Highlight on mouse hover", 'trx_addons'),
										'description' => wp_kses_data( __( 'Used only if option "Mouse helper" is on in the Theme Panel - ThemeREX Addons settings', 'trx_addons' ) ),
										'label_off' => __( 'Off', 'trx_addons' ),
										'label_on' => __( 'On', 'trx_addons' ),
										'return_value' => '1',
										'default' => '',
										'condition' => array(
											'title!' => '',
										),
									)									
								),
								array_slice( $params, $k, null, true)
								);
					break;
				}
			}
		}
		return $params;
	}
}

// Add 'mouse_helper_highlight' to the 'Title' params in VC
if ( !function_exists( 'trx_addons_mouse_helper_highlight_add_title_param_in_vc' ) ) {
	add_filter( 'trx_addons_filter_vc_add_title_param', 'trx_addons_mouse_helper_highlight_add_title_param_in_vc', 10, 3 );
	function trx_addons_mouse_helper_highlight_add_title_param_in_vc( $params, $group, $button ) {
		if ( is_array( $params ) ) {
			foreach( $params as $k => $v ) {
				if ( $v['param_name'] == 'typed' ) {
					$params = array_merge(
								array_slice( $params, 0, $k, true),
								array(
									array(
										"param_name" => "mouse_helper_highlight",
										"heading" => esc_html__("Highlight on mouse hover", 'trx_addons'),
										'edit_field_class' => 'vc_col-sm-4 vc_new_row',
										"admin_label" => true,
										'dependency' => array(
											'element' => 'title',
											'not_empty' => true
										),
										"std" => "0",
										"value" => array(esc_html__("Highlight title", 'trx_addons') => "1" ),
										"type" => "checkbox"
									),
								),
								array_slice( $params, $k, null, true)
								);
					break;
				}
			}
		}
		return $params;
	}
}

// Add 'mouse_helper_highlight' to the default 'Title' values
if ( !function_exists( 'trx_addons_mouse_helper_highlight_add_title_defaults' ) ) {
	add_filter( 'trx_addons_sc_atts', 'trx_addons_mouse_helper_highlight_add_title_defaults', 10, 2 );
	function trx_addons_mouse_helper_highlight_add_title_defaults( $atts, $sc ) {
		if ( isset( $atts['title'] ) && isset( $atts['typed'] ) ) {
			$atts['mouse_helper_highlight'] = 0;
		}
 		return $atts;
 	}
}

// Apply custom color to the tpl.title
if ( !function_exists( 'trx_addons_mouse_helper_highlight_add_title_class_tpl' ) ) {
	add_filter( 'trx_addons_filter_sc_item_title_class', 'trx_addons_mouse_helper_highlight_add_title_class_tpl', 10, 3 );
	function trx_addons_mouse_helper_highlight_add_title_class_tpl( $class, $sc, $args=array() ) {
		if ( ! empty($args['title_color']) ) {
			if ( ! empty($args['mouse_helper_highlight']) && (int) trx_addons_get_option('mouse_helper') > 0 ) {
				$class .= ' ' . trx_addons_add_inline_css_class(
									'color: ' . trx_addons_hex2rgba( $args['title_color'], apply_filters( 'trx_addons_filter_mouse_helper_highlight_opacity', 0.33 ) ) . ' !important;'
									. 'background-image: radial-gradient(closest-side, ' . $args['title_color'] . ' 78%, transparent 0);'
								);
			}
		}
		return $class;
	}
}

// Add 'data-mouse-helper' to the tpl.title
if ( !function_exists( 'trx_addons_mouse_helper_highlight_add_title_data_tpl' ) ) {
	add_action( 'trx_addons_action_sc_item_title_data', 'trx_addons_mouse_helper_highlight_add_title_data_tpl', 10, 2 );
	function trx_addons_mouse_helper_highlight_add_title_data_tpl( $sc, $args=array() ) {
		if ( ! empty($args['mouse_helper_highlight']) && (int) trx_addons_get_option( 'mouse_helper' ) > 0 ) {
			echo ' ' . apply_filters( 'trx_addons_filter_mouse_helper_attributes', 'data-mouse-helper="highlight"', 'titles' );
		}
	}
}

// Apply custom color to the tpe.title (JS code to override variable value)
if ( !function_exists( 'trx_addons_mouse_helper_highlight_add_title_tag_tpe' ) ) {
	add_action( 'trx_addons_filter_tpe_item_title_tag', 'trx_addons_mouse_helper_highlight_add_title_tag_tpe' );
	function trx_addons_mouse_helper_highlight_add_title_tag_tpe() {
		?>
		if ( settings.title_color != '' && settings.mouse_helper_highlight == 1 && TRX_ADDONS_STORAGE['mouse_helper'] > 0 ) {
			title_tag_style = 'color: ' + trx_addons_hex2rgba( settings.title_color, 0.33 ) + ' !important;'
							+ 'background-image: radial-gradient(closest-side, ' + settings.title_color + ' 78%, transparent 0);';
		}
		<?php
	}
}

// Add 'data-mouse-helper' to the tpe.title (JS code to add data-param)
if ( !function_exists( 'trx_addons_mouse_helper_highlight_add_title_data_tpe' ) ) {
	add_action( 'trx_addons_action_tpe_item_title_data', 'trx_addons_mouse_helper_highlight_add_title_data_tpe' );
	function trx_addons_mouse_helper_highlight_add_title_data_tpe() {
		$data = apply_filters( 'trx_addons_filter_mouse_helper_attributes', 'data-mouse-helper="highlight"', 'titles' );
		?>
		+ ( settings.mouse_helper_highlight > 0 ? ' <?php echo $data; ?>' : '' )
		<?php
	}
}


//========================================================================
//  Highlight on mouse hover for Heading
//========================================================================

// Add 'mouse_helper_highlight' to the 'Heading' params
if ( ! function_exists( 'trx_addons_mouse_helper_highlight_add_heading_param_in_elementor' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_mouse_helper_highlight_add_heading_param_in_elementor', 10, 3 );
	function trx_addons_mouse_helper_highlight_add_heading_param_in_elementor( $element, $section_id, $args ) {
		if ( ! is_object($element) ) return;
		$el_name = $element->get_name();
		if ( 'heading' == $el_name && 'section_title' === $section_id && (int) trx_addons_get_option('mouse_helper') > 0 ) {
			$element->add_control( 'mouse_helper_highlight', array(
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label' => __("Highlight on mouse hover", 'trx_addons'),
									'label_on' => __( 'On', 'trx_addons' ),
									'label_off' => __( 'Off', 'trx_addons' ),
									'return_value' => '1',
									'default' => '',
								) );
		}
	}
}


// Add data parameter and color styles to the Heading
if ( ! function_exists( 'trx_addons_mouse_helper_highlight_before_render_heading_in_elementor' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render', 'trx_addons_mouse_helper_highlight_before_render_heading_in_elementor', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_mouse_helper_highlight_before_render_heading_in_elementor', 10, 1 );
	function trx_addons_mouse_helper_highlight_before_render_heading_in_elementor( $element ) {
		if ( is_object( $element ) && (int) trx_addons_get_option( 'mouse_helper' ) > 0 ) {
			$el_name = $element->get_name();
			if ( 'heading' == $el_name ) {
				//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
				$highlight = $element->get_settings( 'mouse_helper_highlight' );
				if ( ! empty( $highlight ) ) {
					$element->add_render_attribute( 'title', 'data-mouse-helper', 'highlight' );
					$title_color = $element->get_settings( 'title_color' );
					if ( ! empty( $title_color ) && substr( $title_color, 0, 1 ) == '#' ) {
						$element->add_render_attribute( 'title', 'class', trx_addons_add_inline_css_class(
							'color: ' . trx_addons_hex2rgba( $title_color, apply_filters( 'trx_addons_filter_mouse_helper_highlight_opacity', 0.33 ) ) . ' !important;'
							. 'background-image: radial-gradient(closest-side, ' . $title_color . ' 78%, transparent 0);'
						) );
					}
				}
			}
		}
	}
}




//========================================================================
//  Mouse Helper for all elements
//========================================================================

// Add "Mouse helper" params to all elements
if (!function_exists('trx_addons_mouse_helper_add_params_to_elements')) {
	add_action( 'elementor/element/before_section_start', 'trx_addons_mouse_helper_add_params_to_elements', 10, 3 );
	add_action( 'elementor/widget/before_section_start', 'trx_addons_mouse_helper_add_params_to_elements', 10, 3 );
	function trx_addons_mouse_helper_add_params_to_elements($element, $section_id, $args) {

		if ( ! is_object( $element ) ) return;

		if ( in_array( $element->get_name(), array( 'section', 'column', 'common' ) ) && $section_id == '_section_responsive' && (int) trx_addons_get_option( 'mouse_helper' ) > 0 ) {
			
			$element->start_controls_section( 'section_trx_mouse_helper', array(
																		'tab' => !empty($args['tab']) ? $args['tab'] : \Elementor\Controls_Manager::TAB_ADVANCED,
																		'label' => __( 'Mouse Helper', 'trx_addons' )
																	) );
			$element->add_control( 'mouse_helper', array(
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __("Enable mouse helper", 'trx_addons'),
				'label_on' => __( 'On', 'trx_addons' ),
				'label_off' => __( 'Off', 'trx_addons' ),
				'return_value' => '1',
				'default' => '',
			) );

			if ( trx_addons_get_option('mouse_helper_replace_cursor') != 'hide' ) {
				$element->add_control( 'mouse_helper_hide_cursor', array(
					'type' => \Elementor\Controls_Manager::SWITCHER,
					'label' => __("Hide system cursor", 'trx_addons'),
					'label_on' => __( 'On', 'trx_addons' ),
					'label_off' => __( 'Off', 'trx_addons' ),
					'return_value' => '1',
					'default' => '',
					'condition' => array(
						'mouse_helper' => '1',
					),
				) );
			}

			$element->add_control( 'mouse_helper_hide_helper', array(
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __("Hide mouse helper", 'trx_addons'),
				'label_on' => __( 'On', 'trx_addons' ),
				'label_off' => __( 'Off', 'trx_addons' ),
				'return_value' => '1',
				'default' => '',
				'condition' => array(
					'mouse_helper' => '1',
				),
			) );

			$element->add_control( 'mouse_helper_action', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Action', 'trx_addons' ),
				'label_block' => false,
				'options' => apply_filters( 'trx_addons_filter_mouse_helper_action', array(
					'hover' => esc_html__( 'Hover', 'trx_addons' ),
				) ),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'
				),
				'default' => 'hover',
			) );

			$element->add_control( 'mouse_helper_centered', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Cursor in the center', 'trx_addons' ),
				'label_block' => false,
				'options' => apply_filters( 'trx_addons_filter_mouse_helper_centered', array(
					'' => esc_html__( 'Default', 'trx_addons' ),
					'0' => esc_html__( 'No', 'trx_addons' ),
					'1'  => esc_html__( 'Yes', 'trx_addons' ),
				) ),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'
				),
				'default' => '',
			) );

			$element->add_control( 'mouse_helper_magnet', array(
				'label' => __( 'Magnet distance', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0,
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'mouse_helper' => '1'
				),
			) );

			$element->add_control( 'mouse_helper_bg_color', array(
				'label' => __( 'Background color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
//				'global' => array(
//					'active' => false,
//				),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'
				),
			) );

			$element->add_control( 'mouse_helper_bd_color', array(
				'label' => __( 'Border color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
//				'global' => array(
//					'active' => false,
//				),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'
				),
			) );

			$element->add_control( 'mouse_helper_bd_width', array(
				'label' => __( 'Border width', 'trx_addons' ),
				'description' => __( '-1 or empty - leave a border width unchanged', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => -1,
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => -1,
						'max' => 10
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'

				),
			) );

			$element->add_control( 'mouse_helper_color', array(
				'label' => __( 'Text color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
//				'global' => array(
//					'active' => false,
//				),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'
				),
			) );

			$element->add_control( 'mouse_helper_mode', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Overlay mode', 'trx_addons' ),
				'label_block' => false,
				'options' => apply_filters( 'trx_addons_filter_mouse_helper_mode', array(
					'' => esc_html__( 'Default', 'trx_addons' ),
					'normal' => esc_html__( 'Normal', 'trx_addons' ),
					'multiply'  => esc_html__( 'Multiply', 'trx_addons' ),
					'screen'  => esc_html__( 'Screen', 'trx_addons' ),
					'overlay'  => esc_html__( 'Overlay', 'trx_addons' ),
					'darken'  => esc_html__( 'Darken', 'trx_addons' ),
					'lighten'  => esc_html__( 'Lighten', 'trx_addons' ),
					'color-dodge'  => esc_html__( 'Color Dodge', 'trx_addons' ),
					'color-burn'  => esc_html__( 'Color Burn', 'trx_addons' ),
					'hard-light'  => esc_html__( 'Hard Light', 'trx_addons' ),
					'soft-light'  => esc_html__( 'Soft Light', 'trx_addons' ),
					'difference'  => esc_html__( 'Difference', 'trx_addons' ),
					'exclusion'  => esc_html__( 'Exclusion', 'trx_addons' ),
					'hue'  => esc_html__( 'Hue', 'trx_addons' ),
					'saturation'  => esc_html__( 'Saturation', 'trx_addons' ),
					'color'  => esc_html__( 'Color', 'trx_addons' ),
					'luminosity'  => esc_html__( 'Luminosity', 'trx_addons' ),
				) ),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'
				),
				'default' => '',
			) );

			$element->add_control( 'mouse_helper_axis', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Motion axis', 'trx_addons' ),
				'label_block' => false,
				'options' => array(
					'xy' => esc_html__( 'Both', 'trx_addons' ),
					'x'  => esc_html__( 'X only', 'trx_addons' ),
					'y'  => esc_html__( 'Y only', 'trx_addons' ),
				),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'
				),
				'default' => 'xy',
			) );

			$element->add_control( 'mouse_helper_delay', array(
				'label' => __( 'Motion delay', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => (int) trx_addons_get_option( 'mouse_helper_delay' ),
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 20
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'

				),
			) );

			$element->add_control( 'mouse_helper_text', array(
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => __( 'Helper text', 'trx_addons' ),
				'label_block' => false,
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'

				),
			) );

			$element->add_control( 'mouse_helper_text_size', array(
				'label' => __( 'Text size', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => '',
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 0.2,
						'max' => 2,
						'step' => 0.1
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_text!' => '',
					'mouse_helper_hide_helper!' => '1'

				),
			) );

			$element->add_control( 'mouse_helper_text_round', array(
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __("Rotate text in a circle", 'trx_addons'),
				'label_on' => __( 'On', 'trx_addons' ),
				'label_off' => __( 'Off', 'trx_addons' ),
				'return_value' => '1',
				'default' => '',
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_text!' => '',
					'mouse_helper_hide_helper!' => '1'

				),
			) );

			$params = trx_addons_get_icon_param();
			$params = trx_addons_array_get_first_value( $params );
			unset( $params['name'] );
			$params['condition'] = array(
				'mouse_helper' => '1',
				'mouse_helper_hide_helper!' => '1'

			);
			$element->add_control( 'mouse_helper_icon', $params );

			$element->add_control( 'mouse_helper_icon_size', array(
				'label' => __( 'Icon size', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => '',
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 5,
						'step' => 0.1
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_icon[value]!' => '',
					'mouse_helper_icon[library]!' => '',
					'mouse_helper_hide_helper!' => '1'

				),
			) );

			$element->add_control( 'mouse_helper_icon_color', array(
				'label' => __( 'Icon color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
//				'global' => array(
//					'active' => false,
//				),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_icon!' => array( '', 'none' ),
					'mouse_helper_hide_helper!' => '1'

				),
			) );

			$element->add_control( 'mouse_helper_image', array(
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => __( 'Image', 'trx_addons' ),
				'default' => array(
					'url' => '',
				),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'

				),
			) );

			$element->add_control( 'mouse_helper_layout', array(
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'label' => __( 'Custom layout', 'trx_addons' ),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'

				),
			) );

			$element->add_control( 'mouse_helper_class', array(
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => __( 'Custom class', 'trx_addons' ),
				'condition' => array(
					'mouse_helper' => '1',
					'mouse_helper_hide_helper!' => '1'

				),
			) );

			$element->add_control( 'mouse_helper_callback', array(
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => __( 'JS Callback', 'trx_addons' ),
				'condition' => array(
					'mouse_helper' => '1'
				),
			) );

			$element->end_controls_section();
		}
	}
}

// Add "data-mouse-helper" to the wrapper of the row
if ( !function_exists( 'trx_addons_mouse_helper_before_render_elements' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_mouse_helper_before_render_elements', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render', 'trx_addons_mouse_helper_before_render_elements', 10, 1 );
	add_action( 'elementor/frontend/column/before_render', 'trx_addons_mouse_helper_before_render_elements', 10, 1 );
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_mouse_helper_before_render_elements', 10, 1 );
	function trx_addons_mouse_helper_before_render_elements($element) {
		if ( is_object( $element ) ) {
			//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
			$mouse_helper = $element->get_settings( 'mouse_helper' );
			if ( ! empty( $mouse_helper ) ) {
				$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
				$data = array(
					'data-mouse-helper' => ! empty( $settings['mouse_helper_action'] ) ? $settings['mouse_helper_action'] : 'hover',
					'data-mouse-helper-hide-helper' => ! empty( $settings['mouse_helper_hide_helper'] ) ? (int) $settings['mouse_helper_hide_helper'] : 0,
					'data-mouse-helper-centered' => ! isset( $settings['mouse_helper_centered'] ) || $settings['mouse_helper_centered'] === ''
														? ( in_array( trx_addons_get_option('mouse_helper_style', 'default'), apply_filters( 'trx_addons_filter_mouse_helper_force_centered', array( 'aim', 'pointer' ) ) )
															? 1
															: (int) trx_addons_get_option('mouse_helper_centered')
															)
														: $settings['mouse_helper_centered'],
					'data-mouse-helper-magnet' => ! empty( $settings['mouse_helper_magnet'] ) ? max(0, $settings['mouse_helper_magnet']['size'] ) : 0,
					'data-mouse-helper-color' => ! empty( $settings['mouse_helper_color'] ) ? $settings['mouse_helper_color'] : '',
					'data-mouse-helper-bg-color' => ! empty( $settings['mouse_helper_bg_color'] ) ? $settings['mouse_helper_bg_color'] : '',
					'data-mouse-helper-bd-color' => ! empty( $settings['mouse_helper_bd_color'] ) ? $settings['mouse_helper_bd_color'] : '',
					'data-mouse-helper-bd-width' => ! empty( $settings['mouse_helper_bd_width'] )
													?  $settings['mouse_helper_bd_width']['size']
													: -1,
					'data-mouse-helper-mode' => ! empty( $settings['mouse_helper_mode'] ) ? $settings['mouse_helper_mode'] : '',
					'data-mouse-helper-axis' => ! empty( $settings['mouse_helper_axis'] ) ? $settings['mouse_helper_axis'] : 'xy',
					'data-mouse-helper-delay' => ! empty( $settings['mouse_helper_delay'] )
													?  $settings['mouse_helper_delay']['size']
													: ( trx_addons_check_option( 'mouse_helper_delay' )
														? (int) trx_addons_get_option( 'mouse_helper_delay' )
														: 10
														),
					'data-mouse-helper-text' => ! empty( $settings['mouse_helper_text'] ) ? $settings['mouse_helper_text'] : '',
					'data-mouse-helper-text-size'  => ! empty( $settings['mouse_helper_text'] ) && ! empty( $settings['mouse_helper_text_size']['size'] ) ? $settings['mouse_helper_text_size']['size'] : '',
					'data-mouse-helper-text-round' => ! empty( $settings['mouse_helper_text_round'] ) ? $settings['mouse_helper_text_round'] : 0,
					'data-mouse-helper-icon' => ! empty( $settings['mouse_helper_icon'] ) ? $settings['mouse_helper_icon'] : '',
					'data-mouse-helper-icon-size'  => ! empty( $settings['mouse_helper_icon'] ) && ! empty( $settings['mouse_helper_icon_size']['size'] ) ? $settings['mouse_helper_icon_size']['size'] : '',
					'data-mouse-helper-icon-color' => ! empty( $settings['mouse_helper_icon_color'] ) ? $settings['mouse_helper_icon_color'] : '',
					'data-mouse-helper-image' => ! empty( $settings['mouse_helper_image']['url'] ) ? $settings['mouse_helper_image']['url'] : '',
					'data-mouse-helper-layout' => ! empty( $settings['mouse_helper_layout'] ) ? $settings['mouse_helper_layout'] : '',
					'data-mouse-helper-class' => ! empty( $settings['mouse_helper_class'] ) ? $settings['mouse_helper_class'] : '',
					'data-mouse-helper-callback' => ! empty( $settings['mouse_helper_callback'] ) ? $settings['mouse_helper_callback'] : '',
				);
				if ( ! trx_addons_is_preview() ) {
					$data['data-mouse-helper-hide-cursor'] = ! empty( $settings['mouse_helper_hide_cursor'] ) ? $settings['mouse_helper_hide_cursor'] : 0;
				}
				$element->add_render_attribute( '_wrapper', $data );
			}
		}
	}
}
