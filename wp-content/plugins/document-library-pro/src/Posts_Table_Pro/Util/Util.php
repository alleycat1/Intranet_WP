<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util;

use WP_Term;
use const Barn2\Plugin\Document_Library_Pro\PLUGIN_FILE;

/**
 * Utility functions for Posts Table Pro.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Util {

	const TABLE_CLASS         = 'posts-data-table';
	const TABLE_WRAPPER_CLASS = 'posts-table-wrapper';

	/**
	 * Combination of array_pad and array_slice.
	 *
	 * @param array $array Input array
	 * @param int   $size  The size of the array to return
	 * @param mixed $pad   What to pad with
	 * @return array The result
	 */
	public static function array_pad_and_slice( $array, $size, $pad ) {
		if ( ! is_array( $array ) ) {
			$array = [];
		}
		return array_slice( array_pad( $array, $size, $pad ), 0, $size );
	}

	/**
	 * Similar to <code>array_diff_assoc</code>, but does a loose type comparison on array values (== not ===).
	 * Supports multi-dimensional arrays, but doesn't support passing more than two arrays.
	 *
	 * @param array $array1 The main array to compare against
	 * @param array $array2 The array to compare with
	 * @return array All entries in $array1 which are not present in $array2 (including key check)
	 */
	public static function array_diff_assoc( $array1, $array2 ) {
		if ( empty( $array1 ) || ! is_array( $array1 ) ) {
			return [];
		}

		if ( empty( $array2 ) || ! is_array( $array2 ) ) {
			return $array1;
		}

		foreach ( $array1 as $k1 => $v1 ) {
			if ( array_key_exists( $k1, $array2 ) ) {
				$v2 = $array2[ $k1 ];

				if ( $v2 == $v1 ) {
					unset( $array1[ $k1 ] );
				}
			}
		}
		return $array1;
	}

	/**
	 * Similar to <code>wp_list_pluck</code> or <code>array_column</code> but plucks several keys from the source array.
	 *
	 * @param array        $list The array of arrays to extract the keys from
	 * @param array|string $keys The list of keys to pluck
	 * @return array An array returned in the same order as $list, but where each item in the array contains just the specified $keys
	 */
	public static function list_pluck_array( $list, $keys = [] ) {
		$result    = [];
		$keys_comp = array_flip( (array) $keys );

		// Return empty array if there are no keys to extract
		if ( ! $keys_comp ) {
			return [];
		}

		foreach ( $list as $key => $item ) {
			if ( ! is_array( $item ) ) {
				// Make sure we have an array to pluck from
				continue;
			}
			$item = array_intersect_key( $item, $keys_comp );

			foreach ( $item as $child_key => $child ) {
				if ( is_array( $child ) ) {
					$item[ $child_key ] = self::list_pluck_array( $child, $keys );
				}
			}

			$result[ $key ] = $item;
		}

		return $result;
	}

	public static function string_list_to_array( $arg ) {
		if ( is_array( $arg ) ) {
			return $arg;
		}
		return array_filter( array_map( 'trim', explode( ',', $arg ) ) );
	}

	// SANITIZE FUNCTIONS

	public static function empty_if_false( $var ) {
		if ( false === $var ) {
			return '';
		}
		return $var;
	}

	public static function maybe_parse_bool( $maybe_bool ) {
		if ( is_bool( $maybe_bool ) ) {
			return $maybe_bool;
		} elseif ( 'true' === $maybe_bool || '1' === $maybe_bool ) {
			return true;
		} elseif ( 'false' === $maybe_bool || '' === $maybe_bool ) {
			return false;
		} else {
			return $maybe_bool;
		}
	}

	public static function set_object_vars( $object, array $vars ) {
		$has = get_object_vars( $object );

		foreach ( $has as $name => $old ) {
			$object->$name = isset( $vars[ $name ] ) && ( null !== $vars[ $name ] ) ? $vars[ $name ] : $old;
		}
	}

	public static function sanitize_enum( $value ) {
		$value = strtolower( $value );
		return preg_replace( '/[^a-z_]/', '', $value );
	}

	public static function sanitize_enum_or_bool( $value ) {
		$value = self::maybe_parse_bool( $value );
		return is_bool( $value ) ? $value : self::sanitize_enum( $value );
	}

	public static function sanitize_image_size( $image_size ) {
		if ( empty( $image_size ) ) {
			return '';
		}

		if ( is_array( $image_size ) ) {
			$image_size = implode( ',', array_map( 'absint', $image_size ) );
		}

		// Strip 'px' from size, e.g. 60px becomes 60.
		$image_size = preg_replace( '/(\d+)px/', '$1', $image_size );

		// Strip anything that's not a letter, digit, underscore, hyphen or comma.
		return preg_replace( '/[^\w\-,]+/', '', $image_size );
	}

	public static function sanitize_list( $value ) {
		// Allows any Unicode letter, digit, underscore, hyphen, comma, plus sign, full-stop, colon, percent and forward slash.
		return preg_replace( '/[^\w+\-\/%:,.]+/u', '', (string) $value );
	}

	public static function sanitize_numeric_list( $value ) {
		if ( is_string( $value ) ) {
			// Allows decimal digit or comma
			return preg_replace( '/[^\d,]/', '', $value );
		}

		return $value;
	}

	public static function sanitize_list_or_bool( $value ) {
		$value = self::maybe_parse_bool( $value );
		return is_bool( $value ) ? $value : self::sanitize_list( $value );
	}

	public static function sanitize_class_name( $class ) {
		return preg_replace( '/[^a-zA-Z0-9-_]/', '', $class );
	}

	/**
	 * Parse a string term arg, as specified in the table shortcode, into a sorted array.
	 *
	 * Terms args are specified as a list of items in the format taxonomy:slug or taxonomy:term_id. The slugs/ids can be specified
	 * without the taxonomy if they belong to the previous taxonomy, e.g. some_category:term1,term2,term3. Terms can be separated
	 * by a comma to denote term1 OR term2 or a plus (+) to denote term1 AND term2. The same notation can be used between taxonomies.
	 *
	 * The result is an array of arrays, with each key being the taxonomy name, where the corresponding value is an  array of
	 * terms to select in that taxonomy.
	 *
	 * If $include_relations is true, an additional '_relations' key is returned in the result, which itself is an array of the
	 * form 'taxonomy' => 'relation', where 'relation' is either 'AND' or 'OR'. If there are 2 or more taxonomies specified, the
	 * '_relations' key will also contain an '_outer' key which denotes the relation between the taxonomies.
	 *
	 * Example:
	 * parse_term_arg( 'document_type:policy+regulations,document_status:approved', true )
	 *
	 * Returns: [
	 *     _relations => [ _outer => OR, document_type => AND ],
	 *     document_type => [ policy, regulations ]
	 *     document_status => [ approved ]
	 * ]
	 *
	 * @param string  $term_arg          The term arg to parse.
	 * @param boolean $include_relations Whether to include the relationships between terms and taxonomies (OR, AND, etc) in the result.
	 * @return array The sorted terms, keyed by taxonomy.
	 */
	public static function parse_term_arg( $term_arg, $include_relations = false ) {
		if ( empty( $term_arg ) ) {
			return [];
		}

		// Split the term arg into taxonomies, terms and delimiters.
		$term_parts = preg_split( '/([,+:])/', $term_arg, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
		$result     = [];

		// We need at least 3 parts (taxonomy, :, term) to have a valid term arg.
		if ( count( $term_parts ) < 3 ) {
			return $result;
		}

		$current_taxonomy = '';
		$relation         = '';

		foreach ( $term_parts as $i => $term_part ) {
			if ( ',' === $term_part ) {
				$relation = 'OR';
				continue;
			} elseif ( '+' === $term_part ) {
				$relation = 'AND';
				continue;
			} elseif ( ':' === $term_part ) {
				continue;
			}

			// Is the current part a taxonomy? It is if the next delimiter is ":"
			if ( isset( $term_parts[ $i + 1 ] ) && ':' === $term_parts[ $i + 1 ] ) {
				$current_taxonomy = $term_part;

				if ( ! taxonomy_exists( $current_taxonomy ) ) {
					$current_taxonomy = '';
					continue;
				}

				if ( $include_relations && $relation && ! empty( $result ) && ! isset( $result['_relations']['_outer'] ) ) {
					$result['_relations']['_outer'] = $relation;
				}

				if ( $relation ) {
					$relation = '';
				}

				continue;
			}

			// If we got this far, then the part must be a term, not a taxonomy or a delimiter.
			if ( $current_taxonomy ) {
				// We have a valid taxonomy, so add the term to the result.
				$result[ $current_taxonomy ][] = $term_part;

				if ( $include_relations && $relation && ! isset( $result['_relations'][ $current_taxonomy ] ) ) {
					$result['_relations'][ $current_taxonomy ] = $relation;
				}

				if ( $relation ) {
					$relation = '';
				}
			}
		} // foreach term part

		if ( $include_relations ) {
			// Sort by key so '_relations' comes first.
			ksort( $result );
		}

		return $result;
	}

	// TERMS

	public static function get_all_term_children( $term_ids, $taxonomy, $include_parents = false ) {
		if ( empty( $term_ids ) ) {
			return [];
		}

		$result = $include_parents ? $term_ids : [];

		foreach ( (array) $term_ids as $term_id ) {
			$result = array_merge( $result, get_term_children( $term_id, $taxonomy ) );
		}

		// Remove duplicates
		return array_unique( $result );
	}

	public static function convert_to_term_ids( $terms, $taxonomy ) {
		$result = [];

		if ( empty( $terms ) ) {
			return $result;
		}

		if ( ! is_array( $terms ) ) {
			$terms = explode( ',', str_replace( '+', ',', $terms ) );
		}

		foreach ( $terms as $slug ) {
			if ( is_numeric( $slug ) ) {
				$result[] = (int) $slug;
			} else {
				$_term = get_term_by( 'slug', $slug, $taxonomy );

				if ( $_term instanceof WP_Term ) {
					$result[] = $_term->term_id;
				}
			}
		}
		return $result;
	}

	// ADVANCED CUSTOM FIELDS

	public static function get_acf_field_object( $field, $post_id = false ) {
		$field_obj = false;

		if ( ! $post_id && function_exists( 'acf_get_field' ) ) {
			// If we're not getting field for a specific post, just check field exists (ACF Pro only)
			$field_obj = acf_get_field( $field );
		} elseif ( function_exists( 'get_field_object' ) ) {
			$field_obj = get_field_object( $field, $post_id, [ 'format_value' => false ] );
		}

		if ( $field_obj ) {
			$field_obj = array_merge( [ 'type' => '' ], $field_obj );

			if ( in_array( $field_obj['type'], [ 'date_picker', 'date_time_picker' ], true ) && isset( $field_obj['date_format'] ) ) {
				// In ACF v4 and below, date picker fields used jQuery date formats and 'return_format' was called 'date_format'
				$field_obj['return_format'] = self::jquery_to_php_date_format( $field_obj['date_format'] );

				// In ACF v4 and below, display_format used jQuery date format
				if ( isset( $field_obj['display_format'] ) ) {
					$field_obj['display_format'] = self::jquery_to_php_date_format( $field_obj['display_format'] );
				}
			}

			return $field_obj;
		}

		return false;
	}

	public static function is_acf_active() {
		return class_exists( '\ACF' );
	}

	// DATES

	/**
	 * Convert a jQuery date format to a PHP one. E.g. 'dd-mm-yy' becomes 'd-m-Y'.
	 *
	 * @see http://api.jqueryui.com/datepicker/ for jQuery formats.
	 *
	 * @param string $jquery_date_format The jQuery date format to convert.
	 * @return string The equivalent PHP date format.
	 */
	public static function jquery_to_php_date_format( $jquery_date_format ) {
		$result = $jquery_date_format;

		if ( false === strpos( $result, 'dd' ) ) {
			$result = str_replace( 'd', 'j', $result );
		}
		if ( false === strpos( $result, 'mm' ) ) {
			$result = str_replace( 'm', 'n', $result );
		}
		if ( false === strpos( $result, 'oo' ) ) {
			$result = str_replace( 'o', 'z', $result );
		}

		return str_replace( [ 'dd', 'oo', 'DD', 'mm', 'MM', 'yy' ], [ 'd', 'z', 'l', 'm', 'F', 'Y' ], $result );
	}

	public static function is_european_date_format( $format ) {
		// It's EU format if the day comes first
		return $format && in_array( substr( $format, 0, 1 ), [ 'd', 'j' ], true );
	}

	/**
	 * Is the value passed a valid UNIX epoch time (i.e. seconds elapsed since 1st January 1970)?
	 *
	 * Not a perfect implementation as it will return false for valid timestamps representing dates
	 * between 31st October 1966 and 3rd March 1973, but this is needed to prevent valid dates held
	 * in numeric formats (e.g. 20171201) being wrongly interpreted as timestamps.
	 *
	 * @param mixed $value The value to check
	 * @return boolean True if $value is a valid epoch timestamp
	 */
	public static function is_unix_epoch_time( $value ) {
		return is_numeric( $value ) && (int) $value == $value && strlen( (string) absint( $value ) ) > 8;
	}

	/**
	 * Convert a date string to a timestamp. A wrapper around strtotime which accounts for dates already formatted
	 * as a timestamp.
	 *
	 * @param string $date The date to convert to a timestamp.
	 * @return int|boolean The timestamp (number of seconds since the Epoch) for this date, or false on failure.
	 */
	public static function strtotime( $date ) {
		if ( self::is_unix_epoch_time( $date ) ) {
			// Already a UNIX timestamp so no need to convert, just return as an int.
			return (int) $date;
		}

		return strtotime( $date );
	}

	// SEARCH

	public static function is_valid_search_term( $search_term ) {
		// Just in case mbstring isn't installed.
		if ( function_exists( 'mb_strlen' ) ) {
			$length = mb_strlen( $search_term, 'UTF-8' );
		} else {
			$length = strlen( $search_term );
		}

		$min_length = max( 1, absint( apply_filters( 'document_library_pro_minimum_search_term_length', 2 ) ) );
		return ! empty( $search_term ) && $length >= $min_length;
	}

	// IMAGES

	public static function get_image_size_width( $size ) {
		$width = false;

		if ( is_array( $size ) ) {
			$width = $size[0];
		} elseif ( is_string( $size ) ) {
			$sizes = wp_get_additional_image_sizes();

			if ( isset( $sizes[ $size ]['width'] ) ) {
				$width = $sizes[ $size ]['width'];
			} elseif ( $w = get_option( "{$size}_size_w" ) ) {
				$width = $w;
			}
		}
		return $width;
	}

	// OTHER

	public static function format_post_link( $post, $link_text = '', $link_class = '' ) {
		$target = '';
		$class  = '';

		if ( ! $link_text ) {
			$link_text = get_the_title( $post );
		}

		if ( apply_filters( 'document_library_pro_open_posts_in_new_tab', false ) ) {
			$target = ' target="_blank"';
		}

		if ( $link_class ) {
			$class = sprintf( ' class="%s"', esc_attr( $link_class ) );
		}

		// If the link text contains no tags, we escape it. Some link_text passed to this function will contain images and other HTML.
		if ( $link_text === wp_strip_all_tags( $link_text ) ) {
			$link_text = esc_html( $link_text );
		}

		return sprintf( '<a href="%1$s"%3$s%4$s>%2$s</a>', esc_url( get_permalink( $post ) ), $link_text, $target, $class );
	}

	public static function get_asset_url( $path = '' ) {
		return plugins_url( 'assets/' . ltrim( $path, '/' ), PLUGIN_FILE );
	}

	public static function get_wrapper_class() {
		$template = sanitize_html_class( strtolower( get_template() ) );
		return apply_filters( 'document_library_pro_wrapper_class', self::TABLE_WRAPPER_CLASS ) . ' ' . $template;
	}

	public static function get_table_class() {
		return apply_filters( 'document_library_pro_class', self::TABLE_CLASS );
	}

	public static function get_language_strings( $post_type ) {
		$single = _x( 'result', 'table results count (single)', 'document-library-pro' );
		$plural = _x( 'results', 'table results count (multiple)', 'document-library-pro' );

		$post_type_string = false;

		if ( is_string( $post_type ) ) {
			$post_type_string = $post_type;
		} elseif ( is_array( $post_type ) && 1 === count( $post_type ) ) {
			$post_type_string = reset( $post_type );
		}

		if ( $post_type_string ) {
			if ( 'attachment' === $post_type_string ) {
				$single = _x( 'file', 'table attachment count (single)', 'document-library-pro' );
				$plural = _x( 'files', 'table attachment count (multiple)', 'document-library-pro' );
			} else {
				$post_type_obj = get_post_type_object( $post_type_string );

				if ( $post_type_obj && ! empty( $post_type_obj->labels ) ) {
					$single = strtolower( $post_type_obj->labels->singular_name );
					$plural = strtolower( $post_type_obj->labels->name );
				}
			}
		}

		$language_strings = [
			// translators: _TOTAL_: the total number of posts; _POSTS_: the content type (e.g. "posts")
			'info'         => __( '_TOTAL_ _POSTS_', 'document-library-pro' ),
			// translators: _POSTS_: the content type (e.g. "posts")
			'infoEmpty'    => __( '0 _POSTS_', 'document-library-pro' ),
			// translators: _POSTS_: the content type (e.g. "posts")
			'emptyTable'   => __( 'No matching _POSTS_.', 'document-library-pro' ),
			// translators: _POSTS_: the content type (e.g. "posts")
			'zeroRecords'  => __( 'No matching _POSTS_.', 'document-library-pro' ),
			'totalsSingle' => $single,
			'totalsPlural' => $plural
		];

		/**
		 * Filter out anything that isn't in the above list after applying the filter. This is because we apply the
		 * filter in Frontend_Scripts::register_scripts as well, and we don't want the global strings in this list.
		 *
		 * @see Frontend_Scripts::register_scripts
		 */
		$language_strings = array_intersect_key( apply_filters( 'document_library_pro_language_defaults', $language_strings ), $language_strings );

		// Replace the singlular and plural placeholders with the actual values (e.g. post and posts).
		return str_replace( [ '_POST_', '_POSTS_' ], [ $language_strings['totalsSingle'], $language_strings['totalsPlural'] ], $language_strings );
	}

	/**
	 * Get an array of WP Pages [ ID => Post Title ]
	 *
	 * @return array
	 */
	public static function get_pages() {
		$pages = get_pages(
			[
				'sort_column'  => 'menu_order',
				'sort_order'   => 'ASC',
				'hierarchical' => 0,
			]
		);

		$options = [ false => __( '— Select —', 'document-library-pro' ) ];

		foreach ( $pages as $page ) {
			$options[ $page->ID ] = ! empty( $page->post_title ) ? $page->post_title : '#' . $page->ID;
		}

		return $options;
	}

	public static function include_template( $template_name ) {
		$template_name = ltrim( $template_name, '/' );

		if ( $located = locate_template( 'dlp_templates/' . $template_name, false ) ) {
			include_once $located;
		} else {
			include_once plugin_dir_path( PLUGIN_FILE ) . 'templates/' . $template_name;
		}
	}

	// DEPRECATED

	public static function sanitize_list_arg( $arg, $allow_space = null ) {
		_deprecated_function( __METHOD__, '2.5.2', 'sanitize_list' );
		return self::sanitize_list( $arg );
	}

	public static function sanitize_numeric_list_arg( $arg ) {
		_deprecated_function( __METHOD__, '2.5.2', 'sanitize_numeric_list' );
		return self::sanitize_numeric_list( $arg );
	}

	public static function sanitize_string_or_bool_arg( $arg ) {
		_deprecated_function( __METHOD__, '2.5.2', 'sanitize_list_or_bool' );
		$maybe_bool = self::maybe_parse_bool( $arg );
		return is_bool( $maybe_bool ) ? $maybe_bool : filter_var( $arg, FILTER_SANITIZE_SPECIAL_CHARS );
	}

	public static function get_the_term_names( $post, $taxonomy, $sep = ', ' ) {
		_deprecated_function( __METHOD__, '2.5.1' );

		$terms = get_the_terms( $post, $taxonomy );

		if ( ! $terms || ! is_array( $terms ) ) {
			return '';
		}
		return implode( $sep, wp_list_pluck( $terms, 'name' ) );
	}

	public static function sanitize_list_arg_allow_space( $arg ) {
		_deprecated_function( __METHOD__, '2.5' );
		return self::sanitize_list( $arg );
	}

	public static function doing_lazy_load() {
		_deprecated_function( __METHOD__, '2.5' );
		return defined( 'DOING_AJAX' ) && DOING_AJAX && is_string( sanitize_key( filter_input( INPUT_POST, 'table_id', FILTER_DEFAULT ) ) );
	}

}
