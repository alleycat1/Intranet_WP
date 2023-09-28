<?php
namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Args,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Posts_Table,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Query,
	Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Options;

/**
 * Handles the caching for post tables.
 *
 * There are 2 types of caching used:
 *  - Table caching: this is used for lazy load tables where we need to initially create the table,
 * then later fetch the table by ID to fetch the actual posts.
 *  - Data caching: this is used to cache the data in a table, and is enabled or disabled using
 * the 'cache' option in the shortcode, or from the plugin settings.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Cache {

	const TABLE_CACHE_EXPIRY = DAY_IN_SECONDS;

	public $id;
	public $args;
	public $query;

	public function __construct( $id, Table_Args $args, Table_Query $query ) {
		$this->id    = $id;
		$this->args  = $args;
		$this->query = $query;
	}

	/**
	 * @deprecated 2.1.1 - Replaced by get_table().
	 */
	public static function load_from_cache( $id ) {
		return self::get_table( $id );
	}

	public static function get_table( $id ) {
		$table_cache = get_transient( $id );
		$table       = false;

		if ( $table_cache && isset( $table_cache['args'] ) ) {
			$table = new Posts_Table( $id, $table_cache['args'] );

			if ( isset( $table_cache['total_posts'] ) ) {
				$table->query->set_total_posts( $table_cache['total_posts'] );
			}
			if ( isset( $table_cache['total_filtered_posts'] ) ) {
				$table->query->set_total_filtered_posts( $table_cache['total_filtered_posts'] );
			}
		}

		return $table;
	}

	public function add_table() {
		if ( ! $this->table_caching_enabled() ) {
			return;
		}

		$table_cache = [ 'args' => $this->args->get_args() ];
		set_transient( $this->get_table_cache_key(), $table_cache, self::TABLE_CACHE_EXPIRY );
	}

	public function update_table( $update_totals = false ) {
		if ( ! $this->table_caching_enabled() ) {
			return;
		}

		if ( $table_cache = get_transient( $this->id ) ) {
			// Existing table found, so update it.
			$table_cache['args'] = $this->args->get_args();

			if ( $update_totals ) {
				$table_cache['total_posts']          = $this->query->get_total_posts();
				$table_cache['total_filtered_posts'] = $this->query->get_total_filtered_posts();
			}

			set_transient( $this->get_table_cache_key(), $table_cache, self::TABLE_CACHE_EXPIRY );
		} else {
			// No existing table in cache, so add it.
			$this->add_table();
		}
	}

	public function get_data() {
		if ( ! $this->data_caching_enabled() ) {
			return false;
		}

		if ( $data = get_transient( $this->get_data_cache_key() ) ) {
			return $data;
		}
		return false;
	}

	public function update_data( $data ) {
		if ( $this->data_caching_enabled() ) {
			// Limit maximum size of cacheable data to prevent storing very large transients
			if ( count( $data ) <= 1000 ) {
				$cache_expiration_length = Options::get_cache_expiration_length() * HOUR_IN_SECONDS;
				set_transient( $this->get_data_cache_key(), $data, apply_filters( 'document_library_pro_data_cache_expiry', $cache_expiration_length, $this ) );
			}
		} else {
			// Flush cache if not using
			$this->delete_data();
		}
	}

	public function delete_data() {
		delete_transient( $this->get_data_cache_key() );
	}

	private function table_caching_enabled() {
		return $this->args->lazy_load;
	}

	private function data_caching_enabled() {
		return apply_filters( 'document_library_pro_use_data_cache', $this->args->cache, $this );
	}

	private function get_table_cache_key() {
		return $this->id;
	}

	private function get_data_cache_key() {
		$key = $this->id . '_data';

		// For lazy load, cache each page of data based on offset
		if ( $this->args->lazy_load ) {
			$key .= '_' . $this->args->offset;
		}

		return $key;
	}

}
