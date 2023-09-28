<?php

namespace Barn2\Plugin\Document_Library_Pro\Admin;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Post_Type,
	Barn2\Plugin\Document_Library_Pro\Taxonomies;
use Barn2\Plugin\Document_Library_Pro\Util\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the custom menu
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Menu implements Registerable, Service {

	private $license;

	/**
	 * Constructor.
	 *
	 * @param Plugin $plugin
	 */
	public function __construct( $plugin ) {
		$this->license = $plugin->get_license();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'add_menu_pages' ] );
		add_action( 'admin_head', [ $this, 'remove_submenu_link' ] );
		add_filter( 'parent_file', [ $this, 'keep_menu_open' ] );
		add_filter( 'submenu_file', [ $this, 'highlight_submenus' ] );
	}

	/**
	 * Registers our custom menu and sub menu pages.
	 * Unactivated license only sees the Settings sub menu
	 *
	 * @return void
	 */
	public function add_menu_pages() {
		// Main Menu
		add_menu_page(
			__( 'Documents', 'document-library-pro' ),
			__( 'Documents', 'document-library-pro' ),
			'edit_posts',
			'document_library_pro',
			'',
			'dashicons-media-document',
			26
		);

		// Hack to remove duplicate submenu created by WordPress
		if ( ! $this->license->is_valid() ) {
			add_submenu_page(
				'document_library_pro',        // parent slug, same as above menu slug
				'',        // empty page title
				'',        // empty menu title
				'edit_posts',        // same capability as above
				'document_library_pro',        // same menu slug as parent slug
				''        // same function as above
			);
		}

		if ( $this->license->is_valid() ) {
			// Add New
			add_submenu_page(
				'document_library_pro',
				__( 'Add New', 'document-library-pro' ),
				__( 'Add New', 'document-library-pro' ),
				'edit_posts',
				'/post-new.php?post_type=dlp_document',
				'',
				5
			);

			// Categories
			add_submenu_page(
				'document_library_pro',
				__( 'Categories', 'document-library-pro' ),
				__( 'Categories', 'document-library-pro' ),
				'edit_posts',
				sprintf( '/edit-tags.php?taxonomy=%s&post_type=dlp_document', Taxonomies::CATEGORY_SLUG ),
				'',
				6
			);

			// Tags
			add_submenu_page(
				'document_library_pro',
				__( 'Tags', 'document-library-pro' ),
				__( 'Tags', 'document-library-pro' ),
				'edit_posts',
				sprintf( '/edit-tags.php?taxonomy=%s&post_type=dlp_document', Taxonomies::TAG_SLUG ),
				'',
				7
			);

			// Authors
			$enabled_fields = Options::get_document_fields();
			$author_enabled = in_array( 'author', $enabled_fields );

			if ( $author_enabled ) {
				add_submenu_page(
					'document_library_pro',
					__( 'Authors', 'document-library-pro' ),
					__( 'Authors', 'document-library-pro' ),
					'edit_posts',
					sprintf( '/edit-tags.php?taxonomy=%s&post_type=dlp_document', Taxonomies::AUTHOR_SLUG ),
					'',
					7
				);
			}

			foreach ( $this->get_custom_taxonomies() as $taxonomy ) {
				$labels = get_taxonomy_labels( get_taxonomy( $taxonomy ) );

				add_submenu_page(
					'document_library_pro',
					$labels->name,
					$labels->name,
					'edit_posts',
					"/edit-tags.php?taxonomy=$taxonomy&post_type=dlp_document",
					'',
					8
				);
			}
		}
	}

	/**
	 * Hide the Import CSV sub menu link
	 */
	public function remove_submenu_link() {
		remove_submenu_page( 'document_library_pro', 'dlp_import_csv' );
	}

	/**
	 * Need to make sure the Documents menu stays open when we are on a taxonomy page
	 *
	 * @param   string  $parent_file The filename of the parent menu.
	 * @return  string  $parent_file
	 */
	public function keep_menu_open( $parent_file ) {
		global $pagenow;

		$taxonomy = filter_input( INPUT_GET, 'taxonomy', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		foreach ( $this->get_all_taxonomies() as $custom_taxonomy ) {
			if ( $pagenow === 'edit-tags.php' && $taxonomy === $custom_taxonomy ) {
				$parent_file = 'document_library_pro';
			}

			if ( $pagenow === 'term.php' && $taxonomy === $custom_taxonomy ) {
				$parent_file = 'document_library_pro';
			}
		}

		return $parent_file;
	}

	/**
	 * Highlight submenu pages.
	 *
	 * @param   string  $submenu_file The filename of the sub menu.
	 * @return  string  $submenu_file
	 */
	public function highlight_submenus( $submenu_file ) {
		global $pagenow, $plugin_page;

		$taxonomy = filter_input( INPUT_GET, 'taxonomy', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		foreach ( $this->get_all_taxonomies() as $custom_taxonomy ) {
			if ( in_array( $pagenow, [ 'term.php', 'edit-tags.php' ], true ) && $taxonomy === $custom_taxonomy ) {
				$submenu_file = "edit-tags.php?taxonomy=$taxonomy&post_type=dlp_document";
			}
		}

		if ( $plugin_page === 'dlp_import_csv' ) {
			$submenu_file = 'dlp_import';
		}

		return $submenu_file;
	}

	/**
	 * Retrieves custom taxonomies not registered by DLP.
	 *
	 * @return string[]
	 */
	private function get_custom_taxonomies() {
		$taxonomies         = get_object_taxonomies( Post_Type::POST_TYPE_SLUG );
		$default_taxonomies = [ Taxonomies::CATEGORY_SLUG, Taxonomies::TAG_SLUG, Taxonomies::AUTHOR_SLUG, Taxonomies::FILE_TYPE_SLUG ];
		$custom_taxonomies  = array_diff( $taxonomies, $default_taxonomies );

		return $custom_taxonomies;
	}

	/**
	 * Retrieves all taxonomies registered to the post type for display.
	 *
	 * @return string[]
	 */
	private function get_all_taxonomies() {
		$custom_taxonomies  = get_object_taxonomies( Post_Type::POST_TYPE_SLUG );
		$hidden_taxonomies = [ Taxonomies::FILE_TYPE_SLUG ];
		$taxonomies = array_diff( $custom_taxonomies, $hidden_taxonomies );

		return $taxonomies;
	}
}
