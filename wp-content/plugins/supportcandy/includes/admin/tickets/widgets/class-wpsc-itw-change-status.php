<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_ITW_Change_Status' ) ) :

	final class WPSC_ITW_Change_Status {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// get edit ticket status.
			add_action( 'wp_ajax_wpsc_it_get_edit_ticket_status', array( __CLASS__, 'it_get_edit_ticket_status' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_get_edit_ticket_status', array( __CLASS__, 'it_get_edit_ticket_status' ) );
			add_action( 'wp_ajax_wpsc_it_set_edit_ticket_status', array( __CLASS__, 'it_set_edit_ticket_status' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_set_edit_ticket_status', array( __CLASS__, 'it_set_edit_ticket_status' ) );

			// ticket status widget.
			add_action( 'wp_ajax_wpsc_get_tw_ticket_status', array( __CLASS__, 'get_tw_ticket_status' ) );
			add_action( 'wp_ajax_wpsc_set_tw_ticket_status', array( __CLASS__, 'set_tw_ticket_status' ) );
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
			}

			$status   = WPSC_Custom_Field::get_cf_by_slug( 'status' );
			$category = WPSC_Custom_Field::get_cf_by_slug( 'category' );
			$priority = WPSC_Custom_Field::get_cf_by_slug( 'priority' );?>

			<div class="wpsc-it-widget wpsc-itw-ticket-info">
				<div class="wpsc-widget-header">
					<h2>
						<?php
						$settings_title = $settings['title'] ? WPSC_Translations::get( 'wpsc-twt-change-status', stripslashes( $settings['title'] ) ) : stripslashes( $settings['title'] );
						echo esc_attr( $settings_title )
						?>
					</h2>
					<?php
					if ( $ticket->is_active && WPSC_Individual_Ticket::$view_profile == 'agent' && WPSC_Individual_Ticket::has_ticket_cap( 'cs' ) ) :
						?>
						<span onclick="wpsc_it_get_edit_ticket_status(<?php echo esc_attr( $ticket->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_it_get_edit_ticket_status' ) ); ?>' )"><?php WPSC_Icons::get( 'edit' ); ?></span>
						<?php
					endif
					?>
				</div>
				<div class="wpsc-widget-body">
					<div class="info-list-item">
						<div class="info-label"><?php echo esc_attr( $status->name ); ?>:</div>
						<div class="info-val"><?php $status->type::print_widget_ticket_field_val( $category, $ticket ); ?></div>
					</div>
					<div class="info-list-item">
						<div class="info-label"><?php echo esc_attr( $category->name ); ?>:</div>
						<div class="info-val"><?php $category->type::print_widget_ticket_field_val( $category, $ticket ); ?></div>
					</div>
					<?php
					if (
						WPSC_Individual_Ticket::$view_profile == 'agent' ||
						( WPSC_Individual_Ticket::$view_profile == 'customer' && $settings['show-priority-to-customer'] )
					) :
						?>
						<div class="info-list-item">
							<div class="info-label"><?php echo esc_attr( $priority->name ); ?>:</div>
							<div class="info-val"><?php $priority->type::print_widget_ticket_field_val( $priority, $ticket ); ?></div>
						</div>
						<?php
					endif;
					do_action( 'wpsc_itw_change_status', $ticket )
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Get edit ticket status,category and priority
		 *
		 * @return void
		 */
		public static function it_get_edit_ticket_status() {

			WPSC_Individual_Ticket::load_current_ticket();

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'cs' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$ticket = WPSC_Individual_Ticket::$ticket;

			$widgets     = get_option( 'wpsc-ticket-widget' );
			$gs          = get_option( 'wpsc-gs-general' );
			$tl_advanced = get_option( 'wpsc-tl-ms-advanced' );
			$title       = $widgets['change-status']['title'];

			$statuses   = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			$categories = WPSC_Category::find( array( 'items_per_page' => 0 ) )['results'];
			$priorities = WPSC_Priority::find( array( 'items_per_page' => 0 ) )['results'];

			$close_flag = in_array( $current_user->agent->role, $gs['allow-close-ticket'] ) ? true : false;

			$allow_status = true;
			if (
				( $ticket->status->id == $gs['close-ticket-status'] || in_array( $ticket->status->id, $tl_advanced['closed-ticket-statuses'] ) ) &&
				! $close_flag
			) {
				$allow_status = false;
			}
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="frm-edit-ticket-status">
				<?php
				if ( $allow_status ) {
					?>
					<div class="wpsc-input-group">
						<?php $cf = WPSC_Custom_Field::get_cf_by_slug( 'status' ); ?>
						<div class="label-container">
							<label for=""><?php echo esc_attr( $cf->name ); ?></label>
						</div>
						<select id="wpsc-select-it-ticket-status" name="status_id">
							<option value=""></option>
							<?php
							foreach ( $statuses as $status ) :
								if (
									( $status->id == $gs['close-ticket-status'] || in_array( $status->id, $tl_advanced['closed-ticket-statuses'] ) ) &&
									! $close_flag
								) {
									continue;
								}
								?>
								<option value="<?php echo esc_attr( $status->id ); ?>" <?php selected( $ticket->status->id, $status->id ); ?>><?php echo esc_attr( $status->name ); ?></option>
								<?php
							endforeach;
							?>
						</select>
						<script>
							jQuery('#wpsc-select-it-ticket-status').selectWoo({
								allowClear: true,
								placeholder: ""
							});
						</script>
					</div>
					<?php
				}
				?>
				<div class="wpsc-input-group">
					<?php $cf = WPSC_Custom_Field::get_cf_by_slug( 'category' ); ?>
					<div class="label-container">
						<label for=""><?php echo esc_attr( $cf->name ); ?></label>
					</div>
					<select id="wpsc-select-it-ticket-category" name="cat_id">
						<?php
						foreach ( $categories as $category ) :
							?>
								<option value="<?php echo esc_attr( $category->id ); ?>" <?php selected( $ticket->category->id, $category->id ); ?>><?php echo esc_attr( $category->name ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-select-it-ticket-category').selectWoo({
							allowClear: true,
							placeholder: ""
						});
					</script>
				</div>
				<div class="wpsc-input-group">
					<?php $cf = WPSC_Custom_Field::get_cf_by_slug( 'priority' ); ?>
					<div class="label-container">
						<label for=""><?php echo esc_attr( $cf->name ); ?></label>
					</div>
					<select id="wpsc-select-it-ticket-priority" name="priority_id">
						<?php
						foreach ( $priorities as $priority ) :
							?>
							<option value="<?php echo esc_attr( $priority->id ); ?>" <?php selected( $ticket->priority->id, $priority->id ); ?>><?php echo esc_attr( $priority->name ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-select-it-ticket-priority').selectWoo({
							allowClear: true,
							placeholder: ""
						});
					</script>
				</div>
				<?php do_action( 'wpsc_get_tw_ticket_status_body', $ticket ); ?>
				<input type="hidden" name="action" value="wpsc_it_set_edit_ticket_status">
				<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_it_set_edit_ticket_status' ) ); ?>">
			</form>
			<?php
			do_action( 'wpsc_get_edit_status_footer', $ticket );
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_it_set_edit_ticket_status(this, <?php echo esc_attr( $ticket->id ); ?>);">
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
		 * Set change status/category/priorities
		 *
		 * @return void
		 */
		public static function it_set_edit_ticket_status() {

			if ( check_ajax_referer( 'wpsc_it_set_edit_ticket_status', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'cs' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$ticket = WPSC_Individual_Ticket::$ticket;
			if ( ! $ticket->is_active ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$status_id   = isset( $_POST['status_id'] ) && intval( $_POST['status_id'] ) ? intval( $_POST['status_id'] ) : $ticket->status->id;
			$cat_id      = isset( $_POST['cat_id'] ) && intval( $_POST['cat_id'] ) ? intval( $_POST['cat_id'] ) : $ticket->category->id;
			$priority_id = isset( $_POST['priority_id'] ) && intval( $_POST['priority_id'] ) ? intval( $_POST['priority_id'] ) : $ticket->priority->id;

			$gs          = get_option( 'wpsc-gs-general' );
			$tl_advanced = get_option( 'wpsc-tl-ms-advanced' );
			$close_flag  = in_array( $current_user->agent->role, $gs['allow-close-ticket'] ) ? true : false;

			// Check status.
			if (
				$ticket->status->id != $status_id ||
				(
					( $status_id == $gs['close-ticket-status'] || in_array( $status_id, $tl_advanced['closed-ticket-statuses'] ) ) &&
					$close_flag && $ticket->status->id != $status_id
				)
			) {
				WPSC_Individual_Ticket::change_status( $ticket->status->id, $status_id, $current_user->customer->id );
			}

			// Check category.
			if ( $ticket->category->id != $cat_id ) {
				WPSC_Individual_Ticket::change_category( $ticket->category->id, $cat_id, $current_user->customer->id );
			}

			// Check priority.
			if ( $ticket->priority->id != $priority_id ) {
				WPSC_Individual_Ticket::change_priority( $ticket->priority->id, $priority_id, $current_user->customer->id );
			}

			do_action( 'wpsc_change_status', WPSC_Individual_Ticket::$ticket );

			wp_die();
		}

		/**
		 * Get Ticket Status
		 */
		public static function get_tw_ticket_status() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );
			$change_status  = $ticket_widgets['change-status'];
			$title          = $change_status['title'];
			$roles          = get_option( 'wpsc-agent-roles', array() );
			ob_start();
			?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-ticket-status">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Title', 'supportcandy' ); ?></label>
					</div>
					<input name="label" type="text" value="<?php echo esc_attr( $change_status['title'] ); ?>" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select name="is_enable">
						<option <?php selected( $change_status['is_enable'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $change_status['is_enable'], '0' ); ?>  value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed for customer', 'supportcandy' ); ?></label>
					</div>
					<select id="allow-customer" name="allow-customer">
						<option <?php selected( $change_status['allow-customer'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $change_status['allow-customer'], '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed agent roles', 'supportcandy' ); ?></label>
					</div>
					<select  multiple id="wpsc-select-agents" name="agents[]" placeholder="search agent...">
						<?php
						foreach ( $roles as $key => $role ) :
							$selected = in_array( $key, $change_status['allowed-agent-roles'] ) ? 'selected="selected"' : ''
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
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Show priority to customer', 'supportcandy' ); ?></label>
					</div>
					<select id="show-priority-to-customer" name="show-priority-to-customer">
						<option <?php selected( $change_status['show-priority-to-customer'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $change_status['show-priority-to-customer'], '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php do_action( 'wpsc_get_ticket_status_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_tw_ticket_status">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_tw_ticket_status' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_tw_ticket_status(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_tw_ticket_status_widget_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 *  Set Ticket Status Widget
		 */
		public static function set_tw_ticket_status() {

			if ( check_ajax_referer( 'wpsc_set_tw_ticket_status', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $label ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$is_enable                 = isset( $_POST['is_enable'] ) ? intval( $_POST['is_enable'] ) : 0;
			$allow_for_customer        = isset( $_POST['allow-customer'] ) ? intval( $_POST['allow-customer'] ) : 0;
			$agents                    = isset( $_POST['agents'] ) ? array_filter( array_map( 'intval', $_POST['agents'] ) ) : array();
			$show_priority_to_customer = isset( $_POST['show-priority-to-customer'] ) ? intval( $_POST['show-priority-to-customer'] ) : 0;

			$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );

			$ticket_widgets['change-status']['title']                     = $label;
			$ticket_widgets['change-status']['is_enable']                 = $is_enable;
			$ticket_widgets['change-status']['allow-customer']            = $allow_for_customer;
			$ticket_widgets['change-status']['allowed-agent-roles']       = $agents;
			$ticket_widgets['change-status']['show-priority-to-customer'] = $show_priority_to_customer;
			update_option( 'wpsc-ticket-widget', $ticket_widgets );

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-twt-change-status' );
			WPSC_Translations::add( 'wpsc-twt-change-status', stripslashes( $label ) );
			wp_die();
		}
	}
endif;

WPSC_ITW_Change_Status::init();
