<?php
/**
 * Shortcode: Countdown (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_countdown] in the VC shortcodes list
if (!function_exists('trx_addons_sc_countdown_add_in_vc')) {
	function trx_addons_sc_countdown_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_countdown", 'trx_addons_sc_countdown_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Countdown extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_countdown_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_countdown_add_in_vc_params')) {
	function trx_addons_sc_countdown_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_countdown",
				"name" => esc_html__("Countdown", 'trx_addons'),
				"description" => wp_kses_data( __("Put the countdown to the specified date and time", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_countdown',
				"class" => "trx_sc_countdown",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Type", 'trx_addons'),
							"description" => wp_kses_data( __("Select counter's type", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
					        'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'countdown'), 'trx_sc_countdown')),
							"std" => "default",
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the countdown", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "none",
							"value" => array_flip(trx_addons_get_list_sc_aligns()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "count_restart",
							"heading" => esc_html__("Restart counter", 'trx_addons'),
							"description" => wp_kses_data( __("If checked - restart count from/to time on each page loading", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"std" => 0,
							"value" => array(esc_html__("Restart counter", 'trx_addons') => 1 ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "count_to",
							"heading" => esc_html__("Count to", 'trx_addons'),
							"description" => wp_kses_data( __("If checked - date above is a finish date, else - is a start date", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"std" => 1,
							"value" => array(esc_html__("Date above is a finish date", 'trx_addons') => 1 ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "date",
							"heading" => esc_html__("Date", 'trx_addons'),
							"description" => wp_kses_data( __("Target date. Attention! Write the date in the format: yyyy-mm-dd", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'value' => '',
							"type" => "textfield",
							'dependency' => array(
								'element' => 'count_restart',
								'value' => '0'
							)							
						),
						array(
							'param_name' => 'time',
							'heading' => esc_html__( 'Time', 'trx_addons' ),
							'description' => esc_html__( 'Target time. Attention! Put the time in the 24-hours format: HH:mm:ss', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-6',
							'value' => '',
							'type' => 'textfield',
							'dependency' => array(
								'element' => 'count_restart',
								'value' => '0'
							)							
						),
						array(
							'param_name' => 'date_time_restart',
							'heading' => esc_html__( 'Time to restart', 'trx_addons' ),
							'description' => esc_html__( 'Specify start value of timer with format "[DD:]HH:MM[:SS]"', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-6',
							'value' => '',
							'type' => 'textfield',
							'dependency' => array(
								'element' => 'count_restart',
								'value' => '1'
							)							
						),
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_countdown' );
	}
}
