<?php
/**
 * Plugin support: Elementor
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Check if plugin 'Elementor' is installed and activated
// Attention! This function is used in many files and was moved to the api.php
/*
if ( !function_exists( 'trx_addons_exists_elementor' ) ) {
	function trx_addons_exists_elementor() {
		return class_exists('Elementor\Plugin');
	}
}
*/

if ( ! function_exists( 'trx_addons_elm_is_preview' ) ) {
	/**
	 * Check if current page is a preview page of Elementor
	 *
	 * @return bool  true if current page is a preview page of Elementor
	 */
	function trx_addons_elm_is_preview() {
		static $is_preview = -1;
		if ( $is_preview === -1 ) {
			if ( trx_addons_exists_elementor() ) {
				$elementor = \Elementor\Plugin::instance();
				$is_preview = ( is_object( $elementor ) && ! empty( $elementor->preview ) && is_object( $elementor->preview ) && $elementor->preview->is_preview_mode() )
							|| trx_addons_get_value_gp( 'elementor-preview' ) > 0
							|| ( trx_addons_get_value_gp( 'action' ) == 'elementor' && (int) trx_addons_get_value_gp( 'post' ) > 0 )
							|| ( is_admin() && in_array( trx_addons_get_value_gp( 'action' ), array( 'elementor', 'elementor_ajax', 'wp_ajax_elementor_ajax' ) ) );
			} else {
				$is_preview = false;
			}
		}
		return $is_preview;
	}
}

if ( ! function_exists( 'trx_addons_elm_is_edit_mode' ) ) {
	/**
	 * Check if current page is a edit page of Elementor (loaded in the left frame of the editor)
	 *
	 * @return bool  true if current page is a edit page of Elementor
	 */
	function trx_addons_elm_is_edit_mode() {
		static $is_edit_mode = -1;
		if ( $is_edit_mode === -1 ) {
			$is_edit_mode = trx_addons_exists_elementor()
								&& ( \Elementor\Plugin::instance()->editor->is_edit_mode()
									|| ( trx_addons_get_value_gp( 'post' ) > 0
										&& trx_addons_get_value_gp( 'action' ) == 'elementor'
										)
									|| ( is_admin()
										&& in_array( trx_addons_get_value_gp( 'action' ), array( 'elementor', 'elementor_ajax', 'wp_ajax_elementor_ajax' ) )
										)
									);
		}
		return $is_edit_mode;
	}
}

if ( ! function_exists( 'trx_addons_is_built_with_elementor' ) ) {
	/**
	 * Check if the post is built with Elementor
	 *
	 * @param int $post_id  post ID
	 * 
	 * @return bool  true if the post is built with Elementor
	 */
	function trx_addons_is_built_with_elementor( $post_id ) {
		// Elementor\DB::is_built_with_elementor` is soft deprecated since 3.2.0
		// Use `Plugin::$instance->documents->get( $post_id )->is_built_with_elementor()` instead
		// return trx_addons_exists_elementor() && \Elementor\Plugin::instance()->db->is_built_with_elementor( $post_id );
		$rez = false;
		if ( trx_addons_exists_elementor() && ! empty( $post_id ) ) {
			$document = \Elementor\Plugin::instance()->documents->get( $post_id );
			if ( is_object( $document ) ) {
				$rez = $document->is_built_with_elementor();
			}
		}
		return $rez;
	}
}

if ( ! function_exists( 'trx_addons_elm_has_custom_breakpoints' ) ) {
	/**
	 * Check if the Elementor has custom breakpoints
	 *
	 * @return bool  true if the Elementor has custom breakpoints
	 */
	function trx_addons_elm_has_custom_breakpoints() {
		return trx_addons_exists_elementor()
				&& ! empty( \Elementor\Plugin::instance()->breakpoints )
				&& is_object( \Elementor\Plugin::instance()->breakpoints )
				&& method_exists( \Elementor\Plugin::instance()->breakpoints, 'has_custom_breakpoints' )
				&& \Elementor\Plugin::instance()->breakpoints->has_custom_breakpoints();
	}
}

