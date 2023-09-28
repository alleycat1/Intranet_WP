<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Translations' ) ) :

	final class WPSC_Translations {

		/**
		 * Init class
		 */
		public static function init() {
			add_action( 'admin_init', array( __CLASS__, 'register_strings' ) );
		}

		/**
		 * Register custom strings for translation
		 */
		public static function register_strings() {

			$strings = get_option( 'wpsc-string-translation', array() );

			// latest compatible.
			if ( defined( 'WPML_PLUGIN_PATH' ) ) {
				foreach ( $strings as $name => $str ) {
					do_action( 'wpml_register_single_string', 'SupportCandy', $name, $str );
				}
				return;
			}

			// depricated but being used by other rival plugins like polylang, translatepress, etc.
			if ( function_exists( 'icl_register_string' ) ) {
				foreach ( $strings as $name => $str ) {
					icl_register_string( 'SupportCandy', $name, $str );
				}
				return;
			}
		}

		/**
		 * Get translated string
		 *
		 * @param mixed $name - String index given earlier while adding it to translation.
		 * @param mixed $str - String to be translated.
		 * @return mixed
		 */
		public static function get( $name, $str ) {

			// latest compatible.
			if ( defined( 'WPML_PLUGIN_PATH' ) ) {
				return apply_filters( 'wpml_translate_single_string', $str, 'SupportCandy', $name, ICL_LANGUAGE_CODE );
			}

			if ( function_exists( 'icl_t' ) ) {
				return icl_t( 'SupportCandy', $name );
			}

			return $str;
		}

		/**
		 * Adds a string to translation
		 *
		 * @param mixed $name - Unique index for string.
		 * @param mixed $str - String to be translated.
		 * @return void
		 */
		public static function add( $name, $str ) {

			if ( ! $str ) {
				return;
			}

			$strings = get_option( 'wpsc-string-translation', array() );
			if ( isset( $strings[ $name ] ) ) {
				self::remove( $name );
			}

			$strings[ $name ] = $str;
			update_option( 'wpsc-string-translation', $strings );

			// register strings.
			self::register_strings();
		}

		/**
		 * Remove string from translation
		 *
		 * @param string $name - translation string name.
		 * @return void
		 */
		public static function remove( $name ) {

			if ( function_exists( 'icl_unregister_string' ) ) {
				icl_unregister_string( 'SupportCandy', $name );
			}

			$strings = get_option( 'wpsc-string-translation', array() );
			if ( isset( $strings[ $name ] ) ) {
				unset( $strings[ $name ] );
				update_option( 'wpsc-string-translation', $strings );
			}
		}
	}
endif;

WPSC_Translations::init();
