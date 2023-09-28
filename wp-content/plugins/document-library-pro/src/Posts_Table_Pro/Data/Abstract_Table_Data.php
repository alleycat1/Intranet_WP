<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Data;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Columns_Util;
use Barn2\Plugin\Document_Library_Pro\Dependencies\Lib\Table\Table_Data_Interface;

/**
 * Abstract post data class used to fetch data for a post in the table.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
abstract class Abstract_Table_Data implements Table_Data_Interface {

	protected $post;
	protected $links;

	public function __construct( $post, $links = '' ) {
		$this->post  = $post;
		$this->links = $links ? (array) $links : [];
	}

	public function get_filter_data() {
		return '';
	}

	public function get_sort_data() {
		return '';
	}

	/**
	 * Get the category, tag, term or custom field separator for displaying a list of items in the table.
	 *
	 * @param string $item_type The type of item.
	 * @return string The separator.
	 */
	protected static function get_separator( $item_type ) {
		$sep = ', ';

		if ( 'custom_field_row' === $item_type ) {
			$sep = '<br/>';
		}

		return apply_filters( 'document_library_pro_separator', apply_filters( "posts_table_separator_$item_type", $sep ) );
	}

	/**
	 * Get a list of terms for the specified column, returned as an HTML list.
	 *
	 * @param string  $column     The column the retrieve terms for (categories, tags, tax:region, etc).
	 * @param boolean $show_links Whether to include links to the term archive page. Default: false.
	 * @param string  $sep        The term separator, Default: ', '
	 * @return string HTML string of the list of terms.
	 */
	protected function get_terms_for_column( $column, $show_links = false, $sep = ', ' ) {
		$taxonomy = Columns_Util::get_column_taxonomy( $column );

		if ( ! $taxonomy ) {
			return '';
		}

		$terms = get_the_terms( $this->post, $taxonomy );

		if ( is_array( $terms ) ) {
			$result = [];

			foreach ( $terms as $term ) {
				$term_html = sprintf( '<span data-slug="%s">%s</span>', esc_attr( $term->slug ), esc_html( $term->name ) );

				if ( $show_links ) {
					$term_url = get_term_link( $term, $term->taxonomy );

					if ( ! is_wp_error( $term_url ) ) {
						$term_html = sprintf(
							'<a href="%s" data-column="%s" rel="tag">%s</a>',
							esc_url( $term_url ),
							esc_attr( Columns_Util::get_column_name( $column ) ),
							$term_html
						);
					}
				}

				$result[] = $term_html;
			}

			return implode( $sep, $result );
		}

		return '';
	}

}