if ( ! function_exists( 'trx_addons_elm_is_experiment_active' ) ) {
	/**
	 * Check if the Elementor experiment is active
	 *
	 * @param string $experiment  Experiment name
	 * 
	 * @return bool  true if the experiment is active
	 */
	function trx_addons_elm_is_experiment_active( $experiment ) {
		return trx_addons_exists_elementor()
				&& ! empty( \Elementor\Plugin::instance()->experiments )
				&& is_object( \Elementor\Plugin::instance()->experiments )
				&& method_exists( \Elementor\Plugin::instance()->experiments, 'is_feature_active' )
				&& \Elementor\Plugin::instance()->experiments->is_feature_active( $experiment );
	}
}

if ( ! function_exists( 'trx_addons_deregister_swiper_from_elementor' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_deregister_swiper_from_elementor', 9999 );
	/**
	 * Deregister Swiper from Elementor to use the plugin's version if a plugin setting 'replace_swiper_from_elementor' is enabled
	 * 
	 * @hooked wp_enqueue_scripts
	 * 
	 * @trigger trx_addons_filter_replace_swiper_from_elementor
	 */
	function trx_addons_deregister_swiper_from_elementor() {
		$replace = apply_filters( 'trx_addons_filter_replace_swiper_from_elementor', trx_addons_get_setting( 'replace_swiper_from_elementor', 'none' ) );
		if ( $replace == 'always' || ( $replace == 'old' && ! trx_addons_elm_is_experiment_active( 'e_swiper_latest' ) ) ) {
			if ( wp_script_is( 'swiper', 'registered' ) ) {
				// Deregister Swiper from Elementor
				wp_deregister_script( 'swiper' );
				if ( wp_style_is( 'swiper', 'registered' ) ) {
					wp_deregister_style( 'swiper' );
				}
				// Enqueue Swiper from the plugin
				trx_addons_enqueue_slider( 'swiper' );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_disable_grab_output' ) ) {
	add_filter( 'trx_addons_filter_grab_output_allowed', 'trx_addons_elm_disable_grab_output' );
	/**
	 * Disable grab output for Elementor preview mode and AJAX requests
	 * 
	 * @hooked trx_addons_filter_grab_output_allowed
	 * 
	 * @param bool $allowed  true if grab output is allowed
	 * 
	 * @return bool  true if grab output is allowed
	 */
	function trx_addons_elm_disable_grab_output( $allowed ) {
		return $allowed
				// Disable in the Elementor preview mode
				&& ! ( function_exists( 'trx_addons_is_preview' ) && trx_addons_is_preview( 'elementor' ) )
				// Disable when a widget from Elementor is requested
				&& ! doing_action( 'wp_ajax_elementor_render_widget' )
				&& ! doing_action( 'wp_ajax_elementor_ajax' )
				&& ! doing_action( 'elementor_ajax' )
				&& ! doing_action( 'admin_action_elementor' );
	}
}

if ( ! function_exists( 'trx_addons_elm_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_elm_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	/**
	 * Load required styles and scripts for Elementor in the frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 */
	function trx_addons_elm_load_scripts_front() {
		if ( trx_addons_exists_elementor() ) {
			if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
				wp_enqueue_style( 'trx_addons-elementor', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'elementor/elementor.css'), array(), null );
			}
			if ( trx_addons_elm_is_preview() ) {
				wp_enqueue_style(  'trx_addons-msgbox', trx_addons_get_file_url('js/msgbox/msgbox.css'), array(), null );
				wp_enqueue_script( 'trx_addons-msgbox', trx_addons_get_file_url('js/msgbox/msgbox.js'), array('jquery'), null, true );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_load_responsive_styles' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_elm_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	/**
	 * Load responsive styles for Elementor in the frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 */
	function trx_addons_elm_load_responsive_styles() {
		if ( trx_addons_exists_elementor() && trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
			wp_enqueue_style(
				'trx_addons-elementor-responsive',
				trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'elementor/elementor.responsive.css'),
				array(),
				null,
				trx_addons_media_for_load_css_responsive( 'elementor', 'lg' )
			);
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_elm_merge_styles' );
	/**
	 * Merge Elementor styles to the single stylesheet to increase page upload speed
	 * 
	 * @hooked trx_addons_filter_merge_styles
	 * 
	 * @param array $list List of styles to merge
	 * 
	 * @return array    List of styles to merge
	 */
	function trx_addons_elm_merge_styles( $list ) {
		if ( trx_addons_exists_elementor() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'elementor/elementor.css' ] = true;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_elm_merge_styles_responsive' ) ) {
	add_filter( "trx_addons_filter_merge_styles_responsive", 'trx_addons_elm_merge_styles_responsive' );
	/**
	 * Merge Elementor responsive styles to the single stylesheet to increase page upload speed
	 * 
	 * @hooked trx_addons_filter_merge_styles_responsive
	 * 
	 * @param array $list List of styles to merge
	 * 
	 * @return array    List of styles to merge
	 */
	function trx_addons_elm_merge_styles_responsive( $list ) {
		if ( trx_addons_exists_elementor() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'elementor/elementor.responsive.css' ] = true;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_elm_merge_scripts' ) ) {
	add_action( "trx_addons_filter_merge_scripts", 'trx_addons_elm_merge_scripts' );
	/**
	 * Merge Elementor scripts to the single file to increase page upload speed
	 * 
	 * @hooked trx_addons_filter_merge_scripts
	 * 
	 * @param array $list List of scripts to merge
	 * 
	 * @return array    List of scripts to merge
	 */
	function trx_addons_elm_merge_scripts( $list ) {
		if ( trx_addons_exists_elementor() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'elementor/elementor.js' ] = true;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_elm_not_defer_scripts' ) ) {
	add_filter( "trx_addons_filter_skip_move_scripts_down", 'trx_addons_elm_not_defer_scripts' );
	add_filter( "trx_addons_filter_skip_async_scripts_load", 'trx_addons_elm_not_defer_scripts' );
	/**
	 * Add Elementor scripts to the list with scripts not need to be deferred or loaded asynchronously
	 * 
	 * @hooked trx_addons_filter_skip_move_scripts_down
	 * @hooked trx_addons_filter_skip_async_scripts_load
	 * 
	 * @param array $list List of scripts
	 * 
	 * @return array    List of scripts
	 */
	function trx_addons_elm_not_defer_scripts( $list ) {
		$list[] = 'elementor';
		$list[] = 'backbone';
		$list[] = 'underscore';
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_elm_editor_load_scripts' ) ) {
	add_action( "elementor/editor/before_enqueue_scripts", 'trx_addons_elm_editor_load_scripts' );
	/**
	 * Load Elementor scripts and styles for the Elementor's editor mode
	 * 
	 * @hooked elementor/editor/before_enqueue_scripts
	 * 
	 * @trigger trx_addons_action_pagebuilder_admin_scripts
	 */
	function trx_addons_elm_editor_load_scripts() {
		trx_addons_load_scripts_admin( true );
		trx_addons_localize_scripts_admin();
		wp_enqueue_style(  'trx_addons-elementor-editor', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'elementor/elementor.editor.css' ), array(), null );
		wp_enqueue_script( 'trx_addons-elementor-editor', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'elementor/elementor.editor.js' ), array('jquery'), null, true );
		do_action( 'trx_addons_action_pagebuilder_admin_scripts' );
	}
}

if ( ! function_exists( 'trx_addons_elm_preview_load_scripts' ) ) {
	add_action( "elementor/frontend/after_enqueue_scripts", 'trx_addons_elm_preview_load_scripts' );
	/**
	 * Load Elementor scripts and styles for the Elementor's preview mode
	 * 
	 * @hooked elementor/frontend/after_enqueue_scripts
	 * 
	 * @trigger trx_addons_action_pagebuilder_preview_scripts
	 */
	function trx_addons_elm_preview_load_scripts() {
		if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
			wp_enqueue_script( 'trx_addons-elementor-preview', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'elementor/elementor.js' ), array('jquery'), null, true );
		}
		trx_addons_enqueue_tweenmax();
		if ( trx_addons_elm_is_preview() ) {
			do_action( 'trx_addons_action_pagebuilder_preview_scripts', 'elementor' );
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_sass_responsive' ) ) {
	add_filter( "trx_addons_filter_responsive_sizes", 'trx_addons_elm_sass_responsive', 11 );
	/**
	 * Add Elementor's responsive sizes to the list
	 * 
	 * @hooked trx_addons_filter_responsive_sizes
	 * 
	 * @param array $list List of responsive sizes
	 * 
	 * @return array    List of responsive sizes
	 */
	function trx_addons_elm_sass_responsive( $list ) {
		if ( ! isset( $list['md_lg'] ) ) {
			$list['md_lg'] = array(
									'min' => $list['sm']['max'] + 1,
									'max' => $list['lg']['max']
									);
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_elm_localize_script' ) ) {
	add_filter( "trx_addons_filter_localize_script", 'trx_addons_elm_localize_script' );
	/**
	 * Add Elementor specific vars to the localize array for the frontend scripts
	 * 
	 * @hooked trx_addons_filter_localize_script
	 * 
	 * @trigger trx_addons_filter_elementor_animate_items
	 * 
	 * @param array $vars List of vars
	 * 
	 * @return array    List of vars
	 */
	function trx_addons_elm_localize_script( $vars ) {
		$vars['elementor_stretched_section_container'] = get_option( 'elementor_stretched_section_container' );
		$vars['pagebuilder_preview_mode'] = ! empty( $vars['pagebuilder_preview_mode'] ) || trx_addons_elm_is_preview();
		// List of selectors for items inside block with specified motion effect (entrance animation)
		// and animated separately (item by item or random, not a whole block)
		$vars['elementor_animate_items'] = join( ',', apply_filters( 'trx_addons_filter_elementor_animate_items', array(
																		'.elementor-heading-title',
																		'.sc_item_subtitle',
																		'.sc_item_title',
																		'.sc_item_descr',
																		'.sc_item_posts_container + .sc_item_button',
																		'.sc_item_button.sc_title_button',
																		//'.social_item',
																		'nav > ul > li'
												) ) );
		$vars['elementor_breakpoints'] = trx_addons_elm_get_breakpoints();
		//$vars['msg_change_layout'] = esc_html__( 'After changing the layout, the page will be reloaded! Continue?', 'trx_addons' );
		//$vars['msg_change_layout_caption'] = esc_html__( 'Change layout', 'trx_addons' );
		return $vars;
	}
}

if ( ! function_exists( 'trx_addons_elm_localize_script_admin' ) ) {
	add_filter( "trx_addons_filter_localize_script_admin", 'trx_addons_elm_localize_script_admin' );
	/**
	 * Add Elementor specific vars to the localize array for the admin scripts
	 * 
	 * @hooked trx_addons_filter_localize_script_admin
	 * 
	 * @trigger trx_addons_filter_elementor_animate_items
	 * 
	 * @param array $vars List of vars
	 * 
	 * @return array    List of vars
	 */
	function trx_addons_elm_localize_script_admin( $vars ) {
		$vars['elementor_breakpoints'] = trx_addons_elm_get_breakpoints();
		return $vars;
	}
}


/* Modify core Elementor's controls
-------------------------------------------------------------------- */

if ( ! function_exists( 'trx_addons_elm_set_default_gap_extended' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_set_default_gap_extended', 10, 3 );
	/**
	 * Set 'extended' as the default value for the 'gap' param of the section
	 *
	 * @param object $element  Element object
	 * @param string $section_id  Section ID. Must be 'section_layout'
	 * @param array $args  Section arguments. Not used
	 */
	function trx_addons_elm_set_default_gap_extended( $element, $section_id, $args ) {
		if ( is_object( $element ) ) {
			$el_name = $element->get_name();
			if ( 'section' == $el_name && 'section_layout' === $section_id ) {
				$element->update_control( 'gap', array(
					'default' => 'extended',
				) );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_custom_width_to_sections' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_elm_add_custom_width_to_sections', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render', 'trx_addons_elm_add_custom_width_to_sections', 10, 1 );
	/**
	 * Add class 'elementor-section-with-custom-width' to the Elementor's sections if a parameter 'size' is specified
	 * 
	 * @hooked elementor/frontend/element/before_render (before Elementor 2.1.0)
	 * @hooked elementor/frontend/section/before_render (after Elementor 2.1.0)
	 *
	 * @param object $element  Element object
	 */
	function trx_addons_elm_add_custom_width_to_sections( $element ) {
		if ( is_object( $element ) && $element->get_name() == 'section' ) {
			//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
			$content_width = $element->get_settings( 'content_width' );
			if ( ! empty( $content_width['size'] ) && (int)$content_width['size'] > 0 ) {
				$element->add_render_attribute( '_wrapper', 'class', 'elementor-section-with-custom-width' );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_get_breakpoints' ) ) {
	/**
	 * Return Elementor's breakpoints as array
	 *
	 * @return array   List of breakpoints
	 */
	function trx_addons_elm_get_breakpoints() {
		static $bp_list = array();
		if ( trx_addons_exists_elementor() && count( $bp_list ) == 0 ) {
			$bp_elm = \Elementor\Plugin::instance()->breakpoints->get_breakpoints_config();
			if ( is_array( $bp_elm ) ) {
				foreach( $bp_elm as $bp_name => $bp_data ) {
					if ( empty( $bp_data['is_enabled'] ) ) {
						continue;
					}
					if ( $bp_name == 'widescreen' && ! isset( $bp_list['desktop'] ) ) {
						$bp_list['desktop'] = $bp_data['value'] - 1;
					}
					$bp_list[ $bp_name ] = $bp_data['direction'] == 'max' ? $bp_data['value'] : 999999;
				}
				if ( ! isset( $bp_list['desktop'] ) ) {
					$bp_list['desktop'] = ! empty( $bp_list['widescreen'] ) ? $bp_list['widescreen'] - 1 : 999999;
					asort( $bp_list );
				}
			}
		}
		return $bp_list;
	}
}

if ( ! function_exists( 'trx_addons_elm_post_edit_link' ) ) {
	add_filter( 'trx_addons_filter_post_edit_link', 'trx_addons_elm_post_edit_link', 10, 2 );
	/**
	 * Return link to edit post/page if a specified page is built with Elementor
	 *
	 * @param string $link  Link to edit post / page
	 * @param int $post_id  Post ID
	 * 
	 * @return string     Link to edit post
	 */
	function trx_addons_elm_post_edit_link( $link, $post_id ) {
		if ( trx_addons_is_built_with_elementor( $post_id ) ) {
			$link = str_replace( 'action=edit', 'action=elementor', $link );
		}
		return $link;
	}
}

if ( ! function_exists( 'trx_addons_get_list_elementor_templates' ) ) {
	/**
	 * Return list of Elementor templates
	 *
	 * @param boolean $not_selected  If true - add 'Not selected' to the begin of the list
	 * @param string $type           Type of the templates. 'all' - for all types
	 * @param string $order          Order of the templates. 'ID' - by ID, 'title' - by title
	 * 
	 * @return array                 List of templates
	 */
	function trx_addons_get_list_elementor_templates( $not_selected = false, $type = 'all', $order = 'ID' ) {
		if ( trx_addons_exists_elementor() ) {
			$args = array(
						'post_type' => 'elementor_library',
						'posts_per_page' => -1,
						'orderby' => $order,
						'order' => 'asc',
						'not_selected' => $not_selected
						);
			if ( $type != 'all' ) {
				$args['taxonomy'] = 'elementor_library_type';
				$args['taxonomy_value'] = $type;
			}
			$list = trx_addons_get_list_posts( false, $args );
		} else {
			$list = array();
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_elm_use_menu_cache' ) ) {
	add_filter( 'trx_addons_add_menu_cache', 'trx_addons_elm_use_menu_cache' );
	/**
	 * Disable menu cache if Elementor is in preview mode
	 *
	 * @param boolean $use  Use cache or not
	 * @param array $args   Menu arguments. Not used here
	 * 
	 * @return boolean      Use cache or not
	 */
	function trx_addons_elm_use_menu_cache( $use, $args = array() ) {
		if ( trx_addons_elm_is_preview() ) {
			$use = false;
		}
		return $use;
	}
}

if ( ! function_exists( 'trx_addons_elm_save_post_from_editor' ) ) {
	add_action( 'wp_ajax_elementor_ajax', 'trx_addons_elm_save_post_from_editor' );
	/**
	 * Generate action on save document from Elementor Editor
	 * 
	 * @hook wp_ajax_elementor_ajax
	 * 
	 * @trigger trx_addons_action_save_post_from_elementor
	 */
	function trx_addons_elm_save_post_from_editor() {
		$id = trx_addons_get_value_gp('editor_post_id');
		if ( (int)$id > 0 ) {
			$actions = trim( trx_addons_get_value_gp( 'actions' ) );
			if ( ! empty( $actions ) && substr( $actions, 0, 1 ) == '{' && substr( $actions, -1 ) == '}' ) {
				$actions = json_decode( $actions, true);
				if ( is_array( $actions ) && ! empty( $actions['save_builder'] ) ) {
					do_action( 'trx_addons_action_save_post_from_elementor', $id, $actions );
				}
			}
		}
	}
}


// Add Elementor's filter 'the_content' to the posts inside shortcodes (like "Blogger", "Services", etc)
//-------------------------------------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_elm_before_full_post_content' ) ) {
	add_action( 'trx_addons_action_before_full_post_content', 'trx_addons_elm_before_full_post_content' );
	/**
	 * Add Elementor's filter 'the_content' to the posts inside shortcodes (like "Blogger", "Services", etc)
	 * before the full post content will be shown
	 * 
	 * @hook trx_addons_action_before_full_post_content
	 */
	function trx_addons_elm_before_full_post_content() {
		if ( trx_addons_is_built_with_elementor( get_the_ID() ) && ! has_filter( 'the_content', array( \Elementor\Plugin::instance()->frontend, 'apply_builder_in_content' ) ) ) {
			set_query_var( 'trx_addons_elm_set_the_content_handler', 1 );
			add_filter( 'the_content', array( \Elementor\Plugin::instance()->frontend, 'apply_builder_in_content' ), \Elementor\Plugin::instance()->frontend->THE_CONTENT_FILTER_PRIORITY );
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_after_full_post_content' ) ) {
	add_action( 'trx_addons_action_after_full_post_content', 'trx_addons_elm_after_full_post_content' );
	/**
	 * Remove Elementor's filter 'the_content' from the posts inside shortcodes (like "Blogger", "Services", etc)
	 * after the full post content was shown
	 * 
	 * @hook trx_addons_action_after_full_post_content
	 */
	function trx_addons_elm_after_full_post_content() {
		if ( trx_addons_exists_elementor() && get_query_var( 'trx_addons_elm_set_the_content_handler' ) == 1 ) {
			remove_filter( 'the_content', array( \Elementor\Plugin::instance()->frontend, 'apply_builder_in_content' ), \Elementor\Plugin::instance()->frontend->THE_CONTENT_FILTER_PRIORITY );
		}
	}
}


// Additional attributes for our shortcodes (analog of 'render_attributes' in Elementor)
//-------------------------------------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_elm_sc_show_attributes' ) ) {
	add_action( 'trx_addons_action_sc_show_attributes', 'trx_addons_elm_sc_show_attributes', 10, 3 );
	/**
	 * Show additional attributes for our shortcodes (analog of 'render_attributes' in Elementor)
	 * 
	 * @hook trx_addons_action_sc_show_attributes
	 * 
	 * @param string $sc    Shortcode name
	 * @param array $args   Shortcode attributes
	 * @param string $area  Area to show attributes
	 */
	function trx_addons_elm_sc_show_attributes( $sc, $args, $area ) {
		if ( ! empty( $args['sc_elementor_object'] ) && is_object( $args['sc_elementor_object'] ) ) {
			echo ' ' . $args['sc_elementor_object']->get_render_attribute_string( $area );
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_remove_object_from_args' ) ) {
	add_filter( 'trx_addons_filter_sc_args_to_serialize', 'trx_addons_elm_remove_object_from_args' );
	/**
	 * Remove 'sc_elementor_object' from the shortcode attributes to serialize
	 * 
	 * @hook trx_addons_filter_sc_args_to_serialize
	 * 
	 * @param array $args   Shortcode attributes to serialize
	 * 
	 * @return array        Shortcode attributes without 'sc_elementor_object'
	 */
	function trx_addons_elm_remove_object_from_args($args) {
		if ( isset( $args['sc_elementor_object'] ) ) {
			unset( $args['sc_elementor_object'] );
		}
		return $args;
	}
}


// Init Elementor's support
//--------------------------------------------------------

if ( ! function_exists( 'trx_addons_elm_init_once' ) ) {
	add_action( 'init', 'trx_addons_elm_init_once', 2 );
	/**
	 * Set Elementor's options once after theme's activation
	 * 
	 * @hook init
	 * 
	 * @trigger trx_addons_action_set_elementor_options
	 */
	function trx_addons_elm_init_once() {
		if ( trx_addons_exists_elementor() && ! get_option( 'trx_addons_setup_elementor_options', false ) ) {
			// Set components specific values to the Elementor's options
			do_action( 'trx_addons_action_set_elementor_options' );
			// Set flag to prevent change Elementor's options again
			update_option( 'trx_addons_setup_elementor_options', 1 );
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_categories' ) ) {
	add_action( 'elementor/elements/categories_registered', 'trx_addons_elm_add_categories' );
	/**
	 * Add a custom category to Elementor for ThemeREX Addons Shortcodes
	 * 
	 * @hook elementor/elements/categories_registered
	 * 
	 * @param object $mgr Elementor's manager
	 */
	function trx_addons_elm_add_categories( $mgr = null ) {

		static $added = false;

		if ( ! $added ) {

			if ( $mgr == null ) {
				$mgr = \Elementor\Plugin::instance()->elements_manager;
			}
			
			// Add a custom category for ThemeREX Addons Shortcodes
			$mgr->add_category( 
				'trx_addons-elements',
				array(
					'title' => __( 'ThemeREX Addons Elements', 'trx_addons' ),
					'icon' => 'eicon-apps', //default icon
					'active' => true,
				)
			);

			// Add a custom category for ThemeREX Addons Widgets
			$mgr->add_category( 
				'trx_addons-widgets',
				array(
					'title' => __( 'ThemeREX Addons Widgets', 'trx_addons' ),
					'icon' => 'eicon-gallery-grid', //default icon
					'active' => false,
				)
			);

			// Add a custom category for ThemeREX Addons CPT
			$mgr->add_category( 
				'trx_addons-cpt',
				array(
					'title' => __( 'ThemeREX Addons Extensions', 'trx_addons' ),
					'icon' => 'eicon-gallery-grid', //default icon
					'active' => false,
				)
			);

			// Add a custom category for third-party shortcodes
			$mgr->add_category( 
				'trx_addons-support',
				array(
					'title' => __( 'ThemeREX Addons Support', 'trx_addons' ),
					'icon' => 'eicon-woocommerce', //default icon
					'active' => false,
				)
			);

			$added = true;
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_wordpress_widget_args' ) ) {
	add_filter( 'elementor/widgets/wordpress/widget_args', 'trx_addons_elm_wordpress_widget_args', 10, 2 );
	/**
	 * Replace widget's args with theme-specific args
	 * 
	 * @hook elementor/widgets/wordpress/widget_args
	 * 
	 * @param array $widget_args    Widget arguments
	 * @param object $widget        Widget object
	 * 
	 * @return array                Modified widget arguments
	 */
	function trx_addons_elm_wordpress_widget_args( $widget_args, $widget ) {
		return trx_addons_prepare_widgets_args( $widget->get_name(), $widget->get_name(), $widget_args );
	}
}

if ( ! function_exists( 'trx_addons_elm_print_inline_css' ) ) {
	add_filter( 'elementor/widget/render_content', 'trx_addons_elm_print_inline_css', 10, 2 );
	/**
	 * Add inline css to the Elementor's widget content if current action is 'wp_ajax_elementor_render_widget'
	 * or 'admin_action_elementor' (old Elementor) or 'elementor_ajax' (new Elementor)
	 * (called from Elementor Editor via AJAX or first load page content to the Editor)
	 * 
	 * @hook elementor/widget/render_content
	 * 
	 * @param string $content       Widget's content
	 * @param object $widget        Widget object. Not used
	 * 
	 * @return string               Modified widget's content
	 */
	function trx_addons_elm_print_inline_css( $content, $widget = null ) {
		if (   doing_action( 'wp_ajax_elementor_render_widget' )
			|| doing_action( 'admin_action_elementor' )
			|| doing_action( 'elementor_ajax' )
			|| doing_action( 'wp_ajax_elementor_ajax' )
		) {
			$css = trx_addons_get_inline_css( true );
			if ( ! empty( $css ) ) {
				$content .= sprintf( '<style type="text/css">%s</style>', $css );
			}
		}
		return $content;
	}
}

if ( ! function_exists( 'trx_addons_elm_add_responsive_columns_to_slider' ) ) {
	add_action( 'trx_addons_action_sc_show_attributes', 'trx_addons_elm_add_responsive_columns_to_slider', 10, 3 );
	/**
	 * Add attribute with responsive columns breakpoints to the all slider's output
	 * 
	 * @hooked trx_addons_action_sc_show_attributes
	 * 
	 * @param string $sc            Shortcode name
	 * @param array $args           Shortcode's attributes
	 * @param string $area          Attribute's area
	 */
	function trx_addons_elm_add_responsive_columns_to_slider( $sc, $args, $area ) {
		if ( $area == 'sc_slider_wrapper' && trx_addons_exists_elementor() && ! empty( $args['columns'] ) ) {
			$bp = trx_addons_elm_get_breakpoints();
			if ( is_array( $bp ) ) {
				// Sort breakpoint on a reverse order (from 'widescreen' to 'mobile' )
				arsort( $bp );
				// Fill an array with columns for all breakpoints
				$columns = $args['columns'];
				$per_view = array();
				foreach ( $bp as $bp_name => $bp_max ) {
					if ( ! empty( $args["columns_{$bp_name}"] ) ) {
						$columns = $per_view[ $bp_max ] = is_array( $args["columns_{$bp_name}"] )
													? ( ! empty( $args["columns_{$bp_name}"]['size'] )
														? $args["columns_{$bp_name}"]['size']
														: $columns
														)
													: $args["columns_{$bp_name}"];
					}
				}
				// Add (override if exists) the resolution for 'desktop' with a main parameter 'columns'
				$per_view[ $bp['desktop'] ] = $args['columns'];
				// Sort a columns list on a direct order (from 'mobile' to 'widescreen')
				ksort( $per_view );
				// Add data-parameter with values for each breakpoint
				echo ' data-slides-per-view-breakpoints="' . esc_attr( json_encode( $per_view ) ) . '"' ;
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_responsive_classes' ) ) {
	add_action( 'trx_addons_filter_responsive_classes', 'trx_addons_elm_add_responsive_classes', 10, 4 );
	/**
	 * Return a list of the responsive classes for the shortcode's attribute
	 * 
	 * @hooked trx_addons_filter_responsive_classes
	 * 
	 * @param array  $list     List of the responsive classes
	 * @param string $prefix   Prefix for the class name
	 * @param string $atts     Shortcode's or item's attributes
	 * @param string $param    Attribute name
	 * 
	 * @return array           List of the responsive classes
	 */
	function trx_addons_elm_add_responsive_classes( $list, $prefix, $atts, $param ) {
		if ( trx_addons_exists_elementor() ) {
			$bp = trx_addons_elm_get_breakpoints();
			if ( is_array( $bp ) ) {
				foreach ( $bp as $bp_name => $bp_max ) {
					if ( ! empty( $atts["{$param}_{$bp_name}"] ) ) {
						$list[] = $prefix . $atts[ "{$param}_{$bp_name}" ] . '_' . $bp_name;
					}
				}
			}
		}
		return $list;
	}
}


// Load extensions
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/fixes.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/go-pro.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/icons.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/stack-sections.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/content-width.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/gradient-animation.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/hide-bg-image.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/extend-background.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/shift-push.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/fly.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/animation-type.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/hide-on-xxx.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/widget-tabs.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/widget-spacer.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/shape-divider.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/parallax-blocks-and-bg-layers.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/parallax-image.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/background-text.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/section-toggle.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/sticky-position.php';
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/macros-in-core-elements.php';

// Attention! New effects in the Swiper 8+ are not backward compatible
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/swiper-effects.php';

require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/edit-layout-in-preview.php';
// Commented because it's already fixed in the Elementor
//require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/extensions/edit-with-elementor.php';


if ( ! function_exists( 'trx_addons_elm_register_custom_controls' ) ) {
	add_action( trx_addons_elementor_get_action_for_controls_registration(), 'trx_addons_elm_register_custom_controls' );
	/**
	 * Register custom controls for Elementor
	 * 
	 * @hooked elementor/controls/register
	 * 
	 * @param object $controls_manager  Controls manager object
	 */
	function trx_addons_elm_register_custom_controls( $controls_manager ) {
		$controls = array('trx_icons');
		foreach ( $controls as $control_id ) {
			$control_filename = str_replace('_', '-', $control_id);
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . "elementor/params/{$control_filename}/{$control_filename}.php";
			$class_name = 'Trx_Addons_Elementor_Control_' . ucwords( $control_id );
			if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.5.0', '>=' ) ) {
				$controls_manager->register( new $class_name() );	
			} else {
				$controls_manager->register_control( $control_id, new $class_name() );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_init' ) ) {
	add_action( 'elementor/init', 'trx_addons_elm_init' );
	/**
	 * Init our support for Elementor
	 */
	function trx_addons_elm_init() {

		// Add categories (for old Elementor)
		trx_addons_elm_add_categories();

		// Load base class for our shortcodes and widgets
		require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/elementor-class-widget.php';
	}
}


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/elementor-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_elementor() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'elementor/elementor-demo-ocdi.php';
}
