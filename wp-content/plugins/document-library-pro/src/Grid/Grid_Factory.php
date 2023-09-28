<?php
namespace Barn2\Plugin\Document_Library_Pro\Grid;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args;

/**
 * A factory for creating Document_Grid objects.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Grid_Factory {

	private static $grids      = [];
	private static $current_id = 1;

	/**
	 * Create new grid based on the supplied args.
	 *
	 * @param array $args The args to use for the table.
	 * @return Document_Grid The document grid object.
	 */
	public static function create( $args ) {
		// Merge in the default args, so our table ID reflects the full list of args, including settings page.
		$args = wp_parse_args( $args, Table_Args::get_site_defaults() );
		$id   = self::generate_id( $args );

		$grid               = new Document_Grid( $id, $args );
		self::$grids[ $id ] = $grid;

		return $grid;
	}

	/**
	 * Fetch an existing grid by ID.
	 *
	 * @param string $id The document grid ID.
	 * @return Document_Grid The document grid object.
	 */
	public static function fetch( $id ) {
		if ( empty( $id ) ) {
			return false;
		}

		$grid = false;

		if ( isset( self::$grids[ $id ] ) ) {
			$grid = self::$grids[ $id ];
		} elseif ( $grid = Grid_Cache::get_grid( $id ) ) {
			self::$grids[ $id ] = $grid;
		}

		return $grid;
	}

	/**
	 * Generate an ID for the Grid.
	 *
	 * @param mixed $args
	 * @return string
	 */
	private static function generate_id( $args ) {
		$id = 'dlp_grid_' . substr( md5( serialize( $args ) ), 0, 16 ) . '_' . self::$current_id; // phpcs:ignore
		self::$current_id ++;

		return $id;
	}

}
