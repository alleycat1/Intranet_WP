<?php
/**
 * Plugin support: AI Engine
 *
 * @package ThemeREX Addons
 * @since v2.20.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_exists_ai_engine' ) ) {
	/**
	 * Check if AI Engine plugin is installed and activated
	 *
	 * @return bool  True if plugin is installed and activated
	 */
	function trx_addons_exists_ai_engine() {
		return defined( 'MWAI_VERSION' );
	}
}

if ( ! function_exists( 'trx_addons_ai_engine_tgmpa_required_plugins' ) ) {
	add_filter( str_replace( '-', '_', get_template() ) . '_filter_tgmpa_required_plugins', 'trx_addons_ai_engine_tgmpa_required_plugins', 11 );
	/**
	 * Add AI Engine plugin to the list of required plugins for ALL themes (force activation)
	 * 
	 * @hooked THEME_SLUG_filter_tgmpa_required_plugins
	 *
	 * @param array $list  List of required plugins
	 * 
	 * @return array    List of required plugins
	 */
	function trx_addons_ai_engine_tgmpa_required_plugins( $list = array() ) {
		$list[] = array(
			'name'     => esc_html__( 'AI Engine', 'trx_addons' ),
			'slug'     => 'ai-engine',
			'required' => false,
		);
		return $list;
	}
}


if ( ! function_exists( 'trx_addons_ai_engine_theme_plugins' ) ) {
	add_filter( str_replace( '-', '_', get_template() ) . '_filter_theme_plugins', 'trx_addons_ai_engine_theme_plugins', 11 );
	/**
	 * Add AI Engine plugin logo to the list of theme-specific plugins
	 * 
	 * @hooked THEME_SLUG_filter_theme_plugins
	 *
	 * @param array $list  List of theme-specific plugins
	 * 
	 * @return array    List of theme-specific plugins
	 */
	function trx_addons_ai_engine_theme_plugins( $list = array() ) {
		if ( ! empty( $list['ai-engine'] ) && empty( $list['ai-engine']['logo'] ) ) {
			$list['ai-engine']['logo'] = 'https://ps.w.org/ai-engine/assets/icon-256x256.png';
		}
		return $list;
	}
}


if ( ! function_exists( 'trx_addons_ai_engine_fix_wp_media' ) ) {
	add_action( 'admin_enqueue_scripts', 'trx_addons_ai_engine_fix_wp_media' );
	/**
	 * Enqueue wp_media_scripts to fix issue "wp.media.view.settings.post is undefined" when AI Engine is active
	 * 
	 * @hooked admin_enqueue_scripts
	 */
	function trx_addons_ai_engine_fix_wp_media() {
		if ( trx_addons_exists_ai_engine() ) {
			wp_enqueue_media();
		}
	}
}
