<?php

namespace Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro;

use Barn2\Plugin\Document_Library_Pro\Posts_Table_Pro\Util\Util;
use WP_Query;

/**
 * Responsible for managing the posts table query, retrieving the list of posts (as an array of WP_Post objects), and finding the post totals.
 *
 * @package   Barn2\posts-table-pro
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class Table_Query {

	public  $args;
	private $posts                = null;
	private $total_posts          = null;
	private $total_filtered_posts = null;

	public function __construct( Table_Args $args ) {
		$this->args = $args;
	}

	private static function array_map_prefix_column( $n ) {
		return '%1$s.' . $n;
	}

	public function get_posts() {
		if ( is_array( $this->posts ) ) {
			return $this->posts;
		}

		// Build query args and retrieve the posts for our table
		$query = $this->run_posts_query( $this->build_posts_query() );
		$posts = ! empty( $query->posts ) ? $query->posts : [];
		$this->set_posts( $posts );

		return $this->posts;
	}

	public function set_posts( $posts ) {
		if ( ! is_array( $posts ) ) {
			$posts = null;
		}
		$this->posts = $posts;
	}

	private function run_posts_query( $query_args ) {
		// Add our query hooks before running the query.
		$this->add_query_hooks();

		do_action( 'document_library_pro_before_posts_query', $this );

		$query = new WP_Query( $query_args );

		// Remove the hooks to prevent them interfering with anything else.
		$this->remove_query_hooks();

		do_action( 'document_library_pro_after_posts_query', $this );

		return $query;
	}

	private function add_query_hooks() {
		// Query optimisations.
		if ( apply_filters( 'document_library_pro_optimize_table_query', true, $this->args ) ) {
			add_filter( 'posts_fields', [ $this, 'filter_wp_posts_selected_columns' ], 10, 2 );
		}
	}

	private function remove_query_hooks() {
		if ( apply_filters( 'document_library_pro_optimize_table_query', true, $this->args ) ) {
			remove_filter( 'posts_fields', [ $this, 'filter_wp_posts_selected_columns' ], 10 );
		}
	}

	private function build_posts_query() {
		$query_args = $this->add_user_search_args( $this->build_base_posts_query() );

		if ( $this->args->lazy_load ) {
			// Ensure rows per page doesn't exceed post limit
			$query_args['posts_per_page'] = $this->check_within_post_limit( $this->args->rows_per_page );
			$query_args['offset']         = $this->args->offset;
		} else {
			$query_args['posts_per_page'] = $this->args->post_limit;
		}

		return apply_filters( 'document_library_pro_query_args', $query_args, $this );
	}

	private function add_user_search_args( array $query_args ) {
		if ( ! empty( $this->args->search_filters ) ) {
			$query_args['tax_query'] = $this->build_search_filters_tax_query( $query_args['tax_query'] );
		}

		if ( ! empty( $this->args->user_search_term ) ) {
			$query_args['s'] = $this->args->user_search_term;
		}

		return $query_args;
	}

	private function build_search_filters_tax_query( $tax_query = [] ) {
		if ( ! is_array( $tax_query ) ) {
			$tax_query = [];
		}

		if ( empty( $this->args->search_filters ) ) {
			return $tax_query;
		}

		$search_filters_query = [];

		// Add tax queries for search filter drop-downs.
		foreach ( $this->args->search_filters as $taxonomy => $term ) {
			// Search filters always use term IDs
			$search_filters_query[] = $this->tax_query_item( $term, $taxonomy, 'IN', 'term_id' );
		}

		$search_filters_query = $this->maybe_add_relation( $search_filters_query );

		if ( empty( $tax_query ) ) {
			// If no tax query, set the whole tax query to the filters query.
			$tax_query = $search_filters_query;
		} elseif ( isset( $tax_query['relation'] ) && 'OR' === $tax_query['relation'] ) {
			// If tax query is an OR, nest it with the search filters query and join with AND.
			$tax_query = [
				$tax_query,
				$search_filters_query,
				'relation' => 'AND'
			];
		} else {
			// Otherwise append search filters and ensure it's AND.
			$tax_query[]           = $search_filters_query;
			$tax_query['relation'] = 'AND';
		}

		return $tax_query;
	}

	/**
	 * Generate an inner array for the 'tax_query' arg in WP_Query.
	 *
	 * @param string $terms    The list of terms as a string
	 * @param string $taxonomy The taxonomy name
	 * @param string $operator The SQL operator: IN, NOT IN, AND, etc
	 * @param string $field    Add tax query by `term_id` or `slug`. Leave empty to auto-detect correct type
	 * @return array A tax query sub-array
	 */
	private function tax_query_item( $terms, $taxonomy, $operator = 'IN', $field = '' ) {
		$and_relation = 'AND' === $operator;

		// comma-delimited list = OR, plus-delimited = AND
		if ( ! is_array( $terms ) ) {
			if ( false !== strpos( $terms, '+' ) ) {
				$terms        = explode( '+', $terms );
				$and_relation = true;
			} else {
				$terms = explode( ',', $terms );
			}
		}

		// Do we have slugs or IDs?
		if ( ! $field ) {
			$using_term_ids = count( $terms ) === count( array_filter( $terms, 'is_numeric' ) );
			$field          = $using_term_ids && ! $this->args->numeric_terms ? 'term_id' : 'slug';
		}

		// Strange bug when using operator => 'AND' in individual tax queries -
		// We need to separate these out into separate 'IN' arrays joined by and outer relation => 'AND'
		if ( $and_relation && count( $terms ) > 1 ) {
			$result = [ 'relation' => 'AND' ];

			foreach ( $terms as $term ) {
				$result[] = [
					'taxonomy' => $taxonomy,
					'terms'    => $term,
					'operator' => 'IN',
					'field'    => $field
				];
			}

			return $result;
		} else {
			return [
				'taxonomy' => $taxonomy,
				'terms'    => $terms,
				'operator' => $operator,
				'field'    => $field
			];
		}
	}

	private function maybe_add_relation( $query, $relation = 'AND' ) {
		if ( is_array( $query ) && count( $query ) > 1 && empty( $query['relation'] ) ) {
			$query['relation'] = $relation;
		}

		return $query;
	}

	private function build_base_posts_query() {
		$query_args = [
			'post_type'        => $this->args->post_type,
			'post_status'      => $this->args->status,
			'tax_query'        => $this->build_tax_query(),
			'meta_query'       => $this->build_meta_query(),
			'year'             => $this->args->year,
			'monthnum'         => $this->args->month,
			'day'              => $this->args->day,
			'author'           => $this->args->author, // ID or string of IDs, not an array
			'order'            => strtoupper( $this->args->sort_order ),
			'orderby'          => $this->get_order_by(),
			'no_found_rows'    => true,
			'suppress_filters' => false // Ensure WPML filters run on this query
		];

		if ( $this->args->include ) {
			$query_args['post__in']            = $this->args->include;
			$query_args['ignore_sticky_posts'] = true;
		} elseif ( $this->args->exclude ) {
			$query_args['post__not_in'] = $this->args->exclude;
		}

		if ( 'attachment' === $this->args->post_type && $this->args->post_mime_type ) {
			$query_args['post_mime_type'] = $this->args->post_mime_type;
		}

		// We only need to apply the search term for lazy load. For standard, the table will handle the search on load.
		if ( ! empty( $this->args->search_term ) && $this->args->lazy_load ) {
			$query_args['s'] = $this->args->search_term;
		}

		return $query_args;
	}

	private function build_tax_query() {
		$tax_query = [];

		// Category args.
		if ( ! empty( $this->args->category ) ) {
			$tax_query[] = $this->tax_query_item( $this->args->category, 'category' );
		}

		if ( ! empty( $this->args->exclude_category ) ) {
			$tax_query[] = $this->tax_query_item( $this->args->exclude_category, 'category', 'NOT IN' );
		}

		// Tags args.
		if ( ! empty( $this->args->tag ) ) {
			$tax_query[] = $this->tax_query_item( $this->args->tag, 'post_tag' );
		}

		// Custom term args.
		if ( ! empty( $this->args->term ) ) {
			$term_query   = [];
			$parsed_terms = Util::parse_term_arg( $this->args->term, true );

			foreach ( $parsed_terms as $taxonomy => $terms ) {
				// Ignore our internal '_relations' key which contains meta info about the term relationships.
				if ( '_relations' === $taxonomy ) {
					continue;
				}

				// Default to 'IN'.
				$operator = 'IN';

				if ( isset( $parsed_terms['_relations'][ $taxonomy ] ) ) {
					$operator = ( 'AND' === $parsed_terms['_relations'][ $taxonomy ] ) ? 'AND' : 'IN';
				}

				$term_query[] = $this->tax_query_item( $terms, $taxonomy, $operator );
			}

			$outer_relation = isset( $parsed_terms['_relations']['_outer'] ) ? $parsed_terms['_relations']['_outer'] : 'OR';
			$term_query     = $this->maybe_add_relation( $term_query, $outer_relation );
			$tax_query      = $this->maybe_nest_query( $tax_query, $term_query );
		}

		if ( ! empty( $this->args->exclude_term ) ) {
			$exclude_term_query = [];

			foreach ( Util::parse_term_arg( $this->args->exclude_term ) as $taxonomy => $terms ) {
				$exclude_term_query[] = $this->tax_query_item( $terms, $taxonomy, 'NOT IN' );
			}

			$exclude_term_query = $this->maybe_add_relation( $exclude_term_query, 'AND' );
			$tax_query          = $this->maybe_nest_query( $tax_query, $exclude_term_query );
		}

		return apply_filters( 'document_library_pro_tax_query', $this->maybe_add_relation( $tax_query ), $this );
	}

	private function maybe_nest_query( $main_query, $inner_query ) {
		// If main query is empty, set the whole query to the inner query. Otherwise append inner query.
		if ( empty( $main_query ) ) {
			$main_query = $inner_query;
		} else {
			$main_query[] = $inner_query;
		}

		return $main_query;
	}

	private function build_meta_query() {
		$meta_query = [];

		if ( $this->args->cf ) {
			$custom_field_query    = [];
			$custom_field_relation = 'OR';

			// comma-delimited = OR, plus-delimited = AND
			if ( false !== strpos( $this->args->cf, '+' ) ) {
				$field_array           = explode( '+', $this->args->cf );
				$custom_field_relation = 'AND';
			} else {
				$field_array = explode( ',', $this->args->cf );
			}

			// Custom fields are in format <field_key>:<field_value>
			foreach ( $field_array as $field ) {
				// Split custom field around the colon and check valid
				$field_split = explode( ':', $field, 2 );

				if ( count( $field_split ) === 2 ) {
					// We have a field key and value
					$field_key = $field_split[0];

					// Decode entities in field value (e.g. &amp;) to ensure the value passed to the meta query matches what was entered by the user.
					$field_value = html_entity_decode( $field_split[1], ENT_QUOTES | ENT_HTML401, 'UTF-8' );
					$compare     = '=';

					// If selecting based on an ACF field, the field value could be stored as an array, so we use a regex
					// comparison to check within a serialized array, in addition to a standard CF check.
					if ( Util::is_acf_active() ) {
						$compare     = 'REGEXP';
						$field_value = sprintf( '^%1$s$|s:%2$u:"%1$s";', preg_quote( $field_value, '/' ), strlen( $field_value ) );
					}

					$custom_field_query[] = [
						'key'     => $field_key,
						'value'   => $field_value,
						'compare' => $compare
					];
				} elseif ( count( $field_split ) === 1 ) {
					// Field key only, so do an 'exists' check instead
					$custom_field_query[] = [
						'key'     => $field_split[0],
						'compare' => 'EXISTS'
					];
				}
			}

			$meta_query['posts_table'] = $this->maybe_add_relation( $custom_field_query, $custom_field_relation );
		}

		return apply_filters( 'document_library_pro_meta_query', $meta_query, $this );
	}

	private function get_order_by() {
		$order_by = '';

		switch ( $this->args->sort_by ) {
			case 'id':
			case 'ID':
				$order_by = 'ID';
				break;
			case 'modified':
			case 'date_modified':
				$order_by = 'modified';
				break;
			case 'title':
			case 'name': // slug
			case 'type': // post type
			case 'date':
			case 'author':
			case 'menu_order':
			case 'rand':
			case 'comment_count':
			case 'none':
			case 'post__in':
				$order_by = $this->args->sort_by;
				break;
		}

		return $order_by;
	}

	private function check_within_post_limit( $count ) {
		return is_int( $this->args->post_limit ) && $this->args->post_limit > 0 ? min( $this->args->post_limit, $count ) : $count;
	}

	public function get_total_posts() {
		if ( is_numeric( $this->total_posts ) ) {
			return $this->total_posts;
		}

		$total = 0;

		if ( $this->args->search_term && $this->args->user_search_term ) {
			// If we have an original and user applied search term, we set the total to match the filtered total to avoid a mismatch.
			$total = $this->get_total_filtered_posts();
		} elseif ( -1 === $this->args->rows_per_page && is_array( $this->posts ) ) {
			// If showing all posts on a single page, the total is the count of $posts array.
			$total = count( $this->posts );
		} else {
			$total_query = new WP_Query( $this->build_post_totals_query() );
			$total       = $total_query->post_count;
		}

		$this->total_posts = $this->check_within_post_limit( $total );

		return $this->total_posts;
	}

	public function set_total_posts( $total_posts ) {
		$this->total_posts = $total_posts;
	}

	public function get_total_filtered_posts() {
		if ( is_numeric( $this->total_filtered_posts ) ) {
			// Return if we've already calculated the filtered total.
			return $this->total_filtered_posts;
		}

		// Calculate filtered total by running a new query.
		$filtered_total_query = $this->run_posts_query( $this->add_user_search_args( $this->build_post_totals_query() ) );
		$filtered_total       = $filtered_total_query->post_count;

		$this->total_filtered_posts = $this->check_within_post_limit( $filtered_total );

		return $this->total_filtered_posts;
	}

	public function set_total_filtered_posts( $total_filtered_posts ) {
		$this->total_filtered_posts = $total_filtered_posts;
	}

	private function build_post_totals_query() {
		$query_args                   = $this->build_base_posts_query();
		$query_args['offset']         = 0;
		$query_args['posts_per_page'] = -1;
		$query_args['fields']         = 'ids';

		return apply_filters( 'document_library_pro_query_args', $query_args, $this );
	}

	public function filter_wp_posts_selected_columns( $fields, $query ) {
		global $wpdb;

		if ( "{$wpdb->posts}.*" !== $fields ) {
			return $fields;
		}

		if ( array_diff( [ 'content', 'excerpt' ], $this->args->columns ) ) {
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

			// Only select post_content if it's definitely needed
			if ( in_array( 'content', $this->args->columns, true ) ) {
				$posts_columns[] = 'post_content';
			}

			// Only select post_excerpt if it's definitely needed
			if ( in_array( 'excerpt', $this->args->columns, true ) ) {
				$posts_columns[] = 'post_excerpt';
				// We need the content as well, in case we need to auto-generate the excerpt from the content
				$posts_columns[] = 'post_content';
			}

			$fields = sprintf( implode( ', ', array_map( [ self::class, 'array_map_prefix_column' ], $posts_columns ) ), $wpdb->posts );
		}

		return $fields;
	}

}
