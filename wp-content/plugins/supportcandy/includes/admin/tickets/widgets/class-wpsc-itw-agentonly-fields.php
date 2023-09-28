<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_ITW_Agentonly_Fields' ) ) :

	final class WPSC_ITW_Agentonly_Fields {

		/**
		 * Ignore ticket custom field types for agentonly fields
		 *
		 * @var array
		 */
		public static $ignore_cft = array();

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// ignore cft.
			add_action( 'init', array( __CLASS__, 'ignore_cft' ) );

			// get edit agentonly fields.
			add_action( 'wp_ajax_wpsc_it_get_edit_agentonly_fields', array( __CLASS__, 'it_get_edit_agentonly_fields' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_get_edit_agentonly_fields', array( __CLASS__, 'it_get_edit_agentonly_fields' ) );
			add_action( 'wp_ajax_wpsc_it_set_edit_agentonly_fields', array( __CLASS__, 'it_set_edit_agentonly_fields' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_set_edit_agentonly_fields', array( __CLASS__, 'it_set_edit_agentonly_fields' ) );

			// agentonly fields.
			add_action( 'wp_ajax_wpsc_get_tw_agentonly_fields', array( __CLASS__, 'get_tw_agentonly_fields' ) );
			add_action( 'wp_ajax_wpsc_set_tw_agentonly_fields', array( __CLASS__, 'set_tw_agentonly_fields' ) );
		}

		/**
		 * Set ignore custom field types for ticket fields
		 *
		 * @return void
		 */
		public static function ignore_cft() {

			self::$ignore_cft = apply_filters( 'wpsc_ignore_edit_agentonly_field_cft', array( 'cf_html' ) );
		}

		/**
		 * Prints body of current widget
		 *
		 * @return void
		 */
		/**
		 * Prints body of current widget
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @param array       $settings - widget settings.
		 * @return void
		 */
		public static function print_widget( $ticket, $settings ) {

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( WPSC_Individual_Ticket::$view_profile == 'agent' && in_array( $current_user->agent->role, $settings['allowed-agent-roles'] ) ) ) {
				return;
			}

			$flag = false?>

			<div class="wpsc-it-widget wpsc-itw-agentonly-fields">
				<div class="wpsc-widget-header">
					<h2>
						<?php
						$settings_title = $settings['title'] ? WPSC_Translations::get( 'wpsc-twt-agentonly-fields', stripslashes( $settings['title'] ) ) : stripslashes( $settings['title'] );
						echo esc_attr( $settings_title )
						?>
					</h2>
					<?php
					if ( $ticket->is_active && WPSC_Individual_Ticket::$view_profile == 'agent' && WPSC_Individual_Ticket::has_ticket_cap( 'caof' ) ) :
						?>
						<span onclick="wpsc_it_get_edit_agentonly_fields(<?php echo esc_attr( $ticket->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_it_get_edit_agentonly_fields' ) ); ?>')"><?php WPSC_Icons::get( 'edit' ); ?></span>
						<?php
					endif
					?>
				</div>
				<div class="wpsc-widget-body">
					<?php
					$cft_exclude = apply_filters( 'wpsc_it_widget_exclude_cft', array() );
					foreach ( WPSC_Custom_Field::$custom_fields as $cf ) :
						if ( in_array( $cf->type::$slug, $cft_exclude ) ) {
							continue;
						}
						if ( $cf->field == 'agentonly' && ! $cf->type::$is_default && ( $ticket->{$cf->slug} || ( $cf->type::$slug == 'cf_number' && is_numeric( $ticket->{$cf->slug} ) ) ) ) :
							if ( $cf->type::$is_date && ! is_object( $ticket->{$cf->slug} ) ) {
								continue;
							}
							$flag = ! $flag ? true : $flag
							?>
							<div class="info-list-item">
								<div class="info-label"><?php echo esc_attr( stripslashes( $cf->name ) ); ?>:</div>
								<div class="info-val">
									<?php
									$cf->type::print_widget_ticket_field_val( $cf, $ticket );
									?>
								</div>
							</div>
							<?php
						endif;
					endforeach;
					if ( ! $flag ) :
						?>
						<div class="wpsc-widget-default"><?php esc_attr_e( 'Not Applicable', 'supportcandy' ); ?></div>
						<?php
						endif;
						do_action( 'wpsc_itw_agent_fields', $ticket )
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Get edit agentonly fields
		 *
		 * @return void
		 */
		public static function it_get_edit_agentonly_fields() {

			if ( check_ajax_referer( 'wpsc_it_get_edit_agentonly_fields', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'caof' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			$ticket = WPSC_Individual_Ticket::$ticket;

			$widgets = get_option( 'wpsc-ticket-widget' );
			$title   = $widgets['agentonly-fields']['title'];

			ob_start()
			?>
			<form action="#" onsubmit="return false;" class="change-agentonly-fields">

				<div class="wpsc-input-group" style="align-items:flex-end;">
					<input type="text" id="cft_search" placeholder="<?php esc_attr_e( 'Search...', 'supportcandy' ); ?>" autocomplete="off" style="max-width:200px;">
				</div>
				<?php

				foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

					if (
						! class_exists( $cf->type ) ||
						$cf->type::$is_default ||
						$cf->field != 'agentonly' ||
						in_array( $cf->type::$slug, self::$ignore_cft )
					) {
						continue;
					}

					$cf->type::print_edit_ticket_cf( $cf, $ticket );
				}

				do_action( 'wpsc_get_tw_agentonly_fields_body', $ticket )
				?>

				<input type="hidden" name="action" value="wpsc_it_set_edit_agentonly_fields">
				<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_it_set_edit_agentonly_fields' ) ); ?>">

				<script>
					jQuery('#cft_search').keyup(function(e) {
						var val = jQuery(this).val().trim();
						var regex = new RegExp(val, "i");
						jQuery('.change-agentonly-fields .wpsc-tff').each(function(){
							var name 	= jQuery(this).find('.wpsc-tff-label').text();
							if (name.search(regex) < 0) {
								jQuery(this).hide();
							} else {
								jQuery(this).show();
							}
						})
					});
				</script>

			</form>
			<?php

			do_action( 'wpsc_get_tw_agentonly_fields_footer', $ticket );
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_it_set_edit_agentonly_fields(this, <?php echo esc_attr( $ticket->id ); ?>);">
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
		 * Change agentonly fields
		 *
		 * @return void
		 */
		public static function it_set_edit_agentonly_fields() {

			if ( check_ajax_referer( 'wpsc_it_set_edit_agentonly_fields', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'caof' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 401 );
			}

			$ticket = clone WPSC_Individual_Ticket::$ticket;
			if ( ! $ticket->is_active ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

				if ( $cf->type::$is_default || $cf->field != 'agentonly' ) {
					continue;
				}
				$ticket = $cf->type::set_edit_ticket_cf( $cf, $ticket );
			}

			if ( $ticket != WPSC_Individual_Ticket::$ticket ) {

				$ticket->date_updated = new DateTime();
				$ticket->save();

				do_action( 'wpsc_change_agentonly_fields', WPSC_Individual_Ticket::$ticket, $ticket );
			}

			wp_die();
		}

		/**
		 * Get agentonly fields
		 */
		public static function get_tw_agentonly_fields() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$ticket_widgets   = get_option( 'wpsc-ticket-widget', array() );
			$agentonly_fields = $ticket_widgets['agentonly-fields'];
			$title            = $agentonly_fields['title'];
			$roles            = get_option( 'wpsc-agent-roles', array() );
			ob_start();
			?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-agentonly-fields">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Title', 'supportcandy' ); ?></label>
					</div>
					<input name="label" type="text" value="<?php echo esc_attr( $agentonly_fields['title'] ); ?>" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select name="is_enable">
						<option <?php selected( $agentonly_fields['is_enable'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $agentonly_fields['is_enable'], '0' ); ?>  value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed agent roles', 'supportcandy' ); ?></label>
					</div>
					<select multiple id="wpsc-select-agents" name="agents[]" placeholder="search agent...">
						<?php
						foreach ( $roles as $key => $role ) :
							$selected = in_array( $key, $agentonly_fields['allowed-agent-roles'] ) ? 'selected="selected"' : ''
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
				<?php do_action( 'wpsc_get_agentonly_fields_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_tw_agentonly_fields">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_tw_agentonly_fields' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_tw_agentonly_fields(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_tw_agentonly_fields_widget_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set agentonly fields
		 */
		public static function set_tw_agentonly_fields() {

			if ( check_ajax_referer( 'wpsc_set_tw_agentonly_fields', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $label ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$is_enable = isset( $_POST['is_enable'] ) ? intval( $_POST['is_enable'] ) : 0;
			$agents    = isset( $_POST['agents'] ) ? array_filter( array_map( 'intval', $_POST['agents'] ) ) : array();

			$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );

			$ticket_widgets['agentonly-fields']['title']               = $label;
			$ticket_widgets['agentonly-fields']['is_enable']           = $is_enable;
			$ticket_widgets['agentonly-fields']['allowed-agent-roles'] = $agents;
			update_option( 'wpsc-ticket-widget', $ticket_widgets );

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-twt-agentonly-fields' );
			WPSC_Translations::add( 'wpsc-twt-agentonly-fields', stripslashes( $label ) );
			wp_die();
		}
	}
endif;

WPSC_ITW_Agentonly_Fields::init();
