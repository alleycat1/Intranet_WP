<?php
/**
 * ThemeREX Web Push
 *
 * @package ThemeREX Addons
 * @since v1.6.48
 */

// Don't load directly
if ( ! defined('TRX_ADDONS_VERSION') ) {
	exit;
}

// Define component's subfolder
if (!defined('TRX_ADDONS_PLUGIN_WEB_PUSH')) {
	define('TRX_ADDONS_PLUGIN_WEB_PUSH', TRX_ADDONS_PLUGIN_COMPONENTS . 'web-push/');
}

// Add component to the global list
if (!function_exists('trx_addons_web_push_add_to_components')) {
	add_filter('trx_addons_components_list', 'trx_addons_web_push_add_to_components');
	function trx_addons_web_push_add_to_components($list = array()) {
		$list['web_push'] = array(
								'title' => __('Web Push', 'trx_addons')
								);
		return $list;
	}
}

// Check if module is enabled
if (!function_exists('trx_addons_web_push_enable')) {
	function trx_addons_web_push_enable() {
		static $enable = null;
		if ($enable === null) {
			$enable = trx_addons_components_is_allowed('components', 'web_push');
		}
		return $enable;
	}
}


// Merge specific scripts into single file
if (!function_exists('trx_addons_web_push_merge_scripts')) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_web_push_merge_scripts', 11);
	function trx_addons_web_push_merge_scripts($list) {
		if (trx_addons_web_push_enable() && trx_addons_is_on(trx_addons_get_option('allow_web_push'))) {
			$list[ TRX_ADDONS_PLUGIN_WEB_PUSH . 'web_push.js' ] = true;
		}
		return $list;
	}
}

// Load module-specific scripts
if (!function_exists('trx_addons_web_push_enqueue_scripts')) {
	add_action('wp_enqueue_scripts', 'trx_addons_web_push_enqueue_scripts', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_web_push_enqueue_scripts() {
		if (trx_addons_web_push_enable() && trx_addons_is_on(trx_addons_get_option('allow_web_push'))) {
			wp_enqueue_script('onesignal', 'https://cdn.onesignal.com/sdks/OneSignalSDK.js', array('jquery'), null, false);
		}
		if (trx_addons_web_push_enable() && trx_addons_is_on(trx_addons_get_option('debug_mode')) && trx_addons_is_on(trx_addons_get_option('allow_web_push'))) {
			wp_enqueue_script('trx_addons-web-push', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_WEB_PUSH . 'web_push.js'), array('jquery'), null, true);
		}
	}
}


// Add module-specific vars to the frontend scripts
if (!function_exists('trx_addons_web_push_localize_scripts')) {
	add_filter('trx_addons_filter_localize_script', 'trx_addons_web_push_localize_scripts');
	function trx_addons_web_push_localize_scripts($vars) {
		if (trx_addons_web_push_enable() && trx_addons_is_on(trx_addons_get_option('allow_web_push'))) {
			$web_push_appid = trx_addons_get_option('web_push_appid');
			if (!empty($web_push_appid)) {
				$vars['web_push_appid'] = $web_push_appid;
			}
		}
		return $vars;
	}
}

// Add 'Web Push' section to the ThemeREX Addons Options
if (!function_exists('trx_addons_web_push_options')) {
	add_filter('trx_addons_filter_options', 'trx_addons_web_push_options');
	function trx_addons_web_push_options($options) {
		// Add section 'Web Push'
		if (trx_addons_web_push_enable()) {
			trx_addons_array_insert_before($options, 'sc_section', array(
				'web_push_section' => array(
					"title" => esc_html__('Web Push', 'trx_addons'),
					"type" => "section"
				),
				'allow_web_push' => array(
					"title" => esc_html__('Allow web push', 'trx_addons'),
					"desc" => wp_kses_data(__('Allow to send web push messages to clients', 'trx_addons')),
					"std" => "1",
					"type" => "checkbox"
				),
				'web_push_appid' => array(
					"title" => esc_html__('App ID', 'trx_addons'),
					"desc" => wp_kses_data(__('You can find this in App Settings > Keys & IDs.', 'trx_addons')),
					"dependency" => array(
						"allow_web_push" => array('1')
					),
					"std" => '',
					"type" => "text"
				)
			));
		}
		return $options;
	}
}
