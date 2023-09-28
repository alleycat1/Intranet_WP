<?php
/**
 * ThemeREX Addons Custom post type: Properties
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants for 'Properties'
if ( ! defined('TRX_ADDONS_CPT_PROPERTIES_PT') ) {
	define('TRX_ADDONS_CPT_PROPERTIES_PT', trx_addons_cpt_param('properties', 'post_type'));
}
if ( ! defined('TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_IMAGE_KEY') ) {
	define('TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_IMAGE_KEY', 'image');
}


// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_properties_init')) {
	add_action( 'init', 'trx_addons_cpt_properties_init' );
	function trx_addons_cpt_properties_init() {
		
		trx_addons_meta_box_register(TRX_ADDONS_CPT_PROPERTIES_PT, array(
			"basic_section" => array(
				"title" => esc_html__('Basic information', 'trx_addons'),
				"desc" => wp_kses_data( __('Basic information about the property', 'trx_addons') ),
				"type" => "section"
			),
			"before_price" => array(
				"title" => esc_html__("Before price", 'trx_addons'),
				"desc" => wp_kses_data( __('Specify any text to display it before the price', 'trx_addons') ),
				"class" => "trx_addons_column-1_4",
				"std" => "",
				"type" => "text"
			),
			"price" => array(
				"title" => esc_html__("Sale or Rent price", 'trx_addons'),
				"desc" => wp_kses_data( __('Specify main price for this property (only digits)', 'trx_addons') ),
				"class" => "trx_addons_column-1_4",
				"std" => "",
				"type" => "text"
			),
			"price2" => array(
				"title" => esc_html__("Second price", 'trx_addons'),
				"desc" => wp_kses_data( __('Optional price for rental or square feet/m (only digits)', 'trx_addons') ),
				"class" => "trx_addons_column-1_4",
				"std" => "",
				"type" => "text"
			),
			"after_price" => array(
				"title" => esc_html__("After price", 'trx_addons'),
				"desc" => wp_kses_data( __('Specify any text to display it after the second price', 'trx_addons') ),
				"class" => "trx_addons_column-1_4",
				"std" => "",
				"type" => "text"
			),
			"area_size" => array(
				"title" => esc_html__("Area size", 'trx_addons'),
				"desc" => wp_kses_data( __('Area size (only digits)', 'trx_addons') ),
				"class" => "trx_addons_column-1_4",
				"std" => "",
				"type" => "text"
			),
			"area_size_prefix" => array(
				"title" => esc_html__("Area size prefix", 'trx_addons'),
				"desc" => wp_kses_data( __('Area size prefix (unit of measurement). Use ^ to display the following digit as an exponent, e.g. m^2', 'trx_addons') ),
				"class" => "trx_addons_column-1_4",
				"std" => "",
				"type" => "text"
			),
			"land_size" => array(
				"title" => esc_html__("Land size", 'trx_addons'),
				"desc" => wp_kses_data( __('Land area size (only digits)', 'trx_addons') ),
				"class" => "trx_addons_column-1_4",
				"std" => "",
				"type" => "text"
			),
			"land_size_prefix" => array(
				"title" => esc_html__("Land size prefix", 'trx_addons'),
				"desc" => wp_kses_data( __('Land area size prefix (unit of measurement). Use ^ to display the following digit as an exponent, e.g. m^2', 'trx_addons') ),
				"class" => "trx_addons_column-1_4",
				"std" => "",
				"type" => "text"
			),
			"bedrooms" => array(
				"title" => esc_html__("Bedrooms", 'trx_addons'),
				"desc" => wp_kses_data( __('Bedrooms number (only digits)', 'trx_addons') ),
				"class" => "trx_addons_column-1_4",
				"std" => 0,
				"options" => trx_addons_get_list_range(0, 10),
				"type" => "select"
			),
			"bathrooms" => array(
				"title" => esc_html__("Bathrooms", 'trx_addons'),
				"desc" => wp_kses_data( __('Bathrooms number (only digits)', 'trx_addons') ),
				"class" => "trx_addons_column-1_4",
				"std" => 0,
				"options" => trx_addons_get_list_range(0, 10),
				"type" => "select"
			),
			"garages" => array(
				"title" => esc_html__("Garages", 'trx_addons'),
				"desc" => wp_kses_data( __('Garages number (only digits)', 'trx_addons') ),
				"class" => "trx_addons_column-1_4",
				"std" => 0,
				"options" => trx_addons_get_list_range(0, 10),
				"type" => "select"
			),
			"garage_size" => array(
				"title" => esc_html__("Garage size", 'trx_addons'),
				"desc" => wp_kses_data( __('Garage size. E.g. "2 auto" or "200 SqFt" or "45 m^2"', 'trx_addons') ),
				"class" => "trx_addons_column-1_4",
				"std" => "",
				"type" => "text"
			),
			"built" => array(
				"title" => esc_html__("Construction / completion date", 'trx_addons'),
				"desc" => wp_kses_data( __('Specify the property construction year (only digits)', 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"type" => "text"
			),
			"id" => array(
				"title" => esc_html__("Property ID", 'trx_addons'),
				"desc" => wp_kses_data( __('Property ID - it will help you search for this property directly', 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"type" => "text"
			),

			"map_section" => array(
				"title" => esc_html__('Location', 'trx_addons'),
				"desc" => wp_kses_data( __('Address and location on the map', 'trx_addons') ),
				"type" => "section"
			),
			"country" => array(
				"title" => esc_html__("Country", 'trx_addons'),
				"desc" => wp_kses_data( __("Select the property's country", 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"options" => array(),
				"type" => "select"
			),
			"state" => array(
				"title" => esc_html__("State", 'trx_addons'),
				"desc" => wp_kses_data( __("Select the property's state", 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"options" => array(),
				"type" => "select"
			),
			"city" => array(
				"title" => esc_html__("City", 'trx_addons'),
				"desc" => wp_kses_data( __("Select the property's city", 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"options" => array(),
				"type" => "select"
			),
			"neighborhood" => array(
				"title" => esc_html__("Neighborhood", 'trx_addons'),
				"desc" => wp_kses_data( __("Select the property's neighborhood", 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"options" => array(),
				"type" => "select"
			),
			"address" => array(
				"title" => esc_html__("Address in the city", 'trx_addons'),
				"desc" => wp_kses_data( __('Specify only street and building number', 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"type" => "text"
			),
			"zip" => array(
				"title" => esc_html__("Zip", 'trx_addons'),
				"desc" => wp_kses_data( __('Zip code', 'trx_addons') ),
				"class" => "trx_addons_column-1_2",
				"std" => "",
				"type" => "text"
			),
			"show_map" => array(
				"title" => esc_html__("Show map", 'trx_addons'),
				"desc" => wp_kses_data( __("Show a map on the single property page", 'trx_addons') ),
				"std" => "0",
				"type" => "checkbox"
			),
			"marker" => array(
				"title" => esc_html__("Map marker", 'trx_addons'),
				"desc" => wp_kses_data( __("Select an image to represent this property on the map. If empty, use a marker for 'Property type' or a default marker", 'trx_addons') ),
				"std" => "",
				"dependency" => array(
					"show_map" => array(1)
				),
				"type" => "image"
			),
			"location" => array(
				"title" => esc_html__("Map location", 'trx_addons'),
				"desc" => wp_kses_data( __('Click on the map or drag marker or find location by address', 'trx_addons') ),
				"std" => "",
				"type" => "map"
			),

			"gallery_section" => array(
				"title" => esc_html__('Gallery', 'trx_addons'),
				"desc" => wp_kses_data( __('Images gallery for this property', 'trx_addons') ),
				"type" => "section"
			),
			"gallery" => array(
				"title" => esc_html__("Images gallery", 'trx_addons'),
				"desc" => wp_kses_data( __("Select images to create a gallery on the single property page. Attention! The gallery is displayed only if the featured image is selected for this post", 'trx_addons') ),
				"std" => "",
				"multiple" => true,
				"type" => "image"
			),
			"video" => array(
				"title" => esc_html__("Video", 'trx_addons'),
				"desc" => wp_kses_data( __('Specify URL with a video from a popular video hosting (Youtube, Vimeo)', 'trx_addons') ),
				"std" => "",
				"type" => "text"
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
			"virtual_tour" => array(
				"title" => esc_html__("Virtual Tour", 'trx_addons'),
				"desc" => wp_kses_data( __('Enter a virtual tour embed code', 'trx_addons') ),
				"std" => "",
				"type" => "textarea"
			),
			"virtual_tour_description" => array(
				"title" => esc_html__("Description", 'trx_addons'),
				"desc" => wp_kses_data( __('Provide a short description for the virtual tour above', 'trx_addons') ),
				"dependency" => array(
					"virtual_tour" => array("not_empty")
				),
				"std" => "",
				"type" => "textarea"
			),
			"attachments" => array(
				"title" => esc_html__("Attachments", 'trx_addons'),
				"desc" => wp_kses_data( __("Select additional files to attach to this post", 'trx_addons') ),
				"std" => "",
				"multiple" => true,
				"type" => "media"
			),
			"attachments_description" => array(
				"title" => esc_html__("Description", 'trx_addons'),
				"desc" => wp_kses_data( __('Provide a short description for the attachments above', 'trx_addons') ),
				"dependency" => array(
					"attachments" => array("not_empty")
				),
				"std" => "",
				"type" => "textarea"
			),

			"floor_section" => array(
				"title" => esc_html__('Floor plans', 'trx_addons'),
				"desc" => wp_kses_data( __('Floor plans with short description', 'trx_addons') ),
				"type" => "section"
			),
			"floor_plans_enable" => array(
				"title" => esc_html__("Display floor plans", 'trx_addons'),
				"desc" => wp_kses_data( __("Show/Hide floor plans on the single page for this property", 'trx_addons') ),
				"std" => "0",
				"type" => "checkbox"
			),
			"floor_plans" => array(
				"title" => esc_html__("Floor plans", 'trx_addons'),
				"desc" => wp_kses_data( __("Floor plan data fields", 'trx_addons') ),
				"dependency" => array(
					"floor_plans_enable" => '1'
				),
				"clone" => true,
				"std" => array(array()),
				"type" => "group",
				"fields" => array(
					"title" => array(
						"title" => esc_html__("Plan title", 'trx_addons'),
						"desc" => wp_kses_data( __('Floor plan title', 'trx_addons') ),
						"class" => "trx_addons_column-1_4",
						"std" => "",
						"type" => "text"
					),
					"area" => array(
						"title" => esc_html__("Floor size", 'trx_addons'),
						"desc" => wp_kses_data( __('Floor area', 'trx_addons') ),
						"class" => "trx_addons_column-1_4",
						"std" => "",
						"type" => "text"
					),
					"bedrooms" => array(
						"title" => esc_html__("Bedrooms", 'trx_addons'),
						"desc" => wp_kses_data( __('Bedrooms number or area', 'trx_addons') ),
						"class" => "trx_addons_column-1_4",
						"std" => "",
						"type" => "text"
					),
					"bathrooms" => array(
						"title" => esc_html__("Bathrooms", 'trx_addons'),
						"desc" => wp_kses_data( __('Bathrooms number or area', 'trx_addons') ),
						"class" => "trx_addons_column-1_4",
						"std" => "",
						"type" => "text"
					),
					"image" => array(
						"title" => esc_html__("Floor plan image", 'trx_addons'),
						"desc" => wp_kses_data( __("Select an image with this floor's plan", 'trx_addons') ),
						"class" => "trx_addons_column-1_2",
						"std" => "",
						"type" => "image"
					),
					"description" => array(
						"title" => esc_html__("Description", 'trx_addons'),
						"desc" => wp_kses_data( __('Provide a short description for this plan (if necessary)', 'trx_addons') ),
						"class" => "trx_addons_column-1_2",
						"std" => "",
						"type" => "textarea"
					)
				)
			),

			"details_section" => array(
				"title" => esc_html__('Additional details', 'trx_addons'),
				"desc" => wp_kses_data( __('Additional (custom) features for this property', 'trx_addons') ),
				"type" => "section"
			),
			"details_enable" => array(
				"title" => esc_html__("Display additional details", 'trx_addons'),
				"desc" => wp_kses_data( __("Show/Hide additional details on the single property page", 'trx_addons') ),
				"std" => "0",
				"type" => "checkbox"
			),
			"details" => array(
				"title" => esc_html__("Additional details", 'trx_addons'),
				"desc" => wp_kses_data( __("Add more information for this car in Title/Value pairs", 'trx_addons') ),
				"dependency" => array(
					"details_enable" => '1'
				),
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

			"agent_section" => array(
				"title" => esc_html__('Agent', 'trx_addons'),
				"desc" => wp_kses_data( __('Agent information', 'trx_addons') ),
				"type" => "section"
			),
			"agent_type" => array(
				"title" => esc_html__("Agent type", 'trx_addons'),
				"desc" => wp_kses_data( __("Select the type of content to be displayed in the contacts block: agent or post author.", 'trx_addons') ),
				"std" => is_user_logged_in() && wp_get_current_user()->has_cap('trx_addons_properties_agent') ? "author" : "agent",
				"options" => array(
					"agent" => esc_html__('Agent', 'trx_addons'),
					"author" => esc_html__('Author', 'trx_addons'),
					"none" => esc_html__('Hide block', 'trx_addons')
				),
				"type" => "radio"
			),
			"agent" => array(
				"title" => esc_html__("Select agent", 'trx_addons'),
				"desc" => wp_kses_data( __("Select an agent", 'trx_addons') ),
				"std" => "0",
				"options" => array(),
				"dependency" => array(
					"agent_type" => array("agent")
				),
				"type" => "select"
			)
		));
		
		// Register post type and taxonomy
		register_post_type(
			TRX_ADDONS_CPT_PROPERTIES_PT,
			apply_filters('trx_addons_filter_register_post_type',
				array(
					'label'               => esc_html__( 'Properties', 'trx_addons' ),
					'description'         => esc_html__( 'Property Description', 'trx_addons' ),
					'labels'              => array(
						'name'                => esc_html__( 'Properties', 'trx_addons' ),
						'singular_name'       => esc_html__( 'Property', 'trx_addons' ),
						'menu_name'           => esc_html__( 'Properties', 'trx_addons' ),
						'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
						'all_items'           => esc_html__( 'All Properties', 'trx_addons' ),
						'view_item'           => esc_html__( 'View Property', 'trx_addons' ),
						'add_new_item'        => esc_html__( 'Add New Property', 'trx_addons' ),
						'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
						'edit_item'           => esc_html__( 'Edit Property', 'trx_addons' ),
						'update_item'         => esc_html__( 'Update Property', 'trx_addons' ),
						'search_items'        => esc_html__( 'Search Property', 'trx_addons' ),
						'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
						'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
					),
					'taxonomies'          => array(
												TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE, 
												TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS,
												TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_FEATURES,
												TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS,
												TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY,
												TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE,
												TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY,
												TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD
												),
					'supports'            => trx_addons_cpt_param('properties', 'supports'),
					'public'              => true,
					'hierarchical'        => false,
					'has_archive'         => true,
					'can_export'          => true,
					'show_in_admin_bar'   => true,
					'show_in_menu'        => true,
					// From WordPress 5.3 'menu_position' must be only integer or null (default)!
					// 'menu_position'       => '53.3',
					'menu_icon'			  => 'dashicons-admin-multisite',
					'map_meta_cap'		  => true,
					'capability_type'     => 'property',
					'rewrite'             => array(
												'slug'         => trx_addons_cpt_param('properties', 'post_type_slug'),
												'with_front'   => false,
												'hierarchical' => false
											)
				),
				TRX_ADDONS_CPT_PROPERTIES_PT
			)
		);
	}
}

// Add capabilities for "Properties"
if (!function_exists('trx_addons_cpt_properties_add_roles_and_caps')) {
	add_action( 'trx_addons_action_add_roles_and_caps', 'trx_addons_cpt_properties_add_roles_and_caps' );
	function trx_addons_cpt_properties_add_roles_and_caps() {
		// Add caps to roles
		trx_addons_add_capabilities( array( 'administrator', 'editor' ), array( 'property' ) );
//		trx_addons_add_capabilities( array( 'author' ), 'property', array( '_others' ) );
//		trx_addons_add_capabilities( array( 'contributor' ), 'property', array( '_others', '_private', '_published', '_terms' ) );
	}
}

// Allow Gutenberg as main editor for this post type
if ( ! function_exists( 'trx_addons_cpt_properties_add_pt_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_pt_to_gutenberg', 'trx_addons_cpt_properties_add_pt_to_gutenberg', 10, 2 );
	function trx_addons_cpt_properties_add_pt_to_gutenberg( $allow, $post_type ) {
		return $allow || $post_type == TRX_ADDONS_CPT_PROPERTIES_PT;
	}
}

// Allow Gutenberg as main editor for taxonomies
if ( ! function_exists( 'trx_addons_cpt_properties_add_taxonomy_to_gutenberg' ) ) {
	add_filter( 'trx_addons_filter_add_taxonomy_to_gutenberg', 'trx_addons_cpt_properties_add_taxonomy_to_gutenberg', 10, 2 );
	function trx_addons_cpt_properties_add_taxonomy_to_gutenberg( $allow, $tax ) {
		return $allow || in_array( $tax, array(
											TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE, 
											TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS,
											TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_FEATURES,
											TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS,
											TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY,
											TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE,
											TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY,
											TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD
											)
									);
	}
}

/* ------------------- Old way - moved to the cpt.php now ---------------------
// Add 'Properties' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_properties_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_cpt_properties_options');
	function trx_addons_cpt_properties_options($options) {

		trx_addons_array_insert_after($options, 'cpt_section', trx_addons_cpt_properties_get_list_options());
		return $options;
	}
}

// Return parameters list for plugin's options
if (!function_exists('trx_addons_cpt_properties_get_list_options')) {
	function trx_addons_cpt_properties_get_list_options($add_parameters=array()) {
		return apply_filters('trx_addons_cpt_list_options', array(
			'properties_info' => array(
				"title" => esc_html__('Properties', 'trx_addons'),
				"desc" => wp_kses_data( __('Settings of the properties archive', 'trx_addons') ),
				"type" => "info"
			),
			'properties_blog_style' => array(
				"title" => esc_html__('Blog archive style', 'trx_addons'),
				"desc" => wp_kses_data( __('Style of the properties archive', 'trx_addons') ),
				"std" => 'default_2',
				"options" => apply_filters('trx_addons_filter_cpt_archive_styles', 
											trx_addons_components_get_allowed_layouts('cpt', 'properties', 'arh'), 
											TRX_ADDONS_CPT_PROPERTIES_PT),
				"type" => "select"
			),
			'properties_single_style' => array(
				"title" => esc_html__('Single property style', 'trx_addons'),
				"desc" => wp_kses_data( __("Style of the single property's page", 'trx_addons') ),
				"std" => 'default',
				"options" => apply_filters('trx_addons_filter_cpt_single_styles', array(
					'default' => esc_html__('Default', 'trx_addons'),
					'tabs' => esc_html__('Tabs', 'trx_addons')
				), TRX_ADDONS_CPT_PROPERTIES_PT),
				"type" => "select"
			),
			'properties_marker' => array(
				"title" => esc_html__('Default marker', 'trx_addons'),
				"desc" => wp_kses_data( __('Default marker to show properties on the map', 'trx_addons') ),
				"std" => '',
				"type" => "image"
			)
		), 'properties');
	}
}
------------------- /Old way --------------------- */


