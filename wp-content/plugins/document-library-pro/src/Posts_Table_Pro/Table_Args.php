<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Columns_Util;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Options;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;

/**
 * Responsible for storing and validating the posts table arguments.
 * Parses an array of args into the corresponding properties.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Args {

	/**
	 * @var array The args array.
	 */
	private $args = [];

	// Table params
	public $columns;
	public $headings; // built from headings
	public $widths;
	public $auto_width;
	public $priorities;
	public $column_breakpoints;
	public $responsive_control;
	public $responsive_display;
	public $wrap;
	public $show_footer;
	public $search_on_click;
	public $filters;
	public $filter_headings; // built from filters
	public $scroll_offset;
	public $content_length;
	public $excerpt_length;
	public $links;
	public $lazy_load;
	public $cache;
	public $image_size;
	public $lightbox;
	public $shortcodes;
	public $date_format;
	public $date_columns;
	public $no_posts_message;
	public $no_posts_filtered_message;
	public $paging_type;
	public $page_length;
	public $search_box;
	public $totals;
	public $pagination;
	public $reset_button;
	public $button_text;

	// Query params
	public $rows_per_page;
	public $post_limit;
	public $sort_by;
	public $sort_order;
	public $post_type;
	public $post_mime_type;
	public $status;
	public $category;
	public $exclude_category;
	public $tag;
	public $term;
	public $exclude_term;
	public $numeric_terms;
	public $cf;
	public $year;
	public $month;
	public $day;
	public $author;
	public $exclude;
	public $include;
	public $search_term;

	// Internal params
	public $show_hidden_columns;

	// Lazy load params
	public $offset;
	public $user_search_term;
	public $search_filters = [];

	// Custom properties
	public $doc_category;
	public $doc_tag;
	public $doc_author;
	public $exclude_doc_category;
	public $folders;
	public $folders_order_by;
	public $folders_order;
	public $folder_status;
	public $folder_status_custom;
	public $folder_icon_custom;
	public $folder_icon_color;
	public $folder_icon_subcolor;
	public $folder_icon_svg_closed;
	public $folder_icon_svg_open;
	public $layout;
	public $multi_download_button;
	public $multi_download_text;
	public $accessing_documents;
	public $preview;
	public $preview_style;
	public $preview_text;
	public $document_link;
	public $link_target;
	public $link_text;
	public $link_destination;
	public $link_style;
	public $grid_content;
	public $grid_columns;

	/**
	 * @var array The default table parameters
	 */
	private static $default_args = [
		'columns'                   => 'image,title,excerpt,categories,author,date',
		// allowed: id, title, content, excerpt, date, categories, tags, author, status, image, tax:<taxonomy_name>, cf:<custom_field>
		'widths'                    => '',
		'auto_width'                => true,
		'priorities'                => '',
		'column_breakpoints'        => '',
		'responsive_control'        => 'inline',
		// inline or column
		'responsive_display'        => 'child_row',
		// child_row, child_row_visible, or modal
		'wrap'                      => true,
		'show_footer'               => false,
		'search_on_click'           => true,
		'filters'                   => false,
		'scroll_offset'             => 15,
		'content_length'            => 15,
		'excerpt_length'            => -1,
		'links'                     => 'title,categories,tags,terms,author',
		// set to all or none, or any combination of id, title, terms, tags, categories, author, image
		'lazy_load'                 => false,
		'cache'                     => false,
		'image_size'                => '70x70',
		'lightbox'                  => true,
		'shortcodes'                => false,
		'date_format'               => '',
		'date_columns'              => '',
		'no_posts_message'          => '',
		'no_posts_filtered_message' => '',
		'paging_type'               => 'numbers',
		'page_length'               => 'bottom',
		'search_box'                => 'top',
		'totals'                    => 'bottom',
		'pagination'                => 'bottom',
		'reset_button'              => true,
		'button_text'               => 'View',
		'rows_per_page'             => 25,
		'post_limit'                => 500,
		'sort_by'                   => 'date',
		'sort_order'                => '',
		// no default set - see parse_args()
		'post_type'                 => 'post',
		'status'                    => 'publish',
		'category'                  => '',
		// list of slugs or IDs
		'exclude_category'          => '',
		// list of slugs or IDs
		'tag'                       => '',
		// list of slugs or IDs
		'term'                      => '',
		// list of terms of the form <taxonomy>:<term>
		'exclude_term'              => '',
		// list of terms of the form <taxonomy>:<term>
		'numeric_terms'             => false,
		// set to true if using categories, tags or terms with numeric slugs
		'cf'                        => '',
		// list of custom fields of the form <field_key>:<field_value>
		'year'                      => '',
		'month'                     => '',
		'day'                       => '',
		'author'                    => '',
		// list of author IDs
		'exclude'                   => '',
		// list of post IDs
		'include'                   => '',
		// list of post IDs
		'search_term'               => '',
		'show_hidden_columns'       => false
	];

	public function __construct( array $args = [] ) {
		$this->set_args( $args );
	}

	public function get_args() {
		return $this->args;
	}

	public function set_args( array $args ) {
		// Lazy load args need to be merged in
		$lazy_load_args = [
			'offset'           => $this->offset,
			'user_search_term' => $this->user_search_term,
			'search_filters'   => $this->search_filters
		];

		// Update args
		$this->args = array_merge( $lazy_load_args, $this->args, $args );

		// Parse/validate args & update properties
		$this->parse_args( $this->args );
	}

	/**
	 * Get the initial default table args, not including plugin settings.
	 *
	 * @return array The table defaults.
	 */
	public static function get_table_defaults() {
		return apply_filters( 'document_library_pro_table_default_args', self::$default_args );
	}

	/**
	 * Get the default table args, including plugin settings. Plugin settings override the initial args.
	 *
	 * @return array The site defaults.
	 */
	public static function get_site_defaults() {
		$defaults = Options::get_shortcode_options( self::get_table_defaults() );
		$defaults = apply_filters_deprecated( 'document_library_pro_user_default_args', [ $defaults ], '2.5.1', 'document_library_pro_site_default_args' );

		return apply_filters( 'document_library_pro_site_default_args', $defaults );
	}

	private function array_filter_validate_boolean( $var ) {
		return $var === FILTER_VALIDATE_BOOLEAN;
	}

	private function array_filter_custom_field_or_taxonomy( $column ) {
		return Columns_Util::is_custom_field( $column ) || Columns_Util::is_custom_taxonomy( $column );
	}

	private function parse_args( array $args ) {
		$defaults = self::get_site_defaults();

		// Merge in defaults so we know all args have been set.
		$args = wp_parse_args( $args, $defaults );

		// Convert any array args to a comma-separated string prior to validation and processing, to ensure we have
		// consistent options to work with.
		foreach (
			[
				'columns',
				'widths',
				'priorities',
				'column_breakpoints',
				'filters',
				'links',
				'image_size',
				'date_columns',
				'post_type',
				'status',
				'category',
				'exclude_category',
				'tag',
				'term',
				'exclude_term',
				'cf',
				'exclude',
				'include'
			] as $arg
		) {
			if ( is_array( $args[ $arg ] ) ) {
				$args[ $arg ] = implode( ',', $args[ $arg ] );
			}
		}

		// Create our validation callbacks.
		$sanitize_list = [
			'filter'  => FILTER_CALLBACK,
			'options' => [ Util::class, 'sanitize_list' ]
		];

		$sanitize_numeric_list = [
			'filter'  => FILTER_CALLBACK,
			'options' => [ Util::class, 'sanitize_numeric_list' ]
		];

		$sanitize_enum = [
			'filter'  => FILTER_CALLBACK,
			'options' => [ Util::class, 'sanitize_enum' ]
		];

		$sanitize_enum_or_bool = [
			'filter'  => FILTER_CALLBACK,
			'options' => [ Util::class, 'sanitize_enum_or_bool' ]
		];

		$validation = apply_filters(
			'document_library_pro_args_validation',
			[
				'columns'                   => FILTER_DEFAULT,
				'widths'                    => $sanitize_list,
				'auto_width'                => FILTER_VALIDATE_BOOLEAN,
				'priorities'                => $sanitize_numeric_list,
				'column_breakpoints'        => $sanitize_list,
				'responsive_control'        => $sanitize_enum,
				'responsive_display'        => $sanitize_enum,
				'wrap'                      => FILTER_VALIDATE_BOOLEAN,
				'show_footer'               => FILTER_VALIDATE_BOOLEAN,
				'search_on_click'           => FILTER_VALIDATE_BOOLEAN,
				'filters'                   => FILTER_DEFAULT,
				'scroll_offset'             => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default' => $defaults['scroll_offset']
					]
				],
				'content_length'            => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['content_length'],
						'min_range' => -1
					]
				],
				'excerpt_length'            => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['excerpt_length'],
						'min_range' => -1
					]
				],
				'links'                     => [
					'filter'  => FILTER_CALLBACK,
					'options' => [ Util::class, 'sanitize_list_or_bool' ]
				],
				'lazy_load'                 => FILTER_VALIDATE_BOOLEAN,
				'cache'                     => FILTER_VALIDATE_BOOLEAN,
				'image_size'                => [
					'filter'  => FILTER_CALLBACK,
					'options' => [ Util::class, 'sanitize_image_size' ]
				],
				'lightbox'                  => FILTER_VALIDATE_BOOLEAN,
				'shortcodes'                => FILTER_VALIDATE_BOOLEAN,
				'date_format'               => FILTER_SANITIZE_SPECIAL_CHARS, // not FILTER_SANITIZE_FULL_SPECIAL_CHARS otherwise non-ASCII characters are encoded in the date.
				'date_columns'              => $sanitize_list,
				'no_posts_message'          => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
				'no_posts_filtered_message' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
				'paging_type'               => $sanitize_enum,
				'page_length'               => $sanitize_enum_or_bool,
				'search_box'                => $sanitize_enum_or_bool,
				'totals'                    => $sanitize_enum_or_bool,
				'pagination'                => $sanitize_enum_or_bool,
				'reset_button'              => FILTER_VALIDATE_BOOLEAN,
				'button_text'               => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
				'rows_per_page'             => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['rows_per_page'],
						'min_range' => -1
					]
				],
				'post_limit'                => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['post_limit'],
						'min_range' => -1,
						'max_range' => 5000,
					]
				],
				'sort_by'                   => $sanitize_list,
				'sort_order'                => $sanitize_enum,
				'post_type'                 => $sanitize_list,
				'status'                    => $sanitize_list,
				'category'                  => $sanitize_list,
				'exclude_category'          => $sanitize_list,
				'tag'                       => $sanitize_list,
				'term'                      => $sanitize_list,
				'exclude_term'              => $sanitize_list,
				'numeric_terms'             => FILTER_VALIDATE_BOOLEAN,
				'cf'                        => FILTER_DEFAULT,
				'year'                      => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['year'],
						'min_range' => 1
					]
				],
				'month'                     => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['month'],
						'min_range' => 1,
						'max_range' => 12
					]
				],
				'day'                       => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => $defaults['day'],
						'min_range' => 1,
						'max_range' => 31
					]
				],
				'author'                    => $sanitize_numeric_list,
				'exclude'                   => $sanitize_numeric_list,
				'include'                   => $sanitize_numeric_list,
				'search_term'               => FILTER_DEFAULT,
				// Internal params
				'show_hidden_columns'       => FILTER_VALIDATE_BOOLEAN,
				// Lazy load params
				'offset'                    => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [
						'default'   => 0,
						'min_range' => 0,
					]
				],
				'user_search_term'          => FILTER_DEFAULT,
				'search_filters'            => [
					'filter' => FILTER_VALIDATE_INT,
					'flags'  => FILTER_REQUIRE_ARRAY
				]
			],
			$args
		);

		// Sanitize/validate all args.
		$args = filter_var_array( $args, $validation );

		// Set custom object properties from consuming plugins (e.g. DLP).
		$custom_properties = apply_filters( 'document_library_pro_args_custom_object_properties', [] );
		$this->set_custom_properties( $custom_properties );

		// Set object properties from args.
		Util::set_object_vars( $this, $args );

		// Fill in any blank properties.
		foreach ( [ 'columns', 'post_type', 'status', 'image_size', 'sort_by' ] as $arg ) {
			if ( empty( $this->$arg ) ) {
				$this->$arg = $defaults[ $arg ];
			}
		}

		// Make sure boolean args are definitely booleans - sometimes filter_var_array doesn't convert them properly.
		foreach ( array_filter( $validation, [ $this, 'array_filter_validate_boolean' ] ) as $arg => $val ) {
			$this->$arg = ( $this->$arg === true || $this->$arg === 'true' ) ? true : false;
		}

		// Convert some list-based args to arrays - columns, filters, links, category, tag, term, exclude_term, cf and post_type are handled separately.
		foreach ( [ 'widths', 'priorities', 'column_breakpoints', 'date_columns', 'status', 'exclude', 'include', 'exclude_category' ] as $arg ) {
			$this->$arg = Util::string_list_to_array( $this->$arg );
		}

		// Validate post type first, as we need it for columns and filters.
		$this->set_post_type();

		// Validate columns and filters.
		$this->set_columns();
		$this->set_filters();

		// Widths and priorities
		if ( $this->widths ) {
			$this->widths = Util::array_pad_and_slice( $this->widths, count( $this->columns ), 'auto' );
		}

		if ( $this->priorities ) {
			$this->priorities = Util::array_pad_and_slice( $this->priorities, count( $this->columns ), 'default' );
		}

		// Responsive options.
		if ( ! in_array( $this->responsive_control, [ 'inline', 'column' ], true ) ) {
			$this->responsive_control = $defaults['responsive_control'];
		}

		if ( ! in_array( $this->responsive_display, [ 'child_row', 'child_row_visible', 'modal' ], true ) ) {
			$this->responsive_display = $defaults['responsive_display'];
		}

		if ( $this->column_breakpoints ) {
			$this->column_breakpoints = Util::array_pad_and_slice( $this->column_breakpoints, count( $this->columns ), 'default' );
		}

		if ( ! $this->auto_width ) {
			// Must use inline responsive control if auto width disabled, otherwise the control column is always shown.
			$this->responsive_control = 'inline';
		} elseif ( ! empty( $this->column_breakpoints ) && 'mobile' === $this->column_breakpoints[0] ) {
			// If first column is mobile visibility, force column control as using inline will override the mobile visibility option.
			$this->responsive_control = 'column';
		}

		// Display options (page length, etc).
		foreach ( [ 'page_length', 'search_box', 'totals', 'pagination' ] as $display_option ) {
			if ( ! in_array( $this->$display_option, [ 'top', 'bottom', 'both', false ], true ) ) {
				$this->$display_option = $defaults[ $display_option ];
			}
		}

		// Links - controls whether certain items are shown as links or plain text.
		$this->links = is_string( $this->links ) ? strtolower( $this->links ) : $this->links;

		if ( true === $this->links || 'all' === $this->links ) {
			$this->links = [ 'all' ];
		} elseif ( false === $this->links || 'none' === $this->links ) {
			$this->links = [];
		} else {
			$linkable_columns = apply_filters( 'document_library_pro_linkable_columns', [ 'id', 'author', 'terms', 'tags', 'categories', 'title', 'image' ] );
			$this->links      = array_intersect( explode( ',', $this->links ), $linkable_columns );
		}

		// Paging type.
		if ( ! in_array( $this->paging_type, [ 'numbers', 'simple', 'simple_numbers', 'full', 'full_numbers' ], true ) ) {
			$this->paging_type = $defaults['paging_type'];
		}

		// Image size.
		$this->set_image_size();
		$this->set_image_column_width();

		// Validate date columns.
		if ( $this->date_columns ) {
			// Date columns must be present in table.
			$this->date_columns = array_intersect( (array) $this->date_columns, $this->columns );

			// Only custom fields or taxonomies allowed.
			$this->date_columns = array_filter( $this->date_columns, [ $this, 'array_filter_custom_field_or_taxonomy' ] );
		}

		// Sort by - force the use of column name if sorting by modified date.
		if ( 'modified' === $this->sort_by ) {
			$this->sort_by = 'date_modified';
		}

		// Sort order.
		$this->sort_order = strtolower( $this->sort_order );

		if ( ! in_array( $this->sort_order, [ 'asc', 'desc' ], true ) ) {
			// Default to descending order for date sorting, ascending for everything else.
			$this->sort_order = in_array( $this->sort_by, array_merge( [ 'date', 'date_modified' ], $this->date_columns ), true ) ? 'desc' : 'asc';
		}

		// Check search terms are valid.
		if ( ! Util::is_valid_search_term( $this->search_term ) ) {
			$this->search_term = '';
		}

		if ( ! Util::is_valid_search_term( $this->user_search_term ) ) {
			$this->user_search_term = '';
		}

		// Content length, exceprt length, rows per page and post limit can be positive integer or -1.
		foreach ( [ 'content_length', 'excerpt_length', 'rows_per_page', 'post_limit' ] as $arg ) {
			// Sanity check in case filter set an invalid value.
			if ( ! is_int( $this->$arg ) || $this->$arg < -1 ) {
				$this->$arg = $defaults[ $arg ];
			}

			if ( 0 === $this->$arg ) {
				$this->$arg = -1;
			}
		}

		// If enabling shortcodes, display the full content
		if ( $this->shortcodes ) {
			$this->content_length = -1;
		}

		// Filter post limit
		$this->post_limit = (int) apply_filters_deprecated( 'document_library_pro_max_posts_limit', [ $this->post_limit, $this ], '2.5.1' );

		// Ignore post limit if lazy loaded and the default post limit is used.
		if ( $this->lazy_load && (int) $defaults['post_limit'] === $this->post_limit ) {
			$this->post_limit = -1;
		}

		// Disable lightbox if explicitly linking from image column.
		if ( in_array( 'image', $this->links, true ) ) {
			$this->lightbox = false;
		}

		// Ensure private posts are hidden if the current user doesn't have the required capability.
		if ( in_array( 'private', $this->status, true ) ) {
			$private_allowed = true;

			if ( 'any' === $this->post_type && ! current_user_can( 'read_private_posts' ) ) {
				// Bit of a hack when using 'any' post type - just check read_private_posts cap.
				$private_allowed = false;
			} else {
				foreach ( (array) $this->post_type as $post_type ) {
					$cap = false;

					if ( $post_type_object = get_post_type_object( $post_type ) ) {
						$cap = $post_type_object->cap->read_private_posts;
					}
					if ( ! $cap ) {
						$cap = 'read_private_' . $post_type . 's';
					}

					if ( ! current_user_can( $cap ) ) {
						$private_allowed = false;
						break;
					}
				}
			}

			if ( ! $private_allowed ) {
				$this->status = array_diff( $this->status, [ 'private' ] );

				if ( empty( $this->status ) ) {
					$this->status = Util::string_list_to_array( $defaults['status'] );
				}
			}
		}

		// Prevent user error where category is used instead of term when specifying a custom taxonomy.
		if ( false !== strpos( $this->category, ':' ) && empty( $this->term ) ) {
			$this->term     = $this->category;
			$this->category = '';
		}

		do_action( 'document_library_pro_parse_args', $this );
	}

	/**
	 * Validate the columns arg, and stores the result in the $columns and $headings properties.
	 */
	private function set_columns() {
		$columns = Columns_Util::parse_columns( $this->columns );

		if ( empty( $columns ) ) {
			$columns = Columns_Util::parse_columns( self::get_table_defaults()['columns'] );
		}

		// Remove any non-applicable columns for the selected post type(s).
		if ( $this->post_type ) {
			$columns = Columns_Util::remove_non_applicable_columns( $columns, $this->post_type );
		}

		$this->columns  = array_keys( $columns );
		$this->headings = array_values( $columns );
	}

	/**
	 * Validate the filters arg, and stores the result in the $filters and $filter_headings properties.
	 */
	private function set_filters() {
		$parsed = Columns_Util::parse_filters( $this->filters, $this->columns );

		// Remove any non-applicable columns for the selected post type(s).
		if ( $this->post_type ) {
			$parsed = Columns_Util::remove_non_applicable_columns( $parsed, $this->post_type );
		}

		$this->filters         = ! empty( $parsed ) ? array_keys( $parsed ) : false;
		$this->filter_headings = array_values( $parsed );
	}

	private function set_image_column_width() {
		if ( false === ( $image_col = array_search( 'image', $this->columns, true ) ) ) {
			return;
		}

		if ( $this->widths && isset( $this->widths[ $image_col ] ) && 'auto' !== $this->widths[ $image_col ] ) {
			return;
		}

		if ( $image_col_width = Util::get_image_size_width( $this->image_size ) ) {
			if ( ! $this->widths ) {
				$this->widths = array_fill( 0, count( $this->columns ), 'auto' );
			}
			$this->widths[ $image_col ] = $image_col_width . 'px';
		}
	}

	private function set_image_size() {
		if ( empty( $this->image_size ) ) {
			return;
		}

		$size_arr           = explode( 'x', str_replace( ',', 'x', $this->image_size ) );
		$size_numeric_count = count( array_filter( $size_arr, 'is_numeric' ) );

		if ( 1 === $size_numeric_count ) {
			// One number, so use for both width and height
			$this->image_size = [ $size_arr[0], $size_arr[0] ];
		} elseif ( 2 === $size_numeric_count ) {
			// Width and height specified
			$this->image_size = $size_arr;
		} // otherwise assume it's a text-based image size, e.g. 'thumbnail'
	}

	private function set_custom_properties( $custom_properties ) {
		if ( ! is_array( $custom_properties ) || empty( $custom_properties ) ) {
			return;
		}

		foreach ( $custom_properties as $property ) {
			$this->{$property} = '';
		}
	}

	private function set_post_type() {
		if ( 'any' === $this->post_type ) {
			return;
		}

		if ( 0 === strpos( $this->post_type, 'attachment' ) ) {
			// Attachments have a status of 'inherit' so we need to set status otherwise no results will be returned
			$this->status    = [ 'inherit' ];
			$this->post_type = strtok( $this->post_type, ':' );
			$post_mime_type  = strtok( ':' );

			if ( $post_mime_type ) {
				$this->post_mime_type = Util::string_list_to_array( $post_mime_type );
			}
		} else {
			$post_type = array_filter( Util::string_list_to_array( $this->post_type ), 'post_type_exists' );

			// Nav menu items not allowed, and attachments can only be used on their own.
			$post_type = array_diff( $post_type, [ 'nav_menu_item', 'attachment' ] );

			if ( empty( $post_type ) ) {
				$post_type = Util::string_list_to_array( self::get_table_defaults()['post_type'] );
			}

			if ( 1 === count( $post_type ) ) {
				$post_type = reset( $post_type );
			}

			$this->post_type = $post_type;
		}
	}

	/**
	 * Deprecated.
	 *
	 * @deprecated 2.3.2 Renamed get_site_defaults().
	 */
	public static function get_defaults() {
		_deprecated_function( __METHOD__, '2.3.2', 'get_site_defaults' );
		return self::get_site_defaults();
	}

	/**
	 * Deprecated.
	 *
	 * @deprecated 2.5.1 Renamed get_site_defaults().
	 */
	public static function get_user_defaults() {
		_deprecated_function( __METHOD__, '2.5.1', 'get_site_defaults' );
		return self::get_site_defaults();
	}

}
