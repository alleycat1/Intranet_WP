<?php
/**
 * Shortcode: Button
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_button_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_button_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_sc_button_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))){
			wp_enqueue_style( 'trx_addons-sc_button', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'button/button.css'), array(), null );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_button_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_button_merge_styles');
	function trx_addons_sc_button_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'button/button.css' ] = true;
		return $list;
	}
}



// trx_sc_button
//-------------------------------------------------------------
/*
[trx_sc_button id="unique_id" type="default" title="Block title" subtitle="" link="#" icon="icon-cog" image="path_to_image"]
*/
if (!function_exists('trx_addons_sc_button')) {	
	function trx_addons_sc_button($atts, $content=null){
		// Compatibility with old versions
		if ( isset($atts['title']) || isset($atts['link']) ) {
			$atts['buttons'] = array(
				array(
					"type" => isset($atts['type']) ? $atts['type'] : "default",
					"size" => isset($atts['size']) ? $atts['size'] : "normal",
					"text_align" => isset($atts['text_align']) ? $atts['text_align'] : "none",
					"bg_image" => isset($atts['bg_image']) ? $atts['bg_image'] : "",
					"back_image" => isset($atts['back_image']) ? $atts['back_image'] : "",		// Alter name for bg_image in VC (it broke bg_image)
					"image" => isset($atts['image']) ? $atts['image'] : "",
					"icon_position" => isset($atts['icon_position']) ? $atts['icon_position'] : "left",
					"title" => isset($atts['title']) ? $atts['title'] : "",
					"subtitle" => isset($atts['subtitle']) ? $atts['subtitle'] : "",
					"link" => isset($atts['link']) ? $atts['link'] : '',
					"link_extra" => isset($atts['link_extra']) ? $atts['link_extra'] : array(),
					"new_window" => isset($atts['new_window']) ? $atts['new_window'] : 0,
				)
			);
		}
		$atts = trx_addons_sc_prepare_atts('trx_sc_button', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"align" => "none",
			"buttons" => "",
			))
		);
		if (function_exists('vc_param_group_parse_atts') && !is_array($atts['buttons'])) {
			$atts['buttons'] = (array) vc_param_group_parse_atts( $atts['buttons'] );
		}
		$output = '';
		if (is_array($atts['buttons']) && count($atts['buttons']) > 0) {
			ob_start();
			trx_addons_get_template_part(array(
											//TRX_ADDONS_PLUGIN_SHORTCODES . 'button/tpl.'.trx_addons_esc($atts['type']).'.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'button/tpl.default.php'
											),
											'trx_addons_args_sc_button', 
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_button', $atts, $content);
	}
}



// Add shortcode [trx_sc_button]
if (!function_exists('trx_addons_sc_button_add_shortcode')) {
	function trx_addons_sc_button_add_shortcode() {
		add_shortcode("trx_sc_button", "trx_addons_sc_button");
	}
	add_action('init', 'trx_addons_sc_button_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'button/button-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'button/button-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'button/button-sc-vc.php';
}
