<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_ITW_Assigned_Agents' ) ) :

	final class WPSC_ITW_Assigned_Agents {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// get edit assigned agents.
			add_action( 'wp_ajax_wpsc_it_get_edit_assigned_agents', array( __CLASS__, 'it_get_edit_assigned_agents' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_get_edit_assigned_agents', array( __CLASS__, 'it_get_edit_assigned_agents' ) );
			add_action( 'wp_ajax_wpsc_it_set_edit_assigned_agents', array( __CLASS__, 'set_edit_assigned_agents' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_set_edit_assigned_agents', array( __CLASS__, 'set_edit_assigned_agents' ) );

			// assigned agents.
			add_action( 'wp_ajax_wpsc_get_tw_agents', array( __CLASS__, 'get_tw_agents' ) );
			add_action( 'wp_ajax_wpsc_set_tw_agents', array( __CLASS__, 'set_tw_agents' ) );

			// agent autocomplete assign cap access check.
			add_action( 'wp_ajax_wpsc_agent_autocomplete_itw_assign', array( __CLASS__, 'agent_autocomplete_itw_assign' ) );
			add_action( 'wp_ajax_nopriv_wpsc_agent_autocomplete_itw_assign', array( __CLASS__, 'agent_autocomplete_itw_assign' ) );
		}

		/**
		 * Prints body of current widget
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @param array       $settings - widget settings.
		 * @return void
		 */
		public static function print_widget( $ticket, $settings ) {

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				(
					(
						WPSC_Individual_Ticket::$view_profile == 'customer' ||
						$ticket->customer->id == $current_user->customer->id
					) &&
					$settings['allow-customer']
				) ||
				( WPSC_Individual_Ticket::$view_profile == 'agent' && in_array( $current_user->agent->role, $settings['allowed-agent-roles'] ) )
			) ) {
				return;
			}?>

			<div class="wpsc-it-widget wpsc-itw-assignee">
				<div class="wpsc-widget-header">
					<h2>
						<?php
						$settings_title = $settings['title'] ? WPSC_Translations::get( 'wpsc-twt-assignee', stripslashes( $settings['title'] ) ) : stripslashes( $settings['title'] );
						echo esc_attr( $settings_title )
						?>
					</h2>
					<?php
					if ( $ticket->is_active && WPSC_Individual_Ticket::$view_profile == 'agent' && WPSC_Individual_Ticket::has_ticket_cap( 'aa' ) ) :
						?>
						<span onclick="wpsc_it_get_edit_assigned_agents(<?php echo esc_attr( $ticket->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_it_get_edit_assigned_agents' ) ); ?>')"><?php WPSC_Icons::get( 'edit' ); ?></span>
						<?php
					endif
					?>
				</div>
				<div class="wpsc-widget-body">
					<?php
					$cf = WPSC_Custom_Field::get_cf_by_slug( 'assigned_agent' );
					$cf->type::print_widget_ticket_field_val( $cf, $ticket );
					do_action( 'wpsc_itw_assigned_agents', $ticket )
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Get edit assign agents
		 *
		 * @return void
		 */
		public static function it_get_edit_assigned_agents() {

			if ( check_ajax_referer( 'wpsc_it_get_edit_assigned_agents', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'aa' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			$ticket = WPSC_Individual_Ticket::$ticket;

			$widgets = get_option( 'wpsc-ticket-widget' );
			$title   = $widgets['assignee']['title'];

			ob_start();
			$unique_id = uniqid()
			?>
			<form action="#" onsubmit="return false;" class="change-assignee <?php echo esc_attr( $unique_id ); ?>">
				<div class="wpsc-input-group" style="flex-direction:row; flex-wrap:nowrap;">
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for=""><?php esc_attr_e( 'Filter by', 'supportcandy' ); ?></label>
						</div>
						<select class="agent-filter-by">
							<?php
							$filter_by = apply_filters(
								'wpsc_assignee_filter_by',
								array(
									'all' => esc_attr__( 'All', 'supportcandy' ),
								)
							);
							foreach ( $filter_by as $key => $label ) :
								?>
								<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $label ); ?></option>
								<?php
							endforeach
							?>
						</select>
					</div>
					<div style="margin:5px;"></div>
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for=""><?php esc_attr_e( 'Sort by', 'supportcandy' ); ?></label>
						</div>
						<select class="agent-sort-by">
							<option value="workload"><?php esc_attr_e( 'Workload', 'supportcandy' ); ?></option>
							<option value="name"><?php esc_attr_e( 'Name', 'supportcandy' ); ?></option>
						</select>
					</div>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Select agent(s)', 'supportcandy' ); ?></label>
					</div>
					<select class="<?php echo esc_attr( $unique_id ); ?>" multiple name="assignee[]">
						<?php
						$assignees = $ticket->assigned_agent;
						foreach ( $assignees as $assignee ) :
							?>
							<option selected value="<?php echo esc_attr( $assignee->id ); ?>"><?php echo esc_attr( $assignee->name ); ?></option>
							<?php
						endforeach
						?>
					</select>
					<script>
						jQuery('select.<?php echo esc_attr( $unique_id ); ?>').selectWoo({
							ajax: {
								url: supportcandy.ajax_url,
								dataType: 'json',
								delay: 250,
								data: function (params) {
									return {
										q: params.term, // search term
										action: 'wpsc_agent_autocomplete_itw_assign',
										_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_agent_autocomplete_itw_assign' ) ); ?>',
										filter_by: jQuery('select.agent-filter-by').val(),
										sort_by: jQuery('select.agent-sort-by').val(),
										isMultiple: 1,
										ticket_id: <?php echo esc_attr( $ticket->id ); ?>
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
							minimumInputLength: 0,
							allowClear: false,
							placeholder: ""
						});
					</script>
				</div>
				<?php do_action( 'wpsc_get_edit_assigned_agent_body', $ticket ); ?>
				<input type="hidden" name="action" value="wpsc_it_set_edit_assigned_agents">
				<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_it_set_edit_assigned_agents' ) ); ?>">
			</form>
			<?php
			do_action( 'wpsc_get_edit_assigned_agent_footer' );
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_it_set_edit_assigned_agents(this, <?php echo esc_attr( $ticket->id ); ?>, '<?php echo esc_attr( $unique_id ); ?>');">
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
			wp_send_json( $response );
		}

		/**
		 * Change assigned agent
		 *
		 * @return void
		 */
		public static function set_edit_assigned_agents() {

			if ( check_ajax_referer( 'wpsc_it_set_edit_assigned_agents', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'aa' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			$ticket = WPSC_Individual_Ticket::$ticket;
			if ( ! $ticket->is_active ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$prev    = $ticket->assigned_agent;
			$new_ids = isset( $_POST['assignee'] ) ? array_filter( array_map( 'intval', $_POST['assignee'] ) ) : array();

			// Check whether all new agets exists.
			$new = array();
			foreach ( $new_ids as $id ) {
				$agent = new WPSC_Agent( $id );
				$new[] = $agent;
				if ( ! $agent->id ) {
					wp_send_json_error( 'Something went wrong!', 400 );
				}
			}

			$prev_ids = array();
			foreach ( $prev as $agent ) {
				$prev_ids[] = $agent->id;
			}

			// Exit if there is no change.
			if (
				count( array_diff( $new_ids, $prev_ids ) ) === 0 &&
				count( array_diff( $prev_ids, $new_ids ) ) === 0
			) {
				wp_die();
			}

			// Change assignee.
			WPSC_Individual_Ticket::change_assignee( $prev, $new, $current_user->customer->id );

			$response = array( 'viewPermission' => WPSC_Individual_Ticket::has_ticket_cap( 'view' ) ? 1 : 0 );
			wp_send_json( $response, 200 );
		}

		/**
		 * Get agent
		 */
		public static function get_tw_agents() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );
			$agents         = $ticket_widgets['assignee'];
			$title          = $agents['title'];
			$roles          = get_option( 'wpsc-agent-roles', array() );
			ob_start();
			?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-agents">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Title', 'supportcandy' ); ?></label>
					</div>
					<input name="label" type="text" value="<?php echo esc_attr( $agents['title'] ); ?>" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select name="is_enable">
						<option <?php selected( $agents['is_enable'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $agents['is_enable'], '0' ); ?>  value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed for customer', 'supportcandy' ); ?></label>
					</div>
					<select id="allow-customer" name="allow-customer">
						<option <?php selected( $agents['allow-customer'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $agents['allow-customer'], '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed agent roles', 'supportcandy' ); ?></label>
					</div>
					<select  multiple id="wpsc-select-agents" name="agents[]" placeholder="search agent...">
						<?php
						foreach ( $roles as $key => $role ) :
							$selected = in_array( $key, $agents['allowed-agent-roles'] ) ? 'selected="selected"' : ''
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $role['label'] ); ?></option>
							<?php
						endforeach;
						?>
					</select>
				</div>
				<script>
					jQuery('#wpsc-select-agents').selectWoo({
						allowClear: false,
						placeholder: ""
					});
				</script>
				<?php do_action( 'wpsc_get_ticket_info_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_tw_agents">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_tw_agents' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_tw_agents(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_tw_agents_widget_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set Agents
		 */
		public static function set_tw_agents() {

			if ( check_ajax_referer( 'wpsc_set_tw_agents', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $label ) {
				wp_die();
			}

			$is_enable          = isset( $_POST['is_enable'] ) ? intval( $_POST['is_enable'] ) : 0;
			$allow_for_customer = isset( $_POST['allow-customer'] ) ? intval( $_POST['allow-customer'] ) : 0;
			$agents             = isset( $_POST['agents'] ) ? array_filter( array_map( 'intval', $_POST['agents'] ) ) : array();

			$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );

			$ticket_widgets['assignee']['title']               = $label;
			$ticket_widgets['assignee']['is_enable']           = $is_enable;
			$ticket_widgets['assignee']['allow-customer']      = $allow_for_customer;
			$ticket_widgets['assignee']['allowed-agent-roles'] = $agents;
			update_option( 'wpsc-ticket-widget', $ticket_widgets );

			// update custom field.
			$cf       = WPSC_Custom_Field::get_cf_by_slug( 'assigned_agent' );
			$cf->name = $label;
			$cf->save();

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-twt-assignee' );
			WPSC_Translations::add( 'wpsc-twt-assignee', stripslashes( $label ) );
			wp_die();
		}

		/**
		 * Agent autocomplete assign agents
		 *
		 * @return void
		 */
		public static function agent_autocomplete_itw_assign() {

			if ( check_ajax_referer( 'wpsc_agent_autocomplete_itw_assign', '_ajax_nonce', false ) !== 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$ticket_id = isset( $_GET['ticket_id'] ) ? intval( $_GET['ticket_id'] ) : 0;
			$ticket    = new WPSC_Ticket( $ticket_id );
			if ( ! $ticket->id ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}
			WPSC_Individual_Ticket::$ticket = $ticket;

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'aa' ) ) ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
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

			$response = WPSC_Agent::agent_autocomplete( $filters );
			wp_send_json( $response );
		}
	}
endif;

WPSC_ITW_Assigned_Agents::init();