// Fill 'options' arrays when its need in the admin mode
if (!function_exists('trx_addons_cpt_properties_before_show_options')) {
	add_filter('trx_addons_filter_before_show_options', 'trx_addons_cpt_properties_before_show_options', 10, 2);
	function trx_addons_cpt_properties_before_show_options($options, $post_type, $group='') {
		if ($post_type == TRX_ADDONS_CPT_PROPERTIES_PT) {
			foreach ($options as $id=>$field) {

				// Recursive call for options type 'group'
				if ($field['type'] == 'group' && !empty($field['fields'])) {
					$options[$id]['fields'] = trx_addons_cpt_properties_before_show_options($field['fields'], $post_type, $id);
					continue;
				}
				
				// Skip elements without param 'options'
				if (!isset($field['options']) || count($field['options'])>0) continue;

				// Fill the 'country' array
				if ($id == 'country') {
					$options[$id]['options'] = trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY);

				// Fill the 'state' array
				} else if ($id == 'state') {
					$options[$id]['options'] = trx_addons_array_merge(
													array( trx_addons_get_not_selected_text( esc_html__( 'State', 'trx_addons' ) ) ),
													trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE, array(
														'meta_key' => 'country',
														'meta_value' => !empty($options['country']['val'])
																			? $options['country']['val']
																			: trx_addons_array_get_first($options['country']['options'])
														)));

				// Fill the 'city' array
				} else if ($id == 'city') {
					if ($options['state']['val'] > 0)
						$args = array(
										'meta_query' => array(
											array(
												'type' => 'NUMERIC',
												'key' => 'country',
												'value' => !empty($options['country']['val'])
																? $options['country']['val']
																: trx_addons_array_get_first($options['country']['options'])
											),
											array(
												'type' => 'NUMERIC',
												'key' => 'state',
												'value' => $options['state']['val']
											)
										)
									);
					else
						$args = array(
									'meta_key' => 'country',
									'meta_value' => !empty($options['country']['val'])
																? $options['country']['val']
																: trx_addons_array_get_first($options['country']['options'])
									);
					$options[$id]['options'] = trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, $args);

				// Fill the 'neighborhood' array
				} else if ($id == 'neighborhood') {
					$args = array(
									'meta_query' => array(
										array(
											'type' => 'NUMERIC',
											'key' => 'country',
											'value' => !empty($options['country']['val'])
																? $options['country']['val']
																: trx_addons_array_get_first($options['country']['options'])
										),
										array(
											'type' => 'NUMERIC',
											'key' => 'city',
											'value' => !empty($options['city']['val'])
																? $options['city']['val']
																: trx_addons_array_get_first($options['city']['options'])
										)
									)
								);
					if ($options['state']['val'] > 0)
						$args['meta_query'][] = array(
											'type' => 'NUMERIC',
											'key' => 'state',
											'value' => $options['state']['val']
										);
					$options[$id]['options'] = trx_addons_array_merge(
													array( trx_addons_get_not_selected_text( esc_html__( 'Neighborhood', 'trx_addons' ) ) ),
													trx_addons_get_list_terms(false, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD, $args)
													);

				// Fill the 'agent' array
				} else if ($id == 'agent') {
					$options[$id]['options'] = trx_addons_get_list_posts(false, array(
																'post_type' => TRX_ADDONS_CPT_AGENTS_PT,
																'orderby' => 'title',
																'order' => 'ASC'
																)
														);
				}
			}
		}
		return $options;
	}
}


