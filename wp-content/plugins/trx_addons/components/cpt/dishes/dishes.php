<?php
/**
 * ThemeREX Addons Custom post type: Dishes
 *
 * @package ThemeREX Addons
 * @since v1.6.09
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_DISHES_PT') ) define('TRX_ADDONS_CPT_DISHES_PT', trx_addons_cpt_param('dishes', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_DISHES_TAXONOMY') ) define('TRX_ADDONS_CPT_DISHES_TAXONOMY', trx_addons_cpt_param('dishes', 'taxonomy'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_dishes_init')) {
	add_action( 'init', 'trx_addons_cpt_dishes_init' );
	function trx_addons_cpt_dishes_init() {

		// Add dishes parameters to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_DISHES_PT, array(
			"price" => array(
				"title" => esc_html__("Price",  'trx_addons'),
				"desc" => wp_kses_data( __("The price of the dish", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"product" => array(
				"title" => __('Link to the dish product',  'trx_addons'),
				"desc" => __("Link to the product page for this dish", 'trx_addons'),
				"std" => '',
				"options" => array(),
				"type" => "select2"
			),
			"spicy" => array(
				"title" => esc_html__("Spiciness",  'trx_addons'),
				"desc" => wp_kses_data( __("Heat level of this dish on a scale from 1 to 5", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"nutritions" => array(
				"title" => esc_html__("Nutrition",  'trx_addons'),
				"desc" => wp_kses_data( __("Nutritional information. Each element on the new row", 'trx_addons') ),
				"std" => "",
				"type" => "textarea"
			),
			"ingredients" => array(
				"title" => esc_html__("Ingredients",  'trx_addons'),
				"desc" => wp_kses_data( __("Ingredients of this dish. Each element on the new row", 'trx_addons') ),
				"std" => "",
				"type" => "textarea"
			)
		));


		// Register taxonomies and post types
		// Taxonomy first, because it can using the combined rewrite rule (contains the slug of the post type)
		register_taxonomy(
			TRX_ADDONS_CPT_DISHES_TAXONOMY,
			TRX_ADDONS_CPT_DISHES_PT,
			apply_filters('trx_addons_filter_register_taxonomy',
				array(
					'post_type' 		=> TRX_ADDONS_CPT_DISHES_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Dishes Group', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Group', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Groups', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Groups', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Group', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Group', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Group', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Group', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Group Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Dishes Groups', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array(
												'slug'         => trx_addons_cpt_param('dishes', 'taxonomy_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_DISHES_PT,
				TRX_ADDONS_CPT_DISHES_TAXONOMY
			)
		);

		register_post_type(
			TRX_ADDONS_CPT_DISHES_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Dishes', 'trx_addons' ),
					'description'         => esc_html__( 'Dish Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Dishes', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Dish', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Dishes', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Dishes', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Dish', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Dish', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Dish', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Dish', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Dishes', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'taxonomies'          => array(TRX_ADDONS_CPT_DISHES_TAXONOMY),
					'supports'            => trx_addons_cpt_param('dishes', 'supports'),
					'public'              => true,
					'hierarchical'        => false,
					'has_archive'         => true,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_in_menu'        => true,
					// From WordPress 5.3 'menu_position' must be only integer or null (default)!
					// 'menu_position'       => '52.6',
					'menu_icon'			  => 'dashicons-carrot',
					'capability_type'     => 'post',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('dishes', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
											)
				),
				TRX_ADDONS_CPT_DISHES_PT
			)
		);
	}
}


// Allow Gutenberg as main editor for this post type
if ( ! function_exists( 'trx_addons_cpt_dishes_add_pt_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_pt_to_gutenberg', 'trx_addons_cpt_dishes_add_pt_to_gutenberg', 10, 2 );
	function trx_addons_cpt_dishes_add_pt_to_gutenberg( $allow, $post_type ) {
		return $allow || $post_type == TRX_ADDONS_CPT_DISHES_PT;
	}
}

// Allow Gutenberg as main editor for taxonomies
if ( ! function_exists( 'trx_addons_cpt_dishes_add_taxonomy_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_taxonomy_to_gutenberg', 'trx_addons_cpt_dishes_add_taxonomy_to_gutenberg', 10, 2 );
	function trx_addons_cpt_dishes_add_taxonomy_to_gutenberg( $allow, $tax ) {
		return $allow || in_array( $tax, array( TRX_ADDONS_CPT_DISHES_TAXONOMY ) );
	}
}

// Fill 'options' arrays when its need in the admin mode
if (!function_exists('trx_addons_cpt_dishes_options_get_list_choises')) {
	add_filter('trx_addons_filter_options_get_list_choises', 'trx_addons_cpt_dishes_options_get_list_choises', 10, 2);
	function trx_addons_cpt_dishes_options_get_list_choises($list, $name) {
		if ( is_array( $list ) && count( $list ) == 0 ) {
			if ($name == 'product') {
				$list = trx_addons_get_list_posts(false, 'product');
			} else if ($name == 'dishes_form') {
				$list = apply_filters('trx_addons_filter_page_contact_form',
					trx_addons_array_merge(
						array(
							'none' => esc_html__('None', 'trx_addons'),
							'default' => esc_html__('Default', 'trx_addons')
							),
						function_exists('trx_addons_exists_cf7') && trx_addons_exists_cf7() && is_admin() && (in_array(trx_addons_get_value_gp('page'), array('trx_addons_options', 'theme_options')) || strpos($_SERVER['REQUEST_URI'], 'customize.php')!==false)
							? trx_addons_get_list_cf7()
							: array()
					), 'dishes'
				);
			}
		}
		return $list;
	}
}

/* ------------------- Old way --------------------------
// Fill 'options' arrays when its need in the admin mode
if (!function_exists('trx_addons_cpt_dishes_before_show_options')) {
	add_filter('trx_addons_filter_before_show_options', 'trx_addons_cpt_dishes_before_show_options', 10, 2);
	function trx_addons_cpt_dishes_before_show_options($options, $post_type, $group='') {
		if ($post_type == TRX_ADDONS_CPT_DISHES_PT) {
			foreach ($options as $id=>$field) {
				// Recursive call for options type 'group'
				if ($field['type'] == 'group' && !empty($field['fields'])) {
					$options[$id]['fields'] = trx_addons_cpt_dishes_before_show_options($field['fields'], $post_type, $id);
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
// Add 'Dishes' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_dishes_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_cpt_dishes_options');
	function trx_addons_cpt_dishes_options($options) {
		trx_addons_array_insert_after($options, 'cpt_section', trx_addons_cpt_dishes_get_list_options());
		return $options;
	}
}

// Return parameters list for plugin's options
if (!function_exists('trx_addons_cpt_dishes_get_list_options')) {
	function trx_addons_cpt_dishes_get_list_options($add_parameters=array()) {
		return apply_filters('trx_addons_cpt_list_options', array(
			'dishes_info' => array(
				"title" => esc_html__('Dishes', 'trx_addons'),
				"desc" => wp_kses_data( __('Settings of the dishes archive', 'trx_addons') ),
				"type" => "info"
			),
			'dishes_style' => array(
				"title" => esc_html__('Style', 'trx_addons'),
				"desc" => wp_kses_data( __('Style of the dishes archive', 'trx_addons') ),
				"std" => 'default_2',
				"options" => apply_filters('trx_addons_filter_cpt_archive_styles',
											trx_addons_components_get_allowed_layouts('cpt', 'dishes', 'arh'),
											TRX_ADDONS_CPT_DISHES_PT),
				"type" => "select"
			)
		), 'dishes');
	}
}
------------------- /Old way --------------------- */

// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_dishes_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_cpt_dishes_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	add_action( "trx_addons_action_pagebuilder_preview_scripts", 'trx_addons_cpt_dishes_load_scripts_front', 10, 1 );
	function trx_addons_cpt_dishes_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'cpt_dishes', $force, array(
			'css'  => array(
				'trx_addons-cpt_dishes' => array( 'src' => TRX_ADDONS_PLUGIN_CPT . 'dishes/dishes.css' ),
			),
			'need' => trx_addons_is_dishes_page(),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_dishes' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/dishes' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_dishes"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_dishes' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_cpt_dishes_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_cpt_dishes_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_cpt_dishes', 'trx_addons_cpt_dishes_load_scripts_front_responsive', 10, 1 );
	function trx_addons_cpt_dishes_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'cpt_dishes', $force, array(
			'css'  => array(
				'trx_addons-cpt_dishes-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_CPT . 'dishes/dishes.responsive.css',
					'media' => 'md'
				),
			),
		) );
	}
}
	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cpt_dishes_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_cpt_dishes_merge_styles');
	function trx_addons_cpt_dishes_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'dishes/dishes.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_cpt_dishes_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_cpt_dishes_merge_styles_responsive');
	function trx_addons_cpt_dishes_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'dishes/dishes.responsive.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_cpt_dishes_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_cpt_dishes_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_cpt_dishes_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_cpt_dishes_check_in_html_output', 10, 1 );
	function trx_addons_cpt_dishes_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_dishes',
				'class=[\'"][^\'"]*type\\-' . TRX_ADDONS_CPT_DISHES_PT,
				'class=[\'"][^\'"]*' . TRX_ADDONS_CPT_DISHES_TAXONOMY . '\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'cpt_dishes', $content, $args ) ) {
			trx_addons_cpt_dishes_load_scripts_front( true );
		}
		return $content;
	}
}


