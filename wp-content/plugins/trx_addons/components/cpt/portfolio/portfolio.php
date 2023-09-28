<?php
/**
 * ThemeREX Addons Custom post type: Portfolio
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_PORTFOLIO_PT') ) define('TRX_ADDONS_CPT_PORTFOLIO_PT', trx_addons_cpt_param('portfolio', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY') ) define('TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY', trx_addons_cpt_param('portfolio', 'taxonomy'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_portfolio_init')) {
	add_action( 'init', 'trx_addons_cpt_portfolio_init' );
	function trx_addons_cpt_portfolio_init() {
		
		// Add Portfolio parameters to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_PORTFOLIO_PT, array(
			"general_section" => array(
				"title" => esc_html__('General', 'trx_addons'),
				"desc" => wp_kses_data( __('Basic information about this project', 'trx_addons') ),
				"type" => "section"
			),
			"subtitle" => array(
				"title" => esc_html__("Project's subtitle",  'trx_addons'),
				"desc" => wp_kses_data( __("Portfolio item subtitle, slogan, position or any other text", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"link" => array(
				"title" => esc_html__("Project's link",  'trx_addons'),
				"desc" => wp_kses_data( __("Alternative link to the project's site. If empty - use this post's permalink", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			'project_author' => array(
				"title" => __("Project's author",  'trx_addons'),
				"desc" => __("Select a team member", 'trx_addons'),
				"std" => 'none',
				"options" => array(),
				"type" => "select2"
			),

			"details_section" => array(
				"title" => esc_html__('Project details', 'trx_addons'),
				"desc" => wp_kses_data( __('Additional details for this project', 'trx_addons') ),
				"type" => "section"
			),
			"details_position" => array(
				"title" => esc_html__("Detail block position", 'trx_addons'),
				"desc" => wp_kses_data( __("Select the position of the block with project details", 'trx_addons') ),
				"std" => 'top',
				"options" => array(
									'top' => __('Top', 'trx_addons'),
									'bottom' => __('Bottom', 'trx_addons'),
									'left' => __('Left', 'trx_addons'),
									'right' => __('Right', 'trx_addons')
									),
				"type" => "select"
			),
			"details" => array(
				"title" => esc_html__("Project details", 'trx_addons'),
				"desc" => wp_kses_data( __("Information about this project", 'trx_addons') ),
				"clone" => true,
				"std" => array(
								array(
										'title' => __('Client', 'trx_addons'),
										'value' => __('Client name', 'trx_addons'),
										'link'  => '',
										'icon'  => ''
										),
								array(
										'title' => __('Year', 'trx_addons'),
										'value' => date('Y'),
										'link'  => '',
										'icon'  => ''
										),
								array(
										'title' => __('Author', 'trx_addons'),
										'value' => __('Author name', 'trx_addons'),
										'link'  => '',
										'icon'  => ''
										),
								),
				"type" => "group",
				"fields" => array(
					"title" => array(
						"title" => esc_html__("Title", 'trx_addons'),
						"desc" => wp_kses_data( __('Feature title', 'trx_addons') ),
						"class" => "trx_addons_column-1_4",
						"std" => "",
						"type" => "text"
					),
					"value" => array(
						"title" => esc_html__("Value", 'trx_addons'),
						"desc" => wp_kses_data( __('Feature value', 'trx_addons') ),
						"class" => "trx_addons_column-1_4",
						"std" => "",
						"type" => "text"
					),
					"link" => array(
						"title" => esc_html__("Link", 'trx_addons'),
						"desc" => wp_kses_data( __('Feature link', 'trx_addons') ),
						"class" => "trx_addons_column-1_4",
						"std" => "",
						"type" => "text"
					),
					"icon" => array(
						"title" => esc_html__("Icon", 'trx_addons'),
						"desc" => wp_kses_data( __('Feature icon', 'trx_addons') ),
						"class" => "trx_addons_column-1_4",
						"std" => "",
						"options" => array(),
						"style" => trx_addons_get_setting('icons_type'),
						"type" => "icons"
					)
				)
			),

			"gallery_section" => array(
				"title" => esc_html__('Gallery', 'trx_addons'),
				"desc" => wp_kses_data( __('Images gallery for this project', 'trx_addons') ),
				"type" => "section"
			),
			"gallery" => array(
				"title" => esc_html__("Images gallery", 'trx_addons'),
				"desc" => wp_kses_data( __("Select images to create a gallery on the single page of this project", 'trx_addons') ),
				"std" => "",
				"multiple" => true,
				"type" => "image"
			),
			"gallery_position" => array(
				"title" => esc_html__("Gallery position", 'trx_addons'),
				"desc" => wp_kses_data( __("Show gallery above or below the project's content or instead of %%GALLERY%% if 'Inside content' is selected", 'trx_addons') ),
				"dependency" => array(
					"gallery" => array("not_empty")
				),
				"std" => 'bottom',
				"options" => array(
									'none' => __('Hide gallery', 'trx_addons'),
									'top' => __('Above content', 'trx_addons'),
									'bottom' => __('Below content', 'trx_addons'),
									'inside' => __('Inside content', 'trx_addons')
									),
				"type" => "select"
			),
			"gallery_layout" => array(
				"title" => esc_html__("Gallery layout", 'trx_addons'),
				"desc" => wp_kses_data( __("Select a layout to display images on the project page", 'trx_addons') ),
				"dependency" => array(
					"gallery" => array("not_empty"),
					"gallery_position" => array("^none"),
				),
				"std" => 'slider',
				"options" => array(
									'slider' => __('Slider', 'trx_addons'),
									'grid_2' => __('Grid /2 columns/', 'trx_addons'),
									'grid_3' => __('Grid /3 columns/', 'trx_addons'),
									'grid_4' => __('Grid /4 columns/', 'trx_addons'),
									'masonry_2' => __('Masonry /2 columns/', 'trx_addons'),
									'masonry_3' => __('Masonry /3 columns/', 'trx_addons'),
									'masonry_4' => __('Masonry /4 columns/', 'trx_addons'),
									'stream' => __('Stream', 'trx_addons'),
									),
				"type" => "select"
			),
			"gallery_description" => array(
				"title" => esc_html__("Description", 'trx_addons'),
				"desc" => wp_kses_data( __('Provide a short description for the gallery above', 'trx_addons') ),
				"dependency" => array(
					"gallery" => array("not_empty")
				),
				"std" => "",
				"type" => "textarea"
			),

			"video_section" => array(
				"title" => esc_html__('Video', 'trx_addons'),
				"desc" => wp_kses_data( __('Featured video for this project', 'trx_addons') ),
				"type" => "section"
			),
			"video" => array(
				"title" => esc_html__("Video", 'trx_addons'),
				"desc" => wp_kses_data( __('Specify an URL with a video', 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"video_autoplay_archive" => array(
				"title" => esc_html__("Allow autoplay on the archive page", 'trx_addons'),
				"desc" => wp_kses_data( __('Autoplay video when archive page or shortcode is loaded', 'trx_addons') ),
				"std" => 0,
				"type" => "checkbox"
			),
			"video_autoplay" => array(
				"title" => esc_html__("Allow autoplay on the single post", 'trx_addons'),
				"desc" => wp_kses_data( __('Autoplay video when single post is loaded', 'trx_addons') ),
				"std" => 0,
				"type" => "checkbox"
			),
			"video_position" => array(
				"title" => esc_html__("Video position", 'trx_addons'),
				"desc" => wp_kses_data( __("Show video above or below the project's content or instead of %%VIDEO%% if 'Inside content' is selected", 'trx_addons') ),
				"dependency" => array(
					"video" => array("not_empty")
				),
				"std" => 'bottom',
				"options" => array(
									'none' => __('Hide video', 'trx_addons'),
									'header' => __('In the page header', 'trx_addons'),
									'top' => __('Above content', 'trx_addons'),
									'bottom' => __('Below content', 'trx_addons'),
									'inside' => __('Inside content', 'trx_addons')
									),
				"type" => "select"
			),
			"video_description" => array(
				"title" => esc_html__("Description", 'trx_addons'),
				"desc" => wp_kses_data( __('Provide a short description for the video above', 'trx_addons') ),
				"dependency" => array(
					"video" => array("not_empty")
				),
				"std" => "",
				"type" => "textarea"
			),
		));
		
		// Register taxonomies and post types
		// Taxonomy first, because it can using the combined rewrite rule (contains the slug of the post type)
		register_taxonomy(
			TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY,
			TRX_ADDONS_CPT_PORTFOLIO_PT,
			apply_filters('trx_addons_filter_register_taxonomy',
				array(
					'post_type' 		=> TRX_ADDONS_CPT_PORTFOLIO_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Portfolio Group', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Group', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Groups', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Groups', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Group', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Group', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Group', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Group', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Group Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Portfolio Groups', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array(
												'slug'         => trx_addons_cpt_param('portfolio', 'taxonomy_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_PORTFOLIO_PT,
				TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY
			)
		);

		register_post_type(
			TRX_ADDONS_CPT_PORTFOLIO_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Portfolio', 'trx_addons' ),
					'description'         => esc_html__( 'Portfolio Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Portfolio', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Portfolio', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Portfolio', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Portfolio items', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Portfolio item', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Portfolio item', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Portfolio item', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Portfolio item', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Portfolio items', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'taxonomies'          => array(TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY),
					'supports'            => trx_addons_cpt_param('portfolio', 'supports'),
					'public'              => true,
					'hierarchical'        => false,
					'has_archive'         => true,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_in_menu'        => true,
					// From WordPress 5.3 'menu_position' must be only integer or null (default)!
					// 'menu_position'       => '53.2',
					'menu_icon'			  => 'dashicons-images-alt',
					'capability_type'     => 'post',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('portfolio', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
											)
				),
				TRX_ADDONS_CPT_PORTFOLIO_PT
			)
		);
	}
}


// Allow Gutenberg as main editor for this post type
if ( ! function_exists( 'trx_addons_cpt_portfolio_add_pt_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_pt_to_gutenberg', 'trx_addons_cpt_portfolio_add_pt_to_gutenberg', 10, 2 );
	function trx_addons_cpt_portfolio_add_pt_to_gutenberg( $allow, $post_type ) {
		return $allow || $post_type == TRX_ADDONS_CPT_PORTFOLIO_PT;
	}
}

// Allow Gutenberg as main editor for taxonomies
if ( ! function_exists( 'trx_addons_cpt_portfolio_add_taxonomy_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_taxonomy_to_gutenberg', 'trx_addons_cpt_portfolio_add_taxonomy_to_gutenberg', 10, 2 );
	function trx_addons_cpt_portfolio_add_taxonomy_to_gutenberg( $allow, $tax ) {
		return $allow || in_array( $tax, array( TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY ) );
	}
}

/* ------------------- Old way - moved to the cpt.php now ---------------------
// Add 'Portfolio' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_portfolio_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_cpt_portfolio_options');
	function trx_addons_cpt_portfolio_options($options) {
		trx_addons_array_insert_after($options, 'cpt_section', trx_addons_cpt_portfolio_get_list_options());
		return $options;
	}
}

// Return parameters list for plugin's options
if (!function_exists('trx_addons_cpt_portfolio_get_list_options')) {
	function trx_addons_cpt_portfolio_get_list_options($add_parameters=array()) {
		return apply_filters('trx_addons_cpt_list_options', array(
			'portfolio_info' => array(
				"title" => esc_html__('Portfolio', 'trx_addons'),
				"desc" => wp_kses_data( __('Settings of the portfolio archive', 'trx_addons') ),
				"type" => "info"
			),
			'portfolio_style' => array(
				"title" => esc_html__('Style', 'trx_addons'),
				"desc" => wp_kses_data( __('Style of the portfolio archive', 'trx_addons') ),
				"std" => 'default_2',
				"options" => apply_filters('trx_addons_filter_cpt_archive_styles',
											trx_addons_components_get_allowed_layouts('cpt', 'portfolio', 'arh'),
											TRX_ADDONS_CPT_PORTFOLIO_PT),
				"type" => "select"
			)
		), 'portfolio');
	}
}
------------------- /Old way --------------------- */


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_portfolio_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_cpt_portfolio_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_cpt_portfolio_load_scripts_front', 10, 1 );
	function trx_addons_cpt_portfolio_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'cpt_portfolio', $force, array(
			'lib' => array(
				'callback' => function() {
					if ( trx_addons_is_on( trx_addons_get_option( 'portfolio_use_gallery' ) ) ) {
						if ( is_post_type_archive( TRX_ADDONS_CPT_PORTFOLIO_PT ) || is_tax( TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY ) ) {
							trx_addons_enqueue_gallery();
						}
					}
					trx_addons_enqueue_masonry();
				}
			),
			'css'  => array(
				'trx_addons-cpt_portfolio' => array( 'src' => TRX_ADDONS_PLUGIN_CPT . 'portfolio/portfolio.css' ),
			),
			'js' => array(
				'trx_addons-cpt_portfolio' => array( 'src' => TRX_ADDONS_PLUGIN_CPT . 'portfolio/portfolio.js', 'deps' => 'jquery' ),
			),
			'need' => trx_addons_is_portfolio_page(),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_portfolio' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/portfolio' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_portfolio"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_portfolio' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_cpt_portfolio_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_cpt_portfolio_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_cpt_portfolio', 'trx_addons_cpt_portfolio_load_scripts_front_responsive', 10, 1 );
	function trx_addons_cpt_portfolio_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'cpt_portfolio', $force, array(
			'css'  => array(
				'trx_addons-cpt_portfolio-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_CPT . 'portfolio/portfolio.responsive.css',
					'media' => 'lg'
				),
			),
		) );
	}
}
	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cpt_portfolio_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_cpt_portfolio_merge_styles');
	function trx_addons_cpt_portfolio_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'portfolio/portfolio.css' ] = false;
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_cpt_portfolio_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_cpt_portfolio_merge_styles_responsive');
	function trx_addons_cpt_portfolio_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'portfolio/portfolio.responsive.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_cpt_portfolio_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_cpt_portfolio_merge_scripts');
	function trx_addons_cpt_portfolio_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'portfolio/portfolio.js' ] = false;
		//$list[ TRX_ADDONS_PLUGIN_CPT . 'portfolio/gallery/gallery.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_cpt_portfolio_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_cpt_portfolio_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_cpt_portfolio_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_cpt_portfolio_check_in_html_output', 10, 1 );
	function trx_addons_cpt_portfolio_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_portfolio',
				'class=[\'"][^\'"]*type\\-' . TRX_ADDONS_CPT_PORTFOLIO_PT,
				'class=[\'"][^\'"]*' . TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY . '\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'cpt_portfolio', $content, $args ) ) {
			trx_addons_cpt_portfolio_load_scripts_front( true );
		}
		return $content;
	}
}

