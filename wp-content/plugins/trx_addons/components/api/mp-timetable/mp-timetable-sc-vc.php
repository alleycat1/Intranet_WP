<?php
/**
 * Plugin support: MP Timetable (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.30
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_sc_mptt_add_in_vc' ) ) {
	add_action('init', 'trx_addons_sc_mptt_add_in_vc', 20);
	/**
	 * Add a shortcode [mp-timetable] to the VC shortcodes list
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_mptt_add_in_vc() {
		if ( ! trx_addons_exists_vc() || ! trx_addons_exists_mptt() ) {
			return;
		}
		vc_lean_map( "mp-timetable", 'trx_addons_sc_mptt_add_in_vc_params');
		class WPBakeryShortCode_Mp_Timetable extends WPBakeryShortCode {}
	}
}
			
if ( ! function_exists( 'trx_addons_sc_mptt_add_in_vc_params' ) ) {
	/**
	 * Return shortcode's [mp-timetable] parameters for VC 
	 * 
	 * @return array  Shortcode's parameters
	 */
	function trx_addons_sc_mptt_add_in_vc_params() {
		return array(
				"base" => "mp-timetable",
				"name" => esc_html__("MP Timetable", "trx_addons"),
				"description" => esc_html__("Insert timetable with events", "trx_addons"),
				"category" => esc_html__('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_mp_timetable',
				"class" => "trx_sc_single trx_sc_mp_timetable",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "col",
						"heading" => esc_html__("Column", "trx_addons"),
						"description" => esc_html__("Select columns to display", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "",
						"size" => 7,
						"multiple" => true,
						"value" => array_flip(trx_addons_get_list_posts(false, array(
													'post_type' => TRX_ADDONS_MPTT_PT_COLUMN,
													'not_selected' => false,
													'orderby' => 'none'
													)
												)
						),
						"type" => "select"
					),
					array(
						"param_name" => "events",
						"heading" => esc_html__("Events", "trx_addons"),
						"description" => esc_html__("Select events to display", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "",
						"size" => 7,
						"multiple" => true,
						"value" => array_flip(trx_addons_get_list_posts(false, array(
													'post_type' => TRX_ADDONS_MPTT_PT_EVENT,
													'not_selected' => false,
													'orderby' => 'title'
													)
												)
						),
						"type" => "select"
					),
					array(
						"param_name" => "event_categ",
						"heading" => esc_html__("Event categories", "trx_addons"),
						"description" => esc_html__("Select event categories to display", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "",
						"size" => 7,
						"multiple" => true,
						"value" => array_flip(trx_addons_get_list_terms(false, TRX_ADDONS_MPTT_TAXONOMY_CATEGORY)),
						"type" => "select"
					),
					array(
						"param_name" => "increment",
						"heading" => esc_html__("Hour measure", "trx_addons"),
						"description" => esc_html__("Select the time interval for the left column.", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"std" => "1",
						"value" => array(
							__( '4 hours', 'trx_addons' ) => '4',
							__( '2 hours', 'trx_addons' ) => '2',
							__( '1 hour', 'trx_addons' ) => '1',
							__( 'Half hour (30 min)', 'trx_addons' ) => '0.5',
							__( 'Quarter hour (15 min)', 'trx_addons' ) => '0.25'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "view",
						"heading" => esc_html__("Filter style", "trx_addons"),
						"description" => esc_html__("Select the filter style for the content.", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "tabs",
				        'save_always' => true,
						"value" => array(
							__( 'Dropdown list', 'trx_addons' ) => 'dropdown_list',
							__( 'Tabs', 'mp-timetable' ) => 'tabs'
						),
						"type" => "dropdown"
					),
					array(
						"param_name" => "label",
						"heading" => esc_html__("Filter label", "trx_addons"),
						"description" => esc_html__("Specify labels of the block with filters", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "textfield"
					),
					array(
						"param_name" => "hide_label",
						"heading" => esc_html__("Hide 'All Events' view", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want to hide 'All Events' view", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"std" => "0",
				        'save_always' => true,
						"value" => array(esc_html__("Hide 'All Events' view", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "hide_hrs",
						"heading" => esc_html__("Hide first (hours) column", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want to hide the first (hours) column", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "0",
				        'save_always' => true,
						"value" => array(esc_html__("Hide first (hours) column", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "hide_empty_rows",
						"heading" => esc_html__("Hide empty rows", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want to hide empty rows", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "1",
				        'save_always' => true,
						"value" => array(esc_html__("Hide empty rows", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "title",
						"heading" => esc_html__("Title", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want to show the event's title", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"std" => "1",
				        'save_always' => true,
						"value" => array(esc_html__("Show Title", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "sub-title",
						"heading" => esc_html__("Subtitle", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want to show the event's subtitle", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "1",
				        'save_always' => true,
						"value" => array(esc_html__("Show Subtitle", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "time",
						"heading" => esc_html__("Time", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want to show the event's time", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "1",
				        'save_always' => true,
						"value" => array(esc_html__("Show Time", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "description",
						"heading" => esc_html__("Description", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want to show the event's description", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"std" => "1",
				        'save_always' => true,
						"value" => array(esc_html__("Show Description", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "user",
						"heading" => esc_html__("User", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want to show the event's user", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "0",
				        'save_always' => true,
						"value" => array(esc_html__("Show User", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "disable_event_url",
						"heading" => esc_html__("Disable event URL", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want to disable event URL", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "0",
				        'save_always' => true,
						"value" => array(esc_html__("Disable event URL", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "text_align",
						"heading" => esc_html__("Text align", "trx_addons"),
						"description" => esc_html__("Select text alignment", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"std" => "center",
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_sc_aligns(false, false)),
						"type" => "dropdown"
					),
					array(
						"param_name" => "row_height",
						"heading" => esc_html__("Row height", "trx_addons"),
						"description" => esc_html__("Specify row height (in px)", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "textfield"
					),
					array(
						"param_name" => "font_size",
						"heading" => esc_html__("Base font size", "trx_addons"),
						"description" => esc_html__("Specify base font size", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-4',
						"type" => "textfield"
					),
					array(
						"param_name" => "id",
						"heading" => esc_html__("ID", "trx_addons"),
						"description" => esc_html__("Specify block ID (if need)", "trx_addons"),
						'edit_field_class' => 'vc_col-sm-4 vc_new_row',
						"type" => "textfield"
					),
					array(
						"param_name" => "responsive",
						"heading" => esc_html__("Responsive", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want make this block responsive", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"std" => "1",
				        'save_always' => true,
						"value" => array(esc_html__("Responsive", 'trx_addons') => "1" ),
						"type" => "checkbox"
					)
				)
			);
	}
}
