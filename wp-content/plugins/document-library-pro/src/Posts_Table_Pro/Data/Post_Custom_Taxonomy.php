<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;

/**
 * Gets post data for a custom taxonomy column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Custom_Taxonomy extends Abstract_Table_Data {

	private $column;
	private $taxonomy;
	private $date_format;
	private $is_date;

	public function __construct( $post, $taxonomy, $links = '', $date_format = '', $date_columns = [] ) {
		parent::__construct( $post, $links );

		$this->column      = 'tax:' . $taxonomy;
		$this->taxonomy    = $taxonomy;
		$this->date_format = $date_format;
		$this->is_date     = in_array( $this->column, $date_columns, true );
	}

	public function get_data() {
		$sep        = parent::get_separator( 'terms' );
		$show_links = array_intersect( [ 'all', 'terms' ], $this->links );

		$terms = parent::get_terms_for_column( $this->column, $show_links, $sep );

		// If taxonomy is a date and there's only 1 term, format value in required date format.
		if ( $this->is_date && $this->date_format && ( false === strpos( $terms, $sep ) ) ) {
			if ( $timestamp = $this->convert_to_timestamp( $terms ) ) {
				$terms = date_i18n( $this->date_format, $timestamp );
			}
		}

		// Filter the result
		$terms = apply_filters( 'document_library_pro_data_custom_taxonomy', $terms, $this->taxonomy, $this->post );
		$terms = apply_filters( 'document_library_pro_data_custom_taxonomy_' . $this->taxonomy, $terms, $this->post );

		return $terms;
	}

	private function convert_to_timestamp( $date ) {
		if ( ! $date ) {
			return false;
		}

		if ( apply_filters( 'document_library_pro_taxonomy_is_eu_au_date', false, $this->taxonomy ) ) {
			$date = str_replace( '/', '-', $date );
		}

		return Util::strtotime( $date );
	}

	public function get_sort_data() {
		if ( $this->is_date ) {
			$date_terms = get_the_terms( $this->post->ID, $this->taxonomy );

			if ( is_array( $date_terms ) && 1 === count( $date_terms ) ) {
				$date_term = reset( $date_terms );

				// Format the hidden date column for sorting
				if ( $timestamp = $this->convert_to_timestamp( $date_term->name ) ) {
					return $timestamp;
				}
			}

			// Need to return non-empty string to ensure all cells have a data-sort value.
			return '0';
		}

		return '';
	}

}