// Save some parameters (like 'price', 'agent', 'id', 'bedrooms', etc.) for search and sorting
// and store 'country', 'state', 'city' and 'neighborhood' as post's terms
if ( !function_exists( 'trx_addons_cpt_properties_save_post_options' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_cpt_properties_save_post_options', 10, 3);
	function trx_addons_cpt_properties_save_post_options($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_CPT_PROPERTIES_PT) {
			global $post;
			// Update post meta and post terms for search and sort
			update_post_meta($post_id, 'trx_addons_properties_price', $options['price']);
			update_post_meta($post_id, 'trx_addons_properties_area_size', $options['area_size']);
			update_post_meta($post_id, 'trx_addons_properties_bathrooms', $options['bathrooms']);
			update_post_meta($post_id, 'trx_addons_properties_bedrooms', $options['bedrooms']);
			update_post_meta($post_id, 'trx_addons_properties_garages', $options['garages']);
			update_post_meta($post_id, 'trx_addons_properties_id', $options['id']);
			update_post_meta($post_id, 'trx_addons_properties_zip', $options['zip']);
			update_post_meta($post_id, 'trx_addons_properties_address', $options['address']);
			update_post_meta($post_id, 'trx_addons_properties_agent', $options['agent_type']=='none' 
																		? 0 
																		: ($options['agent_type']=='agent'
																			? $options['agent']
																			: -get_the_author_meta('ID', !empty($post->ID) && $post->ID==$post_id
																				? $post->post_author
																				: false)
																			)
							);
			wp_set_post_terms($post_id, array((int)$options['country']), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY, false);
			wp_set_post_terms($post_id, array((int)$options['state']), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE, false);
			wp_set_post_terms($post_id, array((int)$options['city']), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, false);
			wp_set_post_terms($post_id, array((int)$options['neighborhood']), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD, false);
			// Update min and max values of the bedrooms, bathrooms, area, price, etc.
			trx_addons_cpt_properties_update_min_max();
		}
		return $options;
	}
}


