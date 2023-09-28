<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util;

/**
 * Functions for handling the Posts Table Pro plugin options.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Options {

	const SHORTCODE_OPTION_KEY = 'dlp_shortcode_defaults';
	const MISC_OPTION_KEY      = 'dlp_misc_settings';
	const SEARCH_PAGE_KEY      = 'dlp_search_page';

	public static function get_shortcode_options( array $defaults = [] ) {
		return self::table_settings_to_args( self::get_option( self::SHORTCODE_OPTION_KEY, $defaults ), $defaults );
	}

	public static function get_additional_options() {
		$defaults = [
			'cache_expiry' => 6,
			'design'       => 'default'
		];

		return self::get_option( self::MISC_OPTION_KEY, $defaults );
	}

	public static function get_search_page_option() {
		$search_page = (int) get_option( self::SEARCH_PAGE_KEY, false ) ?? false;

		if ( $search_page && in_array( get_post_status( $search_page ), [ false, 'trash' ], true ) ) {
			$search_page = false;
		}

		return $search_page;
	}

	public static function get_cache_expiration_length() {
		$options = self::get_additional_options();

		return filter_var(
			$options['cache_expiry'],
			FILTER_VALIDATE_INT,
			[
				'options' => [
					'default'   => 6,
					'min_range' => 1
				]
			]
		);
	}

	private static function get_option( $option, $default ) {
		$value = get_option( $option, $default );

		if ( empty( $value ) || ( is_array( $default ) && ! is_array( $value ) ) ) {
			$value = $default;
		}

		if ( is_array( $value ) && is_array( $default ) ) {
			$value = array_merge( $default, $value );
		}

		return $value;
	}

	private static function table_settings_to_args( array $options, array $defaults = [] ) {
		if ( empty( $options ) ) {
			return $defaults;
		}

		$options = array_merge( $defaults, $options );

		// Check free text options are not empty.
		foreach ( [ 'columns', 'image_size', 'links' ] as $arg ) {
			if ( empty( $options[ $arg ] ) && ! empty( $defaults[ $arg ] ) ) {
				$options[ $arg ] = $defaults[ $arg ];
			}
		}

		// Sanitize custom filters option.
		if ( 'custom' === $options['filters'] ) {
			$options['filters'] = ! empty( $options['filters_custom'] ) ? $options['filters_custom'] : $defaults['filters'];
		}

		unset( $options['filters_custom'] );

		// Sanitize sort by option.
		if ( 'custom' === $options['sort_by'] ) {
			$options['sort_by'] = ! empty( $options['sort_by_custom'] ) ? $options['sort_by_custom'] : $defaults['sort_by'];
		}

		unset( $options['sort_by_custom'] );

		// Convert 'true' or 'false' strings to booleans.
		$options = array_map( [ Util::class, 'maybe_parse_bool' ], $options );

		return $options;
	}

}
