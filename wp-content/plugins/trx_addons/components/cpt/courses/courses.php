<?php
/**
 * ThemeREX Addons Custom post type: Courses
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_COURSES_PT') ) define('TRX_ADDONS_CPT_COURSES_PT', trx_addons_cpt_param('courses', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_COURSES_TAXONOMY') ) define('TRX_ADDONS_CPT_COURSES_TAXONOMY', trx_addons_cpt_param('courses', 'taxonomy'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_courses_init')) {
	add_action( 'init', 'trx_addons_cpt_courses_init' );
	function trx_addons_cpt_courses_init() {

		// Add Courses parameters to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_COURSES_PT, array(
			"date" => array(
				"title" => esc_html__("Date",  'trx_addons'),
				"desc" => wp_kses_data( __("Start date in format: yyyy-mm-dd", 'trx_addons') ),
				"std" => "",
				"type" => "date"
			),
			"time" => array(
				"title" => esc_html__("Time",  'trx_addons'),
				"desc" => wp_kses_data( __("Class times. For example: 7.00pm - 9.00pm, 16:00 - 18:00, etc.", 'trx_addons') ),
				"std" => "",
				"type" => "time"
			),
			"duration" => array(
				"title" => esc_html__("Duration",  'trx_addons'),
				"desc" => wp_kses_data( __("Course duration.", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"price" => array(
				"title" => esc_html__("Price",  'trx_addons'),
				"desc" => wp_kses_data( __("Course price. For example: $99.90, $100.00/month, etc.", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"product" => array(
				"title" => __('Link to course product',  'trx_addons'),
				"desc" => __("Link to product page for this course", 'trx_addons'),
				"std" => '',
				"options" => array(),
				"type" => "select2")
		));
		
		// Register taxonomies and post types
		// Taxonomy first, because it can using the combined rewrite rule (contains the slug of the post type)
		register_taxonomy(
			TRX_ADDONS_CPT_COURSES_TAXONOMY,
			TRX_ADDONS_CPT_COURSES_PT,
			apply_filters('trx_addons_filter_register_taxonomy',
				array(
					'post_type' 		=> TRX_ADDONS_CPT_COURSES_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Courses Group', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Group', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Groups', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Groups', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Group', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Group', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Group', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Group', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Group Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Courses Groups', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array(
												'slug'         => trx_addons_cpt_param('courses', 'taxonomy_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_COURSES_PT,
				TRX_ADDONS_CPT_COURSES_TAXONOMY
			)
		);

		register_post_type(
			TRX_ADDONS_CPT_COURSES_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Courses', 'trx_addons' ),
					'description'         => esc_html__( 'Course Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Courses', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Course', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Courses', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Courses', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Course', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Course', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Course', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Course', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Courses', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'taxonomies'          => array(TRX_ADDONS_CPT_COURSES_TAXONOMY),
					'supports'            => trx_addons_cpt_param('courses', 'supports'),
					'public'              => true,
					'hierarchical'        => false,
					'has_archive'         => true,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_in_menu'        => true,
					// From WordPress 5.3 'menu_position' must be only integer or null (default)!
					// 'menu_position'       => '52.4',
					'menu_icon'			  => 'dashicons-welcome-learn-more',
					'capability_type'     => 'post',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('courses', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
											)
				),
				TRX_ADDONS_CPT_COURSES_PT
			)
		);
	}
}


// Allow Gutenberg as main editor for this post type
if ( ! function_exists( 'trx_addons_cpt_courses_add_pt_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_pt_to_gutenberg', 'trx_addons_cpt_courses_add_pt_to_gutenberg', 10, 2 );
	function trx_addons_cpt_courses_add_pt_to_gutenberg( $allow, $post_type ) {
		return $allow || $post_type == TRX_ADDONS_CPT_COURSES_PT;
	}
}

// Allow Gutenberg as main editor for taxonomies
if ( ! function_exists( 'trx_addons_cpt_courses_add_taxonomy_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_taxonomy_to_gutenberg', 'trx_addons_cpt_courses_add_taxonomy_to_gutenberg', 10, 2 );
	function trx_addons_cpt_courses_add_taxonomy_to_gutenberg( $allow, $tax ) {
		return $allow || in_array( $tax, array( TRX_ADDONS_CPT_COURSES_TAXONOMY ) );
	}
}

// Fill 'options' arrays when its need in the admin mode
if (!function_exists('trx_addons_cpt_courses_options_get_list_choises')) {
	add_filter('trx_addons_filter_options_get_list_choises', 'trx_addons_cpt_courses_options_get_list_choises', 10, 2);
	function trx_addons_cpt_courses_options_get_list_choises($list, $name) {
		if ( is_array( $list ) && count( $list ) == 0 ) {
			if ($name == 'product') {
				$list = trx_addons_get_list_posts(false, 'product');
			} else if ($name == 'courses_form') {
				$list = apply_filters('trx_addons_filter_page_contact_form',
					trx_addons_array_merge(
						array(
							'none' => esc_html__('None', 'trx_addons'),
							'default' => esc_html__('Default', 'trx_addons')
							),
						function_exists('trx_addons_exists_cf7') && trx_addons_exists_cf7() && is_admin() && (in_array(trx_addons_get_value_gp('page'), array('trx_addons_options', 'theme_options')) || strpos($_SERVER['REQUEST_URI'], 'customize.php')!==false)
							? trx_addons_get_list_cf7()
							: array()
					), 'courses'
				);
			}
		}
		return $list;
	}
}

/* ------------------- Old way --------------------------
// Fill 'options' arrays when its need in the admin mode
if (!function_exists('trx_addons_cpt_courses_before_show_options')) {
	add_filter('trx_addons_filter_before_show_options', 'trx_addons_cpt_courses_before_show_options', 10, 2);
	function trx_addons_cpt_courses_before_show_options($options, $post_type, $group='') {
		if ($post_type == TRX_ADDONS_CPT_COURSES_PT) {
			foreach ($options as $id=>$field) {
				// Recursive call for options type 'group'
				if ($field['type'] == 'group' && !empty($field['fields'])) {
					$options[$id]['fields'] = trx_addons_cpt_courses_before_show_options($field['fields'], $post_type, $id);
					continue;
				}
				// Skip elements without param 'options'
				if (!isset($field['options']) || count($field['options'])>0) {
					continue;
				}
				// Fill the 'product' array
				if ($id == 'product') {
					$options[$id]['options'] = trx_addons_get_list_posts(false, 'product');
				}
			}
		}
		return $options;
	}
}
*/

