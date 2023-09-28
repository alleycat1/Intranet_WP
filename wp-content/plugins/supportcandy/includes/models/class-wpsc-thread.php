<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Thread' ) ) :

	final class WPSC_Thread {

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

			// Ticket model.
			add_filter( 'wpsc_ticket_joins', array( __CLASS__, 'ticket_join' ), 10, 2 );
			add_filter( 'wpsc_ticket_where', array( __CLASS__, 'ticket_where' ), 9, 3 );
			add_filter( 'wpsc_ticket_search', array( __CLASS__, 'ticket_search' ), 10, 5 );
		}

		/**
		 * Apply schema for this model
		 *
		 * @return void
		 */
		public static function apply_schema() {

			$schema       = array(
				'id'           => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'ticket'       => array(
					'has_ref'          => true,
					'ref_class'        => 'wpsc_ticket',
					'has_multiple_val' => false,
				),
				'is_active'    => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'customer'     => array(
					'has_ref'          => true,
					'ref_class'        => 'wpsc_customer',
					'has_multiple_val' => false,
				),
				'type'         => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'body'         => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'attachments'  => array(
					'has_ref'          => true,
					'ref_class'        => 'wpsc_attachment',
					'has_multiple_val' => true,
				),
				'log'          => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'ip_address'   => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'source'       => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'os'           => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'browser'      => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'seen'         => array(
					'has_ref'          => true,
					'ref_class'        => 'datetime',
					'has_multiple_val' => false,
				),
				'date_created' => array(
					'has_ref'          => true,
					'ref_class'        => 'datetime',
					'has_multiple_val' => false,
				),
				'date_updated' => array(
					'has_ref'          => true,
					'ref_class'        => 'datetime',
					'has_multiple_val' => false,
				),
			);
			self::$schema = apply_filters( 'wpsc_thread_schema', $schema );

			// Prevent modify.
			$prevent_modify       = array( 'id', 'ticket', 'date_created', 'ip_address', 'os', 'browser', 'type', 'source' );
			self::$prevent_modify = apply_filters( 'wpsc_thread_prevent_modify', $prevent_modify );
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

				$thread = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_threads WHERE id = " . $id, ARRAY_A );
				if ( ! is_array( $thread ) ) {
					return;
				}

				foreach ( $thread as $key => $val ) {
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

				$thd = self::insert( $data );
				if ( $thd ) {
					$this->data = $thd->data;
					$success    = true;
				} else {
					$success = false;
				}
			} else {

				unset( $data['id'] );
				$success = $wpdb->update(
					$wpdb->prefix . 'psmsc_threads',
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
		 * @return WPSC_Thread
		 */
		public static function insert( $data ) {

			global $wpdb;
			$current_date = ( new DateTime() )->format( 'Y-m-d H:i:s' );

			// Set date_created and date_updated.
			if ( ! isset( $data['date_created'] ) ) {
				$data['date_created'] = $current_date;
				$data['date_updated'] = $current_date;
			}

			$success = $wpdb->insert(
				$wpdb->prefix . 'psmsc_threads',
				$data
			);

			if ( ! $success ) {
				return false;
			}

			$thread = new WPSC_Thread( $wpdb->insert_id );
			return $thread;
		}

		/**
		 * Delete record from database
		 *
		 * @param WPSC_Thread $thread - thread object.
		 * @return boolean
		 */
		public static function destroy( $thread ) {

			global $wpdb;

			// Delete thread logs.
			$success = $wpdb->delete(
				$wpdb->prefix . 'psmsc_logs',
				array(
					'type'   => 'thread',
					'ref_id' => $thread->id,
				)
			);
			if ( ! $success ) {
				return false;
			}

			// Mark attachments for removal.
			$wpdb->query( "UPDATE {$wpdb->prefix}psmsc_attachments SET is_active=0 WHERE source IN ('img_editor', '" . $thread->type . "') AND source_id=" . $thread->id );

			$success = $wpdb->delete(
				$wpdb->prefix . 'psmsc_threads',
				array( 'id' => $thread->id )
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

			$sql   = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $wpdb->prefix . 'psmsc_threads ';
			$where = self::get_where( $filter );

			$filter['items_per_page'] = isset( $filter['items_per_page'] ) ? $filter['items_per_page'] : 20;
			$filter['page_no']        = isset( $filter['page_no'] ) ? $filter['page_no'] : 1;
			$filter['orderby']        = isset( $filter['orderby'] ) ? $filter['orderby'] : 'id';
			$filter['order']          = isset( $filter['order'] ) ? $filter['order'] : 'DESC';

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
			foreach ( $response['results'] as $thread ) {

				$ob   = new WPSC_Thread();
				$data = array();
				foreach ( $thread as $key => $val ) {
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

			$classes['wpsc_thread'] = array(
				'class'    => __CLASS__,
				'save-key' => 'id',
			);
			return $classes;
		}

		/**
		 * Add SQL joins to ticket model for this field type
		 *
		 * @param array $joins - array of join string that can be imploded later.
		 * @param array $filter - user filter.
		 * @return array
		 */
		public static function ticket_join( $joins, $filter ) {

			global $wpdb;
			$gs = get_option( 'wpsc-gs-general' );
			$search = WPSC_Functions::get_filter_search_str( $filter );
			if ( ( $search && in_array( 'threads', $gs['allowed-search-fields'] ) ) || WPSC_Ticket::is_filter( 'description', $filter ) ) {
				$joins[] = 'LEFT JOIN ' . $wpdb->prefix . 'psmsc_threads th ON t.id = th.ticket';
			}

			return $joins;
		}

		/**
		 * Set thread type limit based on user meta query and search string
		 *
		 * @param array $where - where array.
		 * @param array $filter - user filter.
		 * @param array $custom_fields - custom fields array.
		 * @return array
		 */
		public static function ticket_where( $where, $filter, $custom_fields ) {

			global $wpdb;
			$current_user = WPSC_Current_User::$current_user;
			$search = esc_sql( $wpdb->esc_like( WPSC_Functions::get_filter_search_str( $filter ) ) );
			$gs = get_option( 'wpsc-gs-general' );

			if ( $search && in_array( 'threads', $gs['allowed-search-fields'] ) ) {
				if ( $current_user->is_agent ) {
					$where[] = 'th.type IN(\'report\', \'reply\', \'note\')';
				} else {
					$where[] = 'th.type IN(\'report\', \'reply\')';
				}
			}

			if ( ! $search && WPSC_Ticket::is_filter( 'description', $filter ) ) {
				$where[] = 'th.type = \'report\'';
			}

			return $where;
		}

		/**
		 * Add ticket search compatibility for fields of this custom field type.
		 *
		 * @param array  $sql - Array of sql peices that can be joined later.
		 * @param array  $filter - User filter.
		 * @param array  $custom_fields - Custom fields array applicable for search.
		 * @param string $search - search string.
		 * @param array  $allowed_search_fields - Allowed search fields.
		 * @return array
		 */
		public static function ticket_search( $sql, $filter, $custom_fields, $search, $allowed_search_fields ) {

			if ( in_array( 'threads', $allowed_search_fields ) ) {
				$sql[] = 'CONVERT(th.body USING utf8) LIKE \'%' . $search . '%\'';
				$search_items = WPSC_Attachment::get_tl_search_string( $search );
				if ( $search_items ) {
					$sql[] = 'th.attachments RLIKE \'(^|[|])(' . implode( '|', $search_items ) . ')($|[|])\'';
				}
			}
			return $sql;
		}

		/**
		 * Get logs for current thread
		 *
		 * @return array
		 */
		public function get_logs() {

			$logs = WPSC_Log::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'type',
							'compare' => '=',
							'val'     => 'thread',
						),
						array(
							'slug'    => 'ref_id',
							'compare' => '=',
							'val'     => $this->id,
						),
					),
				)
			);

			return $logs['results'];
		}

		/**
		 * Delete logs for current thread
		 */
		public function delete_logs() {

			global $wpdb;
			$sql = "DELETE FROM {$wpdb->prefix}psmsc_logs WHERE type='thread' AND ref_id={$this->id}";
			$wpdb->query( $sql );
		}

		/**
		 * Return printable body string for thread. Can be used for replace macros.
		 *
		 * @return string
		 */
		public function get_printable_string() {

			ob_start();
			echo wp_kses_post( $this->body );

			// Thread attachments.
			$attachments = $this->attachments;
			if ( $attachments ) {
				?>
				<div>
					<strong><?php esc_attr_e( 'Attachments', 'supportcandy' ); ?>: </strong>
					<?php
					$en = get_option( 'wpsc-en-general' );
					if ( $en['attachments-in-notification'] == 'file-links' ) {

						$names = array();
						foreach ( $attachments as $attachment ) {
							$link = site_url( '/' ) . '?wpsc_attachment=' . $attachment->id . '&auth_code=' . $this->ticket->auth_code;
							$names[] = '<a href= ' . $link . ' >' . $attachment->name . '</a>';
						}
						echo wp_kses_post( implode( ', ', $names ) );
					} else {
						$names = array_map( fn( $attachment) => $attachment->name, $attachments );
						echo esc_attr( implode( ', ', $names ) );
					}
					?>
				</div>
				<?php
			}

			return ob_get_clean();
		}

		/**
		 * Prints thread for history macro types.
		 *
		 * @return string
		 */
		public function get_history_macro() {

			$advanced = get_option( 'wpsc-ms-advanced-settings', array() );
			$date     = wp_date( $advanced['thread-date-format'], $this->date_created->setTimezone( wp_timezone() )->getTimestamp() );

			ob_start();
			if ( $this->type != 'log' ) {

				?>
				<strong>
					<?php echo esc_attr( $this->customer->name ) . ' '; ?>
					<small>
						<i>
							<?php
							switch ( $this->type ) {
								case 'report':
									esc_attr_e( 'reported', 'supportcandy' );
									break;

								case 'reply':
									esc_attr_e( 'replied', 'supportcandy' );
									break;

								case 'note':
									esc_attr_e( 'added a note', 'supportcandy' );
									break;
							}
							?>
						</i>
					</small>
				</strong>
				<div style="font-size:10px;"><?php echo esc_attr( $date ); ?></div>
				<?php
				echo wp_kses_post( $this->get_printable_string() );

			} else {

				$body    = json_decode( $this->body );
				$is_json = ( json_last_error() == JSON_ERROR_NONE ) ? true : false;

				if ( $is_json ) {

					$cf = WPSC_Custom_Field::get_cf_by_slug( $body->slug );
					if ( ! $cf ) {
						return;
					}
					?>
					<div>
						<?php
						if ( $this->customer ) {

							printf(
								/* translators: %1$s: User Name, %2$s: Field Name */
								esc_attr__( '%1$s changed the %2$s', 'supportcandy' ),
								'<strong>' . esc_attr( $this->customer->name ) . '</strong>',
								'<strong>' . esc_attr( $cf->name ) . '</strong>'
							);

						} else {

							printf(
								/* translators: %1$s: Field Name */
								esc_attr__( 'The %1$s has been changed', 'supportcandy' ),
								'<strong>' . esc_attr( $cf->name ) . '</strong>'
							);
						}
						?>
					</div>
					<div style="font-size:10px;"><?php echo esc_attr( $date ); ?></div>
					<table>
						<tbody>
							<tr>
								<td><?php echo wp_kses_post( $cf->type::get_history_log_val( $cf, $body->prev ) ); ?></td>
								<td>-></td>
								<td><?php echo wp_kses_post( $cf->type::get_history_log_val( $cf, $body->new ) ); ?></td>
							</tr>
						</tbody>
					</table>
					<?php

				} else {

					?>
					<div><?php echo wp_kses_post( $thread->body ); ?></div>
					<div style="font-size:10px;"><?php echo esc_attr( $date ); ?></div>
					<?php
				}
			}

			return ob_get_clean();
		}
	}
endif;

WPSC_Thread::init();
