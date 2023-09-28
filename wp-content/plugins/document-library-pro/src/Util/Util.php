<?php
namespace Barn2\Plugin\Document_Library_Pro\Util;

use Barn2\Plugin\Document_Library_Pro\Util\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Utilities
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Util {

	/**
	 * Create a page and store the ID in an option. (adapted from WooCommerce)
	 *
	 * @param mixed  $slug Slug for the new page.
	 * @param string $option Option name to store the page's ID.
	 * @param string $page_title (default: '') Title for the new page.
	 * @param string $page_content (default: '') Content for the new page.
	 * @param int    $post_parent (default: 0) Parent for the new page.
	 * @return int page ID.
	 */
	public static function create_page( $slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;

		$option_value = get_option( $option );

		if ( $option_value > 0 ) {
			$page_object = get_post( $option_value );

			if ( $page_object && 'page' === $page_object->post_type && ! in_array( $page_object->post_status, [ 'pending', 'trash', 'future', 'auto-draft' ], true ) ) {
				// Valid page is already in place.
				return $page_object->ID;
			}
		}

		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode).
			$shortcode        = str_replace( [ '<!-- wp:shortcode -->', '<!-- /wp:shortcode -->' ], '', $page_content );
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$shortcode}%" ) );
		} else {
			// Search for an existing page with the specified page slug.
			$valid_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug ) );
		}

		$valid_page_found = apply_filters( 'document_library_pro_create_page_id', $valid_page_found, $slug, $page_content );

		if ( $valid_page_found ) {
			if ( $option ) {
				update_option( $option, $valid_page_found );
			}
			return $valid_page_found;
		}

		// Search for a matching valid trashed page.
		if ( strlen( $page_content ) > 0 ) {
			// Search for an existing page with the specified page content (typically a shortcode).
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%" ) );
		} else {
			// Search for an existing page with the specified page slug.
			$trashed_page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug ) );
		}

		if ( $trashed_page_found ) {
			$page_id   = $trashed_page_found;
			$page_data = [
				'ID'          => $page_id,
				'post_status' => 'publish',
			];
			wp_update_post( $page_data );
		} else {
			$page_data = [
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'post_author'    => 1,
				'post_name'      => $slug,
				'post_title'     => $page_title,
				'post_content'   => $page_content,
				'post_parent'    => $post_parent,
				'comment_status' => 'closed',
			];
			$page_id   = wp_insert_post( $page_data );
		}

		if ( $option ) {
			update_option( $option, $page_id );
		}

		return $page_id;
	}

	/**
	 * Round a number using the built-in `round` function, but unless the value to round is numeric
	 * (a number or a string that can be parsed as a number), apply 'floatval' first to it
	 * (so it will convert it to 0 in most cases).
	 *
	 * This is needed because in PHP 7 applying `round` to a non-numeric value returns 0,
	 * but in PHP 8 it throws an error. Specifically, in WooCommerce we have a few places where
	 * round('') is often executed.
	 *
	 * @param mixed $val The value to round.
	 * @param int   $precision The optional number of decimal digits to round to.
	 * @param int   $mode A constant to specify the mode in which rounding occurs.
	 *
	 * @return float The value rounded to the given precision as a float, or the supplied default value.
	 */
	public static function round( $val, int $precision = 0, int $mode = PHP_ROUND_HALF_UP ) : float {
		if ( ! is_numeric( $val ) ) {
			$val = floatval( $val );
		}
		return round( $val, $precision, $mode );
	}

	/**
	 * Converts a string (e.g. 'yes' or 'no') to a bool.
	 *
	 * @param   string|bool $string String to convert. If a bool is passed it will be returned as-is.
	 * @return  bool
	 */
	public static function string_to_bool( $string ) {
		return is_bool( $string ) ? $string : ( 'yes' === strtolower( $string ) || 1 === $string || 'true' === strtolower( $string ) || '1' === $string );
	}

	/**
	 * Sanitize array recursively.
	 *
	 * @param mixed $array
	 * @return mixed
	 */
	public static function sanitize_array_recursive( $array ) {
		if ( is_array( $array ) ) {
			return array_map( 'self::sanitize_array_recursive', $array );
		} else {
			return is_scalar( $array ) ? sanitize_text_field( $array ) : $array;
		}
	}

	/**
	 * Associated attachment to post.
	 *
	 * @param mixed $attachment_id
	 * @param mixed $post_id
	 */
	public static function associate_attachment( $attachment_id, $post_id ) {
		global $wpdb;

		$wpdb->update(
			$wpdb->posts,
			[ 'post_parent' => $post_id ],
			[ 'ID' => $attachment_id ],
			[ '%d' ],
			[ '%d' ]
		);
	}

	/**
	 * Disassociate attachment to post.
	 *
	 * @param mixed $attachment_id
	 */
	public static function disassociate_attachment( $attachment_id ) {
		global $wpdb;

		$wpdb->update(
			$wpdb->posts,
			[ 'post_parent' => 0 ],
			[ 'ID' => $attachment_id ],
			[ '%d' ],
			[ '%d' ]
		);
	}

	/**
	 * Helper function to replace wp_localize_script
	 *
	 * @param string $script_handle
	 * @param string $variable_name
	 * @param array $script_params
	 */
	public static function add_inline_script_params( $script_handle, $variable_name, $script_params ) {
		$script_data = sprintf( 'var %1$s = %2$s', $variable_name, wp_json_encode( $script_params ) );
		wp_add_inline_script( $script_handle, $script_data, 'before' );
	}

	/**
	 * Determins if a string ends with another string
	 *
	 * @param string $haystack
	 * @param string $needle
	 * @return bool
	 */
	public static function str_ends_with( $haystack, $needle ) {
		$length = strlen( $needle );
		return $length > 0 ? substr( $haystack, -$length ) === $needle : true;
	}

	/**
	 * Retrieve a linked list of terms (for Author and File Types).
	 *
	 * @param \WP_Post $post
	 * @param string $taxonomy
	 * @param string $sep
	 * @return string
	 */
	public static function get_the_fake_linked_term_names( $post, $taxonomy, $sep = ', ' ) {
		$terms = get_the_terms( $post, $taxonomy );

		if ( ! $terms || ! is_array( $terms ) ) {
			return '';
		}

		$linked_terms = array_map( function( $term ) {
			return sprintf( '<a href="#">%s</a>', $term->name );
		}, $terms );


		return implode( $sep, $linked_terms );
	}

	/**
	 * Check if the Advanced Custom Fields plugin is active.
	 * 
	 * @return bool
	 */
	public static function is_acf_active() {
		return class_exists( '\ACF' );
	}

	/**
	 * Check if the Easy Post Types plugin is active.
	 * 
	 * @return bool
	 */
	public static function is_ept_active() {
		return class_exists( 'Barn2\Plugin\Easy_Post_Types_Fields\Util' );
	}

	/**
	 * Normalize user arguments provided to shortcode.
	 *
	 * @param array $args
	 * @return array
	 * @deprecated
	 */
	public static function normalize_user_arguments( $args ) {
		_deprecated_function( __METHOD__, '1.3', esc_html( Options::class . '::normalize_user_arguments' ) );
		return Options::normalize_user_arguments( $args );
	}

	/**
	 * Retrive the Document post type fields.
	 *
	 * @return array
	 * @deprecated
	 */
	public static function get_document_fields() {
		_deprecated_function( __METHOD__, '1.3', esc_html( Options::class . '::get_document_fields' ) );
		return Options::get_document_fields();
	}

	/**
	 * Retrieve the single document display option.
	 *
	 * @return array
	 * @deprecated
	 */
	public static function get_document_display_fields() {
		_deprecated_function( __METHOD__, '1.3', esc_html( Options::class . '::get_document_display_fields' ) );
		return Options::get_document_display_fields();
	}
}
