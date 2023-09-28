<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_GS_General' ) ) :

	final class WPSC_GS_General {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// User interface.
			add_action( 'wp_ajax_wpsc_get_gs_general', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_gs_general', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_gs_general', array( __CLASS__, 'reset_settings' ) );

			// Apply ticket status after customer or agent reply.
			add_action( 'wpsc_post_reply', array( __CLASS__, 'change_ticket_status' ) );

			// after new agent role added.
			add_action( 'wpsc_after_add_agent_role', array( __CLASS__, 'after_add_new_role' ) );

			// after delete agent role.
			add_action( 'wpsc_after_delete_agent_role', array( __CLASS__, 'after_delete_role' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			$roles = get_option( 'wpsc-agent-roles' );
			$allow_close_ticket = array( 'customer' );
			$allow_create_ticket = array( 'registered-user' );
			foreach ( $roles as $key => $role ) {
				$allow_close_ticket[] = $key;
				$allow_create_ticket[] = $key;
			}

			$general = apply_filters(
				'wpsc_gs_general',
				array(
					'ticket-status-after-customer-reply' => 3,
					'ticket-status-after-agent-reply'    => 2,
					'close-ticket-status'                => 4,
					'reply-form-position'                => 'top',
					'default-date-format'                => 'Y-m-d H:i:s',
					'ticket-alice'                       => 'Ticket #',
					'allow-close-ticket'                 => $allow_close_ticket,
					'allow-ar-thread-email'              => array( 1, 2 ),
					'allow-create-ticket'                => $allow_create_ticket,
					'allowed-search-fields'              => array( 'id', 'customer', 'subject', 'threads' ),
				)
			);
			update_option( 'wpsc-gs-general', $general );
		}

		/**
		 * Settings user interface
		 *
		 * @return void
		 */
		public static function load_settings_ui() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$statuses   = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			$settings   = get_option( 'wpsc-gs-general', array() );
			$roles      = get_option( 'wpsc-agent-roles' );
			$visibility = array(
				'registered-user' => esc_attr__( 'Registered user', 'supportcandy' ),
				'guest'           => esc_attr__( 'Guest', 'supportcandy' ),
			);
			foreach ( $roles as $key => $role ) :
				$visibility[ $key ] = $role['label'];
			endforeach;

			$allow_close_ticket = array(
				'customer' => esc_attr__( 'Customer', 'supportcandy' ),
			);
			foreach ( $roles as $key => $role ) :
				$allow_close_ticket[ $key ] = $role['label'];
			endforeach;?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-gs-general">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/general-settings/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Ticket status after customer reply', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-tsacr" name="ticket-status-after-customer-reply">
						<option value="0"><?php esc_attr_e( 'Do not change', 'supportcandy' ); ?></option>
						<?php
						foreach ( $statuses as $status ) :
							?>
							<option <?php selected( $status->id, $settings['ticket-status-after-customer-reply'] ); ?> value="<?php echo esc_attr( $status->id ); ?>"><?php echo esc_attr( $status->name ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-tsacr').selectWoo({
							allowClear: true,
							placeholder: ""
						});
					</script>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Ticket status after agent reply', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-tsaar" name="ticket-status-after-agent-reply">
						<option value="0"><?php esc_attr_e( 'Do not change', 'supportcandy' ); ?></option>
						<?php
						foreach ( $statuses as $status ) :
							?>
							<option <?php selected( $status->id, $settings['ticket-status-after-agent-reply'] ); ?> value="<?php echo esc_attr( $status->id ); ?>"><?php echo esc_attr( $status->name ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-tsaar').selectWoo({
							allowClear: true,
							placeholder: ""
						});
					</script>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Close ticket status', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-cts" name="close-ticket-status">
						<?php
						foreach ( $statuses as $status ) :
							?>
							<option <?php selected( $status->id, $settings['close-ticket-status'] ); ?> value="<?php echo esc_attr( $status->id ); ?>"><?php echo esc_attr( $status->name ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-cts').selectWoo({
							allowClear: true,
							placeholder: ""
						});
					</script>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Reply form position', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-rfp" name="reply-form-position">
						<option <?php selected( $settings['reply-form-position'], 'top' ); ?> value="top"><?php esc_attr_e( 'Top', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['reply-form-position'], 'bottom' ); ?> value="bottom"><?php esc_attr_e( 'Bottom', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Default date format', 'supportcandy' ); ?></label>
					</div>
					<input id="wpsc-ddf" type="text" name="default-date-format" value="<?php echo esc_attr( $settings['default-date-format'] ); ?>">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Ticket alice', 'supportcandy' ); ?></label>
					</div>
					<input id="wpsc-ta" type="text" name="ticket-alice" value="<?php echo esc_attr( $settings['ticket-alice'] ); ?>">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allow close ticket', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-allow-close-ticket"  multiple name="allow-close-ticket[]">
						<?php
						foreach ( $allow_close_ticket as $key => $role ) :
							$selected = in_array( $key, $settings['allow-close-ticket'] ) ? 'selected' : ''
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $role ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>jQuery('#wpsc-allow-close-ticket').selectWoo();</script>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allow create new ticket', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-allow-create-ticket"  multiple name="allow-create-ticket[]">
						<?php
						foreach ( $visibility as $key => $role ) :
							$selected = in_array( $key, $settings['allow-create-ticket'] ) ? 'selected' : ''
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $role ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>jQuery('#wpsc-allow-create-ticket').selectWoo();</script>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Thread email visibility', 'supportcandy' ); ?></label>
					</div>
					<select multiple id="wpsc-select-agent-roles" name="allow-ar-thread-email[]" placeholder="search agent roles">
						<?php
						foreach ( $roles as $key => $role ) :
							?>
								<option <?php echo in_array( $key, $settings['allow-ar-thread-email'] ) ? 'selected="selected"' : ''; ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $role['label'] ); ?></option>
							<?php
							endforeach;
						?>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Select allowed search fields', 'supportcandy' ); ?></label>
					</div>
					<select multiple id="wpsc-select-allowed-search-fields" name="allowed-search-fields[]">
						<option <?php echo in_array( 'threads', $settings['allowed-search-fields'] ) ? 'selected' : ''; ?> value="threads"><?php esc_attr_e( 'Threads', 'supportcandy' ); ?></option>
						<?php
						foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {
							if ( ! ( property_exists( $cf->type, 'is_search' ) && $cf->type::$is_search ) || $cf->field == 'usergroup' ) {
								continue;
							}
							$selected = in_array( $cf->slug, $settings['allowed-search-fields'] ) ? 'selected' : '';
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $cf->slug ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
							<?php
						}
						?>
					</select>
				</div>
				<script>
					jQuery('#wpsc-select-agent-roles, #wpsc-select-allowed-search-fields').selectWoo({
						allowClear: false,
						placeholder: ""
					});
				</script>
				<?php do_action( 'wpsc_gs_general' ); ?>
				<input type="hidden" name="action" value="wpsc_set_gs_general">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_gs_general' ) ); ?>">
			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_gs_general(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_gs_general(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_gs_general' ) ); ?>');">
					<?php esc_attr_e( 'Reset default', 'supportcandy' ); ?></button>
			</div>
			<?php
			wp_die();
		}

		/**
		 * Save settings
		 *
		 * @return void
		 */
		public static function save_settings() {

			if ( check_ajax_referer( 'wpsc_set_gs_general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$general = apply_filters(
				'wpsc_set_gs_general',
				array(
					'ticket-status-after-customer-reply' => isset( $_POST['ticket-status-after-customer-reply'] ) ? intval( $_POST['ticket-status-after-customer-reply'] ) : '',
					'ticket-status-after-agent-reply'    => isset( $_POST['ticket-status-after-agent-reply'] ) ? intval( $_POST['ticket-status-after-agent-reply'] ) : '',
					'close-ticket-status'                => isset( $_POST['close-ticket-status'] ) ? intval( $_POST['close-ticket-status'] ) : 4,
					'reply-form-position'                => isset( $_POST['reply-form-position'] ) ? sanitize_text_field( wp_unslash( $_POST['reply-form-position'] ) ) : 'top',
					'default-date-format'                => isset( $_POST['default-date-format'] ) ? sanitize_text_field( wp_unslash( $_POST['default-date-format'] ) ) : 'Y-m-d',
					'ticket-alice'                       => isset( $_POST['ticket-alice'] ) ? sanitize_text_field( wp_unslash( $_POST['ticket-alice'] ) ) : 'Ticket #',
					'allow-close-ticket'                 => isset( $_POST['allow-close-ticket'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['allow-close-ticket'] ) ) ) : array(),
					'allow-ar-thread-email'              => isset( $_POST['allow-ar-thread-email'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['allow-ar-thread-email'] ) ) ) : array(),
					'allow-create-ticket'                => isset( $_POST['allow-create-ticket'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['allow-create-ticket'] ) ) ) : array(),
					'allowed-search-fields'              => isset( $_POST['allowed-search-fields'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['allowed-search-fields'] ) ) ) : array(),
				)
			);
			update_option( 'wpsc-gs-general', $general );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( check_ajax_referer( 'wpsc_reset_gs_general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			self::reset();
			wp_die();
		}

		/**
		 * Change status after agent or customer reply
		 *
		 * @param WPSC_Thread $thread - thread object.
		 * @return void
		 */
		public static function change_ticket_status( $thread ) {

			$gs            = get_option( 'wpsc-gs-general' );
			$reply_profile = WPSC_Individual_Ticket::$reply_profile;

			if ( $reply_profile == 'agent' ) {

				if ( ! $gs['ticket-status-after-agent-reply'] ) {
					return;
				}
				$prev = $thread->ticket->status->id;
				$new  = intval( $gs['ticket-status-after-agent-reply'] ) ? $gs['ticket-status-after-agent-reply'] : $prev;
				if ( $prev == $new ) {
					return;
				}
				WPSC_Individual_Ticket::change_status( $prev, $new, 0 ); // customer id given as '0' because this is system changing the status based on settings.

			} else {

				if ( ! $gs['ticket-status-after-customer-reply'] ) {
					return;
				}
				$prev = $thread->ticket->status->id;
				$new  = intval( $gs['ticket-status-after-customer-reply'] ) ? $gs['ticket-status-after-customer-reply'] : $prev;
				if ( $prev == $new ) {
					return;
				}
				WPSC_Individual_Ticket::change_status( $prev, $new, 0 ); // customer id given as '0' because this is system changing the status based on settings.
			}
		}

		/**
		 * After new agent role added add that role in allow create ticket and thread visibility settings
		 *
		 * @param integer $role_id - agent role id.
		 * @return void
		 */
		public static function after_add_new_role( $role_id ) {

			$gs           = get_option( 'wpsc-gs-general' );
			$allow_ticket = $gs['allow-create-ticket'];
			$allow_thread = $gs['allow-ar-thread-email'];
			$allow_close  = $gs['allow-close-ticket'];

			$allow_ticket[] = $role_id;
			$allow_thread[] = $role_id;
			$allow_close[]  = $role_id;

			$gs['allow-create-ticket']   = $allow_ticket;
			$gs['allow-ar-thread-email'] = $allow_thread;
			$gs['allow-close-ticket']    = $allow_close;
			update_option( 'wpsc-gs-general', $gs );
		}

		/**
		 * After agent role deleted remove that role in allow create ticket and thread visibility settings
		 *
		 * @param integer $role_id - agent role id.
		 * @return void
		 */
		public static function after_delete_role( $role_id ) {

			$gs           = get_option( 'wpsc-gs-general' );
			$allow_ticket = $gs['allow-create-ticket'];
			$allow_thread = $gs['allow-ar-thread-email'];
			$allow_close  = $gs['allow-close-ticket'];

			if ( in_array( $role_id, $allow_ticket ) ) {
				unset( $allow_ticket[ $role_id ] );
			}
			if ( in_array( $role_id, $allow_thread ) ) {
				unset( $allow_thread[ $role_id ] );
			}
			if ( in_array( $role_id, $allow_close ) ) {
				unset( $allow_close[ $role_id ] );
			}

			$gs['allow-create-ticket']   = $allow_ticket;
			$gs['allow-ar-thread-email'] = $allow_thread;
			$gs['allow-close-ticket']    = $allow_close;
			update_option( 'wpsc-gs-general', $gs );
		}
	}
endif;

WPSC_GS_General::init();
