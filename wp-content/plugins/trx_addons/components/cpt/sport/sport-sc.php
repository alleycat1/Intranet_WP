<?php
/**
 * ThemeREX Addons: Sports Reviews Management (SRM).
 *                  Support different sports, championships, rounds, matches and players.
 *                  (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.6.17
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// trx_sc_matches
//-------------------------------------------------------------
/*
[trx_sc_matches id="unique_id" type="default" sport="sport_slug or id" competition="id" round="id" slider="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_matches' ) ) {
	function trx_addons_sc_matches($atts, $content=null) {	

		// Exit to prevent recursion
		if (trx_addons_sc_stack_check('trx_sc_matches')) return '';

		$atts = trx_addons_sc_prepare_atts('trx_sc_matches', $atts, trx_addons_sc_common_atts('id,title,query', array(
			// Individual params
			"type" => "default",
			"main_matches" => 0,
			"position" => 'top',
			"slider" => 0,
			"sport" => '',
			"competition" => '',
			"round" => '',
			))
		);

		if (empty($atts['sport'])) 
			$atts['sport'] = trx_addons_get_option('sport_favorite');
		if (!empty($atts['ids'])) {
			if ( is_array( $atts['ids'] ) ) {
				$atts['ids'] = join(',', $atts['ids']);
			}
			$atts['ids'] = str_replace(array(';', ' '), array(',', ''), $atts['ids']);
			$ids_count = count( explode( ',', $atts['ids'] ) );
			if ( empty( $atts['count'] ) || $atts['count'] >= $ids_count || empty( $atts['pagination'] ) || trx_addons_is_off( $atts['pagination'] ) ) {
				$atts['count'] = $ids_count;
			}
		}
		$atts['offset'] = max(0, (int) $atts['offset']);
		if (empty($atts['orderby'])) $atts['orderby'] = 'post_date';
		if (empty($atts['order'])) $atts['order'] = 'asc';

		// Load CPT-specific scripts and styles
		trx_addons_cpt_sport_load_scripts_front( true );

		// Load template
		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.sc_matches.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.sc_matches.default.php'
										),
                                        'trx_addons_args_sc_matches',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_matches', $atts, $content);
	}
}


// Add shortcode [trx_sc_matches]
if (!function_exists('trx_addons_sc_matches_add_shortcode')) {
	function trx_addons_sc_matches_add_shortcode() {
		add_shortcode("trx_sc_matches", "trx_addons_sc_matches");
	}
	add_action('init', 'trx_addons_sc_matches_add_shortcode', 20);
}


// trx_sc_points
//-------------------------------------------------------------
/*
[trx_sc_points id="unique_id" type="default" sport="sport_slug or id" competition="id"]
*/
if ( !function_exists( 'trx_addons_sc_points' ) ) {
	function trx_addons_sc_points($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_points', $atts, trx_addons_sc_common_atts('id,title', array(
			// Individual params
			"type" => "default",
			"sport" => '',
			"competition" => '',
			"logo" => 0,
			"accented_top" => 3,
			"accented_bottom" => 3,
			))
		);

		$atts['accented_top'] = empty($atts['accented_top']) ? 0 : max(0, (int) $atts['accented_top']);
		$atts['accented_bottom'] = empty($atts['accented_bottom']) ? 0 : max(0, (int) $atts['accented_bottom']);

		// Load CPT-specific scripts and styles
		trx_addons_cpt_sport_load_scripts_front( true );

		// Load template
		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.sc_points.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT . 'sport/tpl.sc_points.default.php'
										),
										'trx_addons_args_sc_points',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_points', $atts, $content);
	}
}


// Add shortcode [trx_sc_points]
if (!function_exists('trx_addons_sc_points_add_shortcode')) {
	function trx_addons_sc_points_add_shortcode() {
		add_shortcode("trx_sc_points", "trx_addons_sc_points");
	}
	add_action('init', 'trx_addons_sc_points_add_shortcode', 20);
}
