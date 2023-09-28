<?php
namespace Barn2\Plugin\Document_Library_Pro\Grid;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args;

/**
 * Handles the HTML for grid pagination
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Grid_Pagination {

	public $query;
	public $args;

	protected $max_num_pages;
	protected $current_page;
	protected $paging_type;

	/**
	 * Constructor.
	 *
	 * @param Grid_Query $query
	 * @param Table_Args $args
	 */
	public function __construct( Grid_Query $query, Table_Args $args ) {
		$this->query         = $query;
		$this->args          = $args;
		$this->max_num_pages = $this->query->get_max_num_pages();
		$this->current_page  = $this->query->get_current_page();
		$this->paging_type   = $this->args->get_args()['paging_type'];
	}

	/**
	 * Retrieves the HTML for the pagination.
	 *
	 * @return string
	 */
	public function get_html() {
		if ( ! is_numeric( $this->max_num_pages ) ) {
			return '<div class="dlp-grid-pagination"></div>';
		}

		if ( $this->max_num_pages <= 1 ) {
			return '<div class="dlp-grid-pagination"></div>';
		}

		$html = '<div class="dlp-grid-pagination">';

		if ( $this->has_first_last_links() ) {
			$html .= $this->get_first_link();
		}

		if ( $this->has_prev_next_links() ) {
			$html .= $this->get_prev_link();
		}

		if ( $this->has_page_number_links() ) {
			$html .= $this->get_page_number_links();
		}

		if ( $this->has_prev_next_links() ) {
			$html .= $this->get_next_link();
		}

		if ( $this->has_first_last_links() ) {
			$html .= $this->get_last_link();
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * Checks whether Previous | Next links are enabled
	 *
	 * @return bool
	 */
	protected function has_prev_next_links() {
		return in_array( $this->paging_type, [ 'simple', 'simple_numbers', 'full', 'full_numbers' ], true );
	}

	/**
	 * Checks whether First | Last links are enabled
	 *
	 * @return bool
	 */
	protected function has_first_last_links() {
		return in_array( $this->paging_type, [ 'full', 'full_numbers' ], true );
	}

	/**
	 * Checks whether Number links are enabled
	 *
	 * @return bool
	 */
	protected function has_page_number_links() {
		return in_array( $this->paging_type, [ 'numbers', 'simple_numbers', 'full_numbers' ], true );
	}

	/**
	 * Retrieve the Number links HTML.
	 *
	 * @return string
	 */
	protected function get_page_number_links() {

		if ( $this->max_num_pages <= 5 ) {
			$page_links = array_fill( 1, $this->max_num_pages, '' );
		} elseif ( $this->current_page <= 4 ) {
			$page_links = array_fill( 1, 5, '' );
			$page_links[6] = 'ellipsis';
			$page_links[ $this->max_num_pages ] = '';
		} elseif ( $this->current_page > $this->max_num_pages - 4 ) {
			$page_links = array_fill( 1, 1, '' );
			$page_links[2] = 'ellipsis';
			$page_links = $page_links + array_fill( $this->max_num_pages - 4, 5, '' );
		} elseif ( $this->current_page > 4 && $this->current_page <= $this->max_num_pages - 4 ) {
			$page_links = array_fill( 1, 1, '' );
			$page_links[2] = 'ellipsis';
			$page_links = $page_links + array_fill( $this->current_page - 1, 3, '' );
			$page_links[ $this->max_num_pages - 1 ] = 'ellipsis';
			$page_links[ $this->max_num_pages ] = '';
		} else {
			$page_links = array_fill( 1, $this->max_num_pages, '' );
		}

		if ( isset( $page_links[ $this->current_page ] ) ) {
			$page_links[ $this->current_page ] = 'current';
		}

		$html = '';

		foreach ( $page_links as $index => $class ) {
			if ( $class === 'ellipsis' ) {
				$html .= '<span class="dlp-grid-paginate-ellipsis">&hellip;</span>';
			} else {
				$html .= sprintf( '<a class="dlp-grid-paginate-button number %1$s" data-page-number="%2$s">%2$s</a>', $class, $index );
			}
		}

		return $html;
	}

	/**
	 * Retrieve the Previous link HTML.
	 *
	 * @return string
	 */
	protected function get_prev_link() {
		$disabled    = $this->current_page === 1 ? 'disabled' : '';
		$page_number = max( $this->current_page - 1, 1 );

		return sprintf( '<a class="dlp-grid-paginate-button prev %1$s" data-page-number="%2$s">%3$s</a>', $disabled, $page_number, __( 'Previous', 'document-library-pro' ) );
	}

	/**
	 * Retrieve the Next link HTML.
	 *
	 * @return string
	 */
	protected function get_next_link() {
		$disabled    = $this->current_page === $this->max_num_pages ? 'disabled' : '';
		$page_number = min( $this->current_page + 1, $this->max_num_pages );

		return sprintf( '<a class="dlp-grid-paginate-button next %1$s" data-page-number="%2$s">%3$s</a>', $disabled, $page_number, __( 'Next', 'document-library-pro' ) );
	}

	/**
	 * Retrieve the First link HTML.
	 *
	 * @return string
	 */
	protected function get_first_link() {
		$disabled = $this->current_page === 1 ? 'disabled' : '';

		return sprintf( '<a class="dlp-grid-paginate-button first %1$s" data-page-number="%2$s">%3$s</a>', $disabled, 1, __( 'First', 'document-library-pro' ) );
	}

	/**
	 * Retrieve the Last link HTML.
	 *
	 * @return string
	 */
	protected function get_last_link() {
		$disabled = $this->current_page === $this->max_num_pages ? 'disabled' : '';

		return sprintf( '<a class="dlp-grid-paginate-button last %1$s" data-page-number="%2$s">%3$s</a>', $disabled, $this->max_num_pages, __( 'Last', 'document-library-pro' ) );
	}

}
