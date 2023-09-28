<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Appearence_Indidual_Ticket' ) ) :

	final class WPSC_Appearence_Indidual_Ticket {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// user interface.
			add_action( 'wp_ajax_wpsc_get_ap_individual_ticket', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_ap_individual_ticket', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_ap_individual_ticket', array( __CLASS__, 'reset_settings' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			update_option(
				'wpsc-ap-individual-ticket',
				array(

					'reply-primary-color'      => '#2c3e50',
					'reply-secondary-color'    => '#777777',
					'reply-icon-color'         => '#777777',

					'note-primary-color'       => '#8e6600',
					'note-secondary-color'     => '#8e8d45',
					'note-icon-color'          => '#8e8d45',

					'log-text'                 => '#2c3e50',

					'widget-header-bg-color'   => '#fff8e5',
					'widget-header-text-color' => '#ff8f2b',

					'widget-body-bg-color'     => '#f9f9f9',
					'widget-body-label-color'  => '#777',
					'widget-body-text-color'   => '#2c3e50',

				)
			);
		}

		/**
		 * Get general settings
		 *
		 * @return void
		 */
		public static function load_settings_ui() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$settings = get_option( 'wpsc-ap-individual-ticket' );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-ap-it">

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Reply thread', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php esc_attr_e( 'Primary color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Secondary color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Icon color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="reply-primary-color" value="<?php echo esc_attr( $settings['reply-primary-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="reply-secondary-color" value="<?php echo esc_attr( $settings['reply-secondary-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="reply-icon-color" value="<?php echo esc_attr( $settings['reply-icon-color'] ); ?>"></td>
						</tr>
					</table>
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Private note thread', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php esc_attr_e( 'Primary color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Secondary color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Icon color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="note-primary-color" value="<?php echo esc_attr( $settings['note-primary-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="note-secondary-color" value="<?php echo esc_attr( $settings['note-secondary-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="note-icon-color" value="<?php echo esc_attr( $settings['note-icon-color'] ); ?>"></td>
						</tr>
					</table>
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Log text', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" type="text" name="log-text" value="<?php echo esc_attr( $settings['log-text'] ); ?>">
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Widget header', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Text color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="widget-header-bg-color" value="<?php echo esc_attr( $settings['widget-header-bg-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="widget-header-text-color" value="<?php echo esc_attr( $settings['widget-header-text-color'] ); ?>"></td>
						</tr>
					</table>
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Widget body', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Label color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Text color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="widget-body-bg-color" value="<?php echo esc_attr( $settings['widget-body-bg-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="widget-body-label-color" value="<?php echo esc_attr( $settings['widget-body-label-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="widget-body-text-color" value="<?php echo esc_attr( $settings['widget-body-text-color'] ); ?>"></td>
						</tr>
					</table>
				</div>

				<input type="hidden" name="action" value="wpsc_set_ap_individual_ticket">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_ap_individual_ticket' ) ); ?>">

				<script>jQuery('.wpsc-color-picker').wpColorPicker();</script>
				<style>
					.wpsc-ap-table td {padding: 5px;}
					.wpsc-ap-table table, .wpsc-ap-table td {border: 1px solid #c3c3c3;}
				</style>

			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_ap_individual_ticket(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_ap_individual_ticket(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_ap_individual_ticket' ) ); ?>');">
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

			if ( check_ajax_referer( 'wpsc_set_ap_individual_ticket', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			update_option(
				'wpsc-ap-individual-ticket',
				array(

					'reply-primary-color'      => isset( $_POST['reply-primary-color'] ) ? sanitize_text_field( wp_unslash( $_POST['reply-primary-color'] ) ) : '#2c3e50',
					'reply-secondary-color'    => isset( $_POST['reply-secondary-color'] ) ? sanitize_text_field( wp_unslash( $_POST['reply-secondary-color'] ) ) : '#777',
					'reply-icon-color'         => isset( $_POST['reply-icon-color'] ) ? sanitize_text_field( wp_unslash( $_POST['reply-icon-color'] ) ) : '#777',

					'note-primary-color'       => isset( $_POST['note-primary-color'] ) ? sanitize_text_field( wp_unslash( $_POST['note-primary-color'] ) ) : '#8e6600',
					'note-secondary-color'     => isset( $_POST['note-secondary-color'] ) ? sanitize_text_field( wp_unslash( $_POST['note-secondary-color'] ) ) : '#8e8d45',
					'note-icon-color'          => isset( $_POST['note-icon-color'] ) ? sanitize_text_field( wp_unslash( $_POST['note-icon-color'] ) ) : '#8e8d45',

					'log-text'                 => isset( $_POST['log-text'] ) ? sanitize_text_field( wp_unslash( $_POST['log-text'] ) ) : '#2c3e50',

					'widget-header-bg-color'   => isset( $_POST['widget-header-bg-color'] ) ? sanitize_text_field( wp_unslash( $_POST['widget-header-bg-color'] ) ) : '#fff8e5',
					'widget-header-text-color' => isset( $_POST['widget-header-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['widget-header-text-color'] ) ) : '#ff8f2b',

					'widget-body-bg-color'     => isset( $_POST['widget-body-bg-color'] ) ? sanitize_text_field( wp_unslash( $_POST['widget-body-bg-color'] ) ) : '#f9f9f9',
					'widget-body-label-color'  => isset( $_POST['widget-body-label-color'] ) ? sanitize_text_field( wp_unslash( $_POST['widget-body-label-color'] ) ) : '#777',
					'widget-body-text-color'   => isset( $_POST['widget-body-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['widget-body-text-color'] ) ) : '#2c3e50',

				)
			);

			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( check_ajax_referer( 'wpsc_reset_ap_individual_ticket', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			self::reset();
			wp_die();
		}
	}
endif;

WPSC_Appearence_Indidual_Ticket::init();
