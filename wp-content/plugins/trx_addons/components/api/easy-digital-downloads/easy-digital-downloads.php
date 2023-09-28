<?php
/**
 * Plugin support: Easy Digital Downloads
 *
 * @package ThemeREX Addons
 * @since v1.6.29
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// -----------------------------------------------------------------
// -- Additional taxonomies and post's meta
// -----------------------------------------------------------------

if ( ! defined( 'TRX_ADDONS_EDD_PT' ) )					define( 'TRX_ADDONS_EDD_PT', 'download' );
if ( ! defined( 'TRX_ADDONS_EDD_TAXONOMY_CATEGORY' ) )	define( 'TRX_ADDONS_EDD_TAXONOMY_CATEGORY', 'download_category' );
if ( ! defined( 'TRX_ADDONS_EDD_TAXONOMY_TAG' ) )		define( 'TRX_ADDONS_EDD_TAXONOMY_TAG', 'download_tag' );


// Check if plugin installed and activated
// Attention! This function is used in many files and was moved to the api.php
/*
if ( ! function_exists( 'trx_addons_exists_edd' ) ) {
	function trx_addons_exists_edd() {
		return class_exists('Easy_Digital_Downloads');
	}
}
*/

if ( ! function_exists( 'trx_addons_is_edd_page' ) ) {
	/**
	 * Check if current page is any EDD page: single, archive, category or tag page
	 * 
	 * @trigger trx_addons_filter_is_edd_page
	 * 
	 * @return boolean    True if current page is any EDD page
	 */
	function trx_addons_is_edd_page() {
		$rez = trx_addons_exists_edd()
					&& ! is_search()
					&& (
						( trx_addons_is_single() && get_post_type() == TRX_ADDONS_EDD_PT )
						|| is_post_type_archive( TRX_ADDONS_EDD_PT )
						|| is_tax( TRX_ADDONS_EDD_TAXONOMY_CATEGORY )
						|| is_tax( TRX_ADDONS_EDD_TAXONOMY_TAG )
						);
		return apply_filters( 'trx_addons_filter_is_edd_page', $rez );
	}
}

