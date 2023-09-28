<?php
/**
 * ThemeREX Addons: Sports Reviews Management (SRM).
 *                  Support different sports, championships, rounds, matches and players.
 *                  (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_matches] in the VC shortcodes list
//--------------------------------------------------------
if (!function_exists('trx_addons_sc_matches_add_in_vc')) {
	function trx_addons_sc_matches_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_matches", 'trx_addons_sc_matches_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Matches extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_matches_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_matches_add_in_vc_params')) {
	function trx_addons_sc_matches_add_in_vc_params() {
		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_sc_matches');
		// Prepare lists
		$sports_list = trx_addons_get_list_terms(false, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
		$sport_default = trx_addons_get_option('sport_favorite');
		$sport = $vc_edit && !empty($vc_params['sport']) ? $vc_params['sport'] : $sport_default;
		if (empty($sport) && count($sports_list) > 0) {
			$keys = array_keys($sports_list);
			$sport = $keys[0];
		}
		$competitions_list = trx_addons_get_list_posts(false, array(
														'post_type' => TRX_ADDONS_CPT_COMPETITIONS_PT,
														'taxonomy' => TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY,
														'taxonomy_value' => $sport,
														'meta_key' => 'trx_addons_competition_date',
														'orderby' => 'meta_value',
														'order' => 'ASC',
														'not_selected' => false
														));
		$competition = $vc_edit && !empty($vc_params['competition']) ? $vc_params['competition'] : '';
		if ((empty($competition) || !isset($competitions_list[$competition])) && count($competitions_list) > 0) {
			$keys = array_keys($competitions_list);
			$competition = $keys[0];
		}
		$rounds_list = trx_addons_array_merge(array(
											'last' => esc_html__('Last round', 'trx_addons'),
											'next' => esc_html__('Next round', 'trx_addons')
											), trx_addons_get_list_posts(false, array(
														'post_type' => TRX_ADDONS_CPT_ROUNDS_PT,
														'post_parent' => $competition,
														'meta_key' => 'trx_addons_round_date',
														'orderby' => 'meta_value',
														'order' => 'ASC',
														'not_selected' => false
														)
											)
						);
		
		$params = array_merge(
				array(
					array(
						"param_name" => "type",
						"heading" => esc_html__("Layout", 'trx_addons'),
						"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
						"admin_label" => true,
						"std" => "default",
						"value" => array_flip(apply_filters('trx_addons_sc_type', array(
							'default' => esc_html__('Default', 'trx_addons')
						), 'trx_sc_matches')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "sport",
						"heading" => esc_html__("Sport", 'trx_addons'),
						"description" => wp_kses_data( __("Select Sport to display matches", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						"std" => $sport_default,
				        'save_always' => true,
						"value" => array_flip($sports_list),
						"type" => "dropdown"
					),
					array(
						"param_name" => "competition",
						"heading" => esc_html__("Competition", 'trx_addons'),
						"description" => wp_kses_data( __("Select competition to display matches", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_flip($competitions_list),
						"type" => "dropdown"
					),
					array(
						"param_name" => "round",
						"heading" => esc_html__("Round", 'trx_addons'),
						"description" => wp_kses_data( __("Select round to display matches", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_flip($rounds_list),
						"type" => "dropdown"
					),
					array(
						"param_name" => "main_matches",
						"heading" => esc_html__("Main matches", 'trx_addons'),
						"description" => wp_kses_data( __("Show large items marked as main match of the round", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						"std" => 0,
						"value" => array(esc_html__("Main matches", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "position",
						"heading" => esc_html__("Position of the matches list", 'trx_addons'),
						"description" => wp_kses_data( __("Select the position of the matches list", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						'dependency' => array(
							'element' => 'main_matches',
							'not_empty' => true
						),
						"std" => "top",
						"value" => array_flip(trx_addons_get_list_sc_matches_positions()),
						"type" => "dropdown"
					),
					array(
						"param_name" => "slider",
						"heading" => esc_html__("Slider", 'trx_addons'),
						"description" => wp_kses_data( __("Show main matches as slider (if two and more)", 'trx_addons') ),
						'edit_field_class' => 'vc_col-sm-4',
						"admin_label" => true,
						'dependency' => array(
							'element' => 'main_matches',
							'not_empty' => true
						),
						"std" => 0,
						"value" => array(esc_html__("Show main matches as slider", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
				),
				trx_addons_vc_add_query_param(''),
				trx_addons_vc_add_title_param(),
				trx_addons_vc_add_id_param()
		);
		
		// Remove 'columns' from params list
		$params = trx_addons_vc_remove_param($params, 'columns');
												
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_matches",
				"name" => esc_html__("Sport: Matches", 'trx_addons'),
				"description" => wp_kses_data( __("Display matches from specified sport, competition and round", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_matches',
				"class" => "trx_sc_matches",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => $params
			), 'trx_sc_matches' );
	}
}


// Add [trx_sc_points] in the VC shortcodes list
//--------------------------------------------------------
if (!function_exists('trx_addons_sc_points_add_in_vc')) {
	function trx_addons_sc_points_add_in_vc() {
		
		if (!trx_addons_exists_vc()) return;

		vc_lean_map("trx_sc_points", 'trx_addons_sc_points_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Points extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_points_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_points_add_in_vc_params')) {
	function trx_addons_sc_points_add_in_vc_params() {
		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_sc_points');
		// Prepare lists
		$sports_list = trx_addons_get_list_terms(false, TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY);
		$sport_default = trx_addons_get_option('sport_favorite');
		$sport = $vc_edit && !empty($vc_params['sport']) ? $vc_params['sport'] : $sport_default;
		if (empty($sport) && count($sports_list) > 0) {
			$keys = array_keys($sports_list);
			$sport = $keys[0];
		}
		$competitions_list = trx_addons_get_list_posts(false, array(
														'post_type' => TRX_ADDONS_CPT_COMPETITIONS_PT,
														'taxonomy' => TRX_ADDONS_CPT_COMPETITIONS_TAXONOMY,
														'taxonomy_value' => $sport,
														'meta_key' => 'trx_addons_competition_date',
														'orderby' => 'meta_value',
														'order' => 'ASC',
														'not_selected' => false
														));
		
		$params = array_merge(
				array(
					array(
						"param_name" => "type",
						"heading" => esc_html__("Layout", 'trx_addons'),
						"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
						"admin_label" => true,
						"std" => "default",
						"value" => array_flip(apply_filters('trx_addons_sc_type', array(
							'default' => esc_html__('Default', 'trx_addons')
						), 'trx_sc_points')),
						"type" => "dropdown"
					),
					array(
						"param_name" => "sport",
						"heading" => esc_html__("Sport", 'trx_addons'),
						"description" => wp_kses_data( __("Select Sport to display points table", 'trx_addons') ),
						"admin_label" => true,
						"std" => $sport_default,
				        'save_always' => true,
						"value" => array_flip($sports_list),
						"type" => "dropdown"
					),
					array(
						"param_name" => "competition",
						"heading" => esc_html__("Competition", 'trx_addons'),
						"description" => wp_kses_data( __("Select competition to display points table", 'trx_addons') ),
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_flip($competitions_list),
						"type" => "dropdown"
					),
					array(
						"param_name" => "logo",
						"heading" => esc_html__("Logo", 'trx_addons'),
						"description" => wp_kses_data( __("Show logo (players photo) in the table", 'trx_addons') ),
						"admin_label" => true,
						"std" => 0,
						"value" => array(esc_html__("Show logo", 'trx_addons') => "1" ),
						"type" => "checkbox"
					),
					array(
						"param_name" => "accented_top",
						"heading" => esc_html__("Accented top", 'trx_addons'),
						"description" => wp_kses_data( __("How many rows should be accented at the top of the table?", 'trx_addons') ),
						"std" => 3,
						"type" => "textfield"
					),
					array(
						"param_name" => "accented_bottom",
						"heading" => esc_html__("Accented bottom", 'trx_addons'),
						"description" => wp_kses_data( __("How many rows should be accented at the bottom of the table?", 'trx_addons') ),
						"std" => 3,
						"type" => "textfield"
					),
				),
				trx_addons_vc_add_title_param(),
				trx_addons_vc_add_id_param()
		);
		
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_points",
				"name" => esc_html__("Sport: Table of points", 'trx_addons'),
				"description" => wp_kses_data( __("Display table of points for specified competition", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_points',
				"class" => "trx_sc_points",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => $params
			), 'trx_sc_points' );
	}
}
