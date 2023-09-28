<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_TE_Agent' ) ) :

	final class WPSC_TE_Agent {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// User interface.
			add_action( 'wp_ajax_wpsc_get_te_agent', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_te_agent', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_te_agent', array( __CLASS__, 'reset_settings' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			$gs_file = get_option( 'wpsc-gs-file-attachments' );

			$attach_notice = sprintf(
				/* translators: %1$s: attachment max file size, %2$s: allowed file extenstions. */
				esc_attr__( 'You can upload files maximum size %1$s mb of types %2$s.', 'supportcandy' ),
				$gs_file['attachments-max-filesize'],
				$gs_file['allowed-file-extensions']
			);

			$agent = apply_filters(
				'wpsc_te_agent',
				array(
					'enable'                      => 1,
					'allow-attachments'           => 1,
					'toolbar'                     => array( 'bold', 'italic', 'underline', 'blockquote', 'alignleft aligncenter alignright', 'bullist', 'numlist', 'rtl', 'link', 'wpsc_insert_editor_img' ),
					'file-attachment-notice'      => 0,
					'file-attachment-notice-text' => $attach_notice,
				)
			);
			update_option( 'wpsc-te-agent', $agent );
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
			$settings = get_option( 'wpsc-te-agent', array() );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-te-agent">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/rich-text-editor/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-te-agent-enable" name="enable">
						<option <?php selected( $settings['enable'], 1 ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['enable'], 0 ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allow attachments', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-te-agent-allow-attach" name="allow-attachments">
						<option <?php selected( $settings['allow-attachments'], 1 ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['allow-attachments'], 0 ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Toolbar actions', 'supportcandy' ); ?></label>
					</div>
					<div class="checkboxes-group">
						<?php
						foreach ( WPSC_Text_Editor::$toolbar as $action ) :
							?>
							<div class="inner-group">
								<?php
								$checked = in_array( $action['value'], $settings['toolbar'] ) ? 'checked' : ''
								?>
								<input name="toolbar[]" type="checkbox" <?php echo esc_attr( $checked ); ?> value="<?php echo esc_attr( $action['value'] ); ?>">
								<?php echo esc_attr( $action['name'] ); ?>
							</div>
							<?php
						endforeach;
						?>
					</div>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Show file attachment notice', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-file-attachment-notice" name="file-attachment-notice">
						<option <?php selected( $settings['file-attachment-notice'], 1 ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['file-attachment-notice'], 0 ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
					<script>
						jQuery('#wpsc-file-attachment-notice').change(function() {
							if (this.value=='1') {			 
								jQuery('#wpsc-file-attachment-notice-text').show(); 
							} else {
								jQuery('#wpsc-file-attachment-notice-text').hide();
							}
						});
					</script>
				</div>
				<?php
				$dispaly = ! $settings['file-attachment-notice'] ? 'display:none' : '';
				?>
				<div class="wpsc-input-group" id="wpsc-file-attachment-notice-text" style="<?php echo esc_attr( $dispaly ); ?>">
					<input type="text" name="file-attachment-notice-text" value="<?php echo esc_attr( $settings['file-attachment-notice-text'] ); ?>" autocomplete="off">
				</div>
				<?php do_action( 'wpsc_te_agent' ); ?>
				<input type="hidden" name="action" value="wpsc_set_te_agent">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_te_agent' ) ); ?>">
			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_te_agent(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_te_agent(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_te_agent' ) ); ?>');">
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

			if ( check_ajax_referer( 'wpsc_set_te_agent', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$agent = apply_filters(
				'wpsc_set_te_agent',
				array(
					'enable'                      => isset( $_POST['enable'] ) ? intval( $_POST['enable'] ) : 1,
					'allow-attachments'           => isset( $_POST['allow-attachments'] ) ? intval( $_POST['allow-attachments'] ) : 1,
					'toolbar'                     => isset( $_POST['toolbar'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['toolbar'] ) ) ) : array(),
					'file-attachment-notice'      => isset( $_POST['file-attachment-notice'] ) ? intval( $_POST['file-attachment-notice'] ) : 0,
					'file-attachment-notice-text' => isset( $_POST['file-attachment-notice-text'] ) ? sanitize_text_field( wp_unslash( $_POST['file-attachment-notice-text'] ) ) : '',
				)
			);
			update_option( 'wpsc-te-agent', $agent );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( check_ajax_referer( 'wpsc_reset_te_agent', '_ajax_nonce', false ) != 1 ) {
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

WPSC_TE_Agent::init();
