<?php
/**
 * Shortcode: Display site Logo
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_layouts_logo_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_layouts_logo_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_cpt_layouts_logo_load_scripts_front() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_layouts-logo', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'logo/logo.css'), array(), null );
		}
	}
}

// Load responsive styles for the frontend
if ( !function_exists( 'trx_addons_cpt_layouts_logo_load_responsive_styles' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_layouts_logo_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY);
	function trx_addons_cpt_layouts_logo_load_responsive_styles() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 
				'trx_addons-sc_layouts-logo-responsive', 
				trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'logo/logo.responsive.css'), 
				array(), 
				null, 
				trx_addons_media_for_load_css_responsive( 'cpt-layouts-logo', 'xl' )
			);
		}
	}
}
	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_logo_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_logo_merge_styles');
	add_filter("trx_addons_filter_merge_styles_layouts", 'trx_addons_sc_layouts_logo_merge_styles');
	function trx_addons_sc_layouts_logo_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'logo/logo.css' ] = true;
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_layouts_logo_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_layouts_logo_merge_styles_responsive');
	add_filter("trx_addons_filter_merge_styles_responsive_layouts", 'trx_addons_sc_layouts_logo_merge_styles_responsive');
	function trx_addons_sc_layouts_logo_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'logo/logo.responsive.css' ] = true;
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_sc_layouts_logo_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_layouts_logo_merge_scripts');
	function trx_addons_sc_layouts_logo_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'logo/logo.js' ] = true;
		return $list;
	}
}



// trx_sc_layouts_logo
//-------------------------------------------------------------
/*
[trx_sc_layouts_logo id="unique_id" logo="image_url" logo_retina="image_url"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_logo' ) ) {
	function trx_addons_sc_layouts_logo($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_logo', $atts, trx_addons_sc_common_atts('id,hide', array(
			// Individual params
			"type" => "default",
			"logo_height" => "",
			"logo" => "",
			"logo_retina" => "",
			"logo_text" => "",
			"logo_slogan" => "",
			))
		);

		if (trx_addons_is_on(trx_addons_get_option('debug_mode')))
			wp_enqueue_script( 'trx_addons-sc_layouts_logo', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'logo/logo.js'), array('jquery'), null, true );

		// Get logo from current theme (if empty)
		if (empty($atts['logo'])) {
			$logo = apply_filters('trx_addons_filter_theme_logo', '');
			if (is_array($logo)) {
				$atts['logo'] = !empty($logo['logo']) ? $logo['logo'] : '';
				$atts['logo_retina'] = !empty($logo['logo_retina']) ? $logo['logo_retina'] : $atts['logo_retina'];
			} else
				$atts['logo'] = $logo;
		}
		
		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'logo/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'logo/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_logo',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_logo', $atts, $content);
	}
}


// Add shortcode [trx_sc_layouts_logo]
if (!function_exists('trx_addons_sc_layouts_logo_add_shortcode')) {
	function trx_addons_sc_layouts_logo_add_shortcode() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		add_shortcode("trx_sc_layouts_logo", "trx_addons_sc_layouts_logo");
	}
	add_action('init', 'trx_addons_sc_layouts_logo_add_shortcode', 15);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'logo/logo-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'logo/logo-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'logo/logo-sc-vc.php';
}
