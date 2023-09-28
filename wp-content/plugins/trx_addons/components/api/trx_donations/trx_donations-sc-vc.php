<?php
/**
 * Plugin support: ThemeREX Donations (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// VC support
//------------------------------------------------------------------------

if ( ! function_exists( 'trx_addons_sc_trx_donations_add_in_vc' ) ) {
	add_action('init', 'trx_addons_sc_trx_donations_add_in_vc', 20);
	/**
	 * Add shortcodes [trx_donations_form], [trx_donations_list] and [trx_donations_info] to the VC shortcodes list
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_trx_donations_add_in_vc() {
	
		if ( ! trx_addons_exists_vc() || ! trx_addons_exists_trx_donations() ) {
			return;
		}

		vc_lean_map( "trx_donations_form", 'trx_addons_sc_trx_donations_add_in_vc_params_df');
		class WPBakeryShortCode_Trx_Donations_Form extends WPBakeryShortCode {}

		vc_lean_map( "trx_donations_list", 'trx_addons_sc_trx_donations_add_in_vc_params_dl');
		class WPBakeryShortCode_Trx_Donations_List extends WPBakeryShortCode {}

		vc_lean_map( "trx_donations_info", 'trx_addons_sc_trx_donations_add_in_vc_params_di');
		class WPBakeryShortCode_Trx_Donations_Info extends WPBakeryShortCode {}
	}
}

if ( ! function_exists( 'trx_addons_sc_trx_donations_add_in_vc_params_df' ) ) {
	/**
	 * Return parameters of the shortcode 'Donations form' for VC
	 * 
	 * @trigger trx_addons_sc_map
	 *
	 * @return array  Shortcode parameters
	 */
	function trx_addons_sc_trx_donations_add_in_vc_params_df() {
		$donations = TRX_DONATIONS::get_instance();
		return apply_filters( 'trx_addons_sc_map', array(
				"base" => "trx_donations_form",
				"name" => esc_html__("Donations form", "trx_addons"),
				"description" => esc_html__("Insert form to allow visitors make donations", "trx_addons"),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				'icon' => 'icon_trx_sc_donations_form',
				"class" => "trx_sc_single trx_sc_donations_form",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "donation",
							"heading" => esc_html__("Donation", 'trx_addons'),
							"description" => wp_kses_data( __("Select donation to display form", 'trx_addons') ),
							"admin_label" => true,
							"value" => array_flip(trx_addons_get_list_posts(false, ['post_type' => TRX_DONATIONS::POST_TYPE, 'not_selected' => false])),
							"std" => "0",
							"type" => "dropdown"
						),
						array(
							"param_name" => "title",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the form", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "subtitle",
							"heading" => esc_html__("Subtitle", 'trx_addons'),
							"description" => wp_kses_data( __("Subtitle of the form", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "description",
							"heading" => esc_html__("Description", 'trx_addons'),
							"description" => wp_kses_data( __("Description of the form", 'trx_addons') ),
							"type" => "textarea_safe"
						),
						array(
							"param_name" => "client_id",
							"heading" => esc_html__("PayPal Client ID", 'trx_addons'),
							"description" => wp_kses_data( __("Client ID from the PayPay application. If empty - used value from ThemeREX Donations options", 'trx_addons') ),
							"group" => esc_html__('PayPal', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-4',
							"type" => "textfield"
						),
						array(
							"param_name" => "amount",
							"heading" => esc_html__("Default amount", 'trx_addons'),
							"description" => wp_kses_data( __("Specify default amount to make donation. If empty - used value from ThemeREX Donations options", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__('PayPal', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "sandbox",
							"heading" => esc_html__("Sandbox", 'trx_addons'),
							"description" => wp_kses_data( __("Enable sandbox mode to testing your payments without real money transfer", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__('PayPal', 'trx_addons'),
							"std" => "",
							"value" => array(
								esc_html__('Inherit', 'trx_addons') => '',
								esc_html__('On', 'trx_addons') => 'on',
								esc_html__('Off', 'trx_addons') => 'off'
							),
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_donations_form' );
	}
}

if ( ! function_exists( 'trx_addons_sc_trx_donations_add_in_vc_params_dl' ) ) {
	/**
	 * Return parameters of the shortcode 'Donations list' for VC
	 * 
	 * @trigger trx_addons_sc_map
	 *
	 * @return array  Shortcode parameters
	 */
	function trx_addons_sc_trx_donations_add_in_vc_params_dl() {
		return apply_filters( 'trx_addons_sc_map', array(
				"base" => "trx_donations_list",
				"name" => esc_html__("Donations list", "trx_addons"),
				"description" => esc_html__("Insert list of donations", "trx_addons"),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				'icon' => 'icon_trx_sc_donations_list',
				"class" => "trx_sc_single trx_sc_donations_list",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Donations category", 'trx_addons') ),
							"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Select category', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, TRX_DONATIONS::TAXONOMY ) ) ),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					array(
						array(
							"param_name" => "title",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the donations list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "subtitle",
							"heading" => esc_html__("Subtitle", 'trx_addons'),
							"description" => wp_kses_data( __("Subtitle of the donations list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "description",
							"heading" => esc_html__("Description", 'trx_addons'),
							"description" => wp_kses_data( __("Description of the donations list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							"type" => "textarea_safe"
						),
						array(
							"param_name" => "link",
							"heading" => esc_html__("Link URL", 'trx_addons'),
							"description" => wp_kses_data( __("Specify URL for the button below list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "link_caption",
							"heading" => esc_html__("Link text", 'trx_addons'),
							"description" => wp_kses_data( __("Specify text for the button below list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_donations_list' );
	}
}

if ( ! function_exists( 'trx_addons_sc_trx_donations_add_in_vc_params_di' ) ) {
	/**
	 * Return parameters of the shortcode 'Donations info' for VC
	 * 
	 * @trigger trx_addons_sc_map
	 *
	 * @return array  Shortcode parameters
	 */
	function trx_addons_sc_trx_donations_add_in_vc_params_di() {
		return apply_filters( 'trx_addons_sc_map', array(
				"base" => "trx_donations_info",
				"name" => esc_html__("Donations info", "trx_addons"),
				"description" => esc_html__("Insert info about selected donation", "trx_addons"),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				'icon' => 'icon_trx_sc_donations_info',
				"class" => "trx_sc_single trx_sc_donations_info",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "donation",
							"heading" => esc_html__("Donation", 'trx_addons'),
							"description" => wp_kses_data( __("Donation", 'trx_addons') ),
							"admin_label" => true,
							"value" => array_flip(trx_addons_get_list_posts(false, ['post_type' => TRX_DONATIONS::POST_TYPE, 'not_selected' => false])),
							"std" => "0",
							"type" => "dropdown"
						),
						array(
							"param_name" => "show_featured",
							"heading" => esc_html__("Show image", 'trx_addons'),
							"description" => wp_kses_data( __("Show featured image", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "1",
							"value" => array(esc_html__("Show featured", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_title",
							"heading" => esc_html__("Show title", 'trx_addons'),
							"description" => wp_kses_data( __("Show title of the donation", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array(esc_html__("Show title", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_excerpt",
							"heading" => esc_html__("Show excerpt", 'trx_addons'),
							"description" => wp_kses_data( __("Show excerpt of the donation", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array(esc_html__("Show excerpt", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_goal",
							"heading" => esc_html__("Show goal", 'trx_addons'),
							"description" => wp_kses_data( __("Show goal value of the donation", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "1",
							"value" => array(esc_html__("Show goal", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_raised",
							"heading" => esc_html__("Show raised", 'trx_addons'),
							"description" => wp_kses_data( __("Show raised value of the donation", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array(esc_html__("Show raised", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_scale",
							"heading" => esc_html__("Show scale", 'trx_addons'),
							"description" => wp_kses_data( __("Show scale with raised value of the donation", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array(esc_html__("Show scale", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_supporters",
							"heading" => esc_html__("Supporters", 'trx_addons'),
							"description" => wp_kses_data( __("How many supporters show in the list", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "3",
					        'save_always' => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "title",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the donations list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "subtitle",
							"heading" => esc_html__("Subtitle", 'trx_addons'),
							"description" => wp_kses_data( __("Subtitle of the donations list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "description",
							"heading" => esc_html__("Description", 'trx_addons'),
							"description" => wp_kses_data( __("Description of the donations list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							"type" => "textarea_safe"
						),
						array(
							"param_name" => "link",
							"heading" => esc_html__("Link URL", 'trx_addons'),
							"description" => wp_kses_data( __("Specify URL for the button below list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							"param_name" => "link_caption",
							"heading" => esc_html__("Link text", 'trx_addons'),
							"description" => wp_kses_data( __("Specify text for the button below list", 'trx_addons') ),
							"group" => esc_html__('Title', 'trx_addons'),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_donations_info' );
	}
}
