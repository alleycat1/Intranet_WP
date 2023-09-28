<?php
namespace Barn2\Plugin\Document_Library_Pro\Grid;

use Barn2\Plugin\Document_Library_Pro\Document,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util as PTP_Util,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Hooks;

/**
 * Handles the display of a Document_Grid
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Document_Grid {

	public $id;
	public $args;
	public $query;
	public $grid_cards;

	private $grid_html;
	private $cache;
	private $hooks;
	private $grid_initialised = false;

	/**
	 * The Documents to be displayed.
	 *
	 * @var Document[] $documents
	 */
	protected $documents;

	/**
	 * Constructor.
	 *
	 * @param mixed $id
	 * @param array $args
	 */
	public function __construct( $id, array $args ) {
		$this->id = $id;

		$this->args  = new Table_Args( self::add_default_grid_args( $args ) );
		$this->query = new Grid_Query( $this->args );
		$this->cache = new Grid_Cache( $this->id, $this->args, $this->query );
		$this->hooks = new Table_Hooks( $this->args );
	}

	/**
	 * Initiliase and/or retrive a grid.
	 *
	 * @param string $output
	 * @return string
	 */
	public function get_grid( $output = 'html' ) {
		if ( ! $this->grid_initialised ) {
			// Add grid to cache.
			$this->cache->add_grid();

			$this->fetch_documents();
		}

		do_action( 'document_library_pro_before_get_grid', $this );

		if ( 'html' === $output ) {
			$result = $this->grid_html->get_html();
		} elseif ( 'html_parts' === $output ) {
			$result = $this->grid_html->get_html_parts();
		}

		return apply_filters( 'document_library_pro_get_grid_output', $result, $output, $this );
	}

	/**
	 * Update the grid
	 *
	 * @param int|array $args
	 */
	public function update( $args ) {

		// back-compatibility: $args was previously $page_number integer
		if ( is_int( $args ) ) {
			$page_number    = $args;
			$args           = [];
			$args['offset'] = ( $page_number - 1 ) * $this->args->rows_per_page;
		}

		if ( isset( $args['page_number'] ) ) {
			$args['offset'] = ( $args['page_number'] - 1 ) * $this->args->rows_per_page;
			unset( $args['page_number'] );
		}

		$this->grid_initialised = false;

		$post_selection_args   = apply_filters(
			'document_library_pro_update_post_selection_args',
			[
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
			]
		);
		$post_sort_paging_args = [ 'rows_per_page', 'post_limit', 'offset', 'sort_by', 'sort_order' ];
		$user_search_args      = [ 'search_filters', 'user_search_term' ];

		// Work out what changed
		$modified_args = array_keys( PTP_Util::array_diff_assoc( $args, get_object_vars( $this->args ) ) );
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
		if ( $this->args->search_term && in_array( 'user_search_term', $modified_args, true ) ) {
			$this->query->set_total_posts( null );
		}

		// Disable caching if using lazy load and query params have been modified (e.g. rows_per_page, sort_by, etc)
		// We don't check offset here as we cache each page of results separately using offset in the cache key
		if ( $posts_reset && $this->args->lazy_load && $this->args->cache ) {
			$args['cache'] = false;
		}

		// Finally, we update the args - this will update the args object in all helper classes as objects are stored by reference.
		$this->args->set_args( $args );

		do_action( 'document_library_pro_grid_args_updated', $this );
	}

	/**
	 * Fetch the documents.
	 */
	private function fetch_documents() {
		// No cache found or caching disabled...
		do_action( 'document_library_pro_before_get_grid_documents', $this );

		// Register the data hooks.
		$this->hooks->register();

		// Add all documents
		$this->grid_cards = array_map(
			function( $document_post ) {
				return new Grid_Card( new Document( $document_post->ID ), $this->args, $document_post );
			},
			$this->query->get_posts()
		);

		$this->grid_html = new Document_Grid_Html( $this->id, $this->grid_cards, $this->query, $this->args );

		// Reset hooks.
		$this->hooks->reset();

		// Update caches.
		$this->cache->update_grid( true );

		do_action( 'document_library_pro_after_get_grid_documents', $this );
	}

	/**
	 * Add default args which always apply to the grid.
	 *
	 * @param array $args
	 * @return array
	 */
	private static function add_default_grid_args( $args ) {
		$args = array_merge( $args, [ 'lazy_load' => true ] );

		return $args;
	}
}
