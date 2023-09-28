<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data\Data_Factory;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Columns_Util;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Table\Html_Data_Table;

/**
 * Represents a data table of WP_Post objects.
 *
 * This class is responsible for creating the table from the supplied args and returning it as a Html_Data_Table object.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Posts_Table {

	public $id;

	/* Helper classes */
	public  $args;
	public  $query;
	public  $hooks;
	public  $data_table;
	private $columns;
	private $data_factory;
	private $config_builder;
	private $cache;

	/* Internal flags */
	private $table_initialised = false;
	private $data_added        = false;

	const CONTROL_COLUMN_DATA_SOURCE = 'control';

	public function __construct( $id, $args = [] ) {
		$this->id = $id;

		// Initialize helper classes
		$this->args           = new Table_Args( $args );
		$this->query          = new Table_Query( $this->args );
		$this->columns        = new Table_Columns( $this->args );
		$this->data_factory   = new Data_Factory( $this->args );
		$this->hooks          = new Table_Hooks( $this->args );
		$this->config_builder = new Config_Builder( $this->id, $this->args, $this->columns );
		$this->cache          = new Table_Cache( $this->id, $this->args, $this->query );
		$this->data_table     = new Html_Data_Table();
	}

	/**
	 * Retrieves the data table containing the list of posts based on the arguments
	 * supplied on construction.
	 *
	 * The table returned includes the table headings, attributes and data.
	 *
	 * If the output is 'object' the returned object is an Html_Data_Table instance.
	 * If the output is 'html', the table returned will be a string containing the <table> element
	 * at its root, and include the <thead>, <tfoot> and <tbody> elements.
	 * If the output is 'array', the table returned will be an array containing the following keys:
	 * 'thead', 'tbody', 'tfoot' and 'attributes'.
	 * If the output is 'json', the return value will be a JSON-encoded string in the same format
	 * as 'array'.
	 *
	 * @param string $output The output format - object, html, array or json. Default 'object'.
	 * @return object|array|string The posts table in the requested format.
	 */
	public function get_table( $output = 'object' ) {

		if ( ! $this->table_initialised ) {
			// Add table to cache.
			$this->cache->add_table();

			// Reset the table
			$this->data_table->reset();

			do_action( 'document_library_pro_before_get_table', $this );

			// Register scripts for this table.
			Frontend_Scripts::load_table_scripts( $this->args );

			// Add attriutes and table headings.
			$this->add_attributes();
			$this->add_headings();

			// Fetch the data.
			$this->fetch_data();

			do_action( 'document_library_pro_after_get_table', $this );

			$this->table_initialised = true;
		}

		$result = $this->data_table;

		if ( 'html' === $output ) {
			$result = $this->data_table->to_html();
		} elseif ( 'array' === $output ) {
			$result = $this->data_table->to_array();
		} elseif ( 'json' === $output ) {
			$result = $this->data_table->to_json();
		}

		return apply_filters( 'document_library_pro_get_table_output', $result, $output, $this );
	}

	/**
	 * Retrieves the data table containing the list of posts based on the specified arguments.
	 *
	 * The table returned includes only the table data itself (i.e. the rows), and doesn't include the header, footer, etc.
	 *
	 * If the output is 'object' the returned object will be an Html_Data_Table instance.
	 * If the output is 'html', the data returned will be a string containing a list of <tr> elements,
	 * one for each product found.
	 * If the output is 'array', the data returned will be an array of rows, one for each product found.
	 * if the output is 'json', the data returned will be a JSON-encoded string in the same format
	 * as 'array'.
	 *
	 * @return object|array|string The posts table data in the requested format.
	 */
	public function get_data( $output = 'object' ) {
		// Fetch the data.
		$this->fetch_data();

		$result = $this->data_table;

		// Build the output.
		if ( 'html' === $output ) {
			$result = $this->data_table->to_html( true );
		} elseif ( 'array' === $output ) {
			$result = $this->data_table->to_array( true );
		} elseif ( 'json' === $output ) {
			$result = $this->data_table->to_json( true );
		}

		return apply_filters( 'document_library_pro_get_data_output', $result, $output, $this );
	}

	public function update( array $args ) {
		$this->table_initialised = false;
		$this->data_added        = false;


		$post_selection_args   = apply_filters( 'document_library_pro_update_post_selection_args', [
			'post_type',
			'status',
			'year',
			'month',
			'day',
			'category',
			'tag',
			'term',
			'cf',
			'author',
			'exclude',
			'include',
			'exclude_category',
			'exclude_term',
			'search_term'
		] );
		$post_sort_paging_args = [ 'rows_per_page', 'post_limit', 'offset', 'sort_by', 'sort_order' ];
		$user_search_args      = [ 'search_filters', 'user_search_term' ];

		// Work out what changed
		$modified_args = array_keys( Util::array_diff_assoc( $args, get_object_vars( $this->args ) ) );
		$posts_reset   = false;

		if ( array_intersect( $modified_args, $post_selection_args ) ) {
			// If any of the post paramaters are updated, reset posts array and totals
			$this->query->set_posts( null );
			$this->query->set_total_posts( null );
			$this->query->set_total_filtered_posts( null );
			$posts_reset = true;
		}

		if ( array_intersect( $modified_args, $post_sort_paging_args ) ) {
			// If just the table paramaters are updated, reset posts but not totals
			$this->query->set_posts( null );
			$posts_reset = true;
		}

		// If the user applied a search, reset posts and filtered total but leave the overall total.
		if ( array_intersect( $modified_args, $user_search_args ) ) {
			$this->query->set_posts( null );
			$this->query->set_total_filtered_posts( null );
			$posts_reset = true;
		}

		// If we have an original search term and a user applied search term, we need to reset the total to avoid conflicts.
		if ( $this->args->search_term && in_array( 'user_search_term', $modified_args ) ) {
			$this->query->set_total_posts( null );
		}

		// Disable caching if using lazy load and query params have been modified (e.g. rows_per_page, sort_by, etc)
		// We don't check offset here as we cache each page of results separately using offset in the cache key
		if ( $posts_reset && $this->args->lazy_load && $this->args->cache ) {
			$args['cache'] = false;
		}

		// Finally, we update the args - this will update the args object in all helper classes as objects are stored by reference.
		$this->args->set_args( $args );

		do_action( 'document_library_pro_args_updated', $this );
	}

	private function fetch_data() {
		if ( $this->data_added || ! $this->can_fetch_data() ) {
			return;
		}

		// Reset the table data
		$this->data_table->reset_data();

		if ( $data = $this->cache->get_data() ) {
			$this->data_table->set_data( $data );
		} else {
			// No cache found or caching disabled...
			do_action( 'document_library_pro_before_get_data', $this );

			// Register the data hooks.
			$this->hooks->register();

			// Add all posts to the table.
			$this->add_posts_to_table( $this->query->get_posts() );

			// Reset hooks.
			$this->hooks->reset();

			// Update caches.
			$this->cache->update_table( true );
			$this->cache->update_data( $this->data_table->get_data() );

			do_action( 'document_library_pro_after_get_data', $this );
		}

		$this->data_added = true;
	}

	private function can_fetch_data() {
		if ( ! $this->args->lazy_load ) {
			return true;
		} else {
			return $this->args->lazy_load && defined( 'DOING_AJAX' ) && DOING_AJAX;
		}
	}

	private function add_posts_to_table( $posts ) {
		if ( ! $posts ) {
			return;
		}

		// To make absolutely sure the global $post gets reset, we store it here and set it back after our posts loop
		$old_global_post = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : false;

		$cols = $this->columns->get_all_columns();

		// Loop through posts and add post data
		foreach ( $posts as $_post ) {
			// Set post global as needed for certain calls, e.g. get_the_content()
			$GLOBALS['post'] = $_post;

			// Setup global post data (id, authordata, etc).
			setup_postdata( $_post );

			$this->data_table->new_row( $this->get_row_attributes( $_post ) );

			// Add empty cell if we're using the control column for responsive child rows
			if ( 'column' === $this->args->responsive_control ) {
				$this->data_table->add_data( '', false, self::CONTROL_COLUMN_DATA_SOURCE );
			}

			// Add the data for this post
			array_walk( $cols, [ $this, 'add_post_data' ], $_post );
		}

		// Reset main WP query as we called setup_postdata
		if ( $old_global_post ) {
			$GLOBALS['post'] = $old_global_post;
		}

		wp_reset_postdata();
	}

	private function add_post_data( $column, $key, $post ) {
		$data = '';
		$atts = false;

		// Get data object for this column
		if ( $data_obj = $this->data_factory->create( $column, $post ) ) {
			$data = $data_obj->get_data();
			$atts = array_filter(
				[
					'data-sort'   => $data_obj->get_sort_data(),
					'data-filter' => $data_obj->get_filter_data()
				],
				[ self::class, 'array_filter_empty_attribute' ]
			);
		} else {
			// Back compat: data object not found (e.g. custom column) so run filter
			$data = apply_filters( 'document_library_pro_custom_data_' . $column, '', $post );
			$atts = apply_filters( 'document_library_pro_custom_data_atts_' . $column, false, $post );
		}

		// @deprecated 2.0 - Replaced by individual column filters.
		$data = apply_filters( 'document_library_pro_cell_data_' . Columns_Util::unprefix_column( $column ), $data, $post );

		$this->data_table->add_data( $data, $atts, Columns_Util::get_column_data_source( $column ) );
	}

	private function add_attributes() {
		// Set table attributes.
		$table_class = [ Util::get_table_class() ];

		if ( ! $this->args->wrap ) {
			$table_class[] = 'nowrap';
		}

		if ( $custom_class = apply_filters( 'document_library_pro_custom_class', '', $this ) ) {
			$table_class = array_merge( $table_class, explode( ' ', $custom_class ) );
		}

		$this->data_table->add_attribute( 'id', $this->id );
		$this->data_table->add_attribute( 'class', implode( ' ', $table_class ) );

		// This is required otherwise tables can expand beyond their container.
		$this->data_table->add_attribute( 'width', '100%' );

		// Add the table config as JSON encoded data.
		$this->data_table->add_attribute( 'data-config', htmlspecialchars( self::json_encode_config( $this->config_builder->get_config() ) ) );
		$this->data_table->add_attribute( 'data-filters', htmlspecialchars( wp_json_encode( $this->config_builder->get_filters() ) ) );

		// Set table ordering during initialisation - default to no ordering (i.e. use post order returned from WP_Query).
		$order_attr = '[]';

		// If column is sortable, set initial sort order for DataTables.
		if ( $this->columns->is_sortable( $this->args->sort_by ) ) {
			$sort_index = $this->columns->column_index( $this->args->sort_by );

			if ( false !== $sort_index ) {
				// 'sort_order' has to be in double quotes (@see https://datatables.net/manual/options).
				$order_attr = sprintf( '[[%u, "%s"]]', $sort_index, $this->args->sort_order );
			}
		}
		$this->data_table->add_attribute( 'data-order', $order_attr );
	}

	private function add_headings() {
		// Add the control column for responsive layouts if required (the column that contains the + / - icon)
		if ( 'column' === $this->args->responsive_control ) {
			$this->add_heading( '', [ 'data-data' => self::CONTROL_COLUMN_DATA_SOURCE ], self::CONTROL_COLUMN_DATA_SOURCE );
		}

		// Add column headings
		foreach ( $this->columns->get_columns() as $i => $column ) {
			$data_source = Columns_Util::get_column_data_source( $column );

			$column_atts = [
				'class'           => $this->columns->get_column_heading_class( $i, $column ),
				'data-name'       => Columns_Util::get_column_name( $column ),
				'data-orderable'  => $this->columns->is_sortable( $column ),
				'data-searchable' => $this->columns->is_searchable( $column ),
				'data-width'      => $this->columns->get_column_width( $i, $column ),
				'data-priority'   => $this->columns->get_column_priority( $i, $column )
			];

			if ( $this->args->lazy_load ) {
				// Data source required only for lazy load.
				$column_atts['data-data'] = $data_source;
			}

			$this->add_heading( $this->columns->get_column_heading( $i, $column ), $column_atts, $data_source );
		}

		// Add hidden columns
		foreach ( $this->columns->get_hidden_columns() as $column ) {
			$data_source = Columns_Util::get_column_data_source( $column );

			$column_atts = [
				'data-name'       => Columns_Util::get_column_name( $column ),
				'data-tax'        => Columns_Util::get_column_taxonomy( $column ),
				'data-searchable' => true,
				'data-visible'    => $this->args->show_hidden_columns
			];

			if ( $this->args->lazy_load ) {
				// Data source required only for lazy load.
				$column_atts['data-data'] = $data_source;
			}

			$this->add_heading( $column, $column_atts, $data_source );
		}
	}

	private function add_heading( $heading, $attributes, $key ) {
		$this->data_table->add_header( $heading, $attributes, $key );

		if ( $this->args->show_footer ) {
			$this->data_table->add_footer( $heading, false, $key ); // attributes not needed in footer.
		}
	}

	private function get_row_attributes( $post ) {
		$classes = [
			'post-row',
			'post-type-' . $post->post_type,
			$post->post_type . '-' . $post->ID,
			$post->post_status
		];


		$row_attributes = [
			'id'    => 'post-row-' . $post->ID,
			'class' => implode( ' ', array_map( 'sanitize_html_class', apply_filters( 'document_library_pro_row_class', $classes, $post ) ) )
		];

		return apply_filters( 'document_library_pro_row_attributes', $row_attributes, $post );
	}

	private static function array_filter_empty_attribute( $value ) {
		return ( '' === $value ) ? false : true;
	}

	private static function json_encode_config( $config ) {
		$json_config = wp_json_encode( $config );

		// Ensure JS functions are defined as a function, not a string, in the encoded json
		return preg_replace( '#"(jQuery\.fn.*)"#U', '$1', $json_config );
	}

}
