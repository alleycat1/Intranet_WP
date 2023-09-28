<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Custom_Field' ) ) :

	final class WPSC_Custom_Field {

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
		 * Custom field type
		 *
		 * @var array
		 */
		public static $cf_types;

		/**
		 * Custom fields with slug indexing.
		 *
		 * @var array
		 */
		public static $custom_fields = array();

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

			// Set custom field types.
			add_action( 'init', array( __CLASS__, 'set_cf_types' ) );
		}

		/**
		 * Apply schema for this model
		 *
		 * @return void
		 */
		public static function apply_schema() {

			$schema = array(
				'id'                => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'name'              => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'extra_info'        => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'slug'              => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'field'             => array( // Set whether this field is ticket field, customer field or agentonly field.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'type'              => array( // Set custom field type.. e.g. TextFeld, Dropdown, or any default types.
					'has_ref'          => true,
					'ref_class'        => 'wpsc_cft',
					'has_multiple_val' => false,
				),
				'default_value'     => array(  // If set, this value will be used if not provided for ticket or customer field types.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => true,
				),
				'placeholder_text'  => array( // Placeholder on create ticket or edit ticket, customer or agentonly fields where applicable.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'char_limit'        => array( // Number of charaters to accept where applicable.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'date_display_as'   => array( // Used in Date Created, Date Updated and Date Closed to define whether you want to display date string or date difference on ticket list.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'date_range'        => array( // Used in "Date" custom field whether to accept past, future or any dates.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'date_format'       => array( // Used in "Date" custom field to display date in given format.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'start_range'       => array( // Used in "Date Datetime and Time" custom field for start date or start time.
					'has_ref'          => true,
					'ref_class'        => 'datetime',
					'has_multiple_val' => false,
				),
				'end_range'         => array( // Used in "Date Datetime and Time" custom field for end date or end time.
					'has_ref'          => true,
					'ref_class'        => 'datetime',
					'has_multiple_val' => false,
				),
				'time_format'       => array( // Used in "Time" custom field to display time in 12 or 24 hrs.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'is_personal_info'  => array( // Used for GDPR compatibility.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'load_order'        => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'is_auto_fill'      => array( // Used for fill value automatically.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'allow_ticket_form' => array( // Used for customer field are add or not in create ticket form.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'allow_my_profile'  => array( // Used for customer field are show/hide in my profile.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'tl_width'          => array( // Used in ticket list for column width.
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
			);
			self::$schema = apply_filters( 'wpsc_cf_schema', $schema );

			// Prevent modify.
			$prevent_modify       = array( 'id', 'slug' );
			self::$prevent_modify = apply_filters( 'wpsc_cf_prevent_modify', $prevent_modify );

			// Set custom fields.
			$custom_fields = self::find( array( 'items_per_page' => 0 ) )['results'];
			foreach ( $custom_fields as $cf ) {
				if ( ! class_exists( $cf->type ) ) {
					continue;
				}
				self::$custom_fields[ $cf->slug ] = $cf;
				self::$cache[ $cf->id ]           = $cf;
			}
		}

		/**
		 * Model constructor
		 *
		 * @param int $id - Optional. Data record id to retrive object for.
		 */
		public function __construct( $id = 0 ) {

			global $wpdb;

			$id = intval( $id );

			if ( $id && isset( self::$cache[ $id ] ) ) {
				$this->data = self::$cache[ $id ]->data;
			}

			if ( $id > 0 ) {
				$cf = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_custom_fields WHERE id = " . $id, ARRAY_A );
				if ( ! is_array( $cf ) ) {
					return;
				}

				foreach ( $cf as $key => $val ) {

					if ( $key == 'name' ) {

						$this->data[ $key ] = $val !== null ? WPSC_Translations::get( 'wpsc-cf-name-' . $cf['id'], stripslashes( $val ) ) : '';

					} elseif ( $key == 'extra_info' ) {

						$this->data[ $key ] = $val !== null ? WPSC_Translations::get( 'wpsc-cf-exi-' . $cf['id'], stripslashes( $val ) ) : '';

					} elseif ( $key == 'placeholder_text' ) {
						$this->data[ $key ] = $val !== null ? WPSC_Translations::get( 'wpsc-cf-pl-txt-' . $cf['id'], stripslashes( $val ) ) : '';

					} else {

						$this->data[ $key ] = $val !== null ? $val : '';
					}
				}
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

				$cf = self::insert( $data );
				if ( $cf ) {
					$this->data = $cf->data;
					$success    = true;
				} else {
					$success = false;
				}
			} else {

				unset( $data['id'] );
				$success = $wpdb->update(
					$wpdb->prefix . 'psmsc_custom_fields',
					$data,
					array( 'id' => $this->id )
				);

				WPSC_Translations::add( 'wpsc-cf-name-' . $this->id, $data['name'] );
				$data['extra_info'] ? WPSC_Translations::add( 'wpsc-cf-exi-' . $this->id, $data['extra_info'] ) : WPSC_Translations::remove( 'wpsc-cf-exi-' . $this->id );
				$data['placeholder_text'] ? WPSC_Translations::add( 'wpsc-cf-pl-txt-' . $this->id, $data['placeholder_text'] ) : WPSC_Translations::remove( 'wpsc-cf-pl-txt-' . $this->id );

				if ( $success ) {
					self::$custom_fields[ $this->slug ] = $this;
					self::$cache[ $this->id ]           = $this;
				}
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

			if ( ! isset( $data['load_order'] ) ) {
				$max_order          = $wpdb->get_var( "SELECT MAX(load_order) FROM {$wpdb->prefix}psmsc_custom_fields" );
				$data['load_order'] = intval( $max_order ) + 1;
			}

			$success = $wpdb->insert(
				$wpdb->prefix . 'psmsc_custom_fields',
				$data
			);

			if ( ! $success ) {
				return false;
			}

			$id = $wpdb->insert_id;

			// string translation.
			WPSC_Translations::add( 'wpsc-cf-name-' . $id, $data['name'] );
			if ( isset( $data['extra_info'] ) ) {
				WPSC_Translations::add( 'wpsc-cf-exi-' . $id, $data['extra_info'] );
			}
			if ( isset( $data['placeholder_text'] ) ) {
				WPSC_Translations::add( 'wpsc-cf-pl-txt-' . $id, $data['placeholder_text'] );
			}

			$cf = new WPSC_Custom_Field( $id );

			// Save slug if not already set. Reasone we have to do it here because slug is in $prevent_modify.
			if ( ! $cf->slug ) {
				$slug = 'cust_' . $cf->id;
				$wpdb->update(
					$wpdb->prefix . 'psmsc_custom_fields',
					array( 'slug' => $slug ),
					array( 'id' => $cf->id )
				);
				$cf->data['slug'] = $slug;
			}

			// Disable InnoDB strict mode if enabled to avoid limit of number of columns in tables.
			$strict_mode = $wpdb->get_var( "SHOW VARIABLES LIKE 'innodb_strict_mode'", 1 );
			if ( $strict_mode == 'ON' ) {
				$wpdb->query( "SET innodb_strict_mode = 'OFF'" );
			}

			// Add column to tickets table if field is either ticket or agentonly.
			if ( in_array( $cf->field, array( 'ticket', 'agentonly' ) ) && $cf->type::$slug != 'cf_html' ) {
				$success = $cf->create_ticket_tbl_col();
			}

			// Add column to customer table if field is customer.
			if ( $cf->field === 'customer' ) {
				$success = $cf->create_customer_tbl_col();
			}

			$success = apply_filters( 'wpsc_add_cf_table_column', $success, $cf );

			self::$custom_fields[ $cf->slug ] = $cf;
			self::$cache[ $cf->id ] = $cf;

			if ( ! $success ) {
				return false;
			}
			return $cf;
		}

		/**
		 * Delete record of given ID
		 *
		 * @param int $id - category id.
		 * @return boolean
		 */
		public static function destroy( $id ) {

			global $wpdb;

			$cf = new WPSC_Custom_Field( $id );
			if ( $cf->type::$is_default ) {
				return false;
			}

			if ( in_array( $cf->field, array( 'ticket', 'agentonly' ) ) && $cf->type::$slug != 'cf_html' ) {
				$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets DROP {$cf->slug}" );
			}

			if ( $cf->field === 'customer' ) {
				$wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_customers DROP {$cf->slug}" );
			}

			do_action( 'wpsc_custom_field_before_destroy', $cf );

			// Remove from DB.
			$success = $wpdb->delete(
				$wpdb->prefix . 'psmsc_custom_fields',
				array( 'id' => $cf->id )
			);
			if ( ! $success ) {
				return false;
			}

			// Remove options if any.
			if ( $cf->type::$has_options ) {
				$cf->delete_options();
			}

			unset( self::$custom_fields[ $cf->slug ] );
			unset( self::$cache[ $cf->id ] );

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-cf-name-' . $cf->id );
			WPSC_Translations::remove( 'wpsc-cf-exi-' . $cf->id );
			WPSC_Translations::remove( 'wpsc-cf-pl-txt-' . $cf->id );

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

					$this->data[ $var_name ] = $val !== null ? WPSC_Translations::get( 'wpsc-cf-name-' . $data['id'], stripslashes( $val ) ) : '';

				} elseif ( $var_name == 'extra_info' ) {

					$this->data[ $var_name ] = $val !== null ? WPSC_Translations::get( 'wpsc-cf-exi-' . $data['id'], stripslashes( $val ) ) : '';

				} elseif ( $var_name == 'placeholder_text' ) {

					$this->data[ $var_name ] = $val !== null ? WPSC_Translations::get( 'wpsc-cf-pl-txt-' . $data['id'], stripslashes( $val ) ) : '';

				} else {

					$this->data[ $var_name ] = $val !== null ? $val : '';
				}
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

			$sql   = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $wpdb->prefix . 'psmsc_custom_fields ';
			$where = self::get_where( $filter );

			$filter['items_per_page'] = isset( $filter['items_per_page'] ) ? $filter['items_per_page'] : 0;
			$filter['page_no']        = isset( $filter['page_no'] ) ? $filter['page_no'] : 1;
			$filter['orderby']        = isset( $filter['orderby'] ) ? $filter['orderby'] : 'load_order';
			$filter['order']          = isset( $filter['order'] ) ? $filter['order'] : 'ASC';

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
			foreach ( $response['results'] as $cf ) {

				$ob   = new WPSC_Custom_Field();
				$data = array();
				foreach ( $cf as $key => $val ) {
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

		/**
		 * Create column for custom field in ticket table
		 *
		 * @return boolean
		 */
		public function create_ticket_tbl_col() {

			global $wpdb;
			return $wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_tickets ADD {$this->slug} {$this->type::$data_type}" );
		}

		/**
		 * Create column for custom field in customer table
		 *
		 * @return boolean
		 */
		public function create_customer_tbl_col() {

			global $wpdb;
			return $wpdb->query( "ALTER TABLE {$wpdb->prefix}psmsc_customers ADD {$this->slug} {$this->type::$data_type}" );
		}

		/**
		 * Set custom field types
		 */
		public static function set_cf_types() {

			self::$cf_types = apply_filters( 'wpsc_cf_types', array() );
		}

		/**
		 * Reset options for the field
		 *
		 * @param array   $options - option ids.
		 * @param boolean $is_edit - set whether it is called from edit custom field.
		 * @return void
		 */
		public function set_options( $options, $is_edit = false ) {

			global $wpdb;

			if ( $is_edit ) {
				$wpdb->update(
					$wpdb->prefix . 'psmsc_options',
					array( 'custom_field' => 0 ),
					array( 'custom_field' => $this->id )
				);
			}

			if ( $this->type::$has_options ) {
				$count = 1;
				foreach ( $options as $option_id ) {
					$option = new WPSC_Option( $option_id );
					if ( ! $option->id ) {
						continue;
					}
					$option->custom_field = $this->id;
					$option->load_order   = $count++;
					$option->save();
				}
			}
		}

		/**
		 * Get options for this field
		 */
		public function get_options() {

			$filter = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'slug'    => 'custom_field',
						'compare' => '=',
						'val'     => $this->id,
					),
				),
			);
			return WPSC_Option::find( $filter )['results'];
		}

		/**
		 * Delete all options for this custom field
		 */
		public function delete_options() {

			global $wpdb;
			$wpdb->delete(
				$wpdb->prefix . 'psmsc_options',
				array( 'custom_field' => $this->id )
			);
		}

		/**
		 * Return CF object by slug
		 *
		 * @param string $slug - custom field slug.
		 * @return WPSC_Custom_Field
		 */
		public static function get_cf_by_slug( $slug ) {

			$cf = isset( self::$custom_fields[ $slug ] ) ? self::$custom_fields[ $slug ] : false;
			return $cf;
		}

		/**
		 * Set load order for ids given
		 *
		 * @param array $ids - custom field ids.
		 * @return void
		 */
		public static function set_load_order( $ids ) {

			$count = 1;
			foreach ( $ids as $id ) {
				$cf = new WPSC_Custom_Field( $id );
				if ( ! $cf ) {
					wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
				}
				$cf->load_order = $count++;
				$cf->save();
			}
		}
	}
endif;

WPSC_Custom_Field::init();
