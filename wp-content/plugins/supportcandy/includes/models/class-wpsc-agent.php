<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Agent' ) ) :

	final class WPSC_Agent {

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

			// after profile update.
			add_action( 'profile_update', array( __CLASS__, 'agent_profile_update' ), 10, 2 );

			// reset missing count.
			add_action( 'init', array( __CLASS__, 'reset_count_check' ), 200 );
			add_action( 'wpsc_reset_missing_counts', array( __CLASS__, 'reset_missing_counts' ) );

			// create ticket event to reset unresolved and workload counts of related agents to the ticket.
			add_action( 'wpsc_create_new_ticket', array( __CLASS__, 'create_new_ticket' ), 500 );

			// change status event to reset unresolved and workload counts of related agents to the ticket.
			add_action( 'wpsc_change_ticket_status', array( __CLASS__, 'change_status' ), 200, 4 );

			// assigned agent event to reset unresolved and workload counts of related agents to the ticket.
			add_action( 'wpsc_change_assignee', array( __CLASS__, 'change_assignee' ), 200, 4 );

			// change raised by event to reset unresolved and workload counts of related agents to the ticket.
			add_action( 'wpsc_change_raised_by', array( __CLASS__, 'change_raised_by' ), 200, 4 );

			// delete ticket event to reset unresolved and workload counts of related agents to the ticket.
			add_action( 'wpsc_delete_ticket', array( __CLASS__, 'delete_ticket' ), 200, 1 );

			// restore ticket event to reset unresolved and workload counts of related agents to the ticket.
			add_action( 'wpsc_ticket_restore', array( __CLASS__, 'restore_ticket' ), 200, 1 );

			// agent autocomplete admin access only.
			add_action( 'wp_ajax_wpsc_agent_autocomplete_admin_access', array( __CLASS__, 'agent_autocomplete_admin_access' ) );

			// reset unresolved count after agent role update.
			add_action( 'wpsc_agent_role_update', array( __CLASS__, 'agent_role_update' ) );
		}

		/**
		 * Apply schema for this model
		 *
		 * @return void
		 */
		public static function apply_schema() {

			$schema       = array(
				'id'               => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'user'             => array(
					'has_ref'          => true,
					'ref_class'        => 'wp_user',
					'has_multiple_val' => false,
				),
				'customer'         => array(
					'has_ref'          => true,
					'ref_class'        => 'wpsc_customer',
					'has_multiple_val' => false,
				),
				'role'             => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'name'             => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'workload'         => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'unresolved_count' => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'is_agentgroup'    => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'is_active'        => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
			);
			self::$schema = apply_filters( 'wpsc_agent_schema', $schema );

			// Prevent modify.
			$prevent_modify       = array( 'id' );
			self::$prevent_modify = apply_filters( 'wpsc_agent_prevent_modify', $prevent_modify );
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

				$agent = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_agents WHERE id = " . $id, ARRAY_A );
				if ( ! is_array( $agent ) ) {
					return;
				}

				foreach ( $agent as $key => $val ) {
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

			if ( ! isset( $this->data[ $var_name ] ) ) {
				return;
			}

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

			$data              = $this->data;
			$data['is_active'] = intval( $data['is_active'] );

			$success = true;

			unset( $data['id'] );
			$success = $wpdb->update(
				$wpdb->prefix . 'psmsc_agents',
				$data,
				array( 'id' => $this->data['id'] )
			);

			$this->is_modified        = false;
			self::$cache[ $this->id ] = $this;
			return $success ? true : false;
		}

		/**
		 * Insert new record
		 *
		 * @param array $data - record data.
		 * @return WPSC_Agent
		 */
		public static function insert( $data ) {

			global $wpdb;

			$success = $wpdb->insert(
				$wpdb->prefix . 'psmsc_agents',
				$data
			);

			if ( ! $success ) {
				return false;
			}

			$agent                     = new WPSC_Agent( $wpdb->insert_id );
			self::$cache[ $agent->id ] = $agent;
			return $agent;
		}

		/**
		 * Make it inactive so that garbage collector will delete files associated in
		 * background and then delete the record. This will improve its performance.
		 *
		 * @param WPSC_Agent $agent - agent model.
		 * @return boolean
		 */
		public static function destroy( $agent ) {

			global $wpdb;
			$agent->is_active = 0;
			$agent->save();
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

			$filter['items_per_page'] = isset( $filter['items_per_page'] ) ? $filter['items_per_page'] : 20;
			$filter['page_no']        = isset( $filter['page_no'] ) ? $filter['page_no'] : 1;
			$filter['orderby']        = isset( $filter['orderby'] ) ? $filter['orderby'] : 'name';
			$filter['order']          = isset( $filter['order'] ) ? $filter['order'] : 'ASC';

			// orderby.
			$filter['orderby'] = apply_filters( 'wpsc_agent_orderby_string', $filter['orderby'] );

			// Add table alice to orderby.
			$filter['orderby'] = 'a.' . $filter['orderby'];

			$sql   = 'SELECT SQL_CALC_FOUND_ROWS a.* FROM ' . $wpdb->prefix . 'psmsc_agents a ';
			$order = WPSC_Functions::parse_order( $filter );
			$limit = WPSC_Functions::parse_limit( $filter );
			$join  = self::get_joins( $filter );
			$where = self::get_where( $filter );

			$group_by = 'GROUP BY a.id ';

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
			foreach ( $response['results'] as $agent ) {

				$ob   = new WPSC_Agent();
				$data = array();
				foreach ( $agent as $key => $val ) {
					$data[ $key ] = $val;
				}
				$ob->set_data( $data );
				$temp_results[] = $ob;

				// set cache.
				self::$cache[ $ob->id ] = $ob;
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
				$joins = array( 'LEFT JOIN ' . $wpdb->users . ' u ON a.user = u.ID' );
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
				$where[] = WPSC_Functions::parse_user_filters( __CLASS__, $meta_query );
			}

			// Search.
			$search = WPSC_Functions::get_filter_search_str( $filter );
			if ( $search ) {
				$search_query = array(
					'CONVERT(a.name USING utf8) LIKE \'%' . $search . '%\'',
					'CONVERT(u.user_login USING utf8) LIKE \'%' . $search . '%\'',
					'CONVERT(u.user_nicename USING utf8) LIKE \'%' . $search . '%\'',
					'CONVERT(u.user_email USING utf8) LIKE \'%' . $search . '%\'',
					'CONVERT(u.display_name USING utf8) LIKE \'%' . $search . '%\'',
				);
				$where[]      = implode( ' OR ', $search_query );
			}

			return 'WHERE (' . implode( ') AND (', $where ) . ') ';
		}

		/**
		 * Load current class to reference classes
		 *
		 * @param array $classes - Associative array of class names indexed by its slug.
		 * @return array
		 */
		public static function load_ref_class( $classes ) {

			$classes['wpsc_agent'] = array(
				'class'    => __CLASS__,
				'save-key' => 'id',
			);
			return $classes;
		}

		/**
		 * Check whether wp user of given id is an agent or not
		 *
		 * @param int $user_id - wp user id.
		 * @return boolean
		 */
		public static function get_by_user_id( $user_id ) {

			if ( ! $user_id ) {
				return new WPSC_Agent();
			}

			$response = self::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'user',
							'compare' => '=',
							'val'     => $user_id,
						),
					),
				)
			);

			return $response['results'] ? $response['results'][0] : new WPSC_Agent();
		}

		/**
		 * Get agents by role.
		 *
		 * @param int $role_id - role id.
		 * @return array.
		 */
		public static function get_by_role( $role_id ) {

			$agents = self::find(
				array(
					'items_per_page' => 0,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'slug'    => 'role',
							'compare' => '=',
							'val'     => $role_id,
						),
						array(
							'slug'    => 'is_active',
							'compare' => '=',
							'val'     => 1,
						),
					),
				)
			)['results'];

			return $agents;
		}

		/**
		 * Get an agent by customer
		 *
		 * @param WPSC_Customer $customer - customer object.
		 * @return WPSC_Agent
		 */
		public static function get_by_customer( $customer ) {

			$results = self::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'customer',
							'compare' => '=',
							'val'     => $customer->id,
						),
						array(
							'slug'    => 'is_active',
							'compare' => '=',
							'val'     => 1,
						),
					),
				)
			)['results'];

			return $results ? $results[0] : new WPSC_Agent();
		}

		/**
		 * Agent autocomplete callback
		 *
		 * @param array $filters - autocomplete filter.
		 * @return array
		 */
		public static function agent_autocomplete( $filters ) {

			$args = array(
				'search'         => $filters['term'],
				'items_per_page' => 25,
				'orderby'        => $filters['sort_by'],
				'order'          => 'ASC',
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => 1,
					),
				),
			);

			if ( is_numeric( $filters['isAgentgroup'] ) ) {
				$args['meta_query'][] = array(
					'slug'    => 'is_agentgroup',
					'compare' => '=',
					'val'     => $filters['isAgentgroup'],
				);
			}

			$args   = apply_filters( 'wpsc_agent_autocomplete_filters', $args, $filters['filter_by'] );
			$agents = self::find( $args )['results'];

			$response = array();

			if ( ! $filters['isMultiple'] ) {
				$response[] = array(
					'id'    => 0,
					'title' => esc_attr__( 'None', 'supportcandy' ),
				);
			}

			foreach ( $agents as $agent ) {
				$response[] = array(
					'id'    => $agent->id,
					'title' => $agent->name,
				);
			}

			return $response;
		}

		/**
		 * Return whether or not agent has given capability
		 *
		 * @param string $cap - capability slug.
		 * @return boolean
		 */
		public function has_cap( $cap ) {

			$roles = get_option( 'wpsc-agent-roles', array() );
			if ( ! isset( $roles[ $this->role ] ) ) {
				return false;
			}
			$role = $roles[ $this->role ];
			return isset( $role['caps'][ $cap ] ) && $role['caps'][ $cap ] ? true : false;
		}

		/**
		 * Get signature of the agent
		 *
		 * @return string
		 */
		public function get_signature() {

			$signature = get_user_meta( $this->user->ID, get_current_blog_id() . '_wpsc_email_signature', true );
			return $signature ? $signature : '';
		}

		/**
		 * Set signature for the agent
		 *
		 * @param string $signature - signature string.
		 * @return void
		 */
		public function set_signature( $signature ) {

			update_user_meta( $this->user->ID, get_current_blog_id() . '_wpsc_email_signature', $signature );
		}

		/**
		 * Get default filter for the agent
		 *
		 * @return string
		 */
		public function get_default_filter() {

			$current_user    = WPSC_Current_User::$current_user;
			$default_filters = get_option( 'wpsc-atl-default-filters' );
			$saved_filters   = $current_user->get_saved_filters();
			$agent_view      = get_option( 'wpsc-tl-ms-agent-view', array() );

			$default_filter = get_user_meta( $this->user->ID, get_current_blog_id() . '_wpsc_tl_default_filter', true );
			if ( ! $default_filter ) {
				$this->set_default_filter( $agent_view['default-filter'] );
				return $agent_view['default-filter'];
			}

			$default_flag = preg_match( '/default-(\d*)$/', $default_filter, $default_matches );
			$saved_flag   = preg_match( '/saved-(\d*)$/', $default_filter, $saved_matches );

			if (
				! (
					isset( $default_filters[ $default_filter ] ) ||
					( $default_flag && isset( $default_filters[ $default_matches[1] ] ) ) ||
					( $saved_flag && isset( $saved_filters[ $saved_matches[1] ] ) )
				) ||
				( isset( $default_filters[ $default_filter ] ) && ! $default_filters[ $default_filter ]['is_enable'] ) ||
				( $default_flag && isset( $default_filters[ $default_matches[1] ] ) && ! $default_filters[ $default_matches[1] ]['is_enable'] )
			) {
				$this->set_default_filter( $agent_view['default-filter'] );
				return $agent_view['default-filter'];
			}

			return $default_filter;
		}

		/**
		 * Set default filter for the agent
		 *
		 * @param string $filter - filter slug.
		 * @return void
		 */
		public function set_default_filter( $filter ) {

			update_user_meta( $this->user->ID, get_current_blog_id() . '_wpsc_tl_default_filter', $filter );
		}

		/**
		 * Update name of agent if there is change in wp user meta.
		 *
		 * @param int     $user_id - wp user id.
		 * @param WP_User $old_user_data - updated user object.
		 * @return void
		 */
		public static function agent_profile_update( $user_id, $old_user_data ) {

			$agent = self::get_by_user_id( $user_id );
			if ( $agent ) {
				$user        = get_userdata( $user_id );
				$agent->name = $user->display_name;
				$agent->save();
			}
		}

		/**
		 * Get working hrs of current agent
		 *
		 * @return array
		 */
		public function get_working_hrs() {

			return WPSC_Working_Hour::get( $this->id );
		}

		/**
		 * Get exceptions of current agent
		 *
		 * @return array
		 */
		public function get_wh_exceptions() {

			return WPSC_Wh_Exception::get( $this->id );
		}

		/**
		 * Reset count check
		 *
		 * @return void
		 */
		public static function reset_count_check() {

			$is_reset = get_option( 'wpsc-unresolved-reset-status', 0 );
			if ( ! $is_reset ) {
				if ( ! wp_next_scheduled( 'wpsc_reset_missing_counts' ) ) {
					wp_schedule_event(
						time(),
						'wpsc_1min',
						'wpsc_reset_missing_counts'
					);
				}
			}
		}

		/**
		 * Calculate agent workloads
		 *
		 * @return void
		 */
		public static function reset_missing_counts() {

			$agents = self::find(
				array(
					'items_per_page' => 10,
					'meta_query'     => array(
						'relation' => 'AND',
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
						array(
							'relation' => 'OR',
							array(
								'slug'    => 'workload',
								'compare' => 'IS',
								'val'     => null,
							),
							array(
								'slug'    => 'unresolved_count',
								'compare' => 'IS',
								'val'     => null,
							),
						),
					),
				)
			)['results'];

			foreach ( $agents as $agent ) {
				$agent->reset_unresolved_count();
				$agent->reset_workload();
			}

			// reset done.
			if ( ! $agents ) {
				update_option( 'wpsc-unresolved-reset-status', 1 );
			}
		}

		/**
		 * Reset unresolved count for the agent
		 */
		public function reset_unresolved_count() {

			// tickets filter.
			$filters = array( 'items_per_page' => 1 );

			// system query.
			$system_query = array(
				'relation' => 'OR',
				array(
					'slug'    => 'customer',
					'compare' => '=',
					'val'     => $this->customer->id,
				),
			);
			if ( $this->has_cap( 'view-assigned-me' ) ) {
				$system_query[] = array(
					'slug'    => 'assigned_agent',
					'compare' => '=',
					'val'     => $this->id,
				);
			}
			if ( $this->has_cap( 'view-unassigned' ) ) {
				$system_query[] = array(
					'slug'    => 'assigned_agent',
					'compare' => '=',
					'val'     => '',
				);
			}
			if ( $this->has_cap( 'view-assigned-others' ) ) {
				$system_query[] = array(
					'slug'    => 'assigned_agent',
					'compare' => 'NOT IN',
					'val'     => array( $this->id, '' ),
				);
			}
			$filters['system_query'] = apply_filters( 'wpsc_reset_agent_unresolved_system_query', $system_query, $this );

			// meta query.
			$more_settings = get_option( 'wpsc-tl-ms-agent-view' );
			$filters['meta_query'] = array(
				'relation' => 'OR',
				array(
					'slug'    => 'status',
					'compare' => 'IN',
					'val'     => $more_settings['unresolved-ticket-statuses'],
				),
			);

			// get tickets.
			$response = WPSC_Ticket::find( $filters );

			// save unresolved count.
			$this->unresolved_count = $response['total_items'];
			$this->save();
		}

		/**
		 * Reset workload count for the agent
		 */
		public function reset_workload() {

			if ( $this->is_agentgroup ) {
				return;
			}

			$more_settings = get_option( 'wpsc-tl-ms-agent-view' );
			$response = WPSC_Ticket::find(
				array(
					'items_per_page' => 1,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'slug'    => 'status',
							'compare' => 'IN',
							'val'     => $more_settings['unresolved-ticket-statuses'],
						),
						array(
							'slug'    => 'assigned_agent',
							'compare' => '=',
							'val'     => $this->id,
						),
					),
				)
			);

			// save workload.
			$this->workload = $response['total_items'];
			$this->save();
		}

		/**
		 * Reset unresolved count and workload after create new ticket.
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return void
		 */
		public static function create_new_ticket( $ticket ) {

			// reset workload for applicable agents.
			foreach ( $ticket->assigned_agent as $agent ) {
				$agent->reset_workload();
			}

			// reset unresolved count for applicable agents.
			$agents = $ticket->get_current_read_permission_agents();
			foreach ( $agents as $agent ) {
				$agent->reset_unresolved_count();
			}
		}


		/**
		 * Reset unresolved count and workload after change status.
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @param int         $prev - previous value.
		 * @param int         $new - new value.
		 * @param int         $customer_id - customer object id who made this change.
		 * @return void
		 */
		public static function change_status( $ticket, $prev, $new, $customer_id ) {

			// reset workload for applicable agents.
			foreach ( $ticket->assigned_agent as $agent ) {
				$agent->reset_workload();
			}

			// reset unresolved for applicable agents.
			$agents = $ticket->get_current_read_permission_agents();
			foreach ( $agents as $agent ) {
				$agent->reset_unresolved_count();
			}
		}

		/**
		 * Reset unresolved count and workload after change assignee
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @param int         $prev - previous value.
		 * @param int         $new - new value.
		 * @param int         $customer_id - customer object id who made this change.
		 * @return void
		 */
		public static function change_assignee( $ticket, $prev, $new, $customer_id ) {

			// reset workload for applicable agents.
			$agents      = array_merge( $prev, $new );
			$temp_agents = array();
			foreach ( $agents as $agent ) {
				if ( ! isset( $temp_agents[ $agent->id ] ) ) {
					$temp_agents[ $agent->id ] = $agent;
				}
			}
			foreach ( $temp_agents as $agent ) {
				$agent->reset_workload();
			}

			// reset unresolved for applicable agents.
			$agents      = $ticket->get_current_read_permission_agents();
			$agents      = array_merge( $agents, $ticket->get_prev_read_permission_agents( $prev ) );
			$temp_agents = array();
			foreach ( $agents as $agent ) {
				if ( ! isset( $temp_agents[ $agent->id ] ) ) {
					$temp_agents[ $agent->id ] = $agent;
				}
			}
			foreach ( $temp_agents as $agent ) {
				$agent->reset_unresolved_count();
			}
		}

		/**
		 * Reset unresolved count and workload after change raised by
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @param int         $prev - previous value.
		 * @param int         $new - new value.
		 * @param int         $customer_id - customer object id who made this change.
		 * @return void
		 */
		public static function change_raised_by( $ticket, $prev, $new, $customer_id ) {

			$agents = array();

			$prev_user = $prev->user;
			if ( $prev_user ) {
				$prev_agent = self::get_by_user_id( $prev_user->ID );
				if ( $prev_agent->id && $prev_agent->is_active ) {
					$agents[] = $prev_agent;
				}
			}

			$new_user = $new->user;
			if ( $new_user ) {
				$new_agent = self::get_by_user_id( $new_user->ID );
				if ( $new_agent->id && $new_agent->is_active ) {
					$agents[] = $new_agent;
				}
			}

			if ( $agents ) {
				foreach ( $agents as $agent ) {
					$agent->reset_unresolved_count();
				}
			}
		}

		/**
		 * Reset unresolved count and workload after delete ticket
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return void
		 */
		public static function delete_ticket( $ticket ) {

			$tl_advanced = get_option( 'wpsc-tl-ms-advanced' );
			if ( in_array( $ticket->status->id, $tl_advanced['closed-ticket-statuses'] ) ) {
				return;
			}

			// reset workload for applicable agents.
			foreach ( $ticket->assigned_agent as $agent ) {
				if ( ! $agent->is_active ) {
					continue;
				}
				$agent->reset_workload();
			}

			// reset unresolved for applicable agents.
			$agents = $ticket->get_current_read_permission_agents();
			foreach ( $agents as $agent ) {
				if ( ! $agent->is_active ) {
					continue;
				}
				$agent->reset_unresolved_count();
			}
		}

		/**
		 * Reset unresolved count and workload after restore ticket
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return void
		 */
		public static function restore_ticket( $ticket ) {

			$tl_advanced = get_option( 'wpsc-tl-ms-advanced' );
			if ( in_array( $ticket->status->id, $tl_advanced['closed-ticket-statuses'] ) ) {
				return;
			}

			// reset workload for applicable agents.
			foreach ( $ticket->assigned_agent as $agent ) {
				if ( ! $agent->is_active ) {
					continue;
				}
				$agent->reset_workload();
			}

			// reset unresolved for applicable agents.
			$agents = $ticket->get_current_read_permission_agents();
			foreach ( $agents as $agent ) {
				if ( ! $agent->is_active ) {
					continue;
				}
				$agent->reset_unresolved_count();
			}
		}

		/**
		 * Get editor of the agent
		 *
		 * @return string
		 */
		public function get_signature_editor() {

			return get_user_meta( $this->user->ID, get_current_blog_id() . '-wpsc_agent_signature_editor', true );
		}

		/**
		 * Set editor for current user
		 *
		 * @param string $editor - text/html editor for signature.
		 * @return void
		 */
		public function set_signature_editor( $editor ) {

			update_user_meta( $this->user->ID, get_current_blog_id() . '-wpsc_agent_signature_editor', $editor );
		}

		/**
		 * Agent autocomplete for admin access only
		 */
		public static function agent_autocomplete_admin_access() {

			if ( check_ajax_referer( 'wpsc_agent_autocomplete_admin_access', '_ajax_nonce', false ) !== 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$filters = array();

			$filters['term']       = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
			$filters['filter_by']  = isset( $_GET['filter_by'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_by'] ) ) : 'all';
			$filters['sort_by']    = isset( $_GET['sort_by'] ) ? sanitize_text_field( wp_unslash( $_GET['sort_by'] ) ) : 'name';
			$filters['isMultiple'] = isset( $_GET['isMultiple'] ) ? intval( wp_unslash( $_GET['isMultiple'] ) ) : 0;

			$filters['isAgentgroup'] = 0;
			if ( class_exists( 'WPSC_Agentgroups' ) ) {
				$filters['isAgentgroup'] = isset( $_GET['isAgentgroup'] ) ? intval( $_GET['isAgentgroup'] ) : null;
			}

			$response = self::agent_autocomplete( $filters );
			wp_send_json( $response );
		}

		/**
		 * Update unresolved count after agent role capabilities are updated
		 *
		 * @param integer $role_id - agent role id.
		 * @return void
		 */
		public static function agent_role_update( $role_id ) {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$args      = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'slug'    => 'role',
						'compare' => '=',
						'val'     => $role_id,
					),
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => 1,
					),
				),
			);
			$agents = self::find( $args )['results'];

			foreach ( $agents as $agent ) {

				$agent->reset_unresolved_count();
			}
		}
	}
endif;

WPSC_Agent::init();
