<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_ITW_Additional_Recipients' ) ) :

	final class WPSC_ITW_Additional_Recipients {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Individual ticket edit mode.
			add_action( 'wp_ajax_wpsc_it_get_add_ar', array( __CLASS__, 'it_get_add_ar' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_get_add_ar', array( __CLASS__, 'it_get_add_ar' ) );
			add_action( 'wp_ajax_wpsc_it_set_add_ar', array( __CLASS__, 'it_set_add_ar' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_set_add_ar', array( __CLASS__, 'it_set_add_ar' ) );

			// additional recipients.
			add_action( 'wp_ajax_wpsc_get_tw_additional_recipients', array( __CLASS__, 'get_tw_additional_recipients' ) );
			add_action( 'wp_ajax_wpsc_set_tw_additional_recipients', array( __CLASS__, 'set_tw_additional_recipients' ) );
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
			if ( $current_user->is_guest || ! (
					(
						(
							WPSC_Individual_Ticket::$view_profile == 'customer' ||
							$ticket->customer->id == $current_user->customer->id
						) &&
						$settings['allow-customer']
					) ||
					( WPSC_Individual_Ticket::$view_profile == 'agent' && in_array( $current_user->agent->role, $settings['allowed-agent-roles'] ) )
				)
			) {
				return;
			}?>

			<div class="wpsc-it-widget wpsc-itw-add-rec">

				<div class="wpsc-widget-header">
					<h2>
						<?php
						$settings_title = $settings['title'] ? WPSC_Translations::get( 'wpsc-twt-additional-recipients', stripslashes( $settings['title'] ) ) : stripslashes( $settings['title'] );
						echo esc_attr( $settings_title )
						?>
					</h2>
					<?php
					if ( $ticket->is_active && $current_user->is_customer &&
						(
							( WPSC_Individual_Ticket::$view_profile == 'customer' || $ticket->customer->id == $current_user->customer->id ) ||
							( WPSC_Individual_Ticket::$view_profile == 'agent' && WPSC_Individual_Ticket::has_ticket_cap( 'ar' ) )
						)
					) :
						?>
						<span onclick="wpsc_it_get_add_ar(<?php echo esc_attr( $ticket->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_it_get_add_ar' ) ); ?>')"><?php WPSC_Icons::get( 'edit' ); ?></span>
						<?php
					endif
					?>
				</div>

				<div class="wpsc-widget-body">
					<div class="info-list-item">
						<div class="info-label"><?php echo esc_attr__( 'Emails', 'supportcandy' ); ?>:</div>
						<div class="info-val fullwidth">
							<?php
							$cf = WPSC_Custom_Field::get_cf_by_slug( 'add_recipients' );
							$cf->type::print_widget_ticket_field_val( $cf, $ticket );
							?>
						</div>
					</div>
					<?php do_action( 'wpsc_itw_additional_recipients', $ticket ); ?>
				</div>

			</div>
			<?php
		}

		/**
		 * Get add additional recipients
		 *
		 * @return void
		 */
		public static function it_get_add_ar() {

			if ( check_ajax_referer( 'wpsc_it_get_add_ar', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();
			$ticket       = WPSC_Individual_Ticket::$ticket;
			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				( WPSC_Individual_Ticket::$view_profile == 'customer' || $ticket->customer->id == $current_user->customer->id ) ||
				( WPSC_Individual_Ticket::$view_profile == 'agent' && WPSC_Individual_Ticket::has_ticket_cap( 'ar' ) )
			) ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			$widgets = get_option( 'wpsc-ticket-widget' );
			$title   = $widgets['additional-recipients']['title'];

			// Additional recipients.
			$add_recipients = implode( PHP_EOL, $ticket->add_recipients );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="additional-recipients">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Email address (one per line)', 'supportcandy' ); ?></label>
					</div>
					<p><?php esc_attr_e( 'All customer email notifications will be sent to these email addresses.', 'supportcandy' ); ?></p>
					<textarea rows="9" name="add_recipients"><?php echo esc_textarea( $add_recipients ); ?></textarea>
				</div>
				<?php do_action( 'wpsc_it_edit_additional_recipients', $ticket ); ?>
				<input type="hidden" name="action" value="wpsc_it_set_add_ar">
				<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_it_set_add_ar' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_it_set_add_ar(this, <?php echo esc_attr( $ticket->id ); ?>);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_add_additional_recipients_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set additinal recipients
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @return void
		 */
		public static function it_set_add_ar() {

			if ( check_ajax_referer( 'wpsc_it_set_add_ar', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();
			$ticket = WPSC_Individual_Ticket::$ticket;
			if ( ! $ticket->is_active ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				( WPSC_Individual_Ticket::$view_profile == 'customer' || $ticket->customer->id == $current_user->customer->id ) ||
				( WPSC_Individual_Ticket::$view_profile == 'agent' && WPSC_Individual_Ticket::has_ticket_cap( 'ar' ) )
			) ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			$new = isset( $_POST['add_recipients'] ) ? sanitize_textarea_field( wp_unslash( $_POST['add_recipients'] ) ) : '';
			$new = array_unique( array_filter( array_map( 'sanitize_email', explode( PHP_EOL, $new ) ) ) );

			$prev = $ticket->add_recipients;

			do_action( 'wpsc_before_change_add_recipients', $ticket, $prev, $new, $current_user->customer->id );

			// Exit if no change.
			if ( ! ( array_diff( $new, $prev ) || array_diff( $prev, $new ) ) ) {
				wp_die();
			}
			$ticket->add_recipients = $new;
			$ticket->date_updated   = new DateTime();
			$ticket->save();

			do_action( 'wpsc_change_ticket_add_recipients', $ticket, $prev, $new, $current_user->customer->id );
			wp_die();
		}

		/**
		 * Get additional recipients
		 */
		public static function get_tw_additional_recipients() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$ticket_widgets        = get_option( 'wpsc-ticket-widget', array() );
			$additional_recipients = $ticket_widgets['additional-recipients'];
			$title                 = $additional_recipients['title'];
			$roles                 = get_option( 'wpsc-agent-roles', array() );
			ob_start();
			?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-additional-recipients">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Title', 'supportcandy' ); ?></label>
					</div>
					<input name="label" type="text" value="<?php echo esc_attr( $additional_recipients['title'] ); ?>" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select name="is_enable">
						<option <?php selected( $additional_recipients['is_enable'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $additional_recipients['is_enable'], '0' ); ?>  value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed for customer', 'supportcandy' ); ?></label>
					</div>
					<select id="allow-customer" name="allow-customer">
						<option <?php selected( $additional_recipients['allow-customer'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $additional_recipients['allow-customer'], '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed agent roles', 'supportcandy' ); ?></label>
					</div>
					<select multiple id="wpsc-select-agents" name="agents[]" placeholder="search agent...">
						<?php
						foreach ( $roles as $key => $role ) :
							$selected = in_array( $key, $additional_recipients['allowed-agent-roles'] ) ? 'selected="selected"' : ''
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
				<?php do_action( 'wpsc_get_additional_recipients_fields_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_tw_additional_recipients">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_tw_additional_recipients' ) ); ?>">
			</form>
			<?php

			$body = ob_get_clean();
			ob_start();
			?>

			<button class="wpsc-button small primary" onclick="wpsc_set_tw_additional_recipients(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_tw_additional_recipients_widget_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set additional_recipients
		 */
		public static function set_tw_additional_recipients() {

			if ( check_ajax_referer( 'wpsc_set_tw_additional_recipients', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $label ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$is_enable          = isset( $_POST['is_enable'] ) ? intval( $_POST['is_enable'] ) : 0;
			$allow_for_customer = isset( $_POST['allow-customer'] ) ? intval( $_POST['allow-customer'] ) : 0;
			$agents             = isset( $_POST['agents'] ) ? array_filter( array_map( 'intval', $_POST['agents'] ) ) : array();

			$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );

			$ticket_widgets['additional-recipients']['title']               = $label;
			$ticket_widgets['additional-recipients']['is_enable']           = $is_enable;
			$ticket_widgets['additional-recipients']['allow-customer']      = $allow_for_customer;
			$ticket_widgets['additional-recipients']['allowed-agent-roles'] = $agents;
			update_option( 'wpsc-ticket-widget', $ticket_widgets );

			$cf       = WPSC_Custom_Field::get_cf_by_slug( 'add_recipients' );
			$cf->name = $label;
			$cf->save();

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-twt-additional-recipients' );
			WPSC_Translations::add( 'wpsc-twt-additional-recipients', stripslashes( $label ) );
			wp_die();
		}
	}
endif;

WPSC_ITW_Additional_Recipients::init();
