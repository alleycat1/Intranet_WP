<?php
/**
 * Shortcode: Content container
 *
 * @package ThemeREX Addons
 * @since v1.4.3
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_title_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_title_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_sc_title_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))){
			wp_enqueue_style( 'trx_addons-sc_title', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'title/title.css'), array(), null );
		}
	}
}

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_title_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_title_merge_styles');
	function trx_addons_sc_title_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'title/title.css' ] = true;
		return $list;
	}
}

// Load 'typed' script for the pagebuilders preview mode
if ( !function_exists( 'trx_addons_sc_title_load_scripts_preview' ) ) {
	add_action("trx_addons_action_pagebuilder_preview_scripts", 'trx_addons_sc_title_load_scripts_preview');
	function trx_addons_sc_title_load_scripts_preview($builder='') {
		wp_enqueue_script( 'typed', trx_addons_get_file_url('js/typed/typed.min.js'), array(), null, true );
	}
}


// trx_sc_title
//-------------------------------------------------------------
/*
[trx_sc_title id="unique_id" title="" subtitle="" description=""]
*/
if ( !function_exists( 'trx_addons_sc_title' ) ) {
	function trx_addons_sc_title($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_title', $atts, trx_addons_sc_common_atts('id,title', array(
			// Individual params
			'type' => '',
			))
		);
		
		$output = '';

		if (empty($atts['type'])) $atts['type'] = $atts['title_style'];
		else $atts['title_style'] = $atts['type'];
		
		if (!empty($atts['title']) || !empty($atts['subtitle']) || !empty($atts['description'])) {

			ob_start();
			trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'title/tpl.'.trx_addons_esc($atts['type']).'.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'title/tpl.default.php'
											),
                                            'trx_addons_args_sc_title',
                                            $atts
                                        );
			$output = ob_get_contents();
			ob_end_clean();

		}
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_title', $atts, $content);
	}
}


// Add shortcode [trx_sc_content]
if (!function_exists('trx_addons_sc_title_add_shortcode')) {
	function trx_addons_sc_title_add_shortcode() {
		add_shortcode("trx_sc_title", "trx_addons_sc_title");
	}
	add_action('init', 'trx_addons_sc_title_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'title/title-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'title/title-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'title/title-sc-vc.php';
}
