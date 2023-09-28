<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Agent_Working_Hrs' ) ) :

	final class WPSC_Agent_Working_Hrs {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// section.
			add_action( 'wp_ajax_wpsc_get_agent_working_hrs', array( __CLASS__, 'get_working_hrs' ) );

			// get working hrs.
			add_action( 'wp_ajax_wpsc_get_agent_wh_hrs', array( __CLASS__, 'get_agent_wh_hrs' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_agent_wh_hrs', array( __CLASS__, 'get_agent_wh_hrs' ) );

			// set working hrs.
			add_action( 'wp_ajax_wpsc_set_agent_wh_hrs', array( __CLASS__, 'set_agent_wh_hrs' ) );
			add_action( 'wp_ajax_nopriv_wpsc_set_agent_wh_hrs', array( __CLASS__, 'set_agent_wh_hrs' ) );

			// get exceptions list.
			add_action( 'wp_ajax_wpsc_get_agent_wh_exceptions', array( __CLASS__, 'get_agent_wh_exceptions' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_agent_wh_exceptions', array( __CLASS__, 'get_agent_wh_exceptions' ) );

			// get add exceptions.
			add_action( 'wp_ajax_wpsc_get_add_agent_wh_exception', array( __CLASS__, 'get_add_agent_wh_exception' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_add_agent_wh_exception', array( __CLASS__, 'get_add_agent_wh_exception' ) );

			// set add exceptions.
			add_action( 'wp_ajax_wpsc_set_add_agent_wh_exception', array( __CLASS__, 'set_add_agent_wh_exception' ) );
			add_action( 'wp_ajax_nopriv_wpsc_set_add_agent_wh_exception', array( __CLASS__, 'set_add_agent_wh_exception' ) );

			// get edit exceptions.
			add_action( 'wp_ajax_wpsc_get_edit_agent_wh_exception', array( __CLASS__, 'get_edit_agent_wh_exception' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_edit_agent_wh_exception', array( __CLASS__, 'get_edit_agent_wh_exception' ) );

			// set edit exceptions.
			add_action( 'wp_ajax_wpsc_set_edit_agent_wh_exception', array( __CLASS__, 'set_edit_agent_wh_exception' ) );
			add_action( 'wp_ajax_nopriv_wpsc_set_edit_agent_wh_exception', array( __CLASS__, 'set_edit_agent_wh_exception' ) );

			// delete exception.
			add_action( 'wp_ajax_wpsc_delete_agent_wh_exception', array( __CLASS__, 'delete_agent_wh_exception' ) );
			add_action( 'wp_ajax_nopriv_wpsc_delete_agent_wh_exception', array( __CLASS__, 'delete_agent_wh_exception' ) );
		}

		/**
		 * Agent working hrs
		 *
		 * @return void
		 */
		public static function get_working_hrs() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$args  = array(
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
				),
				'items_per_page' => 1,
			);
			$agent = WPSC_Agent::find( $args )['results'][0];?>
			<div class="wpsc-setting-header">
				<h2><?php esc_attr_e( 'Working Hours', 'supportcandy' ); ?></h2>
			</div>
			<div class="wpsc-setting-filter-container">
				<div class="setting-filter-item" style="width: 40%;">
					<span class="label"><?php esc_attr_e( 'Agent', 'supportcandy' ); ?></span>
					<select class="wpsc-agent-search">
						<option value="<?php echo esc_attr( $agent->id ); ?>"><?php echo esc_attr( $agent->name ); ?></option>
					</select>
				</div>
			</div>
			<div class="wpsc-setting-tab-container" style="margin-top: 10px;">
				<button class="tab working-hrs active" onclick="wpsc_get_agent_wh_hrs();"><?php esc_attr_e( 'Working Hours', 'supportcandy' ); ?></button>
				<button class="tab exceptions" onclick="wpsc_get_agent_wh_exceptions();"><?php esc_attr_e( 'Exceptions', 'supportcandy' ); ?></button>
			</div>
			<div class="wpsc-setting-section-body"></div>
			<script>
				jQuery('select.wpsc-agent-search').selectWoo({
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
					placeholder: "",
				});
				jQuery('select.wpsc-agent-search').change(function(){
					var agent_id = jQuery(this).val();
					if (supportcandy.temp.agent_id === agent_id) return;
					supportcandy.temp.agent_id = agent_id;
					wpsc_get_agent_wh_hrs();
				});
				supportcandy.temp.agent_id = <?php echo esc_attr( $agent->id ); ?>;
			</script>
			<?php
			wp_die();
		}

		/**
		 * Get agent working hrs
		 *
		 * @return void
		 */
		public static function get_agent_wh_hrs() {

			if ( check_ajax_referer( 'general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$agent_id = isset( $_POST['agent_id'] ) ? intval( $_POST['agent_id'] ) : 0;
			if ( ! $agent_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$agent = new WPSC_Agent( $agent_id );
			if ( ! $agent->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$settings = get_option( 'wpsc-wh-settings', array() );

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				WPSC_Functions::is_site_admin() ||
				(
					$current_user->is_agent &&
					$settings['allow-agent-modify-wh'] &&
					$current_user->agent->id == $agent_id
				)
			) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$working_hrs = $agent->get_working_hrs();
			?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-agent-wh">
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
									<?php WPSC_Working_Hour::get_start_time_slots( $start_time ); ?>
								</select>
							</td>
							<td style="<?php echo esc_attr( $style ); ?>">-</td>
							<td style="<?php echo esc_attr( $style ); ?>">
								<select class="wpsc-wh-end-time" name="wh[<?php echo esc_attr( $i ); ?>][end_time]">
									<?php WPSC_Working_Hour::get_end_time_slots( $start_time, $end_time ); ?>
								</select>
							</td>
						</tr>
						<?php
					endfor;
				?>
				</table>
				<input type="hidden" name="action" value="wpsc_set_agent_wh_hrs">
				<input type="hidden" name="agent_id" value="<?php echo esc_attr( $agent->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_agent_wh_hrs' ) ); ?>">
			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_agent_wh_hrs();">
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
				supportcandy.temp.end_times = end_times;

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
		 * Set agent working hrs
		 *
		 * @return void
		 */
		public static function set_agent_wh_hrs() {

			if ( check_ajax_referer( 'wpsc_set_agent_wh_hrs', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$agent_id = isset( $_POST['agent_id'] ) ? intval( $_POST['agent_id'] ) : 0;
			if ( ! $agent_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$agent = new WPSC_Agent( $agent_id );
			if ( ! $agent->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$settings = get_option( 'wpsc-wh-settings', array() );

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				WPSC_Functions::is_site_admin() ||
				(
					$current_user->is_agent &&
					$settings['allow-agent-modify-wh'] &&
					$current_user->agent->id == $agent_id
				)
			) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			// working hrs.
			$wh = isset( $_POST['wh'] ) ? map_deep( wp_unslash( $_POST['wh'] ), 'sanitize_text_field' ) : array();
			if ( ! $wh ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			// set working hrs.
			WPSC_Working_Hour::set( $wh, $agent_id );
			wp_die();
		}

		/**
		 * Get agent workinh hour exceptions
		 *
		 * @return void
		 */
		public static function get_agent_wh_exceptions() {

			if ( check_ajax_referer( 'general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$agent_id = isset( $_POST['agent_id'] ) ? intval( $_POST['agent_id'] ) : 0;
			if ( ! $agent_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$agent = new WPSC_Agent( $agent_id );
			if ( ! $agent->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$settings = get_option( 'wpsc-wh-settings', array() );

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				WPSC_Functions::is_site_admin() ||
				(
					$current_user->is_agent &&
					$settings['allow-agent-modify-wh'] &&
					$current_user->agent->id == $agent_id
				)
			) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			// ui source.
			$source = isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : '';

			$exceptions = $agent->get_wh_exceptions();
			$unique_id  = uniqid( 'wpsc_' );
			?>

			<div class="wpsc-dock-container">
				<?php
				printf(
					/* translators: Click here to see the documentation */
					esc_attr__( '%s to see the documentation!', 'supportcandy' ),
					'<a href="https://supportcandy.net/docs/working-hours/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
				);
				?>
			</div>
			<table class="wpsc-setting-tbl <?php echo esc_attr( $unique_id ); ?>">
				<thead>
					<tr>
						<th><?php esc_attr_e( 'Title', 'supportcandy' ); ?></th>
						<th><?php esc_attr_e( 'Date', 'supportcandy' ); ?></th>
						<th><?php esc_attr_e( 'Schedule', 'supportcandy' ); ?></th>
						<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $exceptions as $exception ) {
						?>
						<tr data-id="<?php echo esc_attr( $exception->id ); ?>">
							<td><?php echo esc_attr( $exception->title ); ?></td>
							<td><?php echo esc_attr( $exception->exception_date->format( 'F d, Y' ) ); ?></td>
							<td>
								<?php
									$start_time = explode( ':', $exception->start_time );
									$start_time = $start_time[0] . ':' . $start_time[1];
									$end_time   = explode( ':', $exception->end_time );
									$end_time   = $end_time[0] . ':' . $end_time[1];
									/* translators: %1$s: start time, %2$s: end time e.g. 04:00 - 05:00 */
									printf( esc_attr__( '%1$s - %2$s', 'supportcandy' ), esc_attr( $start_time ), esc_attr( $end_time ) );
								?>
							</td>
							<td>
								<a class="wpsc-link"><span class="edit <?php echo esc_attr( $unique_id ); ?>"><?php esc_attr_e( 'Edit', 'wpsc-cr' ); ?></span></a> | 
								<a class="wpsc-link"><span class="delete <?php echo esc_attr( $unique_id ); ?>"><?php esc_attr_e( 'Delete', 'supportcandy' ); ?></span></a>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<script>
				var source = '<?php echo esc_attr( $source ); ?>';
				// Add datatable
				jQuery('table.<?php echo esc_attr( $unique_id ); ?>').DataTable({
					ordering: false,
					pageLength: 20,
					bLengthChange: false,
					columnDefs: [ 
						{ targets: -1, searchable: false },
						{ targets: '_all', className: 'dt-left' }
					],
					dom: 'Bfrtip',
					buttons: [
						{
							text: '<?php echo esc_attr( wpsc__( 'Add new', 'supportcandy' ) ); ?>',
							className: 'wpsc-button small primary',
							action: function ( e, dt, node, config ) {
								if (source === 'agent-profile') {
									jQuery('.wpsc-section-container').html(supportcandy.loader_html);
								} else {
									jQuery('.wpsc-setting-section-body').html(supportcandy.loader_html);
								}
								var data = { 
									action: 'wpsc_get_add_agent_wh_exception', 
									agent_id: supportcandy.temp.agent_id,
									_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_add_agent_wh_exception' ) ); ?>'
								};
								jQuery.post(supportcandy.ajax_url, data, function (response) {
									if (source === 'agent-profile') {
										jQuery('.wpsc-section-container').html(response);
									} else {
										jQuery('.wpsc-setting-section-body').html(response);
									}
								});
							}
						}
					],
					language: supportcandy.translations.datatables
				});
				// Edit.
				jQuery('span.edit.<?php echo esc_attr( $unique_id ); ?>').click(function(){

					var exception_id = jQuery(this).closest('tr').data('id');
					if (source === 'agent-profile') {
						jQuery('.wpsc-section-container').html(supportcandy.loader_html);
					} else {
						jQuery('.wpsc-setting-section-body').html(supportcandy.loader_html);
					}
					var data = { 
						action: 'wpsc_get_edit_agent_wh_exception', 
						agent_id: supportcandy.temp.agent_id,
						exception_id,
						_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_agent_wh_exception' ) ); ?>'
					};
					jQuery.post(supportcandy.ajax_url, data, function (res) {
						if (source === 'agent-profile') {
							jQuery('.wpsc-section-container').html(res);
						} else {
							jQuery('.wpsc-setting-section-body').html(res);
						}
					});
				});
				// Delete.
				jQuery('span.delete.<?php echo esc_attr( $unique_id ); ?>').click(function(){

					var flag = confirm(supportcandy.translations.confirm);
					if (!flag) return;
					var exception_id = jQuery(this).closest('tr').data('id');
					if (source === 'agent-profile') {
						jQuery('.wpsc-section-container').html(supportcandy.loader_html);
					} else {
						jQuery('.wpsc-setting-section-body').html(supportcandy.loader_html);
					}
					var data = { 
						action: 'wpsc_delete_agent_wh_exception', 
						agent_id: supportcandy.temp.agent_id,
						exception_id,
						_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_delete_agent_wh_exception' ) ); ?>'
					};
					jQuery.post(supportcandy.ajax_url, data, function (res) {
						jQuery('button.tab.exceptions').trigger('click');
					});
				});
				supportcandy.temp.uniqueId = '<?php echo esc_attr( $unique_id ); ?>';
			</script>
			<?php
			wp_die();
		}

		/**
		 * Get add agent working hour exception
		 *
		 * @return void
		 */
		public static function get_add_agent_wh_exception() {

			if ( check_ajax_referer( 'wpsc_get_add_agent_wh_exception', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$agent_id = isset( $_POST['agent_id'] ) ? intval( $_POST['agent_id'] ) : 0;
			if ( ! $agent_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$agent = new WPSC_Agent( $agent_id );
			if ( ! $agent->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$settings = get_option( 'wpsc-wh-settings', array() );

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				WPSC_Functions::is_site_admin() ||
				(
					$current_user->is_agent &&
					$settings['allow-agent-modify-wh'] &&
					$current_user->agent->id == $agent_id
				)
			) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$unique_id = uniqid( 'wpsc_' );
			?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-add-agent-exception">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Title', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<input name="title" type="text" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Date', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<input class="date exception_date <?php echo esc_attr( $unique_id ); ?>" name="exception_date" type="text" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Schedule', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-working-hrs">
						<tr>
							<td style="padding: 0 !important;">
								<select class="wpsc-wh-start-time" name="start_time">
									<?php WPSC_Wh_Exception::get_start_time_slots( '00:00:00' ); ?>
								</select>
							</td>
							<td style="text-align: center; width:45px; padding: 0 !important;">-</td>
							<td style="padding: 0 !important;">
								<select class="wpsc-wh-end-time" name="end_time">
									<?php WPSC_Working_Hour::get_end_time_slots( '00:00:00', '00:15:00' ); ?>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<script type="text/javascript">
					var end_times = [];
					jQuery('.date.<?php echo esc_attr( $unique_id ); ?>').flatpickr({minDate:new Date, disableMobile:true});
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
					supportcandy.temp.end_times = end_times;

					// Change event
					jQuery('.wpsc-wh-start-time').change(function(){
						var start_time = jQuery(this).val();
						var tempArr = start_time.split(":");
						var startDate = new Date(2020, 0, 1, tempArr[0], tempArr[1], tempArr[2]);
						var cmbEndTime = jQuery('.wpsc-wh-end-time');
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
				<input type="hidden" name="action" value="wpsc_set_add_agent_wh_exception">
				<input type="hidden" name="agent_id" value="<?php echo esc_attr( $agent->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_agent_wh_exception' ) ); ?>">
			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_add_agent_wh_exception(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
				</button>
				<button 
					class="wpsc-button normal secondary"
					onclick="jQuery('button.tab.exceptions').trigger('click');">
					<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
				</button>
			</div>
			<?php

			wp_die();
		}

		/**
		 * Save new agent working hour exception
		 *
		 * @return void
		 */
		public static function set_add_agent_wh_exception() {

			if ( check_ajax_referer( 'wpsc_set_add_agent_wh_exception', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$agent_id = isset( $_POST['agent_id'] ) ? intval( $_POST['agent_id'] ) : 0;
			if ( ! $agent_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$agent = new WPSC_Agent( $agent_id );
			if ( ! $agent->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$settings = get_option( 'wpsc-wh-settings', array() );

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				WPSC_Functions::is_site_admin() ||
				(
					$current_user->is_agent &&
					$settings['allow-agent-modify-wh'] &&
					$current_user->agent->id == $agent_id
				)
			) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
			if ( ! $title ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			// start date.
			$exception_date = isset( $_POST['exception_date'] ) ? sanitize_text_field( wp_unslash( $_POST['exception_date'] ) ) : '';
			if ( ! $exception_date ) {
				wp_send_json_error( 'Bad request', 400 );
			}
			$flag = preg_match( '/\w{4}-\w{2}-\w{2}/', $exception_date );
			if ( $flag !== 1 ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			// start time.
			$start_time = isset( $_POST['start_time'] ) ? sanitize_text_field( wp_unslash( $_POST['start_time'] ) ) : '';
			if ( ! $start_time ) {
				wp_send_json_error( 'Bad request', 400 );
			}
			$flag = preg_match( '/\w{2}:\w{2}:00/', $start_time );
			if ( $flag !== 1 ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			// end time.
			$default_end_time = new DateTime( '2020-01-01 ' . $start_time );
			$default_end_time->add( new DateInterval( 'PT15M' ) );
			$end_time = isset( $_POST['end_time'] ) ? sanitize_text_field( wp_unslash( $_POST['end_time'] ) ) : $default_end_time->format( 'H:i:s' );
			if ( ! $end_time ) {
				wp_send_json_error( 'Bad request', 400 );
			}
			$flag = preg_match( '/\w{2}:\w{2}:00/', $end_time );
			if ( $flag !== 1 ) {
				wp_send_json_error( '010', 'Bad request', 400 );
			}

			// delete existing record for the date if exists.
			$exceptions = WPSC_Wh_Exception::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'agent',
							'compare' => '=',
							'val'     => $agent_id,
						),
						array(
							'slug'    => 'exception_date',
							'compare' => '=',
							'val'     => $exception_date . ' 00:00:00',
						),
					),
				)
			)['results'];
			if ( $exceptions ) {
				WPSC_Wh_Exception::destroy( $exceptions[0] );
			}

			WPSC_Wh_Exception::insert(
				array(
					'agent'          => $agent_id,
					'title'          => $title,
					'exception_date' => $exception_date . ' 00:00:00',
					'start_time'     => $start_time,
					'end_time'       => $end_time,
				)
			);

			wp_die();
		}

		/**
		 * Edit agent working hour exceptions
		 *
		 * @return void
		 */
		public static function get_edit_agent_wh_exception() {

			if ( check_ajax_referer( 'wpsc_get_edit_agent_wh_exception', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$agent_id = isset( $_POST['agent_id'] ) ? intval( $_POST['agent_id'] ) : 0;
			if ( ! $agent_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$agent = new WPSC_Agent( $agent_id );
			if ( ! $agent->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$settings = get_option( 'wpsc-wh-settings', array() );

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				WPSC_Functions::is_site_admin() ||
				(
					$current_user->is_agent &&
					$settings['allow-agent-modify-wh'] &&
					$current_user->agent->id == $agent_id
				)
			) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['exception_id'] ) ? intval( $_POST['exception_id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$exception = new WPSC_Wh_Exception( $id );
			if ( ! $exception->id ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$unique_id = uniqid( 'wpsc_' );
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-agent-exception">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Title', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<input 
						name="title" 
						type="text" 
						value="<?php echo esc_attr( $exception->title ); ?>" 
						autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Date', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<input class="date exception_date <?php echo esc_attr( $unique_id ); ?>" value="<?php echo esc_attr( $exception->exception_date->format( 'Y-m-d' ) ); ?>" name="exception_date" type="text" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Schedule', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-working-hrs">
						<tr>
							<td style="padding: 0 !important;">
								<select class="wpsc-wh-start-time" name="start_time">
									<?php WPSC_Wh_Exception::get_start_time_slots( $exception->start_time ); ?>
								</select>
							</td>
							<td style="text-align: center; width:45px; padding: 0 !important;">-</td>
							<td style="padding: 0 !important;">
								<select class="wpsc-wh-end-time" name="end_time">
									<?php WPSC_Working_Hour::get_end_time_slots( $exception->start_time, $exception->end_time ); ?>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<script>
					var end_times = [];
					jQuery('.date.<?php echo esc_attr( $unique_id ); ?>').flatpickr();
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
					supportcandy.temp.end_times = end_times;

					// Change event
					jQuery('.wpsc-wh-start-time').change(function(){
						var start_time = jQuery(this).val();
						var tempArr = start_time.split(":");
						var startDate = new Date(2020, 0, 1, tempArr[0], tempArr[1], tempArr[2]);
						var cmbEndTime = jQuery('.wpsc-wh-end-time');
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
				<input type="hidden" name="action" value="wpsc_set_edit_agent_wh_exception">
				<input type="hidden" name="exception_id" value="<?php echo esc_attr( $exception->id ); ?>">
				<input type="hidden" name="agent_id" value="<?php echo esc_attr( $agent->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_agent_wh_exception' ) ); ?>">
			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_edit_agent_wh_exception(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
				</button>
				<button 
					class="wpsc-button normal secondary"
					onclick="jQuery('button.tab.exceptions').trigger('click');">
					<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
				</button>
			</div>
			<?php

			wp_die();
		}

		/**
		 * Update agent working hour exceptions
		 *
		 * @return void
		 */
		public static function set_edit_agent_wh_exception() {

			if ( check_ajax_referer( 'wpsc_set_edit_agent_wh_exception', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$agent_id = isset( $_POST['agent_id'] ) ? intval( $_POST['agent_id'] ) : 0;
			if ( ! $agent_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$agent = new WPSC_Agent( $agent_id );
			if ( ! $agent->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$settings = get_option( 'wpsc-wh-settings', array() );

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				WPSC_Functions::is_site_admin() ||
				(
					$current_user->is_agent &&
					$settings['allow-agent-modify-wh'] &&
					$current_user->agent->id == $agent_id
				)
			) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['exception_id'] ) ? intval( $_POST['exception_id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$exception = new WPSC_Wh_Exception( $id );
			if ( ! $exception->id ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$title = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
			if ( ! $title ) {
				wp_send_json_error( 'Bad request', 400 );
			}
			$exception->title = $title;

			// start date.
			$exception_date = isset( $_POST['exception_date'] ) ? sanitize_text_field( wp_unslash( $_POST['exception_date'] ) ) : '';
			if ( ! $exception_date ) {
				wp_send_json_error( 'Bad request', 400 );
			}
			$flag = preg_match( '/\w{4}-\w{2}-\w{2}/', $exception_date );
			if ( $flag !== 1 ) {
				wp_send_json_error( 'Bad request', 400 );
			}
			$exception->exception_date = $exception_date . ' 00:00:00';

			// start time.
			$start_time = isset( $_POST['start_time'] ) ? sanitize_text_field( wp_unslash( $_POST['start_time'] ) ) : '';
			if ( ! $start_time ) {
				wp_send_json_error( 'Bad request', 400 );
			}
			$flag = preg_match( '/\w{2}:\w{2}:00/', $start_time );
			if ( $flag !== 1 ) {
				wp_send_json_error( 'Bad request', 400 );
			}
			$exception->start_time = $start_time;

			// end time.
			$default_end_time = new DateTime( '2020-01-01 ' . $start_time );
			$default_end_time->add( new DateInterval( 'PT15M' ) );
			$end_time = isset( $_POST['end_time'] ) ? sanitize_text_field( wp_unslash( $_POST['end_time'] ) ) : $default_end_time->format( 'H:i:s' );
			if ( ! $end_time ) {
				wp_send_json_error( 'Bad request', 400 );
			}
			$flag = preg_match( '/\w{2}:\w{2}:00/', $end_time );
			if ( $flag !== 1 ) {
				wp_send_json_error( 'Bad request', 400 );
			}
			$exception->end_time = $end_time;

			$exception->save();
			wp_die();
		}

		/**
		 * Delete agent working hour exception
		 */
		public static function delete_agent_wh_exception() {

			if ( check_ajax_referer( 'wpsc_delete_agent_wh_exception', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$agent_id = isset( $_POST['agent_id'] ) ? intval( $_POST['agent_id'] ) : 0;
			if ( ! $agent_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$agent = new WPSC_Agent( $agent_id );
			if ( ! $agent->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$settings = get_option( 'wpsc-wh-settings', array() );

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				WPSC_Functions::is_site_admin() ||
				(
					$current_user->is_agent &&
					$settings['allow-agent-modify-wh'] &&
					$current_user->agent->id == $agent_id
				)
			) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['exception_id'] ) ? intval( $_POST['exception_id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$exception = new WPSC_Wh_Exception( $id );
			if ( ! $exception->id ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			WPSC_Wh_Exception::destroy( $exception );
			wp_die();
		}
	}
endif;

WPSC_Agent_Working_Hrs::init();
