<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_ITW_Biographical_Info' ) ) :

	final class WPSC_ITW_Biographical_Info {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Biographical Info.
			add_action( 'wp_ajax_wpsc_get_tw_biographical_info', array( __CLASS__, 'get_tw_biographical_info' ) );
			add_action( 'wp_ajax_wpsc_set_tw_biographical_info', array( __CLASS__, 'set_tw_biographical_info' ) );
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
				WPSC_Individual_Ticket::$view_profile == 'agent' &&
				in_array( $current_user->agent->role, $settings['allowed-agent-roles'] )
			) ) {
				return;
			}?>

			<div class="wpsc-it-widget wpsc-itw-bio-info">
				<div class="wpsc-widget-header">
					<h2><?php echo esc_attr( $settings['title'] ); ?></h2>
				</div>
				<div class="wpsc-widget-body">
					<div class="info-list-item">

						<div class="info-val">
							<?php
							if ( $ticket->customer->user ) {

								$user_description = get_user_meta( $ticket->customer->user->ID, 'description', true );
								if ( $user_description ) {

									echo wp_kses_post( nl2br( $user_description ) );

								} else {

									esc_attr_e( 'No info added', 'supportcandy' );

								}
							} else {

								esc_attr_e( 'Not Applicable', 'supportcandy' );

							}
							?>
						</div>
					</div>
					<?php do_action( 'wpsc_itw_biographical_info', $ticket ); ?>
				</div>
			</div>
			<?php
		}

		/**
		 * Get Biographical info
		 */
		public static function get_tw_biographical_info() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$ticket_widgets    = get_option( 'wpsc-ticket-widget', array() );
			$biographical_info = $ticket_widgets['biographical-info'];
			$title             = $biographical_info['title'];
			$roles             = get_option( 'wpsc-agent-roles', array() );
			ob_start();
			?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-biographical-info">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Title', 'supportcandy' ); ?></label>
					</div>
					<input name="label" type="text" value="<?php echo esc_attr( $biographical_info['title'] ); ?>" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select name="is_enable">
						<option <?php selected( $biographical_info['is_enable'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $biographical_info['is_enable'], '0' ); ?>  value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed agent roles', 'supportcandy' ); ?></label>
					</div>
					<select  multiple id="wpsc-select-agents" name="agents[]" placeholder="search agent...">
						<?php
						foreach ( $roles as $key => $role ) :
							$selected = in_array( $key, $biographical_info['allowed-agent-roles'] ) ? 'selected="selected"' : ''
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
				<?php do_action( 'wpsc_get_biographical_info_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_tw_biographical_info">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_tw_biographical_info' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_tw_biographical_info(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_tw_biographical_info_widget_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);

			wp_send_json( $response );
		}

		/**
		 * Set biographical info
		 */
		public static function set_tw_biographical_info() {

			if ( check_ajax_referer( 'wpsc_set_tw_biographical_info', '_ajax_nonce', false ) != 1 ) {
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

			$ticket_widgets['biographical-info']['title']               = $label;
			$ticket_widgets['biographical-info']['is_enable']           = $is_enable;
			$ticket_widgets['biographical-info']['allowed-agent-roles'] = $agents;
			update_option( 'wpsc-ticket-widget', $ticket_widgets );

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-twt-biographical-info' );
			WPSC_Translations::add( 'wpsc-twt-biographical-info', stripslashes( $label ) );
			wp_die();
		}
	}
endif;

WPSC_ITW_Biographical_Info::init();
