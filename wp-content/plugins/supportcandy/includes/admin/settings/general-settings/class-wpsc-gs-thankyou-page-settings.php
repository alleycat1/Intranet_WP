<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_GS_Thankyou_Page_Settings' ) ) :

	final class WPSC_GS_Thankyou_Page_Settings {

		/**
		 *
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// User interface.
			add_action( 'wp_ajax_wpsc_get_gs_thankyou', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_gs_thankyou', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_gs_thankyou', array( __CLASS__, 'reset_settings' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			$wpsc_thankyou_html = '<p>Thanks for reaching out, we\'ve received your request!</p><p>{{ticket_url}}</p>';
			$thank_you_page_settings = apply_filters(
				'wpsc_gs_thank_you_page_settings',
				array(
					'action-agent'      => 'text',
					'action-customer'   => 'text',
					'html-agent'        => $wpsc_thankyou_html,
					'html-customer'     => $wpsc_thankyou_html,
					'page-url-agent'    => '',
					'page-url-customer' => '',
					'editor-agent'      => 'html',
					'editor-customer'   => 'html',
				)
			);
			update_option( 'wpsc-gs-thankyou-page-settings', $thank_you_page_settings );
			WPSC_Translations::remove( 'wpsc-thankyou-html-agent', $thank_you_page_settings['html-agent'] );
			WPSC_Translations::remove( 'wpsc-thankyou-html', $thank_you_page_settings['html-customer'] );
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
			$settings = get_option( 'wpsc-gs-thankyou-page-settings', array() );
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-gs-thankyoupage">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/thank-you-page/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<h3><?php esc_attr_e( 'Agent', 'supportcandy' ); ?></h3>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Choose action after creating a ticket', 'supportcandy' ); ?></label>
					</div>
					<select class="wpsc-action-agent" name="action-agent">
						<option <?php selected( $settings['action-agent'], 'text' ); ?> value="text"><?php esc_attr_e( 'Thank you text', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['action-agent'], 'url' ); ?> value="url"><?php esc_attr_e( 'Page url', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['action-agent'], 'ticket' ); ?> value="ticket"><?php esc_attr_e( 'Open ticket', 'supportcandy' ); ?></option>
					</select>
				</div>

				<?php $display_url = $settings['action-agent'] === 'url' ? '' : 'display:none'; ?>
				<div class="wpsc-input-group" id="wpsc-thank-you-url-agent" style="<?php echo esc_attr( $display_url ); ?>">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Page url', 'supportcandy' ); ?></label>
					</div>
					<input type="text" name="page-url-agent" value="<?php echo esc_url( $settings['page-url-agent'] ); ?>" autocomplete="off"/>
				</div>

				<?php $display_text = $settings['action-agent'] === 'text' ? '' : 'display:none'; ?>
				<div class="wpsc-input-group" id="wpsc-thank-you-text-agent" style="<?php echo esc_attr( $display_text ); ?>">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Thank you text', 'supportcandy' ); ?></label>
					</div>
					<div class = "textarea-container ">
						<div class = "wpsc_tinymce_editor_btns">
							<div class="inner-container">
								<button class="visual wpsc-switch-editor wpsc-switch-editor-agent <?php echo $settings['editor-agent'] == 'html' ? 'active' : ''; ?>" type="button" onclick="wpsc_get_tinymce(this, 'wpsc-html-agent','thankyou_body');"><?php esc_attr_e( 'Visual', 'supportcandy' ); ?></button>
								<button class="text wpsc-switch-editor wpsc-switch-editor-agent <?php echo $settings['editor-agent'] == 'text' ? 'active' : ''; ?>" type="button" onclick="wpsc_get_textarea(this, 'wpsc-html-agent')"><?php esc_attr_e( 'Text', 'supportcandy' ); ?></button>
							</div>
						</div>
						<?php
						$thank_you = $settings['html-agent'] ? WPSC_Translations::get( 'wpsc-thankyou-html-agent', stripslashes( $settings['html-agent'] ) ) : stripslashes( $settings['html-agent'] );
						?>
						<textarea name="wpsc-html-agent" id="wpsc-html-agent" class="wpsc_textarea"><?php echo wp_kses_post( $thank_you ); ?></textarea>
						<div class="wpsc-it-editor-action-container">
							<div class="actions">
								<div class="wpsc-editor-actions">
									<span class="wpsc-link" onclick="wpsc_get_macros()"><?php esc_attr_e( 'Insert Macro', 'supportcandy' ); ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<hr>
				<h3><?php esc_attr_e( 'Customer', 'supportcandy' ); ?></h3>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Choose action after creating a ticket', 'supportcandy' ); ?></label>
					</div>
					<select class="wpsc-action-customer" name="action-customer">
						<option <?php selected( $settings['action-customer'], 'text' ); ?> value="text"><?php esc_attr_e( 'Thank you text', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['action-customer'], 'url' ); ?> value="url"><?php esc_attr_e( 'Page url', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['action-customer'], 'ticket' ); ?> value="ticket"><?php esc_attr_e( 'Open ticket', 'supportcandy' ); ?></option>
					</select>
				</div>

				<?php $display_text = $settings['action-customer'] === 'text' ? '' : 'display:none'; ?>
				<div class="wpsc-input-group" id="wpsc-thank-you-text-customer" style="<?php echo esc_attr( $display_text ); ?>">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Thank you text', 'supportcandy' ); ?></label>
					</div>
					<div class="textarea-container">
						<div class="wpsc_tinymce_editor_btns">
							<div class="inner-container">
								<button class="visual wpsc-switch-editor wpsc-switch-editor-customer <?php echo $settings['editor-customer'] == 'html' ? 'active' : ''; ?>" type="button" onclick="wpsc_get_tinymce(this, 'wpsc-html-customer','thankyou_body');"><?php esc_attr_e( 'Visual', 'supportcandy' ); ?></button>
								<button class="text wpsc-switch-editor wpsc-switch-editor-customer <?php echo $settings['editor-customer'] == 'text' ? 'active' : ''; ?>" type="button" onclick="wpsc_get_textarea(this, 'wpsc-html-customer')"><?php esc_attr_e( 'Text', 'supportcandy' ); ?></button>
							</div>
						</div>
						<?php
						$thank_you = $settings['html-customer'] ? WPSC_Translations::get( 'wpsc-thankyou-html', stripslashes( $settings['html-customer'] ) ) : stripslashes( $settings['html-customer'] );
						?>
						<textarea name="wpsc-html-customer" id="wpsc-html-customer" class="wpsc_textarea"><?php echo wp_kses_post( $thank_you ); ?></textarea>
						<div class="wpsc-it-editor-action-container">
							<div class="actions">
								<div class="wpsc-editor-actions">
									<span class="wpsc-link" onclick="wpsc_get_macros()"><?php esc_attr_e( 'Insert Macro', 'supportcandy' ); ?></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php $display_url = $settings['action-customer'] === 'url' ? '' : 'display:none'; ?>
				<div class="wpsc-input-group" id="wpsc-thank-you-url-customer" style="<?php echo esc_attr( $display_url ); ?>">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Page url', 'supportcandy' ); ?></label>
					</div>
					<input type="text" name="page-url-customer" value="<?php echo esc_url( $settings['page-url-customer'] ); ?>" autocomplete="off"/>
				</div>

				<?php do_action( 'wpsc_gs_thankyou_page' ); ?>
				<input type="hidden" name="action" value="wpsc_set_gs_thankyou">
				<input id="editor-agent" type="hidden" name="editor-agent" value="<?php echo esc_attr( $settings['editor-agent'] ); ?>">
				<input id="editor-customer" type="hidden" name="editor-customer" value="<?php echo esc_attr( $settings['editor-customer'] ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_gs_thankyou' ) ); ?>">
			</form>

			<script>
				<?php
				if ( $settings['editor-agent'] == 'html' ) {
					?>
					jQuery('.wpsc-switch-editor-agent.visual').trigger('click');
					<?php
				}
				if ( $settings['editor-customer'] == 'html' ) {
					?>
					jQuery('.wpsc-switch-editor-customer.visual').trigger('click');
					<?php
				}
				?>

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
					if( selector == 'wpsc-html-agent' ) {
						jQuery('#editor-agent').val('html');
					}else{
						jQuery('#editor-customer').val('html');
					}
				}

				function wpsc_get_textarea(el, selector){
					console.log('test');
					jQuery(el).parent().find('.visual').removeClass('active');
					jQuery(el).addClass('active');
					tinymce.remove('#'+selector);
					if( selector == 'wpsc-html-agent' ) {
						jQuery('#editor-agent').val('text');
					}else{
						jQuery('#editor-customer').val('text');
					}
				}

				jQuery('.wpsc-action-agent').change(function() {
					if (this.value=='url') {
						jQuery('#wpsc-thank-you-text-agent').hide();
						jQuery('#wpsc-thank-you-url-agent').show();
					} else if(this.value=='text') {
						jQuery('#wpsc-thank-you-url-agent').hide(); 
						jQuery('#wpsc-thank-you-text-agent').show();
					}else {
						jQuery('#wpsc-thank-you-text-agent').hide();
						jQuery('#wpsc-thank-you-url-agent').hide(); 
					}
				});

				jQuery('.wpsc-action-customer').change(function() {
					if (this.value=='url') {
						jQuery('#wpsc-thank-you-text-customer').hide();
						jQuery('#wpsc-thank-you-url-customer').show();
					} else if(this.value=='text') {
						jQuery('#wpsc-thank-you-url-customer').hide(); 
						jQuery('#wpsc-thank-you-text-customer').show();
					}else {
						jQuery('#wpsc-thank-you-text-customer').hide();
						jQuery('#wpsc-thank-you-url-customer').hide(); 
					}
				});
			</script>

			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_gs_thankyou(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_gs_thankyou(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_gs_thankyou' ) ); ?>');">
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

			if ( check_ajax_referer( 'wpsc_set_gs_thankyou', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$thankyou_page = apply_filters(
				'wpsc_set_gs_thankyou_page',
				array(
					'html-agent'        => isset( $_POST['html-agent'] ) ? wp_kses_post( wp_unslash( $_POST['html-agent'] ) ) : '',
					'action-agent'      => isset( $_POST['action-agent'] ) ? sanitize_text_field( wp_unslash( $_POST['action-agent'] ) ) : '',
					'page-url-agent'    => isset( $_POST['page-url-agent'] ) ? sanitize_text_field( wp_unslash( $_POST['page-url-agent'] ) ) : '',
					'editor-agent'      => isset( $_POST['editor-agent'] ) ? sanitize_text_field( wp_unslash( $_POST['editor-agent'] ) ) : 'html',
					'html-customer'     => isset( $_POST['html-customer'] ) ? wp_kses_post( wp_unslash( $_POST['html-customer'] ) ) : '',
					'action-customer'   => isset( $_POST['action-customer'] ) ? sanitize_text_field( wp_unslash( $_POST['action-customer'] ) ) : '',
					'page-url-customer' => isset( $_POST['page-url-customer'] ) ? sanitize_text_field( wp_unslash( $_POST['page-url-customer'] ) ) : '',
					'editor-customer'   => isset( $_POST['editor-customer'] ) ? sanitize_text_field( wp_unslash( $_POST['editor-customer'] ) ) : 'html',
				)
			);
			update_option( 'wpsc-gs-thankyou-page-settings', $thankyou_page );

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-thankyou-html' );
			WPSC_Translations::remove( 'wpsc-thankyou-html-agent' );

			// add string translations.
			WPSC_Translations::add( 'wpsc-thankyou-html-agent', $thankyou_page['html-agent'] );
			WPSC_Translations::add( 'wpsc-thankyou-html', $thankyou_page['html-customer'] );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( check_ajax_referer( 'wpsc_reset_gs_thankyou', '_ajax_nonce', false ) != 1 ) {
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

WPSC_GS_Thankyou_Page_Settings::init();
