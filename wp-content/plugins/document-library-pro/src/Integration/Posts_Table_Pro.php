<?php

namespace Barn2\Plugin\Document_Library_Pro\Integration;

use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Registerable,
	Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Service,
	Barn2\Plugin\Document_Library_Pro\Table_Data,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Query,
	Barn2\Plugin\Document_Library_Pro\Taxonomies,
	Barn2\Plugin\Document_Library_Pro\Shortcode,
	Barn2\Plugin\Document_Library_Pro\Util\Options,
	Barn2\Plugin\Document_Library_Pro\Util\SVG_Icon;

defined( 'ABSPATH' ) || exit;

/**
 * Handles the integration with PTP
 *
 * @package   Barn2/document-library-pro
 * @author    Barn2 Plugins <info@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table_Pro implements Registerable, Service {
	/**
	 * {@inheritdoc}
	 */
	public function register() {
		// Register New Table Args
		add_filter( 'document_library_pro_table_default_args', [ $this, 'register_table_default_args' ], 10, 1 );
		add_filter( 'document_library_pro_args_validation', [ $this, 'register_table_args_validation' ], 10, 2 );
		add_filter( 'document_library_pro_args_custom_object_properties', [ $this, 'register_custom_table_args_properties' ], 10, 1 );
		add_action( 'document_library_pro_parse_args', [ $this, 'parse_custom_table_args' ] );

		// Register Document Category & Exclude Document Category Queries
		add_filter( 'document_library_pro_tax_query', [ $this, 'register_tax_queries' ], 10, 2 );

		// Register Document Tag and Category hook points
		add_filter( 'document_library_pro_filterable_columns', [ $this, 'register_filterable_columns' ], 10, 1 );
		add_filter( 'document_library_pro_linkable_columns', [ $this, 'register_linkable_columns' ], 10, 1 );
		add_filter( 'document_library_pro_update_post_selection_args', [ $this, 'register_update_args' ], 10, 1 );

		// Change Document Categories and Tags filter headings
		add_filter( 'document_library_pro_search_filter_heading_doc_categories', [ $this, 'change_document_categories_filter_heading' ], 10, 2 );
		add_filter( 'document_library_pro_search_filter_heading_doc_tags', [ $this, 'change_document_tags_filter_heading' ], 10, 2 );

		// Click filterable taxonomies
		add_filter( 'document_library_pro_column_click_filterable_doc_categories', '__return_true', 10, 1 );
		add_filter( 'document_library_pro_column_click_filterable_doc_tags', '__return_true', 10, 1 );
		add_filter( 'document_library_pro_column_click_filterable_doc_author', '__return_true', 10, 1 );
		add_filter( 'document_library_pro_column_click_filterable_file_type', '__return_true', 10, 1 );

		// Handle Shortcode Stripping
		add_filter( 'document_library_pro_maybe_strip_shortcodes_tag', [ $this, 'shortcode_tag_to_strip' ], 10, 1 );

		// Register New Column Data
		add_filter( 'document_library_pro_custom_table_data_doc_categories', [ $this, 'get_document_categories_data' ], 10, 3 );
		add_filter( 'document_library_pro_custom_table_data_doc_tags', [ $this, 'get_document_tags_data' ], 10, 3 );
		add_filter( 'document_library_pro_custom_table_data_link', [ $this, 'get_document_link_data' ], 10, 3 );
		add_filter( 'document_library_pro_custom_table_data_version', [ $this, 'get_document_version_data' ], 10, 3 );
		add_filter( 'document_library_pro_custom_table_data_file_size', [ $this, 'get_file_size_data' ], 10, 3 );
		add_filter( 'document_library_pro_custom_table_data_file_type', [ $this, 'get_file_type_data' ], 10, 3 );
		add_filter( 'document_library_pro_custom_table_data_doc_author', [ $this, 'get_doc_author_data' ], 10, 3 );
		add_filter( 'document_library_pro_custom_table_data_download_count', [ $this, 'get_download_count_data' ], 10, 3 );
		add_filter( 'document_library_pro_column_defaults', [ $this, 'add_column_defaults' ], 10, 1 );
		add_filter( 'document_library_pro_column_sortable', [ $this, 'is_sortable_column' ], 10, 2 );
		add_filter( 'document_library_pro_column_searchable', [ $this, 'is_searchable_column' ], 10, 2 );

		// Add Extra Config Data
		add_filter( 'document_library_pro_data_config', [ $this, 'add_config_data' ], 10, 2 );
	}

	/**
	 * Adds new custom public properites to Table_Args
	 *
	 * @param mixed $custom_properties
	 * @return string[]
	 */
	public function register_custom_table_args_properties( $custom_properties ) {
		$custom_properties = [
			'doc_category',
			'doc_tag',
			'doc_author',
			'exclude_doc_category',
			'folders',
			'folders_order_by',
			'folders_order',
			'folder_status',
			'folder_status_custom',
			'folder_icon_custom',
			'folder_icon_color',
			'folder_icon_subcolor',
			'folder_icon_svg_closed',
			'folder_icon_svg_open',
			'layout',
			'multi_download_button',
			'multi_download_text',
			'accessing_documents',
			'preview',
			'preview_style',
			'preview_text',
			'document_link',
			'link_target',
			'link_text',
			'link_destination',
			'link_style',
			'grid_content',
			'grid_columns'
		];

		return $custom_properties;
	}

	/**
	 * Adds the default args for new custom Table_Args properties
	 *
	 * @param array $default_args
	 * @return array
	 */
	public function register_table_default_args( $default_args ) {
		return array_merge( $default_args, Options::get_dlp_specific_default_args() );
	}

	/**
	 * Register the validation handlers for custom Table_Args properties
	 *
	 * @param array $default_validation
	 * @param array $args
	 * @return array
	 */
	public function register_table_args_validation( $default_validation, $args ) {
		$sanitize_list = [
			'filter'  => FILTER_CALLBACK,
			'options' => [ Util::class, 'sanitize_list' ]
		];

		$sanitize_string_array = [
			'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'flags'  => FILTER_REQUIRE_ARRAY
		];

		$dlp_validation = [
			'folders'                => FILTER_VALIDATE_BOOLEAN,
			'folders_order_by'       => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'folders_order'          => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'folder_status'          => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'folder_status_custom'   => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'folder_icon_custom'     => FILTER_VALIDATE_BOOLEAN,
			'folder_icon_color'      => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'folder_icon_subcolor'   => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'layout'                 => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'grid_content'           => is_array( $args['grid_content'] ) ? $sanitize_string_array : FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'grid_columns'           => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'multi_download_button'  => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'multi_download_text'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'accessing_documents'    => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'preview'                => FILTER_VALIDATE_BOOLEAN,
			'preview_style'          => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'preview_text'           => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'document_link'          => FILTER_VALIDATE_BOOLEAN,
			'link_target'            => FILTER_VALIDATE_BOOLEAN,
			'link_text'              => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'link_style'             => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'link_destination'       => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'doc_author'             => $sanitize_list,
			'doc_category'           => $sanitize_list,
			'doc_tag'                => $sanitize_list,
			'exclude_doc_category'   => $sanitize_list,
		];

		return array_merge( $default_validation, $dlp_validation );
	}

	/**
	 * Parse any custom args.
	 *
	 * @param Table_Args $table_args
	 */
	public function parse_custom_table_args( $table_args ) {

		$defaults = $table_args::get_site_defaults();

		// Folders order.
		$table_args->folders_order = strtolower( $table_args->folders_order );
		if ( ! in_array( $table_args->folders_order, [ 'asc', 'desc' ], true ) ) {
			$table_args->folders_order = $defaults['folders_order'];
		}

		// Grid content multicheckbox array.
		$table_args->grid_content = is_array( $table_args->grid_content ) ? $table_args->grid_content : Options::sanitize_grid_content( Options::string_list_to_multicheckbox_array( $table_args->grid_content ) );

		// Grid columns.
		if ( ! is_numeric( $table_args->grid_columns ) ) {
			$table_args->grid_columns = $defaults['grid_columns'];
		}

		// Multi download button.
		if ( ! in_array( $table_args->multi_download_button, [ 'below', 'above', 'both' ], true ) ) {
			$table_args->multi_download_button = $defaults['multi_download_button'];
		}

		// Accessing documents.
		if ( ! in_array( $table_args->accessing_documents, [ 'link', 'checkbox', 'both' ], true ) ) {
			$table_args->accessing_documents = $defaults['accessing_documents'];
		}

		// Preview style.
		if ( ! in_array( $table_args->preview_style, [ 'button', 'button_icon_text', 'button_icon', 'icon_only', 'link' ], true ) ) {
			$table_args->preview_style = $defaults['preview_style'];
		}

		// Link style.
		if ( ! in_array( $table_args->link_style, [ 'button', 'button_icon_text', 'button_icon', 'icon_only', 'icon', 'text' ], true ) ) {
			$table_args->link_style = $defaults['link_style'];
		}

		// Link destination.
		if ( ! in_array( $table_args->link_destination, [ 'direct', 'single' ], true ) ) {
			$table_args->link_destination = $defaults['link_destination'];
		}
	}

	/**
	 * Adjust the column defaults for the table in PTP.
	 *
	 * @param array $column_defaults
	 * @return array
	 */
	public function add_column_defaults( $column_defaults ) {
		return array_merge(
			$column_defaults,
			[
				'doc_categories' => [
					'heading'  => __( 'Categories', 'document-library-pro' ),
					'priority' => 6
				],
				'doc_tags'       => [
					'heading'  => __( 'Tags', 'document-library-pro' ),
					'priority' => 10
				],
				'doc_author'     => [
					'heading'  => __( 'Author', 'document-library-pro' ),
					'priority' => 10
				],
				'file_size'      => [
					'heading'  => __( 'File Size', 'document-library-pro' ),
					'priority' => 12
				],
				'file_type'      => [
					'heading'  => __( 'File Type', 'document-library-pro' ),
					'priority' => 11
				],
				'download_count' => [
					'heading'  => __( 'Downloads', 'document-library-pro' ),
					'priority' => 13
				],
				'link'           => [
					'heading'  => __( 'Link', 'document-library-pro' ),
					'priority' => 10
				],
			]
		);
	}

	/**
	 * Determines sortable status of columns
	 *
	 * @param boolean $sortable
	 * @param string $column
	 * @return boolean
	 */
	public function is_sortable_column( $sortable, $column ) {
		if ( in_array( $column, [ 'link' ], true ) ) {
			$sortable = false;
		}

		return $sortable;
	}

	/**
	 * Determines searchable status of columns
	 *
	 * @param boolean $searchable
	 * @param string $column
	 * @return boolean
	 */
	public function is_searchable_column( $searchable, $column ) {
		if ( in_array( $column, [ 'link' ], true ) ) {
			$searchable = false;
		}

		return $searchable;
	}

	/**
	 * Register the columns which should be filterable
	 *
	 * @param string[] $columns
	 * @return string[]
	 */
	public function register_filterable_columns( $columns ) {
		return array_merge( $columns, [ Taxonomies::CATEGORY_SLUG, Taxonomies::AUTHOR_SLUG, Taxonomies::TAG_SLUG, Taxonomies::FILE_TYPE_SLUG ] );
	}

	/**
	 * Register the columns which should have filter links
	 *
	 * @param string[] $columns
	 * @return string[]
	 */
	public function register_linkable_columns( $columns ) {
		return array_merge( $columns, [ Taxonomies::CATEGORY_SLUG, Taxonomies::AUTHOR_SLUG, Taxonomies::TAG_SLUG, Taxonomies::FILE_TYPE_SLUG  ] );
	}

	/**
	 * Register the args which should update the filters
	 *
	 * @param string[] $columns
	 * @return string[]
	 */
	public function register_update_args( $columns ) {
		return array_merge( $columns, [ 'exclude_doc_category', 'doc_category', 'doc_tag', 'doc_author' ] );
	}

	/**
	 * Change Document Categories filter heading
	 *
	 * @param string $heading
	 * @param Table_Args $args
	 * @return string
	 */
	public function change_document_categories_filter_heading( $heading, $args ) {
		if ( in_array( $heading, $args->filter_headings ) ) {
			return $heading;
		}
		return __( 'Category', 'document-library-pro' );
	}

	/**
	 * Change Document Tags filter heading
	 *
	 * @param string $heading
	 * @param Table_Args $args
	 * @return string
	 */
	public function change_document_tags_filter_heading( $heading, $args ) {
		if ( in_array( $heading, $args->filter_headings ) ) {
			return $heading;
		}
		return __( 'Tag', 'document-library-pro' );
	}

	/**
	 * Gets the Shortcode tag.
	 *
	 * @param mixed $shortcode_tag
	 * @return string
	 */
	public function shortcode_tag_to_strip( $shortcode_tag ) {
		return Shortcode::SHORTCODE;
	}

	/**
	 * Add the Document Categories column to the Table.
	 *
	 * @param mixed $data
	 * @param WP_Post $post
	 * @param Table_Args $args
	 * @return Document_Categories
	 */
	public function get_document_categories_data( $data, $post, $args ) {
		return new Table_Data\Document_Categories( $post, $args );
	}

	/**
	 * Add the Document Categories column to the Table.
	 *
	 * @param mixed $data
	 * @param WP_Post $post
	 * @param Table_Args $args
	 * @return Document_Tags
	 */
	public function get_document_tags_data( $data, $post, $args ) {
		return new Table_Data\Document_Tags( $post, $args );
	}

	/**
	 * Add the Document Link column to the Table.
	 *
	 * @param mixed $data
	 * @param WP_Post $post
	 * @param Table_Args $args
	 * @return Document_Categories
	 */
	public function get_document_link_data( $data, $post, $args ) {
		return new Table_Data\Document_Link( $post, $args );
	}

	/**
	 * Add the Document Version column to the Table.
	 *
	 * @param mixed $data
	 * @param WP_Post $post
	 * @param Table_Args $args
	 * @return Document_Categories
	 */
	public function get_document_version_data( $data, $post, $args ) {
		return new Table_Data\Document_Version( $post, $args );
	}

	/**
	 * Add the Author column to the Table.
	 *
	 * @param mixed $data
	 * @param WP_Post $post
	 * @param Table_Args $args
	 * @return Document_Author
	 */
	public function get_doc_author_data( $data, $post, $args ) {
		return new Table_Data\Document_Author( $post, $args );
	}

	/**
	 * Add the File Type column to the Table.
	 *
	 * @param mixed $data
	 * @param WP_Post $post
	 * @param Table_Args $args
	 * @return Document_Categories
	 */
	public function get_file_type_data( $data, $post, $args ) {
		return new Table_Data\File_Type( $post, $args );
	}

	/**
	 * Add the File Size column to the Table.
	 *
	 * @param mixed $data
	 * @param WP_Post $post
	 * @param Table_Args $args
	 * @return File_Size
	 */
	public function get_file_size_data( $data, $post, $args ) {
		return new Table_Data\File_Size( $post, $args );
	}

	/**
	 * Add the Download Count column to the Table.
	 *
	 * @param mixed $data
	 * @param WP_Post $post
	 * @param Table_Args $args
	 * @return Download_Count
	 */
	public function get_download_count_data( $data, $post, $args ) {
		return new Table_Data\Download_Count( $post, $args );
	}


	/**
	 * Add our taxonomies to the table query
	 *
	 * @param   array       $tax_query
	 * @param   Table_Query $table_query
	 * @return  array       $tax_query
	 */
	public function register_tax_queries( $tax_query, Table_Query $table_query ) {
		if ( ! empty( $table_query->args->doc_category ) ) {
			$tax_query[] = $this->tax_query_item( $table_query->args->numeric_terms, $table_query->args->doc_category, Taxonomies::CATEGORY_SLUG );
		}

		if ( ! empty( $table_query->args->exclude_doc_category ) ) {
			$tax_query[] = $this->tax_query_item( $table_query->args->numeric_terms, $table_query->args->exclude_doc_category, Taxonomies::CATEGORY_SLUG, 'NOT IN' );
		}

		if ( ! empty( $table_query->args->doc_tag ) ) {
			$tax_query[] = $this->tax_query_item( $table_query->args->numeric_terms, $table_query->args->doc_tag, Taxonomies::TAG_SLUG );
		}

		if ( ! empty( $table_query->args->doc_author ) ) {
			$tax_query[] = $this->tax_query_item( $table_query->args->numeric_terms, $table_query->args->doc_author, Taxonomies::AUTHOR_SLUG );
		}

		return $tax_query;
	}

	/**
	 * Adds config data to the table html
	 *
	 * @param array $config
	 * @param object $args
	 * @return array
	 */
	public function add_config_data( $config, $args ) {
		$config['multiDownloadButton']   = in_array( $args->accessing_documents, [ 'checkbox', 'both' ], true );
		$config['multiDownloadPosition'] = $args->multi_download_button;
		$config['multiDownloadText']     = $args->multi_download_text;

		return $config;
	}

	/**
	 * Generate an inner array for the 'tax_query' arg in WP_Query.
	 *
	 * @param   bool    $numeric_terms Term ID's or slugs
	 * @param   string  $terms The list of terms as a string
	 * @param   string  $taxonomy The taxonomy name
	 * @param   string  $operator IN, NOT IN, AND, etc
	 * @param   string  $field
	 * @return  array   A tax query sub-array
	 */
	private function tax_query_item( $numeric_terms, $terms, $taxonomy, $operator = 'IN', $field = '' ) {
		$and_relation = 'AND' === $operator;

		// comma-delimited list = OR, plus-delimited = AND
		if ( ! is_array( $terms ) ) {
			if ( false !== strpos( $terms, '+' ) ) {
				$terms        = explode( '+', $terms );
				$and_relation = true;
			} else {
				$terms = explode( ',', $terms );
			}
		}

		// Do we have slugs or IDs?
		if ( ! $field ) {
			$using_term_ids = count( $terms ) === count( array_filter( $terms, 'is_numeric' ) );
			$field          = $using_term_ids && ! $numeric_terms ? 'term_id' : 'slug';
		}

		// Strange bug when using operator => 'AND' in individual tax queries -
		// We need to separate these out into separate 'IN' arrays joined by and outer relation => 'AND'
		if ( $and_relation && count( $terms ) > 1 ) {
			$result = [ 'relation' => 'AND' ];

			foreach ( $terms as $term ) {
				$result[] = [
					'taxonomy' => $taxonomy,
					'terms'    => $term,
					'operator' => 'IN',
					'field'    => $field
				];
			}

			return $result;
		} else {
			return [
				'taxonomy' => $taxonomy,
				'terms'    => $terms,
				'operator' => $operator,
				'field'    => $field
			];
		}
	}
}
