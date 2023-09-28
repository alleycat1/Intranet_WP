<?php
/**
 * Shortcode: Socials
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_socials_load_scripts_front' ) ) {
	add_action( "wp_enqueue_scripts", 'trx_addons_sc_socials_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	function trx_addons_sc_socials_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))){
			wp_enqueue_style( 'trx_addons-sc_socials', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/socials.css'), array(), null );
		}
	}
}

// Load responsive styles for the frontend
if ( !function_exists( 'trx_addons_sc_socials_load_responsive_styles' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_socials_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY);
	function trx_addons_sc_socials_load_responsive_styles() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 
				'trx_addons-sc_socials-responsive', 
				trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/socials.responsive.css'), 
				array(), 
				null, 
				trx_addons_media_for_load_css_responsive( 'sc-socials', 'sm' ) 
			);
		}
	}
}

	
// Merge contact form specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_socials_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_socials_merge_styles');
	function trx_addons_sc_socials_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/socials.css' ] = true;
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_socials_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_socials_merge_styles_responsive');
	function trx_addons_sc_socials_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/socials.responsive.css' ] = true;
		return $list;
	}
}



// trx_sc_socials
//-------------------------------------------------------------
/*
[trx_sc_socials id="unique_id" icons="encoded_json_data"]
*/
if ( !function_exists( 'trx_addons_sc_socials' ) ) {
	function trx_addons_sc_socials($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_socials', $atts, trx_addons_sc_common_atts('id,title', array(
			// Individual params
			"type" => "default",
			"icons_type" => "socials",
			"icons" => "",
			"align" => "",
			))
		);
		if (function_exists('vc_param_group_parse_atts') && !is_array($atts['icons'])) {
			$atts['icons'] = (array) vc_param_group_parse_atts($atts['icons']);
		}
		// Load template
		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/tpl.default.php'
										),
                                        'trx_addons_args_sc_socials',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_socials', $atts, $content);
	}
}


// Add shortcode [trx_sc_socials]
if (!function_exists('trx_addons_sc_socials_add_shortcode')) {
	function trx_addons_sc_socials_add_shortcode() {
		add_shortcode("trx_sc_socials", "trx_addons_sc_socials");
	}
	add_action('init', 'trx_addons_sc_socials_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/socials-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/socials-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/socials-sc-vc.php';
}
