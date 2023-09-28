<?php
/**
 * Single Page Application imitation - preload pages on links hover.
 *
 * @addon spa
 * @version 1.1
 *
 * @package ThemeREX Addons
 * @since v2.15.0
 */



//========================================================================
//  Add params to the ThemeREX Addons Options and layout to the page
//========================================================================

// Add params to the ThemeREX Addons Options.
if ( ! function_exists( 'trx_addons_spa_add_options' ) ) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_spa_add_options' );
	function trx_addons_spa_add_options( $options ) {
		trx_addons_array_insert_before( $options, 'scroll_info', apply_filters( 'trx_addons_filter_options_spa', array(

			'spa_info' => array(
				"title" => esc_html__('SPA imitation', 'trx_addons'),
				"desc" => wp_kses_data( __("Imitation of the Single Page Application", 'trx_addons') ),
				"type" => "info"
			),
			'spa_mode' => array(
				"title" => esc_html__('Allow SPA mode', 'trx_addons'),
				"desc" => wp_kses_data( __('Single Page Application imitation - preload pages on specified links hover.', 'trx_addons') )
						. "<br>"
						. wp_kses_data( __('"By selector" - preload only links with a CSS selector, specified in the field below.', 'trx_addons') )
						. "<br>"
						. wp_kses_data( __('"All links" - preload all links on the current page.', 'trx_addons') ),
				"std" => "none",
				"options" => array(
					'none' => esc_html__( 'None', 'trx_addons' ),
					'selector' => esc_html__( 'By selector', 'trx_addons' ),
					'all' => esc_html__( 'All links', 'trx_addons' ),
				),
				"type" => "radio"
			),
			'spa_preload' => array(
				"title" => esc_html__('Selector to preload', 'trx_addons'),
				"desc" => wp_kses_data( __('Selector of links to be preloaded on hover.', 'trx_addons') ),
				"std" => ".trx_spa_preload.menu-item > a",
				"dependency" => array(
					'spa_mode' => array( 'selector' )
				),
				"type" => "text"
			),
			'spa_wrapper' => array(
				"title" => esc_html__('Selector for replace', 'trx_addons'),
				"desc" => wp_kses_data( __('Element selector whose content is replaced when a new page is loaded.', 'trx_addons') ),
				"std" => ".page_wrap",
				"dependency" => array(
					'spa_mode' => array( '^none' )
				),
				"type" => "text"
			),
		)));
		return $options;
	}
}


//  Load the script with SPA support
if ( ! function_exists( 'trx_addons_spa_add_to_html' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_spa_add_to_html', 1 );
	function trx_addons_spa_add_to_html() {
		$spa_mode = trx_addons_get_option( 'spa_mode' );
		if ( ! trx_addons_is_off( $spa_mode ) && ! trx_addons_is_preview() ) {
			wp_enqueue_script( 'trx_addons-spa', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'spa/spa.js' ), array( 'jquery' ), null, true );
			wp_localize_script( 'trx_addons-spa', 'TRX_ADDONS_SPA_SETTINGS', apply_filters( 'trx_addons_filter_spa_settings', array(
				'spa_mode' => $spa_mode,
				'preload_selector' => trx_addons_get_option( 'spa_preload' ),
				'replace_selector' => trx_addons_get_option( 'spa_wrapper' ),
				'theme_name' => get_template(),
				'theme_slug' => str_replace( '-', '_', get_template() )
			) ) );
		}
	}
}
