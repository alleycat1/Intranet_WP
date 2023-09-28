<?php // phpcs:ignore
/**
 * Plugin Name: SupportCandy
 * Plugin URI: https://wordpress.org/plugins/supportcandy/
 * Description: Easy & Powerful support ticket system for WordPress
 * Version: 3.2.1
 * Author: SupportCandy
 * Author URI: https://supportcandy.net/
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Tested up to: 6.3
 * Text Domain: supportcandy
 * Domain Path: /i18n
 */

if ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) {
	return;
}

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'PSM_Support_Candy' ) ) :

	final class PSM_Support_Candy {

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		public static $version = '3.2.1';

		/**
		 * Database version
		 *
		 * @var string
		 */
		public static $db_version = '3.0';

		/**
		 * Constructor for main class
		 */
		public static function init() {

			self::define_constants();
			add_action( 'init', array( __CLASS__, 'load_textdomain' ), 1 );
			self::load_files();
		}

		/**
		 * Defines global constants that can be availabel anywhere in WordPress
		 *
		 * @return void
		 */
		public static function define_constants() {

			self::define( 'WPSC_STORE_URL', 'https://supportcandy.net' );
			self::define( 'WPSC_PLUGIN_FILE', __FILE__ );
			self::define( 'WPSC_ABSPATH', dirname( __FILE__ ) . '/' );
			self::define( 'WPSC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			self::define( 'WPSC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			self::define( 'WPSC_VERSION', self::$version );
			self::define( 'WPSC_DB_VERSION', self::$db_version );
		}

		/**
		 * Loads internationalization strings
		 *
		 * @return void
		 */
		public static function load_textdomain() {

			$locale = apply_filters( 'plugin_locale', get_locale(), 'supportcandy' );
			load_textdomain( 'supportcandy', WP_LANG_DIR . '/supportcandy/supportcandy-' . $locale . '.mo' );
			load_plugin_textdomain( 'supportcandy', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n' );
		}

		/**
		 * Load all classes
		 *
		 * @return void
		 */
		private static function load_files() {

			// Load installation.
			include_once WPSC_ABSPATH . 'class-wpsc-installation.php';
			include_once WPSC_ABSPATH . 'global-functions.php';

			// Return if installation is in progress.
			if ( defined( 'WPSC_INSTALLING' ) ) {
				return;
			}

			// Database upgrade functionality.
			if ( defined( 'WPSC_DB_UPGRADING' ) ) {

				include_once WPSC_ABSPATH . 'includes/class-wpsc-sc-upgrade.php';

				switch ( WPSC_Installation::$current_db_version ) {

					case '1.0':
						include_once WPSC_ABSPATH . 'upgrade/class-wpsc-upgrade-db-v1.php';
						break;

					case '2.0':
						include_once WPSC_ABSPATH . 'upgrade/class-wpsc-upgrade-db-v2.php';
						break;
				}

				// do not load further files.
				return;
			}

			$advanced = get_option( 'wpsc-ms-advanced-settings' );

			// Load common classes.
			foreach ( glob( WPSC_ABSPATH . 'includes/*.php' ) as $filename ) {
				include_once $filename;
			}

			// Load custom field types.
			foreach ( glob( WPSC_ABSPATH . 'includes/custom-field-types/*.php' ) as $filename ) {
				include_once $filename;
			}

			// Load models.
			foreach ( glob( WPSC_ABSPATH . 'includes/models/*.php' ) as $filename ) {
				include_once $filename;
			}

			// Load rest api classes.
			if ( $advanced['rest-api'] ) {
				foreach ( glob( WPSC_ABSPATH . 'includes/rest-api/*.php' ) as $filename ) {
					include_once $filename;
				}
			}

			// Load framework.
			if ( ! ( defined( 'DOING_AJAX' ) || defined( 'DOING_CRON' ) ) ) {
				foreach ( glob( WPSC_ABSPATH . 'framework/*.php' ) as $filename ) {
					include_once $filename;
				}
			}

			// Load classes that is related to admin section.
			foreach ( glob( WPSC_ABSPATH . 'includes/admin/*.php' ) as $filename ) {
				include_once $filename;
			}

			// Load classes that is related to frontend section.
			foreach ( glob( WPSC_ABSPATH . 'includes/frontend/*.php' ) as $filename ) {
				include_once $filename;
			}
		}

		/**
		 * Define constants
		 *
		 * @param string $name - name of global constant.
		 * @param string $value - value of constant.
		 * @return void
		 */
		private static function define( $name, $value ) {

			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}
	}
endif;

PSM_Support_Candy::init();
