<?php
/**
 * Shortcode: Table (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_table] in the VC shortcodes list
if (!function_exists('trx_addons_sc_table_add_in_vc')) {
	function trx_addons_sc_table_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_table", 'trx_addons_sc_table_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Table extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_table_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_table_add_in_vc_params')) {
	function trx_addons_sc_table_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_table",
				"name" => esc_html__("Table", 'trx_addons'),
				"description" => wp_kses_data( __("Insert a table", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_table',
				"class" => "trx_sc_table",
				'content_element' => true,
				'is_container' => false,
//				'as_child' => array('except' => 'trx_sc_table'),
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'table'), 'trx_sc_table')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "align",
							"heading" => esc_html__("Table alignment", 'trx_addons'),
							"description" => wp_kses_data( __("Select alignment of the table", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"value" => array_flip(trx_addons_get_list_sc_aligns()),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							"param_name" => "width",
							"heading" => esc_html__("Width", 'trx_addons'),
							"description" => wp_kses_data( __("Width of the table", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => '100%',
							"type" => "textfield"
						),
						array(
							'heading' => __( 'Content', 'trx_addons' ),
							"description" => wp_kses_data( __("Content, created with any table-generator, for example: http://www.impressivewebs.com/html-table-code-generator/ or http://html-tables.com/", 'trx_addons') ),
							'param_name' => 'content',
							'value' => '',
							'holder' => 'div',
							'type' => 'textarea_html',
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
				
			), 'trx_sc_table' );
	}
}
