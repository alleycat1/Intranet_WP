<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Holiday' ) ) :

	final class WPSC_Holiday {

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
			add_action( 'wp_ajax_wpsc_get_holidays', array( __CLASS__, 'get_holidays' ) );
			add_action( 'wp_ajax_wpsc_get_company_holiday_actions', array( __CLASS__, 'get_company_holiday_actions' ) );
			add_action( 'wp_ajax_wpsc_set_company_holiday_actions', array( __CLASS__, 'set_company_holiday_actions' ) );
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
				'agent'        => array(
					'has_ref'          => true,
					'ref_class'        => 'wpsc_agent',
					'has_multiple_val' => false,
				),
				'holiday'      => array(
					'has_ref'          => true,
					'ref_class'        => 'datetime',
					'has_multiple_val' => false,
				),
				'is_recurring' => array(
					'has_ref'          => false,
					'ref_class'        => '',
					'has_multiple_val' => false,
				),
			);
			self::$schema = apply_filters( 'wpsc_holidays_schema', $schema );

			// Prevent modify.
			$prevent_modify       = array( 'id' );
			self::$prevent_modify = apply_filters( 'wpsc_holidays_prevent_modify', $prevent_modify );
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

				$holiday = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}psmsc_holidays WHERE id = " . $id, ARRAY_A );
				if ( ! is_array( $holiday ) ) {
					return;
				}

				foreach ( $holiday as $key => $val ) {
					$this->data[ $key ] = $val !== null ? $val : '';
				}

				self::$cache[ $id ] = $this;
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
				$wpdb->prefix . 'psmsc_holidays',
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
		 * @param array $data - insert data.
		 * @return WPSC_Holiday
		 */
		public static function insert( $data ) {

			global $wpdb;

			$success = $wpdb->insert(
				$wpdb->prefix . 'psmsc_holidays',
				$data
			);

			if ( ! $success ) {
				return false;
			}

			$working_hr = new WPSC_Holiday( $wpdb->insert_id );
			return $working_hr;
		}

		/**
		 * Delete record of given ID
		 *
		 * @param WPSC_Holiday $holiday - holiday object.
		 * @return boolean
		 */
		public static function destroy( $holiday ) {

			global $wpdb;

			$success = $wpdb->delete(
				$wpdb->prefix . 'psmsc_holidays',
				array( 'id' => $holiday->id )
			);

			if ( ! $success ) {
				return false;
			}

			unset( self::$cache[ $holiday->id ] );
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

			$sql   = 'SELECT SQL_CALC_FOUND_ROWS * FROM ' . $wpdb->prefix . 'psmsc_holidays ';
			$where = self::get_where( $filter );

			$filter['items_per_page'] = isset( $filter['items_per_page'] ) ? $filter['items_per_page'] : 0;
			$filter['page_no']        = isset( $filter['page_no'] ) ? $filter['page_no'] : 0;
			$filter['orderby']        = isset( $filter['orderby'] ) ? $filter['orderby'] : 'holiday';
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

				$ob   = new WPSC_Holiday();
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
		 * Load current class to reference classes
		 *
		 * @param array $classes - Associative array of class names indexed by its slug.
		 * @return array
		 */
		public static function load_ref_class( $classes ) {

			$classes['wpsc_holiday'] = array(
				'class'    => __CLASS__,
				'save-key' => 'id',
			);
			return $classes;
		}

		/**
		 * Get holidays settings
		 *
		 * @return void
		 */
		public static function get_holidays() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			// get non-recurring holidays.
			$non_recurring_holidays = array_map(
				fn( $holiday ) => $holiday->holiday->format( 'Y-m-d' ),
				self::find(
					array(
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'slug'    => 'agent',
								'compare' => '=',
								'val'     => 0,
							),
							array(
								'slug'    => 'is_recurring',
								'compare' => '=',
								'val'     => 0,
							),
						),
					)
				)['results']
			);

			// get recurring holidays.
			$recurring_holidays = array_map(
				fn( $holiday ) => $holiday->holiday->format( 'm-d' ),
				self::find(
					array(
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'slug'    => 'agent',
								'compare' => '=',
								'val'     => 0,
							),
							array(
								'slug'    => 'is_recurring',
								'compare' => '=',
								'val'     => 1,
							),
						),
					)
				)['results']
			);

			$locale = explode( '_', get_locale() );?>

			<div class="wpsc-dock-container">
				<?php
				printf(
					/* translators: Click here to see the documentation */
					esc_attr__( '%s to see the documentation!', 'supportcandy' ),
					'<a href="https://supportcandy.net/docs/working-hours/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
				);
				?>
			</div>
			<div id="wpsc-calendar"></div>
			<script>
				supportcandy.temp.holidayList = {
					'nonRecurring': <?php echo wp_json_encode( $non_recurring_holidays ); ?>,
					'recurring': <?php echo wp_json_encode( $recurring_holidays ); ?>
				};
				var calendarEl = document.getElementById('wpsc-calendar');
				var calendar = new FullCalendar.Calendar(calendarEl, {
					initialView: 'dayGridMonth',
					selectable: true,
					locale: '<?php echo esc_attr( $locale[0] ); ?>',
					dayCellDidMount: function(args) {

						// non-recurring
						var dateToCompare = args.date.toLocaleDateString('en-CA');
						if (jQuery.inArray(dateToCompare, supportcandy.temp.holidayList.nonRecurring) != -1) {
							jQuery(args.el).css('background-color', '#f0932b');
						}

						// recurring
						var strArr = dateToCompare.split('-');
						if (jQuery.inArray(strArr[1] + '-' + strArr[2], supportcandy.temp.holidayList.recurring) != -1) {
							jQuery(args.el).css('background-color', '#eb4d4b');
						}

					},
					select: function(info) {

						var start = info.start;
						var end = info.end;
						end.setDate(end.getDate()-1);

						var dateSelected = [];
						do {
							var d = start.toLocaleDateString('en-CA');
							dateSelected.push(d);
							start.setDate(parseInt(start.getDate())+1);
						} while (start <= end);

						wpsc_get_company_holiday_actions(dateSelected, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_company_holiday_actions' ) ); ?>');
					}
				}).render();
			</script>
			<?php
			wp_die();
		}

		/**
		 * Get company holiday actions
		 *
		 * @return void
		 */
		public static function get_company_holiday_actions() {

			if ( check_ajax_referer( 'wpsc_get_company_holiday_actions', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorized', 400 );
			}

			$date_selected = isset( $_POST['dateSelected'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['dateSelected'] ) ) ) : array();
			if ( ! $date_selected ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			$title     = esc_attr__( 'Add/Delete Holidays', 'supportcandy' );
			$unique_id = uniqid( 'wpsc_' );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-comp-holiday-actions">

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Action', 'supportcandy' ); ?></label>
					</div>
					<select class="<?php echo esc_attr( $unique_id ); ?>" name="holiday-action">
						<option value="add"><?php esc_attr_e( 'Add new holidays', 'supportcandy' ); ?></option>
						<option value="delete"><?php esc_attr_e( 'Delete existing holidays', 'supportcandy' ); ?></option>
					</select>
				</div>

				<div class="wpsc-input-group is-recurring">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Repeate every year', 'supportcandy' ); ?></label>
					</div>
					<select name="is-recurring">
						<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>

				<input type="hidden" name="action" value="wpsc_set_company_holiday_actions">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_company_holiday_actions' ) ); ?>">

			</form>
			<script>
				jQuery('.<?php echo esc_attr( $unique_id ); ?>').change(function(){
					if (jQuery(this).val() == 'add') {
						jQuery('.wpsc-input-group.is-recurring').show();
					} else {
						jQuery('.wpsc-input-group.is-recurring').hide();
					}
				});
			</script>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_company_holiday_actions(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);

			wp_send_json( $response, 200 );
		}

		/**
		 * Set company holiday actions
		 *
		 * @return void
		 */
		public static function set_company_holiday_actions() {

			if ( check_ajax_referer( 'wpsc_set_company_holiday_actions', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			global $wpdb;

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorized', 400 );
			}

			$date_selected = isset( $_POST['dateSelected'] ) ? sanitize_text_field( wp_unslash( $_POST['dateSelected'] ) ) : '';
			$date_selected = $date_selected ? array_filter( array_map( 'sanitize_text_field', explode( ',', $date_selected ) ) ) : array();
			if ( ! $date_selected ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			$action = isset( $_POST['holiday-action'] ) ? sanitize_text_field( wp_unslash( $_POST['holiday-action'] ) ) : '';
			if ( ! $action ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			$is_recurring = isset( $_POST['is-recurring'] ) ? intval( $_POST['is-recurring'] ) : '';
			if ( ! is_numeric( $is_recurring ) ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			foreach ( $date_selected as $date ) {

				$date = new DateTime( $date . ' 00:00:00' );

				// delete non-recurring record if exists.
				$wpdb->delete(
					$wpdb->prefix . 'psmsc_holidays',
					array(
						'holiday' => $date->format( 'Y-m-d H:i:s' ),
						'agent'   => 0,
					)
				);

				// delete recurring record if exists.
				$wpdb->query( "DELETE FROM {$wpdb->prefix}psmsc_holidays WHERE agent=0 AND DAYOFMONTH(holiday)=" . $date->format( 'd' ) . ' AND MONTH(holiday)=' . $date->format( 'm' ) . ' AND is_recurring=1' );

				// add record.
				if ( $action == 'add' ) {
					self::insert(
						array(
							'agent'        => 0,
							'holiday'      => $date->format( 'Y-m-d H:i:s' ),
							'is_recurring' => $is_recurring,
						)
					);
				}
			}

			// get non-recurring holidays.
			$non_recurring_holidays = array();
			$holidays               = self::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'agent',
							'compare' => '=',
							'val'     => 0,
						),
						array(
							'slug'    => 'is_recurring',
							'compare' => '=',
							'val'     => 0,
						),
					),
				)
			)['results'];
			foreach ( $holidays as $holiday ) {
				$non_recurring_holidays[] = $holiday->holiday->format( 'Y-m-d' );
			}

			// get recurring holidays.
			$recurring_holidays = array();
			$holidays           = self::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'agent',
							'compare' => '=',
							'val'     => 0,
						),
						array(
							'slug'    => 'is_recurring',
							'compare' => '=',
							'val'     => 1,
						),
					),
				)
			)['results'];
			foreach ( $holidays as $holiday ) {
				$recurring_holidays[] = $holiday->holiday->format( 'm-d' );
			}

			$response = array(
				'action'       => $action,
				'is_recurring' => $is_recurring,
				'holidayList'  => array(
					'nonRecurring' => $non_recurring_holidays,
					'recurring'    => $recurring_holidays,
				),
			);

			wp_send_json( $response, 200 );
		}

		/**
		 * Get holiday by date and agent id
		 *
		 * @param DateTime $date - datetime object.
		 * @param integer  $agent_id - agent id.
		 * @return WPSC_Holiday
		 */
		public static function get_holiday_by_date( $date, $agent_id = 0 ) {

			$holiday = self::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'agent',
							'compare' => '=',
							'val'     => $agent_id,
						),
						array(
							'slug'    => 'holiday',
							'compare' => '=',
							'val'     => $date->format( 'Y-m-d H:i:s' ),
						),
					),
				)
			);

			return $holiday['results'] ? $holiday['results'][0] : false;
		}
	}
endif;

WPSC_Holiday::init();
