<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service;
use Barn2\Plugin\Document_Library_Pro\Util\Options;

defined( 'ABSPATH' ) || exit;

/**
 * Register the Document Library associated taxonomies
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Taxonomies implements Registerable, Service {
	const CATEGORY_SLUG          = 'doc_categories';
	const TAG_SLUG               = 'doc_tags';
	const AUTHOR_SLUG            = 'doc_author';
	const FILE_TYPE_SLUG         = 'file_type';
	const DOCUMENT_DOWNLOAD_SLUG = 'document_download';

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'init', [ $this, 'register_document_category' ] );
		add_action( 'init', [ $this, 'register_document_tag' ] );
		add_action( 'init', [ $this, 'register_document_author' ] );
		add_action( 'init', [ $this, 'register_file_types' ] );
		add_action( 'init', [ $this, 'register_document_download_taxonomy' ] );
	}

	/**
	 * Registers the document category taxonomy.
	 */
	public function register_document_category() {
		$labels = [
			'name'                       => _x( 'Document Categories', 'Taxonomy General Name', 'document-library-pro' ),
			'singular_name'              => _x( 'Document Category', 'Taxonomy Singular Name', 'document-library-pro' ),
			'menu_name'                  => __( 'Categories', 'document-library-pro' ),
			'all_items'                  => __( 'All Categories', 'document-library-pro' ),
			'parent_item'                => __( 'Parent Category', 'document-library-pro' ),
			'parent_item_colon'          => __( 'Parent Category:', 'document-library-pro' ),
			'new_item_name'              => __( 'New Category Name', 'document-library-pro' ),
			'add_new_item'               => __( 'Add New Category', 'document-library-pro' ),
			'edit_item'                  => __( 'Edit Category', 'document-library-pro' ),
			'update_item'                => __( 'Update Category', 'document-library-pro' ),
			'view_item'                  => __( 'View Category', 'document-library-pro' ),
			'separate_items_with_commas' => __( 'Separate categories with commas', 'document-library-pro' ),
			'add_or_remove_items'        => __( 'Add or remove categories', 'document-library-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'document-library-pro' ),
			'popular_items'              => __( 'Popular Categories', 'document-library-pro' ),
			'search_items'               => __( 'Search Categories', 'document-library-pro' ),
			'not_found'                  => __( 'Not Found', 'document-library-pro' ),
			'no_terms'                   => __( 'No categories', 'document-library-pro' ),
			'items_list'                 => __( 'Categories list', 'document-library-pro' ),
			'items_list_navigation'      => __( 'Categories list navigation', 'document-library-pro' ),
		];

		$args = [
			'labels'            => $labels,
			'public'            => true,
			'hierarchical'      => true,
			'rewrite'           => [
				'slug'         => 'document-category',
				'hierarchical' => true
			],
			'capabilities'      => [ Post_Type::POST_TYPE_SLUG ],
			'show_admin_column' => true,
		];

		register_taxonomy( self::CATEGORY_SLUG, Post_Type::POST_TYPE_SLUG, $args );
	}

	/**
	 * Registers the document tag taxonomy.
	 */
	public function register_document_author() {

		$enabled_fields = Options::get_document_fields();

		$labels = [
			'name'                       => _x( 'Document Authors', 'Taxonomy General Name', 'document-library-pro' ),
			'singular_name'              => _x( 'Document Author', 'Taxonomy Singular Name', 'document-library-pro' ),
			'menu_name'                  => __( 'Authors', 'document-library-pro' ),
			'all_items'                  => __( 'All Authors', 'document-library-pro' ),
			'parent_item'                => __( 'Parent Author', 'document-library-pro' ),
			'parent_item_colon'          => __( 'Parent Author:', 'document-library-pro' ),
			'new_item_name'              => __( 'New Author Name', 'document-library-pro' ),
			'add_new_item'               => __( 'Add New Author', 'document-library-pro' ),
			'edit_item'                  => __( 'Edit Author', 'document-library-pro' ),
			'update_item'                => __( 'Update Author', 'document-library-pro' ),
			'view_item'                  => __( 'View Author', 'document-library-pro' ),
			'separate_items_with_commas' => __( 'Separate authors with commas', 'document-library-pro' ),
			'add_or_remove_items'        => __( 'Add or remove authors', 'document-library-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'document-library-pro' ),
			'popular_items'              => __( 'Popular Authors', 'document-library-pro' ),
			'search_items'               => __( 'Search Authors', 'document-library-pro' ),
			'not_found'                  => __( 'Not Found', 'document-library-pro' ),
			'no_terms'                   => __( 'No authors', 'document-library-pro' ),
			'items_list'                 => __( 'Authors list', 'document-library-pro' ),
			'items_list_navigation'      => __( 'Authors list navigation', 'document-library-pro' ),
		];

		$enabled = in_array( 'author', $enabled_fields );

		$args = [
			'labels'            => $labels,
			'public'            => false,
			'hierarchical'      => false,
			'rewrite'           => false,
			'capabilities'      => [ Post_Type::POST_TYPE_SLUG ],
			'show_admin_column' => $enabled,
			'show_in_menu' => $enabled,
			'show_in_nav_menus' => $enabled,
			'show_ui'           => $enabled,
		];

		register_taxonomy( self::AUTHOR_SLUG, Post_Type::POST_TYPE_SLUG, $args );
	}

	/**
	 * Registers the document tag taxonomy.
	 */
	public function register_document_tag() {
		$labels = [
			'name'                       => _x( 'Document Tags', 'Taxonomy General Name', 'document-library-pro' ),
			'singular_name'              => _x( 'Document Tag', 'Taxonomy Singular Name', 'document-library-pro' ),
			'menu_name'                  => __( 'Tags', 'document-library-pro' ),
			'all_items'                  => __( 'All Tags', 'document-library-pro' ),
			'parent_item'                => __( 'Parent Tag', 'document-library-pro' ),
			'parent_item_colon'          => __( 'Parent Tag:', 'document-library-pro' ),
			'new_item_name'              => __( 'New Tag Name', 'document-library-pro' ),
			'add_new_item'               => __( 'Add New Tag', 'document-library-pro' ),
			'edit_item'                  => __( 'Edit Tag', 'document-library-pro' ),
			'update_item'                => __( 'Update Tag', 'document-library-pro' ),
			'view_item'                  => __( 'View Tag', 'document-library-pro' ),
			'separate_items_with_commas' => __( 'Separate tags with commas', 'document-library-pro' ),
			'add_or_remove_items'        => __( 'Add or remove tags', 'document-library-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'document-library-pro' ),
			'popular_items'              => __( 'Popular Tags', 'document-library-pro' ),
			'search_items'               => __( 'Search Tags', 'document-library-pro' ),
			'not_found'                  => __( 'Not Found', 'document-library-pro' ),
			'no_terms'                   => __( 'No tags', 'document-library-pro' ),
			'items_list'                 => __( 'Tags list', 'document-library-pro' ),
			'items_list_navigation'      => __( 'Tags list navigation', 'document-library-pro' ),
		];

		$args = [
			'labels'            => $labels,
			'public'            => true,
			'hierarchical'      => false,
			'rewrite'           => [ 'slug' => 'document-tag' ],
			'capabilities'      => [ Post_Type::POST_TYPE_SLUG ],
			'show_admin_column' => true,
		];

		register_taxonomy( self::TAG_SLUG, Post_Type::POST_TYPE_SLUG, $args );
	}

	/**
	 * Registers the file types taxonomy.
	 */
	public function register_file_types() {
		$labels = [
			'name'                       => _x( 'File Types', 'Taxonomy General Name', 'document-library-pro' ),
			'singular_name'              => _x( 'File Type', 'Taxonomy Singular Name', 'document-library-pro' ),
			'menu_name'                  => __( 'File Types', 'document-library-pro' ),
			'all_items'                  => __( 'All File Types', 'document-library-pro' ),
			'parent_item'                => __( 'Parent File Type', 'document-library-pro' ),
			'parent_item_colon'          => __( 'Parent File Type:', 'document-library-pro' ),
			'new_item_name'              => __( 'New File Type Name', 'document-library-pro' ),
			'add_new_item'               => __( 'Add New File Type', 'document-library-pro' ),
			'edit_item'                  => __( 'Edit File Type', 'document-library-pro' ),
			'update_item'                => __( 'Update File Type', 'document-library-pro' ),
			'view_item'                  => __( 'View File Type', 'document-library-pro' ),
			'separate_items_with_commas' => __( 'Separate file types with commas', 'document-library-pro' ),
			'add_or_remove_items'        => __( 'Add or remove file types', 'document-library-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'document-library-pro' ),
			'popular_items'              => __( 'Popular File Types', 'document-library-pro' ),
			'search_items'               => __( 'Search File Types', 'document-library-pro' ),
			'not_found'                  => __( 'Not Found', 'document-library-pro' ),
			'no_terms'                   => __( 'No file types', 'document-library-pro' ),
			'items_list'                 => __( 'File types list', 'document-library-pro' ),
			'items_list_navigation'      => __( 'File types list navigation', 'document-library-pro' ),
		];

		$args = [
			'labels'            => $labels,
			'public'            => false,
			'publicly_querable' => true,
			'hierarchical'      => false,
			'rewrite'           => true,
			'capabilities'      => [ Post_Type::POST_TYPE_SLUG ],
			'show_admin_column' => true,
		];

		register_taxonomy( self::FILE_TYPE_SLUG, Post_Type::POST_TYPE_SLUG, $args );
	}

	/**
	 * Registers the document download taxonomy (attached to media library items).
	 */
	public function register_document_download_taxonomy() {
		$labels = [
			'name'                       => _x( 'Document Download', 'Taxonomy General Name', 'document-library-pro' ),
			'singular_name'              => _x( 'Document Download', 'Taxonomy Singular Name', 'document-library-pro' ),
			'menu_name'                  => __( 'Document Downloads', 'document-library-pro' ),
			'all_items'                  => __( 'All Items', 'document-library-pro' ),
			'parent_item'                => __( 'Parent Item', 'document-library-pro' ),
			'parent_item_colon'          => __( 'Parent Item:', 'document-library-pro' ),
			'new_item_name'              => __( 'New Item Name', 'document-library-pro' ),
			'add_new_item'               => __( 'Add New Item', 'document-library-pro' ),
			'edit_item'                  => __( 'Edit Item', 'document-library-pro' ),
			'update_item'                => __( 'Update Item', 'document-library-pro' ),
			'view_item'                  => __( 'View Item', 'document-library-pro' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'document-library-pro' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'document-library-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'document-library-pro' ),
			'popular_items'              => __( 'Popular Items', 'document-library-pro' ),
			'search_items'               => __( 'Search Items', 'document-library-pro' ),
			'not_found'                  => __( 'Not Found', 'document-library-pro' ),
			'no_terms'                   => __( 'No items', 'document-library-pro' ),
			'items_list'                 => __( 'Items list', 'document-library-pro' ),
			'items_list_navigation'      => __( 'Items list navigation', 'document-library-pro' ),
		];

		$args = [
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => false,
			'publicly_querable' => true,
			'rewrite'           => [ 'slug' => 'document-download' ],
		];

		register_taxonomy( self::DOCUMENT_DOWNLOAD_SLUG, 'attachment', $args );
	}
}
