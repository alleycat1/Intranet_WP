<?php
namespace Barn2\Plugin\Document_Library_Pro\Grid;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args;

/**
 * Handles the caching for document grids.
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Grid_Cache {

	const GRID_CACHE_EXPIRY = DAY_IN_SECONDS;

	public $id;
	public $args;
	public $query;

	/**
	 * Constructor.
	 *
	 * @param mixed $id
	 * @param Table_Args $args
	 * @param Grid_Query $query
	 */
	public function __construct( $id, Table_Args $args, Grid_Query $query ) {
		$this->id    = $id;
		$this->args  = $args;
		$this->query = $query;
	}

	/**
	 * Retrieve a Grid from the cache.
	 *
	 * @param mixed $id
	 * @return false|Document_Grid
	 */
	public static function get_grid( $id ) {
		$grid_cache = get_transient( $id );
		$grid       = false;

		if ( $grid_cache && isset( $grid_cache['args'] ) ) {
			$grid = new Document_Grid( $id, $grid_cache['args'] );

			if ( isset( $grid_cache['total_posts'] ) ) {
				$grid->query->set_total_posts( $grid_cache['total_posts'] );
			}
		}

		return $grid;
	}

	/**
	 * Add a Grid to the cache.
	 */
	public function add_grid() {
		$grid_cache = [ 'args' => $this->args->get_args() ];

		set_transient( $this->get_grid_cache_key(), $grid_cache, self::GRID_CACHE_EXPIRY );
	}

	/**
	 * Update a Grid in the cache.
	 *
	 * @param bool $update_totals
	 */
	public function update_grid( $update_totals = false ) {
		$grid_cache = get_transient( $this->id );

		if ( $grid_cache ) {
			// Existing grid found, so update it.
			$grid_cache['args'] = $this->args->get_args();

			if ( $update_totals ) {
				$grid_cache['total_posts']          = $this->query->get_total_posts();
				$grid_cache['total_filtered_posts'] = $this->query->get_total_filtered_posts();
			}

			set_transient( $this->get_grid_cache_key(), $grid_cache, self::GRID_CACHE_EXPIRY );
		} else {
			// No existing grid in cache, so add it.
			$this->add_grid();
		}
	}

	/**
	 * Get the Grid cache key.
	 */
	private function get_grid_cache_key() {
		return $this->id;
	}
}