// Update min and max values of the bedrooms, bathrooms, area, price, etc.
if ( !function_exists( 'trx_addons_cpt_properties_update_min_max' ) ) {
	function trx_addons_cpt_properties_update_min_max() {
		global $wpdb;
		$rez = $wpdb->get_results( "SELECT min(bedrooms.meta_value+0) as bed_min, max(bedrooms.meta_value+0) as bed_max,
										 min(bathrooms.meta_value+0) as bath_min, max(bathrooms.meta_value+0) as bath_max,
										 min(area.meta_value+0.0) as area_min, max(area.meta_value+0.0) as area_max,
										 min(price.meta_value+0.0) as price_min, max(price.meta_value+0.0) as price_max
									FROM {$wpdb->posts}
										INNER JOIN {$wpdb->postmeta} AS bedrooms ON {$wpdb->posts}.ID = bedrooms.post_id
										INNER JOIN {$wpdb->postmeta} AS bathrooms ON {$wpdb->posts}.ID = bathrooms.post_id
										INNER JOIN {$wpdb->postmeta} AS area ON {$wpdb->posts}.ID = area.post_id
										INNER JOIN {$wpdb->postmeta} AS price ON {$wpdb->posts}.ID = price.post_id
									WHERE 1=1
										AND ({$wpdb->posts}.post_status='publish')
										AND bedrooms.meta_key='trx_addons_properties_bedrooms'
										AND bathrooms.meta_key='trx_addons_properties_bathrooms'
										AND area.meta_key='trx_addons_properties_area_size'
										AND price.meta_key='trx_addons_properties_price'",
									ARRAY_A
									);
		update_option('trx_addons_properties_min_max', $rez[0]);
	}
}


