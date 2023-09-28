<?php
/**
 * Widget: Twitter (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_widget_twitter] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_twitter_add_in_vc')) {
	function trx_addons_sc_widget_twitter_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map( "trx_widget_twitter", 'trx_addons_sc_widget_twitter_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Twitter extends WPBakeryShortCode {}

	}
	add_action('init', 'trx_addons_sc_widget_twitter_add_in_vc', 20);
}


// Return params
if (!function_exists('trx_addons_sc_widget_twitter_add_in_vc_params')) {
	
	function trx_addons_sc_widget_twitter_add_in_vc_params() {
		
		$params = array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select widget's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "list",
							"admin_label" => true,
							'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('widgets', 'twitter'), 'trx_widget_twitter')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "title",
							"heading" => esc_html__("Widget title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the widget", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "count",
							"heading" => esc_html__("Tweets number", 'trx_addons'),
							"description" => wp_kses_data( __("Tweets number to show in the feed", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => "2",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => 'default'
							),
							"type" => "textfield"
						),
						array(
							"param_name" => "follow",
							"heading" => esc_html__("Show Follow Us", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display Follow Us link below the feed?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array( esc_html__("Show Follow Us", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
					),
					trx_addons_vc_add_slider_param(''),
					array(
						array(
							"param_name" => "back_image",		// Alter name for 'bg_image' in VC (it broke bg_image)
							"heading" => esc_html__("Widget background", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image or write URL from other site for use it as widget background", 'trx_addons') ),
							"type" => "attach_image"
						),
						array(
							"param_name" => "twitter_api",
							"heading" => esc_html__("Twitter API", 'trx_addons'),
							"description" => wp_kses_data( __("Use specified API to get tweets", 'trx_addons') ),
							"std" => "token",
							"admin_label" => true,
							'save_always' => true,
							"value" => array_flip( trx_addons_get_list_sc_twitter_api() ),
							"type" => "dropdown"
						),
						array(
							"param_name" => "username",
							"heading" => esc_html__("Twitter Username", 'trx_addons'),
							"description" => wp_kses_data( __("Twitter Username", 'trx_addons') ),
							"group" => esc_html__('Twitter account', 'trx_addons'),
							"type" => "textfield"
						),

						// Params for API == 'embed'
						array(
							"param_name" => "embed_header",
							"heading" => esc_html__("Show embed header", 'trx_addons'),
							"description" => '',
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'twitter_api',
								'value' => 'embed'
							),
							"std" => "1",
							"value" => array( esc_html__( "Show header", 'trx_addons' ) => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "embed_footer",
							"heading" => esc_html__("Show embed footer", 'trx_addons'),
							"description" => '',
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'twitter_api',
								'value' => 'embed'
							),
							"std" => "1",
							"value" => array( esc_html__( "Show footer", 'trx_addons' ) => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "embed_borders",
							"heading" => esc_html__("Show embed borders", 'trx_addons'),
							"description" => '',
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'twitter_api',
								'value' => 'embed'
							),
							"std" => "1",
							"value" => array( esc_html__( "Show borders", 'trx_addons' ) => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "embed_scrollbar",
							"heading" => esc_html__("Show embed scrollbar", 'trx_addons'),
							"description" => '',
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'twitter_api',
								'value' => 'embed'
							),
							"std" => "1",
							"value" => array( esc_html__( "Show scrollbar", 'trx_addons' ) => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "embed_transparent",
							"heading" => esc_html__("Make embed bg transparent", 'trx_addons'),
							"description" => '',
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'twitter_api',
								'value' => 'embed'
							),
							"std" => "1",
							"value" => array( esc_html__( "Transparent", 'trx_addons' ) => "1" ),
							"type" => "checkbox"
						),


						// End params for API == 'embed'

						// Params for API == 'token'
						array(
							"param_name" => "consumer_key",
							"heading" => esc_html__("Consumer Key", 'trx_addons'),
							"description" => wp_kses_data( __("Specify a Consumer Key from Twitter application", 'trx_addons') ),
							"group" => esc_html__('Twitter account', 'trx_addons'),
							'dependency' => array(
								'element' => 'twitter_api',
								'value' => 'token'
							),
							"type" => "textfield"
						),
						array(
							"param_name" => "consumer_secret",
							"heading" => esc_html__("Consumer Secret", 'trx_addons'),
							"description" => wp_kses_data( __("Specify a Consumer Secret from Twitter application", 'trx_addons') ),
							"group" => esc_html__('Twitter account', 'trx_addons'),
							'dependency' => array(
								'element' => 'twitter_api',
								'value' => 'token'
							),
							"type" => "textfield"
						),
						array(
							"param_name" => "token_key",
							"heading" => esc_html__("Token Key", 'trx_addons'),
							"description" => wp_kses_data( __("Specify a Token Key from Twitter application", 'trx_addons') ),
							"group" => esc_html__('Twitter account', 'trx_addons'),
							'dependency' => array(
								'element' => 'twitter_api',
								'value' => 'token'
							),
							"type" => "textfield"
						),
						array(
							"param_name" => "token_secret",
							"heading" => esc_html__("Token Secret", 'trx_addons'),
							"description" => wp_kses_data( __("Specify a Token Secret from Twitter application", 'trx_addons') ),
							"group" => esc_html__('Twitter account', 'trx_addons'),
							'dependency' => array(
								'element' => 'twitter_api',
								'value' => 'token'
							),
							"type" => "textfield"
						),
						// End params for API == 'token'

						// Params for API == 'bearer'
						array(
							"param_name" => "bearer",
							"heading" => esc_html__("Bearer", 'trx_addons'),
							"description" => wp_kses_data( __("Specify a Bearer authorization token from a Twitter application", 'trx_addons') ),
							"group" => esc_html__('Twitter account', 'trx_addons'),
							'dependency' => array(
								'element' => 'twitter_api',
								'value' => 'bearer'
							),
							"type" => "textfield"
						),
					),
					trx_addons_vc_add_id_param()
				);
		
		$params = trx_addons_vc_add_param_option($params, 'slider', array( 
																		'dependency' => array(
																			'element' => 'type',
																			'value' => 'default'
																			)
																		)
												);

		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_twitter",
				"name" => esc_html__("Widget: Twitter", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with Twitter feed", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_twitter',
				"class" => "trx_widget_twitter",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => $params
			), 'trx_widget_twitter' );
			
	}
}
