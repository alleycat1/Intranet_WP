<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Priority' ) ) :

	final class WPSC_Priority {

		/**
		 * Object data in key => val pair.
		 *
		 * @var array
		 */
		private $data = array();

		/**
		 * Set whether or not current object properties modified
		 *
		 * @var boolean
		 */
		private $is_modified = false;

		/**
		 * Schema for this model
		 *
		 * @var array
		 */
		public static $schema;

		/**
		 * Prevent fields to modify
		 *
		 * @var array
		 */
		public static $prevent_modify;

		/**
		 * DB object caching
		 *
		 * @var array
		 */
		private static $cache = array();

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Apply schema for this model.
			add_action( 'init', array( __CLASS__, 'apply_schema' ), 2 );

			// Get object of this class.
			add_filter( 'wpsc_load_ref_classes', array( __CLASS__, 'load_ref_class' ) );
		}

		/**
		 * Apply schema for this model
		 *
		 * @return void
		 */
		public static function apply_schema() {

			$schema       = array(
				'id'         => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'name'       => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'color'      => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'bg_color'   => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'load_order' => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
			);
			self::$schema = apply_filters( 'wpsc_priority_schema', $schema );

			// Prevent modify.
			$prevent_modify       = array( 'id' );
			self::$prevent_modify = apply_filters( 'wpsc_priority_prevent_modify', $prevent_modify );
		}

		/**
		 * Model constructor
		 *
		 * @param int $id - Optional. Data record id to retrive object for.
		 */
		public function __construct( $id = 0 ) {

			global $wpdb;

			$id = intval( $id );

			if ( isset( self::$cache[ $id ] ) ) {
				$this->data = self::$cache[ $id ]->data;
				return;
			}

			if ( $id > 0 ) {

				$priority = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_priorities WHERE id = " . $id, ARRAY_A );
				if ( ! is_array( $priority ) ) {
					return;
				}

				foreach ( $priority as $key => $val ) {

					if ( $key == 'name' ) {

						$this->data[ $key ] = $val !== null ? WPSC_Translations::get( 'wpsc-priority-' . $priority['id'], stripslashes( $val ) ) : '';

					} else {

						$this->data[ $key ] = $val !== null ? $val : '';
					}
				}

				self::$cache[ $id ] = $this;
			}
		}

		/**
		 * Convert object into an array
		 *
		 * @return array
		 */
		public function to_array() {

			return $this->data;
		}

		/**
		 * Magic get function to use with object arrow function
		 *
		 * @param string $var_name - variable name.
		 * @return mixed
		 */
		public function __get( $var_name ) {

			if ( ! isset( $this->data[ $var_name ] ) ||
				$this->data[ $var_name ] == null ||
				$this->data[ $var_name ] == ''
			) {
				return self::$schema[ $var_name ]['has_multiple_val'] ? array() : '';
			}

			if ( self::$schema[ $var_name ]['has_multiple_val'] ) {

				$response = array();
				$values   = $this->data[ $var_name ] ? explode( '|', $this->data[ $var_name ] ) : array();
				foreach ( $values as $val ) {
					$response[] = self::$schema[ $var_name ]['has_ref'] ?
									WPSC_Functions::get_object( self::$schema[ $var_name ]['ref_class'], $val ) :
									$val;
				}
				return $response;

			} else {

				return self::$schema[ $var_name ]['has_ref'] && $this->data[ $var_name ] ?
					WPSC_Functions::get_object( self::$schema[ $var_name ]['ref_class'], $this->data[ $var_name ] ) :
					$this->data[ $var_name ];
			}
		}

		/**
		 * Magic function to use setting object field with arrow function
		 *
		 * @param string $var_name - (Required) property slug.
		 * @param mixed  $value - (Required) value to set for a property.
		 * @return void
		 */
		public function __set( $var_name, $value ) {

			if (
				! isset( $this->data[ $var_name ] ) ||
				in_array( $var_name, self::$prevent_modify )
			) {
				return;
			}

			$data_val = '';
			if ( self::$schema[ $var_name ]['has_multiple_val'] ) {

				$data_vals = array_map(
					fn( $val ) => is_object( $val ) ? WPSC_Functions::set_object( self::$schema[ $var_name ]['ref_class'], $val ) : $val,
					$value
				);

				$data_val = $data_vals ? implode( '|', $data_vals ) : '';

			} else {

				$data_val = is_object( $value ) ? WPSC_Functions::set_object( self::$schema[ $var_name ]['ref_class'], $value ) : $value;
			}

			if ( $this->data[ $var_name ] == $data_val ) {
				return;
			}

			$this->data[ $var_name ] = $data_val;
			$this->is_modified       = true;
		}

		/**
		 * Save changes made
		 *
		 * @return boolean
		 */
		public function save() {

			global $wpdb;

			if ( ! $this->is_modified ) {
				return true;
			}

			$data    = $this->data;
			$success = true;

			if ( ! isset( $data['id'] ) ) {

				$pr = self::insert( $data );
				if ( $pr ) {
					$this->data = $pr->data;
					$success    = true;
				} else {
					$success = false;
				}
			} else {

				unset( $data['id'] );
				$success = $wpdb->update(
					$wpdb->prefix . 'psmsc_priorities',
					$data,
					array( 'id' => $this->data['id'] )
				);

				// Set string translation.
				WPSC_Translations::add( 'wpsc-priority-' . $this->data['id'], $this->data['name'] );
			}
			$this->is_modified        = false;
			self::$cache[ $this->id ] = $this;
			return $success ? true : false;
		}

		/**
		 * Insert new record
		 *
		 * @param array $data - insert data.
		 * @return WPSC_Priority
		 */
		public static function insert( $data ) {

			global $wpdb;

			if ( ! isset( $data['load_order'] ) ) {
				$max_order          = $wpdb->get_var( "SELECT MAX(load_order) FROM {$wpdb->prefix}psmsc_priorities" );
				$data['load_order'] = intval( $max_order ) + 1;
			}

			$success = $wpdb->insert(
				$wpdb->prefix . 'psmsc_priorities',
				$data
			);

			if ( ! $success ) {
				return false;
			}

			$id = $wpdb->insert_id;

			// string translation.
			WPSC_Translations::add( 'wpsc-priority-' . $id, $data['name'] );

			$priority = new WPSC_Priority( $id );

			return $priority;
		}

		/**
		 * Delete record of given ID
		 *
		 * @param WPSC_Priority $priority - priority object.
		 * @return boolean
		 */
		public static function destroy( $priority ) {

			global $wpdb;

			$success = $wpdb->delete(
				$wpdb->prefix . 'psmsc_priorities',
				array( 'id' => $priority->id )
			);
			if ( ! $success ) {
				return false;
			}

			unset( self::$cache[ $priority->id ] );

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-priority-' . $priority->id );
			return true;
		}

		/**
		 * Set data to create new object using direct data. Used in find method
		 *
		 * @param array $data - data to set for object.
		 * @return void
		 */
		private function set_data( $data ) {

			foreach ( $data as $var_name => $val ) {

				if ( $var_name == 'name' ) {

					$this->data[ $var_name ] = $val !== null ? WPSC_Translations::get( 'wpsc-priority-' . $data['id'], stripslashes( $val ) ) : '';

				} else {

					$this->data[ $var_name ] = $val !== null ? $val : '';
				}
			}
			self::$cache[ $this->id ] = $this;
		}

		/**
		 * Find records based on given filters
		 *
		 * @param array   $filter - array containing array items like search, where, orderby, order, page_no, items_per_page, etc.
		 * @param boolean $is_object - return data as array or object. Default object.
		 * @return mixed
		 */
		public static function find( $filter = array(), $is_object = true ) {

			global $wpdb;

			$sql   = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $wpdb->prefix . 'psmsc_priorities ';
			$where = self::get_where( $filter );

			$filter['items_per_page'] = isset( $filter['items_per_page'] ) ? $filter['items_per_page'] : 0;
			$filter['page_no']        = isset( $filter['page_no'] ) ? $filter['page_no'] : 0;
			$filter['orderby']        = isset( $filter['orderby'] ) ? $filter['orderby'] : 'load_order';
			$filter['order']          = isset( $filter['order'] ) ? $filter['order'] : 'ASC';

			$order = WPSC_Functions::parse_order( $filter );

			$sql = $sql . $where . $order;

			$results     = $wpdb->get_results( $sql, ARRAY_A );
			$total_items = $wpdb->get_var( 'SELECT FOUND_ROWS()' );

			$response = WPSC_Functions::parse_response( $results, $total_items, $filter );

			// Return array.
			if ( ! $is_object ) {
				return $response;
			}

			// create and return array of objects.
			$temp_results = array();
			foreach ( $response['results'] as $priority ) {

				$ob   = new WPSC_Priority();
				$data = array();
				foreach ( $priority as $key => $val ) {
					$data[ $key ] = $val;
				}
				$ob->set_data( $data );
				$temp_results[] = $ob;
			}
			$response['results'] = $temp_results;

			return $response;
		}

		/**
		 * Get where for find method
		 *
		 * @param array $filter - user filter.
		 * @return array
		 */
		private static function get_where( $filter ) {

			$where = '';

			// Set user defined filters.
			$meta_query = isset( $filter['meta_query'] ) && $filter['meta_query'] ? $filter['meta_query'] : array();
			$meta_query = apply_filters( 'wpsc_priority_meta_query', $meta_query, $filter );
			if ( $meta_query ) {

				$meta_query = WPSC_Functions::parse_user_filters( __CLASS__, $meta_query );
				$where      = $meta_query . ' ';
			}

			return $where ? 'WHERE ' . $where : '';
		}

		/**
		 * Load current class to reference classes
		 *
		 * @param array $classes - Associative array of class names indexed by its slug.
		 * @return array
		 */
		public static function load_ref_class( $classes ) {

			$classes['wpsc_priority'] = array(
				'class'    => __CLASS__,
				'save-key' => 'id',
			);
			return $classes;
		}
	}
endif;

WPSC_Priority::init();
