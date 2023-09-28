<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Appearence_Ticket_List' ) ) :

	final class WPSC_Appearence_Ticket_List {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// user interface.
			add_action( 'wp_ajax_wpsc_get_ap_ticket_list', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_ap_ticket_list', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_ap_ticket_list', array( __CLASS__, 'reset_settings' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			update_option(
				'wpsc-ap-ticket-list',
				array(
					'list-header-background-color'     => '#2c3e50',
					'list-header-text-color'           => '#fff',
					'list-item-odd-background-color'   => '#fff',
					'list-item-odd-text-color'         => '#2c3e50',
					'list-item-even-background-color'  => '#f2f2f2',
					'list-item-even-text-color'        => '#2c3e50',
					'list-item-hover-background-color' => '#dfe4ea',
					'list-item-hover-text-color'       => '#2c3e50',
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

			$settings = get_option( 'wpsc-ap-ticket-list' );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-ap-tl">

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'List header', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Text color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="list-header-background-color" value="<?php echo esc_attr( $settings['list-header-background-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="list-header-text-color" value="<?php echo esc_attr( $settings['list-header-text-color'] ); ?>"></td>
						</tr>
					</table>
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'List item (odd)', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Text color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="list-item-odd-background-color" value="<?php echo esc_attr( $settings['list-item-odd-background-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="list-item-odd-text-color" value="<?php echo esc_attr( $settings['list-item-odd-text-color'] ); ?>"></td>
						</tr>
					</table>
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'List item (even)', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Text color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="list-item-even-background-color" value="<?php echo esc_attr( $settings['list-item-even-background-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="list-item-even-text-color" value="<?php echo esc_attr( $settings['list-item-even-text-color'] ); ?>"></td>
						</tr>
					</table>
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'List item (hover)', 'supportcandy' ); ?></label>
					</div>
					<table class="wpsc-ap-table">
						<tr>
							<td><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></td>
							<td><?php esc_attr_e( 'Text color', 'supportcandy' ); ?></td>
						</tr>
						<tr>
							<td><input class="wpsc-color-picker" type="text" name="list-item-hover-background-color" value="<?php echo esc_attr( $settings['list-item-hover-background-color'] ); ?>"></td>
							<td><input class="wpsc-color-picker" type="text" name="list-item-hover-text-color" value="<?php echo esc_attr( $settings['list-item-hover-text-color'] ); ?>"></td>
						</tr>
					</table>
				</div>

				<input type="hidden" name="action" value="wpsc_set_ap_ticket_list">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_ap_ticket_list' ) ); ?>">

				<script>jQuery('.wpsc-color-picker').wpColorPicker();</script>
				<style>
					.wpsc-ap-table td {padding: 5px;}
					.wpsc-ap-table table, .wpsc-ap-table td {border: 1px solid #c3c3c3;}
				</style>

			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_ap_ticket_list(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_ap_ticket_list(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_ap_ticket_list' ) ); ?>');">
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

			if ( check_ajax_referer( 'wpsc_set_ap_ticket_list', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$list_header_bg_color = isset( $_POST['list-header-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['list-header-background-color'] ) ) : '';
			if ( ! $list_header_bg_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$list_header_text_color = isset( $_POST['list-header-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['list-header-text-color'] ) ) : '';
			if ( ! $list_header_text_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$list_item_odd_bg_color = isset( $_POST['list-item-odd-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['list-item-odd-background-color'] ) ) : '';
			if ( ! $list_item_odd_bg_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$list_item_odd_text_color = isset( $_POST['list-item-odd-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['list-item-odd-text-color'] ) ) : '';
			if ( ! $list_item_odd_text_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$list_item_even_bg_color = isset( $_POST['list-item-even-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['list-item-even-background-color'] ) ) : '';
			if ( ! $list_item_even_bg_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$list_item_even_text_color = isset( $_POST['list-item-even-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['list-item-even-text-color'] ) ) : '';
			if ( ! $list_item_even_text_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$list_item_hover_bg_color = isset( $_POST['list-item-hover-background-color'] ) ? sanitize_text_field( wp_unslash( $_POST['list-item-hover-background-color'] ) ) : '';
			if ( ! $list_item_hover_bg_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$list_item_hover_text_color = isset( $_POST['list-item-hover-text-color'] ) ? sanitize_text_field( wp_unslash( $_POST['list-item-hover-text-color'] ) ) : '';
			if ( ! $list_item_hover_text_color ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			update_option(
				'wpsc-ap-ticket-list',
				array(
					'list-header-background-color'     => $list_header_bg_color,
					'list-header-text-color'           => $list_header_text_color,
					'list-item-odd-background-color'   => $list_item_odd_bg_color,
					'list-item-odd-text-color'         => $list_item_odd_text_color,
					'list-item-even-background-color'  => $list_item_even_bg_color,
					'list-item-even-text-color'        => $list_item_even_text_color,
					'list-item-hover-background-color' => $list_item_hover_bg_color,
					'list-item-hover-text-color'       => $list_item_hover_text_color,
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

			if ( check_ajax_referer( 'wpsc_reset_ap_ticket_list', '_ajax_nonce', false ) != 1 ) {
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

WPSC_Appearence_Ticket_List::init();
