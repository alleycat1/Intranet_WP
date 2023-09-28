<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Columns_Util;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;

/**
 * Responsible for creating the posts table config script.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Config_Builder {

	/**
	 * @var string The table ID.
	 */
	public $id;

	/**
	 * @var Table_Args The table args.
	 */
	public $args;

	/**
	 * @var Table_Columns The table columns.
	 */
	public $columns;

	public function __construct( $id, Table_Args $args, Table_Columns $columns ) {
		$this->id      = $id;
		$this->args    = $args;
		$this->columns = $columns;
	}

	/**
	 * Build config for this table to add as an inline script below table.
	 *
	 * @return array The posts table config
	 */
	public function get_config() {
		$config = [
			'pageLength'   => $this->args->rows_per_page,
			'pagingType'   => $this->args->paging_type,
			'serverSide'   => $this->args->lazy_load,
			'autoWidth'    => $this->args->auto_width,
			'clickFilter'  => $this->args->search_on_click,
			'scrollOffset' => $this->args->scroll_offset,
			'resetButton'  => $this->args->reset_button
		];

		$config['lengthMenu'] = [ 10, 25, 50, 100 ];

		if ( $this->args->rows_per_page > 0 && ! in_array( $this->args->rows_per_page, $config['lengthMenu'] ) ) {
			// Remove any default page lengths that are too close to 'rows_per_page'
			$config['lengthMenu'] = array_filter( $config['lengthMenu'], [ $this, 'array_filter_length_menu' ] );

			// Add 'rows_per_page' to length menu and sort
			array_push( $config['lengthMenu'], $this->args->rows_per_page );
			sort( $config['lengthMenu'] );
		}

		// All show all to menu
		if ( ! $this->args->lazy_load || -1 === $this->args->rows_per_page ) {
			$config['lengthMenu']      = [ $config['lengthMenu'], $config['lengthMenu'] ];
			$config['lengthMenu'][0][] = -1;
			$config['lengthMenu'][1][] = _x( 'All', 'show all posts option', 'document-library-pro' );
		}

		$responsive_details = [];

		// Set responsive control column
		if ( 'column' === $this->args->responsive_control ) {
			$responsive_details     = [ 'type' => 'column' ];
			$config['columnDefs'][] = [
				'className' => 'control',
				'orderable' => false,
				'targets'   => 0
			];
		}

		foreach ( $this->columns->get_columns() as $column ) {
			$class = [ Columns_Util::get_column_class( $column ) ];

			if ( 'date' === $column ) {
				// If date column used and date format contains no spaces, make sure we 'nowrap' this column
				$date_format = $this->args->date_format ? $this->args->date_format : get_option( 'date_format' );

				if ( false === strpos( $date_format, ' ' ) ) {
					$class[] = 'nowrap';
				}
			}

			$class = apply_filters( 'document_library_pro_column_class_' . Columns_Util::unprefix_column( $column ), $class );

			$config['columnDefs'][] = [
				'className' => implode( ' ', array_filter( $class ) ),
				'targets'   => $this->columns->column_index( $column )
			];
		}

		// Set responsive display function
		$responsive_details   = array_merge( $responsive_details, [ 'display' => $this->args->responsive_display ] );
		$config['responsive'] = [ 'details' => $responsive_details ];

		// Add table-specific language strings.
		$config['language'] = Util::get_language_strings( $this->args->post_type );

		// Set custom messages from table args (these override get_language_strings).
		if ( $this->args->no_posts_message ) {
			$config['language']['emptyTable'] = esc_html( $this->args->no_posts_message );
		}
		if ( $this->args->no_posts_filtered_message ) {
			$config['language']['zeroRecords'] = esc_html( $this->args->no_posts_filtered_message );
		}

		// Set initial search term
		if ( $this->args->search_term ) {
			$config['search']['search'] = $this->args->search_term;
		}

		// DOM option
		$dom_top         = '';
		$dom_bottom      = '';
		$display_options = [
			'l' => 'page_length',
			'f' => 'search_box',
			'i' => 'totals',
			'p' => 'pagination'
		];

		foreach ( $display_options as $letter => $option ) {
			if ( 'top' === $this->args->$option || 'both' === $this->args->$option ) {
				$dom_top .= $letter;
			}
			if ( 'bottom' === $this->args->$option || 'both' === $this->args->$option ) {
				$dom_bottom .= $letter;
			}
		}

		$dom_top       = '<"posts-table-above posts-table-controls"' . $dom_top . '>';
		$dom_bottom    = $dom_bottom ? '<"posts-table-below posts-table-controls"' . $dom_bottom . '>' : '';
		$config['dom'] = sprintf( '<"%s"%st%s>', esc_attr( Util::get_wrapper_class() ), $dom_top, $dom_bottom );

		// Filter and return the config array.
		return apply_filters( 'document_library_pro_data_config', $config, $this->args );
	}

	public function get_filters() {
		if ( ! $this->args->filters ) {
			return false;
		}

		$filters = [];

		// Add drop-down values for each search filter
		foreach ( $this->args->filters as $i => $filter ) {
			if ( ! ( $tax = Columns_Util::get_column_taxonomy( $filter ) ) ) {
				continue;
			}

			if ( ! ( $terms = $this->get_terms_for_filter( $tax ) ) ) {
				continue;
			}

			$column_name = Columns_Util::get_column_name( $filter );

			// Add terms to array
			$filters[ $column_name ] = [
				'taxonomy'     => $tax,
				'heading'      => $this->get_filter_heading( $i, $filter ),
				'terms'        => $terms,
				'searchColumn' => Columns_Util::get_column_name( 'hf:' . $filter )
			];
		}

		$filters = apply_filters( 'document_library_pro_data_filters', $filters, $this->args );
		return $filters ? $filters : false;
	}

	private function get_filter_heading( $index, $filter ) {
		$heading = false;

		if ( ! empty( $this->args->filter_headings[ $index ] ) ) {
			// 1. Use custom filter heading if set.
			$heading = $this->args->filter_headings[ $index ];
		} elseif ( false !== ( $filter_column_index = array_search( $filter, $this->columns->get_columns(), true ) ) ) {
			// 2. Use custom column heading if set, and we're showing the filter and column together.
			if ( ! empty( $this->args->headings[ $filter_column_index ] ) ) {
				$heading = $this->args->headings[ $filter_column_index ];
			}
		}

		$heading     = Columns_Util::check_blank_heading( $heading );
		$column_name = Columns_Util::unprefix_column( $filter );

		if ( false === $heading ) {
			// 3. Use the taxonomy label (singular).
			$tax     = Columns_Util::get_column_taxonomy( $filter );
			$tax_obj = $tax ? get_taxonomy( $tax ) : false;

			if ( $tax_obj ) {
				$heading = $tax_obj->labels->singular_name;
			} else {
				// 4. Fallback if taxonomy not found - use the filter column name.
				$heading = ucfirst( $column_name );
			}
		}

		return apply_filters( 'document_library_pro_search_filter_heading_' . $column_name, $heading, $this->args );
	}

	private function get_terms_for_filter( $taxonomy ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return false;
		}

		$term_args = [
			'taxonomy'     => $taxonomy,
			'fields'       => 'all',
			'hide_empty'   => true,
			'hierarchical' => true,
		];

		if ( 'category' === $taxonomy ) {
			if ( $this->args->category ) {
				// We're including one or more categories, so find all descendents and include them in term query.
				if ( $include_ids = Util::get_all_term_children( Util::convert_to_term_ids( $this->args->category, 'category' ), 'category', true ) ) {
					$term_args['include'] = $include_ids;
				}
			}

			if ( $this->args->exclude_category && empty( $term_args['include'] ) ) {
				// We're excluding categories, so remove this and all descendants from the category search filter.
				// We only do if include is empty, as exclude_tree is ignored when include is set.
				if ( $exclude_ids = Util::convert_to_term_ids( $this->args->exclude_category, 'category' ) ) {
					$term_args['exclude_tree'] = $exclude_ids;
				}
			}
		}

		if ( $this->args->term && empty( $term_args['include'] ) ) {
			// We're selecting by term - see if we need to restrict items in the corresponding search filter.
			$include_terms = Util::parse_term_arg( $this->args->term );

			if ( ! empty( $include_terms[ $taxonomy ] ) && $include_term_ids = Util::convert_to_term_ids( $include_terms[ $taxonomy ], $taxonomy ) ) {
				$term_args['include'] = Util::get_all_term_children( $include_term_ids, $taxonomy, true );
			}
		}

		if ( $this->args->exclude_term && empty( $term_args['include'] ) && empty( $term_args['exclude_tree'] ) ) {
			// We're excluding terms from the table, and no includes or excludes were set above.
			$exclude_terms = Util::parse_term_arg( $this->args->exclude_term );

			if ( ! empty( $exclude_terms[ $taxonomy ] ) && $exclude_term_ids = Util::convert_to_term_ids( $exclude_terms[ $taxonomy ], $taxonomy ) ) {
				$term_args['exclude_tree'] = $exclude_term_ids;
			}
		}

		// Get the terms
		$terms = get_terms( apply_filters( 'document_library_pro_search_filter_get_terms_args', $term_args, $taxonomy, $this->args ) );

		if ( ! is_array( $terms ) ) {
			$terms = [];
		}

		// Filter the terms.
		$terms = apply_filters( 'document_library_pro_search_filter_terms', $terms, $taxonomy, $this->args );
		$terms = apply_filters( 'document_library_pro_search_filter_terms_' . $taxonomy, $terms, $this->args );

		if ( empty( $terms ) ) {
			return $terms;
		}

		// Convert term objects to arrays, and re-key
		$result = array_map( 'get_object_vars', array_values( $terms ) );

		// Build term hierarchy so we can create the nested filter items
		if ( is_taxonomy_hierarchical( $taxonomy ) ) {
			$result = $this->build_term_tree( $result );
		}

		// Just return term name, slug and child terms for the filter
		return Util::list_pluck_array( $result, [ 'name', 'slug', 'children' ] );
	}

	private function build_term_tree( array &$terms, int $parent_id = 0 ) {
		$branch = [];

		foreach ( $terms as $i => $term ) {
			if ( isset( $term['parent'] ) && (int) $term['parent'] === $parent_id ) {
				$children = $this->build_term_tree( $terms, (int) $term['term_id'] );

				if ( $children ) {
					$term['children'] = $children;
				}
				$branch[] = $term;
				unset( $terms[ $i ] );
			}
		}

		// If we're at the top level branch (parent = 0) and there are terms remaining, we need to
		// loop through each and build the tree for that term.
		if ( 0 === $parent_id && $terms ) {
			$remaining_term_ids = wp_list_pluck( $terms, 'term_id' );

			foreach ( $terms as $term ) {
				if ( ! isset( $term['parent'] ) ) {
					continue;
				}

				// Only build tree if term won't be 'picked up' by its parent term.
				if ( ! in_array( $term['parent'], $remaining_term_ids, true ) ) {
					$branch = array_merge( $branch, $this->build_term_tree( $terms, $term['parent'] ) );
				}
			}
		}

		return $branch;
	}

	private function array_filter_length_menu( $length ) {
		$diff = abs( $length - $this->args->rows_per_page );
		return $diff / $length > 0.2 || $diff > 4;
	}

}
