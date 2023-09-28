<?php
/**
 * Plugin support: The Events Calendar (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_sc_events_add_in_vc' ) ) {
	add_action( 'init', 'trx_addons_sc_events_add_in_vc', 20 );
	/**
	 * Add [trx_sc_events] to the VC shortcodes list
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_events_add_in_vc() {

		if ( ! trx_addons_exists_tribe_events() ) {
			return;
		}

		if ( ! trx_addons_exists_vc() ) {
			return;
		}

		vc_lean_map( "trx_sc_events", 'trx_addons_sc_events_add_in_vc_params' );
		class WPBakeryShortCode_Trx_Sc_Events extends WPBakeryShortCode {}
	}
}

if ( ! function_exists( 'trx_addons_sc_events_add_in_vc_params' ) ) {
	/**
	 * Return parameters of the shortcode [trx_sc_events] for the shortcode's VC support
	 * 
	 * @trigger trx_addons_sc_map
	 *
	 * @return array  Shortcode's parameters
	 */
	function trx_addons_sc_events_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_events",
				"name" => esc_html__("Events", 'trx_addons'),
				"description" => wp_kses_data( __("Display events from specified group", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_events',
				"class" => "trx_sc_events",
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
							"admin_label" => true,
							"std" => "default",
					        'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('api', 'the-events-calendar'), 'trx_sc_events')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "past",
							"heading" => esc_html__("Past events", 'trx_addons'),
							"description" => wp_kses_data( __("Show the past events if checked, else - show upcoming events", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"std" => "0",
							"value" => array(esc_html__("Show past events", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Events category", 'trx_addons') ),
							"value" => array_merge( array( trx_addons_get_not_selected_text( esc_html__( 'Select category', 'trx_addons' ) ) => 0 ), array_flip( trx_addons_get_list_terms( false, Tribe__Events__Main::TAXONOMY ) ) ),
							"std" => 0,
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_events' );
	}
}