// Return min and max values of the bedrooms, bathrooms, area, price, etc.
if ( !function_exists( 'trx_addons_cpt_properties_get_min_max' ) ) {
	function trx_addons_cpt_properties_get_min_max($key='') {
		static $min_max=false;
		if ($min_max === false)
			$min_max = array_merge(array(
									'bed_min' => 0,
									'bed_max' => 10,
									'bath_min' => 0,
									'bath_max' => 10,
									'area_min' => 0,
									'area_max' => 1000,
									'price_min' => 0,
									'price_max' => 1000000
									),
								get_option('trx_addons_properties_min_max', array())
								);
		return empty($key) ? $min_max : $min_max[$key];
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_properties_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_cpt_properties_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	add_action( "trx_addons_action_pagebuilder_preview_scripts", 'trx_addons_cpt_properties_load_scripts_front', 10, 1 );
	function trx_addons_cpt_properties_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'cpt_properties', $force, array(
			'css'  => array(
				'trx_addons-cpt_properties' => array( 'src' => TRX_ADDONS_PLUGIN_CPT . 'properties/properties.css' ),
			),
			'js' => array(
				'trx_addons-cpt_properties' => array( 'src' => TRX_ADDONS_PLUGIN_CPT . 'properties/properties.js', 'deps' => 'jquery' ),
			),
			'need' => trx_addons_is_properties_page(),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_properties' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/properties' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_properties"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_properties' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_cpt_properties_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_cpt_properties_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_cpt_properties', 'trx_addons_cpt_properties_load_scripts_front_responsive', 10, 1 );
	function trx_addons_cpt_properties_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'cpt_properties', $force, array(
			'css'  => array(
				'trx_addons-cpt_properties-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_CPT . 'properties/properties.responsive.css',
					'media' => 'xl'
				),
			),
		) );
	}
}
	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cpt_properties_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_cpt_properties_merge_styles');
	function trx_addons_cpt_properties_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'properties/properties.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_cpt_properties_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_cpt_properties_merge_styles_responsive');
	function trx_addons_cpt_properties_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'properties/properties.responsive.css' ] = false;
		return $list;
	}
}
	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_cpt_properties_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_cpt_properties_merge_scripts');
	function trx_addons_cpt_properties_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT . 'properties/properties.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_cpt_properties_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_cpt_properties_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_cpt_properties_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_cpt_properties_check_in_html_output', 10, 1 );
	function trx_addons_cpt_properties_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_properties',
				'class=[\'"][^\'"]*type\\-(' . TRX_ADDONS_CPT_PROPERTIES_PT . '|' . TRX_ADDONS_CPT_AGENTS_PT . ')',
				'class=[\'"][^\'"]*(' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS
								. '|' . TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_FEATURES
								. '|' . TRX_ADDONS_CPT_AGENTS_TAXONOMY
								. ')\\-',
			)
		);
		if ( trx_addons_check_in_html_output( 'cpt_properties', $content, $args ) ) {
			trx_addons_cpt_properties_load_scripts_front( true );
		}
		return $content;
	}
}


// Load required styles and scripts for the backend
if ( !function_exists( 'trx_addons_cpt_properties_load_scripts_admin' ) ) {
	add_action("admin_enqueue_scripts", 'trx_addons_cpt_properties_load_scripts_admin');
	function trx_addons_cpt_properties_load_scripts_admin() {
		wp_enqueue_script('trx_addons-cpt_properties', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'properties/properties.admin.js'), array('jquery'), null, true );
	}
}


// Return true if it's properties page
if ( !function_exists( 'trx_addons_is_properties_page' ) ) {
	function trx_addons_is_properties_page() {
		return defined('TRX_ADDONS_CPT_PROPERTIES_PT') 
					&& !is_search()
					&& (
						(trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_PROPERTIES_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_PROPERTIES_PT)
						|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE)
						|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS)
						|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_FEATURES)
						|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS)
						|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY)
						|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE)
						|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY)
						|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD)
						);
	}
}


// Return taxonomy for the current post type
if ( !function_exists( 'trx_addons_cpt_properties_post_type_taxonomy' ) ) {
	add_filter( 'trx_addons_filter_post_type_taxonomy',	'trx_addons_cpt_properties_post_type_taxonomy', 10, 2 );
	function trx_addons_cpt_properties_post_type_taxonomy($tax='', $post_type='') {
		if ( defined('TRX_ADDONS_CPT_PROPERTIES_PT') && $post_type == TRX_ADDONS_CPT_PROPERTIES_PT )
			$tax = TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE;
		return $tax;
	}
}


// Return link to the all posts for the breadcrumbs
if ( !function_exists( 'trx_addons_cpt_properties_get_blog_all_posts_link' ) ) {
	add_filter('trx_addons_filter_get_blog_all_posts_link', 'trx_addons_cpt_properties_get_blog_all_posts_link', 10, 2);
	function trx_addons_cpt_properties_get_blog_all_posts_link($link='', $args=array()) {
		if ($link=='') {
			if (trx_addons_is_properties_page() 
				&& (!is_post_type_archive(TRX_ADDONS_CPT_PROPERTIES_PT) || (int) trx_addons_get_value_gp('compare') == 1)) {
				if (($url = get_post_type_archive_link( TRX_ADDONS_CPT_PROPERTIES_PT )) != '') {
					$obj = get_post_type_object(TRX_ADDONS_CPT_PROPERTIES_PT);
					$link = '<a href="'.esc_url($url).'">' . esc_html($obj->labels->all_items) . '</a>';
				}
			}
		}
		return $link;
	}
}


// Return current page title
if ( !function_exists( 'trx_addons_cpt_properties_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_cpt_properties_get_blog_title');
	function trx_addons_cpt_properties_get_blog_title($title='') {
		if ( defined('TRX_ADDONS_CPT_PROPERTIES_PT') && is_post_type_archive(TRX_ADDONS_CPT_PROPERTIES_PT) && (int) trx_addons_get_value_gp('compare') == 1) {
			$title = esc_html__('Compare Properties', 'trx_addons');
		}
		return $title;
	}
}


// Parse query params from GET/POST and wp_query_parameters
if ( !function_exists( 'trx_addons_cpt_properties_query_params' ) ) {
	function trx_addons_cpt_properties_query_params($params=array()) {
		$q_obj = get_queried_object();
		if ( ($value = trx_addons_get_value_gp('properties_keyword')) != '' )	$params['properties_keyword'] = sanitize_text_field($value);
		if ( ($value = trx_addons_get_value_gp('properties_order')) != '' )		$params['properties_order'] = sanitize_text_field($value);
		if ( trx_addons_is_single() && get_post_type()==TRX_ADDONS_CPT_AGENTS_PT)			$params['properties_agent'] = (int) $q_obj->ID;
		else if ( ($value = trx_addons_get_value_gp('properties_agent')) > 0 )	$params['properties_agent'] = (int) $value;
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD))			$params['properties_neighborhood'] = (int) $q_obj->term_id;
		else if ( ($value = trx_addons_get_value_gp('properties_neighborhood')) > 0) $params['properties_neighborhood'] = (int) $value;
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY))					$params['properties_city'] = (int) $q_obj->term_id;
		else if ( ($value = trx_addons_get_value_gp('properties_city')) > 0 )	$params['properties_city'] = (int) $value;
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE))					$params['properties_state'] = (int) $q_obj->term_id;
		else if ( ($value = trx_addons_get_value_gp('properties_state')) > 0 )	$params['properties_state'] = (int) $value;
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY))				$params['properties_country'] = (int) $q_obj->term_id;
		else if ( ($value = trx_addons_get_value_gp('properties_country')) > 0 )$params['properties_country'] = (int) $value;
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE))					$params['properties_type'] = (int) $q_obj->term_id;
		else if ( ($value = trx_addons_get_value_gp('properties_type')) > 0 )	$params['properties_type'] = (int) $value;
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS))					$params['properties_status'] = (int) $q_obj->term_id;
		else if ( ($value = trx_addons_get_value_gp('properties_status')) > 0 )	$params['properties_status'] = (int) $value;
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS))					$params['properties_labels'] = (int) $q_obj->term_id;
		else if ( ($value = trx_addons_get_value_gp('properties_labels')) > 0 )	$params['properties_labels'] = (int) $value;
		if ( ($value = trx_addons_get_value_gp('properties_bedrooms')) != '' )	$params['properties_bedrooms'] = sanitize_text_field($value);
		if ( ($value = trx_addons_get_value_gp('properties_bathrooms')) != '' )	$params['properties_bathrooms'] = sanitize_text_field($value);
		if ( ($value = trx_addons_get_value_gp('properties_area')) != '' )		$params['properties_area'] = sanitize_text_field($value);
		if ( ($value = trx_addons_get_value_gp('properties_price')) != '' )		$params['properties_price'] = sanitize_text_field($value);
		// Collect properties_features_xxx to the single param
		foreach ($_GET as $k=>$v) {
			if ( strpos($k, 'properties_features') === 0 ) {
				if (!isset($params['properties_features'])) $params['properties_features'] = array();
				$params['properties_features'][] = (int) $v;
			}
		}
		return apply_filters( 'trx_addons_filter_cpt_properties_query_params', $params );
	}
}


