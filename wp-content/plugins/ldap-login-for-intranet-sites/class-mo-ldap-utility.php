<?php
/**
 * This file class with static function used by the plugin.
 *
 * @package miniOrange_LDAP_AD_Integration
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'MO_LDAP_Utility' ) ) {
	/**
	 * MO_LDAP_Utility : Contains static function used on global level by the plugin.
	 */
	class MO_LDAP_Utility {

		/**
		 * Function is_customer_registered : Check if customer is registered.
		 *
		 * @return bool
		 */
		public static function is_customer_registered() {
			$email        = get_option( 'mo_ldap_local_admin_email' );
			$customer_key = get_option( 'mo_ldap_local_admin_customer_key' );
			if ( ! $email || ! $customer_key || ! is_numeric( trim( $customer_key ) ) ) {
				return 0;
			} else {
				return 1;
			}
		}

		/**
		 * Function mo_ldap_is_user_logs_empty : Check if user auth logs exist or not.
		 *
		 * @return bool
		 */
		public static function mo_ldap_is_user_logs_empty() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'user_report';

			$mo_user_report_table_exist = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) ) ) === $table_name; //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Fetching data from a custom table.
			if ( $mo_user_report_table_exist ) {
				$wp_user_reports_count_cache = wp_cache_get( 'mo_ldap_user_report_count_cache' );
				if ( $wp_user_reports_count_cache ) {
					$user_count = $wp_user_reports_count_cache;
				} else {
					$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}user_report" ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Fetching data from a custom table.
					wp_cache_set( 'mo_ldap_user_report_count_cache', $user_count );
				}
				if ( 0 === $user_count ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Function generate_random_string : Used to form a random string
		 *
		 * @param  int $length : Lenngth of the randomo strength requested.
		 * @return string
		 */
		public static function generate_random_string( $length = 8 ) {
			$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			$crypto_rand_secure = function ( $min, $max ) {
				$range = $max - $min;
				if ( $range < 0 ) {
					return $min;
				}
				$log    = log( $range, 2 );
				$bytes  = (int) ( $log / 8 ) + 1;
				$bits   = (int) $log + 1;
				$filter = (int) ( 1 << $bits ) - 1;
				do {
					$rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
					$rnd = $rnd & $filter;
				} while ( $rnd >= $range );
				return $min + $rnd;
			};

			$token = '';
			$max   = strlen( $pool );
			for ( $i = 0; $i < $length; $i++ ) {
				$token .= $pool[ $crypto_rand_secure( 0, $max ) ];
			}
			return $token;
		}

		/**
		 * Function check_empty_or_null : Check of string is empty or null
		 *
		 * @param  string $value : String to be checked.
		 * @return bool
		 */
		public static function check_empty_or_null( $value ) {
			if ( ! isset( $value ) || empty( $value ) ) {
				return true;
			}
			return false;
		}

		/**
		 * Function encrypt : Encrypt a string
		 *
		 * @param  string $str : String to be encrypted.
		 * @return string
		 */
		public static function encrypt( $str ) {
			if ( ! self::is_extension_installed( 'openssl' ) ) {
				return;
			}

			$key           = get_option( 'mo_ldap_local_customer_token' );
			$method        = 'AES-128-ECB';
			$encrypted_str = openssl_encrypt( $str, $method, $key, OPENSSL_RAW_DATA || OPENSSL_ZERO_PADDING );

			return base64_encode( $encrypted_str ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- function not being used to obfuscate the code
		}

		/**
		 * Function decrypt : Decrypt a string
		 *
		 * @param  string $value : String to be decrypted.
		 * @return string
		 */
		public static function decrypt( $value ) {
			if ( ! self::is_extension_installed( 'openssl' ) ) {
				return;
			}

			$str_in  = base64_decode( $value ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode -- function not being used to obfuscate the code
			$key     = get_option( 'mo_ldap_local_customer_token' );
			$method  = 'AES-128-ECB';
			$iv_size = openssl_cipher_iv_length( $method );
			$data    = substr( $str_in, $iv_size );
			return openssl_decrypt( $data, $method, $key, OPENSSL_RAW_DATA || OPENSSL_ZERO_PADDING );
		}

		/**
		 * Function is_extension_installed : Check if PHP extension is enabled
		 *
		 * @param  mixed $name : PHP extension name.
		 * @return bool
		 */
		public static function is_extension_installed( $name ) {
			return in_array( $name, get_loaded_extensions(), true );
		}

		/**
		 * Function update_user_auth_table_headers : Updates the user report table columns
		 *
		 * @return void
		 */
		public static function update_user_auth_table_headers() {
			global $wpdb;

			$wpdb->query( "ALTER TABLE {$wpdb->prefix}user_report CHANGE `Ldap_status` `ldap_status` VARCHAR(250), CHANGE `Ldap_error` `ldap_error` VARCHAR(250)" ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange,  - Fetching data from a custom table.
		}
	}
}
