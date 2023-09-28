<?php
namespace Barn2\Plugin\Document_Library_Pro\Util;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Options as PTP_Options,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util as PTP_Util;

defined( 'ABSPATH' ) || exit;

/**
 * Settings Options Utilities
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
final class Options {

	const DOCUMENT_FIELDS_OPTION_KEY         = 'dlp_document_fields';
	const DOCUMENT_SLUG_OPTION_KEY           = 'dlp_document_slug';
	const DOCUMENT_PAGE_OPTION_KEY           = 'dlp_document_page';
	const SEARCH_PAGE_OPTION_KEY             = 'dlp_search_page';
	const SHORTCODE_OPTION_KEY               = 'dlp_shortcode_defaults';
	const MISC_OPTION_KEY                    = 'dlp_misc_settings';
	const SINGLE_DOCUMENT_DISPLAY_OPTION_KEY = 'dlp_document_fields_display';
	const FOLDER_CLOSE_SVG_OPTION_KEY 	     = 'dlp_folder_icon_svg_closed';
	const FOLDER_OPEN_SVG_OPTION_KEY 	     = 'dlp_folder_icon_svg_open';

	const GENERAL_OPTION_GROUP = 'document_library_pro_general';
	const TABLE_OPTION_GROUP   = 'document_library_pro_table';
	const GRID_OPTION_GROUP    = 'document_library_pro_grid';
	const SINGLE_OPTION_GROUP  = 'document_library_pro_single_document';

	public static function update_shortcode_option( $values = [] ) {
		if ( ! is_array( $values ) || empty( $values ) ) {
			return false;
		}

		$options = self::get_shortcode_options();

		$allowed_keys = array_keys( array_merge( Table_Args::get_table_defaults(), Options::get_dlp_specific_default_args(), [ 'filters_custom' => '' ] ) );

		foreach ( $values as $key => $value ) {
			if ( ! in_array( $key, $allowed_keys ) ) {
				unset( $values[ $key ] );
			}
		}

		update_option( self::SHORTCODE_OPTION_KEY, array_merge( $options, $values ) );
	}

	public static function get_user_shortcode_options() {
		$table_defaults = Table_Args::get_table_defaults();
		$dlp_defaults   = self::get_dlp_specific_default_args();
		$merged = array_merge( $table_defaults, $dlp_defaults );
		return self::get_shortcode_options( array_merge( Table_Args::get_table_defaults(), Options::get_dlp_specific_default_args() ) );
	}

	/**
	 * Retrieve the shortcode options.
	 * We use the PTP function to maintain consistency across grid and table code.
	 *
	 * @param array $defaults
	 * @return array
	 */
	public static function get_shortcode_options( array $defaults = [] ) {
        return self::sanitize_shortcode_options( self::get_option( self::SHORTCODE_OPTION_KEY, $defaults ), $defaults );
    }

	private static function sanitize_shortcode_options( array $options, array $defaults = [] ) {
		if ( empty( $options ) ) {
			return $defaults;
		}

		$options = array_merge( $defaults, $options );

		// Check free text options are not empty.
		foreach ( [ 'columns', 'image_size', 'links' ] as $arg ) {
			if ( empty( $options[$arg] ) && ! empty( $defaults[$arg] ) ) {
				$options[$arg] = $defaults[$arg];
			}
		}

		// Sanitize custom filters option.
		if ( isset( $options['filters'] ) && 'custom' === $options['filters'] ) {
			$options['filters'] = ! empty( $options['filters_custom'] ) ? $options['filters_custom'] : $defaults['filters'];
		}

		unset( $options['filters_custom'] );

		// Sanitize sort by option.
		if ( isset( $options['sort_by'] ) && 'custom' === $options['sort_by'] ) {
			$options['sort_by'] = ! empty( $options['sort_by_custom'] ) ? $options['sort_by_custom'] : $defaults['sort_by'];
		}

		unset( $options['sort_by_custom'] );

		// Convert 'true' or 'false' strings to booleans.
		$options = array_map( [ PTP_Util::class, 'maybe_parse_bool' ], $options );

		// Adjust grid column if converted above.
		if ( isset( $options['grid_columns'] ) && $options['grid_columns'] === true ) {
			$options['grid_columns'] = '1';
		}

		if ( isset( $options['grid_content'] ) ) {
			$options['grid_content'] = self::sanitize_grid_content( $options['grid_content'] );
		}

		return $options;
	}

	/**
	 * Get additional options.
	 * We use the PTP function to maintain consistency across grid and table code.
	 *
	 * @return mixed
	 */
	public static function get_additional_options() {
		return PTP_Options::get_additional_options();
	}

	/**
	 * Retrive the Document post type fields.
	 *
	 * @return array
	 */
	public static function get_document_fields() {
		$document_fields_structure = [
			'editor'        => '1',
			'excerpt'       => '1',
			'thumbnail'     => '1',
			'comments'      => '0',
			'author'        => '1',
			'custom-fields' => '0',
		];

		$fields = get_option( 'dlp_document_fields', $document_fields_structure );

		if ( ! is_array( $fields ) ) {
			$fields = $document_fields_structure;
		}

		$active_fields = array_keys(
			array_filter(
				$fields,
				function( $field ) {
					return $field === '1';
				}
			)
		);

		return $active_fields;
	}

	/**
	 * Retrieve the single document display option.
	 *
	 * @return array
	 */
	public static function get_document_display_fields() {
		$document_fields_display_structure = [
			'excerpt'        => '1',
			'thumbnail'      => '1',
			'comments'       => '0',
			'doc_categories' => '1',
			'doc_tags'       => '1',
			'doc_author'     => '1',
			'file_type'      => '1',
			'custom-fields'  => '0',
			'download_count' => '0',
		];

		$fields = get_option( 'dlp_document_fields_display', $document_fields_display_structure );

		if ( ! is_array( $fields ) ) {
			$fields = $document_fields_display_structure;
		}

		$active_fields = array_keys(
			array_filter(
				$fields,
				function( $field ) {
					return $field === '1';
				}
			)
		);

		return $active_fields;
	}

	/**
	 * Retrieve the document slug
	 *
	 * @return array
	 */
	public static function get_document_slug() {
		return get_option( 'dlp_document_slug', 'slug' );
	}

	/**
	 * Sanitizes grid content data to the correct array format.
	 *
	 * @param mixed $fields
	 * @return array
	 */
	public static function sanitize_grid_content( $fields ) {
		if ( ! is_array( $fields ) ) {
			$fields = self::string_list_to_multicheckbox_array( $fields );
		}

		if ( is_null( $fields ) ) {
			$fields = [];
		}

		$grid_content_structure = [
			'image'          => '0',
			'title'          => '0',
			'file_type'      => '0',
			'file_size'      => '0',
			'download_count' => '0',
			'doc_categories' => '0',
			'excerpt'        => '0',
			'custom_fields'  => '0',
			'link'           => '0',
		];

		$fields = array_merge( $grid_content_structure, $fields );

		$fields = array_map(
			function( $value ) {
				return (bool) $value;
			},
			$fields
		);

		return $fields;
	}

	/**
	 * Convert a string list to a multicheckbox array.
	 *
	 * @param mixed $string_list
	 * @return null|array
	 */
	public static function string_list_to_multicheckbox_array( $string_list ) {
		if ( ! is_string( $string_list ) ) {
			return null;
		}

		$key_array   = array_filter( array_map( 'trim', explode( ',', $string_list ) ) );
		$value_array = array_pad( [], count( $key_array ), '1' );

		$multicheckbox_array = array_combine( $key_array, $value_array );

		return $multicheckbox_array;
	}

	/**
	 * Normalize user arguments provided to shortcode.
	 *
	 * @param array $args
	 * @return array
	 */
	public static function normalize_user_arguments( $args ) {
		// bools
		if ( isset( $args['document_link'] ) ) {
			if ( $args['document_link'] === 'true' ) {
				$args['document_link'] = true;
			}

			if ( $args['document_link'] === 'false' ) {
				$args['document_link'] = false;
			}
		}

		if ( isset( $args['folders'] ) ) {
			if ( $args['folders'] === 'true' ) {
				$args['folders'] = true;
			}

			if ( $args['folders'] === 'false' ) {
				$args['folders'] = false;
			}
		}

		if ( isset( $args['reset_button'] ) ) {
			if ( $args['reset_button'] === 'true' ) {
				$args['reset_button'] = true;
			}

			if ( $args['reset_button'] === 'false' ) {
				$args['reset_button'] = false;
			}
		}

		// link target
		if ( isset( $args['link_target'] ) ) {
			if ( $args['link_target'] === 'blank' ) {
				$args['link_target'] = true;
			}

			if ( $args['link_target'] === 'self' ) {
				$args['link_target'] = false;
			}
		}

		// link_style attribute option deprecation: file_type_icon --> icon
		if ( isset( $args['link_style'] ) && $args['link_style'] === 'file_type_icon' ) {
			$args['link_style'] = 'icon';
		}

		// alternative attributes
		if ( isset( $args['clickable_columns'] ) ) {
			$args['links'] = $args['clickable_columns'];
			unset( $args['clickable_columns'] );
		}

		if ( isset( $args['no_docs_message'] ) ) {
			$args['no_posts_message'] = $args['no_docs_message'];
			unset( $args['no_docs_message'] );
		}

		if ( isset( $args['no_docs_filtered_message'] ) ) {
			$args['no_posts_filtered_message'] = $args['no_docs_filtered_message'];
			unset( $args['no_docs_filtered_message'] );
		}

		if ( isset( $args['docs_per_page'] ) ) {
			$args['rows_per_page'] = $args['docs_per_page'];
			unset( $args['docs_per_page'] );
		}

		if ( isset( $args['doc_limit'] ) ) {
			$args['post_limit'] = $args['doc_limit'];
			unset( $args['doc_limit'] );
		}

		if ( isset( $args['clickable_fields'] ) ) {
			$args['links'] = $args['clickable_fields'];
			unset( $args['clickable_fields'] );
		}

		// handle shared attributes
		if ( isset( $args['layout'] ) && in_array( $args['layout'], [ 'table', 'grid' ], true ) ) {
			$args['layout'] = $args['layout'];
		} else {
			$args['layout'] = Table_Args::get_site_defaults()['layout'];
		}

		if ( isset( $args['content'] ) ) {
			if ( $args['layout'] === 'grid' ) {
				$args['grid_content'] = $args['content'];
				unset( $args['content'] );
			} elseif ( $args['layout'] === 'table' ) {
				$args['columns'] = $args['content'];
				unset( $args['content'] );
			}
		}

		if ( isset( $args['folder_status'] ) && 'open' !== $args['folder_status'] && 'closed' !== $args['folder_status'] ) {
			$args['folder_status_custom'] = $args['folder_status'];
			$args['folder_status']        = 'custom';
		}

		return $args;
	}

	/**
	 * Retrieves the default args specific to DLP (as opposed to the PTP defaults)
	 *
	 * @return string[]
	 */
	public static function get_dlp_specific_default_args() {
		$dlp_args = [
			'multi_download_button'     => 'above',
			'multi_download_text'       => __( 'Download Selected Documents', 'document-library-pro' ),
			'accessing_documents'       => 'link',
			'preview'                   => false,
			'preview_style'             => 'button_icon',
			'preview_text'              => __( 'Preview', 'document-library-pro' ),
			'document_link'             => true,
			'link_style'                => 'button',
			'link_text'                 => __( 'Download', 'document-library-pro' ),
			'link_destination'          => 'direct',
			'link_target'               => false,
			'folders'                   => false,
			'folders_order_by'          => 'name',
			'folders_order'             => 'ASC',
			'folder_status'             => 'closed',
			'folder_status_custom'      => '',
			'folder_icon_custom'        => false,
			'folder_icon_color'         => '#f6b900',
			'folder_icon_subcolor'      => '#333',
			'folder_icon_svg_closed'    => '',
			'folder_icon_svg_open'      => '',
			'layout'                    => 'table',
			'grid_content'              => [
				'image'          => '1',
				'title'          => '1',
				'file_type'      => '0',
				'file_size'      => '0',
				'doc_categories' => '0',
				'download_count' => '0',
				'excerpt'        => '1',
				'custom_fields'  => '0',
				'link'           => '1',
			],
			'grid_columns'              => 'autosize',
			'doc_tag'                   => '',
			'doc_category'              => '',
			'doc_author'                => '',
			'exclude_doc_category'      => '',
			'columns'                   => 'title,excerpt,doc_categories,link',
			'links'                     => 'title,doc_categories,doc_tags,terms,doc_author',
			'version_control'           => false,
			'version_control_mode'      => 'keep',
		];

		return $dlp_args;
	}

	/**
	 * Retrieve an option.
	 *
	 * @param string $option
	 * @param mixed $default
	 * @return mixed
	 */
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

	/**
	 * Return the version control mode (keep or delete) or false if version control is disabled
	 *
	 * @return bool|string Either 'keep', 'delete' or false
	 */
	public static function get_version_control_mode() {
		$misc_options = self::get_option( self::DOCUMENT_FIELDS_OPTION_KEY, [] );
		$is_vc_active = $misc_options && isset( $misc_options['version_control'] ) ? (bool) $misc_options['version_control'] : false;

		if ( $is_vc_active ) {
			return $misc_options['version_control_mode'];
		}

		return false;
	}

	/**
	 * Determine whether the version control is enabled and the replacing file strategy is set to `keep`
	 *
	 * @return bool
	 */
	public static function is_version_history_active() {
		return 'keep' === self::get_version_control_mode();
	}

	public static function get_search_page_option() {
		$search_page = (int) get_option( self::SEARCH_PAGE_OPTION_KEY, false ) ?? false;

		if ( $search_page && in_array( get_post_status( $search_page ), [ false, 'trash' ], true ) ) {
			$search_page = false;
		}

		return $search_page;
	}

	/**
	 * Determine if admin notifications for frontend submissions are active.
	 *
	 * @return boolean
	 */
	public static function is_submission_admin_email_active() {
		$options = self::get_option( self::DOCUMENT_FIELDS_OPTION_KEY, [] );
		return isset( $options['fronted_email_admin'] ) && $options['fronted_email_admin'] === '1';
	}

	/**
	 * Determine if frontend submissions are moderated.
	 *
	 * @return boolean
	 */
	public static function is_submission_moderated() {
		$options = self::get_option( self::DOCUMENT_FIELDS_OPTION_KEY, [] );
		return isset( $options['fronted_moderation'] ) && $options['fronted_moderation'] === '1';
	}
}
