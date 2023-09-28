<?php
/**
 * ThemeREX Addons Pluggable modules (Components)
 *
 * @package ThemeREX Addons
 * @since v1.6.29
 */


// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Define base (required) component's paths
//---------------------------------------------------
if ( !defined('TRX_ADDONS_PLUGIN_API') )		define('TRX_ADDONS_PLUGIN_API',			TRX_ADDONS_PLUGIN_COMPONENTS . 'api/');
if ( !defined('TRX_ADDONS_PLUGIN_CPT') )		define('TRX_ADDONS_PLUGIN_CPT',			TRX_ADDONS_PLUGIN_COMPONENTS . 'cpt/');
if ( !defined('TRX_ADDONS_PLUGIN_SHORTCODES') )	define('TRX_ADDONS_PLUGIN_SHORTCODES',	TRX_ADDONS_PLUGIN_COMPONENTS . 'shortcodes/');
if ( !defined('TRX_ADDONS_PLUGIN_WIDGETS') )	define('TRX_ADDONS_PLUGIN_WIDGETS',		TRX_ADDONS_PLUGIN_COMPONENTS . 'widgets/');


// Load components
//---------------------------------------------------
$trx_addons_components_list = glob(TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_COMPONENTS . '*', GLOB_ONLYDIR);
if (is_array($trx_addons_components_list)) {
	sort($trx_addons_components_list);
	foreach ($trx_addons_components_list as $c) {
		$fname = trailingslashit($c) . str_replace(TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_COMPONENTS, '', $c) . '.php';
		if (file_exists($fname)) { require_once $fname; }
	}
}

// Add 'Other Components' block in the ThemeREX Addons Components
if (!function_exists('trx_addons_components_components')) {
	add_filter( 'trx_addons_filter_components_blocks', 'trx_addons_components_components');
	function trx_addons_components_components($blocks=array()) {
		$blocks['components'] = __('Other components', 'trx_addons');
		return $blocks;
	}
}

// Define list with other components
if (!function_exists('trx_addons_components_setup')) {
	add_action( 'after_setup_theme', 'trx_addons_components_setup', 2 );
	function trx_addons_components_setup() {
		static $loaded = false;
		if ($loaded) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['components_list'] = apply_filters( 'trx_addons_components_list', array() );
	}
}
