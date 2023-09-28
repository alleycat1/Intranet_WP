<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Columns_Util;

/**
 * Responsible for managing the columns for a specific Posts Table, and column utility functions.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Columns {

	/**
	 * @var Table_Args The table args.
	 */
	private $args;

	public function __construct( Table_Args $args ) {
		$this->args = $args;
	}

	public function get_all_columns() {
		return array_merge( $this->get_columns(), $this->get_hidden_columns() );
	}

	public function get_columns() {
		return $this->args->columns;
	}

	public function get_hidden_columns() {
		$hidden = [];

		if ( $this->args->filters ) {
			$hidden = preg_replace( '/^/', 'hf:', $this->args->filters );
		}

		return $hidden;
	}

	public function column_index( $column, $incude_hidden = false ) {
		$cols  = $incude_hidden ? $this->get_all_columns() : $this->get_columns();
		$index = array_search( $column, $cols );
		$index = is_int( $index ) ? $index : false; // sanity check

		if ( false !== $index ) {
			if ( 'column' === $this->args->responsive_control ) {
				$index++;
			}
		}
		return $index;
	}

	public function get_column_heading( $index, $column ) {
		$standard_cols  = Columns_Util::column_defaults();
		$unprefixed_col = Columns_Util::unprefix_column( $column );
		$heading        = '';

		if ( isset( $standard_cols[ $column ]['heading'] ) ) {
			$heading = $standard_cols[ $column ]['heading'];
		} elseif ( $tax = Columns_Util::get_custom_taxonomy( $column ) ) {
			// If custom taxonomy, get the main taxonomy label
			if ( $tax_obj = get_taxonomy( $tax ) ) {
				$heading = $tax_obj->label;
			}
		} else {
			$heading = trim( ucwords( str_replace( [ '_', '-' ], ' ', $unprefixed_col ) ) );
		}

		$heading = apply_filters( 'document_library_pro_column_heading_' . $unprefixed_col, $heading );

		if ( is_int( $index ) && ! empty( $this->args->headings[ $index ] ) ) {
			// Custom heading.
			$heading = Columns_Util::check_blank_heading( $this->args->headings[ $index ] );
		}

		return $heading;
	}

	/**
	 * Get the class for each column header <th> tag. This sets the responsive visibility class based on the responsive options.
	 *
	 * @param int    $index  The column index (0 based)
	 * @param string $column The column name
	 * @return string The CSS class
	 */
	public function get_column_heading_class( $index, $column ) {
		$class = [];

		if ( 0 === $index && 'inline' === $this->args->responsive_control ) {
			$class[] = 'all';
		} elseif ( is_int( $index ) && isset( $this->args->column_breakpoints[ $index ] ) && 'default' !== $this->args->column_breakpoints[ $index ] ) {
			$class[] = $this->args->column_breakpoints[ $index ];
		}

		return implode( ' ', apply_filters( 'document_library_pro_header_class_' . Columns_Util::unprefix_column( $column ), $class ) );
	}

	public function get_column_priority( $index, $column ) {
		$standard_cols = Columns_Util::column_defaults();

		$priority = isset( $standard_cols[ $column ]['priority'] ) ? $standard_cols[ $column ]['priority'] : '';
		$priority = apply_filters( 'document_library_pro_column_priority_' . Columns_Util::unprefix_column( $column ), $priority );

		if ( is_int( $index ) && isset( $this->args->priorities[ $index ] ) ) {
			$priority = $this->args->priorities[ $index ];
		}

		return $priority;
	}

	public function get_column_width( $index, $column ) {
		$width = apply_filters( 'document_library_pro_column_width_' . Columns_Util::unprefix_column( $column ), '' );

		if ( is_int( $index ) && isset( $this->args->widths[ $index ] ) ) {
			$width = $this->args->widths[ $index ];
		}

		if ( 'auto' === $width ) {
			$width = '';
		} elseif ( is_numeric( $width ) ) {
			$width = $width . '%';
		}

		return $width;
	}

	public function is_click_filterable( $column ) {
		$click_filterable = in_array( $column, [ 'categories', 'tags', 'author' ] ) || Columns_Util::is_custom_taxonomy( $column );

		if ( $this->args->lazy_load && 'author' === $column ) {
			$click_filterable = false;
		}

		$click_filterable = apply_filters( 'document_library_pro_column_click_filterable_' . Columns_Util::unprefix_column( $column ), $click_filterable );

		return $click_filterable;
	}

	public function is_searchable( $column ) {
		$searchable = true;

		if ( in_array( $column, [ 'image', 'button' ] ) ) {
			$searchable = false;
		}

		// Only allow filtering if column is searchable.
		if ( $searchable ) {
			$searchable = apply_filters( 'document_library_pro_column_searchable', $searchable, Columns_Util::unprefix_column( $column ) );
			$searchable = apply_filters( 'document_library_pro_column_searchable_' . Columns_Util::unprefix_column( $column ), $searchable );
		}

		return $searchable;
	}

	public function is_sortable( $column ) {
		$sortable = false;

		if ( ! $this->args->lazy_load && ! in_array( $column, [ 'image', 'button' ] ) ) {
			$sortable = true;
		}

		if ( $this->args->lazy_load && in_array( $column, [ 'id', 'title', 'date', 'date_modified', 'author' ] ) ) {
			$sortable = true;
		}

		// Only allow filtering if column is sortable.
		if ( $sortable ) {
			$sortable = apply_filters( 'document_library_pro_column_sortable', $sortable, Columns_Util::unprefix_column( $column ) );
			$sortable = apply_filters( 'document_library_pro_column_sortable_' . Columns_Util::unprefix_column( $column ), $sortable );
		}

		return $sortable;
	}

}
