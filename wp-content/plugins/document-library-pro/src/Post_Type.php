<?php

namespace Barn2\Plugin\Document_Library_Pro;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service;

defined( 'ABSPATH' ) || exit;

/**
 * Register the Document Library post type
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Type implements Registerable, Service {

	const POST_TYPE_SLUG = 'dlp_document';

	private $default_fields;
	private $document_slug;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->default_fields = array_merge( [ 'author', 'title' ], Util\Options::get_document_fields() );
		$this->document_slug  = Util\Options::get_document_slug();
	}

	/**
	 * {@inheritdoc}
	 */
	public function register() {
		add_action( 'init', [ $this, 'register_post_type' ], 15 );
		add_action( 'init', [ $this, 'flush_rewrite_rules' ], 16 );
	}

	/**
	 * Register the Document post type.
	 */
	public function register_post_type() {
		$labels = [
			'name'                  => _x( 'Documents', 'Post Type General Name', 'document-library-pro' ),
			'singular_name'         => _x( 'Document', 'Post Type Singular Name', 'document-library-pro' ),
			'menu_name'             => _x( 'Documents', 'Admin Menu text', 'document-library-pro' ),
			'name_admin_bar'        => _x( 'Document', 'Add New on Toolbar', 'document-library-pro' ),
			'archives'              => __( 'Documents Archives', 'document-library-pro' ),
			'attributes'            => __( 'Documents Attributes', 'document-library-pro' ),
			'parent_item_colon'     => __( 'Parent Documents:', 'document-library-pro' ),
			'all_items'             => __( 'All Documents', 'document-library-pro' ),
			'add_new_item'          => __( 'Add New Document', 'document-library-pro' ),
			'add_new'               => __( 'Add New', 'document-library-pro' ),
			'new_item'              => __( 'New Document', 'document-library-pro' ),
			'edit_item'             => __( 'Edit Document', 'document-library-pro' ),
			'update_item'           => __( 'Update Document', 'document-library-pro' ),
			'view_item'             => __( 'View Document', 'document-library-pro' ),
			'view_items'            => __( 'View Documents', 'document-library-pro' ),
			'search_items'          => __( 'Search Documents', 'document-library-pro' ),
			'not_found'             => __( 'Not found', 'document-library-pro' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'document-library-pro' ),
			'featured_image'        => __( 'Featured Image', 'document-library-pro' ),
			'set_featured_image'    => __( 'Set featured image', 'document-library-pro' ),
			'remove_featured_image' => __( 'Remove featured image', 'document-library-pro' ),
			'use_featured_image'    => __( 'Use as featured image', 'document-library-pro' ),
			'insert_into_item'      => __( 'Insert into Document', 'document-library-pro' ),
			'uploaded_to_this_item' => __( 'Uploaded to this document', 'document-library-pro' ),
			'items_list'            => __( 'Document list', 'document-library-pro' ),
			'items_list_navigation' => __( 'Documents list navigation', 'document-library-pro' ),
			'filter_items_list'     => __( 'Filter Documents list', 'document-library-pro' ),
		];

		$args = [
			'label'               => __( 'Documents', 'document-library-pro' ),
			'description'         => __( 'Document Library Pro documents.', 'document-library-pro' ),
			'labels'              => $labels,
			'menu_icon'           => 'dashicons-media-document',
			'supports'            => $this->default_fields,
			'taxonomies'          => [],
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'document_library_pro',
			'menu_position'       => 26,
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'hierarchical'        => false,
			'exclude_from_search' => false,
			'show_in_rest'        => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'rewrite'             => [ 'slug' => $this->document_slug ],
		];

		register_post_type( self::POST_TYPE_SLUG, $args );
	}

	/**
	 * Flushes rewrite rules once after activation and CPT registration.
	 */
	public function flush_rewrite_rules() {
		if ( get_option( 'dlp_should_flush_rewrite_rules' ) ) {
			flush_rewrite_rules();
			update_option( 'dlp_should_flush_rewrite_rules', false );
		}
	}
}