/* ------------------- Old way - moved to the cpt.php now ---------------------
// Add 'Courses' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_courses_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_cpt_courses_options');
	function trx_addons_cpt_courses_options($options) {
		trx_addons_array_insert_after($options, 'cpt_section', trx_addons_cpt_courses_get_list_options());
		return $options;
	}
}

// Return parameters list for plugin's options
if (!function_exists('trx_addons_cpt_courses_get_list_options')) {
	function trx_addons_cpt_courses_get_list_options($add_parameters=array()) {
		return apply_filters('trx_addons_cpt_list_options', array(
			'courses_info' => array(
				"title" => esc_html__('Courses', 'trx_addons'),
				"desc" => wp_kses_data( __('Settings of the courses archive', 'trx_addons') ),
				"type" => "info"
			),
			'courses_style' => array(
				"title" => esc_html__('Style', 'trx_addons'),
				"desc" => wp_kses_data( __('Style of the courses archive', 'trx_addons') ),
				"std" => 'default_2',
				"options" => apply_filters('trx_addons_filter_cpt_archive_styles',
											trx_addons_components_get_allowed_layouts('cpt', 'courses', 'arh'),
											TRX_ADDONS_CPT_COURSES_PT),
				"type" => "select"
			)
		), 'courses');
	}
}
------------------- /Old way --------------------- */

// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_courses_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_courses_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_cpt_courses_load_scripts_front', 10, 1 );
	function trx_addons_cpt_courses_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'cpt_courses', $force, array(
			'css'  => array(
				'trx_addons-cpt_courses' => array( 'src' => TRX_ADDONS_PLUGIN_CPT . 'courses/courses.css' ),
			),
			'need' => trx_addons_is_courses_page(),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_courses' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/courses' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_courses"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_courses' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_cpt_courses_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_cpt_courses_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_cpt_courses', 'trx_addons_cpt_courses_load_scripts_front_responsive', 10, 1 );
	function trx_addons_cpt_courses_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'cpt_courses', $force, array(
			'css'  => array(
				'trx_addons-cpt_courses-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_CPT . 'courses/courses.responsive.css',
					'media' => 'md'
				),
			),
		) );
	}
}

// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cpt_courses_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_cpt_courses_merge_styles');
	function trx_addons_cpt_courses_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'courses/courses.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_cpt_courses_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_cpt_courses_merge_styles_responsive');
	function trx_addons_cpt_courses_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'courses/courses.responsive.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_cpt_courses_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_cpt_courses_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_cpt_courses_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_cpt_courses_check_in_html_output', 10, 1 );
	function trx_addons_cpt_courses_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_courses',
				'class=[\'"][^\'"]*type\\-' . TRX_ADDONS_CPT_COURSES_PT,
				'class=[\'"][^\'"]*' . TRX_ADDONS_CPT_COURSES_TAXONOMY . '\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'cpt_courses', $content, $args ) ) {
			trx_addons_cpt_courses_load_scripts_front( true );
		}
		return $content;
	}
}

	
// Add sort in the query for the courses
if ( !function_exists( 'trx_addons_cpt_courses_add_sort_order' ) ) {
	add_filter('trx_addons_filter_add_sort_order',	'trx_addons_cpt_courses_add_sort_order', 10, 3);
	function trx_addons_cpt_courses_add_sort_order($q, $orderby, $order='desc') {
		if ($orderby == 'courses_date') {
			$q['order'] = $order;
			$q['orderby'] = 'meta_value';
			$q['meta_key'] = 'trx_addons_courses_date';
		}
		return $q;
	}
}


// Save courses date for search, sorting, etc.
if ( !function_exists( 'trx_addons_cpt_courses_save_post_options' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_cpt_courses_save_post_options', 10, 3);
	function trx_addons_cpt_courses_save_post_options($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_CPT_COURSES_PT) {
			$tm = explode('-', str_replace(' ', '', strtoupper($options['time'])));
			$tm_add = strpos($tm[0], 'PM')!==false ? 12 : 0;
			$tm = explode(':', str_replace(array('.', 'AM', 'PM', ' '), array(':', '', '', ''), $tm[0]));
			update_post_meta($post_id, 'trx_addons_courses_date', $options['date'].' '.(!empty($tm[1]) ? ($tm[0]+$tm_add).':'.$tm[1] : $tm[0]));
			update_post_meta($post_id, 'trx_addons_courses_price', $options['price']);
		}
		return $options;
	}
}


// Return true if it's courses page
if ( !function_exists( 'trx_addons_is_courses_page' ) ) {
	function trx_addons_is_courses_page() {
		return defined('TRX_ADDONS_CPT_COURSES_PT') 
					&& !is_search()
					&& (
						(trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_COURSES_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_COURSES_PT)
						|| is_tax(TRX_ADDONS_CPT_COURSES_TAXONOMY)
						);
	}
}


