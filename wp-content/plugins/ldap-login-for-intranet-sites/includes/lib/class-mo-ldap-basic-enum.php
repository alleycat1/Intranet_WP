<?php
/**
 * This file contains Abstract Class inherited in classes for declaring plugin constant and pointers.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage lib
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MO_LDAP_Basic_Enum' ) ) {
	/**
	 * MO_LDAP_Basic_Enum
	 */
	abstract class MO_LDAP_Basic_Enum {

		/**
		 * Var const_cache_array
		 *
		 * @var array
		 */
		private static $const_cache_array = null;

		/**
		 * Function get_constants : Get the constants list.
		 *
		 * @return array
		 */
		public static function get_constants() {
			if ( null === self::$const_cache_array ) {
				self::$const_cache_array = array();
			}
			$called_class = get_called_class();
			if ( ! array_key_exists( $called_class, self::$const_cache_array ) ) {
				$reflect                                  = new ReflectionClass( $called_class );
				self::$const_cache_array[ $called_class ] = $reflect->getConstants();
			}
			return self::$const_cache_array[ $called_class ];
		}
		/**
		 * Function is_valid_name
		 *
		 * @param  string $name : Constant to be checked.
		 * @param  bool   $strict : If check for strict comparison.
		 * @return bool
		 */
		public static function is_valid_name( $name, $strict = false ) {
			$constants = self::get_constants();

			if ( $strict ) {
				return array_key_exists( $name, $constants );
			}

			$keys = array_map( 'strtolower', array_keys( $constants ) );
			return in_array( strtolower( $name ), $keys, true );
		}

		/**
		 * Function is_valid_value
		 *
		 * @param  string $value : value to be checked if valid.
		 * @return bool
		 */
		public static function is_valid_value( $value ) {
			$values = array_values( self::get_constants() );
			return in_array( $value, $values, true );
		}
	}
}
