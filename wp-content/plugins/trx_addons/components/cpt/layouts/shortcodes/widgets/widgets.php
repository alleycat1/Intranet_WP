<?php
/**
 * Shortcode: Display selected widgets area
 *
 * @package ThemeREX Addons
 * @since v1.6.19
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// trx_sc_layouts_widgets
//-------------------------------------------------------------
/*
[trx_sc_layouts_widgets id="unique_id" widgets="slug"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_widgets' ) ) {
	function trx_addons_sc_layouts_widgets($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_widgets', $atts, trx_addons_sc_common_atts('id,hide', array(
			// Individual params
			"type" => "default",
			"widgets" => "",
			"columns" => "",
			"columns_tablet" => "",
			"columns_mobile" => "",
			))
		);

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'widgets/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'widgets/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_widgets',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_widgets', $atts, $content);
	}
}


// Add shortcode [trx_sc_layouts_widgets]
if (!function_exists('trx_addons_sc_layouts_widgets_add_shortcode')) {
	function trx_addons_sc_layouts_widgets_add_shortcode() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;

		add_shortcode("trx_sc_layouts_widgets", "trx_addons_sc_layouts_widgets");
	}
	add_action('init', 'trx_addons_sc_layouts_widgets_add_shortcode', 15);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'widgets/widgets-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'widgets/widgets-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'widgets/widgets-sc-vc.php';
}
