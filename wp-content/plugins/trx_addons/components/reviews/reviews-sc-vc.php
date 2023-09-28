<?php
/**
 * ThemeREX Addons Posts and Comments Reviews (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.47
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_reviews] in the VC shortcodes list
if (!function_exists('trx_addons_sc_reviews_add_in_vc')) {
	function trx_addons_sc_reviews_add_in_vc() {

		if (!trx_addons_reviews_enable()) return;

		if (!trx_addons_exists_vc()) return;

		vc_lean_map( "trx_sc_reviews", 'trx_addons_sc_reviews_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Reviews extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_reviews_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_reviews_add_in_vc_params')) {
	function trx_addons_sc_reviews_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_reviews",
				"name" => esc_html__("Reviews", 'trx_addons'),
				"description" => wp_kses_data( __("Display post reviews block", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_reviews',
				"class" => "trx_sc_reviews",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label' => true,
							"std" => "short",
					        'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_reviews_sc_type_list(), 'trx_sc_reviews')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Alignment of the block in the content", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'dependency' => array(
								'element' => 'type',
								'value' => 'short'
							),
							'admin_label' => true,
							"std" => "right",
							"value" => array_flip( trx_addons_get_list_sc_floats() ),
							"type" => "dropdown"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_reviews' );
	}
}
