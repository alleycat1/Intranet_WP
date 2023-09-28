<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_EN_User_Reg_OTP' ) ) :

	final class WPSC_EN_User_Reg_OTP extends WPSC_Email_Notifications {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// template settings.
			add_action( 'wp_ajax_wpsc_get_en_user_reg_otp', array( __CLASS__, 'get_settings' ) );
			add_action( 'wp_ajax_wpsc_set_en_user_reg_otp', array( __CLASS__, 'set_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_en_user_reg_otp', array( __CLASS__, 'reset_settings' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			$user_otp = array(
				'subject' => 'User registration OTP - {{otp}}',
				'body'    => '<p>Hello {{firstname}},</p><p>Below is your one time password for user registration:</p><p><strong>{{otp}}</strong></p>',
				'editor'  => 'html',
			);
			update_option( 'wpsc-en-user-reg', $user_otp );
			WPSC_Translations::remove( 'wpsc-user-otp-subject', $user_otp['subject'] );
			WPSC_Translations::remove( 'wpsc-user-otp-body', $user_otp['body'] );
		}

		/**
		 * Get template settings
		 *
		 * @return void
		 */
		public static function get_settings() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$settings = get_option( 'wpsc-en-user-reg' )?>

			<div class="wpsc-setting-header">
				<h2><?php esc_attr_e( 'User Registration OTP', 'supportcandy' ); ?></h2>
			</div>
			<div class="wpsc-setting-section-body">
				<form action="#" onsubmit="return false;" class="wpsc-frm-en-user-reg-otp">
					<div class="wpsc-dock-container">
						<?php
						printf(
							/* translators: Click here to see the documentation */
							esc_attr__( '%s to see the documentation!', 'supportcandy' ),
							'<a href="https://supportcandy.net/docs/user-registration-otp/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
						);
						?>
					</div>
					<div class="wpsc-input-group">
						<div class="label-container"><label for=""><?php esc_attr_e( 'Subject', 'supportcandy' ); ?></label></div>
						<?php
						$subject = $settings['subject'] ? WPSC_Translations::get( 'wpsc-user-otp-subject', stripslashes( $settings['subject'] ) ) : stripslashes( $settings['subject'] );
						?>
						<input type="text" name="subject" value="<?php echo esc_attr( $subject ); ?>" autocomplete="off" />
					</div>
					<div class="wpsc-input-group">
						<div class="label-container">
							<label for=""><?php esc_attr_e( 'Body', 'supportcandy' ); ?></label>
						</div>
						<div class="textarea-container ">
							<div class = "wpsc_tinymce_editor_btns">
								<div class="inner-container">
									<button class="visual wpsc-switch-editor <?php echo esc_attr( $settings['editor'] ) == 'html' ? 'active' : ''; ?>" type="button" onclick="wpsc_get_tinymce(this, 'wpsc-en-body','wpsc_en_body');"><?php esc_attr_e( 'Visual', 'supportcandy' ); ?></button>
									<button class="text wpsc-switch-editor <?php echo esc_attr( $settings['editor'] ) == 'text' ? 'active' : ''; ?>" type="button" onclick="wpsc_get_textarea(this, 'wpsc-en-body')"><?php esc_attr_e( 'Text', 'supportcandy' ); ?></button>
								</div>
							</div>
							<?php
							$body = $settings['body'] ? WPSC_Translations::get( 'wpsc-user-otp-body', stripslashes( $settings['body'] ) ) : stripslashes( $settings['body'] );
							?>
							<textarea id="wpsc-en-body" name="body" class="wpsc_textarea"><?php echo wp_kses_post( $body ); ?></textarea>
						</div>
						<small>{{otp}} - <?php esc_attr_e( 'One time password', 'supportcandy' ); ?></small>
						<small>{{username}} - <?php esc_attr_e( 'Username', 'supportcandy' ); ?></small>
						<small>{{firstname}} - <?php esc_attr_e( 'First name', 'supportcandy' ); ?></small>
						<small>{{lastname}} - <?php esc_attr_e( 'Last name', 'supportcandy' ); ?></small>
						<small>{{fullname}} - <?php esc_attr_e( 'Full name', 'supportcandy' ); ?></small>
						<small>{{email}} - <?php esc_attr_e( 'Email address', 'supportcandy' ); ?></small>
						<script>
							<?php
							if ( $settings['editor'] == 'html' ) :
								?>
								jQuery('.wpsc-switch-editor.visual').trigger('click');
								<?php
							endif;
							?>
							/**
							 * Switch to editor
							 */
							function wpsc_get_tinymce(el, selector, body_id){
								jQuery(el).parent().find('.text').removeClass('active');
								jQuery(el).addClass('active');
								tinymce.remove('#'+selector);
								tinymce.init({ 
									selector:'#'+selector,
									body_id: body_id,
									menubar: false,
									statusbar: false,
									height : '200',
									plugins: [
									'lists link image directionality paste'
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
									paste_as_text: true,
									setup: function (editor) {
									}
								});
								jQuery('#editor').val('html');
							}
							/**
							 * Switch to plain text
							 */
							function wpsc_get_textarea(el, selector){
								jQuery(el).parent().find('.visual').removeClass('active');
								jQuery(el).addClass('active');
								tinymce.remove('#'+selector);
								jQuery('#editor').val('text');
							}
						</script>
					</div>
					<?php do_action( 'wpsc_en_user_registration_otp' ); ?>
					<input type="hidden" name="action" value="wpsc_set_en_user_reg_otp">
					<input id="editor" type="hidden" name="editor" value="<?php echo esc_attr( $settings['editor'] ); ?>">
					<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_en_user_reg_otp' ) ); ?>">
				</form>
				<div class="setting-footer-actions">
					<button 
						class="wpsc-button normal primary margin-right"
						onclick="wpsc_set_en_user_reg_otp(this);">
						<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
					<button 
						class="wpsc-button normal secondary"
						onclick="wpsc_reset_en_user_reg_otp(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_en_user_reg_otp' ) ); ?>');">
						<?php esc_attr_e( 'Reset default', 'supportcandy' ); ?></button>
				</div>
			</div>
			<?php
			wp_die();
		}

		/**
		 * Save settings
		 *
		 * @return void
		 */
		public static function set_settings() {

			if ( check_ajax_referer( 'wpsc_set_en_user_reg_otp', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
			if ( ! $subject ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$body = isset( $_POST['body'] ) ? wp_kses_post( wp_unslash( $_POST['body'] ) ) : '';
			if ( ! $body ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$editor = isset( $_POST['editor'] ) ? sanitize_text_field( wp_unslash( $_POST['editor'] ) ) : 'html';

			update_option(
				'wpsc-en-user-reg',
				array(
					'subject' => $subject,
					'body'    => $body,
					'editor'  => $editor,
				)
			);

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-user-otp-subject' );
			WPSC_Translations::remove( 'wpsc-user-otp-body' );

			// add string translations.
			WPSC_Translations::add( 'wpsc-user-otp-subject', $subject );
			WPSC_Translations::add( 'wpsc-user-otp-body', $body );
			wp_die();
		}

		/**
		 * Reset this settings
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( check_ajax_referer( 'wpsc_reset_en_user_reg_otp', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			self::reset();
			wp_die();
		}

		/**
		 * Send OTP
		 *
		 * @param WPSC_Email_OTP $otp - otp object.
		 * @return bool
		 */
		public static function send_otp( $otp ) {

			$settings = get_option( 'wpsc-en-user-reg' );
			$en       = new self();

			// from name & email.
			$en_general = get_option( 'wpsc-en-general' );
			if ( ! $en_general['from-name'] || ! $en_general['from-email'] ) {
				return false;
			}
			$en->from_name  = $en_general['from-name'];
			$en->from_email = $en_general['from-email'];
			$en->reply_to   = $en_general['reply-to'] ? $en_general['reply-to'] : $en->from_email;

			$subject = $settings['subject'] ? WPSC_Translations::get( 'wpsc-user-otp-subject', stripslashes( $settings['subject'] ) ) : stripslashes( $settings['subject'] );
			$body    = $settings['body'] ? WPSC_Translations::get( 'wpsc-user-otp-body', stripslashes( $settings['body'] ) ) : stripslashes( $settings['body'] );

			$en->subject = self::replace_macros( $subject, $otp );
			$en->body    = self::replace_macros( $body, $otp );
			$en->to      = array( $otp->email );
			$en->send();
		}

		/**
		 * Replace macros for OTP
		 *
		 * @param string         $str - email text.
		 * @param WPSC_Email_OTP $otp - otp object.
		 * @return string
		 */
		public static function replace_macros( $str, $otp ) {

			$data = json_decode( $otp->data );

			// get all macros within string so that will replace only matched.
			preg_match_all( '/{(\w*)}/', $str, $matches );
			$matches = isset( $matches[1] ) ? array_unique( $matches[1] ) : array();

			// replace matched tags.
			foreach ( $matches as $macro ) {

				switch ( $macro ) {

					case 'otp':
						$str = str_replace(
							'{{otp}}',
							$otp->otp,
							$str
						);
						break;

					case 'username':
						$str = str_replace(
							'{{username}}',
							$data->username,
							$str
						);
						break;

					case 'firstname':
						$str = str_replace(
							'{{firstname}}',
							stripslashes( $data->firstname ),
							$str
						);
						break;

					case 'lastname':
						$str = str_replace(
							'{{lastname}}',
							stripslashes( $data->lastname ),
							$str
						);
						break;

					case 'fullname':
						$display_name = stripslashes( $data->firstname ) . ' ' . stripslashes( $data->lastname );
						$str          = str_replace(
							'{{fullname}}',
							$display_name,
							$str
						);
						break;

					case 'email':
						$str = str_replace(
							'{{email}}',
							$otp->email,
							$str
						);
						break;
				}
			}

			return $str;
		}
	}
endif;

WPSC_EN_User_Reg_OTP::init();
