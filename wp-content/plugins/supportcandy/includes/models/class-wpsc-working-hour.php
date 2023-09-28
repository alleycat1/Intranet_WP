<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Working_Hour' ) ) :

	final class WPSC_Working_Hour {

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

			// Settings section.
			add_action( 'wp_ajax_wpsc_get_working_hrs', array( __CLASS__, 'get_working_hrs' ) );
			add_action( 'wp_ajax_wpsc_set_working_hrs', array( __CLASS__, 'set_working_hrs' ) );
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
				'agent'      => array(
					'has_ref'          => true,
					'ref_class'        => 'wpsc_agent',
					'has_multiple_val' => false,
				),
				'day'        => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'start_time' => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
				'end_time'   => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
			);
			self::$schema = apply_filters( 'wpsc_wh_schema', $schema );

			// Prevent modify.
			$prevent_modify       = array( 'id' );
			self::$prevent_modify = apply_filters( 'wpsc_wh_prevent_modify', $prevent_modify );
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

				$working_hr = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_working_hrs WHERE id = " . $id, ARRAY_A );
				if ( ! is_array( $working_hr ) ) {
					return;
				}

				foreach ( $working_hr as $key => $val ) {
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
		 * Save changes made
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
				$wpdb->prefix . 'psmsc_working_hrs',
				$data,
				array( 'id' => $this->data['id'] )
			);

			$this->is_modified = false;
			return $success ? true : false;
		}

		/**
		 * Insert new record
		 *
		 * @param array $data - insert data.
		 * @return WPSC_Working_Hour
		 */
		public static function insert( $data ) {

			global $wpdb;

			$success = $wpdb->insert(
				$wpdb->prefix . 'psmsc_working_hrs',
				$data
			);

			if ( ! $success ) {
				return false;
			}

			$working_hr = new WPSC_Working_Hour( $wpdb->insert_id );
			return $working_hr;
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

			$sql   = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $wpdb->prefix . 'psmsc_working_hrs ';
			$where = self::get_where( $filter );

			$filter['items_per_page'] = isset( $filter['items_per_page'] ) ? $filter['items_per_page'] : 0;
			$filter['page_no']        = isset( $filter['page_no'] ) ? $filter['page_no'] : 0;
			$filter['orderby']        = isset( $filter['orderby'] ) ? $filter['orderby'] : 'day';
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
			foreach ( $response['results'] as $working_hr ) {

				$ob   = new WPSC_Working_Hour();
				$data = array();
				foreach ( $working_hr as $key => $val ) {
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
		 * Get working hrs of agent
		 *
		 * @param int $agent_id - Agent ID.
		 * @return array
		 */
		public static function get( $agent_id = 0 ) {

			// return from cache if found.
			if ( isset( self::$cache[ $agent_id ] ) ) {
				return self::$cache[ $agent_id ];
			}

			// get it from db.
			$working_hrs = self::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'agent',
							'compare' => '=',
							'val'     => $agent_id,
						),
					),
				)
			);
			$response    = array();
			foreach ( $working_hrs['results'] as $key => $working_hr ) {
				$response[ $key + 1 ] = $working_hr;
			}

			// add it to cache.
			self::$cache[ $agent_id ] = $response;

			return $response;
		}

		/**
		 * Set working hrs of given agent id
		 *
		 * @param array   $wh - weekly working hrs.
		 * @param integer $agent_id - agent id.
		 * @return void
		 */
		public static function set( $wh, $agent_id = 0 ) {

			// sanitize request data.
			$working_hrs = array();
			foreach ( $wh as $day => $working_hr ) {

				$day = intval( $day );
				if ( ! $day ) {
					wp_send_json_error( 'Bad request', 400 );
				}

				$start_time = isset( $working_hr['start_time'] ) ? sanitize_text_field( $working_hr['start_time'] ) : '';
				if ( ! $start_time ) {
					wp_send_json_error( 'Bad request', 400 );
				}

				$end_time = isset( $working_hr['end_time'] ) ? sanitize_text_field( $working_hr['end_time'] ) : '';
				if ( ! $end_time ) {
					wp_send_json_error( 'Bad request', 400 );
				}

				$working_hrs[ $day ] = array(
					'start_time' => $start_time,
					'end_time'   => $end_time,
				);
			}

			// save changes.
			$whs = self::get( $agent_id );
			for ( $i = 1; $i <= 7; $i++ ) {

				$working_hr = $whs[ $i ];

				$start_time             = $working_hrs[ $i ]['start_time'];
				$working_hr->start_time = $start_time;

				$end_time             = $start_time != 'off' ? $working_hrs[ $i ]['end_time'] : 'off';
				$working_hr->end_time = $end_time;

				$working_hr->save();
			}

			// remove from cache so that next time it will be pulled from db.
			unset( self::$cache[ $agent_id ] );
		}

		/**
		 * Load current class to reference classes
		 *
		 * @param array $classes - Associative array of class names indexed by its slug.
		 * @return array
		 */
		public static function load_ref_class( $classes ) {

			$classes['wpsc_working_hr'] = array(
				'class'    => __CLASS__,
				'save-key' => 'id',
			);
			return $classes;
		}

		/**
		 * Get working hrs settings
		 *
		 * @return void
		 */
		public static function get_working_hrs() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$working_hrs = self::get();?>

			<form onsubmit="return false;" class="wpsc-wh-settings">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/working-hours/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<table class="wpsc-working-hrs">
				<?php
				for ( $i = 1; $i <= 7; $i++ ) :
					$start_time = $working_hrs[ $i ]->start_time;
					$end_time   = $working_hrs[ $i ]->end_time;
					$style      = $start_time == 'off' ? 'display: none;' : '';
					?>
						<tr>
							<td class="dayName"><?php echo esc_attr( WPSC_Functions::get_day_name( $i ) ); ?>:</td>
							<td>
								<select class="wpsc-wh-start-time" name="wh[<?php echo esc_attr( $i ); ?>][start_time]">
									<?php self::get_start_time_slots( $start_time ); ?>
								</select>
							</td>
							<td style="<?php echo esc_attr( $style ); ?>">-</td>
							<td style="<?php echo esc_attr( $style ); ?>">
								<select class="wpsc-wh-end-time" name="wh[<?php echo esc_attr( $i ); ?>][end_time]">
									<?php self::get_end_time_slots( $start_time, $end_time ); ?>
								</select>
							</td>
						</tr>
						<?php
					endfor;
				?>
				</table>
				<input type="hidden" name="action" value="wpsc_set_working_hrs">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_working_hrs' ) ); ?>">
			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_working_hrs();">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
				</button>
			</div>
			<script>
				var end_times = [];
				<?php
				$current_slot     = new DateTime( '2020-01-01 00:15:00' );
				$second_last_slot = new DateTime( '2020-01-01 23:45:00' );
				$last_slot        = new DateTime( '2020-01-01 23:59:59' );

				do {
					$time = $current_slot->format( 'H:i:s' )
					?>
						end_times.push({
							val: '<?php echo esc_attr( $time ); ?>',
							display_val: '<?php echo esc_attr( $current_slot->format( 'H:i' ) ); ?>',
						});
						<?php
						if ( $current_slot == $second_last_slot ) {
							$current_slot->add( new DateInterval( 'PT14M59S' ) );
						} else {
							$current_slot->add( new DateInterval( 'PT15M' ) );
						}
				} while ( $current_slot <= $last_slot );
				?>
				supportcandy.temp = {end_times};

				// Change event
				jQuery('.wpsc-wh-start-time').change(function(){
					var start_time = jQuery(this).val();
					var td1 = jQuery(this).parent().next();
					var td2 = td1.next();
					if (start_time === 'off') {
						td1.hide();
						td2.hide();
						return;
					} else {
						td1.show();
						td2.show();
					}
					var tempArr = start_time.split(":");
					var startDate = new Date(2020, 0, 1, tempArr[0], tempArr[1], tempArr[2]);
					var cmbEndTime = jQuery(this).closest('tr').find('.wpsc-wh-end-time');
					cmbEndTime.find('option').remove();
					jQuery.each(supportcandy.temp.end_times, function(index, end_time){
						var tempArr = end_time.val.split(":");
						var endDate = new Date(2020, 0, 1, tempArr[0], tempArr[1], tempArr[2]);
						if (startDate < endDate) {
							var obj = document.createElement('OPTION');
							var displayVal = document.createTextNode(end_time.display_val);
							obj.setAttribute("value", end_time.val);
							obj.appendChild(displayVal);
							cmbEndTime.append(obj);
						}
					});
				});
			</script>
			<?php
			wp_die();
		}

		/**
		 * Set company working hrs
		 *
		 * @return void
		 */
		public static function set_working_hrs() {

			if ( check_ajax_referer( 'wpsc_set_working_hrs', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$wh = isset( $_POST['wh'] ) ? map_deep( wp_unslash( $_POST['wh'] ), 'sanitize_text_field' ) : array();
			if ( ! $wh ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			self::set( $wh );
			wp_die();
		}

		/**
		 * Get start time slots
		 *
		 * @param string $start_time - time slots from.
		 * @return void
		 */
		public static function get_start_time_slots( $start_time ) {

			$current_slot = new DateTime( '2020-01-01 00:00:00' );
			$last_slot    = new DateTime( '2020-01-01 23:45:00' );
			?>
			<option value="off"><?php esc_attr_e( 'OFF', 'supportcandy' ); ?></option>
			<?php
			do {
				$time = $current_slot->format( 'H:i:s' );
				?>
				<option <?php selected( $time, $start_time ); ?> value="<?php echo esc_attr( $time ); ?>"><?php echo esc_attr( $current_slot->format( 'H:i' ) ); ?></option>
				<?php
				$current_slot->add( new DateInterval( 'PT15M' ) );
			} while ( $current_slot <= $last_slot );
		}

		/**
		 * Get end time slots
		 *
		 * @param string $start_time - start time for reference. end time will be greater than start time.
		 * @param string $end_time - preselected end time.
		 * @return void
		 */
		public static function get_end_time_slots( $start_time, $end_time ) {

			$current_slot     = new DateTime( '2020-01-01 00:15:00' );
			$second_last_slot = new DateTime( '2020-01-01 23:45:00' );
			$last_slot        = new DateTime( '2020-01-01 23:59:59' );

			do {
				$time = $current_slot->format( 'H:i:s' );
				?>
				<option <?php selected( $time, $end_time ); ?> value="<?php echo esc_attr( $time ); ?>"><?php echo esc_attr( $current_slot->format( 'H:i' ) ); ?></option>
				<?php
				if ( $current_slot == $second_last_slot ) {
					$current_slot->add( new DateInterval( 'PT14M59S' ) );
				} else {
					$current_slot->add( new DateInterval( 'PT15M' ) );
				}
			} while ( $current_slot <= $last_slot );
		}

		/**
		 * Return working hrs for given date for company or agent
		 *
		 * @param DateTime $date - given date.
		 * @param integer  $agent_id - agent id form whom working hrs to returned from.
		 * @return boolean
		 */
		public static function get_working_hrs_by_date( $date, $agent_id = 0 ) {

			$date = ( clone $date )->setTime( 0, 0 );

			// check agent leave on this date. Not applicable for company.
			if ( $agent_id ) {
				$holiday = WPSC_Holiday::get_holiday_by_date( $date, $agent_id );
				if ( $holiday ) {
					return false;
				}
			}

			// check for exception on this date.
			$exception = WPSC_Wh_Exception::get_exception_by_date( $date, $agent_id );
			if ( $exception ) {
				return array(
					'start_time' => $exception->start_time,
					'end_time'   => $exception->end_time,
				);
			}

			// check comapny holiday.
			if ( $agent_id == 0 ) {
				$holiday = WPSC_Holiday::get_holiday_by_date( $date, $agent_id );
				if ( $holiday ) {
					return false;
				}
			}

			// get working hrs for date.
			$working_hrs = self::get( $agent_id );
			$wh          = $working_hrs[ $date->format( 'N' ) ];

			// check whether it is off.
			if ( $wh->start_time == 'off' ) {
				return false;
			}

			// return working hrs.
			return array(
				'start_time' => $wh->start_time,
				'end_time'   => $wh->end_time,
			);
		}

		/**
		 * Get closest working hr for company or an agent
		 *
		 * @param DateTime $date - date from which closest working hrs to be given.
		 * @param integer  $agent_id - agent id for whom working hrs to be returned.
		 * @return array
		 */
		public static function get_closest_wh_by_date( $date, $agent_id = 0 ) {

			$tz   = wp_timezone();
			$date = clone $date;

			// check for given date.
			$wh = self::get_working_hrs_by_date( $date, $agent_id );
			if ( $wh ) {

				// calculate maximum start time for given date.
				$max_start = new DateTime( $date->format( 'Y-m-d' ) . ' ' . $wh['end_time'], $tz );
				if ( $wh['end_time'] == '23:59:59' ) {
					$max_start->sub( new DateInterval( 'PT14M' ) );
				} else {
					$max_start->sub( new DateInterval( 'PT15M' ) );
				}

				// return working hr if given date is less than maxstart time.
				if ( $date < $max_start ) {
					$start_time = new DateTime( $date->format( 'Y-m-d' ) . ' ' . $wh['start_time'], $tz );
					if ( $date > $start_time ) {
						$start_time = $date;
					}
					return array(
						'start_time' => $start_time,
						'end_time'   => new DateTime( $date->format( 'Y-m-d' ) . ' ' . $wh['end_time'], $tz ),
					);
				}
			}

			do {

				$date->add( new DateInterval( 'P1D' ) );
				$wh = self::get_working_hrs_by_date( $date, $agent_id );

				if ( $wh ) {
					return array(
						'start_time' => new DateTime( $date->format( 'Y-m-d' ) . ' ' . $wh['start_time'], $tz ),
						'end_time'   => new DateTime( $date->format( 'Y-m-d' ) . ' ' . $wh['end_time'], $tz ),
					);
				}
			} while ( true );
		}
	}
endif;

WPSC_Working_Hour::init();
