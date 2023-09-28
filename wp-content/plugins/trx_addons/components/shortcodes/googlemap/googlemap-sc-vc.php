<?php
/**
 * Shortcode: Google Map (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Add [trx_sc_googlemap] in the VC shortcodes list
if (!function_exists('trx_addons_sc_googlemap_add_in_vc')) {
	function trx_addons_sc_googlemap_add_in_vc() {

		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_googlemap", 'trx_addons_sc_googlemap_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Googlemap extends WPBakeryShortCodesContainer {}
	}
	add_action('init', 'trx_addons_sc_googlemap_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_googlemap_add_in_vc_params')) {
	function trx_addons_sc_googlemap_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_googlemap",
				"name" => esc_html__("Google Map", 'trx_addons'),
				"description" => wp_kses_data( __("Google map with custom styles and several markers", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_googlemap',
				"class" => "trx_sc_googlemap",
				'content_element' => true,
				'is_container' => true,
				'as_child' => array('except' => 'trx_sc_googlemap'),
				"js_view" => 'VcTrxAddonsContainerView',	//'VcColumnView',
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'googlemap'), 'trx_sc_googlemap')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "style",
							"heading" => esc_html__("Style", 'trx_addons'),
							"description" => wp_kses_data( __("Map's custom style", 'trx_addons') ),
							"admin_label" => true,
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-6',
							"value" => array_flip(trx_addons_get_list_sc_googlemap_styles()),
							"std" => "default",
							"type" => "dropdown"
						),
						array(
							"param_name" => "zoom",
							"heading" => esc_html__("Zoom", 'trx_addons'),
							"description" => wp_kses_data( __("Map zoom factor on a scale from 1 to 20. If assigned the value '0' or left empty, fit the bounds to markers.", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"value" => "16",
							"type" => "textfield"
						),
						array(
							"param_name" => "center",
							"heading" => esc_html__("Center", 'trx_addons'),
							"description" => wp_kses_data( __("Comma separated coordinates of the map's center. If left empty, the coordinates of the first marker will be used.", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "width",
							"heading" => esc_html__("Width", 'trx_addons'),
							"description" => wp_kses_data( __("Width of the element", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => '100%',
							"type" => "textfield"
						),
						array(
							"param_name" => "height",
							"heading" => esc_html__("Height", 'trx_addons'),
							"description" => wp_kses_data( __("Height of the element", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => 350,
							"type" => "textfield"
						),
						array(
							"param_name" => "cluster",
							"heading" => esc_html__("Cluster icon", 'trx_addons'),
							"description" => wp_kses_data( __("Select or upload image for markers clusterer", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"value" => "",
							"type" => "attach_image"
						),
						array(
							"param_name" => "prevent_scroll",
							"heading" => esc_html__("Prevent_scroll", 'trx_addons'),
							"description" => wp_kses_data( __("Disallow scrolling of the map", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"std" => 0,
							"value" => array(esc_html__("Prevent scroll", 'trx_addons') => 1 ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "address",
							"heading" => esc_html__("Address or Lat,Lng", 'trx_addons'),
							"description" => wp_kses_data( __("Specify the address (or comma separated LatLng) if you don't need a unique marker, title or LatLng coordinates. Otherwise, leave this field empty and specify the markers below.", 'trx_addons') ),
							"value" => '',
							"type" => "textfield"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'markers',
							'heading' => esc_html__( 'Markers', 'trx_addons' ),
							"description" => wp_kses_data( __("Add markers to the map", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
								array(
									'title' => esc_html__( 'One', 'trx_addons' ),
									'description' => '',
									'address' => '',
									'animation' => 'none',
									'html' => '',
									'icon' => '',
									'icon_retina' => '',
									'icon_width' => '',
									'icon_height' => '',
								),
							), 'trx_sc_googlemap') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array(
								array(
									"param_name" => "address",
									"heading" => esc_html__("Address or Lat,Lng", 'trx_addons'),
									"description" => wp_kses_data( __("Address (or comma separated coordinates) of this marker", 'trx_addons') ),
									"admin_label" => true,
									"value" => "",
									"type" => "textfield"
								),
								array(
									"param_name" => "html",
									"heading" => esc_html__("Custom HTML", 'trx_addons'),
									"description" => wp_kses_data( __("Custom HTML-code of the marker", 'trx_addons') ),
									"value" => "",
									"type" => "textarea_safe"
								),
								array(
									"param_name" => "icon",
									"heading" => esc_html__("Marker image", 'trx_addons'),
									"description" => wp_kses_data( __("Select or upload image of this marker", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-6 vc_new_row',
									"value" => "",
									"type" => "attach_image"
								),
								array(
									"param_name" => "icon_retina",
									"heading" => esc_html__("Marker for Retina", 'trx_addons'),
									"description" => wp_kses_data( __("Select or upload image of this marker for Retina device", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-6',
									"value" => "",
									"type" => "attach_image"
								),
								array(
									"param_name" => "icon_width",
									"heading" => esc_html__("Width", 'trx_addons'),
									"description" => wp_kses_data( __("Width of this marker. If empty - use original size", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-6 vc_new_row',
									"value" => "",
									"type" => "textfield"
								),
								array(
									"param_name" => "icon_height",
									"heading" => esc_html__("Height", 'trx_addons'),
									"description" => wp_kses_data( __("Height of this marker. If empty - use original size", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-6',
									"value" => "",
									"type" => "textfield"
								),
								array(
									"param_name" => "title",
									"heading" => esc_html__("Title", 'trx_addons'),
									"description" => wp_kses_data( __("Title of the marker", 'trx_addons') ),
									"admin_label" => true,
									'edit_field_class' => 'vc_col-sm-6 vc_new_row',
									"value" => "",
									"type" => "textfield"
								),
								array(
									"param_name" => "animation",
									"heading" => esc_html__("Animation", 'trx_addons'),
									"description" => wp_kses_data( __("Marker's animation", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-6',
									"value" => array_flip(trx_addons_get_list_sc_googlemap_animations()),
									"std" => "none",
									"type" => "dropdown"
								),
								array(
									"param_name" => "description",
									"heading" => esc_html__("Description", 'trx_addons'),
									"description" => wp_kses_data( __("Description of the marker", 'trx_addons') ),
									"value" => "",
									"type" => "textarea_safe"
								)
							), 'trx_sc_googlemap')
						)
					),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
				
			), 'trx_sc_googlemap' );
	}
}
