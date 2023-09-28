<?php
/**
 * ThemeREX Addons Custom post type: Testimonials
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
if ( ! defined('TRX_ADDONS_CPT_TESTIMONIALS_PT') ) define('TRX_ADDONS_CPT_TESTIMONIALS_PT', trx_addons_cpt_param('testimonials', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY') ) define('TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY', trx_addons_cpt_param('testimonials', 'taxonomy'));


// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_testimonials_init')) {
	add_action( 'init', 'trx_addons_cpt_testimonials_init' );
	function trx_addons_cpt_testimonials_init() {

		// Add Testimonials to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_TESTIMONIALS_PT, array(
			"subtitle" => array(
				"title" => esc_html__("Item's subtitle",  'trx_addons'),
				"desc" => wp_kses_data( __("Testimonial author's position or any other text", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"rating" => array(
				"title" => esc_html__("Item's rating",  'trx_addons'),
				"desc" => wp_kses_data( __("Testimonial author's rating. If 0 - item is no rated.", 'trx_addons') ),
				"std" => 0,
				"min" => 0,
				"max" => 5,
				"step" => 1,
				"type" => "slider"
			)
		));

		// Register taxonomies and post types
		// Taxonomy first, because it can using the combined rewrite rule (contains the slug of the post type)
		register_taxonomy(
			TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY,
			TRX_ADDONS_CPT_TESTIMONIALS_PT,
			apply_filters('trx_addons_filter_register_taxonomy',
				array(
					'post_type' 		=> TRX_ADDONS_CPT_TESTIMONIALS_PT,
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => esc_html__( 'Testimonials Group', 'trx_addons' ),
						'singular_name'     => esc_html__( 'Group', 'trx_addons' ),
						'search_items'      => esc_html__( 'Search Groups', 'trx_addons' ),
						'all_items'         => esc_html__( 'All Groups', 'trx_addons' ),
						'parent_item'       => esc_html__( 'Parent Group', 'trx_addons' ),
						'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_addons' ),
						'edit_item'         => esc_html__( 'Edit Group', 'trx_addons' ),
						'update_item'       => esc_html__( 'Update Group', 'trx_addons' ),
						'add_new_item'      => esc_html__( 'Add New Group', 'trx_addons' ),
						'new_item_name'     => esc_html__( 'New Group Name', 'trx_addons' ),
						'menu_name'         => esc_html__( 'Testimonial Group', 'trx_addons' ),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array(
												'slug'         => trx_addons_cpt_param('testimonials', 'taxonomy_slug'),
												'with_front'   => false,
												'hierarchical' => true
											)
				),
				TRX_ADDONS_CPT_TESTIMONIALS_PT,
				TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY
			)
		);

		register_post_type(
			TRX_ADDONS_CPT_TESTIMONIALS_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Testimonial', 'trx_addons' ),
					'description'         => esc_html__( 'Testimonial Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Testimonials', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Testimonial', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Testimonials', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Testimonials', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Testimonial', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Testimonial', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Testimonial', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Testimonial', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Testimonial', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'taxonomies'          => array(TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY),
					'supports'            => trx_addons_cpt_param('testimonials', 'supports'),
					'public'              => true,
					'publicly_queryable'  => false,
					'hierarchical'        => false,
					'has_archive'         => false,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'exclude_from_search' => true,
					// From WordPress 5.3 'menu_position' must be only integer or null (default)!
					// 'menu_position'       => '54.0',
					'menu_icon'			  => 'dashicons-format-status',
					'capability_type'     => 'post',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('testimonials', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
											)
				),
				TRX_ADDONS_CPT_TESTIMONIALS_PT
			)
		);
	}
}


// Allow Gutenberg as main editor for this post type
if ( ! function_exists( 'trx_addons_cpt_testimonials_add_pt_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_pt_to_gutenberg', 'trx_addons_cpt_testimonials_add_pt_to_gutenberg', 10, 2 );
	function trx_addons_cpt_testimonials_add_pt_to_gutenberg( $allow, $post_type ) {
		return $allow || $post_type == TRX_ADDONS_CPT_TESTIMONIALS_PT;
	}
}

// Allow Gutenberg as main editor for taxonomies
if ( ! function_exists( 'trx_addons_cpt_testimonials_add_taxonomy_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_taxonomy_to_gutenberg', 'trx_addons_cpt_testimonials_add_taxonomy_to_gutenberg', 10, 2 );
	function trx_addons_cpt_testimonials_add_taxonomy_to_gutenberg( $allow, $tax ) {
		return $allow || in_array( $tax, array( TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY ) );
	}
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_testimonials_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_cpt_testimonials_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_cpt_testimonials_load_scripts_front', 10, 1 );
	function trx_addons_cpt_testimonials_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'cpt_testimonials', $force, array(
			'css'  => array(
				'trx_addons-cpt_testimonials' => array( 'src' => TRX_ADDONS_PLUGIN_CPT . 'testimonials/testimonials.css' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_testimonials' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/testimonials' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_testimonials"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_testimonials' ),
			)
		) );
	}
}
	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cpt_testimonials_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_cpt_testimonials_merge_styles');
	function trx_addons_cpt_testimonials_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'testimonials/testimonials.css' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_cpt_testimonials_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_cpt_testimonials_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_cpt_testimonials_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_cpt_testimonials_check_in_html_output', 10, 1 );
	function trx_addons_cpt_testimonials_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_testimonials',
				'class=[\'"][^\'"]*type\\-' . TRX_ADDONS_CPT_TESTIMONIALS_PT,
				'class=[\'"][^\'"]*' . TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY . '\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'cpt_testimonials', $content, $args ) ) {
			trx_addons_cpt_testimonials_load_scripts_front( true );
		}
		return $content;
	}
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with testimonials categories in the admin filters area
if (!function_exists('trx_addons_cpt_testimonials_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_testimonials_admin_filters' );
	function trx_addons_cpt_testimonials_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_TESTIMONIALS_PT, TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_testimonials_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY, 'trx_addons_cpt_testimonials_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY, 'trx_addons_cpt_testimonials_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY, 'trx_addons_cpt_testimonials_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_testimonials_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY);
	}
}



// Show stars
//----------------------------------------------------------------------------
if ( ! function_exists( 'trx_addons_testimonials_show_rating' ) ) {
	function trx_addons_testimonials_show_rating( $rating ) {
		if ( $rating == 0 ) return;
		$rating = max( 1, min( 5, $rating ) );
		?><span class="sc_testimonials_rating">
			<span class="sc_testimonials_rating_stars">
				<span class="sc_testimonials_rating_stars_default"><?php
					$icon = trx_addons_check_option('reviews_mark_icon')
								? trx_addons_get_option('reviews_mark_icon', 'trx_addons_icon-star')
								: 'trx_addons_icon-star';
					for ($i = 0; $i < 5; $i++) {
						?><span class="sc_testimonials_rating_star <?php echo esc_attr( $icon ); ?>"></span><?php
					}
				?></span><?php
				?><span class="sc_testimonials_rating_stars_hover" style="width:<?php echo $rating * 100 / 5; ?>%;"><?php
					for ($i = 0; $i < 5; $i++) {
						?><span class="sc_testimonials_rating_star <?php echo esc_attr( $icon ); ?>"></span><?php
					}
				?></span>
			</span>
		</span><?php
	}
}


// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'testimonials/testimonials-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'testimonials/testimonials-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'testimonials/testimonials-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'testimonials/testimonials-sc-vc.php';
}

// Create our widget
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'testimonials/testimonials-widget.php';
