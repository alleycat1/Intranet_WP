<?php
/**
 * Plugin Name:Passwords Manager
 * Plugin URI:https://wordpress.org/plugins/passwords-manager/
 * Description:Passwords Manager let you store all your passwords at one place.
 * Version:1.4.6
 * Author:Coder426
 * Text Domain:passwords-manager
 * Domain Path:/languages
 * Author URI:https://www.hirewebxperts.com
 * License:GPLv2 or later
 * License URI:http://www.gnu.org/licenses/gpl-2.0.txt
*/

/*
**define plugin paths
*/
define('PWDMS_VAR', '1.6.1');
define('PWDMS_NAME', 'passwords-manager');
define('PWDMS_PLUGIN_URL',plugin_dir_url( __FILE__ ));
define('PWDMS_PLUGIN_DIR',dirname( __FILE__ ));
define('PWDMS_ASSETS',PWDMS_PLUGIN_URL. 'assets/');
define('PWDMS_IMG',PWDMS_PLUGIN_URL. 'assets/img/');
define('PWDMS_INC',PWDMS_PLUGIN_DIR. '/include/');
define('PWDMS_INC_URL',PWDMS_PLUGIN_URL. '/include/');

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // exit if accessed directly
}
/**
* Add languages files
* Translate language
*/
add_action('init', 'pms_language_translate');
function pms_language_translate(){
	$locale = determine_locale();
	$locale = apply_filters( 'plugin_locale', $locale, 'passwords-manager' );
	unload_textdomain( 'passwords-manager' );
	load_textdomain( 'passwords-manager', PWDMS_PLUGIN_DIR . '/languages/passwords-manager-' . $locale . '.mo' );
	load_plugin_textdomain( 'passwords-manager', false, dirname(plugin_basename(__FILE__)) . '/languages' );
}

/*
**Create Datatable for plugin  activation
*/	
if ( ! function_exists('pms_db_install') ){
	function pms_db_install() {
		global $wpdb;
		/*
		**create pms_category datatable
		*/
		$table_name = $wpdb->prefix . 'pms_category';
		$sql = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			category varchar(55) NOT NULL,
			PRIMARY KEY  (id)
		)ENGINE=InnoDB DEFAULT CHARSET=latin1";
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		/*
		**create pms_passwords datatable
		*/
		$table_name = $wpdb->prefix . 'pms_passwords';
		$sql1 = "CREATE TABLE $table_name (
			pass_id int(11) NOT NULL AUTO_INCREMENT,
			user_name varchar(200) NOT NULL,
			user_email varchar(200) NOT NULL,
			user_password longtext NOT NULL,
			category_id int(11) NOT NULL,
			note text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
			url longtext NOT NULL,
			PRIMARY KEY  (pass_id)
		)ENGINE=InnoDB DEFAULT CHARSET=latin1";
		dbDelta( $sql1 );
	}
		register_activation_hook( __FILE__, 'pms_db_install' );
}

/*
**Drop datatable
*/		
if ( ! function_exists('delete_plugin_database_tables') ){
	function delete_plugin_database_tables(){
			global $wpdb;
			$prefix = $wpdb->prefix;
			delete_option('pms_encrypt_key');
			$tableArray = array(   
				$wpdb->prefix . "pms_passwords",
				$wpdb->prefix . "pms_category",
			);
			foreach ($tableArray as $tablename) {
				$wpdb->query("DROP TABLE IF EXISTS $tablename");
			}
		}

	register_uninstall_hook(__FILE__, 'delete_plugin_database_tables');	
}
		
/*
**After Plugin Activation redirect
*/
if( !function_exists( 'pms_after_activation_redirect' ) ){
	function pms_after_activation_redirect( $plugin ) {
	if( $plugin == plugin_basename( __FILE__ ) ) {
		exit( wp_redirect( admin_url( 'admin.php?page=pms_menu&tab=settings' ) ) );
	}
	}
	add_action( 'activated_plugin', 'pms_after_activation_redirect' );
}
/*
**After Plugin Activation redirect
*/
if( !function_exists( 'pms_insert_category' ) ){
	function pms_insert_category( $plugin ) {
	global $wpdb;
		
	$table_name = $wpdb->prefix . 'pms_category';
	$query = $wpdb->get_var("SELECT COUNT(*) FROM ".$table_name );   

		if ( $query == 0 ) {
			$result	=	$wpdb->insert(
				$table_name, 
				array('category' =>'Uncategorized','category_color' =>'#bada55') ,
				array('%s','%s')
			);
		}
		$row = $wpdb->get_row("SELECT * FROM ".$table_name);
		if(!isset($row->category_color)):
			$wpdb->query("ALTER TABLE ".$table_name." ADD category_color varchar(255) NOT NULL");
		endif;
	}
	add_action( 'init', 'pms_insert_category' );
}

/**
 * Setting link to pluign
 */	
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'pms_add_plugin_page_settings_link');
function pms_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' .admin_url( 'admin.php?page=pms_menu&tab=settings' ) .'">'. __('Settings','passwords-manager').'</a>';
	return $links;
}

/*
*Live demo plugin row meta
*/
add_filter('plugin_row_meta' , 'pms_live_demo_meta_links', 10, 2);
if ( ! function_exists('pms_live_demo_meta_links') ) {
	function pms_live_demo_meta_links($meta_fields, $file) {
		if ( plugin_basename(__FILE__) == $file ) {
		$plugin_url = "https://youtu.be/B9NvLynWueU";
		$meta_fields[] = "<a href='" . esc_url($plugin_url) ."' target='_blank' title='" .__('Live Demo','passwords-manager') . "'>
				<i class='fa fa-desktop' aria-hidden='true'>"
			. "&nbsp;<span>".__('Live Demo','passwords-manager') ."</span>". "</i></a>";      
		
		}
		return $meta_fields;
	}
}

/*
**include script & style file
*/
include(PWDMS_INC .'pms-srcipts-styles.php');
/*
**include encryption file
*/
include(PWDMS_INC .'pms-functions.php');
/*
**include frontend shortcode file
*/
include(PWDMS_INC .'pms-front-shortcode.php');
/*
**include encryption file
*/
include(PWDMS_INC .'pms-encryption.php');	
/*
**include category action file
*/	
include(PWDMS_INC .'pms-categories-ajax-action.php');
/*
**include pass action file
*/	
include(PWDMS_INC .'pms-passwords-ajax-action.php');
/*
**include Setting action file
*/
include(PWDMS_INC .'pms-settings-ajax-action.php');

include(PWDMS_INC .'admin-page/addon/csv-export/index.php');

?>