// Make new query to search properties or return $wp_query object if haven't search parameters
if ( !function_exists( 'trx_addons_cpt_properties_query_params_to_args' ) ) {
	function trx_addons_cpt_properties_query_params_to_args($params=array(), $new_query=false) {
		$params = trx_addons_cpt_properties_query_params($params);

		$args = $keywords = array();
		
		// Use only closest location
		if (!empty($params['properties_neighborhood']))
			$args = trx_addons_query_add_taxonomy($args, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD, $params['properties_neighborhood']);
		else if (!empty($params['properties_city']))
			$args = trx_addons_query_add_taxonomy($args, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY, $params['properties_city']);
		else if (!empty($params['properties_state']))
			$args = trx_addons_query_add_taxonomy($args, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE, $params['properties_state']);
		else if (!empty($params['properties_country']))
			$args = trx_addons_query_add_taxonomy($args, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY, $params['properties_country']);

		// Other params
		if (!empty($params['properties_agent']))
			$args = trx_addons_query_add_meta($args, 'trx_addons_properties_agent', $params['properties_agent']);
		if (!empty($params['properties_type']))
			$args = trx_addons_query_add_taxonomy($args, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE, $params['properties_type']);
		if (!empty($params['properties_status']))
			$args = trx_addons_query_add_taxonomy($args, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS, $params['properties_status']);
		if (!empty($params['properties_labels']))
			$args = trx_addons_query_add_taxonomy($args, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS, $params['properties_labels']);
		if (!empty($params['properties_features']))
			foreach ($params['properties_features'] as $v)
				$args = trx_addons_query_add_taxonomy($args, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_FEATURES, $v);
		if (!empty($params['properties_bedrooms']))
			if ($params['properties_bedrooms']!=trx_addons_cpt_properties_get_min_max('bed_min').','.trx_addons_cpt_properties_get_min_max('bed_max'))
				$args = trx_addons_query_add_meta($args, 'trx_addons_properties_bedrooms', $params['properties_bedrooms']);
		if (!empty($params['properties_bathrooms']))
			if ($params['properties_bathrooms']!=trx_addons_cpt_properties_get_min_max('bath_min').','.trx_addons_cpt_properties_get_min_max('bath_max'))
				$args = trx_addons_query_add_meta($args, 'trx_addons_properties_bathrooms', $params['properties_bathrooms']);
		if (!empty($params['properties_area']))
			if ($params['properties_area']!=trx_addons_cpt_properties_get_min_max('area_min').','.trx_addons_cpt_properties_get_min_max('area_max'))
				$args = trx_addons_query_add_meta($args, 'trx_addons_properties_area_size', $params['properties_area']);
		if (!empty($params['properties_price']))
			if ($params['properties_price']!=trx_addons_cpt_properties_get_min_max('price_min').','.trx_addons_cpt_properties_get_min_max('price_max'))
				$args = trx_addons_query_add_meta($args, 'trx_addons_properties_price', $params['properties_price']);
		if (!empty($params['properties_keyword'])) {
			// Search by any string
			// $args['s'] = $params['properties_keyword'];
			// or Search by property's id, zip code or address
			$keywords = array(
				'relation' => 'OR',
				array(
					'key' => 'trx_addons_properties_address',
					'value' => $params['properties_keyword'],
					'type' => 'CHAR',
					'compare' => 'LIKE'
				),
				array(
					'key' => 'trx_addons_properties_zip',
					'value' => $params['properties_keyword'],
					'type' => 'CHAR',
					'compare' => '='
				),
				array(
					'key' => 'trx_addons_properties_id',
					'value' => $params['properties_keyword'],
					'type' => 'CHAR',
					'compare' => '='
				)
			);
		}
		if (!empty($params['properties_order'])) {
			$args['order'] = strpos($params['properties_order'], '_desc') !== false ? 'desc' : 'asc';
			$params['properties_order'] = str_replace(array('_asc', '_desc'), '' , $params['properties_order']);
			if ($params['properties_order'] == 'price') {
				$args['meta_key'] = 'trx_addons_properties_price';
				$args['orderby'] = 'meta_value_num';
			} else if (in_array($params['properties_order'], array('title', 'post_title')))
				$args['orderby'] = 'title';
			else if (in_array($params['properties_order'], array('date', 'post_date')))
				$args['orderby'] = 'date';
			else if ($params['properties_order'] == 'rand')
				$args['orderby'] = 'rand';
		}

		// Add keywords
		if (!empty($keywords)) {
			if (empty($args['meta_query']))
				$args['meta_query'] = $keywords;
			else
				$args['meta_query'] = array(
											'relation' => 'AND',
											$keywords,
											array(
												$args['meta_query']
											)
										);
		}

		// Prepare args for new query (not in 'pre_query')
		if ($new_query) {	// && count($args) > 0) {
			$args = array_merge(array(
						'post_type' => TRX_ADDONS_CPT_PROPERTIES_PT,
						'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') 
											? array('publish', 'private') 
											: 'publish'
					), $args);
			$page_number = get_query_var('paged') 
								? get_query_var('paged') 
								: (get_query_var('page') 
									? get_query_var('page') 
									: 1);
			if ($page_number > 1) {
				$args['paged'] = $page_number;
				$args['ignore_sticky_posts'] = true;
			}
			$ppp = get_option('posts_per_page');
			if ((int) $ppp == 0) $ppp = 10;
			$args['posts_per_page'] = (int) $ppp;
		}

		return apply_filters( 'trx_addons_filter_cpt_properties_query_params_to_args', $args, $params );
	}
}


// Add query vars to filter posts
if (!function_exists('trx_addons_cpt_properties_pre_get_posts')) {
	add_action( 'pre_get_posts', 'trx_addons_cpt_properties_pre_get_posts' );
	function trx_addons_cpt_properties_pre_get_posts($query) {
		if (!$query->is_main_query()) return;

		if ($query->get('post_type') == TRX_ADDONS_CPT_PROPERTIES_PT) {
			
			// Filters and sort for the admin lists
			if (is_admin()) {
				$agent = trx_addons_get_value_gp('agent');
				$query->set('meta_key', 'trx_addons_properties_agent');
				$query->set('meta_value', $agent);

			// Filters and sort for the foreground lists
			} else {
				$args = trx_addons_cpt_properties_query_params_to_args(array(), (int) trx_addons_get_value_gp('properties_query'));
				if (is_array($args) && count($args) > 0) {
					foreach ($args as $k=>$v) {
						$query->set($k, $v);
					}
				} else if ((int) trx_addons_get_value_gp('compare') == 1) {
					$posts = array();
					$list = urldecode(trx_addons_get_value_gpc('trx_addons_properties_compare_list', ''));
					$list = !empty($list) ? json_decode($list, true) : array();
					if (is_array($list)) {
						foreach ($list as $k=>$v) {
							$id = (int) str_replace('id_', '', $k);
							if ($id > 0) $posts[] = $id;
						}
					}
					if (count($posts) > 0) {
						$query->set('post__in', $posts);
					}
				}
			}
		}
	}
}


// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for properties posts
if ( !function_exists( 'trx_addons_cpt_properties_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_properties_single_template');
	function trx_addons_cpt_properties_single_template($template) {
		global $post;
		if (trx_addons_is_single() && $post->post_type == TRX_ADDONS_CPT_PROPERTIES_PT)
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.single.php');
		return $template;
	}
}

// Change standard archive template for properties posts
if ( !function_exists( 'trx_addons_cpt_properties_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_properties_archive_template');
	function trx_addons_cpt_properties_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_PROPERTIES_PT) ) {
			if ((int) trx_addons_get_value_gp('compare') == 1)
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.compare.php');
			else
				$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.archive.php');
		}
		return $template;
	}	
}

// Change standard taxonomy template for properties posts
if ( !function_exists( 'trx_addons_cpt_properties_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_properties_taxonomy_template');
	function trx_addons_cpt_properties_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_NEIGHBORHOOD)
			|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY)
			|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATE)
			|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_COUNTRY)
			|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE)
			|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS)
			|| is_tax(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS)
		) {
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.archive.php');
		}
		return $template;
	}	
}

// Change standard author template to the custom page
if ( ! function_exists( 'trx_addons_cpt_properties_set_author_template' ) ) {
	add_filter( 'author_template', 'trx_addons_cpt_properties_set_author_template', 100 );
	function trx_addons_cpt_properties_set_author_template( $template ) {
		$obj = get_queried_object();
		if ( ! empty( $obj->caps['trx_addons_properties_agent'] ) ) {
			$template = trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.author.php' );
		}
		return $template;
	}	
}


// Show related posts
//--------------------------------------------
if ( !function_exists( 'trx_addons_cpt_properties_related_posts_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_properties_related_posts_after_article', 20, 1);
	function trx_addons_cpt_properties_related_posts_after_article( $mode ) {
		if ($mode == 'properties.single' && apply_filters('trx_addons_filter_show_related_posts_after_article', true)) {
			do_action('trx_addons_action_related_posts', $mode);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_properties_related_posts_show' ) ) {
	add_filter('trx_addons_filter_show_related_posts', 'trx_addons_cpt_properties_related_posts_show');
	function trx_addons_cpt_properties_related_posts_show( $show ) {
		if (!$show && trx_addons_is_single() && get_post_type() == TRX_ADDONS_CPT_PROPERTIES_PT) {
			do_action('trx_addons_action_related_posts', 'properties.single');
			$show = true;
		}
		return $show;
	}
}

if ( !function_exists( 'trx_addons_cpt_properties_related_posts' ) ) {
	add_action('trx_addons_action_related_posts', 'trx_addons_cpt_properties_related_posts', 10, 1);
	function trx_addons_cpt_properties_related_posts( $mode ) {
		if ($mode == 'properties.single') {
			$trx_addons_related_style   = explode('_', trx_addons_get_option('properties_blog_style'));
			$trx_addons_related_type    = $trx_addons_related_style[0];
			$trx_addons_related_columns = empty($trx_addons_related_style[1]) ? 1 : max(1, $trx_addons_related_style[1]);

			trx_addons_get_template_part( apply_filters( 'trx_addons_filter_posts_related_template', 'templates/tpl.posts-related.php', 'properties' ),
												'trx_addons_args_related',
												apply_filters('trx_addons_filter_args_related', array(
																	'class' => 'properties_page_related sc_properties sc_properties_'.esc_attr($trx_addons_related_type),
																	'posts_per_page' => $trx_addons_related_columns,
																	'columns' => $trx_addons_related_columns,
																	'template' => TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.'.trim($trx_addons_related_type).'-item.php',
																	'template_args_name' => 'trx_addons_args_sc_properties',
																	'post_type' => TRX_ADDONS_CPT_PROPERTIES_PT,
																	'taxonomies' => array(TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY)	//, TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE)
																	)
															)
											);
		}
	}
}



// Admin utils
// -----------------------------------------------------------------

// Create additional column in the posts list
if ( ! function_exists('trx_addons_cpt_properties_add_custom_column' ) ) {
	add_filter('manage_edit-'.trx_addons_cpt_param('properties', 'post_type').'_columns', 'trx_addons_cpt_properties_add_custom_column', 9);
	function trx_addons_cpt_properties_add_custom_column( $columns ){
		if (is_array($columns) && count($columns)>0) {
			$new_columns = array();
			foreach( $columns as $k => $v ) {
				if ( ! in_array( $k, array( 'author', 'comments', 'date', 'taxonomy-'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY ) ) ) {
					$new_columns[$k] = $v;
				}
				if ( $k == 'title' ) {
					$new_columns['cpt_properties_image'] = esc_html__('Photo', 'trx_addons');
					$new_columns['cpt_properties_id'] = esc_html__('ID', 'trx_addons');
					$new_columns['taxonomy-'.TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_CITY] = esc_html__('City', 'trx_addons');
				}
			}
			$new_columns['cpt_properties_price'] = esc_html__('Price', 'trx_addons');
			$new_columns['cpt_properties_details'] = esc_html__('Details', 'trx_addons');
			if ( ! empty( $columns['author'] ) && current_user_can( sprintf( 'edit_others_%ss', 'property' ) ) ) {
				$new_columns['author'] = $columns['author'];
			}
			$columns = $new_columns;
		}
		return $columns;
	}
}

// Fill custom columns in the posts list
if ( ! function_exists( 'trx_addons_cpt_properties_fill_custom_column' ) ) {
	add_action( 'manage_'.trx_addons_cpt_param('properties', 'post_type').'_posts_custom_column', 'trx_addons_cpt_properties_fill_custom_column', 9, 2 );
	function trx_addons_cpt_properties_fill_custom_column( $column_name = '', $post_id = 0 ) {
		static $meta_buffer = array();
		if ( empty( $meta_buffer[ $post_id ] ) ) {
			$meta_buffer[ $post_id ] = get_post_meta( $post_id, 'trx_addons_options', true );
		}
		$meta = $meta_buffer[ $post_id ];
		if ( $column_name == 'cpt_properties_image' ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), trx_addons_get_thumb_size('masonry') );
			if ( ! empty( $image[0] ) ) {
				?><img class="trx_addons_cpt_column_image_preview trx_addons_cpt_properties_image_preview" 
						src="<?php echo esc_url($image[0]); ?>" 
						alt="<?php esc_attr_e('Property preview', 'trx_addons'); ?>"<?php
						if (!empty($image[1])) echo ' width="'.intval($image[1]).'"';
						if (!empty($image[2])) echo ' height="'.intval($image[2]).'"';
				?>><?php
			}
		} else if ( $column_name == 'cpt_properties_id' ) {
			if ( ! empty( $meta['id'] ) ) {
				?><div class="trx_addons_meta_row">
					<span class="trx_addons_meta_data"><?php echo esc_html($meta['id']); ?></span>
				</div><?php
			}
		} else if ( $column_name == 'cpt_properties_price' ) {
			if ( ! empty( $meta['price'] ) ) {
				?><div class="trx_addons_meta_row">
					<span class="trx_addons_meta_label"><?php echo wp_kses(trx_addons_prepare_macros($meta['before_price']), 'trx_addons_kses_content'); ?></span>
					<span class="trx_addons_meta_data"><?php echo esc_html(trx_addons_format_price($meta['price'])); ?></span>
					<?php if (empty($meta['price2'])) { ?>
					<span class="trx_addons_meta_label"><?php echo wp_kses(trx_addons_prepare_macros($meta['after_price']), 'trx_addons_kses_content'); ?></span>
					<?php } ?>
				</div><?php
			}
			if ( ! empty( $meta['price2'] ) ) {
				?><div class="trx_addons_meta_row">
				<?php if (empty($meta['price'])) { ?>
					<span class="trx_addons_meta_label"><?php echo wp_kses(trx_addons_prepare_macros($meta['before_price']), 'trx_addons_kses_content'); ?></span>
				<?php } ?>
					<span class="trx_addons_meta_data"><?php echo esc_html(trx_addons_format_price($meta['price2'])); ?></span>
					<span class="trx_addons_meta_label"><?php echo wp_kses(trx_addons_prepare_macros($meta['after_price']), 'trx_addons_kses_content'); ?></span>
				</div><?php
			}
		} else if ( $column_name == 'cpt_properties_details' ) {
			?><div class="trx_addons_meta_row">
				<span class="trx_addons_meta_label"><?php esc_html_e('Published', 'trx_addons'); ?></span>
				<span class="trx_addons_meta_label"><?php echo esc_html(get_the_date()); ?></span>
			</div><?php
			?><div class="trx_addons_meta_row">
				<span class="trx_addons_meta_label"><?php esc_html_e('by', 'trx_addons'); ?></span>
				<span class="trx_addons_meta_label"><?php the_author(); ?></span>
			</div><?php
			if ( $meta['agent_type'] != 'none' && ( $meta['agent_type'] == 'author' || $meta['agent'] != 0 ) ) {
				?><div class="trx_addons_meta_row">
					<span class="trx_addons_meta_label"><?php esc_html_e('Agent', 'trx_addons'); ?></span>
					<span class="trx_addons_meta_label"><a href="<?php
							echo esc_url( get_admin_url( null, 'edit.php?post_type=' . TRX_ADDONS_CPT_PROPERTIES_PT
																	. '&agent=' . ( $meta['agent_type']=='author'
																						? -get_the_author_meta('ID')
																						: intval($meta['agent'])
																					)
														) ); 
							?>"><?php
							if ( $meta['agent_type'] == 'author' ) {
								the_author();
							} else {
								echo esc_html( get_the_title( $meta['agent'] ) );
							}
						?></a>
					</span>
				</div><?php
			}
		}
	}
}


