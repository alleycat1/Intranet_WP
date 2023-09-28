<?php
/**
 * Shortcode: Skills
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_skills_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_skills_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_sc_skills_load_scripts_front', 10, 1 );
	function trx_addons_sc_skills_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_skills', $force, array(
			'lib' => array(
				'js' => array(
					'chart-legacy' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/chart-legacy.min.js', 'deps' => 'jquery' ),
				),
			),
			'css'  => array(
				'trx_addons-sc_skills' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/skills.css' ),
			),
			'js' => array(
				'trx_addons-sc_skills' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/skills.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_skills' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/skills' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_skills"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_skills' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_skills_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_skills_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_skills', 'trx_addons_sc_skills_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_skills_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'sc_skills', $force, array(
			'css'  => array(
				'trx_addons-sc_skills-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/skills.responsive.css',
					'media' => 'md'
				),
			),
		) );
	}
}

// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_skills_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_skills_merge_styles');
	function trx_addons_sc_skills_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/skills.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_skills_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_skills_merge_styles_responsive');
	function trx_addons_sc_skills_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/skills.responsive.css' ] = false;
		return $list;
	}
}

// Merge skills specific scripts into single file
if ( !function_exists( 'trx_addons_sc_skills_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_skills_merge_scripts');
	function trx_addons_sc_skills_merge_scripts($list) {
		//$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/chart-legacy.min.js' ] = false;
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/skills.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_skills_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_skills_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_skills_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_skills_check_in_html_output', 10, 1 );
	function trx_addons_sc_skills_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_skills'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_skills', $content, $args ) ) {
			trx_addons_sc_skills_load_scripts_front( true );
		}
		return $content;
	}
}


// Return groups with 10 spans for slide digits
if ( !function_exists( 'trx_addons_sc_skills_split_by_digits' ) ) {
	function trx_addons_sc_skills_split_by_digits($value, $max, $unit='') {
		$output = '<span class="sc_skills_digits">';
		if ( empty( $value ) ) $value = 0;
		$sm = "{$max}";
		$sv = str_pad( "{$value}", strlen($sm), '0', STR_PAD_LEFT );
		for ( $i = 0; $i < strlen($sv); $i++ ) {
			$digit = substr( $sv, $i, 1);
			$output .= '<span class="sc_skills_digit">'
						. '<span class="sc_skills_digit_placeholder">8</span>'
						. '<span class="sc_skills_digit_wrap">'
							. '<span class="sc_skills_digit_ribbon">'
								. '<span class="sc_skills_digit_value">'
									. esc_html( $digit )
								. '</span>'
							. '</span>'
						. '</span>'
					. '</span>';
		}
		if ( ! empty( $unit ) ) {
			$output .= '<span class="sc_skills_unit">'
							. esc_html( $unit )
						. '</span>';
		}
		$output .= '</span>';
		return $output;
	}
}


// trx_sc_skills
//-------------------------------------------------------------
/*
[trx_sc_skills id="unique_id" type="pie" cutout="99" values="encoded json data"]
*/
if ( !function_exists( 'trx_addons_sc_skills' ) ) {
	function trx_addons_sc_skills($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_skills', $atts, trx_addons_sc_common_atts('id,title', array(
			// Individual params
			"type" => "counter",
			"style" => "counter",
			"icon_position" => "top",
			"cutout" => 0,
			"compact" => 0,
			"max" => 100,
			"duration" => 1500,
			"color" => '',
			"icon_color" => '',
			"item_title_color" => '',
			"bg_color" => '',
			"back_color" => '',		// Alter param name for VC (it broke bg_color)
			"border_color" => '',
			"columns" => "",
			"columns_tablet" => "",
			"columns_mobile" => "",
			"values" => "",
			))
		);

		if (function_exists('vc_param_group_parse_atts') && !is_array($atts['values'])) {
			$atts['values'] = (array) vc_param_group_parse_atts($atts['values']);
		}

		$output = '';

		if (is_array($atts['values']) && count($atts['values']) > 0) {

			if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
				wp_enqueue_script( 'trx_addons-sc_skills', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/skills.js'), array('jquery'), null, true );
			}
	
			if (empty($atts['bg_color'])) $atts['bg_color'] = $atts['back_color'];
	
			$atts['cutout'] = min(100, max(0, (int) $atts['cutout']));
	
			$max = 0;
			foreach ($atts['values'] as $k=>$v) {
				if (preg_match('/([+\-]?[\.0-9]+)(.*)/', $v['value'], $matches)) {
					$atts['values'][$k]['value'] = (float)$matches[1];
					$atts['values'][$k]['units'] = $matches[2];
				} else {
					$atts['values'][$k]['value'] = (float)str_replace('%', '', $v['value']);
					$atts['values'][$k]['units'] = '';
				}
				if ($max < $atts['values'][$k]['value']) $max = $atts['values'][$k]['value'];
			}
			if (empty($atts['max'])) {
				$atts['max'] = $max;
			} else {
				if (preg_match('/([+\-]?[\.0-9]+)(.*)/', $atts['max'], $matches)) {
					$atts['max'] = (float)$matches[1];
				} else
					$atts['max'] = str_replace('%', '', $atts['max']);
			}
	
			$atts['compact'] = $atts['compact']<1 ? 0 : 1;
			$atts['columns'] = $atts['compact']==0 
									? ($atts['columns'] < 1 
										? count($atts['values']) 
										: min($atts['columns'], count($atts['values']))
										)
									: 1;
			if (!empty($atts['columns_tablet']) && $atts['compact']==0) $atts['columns_tablet'] = max(1, min(count($atts['values']), (int) $atts['columns_tablet']));
			if (!empty($atts['columns_mobile']) && $atts['compact']==0) $atts['columns_mobile'] = max(1, min(count($atts['values']), (int) $atts['columns_mobile']));
	
			// Load shortcode-specific scripts and styles
			trx_addons_sc_skills_load_scripts_front( true );

			// Load template
			ob_start();
			trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/tpl.'.trx_addons_esc($atts['type']).'.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/tpl.counter.php'
											),
											'trx_addons_args_sc_skills', 
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_skills', $atts, $content);
	}
}


// Add shortcode [trx_sc_skills]
if (!function_exists('trx_addons_sc_skills_add_shortcode')) {
	function trx_addons_sc_skills_add_shortcode() {
		add_shortcode("trx_sc_skills", "trx_addons_sc_skills");
	}
	add_action('init', 'trx_addons_sc_skills_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/skills-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/skills-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/skills-sc-vc.php';
}
