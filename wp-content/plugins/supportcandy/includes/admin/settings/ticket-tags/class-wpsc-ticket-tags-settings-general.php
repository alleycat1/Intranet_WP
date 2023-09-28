<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Ticket_Tags_Settings_General' ) ) :

	final class WPSC_Ticket_Tags_Settings_General {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// setting actions.
			add_action( 'wp_ajax_wpsc_ticket_tags_get_general_settings', array( __CLASS__, 'get_general_settings' ) );
			add_action( 'wp_ajax_wpsc_ticket_tags_set_general_settings', array( __CLASS__, 'set_general_settings' ) );
			add_action( 'wp_ajax_wpsc_ticket_tags_reset_general_settings', array( __CLASS__, 'reset_general_settings' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			// general settings.
			update_option(
				'wpsc-ticket-tags-general-settings',
				array(
					'color'    => '#000000',
					'bg-color' => '#ffc300',
				)
			);
		}

		/**
		 * Get general settings
		 *
		 * @return void
		 */
		public static function get_general_settings() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorized!', 401 );
			}

			$general = get_option( 'wpsc-ticket-tags-general-settings' );
			?>
			<form action="#" onsubmit="return false;" class="wpsc-ticket-tags-general-settings">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/tag-general-setting/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Ticket Tags', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php echo esc_attr( wpsc__( 'Background color', 'supportcandy' ) ); ?></td>
							<td><?php esc_attr_e( 'Text color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="color" value="<?php echo esc_attr( $general['color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="bg-color" value="<?php echo esc_attr( $general['bg-color'] ); ?>"></td>
						</tr>
					</table>
				</div>
				<script>jQuery('.wpsc-color-picker').wpColorPicker();</script>
				<style>
					.wpsc-ap-table td { padding: 5px; }
					.wpsc-ap-table table, .wpsc-ap-table td { border: 1px solid #c3c3c3; }
				</style>
				<input type="hidden" name="action" value="wpsc_ticket_tags_set_general_settings">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_ticket_tags_set_general_settings' ) ); ?>">
			</form>

			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_ticket_tags_set_general_settings(this);">
					<?php echo esc_attr( wpsc__( 'Submit', 'supportcandy' ) ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_ticket_tags_reset_general_settings(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_ticket_tags_reset_general_settings' ) ); ?>');">
					<?php echo esc_attr( wpsc__( 'Reset default', 'supportcandy' ) ); ?></button>
			</div>
			<?php

			wp_die();
		}

		/**
		 * Set general settings
		 *
		 * @return void
		 */
		public static function set_general_settings() {

			if ( check_ajax_referer( 'wpsc_ticket_tags_set_general_settings', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorized request!', 400 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorized!', 401 );
			}

			update_option(
				'wpsc-ticket-tags-general-settings',
				array(
					'color'    => isset( $_POST['color'] ) ? sanitize_text_field( wp_unslash( $_POST['color'] ) ) : '#000000',
					'bg-color' => isset( $_POST['bg-color'] ) ? sanitize_text_field( wp_unslash( $_POST['bg-color'] ) ) : '#ffc300',
				)
			);

			wp_die();
		}

		/**
		 * Reset general settings
		 *
		 * @return void
		 */
		public static function reset_general_settings() {

			if ( check_ajax_referer( 'wpsc_ticket_tags_reset_general_settings', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorized!', 401 );
			}

			self::reset();
			wp_die();
		}
	}
endif;

WPSC_Ticket_Tags_Settings_General::init();