// AJAX handlers of the 'send_form' action
//-----------------------------------------------------------------
if ( !function_exists( 'trx_addons_cpt_properties_ajax_send_sc_form' ) ) {
	// Use 9 priority to early handling action (before standard handler from shortcode 'sc_form')
	add_action('wp_ajax_send_sc_form',			'trx_addons_cpt_properties_ajax_send_sc_form', 9);
	add_action('wp_ajax_nopriv_send_sc_form',	'trx_addons_cpt_properties_ajax_send_sc_form', 9);
	function trx_addons_cpt_properties_ajax_send_sc_form() {

		trx_addons_verify_nonce();
	
		parse_str($_POST['data'], $post_data);
		
		if (empty($post_data['property_agent'])) return;
		$agent_id = (int) $post_data['property_agent'];
		$agent_email = '';
		if ($agent_id > 0) {			// Agent
			$meta = (array)get_post_meta($agent_id, 'trx_addons_options', true);
			$agent_email = $meta['email'];
		} else if ($agent_id < 0) {		// Author
			$user_id = abs($agent_id);
			$user_data = get_userdata($user_id);
			$agent_email = $user_data->user_email;
		}
		if (empty($agent_email)) return;
		
		$property_id = !empty($post_data['property_id']) ? (int) $post_data['property_id'] : 0;
		$property_title = !empty($property_id) ? get_the_title($property_id) : '';

		$response = array('error'=>'');
		
		$user_name	= !empty($post_data['name']) ? stripslashes($post_data['name']) : '';
		$user_email	= !empty($post_data['email']) ? stripslashes($post_data['email']) : '';
		$user_phone	= !empty($post_data['phone']) ? stripslashes($post_data['phone']) : '';
		$user_msg	= !empty($post_data['message']) ? stripslashes($post_data['message']) : '';
		
		// Attention! Strings below not need html-escaping, because mail is a plain text
		$subj = $property_id > 0
					? sprintf(__('Query on property "%s" from "%s"', 'trx_addons'), $property_title, $user_name)
					: sprintf(__('Query on help from "%s"', 'trx_addons'), $user_name);
		$msg = (!empty($user_name)	? "\n".sprintf(__('Name: %s', 'trx_addons'), $user_name) : '')
			.  (!empty($user_email) ? "\n".sprintf(__('E-mail: %s', 'trx_addons'), $user_email) : '')
			.  (!empty($user_phone) ? "\n".sprintf(__('Phone:', 'trx_addons'), $user_phone) : '')
			.  (!empty($user_msg)	? "\n\n".trim($user_msg) : '')
			.  "\n\n............. " . get_bloginfo('site_name') . " (" . esc_url(home_url('/')) . ") ............";

		if (is_email($agent_email) && !wp_mail($agent_email, $subj, $msg)) {
			$response['error'] = esc_html__('Error send message!', 'trx_addons');
		}
	
		trx_addons_ajax_response( $response );
	}
}


// Include additional files
// Attention! Must be included after the post type 'Properties' registration
//----------------------------------------------------------------------------
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "properties/properties.taxonomy_type.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "properties/properties.taxonomy_status.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "properties/properties.taxonomy_features.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "properties/properties.taxonomy_labels.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "properties/properties.taxonomy_country.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "properties/properties.taxonomy_state.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "properties/properties.taxonomy_city.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "properties/properties.taxonomy_neighborhood.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "properties/properties.agents.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "properties/widget.properties_compare.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "properties/widget.properties_sort.php")) != '') { include_once $fdir; }
if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "properties/widget.properties_search.php")) != '') { include_once $fdir; }




// Add shortcodes
//----------------------------------------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'properties/properties-sc.php';

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'properties/properties-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'properties/properties-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT . 'properties/properties-sc-vc.php';
}
