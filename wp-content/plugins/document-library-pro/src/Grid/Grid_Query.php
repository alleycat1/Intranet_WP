<?php
namespace Barn2\Plugin\Document_Library_Pro\Grid;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Table_Query;

/**
 * Handles the query for a Document_Grid;
 *
 * @package   Barn2\document-library-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Grid_Query extends Table_Query {

	private $max_num_pages = null;
	private $current_page  = null;

	/**
	 * Constructor.
	 *
	 * @param mixed $args
	 */
	public function __construct( $args ) { // phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
		parent::__construct( $args );
	}

	/**
	 * Filter the wp_posts columns for the query.
	 *
	 * @param mixed $fields
	 * @param mixed $query
	 * @return mixed
	 */
	public function filter_wp_posts_selected_columns( $fields, $query ) {
		global $wpdb;

		if ( "{$wpdb->posts}.*" !== $fields ) {
			return $fields;
		}

		$grid_content = array_keys(
			array_filter(
				$this->args->grid_content,
				function( $value ) {
					return $value === '1';
				}
			)
		);

		if ( $grid_content ) {
			$posts_columns = [
				'ID',
				'post_author',
				'post_date',
				'post_date_gmt',
				'post_title',
				'post_status',
				'comment_status',
				'ping_status',
				'post_password',
				'post_name',
				'to_ping',
				'pinged',
				'post_modified',
				'post_modified_gmt',
				'post_content_filtered',
				'post_parent',
				'guid',
				'menu_order',
				'post_type',
				'post_mime_type',
				'comment_count'
			];

			// Only select post_excerpt and post content if it's definitely needed
			if ( in_array( 'excerpt', $grid_content, true ) ) {
				$posts_columns[] = 'post_excerpt';
				$posts_columns[] = 'post_content';
			}

			$fields = sprintf( implode( ', ', array_map( [ self::class, 'array_map_prefix_column' ], $posts_columns ) ), $wpdb->posts );
		}

		return $fields;
	}

	/**
	 * Get the total number of pages.
	 *
	 * @return int|string
	 */
	public function get_max_num_pages() {
		if ( is_numeric( $this->max_num_pages ) ) {
			return $this->max_num_pages;
		}

		$this->max_num_pages = (int) ceil( $this->get_total_filtered_posts() / $this->args->rows_per_page );

		return $this->max_num_pages;
	}

	/**
	 * Get the current page.
	 *
	 * @return int|string
	 */
	public function get_current_page() {
		if ( is_numeric( $this->current_page ) ) {
			return $this->current_page;
		}

		$this->current_page = $this->args->offset >= $this->get_total_filtered_posts() ? -1 : intval( $this->args->offset / $this->args->rows_per_page ) + 1;

		return $this->current_page;
	}

	/**
	 * Is the query filtered by the user on the frontend.
	 */
	public function is_filtered_frontend() {
		return ! empty( $this->args->user_search_term );
	}

	/**
	 * Array map callback to prefix column.
	 *
	 * @param string $n
	 * @return string
	 */
	private static function array_map_prefix_column( $n ) {
		return '%1$s.' . $n;
	}
}
