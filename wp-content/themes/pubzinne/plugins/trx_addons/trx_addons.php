<?php
/* ThemeREX Addons support functions
------------------------------------------------------------------------------- */

// Add theme-specific functions
require_once pubzinne_get_file_dir( 'plugins/trx_addons/trx_addons-setup.php' );

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if ( ! function_exists( 'pubzinne_trx_addons_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_trx_addons_theme_setup1', 1 );
	function pubzinne_trx_addons_theme_setup1() {
		if ( pubzinne_exists_trx_addons() ) {
			add_filter( 'pubzinne_filter_list_posts_types', 'pubzinne_trx_addons_list_post_types' );
			add_filter( 'pubzinne_filter_list_header_footer_types', 'pubzinne_trx_addons_list_header_footer_types' );
			add_filter( 'pubzinne_filter_list_header_styles', 'pubzinne_trx_addons_list_header_styles' );
			add_filter( 'pubzinne_filter_list_footer_styles', 'pubzinne_trx_addons_list_footer_styles' );
			add_filter( 'pubzinne_filter_list_sidebar_styles', 'pubzinne_trx_addons_list_sidebar_styles' );
			add_filter( 'pubzinne_filter_list_blog_styles', 'pubzinne_trx_addons_list_blog_styles', 10, 2 );
			add_action( 'wp', 'pubzinne_trx_addons_add_link_edit_layout' );	// Old way: 'pubzinne_action_load_options'
			add_action( 'pubzinne_action_save_options', 'pubzinne_trx_addons_action_save_options', 1 );
			add_action( 'pubzinne_action_before_body', 'pubzinne_trx_addons_action_before_body', 1);
			add_action( 'pubzinne_action_page_content_wrap', 'pubzinne_trx_addons_action_page_content_wrap', 10, 1 );
			add_action( 'pubzinne_action_before_header', 'pubzinne_trx_addons_action_before_header' );
			add_action( 'pubzinne_action_after_header', 'pubzinne_trx_addons_action_after_header' );
			add_action( 'pubzinne_action_before_footer', 'pubzinne_trx_addons_action_before_footer' );
			add_action( 'pubzinne_action_after_footer', 'pubzinne_trx_addons_action_after_footer' );
			add_action( 'pubzinne_action_before_sidebar_wrap', 'pubzinne_trx_addons_action_before_sidebar_wrap', 10, 1 );
			add_action( 'pubzinne_action_after_sidebar_wrap', 'pubzinne_trx_addons_action_after_sidebar_wrap', 10, 1 );
			add_action( 'pubzinne_action_before_sidebar', 'pubzinne_trx_addons_action_before_sidebar', 10, 1 );
			add_action( 'pubzinne_action_after_sidebar', 'pubzinne_trx_addons_action_after_sidebar', 10, 1 );
			add_action( 'pubzinne_action_between_posts', 'pubzinne_trx_addons_action_between_posts' );
			add_action( 'pubzinne_action_before_post_header', 'pubzinne_trx_addons_action_before_post_header' );
			add_action( 'pubzinne_action_after_post_header', 'pubzinne_trx_addons_action_after_post_header' );
			add_action( 'pubzinne_action_before_post_content', 'pubzinne_trx_addons_action_before_post_content' );
			add_action( 'pubzinne_action_after_post_content', 'pubzinne_trx_addons_action_after_post_content' );
			add_filter( 'trx_addons_filter_default_layouts', 'pubzinne_trx_addons_default_layouts' );
			add_filter( 'trx_addons_filter_load_options', 'pubzinne_trx_addons_default_components' );
			add_filter( 'trx_addons_cpt_list_options', 'pubzinne_trx_addons_cpt_list_options', 10, 2 );
			add_filter( 'trx_addons_filter_sass_import', 'pubzinne_trx_addons_sass_import', 10, 2 );
			add_filter( 'trx_addons_filter_override_options', 'pubzinne_trx_addons_override_options' );
			add_filter( 'trx_addons_filter_post_meta', 'pubzinne_trx_addons_post_meta', 10, 2 );
			add_filter( 'trx_addons_filter_post_meta_args',	'pubzinne_trx_addons_post_meta_args', 10, 3);
			add_filter( 'pubzinne_filter_post_meta_args', 'pubzinne_trx_addons_post_meta_args', 10, 3 );
			add_filter( 'pubzinne_filter_list_meta_parts', 'pubzinne_trx_addons_list_meta_parts' );
			add_filter( 'trx_addons_filter_get_list_meta_parts', 'pubzinne_trx_addons_get_list_meta_parts' );
			add_action( 'pubzinne_action_show_post_meta', 'pubzinne_trx_addons_show_post_meta', 10, 3 );
		}
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'pubzinne_trx_addons_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_trx_addons_theme_setup9', 9 );
	function pubzinne_trx_addons_theme_setup9() {
		if ( pubzinne_exists_trx_addons() ) {
			add_filter( 'trx_addons_filter_add_thumb_sizes', 'pubzinne_trx_addons_add_thumb_sizes' );
			add_filter( 'trx_addons_filter_get_thumb_size', 'pubzinne_trx_addons_get_thumb_size' );
			add_filter( 'trx_addons_filter_featured_image', 'pubzinne_trx_addons_featured_image', 10, 2 );
			add_filter( 'trx_addons_filter_get_list_sc_image_hover', 'pubzinne_trx_addons_get_list_sc_image_hover' );
			add_filter( 'trx_addons_filter_no_image', 'pubzinne_trx_addons_no_image' );
			add_filter( 'trx_addons_filter_sc_blogger_template', 'pubzinne_trx_addons_sc_blogger_template', 10, 2 );
			add_filter( 'trx_addons_filter_get_list_icons_classes', 'pubzinne_trx_addons_get_list_icons_classes', 10, 2 );
			add_filter( 'trx_addons_filter_clear_icon_name', 'pubzinne_trx_addons_clear_icon_name' );
			add_filter( 'pubzinne_filter_add_sort_order', 'pubzinne_trx_addons_add_sort_order', 10, 3 );
			add_filter( 'pubzinne_filter_post_content', 'pubzinne_trx_addons_filter_post_content' );
			add_filter( 'pubzinne_filter_get_post_categories', 'pubzinne_trx_addons_get_post_categories' );
			add_filter( 'pubzinne_filter_get_post_date', 'pubzinne_trx_addons_get_post_date' );
			add_filter( 'trx_addons_filter_get_post_date', 'pubzinne_trx_addons_get_post_date_wrap' );
			add_filter( 'pubzinne_filter_post_type_taxonomy', 'pubzinne_trx_addons_post_type_taxonomy', 10, 2 );
			add_filter( 'pubzinne_filter_term_name', 'pubzinne_trx_addons_term_name', 10, 2 );
			add_filter( 'trx_addons_filter_theme_logo', 'pubzinne_trx_addons_theme_logo' );
			add_filter( 'trx_addons_filter_show_site_name_as_logo', 'pubzinne_trx_addons_show_site_name_as_logo' );
			add_filter( 'trx_addons_filter_get_theme_info', 'pubzinne_trx_addons_get_theme_info', 9 );
			add_filter( 'trx_addons_filter_get_theme_data', 'pubzinne_trx_addons_get_theme_data', 9, 2 );
			add_filter( 'pubzinne_filter_sidebar_present', 'pubzinne_trx_addons_sidebar_present' );
			add_filter( 'trx_addons_filter_get_theme_file_dir', 'pubzinne_trx_addons_get_theme_file_dir', 10, 3 );
			add_filter( 'trx_addons_filter_get_theme_folder_dir', 'pubzinne_trx_addons_get_theme_folder_dir', 10, 3 );
			add_filter( 'trx_addons_filter_privacy_text', 'pubzinne_trx_addons_privacy_text' );
			add_filter( 'trx_addons_filter_privacy_url', 'pubzinne_trx_addons_privacy_url' );
			add_action( 'pubzinne_action_show_layout', 'pubzinne_trx_addons_action_show_layout', 10, 2 );
			add_filter( 'trx_addons_filter_get_theme_accent_color', 'pubzinne_trx_addons_get_theme_accent_color' );
			add_filter( 'pubzinne_filter_detect_blog_mode', 'pubzinne_trx_addons_detect_blog_mode' );
			add_filter( 'pubzinne_filter_get_blog_title', 'pubzinne_trx_addons_get_blog_title' );
			add_action( 'pubzinne_action_login', 'pubzinne_trx_addons_action_login' );
			add_action( 'pubzinne_action_cart', 'pubzinne_trx_addons_action_cart' );
			add_action( 'pubzinne_action_breadcrumbs', 'pubzinne_trx_addons_action_breadcrumbs' );
			add_filter( 'pubzinne_filter_get_translated_layout', 'pubzinne_trx_addons_filter_get_translated_layout', 10, 1 );
			if ( is_admin() ) {
				add_filter( 'pubzinne_filter_allow_override_options', 'pubzinne_trx_addons_allow_override_options', 10, 2 );
				add_filter( 'pubzinne_filter_allow_theme_icons', 'pubzinne_trx_addons_allow_theme_icons', 10, 2 );
				add_filter( 'trx_addons_filter_export_options', 'pubzinne_trx_addons_export_options' );
				add_filter( 'pubzinne_filter_options_get_list_choises', 'pubzinne_trx_addons_options_get_list_choises', 999, 2 );
				add_filter( 'trx_addons_filter_disallow_term_name_modify', 'pubzinne_trx_addons_disallow_term_name_modify', 10, 3 );
			} else {
				add_filter( 'trx_addons_filter_inc_views', 'pubzinne_trx_addons_inc_views' );
				add_filter( 'pubzinne_filter_related_thumb_size', 'pubzinne_trx_addons_related_thumb_size' );
				add_filter( 'pubzinne_filter_show_related_posts', 'pubzinne_trx_addons_show_related_posts' );
				add_filter( 'trx_addons_filter_show_related_posts_after_article', 'pubzinne_trx_addons_show_related_posts_after_article' );
				add_filter( 'trx_addons_filter_args_related', 'pubzinne_trx_addons_args_related' );
				add_filter( 'trx_addons_filter_seo_snippets', 'pubzinne_trx_addons_seo_snippets' );
				add_action( 'trx_addons_action_article_start', 'pubzinne_trx_addons_article_start', 10, 1 );
				add_filter( 'pubzinne_filter_get_mobile_menu', 'pubzinne_trx_addons_get_mobile_menu' );
				add_action( 'pubzinne_action_user_meta', 'pubzinne_trx_addons_action_user_meta', 10, 1 );
				add_filter( 'trx_addons_filter_featured_image_override', 'pubzinne_trx_addons_featured_image_override' );
				add_filter( 'trx_addons_filter_get_current_mode_image', 'pubzinne_trx_addons_get_current_mode_image' );
				add_filter( 'pubzinne_filter_get_post_iframe', 'pubzinne_trx_addons_get_post_iframe', 10, 1 );
				add_action( 'pubzinne_action_before_full_post_content', 'pubzinne_trx_addons_before_full_post_content' );
				add_action( 'pubzinne_action_after_full_post_content', 'pubzinne_trx_addons_after_full_post_content' );
				add_filter( 'trx_addons_filter_disable_animation_on_mobile', 'pubzinne_trx_addons_disable_animation_on_mobile' );
			}
		}

		// Add this filter any time: if plugin exists - load plugin's styles, if not exists - load layouts.css instead plugin's styles
		add_action( 'wp_enqueue_scripts', 'pubzinne_trx_addons_layouts_styles' );

		if ( pubzinne_exists_trx_addons() ) {
			add_action( 'wp_enqueue_scripts', 'pubzinne_trx_addons_frontend_scripts', 1100 );
			add_action( 'wp_enqueue_scripts', 'pubzinne_trx_addons_responsive_styles', 2000 );
			add_filter( 'pubzinne_filter_merge_styles', 'pubzinne_trx_addons_merge_styles' );
			add_filter( 'pubzinne_filter_merge_styles_responsive', 'pubzinne_trx_addons_merge_styles_responsive' );
			add_filter( 'pubzinne_filter_merge_scripts', 'pubzinne_trx_addons_merge_scripts' );
			add_filter( 'pubzinne_filter_prepare_css', 'pubzinne_trx_addons_prepare_css', 10, 2 );
			add_filter( 'pubzinne_filter_prepare_js', 'pubzinne_trx_addons_prepare_js', 10, 2 );
			add_filter( 'pubzinne_filter_localize_script', 'pubzinne_trx_addons_localize_script' );
		}

		if ( is_admin() ) {
			add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_trx_addons_tgmpa_required_plugins', 1 );
			add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_trx_addons_tgmpa_required_plugins_all', 999 );
		} else {
			add_action( 'pubzinne_action_search', 'pubzinne_trx_addons_action_search', 10, 1 );
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'pubzinne_trx_addons_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_trx_addons_tgmpa_required_plugins', 1 );
	function pubzinne_trx_addons_tgmpa_required_plugins( $list = array() ) {
		if ( pubzinne_storage_isset( 'required_plugins', 'trx_addons' ) && pubzinne_storage_get_array( 'required_plugins', 'trx_addons', 'install' ) !== false ) {
			$path = pubzinne_get_plugin_source_path( 'plugins/trx_addons/trx_addons.zip' );
			if ( ! empty( $path ) || pubzinne_get_theme_setting( 'tgmpa_upload' ) ) {
				$list[] = array(
					'name'     => pubzinne_storage_get_array( 'required_plugins', 'trx_addons', 'title' ),
					'slug'     => 'trx_addons',
					'version'  => '2.18.1',
					'source'   => ! empty( $path ) ? $path : 'upload://trx_addons.zip',
					'required' => true,
				);
			}
		}
		return $list;
	}
}


/* Add options in the Theme Options Customizer
------------------------------------------------------------------------------- */

if ( ! function_exists( 'pubzinne_trx_addons_cpt_list_options' ) ) {
	// Handler of the add_filter( 'trx_addons_cpt_list_options', 'pubzinne_trx_addons_cpt_list_options', 10, 2);
	function pubzinne_trx_addons_cpt_list_options( $options, $cpt ) {
		if ( 'layouts' == $cpt && PUBZINNE_THEME_FREE ) {
			$options = array();
		} elseif ( is_array( $options ) ) {
			foreach ( $options as $k => $v ) {
				// Store this option in the external (not theme's) storage
				$options[ $k ]['options_storage'] = 'trx_addons_options';
				// Hide this option from plugin's options (only for overriden options)
				if ( in_array( $cpt, array( 'cars', 'cars_agents', 'certificates', 'courses', 'dishes', 'portfolio', 'properties', 'agents', 'resume', 'services', 'sport', 'team', 'testimonials' ) ) ) {
					$options[ $k ]['hidden'] = true;
				}
			}
		}
		return $options;
	}
}

// Return plugin's specific options for CPT
if ( ! function_exists( 'pubzinne_trx_addons_get_list_cpt_options' ) ) {
	function pubzinne_trx_addons_get_list_cpt_options( $cpt ) {
		$options = array();
		if ( 'cars' == $cpt ) {
			$options = array_merge(
				trx_addons_cpt_cars_get_list_options(),
				trx_addons_cpt_cars_agents_get_list_options()
			);
		} elseif ( 'certificates' == $cpt ) {
			$options = trx_addons_cpt_certificates_get_list_options();
		} elseif ( 'courses' == $cpt ) {
			$options = trx_addons_cpt_courses_get_list_options();
		} elseif ( 'dishes' == $cpt ) {
			$options = trx_addons_cpt_dishes_get_list_options();
		} elseif ( 'portfolio' == $cpt ) {
			$options = trx_addons_cpt_portfolio_get_list_options();
		} elseif ( 'resume' == $cpt ) {
			$options = trx_addons_cpt_resume_get_list_options();
		} elseif ( 'services' == $cpt ) {
			$options = trx_addons_cpt_services_get_list_options();
		} elseif ( 'properties' == $cpt ) {
			$options = array_merge(
				trx_addons_cpt_properties_get_list_options(),
				trx_addons_cpt_agents_get_list_options()
			);
		} elseif ( 'sport' == $cpt ) {
			$options = trx_addons_cpt_sport_get_list_options();
		} elseif ( 'team' == $cpt ) {
			$options = trx_addons_cpt_team_get_list_options();
		} elseif ( 'testimonials' == $cpt ) {
			$options = trx_addons_cpt_testimonials_get_list_options();
		}

		foreach ( $options as $k => $v ) {
			// Disable refresh the preview area on change any plugin's option
			$options[ $k ]['refresh'] = false;
			// Remove parameter 'hidden'
			if ( ! empty( $v['hidden'] ) ) {
				unset( $options[ $k ]['hidden'] );
			}
			// Add description
			if ( 'info' == $v['type'] ) {
				$options[ $k ]['desc'] = wp_kses_data( __( 'In order to see changes made by settings of this section, click "Save" and refresh the page', 'pubzinne' ) );
			}
		}
		return $options;
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'pubzinne_trx_addons_setup3' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_trx_addons_setup3', 3 );
	function pubzinne_trx_addons_setup3() {
		// Section 'Cars' - settings to show 'Cars' blog archive and single posts
		if ( pubzinne_exists_cars() ) {
			pubzinne_storage_merge_array(
				'options', '', array_merge(
					array(
						'cars' => array(
							'title' => esc_html__( 'Cars', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the cars pages.', 'pubzinne' ) ),
							'type'  => 'section',
						),
					),
					pubzinne_trx_addons_get_list_cpt_options( 'cars' ),
					pubzinne_options_get_list_cpt_options( 'cars' ),
					array(
						'single_info_cars'        => array(
							'title' => esc_html__( 'Single car', 'pubzinne' ),
							'desc'  => '',
							'type'  => 'info',
						),
						'show_related_posts_cars' => array(
							'title' => esc_html__( 'Show related posts', 'pubzinne' ),
							'desc'  => wp_kses_data( __( "Show section 'Related cars' on the single car page", 'pubzinne' ) ),
							'std'   => 1,
							'type'  => 'switch',
						),
						'related_posts_cars'      => array(
							'title'      => esc_html__( 'Related cars', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many related cars should be displayed on the single car page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_cars' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 9 ),
							'type'       => 'select',
						),
						'related_columns_cars'    => array(
							'title'      => esc_html__( 'Related columns', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many columns should be used to output related cars on the single car page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_cars' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 4 ),
							'type'       => 'select',
						),
					)
				)
			);
		}

		// Section 'Certificates'
		if ( pubzinne_exists_certificates() ) {
			pubzinne_storage_merge_array(
				'options', '', array_merge(
					array(
						'certificates' => array(
							'title' => esc_html__( 'Certificates', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display "Certificates"', 'pubzinne' ) ),
							'type'  => 'section',
						),
					),
					pubzinne_trx_addons_get_list_cpt_options( 'certificates' )
				)
			);
		}

		// Section 'Courses' - settings to show 'Courses' blog archive and single posts
		if ( pubzinne_exists_courses() ) {

			pubzinne_storage_merge_array(
				'options', '', array_merge(
					array(
						'courses' => array(
							'title' => esc_html__( 'Courses', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the courses pages', 'pubzinne' ) ),
							'type'  => 'section',
						),
					),
					pubzinne_trx_addons_get_list_cpt_options( 'courses' ),
					pubzinne_options_get_list_cpt_options( 'courses' ),
					array(
						'single_info_courses'        => array(
							'title' => esc_html__( 'Single course', 'pubzinne' ),
							'desc'  => '',
							'type'  => 'info',
						),
						'show_related_posts_courses' => array(
							'title' => esc_html__( 'Show related posts', 'pubzinne' ),
							'desc'  => wp_kses_data( __( "Show section 'Related courses' on the single course page", 'pubzinne' ) ),
							'std'   => 1,
							'type'  => 'switch',
						),
						'related_posts_courses'      => array(
							'title'      => esc_html__( 'Related courses', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many related courses should be displayed on the single course page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_courses' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 9 ),
							'type'       => 'select',
						),
						'related_columns_courses'    => array(
							'title'      => esc_html__( 'Related columns', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many columns should be used to output related courses on the single course page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_courses' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 4 ),
							'type'       => 'select',
						),
					)
				)
			);
		}

		// Section 'Dishes' - settings to show 'Dishes' blog archive and single posts
		if ( pubzinne_exists_dishes() ) {

			pubzinne_storage_merge_array(
				'options', '', array_merge(
					array(
						'dishes' => array(
							'title' => esc_html__( 'Dishes', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the dishes pages', 'pubzinne' ) ),
							'type'  => 'section',
						),
					),
					pubzinne_trx_addons_get_list_cpt_options( 'dishes' ),
					pubzinne_options_get_list_cpt_options( 'dishes' ),
					array(
						'single_info_dishes'        => array(
							'title' => esc_html__( 'Single dish', 'pubzinne' ),
							'desc'  => '',
							'type'  => 'info',
						),
						'show_related_posts_dishes' => array(
							'title' => esc_html__( 'Show related posts', 'pubzinne' ),
							'desc'  => wp_kses_data( __( "Show section 'Related dishes' on the single dish page", 'pubzinne' ) ),
							'std'   => 1,
							'type'  => 'switch',
						),
						'related_posts_dishes'      => array(
							'title'      => esc_html__( 'Related dishes', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many related dishes should be displayed on the single dish page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_dishes' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 9 ),
							'type'       => 'select',
						),
						'related_columns_dishes'    => array(
							'title'      => esc_html__( 'Related columns', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many columns should be used to output related dishes on the single dish page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_dishes' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 4 ),
							'type'       => 'select',
						),
					)
				)
			);
		}

		// Section 'Portfolio' - settings to show 'Portfolio' blog archive and single posts
		if ( pubzinne_exists_portfolio() ) {
			pubzinne_storage_merge_array(
				'options', '', array_merge(
					array(
						'portfolio' => array(
							'title' => esc_html__( 'Portfolio', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the portfolio pages', 'pubzinne' ) ),
							'type'  => 'section',
						),
					),
					pubzinne_trx_addons_get_list_cpt_options( 'portfolio' ),
					pubzinne_options_get_list_cpt_options( 'portfolio' ),
					array(
						'single_info_portfolio'        => array(
							'title' => esc_html__( 'Single portfolio item', 'pubzinne' ),
							'desc'  => '',
							'type'  => 'info',
						),
						'show_related_posts_portfolio' => array(
							'title' => esc_html__( 'Show related posts', 'pubzinne' ),
							'desc'  => wp_kses_data( __( "Show section 'Related portfolio items' on the single portfolio page", 'pubzinne' ) ),
							'std'   => 1,
							'type'  => 'switch',
						),
						'related_posts_portfolio'      => array(
							'title'      => esc_html__( 'Related portfolio items', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many related portfolio items should be displayed on the single portfolio page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_portfolio' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 9 ),
							'type'       => 'select',
						),
						'related_columns_portfolio'    => array(
							'title'      => esc_html__( 'Related columns', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many columns should be used to output related portfolio on the single portfolio page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_portfolio' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 4 ),
							'type'       => 'select',
						),
					)
				)
			);
		}

		// Section 'Properties' - settings to show 'Properties' blog archive and single posts
		if ( pubzinne_exists_properties() ) {

			pubzinne_storage_merge_array(
				'options', '', array_merge(
					array(
						'properties' => array(
							'title' => esc_html__( 'Properties', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the properties pages', 'pubzinne' ) ),
							'type'  => 'section',
						),
					),
					pubzinne_trx_addons_get_list_cpt_options( 'properties' ),
					pubzinne_options_get_list_cpt_options( 'properties' ),
					array(
						'single_info_properties'        => array(
							'title' => esc_html__( 'Single property', 'pubzinne' ),
							'desc'  => '',
							'type'  => 'info',
						),
						'show_related_posts_properties' => array(
							'title' => esc_html__( 'Show related posts', 'pubzinne' ),
							'desc'  => wp_kses_data( __( "Show section 'Related properties' on the single property page", 'pubzinne' ) ),
							'std'   => 1,
							'type'  => 'switch',
						),
						'related_posts_properties'      => array(
							'title'      => esc_html__( 'Related properties', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many related properties should be displayed on the single property page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_properties' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 9 ),
							'type'       => 'select',
						),
						'related_columns_properties'    => array(
							'title'      => esc_html__( 'Related columns', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many columns should be used to output related properties on the single property page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_properties' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 4 ),
							'type'       => 'select',
						),
					)
				)
			);
		}

		// Section 'Resume'
		if ( pubzinne_exists_resume() ) {
			pubzinne_storage_merge_array(
				'options', '', array_merge(
					array(
						'resume' => array(
							'title' => esc_html__( 'Resume', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display "Resume"', 'pubzinne' ) ),
							'type'  => 'section',
						),
					),
					pubzinne_trx_addons_get_list_cpt_options( 'resume' )
				)
			);
		}

		// Section 'Services' - settings to show 'Services' blog archive and single posts
		if ( pubzinne_exists_services() ) {

			pubzinne_storage_merge_array(
				'options', '', array_merge(
					array(
						'services' => array(
							'title' => esc_html__( 'Services', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the services pages', 'pubzinne' ) ),
							'type'  => 'section',
						),
					),
					pubzinne_trx_addons_get_list_cpt_options( 'services' ),
					pubzinne_options_get_list_cpt_options( 'services' ),
					array(
						'single_info_services'        => array(
							'title' => esc_html__( 'Single service item', 'pubzinne' ),
							'desc'  => '',
							'type'  => 'info',
						),
						'show_related_posts_services' => array(
							'title' => esc_html__( 'Show related posts', 'pubzinne' ),
							'desc'  => wp_kses_data( __( "Show section 'Related services' on the single service page", 'pubzinne' ) ),
							'std'   => 0,
							'type'  => 'switch',
						),
						'related_posts_services'      => array(
							'title'      => esc_html__( 'Related services', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many related services should be displayed on the single service page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_services' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 9 ),
							'type'       => 'select',
						),
						'related_columns_services'    => array(
							'title'      => esc_html__( 'Related columns', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many columns should be used to output related services on the single service page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_services' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 4 ),
							'type'       => 'select',
						),
					)
				)
			);
		}

		// Section 'Sport' - settings to show 'Sport' blog archive and single posts
		if ( pubzinne_exists_sport() ) {
			pubzinne_storage_merge_array(
				'options', '', array_merge(
					array(
						'sport' => array(
							'title' => esc_html__( 'Sport', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the sport pages', 'pubzinne' ) ),
							'type'  => 'section',
						),
					),
					pubzinne_trx_addons_get_list_cpt_options( 'sport' ),
					pubzinne_options_get_list_cpt_options( 'sport' )
				)
			);
		}

		// Section 'Team' - settings to show 'Team' blog archive and single posts
		if ( pubzinne_exists_team() ) {
			pubzinne_storage_merge_array(
				'options', '', array_merge(
					array(
						'team' => array(
							'title' => esc_html__( 'Team', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display the team members pages.', 'pubzinne' ) ),
							'type'  => 'section',
						),
					),
					pubzinne_trx_addons_get_list_cpt_options( 'team' ),
					pubzinne_options_get_list_cpt_options( 'team' ),
					array(
						'single_info_team'            => array(
							'title' => esc_html__( 'Team member single page', 'pubzinne' ),
							'desc'  => '',
							'type'  => 'info',
						),
						'show_related_posts_team'     => array(
							'title' => esc_html__( 'Show team member’s posts', 'pubzinne' ),
							'desc'  => wp_kses_data( __( "Show section 'Team members posts' on the team members single page", 'pubzinne' ) ),
							'std'   => 0,
							'type'  => 'switch',
						),
						'related_posts_team'          => array(
							'title'      => esc_html__( 'Post count', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many posts should be displayed on the single team members page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_team' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 9 ),
							'type'       => 'select',
						),
						'related_columns_team'       => array(
							'title'      => esc_html__( 'Post columns', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many columns should be used to output posts on the single team members page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_team' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 4 ),
							'type'       => 'select',
						),
					)
				)
			);
		}

		// Section 'Testimonials'
		if ( pubzinne_exists_testimonials() ) {
			pubzinne_storage_merge_array(
				'options', '', array_merge(
					array(
						'testimonials' => array(
							'title' => esc_html__( 'Testimonials', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'Select parameters to display "Testimonials"', 'pubzinne' ) ),
							'type'  => 'section',
						),
					),
					pubzinne_trx_addons_get_list_cpt_options( 'testimonials' )
				)
			);
		}
	}
}

// Add 'layout edit' link to the 'description' in the 'header_style' and 'footer_style' parameters
if ( ! function_exists( 'pubzinne_trx_addons_add_link_edit_layout' ) ) {
	//Old way: Handler of the add_action( 'pubzinne_action_load_options', 'pubzinne_trx_addons_add_link_edit_layout');
	//New way: Handler of the add_action( 'wp', 'pubzinne_trx_addons_add_link_edit_layout');
	function pubzinne_trx_addons_add_link_edit_layout() {
		static $added = false;
		if ( $added ) {
			return;
		}
		$added   = true;
		$options = pubzinne_storage_get( 'options' );
		foreach ( $options as $k => $v ) {
			if ( ! isset( $v['std'] ) ) {
				continue;
			}
			$k1 = substr( $k, 0, 12 );
			if ( 'header_style' == $k1 || 'footer_style' == $k1 ) {
				$layout = pubzinne_get_theme_option( $k );
				if ( pubzinne_is_inherit( $layout ) ) {
					$layout = pubzinne_get_theme_option( $k1 );
				}
				if ( ! empty( $layout ) ) {
					$layout = explode( '-', $layout );
					$layout = $layout[ count( $layout ) - 1 ];
					if ( (int)$layout > 0 ) {
						pubzinne_storage_set_array2(
							'options', $k, 'desc', $v['desc']
												. '<br>'
												. sprintf(
													'<a href="%1$s" class="pubzinne_post_editor' . ( intval( $layout ) == 0 ? ' pubzinne_hidden' : '' ) . '" target="_blank">%2$s</a>',
													admin_url( apply_filters( 'pubzinne_filter_post_edit_link', sprintf( 'post.php?post=%d&amp;action=edit', $layout ), $layout ) ),
													__( 'Open selected layout in a new tab to edit', 'pubzinne' )
												)
						);
					}
				}
			}
		}
	}
}


// Setup internal plugin's parameters
if ( ! function_exists( 'pubzinne_trx_addons_init_settings' ) ) {
	add_filter( 'trx_addons_init_settings', 'pubzinne_trx_addons_init_settings' );
	function pubzinne_trx_addons_init_settings( $settings ) {
		$settings['socials_type']              = pubzinne_get_theme_setting( 'socials_type' );
		$settings['icons_type']                = pubzinne_get_theme_setting( 'icons_type' );
		$settings['icons_selector']            = pubzinne_get_theme_setting( 'icons_selector' );
		$settings['gutenberg_safe_mode']       = pubzinne_get_theme_setting( 'gutenberg_safe_mode' );
		$settings['gutenberg_add_context']     = pubzinne_get_theme_setting( 'gutenberg_add_context' );
		$settings['modify_gutenberg_blocks']   = pubzinne_get_theme_setting( 'modify_gutenberg_blocks' );
		$settings['allow_gutenberg_blocks']    = pubzinne_get_theme_setting( 'allow_gutenberg_blocks' );
		$settings['subtitle_above_title']      = pubzinne_get_theme_setting( 'subtitle_above_title' );
		$settings['add_hide_on_xxx']           = pubzinne_get_theme_setting( 'add_hide_on_xxx' );
		$settings['options_tabs_position']     = pubzinne_get_theme_setting( 'options_tabs_position' );		
		$settings['wrap_menu_items_with_span'] = pubzinne_get_theme_setting( 'wrap_menu_items_with_span' );		
		$settings['remove_empty_menu_items']   = pubzinne_get_theme_setting( 'remove_empty_menu_items' );		
		$settings['banners_show_effect']       = pubzinne_get_theme_setting( 'banners_show_effect' );		
		return $settings;
	}
}


// Return theme-specific data by var name
if ( ! function_exists( 'pubzinne_trx_addons_get_theme_data' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_get_theme_data, 'pubzinne_trx_addons_get_theme_data', 9, 2);
	function pubzinne_trx_addons_get_theme_data( $data, $name ) {
		if ( pubzinne_storage_isset( $name ) ) {
			$data = pubzinne_storage_get( $name );
		}
		return $data;
	}
}

// Return theme-specific data to the Dashboard Widget and Theme Panel
// Attention:
// 1) To show the item in the Dashboard Widget you need specify 'link' and 'link_text'
// 2) To show the item in the Theme Dashboard you need specify 'link', 'image', 'icon' (optional), 'title', 'description' and 'button'
if ( ! function_exists( 'pubzinne_trx_addons_get_theme_info' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_get_theme_info', 'pubzinne_trx_addons_get_theme_info', 9);
	function pubzinne_trx_addons_get_theme_info( $theme_info ) {
		$theme_info['theme_activated']  = (int) get_option( 'pubzinne_theme_activated' );
		$theme_info['theme_pro_key']    = pubzinne_storage_get( 'theme_pro_key' );
		$theme_info['theme_plugins']    = pubzinne_storage_get( 'theme_plugins' );
		$theme_info['theme_categories'] = explode( ',', pubzinne_storage_get( 'theme_categories' ) );
		$theme_info['theme_actions']    = array(
			'options'         => array(
				'link'        => admin_url() . 'customize.php',
				'image'       => pubzinne_get_file_url( 'theme-specific/theme-about/images/theme-panel-options.svg' ),
				'title'       => esc_html__( 'Theme Options', 'pubzinne' ),
				'description' => esc_html__( "Customize the appearance of your theme and adjust specific theme settings. Both WordPress Customizer and Theme Options are available.", 'pubzinne' ),
				'button'      => esc_html__( 'Customizer', 'pubzinne' ),
			),
			'demo'    => array(
				'link'        => pubzinne_storage_get( 'theme_demo_url' ),
				'link_text'   => esc_html__( 'Demo', 'pubzinne' ),                 // If not empty - action visible in "Dashboard widget"
			),
			'doc'     => array(
				'link'        => pubzinne_storage_get( 'theme_doc_url' ),
				'link_text'   => esc_html__( 'Docs', 'pubzinne' ),
				'image'       => pubzinne_get_file_url( 'theme-specific/theme-about/images/theme-panel-doc.svg' ),
				'title'       => esc_html__( 'Documentation', 'pubzinne' ),
				'description' => esc_html__( "Having questions? Learn all the ins and outs of the theme in our detailed documentation. That's the go-to place if you need advice.", 'pubzinne' ),
				'button'      => esc_html__( 'Open Documentation', 'pubzinne' ),   // If not empty - action visible in "Theme Panel"
			),
			'support' => array(
				'link'        => pubzinne_storage_get( 'theme_support_url' ),
				'link_text'   => esc_html__( 'Support', 'pubzinne' ),
				'image'       => pubzinne_get_file_url( 'theme-specific/theme-about/images/theme-panel-support.svg' ),
				'title'       => esc_html__( 'Support', 'pubzinne' ),
				'description' => esc_html__( "Are you stuck and need help? Don't worry, you can always submit a support ticket, and our team will help you out.", 'pubzinne' ),
				'button'      => esc_html__( 'Read Policy & Submit Ticket', 'pubzinne' ),
			),
		);
		if ( PUBZINNE_THEME_FREE ) {
			$theme_info['theme_name']          .= ' ' . esc_html__( 'Free', 'pubzinne' );
			$theme_info['theme_free']           = true;
			$theme_info['theme_actions']['pro'] = array(
				'link'        => pubzinne_storage_get( 'theme_download_url' ),
				'link_text'   => esc_html__( 'Go PRO', 'pubzinne' ),
				'image'       => pubzinne_get_file_url( 'theme-specific/theme-about/images/theme-panel-pro.svg' ),
				'title'       => esc_html__( 'Go Pro', 'pubzinne' ),
				'description' => esc_html__( 'Get Pro version to increase power of this theme in many times!', 'pubzinne' ),
				'button'      => esc_html__( 'Get PRO Version', 'pubzinne' ),
			);
		} else if ( pubzinne_is_theme_activated() ) {
			$theme_info['theme_actions']['custom'] = array(
				'link'        => pubzinne_storage_get( 'theme_custom_url' ),
				'link_text'   => esc_html__( 'Pro Custom', 'pubzinne' ),
				'image'       => pubzinne_get_file_url( 'theme-specific/theme-about/images/theme-panel-custom.svg' ),
				'title'       => esc_html__( 'Website Customization', 'pubzinne' ),
				'description' => esc_html__( 'You can order professional website customization. The estimated cost is $350 for 6 pages. The estimated time is 5 business days.', 'pubzinne' ),
				'button'      => esc_html__( 'Get a Quote', 'pubzinne' ),
			);			
		}
		return $theme_info;
	}
}

if ( ! function_exists( 'pubzinne_trx_addons_tgmpa_required_plugins_all' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_trx_addons_tgmpa_required_plugins_all', 999 );
	function pubzinne_trx_addons_tgmpa_required_plugins_all( $list = array() ) {
		$theme_plugins = array();
		if ( is_array( $list ) ) {
			foreach( $list as $item ) {
				$theme_plugins[ $item['slug'] ] = pubzinne_storage_isset( 'required_plugins', $item['slug'] )
													? pubzinne_storage_get_array( 'required_plugins', $item['slug'] )
													: array(
															'title'       => $item['name'],
															'description' => '',
															'required'    => false,
															);
			}
		}
		pubzinne_storage_set( 'theme_plugins', apply_filters( 'pubzinne_filter_theme_plugins', $theme_plugins ) );
		return $list;
	}
}

// Hide sidebar on the news feed pages
if ( ! function_exists( 'pubzinne_trx_addons_sidebar_present' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_sidebar_present', 'pubzinne_trx_addons_sidebar_present' );
	function pubzinne_trx_addons_sidebar_present( $present ) {
		return get_post_type() != 'trx_feed' && $present;
	}
}

// Return text for the "I agree ..." checkbox
if ( ! function_exists( 'pubzinne_trx_addons_privacy_text' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_privacy_text', 'pubzinne_trx_addons_privacy_text' );
	function pubzinne_trx_addons_privacy_text( $text='' ) {
		return pubzinne_get_privacy_text();
	}
}

// Return URI of the theme author's Privacy Policy page
if ( ! function_exists( 'pubzinne_trx_addons_privacy_url' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_privacy_url', 'pubzinne_trx_addons_privacy_url' );
	function pubzinne_trx_addons_privacy_url( $url='' ) {
		$new = pubzinne_storage_get('theme_privacy_url');
		if ( ! empty( $new ) ) {
			$url = $new;
		}
		return $url;
	}
}

// Hide sidebar on the news feed pages
if ( ! function_exists( 'pubzinne_trx_addons_disallow_term_name_modify' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_disallow_term_name_modify', 'pubzinne_trx_addons_disallow_term_name_modify', 10, 3 );
	function pubzinne_trx_addons_disallow_term_name_modify( $disallow, $term_name, $term_obj ) {
		if ( $disallow && wp_doing_ajax() && in_array( trx_addons_get_value_gp( 'action' ), array( 'pubzinne_ajax_get_posts' ) ) ) {
			$disallow = false;
		}
		return $disallow;
	}
}



/* Plugin's support utilities
------------------------------------------------------------------------------- */

// Check if plugin installed and activated
if ( ! function_exists( 'pubzinne_exists_trx_addons' ) ) {
	function pubzinne_exists_trx_addons() {
		return defined( 'TRX_ADDONS_VERSION' );
	}
}

// Return true if cars is supported
if ( ! function_exists( 'pubzinne_exists_cars' ) ) {
	function pubzinne_exists_cars() {
		return defined( 'TRX_ADDONS_CPT_CARS_PT' );
	}
}

// Return true if certificates is supported
if ( ! function_exists( 'pubzinne_exists_certificates' ) ) {
	function pubzinne_exists_certificates() {
		return defined( 'TRX_ADDONS_CPT_CERTIFICATES_PT' );
	}
}

// Return true if courses is supported
if ( ! function_exists( 'pubzinne_exists_courses' ) ) {
	function pubzinne_exists_courses() {
		return defined( 'TRX_ADDONS_CPT_COURSES_PT' );
	}
}

// Return true if dishes is supported
if ( ! function_exists( 'pubzinne_exists_dishes' ) ) {
	function pubzinne_exists_dishes() {
		return defined( 'TRX_ADDONS_CPT_DISHES_PT' );
	}
}

// Return true if layouts is supported
if ( ! function_exists( 'pubzinne_exists_layouts' ) ) {
	function pubzinne_exists_layouts() {
		return defined( 'TRX_ADDONS_CPT_LAYOUTS_PT' );
	}
}

// Return true if portfolio is supported
if ( ! function_exists( 'pubzinne_exists_portfolio' ) ) {
	function pubzinne_exists_portfolio() {
		return defined( 'TRX_ADDONS_CPT_PORTFOLIO_PT' );
	}
}

// Return true if properties is supported
if ( ! function_exists( 'pubzinne_exists_properties' ) ) {
	function pubzinne_exists_properties() {
		return defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' );
	}
}

// Return true if resume is supported
if ( ! function_exists( 'pubzinne_exists_resume' ) ) {
	function pubzinne_exists_resume() {
		return defined( 'TRX_ADDONS_CPT_RESUME_PT' );
	}
}

// Return true if services is supported
if ( ! function_exists( 'pubzinne_exists_services' ) ) {
	function pubzinne_exists_services() {
		return defined( 'TRX_ADDONS_CPT_SERVICES_PT' );
	}
}

// Return true if sport is supported
if ( ! function_exists( 'pubzinne_exists_sport' ) ) {
	function pubzinne_exists_sport() {
		return defined( 'TRX_ADDONS_CPT_COMPETITIONS_PT' );
	}
}

// Return true if team is supported
if ( ! function_exists( 'pubzinne_exists_team' ) ) {
	function pubzinne_exists_team() {
		return defined( 'TRX_ADDONS_CPT_TEAM_PT' );
	}
}

// Return true if testimonials is supported
if ( ! function_exists( 'pubzinne_exists_testimonials' ) ) {
	function pubzinne_exists_testimonials() {
		return defined( 'TRX_ADDONS_CPT_TESTIMONIALS_PT' );
	}
}

// Return true if rating (reviews) is supported
if ( ! function_exists( 'pubzinne_exists_reviews' ) ) {
	function pubzinne_exists_reviews() {
		return function_exists( 'trx_addons_reviews_enable' ) && trx_addons_reviews_enable();
	}
}


// Return true if it's cars page
if ( ! function_exists( 'pubzinne_is_cars_page' ) ) {
	function pubzinne_is_cars_page() {
		return ( function_exists( 'trx_addons_is_cars_page' ) && trx_addons_is_cars_page() )
				|| pubzinne_is_cars_agents_page();
	}
}

// Return true if it's car's agents page
if ( ! function_exists( 'pubzinne_is_cars_agents_page' ) ) {
	function pubzinne_is_cars_agents_page() {
		return function_exists( 'trx_addons_is_cars_agents_page' ) && trx_addons_is_cars_agents_page();
	}
}

// Return true if it's courses page
if ( ! function_exists( 'pubzinne_is_courses_page' ) ) {
	function pubzinne_is_courses_page() {
		return function_exists( 'trx_addons_is_courses_page' ) && trx_addons_is_courses_page();
	}
}

// Return true if it's dishes page
if ( ! function_exists( 'pubzinne_is_dishes_page' ) ) {
	function pubzinne_is_dishes_page() {
		return function_exists( 'trx_addons_is_dishes_page' ) && trx_addons_is_dishes_page();
	}
}

// Return true if it's properties page
if ( ! function_exists( 'pubzinne_is_properties_page' ) ) {
	function pubzinne_is_properties_page() {
		return ( function_exists( 'trx_addons_is_properties_page' ) && trx_addons_is_properties_page() )
				|| pubzinne_is_properties_agents_page();
	}
}

// Return true if it's properties page
if ( ! function_exists( 'pubzinne_is_properties_agents_page' ) ) {
	function pubzinne_is_properties_agents_page() {
		return function_exists( 'trx_addons_is_agents_page' ) && trx_addons_is_agents_page();
	}
}

// Return true if it's portfolio page
if ( ! function_exists( 'pubzinne_is_portfolio_page' ) ) {
	function pubzinne_is_portfolio_page() {
		return function_exists( 'trx_addons_is_portfolio_page' ) && trx_addons_is_portfolio_page();
	}
}

// Return true if it's services page
if ( ! function_exists( 'pubzinne_is_services_page' ) ) {
	function pubzinne_is_services_page() {
		return function_exists( 'trx_addons_is_services_page' ) && trx_addons_is_services_page();
	}
}

// Return true if it's team page
if ( ! function_exists( 'pubzinne_is_team_page' ) ) {
	function pubzinne_is_team_page() {
		return function_exists( 'trx_addons_is_team_page' ) && trx_addons_is_team_page();
	}
}

// Return true if it's sport page
if ( ! function_exists( 'pubzinne_is_sport_page' ) ) {
	function pubzinne_is_sport_page() {
		return function_exists( 'trx_addons_is_sport_page' ) && trx_addons_is_sport_page();
	}
}

// Return true if custom layouts are available
if ( ! function_exists( 'pubzinne_is_layouts_available' ) ) {
	function pubzinne_is_layouts_available() {
		$required_plugins = pubzinne_storage_get( 'required_plugins' );
		return pubzinne_exists_trx_addons()
				&& (
					(
						! empty( $required_plugins['elementor'] )
						&& ( ! isset( $required_plugins['elementor']['install'] )
							|| $required_plugins['elementor']['install']
							)
						&& function_exists( 'pubzinne_exists_elementor' )
						&& pubzinne_exists_elementor()
					)
					||
					(
						! empty( $required_plugins['js_composer'] )
						&& ( ! isset( $required_plugins['js_composer']['install'] )
							|| $required_plugins['js_composer']['install']
							)
						&& function_exists( 'pubzinne_exists_vc' )
						&& pubzinne_exists_vc()
					)
					||
					(
						( empty( $required_plugins['elementor'] )
							|| ( isset( $required_plugins['elementor']['install'] )
								&& false == $required_plugins['elementor']['install']
								)
							)
						&& ( empty( $required_plugins['js_composer'] )
							|| ( isset( $required_plugins['js_composer']['install'] )
								&& false == $required_plugins['js_composer']['install']
								)
							)
						&& function_exists( 'pubzinne_exists_gutenberg' )
						&& pubzinne_exists_gutenberg()
					)
				);
	}
}

// Return true if theme is activated in the Theme Panel
if ( ! function_exists( 'pubzinne_is_theme_activated' ) ) {
	function pubzinne_is_theme_activated() {
		return function_exists( 'trx_addons_is_theme_activated' ) && trx_addons_is_theme_activated();
	}
}

// Return theme activation code
if ( ! function_exists( 'pubzinne_get_theme_activation_code' ) ) {
	function pubzinne_get_theme_activation_code() {
		return function_exists( 'trx_addons_get_theme_activation_code' ) ? trx_addons_get_theme_activation_code() : '';
	}
}

// Detect current blog mode
if ( ! function_exists( 'pubzinne_trx_addons_detect_blog_mode' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_detect_blog_mode', 'pubzinne_trx_addons_detect_blog_mode' );
	function pubzinne_trx_addons_detect_blog_mode( $mode = '' ) {
		if ( pubzinne_is_cars_page() ) {
			$mode = 'cars';
		} elseif ( pubzinne_is_courses_page() ) {
			$mode = 'courses';
		} elseif ( pubzinne_is_dishes_page() ) {
			$mode = 'dishes';
		} elseif ( pubzinne_is_properties_page() ) {
			$mode = 'properties';
		} elseif ( pubzinne_is_portfolio_page() ) {
			$mode = 'portfolio';
		} elseif ( pubzinne_is_services_page() ) {
			$mode = 'services';
		} elseif ( pubzinne_is_sport_page() ) {
			$mode = 'sport';
		} elseif ( pubzinne_is_team_page() ) {
			$mode = 'team';
		}
		return $mode;
	}
}

// Disallow increment views counter on the blog archive
if ( ! function_exists( 'pubzinne_trx_addons_inc_views' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_inc_views', 'pubzinne_trx_addons_inc_views');
	function pubzinne_trx_addons_inc_views( $allow = false ) {
		return $allow && is_page() && pubzinne_storage_isset( 'blog_archive' ) ? false : $allow;
	}
}

// Add team, courses, etc. to the supported posts list
if ( ! function_exists( 'pubzinne_trx_addons_list_post_types' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_list_posts_types', 'pubzinne_trx_addons_list_post_types');
	function pubzinne_trx_addons_list_post_types( $list = array() ) {
		if ( function_exists( 'trx_addons_get_cpt_list' ) ) {
			$cpt_list = trx_addons_get_cpt_list();
			foreach ( $cpt_list as $cpt => $title ) {
				if (
					( defined( 'TRX_ADDONS_CPT_CARS_PT' ) && TRX_ADDONS_CPT_CARS_PT == $cpt )
					|| ( defined( 'TRX_ADDONS_CPT_COURSES_PT' ) && TRX_ADDONS_CPT_COURSES_PT == $cpt )
					|| ( defined( 'TRX_ADDONS_CPT_DISHES_PT' ) && TRX_ADDONS_CPT_DISHES_PT == $cpt )
					|| ( defined( 'TRX_ADDONS_CPT_PORTFOLIO_PT' ) && TRX_ADDONS_CPT_PORTFOLIO_PT == $cpt )
					|| ( defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' ) && TRX_ADDONS_CPT_PROPERTIES_PT == $cpt )
					|| ( defined( 'TRX_ADDONS_CPT_SERVICES_PT' ) && TRX_ADDONS_CPT_SERVICES_PT == $cpt )
					|| ( defined( 'TRX_ADDONS_CPT_COMPETITIONS_PT' ) && TRX_ADDONS_CPT_COMPETITIONS_PT == $cpt )
					|| ( defined( 'TRX_ADDONS_CPT_TEAM_PT' ) && TRX_ADDONS_CPT_TEAM_PT == $cpt )
					) {
					$list[ $cpt ] = $title;
				}
			}
		}
		return $list;
	}
}

// Return taxonomy for current post type
if ( ! function_exists( 'pubzinne_trx_addons_post_type_taxonomy' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_post_type_taxonomy',	'pubzinne_trx_addons_post_type_taxonomy', 10, 2 );
	function pubzinne_trx_addons_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( defined( 'TRX_ADDONS_CPT_CARS_PT' ) && TRX_ADDONS_CPT_CARS_PT == $post_type ) {
			$tax = TRX_ADDONS_CPT_CARS_TAXONOMY_MAKER;
		} elseif ( defined( 'TRX_ADDONS_CPT_COURSES_PT' ) && TRX_ADDONS_CPT_COURSES_PT == $post_type ) {
			$tax = TRX_ADDONS_CPT_COURSES_TAXONOMY;
		} elseif ( defined( 'TRX_ADDONS_CPT_DISHES_PT' ) && TRX_ADDONS_CPT_DISHES_PT == $post_type ) {
			$tax = TRX_ADDONS_CPT_DISHES_TAXONOMY;
		} elseif ( defined( 'TRX_ADDONS_CPT_PORTFOLIO_PT' ) && TRX_ADDONS_CPT_PORTFOLIO_PT == $post_type ) {
			$tax = TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY;
		} elseif ( defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' ) && TRX_ADDONS_CPT_PROPERTIES_PT == $post_type ) {
			$tax = TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE;
		} elseif ( defined( 'TRX_ADDONS_CPT_SERVICES_PT' ) && TRX_ADDONS_CPT_SERVICES_PT == $post_type ) {
			$tax = TRX_ADDONS_CPT_SERVICES_TAXONOMY;
		} elseif ( defined( 'TRX_ADDONS_CPT_COMPETITIONS_PT' ) && TRX_ADDONS_CPT_COMPETITIONS_PT == $post_type ) {
			$tax = TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY;
		} elseif ( defined( 'TRX_ADDONS_CPT_TEAM_PT' ) && TRX_ADDONS_CPT_TEAM_PT == $post_type ) {
			$tax = TRX_ADDONS_CPT_TEAM_TAXONOMY;
		}
		return $tax;
	}
}

// Show categories of the team, courses, etc.
if ( ! function_exists( 'pubzinne_trx_addons_get_post_categories' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_get_post_categories', 		'pubzinne_trx_addons_get_post_categories');
	function pubzinne_trx_addons_get_post_categories( $cats = '' ) {

		if ( defined( 'TRX_ADDONS_CPT_CARS_PT' ) ) {
			if ( get_post_type() == TRX_ADDONS_CPT_CARS_PT ) {
				$cats = pubzinne_get_post_terms( ', ', get_the_ID(), TRX_ADDONS_CPT_CARS_TAXONOMY_TYPE );
			}
		}
		if ( defined( 'TRX_ADDONS_CPT_COURSES_PT' ) ) {
			if ( get_post_type() == TRX_ADDONS_CPT_COURSES_PT ) {
				$cats = pubzinne_get_post_terms( ', ', get_the_ID(), TRX_ADDONS_CPT_COURSES_TAXONOMY );
			}
		}
		if ( defined( 'TRX_ADDONS_CPT_DISHES_PT' ) ) {
			if ( get_post_type() == TRX_ADDONS_CPT_DISHES_PT ) {
				$cats = pubzinne_get_post_terms( ', ', get_the_ID(), TRX_ADDONS_CPT_DISHES_TAXONOMY );
			}
		}
		if ( defined( 'TRX_ADDONS_CPT_PORTFOLIO_PT' ) ) {
			if ( get_post_type() == TRX_ADDONS_CPT_PORTFOLIO_PT ) {
				$cats = pubzinne_get_post_terms( ', ', get_the_ID(), TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY );
			}
		}
		if ( defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' ) ) {
			if ( get_post_type() == TRX_ADDONS_CPT_PROPERTIES_PT ) {
				$cats = pubzinne_get_post_terms( ', ', get_the_ID(), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE );
			}
		}
		if ( defined( 'TRX_ADDONS_CPT_SERVICES_PT' ) ) {
			if ( get_post_type() == TRX_ADDONS_CPT_SERVICES_PT ) {
				$cats = pubzinne_get_post_terms( ', ', get_the_ID(), TRX_ADDONS_CPT_SERVICES_TAXONOMY );
			}
		}
		if ( defined( 'TRX_ADDONS_CPT_COMPETITIONS_PT' ) ) {
			if ( get_post_type() == TRX_ADDONS_CPT_COMPETITIONS_PT ) {
				$cats = pubzinne_get_post_terms( ', ', get_the_ID(), TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY );
			}
		}
		if ( defined( 'TRX_ADDONS_CPT_TEAM_PT' ) ) {
			if ( get_post_type() == TRX_ADDONS_CPT_TEAM_PT ) {
				$cats = pubzinne_get_post_terms( ', ', get_the_ID(), TRX_ADDONS_CPT_TEAM_TAXONOMY );
			}
		}
		return $cats;
	}
}

// Show post's date with the theme-specific format
if ( ! function_exists( 'pubzinne_trx_addons_get_post_date_wrap' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_get_post_date', 'pubzinne_trx_addons_get_post_date_wrap');
	function pubzinne_trx_addons_get_post_date_wrap( $dt = '' ) {
		return apply_filters( 'pubzinne_filter_get_post_date', $dt );
	}
}

// Show date of the courses
if ( ! function_exists( 'pubzinne_trx_addons_get_post_date' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_get_post_date', 'pubzinne_trx_addons_get_post_date');
	function pubzinne_trx_addons_get_post_date( $dt = '' ) {

		if ( defined( 'TRX_ADDONS_CPT_COURSES_PT' ) && get_post_type() == TRX_ADDONS_CPT_COURSES_PT ) {
			$meta = get_post_meta( get_the_ID(), 'trx_addons_options', true );
			$dt   = $meta['date'];
			$dt   = sprintf(
				// Translators: Add formatted date to the output
				$dt < date( 'Y-m-d' ) ? esc_html__( 'Started on %s', 'pubzinne' ) : esc_html__( 'Starting %s', 'pubzinne' ),
				date_i18n( get_option( 'date_format' ), strtotime( $dt ) )
			);

		} elseif ( defined( 'TRX_ADDONS_CPT_COMPETITIONS_PT' ) && in_array( get_post_type(), array( TRX_ADDONS_CPT_COMPETITIONS_PT, TRX_ADDONS_CPT_ROUNDS_PT, TRX_ADDONS_CPT_MATCHES_PT ) ) ) {
			$meta = get_post_meta( get_the_ID(), 'trx_addons_options', true );
			$dt   = $meta['date_start'];
			$dt   = sprintf(
				// Translators: Add formatted date to the output
				$dt < date( 'Y-m-d' ) . ( ! empty( $meta['time_start'] ) ? ' H:i' : '' ) ? esc_html__( 'Started on %s', 'pubzinne' ) : esc_html__( 'Starting %s', 'pubzinne' ),
				date_i18n( get_option( 'date_format' ) . ( ! empty( $meta['time_start'] ) ? ' ' . get_option( 'time_format' ) : '' ), strtotime( $dt . ( ! empty( $meta['time_start'] ) ? ' ' . trim( $meta['time_start'] ) : '' ) ) )
			);

		} elseif ( defined( 'TRX_ADDONS_CPT_COMPETITIONS_PT' ) && get_post_type() == TRX_ADDONS_CPT_PLAYERS_PT ) {
			// Uncomment (remove) next line if you want to show player's birthday in the page title block
			if ( false ) {
				$meta = get_post_meta( get_the_ID(), 'trx_addons_options', true );
				// Translators: Add formatted date to the output
				$dt = ! empty( $meta['birthday'] ) ? sprintf( esc_html__( 'Birthday: %s', 'pubzinne' ), date_i18n( get_option( 'date_format' ), strtotime( $meta['birthday'] ) ) ) : '';
			} else {
				$dt = '';
			}
		}
		return $dt;
	}
}

// Parse layouts in the content
if ( ! function_exists( 'pubzinne_trx_addons_filter_post_content' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_post_content', 'pubzinne_trx_addons_filter_post_content');
	function pubzinne_trx_addons_filter_post_content( $content ) {
		return apply_filters( 'trx_addons_filter_sc_layout_content', $content );
	}
}

// Check if meta box is allowed
if ( ! function_exists( 'pubzinne_trx_addons_allow_override_options' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_allow_override_options', 'pubzinne_trx_addons_allow_override_options', 10, 2);
	function pubzinne_trx_addons_allow_override_options( $allow, $post_type ) {
		return $allow
					|| ( function_exists( 'trx_addons_extended_taxonomy_get_supported_post_types' ) && in_array( $post_type, trx_addons_extended_taxonomy_get_supported_post_types() ) )
					|| ( defined( 'TRX_ADDONS_CPT_CARS_PT' ) && in_array(
						$post_type, array(
							TRX_ADDONS_CPT_CARS_PT,
							TRX_ADDONS_CPT_CARS_AGENTS_PT,
						)
					) )
					|| ( defined( 'TRX_ADDONS_CPT_COURSES_PT' ) && TRX_ADDONS_CPT_COURSES_PT == $post_type )
					|| ( defined( 'TRX_ADDONS_CPT_DISHES_PT' ) && TRX_ADDONS_CPT_DISHES_PT == $post_type )
					|| ( defined( 'TRX_ADDONS_CPT_PORTFOLIO_PT' ) && TRX_ADDONS_CPT_PORTFOLIO_PT == $post_type )
					|| ( defined( 'TRX_ADDONS_CPT_PROPERTIES_PT' ) && in_array(
						$post_type, array(
							TRX_ADDONS_CPT_PROPERTIES_PT,
							TRX_ADDONS_CPT_AGENTS_PT,
						)
					) )
					|| ( defined( 'TRX_ADDONS_CPT_RESUME_PT' ) && TRX_ADDONS_CPT_RESUME_PT == $post_type )
					|| ( defined( 'TRX_ADDONS_CPT_SERVICES_PT' ) && TRX_ADDONS_CPT_SERVICES_PT == $post_type )
					|| ( defined( 'TRX_ADDONS_CPT_COMPETITIONS_PT' ) && in_array(
						$post_type, array(
							TRX_ADDONS_CPT_COMPETITIONS_PT,
							TRX_ADDONS_CPT_ROUNDS_PT,
							TRX_ADDONS_CPT_MATCHES_PT,
						)
					) )
					|| ( defined( 'TRX_ADDONS_CPT_TEAM_PT' ) && TRX_ADDONS_CPT_TEAM_PT == $post_type );
	}
}

// Check if theme icons is allowed
if ( ! function_exists( 'pubzinne_trx_addons_allow_theme_icons' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_allow_theme_icons', 'pubzinne_trx_addons_allow_theme_icons', 10, 2);
	function pubzinne_trx_addons_allow_theme_icons( $allow, $post_type ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;
		return $allow
					|| ( defined( 'TRX_ADDONS_CPT_LAYOUTS_PT' ) && TRX_ADDONS_CPT_LAYOUTS_PT == $post_type )
					|| ( ! empty( $screen->id ) 
						&& ( false !== strpos($screen->id, '_page_trx_addons_options')
							|| in_array( $screen->id, array(
									'profile',
									'widgets',
									)
								)
							)
						);
	}
}

// Disable theme-specific fields in the exported options
if ( ! function_exists( 'pubzinne_trx_addons_export_options' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_export_options', 'pubzinne_trx_addons_export_options');
	function pubzinne_trx_addons_export_options( $options ) {
		// ThemeREX Addons
		if ( ! empty( $options['trx_addons_options'] ) ) {
			$options['trx_addons_options']['debug_mode']             = 0;
			$options['trx_addons_options']['api_google']             = '';
			$options['trx_addons_options']['api_google_analitics']   = '';
			$options['trx_addons_options']['api_google_remarketing'] = '';
			$options['trx_addons_options']['api_yandex']             = '';
			$options['trx_addons_options']['demo_enable']            = 0;
			$options['trx_addons_options']['demo_referer']           = '';
			$options['trx_addons_options']['demo_default_url']       = '';
			$options['trx_addons_options']['demo_logo']              = '';
			$options['trx_addons_options']['demo_post_type']         = '';
			$options['trx_addons_options']['demo_taxonomy']          = '';
			$options['trx_addons_options']['demo_logo']              = '';
			$options['trx_addons_options']['demo_logo']              = '';
			unset( $options['trx_addons_options']['themes_market_referals'] );
		}
		// ThemeREX Donations
		if ( ! empty( $options['trx_donations_options'] ) ) {
			$options['trx_donations_options']['pp_account'] = '';
		}
		// WooCommerce
		if ( ! empty( $options['woocommerce_paypal_settings'] ) ) {
			$options['woocommerce_paypal_settings']['email']          = '';
			$options['woocommerce_paypal_settings']['receiver_email'] = '';
			$options['woocommerce_paypal_settings']['identity_token'] = '';
		}
		return $options;
	}
}

// Set related posts and columns for the plugin's output
if ( ! function_exists( 'pubzinne_trx_addons_args_related' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_args_related', 'pubzinne_trx_addons_args_related');
	function pubzinne_trx_addons_args_related( $args ) {
		if ( ! empty( $args['template_args_name'] )
			&& in_array(
				$args['template_args_name'],
				array(
					'trx_addons_args_sc_cars',
					'trx_addons_args_sc_courses',
					'trx_addons_args_sc_dishes',
					'trx_addons_args_sc_portfolio',
					'trx_addons_args_sc_properties',
					'trx_addons_args_sc_services',
					'trx_addons_args_sc_team',
				)
			) ) {
			$args['posts_per_page']    = (int) pubzinne_get_theme_option( 'show_related_posts' )
												? pubzinne_get_theme_option( 'related_posts' )
												: 0;
			$args['columns']           = pubzinne_get_theme_option( 'related_columns' );
			$args['slider']            = (int) pubzinne_get_theme_option( 'related_slider' );
			$args['slides_space']      = pubzinne_get_theme_option( 'related_slider_space' );
			$args['slider_controls']   = pubzinne_get_theme_option( 'related_slider_controls' );
			$args['slider_pagination'] = pubzinne_get_theme_option( 'related_slider_pagination' );
		}
		return $args;
	}
}

// Redirect filter to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_show_related_posts' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_show_related_posts', 'pubzinne_trx_addons_show_related_posts' );
	function pubzinne_trx_addons_show_related_posts( $show ) {
		return apply_filters( 'trx_addons_filter_show_related_posts', $show );
	}
}

// Return false if related posts must be showed below page
if ( ! function_exists( 'pubzinne_trx_addons_show_related_posts_after_article' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_show_related_posts_after_article', 'pubzinne_trx_addons_show_related_posts_after_article' );
	function pubzinne_trx_addons_show_related_posts_after_article( $show ) {
		return $show && pubzinne_get_theme_option( 'related_position' ) == 'below_content';
	}
}

// Add 'custom' to the headers types list
if ( ! function_exists( 'pubzinne_trx_addons_list_header_footer_types' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_list_header_footer_types', 'pubzinne_trx_addons_list_header_footer_types');
	function pubzinne_trx_addons_list_header_footer_types( $list = array() ) {
		if ( pubzinne_exists_layouts() ) {
			$list['custom'] = esc_html__( 'Custom', 'pubzinne' );
		}
		return $list;
	}
}

// Add layouts to the headers list
if ( ! function_exists( 'pubzinne_trx_addons_list_header_styles' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_list_header_styles', 'pubzinne_trx_addons_list_header_styles');
	function pubzinne_trx_addons_list_header_styles( $list = array() ) {
		if ( pubzinne_exists_layouts() ) {
			$layouts  = pubzinne_get_list_posts(
				false, array(
					'post_type'    => TRX_ADDONS_CPT_LAYOUTS_PT,
					'meta_key'     => 'trx_addons_layout_type',
					'meta_value'   => 'header',
					'orderby'      => 'ID',
					'order'        => 'asc',
					'not_selected' => false,
				)
			);
			$new_list = array();
			foreach ( $layouts as $id => $title ) {
				if ( 'none' != $id ) {
					$new_list[ 'header-custom-' . intval( $id ) ] = $title;
				}
			}
			$list = pubzinne_array_merge( $new_list, $list );
		}
		return $list;
	}
}

// Add layouts to the footers list
if ( ! function_exists( 'pubzinne_trx_addons_list_footer_styles' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_list_footer_styles', 'pubzinne_trx_addons_list_footer_styles');
	function pubzinne_trx_addons_list_footer_styles( $list = array() ) {
		if ( pubzinne_exists_layouts() ) {
			$layouts  = pubzinne_get_list_posts(
				false, array(
					'post_type'    => TRX_ADDONS_CPT_LAYOUTS_PT,
					'meta_key'     => 'trx_addons_layout_type',
					'meta_value'   => 'footer',
					'orderby'      => 'ID',
					'order'        => 'asc',
					'not_selected' => false,
				)
			);
			$new_list = array();
			foreach ( $layouts as $id => $title ) {
				if ( 'none' != $id ) {
					$new_list[ 'footer-custom-' . intval( $id ) ] = $title;
				}
			}
			$list = pubzinne_array_merge( $new_list, $list );
		}
		return $list;
	}
}

// Add layouts to the sidebars list
if ( ! function_exists( 'pubzinne_trx_addons_list_sidebar_styles' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_list_sidebar_styles', 'pubzinne_trx_addons_list_sidebar_styles');
	function pubzinne_trx_addons_list_sidebar_styles( $list = array() ) {
		if ( pubzinne_exists_layouts() ) {
			$layouts  = pubzinne_get_list_posts(
				false, array(
					'post_type'    => TRX_ADDONS_CPT_LAYOUTS_PT,
					'meta_key'     => 'trx_addons_layout_type',
					'meta_value'   => 'sidebar',
					'orderby'      => 'ID',
					'order'        => 'asc',
					'not_selected' => false,
				)
			);
			$new_list = array();
			foreach ( $layouts as $id => $title ) {
				if ( 'none' != $id ) {
					$new_list[ 'sidebar-custom-' . intval( $id ) ] = $title;
				}
			}
			$list = pubzinne_array_merge( $new_list, $list );
		}
		return $list;
	}
}

// Add layouts to the blog styles list
if ( ! function_exists( 'pubzinne_trx_addons_list_blog_styles' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_list_blog_styles', 'pubzinne_trx_addons_list_blog_styles', 10, 2);
	function pubzinne_trx_addons_list_blog_styles( $list, $filter ) {
		static $new_list = array();
		if ( pubzinne_exists_layouts() ) {
			if ( empty( $new_list[ $filter ] ) ) {
				$new_list[ $filter ] = array();
				$custom_blog_use_id = false;	// Use post ID or sanitized post title as part XXX of the layout key 'blog-custom-XXX_columns'
				$layouts  = pubzinne_get_list_posts(
					false, array(
						'post_type'    => TRX_ADDONS_CPT_LAYOUTS_PT,
						'meta_key'     => 'trx_addons_layout_type',
						'meta_value'   => 'blog',
						'orderby'      => 'title',
						'order'        => 'asc',
						'not_selected' => false,
					)
				);
				foreach ( $layouts as $id => $title ) {
					if ( $filter == 'arh' ) {
						$from = 1;
						$to = 1;
						$meta = get_post_meta( $id, 'trx_addons_options', true );
						if ( ! empty( $meta['columns_allowed'] ) ) {
							$parts = explode( ',', $meta['columns_allowed'] );
							if ( count($parts) == 1) {
								$to = min( 6, max( $from, (int) $parts[0] ) );
							} else {
								$from = min( 6, max( 1, (int) $parts[0] ) );
								$to = min( 6, max( $from, (int) $parts[1] ) );
							}
						}
						$new_row = $from < $to;
						for ( $i = $from; $i <= $to; $i++ ) {
							$new_list[ $filter ][ 'blog-custom-'
										. ( $custom_blog_use_id ? (int) $id : sanitize_title( $title ) ) 
										. ( $from < $to ? "_{$i}" : '')
									] = array(
											'title'   => $from < $to
															// Translators: Make blog style title: "Layout name /X columns/"
															? sprintf( _n( '%1$s /%2$d column/', '%1$s /%2$d columns/', $i, 'pubzinne'), $title, $i )
															: $title,
											'icon'    => 'images/theme-options/blog-style/custom.png',
											'new_row' => $new_row,
											);
							$new_row = false;
						}
					} else {
						$new_list[ $filter ][ 'blog-custom-'
										. ( $custom_blog_use_id ? (int) $id : sanitize_title( $title ) ) 
									] = $title;
					}
				}
			}
			if ( ! empty( $new_list[ $filter ] ) && count( $new_list[ $filter ] ) > 0 ) {
				$list = pubzinne_array_merge( $list, $new_list[ $filter ] );
			}
		}
		return $list;
	}
}


// Return id of the custom header or footer for current mode
if ( ! function_exists( 'pubzinne_get_custom_layout_id' ) ) {
	function pubzinne_get_custom_layout_id( $type, $layout_style = '' ) {
		$layout_id = 0;
		if ( empty( $layout_style ) ) {
			$layout_style = pubzinne_get_theme_option( "{$type}_style" );
		}
		if ( strpos( $layout_style, "{$type}-custom-" ) !== false ) {
			$layout_id = str_replace( "{$type}-custom-", '', $layout_style );
			if ( strpos( $layout_id, '_' ) !== false ) {
				$parts = explode( '_', $layout_id );
				$layout_id = $parts[0];
			}
			if ( 0 == (int) $layout_id ) {
				$layout_id = pubzinne_get_post_id(
					array(
						'name'      => $layout_id,
						'post_type' => defined( 'TRX_ADDONS_CPT_LAYOUTS_PT' ) ? TRX_ADDONS_CPT_LAYOUTS_PT : 'cpt_layouts',
					)
				);
			} else {
				$layout_id = apply_filters( 'pubzinne_filter_get_translated_layout', $layout_id );
			}
		}
		return $layout_id;
	}
}

if ( ! function_exists( 'pubzinne_get_custom_header_id' ) ) {
	function pubzinne_get_custom_header_id() {
		static $layout_id = -1;
		if ( -1 == $layout_id && pubzinne_get_theme_option( 'header_type' ) == 'custom' ) {
			$layout_id = pubzinne_get_custom_layout_id( 'header' );
		}
		return $layout_id;
	}
}

if ( ! function_exists( 'pubzinne_get_custom_footer_id' ) ) {
	function pubzinne_get_custom_footer_id() {
		static $layout_id = -1;
		if ( -1 == $layout_id && pubzinne_get_theme_option( 'footer_type' ) == 'custom' ) {
			$layout_id = pubzinne_get_custom_layout_id( 'footer' );
		}
		return $layout_id;
	}
}

if ( ! function_exists( 'pubzinne_get_custom_sidebar_id' ) ) {
	function pubzinne_get_custom_sidebar_id() {
		static $layout_id = -1;
		if ( -1 == $layout_id && pubzinne_get_theme_option( 'sidebar_type' ) == 'custom' ) {
			$layout_id = pubzinne_get_custom_layout_id( 'sidebar' );
		}
		return $layout_id;
	}
}

if ( ! function_exists( 'pubzinne_get_custom_blog_id' ) ) {
	function pubzinne_get_custom_blog_id( $style ) {
		static $layout_id = array();
		if ( empty( $layout_id[ $style ] ) ) {
			$layout_id[ $style ] = pubzinne_get_custom_layout_id( 'blog', $style );
		}
		return $layout_id[ $style ];
	}
}

// Return meta data from custom layout
if ( ! function_exists( 'pubzinne_get_custom_layout_meta' ) ) {
	function pubzinne_get_custom_layout_meta( $id ) {
		static $meta = array();
		if ( empty( $meta[ $id ] ) ) {
			$meta[ $id ] = get_post_meta( $id, 'trx_addons_options', true );
		}
		return $meta[ $id ];
	}
}


// Add theme-specific layouts to the list
if ( ! function_exists( 'pubzinne_trx_addons_default_layouts' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_default_layouts',	'pubzinne_trx_addons_default_layouts');
	function pubzinne_trx_addons_default_layouts( $default_layouts = array() ) {
		if ( pubzinne_storage_isset( 'trx_addons_default_layouts' ) ) {
			$layouts = pubzinne_storage_get( 'trx_addons_default_layouts' );
		} else {
			include_once pubzinne_get_file_dir( 'theme-specific/trx_addons-layouts.php' );
			if ( ! isset( $layouts ) || ! is_array( $layouts ) ) {
				$layouts = array();
			} else if ( function_exists( 'trx_addons_url_replace' ) ) {
				// Replace demo-site urls with current site url
				trx_addons_url_replace( pubzinne_storage_get( 'theme_demo_url' ), get_home_url(), $layouts );
			}
			pubzinne_storage_set( 'trx_addons_default_layouts', $layouts );
		}
		if ( count( $layouts ) > 0 ) {
			$default_layouts = array_merge( $default_layouts, $layouts );
		}
		return $default_layouts;
	}
}


// Add theme-specific components to the plugin's options
if ( ! function_exists( 'pubzinne_trx_addons_default_components' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_load_options',	'pubzinne_trx_addons_default_components');
	function pubzinne_trx_addons_default_components( $options = array() ) {
		if ( empty( $options['components_present'] ) ) {
			if ( pubzinne_storage_isset( 'trx_addons_default_components' ) ) {
				$components = pubzinne_storage_get( 'trx_addons_default_components' );
			} else {
				include_once pubzinne_get_file_dir( 'theme-specific/trx_addons-components.php' );
				if ( ! isset( $components ) || ! is_array( $components ) ) {
					$components = array();
				}
				pubzinne_storage_set( 'trx_addons_default_components', $components );
			}
			$options = is_array( $options ) && count( $components ) > 0
									? array_merge( $options, $components )
									: $components;
		}
		// Turn on API of the theme required plugins
		$plugins = pubzinne_storage_get( 'required_plugins' );
		foreach ( $plugins as $p => $v ) {
			//Disable check, because some components can be added after the plugin's options are saved
			if ( true || isset( $options[ "components_api_{$p}" ] ) ) {
				$options[ "components_api_{$p}" ] = 1;
			}
		}
		return $options;
	}
}


// Add theme-specific options to the post's options
if ( ! function_exists( 'pubzinne_trx_addons_override_options' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_override_options', 'pubzinne_trx_addons_override_options');
	function pubzinne_trx_addons_override_options( $options = array() ) {
		return apply_filters( 'pubzinne_filter_override_options', $options );
	}
}

// Enqueue custom styles
if ( ! function_exists( 'pubzinne_trx_addons_layouts_styles' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'pubzinne_trx_addons_layouts_styles' );
	function pubzinne_trx_addons_layouts_styles() {
		if ( ! pubzinne_exists_trx_addons() ) {
			$pubzinne_url = pubzinne_get_file_url( 'plugins/trx_addons/layouts/layouts.css' );
			if ( '' != $pubzinne_url ) {
				wp_enqueue_style( 'pubzinne-trx-addons-layouts', $pubzinne_url, array(), null );
			}
			$pubzinne_url = pubzinne_get_file_url( 'plugins/trx_addons/layouts/layouts.responsive.css' );
			if ( '' != $pubzinne_url ) {
				wp_enqueue_style( 'pubzinne-trx-addons-layouts-responsive', $pubzinne_url, array(), null );
			}
		}
	}
}

// Enqueue styles for frontend
if ( ! function_exists( 'pubzinne_trx_addons_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'pubzinne_trx_addons_frontend_scripts', 1100 );
	function pubzinne_trx_addons_frontend_scripts() {
		if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
			$pubzinne_url = pubzinne_get_file_url( 'plugins/trx_addons/trx_addons.css' );
			if ( '' != $pubzinne_url ) {
				wp_enqueue_style( 'pubzinne-trx-addons', $pubzinne_url, array(), null );
			}
			$pubzinne_url = pubzinne_get_file_url( 'plugins/trx_addons/trx_addons.js' );
			if ( '' != $pubzinne_url ) {
				wp_enqueue_script( 'pubzinne-trx-addons', $pubzinne_url, array( 'jquery' ), null, true );
			}
		}
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'pubzinne_trx_addons_responsive_styles' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'pubzinne_trx_addons_responsive_styles', 2000 );
	function pubzinne_trx_addons_responsive_styles() {
		if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
			$pubzinne_url = pubzinne_get_file_url( 'plugins/trx_addons/trx_addons-responsive.css' );
			if ( '' != $pubzinne_url ) {
				wp_enqueue_style( 'pubzinne-trx-addons-responsive', $pubzinne_url, array(), null );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'pubzinne_trx_addons_merge_styles' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_merge_styles', 'pubzinne_trx_addons_merge_styles');
	function pubzinne_trx_addons_merge_styles( $list ) {
		$list[] = 'plugins/trx_addons/trx_addons.css';
		return $list;
	}
}

// Merge responsive styles
if ( ! function_exists( 'pubzinne_trx_addons_merge_styles_responsive' ) ) {
	//Handler of the add_filter('pubzinne_filter_merge_styles_responsive', 'pubzinne_trx_addons_merge_styles_responsive');
	function pubzinne_trx_addons_merge_styles_responsive( $list ) {
		$list[] = 'plugins/trx_addons/trx_addons-responsive.css';
		return $list;
	}
}

// Add theme-specific vars to the list of responsive files of ThemeREX Addons
if ( ! function_exists( 'pubzinne_trx_addons_sass_import' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_sass_import','pubzinne_trx_addons_sass_import', 10, 2);
	function pubzinne_trx_addons_sass_import( $output = '', $file = '' ) {
		if ( strpos( $file, 'vars.scss' ) !== false ) {
			$output .= "\n" . pubzinne_fgc( pubzinne_get_file_dir( 'css/_theme-vars.scss' ) )
						. "\n" . pubzinne_fgc( pubzinne_get_file_dir( 'css/_skin-vars.scss' ) )
						. "\n";
		}
		return $output;
	}
}

// Merge custom scripts
if ( ! function_exists( 'pubzinne_trx_addons_merge_scripts' ) ) {
	//Handler of the add_filter('pubzinne_filter_merge_scripts', 'pubzinne_trx_addons_merge_scripts');
	function pubzinne_trx_addons_merge_scripts( $list ) {
		$list[] = 'plugins/trx_addons/trx_addons.js';
		return $list;
	}
}



// Plugin API - theme-specific wrappers for plugin functions
//------------------------------------------------------------------------

// Debug functions wrappers
if ( ! function_exists( 'ddo' ) ) {
	function ddo( $obj, $level = -1 ) {
		return var_dump( $obj ); }
}
if ( ! function_exists( 'dcl' ) ) {
	function dcl( $msg, $level = -1 ) {
		echo '<br><pre>' . esc_html( $msg ) . '</pre><br>'; }
}
if ( ! function_exists( 'dco' ) ) {
	function dco( $obj, $level = -1 ) {
		dcl( ddo( $obj, $level ), $level ); }
}
if ( ! function_exists( 'dcs' ) ) {
	function dcs( $level = -1 ) {
		$s = debug_backtrace();
		array_shift( $s );
		dco( $s, $level ); }
}
if ( ! function_exists( 'dfo' ) ) {
	function dfo( $obj, $level = -1 ) {}
}
if ( ! function_exists( 'dfl' ) ) {
	function dfl( $msg, $level = -1 ) {}
}

// Check if layouts components are showed or set new state
if ( ! function_exists( 'pubzinne_sc_layouts_showed' ) ) {
	function pubzinne_sc_layouts_showed( $name, $val = null ) {
		if ( function_exists( 'trx_addons_sc_layouts_showed' ) ) {
			if ( null !== $val ) {
				trx_addons_sc_layouts_showed( $name, $val );
			} else {
				return trx_addons_sc_layouts_showed( $name );
			}
		} else {
			if ( null !== $val ) {
				pubzinne_storage_set_array( 'sc_layouts_components', $name, $val );
			} else {
				return pubzinne_storage_get_array( 'sc_layouts_components', $name );
			}
		}
	}
}

// Return image size multiplier
if ( ! function_exists( 'pubzinne_get_retina_multiplier' ) ) {
	function pubzinne_get_retina_multiplier( $force_retina = 0 ) {
		$mult = function_exists( 'trx_addons_get_retina_multiplier' ) ? trx_addons_get_retina_multiplier( $force_retina ) : max( 1, $force_retina );
		return max( 1, $mult );
	}
}

// Return slider layout
if ( ! function_exists( 'pubzinne_get_slider_layout' ) ) {
	function pubzinne_get_slider_layout( $args ) {
		return function_exists( 'trx_addons_get_slider_layout' )
					? trx_addons_get_slider_layout( $args )
					: '';
	}
}

// Return slider wrapper first part
if ( ! function_exists( 'pubzinne_get_slider_wrap_start' ) ) {
	function pubzinne_get_slider_wrap_start( $sc, $args ) {
		if ( function_exists( 'trx_addons_sc_show_slider_wrap_start' ) ) {
			trx_addons_sc_show_slider_wrap_start( $sc, $args );
		}
	}
}

// Return slider wrapper last part
if ( ! function_exists( 'pubzinne_get_slider_wrap_end' ) ) {
	function pubzinne_get_slider_wrap_end( $sc, $args ) {
		if ( function_exists( 'trx_addons_sc_show_slider_wrap_end' ) ) {
			trx_addons_sc_show_slider_wrap_end( $sc, $args );
		}
	}
}

// Return video frame layout
if ( ! function_exists( 'pubzinne_get_video_layout' ) ) {
	function pubzinne_get_video_layout( $args ) {
		return function_exists( 'trx_addons_get_video_layout' )
					? trx_addons_get_video_layout( $args )
					: '';
	}
}

// Include theme-specific blog style content
if ( ! function_exists( 'pubzinne_trx_addons_sc_blogger_template' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_sc_blogger_template', 'pubzinne_trx_addons_sc_blogger_template', 10, 2);
	function pubzinne_trx_addons_sc_blogger_template( $result, $args ) {
		if ( ! $result ) {
			$tpl = pubzinne_blog_item_get_template( $args['type'] );
			if ( '' != $tpl ) {
				$tpl = pubzinne_get_file_dir( $tpl . '.php' );
				if ( '' != $tpl ) {
					set_query_var( 'pubzinne_template_args', $args );
					include $tpl;
					set_query_var( 'pubzinne_template_args', false );
					$result = true;
				}
			}
		}
		return $result;
	}
}

// Return theme specific layout of the featured image block
if ( ! function_exists( 'pubzinne_trx_addons_featured_image' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_featured_image', 'pubzinne_trx_addons_featured_image', 10, 2);
	function pubzinne_trx_addons_featured_image( $processed = false, $args = array() ) {
		$args['hover'] = isset( $args['hover'] )
							? ( '' == $args['hover'] 
								? ''
								: ( '!' == $args['hover'][0] && '!inherit' != $args['hover']
									? substr( $args['hover'], 1 )
									: pubzinne_get_theme_option( 'image_hover' )
									)
								)
							: pubzinne_get_theme_option( 'image_hover' );
		pubzinne_show_post_featured( $args );
		return true;
	}
}


// Return list of theme-specific hovers
if ( ! function_exists( 'pubzinne_trx_addons_get_list_sc_image_hover' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_get_list_sc_image_hover', 'pubzinne_trx_addons_get_list_sc_image_hover' );
	function pubzinne_trx_addons_get_list_sc_image_hover( $list ) {
		$list = array_merge(
					array(
						'inherit' => esc_html__('Inherit', 'pubzinne'),
						'none' => esc_html__('No hover', 'pubzinne'),
					),
					pubzinne_get_list_hovers()
		);
		return $list;
	}
}


// Remove some thumb-sizes from the ThemeREX Addons list
if ( ! function_exists( 'pubzinne_trx_addons_add_thumb_sizes' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_add_thumb_sizes', 'pubzinne_trx_addons_add_thumb_sizes');
	function pubzinne_trx_addons_add_thumb_sizes( $list = array() ) {
		if ( is_array( $list ) ) {
			$thumb_sizes = pubzinne_storage_get( 'theme_thumbs' );
			foreach ( $thumb_sizes as $v ) {
				if ( ! empty( $v['subst'] ) && isset( $list[ $v['subst'] ] ) ) {
					unset( $list[ $v['subst'] ] );
				}
			}
		}
		return $list;
	}
}

// and replace removed styles with theme-specific thumb size
if ( ! function_exists( 'pubzinne_trx_addons_get_thumb_size' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_get_thumb_size', 'pubzinne_trx_addons_get_thumb_size');
	function pubzinne_trx_addons_get_thumb_size( $thumb_size = '' ) {
		$thumb_sizes = pubzinne_storage_get( 'theme_thumbs' );
		foreach ( $thumb_sizes as $k => $v ) {
			if ( strpos( $thumb_size, $v['subst'] ) !== false ) {
				$thumb_size = str_replace( $thumb_size, $v['subst'], $k );
				break;
			}
		}
		return $thumb_size;
	}
}

// Return theme specific 'no-image' picture
if ( ! function_exists( 'pubzinne_trx_addons_no_image' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_no_image', 'pubzinne_trx_addons_no_image');
	function pubzinne_trx_addons_no_image( $no_image = '' ) {
		return pubzinne_get_no_image( $no_image );
	}
}

// Return theme-specific icons
if ( ! function_exists( 'pubzinne_trx_addons_get_list_icons_classes' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_get_list_icons_classes', 'pubzinne_trx_addons_get_list_icons_classes', 10, 2 );
	function pubzinne_trx_addons_get_list_icons_classes( $list, $prepend_inherit ) {
		return pubzinne_get_list_icons_classes( $prepend_inherit );
	}
}

// Remove 'icon-' from the name
if ( ! function_exists( 'pubzinne_trx_addons_clear_icon_name' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_clear_icon_name', 'pubzinne_trx_addons_clear_icon_name' );
	function pubzinne_trx_addons_clear_icon_name( $icon ) {
		return substr( $icon, 0, 5 ) == 'icon-' ? substr( $icon, 5 ) : $icon;
	}
}

// Return theme-specific accent color
if ( ! function_exists( 'pubzinne_trx_addons_get_theme_accent_color' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_get_theme_accent_color', 'pubzinne_trx_addons_get_theme_accent_color' );
	function pubzinne_trx_addons_get_theme_accent_color( $color ) {
		return pubzinne_get_scheme_color( 'text_link' );
	}
}

// Return links to the social profiles
if ( ! function_exists( 'pubzinne_get_socials_links' ) ) {
	function pubzinne_get_socials_links( $style = 'icons' ) {
		return function_exists( 'trx_addons_get_socials_links' )
					? trx_addons_get_socials_links( $style )
					: '';
	}
}

// Return links to share post
if ( ! function_exists( 'pubzinne_get_share_links' ) ) {
	function pubzinne_get_share_links( $args = array() ) {
		return function_exists( 'trx_addons_get_share_links' )
					? trx_addons_get_share_links( $args )
					: '';
	}
}

// Display links to share post
if ( ! function_exists( 'pubzinne_show_share_links' ) ) {
	function pubzinne_show_share_links( $args = array() ) {
		if ( function_exists( 'trx_addons_get_share_links' ) ) {
			$args['echo'] = true;
			trx_addons_get_share_links( $args );
		}
	}
}

// Return post icon
if ( ! function_exists( 'pubzinne_get_post_icon' ) ) {
	function pubzinne_get_post_icon( $post_id = 0 ) {
		return function_exists( 'trx_addons_get_post_icon' )
					? trx_addons_get_post_icon( $post_id )
					: '';
	}
}

// Return image from the term
if ( ! function_exists( 'pubzinne_get_term_image' ) ) {
	function pubzinne_get_term_image( $term_id = 0 ) {
		return function_exists( 'trx_addons_get_term_image' )
					? trx_addons_get_term_image( $term_id )
					: '';
	}
}

// Return small image from the term
if ( ! function_exists( 'pubzinne_get_term_image_small' ) ) {
	function pubzinne_get_term_image_small( $term_id = 0 ) {
		return function_exists( 'trx_addons_get_term_image_small' )
					? trx_addons_get_term_image_small( $term_id )
					: '';
	}
}

// Enable/Disable animation effects on mobile devices
if ( ! function_exists( 'pubzinne_trx_addons_disable_animation_on_mobile' ) ) {
	// Handler of the add_filter( 'trx_addons_filter_disable_animation_on_mobile', 'pubzinne_trx_addons_disable_animation_on_mobile' );
	function pubzinne_trx_addons_disable_animation_on_mobile( $disable ) {
		return pubzinne_get_theme_option( 'disable_animation_on_mobile' ) == 1;
	}
}

// Return list with animation effects
if ( ! function_exists( 'pubzinne_get_list_animations_in' ) ) {
	function pubzinne_get_list_animations_in( $prepend_inherit=false ) {
		return function_exists( 'trx_addons_get_list_animations_in' )
					? trx_addons_get_list_animations_in( $prepend_inherit )
					: array();
	}
}

// Return classes list for the specified animation
if ( ! function_exists( 'pubzinne_get_animation_classes' ) ) {
	function pubzinne_get_animation_classes( $animation, $speed = 'normal', $loop = 'none' ) {
		return function_exists( 'trx_addons_get_animation_classes' )
					? trx_addons_get_animation_classes( $animation, $speed, $loop )
					: '';
	}
}

// Return parameter data-animation for the posts archive
if (!function_exists('pubzinne_add_blog_animation')) {
	function pubzinne_add_blog_animation($args=array()) {
		if ( ! isset( $args['count_extra'] ) ) {
			$animation = '';
			if ( !empty($args['animation'])) {
				$animation = $args['animation'];
			} else {
				$animation = pubzinne_get_theme_option( 'blog_animation' );
			}
			if ( ! pubzinne_is_off( $animation ) && empty( $args['slider'] ) ) {
				$animation_classes = pubzinne_get_animation_classes( $animation );
				if ( ! empty( $animation_classes ) ) {
					echo ' data-animation="' . esc_attr( $animation_classes ) . '"';
				}
			}
		}
	}
}

// Return string with the likes counter for the specified comment
if ( ! function_exists( 'pubzinne_get_comment_counters' ) ) {
	function pubzinne_get_comment_counters( $counters = 'likes' ) {
		return function_exists( 'trx_addons_get_comment_counters' )
					? trx_addons_get_comment_counters( $counters )
					: '';
	}
}

// Display likes counter for the specified comment
if ( ! function_exists( 'pubzinne_show_comment_counters' ) ) {
	function pubzinne_show_comment_counters( $counters = 'likes' ) {
		if ( function_exists( 'trx_addons_get_comment_counters' ) ) {
			trx_addons_get_comment_counters( $counters, true );
		}
	}
}

// Add query params to sort posts by views or likes
if ( ! function_exists( 'pubzinne_trx_addons_add_sort_order' ) ) {
	//Handler of the add_filter('pubzinne_filter_add_sort_order', 'pubzinne_trx_addons_add_sort_order', 10, 3);
	function pubzinne_trx_addons_add_sort_order( $q = array(), $orderby = 'date', $order = 'desc' ) {
		return apply_filters( 'trx_addons_filter_add_sort_order', $q, $orderby, $order );
	}
}

// Return theme-specific logo to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_theme_logo' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_theme_logo', 'pubzinne_trx_addons_theme_logo');
	function pubzinne_trx_addons_theme_logo( $logo ) {
		return pubzinne_get_logo_image();
	}
}

// Return true, if theme allow use site name as logo
if ( ! function_exists( 'pubzinne_trx_addons_show_site_name_as_logo' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_show_site_name_as_logo', 'pubzinne_trx_addons_show_site_name_as_logo');
	function pubzinne_trx_addons_show_site_name_as_logo( $allow = true ) {
		return $allow && pubzinne_is_on( pubzinne_get_theme_option( 'logo_text' ) );
	}
}

// Redirect action to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_show_post_meta' ) ) {
	//Handler of the add_action( 'pubzinne_action_show_post_meta', 'pubzinne_trx_addons_show_post_meta', 10, 3 );
	function pubzinne_trx_addons_show_post_meta( $meta, $post_id, $args=array() ) {
		do_action( 'trx_addons_action_show_post_meta', $meta, $post_id, $args );
	}
}


// Return theme-specific post meta to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_post_meta' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_post_meta',	'pubzinne_trx_addons_post_meta', 10, 2);
	function pubzinne_trx_addons_post_meta( $meta, $args = array() ) {
		return pubzinne_show_post_meta( apply_filters( 'pubzinne_filter_post_meta_args', $args, 'trx_addons', 1 ) );
	}
}

// Return theme-specific post meta args
if ( ! function_exists( 'pubzinne_trx_addons_post_meta_args' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_post_meta_args', 'pubzinne_trx_addons_post_meta_args', 10, 3);
	//Handler of the add_filter( 'trx_addons_filter_post_meta_args', 'pubzinne_trx_addons_post_meta_get_args', 10, 3);
	function pubzinne_trx_addons_post_meta_args( $args = array(), $from = '', $columns = 1 ) {
		$theme_specific = ! isset( $args['theme_specific'] ) || $args['theme_specific'];
		if ( ( is_singular() && 'trx_addons' == $from && $theme_specific ) || empty( $args ) ) {
			$args['components'] = join( ',', pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'meta_parts' ) ) );
			$args['seo']        = pubzinne_is_on( pubzinne_get_theme_option( 'seo_snippets' ) );
		}
		return $args;
	}
}

// Add Rating to the meta parts list
if ( ! function_exists( 'pubzinne_trx_addons_list_meta_parts' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_list_meta_parts', 'pubzinne_trx_addons_list_meta_parts' );
	function pubzinne_trx_addons_list_meta_parts( $list ) {
		if ( pubzinne_exists_reviews() ) {
			$list['rating'] = esc_html__( 'Rating', 'pubzinne' );
		}
		return $list;
	}
}

// Return list of the meta parts
if ( ! function_exists( 'pubzinne_trx_addons_get_list_meta_parts' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_get_list_meta_parts', 'pubzinne_trx_addons_get_list_meta_parts' );
	function pubzinne_trx_addons_get_list_meta_parts( $list ) {
		return pubzinne_get_list_meta_parts();
	}
}

// Check if featured image override is allowed
if ( ! function_exists( 'pubzinne_trx_addons_featured_image_override' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_featured_image_override','pubzinne_trx_addons_featured_image_override');
	function pubzinne_trx_addons_featured_image_override( $flag = false ) {
		if ( $flag ) {
			$flag = pubzinne_is_on( pubzinne_get_theme_option( 'header_image_override' ) )
					&& apply_filters( 'pubzinne_filter_allow_override_header_image', true );
		}		
		return $flag;
	}
}

// Return featured image for current mode (post/page/category/blog template ...)
if ( ! function_exists( 'pubzinne_trx_addons_get_current_mode_image' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_get_current_mode_image','pubzinne_trx_addons_get_current_mode_image');
	function pubzinne_trx_addons_get_current_mode_image( $img = '' ) {
		return pubzinne_get_current_mode_image( $img );
	}
}


// Return featured image size for related posts
if ( ! function_exists( 'pubzinne_trx_addons_related_thumb_size' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_related_thumb_size', 'pubzinne_trx_addons_related_thumb_size');
	function pubzinne_trx_addons_related_thumb_size( $size = '' ) {
		if ( defined( 'TRX_ADDONS_CPT_CERTIFICATES_PT' ) && get_post_type() == TRX_ADDONS_CPT_CERTIFICATES_PT ) {
			$size = pubzinne_get_thumb_size( 'masonry-big' );
		}
		return $size;
	}
}

// Redirect action 'get_mobile_menu' to the plugin
// Return stored items as mobile menu
if ( ! function_exists( 'pubzinne_trx_addons_get_mobile_menu' ) ) {
	//Handler of the add_filter("pubzinne_filter_get_mobile_menu", 'pubzinne_trx_addons_get_mobile_menu');
	function pubzinne_trx_addons_get_mobile_menu( $menu ) {
		return apply_filters( 'trx_addons_filter_get_mobile_menu', $menu );
	}
}

// Redirect action 'login' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_login' ) ) {
	//Handler of the add_action( 'pubzinne_action_login', 'pubzinne_trx_addons_action_login');
	function pubzinne_trx_addons_action_login( $args = array() ) {
		do_action( 'trx_addons_action_login', $args );
	}
}

// Redirect action 'cart' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_cart' ) ) {
	//Handler of the add_action( 'pubzinne_action_cart', 'pubzinne_trx_addons_action_cart');
	function pubzinne_trx_addons_action_cart( $args = array() ) {
		do_action( 'trx_addons_action_cart', $args );
	}
}

// Redirect action 'search' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_search' ) ) {
	//Handler of the add_action( 'pubzinne_action_search', 'pubzinne_trx_addons_action_search', 10, 1);
	function pubzinne_trx_addons_action_search( $args ) {
		if ( pubzinne_exists_trx_addons() ) {
			do_action( 'trx_addons_action_search', $args );
		} else {
			set_query_var( 'pubzinne_search_args', $args );
			get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/search-form' ) );
			set_query_var( 'pubzinne_search_args', array() );
		}
	}
}

// Redirect filter 'pubzinne_filter_options_get_list_choises' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_options_get_list_choises' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_options_get_list_choises', 'pubzinne_trx_addons_options_get_list_choises', 999, 2 );
	function pubzinne_trx_addons_options_get_list_choises( $list, $id ) {
		if ( is_array( $list ) && count( $list ) == 0 ) {
			$list = apply_filters( 'trx_addons_filter_options_get_list_choises', $list, $id );
		}
		return $list;
	}
}

// Redirect action 'pubzinne_action_save_options' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_save_options' ) ) {
	//Handler of the add_action( 'pubzinne_action_save_options', 'pubzinne_trx_addons_action_save_options', 1 );
	function pubzinne_trx_addons_action_save_options() {
		do_action( 'trx_addons_action_save_options_theme' );
	}
}

// Redirect action 'pubzinne_action_before_body' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_before_body' ) ) {
	//Handler of the add_action( 'pubzinne_action_before_body', 'pubzinne_trx_addons_action_before_body', 1);
	function pubzinne_trx_addons_action_before_body() {
		do_action( 'trx_addons_action_before_body' );
	}
}

// Redirect action 'pubzinne_action_page_content_wrap' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_page_content_wrap' ) ) {
	//Handler of the add_action( 'pubzinne_action_page_content_wrap', 'pubzinne_trx_addons_action_page_content_wrap', 10, 1 );
	function pubzinne_trx_addons_action_page_content_wrap( $ajax = false ) {
		do_action( 'trx_addons_action_page_content_wrap', $ajax );
	}
}

// Redirect action 'pubzinne_action_before_header' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_before_header' ) ) {
	//Handler of the add_action( 'pubzinne_action_before_header', 'pubzinne_trx_addons_action_before_header' );
	function pubzinne_trx_addons_action_before_header() {
		do_action( 'trx_addons_action_before_header' );
	}
}

// Redirect action 'pubzinne_action_after_header' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_after_header' ) ) {
	//Handler of the add_action( 'pubzinne_action_after_header', 'pubzinne_trx_addons_action_after_header' );
	function pubzinne_trx_addons_action_after_header() {
		do_action( 'trx_addons_action_after_header' );
	}
}

// Redirect action 'pubzinne_action_before_footer' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_before_footer' ) ) {
	//Handler of the add_action( 'pubzinne_action_before_footer', 'pubzinne_trx_addons_action_before_footer' );
	function pubzinne_trx_addons_action_before_footer() {
		do_action( 'trx_addons_action_before_footer' );
	}
}

// Redirect action 'pubzinne_action_after_footer' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_after_footer' ) ) {
	//Handler of the add_action( 'pubzinne_action_after_footer', 'pubzinne_trx_addons_action_after_footer' );
	function pubzinne_trx_addons_action_after_footer() {
		do_action( 'trx_addons_action_after_footer' );
	}
}

// Redirect action 'pubzinne_action_before_sidebar_wrap' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_before_sidebar_wrap' ) ) {
	//Handler of the add_action( 'pubzinne_action_before_sidebar_wrap', 'pubzinne_trx_addons_action_before_sidebar_wrap', 10, 1 );
	function pubzinne_trx_addons_action_before_sidebar_wrap( $sb = '' ) {
		do_action( 'trx_addons_action_before_sidebar_wrap', $sb );
	}
}

// Redirect action 'pubzinne_action_after_sidebar' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_after_sidebar_wrap' ) ) {
	//Handler of the add_action( 'pubzinne_action_after_sidebar_wrap', 'pubzinne_trx_addons_action_after_sidebar_wrap', 10, 1 );
	function pubzinne_trx_addons_action_after_sidebar_wrap( $sb = '' ) {
		do_action( 'trx_addons_action_after_sidebar_wrap', $sb );
	}
}

// Redirect action 'pubzinne_action_before_sidebar' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_before_sidebar' ) ) {
	//Handler of the add_action( 'pubzinne_action_before_sidebar', 'pubzinne_trx_addons_action_before_sidebar', 10, 1 );
	function pubzinne_trx_addons_action_before_sidebar( $sb = '' ) {
		do_action( 'trx_addons_action_before_sidebar', $sb );
	}
}

// Redirect action 'pubzinne_action_after_sidebar' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_after_sidebar' ) ) {
	//Handler of the add_action( 'pubzinne_action_after_sidebar', 'pubzinne_trx_addons_action_after_sidebar', 10, 1 );
	function pubzinne_trx_addons_action_after_sidebar( $sb = '' ) {
		do_action( 'trx_addons_action_after_sidebar', $sb );
	}
}

// Redirect action 'pubzinne_action_between_posts' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_between_posts' ) ) {
	//Handler of the add_action( 'pubzinne_action_between_posts', 'pubzinne_trx_addons_action_between_posts' );
	function pubzinne_trx_addons_action_between_posts() {
		do_action( 'trx_addons_action_between_posts' );
	}
}

// Redirect action 'pubzinne_action_before_post_header' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_before_post_header' ) ) {
	//Handler of the add_action( 'pubzinne_action_before_post_header', 'pubzinne_trx_addons_action_before_post_header' );
	function pubzinne_trx_addons_action_before_post_header() {
		do_action( 'trx_addons_action_before_post_header' );
	}
}

// Redirect action 'pubzinne_action_after_post_header' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_after_post_header' ) ) {
	//Handler of the add_action( 'pubzinne_action_after_post_header', 'pubzinne_trx_addons_action_after_post_header' );
	function pubzinne_trx_addons_action_after_post_header() {
		do_action( 'trx_addons_action_after_post_header' );
	}
}

// Redirect action 'pubzinne_action_before_post_content' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_before_post_content' ) ) {
	//Handler of the add_action( 'pubzinne_action_before_post_content', 'pubzinne_trx_addons_action_before_post_content' );
	function pubzinne_trx_addons_action_before_post_content() {
		do_action( 'trx_addons_action_before_post_content' );
	}
}

// Redirect action 'pubzinne_action_after_post_content' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_after_post_content' ) ) {
	//Handler of the add_action( 'pubzinne_action_after_post_content', 'pubzinne_trx_addons_action_after_post_content' );
	function pubzinne_trx_addons_action_after_post_content() {
		do_action( 'trx_addons_action_after_post_content' );
	}
}

// Redirect action 'breadcrumbs' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_breadcrumbs' ) ) {
	//Handler of the add_action( 'pubzinne_action_breadcrumbs',	'pubzinne_trx_addons_action_breadcrumbs' );
	function pubzinne_trx_addons_action_breadcrumbs() {
		do_action( 'trx_addons_action_breadcrumbs' );
	}
}

// Redirect action 'show_layout' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_action_show_layout' ) ) {
	//Handler of the add_action( 'pubzinne_action_show_layout', 'pubzinne_trx_addons_action_show_layout', 10, 2 );
	function pubzinne_trx_addons_action_show_layout( $layout_id = '', $post_id = 0 ) {
		do_action( 'trx_addons_action_show_layout', $layout_id, $post_id );
	}
}

// Redirect action 'before_full_post_content' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_before_full_post_content' ) ) {
	//Handler of the add_action( 'pubzinne_action_before_full_post_content', 'pubzinne_trx_addons_before_full_post_content' );
	function pubzinne_trx_addons_before_full_post_content() {
		do_action( 'trx_addons_action_before_full_post_content' );
	}
}

// Redirect action 'after_full_post_content' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_after_full_post_content' ) ) {
	//Handler of the add_action( 'pubzinne_action_after_full_post_content', 'pubzinne_trx_addons_after_full_post_content' );
	function pubzinne_trx_addons_after_full_post_content() {
		do_action( 'trx_addons_action_after_full_post_content' );
	}
}

// Redirect filter 'get_translated_layout' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_filter_get_translated_layout' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_get_translated_layout', 'pubzinne_trx_addons_filter_get_translated_layout', 10, 1);
	function pubzinne_trx_addons_filter_get_translated_layout( $layout_id = '' ) {
		return apply_filters( 'trx_addons_filter_get_translated_layout', $layout_id );
	}
}

// Show user meta (socials)
if ( ! function_exists( 'pubzinne_trx_addons_action_user_meta' ) ) {
	//Handler of the add_action( 'pubzinne_action_user_meta', 'pubzinne_trx_addons_action_user_meta', 10, 1 );
	function pubzinne_trx_addons_action_user_meta( $from='' ) {
		do_action( 'trx_addons_action_user_meta' );
	}
}

// Redirect filter 'get_blog_title' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_get_blog_title' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_get_blog_title', 'pubzinne_trx_addons_get_blog_title');
	function pubzinne_trx_addons_get_blog_title( $title = '' ) {
		return apply_filters( 'trx_addons_filter_get_blog_title', $title );
	}
}

// Redirect filter 'term_name' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_term_name' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_term_name', 'pubzinne_trx_addons_term_name', 10, 2 );
	function pubzinne_trx_addons_term_name( $term_name, $taxonomy ) {
		return apply_filters( 'trx_addons_filter_term_name', $term_name, $taxonomy );
	}
}

// Redirect filter 'get_post_iframe' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_get_post_iframe' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_get_post_iframe', 'pubzinne_trx_addons_get_post_iframe', 10, 1);
	function pubzinne_trx_addons_get_post_iframe( $html = '' ) {
		return apply_filters( 'trx_addons_filter_get_post_iframe', $html );
	}
}

// Return true, if theme need a SEO snippets
if ( ! function_exists( 'pubzinne_trx_addons_seo_snippets' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_seo_snippets', 'pubzinne_trx_addons_seo_snippets');
	function pubzinne_trx_addons_seo_snippets( $enable = false ) {
		return pubzinne_is_on( pubzinne_get_theme_option( 'seo_snippets' ) );
	}
}

// Show user meta (socials)
if ( ! function_exists( 'pubzinne_trx_addons_article_start' ) ) {
	//Handler of the add_action( 'trx_addons_action_article_start', 'pubzinne_trx_addons_article_start', 10, 1);
	function pubzinne_trx_addons_article_start( $page = '' ) {
		if ( pubzinne_is_on( pubzinne_get_theme_option( 'seo_snippets' ) ) ) {
			get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/seo' ) );
		}
	}
}

// Redirect filter 'prepare_css' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_prepare_css' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_prepare_css',	'pubzinne_trx_addons_prepare_css', 10, 2);
	function pubzinne_trx_addons_prepare_css( $css = '', $remove_spaces = true ) {
		return apply_filters( 'trx_addons_filter_prepare_css', $css, $remove_spaces );
	}
}

// Redirect filter 'prepare_js' to the plugin
if ( ! function_exists( 'pubzinne_trx_addons_prepare_js' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_prepare_js',	'pubzinne_trx_addons_prepare_js', 10, 2);
	function pubzinne_trx_addons_prepare_js( $js = '', $remove_spaces = true ) {
		return apply_filters( 'trx_addons_filter_prepare_js', $js, $remove_spaces );
	}
}

// Add plugin's specific variables to the scripts
if ( ! function_exists( 'pubzinne_trx_addons_localize_script' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_localize_script',	'pubzinne_trx_addons_localize_script');
	function pubzinne_trx_addons_localize_script( $arr ) {
		$arr['trx_addons_exists'] = pubzinne_exists_trx_addons();
		return $arr;
	}
}

// Redirect filter 'trx_addons_filter_get_theme_file_dir' to the theme
if ( ! function_exists( 'pubzinne_trx_addons_get_theme_file_dir' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_get_theme_file_dir', 'pubzinne_trx_addons_get_theme_file_dir', 10, 3);
	function pubzinne_trx_addons_get_theme_file_dir( $dir, $file, $return_url ) {
		return apply_filters( 'pubzinne_filter_get_theme_file_dir', $dir, $file, $return_url );
	}
}

// Redirect filter 'trx_addons_filter_get_theme_folder_dir' to the theme
if ( ! function_exists( 'pubzinne_trx_addons_get_theme_folder_dir' ) ) {
	//Handler of the add_filter( 'trx_addons_filter_get_theme_folder_dir', 'pubzinne_trx_addons_get_theme_folder_dir', 10, 3);
	function pubzinne_trx_addons_get_theme_folder_dir( $dir, $folder, $return_url ) {
		return apply_filters( 'pubzinne_filter_get_theme_file_dir', $dir, $folder, $return_url );
	}
}

// Add plugin-specific colors and fonts to the custom CSS
if ( pubzinne_exists_trx_addons() ) {
	require_once pubzinne_get_file_dir( 'plugins/trx_addons/trx_addons-style.php' );
}
