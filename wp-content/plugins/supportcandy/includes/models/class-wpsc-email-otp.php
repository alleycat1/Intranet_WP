<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Email_OTP' ) ) :

	final class WPSC_Email_OTP {

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
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Apply schema for this model.
			add_action( 'init', array( __CLASS__, 'apply_schema' ), 2 );

			// Get object of this class.
			add_filter( 'wpsc_load_ref_classes', array( __CLASS__, 'load_ref_class' ) );

			// garbage collection.
			add_action( 'wpsc_cron_daily', array( __CLASS__, 'garbage_collector' ) );
		}

		/**
		 * Apply schema for this model
		 *
		 * @return void
		 */
		public static function apply_schema() {

			$schema       = array(
				'id'          => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'email'       => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'otp'         => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'date_expiry' => array(
					'has_ref'          => true,
					'ref_class'        => 'datetime',
					'has_multiple_val' => false,
				),
				'data'        => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
			);
			self::$schema = apply_filters( 'wpsc_email_otp_schema', $schema );

			// Prevent modify.
			$prevent_modify       = array( 'id' );
			self::$prevent_modify = apply_filters( 'wpsc_email_otp_prevent_modify', $prevent_modify );
		}

		/**
		 * Model constructor
		 *
		 * @param int $id - Optional. Data record id to retrive object for.
		 */
		public function __construct( $id = 0 ) {

			global $wpdb;

			$id = intval( $id );

			if ( $id > 0 ) {

				$otp = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_email_otp WHERE id = " . $id, ARRAY_A );
				if ( ! is_array( $otp ) ) {
					return;
				}

				foreach ( $otp as $key => $val ) {
					$this->data[ $key ] = $val !== null ? $val : '';
				}
			}
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

			return self::$schema[ $var_name ]['has_ref'] && $this->data[ $var_name ] ?
				WPSC_Functions::get_object( self::$schema[ $var_name ]['ref_class'], $this->data[ $var_name ] ) :
				$this->data[ $var_name ];
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

			$data_val = is_object( $value ) ?
				WPSC_Functions::set_object( self::$schema[ $var_name ]['ref_class'], $value ) :
				$value;

			if ( $this->data[ $var_name ] == $data_val ) {
				return;
			}

			$this->data[ $var_name ] = $data_val;
			$this->is_modified       = true;
		}

		/**
		 * Save changes made. If id is not present that means we have to create new otp.
		 *
		 * @return boolean
		 */
		public function save() {

			global $wpdb;

			if ( ! $this->is_modified ) {
				return true;
			}

			$data = $this->data;

			unset( $data['id'] );
			$success = $wpdb->update(
				$wpdb->prefix . 'psmsc_email_otp',
				$data,
				array( 'id' => $this->data['id'] )
			);

			$this->is_modified = false;
			return $success ? true : false;
		}

		/**
		 * Insert new OTP
		 *
		 * @param array $data - insert data.
		 * @return WPSC_Email_OTP
		 */
		public static function insert( $data ) {

			global $wpdb;

			$wpdb->delete(
				$wpdb->prefix . 'psmsc_email_otp',
				array( 'email' => $data['email'] )
			);

			$data['otp'] = wp_rand( 100000, 999999 );
			$success     = $wpdb->insert(
				$wpdb->prefix . 'psmsc_email_otp',
				$data
			);

			if ( ! $success ) {
				return false;
			}

			return new WPSC_Email_OTP( $wpdb->insert_id );
		}

		/**
		 * Delete record of given ID
		 *
		 * @param WPSC_Email_OTP $otp - OTP object.
		 * @return boolean
		 */
		public static function destroy( $otp ) {

			global $wpdb;

			$success = $wpdb->delete(
				$wpdb->prefix . 'psmsc_email_otp',
				array( 'id' => $otp->id )
			);
			if ( ! $success ) {
				return false;
			}
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
				$this->data[ $var_name ] = $val !== null ? $val : '';
			}
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

			$sql   = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $wpdb->prefix . 'psmsc_email_otp ';
			$where = self::get_where( $filter );

			$filter['items_per_page'] = isset( $filter['items_per_page'] ) ? $filter['items_per_page'] : 0;
			$filter['page_no']        = isset( $filter['page_no'] ) ? $filter['page_no'] : 0;
			$filter['orderby']        = isset( $filter['orderby'] ) ? $filter['orderby'] : 'id';
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
			foreach ( $response['results'] as $otp ) {

				$ob   = new WPSC_Email_OTP();
				$data = array();
				foreach ( $otp as $key => $val ) {
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

			$classes['wpsc_email_otp'] = array(
				'class'    => __CLASS__,
				'save-key' => 'id',
			);
			return $classes;
		}

		/**
		 * Verify OTP for the object
		 *
		 * @param int $verification_otp - OTP to verify.
		 * @return boolean
		 */
		public function is_valid( $verification_otp ) {

			$now = new DateTime();
			return $this->otp == $verification_otp && $this->date_expiry >= $now ? true : false;
		}

		/**
		 * Garbage collector
		 *
		 * @return void
		 */
		public static function garbage_collector() {

			global $wpdb;

			$now = ( new DateTime() )->format( 'Y-m-d H:i:s' );

			$sql = "DELETE FROM {$wpdb->prefix}psmsc_email_otp WHERE date_expiry <= '$now'";
			$wpdb->query( $sql );
		}
	}
endif;

WPSC_Email_OTP::init();
