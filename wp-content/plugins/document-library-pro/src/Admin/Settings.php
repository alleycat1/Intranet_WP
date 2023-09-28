<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Util\Options,
	Barn2\Plugin\Document_Library_Pro\Util\SVG_Icon,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args as PTP_Table_Args;

defined( 'ABSPATH' ) || exit;

/**
 * Settings Registry
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Settings implements Registerable, Service {

	private $plugin;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_init', [ $this, 'filter_allowed_options' ] );
	}

	/**
	 * Register our settings parent options with Settings API.
	 */
	public function register_settings() {

		register_setting(
			Options::GENERAL_OPTION_GROUP,
			Options::DOCUMENT_FIELDS_OPTION_KEY,
			[
				'type'              => 'string', // array type not supported, so just use string
				'description'       => 'Document Fields',
				'sanitize_callback' => [ $this, 'sanitize_document_fields' ]
			]
		);

		register_setting(
			Options::GENERAL_OPTION_GROUP,
			Options::DOCUMENT_PAGE_OPTION_KEY,
			[
				'type'              => 'string', // array type not supported, so just use string
				'description'       => 'Document Library Pro default page',
				'sanitize_callback' => [ $this, 'sanitize_document_page' ]
			]
		);

		register_setting(
			Options::GENERAL_OPTION_GROUP,
			Options::DOCUMENT_SLUG_OPTION_KEY,
			[
				'type'              => 'string', // array type not supported, so just use string
				'description'       => 'Document Library Pro default page',
				'sanitize_callback' => [ $this, 'sanitize_document_slug' ]
			]
		);

		register_setting(
			Options::GENERAL_OPTION_GROUP,
			Options::SEARCH_PAGE_OPTION_KEY,
			[
				'type'              => 'string', // array type not supported, so just use string
				'description'       => 'Document Library Pro search page',
				'sanitize_callback' => [ $this, 'sanitize_search_page_setting' ]
			]
		);

		register_setting(
			Options::GENERAL_OPTION_GROUP,
			Options::FOLDER_CLOSE_SVG_OPTION_KEY,
			[
				'type'              => 'string', // array type not supported, so just use string
				'description'       => 'Custom SVG icon for closed folder',
				'sanitize_callback' => [ $this, 'sanitize_svg' ]
			]
		);

		register_setting(
			Options::GENERAL_OPTION_GROUP,
			Options::FOLDER_OPEN_SVG_OPTION_KEY,
			[
				'type'              => 'string', // array type not supported, so just use string
				'description'       => 'Custom SVG icon for open folder',
				'sanitize_callback' => [ $this, 'sanitize_svg' ]
			]
		);

		register_setting(
			Options::TABLE_OPTION_GROUP,
			Options::SHORTCODE_OPTION_KEY,
			[
				'type'              => 'string', // array type not supported, so just use string
				'description'       => 'Document Library Pro shortcode defaults',
				'sanitize_callback' => [ $this, 'sanitize_shortcode_settings' ]
			]
		);

		register_setting(
			Options::TABLE_OPTION_GROUP,
			Options::MISC_OPTION_KEY,
			[
				'type'              => 'string', // array type not supported, so just use string
				'description'       => 'Document Library Pro miscellaneous settings',
				'sanitize_callback' => [ $this, 'sanitize_misc_settings' ]
			]
		);

		register_setting(
			Options::SINGLE_OPTION_GROUP,
			Options::SINGLE_DOCUMENT_DISPLAY_OPTION_KEY,
			[
				'type'              => 'string', // array type not supported, so just use string
				'description'       => 'Document Display',
				'sanitize_callback' => [ $this, 'sanitize_document_display_fields' ]
			]
		);
	}

	/**
	 * Hook into the allowed_options filter.
	 * Back compatibility ( < 5.5 ) included with 'whitelist_options'.
	 */
	public function filter_allowed_options() {
		if ( function_exists( 'add_allowed_options' ) ) {
			add_filter( 'allowed_options', [ $this, 'allowed_options' ] );
		} else {
			add_filter( 'whitelist_options', [ $this, 'allowed_options' ] );
		}
	}

	/**
	 * Adjust the allowed_options so that single settings keys can be shared across tabs.
	 *
	 * @param array $options
	 * @return array
	 */
	public function allowed_options( $options ) {
		$new_options[ Options::GENERAL_OPTION_GROUP ] = [ Options::SHORTCODE_OPTION_KEY ];
		$new_options[ Options::GRID_OPTION_GROUP ]    = [
			Options::SHORTCODE_OPTION_KEY,
			Options::MISC_OPTION_KEY
		];

		if ( function_exists( 'add_allowed_options' ) ) {
			$options = add_allowed_options( $new_options, $options );
		} else {
			$options = add_option_whitelist( $new_options, $options );
		}

		return $options;
	}

	/**
	 * Sanitize the document post type fields.
	 *
	 * @param mixed $args
	 * @return string[]
	 */
	public function sanitize_document_fields( $args ) {
		$this->plugin->get_license_setting()->save_posted_license_key();

		if ( is_null( $args ) ) {
			$args = [];
		}

		$document_fields_structure = [
			'editor'    => '0',
			'excerpt'   => '0',
			'thumbnail' => '0',
			'comments'  => '0',
			'author' => '0',
		];

		return array_merge( $document_fields_structure, $args );
	}

	/**
	 * Sanitize the document display fields.
	 *
	 * @param mixed $args
	 * @return string[]
	 */
	public function sanitize_document_display_fields( $args ) {
		if ( is_null( $args ) ) {
			$args = [];
		}

		$document_fields_structure = [
			'excerpt'        => '0',
			'thumbnail'      => '0',
			'comments'       => '0',
			'doc_categories' => '0',
			'doc_tags'       => '0',
			'doc_author'     => '0',
			'file_type'      => '0',
			'download_count' => '0',
		];

		return array_merge( $document_fields_structure, $args );
	}

	/**
	 * Sanitize the Document Page setting.
	 *
	 * @param string $page_setting
	 * @return string
	 */
	public function sanitize_document_page( $page_setting ) {
		if ( ! is_numeric( $page_setting ) ) {
			return;
		}

		$page = get_post( absint( $page_setting ) );

		$update_page = [ 'ID' => $page->ID ];

		// Add the doc library shortcode if we don't have it
		if ( $page && 'publish' === $page->post_status && ! stripos( $page->post_content, '[doc_library' ) ) {
			$update_page['post_content'] = $page->post_content . '<!-- wp:shortcode -->[doc_library]<!-- /wp:shortcode -->';
		}

		// We always update post when changing pages to clear any cache
		wp_update_post( $update_page );

		return $page_setting;
	}

	/**
	 * Sanitize the Search Page setting.
	 *
	 * @param string $page_setting
	 * @return string
	 */
	public function sanitize_search_page_setting( $page_setting ) {
		if ( ! is_numeric( $page_setting ) ) {
			return '';
		}

		$page = get_post( absint( $page_setting ) );

		$update_page = [ 'ID' => $page->ID ];

		// Update post when changing pages to clear any cache
		wp_update_post( $update_page );

		return $page_setting;
	}

	/**
	 * Sanitize the Document Slug setting.
	 *
	 * @param string $slug_setting
	 * @return string
	 */
	public function sanitize_document_slug( $slug_setting ) {
		if ( ! is_string( $slug_setting ) ) {
			return 'document';
		}

		$slug_setting = sanitize_key( $slug_setting );

		update_option( 'dlp_should_flush_rewrite_rules', true );

		return $slug_setting;
	}

	/**
	 * Sanitize the shortcode setting depending on the setting tab.
	 *
	 * @param mixed $args
	 * @return array
	 */
	public function sanitize_shortcode_settings( $args ) {
		$existing_options = $this->get_existing_shortcode_options();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$option_page = $_REQUEST['option_page'];

		if ( is_null( $args ) ) {
			$args = [];
		}

		if ( $option_page === Options::GENERAL_OPTION_GROUP ) {
			// Check ints
			foreach ( [ 'rows_per_page', 'content_length', 'excerpt_length', 'post_limit' ] as $arg ) {

				if ( ! isset( $args[ $arg ] ) ) {
					continue;
				}

				$int_val = filter_var( $args[ $arg ], FILTER_VALIDATE_INT );

				if ( false === $int_val ) {
					$args[ $arg ] = PTP_Table_Args::get_table_defaults()[ $arg ];
				}
				// These can be a positive int or -1 only
				if ( 0 === $int_val || $int_val < -1 ) {
					$args[ $arg ] = -1;
				}
			}

			// Check bools
			foreach ( [ 'lightbox', 'shortcodes', 'link_target', 'document_link', 'preview', 'folders', 'reset_button' ] as $arg ) {
				if ( ! isset( $args[ $arg ] ) ) {
					$args[ $arg ] = false;
				}
				$args[ $arg ] = filter_var( $args[ $arg ], FILTER_VALIDATE_BOOLEAN );
			}

			// Check for empties
			foreach ( [ 'links' ] as $arg ) {
				if ( empty( $args[ $arg ] ) ) {
					$args[ $arg ] = PTP_Table_Args::get_table_defaults()[ $arg ];
				}
			}
		} elseif ( $option_page === Options::TABLE_OPTION_GROUP ) {
			// Check for empties
			foreach ( [ 'image_size' ] as $arg ) {
				if ( empty( $args[ $arg ] ) ) {
					$args[ $arg ] = PTP_Table_Args::get_table_defaults()[ $arg ];
				}
			}

			// Sanitize image size
			if ( isset( $args['image_size'] ) ) {
				$args['image_size'] = preg_replace( '/[^\wx\-]/', '', $args['image_size'] );
			}

			// Checkboxes
			foreach ( [ 'lazy_load', 'cache' ] as $arg ) {
				if ( ! isset( $args[ $arg ] ) ) {
					$args[ $arg ] = false;
				}
				$args[ $arg ] = filter_var( $args[ $arg ], FILTER_VALIDATE_BOOLEAN );
			}
		} elseif ( $option_page === Options::GRID_OPTION_GROUP ) {
			if ( isset( $args['grid_content'] ) ) {
				$args['grid_content'] = Options::sanitize_grid_content( $args['grid_content'] );
			} else {
				add_settings_error(
					'document-library-pro',
					'grid-fields-empty',
					__( 'You need to select at least one option for Display. Your Display setting has been reverted to the previous configuration.', 'document-library-pro' ),
					'warning'
				);

				$args['grid_content'] = Options::sanitize_grid_content( $existing_options['grid_content'] );
			}
		}

		$merge_settings = array_merge( $existing_options, $args );

		return $merge_settings;
	}

	/**
	 * Sanitize the Misc Settings.
	 *
	 * @param mixed $args
	 * @return mixed
	 */
	public function sanitize_misc_settings( $args ) {
		$existing_options = $this->get_existing_misc_settings();

		if ( isset( $args['cache_expiry'] ) ) {
			$args['cache_expiry'] = filter_var( $args['cache_expiry'], FILTER_VALIDATE_INT, [ 'options' => [ 'default' => 6 ] ] );
		}

		$merge_settings = array_merge( $existing_options, $args );

		return $merge_settings;
	}

	/**
	 * Sanitize a SVG string.
	 */
	public function sanitize_svg( $svg ) {
		return SVG_Icon::sanitize_svg( $svg );
	}

	/**
	 * Retrieve the existing shortcode options.
	 *
	 * This is used so we can merge the shared settings from other tabs before
	 * WordPress saves the setting.
	 *
	 * @return array
	 */
	private function get_existing_shortcode_options() {
		$current_options = get_option( Options::SHORTCODE_OPTION_KEY );
		$default_args    = array_merge( PTP_Table_Args::get_table_defaults(), Options::get_dlp_specific_default_args(), [ 'cache_expiry' => 6 ] );

		$option_keys = [
			// general
			'layout',
			'document_link',
			'link_style',
			'link_text',
			'link_destination',
			'link_target',
			'links',
			'preview',
			'preview_style',
			'preview_text',
			'folders',
			'folders_order_by',
			'folders_order',
			'folder_status',
			'folder_status_custom',
			'folder_icon_custom',
			'folder_icon_color',
			'folder_icon_subcolor',
			'lightbox',
			'shortcodes',
			'excerpt_length',
			'content_length',
			'rows_per_page',
			'paging_type',
			'pagination',
			'totals',
			'sort_by',
			'sort_order',
			'version_control',
			'version_control_mode',
			// document tables
			'columns',
			'image_size',
			'accessing_documents',
			'multi_download_button',
			'multi_download_text',
			'lazy_load',
			'post_limit',
			'cache',
			'cache_expiry',
			'filters',
			'filters_custom', // Saved to 'filters'
			'page_length',
			'search_box',
			'reset_button',
			// grid
			'grid_content',
			'grid_columns'
		];

		$existing_options = [];

		foreach ( $option_keys as $option ) {
			$existing_options[ $option ] = isset( $current_options[ $option ] ) ? $current_options[ $option ] : $default_args[ $option ] ?? '';
		}

		return $existing_options;
	}

	/**
	 * Retrieve the existing misc options.
	 *
	 * This is used so we can merge the shared settings from other tabs before
	 * WordPress saves the setting.
	 *
	 * @return array
	 */
	private function get_existing_misc_settings() {
		$current_options = get_option( Options::MISC_OPTION_KEY );

		$option_keys = [
			// table
			'design',
			'external_border',
			'header_border',
			'body_border',
			'header_bg',
			'header_text',
			'body_bg',
			'body_bg_alt',
			'body_text',
			'table_spacing',
			// grid
			'grid_image_bg',
			'grid_category_bg',
		];

		$existing_options = [];

		foreach ( $option_keys as $option ) {
			if ( isset( $current_options[ $option ] ) ) {
				$existing_options[ $option ] = $current_options[ $option ];
			} else {
				continue;
			}
		}

		return $existing_options;
	}
}
