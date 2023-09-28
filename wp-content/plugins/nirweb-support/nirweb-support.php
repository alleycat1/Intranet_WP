<?php
/**
Plugin Name: nirweb support
Description: NirWeb support is a great help desk and support plugin for WordPress with full support of WooCommerce
Author: NirWp Team
Version: 3.0.2.1
Author URI:  https://www.nirwp.com
Text Domain: nirweb-support
Domain Path: /languages
 **/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // No direct access allowed
}
// Define PATH and URL constant
define( 'NIRWEB_SUPPORT_TICKET_VER', '3.0.2.1 ');
define( 'NIRWEB_SUPPORT_SLUG', plugin_basename(__FILE__) );
define( 'NIRWEB_SUPPORT_TICKET_FILE', __FILE__ );
define( 'NIRWEB_SUPPORT_TICKET', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'NIRWEB_SUPPORT_INC_TICKET', trailingslashit( NIRWEB_SUPPORT_TICKET . 'inc' ) );
define( 'NIRWEB_SUPPORT_INC_ADMIN_TICKET', trailingslashit( NIRWEB_SUPPORT_INC_TICKET . 'admin' ) );
define( 'NIRWEB_SUPPORT_INC_ADMIN_THEMES_TICKET', trailingslashit( NIRWEB_SUPPORT_INC_ADMIN_TICKET . 'themes' ) );
define( 'NIRWEB_SUPPORT_INC_ADMIN_FUNCTIONS_TICKET', trailingslashit( NIRWEB_SUPPORT_INC_ADMIN_TICKET . 'functions' ) );
define( 'NIRWEB_SUPPORT_URL_TICKET', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'NIRWEB_SUPPORT_URL_CSS_TICKET', trailingslashit( NIRWEB_SUPPORT_URL_TICKET . 'assets/css' ) );
define( 'NIRWEB_SUPPORT_URL_JS_TICKET', trailingslashit( NIRWEB_SUPPORT_URL_TICKET . 'assets/js' ) );
define( 'NIRWEB_SUPPORT_URL_CODESTAR_TICKET', trailingslashit( NIRWEB_SUPPORT_URL_TICKET . 'assets/codestar' ) );
define( 'NIRWEB_SUPPORT_INC_USER_THEMES_TICKET', trailingslashit( NIRWEB_SUPPORT_INC_TICKET . 'user/themes' ) );
define( 'NIRWEB_SUPPORT_INC_USER_FUNCTIONS_TICKET', trailingslashit( NIRWEB_SUPPORT_INC_TICKET . 'user/functions' ) );
define( 'wpyar_ticket', get_option( 'nirweb_ticket_perfix' ) );

if ( ! function_exists( 'is_plugin_active' ) ) {
	   require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
if ( ! function_exists( 'check_active_pro_wpyar_ticket' ) ) {
	function check_active_pro_wpyar_ticket() {
		if ( is_plugin_active( 'nirweb_ticket_pro/nirweb_ticket_pro.php' ) ) {
			deactivate_plugins( wpyar_ticket );
			die( '<div dir=rtl style="font-family: tahoma; color: red;">' . esc_html__( 'To activate the free version, please disable the premium version first', 'nirweb-support' ) . '</div>' );    }
	}
}

register_activation_hook( __FILE__, 'check_active_pro_wpyar_ticket' );
add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_script( 'jquery' );
	}
);
add_action(
	'plugins_loaded',
	function() {
		load_textdomain( 'nirweb-support', NIRWEB_SUPPORT_TICKET . 'languages/nirweb-support-' . get_locale() . '.mo' );
	}
);

 require_once NIRWEB_SUPPORT_TICKET . 'core/core.php';

