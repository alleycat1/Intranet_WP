<?php
/**
 * Shortcode: Skills (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_skills] in the VC shortcodes list
if (!function_exists('trx_addons_sc_skills_add_in_vc')) {
	function trx_addons_sc_skills_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_skills", 'trx_addons_sc_skills_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Skills extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_skills_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_skills_add_in_vc_params')) {
	function trx_addons_sc_skills_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_skills",
				"name" => esc_html__("Skills", 'trx_addons'),
				"description" => wp_kses_data( __("Skill counters and pie charts", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_skills',
				"class" => "trx_sc_skills",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Type", 'trx_addons'),
							"description" => wp_kses_data( __("Select counter's type", 'trx_addons') ),
							"admin_label" => true,
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-4',
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'skills'), 'trx_sc_skills')),
							"std" => "counter",
							"type" => "dropdown"
						),
						array(
							"param_name" => "style",
							"heading" => esc_html__("Style", 'trx_addons'),
							"description" => wp_kses_data( __("Select counter's style", 'trx_addons') ),
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-4',
							"value" => array_flip(apply_filters('trx_addons_sc_style', trx_addons_get_list_sc_skills_counter_styles(), 'trx_sc_skills')),
							"std" => "counter",
							"type" => "dropdown"
						),
						array(
							"param_name" => "cutout",
							"heading" => esc_html__("Cutout", 'trx_addons'),
							"description" => wp_kses_data( __("Specify the pie cutout radius. Border width = 100% - cutout value.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => 'pie'
							),
							"type" => "textfield"
						),
						array(
							"param_name" => "compact",
							"heading" => esc_html__("Compact pie", 'trx_addons'),
							"description" => wp_kses_data( __("Show all values in one pie or each value in the single pie", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => 'pie'
							),
							"std" => "0",
							"value" => array(esc_html__("Compact", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							'param_name' => 'color',
							'heading' => esc_html__( 'Color', 'trx_addons' ),
							'description' => esc_html__( 'Select custom color to fill each item', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'value' => '',
							'type' => 'colorpicker',
						),
						array(
							'param_name' => 'back_color',	// Alter name for bg_color in VC (it broke bg_color)
							'heading' => esc_html__( 'Background color', 'trx_addons' ),
							'description' => esc_html__( "Select custom color for item's background", 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => 'pie'
							),
							'value' => '',
							'type' => 'colorpicker',
						),
						array(
							'param_name' => 'border_color',
							'heading' => esc_html__( 'Border color', 'trx_addons' ),
							'description' => esc_html__( "Select custom color for item's border", 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'dependency' => array(
								'element' => 'type',
								'value' => 'pie'
							),
							'value' => '',
							'type' => 'colorpicker',
						),
						array(
							'param_name' => 'max',
							'heading' => esc_html__( 'Max. value', 'trx_addons' ),
							'description' => esc_html__( 'Enter max value for all items', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-6 vc_new_row',
							'value' => 100,
							'type' => 'textfield',
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"type" => "textfield"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'values',
							'heading' => esc_html__( 'Values', 'trx_addons' ),
							"description" => wp_kses_data( __("Specify values for each counter", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
								array(
									'title' => esc_html__( 'One', 'trx_addons' ),
									'value' => '60',
									'color' => '',
									'icon' => '',
									'icon_fontawesome' => 'empty',
									'icon_openiconic' => 'empty',
									'icon_typicons' => 'empty',
									'icon_entypo' => 'empty',
									'icon_linecons' => 'empty'
								),
								array(
									'title' => esc_html__( 'Two', 'trx_addons' ),
									'value' => '40',
									'color' => '',
									'icon' => '',
									'icon_fontawesome' => 'empty',
									'icon_openiconic' => 'empty',
									'icon_typicons' => 'empty',
									'icon_entypo' => 'empty',
									'icon_linecons' => 'empty'
								),
							), 'trx_sc_skills') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array_merge(array(
									array(
										'param_name' => 'title',
										'heading' => esc_html__( 'Title', 'trx_addons' ),
										'description' => esc_html__( 'Enter title of this item', 'trx_addons' ),
										'admin_label' => true,
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'color',
										'heading' => esc_html__( 'Color', 'trx_addons' ),
										'description' => esc_html__( 'Select custom color of this item', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'colorpicker',
									),
									array(
										'param_name' => 'value',
										'heading' => esc_html__( 'Value', 'trx_addons' ),
										'description' => esc_html__( 'Enter value of this item', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-4',
										'type' => 'textfield',
									),
								), trx_addons_vc_add_icon_param('')
							), 'trx_sc_skills' )
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_skills' );
	}
}
