<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro;

/**
 * A factory for creating Posts_Table objects.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Factory {

	private static $tables     = [];
	private static $current_id = 1;

	/**
	 * Create a new table based on the supplied args.
	 *
	 * @param array $args The args to use for the table.
	 * @return Posts_Table The posts table object.
	 */
	public static function create( array $args = [] ) {
		// Merge in the default args, so our table ID reflects the full list of args including plugin settings.
		$args = wp_parse_args( $args, Table_Args::get_site_defaults() );
		$id   = self::generate_id( $args );

		$table               = new Posts_Table( $id, $args );
		self::$tables[ $id ] = $table;

		return $table;
	}

	/**
	 * Fetch an existing table by ID.
	 *
	 * @param string $id The posts table ID.
	 * @return Posts_Table|false The posts table object.
	 */
	public static function fetch( $id ) {
		if ( empty( $id ) ) {
			return false;
		}

		$table = false;

		if ( isset( self::$tables[ $id ] ) ) {
			$table = self::$tables[ $id ];
		} elseif ( $table = Table_Cache::get_table( $id ) ) {
			self::$tables[ $id ] = $table;
		}

		return $table;
	}

	private static function generate_id( array $args ) {
		$id = 'dlp_' . substr( md5( serialize( $args ) ), 0, 16 ) . '_' . self::$current_id;
		self::$current_id++;

		return $id;
	}

}
