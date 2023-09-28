<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Customer' ) ) :

	final class WPSC_Customer {

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

			// after customer profile update.
			add_action( 'profile_update', array( __CLASS__, 'customer_profile_update' ), 10, 2 );

			// after user registration.
			add_action( 'user_register', array( __CLASS__, 'register_customer' ), 10, 2 );
		}

		/**
		 * Apply schema for this model
		 *
		 * @return void
		 */
		public static function apply_schema() {

			$schema = array(
				'id'           => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'user'         => array(
					'has_ref'          => true,
					'ref_class'        => 'wp_user',
					'has_multiple_val' => false,
				),
				'ticket_count' => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
			);

			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

				if ( $cf->field == 'customer' ) {
					$schema[ $cf->slug ] = array(
						'has_ref'          => $cf->type::$has_ref,
						'ref_class'        => $cf->type::$ref_class,
						'has_multiple_val' => $cf->type::$has_multiple_val,
					);
				}
			}

			self::$schema = apply_filters( 'wpsc_customer_schema', $schema );

			// Prevent modify.
			$prevent_modify       = array( 'id' );
			self::$prevent_modify = apply_filters( 'wpsc_customer_prevent_modify', $prevent_modify );
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

				$customer = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_customers WHERE id = " . $id, ARRAY_A );
				if ( ! is_array( $customer ) ) {
					return;
				}

				foreach ( $customer as $key => $val ) {
					$this->data[ $key ] = $val !== null ? $val : '';
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

				$cr = self::insert( $data );
				if ( $cr ) {
					$this->data = $cr->data;
					$success    = true;
				} else {
					$success = false;
				}
			} else {

				unset( $data['id'] );
				$success = $wpdb->update(
					$wpdb->prefix . 'psmsc_customers',
					$data,
					array( 'id' => $this->data['id'] )
				);
			}
			$this->is_modified        = false;
			self::$cache[ $this->id ] = $this;
			return $success ? true : false;
		}

		/**
		 * Insert new record
		 *
		 * @param array $data - insert data.
		 * @return WPSC_Customer
		 */
		public static function insert( $data ) {

			global $wpdb;
			$user = self::get_by_email( $data['email'] );
			if ( $user->id ) {

				return $user;
			}

			$success = $wpdb->insert(
				$wpdb->prefix . 'psmsc_customers',
				$data
			);

			if ( ! $success ) {
				return false;
			}

			$customer = new WPSC_Customer( $wpdb->insert_id );
			return $customer;
		}

		/**
		 * Delete record of given ID
		 *
		 * @param WPSC_Customer $customer - customer object.
		 * @return boolean
		 */
		public static function destroy( $customer ) {

			global $wpdb;

			// Delete attachments of the customer.
			$success = $wpdb->update(
				$wpdb->prefix . 'psmsc_attachments',
				array( 'is_active' => '0' ),
				array( 'customer_id' => $customer->id )
			);

			$success = $wpdb->delete(
				$wpdb->prefix . 'psmsc_customers',
				array( 'id' => $customer->id )
			);
			if ( ! $success ) {
				return false;
			}

			unset( self::$cache[ $customer->id ] );
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

			$filter['items_per_page'] = isset( $filter['items_per_page'] ) ? $filter['items_per_page'] : 20;
			$filter['page_no']        = isset( $filter['page_no'] ) ? $filter['page_no'] : 1;
			$filter['orderby']        = isset( $filter['orderby'] ) ? $filter['orderby'] : 'name';
			$filter['order']          = isset( $filter['order'] ) ? $filter['order'] : 'ASC';

			$sql   = 'SELECT SQL_CALC_FOUND_ROWS c.* FROM ' . $wpdb->prefix . 'psmsc_customers c ';
			$join  = self::get_joins( $filter );
			$where = self::get_where( $filter );

			// Add table alice to orderby.
			$filter['orderby'] = 'c.' . $filter['orderby'];

			$order = WPSC_Functions::parse_order( $filter );
			$limit = WPSC_Functions::parse_limit( $filter );

			$group_by = 'GROUP BY c.id ';

			$sql = $sql . $join . $where . $group_by . $order . $limit;

			$results     = $wpdb->get_results( $sql, ARRAY_A );
			$total_items = $wpdb->get_var( 'SELECT FOUND_ROWS()' );

			$response = WPSC_Functions::parse_response( $results, $total_items, $filter );

			// Return array.
			if ( ! $is_object ) {
				return $response;
			}

			// create and return array of objects.
			$temp_results = array();
			foreach ( $response['results'] as $customer ) {

				$ob   = new WPSC_Customer();
				$data = array();
				foreach ( $customer as $key => $val ) {
					$data[ $key ] = $val;
				}
				$ob->set_data( $data );
				$temp_results[] = $ob;
			}
			$response['results'] = $temp_results;

			return $response;
		}

		/**
		 * Apply joins to the query if search is needed
		 *
		 * @param array $filter - user filter.
		 * @return array
		 */
		private static function get_joins( $filter ) {

			global $wpdb;

			$joins  = array();
			$search = WPSC_Functions::get_filter_search_str( $filter );
			if ( $search ) {
				$joins = array( 'LEFT JOIN ' . $wpdb->users . ' u ON c.user = u.ID' );
			}

			return $joins ? implode( ' ', $joins ) . ' ' : '';
		}

		/**
		 * Get where for find method
		 *
		 * @param array $filter - user filter.
		 * @return array
		 */
		private static function get_where( $filter ) {

			$where = array( '1=1' );

			// Load meta filters.
			$meta_query = isset( $filter['meta_query'] ) ? $filter['meta_query'] : array();
			if ( $meta_query ) {
				$where[] = self::parse_filters( $meta_query );
			}

			$search = WPSC_Functions::get_filter_search_str( $filter );
			if ( $search ) {

				$search_query = array();

				// Customers table cols.
				foreach ( self::$schema as $slug => $schema ) {

					if ( ! $schema['has_ref'] && $slug != 'id' ) {
						$search_query[] = 'CONVERT(c.' . $slug . ' USING utf8) LIKE \'%' . $search . '%\'';
					}
				}

				// Users table cols.
				$users_search = array(
					'CONVERT(u.user_login USING utf8) LIKE \'%' . $search . '%\'',
					'CONVERT(u.user_nicename USING utf8) LIKE \'%' . $search . '%\'',
					'CONVERT(u.user_email USING utf8) LIKE \'%' . $search . '%\'',
					'CONVERT(u.display_name USING utf8) LIKE \'%' . $search . '%\'',
				);

				$search_query = array_merge( $search_query, $users_search );
				$where[]      = implode( ' OR ', $search_query );
			}

			return 'WHERE (' . implode( ') AND (', $where ) . ') ';
		}

		/**
		 * Parse user filters for this model
		 *
		 * @param array $filters - user filters.
		 * @return string
		 */
		private static function parse_filters( $filters ) {

			// Invalid filter.
			if ( ! isset( $filters['relation'] ) || count( $filters ) < 2 ) {
				return '1=1';
			}

			$relation   = $filters['relation'];
			$filter_str = array();

			foreach ( $filters as $key => $filter ) {

				// Skip if current element is relation indicator.
				if ( $key === 'relation' ) {
					continue;
				}

				// Invalid filter if it is not an array.
				if ( ! is_array( $filter ) ) {
					return '1=1';
				}

				// Call recursively if there is multi-layer filter detected.
				if ( isset( $filter['relation'] ) ) {
					$filter_str[] = self::parse_user_filters( $filter );
					continue;
				}

				// Invalid filter if it does not contain slug, compare and val indexes.
				$slug    = isset( $filter['slug'] ) ? WPSC_Functions::sanitize_sql_key( $filter['slug'] ) : false;
				$compare = isset( $filter['compare'] ) ? $filter['compare'] : false;
				$val     = isset( $filter['val'] ) ? $filter['val'] : false;
				if ( ! $slug || ! $compare || $val === false ) {
					return '1=1';
				}

				// custom filter.
				if ( $slug === 'custom_query' ) {

					$filter_str[] = $val;

				} else {

					switch ( $compare ) {

						case '<':
						case '=':
						case '>':
						case '<=':
						case '>=':
							if ( self::$schema[ $slug ]['has_multiple_val'] ) {
								$filter_str[] = '1=1';
								break;
							}
							$filter_str[] = 'c.' . $slug . ' ' . $compare . ' \'' . esc_sql( $val ) . '\'';
							break;

						case 'BETWEEN':
							$filter_str[] = 'c.' . $slug . ' BETWEEN \'' . esc_sql( $val[0] ) . '\' AND \'' . esc_sql( $val[1] ) . '\'';
							break;

						case 'IN':
							if ( self::$schema[ $slug ]['has_multiple_val'] ) {

								$filter_str[] = 'c.' . $slug . ' RLIKE \'(^|[|])(' . implode( '|', esc_sql( $val ) ) . ')($|[|])\'';

							} else {

								$filter_str[] = 'c.' . $slug . ' IN ( \'' . implode( '\', \'', esc_sql( $val ) ) . '\' )';
							}
							break;

						case 'NOT IN':
							if ( self::$schema[ $slug ]['has_multiple_val'] ) {

								$filter_str[] = 'c.' . $slug . ' NOT RLIKE \'(^|[|])(' . implode( '|', esc_sql( $val ) ) . ')($|[|])\'';

							} else {

								$filter_str[] = 'c.' . $slug . ' NOT IN ( \'' . implode( '\', \'', esc_sql( $val ) ) . '\' )';
							}
							break;
					}
				}
			}

			return count( $filter_str ) > 1 ?
				'( ' . implode( ' ' . $relation . ' ', $filter_str ) . ' )' :
				$filter_str[0];
		}

		/**
		 * Load current class to reference classes
		 *
		 * @param array $classes - Associative array of class names indexed by its slug.
		 * @return array
		 */
		public static function load_ref_class( $classes ) {

			$classes['wpsc_customer'] = array(
				'class'    => __CLASS__,
				'save-key' => 'id',
			);
			return $classes;
		}

		/**
		 * Customer autocomplete callback
		 *
		 * @param string $term - search term.
		 * @return array
		 */
		public static function customer_autocomplete( $term ) {

			return array_map(
				fn( $customer)=>array(
					'id'    => $customer->id,
					'title' => sprintf(
						/* translators: %1$s: Name, %2$s: Email Address */
						esc_attr__( '%1$s (%2$s)', 'supportcandy' ),
						$customer->name,
						$customer->email
					),
				),
				self::customer_search( $term )
			);
		}

		/**
		 * Search customer and return array of customer objects.
		 *
		 * @param string $term - search string.
		 * @return array
		 */
		public static function customer_search( $term ) {

			// search in wp user table.
			$users = ( new WP_User_Query(
				array(
					'search'         => '*' . esc_attr( $term ) . '*',
					'search_columns' => array(
						'user_login',
						'user_nicename',
						'user_email',
						'display_name',
					),
					'number'         => 10,
				)
			) )->get_results();

			// import into customer table if not already exists.
			foreach ( $users as $user ) {
				self::get_by_user_id( $user->ID );
			}

			// finally search customer table.
			$customers = self::find(
				array(
					'search'         => $term,
					'items_per_page' => 10,
				)
			)['results'];

			// return results.
			return array_map( fn( $customer) => $customer, $customers );
		}

		/**
		 * Return customer by an email address
		 *
		 * @param string $email - email address.
		 * @return WPSC_Customer
		 */
		public static function get_by_email( $email ) {

			if ( ! $email ) {
				return new WPSC_Customer();
			}

			$response = self::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'email',
							'compare' => '=',
							'val'     => $email,
						),
					),
				)
			);

			return $response['results'] ? $response['results'][0] : new WPSC_Customer();
		}

		/**
		 * Return customer object by wp user id
		 *
		 * @param int $id - wp user id.
		 * @return boolean
		 */
		public static function get_by_user_id( $id ) {

			if ( ! $id ) {
				return new WPSC_Customer();
			}

			$response = self::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'user',
							'compare' => '=',
							'val'     => $id,
						),
					),
				)
			);

			if ( $response['results'] ) {

				return $response['results'][0];

			} else {

				$user = get_user_by( 'id', $id );
				if ( ! $user ) {
					return new WPSC_Customer();
				}

				return self::insert(
					array(
						'user'  => $user->ID,
						'name'  => $user->display_name,
						'email' => $user->user_email,
					)
				);
			}
		}

		/**
		 * Update customer record such as name, email address, user type, etc. when wp user record is updated.
		 *
		 * @param int     $user_id - wp user id.
		 * @param WP_User $old_user_data - user object before change.
		 * @return void
		 */
		public static function customer_profile_update( $user_id, $old_user_data ) {

			global $wpdb;

			$user = get_userdata( $user_id );
			$customer = self::get_by_user_id( $user_id );
			if ( $customer ) {

				$customer->name = $user->display_name;

				// update user_type from "guest" to "registered" as well as customer id for tickets which was created as guest with email address that is now updated.
				if ( $customer->email != $user->user_email ) {

					// get the guest customer object of newly added email. Update tickets (if any).
					$guest_customer = self::get_by_email( $user->user_email );
					if ( $guest_customer->id ) {
						$wpdb->update(
							$wpdb->prefix . 'psmsc_tickets',
							array(
								'customer'  => $customer->id,
								'user_type' => 'registered',
							),
							array(
								'customer' => $guest_customer->id,
							)
						);

						// destroy guest customer.
						self::destroy( $guest_customer );
					}

					// update customer email.
					$customer->email = $user->user_email;
				}

				$customer->save();
			}
		}

		/**
		 * Add/update customer record after wp user registration
		 *
		 * @param int   $user_id - wp user id.
		 * @param array $user_data - registered user data.
		 * @return void
		 */
		public static function register_customer( $user_id, $user_data ) {

			$user = get_user_by( 'ID', $user_id );
			$name = isset( $user->display_name ) ? $user->display_name : $user_data['user_login'];

			$customer = self::get_by_email( $user_data['user_email'] );

			if ( ! $customer->id ) {

				// Create customer record.
				$new_customer = array(
					'user'  => $user_id,
					'name'  => $name,
					'email' => $user_data['user_email'],
				);
				$customer     = self::insert( $new_customer );

			} else {

				// update customer record.
				$customer->user = $user_id;
				$customer->name = $name;
				$customer->save();
			}
		}

		/**
		 * Get object for anonymous customer
		 *
		 * @return WPSC_Customer
		 */
		public static function get_anonimus_customer() {

			$data   = array(
				'id'    => '0',
				'name'  => 'Anonymous',
				'email' => 'anonymous@anonymous.anonymous',
			);
			$object = new WPSC_Customer();
			$object->set_data( $data );
			return $object;
		}

		/**
		 * Update total ticket count for the customer in database
		 *
		 * @return void
		 */
		public function update_ticket_count() {

			global $wpdb;
			$count = $wpdb->get_var( "SELECT count(id) from {$wpdb->prefix}psmsc_tickets WHERE is_active=1 AND customer=" . $this->id );
			$this->ticket_count = $count;
			$this->save();
		}

		/**
		 * Check whether customer is an agent or not
		 *
		 * @return boolean
		 */
		public function is_agent() {

			$agent = WPSC_Agent::get_by_customer( $this );
			return $agent->id ? true : false;
		}
	}
endif;

WPSC_Customer::init();
