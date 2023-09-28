<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Ticket' ) ) :

	final class WPSC_Ticket {

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
		}

		/**
		 * Apply schema for this model
		 *
		 * @return void
		 */
		public static function apply_schema() {

			// start with schema item not present as custom field type.
			$schema = array(
				'is_active'   => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'auth_code'   => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'live_agents' => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
			);

			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

				if ( in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ) {
					$schema[ $cf->slug ] = array(
						'has_ref'          => $cf->type::$has_ref,
						'ref_class'        => $cf->type::$ref_class,
						'has_multiple_val' => $cf->type::$has_multiple_val,
					);
				}
			}

			self::$schema = apply_filters( 'wpsc_ticket_schema', $schema );

			// Prevent modify.
			$prevent_modify       = array( 'id', 'description', 'agent_created', 'date_created' );
			self::$prevent_modify = apply_filters( 'wpsc_ticket_prevent_modify', $prevent_modify );
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

				$ticket = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_tickets WHERE id = " . $id, ARRAY_A );
				if ( ! is_array( $ticket ) ) {
					return;
				}

				foreach ( $ticket as $key => $val ) {
					$this->data[ $key ] = $val !== null ? $val : '';
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

				if ( $var_name == 'customer_name' ) {
					return $this->customer->name;
				}
				if ( $var_name == 'customer_email' ) {
					return $this->customer->email_address;
				}
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

				if ( ( self::$schema[ $var_name ]['has_ref'] && $this->data[ $var_name ] ) || ( self::$schema[ $var_name ]['has_ref'] && self::$schema[ $var_name ]['ref_class'] == 'wpsc_customer' ) ) {
					return WPSC_Functions::get_object( self::$schema[ $var_name ]['ref_class'], $this->data[ $var_name ] );
				} else {
					return $this->data[ $var_name ];
				}
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

				$tic = self::insert( $data );
				if ( $tic ) {
					$this->data = $tic->data;
					$success    = true;
				} else {
					$success = false;
				}
			} else {

				unset( $data['id'] );
				$success = $wpdb->update(
					$wpdb->prefix . 'psmsc_tickets',
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

			$ad_settings = get_option( 'wpsc-ms-advanced-settings' );
			if ( ! isset( $data['id'] ) && $ad_settings['ticket-id-format'] === 'random' ) {

				$range = self::get_ticket_id_range();
				do {
					$id     = wp_rand( $range['start_range'], $range['end_range'] );
					$sql    = "select id from {$wpdb->prefix}psmsc_tickets where id=" . $id;
					$result = $wpdb->get_var( $sql );
				} while ( $result );
				$data['id'] = $id;
			}

			// Insert ticket to DB.
			$success = $wpdb->insert(
				$wpdb->prefix . 'psmsc_tickets',
				$data
			);

			if ( ! $success ) {
				return false;
			}
			return new WPSC_Ticket( $wpdb->insert_id );
		}

		/**
		 * Delete record from database
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return boolean
		 */
		public static function destroy( $ticket ) {

			global $wpdb;

			// Delete attachments of the ticket.
			$success = $wpdb->update(
				$wpdb->prefix . 'psmsc_attachments',
				array( 'is_active' => '0' ),
				array( 'ticket_id' => $ticket->id )
			);

			// Delete threads for the ticket.
			$success = $wpdb->delete(
				$wpdb->prefix . 'psmsc_threads',
				array( 'ticket' => $ticket->id )
			);
			if ( ! $success ) {
				return false;
			}

			// Finally delete ticket.
			$success = $wpdb->delete(
				$wpdb->prefix . 'psmsc_tickets',
				array( 'id' => $ticket->id )
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

			$filter['items_per_page'] = isset( $filter['items_per_page'] ) ? $filter['items_per_page'] : 20;
			$filter['page_no']        = isset( $filter['page_no'] ) ? $filter['page_no'] : 1;
			$filter['orderby']        = isset( $filter['orderby'] ) ? $filter['orderby'] : 'date_updated';
			$filter['orderby_slug']   = $filter['orderby'];

			// orderby.
			$cf = WPSC_Custom_Field::get_cf_by_slug( $filter['orderby'] );
			if ( ! $cf ) {
				$cf = WPSC_Custom_Field::get_cf_by_slug( 'date_updated' );
			}
			$filter['orderby'] = $cf->type::get_orderby_string( $cf );

			$filter['order']     = isset( $filter['order'] ) ? $filter['order'] : 'DESC';
			$filter['is_active'] = isset( $filter['is_active'] ) ? $filter['is_active'] : 1;

			$sql      = 'SELECT SQL_CALC_FOUND_ROWS t.* FROM ' . $wpdb->prefix . 'psmsc_tickets t ';
			$joins    = self::get_joins( $filter );
			$where    = self::get_where( $filter );
			$order    = WPSC_Functions::parse_order( $filter );
			$limit    = WPSC_Functions::parse_limit( $filter );
			$group_by = 'GROUP BY t.id ';

			$sql = $sql . $joins . $where . $group_by . $order . $limit;

			$results     = $wpdb->get_results( $sql, ARRAY_A );
			$total_items = $wpdb->get_var( 'SELECT FOUND_ROWS()' );

			$response = WPSC_Functions::parse_response( $results, $total_items, $filter );

			// Return array.
			if ( ! $is_object ) {
				return $response;
			}

			// create and return array of objects.
			$temp_results = array();
			foreach ( $response['results'] as $ticket ) {

				$ob   = new WPSC_Ticket();
				$data = array();
				foreach ( $ticket as $key => $val ) {
					$data[ $key ] = $val;
				}
				$ob->set_data( $data );
				$temp_results[] = $ob;
			}
			$response['results'] = $temp_results;

			return $response;
		}

		/**
		 * Return join string
		 *
		 * @param array $filter - user filter.
		 * @return string
		 */
		private static function get_joins( $filter ) {

			$joins = apply_filters( 'wpsc_ticket_joins', array(), $filter );
			return implode( ' ', $joins ) . ' ';
		}

		/**
		 * Get where for find method
		 *
		 * @param array $filter - user filter.
		 * @return array
		 */
		private static function get_where( $filter ) {

			global $wpdb;

			// Custom fields by type.
			$cfs           = array();
			$custom_fields = WPSC_Custom_Field::$custom_fields;
			foreach ( $custom_fields as $cf ) {
				$cfs[ $cf->type::$slug ][] = $cf;
			}

			$where = array( '1=1' );

			// Load system filters.
			$system_query = isset( $filter['system_query'] ) ? $filter['system_query'] : array();
			if ( $system_query ) {
				$where[] = self::parse_filters( $system_query );
			}

			// Load meta filters.
			$meta_query = isset( $filter['meta_query'] ) ? $filter['meta_query'] : array();
			if ( $meta_query ) {
				$where[] = self::parse_filters( $meta_query );
			}

			// Load search query.
			$search = esc_sql( $wpdb->esc_like( WPSC_Functions::get_filter_search_str( $filter ) ) );
			if ( $search ) {

				$gs = get_option( 'wpsc-gs-general' );
				$search_query = apply_filters( 'wpsc_ticket_search', array(), $filter, $cfs, $search, $gs['allowed-search-fields'] );
				if ( $search_query ) {
					$where[] = implode( ' OR ', $search_query );
				}
			}

			$where = apply_filters( 'wpsc_ticket_where', $where, $filter, $cfs );

			// is_active.
			$where[] = 't.is_active=' . $filter['is_active'];

			return 'WHERE (' . implode( ') AND (', $where ) . ') ';
		}

		/**
		 * Check whether slug is present in either system filters or meta query filters
		 *
		 * @param string $slug - custom field slug.
		 * @param array  $filter - filter array.
		 * @return boolean
		 */
		public static function is_filter( $slug, $filter ) {

			$flag        = false;
			$search_term = '"slug":"' . $slug . '"';

			if ( isset( $filter['meta_query'] ) ) {

				$meta_query = wp_json_encode( $filter['meta_query'] );
				if ( is_numeric( strpos( $meta_query, $search_term ) ) ) {
					$flag = true;
				}
			}

			return $flag;
		}

		/**
		 * Parse user filters for this model
		 *
		 * @param array $filters - filter array.
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
					$filter_str[] = self::parse_filters( $filter );
					continue;
				}

				// Invalid filter if it does not contain slug, compare and val indexes.
				$slug    = isset( $filter['slug'] ) ? WPSC_Functions::sanitize_sql_key( $filter['slug'] ) : false;
				$compare = isset( $filter['compare'] ) ? $filter['compare'] : false;
				$val     = isset( $filter['val'] ) ? $filter['val'] : false;
				if ( ! $slug || ! $compare || $val === false ) {
					$filter_str[] = '1=1';
				}

				// custom filter.
				if ( $slug === 'custom_query' ) {

					$filter_str[] = $val;

				} else {

					// Get custom field object for the slug.
					$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
					$filter_str[] = $cf ? $cf->type::parse_filter( $cf, $compare, $val ) : '1=1';
				}
			}

			return count( $filter_str ) > 1 ?
				'(' . implode( ' ' . $relation . ' ', $filter_str ) . ')' :
				$filter_str[0];
		}

		/**
		 * Load current class to reference classes
		 *
		 * @param array $classes - Associative array of class names indexed by its slug.
		 * @return array
		 */
		public static function load_ref_class( $classes ) {

			$classes['wpsc_ticket'] = array(
				'class'    => __CLASS__,
				'save-key' => 'id',
			);
			return $classes;
		}

		/**
		 * Return array of thread objects for current ticket
		 *
		 * @param integer $page_no - current page.
		 * @param integer $items_per_page - number of records per page.
		 * @param array   $types - thread types: reply, log, etc.
		 * @param string  $orderby - orderby slug.
		 * @param string  $order - order flag.
		 * @return array
		 */
		public function get_threads( $page_no = 1, $items_per_page = 0, $types = array(), $orderby = 'date_created', $order = 'DESC' ) {

			$args = array(
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'slug'    => 'ticket',
						'compare' => '=',
						'val'     => $this->id,
					),
				),
				'items_per_page' => $items_per_page,
				'page_no'        => $page_no,
				'orderby'        => $orderby,
				'order'          => $order,
			);

			if ( $types ) {
				$args['meta_query'][] = array(
					'slug'    => 'type',
					'compare' => 'IN',
					'val'     => $types,
				);
			}

			return WPSC_Thread::find( $args )['results'];
		}

		/**
		 * Return last reply of the ticket.
		 *
		 * @return WPSC_Thread|boolean
		 */
		public function get_last_reply() {

			$filters = array(
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'slug'    => 'ticket',
						'compare' => '=',
						'val'     => $this->id,
					),
					array(
						'slug'    => 'type',
						'compare' => 'IN',
						'val'     => array( 'reply', 'report' ),
					),
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => '1',
					),
				),
				'orderby'        => 'id',
				'order'          => 'DESC',
				'items_per_page' => 1,
			);
			$threads = WPSC_Thread::find( $filters );

			return isset( $threads['results'][0] ) ? $threads['results'][0] : false;
		}

		/**
		 * Return last note of the ticket.
		 *
		 * @return WPSC_Thread|boolean
		 */
		public function get_last_note() {

			$filters = array(
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'slug'    => 'ticket',
						'compare' => '=',
						'val'     => $this->id,
					),
					array(
						'slug'    => 'type',
						'compare' => '=',
						'val'     => 'note',
					),
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => '1',
					),
				),
				'orderby'        => 'id',
				'order'          => 'DESC',
				'items_per_page' => 1,
			);
			$threads = WPSC_Thread::find( $filters );

			return isset( $threads['results'][0] ) ? $threads['results'][0] : false;
		}

		/**
		 * Return array of agents allowed to view this ticket
		 */
		public function get_current_read_permission_agents() {

			$agents      = array();
			$agent_roles = get_option( 'wpsc-agent-roles' );

			// check customer whether he is an agent.
			if ( $this->customer->user ) {
				$agent = WPSC_Agent::get_by_user_id( $this->customer->user->ID );
				if ( $agent->id && $agent->is_active ) {
					$agents[] = $agent;
				}
			}

			// unassigned.
			if ( ! $this->assigned_agent ) {

				$applicable_roles = array();
				foreach ( $agent_roles as $key => $role ) {
					if ( $role['caps']['view-unassigned'] ) {
						$applicable_roles[] = $key;
					}
				}
				$temp_agents = WPSC_Agent::find(
					array(
						'items_per_page' => 0,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'slug'    => 'role',
								'compare' => 'IN',
								'val'     => $applicable_roles,
							),
							array(
								'slug'    => 'is_active',
								'compare' => '=',
								'val'     => 1,
							),
							array(
								'slug'    => 'is_agentgroup',
								'compare' => '=',
								'val'     => 0,
							),
						),
					)
				)['results'];
				if ( $temp_agents ) {
					$agents = array_merge( $agents, $temp_agents );
				}
			} else {  // assigned to agents.

				// assign to me.
				foreach ( $this->assigned_agent as $agent ) {

					if ( $agent->is_agentgroup ) {
						continue;
					}
					if ( $agent_roles[ $agent->role ]['caps']['view-assigned-me'] ) {
						$agents[] = $agent;
					}
				}

				// assign to other.
				$applicable_roles = array();
				foreach ( $agent_roles as $key => $role ) {
					if ( $role['caps']['view-assigned-others'] ) {
						$applicable_roles[] = $key;
					}
				}
				$temp_agents = WPSC_Agent::find(
					array(
						'items_per_page' => 0,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'slug'    => 'role',
								'compare' => 'IN',
								'val'     => $applicable_roles,
							),
							array(
								'slug'    => 'is_active',
								'compare' => '=',
								'val'     => 1,
							),
							array(
								'slug'    => 'is_agentgroup',
								'compare' => '=',
								'val'     => 0,
							),
						),
					)
				)['results'];
				if ( $temp_agents ) {
					$agents = array_merge( $agents, $temp_agents );
				}

				// filter for agentgroups.
				$agents = apply_filters( 'wpsc_get_current_read_permission_agents', $agents, $this );
			}

			$temp_agents = array();
			foreach ( $agents as $agent ) {
				if ( ! isset( $temp_agents[ $agent->id ] ) ) {
					$temp_agents[ $agent->id ] = $agent;
				}
			}

			return $temp_agents;
		}

		/**
		 * Return agents associated to this ticket previously with read permission
		 *
		 * @param array $prev - array of agent objects.
		 * @return array
		 */
		public function get_prev_read_permission_agents( $prev ) {

			$agents      = array();
			$agent_roles = get_option( 'wpsc-agent-roles' );

			// unassigned.
			if ( ! $prev ) {

				$applicable_roles = array();
				foreach ( $agent_roles as $key => $role ) {
					if ( $role['caps']['view-unassigned'] ) {
						$applicable_roles[] = $key;
					}
				}
				$temp_agents = WPSC_Agent::find(
					array(
						'items_per_page' => 0,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'slug'    => 'role',
								'compare' => 'IN',
								'val'     => $applicable_roles,
							),
							array(
								'slug'    => 'is_active',
								'compare' => '=',
								'val'     => 1,
							),
							array(
								'slug'    => 'is_agentgroup',
								'compare' => '=',
								'val'     => 0,
							),
						),
					)
				)['results'];
				if ( $temp_agents ) {
					$agents = array_merge( $agents, $temp_agents );
				}
			} else {

				// assign to me.
				foreach ( $prev as $agent ) {
					if ( $agent->is_agentgroup ) {
						continue;
					}
					if ( $agent_roles[ $agent->role ]['caps']['view-assigned-me'] ) {
						$agents[] = $agent;
					}
				}

				// assign to other.
				$applicable_roles = array();
				foreach ( $agent_roles as $key => $role ) {
					if ( $role['caps']['view-assigned-others'] ) {
						$applicable_roles[] = $key;
					}
				}
				$temp_agents = WPSC_Agent::find(
					array(
						'items_per_page' => 0,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'slug'    => 'role',
								'compare' => 'IN',
								'val'     => $applicable_roles,
							),
							array(
								'slug'    => 'is_active',
								'compare' => '=',
								'val'     => 1,
							),
							array(
								'slug'    => 'is_agentgroup',
								'compare' => '=',
								'val'     => 0,
							),
						),
					)
				)['results'];
				if ( $temp_agents ) {
					$agents = array_merge( $agents, $temp_agents );
				}

				// filter for agentgroups.
				$agents = apply_filters( 'wpsc_get_prev_read_permission_agents', $agents, $this );
			}

			$temp_agents = array();
			foreach ( $agents as $agent ) {
				if ( ! isset( $temp_agents[ $agent->id ] ) ) {
					$temp_agents[ $agent->id ] = $agent;
				}
			}

			return $temp_agents;
		}

		/**
		 * Get ticket id range
		 *
		 * @return array start id and end id of random number
		 */
		public static function get_ticket_id_range() {

			$ad_settings = get_option( 'wpsc-ms-advanced-settings' );
			$length      = $ad_settings['random-id-length'];

			$start = '1';
			for ( $i = 1; $i < $length; $i++ ) {
				$start .= '0';
			}

			$end = '9';
			for ( $i = 1; $i < $length; $i++ ) {
				$end .= '9';
			}

			return array(
				'start_range' => intval( $start ),
				'end_range'   => intval( $end ),
			);
		}

		/**
		 * Get ticket url
		 */
		public function get_url() {

			$page_settings = get_option( 'wpsc-gs-page-settings' );
			$ticket_url    = '';

			if ( ! $this->auth_code ) {
				$this->auth_code = WPSC_Functions::get_random_string();
				$this->save();
			}

			// support page.
			if ( $page_settings['ticket-url-page'] == 'support-page' && $page_settings['support-page'] ) {
				$url        = get_permalink( $page_settings['support-page'] );
				$ticket_url = add_query_arg(
					array(
						'wpsc-section' => 'ticket-list',
						'ticket-id'    => $this->id,
						'auth-code'    => $this->auth_code,
					),
					$url
				);
			}

			// open ticket page.
			if ( $page_settings['ticket-url-page'] == 'open-ticket-page' && $page_settings['open-ticket-page'] ) {
				$url        = get_permalink( $page_settings['open-ticket-page'] );
				$ticket_url = add_query_arg(
					array(
						'ticket-id' => $this->id,
						'auth-code' => $this->auth_code,
					),
					$url
				);
			}

			return apply_filters( 'wpsc_get_ticket_url', $ticket_url, $this );
		}

		/**
		 * Get description thread model object
		 *
		 * @return WPSC_Thread
		 */
		public function get_description_thread() {

			$results = WPSC_Thread::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'ticket',
							'compare' => '=',
							'val'     => $this->id,
						),
						array(
							'slug'    => 'type',
							'compare' => '=',
							'val'     => 'report',
						),
					),
				)
			)['results'];

			return $results ? $results[0] : false;
		}
	}
endif;

WPSC_Ticket::init();