// Return true if it's dishes page
if ( !function_exists( 'trx_addons_is_dishes_page' ) ) {
	function trx_addons_is_dishes_page() {
		return defined('TRX_ADDONS_CPT_DISHES_PT') 
					&& !is_search()
					&& (
						(trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_DISHES_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_DISHES_PT)
						|| is_tax(TRX_ADDONS_CPT_DISHES_TAXONOMY)
						);
	}
}


// Return current page title
if ( !function_exists( 'trx_addons_cpt_dishes_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_cpt_dishes_get_blog_title', 20 );
	function trx_addons_cpt_dishes_get_blog_title( $title = '' ) {
		if ( defined( 'TRX_ADDONS_CPT_DISHES_PT' ) ) {
			if ( trx_addons_is_single() && get_post_type() == TRX_ADDONS_CPT_DISHES_PT ) {
				$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
				if ( ! empty( $meta['product'] ) && (int) $meta['product'] > 0 ) {
					$title = array(
						'text' => get_the_title(),
						'class' => 'dishes_page_title',
						'link'  => get_permalink( $meta['product'] ),
						'link_text' => ! empty( $meta['price'] )
											? sprintf( __('Buy for %s', 'trx_addons'), $meta['price'] )
											: __('Order now', 'trx_addons')
					);
				} else {
					$url = apply_filters( 'trx_addons_filter_cpt_add_to_cart_url', '' );
					if ( ! empty( $url ) && $url != get_permalink() ) {
						$title = array(
							'text' => get_the_title(),
							'class' => 'dishes_page_title',
							'link'  => $url,
							'link_text' => ! empty( $meta['price'] )
												? sprintf( __('Buy for %s', 'trx_addons'), $meta['price'] )
												: apply_filters( 'trx_addons_filter_cpt_add_to_cart_text', '' )
						);
					}
				}
/*
			} else if ( is_post_type_archive(TRX_ADDONS_CPT_DISHES_PT) ) {
				$obj = get_post_type_object(TRX_ADDONS_CPT_DISHES_PT);
				$title = $obj->labels->all_items;
*/
			}
		}
		return $title;
	}
}


// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for the dishes posts
if ( !function_exists( 'trx_addons_cpt_dishes_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_dishes_single_template');
	function trx_addons_cpt_dishes_single_template($template) {
		global $post;
		if (trx_addons_is_single() && $post->post_type == TRX_ADDONS_CPT_DISHES_PT)
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'dishes/tpl.single.php');
		return $template;
	}
}

// Change standard archive template for the dishes posts
if ( !function_exists( 'trx_addons_cpt_dishes_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_dishes_archive_template');
	function trx_addons_cpt_dishes_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_DISHES_PT) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'dishes/tpl.archive.php');
		return $template;
	}	
}

// Change standard category template for the dishes categories (groups)
if ( !function_exists( 'trx_addons_cpt_dishes_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_dishes_taxonomy_template');
	function trx_addons_cpt_dishes_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_DISHES_TAXONOMY) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'dishes/tpl.archive.php');
		return $template;
	}	
}


// Related posts
//-------------------------------------------------------------