// Return current page title
if ( !function_exists( 'trx_addons_cpt_courses_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_cpt_courses_get_blog_title', 20 );
	function trx_addons_cpt_courses_get_blog_title( $title = '' ) {
		if ( defined( 'TRX_ADDONS_CPT_COURSES_PT' )  ) {
			if ( trx_addons_is_single() && get_post_type() == TRX_ADDONS_CPT_COURSES_PT ) {
				$meta = (array)get_post_meta( get_the_ID(), 'trx_addons_options', true );
				if ( ! empty( $meta['product'] ) && (int) $meta['product'] > 0 ) {
					$title = array(
						'text'  => get_the_title(),
						'class' => 'courses_page_title',
						'link'  => get_permalink( $meta['product'] ),
						'link_text' => ! empty( $meta['price'] )
											? sprintf( __('Buy for %s', 'trx_addons'), $meta['price'] )
											: __('Join the Course', 'trx_addons')
					);
				} else {
					$url = apply_filters( 'trx_addons_filter_cpt_add_to_cart_url', '' );
					if ( ! empty( $url ) && $url != get_permalink() ) {
						$title = array(
							'text'  => get_the_title(),
							'class' => 'courses_page_title',
							'link'  => $url,
							'link_text' => ! empty( $meta['price'] )
												? sprintf( __('Buy for %s', 'trx_addons'), $meta['price'] )
												: apply_filters( 'trx_addons_filter_cpt_add_to_cart_text', '' )
						);
					}
				}
/*
			} else if ( is_post_type_archive(TRX_ADDONS_CPT_COURSES_PT) ) {
				$obj = get_post_type_object(TRX_ADDONS_CPT_COURSES_PT);
				$title = $obj->labels->all_items;
*/
			}
		}
		return $title;
	}
}


// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for the courses posts
if ( !function_exists( 'trx_addons_cpt_courses_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_courses_single_template');
	function trx_addons_cpt_courses_single_template($template) {
		global $post;
		if (trx_addons_is_single() && $post->post_type == TRX_ADDONS_CPT_COURSES_PT)
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'courses/tpl.single.php');
		return $template;
	}
}

// Change standard archive template for the courses posts
if ( !function_exists( 'trx_addons_cpt_courses_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_courses_archive_template');
	function trx_addons_cpt_courses_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_COURSES_PT) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'courses/tpl.archive.php');
		return $template;
	}	
}

// Change standard category template for the courses categories (groups)
if ( !function_exists( 'trx_addons_cpt_courses_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_courses_taxonomy_template');
	function trx_addons_cpt_courses_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_COURSES_TAXONOMY) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'courses/tpl.archive.php');
		return $template;
	}	
}

