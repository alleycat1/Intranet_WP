<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_MS_TAC' ) ) :

	final class WPSC_MS_TAC {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// User interface.
			add_action( 'wp_ajax_wpsc_get_ms_term_and_conditions', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_ms_term_and_conditions', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_ms_term_and_conditions', array( __CLASS__, 'reset_settings' ) );

			// Print in create ticket form.
			add_action( 'wpsc_print_tff', array( __CLASS__, 'print_tff' ) );

			// TFF! validation.
			add_action( 'wpsc_js_validate_ticket_form', array( __CLASS__, 'js_validate_ticket_form' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			$wpsc_tandc_text = '<p>I agree to the terms and conditions</p>';
			$condition       = apply_filters(
				'wpsc_term_and_conditions',
				array(
					'allow-term-and-conditions' => 1,
					'tandc-text'                => $wpsc_tandc_text,
					'editor'                    => 'html',
				)
			);
			update_option( 'wpsc-term-and-conditions', $condition );
			WPSC_Translations::remove( 'wpsc-term-and-conditions', $wpsc_tandc_text );
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
			$settings = get_option( 'wpsc-term-and-conditions', array() );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-ms-tandc">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/terms-conditions/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-allow-term-and-conditions" name="allow-term-and-conditions">
						<option <?php selected( $settings['allow-term-and-conditions'], 1 ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['allow-term-and-conditions'], 0 ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Checkbox text', 'supportcandy' ); ?></label>
					</div>
					<div class="textarea-container ">
						<div class="wpsc_tinymce_editor_btns">
							<div class="inner-container">
								<button class="visual wpsc-switch-editor <?php echo esc_attr( $settings['editor'] ) == 'html' ? 'active' : ''; ?>" type="button" onclick="wpsc_get_tinymce(this, 'tandc-text','tandc_body');"><?php esc_attr_e( 'Visual', 'supportcandy' ); ?></button>
								<button class="text wpsc-switch-editor <?php echo esc_attr( $settings['editor'] ) == 'text' ? 'active' : ''; ?>" type="button" onclick="wpsc_get_textarea(this, 'tandc-text')"><?php esc_attr_e( 'Text', 'supportcandy' ); ?></button>
							</div>
						</div>
						<?php
						$tandc_text = $settings['tandc-text'] ? WPSC_Translations::get( 'wpsc-term-and-conditions', stripslashes( $settings['tandc-text'] ) ) : stripslashes( $settings['tandc-text'] );
						?>
						<textarea name="tandc-text" id="tandc-text" class = "wpsc_textarea"><?php echo wp_kses_post( $tandc_text ); ?></textarea>
					</div>
					<script>
						<?php
						if ( $settings['editor'] == 'html' ) {
							?>
							jQuery('.wpsc-switch-editor.visual').trigger('click');
							<?php
						} else {
							?>
							jQuery('.wpsc-switch-editor.text').trigger('click');
							<?php
						}
						?>

						function wpsc_get_tinymce(el, selector, body_id){
							jQuery(el).parent().find('.text').removeClass('active');
							jQuery(el).addClass('active');
							tinymce.remove();
							tinymce.init({ 
								selector:'#'+selector,
								body_id: body_id,
								menubar: false,
								statusbar: false,
								height : '200',
								plugins: [
								'lists link image directionality'
								],
								image_advtab: true,
								toolbar: 'bold italic underline blockquote | alignleft aligncenter alignright | bullist numlist | rtl | link image',
								directionality: '<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>',
								branding: false,
								autoresize_bottom_margin: 20,
								browser_spellcheck : true,
								relative_urls : false,
								remove_script_host : false,
								convert_urls : true,
								setup: function (editor) {
								}
							});
							jQuery('#editor').val('html');
						}

						function wpsc_get_textarea(el, selector){
							jQuery(el).parent().find('.visual').removeClass('active');
							jQuery(el).addClass('active');
							tinymce.remove();
							jQuery('#editor').val('text');
						}
					</script>
				</div>    
				<?php do_action( 'wpsc_ms_term_and_conditions' ); ?>
				<input type="hidden" name="action" value="wpsc_set_ms_term_and_conditions">
				<input id="editor" type="hidden" name="editor" value="<?php echo esc_attr( $settings['editor'] ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_ms_term_and_conditions' ) ); ?>">
			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_ms_term_and_conditions(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_ms_term_and_conditions(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_ms_term_and_conditions' ) ); ?>');">
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

			if ( check_ajax_referer( 'wpsc_set_ms_term_and_conditions', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$conditions = apply_filters(
				'wpsc_set_term_conditions',
				array(
					'allow-term-and-conditions' => isset( $_POST['allow-term-and-conditions'] ) ? intval( $_POST['allow-term-and-conditions'] ) : 1,
					'tandc-text'                => isset( $_POST ) && isset( $_POST['tandc-text'] ) ? wp_kses_post( wp_unslash( $_POST['tandc-text'] ) ) : '',
					'editor'                    => isset( $_POST['editor'] ) ? sanitize_text_field( wp_unslash( $_POST['editor'] ) ) : 'html',
				)
			);
			update_option( 'wpsc-term-and-conditions', $conditions );

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-term-and-conditions' );

			// add string translations.
			WPSC_Translations::add( 'wpsc-term-and-conditions', $conditions['tandc-text'] );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( check_ajax_referer( 'wpsc_reset_ms_term_and_conditions', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			self::reset();
			wp_die();
		}

		/**
		 * Print ticket form field
		 *
		 * @return void
		 */
		public static function print_tff() {

			$tc = get_option( 'wpsc-term-and-conditions' );
			if ( intval( $tc['allow-term-and-conditions'] ) === 1 ) :
				?>
				<div class="wpsc-tff term-and-conditions wpsc-xs-12 wpsc-sm-12 wpsc-md-12 wpsc-lg-12 required wpsc-visible" data-cft="term-and-conditions">
					<div class="checkbox-container">
						<?php $unique_id = uniqid( 'wpsc_' ); ?>
						<input id="<?php echo esc_attr( $unique_id ); ?>" type="checkbox" value="1"/>
						<?php
						$name = WPSC_Translations::get( 'wpsc-term-and-conditions', stripslashes( $tc['tandc-text'] ) );
						?>
						<label for="<?php echo esc_attr( $unique_id ); ?>"><?php echo wp_kses_post( $name ); ?></label>
					</div>
				</div>
				<?php
			endif;
		}

		/**
		 * Validate this type field in create ticket
		 *
		 * @return void
		 */
		public static function js_validate_ticket_form() {
			?>

			case 'term-and-conditions':
				var checkbox = customField.find('input:checked');
				if (checkbox.length === 0) {
					isValid = false;
					alert(supportcandy.translations.req_term_cond);
				}
				break;
			<?php
			echo PHP_EOL;
		}
	}
endif;

WPSC_MS_TAC::init();