// Show related posts
if ( !function_exists( 'trx_addons_cpt_dishes_related_posts_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_dishes_related_posts_after_article', 20, 1);
	function trx_addons_cpt_dishes_related_posts_after_article( $mode ) {
		if ($mode == 'dishes.single' && apply_filters('trx_addons_filter_show_related_posts_after_article', true)) {
			do_action('trx_addons_action_related_posts', $mode);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_dishes_related_posts_show' ) ) {
	add_filter('trx_addons_filter_show_related_posts', 'trx_addons_cpt_dishes_related_posts_show');
	function trx_addons_cpt_dishes_related_posts_show( $show ) {
		if (!$show && trx_addons_is_single() && get_post_type() == TRX_ADDONS_CPT_DISHES_PT) {
			do_action('trx_addons_action_related_posts', 'dishes.single');
			$show = true;
		}
		return $show;
	}
}

if ( !function_exists( 'trx_addons_cpt_dishes_related_posts' ) ) {
	add_action('trx_addons_action_related_posts', 'trx_addons_cpt_dishes_related_posts', 10, 1);
	function trx_addons_cpt_dishes_related_posts( $mode ) {
		if ($mode == 'dishes.single') {
			$trx_addons_related_style   = explode('_', trx_addons_get_option('dishes_style'));
			$trx_addons_related_type    = $trx_addons_related_style[0];
			$trx_addons_related_columns = empty($trx_addons_related_style[1]) ? 1 : max(1, $trx_addons_related_style[1]);

			trx_addons_get_template_part( apply_filters( 'trx_addons_filter_posts_related_template', 'templates/tpl.posts-related.php', 'dishes' ),
												'trx_addons_args_related',
												apply_filters('trx_addons_filter_args_related', array(
																	'class' => 'dishes_page_related sc_dishes sc_dishes_'.esc_attr($trx_addons_related_type),
																	'posts_per_page' => $trx_addons_related_columns,
																	'columns' => $trx_addons_related_columns,
																	'template' => TRX_ADDONS_PLUGIN_CPT . 'dishes/tpl.'.trim($trx_addons_related_type).'-item.php',
																	'template_args_name' => 'trx_addons_args_sc_dishes',
																	'post_type' => TRX_ADDONS_CPT_DISHES_PT,
																	'taxonomies' => array(TRX_ADDONS_CPT_DISHES_TAXONOMY)
																	)
															)
											);
		}
	}
}

// Show contact form
if ( !function_exists( 'trx_addons_cpt_dishes_contact_form_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_dishes_contact_form_after_article', 50, 1);
	function trx_addons_cpt_dishes_contact_form_after_article( $mode ) {
		if ($mode == 'dishes.single') {
			do_action('trx_addons_action_contact_form', $mode);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_dishes_contact_form' ) ) {
	add_action('trx_addons_action_contact_form', 'trx_addons_cpt_dishes_contact_form', 10, 1);
	function trx_addons_cpt_dishes_contact_form( $mode ) {
		if ($mode == 'dishes.single') {
			$form_id = trx_addons_get_option('dishes_form');
			if ( !empty($form_id) && !trx_addons_is_off($form_id) ) {
				?><section class="page_contact_form dishes_page_form">
					<h3 class="section_title page_contact_form_title"><?php
						esc_html_e('Order this dish', 'trx_addons');
					?></h3><?php
					if ( (int) $form_id > 0 ) {
						// Add filter 'wpcf7_form_elements' before Contact Form 7 show form to add text
						add_filter('wpcf7_form_elements', 'trx_addons_cpt_dishes_wpcf7_form_elements');
						// Store data for the form for 4 hours
						set_transient(sprintf('trx_addons_cf7_%d_data', $form_id), array(
															'item'  => get_the_ID()
															), 4 * 60 * 60);
						// Display Contact Form 7
						trx_addons_show_layout( do_shortcode('[contact-form-7 id="'.esc_attr($form_id).'"]') );
						// Remove filter 'wpcf7_form_elements' after Contact Form 7 showed
						remove_filter('wpcf7_form_elements', 'trx_addons_cpt_dishes_wpcf7_form_elements');
			
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
if ( !function_exists( 'trx_addons_cpt_dishes_wpcf7_form_elements' ) ) {
	// Handler of the add_filter('wpcf7_form_elements', 'trx_addons_cpt_dishes_wpcf7_form_elements');
	function trx_addons_cpt_dishes_wpcf7_form_elements($elements) {
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

// Show <select> with dishes categories in the admin filters area
if (!function_exists('trx_addons_cpt_dishes_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_dishes_admin_filters' );
	function trx_addons_cpt_dishes_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_DISHES_PT, TRX_ADDONS_CPT_DISHES_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_dishes_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_DISHES_TAXONOMY, 'trx_addons_cpt_dishes_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_DISHES_TAXONOMY, 'trx_addons_cpt_dishes_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_DISHES_TAXONOMY, 'trx_addons_cpt_dishes_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_dishes_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_DISHES_TAXONOMY);
	}
}


// AJAX details
// ------------------------------------------------------------
if ( !function_exists( 'trx_addons_callback_ajax_dishes_details' ) ) {
	add_action('wp_ajax_trx_addons_post_details_in_popup',			'trx_addons_callback_ajax_dishes_details');
	add_action('wp_ajax_nopriv_trx_addons_post_details_in_popup',	'trx_addons_callback_ajax_dishes_details');
	function trx_addons_callback_ajax_dishes_details() {
		
		trx_addons_verify_nonce();

		if (($post_type = $_REQUEST['post_type']) == TRX_ADDONS_CPT_DISHES_PT) {
			$post_id = $_REQUEST['post_id'];

			$response = array('error'=>'', 'data' => '');
	
			if (!empty($post_id)) {
				global $post;
				$post = get_post($post_id);
				setup_postdata( $post );
				ob_start();
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'dishes/tpl.details.php');
				$response['data'] = ob_get_contents();
				ob_end_clean();
			} else {
				$response['error'] = '<article class="dishes_page">' . esc_html__('Invalid query parameter!', 'trx_addons') . '</article>';
			}
		
			trx_addons_ajax_response( apply_filters( 'trx_addons_filter_post_details_in_popup', $response, 'dishes' ) );
		}
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'dishes/dishes-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'dishes/dishes-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'dishes/dishes-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'dishes/dishes-sc-vc.php';
}
