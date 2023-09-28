<?php
/**
 * Includes global functions that can be accessible without class name
 * or objects throught the WordPress
 *
 * @package SupportCandy
 */

/**
 * Perform outside scope translations within plugin or addons
 *
 * @param string $text - string to be translated.
 * @param string $domain - textdomain.
 * @return string
 */
function wpsc__( $text, $domain = 'default' ) {
	return __( $text, $domain ); // phpcs:ignore
}

/**
 * Translate common strings required across addons but not used within the core product
 *
 * @param string $key - unique key for the string.
 * @return string
 */
function wpsc_translate_common_strings( $key ) {

	switch ( $key ) {

		case 'activate-license':
			return __( 'Please activate your license!', 'supportcandy' );

		case 'license-activated':
			return __( 'Your license key activated!', 'supportcandy' );

		case 'license-expires':
			/* translators: %s: date */
			return __( 'Your license key expires on %s', 'supportcandy' );

		case 'license-expired':
			/* translators: %s: date */
			return __( 'Your license key expired on %s', 'supportcandy' );

		case 'view-details':
			return __( 'View Details', 'supportcandy' );
	}
}