if ( ! function_exists( 'trx_addons_edd_options' ) ) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_edd_options' );
	/**
	 * Add EDD options to the ThemeREX Addons Options
	 *
	 * @param array $options  Array of options
	 * 
	 * @return array          Array of options
	 */
	function trx_addons_edd_options( $options ) {
		if ( trx_addons_exists_edd() ) {
			trx_addons_array_insert_before( $options, 'theme_specific_section', array(
					'edd_section' => array(
						"title" => esc_html__('EDD', 'trx_addons'),
						"desc" => wp_kses_data( __("Easy Digital Downloads settings", 'trx_addons') ),
						'icon' => 'trx_addons_icon-cart',
						"type" => "section"
					),
					'edd_additional_info' => array(
						"title" => esc_html__('Additional price parameters', 'trx_addons'),
						"desc" => wp_kses_data( __("Additional info for regular and extended prices", 'trx_addons') ),
						"type" => "info"
					),
					'edd_regular_price_description' => array(
						"title" => esc_html__('Regular price description',  'trx_addons'),
						"desc" => wp_kses_data( __('Description under the regular price',  'trx_addons') ),
						"std" => "",
						"type" => "textarea"
					),
					'edd_extended_price_description' => array(
						"title" => esc_html__('Extended price description',  'trx_addons'),
						"desc" => wp_kses_data( __('Description under the extended price',  'trx_addons') ),
						"std" => "",
						"type" => "textarea"
					),
					'edd_price_info' => array(
						"title" => esc_html__('Info below the price',  'trx_addons'),
						"desc" => wp_kses_data( __('Additional info after the price selector',  'trx_addons') ),
						"std" => "",
						"type" => "textarea"
					),
					'edd_free_info' => array(
						"title" => esc_html__('Info about free items',  'trx_addons'),
						"desc" => wp_kses_data( __('Additional info about free items above the "Download" link',  'trx_addons') ),
						"std" => "",
						"type" => "textarea"
					),
					'edd_referals_info' => array(
						"title" => esc_html__('Affiliates', 'trx_addons'),
						"desc" => wp_kses_data( __("Specify your affiliate parameters from marketplaces", 'trx_addons') ),
						"type" => "info"
					),
					'edd_referals' => array(
						"title" => esc_html__("Referals", 'trx_addons'),
						"desc" => wp_kses_data( __("If product's URL has this substring, the following parameter should be added.", 'trx_addons') ),
						"clone" => true,
						"std" => array(array()),
						"type" => "group",
						"fields" => array(
							'url' => array(
								"title" => esc_html__("Part of the marketplace's URL", 'trx_addons'),
								"desc" => wp_kses_data( __("If product's URL have this substring - next param should be added", 'trx_addons') ),
								"class" => "trx_addons_column-1_2 trx_addons_new_row",
								"std" => "",
								"type" => "text"
							),
							'param' => array(
								"title" => esc_html__('Parameters to add', 'trx_addons'),
								"desc" => wp_kses_data( __("Parameters to add to the URL (as key1=value1&key2=value2...)", 'trx_addons') ),
								"class" => "trx_addons_column-1_2",
								"std" => "",
								"type" => "text"
							)
						)
					)
				)
			);
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_edd_init' ) ) {
	add_action( 'init', 'trx_addons_edd_init' );
	/**
	 * Register additional meta fields for downloads - add them to the Meta Box support
	 * 
	 * @hooked init
	 * 
	 * @trigger trx_addons_filter_edd_meta_box
	 */
	function trx_addons_edd_init() {

		if ( ! trx_addons_exists_edd() ) {
			return;
		}

		// Add Downloads parameters to the Meta Box support
		trx_addons_meta_box_register( TRX_ADDONS_EDD_PT, apply_filters('trx_addons_filter_edd_meta_box', array(
			"general_section" => array(
				"title" => esc_html__("General", 'trx_addons'),
				"desc" => wp_kses_data( __('General options', 'trx_addons') ),
				"type" => "section"
			),
			"slug" => array(
				"title" => esc_html__("Slug", 'trx_addons'),
				"desc" => wp_kses_data( __('Slug to create the demo link', 'trx_addons') ),
				"std" => '',
				"type" => "text"
			),
			"date_created" => array(
				"title" => esc_html__("Date created", 'trx_addons'),
				"desc" => wp_kses_data( __('The creation date of the item in the format "YYYY-mm-dd"', 'trx_addons') ),
				"std" => date('Y-m-d'),
				"type" => "date"
			),
			"date_updated" => array(
				"title" => esc_html__("Last update", 'trx_addons'),
				"desc" => wp_kses_data( __('Date of last update of this item in the format "YYYY-mm-dd"', 'trx_addons') ),
				"std" => date('Y-m-d'),
				"type" => "date"
			),
			"version" => array(
				"title" => esc_html__("Version", 'trx_addons'),
				"desc" => wp_kses_data( __("Current version of this product", 'trx_addons') ),
				"std" => '1.0',
				"type" => "text"
			),
			"screenshot_url" => array(
				"title" => esc_html__("Screenshot URL", 'trx_addons'),
				"desc" => wp_kses_data( __("Select local or specify remote URL with the item's screenshot", 'trx_addons') ),
				"std" => '',
				"type" => "image"
			),
			"demo_url" => array(
				"title" => esc_html__("Product preview URL", 'trx_addons'),
				"desc" => wp_kses_data( __("Specify URL of the item's demo site", 'trx_addons') ),
				"std" => '',
				"type" => "text"
			),
			"download_url" => array(
				"title" => esc_html__("Product download URL", 'trx_addons'),
				"desc" => wp_kses_data( __("The URL for downloading this item, if this item placed on some marketplace. If empty - internal shop is used to sale this item", 'trx_addons') ),
				"std" => '',
				"type" => "text"
			),
			"doc_url" => array(
				"title" => esc_html__("Online documentation URL", 'trx_addons'),
				"desc" => wp_kses_data( __("Specify URL of the item's online documentation", 'trx_addons') ),
				"std" => '',
				"type" => "text"
			),

			"additional_section" => array(
				"title" => esc_html__('Additional details', 'trx_addons'),
				"desc" => wp_kses_data( __('Additional (custom) details for this download', 'trx_addons') ),
				"type" => "section"
			),
			"details" => array(
				"title" => esc_html__("Item details", 'trx_addons'),
				"desc" => wp_kses_data( __("Add more information for this download in Title/Value pairs", 'trx_addons') ),
				"clone" => true,
				"std" => array(array()),
				"type" => "group",
				"fields" => array(
					"title" => array(
						"title" => esc_html__("Title", 'trx_addons'),
						"desc" => wp_kses_data( __('Enter a title for an additional feature', 'trx_addons') ),
						"class" => "trx_addons_column-1_2",
						"std" => "",
						"type" => "text"
					),
					"value" => array(
						"title" => esc_html__("Value", 'trx_addons'),
						"desc" => wp_kses_data( __('Enter a value for an additional feature', 'trx_addons') ),
						"class" => "trx_addons_column-1_2",
						"std" => "",
						"type" => "text"
					)
				)
			),
		) ) );
	}
}

if ( ! function_exists( 'trx_addons_edd_save_post_options' ) ) {
	add_filter( 'trx_addons_filter_save_post_options', 'trx_addons_edd_save_post_options', 10, 3 );
	/**
	 * Save download's slug and date as a separate meta field to use it in the search and sorting queries
	 * 
	 * @hooked trx_addons_filter_save_post_options
	 * 
	 * @param array $options  Options to save
	 * @param int $post_id    Post ID
	 * @param string $post_type Post type
	 * 
	 * @return array  	  Options to save
	 */
	function trx_addons_edd_save_post_options( $options, $post_id, $post_type ) {
		if ( $post_type == TRX_ADDONS_EDD_PT && trx_addons_exists_edd() ) { 
			update_post_meta( $post_id, 'trx_addons_edd_slug', $options['slug'] );
			update_post_meta( $post_id, 'trx_addons_edd_date_created', $options['date_created'] );
			update_post_meta( $post_id, 'trx_addons_edd_date_updated', $options['date_updated'] );
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_edd_post_type_taxonomy' ) ) {
	add_filter( 'trx_addons_filter_post_type_taxonomy',	'trx_addons_edd_post_type_taxonomy', 10, 2 );
	/**
	 * Return a "main" taxonomy for the post type: need if post_type have 2+ taxonomies
	 * 
	 * @hooked trx_addons_filter_post_type_taxonomy
	 *
	 * @param string $tax       Taxonomy name
	 * @param string $post_type Post type name
	 * 
	 * @return string           Taxonomy name
	 */
	function trx_addons_edd_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( $post_type == TRX_ADDONS_EDD_PT ) {
			$tax = TRX_ADDONS_EDD_TAXONOMY_CATEGORY;
		}
		return $tax;
	}
}

if ( ! function_exists( 'trx_addons_edd_create_empty_post_on_404' ) ) {
	add_action( 'wp_head', 'trx_addons_edd_create_empty_post_on_404', 1 );
	/**
	 * Create empty post for 404 page to prevent errors
	 *
	 * @hooked wp_head
	 */
	function trx_addons_edd_create_empty_post_on_404() {
		if ( trx_addons_exists_edd() && is_404() && ! isset( $GLOBALS['post'] ) ) {
			$GLOBALS['post'] = new stdClass();
			$GLOBALS['post']->ID = 0;
			$GLOBALS['post']->post_type = 'unknown';
			$GLOBALS['post']->post_content = '';
		}
	}
}

if ( ! function_exists( 'trx_addons_edd_check_user_before_download' ) ) {
	add_action( 'edd_file_download_has_access', 'trx_addons_edd_check_user_before_download', 100, 3 );
	/**
	 * Check if user has access to download any file
	 *
	 * @hooked edd_file_download_has_access
	 * 
	 * @param boolean $allow  Allow download
	 * @param int $payment    Payment ID
	 * @param array $args     Additional arguments
	 * 
	 * @return boolean        Allow download
	 */
	function trx_addons_edd_check_user_before_download( $allow = false, $payment = 0, $args = array() ) {
		if ( $allow && is_user_logged_in() ) {
			$user = get_current_user_id();
			if ( 0 === $user ) {
				return false;
			}
			$by_user_id = is_numeric( $user ) ? true : false;
			$customer   = new EDD_Customer( $user, $by_user_id );
			if ( !empty( $customer->payment_ids ) ) {
				$payments = array_map( 'absint', explode( ',', $customer->payment_ids ) );
				$allow = in_array($payment, $payments);
			} else {
				$allow = false;
			}
		} else {
			$allow = false;
		}
		if ( ! $allow ) {
			trx_addons_set_front_message( __('You do not have permission to download this file!<br>Please, login and try again!', 'trx_addons'),
										'error',
										true );
			wp_safe_redirect( home_url() );
			exit;
		}
		return $allow;
	}
}

if ( ! function_exists( 'trx_addons_edd_redirect_subscribers' ) ) {
	add_filter( 'login_redirect', 'trx_addons_edd_redirect_subscribers', 100, 3 );
	/**
	 * Redirect subscribers to the purchase history page
	 *
	 * @hooked login_redirect
	 * 
	 * @param string $redirect_to  Redirect URL
	 * @param string $request      Requested URL
	 * @param object $user         User object
	 * 
	 * @return string              Redirect URL
	 */
	function trx_addons_edd_redirect_subscribers( $redirect_to, $request, $user ) {
		if ( trx_addons_exists_edd()
				&& ( is_user_logged_in() || ( $user instanceof WP_User ) )
				&& ( ( $user instanceof WP_User ) && ! $user->has_cap( 'edit_posts' ) )
				&& ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() )
		) {
			$purchase_history = edd_get_option( 'purchase_history_page', 0 );
			return ! empty( $purchase_history ) ? get_permalink( $purchase_history ) : home_url();
		}
		return $redirect_to;
	}
}


// Scripts and styles
//------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_edd_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_edd_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_edd_load_scripts_front', 10, 1 );
	/**
	 * Enqueue scripts and styles for frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_pagebuilder_preview_scripts
	 * 
	 * @trigger trx_addons_action_load_scripts_front
	 *
	 * @param boolean $force  Force to load scripts
	 */
	function trx_addons_edd_load_scripts_front( $force = false ) {
		if ( ! trx_addons_exists_edd() ) {
			return;
		}
		trx_addons_enqueue_optimized( 'edd', $force, array(
			'css'  => array(
				'trx_addons-edd' => array( 'src' => TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/easy-digital-downloads.css' ),
			),
			'js' => array(
				'trx_addons-edd' => array( 'src' => TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/easy-digital-downloads.js', 'deps' => 'jquery' ),
			),
			'need' => trx_addons_is_edd_page(),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_edd_details' ),
				array( 'type' => 'sc',  'sc' => 'trx_sc_edd_add_to_cart' ),
				array( 'type' => 'sc',  'sc' => 'trx_widget_edd_search' ),
				array( 'type' => 'sc',  'sc' => 'download_checkout' ),
				array( 'type' => 'sc',  'sc' => 'download_cart' ),
				array( 'type' => 'sc',  'sc' => 'purchase_link' ),
				array( 'type' => 'sc',  'sc' => 'purchase_history' ),
				array( 'type' => 'sc',  'sc' => 'purchase_collection' ),
				array( 'type' => 'sc',  'sc' => 'downloads' ),
				array( 'type' => 'sc',  'sc' => 'edd_downloads' ),
				array( 'type' => 'sc',  'sc' => 'edd_price' ),
				array( 'type' => 'sc',  'sc' => 'edd_receipt' ),
				array( 'type' => 'sc',  'sc' => 'edd_login' ),
				array( 'type' => 'sc',  'sc' => 'edd_register' ),
				array( 'type' => 'sc',  'sc' => 'edd_profile_editor' ),
				//array( 'type' => 'gb',  'sc' => 'wp:trx-addons/events' ),	// This sc is not exists for GB
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_widget_edd_search"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_edd_' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[download_' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[downloads' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[purchase_' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[edd_' ),
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_edd_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_edd_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_edd', 'trx_addons_edd_load_scripts_front_responsive', 10, 1 );
	/**
	 * Load responsive styles for the frontend
	 * 
	 * @hooked wp_enqueue_scripts
	 * @hooked trx_addons_action_load_scripts_front_edd
	 * 
	 * @param bool $force  Force load styles
	 */
	function trx_addons_edd_load_scripts_front_responsive( $force = false ) {
		if ( ! trx_addons_exists_edd() ) {
			return;
		}
		trx_addons_enqueue_optimized_responsive( 'edd', $force, array(
			'need' => ! trx_addons_is_preview( 'gutenberg' ),
			'css'  => array(
				'trx_addons-edd-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/easy-digital-downloads.responsive.css',
					'media' => 'md'
				),
			),
		) );
	}
}

if ( ! function_exists( 'trx_addons_edd_merge_styles' ) ) {
	add_filter( "trx_addons_filter_merge_styles", 'trx_addons_edd_merge_styles' );
	/**
	 * Merge plugin-specific styles to the single stylesheet to increase page upload speed.
	 * A relative path to the CSS used as a key.
	 * Value 'false' means that the style is not merged to the common stylesheet, but loaded separately.
	 * 
	 * @hooked trx_addons_filter_merge_styles
	 * 
	 * @param array $list  List of stylesheets to merge
	 * 
	 * @return array  Modified list
	 */
	function trx_addons_edd_merge_styles( $list ) {
		if ( trx_addons_exists_edd() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/easy-digital-downloads.css' ] = false;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_edd_merge_styles_responsive' ) ) {
	add_filter( "trx_addons_filter_merge_styles_responsive", 'trx_addons_edd_merge_styles_responsive' );
	/**
	 * Merge responsive styles to the single stylesheet to increase page upload speed.
	 * A relative path to the CSS used as a key.
	 * Value 'false' means that the style is not merged to the common stylesheet, but loaded separately.
	 * 
	 * @hooked trx_addons_filter_merge_styles_responsive
	 * 
	 * @param array $list  List of stylesheets to merge
	 * 
	 * @return array  Modified list
	 */
	function trx_addons_edd_merge_styles_responsive($list) {
		if ( trx_addons_exists_edd() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/easy-digital-downloads.responsive.css' ] = false;
		}
		return $list;
	}
}
	
if ( ! function_exists( 'trx_addons_edd_merge_scripts' ) ) {
	add_action( "trx_addons_filter_merge_scripts", 'trx_addons_edd_merge_scripts' );
	/**
	 * Merge plugin-specific scripts to the single file to increase page upload speed
	 * A relative path to the script used as a key.
	 * Value 'false' means that the script is not merged to the common file, but loaded separately.
	 * 
	 * @hooked trx_addons_filter_merge_scripts
	 * 
	 * @param array $list  List of scripts to merge
	 * 
	 * @return array  Modified list
	 */
	function trx_addons_edd_merge_scripts($list) {
		if ( trx_addons_exists_edd() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/easy-digital-downloads.js' ] = false;
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_edd_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_edd_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_edd_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_edd_check_in_html_output', 10, 1 );
	/**
	 * Check if the plugin's output is present in the page content and force load necessary styles and scripts
	 * 
	 * @hooked trx_addons_action_check_page_content
	 * 
	 * @trigger trx_addons_filter_check_in_html
	 * 
	 * @param string $content  HTML content to check
	 * 
	 * @return string  Checked content
	 */
	function trx_addons_edd_check_in_html_output( $content = '' ) {
		if ( ! trx_addons_exists_edd() ) {
			return $content;
		}
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*(download\\-template\\-|widget_edd_)',
//				'class=[\'"][^\'"]*sc_layouts_cart',
				'class=[\'"][^\'"]*type\\-download',
				'class=[\'"][^\'"]*(download_category|download_tag)\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'edd', $content, $args ) ) {
			trx_addons_edd_load_scripts_front( true );
		}
		return $content;
	}
}

if ( ! function_exists( 'trx_addons_edd_filter_head_output' ) ) {
	add_filter( 'trx_addons_filter_page_head', 'trx_addons_edd_filter_head_output', 10, 1 );
	/**
	 * Remove plugin-specific styles if present in the page head output and optimize CSS loading is set to 'full'
	 * 
	 * @hooked trx_addons_filter_page_head
	 * 
	 * @trigger trx_addons_filter_remove_3rd_party_styles
	 * 
	 * @param string $content  Head content
	 * 
	 * @return string  Modified content
	 */
	function trx_addons_edd_filter_head_output( $content = '' ) {
		if ( ! trx_addons_exists_edd() ) {
			return $content;
		}
		return trx_addons_filter_head_output( 'edd', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/easy-digital-downloads/[^>]*>#'
			)
		) );
	}
}

if ( ! function_exists( 'trx_addons_edd_filter_body_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_edd_filter_body_output', 10, 1 );
	/**
	 * Remove plugin-specific styles if present in the page body output and optimize CSS loading is set to 'full'
	 * 
	 * @hooked trx_addons_filter_page_content
	 * 
	 * @trigger trx_addons_filter_remove_3rd_party_styles
	 * 
	 * @param string $content  Body content
	 * 
	 * @return string  Modified content
	 */
	function trx_addons_edd_filter_body_output( $content = '' ) {
		if ( ! trx_addons_exists_edd() ) {
			return $content;
		}
		return trx_addons_filter_body_output( 'edd', $content, array(
			'check' => array(
				'#<link[^>]*href=[\'"][^\'"]*/easy-digital-downloads/[^>]*>#',
				'#<script[^>]*src=[\'"][^\'"]*/easy-digital-downloads/[^>]*>[\\s\\S]*</script>#U'
			)
		) );
	}
}



// Extended details
//------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_edd_after_download_content' ) ) {
	add_action( 'edd_after_download_content', 'trx_addons_edd_after_download_content', 9, 1 );
	/**
	 * Remove 'Buy' link after the download content if this download placed on the external marketplace.
	 * Show download's details after the content if shortcode 'trx_sc_edd_details' is not present
	 * in the content (temporary disabled)
	 * 
	 * @hooked edd_after_download_content, 9
	 * 
	 * @param int $post_id  Download ID
	 */
	function trx_addons_edd_after_download_content( $post_id = 0 ) {
		if ( trx_addons_is_single() && get_post_type() == TRX_ADDONS_EDD_PT ) {
			// Remove 'Buy' link after the download content if this download placed on the external marketplace
			remove_action( 'edd_after_download_content', 'edd_append_purchase_link' );
			// Show download's details after the content if shortcode 'trx_sc_edd_details' is not present in the content
			if ( false ) {
				if ( $post_id == 0 ) {
					$post_id = get_the_ID();
				}
				if ( strpos( get_the_content(), '[trx_sc_edd_details') === false ) {
					set_query_var( 'trx_addons_args_sc_edd_details', array( 'class' => 'downloads_page_info' ) );
					require_once trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/tpl.edd-details.default.php' );
				}
				// Add buttons
				trx_addons_edd_add_buttons( $post_id );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_edd_add_buttons' ) ) {
	/**
	 * Add buttons 'Live Demo' and 'Purchase' after the download's content.
	 * Show price block and info for single-price items also
	 *
	 * @param int $post_id   Download ID
	 * @param boolean $demo  Show demo button
	 */
	function trx_addons_edd_add_buttons( $post_id = 0, $demo = true ) {
		if ( $post_id == 0 && trx_addons_is_single() && get_post_type() == TRX_ADDONS_EDD_PT ) {
			$post_id = get_the_ID();
		}
		set_query_var( 'trx_addons_edd_demo', $demo );
		?><div class="trx_addons_buttons trx_addons_edd_purchase_buttons"><?php
			$free = edd_is_free_download( $post_id );
			// Show price block and info for single-price items
			if ( trx_addons_is_single() && !edd_has_variable_prices( $post_id ) ) {
				?><div class="trx_addons_edd_purchase_price">
					<div class="trx_addons_edd_purchase_price_label">
						<span class="edd_price_option_name"><?php esc_html_e( 'Regular price', 'trx_addons' ); ?></span><?php
						?><span class="edd_price_option_price"><?php
							if ( $free ) {
								esc_html_e('Free', 'trx_addons');
							} else {
								edd_price( $post_id );
							}
						?></span>
					</div>
				</div><?php
				// Additional info (from shortcode)
				$trx_addons_args = get_query_var('trx_addons_args_sc_edd_add_to_cart');
				trx_addons_show_layout( ! empty( $trx_addons_args['content'] )
											? $trx_addons_args['content'] 
											: ( $free
													? trx_addons_get_option('edd_free_info')
													: trx_addons_get_option('edd_price_info')
												),
										'<div class="trx_addons_edd_purchase_info">',
										'</div>');
			}
			// Add buttons
			$trx_addons_meta = get_post_meta( $post_id, 'trx_addons_options', true );
			if ( ! empty( $trx_addons_meta['download_url'] ) ) {
				?><div class="edd_download_purchase_form"><?php
					?><a href="<?php echo esc_url( trx_addons_add_referals_to_url( $trx_addons_meta['download_url'], trx_addons_get_option( 'edd_referals' ) ) ); ?>" class="sc_button" target="_blank"><?php
						echo true == $free
								? esc_html__('Download', 'trx_addons')
								: wp_kses_data( sprintf( __( '%s - Purchase', 'trx_addons' ), edd_price( $post_id, false ) ) );
					?></a><?php
					trx_addons_edd_add_demo_button( $post_id, array(), $trx_addons_meta );
				?></div><?php
			} else {
				edd_append_purchase_link( $post_id );
			}
		?></div><?php
	}
}

if ( ! function_exists( 'trx_addons_edd_add_buttons_title' ) ) {
	//add_action( 'edd_purchase_link_top', 'trx_addons_edd_add_buttons_title', 9, 2 );
	/**
	 * Add title 'Select item's option to purchase' before the download's content for variable-price items
	 * in the single download's page
	 * 
	 * @hooked edd_purchase_link_top, 9
	 *
	 * @param int $post_id   Download ID
	 * @param array $args    Download arguments
	 */
	function trx_addons_edd_add_buttons_title( $post_id, $args ) {
		if ( trx_addons_is_single() && edd_has_variable_prices( $post_id ) ) {
			?><h5 class="edd_download_purchase_form_title"><?php esc_html_e( "Select item's option to purchase", 'trx_addons' ); ?></h5><?php
		}
	}
}

if ( ! function_exists( 'trx_addons_edd_add_demo_button' ) ) {
	add_action( 'edd_purchase_link_end', 'trx_addons_edd_add_demo_button', 10, 2 );
	/**
	 * Show 'Demo' or 'Details' after the 'Buy now' button
	 * 
	 * @hooked edd_purchase_link_end
	 *
	 * @param int $post_id   Download ID
	 * @param array $args    Download arguments
	 */
	function trx_addons_edd_add_demo_button( $post_id = 0, $args = array(), $trx_addons_meta = false ) {
		$details = ! get_query_var( 'trx_addons_edd_demo', true );
		$url = $details ? get_permalink( $post_id ) : '';
		if ( empty( $url ) ) {
			if ( $trx_addons_meta === false ) {
				$trx_addons_meta = get_post_meta( $post_id, 'trx_addons_options', true );
			}
			$url = defined( 'TRX_ADDONS_DEMO_PARAM' )
						? trx_addons_get_demo_page_link( $trx_addons_meta['slug'] )
						: ( ! empty( $trx_addons_meta['demo_url'] ) ? $trx_addons_meta['demo_url'] : '' );
		}
		if ( ! empty( $url ) ) {
			?><a href="<?php echo esc_url( $url ); ?>" class="sc_button"<?php if ( ! $details ) echo ' target="_blank"'; ?>><?php
				if ( $details ) {
					esc_html_e('View details', 'trx_addons');
				} else {
					esc_html_e('Live demo', 'trx_addons');
				}
			?></a><?php
		}
	}
}

if ( ! function_exists( 'trx_addons_edd_remove_decimals' ) ) {
	add_filter( 'edd_format_amount', 'trx_addons_edd_remove_decimals', 10, 5 );
	/**
	 * Remove decimals from the price if it is integer (2.00 -> 2)
	 * 
	 * @hooked edd_format_amount
	 *
	 * @param string $formatted  Formatted price
	 * @param float $amount      Price
	 * @param int $decimals      Number of decimals
	 * @param string $decimal_sep  Decimal separator
	 * @param string $thousands_sep  Thousands separator
	 * 
	 * @return string  Formatted price
	 */
	function trx_addons_edd_remove_decimals( $formatted, $amount, $decimals, $decimal_sep, $thousands_sep ) {
		return str_replace( $decimal_sep . str_repeat( '0', $decimals ), '', $formatted );
	}
}

if ( ! function_exists( 'trx_addons_edd_edd_purchase_link_args' ) ) {
	add_filter( 'edd_purchase_link_args', 'trx_addons_edd_edd_purchase_link_args', 20 );
	/**
	 * Change text 'Free' to 'Download' in the 'Buy now' button
	 * 
	 * @hooked edd_purchase_link_args
	 *
	 * @param array $args  Download arguments
	 * 
	 * @return array  Download arguments
	 */
	function trx_addons_edd_edd_purchase_link_args( $args ) {
		if ( strpos( $args['text'], __( 'Free', 'trx_addons' ) ) !== false ) {
			$args['text'] = __( 'Download', 'trx_addons' );
		}
		return $args;
	}
}

if ( ! function_exists( 'trx_addons_edd_add_price_selector' ) ) {
	add_action( 'edd_before_price_options', 'trx_addons_edd_add_price_selector', 10, 1 );
	/**
	 * Show Regular | Extended price selector before the options list
	 * 
	 * @hooked edd_before_price_options
	 * 
	 * @trigger edd_purchase_variable_prices
	 * @trigger edd_price_option_output
	 *
	 * @param int $post_id   Download ID
	 */
	function trx_addons_edd_add_price_selector( $post_id = 0 ) {
		// If we've already generated a form ID for this download ID, append -#
		global $edd_displayed_form_ids;
		$form_id = '';
		if ( $edd_displayed_form_ids[ $post_id ] > 1 ) {
			$form_id .= '-' . $edd_displayed_form_ids[ $post_id ];
		}
		$ext_present = false;
		$prices = array();
		if ( edd_has_variable_prices( $post_id ) ) {
			$prices = apply_filters( 'edd_purchase_variable_prices', edd_get_variable_prices( $post_id ), $post_id );
			if ( is_array( $prices ) ) {
				foreach ( $prices as $key => $price ) {
					if ( ! empty( $price['name'] ) && strpos( strtolower( $price['name'] ), 'extended' ) !== false ) {
						$ext_present = true;
						break;
					}
				}
			}
		}
		if ( empty( $prices[1]['name'] ) ) {
			$prices = array(
							1 => array(
								'name' => __('Regular license', 'trx_addons'),
								'amount' => edd_get_download_price( $post_id )
								)
							);
		}
		?><div class="trx_addons_edd_purchase_price<?php echo esc_attr( $ext_present ? ' trx_addons_edd_purchase_price_selector' : '' ); ?>"><?php
			foreach ( $prices as $key => $price ) {
				$free = edd_is_free_download( $post_id, $key );
				$free_label = __('Free', 'trx_addons');
				?><div class="trx_addons_edd_purchase_price_label"><?php
					trx_addons_show_layout( apply_filters('edd_price_option_output',
										 '<span class="edd_price_option_name">' 
											. esc_html( $price['name'] ) 
										. '</span>'
										. '<span class="edd_price_option_price">'
											. esc_html( $free ? $free_label : edd_currency_filter( edd_format_amount( $price['amount'] ) ) )
										. '</span>'
										. (!empty($price['regular_amount'])
											? '<span class="edd_price_option_price"><del>'
												. esc_html( $free ? $free_label : edd_currency_filter( edd_format_amount( $price['regular_amount'] ) ) )
											. '</del></span>'
											: ''
											),
										$post_id, $key, $price, $form_id, '' ) );
				?></div><?php
				break;
			}
			// Prices
			if ( $ext_present ) {
				?><div class="trx_addons_edd_purchase_price_list"><?php
					$num = 0;
					foreach ( $prices as $key => $price ) {
						$num++;
						?><div class="trx_addons_edd_purchase_price_list_item">
							<div class="trx_addons_edd_purchase_price_list_item_label"><?php
								trx_addons_show_layout( apply_filters('edd_price_option_output',
													 '<span class="edd_price_option_name">' 
														. esc_html( $price['name'] ) 
													. '</span>'
													. '<span class="edd_price_option_price">'
														. esc_html( $free ? $free_label : edd_currency_filter( edd_format_amount( $price['amount'] ) ) )
													. '</span>'
													. (!empty($price['regular_amount'])
														? '<span class="edd_price_option_price"><del>'
															. esc_html( $free ? $free_label : edd_currency_filter( edd_format_amount( $price['regular_amount'] ) ) )
														. '</del></span>'
														: ''
														),
													$post_id, $key, $price, $form_id, '' ) );
							?></div><?php
							// Description
							if ( ( $desc = trx_addons_get_option( 'edd_' . ( $num == 1 ? 'regular' : 'extended' ) . '_price_description' ) ) != '' ) {
								?><div class="trx_addons_edd_purchase_price_list_item_description"><?php
									trx_addons_show_layout( $desc );
								?></div><?php
							}
						?></div><?php
						if ( $num == 2 ) break;
					}
				?></div><?php
			}
		?></div><?php
		
		// Additional info (from shortcode)
		$trx_addons_args = get_query_var( 'trx_addons_args_sc_edd_add_to_cart' );
		trx_addons_show_layout( ! empty( $trx_addons_args['content']) 
									? $trx_addons_args['content'] 
									: trx_addons_get_option( 'edd_price_info' ),
								'<div class="trx_addons_edd_purchase_info">',
								'</div>');
	}
}

if ( ! function_exists( 'trx_addons_edd_add_subtotal' ) ) {
	add_action( 'edd_after_price_options_list', 'trx_addons_edd_add_subtotal', 10, 3 );
	/**
	 * Add subtotal after the options list
	 * 
	 * @hooked edd_after_price_options_list
	 * 
	 * @param int $post_id  Download (Post) ID
	 * @param array $prices Array of prices
	 * @param string $type  Type of price options
	 */
	function trx_addons_edd_add_subtotal( $post_id = 0, $prices = array(), $type = '' ) {
	    if ( edd_has_variable_prices( $post_id ) && $type == 'checkbox' ) {
			$old_price = 0;
			$type = edd_get_download_type( $post_id );
			if ( ! get_query_var( 'trx_addons_edd_demo', true ) && $type == 'bundle' ) {
				$list = edd_get_bundled_products( $post_id );
				if ( is_array( $list ) && count( $list ) > 0 ) {
					foreach ( $list as $id ) {
						$old_price += edd_get_download_price( $id );
					}
				}
			}
			?><div class="trx_addons_edd_purchase_subtotal trx_addons_edd_purchase_subtotal_<?php echo esc_attr( $old_price > 0 ? $type : 'default' ); ?>"><?php
				// Title
				?><span class="trx_addons_edd_purchase_subtotal_label"><?php esc_html_e('Subtotal:', 'trx_addons'); ?></span><?php
				// Value
				?><span class="trx_addons_edd_purchase_subtotal_value"><?php edd_price( $post_id ); ?></span><?php
				// Old Value
				if ( $old_price > 0 ) {
					?><span class="trx_addons_edd_purchase_subtotal_value_old"><?php echo esc_html( edd_currency_filter( edd_format_amount( $old_price ) ) ); ?></span><?php
				}
			?></div><?php
		}
	}
}

if ( ! function_exists( 'trx_addons_edd_payment_receipt_after' ) ) {
	add_action( 'edd_payment_receipt_after', 'trx_addons_edd_payment_receipt_after', 10, 2 );
	/**
	 * Add purchase key to the payment receipt
	 * 
	 * @hooked edd_payment_receipt_after
	 * 
	 * @param object $payment  Payment object
	 * @param array $args      Array of arguments
	 */
	function trx_addons_edd_payment_receipt_after( $payment, $args ) {
		$meta = edd_get_payment_meta( $payment->ID );
		if ( ! empty( $meta['key'] ) ) {
			?>
			<tr>
				<th class="edd_receipt_payment_key"><strong><?php esc_html_e( 'Purchase Key', 'trx_addons' ); ?>:</strong></th>
				<th class="edd_receipt_payment_key"><?php echo esc_html( $meta['key'] ); ?></th>
			</tr>
			<?php
		}
	}
}

if ( ! function_exists( 'trx_addons_edd_post_class' ) ) {
	add_filter( 'post_class', 'trx_addons_edd_post_class', 11 );
	/**
	 * Add class 'download_market_[internal|external]' to the <article>
	 * 
	 * @hooked post_class, 11
	 * 
	 * @param array $classes  Array of classes
	 * 
	 * @return array  	  Modified array of classes
	 */
	function trx_addons_edd_post_class( $classes ) {
		if ( get_post_type() == TRX_ADDONS_EDD_PT ) {
			$trx_addons_meta = get_post_meta( get_the_ID(), 'trx_addons_options', true );
			$classes[] = 'download_market_' . ( ! empty( $trx_addons_meta['download_url'] ) ? 'external' : 'internal' );
		}
		return $classes;
	}
}

if ( ! function_exists( 'trx_addons_edd_custom_meta_value' ) ) {
	add_filter( 'trx_addons_filter_custom_meta_value', 'trx_addons_edd_custom_meta_value', 10, 2 );
	/**
	 * Return value of the custom field for the custom blog items: return price for the EDD
	 * 
	 * @hooked trx_addons_filter_custom_meta_value
	 * 
	 * @param string $value  Meta value
	 * @param string $key    Meta key
	 * 
	 * @return string  	  Modified meta value
	 */
	function trx_addons_edd_custom_meta_value( $value, $key ) {
		if ( get_post_type() == TRX_ADDONS_EDD_PT && trx_addons_exists_edd() ) {
			if ( $key == 'price' ) {
				$value = edd_price( get_the_ID(), false );
			}
		}
		return $value;
	}
}


// Query parameters
//------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_edd_query_params' ) ) {
	/**
	 * Parse query params from GET/POST and wp_query_parameters
	 * 
	 * @trigger trx_addons_filter_edd_query_params
	 * 
	 * @param array $params  Array of parameters
	 * 
	 * @return array  	  Modified array of parameters
	 */
	function trx_addons_edd_query_params( $params = array() ) {
		$q_obj = get_queried_object();
		if ( ( $value = trx_addons_get_value_gp( 'themes_keyword' ) ) != '' ) {
			$params['themes_keyword'] = sanitize_text_field( $value );
		}
		if ( ( $value = trx_addons_get_value_gp( 'themes_order' ) ) != '' )	{
			$params['themes_order'] = sanitize_text_field( $value );
		}
		if ( is_tax( TRX_ADDONS_EDD_TAXONOMY_CATEGORY ) ) {
			$params['themes_category'] = (int)$q_obj->term_id;
		} else if ( ( $value = trx_addons_get_value_gp( 'themes_category' ) ) > 0 ) {
			$params['themes_category'] = array_map( 'intval', $value );
		}
		return apply_filters( 'trx_addons_filter_edd_query_params', $params );
	}
}

if ( ! function_exists( 'trx_addons_edd_query_params_to_args' ) ) {
	/**
	 * Add query parameters to the query args
	 * 
	 * @trigger trx_addons_filter_edd_query_params_to_args
	 * 
	 * @param array $params    Array of parameters
	 * @param bool $new_query  Create new query parameters or filter current. Default - false
	 * 
	 * @return array  	  Modified array of parameters
	 */
	function trx_addons_edd_query_params_to_args( $params = array(), $new_query = false ) {
		$params = trx_addons_edd_query_params( $params );
		$args = $keywords = array();
		if ( ! empty( $params['themes_keyword'] ) ) {
			$args['s'] = $params['themes_keyword'];
		}
		if ( ! empty( $params['themes_category'] ) ) {
			$args = trx_addons_query_add_taxonomy( $args, TRX_ADDONS_EDD_TAXONOMY_CATEGORY, $params['themes_category'] );
		}
		if ( ! empty( $params['themes_order'] ) ) {
			$order = explode( '_', $params['themes_order'] );
			if ( count( $order ) == 1 ) {
				$order[] = 'asc';
			}
			if ( $order[0] == 'title' ) {
				$args['orderby'] = 'title';
			} else if ( $order[0] == 'rand' ) {
				$args['orderby'] = 'rand';
			} else if ( $order[0] == 'date' ) {
				$args['orderby'] = 'date';
			}
			if ( ! empty( $args['orderby'] ) ) {
				$args['order'] = $order[1] == 'asc' ? 'ASC' : 'DESC';
			}
		}

		$args = apply_filters( 'trx_addons_filter_edd_query_params_to_args', $args, $params, $new_query );

		// Prepare args for new query (not in 'pre_query')
		if ( $new_query ) {	// && count( $args ) > 0 ) {
			$args = array_merge( array(
						'post_type' => TRX_ADDONS_EDD_PT,
						'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') 
											? array('publish', 'private') 
											: 'publish'
					), $args );
			$page_number = get_query_var('paged') 
								? get_query_var('paged') 
								: ( get_query_var('page') 
									? get_query_var('page') 
									: 1
									);
			if ( $page_number > 1 ) {
				$args['paged'] = $page_number;
				$args['ignore_sticky_posts'] = true;
			}
			$ppp = get_option( 'posts_per_page' );
			if ( (int)$ppp == 0 ) {
				$ppp = 10;
			}
			$args['posts_per_page'] = (int)$ppp;
		}
		return $args;
	}
}

if ( ! function_exists( 'trx_addons_edd_pre_get_posts' ) ) {
	add_action( 'pre_get_posts', 'trx_addons_edd_pre_get_posts' );
	/**
	 * Add query vars to filter posts
	 * 
	 * @hooked pre_get_posts
	 *
	 * @param WP_Query $query  Current query object
	 */
	function trx_addons_edd_pre_get_posts( $query ) {
		if ( ! $query->is_main_query() || is_admin() ) {
			return;
		}
		if ( trx_addons_exists_edd() && $query->get('post_type') == TRX_ADDONS_EDD_PT ) {
			$args = trx_addons_edd_query_params_to_args( array(), (int)trx_addons_get_value_gp('edd_search_query') );
			if ( is_array( $args ) && count( $args ) > 0 ) {
				foreach ( $args as $k => $v ) {
					$query->set( $k, $v );
				}
			}
		}
	}
}
	

// Admin utils
// -----------------------------------------------------------------

if ( ! function_exists( 'trx_addons_edd_add_custom_column' ) ) {
	add_filter( 'manage_edit-' . TRX_ADDONS_EDD_PT . '_columns', 'trx_addons_edd_add_custom_column', 11 );
	/**
	 * Add custom column to the posts list before the 'Price' column
	 * 
	 * @hooked manage_edit-{post_type}_columns, 11
	 *
	 * @param array $columns  Array of columns
	 * 
	 * @return array  	  Modified array of columns
	 */
	function trx_addons_edd_add_custom_column( $columns ){
		if ( trx_addons_exists_edd() ) {
			if ( is_array( $columns ) && count( $columns ) > 0 ) {
				$new_columns = array();
				foreach( $columns as $k => $v ) {
					if ( $k == 'price' ) {
						$new_columns['edd_slug'] = esc_html__('Slug', 'trx_addons');
					}
					$new_columns[$k] = $v;
				}
				$columns = $new_columns;
			}
	    }
		return $columns;
	}
}

if ( ! function_exists( 'trx_addons_edd_fill_custom_column' ) ) {
	add_action( 'manage_' . TRX_ADDONS_EDD_PT . '_posts_custom_column', 'trx_addons_edd_fill_custom_column', 11, 2 );
	/**
	 * Fill custom column in the posts list
	 * 
	 * @hooked manage_{post_type}_posts_custom_column, 11
	 *
	 * @param string $column_name  Name of the column
	 * @param int    $post_id      ID of the current post
	 */
	function trx_addons_edd_fill_custom_column( $column_name = '', $post_id = 0 ) {
		if ( $column_name == 'edd_slug' ) {
			$slug = get_post_meta( $post_id, 'trx_addons_edd_slug', true );
			if ( ! empty( $slug ) ) {
				?><div class="trx_addons_meta_row">
					<span class="trx_addons_meta_label"><?php echo esc_html( $slug ); ?></span>
				</div><?php
			}
		}
	}
}


// Shortcodes support
//------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_edd_add_cart_market' ) ) {
	add_filter( 'trx_addons_sc_cart_market', 'trx_addons_edd_add_cart_market', 10, 2 );
	/**
	 * Add 'Easy Digital Downloads' to the list of supported markets for the Layouts - Cart
	 * 
	 * @hooked trx_addons_sc_cart_market
	 *
	 * @param array  $list  List of cart markets
	 * @param string $sc    Shortcode name
	 * 
	 * @return array        Modified list of cart markets
	 */
	function trx_addons_edd_add_cart_market( $list, $sc ) {
		if ( $sc == 'trx_sc_layouts_cart' && trx_addons_exists_edd() ) {
			$list['edd'] = esc_html__('Easy Digital Downloads', 'trx_addons');
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_edd_add_cart_item_image' ) ) {
	add_filter( 'edd_cart_item', 'trx_addons_edd_add_cart_item_image', 10, 2 );
	/**
	 * Add image to the cart item in the cart widget for downloads
	 * 
	 * @hooked edd_cart_item
	 *
	 * @param string $item  Cart item
	 * @param int    $id    Cart item ID
	 * 
	 * @return string       Modified cart item
	 */
	function trx_addons_edd_add_cart_item_image( $item, $id = 0 ) {
		$item = str_replace( '&nbsp;@&nbsp;', '&nbsp;x&nbsp;', $item );
		if ( $id > 0 ) {
			if ( ( $image = trx_addons_get_attachment_url( get_post_thumbnail_id( $id ), trx_addons_get_thumb_size( 'tiny' ) ) ) != '' ) {
				$wrapper = '<li class="edd-cart-item">';
				$attr = trx_addons_getimagesize( $image );
				$item = str_replace( $wrapper,
									$wrapper . '<span class="edd-cart-item-image"><img src="' . esc_url( $image ) . '"'
											. ( ! empty( $attr[3] ) ? ' ' . trim( $attr[3] ) : '' )
											. ' alt="' . esc_attr__( 'Cart item', 'trx_addons' ) . '"></span>',
									$item
									);
			}
		}
		return $item;
	}
}

if ( ! function_exists( 'trx_addons_edd_login_menu_settings' ) ) {
	add_action( "trx_addons_action_login_menu_settings", 'trx_addons_edd_login_menu_settings' );
	/**
	 * Add 'Purchase history' to the login menu for current user
	 * 
	 * @hooked trx_addons_action_login_menu_settings
	 */
	function trx_addons_edd_login_menu_settings() {
		if ( trx_addons_exists_edd() ) {
			$purchase_history = edd_get_option( 'purchase_history_page', 0 );
			if ( ! empty( $purchase_history ) ) {
				?><li class="menu-item trx_addons_icon-basket"><a href="<?php echo esc_url( get_permalink( $purchase_history ) ); ?>"><span><?php esc_html_e( 'Purchase history', 'trx_addons' ); ?></span></a></li><?php
			}
		}
	}
}


// Add shortcodes
//----------------------------------------------------------------------------

require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/easy-digital-downloads-sc.php';

// Add shortcodes to VC
if ( trx_addons_exists_edd() && trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/easy-digital-downloads-sc-vc.php';
}


// Load widgets
//----------------------------------------------------------------------------

require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/widget.edd-search.php';


// Demo data install
//----------------------------------------------------------------------------

// One-click import support
if ( is_admin() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/easy-digital-downloads-demo-importer.php';
}

// OCDI support
if ( is_admin() && trx_addons_exists_edd() && function_exists( 'trx_addons_exists_ocdi' ) && trx_addons_exists_ocdi() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'easy-digital-downloads/easy-digital-downloads-demo-ocdi.php';
}