// Add gallery specific scripts
if ( !function_exists( 'trx_addons_enqueue_gallery' ) ) {
	function trx_addons_enqueue_gallery() {
		static $loaded = false;
		if ( ! $loaded ) {
			wp_enqueue_script( 'modernizr', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'portfolio/gallery/modernizr.min.js' ), array(), null, false );
			wp_enqueue_script( 'classie', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'portfolio/gallery/classie.min.js' ), array(), null, true );
			wp_enqueue_script( 'trx_addons-gallery', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'portfolio/gallery/gallery.js' ), array(), null, true );
			$loaded = true;
		}
	}
}

// Add portfolio specific vars to the JS storage
if ( !function_exists( 'trx_addons_cpt_portfolio_localize_script' ) ) {
	add_filter("trx_addons_filter_localize_script", 'trx_addons_cpt_portfolio_localize_script');
	function trx_addons_cpt_portfolio_localize_script($vars) {
		$vars['portfolio_use_gallery'] = trx_addons_is_on( trx_addons_get_option( 'portfolio_use_gallery' ) );
		return $vars;
	}
}


// Return true if it's portfolio page
if ( !function_exists( 'trx_addons_is_portfolio_page' ) ) {
	function trx_addons_is_portfolio_page() {
		return defined('TRX_ADDONS_CPT_PORTFOLIO_PT') 
					&& ! is_search()
					&& (
						( trx_addons_is_single() && get_post_type() == TRX_ADDONS_CPT_PORTFOLIO_PT )
						|| is_post_type_archive( TRX_ADDONS_CPT_PORTFOLIO_PT )
						|| is_tax( TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY )
						);
	}
}



// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for portfolio posts
if ( !function_exists( 'trx_addons_cpt_portfolio_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_portfolio_single_template');
	function trx_addons_cpt_portfolio_single_template($template) {
		global $post;
		if ( trx_addons_is_single() && $post->post_type == TRX_ADDONS_CPT_PORTFOLIO_PT ) {
			$template = trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT . 'portfolio/tpl.single.php' );
		}
		return $template;
	}
}

// Change standard archive template for portfolio posts
if ( !function_exists( 'trx_addons_cpt_portfolio_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_portfolio_archive_template');
	function trx_addons_cpt_portfolio_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_PORTFOLIO_PT) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'portfolio/tpl.archive.php');
		return $template;
	}	
}

// Change standard category template for portfolio categories (groups)
if ( !function_exists( 'trx_addons_cpt_portfolio_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_portfolio_taxonomy_template');
	function trx_addons_cpt_portfolio_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'portfolio/tpl.archive.php');
		return $template;
	}	
}

// Show related posts
if ( !function_exists( 'trx_addons_cpt_portfolio_related_posts_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_portfolio_related_posts_after_article', 20, 1);
	function trx_addons_cpt_portfolio_related_posts_after_article( $mode ) {
		if ($mode == 'portfolio.single' && apply_filters('trx_addons_filter_show_related_posts_after_article', true)) {
			do_action('trx_addons_action_related_posts', $mode);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_portfolio_related_posts_show' ) ) {
	add_filter('trx_addons_filter_show_related_posts', 'trx_addons_cpt_portfolio_related_posts_show');
	function trx_addons_cpt_portfolio_related_posts_show( $show ) {
		if (!$show && trx_addons_is_single() && get_post_type() == TRX_ADDONS_CPT_PORTFOLIO_PT) {
			do_action('trx_addons_action_related_posts', 'portfolio.single');
			$show = true;
		}
		return $show;
	}
}

if ( !function_exists( 'trx_addons_cpt_portfolio_related_posts' ) ) {
	add_action('trx_addons_action_related_posts', 'trx_addons_cpt_portfolio_related_posts', 10, 1);
	function trx_addons_cpt_portfolio_related_posts( $mode ) {
		if ($mode == 'portfolio.single') {
			$trx_addons_related_style   = explode('_', trx_addons_get_option('portfolio_style'));
			$trx_addons_related_type    = $trx_addons_related_style[0];
			$trx_addons_related_columns = empty($trx_addons_related_style[1]) ? 1 : max(1, $trx_addons_related_style[1]);
			
			trx_addons_get_template_part( apply_filters( 'trx_addons_filter_posts_related_template', 'templates/tpl.posts-related.php', 'portfolio' ),
												'trx_addons_args_related',
												apply_filters('trx_addons_filter_args_related', array(
																	'class' => 'portfolio_page_related sc_portfolio sc_portfolio_'.esc_attr($trx_addons_related_type),
																	'posts_per_page' => $trx_addons_related_columns,
																	'columns' => $trx_addons_related_columns,
																	'use_masonry' => false,
																	'template' => TRX_ADDONS_PLUGIN_CPT . 'portfolio/tpl.'.trim($trx_addons_related_type).'-item.php',
																	'template_args_name' => 'trx_addons_args_sc_portfolio',
																	'post_type' => TRX_ADDONS_CPT_PORTFOLIO_PT,
																	'taxonomies' => array(TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY)
																	)
															)
											);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_portfolio_show_details' ) ) {
	function trx_addons_cpt_portfolio_show_details( $args ) {
		$args = array_merge(
							array(
								'meta'  => '',
								'class' => '',
								'share' => false,
								'count' => -1
							),
							$args
							);
		if ( empty( $args['meta'] ) ) {
			$args['meta'] = get_post_meta( get_the_ID(), 'trx_addons_options', true );
		}
		if ( ! empty( $args['meta']['details'] ) && count( $args['meta']['details'] ) > 0 && ! empty( $args['meta']['details'][0]['title'] ) ) {
			trx_addons_get_template_part( TRX_ADDONS_PLUGIN_CPT . 'portfolio/tpl.details.php',
											'trx_addons_args_portfolio_details',
											$args
										);
		}
	}
}


// Modify featured args - add video (if specified)
if (!function_exists('trx_addons_cpt_portfolio_args_featured')) {
	add_filter( 'trx_addons_filter_args_featured', 'trx_addons_cpt_portfolio_args_featured', 10, 3 );
	function trx_addons_cpt_portfolio_args_featured( $featured_args, $sc='', $args=array() ) {
		if ( get_post_type() == TRX_ADDONS_CPT_PORTFOLIO_PT && ! isset( $featured_args['autoplay'] ) ) {
			$meta = (array)get_post_meta( get_the_ID(), 'trx_addons_options', true );
			if ( ! empty( $meta['video'] ) && empty( $featured_args['video'] ) ) {
				$key = 'video_autoplay' . ( trx_addons_is_single() && ! trx_addons_sc_stack_check( 'trx_sc_portfolio' ) ? '' : '_archive' );
				$featured_args['video'] = trx_addons_get_video_layout( apply_filters( 'trx_addons_filter_get_video_layout_args', array(
												'link' => $meta['video'],
												'autoplay' => ! empty( $meta[$key] ),
												'mute' => ! empty( $meta[$key] ),
												'loop' => ! empty( $meta[$key] ),
												'show_cover' => empty( $meta[$key] )
											), 'portfolio.item' ) );
				if ( ! empty( $meta[$key] ) ) {
					$featured_args['autoplay'] = $meta[$key] > 0;
				}
			}
		}
		return $featured_args;
	}
}

// Modify featured classes - add video (if specified)
if (!function_exists('trx_addons_cpt_portfolio_post_featured_classes')) {
	add_filter( 'trx_addons_filter_post_featured_classes', 'trx_addons_cpt_portfolio_post_featured_classes', 10, 3 );
	function trx_addons_cpt_portfolio_post_featured_classes( $classes, $args=array(), $mode='' ) {
		if ( get_post_type() == TRX_ADDONS_CPT_PORTFOLIO_PT && strpos( $classes, 'with_video_autoplay' ) === false ) {
			$meta = (array)get_post_meta( get_the_ID(), 'trx_addons_options', true );
			$key = 'video_autoplay' . ( trx_addons_is_single() && ! trx_addons_sc_stack_check( 'trx_sc_portfolio' ) ? '' : '_archive' );
			if ( ! empty( $meta['video'] ) && ! empty( $meta[$key] ) ) {
				$classes .= ' with_video_autoplay';
			}
		}
		return $classes;
	}
}

// Add video to the layouts if autoplay is enabled
if (!function_exists('trx_addons_cpt_portfolio_before_layouts_title_content')) {
	add_action( 'trx_addons_action_before_layouts_title_content', 'trx_addons_cpt_portfolio_before_layouts_title_content', 10, 1 );
	function trx_addons_cpt_portfolio_before_layouts_title_content( $args=array() ) {
		if ( trx_addons_is_single() && get_post_type() == TRX_ADDONS_CPT_PORTFOLIO_PT ) {
			$meta = (array)get_post_meta( get_the_ID(), 'trx_addons_options', true );
			if ( ! empty( $meta['video_position'] ) && $meta['video_position'] == 'header' && ! empty( $meta['video'] ) && ! empty( $meta['video_autoplay'] ) ) {
				trx_addons_show_layout( trx_addons_get_video_layout( apply_filters( 'trx_addons_filter_get_video_layout_args', array(
					'link' => $meta['video'],
					'autoplay' => true,
					'mute' => true,
					'loop' => true,
					'show_cover' => false
				), 'portfolio.header' ) ) );
			}
		}
		return $args;
	}
}

// Replace permalink to the custom link (if defined for current post)
if (!function_exists('trx_addons_cpt_portfolio_get_post_link')) {
	add_filter( 'trx_addons_filter_get_post_link', 'trx_addons_cpt_portfolio_get_post_link', 10, 1 );
	function trx_addons_cpt_portfolio_get_post_link( $link ) {
		if ( get_post_type() == TRX_ADDONS_CPT_PORTFOLIO_PT ) {
			$meta = (array)get_post_meta( get_the_ID(), 'trx_addons_options', true );
			if ( ! empty( $meta['link'] ) ) {
				$link = $meta['link'];
			}
		}
		return $link;
	}
}


// Admin utils
// -----------------------------------------------------------------

// Show <select> with portfolio categories in the admin filters area
if (!function_exists('trx_addons_cpt_portfolio_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_portfolio_admin_filters' );
	function trx_addons_cpt_portfolio_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_PORTFOLIO_PT, TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_portfolio_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY, 'trx_addons_cpt_portfolio_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY, 'trx_addons_cpt_portfolio_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY, 'trx_addons_cpt_portfolio_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_portfolio_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_PORTFOLIO_TAXONOMY);
	}
}


// Create additional column in the posts list
if (!function_exists('trx_addons_cpt_portfolio_add_custom_column')) {
	add_filter('manage_edit-'.trx_addons_cpt_param('portfolio', 'post_type').'_columns',	'trx_addons_cpt_portfolio_add_custom_column', 9);
	function trx_addons_cpt_portfolio_add_custom_column( $columns ){
		if (is_array($columns) && count($columns)>0) {
			$new_columns = array();
			foreach($columns as $k=>$v) {
				if (!in_array($k, array('author'))) {
					$new_columns[$k] = $v;
				} else {
					$new_columns['cpt_project_author'] = esc_html__('Author', 'trx_addons');
				}
			}
			$columns = $new_columns;
		}
		return $columns;
	}
}

// Fill custom columns in the posts list
if (!function_exists('trx_addons_cpt_portfolio_fill_custom_column')) {
	add_action('manage_'.trx_addons_cpt_param('portfolio', 'post_type').'_posts_custom_column', 'trx_addons_cpt_portfolio_fill_custom_column', 9, 2);
	function trx_addons_cpt_portfolio_fill_custom_column($column_name='', $post_id=0) {
		$meta = (array)get_post_meta($post_id, 'trx_addons_options', true);
		if ($column_name == 'cpt_project_author') {
			if ( empty( $meta['project_author'] ) || $meta['project_author'] == 'none') {
				// Translators: Add WordPress user name to the message
				echo esc_html( sprintf( __( 'User: %s', 'trx_addons' ), get_the_author() ) );
			} else {
				// Translators: Add Team member's name to the message
				echo esc_html( sprintf( __( 'Team: %s', 'trx_addons' ), get_the_title( $meta['project_author'] ) ) );
			}
		}
	}
}

// Fill 'options' arrays when its need in the admin mode
if (!function_exists('trx_addons_cpt_portfolio_options_get_list_choises')) {
	add_filter('trx_addons_filter_options_get_list_choises', 'trx_addons_cpt_portfolio_options_get_list_choises', 10, 2);
	function trx_addons_cpt_portfolio_options_get_list_choises($list, $name) {
		if ( is_array( $list ) && count( $list ) == 0 ) {
			if ($name == 'project_author') {
				$list = trx_addons_get_list_posts(false, array(
															'post_type' => defined( 'TRX_ADDONS_CPT_TEAM_PT' ) ? TRX_ADDONS_CPT_TEAM_PT : trx_addons_cpt_param( 'team', 'post_type' ),
															'orderby' => 'title',
															'order' => 'ASC'
															)
						);
			}
		}
		return $list;
	}
}


// Save portfolio date for search, sorting, etc.
if ( !function_exists( 'trx_addons_cpt_portfolio_save_post_options' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_cpt_portfolio_save_post_options', 10, 3);
	function trx_addons_cpt_portfolio_save_post_options($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_CPT_PORTFOLIO_PT) {
			update_post_meta($post_id, 'trx_addons_project_author', $options['project_author']);
		}
		return $options;
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'portfolio/portfolio-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
    require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'portfolio/portfolio-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'portfolio/portfolio-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'portfolio/portfolio-sc-vc.php';
}
