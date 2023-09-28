<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Agent_Leaves' ) ) :

	final class WPSC_Agent_Leaves {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// listing.
			add_action( 'wp_ajax_wpsc_get_agent_leaves', array( __CLASS__, 'get_agent_leaves' ) );
			add_action( 'wp_ajax_wpsc_get_agent_leave_events', array( __CLASS__, 'get_agent_leave_events' ) );

			// add new.
			add_action( 'wp_ajax_wpsc_get_add_agent_leaves', array( __CLASS__, 'get_add_agent_leaves' ) );
			add_action( 'wp_ajax_wpsc_set_add_agent_leaves', array( __CLASS__, 'set_add_agent_leaves' ) );

			// delete.
			add_action( 'wp_ajax_wpsc_delete_agent_leave', array( __CLASS__, 'delete_agent_leave' ) );
		}

		/**
		 * Get agent leaves list
		 *
		 * @return void
		 */
		public static function get_agent_leaves() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$locale = explode( '_', get_locale() );?>

			<div class="wpsc-setting-header"><h2><?php esc_attr_e( 'Leaves', 'supportcandy' ); ?></h2></div>
			<div class="wpsc-setting-section-body">
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
					var calendarEl = document.getElementById('wpsc-calendar');
					var calendar = new FullCalendar.Calendar(calendarEl, {
						headerToolbar: {
							left: 'prev,next today',
							center: 'title',
							right: 'dayGridMonth,timeGridWeek,timeGridDay'
						},
						selectable: true,
						locale: '<?php echo esc_attr( $locale[0] ); ?>',
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

							wpsc_get_add_agent_leaves(dateSelected, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_add_agent_leaves' ) ); ?>');
						},
						events: function(info, successCallback, failureCallback) {

							var data = {
								action: 'wpsc_get_agent_leave_events',
								start: info.start.toLocaleDateString('en-CA'),
								end: info.end.toLocaleDateString('en-CA'),
								_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_agent_leave_events' ) ); ?>'
							};
							jQuery.post(supportcandy.ajax_url, data, function (response) {
								successCallback(response);
							});

						},
						eventClick: function(info) {
							wpsc_delete_agent_leave(info.event.id, '<?php echo esc_attr( wp_create_nonce( 'wpsc_delete_agent_leave' ) ); ?>' );
						}
					});
					calendar.render();

					// ADD translation for confirmation window of delete event
					supportcandy.translations.deleteLeaveConfirmation = '<?php esc_attr_e( 'Are you sure to delete this leave?', 'supportcandy' ); ?>';

				</script>
			</div>
			<?php
			wp_die();
		}

		/**
		 * Load agent leave events
		 *
		 * @return void
		 */
		public static function get_agent_leave_events() {

			if ( check_ajax_referer( 'wpsc_get_agent_leave_events', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			global $wpdb;

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$start_date = isset( $_POST['start'] ) ? sanitize_text_field( wp_unslash( $_POST['start'] ) ) : '';
			if ( ! $start_date ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}
			$start_date = new DateTime( $start_date );

			$end_date = isset( $_POST['end'] ) ? sanitize_text_field( wp_unslash( $_POST['end'] ) ) : '';
			if ( ! $end_date ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}
			$end_date = new DateTime( $end_date );

			$response = array();

			// non-recurring.
			$holidays = WPSC_Holiday::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'agent',
							'compare' => '>',
							'val'     => 0,
						),
						array(
							'slug'    => 'is_recurring',
							'compare' => '=',
							'val'     => 0,
						),
						array(
							'slug'    => 'holiday',
							'compare' => 'BETWEEN',
							'val'     => array(
								$start_date->format( 'Y-m-d H:i:s' ),
								$end_date->format( 'Y-m-d H:i:s' ),
							),
						),
					),
				)
			)['results'];
			foreach ( $holidays as $holiday ) {
				$response[] = array(
					'id'        => $holiday->id,
					'title'     => $holiday->agent->name,
					'start'     => $holiday->holiday->format( 'Y-m-d' ),
					'color'     => '#f0932b',
					'textColor' => '#ffffff',
				);
			}

			// recurring.
			$sql     = 'SELECT * FROM ' . $wpdb->prefix . 'psmsc_holidays WHERE ';
			$sql    .= 'is_recurring=1 AND agent > 0 AND ';
			$sql    .= "DATE_FORMAT(holiday, '%m-%d') BETWEEN '" . $start_date->format( 'm-d' ) . "' and '" . $end_date->format( 'm-d' ) . "'";
			$results = $wpdb->get_results( $sql );
			foreach ( $results as $holiday ) {

				$holiday = new WPSC_Holiday( $holiday->id );

				$start = '';
				if ( intval( $start_date->format( 'Y' ) ) == intval( $end_date->format( 'Y' ) ) ) {
					$start .= $start_date->format( 'Y' ) . '-' . $holiday->holiday->format( 'm-d' );
				} else {
					if ( $holiday->holiday->format( 'm' ) == '12' ) {
						$start .= $start_date->format( 'Y' ) . '-' . $holiday->holiday->format( 'm-d' );
					} else {
						$start .= $end_date->format( 'Y' ) . '-' . $holiday->holiday->format( 'm-d' );
					}
				}

				$response[] = array(
					'id'        => $holiday->id,
					'title'     => $holiday->agent->name,
					'start'     => $start,
					'color'     => '#eb4d4b',
					'textColor' => '#ffffff',
				);
			}

			wp_send_json( $response, 200 );
		}

		/**
		 * Add agent leaves modal
		 *
		 * @return void
		 */
		public static function get_add_agent_leaves() {

			if ( check_ajax_referer( 'wpsc_get_add_agent_leaves', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$date_selected = isset( $_POST['dateSelected'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['dateSelected'] ) ) ) : array();
			if ( ! $date_selected ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			$title     = esc_attr( wpsc__( 'Add new', 'supportcandy' ) );
			$unique_id = uniqid( 'wpsc_' );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-agent-holiday-actions">

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Agents', 'supportcandy' ); ?></label>
					</div>
					<select class="<?php echo esc_attr( $unique_id ); ?>" name="agents[]" multiple></select>
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

				<input type="hidden" name="action" value="wpsc_set_add_agent_leaves">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_agent_leaves' ) ); ?>">

			</form>
			<script>
				jQuery('select.<?php echo esc_attr( $unique_id ); ?>').selectWoo({
					ajax: {
						url: supportcandy.ajax_url,
						dataType: 'json',
						delay: 250,
						data: function (params) {
							return {
								q: params.term, // search term
								page: params.page,
								action: 'wpsc_agent_autocomplete_admin_access',
								_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_agent_autocomplete_admin_access' ) ); ?>',
								isMultiple: 1,
								isAgentgroup: 0
							};
						},
						processResults: function (data, params) {
							var terms = [];
							if ( data ) {
								jQuery.each( data, function( id, text ) {
									terms.push( { id: text.id, text: text.title } );
								});
							}
							return {
								results: terms
							};
						},
						cache: true
					},
					escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
					minimumInputLength: 1,
					allowClear: false,
				});
			</script>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_add_agent_leaves(this);">
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
		 * Set add agent leaves
		 *
		 * @return void
		 */
		public static function set_add_agent_leaves() {

			if ( check_ajax_referer( 'wpsc_set_add_agent_leaves', '_ajax_nonce', false ) != 1 ) {
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

			$agents = isset( $_POST['agents'] ) ? array_filter( array_map( 'intval', wp_unslash( $_POST['agents'] ) ) ) : array();
			if ( ! $agents ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			$is_recurring = isset( $_POST['is-recurring'] ) ? intval( $_POST['is-recurring'] ) : '';
			if ( ! is_numeric( $is_recurring ) ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			foreach ( $date_selected as $date ) {

				$date = new DateTime( $date . ' 00:00:00' );

				// delete non-recurring record if exists.
				$sql  = "DELETE FROM {$wpdb->prefix}psmsc_holidays WHERE ";
				$sql .= 'agent IN(' . implode( ',', $agents ) . ') AND ';
				$sql .= 'is_recurring = 0 AND ';
				$sql .= "holiday = '" . $date->format( 'Y-m-d H:i:s' ) . "'";
				$wpdb->query( $sql );

				// delete recurring record if exists.
				$sql  = "DELETE FROM {$wpdb->prefix}psmsc_holidays WHERE ";
				$sql .= 'agent IN(' . implode( ',', $agents ) . ') AND ';
				$sql .= 'is_recurring = 1 AND ';
				$sql .= "DATE_FORMAT(holiday, '%m-%d') = '" . $date->format( 'm-d' ) . "'";
				$wpdb->query( $sql );

				// add record.
				foreach ( $agents as $agent ) {
					WPSC_Holiday::insert(
						array(
							'agent'        => $agent,
							'holiday'      => $date->format( 'Y-m-d H:i:s' ),
							'is_recurring' => $is_recurring,
						)
					);
				}
			}

			wp_die();
		}

		/**
		 * Delete agent leave
		 *
		 * @return void
		 */
		public static function delete_agent_leave() {

			if ( check_ajax_referer( 'wpsc_delete_agent_leave', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorized', 400 );
			}

			$holiday_id = isset( $_POST['holidayId'] ) ? intval( $_POST['holidayId'] ) : 0;
			if ( ! $holiday_id ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			$holiday = new WPSC_Holiday( $holiday_id );
			if ( ! $holiday->id ) {
				wp_send_json_error( 'Bad Request', 401 );
			}

			WPSC_Holiday::destroy( $holiday );
			wp_die();
		}
	}
endif;

WPSC_Agent_Leaves::init();
