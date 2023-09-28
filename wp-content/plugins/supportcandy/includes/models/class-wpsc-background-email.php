<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Background_Email' ) ) :

	final class WPSC_Background_Email {

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
			add_action( 'init', array( __CLASS__, 'apply_schema' ), 1 );

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
				'id'          => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'from_name'   => array( // Used for email from name.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'from_email'  => array( // Used for email from email.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'reply_to'    => array( // Used for email reply to.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'subject'     => array( // Used for mail subject.
					'has_ref'          => true,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'body'        => array(  // Used for email body.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'to_email'    => array( // Used for TO emails.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => true,
				),
				'cc_email'    => array( // Used for CC emails.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => true,
				),
				'bcc_email'   => array( // Used for BCC emails.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => true,
				),
				'attachments' => array(
					'has_ref'          => true,
					'ref_class'        => 'wpsc_attachment',
					'has_multiple_val' => true,
				),
				'attempt'     => array( // Used for check how many time this mail is send after mail sending fail.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'priority'    => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
			);
			self::$schema = apply_filters( 'wpsc_background_email_schema', $schema );

			// Prevent modify.
			$prevent_modify       = array( 'id' );
			self::$prevent_modify = apply_filters( 'wpsc_background_email_prevent_modify', $prevent_modify );
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

				$email = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_email_notifications WHERE id = " . $id, ARRAY_A );
				if ( ! is_array( $email ) ) {
					return;
				}

				foreach ( $email as $key => $val ) {
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

			if ( in_array( $var_name, self::$prevent_modify ) ) {
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

			if ( isset( $this->data[ $var_name ] ) && $this->data[ $var_name ] == $data_val ) {
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

				$email = self::insert( $data );
				if ( $email ) {
					$this->data = $email->data;
					$success    = true;
				} else {
					$success = false;
				}
			} else {

				unset( $data['id'] );
				$success = $wpdb->update(
					$wpdb->prefix . 'psmsc_email_notifications',
					$data,
					array( 'id' => $this->data['id'] )
				);
			}
			$this->is_modified = false;
			return $success ? true : false;
		}

		/**
		 * Insert new record
		 *
		 * @param array $data - insert data.
		 * @return WPSC_Custom_Field
		 */
		public static function insert( $data ) {

			global $wpdb;

			$success = $wpdb->insert(
				$wpdb->prefix . 'psmsc_email_notifications',
				$data
			);

			if ( ! $success ) {
				return false;
			}

			$email = new WPSC_Background_Email( $wpdb->insert_id );
			return $email;
		}

		/**
		 * Delete record of given ID
		 *
		 * @param int $id - email id.
		 * @return boolean
		 */
		public static function destroy( $id ) {

			global $wpdb;

			$success = $wpdb->delete(
				$wpdb->prefix . 'psmsc_email_notifications',
				array( 'id' => $id )
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

			$sql = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $wpdb->prefix . 'psmsc_email_notifications ';

			$filter['items_per_page'] = isset( $filter['items_per_page'] ) ? $filter['items_per_page'] : 5;
			$filter['orderby']        = isset( $filter['orderby'] ) ? $filter['orderby'] : 'id';
			$filter['order']          = isset( $filter['order'] ) ? $filter['order'] : 'ASC';

			$where = self::get_where( $filter );
			$order = WPSC_Functions::parse_order( $filter );
			$limit = WPSC_Functions::parse_limit( $filter );

			$sql = $sql . $where . $order . $limit;

			$results     = $wpdb->get_results( $sql, ARRAY_A );
			$total_items = $wpdb->get_var( 'SELECT FOUND_ROWS()' );

			$response = WPSC_Functions::parse_response( $results, $total_items, $filter );

			// Return array.
			if ( ! $is_object ) {
				return $response;
			}

			// create and return array of objects.
			$temp_results = array();
			foreach ( $response['results'] as $email ) {

				$ob   = new WPSC_Background_Email();
				$data = array();
				foreach ( $email as $key => $val ) {
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
			$meta_query = apply_filters( 'wpsc_cf_meta_query', $meta_query, $filter );
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

			$classes['wpsc_cf'] = array(
				'class'    => __CLASS__,
				'save-key' => 'id',
			);
			return $classes;
		}
	}
endif;

WPSC_Background_Email::init();