// Show related posts
if ( !function_exists( 'trx_addons_cpt_courses_related_posts_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_courses_related_posts_after_article', 20, 1);
	function trx_addons_cpt_courses_related_posts_after_article( $mode ) {
		if ($mode == 'courses.single' && apply_filters('trx_addons_filter_show_related_posts_after_article', true)) {
			do_action('trx_addons_action_related_posts', $mode);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_courses_related_posts_show' ) ) {
	add_filter('trx_addons_filter_show_related_posts', 'trx_addons_cpt_courses_related_posts_show');
	function trx_addons_cpt_courses_related_posts_show( $show ) {
		if (!$show && trx_addons_is_single() && get_post_type() == TRX_ADDONS_CPT_COURSES_PT) {
			do_action('trx_addons_action_related_posts', 'courses.single');
			$show = true;
		}
		return $show;
	}
}

if ( !function_exists( 'trx_addons_cpt_courses_related_posts' ) ) {
	add_action('trx_addons_action_related_posts', 'trx_addons_cpt_courses_related_posts', 10, 1);
	function trx_addons_cpt_courses_related_posts( $mode ) {
		if ($mode == 'courses.single') {
			$trx_addons_related_style   = explode('_', trx_addons_get_option('courses_style'));
			$trx_addons_related_type    = $trx_addons_related_style[0];
			$trx_addons_related_columns = empty($trx_addons_related_style[1]) ? 1 : max(1, $trx_addons_related_style[1]);
			
			trx_addons_get_template_part( apply_filters( 'trx_addons_filter_posts_related_template', 'templates/tpl.posts-related.php', 'courses' ),
												'trx_addons_args_related',
												apply_filters('trx_addons_filter_args_related', array(
																	'class' => 'courses_page_related sc_courses sc_courses_'.esc_attr($trx_addons_related_type),
																	'posts_per_page' => $trx_addons_related_columns,
																	'columns' => $trx_addons_related_columns,
																	'template' => TRX_ADDONS_PLUGIN_CPT . 'courses/tpl.'.trim($trx_addons_related_type).'-item.php',
																	'template_args_name' => 'trx_addons_args_sc_courses',
																	'post_type' => TRX_ADDONS_CPT_COURSES_PT,
																	'taxonomies' => array(TRX_ADDONS_CPT_COURSES_TAXONOMY),
																	'more_text' => __('Learn more', 'trx_addons')
																	)
															)
											);
		}
	}
}

// Show contact form
if ( !function_exists( 'trx_addons_cpt_courses_contact_form_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_courses_contact_form_after_article', 50, 1);
	function trx_addons_cpt_courses_contact_form_after_article( $mode ) {
		if ($mode == 'courses.single') {
			do_action('trx_addons_action_contact_form', $mode);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_courses_contact_form' ) ) {
	add_action('trx_addons_action_contact_form', 'trx_addons_cpt_courses_contact_form', 10, 1);
	function trx_addons_cpt_courses_contact_form( $mode ) {
		if ($mode == 'courses.single') {
			$form_id = trx_addons_get_option('courses_form');
			if ( !empty($form_id) && !trx_addons_is_off($form_id) ) {
				?><section class="page_contact_form courses_page_form">
					<h3 class="section_title page_contact_form_title"><?php
						esc_html_e('Join this course', 'trx_addons');
					?></h3><?php
					if ( (int) $form_id > 0 ) {
						// Add filter 'wpcf7_form_elements' before Contact Form 7 show form to add text
						add_filter('wpcf7_form_elements', 'trx_addons_cpt_courses_wpcf7_form_elements');
						// Store data for the form for 4 hours
						set_transient(sprintf('trx_addons_cf7_%d_data', $form_id), array(
															'item'  => get_the_ID()
															), 4 * 60 * 60);
						// Display Contact Form 7
						trx_addons_show_layout( do_shortcode( '[contact-form-7 id="'.esc_attr($form_id).'"]' ) );
						// Remove filter 'wpcf7_form_elements' after Contact Form 7 showed
						remove_filter('wpcf7_form_elements', 'trx_addons_cpt_courses_wpcf7_form_elements');
			
					// Default form
					} else if ($form_id == 'default' && function_exists( 'trx_addons_sc_form' ) ) {
						trx_addons_show_layout( trx_addons_sc_form( array() ) );
					}
				?></section><?php
			}
		}
	}
}

// Add filter 'wpcf7_form_elements' before Contact Form 7 show form to add text
if ( !function_exists( 'trx_addons_cpt_courses_wpcf7_form_elements' ) ) {
	// Handler of the add_filter('wpcf7_form_elements', 'trx_addons_cpt_courses_wpcf7_form_elements');
	function trx_addons_cpt_courses_wpcf7_form_elements($elements) {
		$elements = str_replace('</textarea>',
								esc_html(sprintf(__("Hi.\nI'm interested in '%s'.\nPlease, get in touch with me.", 'trx_addons'), get_the_title()))
								. '</textarea>',
								$elements
								);
		return $elements;
	}
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with courses categories in the admin filters area
if (!function_exists('trx_addons_cpt_courses_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_courses_admin_filters' );
	function trx_addons_cpt_courses_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_COURSES_PT, TRX_ADDONS_CPT_COURSES_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_courses_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_COURSES_TAXONOMY, 'trx_addons_cpt_courses_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_COURSES_TAXONOMY, 'trx_addons_cpt_courses_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_COURSES_TAXONOMY, 'trx_addons_cpt_courses_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_courses_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_COURSES_TAXONOMY);
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'courses/courses-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'courses/courses-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'courses/courses-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'courses/courses-sc-vc.php';
}
