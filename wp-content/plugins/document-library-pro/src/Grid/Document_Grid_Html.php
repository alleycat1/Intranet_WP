<?php
namespace Barn2\Plugin\Document_Library_Pro\Grid;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args;
use Barn2\Plugin\Document_Library_Pro\Util\SVG_Icon;
use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util as PTP_Util;

/**
 * Handles the display of a Document_Grid
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Grid_Html {

	public $id;
	public $args;
	public $query;
	public $grid_cards;

	protected $documents;

	private $html;
	private $html_parts;

	/**
	 * Constructor.
	 *
	 * @param mixed $id
	 * @param array $grid_cards
	 * @param Grid_Query $query
	 * @param Table_Args $args
	 */
	public function __construct( $id, array $grid_cards, Grid_Query $query, Table_Args $args ) {
		$this->id         = $id;
		$this->args       = $args;
		$this->query      = $query;
		$this->grid_cards = $grid_cards;
		$this->html_parts = $this->generate_html_parts();
		$this->html       = $this->generate_html();
	}

	/**
	 * Get the grid HTML.
	 *
	 * @return string
	 */
	public function get_html() {
		return $this->html;
	}

	/**
	 * Get the grid HTML parts.
	 *
	 * @return array
	 */
	public function get_html_parts() {
		return $this->html_parts;
	}

	/**
	 * Generate the grid HTML parts
	 *
	 * @return string
	 */
	private function generate_html_parts() {
		$html_parts = [
			'grid'       => $this->get_grid_html(),
			'filters'    => [
				'header' => $this->get_filters_html( 'top' ),
				'footer' => $this->get_filters_html( 'bottom' ),
			],
			'totals'     => [
				'header' => $this->get_totals_html( 'top' ),
				'footer' => $this->get_totals_html( 'bottom' ),
			],
			'pagination' => [
				'header' => $this->get_pagination_html( 'top' ),
				'footer' => $this->get_pagination_html( 'bottom' ),
			],
		];

		return $html_parts;
	}

	/**
	 * Generates the grid HTML.
	 *
	 * @return string
	 */
	public function generate_html() {
		$html = sprintf( '<div id="%s" class="dlp-grid-container">', $this->id );

		$html .= $this->get_header_html();

		$html .= $this->html_parts['grid'];

		$html .= $this->get_footer_html();

		$html .= '</div>';

		return $html;
	}

	/**
	 * Gets the Main Grid Section HTML.
	 *
	 * @return string
	 */
	private function get_grid_html() {

		$grid_class = $this->args->grid_columns === 'autosize' ? 'grid-autosize' : sprintf( 'grid-columns columns-%s', $this->args->grid_columns );
		$grid_html  = sprintf( '<div class="dlp-grid-documents %s">', $grid_class );

		if ( ! empty( $this->query->get_posts() ) ) {
			foreach ( $this->grid_cards as $card ) {
				$grid_html .= $card->get_html();
			}
		} else {
			$message_type = $this->query->is_filtered_frontend() ? 'no_posts_filtered_message' : 'no_posts_message';
			$grid_html .= ! empty( $this->args->get_args()[ $message_type ] ) ? $this->args->get_args()[ $message_type ] : __( 'No matching documents', 'document-library-pro' );
		}

		$grid_html .= '</div>';

		return $grid_html;
	}


	/**
	 * Get thes the Grid Header HTML.
	 *
	 * @return string
	 */
	private function get_header_html() {

		$header_html = '<header class="dlp-grid-header">';

		$header_html .= $this->html_parts['filters']['header'];

		$header_html .= $this->html_parts['totals']['header'];

		$header_html .= $this->html_parts['pagination']['header'];

		$header_html .= '</header>';

		return $header_html;
	}

	/**
	 * Gets the Grid Footer HTML.
	 *
	 * @return string
	 */
	private function get_footer_html() {

		$footer_html = '<footer class="dlp-grid-footer">';

		$footer_html .= $this->html_parts['filters']['footer'];

		$footer_html .= $this->html_parts['totals']['footer'];

		$footer_html .= $this->html_parts['pagination']['footer'];

		$footer_html .= '</footer>';

		return $footer_html;
	}

	/**
	 * Gets the Filters HTML.
	 *
	 * @param string $context
	 * @return string
	 */
	private function get_filters_html( $context = 'top' ) {
		$search_position = $this->args->get_args()['search_box'];
		$reset_button    = $this->args->get_args()['reset_button'];

		$filters_html = '<div class="dlp-grid-filters">';

		// Search Input
		if ( in_array( $search_position, [ $context, 'both' ], true ) ) {
			$filters_html .= $this->get_search_box();
		}

		if ( $context === 'top' && $reset_button ) {
			$filters_html .= $this->get_reset_button();
		}

		$filters_html .= '</div>';

		return $filters_html;
	}

	/**
	 * Gets the Totals HTML.
	 *
	 * @param string $context
	 * @return string
	 */
	private function get_totals_html( $context = 'top' ) {
		$totals_position = $this->args->get_args()['totals'];
		$totals_html     = '';

		if ( in_array( $totals_position, [ $context, 'both' ], true ) ) {
			$totals_html = $this->get_result_count();
		}

		return $totals_html;
	}

	/**
	 * Gets the Pagination HTML.
	 *
	 * @param string $context
	 * @return string
	 */
	private function get_pagination_html( $context = 'top' ) {
		$pagination          = new Grid_Pagination( $this->query, $this->args );
		$pagination_position = $this->args->get_args()['pagination'];
		$pagination_html     = '';

		if ( in_array( $pagination_position, [ $context, 'both' ], true ) ) {
			$pagination_html = $pagination->get_html();
		}

		return $pagination_html;
	}

	/**
	 * Gets the Result Count HTML.
	 *
	 * @return string
	 */
	private function get_result_count() {
		$language_config = PTP_Util::get_language_strings( 'dlp_document' );

		if ( $this->query->get_total_posts() !== $this->query->get_total_filtered_posts() ) {
			/* translators: %d: The total number of documents. */
			$document_count = esc_html( sprintf( __( '%1$d %3$s (%2$d in total)', 'document-library-pro' ), $this->query->get_total_filtered_posts(), $this->query->get_total_posts(), $language_config['totalsPlural'] ) );
		} else {
			/* translators: %d: The total number of documents. */
			$document_count = esc_html( sprintf( __( '%1$d %2$s', 'document-library-pro' ), $this->query->get_total_filtered_posts(), $language_config['totalsPlural'] ) );
		}

		return sprintf( '<div class="dlp-grid-totals">%s</div>', $document_count );
	}

	/**
	 * Gets the Search Box HTML.
	 *
	 * @return string
	 */
	private function get_search_box() {
		$search_term      = $this->args->get_args()['search_term'];
		$user_search_term = filter_input( INPUT_GET, 'dlp_search', FILTER_SANITIZE_FULL_SPECIAL_CHARS ) ?? '';

		$search_query = ! empty( $user_search_term ) ? $user_search_term : $search_term;

		return sprintf(
			'<div class="dlp-grid-search"><label>%s<input type="search" value="%s"></label></div>',
			__( 'Search:', 'document-library-pro' ),
			$search_query
		);
	}

	/**
	 * Gets the Reset Button HTML
	 *
	 * @return string
	 */
	private function get_reset_button() {

		return sprintf(
			'<div class="dlp-grid-reset"><a class="reset" href="#">%1$s %2$s</a></div>',
			SVG_Icon::get( 'reset' ),
			esc_html__( 'Reset', 'document-library-pro' )
		);

	}
}
