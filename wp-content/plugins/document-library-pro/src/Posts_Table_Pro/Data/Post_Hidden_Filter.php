<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Columns_Util;
use WP_Term;

/**
 * Gets post data for a hidden filter column.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Post_Hidden_Filter extends Abstract_Table_Data {

	private $filter_column;
	private $lazy_load;

	public function __construct( $post, $filter_column, $lazy_load = false ) {
		parent::__construct( $post );

		$this->filter_column = $filter_column;
		$this->lazy_load     = $lazy_load;
	}

	public function get_data() {
		// Hidden filter data not used for lazy load as it's handled server side, so we can just return an empty string.
		if ( $this->lazy_load ) {
			return '';
		}

		$taxonomy = Columns_Util::get_column_taxonomy( $this->filter_column );

		if ( ! $taxonomy ) {
			return '';
		}

		$result     = '';
		$post_terms = get_the_terms( $this->post, $taxonomy );

		if ( $post_terms && is_array( $post_terms ) ) {
			// If tax is hierarchical, we need to add any ancestor terms for each term this product has
			if ( is_taxonomy_hierarchical( $taxonomy ) ) {
				$ancestors = [];

				// Get the ancestors term IDs for all terms for this product
				foreach ( $post_terms as $term ) {
					$ancestors = array_merge( $ancestors, get_ancestors( $term->term_id, $taxonomy, 'taxonomy' ) );
				}

				// Remove duplicates
				$ancestors     = array_unique( $ancestors );
				$post_term_ids = wp_list_pluck( $post_terms, 'term_id' );

				// If not already in term list, convert ancestor to WP_Term object and add to results
				foreach ( $ancestors as $ancestors_term_id ) {
					if ( ! in_array( $ancestors_term_id, $post_term_ids, true ) ) {
						$term = get_term( $ancestors_term_id, $taxonomy );

						if ( $term instanceof WP_Term ) {
							$post_terms[] = $term;
						}
					}
				}
			}

			$result = implode( self::get_term_separator(), wp_list_pluck( $post_terms, 'slug' ) );
		}

		return apply_filters( 'document_library_pro_data_hidden_filter', $result, $this->filter_column, $this->post );
	}

	public static function get_term_separator() {
		return ' ';
	}

}
