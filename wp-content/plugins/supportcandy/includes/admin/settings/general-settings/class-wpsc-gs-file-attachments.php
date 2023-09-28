<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_GS_File_Attachments' ) ) :

	final class WPSC_GS_File_Attachments {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// User interface.
			add_action( 'wp_ajax_wpsc_get_gs_file_attachments', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_gs_file_attachments', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_gs_file_attachments', array( __CLASS__, 'reset_settings' ) );

			// File upload js function.
			add_action( 'wpsc_js_ticket_form_functions', array( __CLASS__, 'js_upload_function' ) );
			add_action( 'wpsc_js_my_profile_functions', array( __CLASS__, 'js_upload_function' ) );
			add_action( 'wpsc_js_it_functions', array( __CLASS__, 'js_upload_function' ) );
			add_action( 'wpsc_js_customer_list_functions', array( __CLASS__, 'js_upload_function' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			$file_attachments = apply_filters(
				'wpsc_gs_file_attachments',
				array(
					'attachments-max-filesize' => 20,
					'allowed-file-extensions'  => 'jpg, jpeg, png, gif, pdf, doc, docx, ppt, pptx, pps, ppsx, odt, xls, xlsx, mp3, m4a, ogg, wav, mp4, m4v, mov, wmv, avi, mpg, ogv, 3gp, 3g2, zip, eml',
					'image-download-behaviour' => 'open-browser',
				)
			);
			update_option( 'wpsc-gs-file-attachments', $file_attachments );
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
			$settings = get_option( 'wpsc-gs-file-attachments', array() );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-gs-fa">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/file-attachments/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Attachment max file size(mb)', 'supportcandy' ); ?></label>
					</div>
					<input id="wpsc-attachfile" type="text" name="attachments-max-filesize" value="<?php echo intval( $settings['attachments-max-filesize'] ); ?>">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed file extensions(comma separated)', 'supportcandy' ); ?></label>
					</div>
					<input id="wpsc-allowed-file" type="text" name="allowed-file-extensions" value="<?php echo esc_attr( $settings['allowed-file-extensions'] ); ?>">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Download behaviour', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-db" name="image-download-behaviour">
						<option <?php selected( $settings['image-download-behaviour'], 'open-browser' ); ?> value="open-browser"><?php esc_attr_e( 'Try opening in browser', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['image-download-behaviour'], 'download' ); ?> value="download"><?php esc_attr_e( 'Download', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php do_action( 'wpsc_gs_file_attachments' ); ?>
				<input type="hidden" name="action" value="wpsc_set_gs_file_attachments">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_gs_file_attachments' ) ); ?>">
			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_gs_file_attachments(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_gs_file_attachments(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_gs_file_attachments' ) ); ?>');">
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

			if ( check_ajax_referer( 'wpsc_set_gs_file_attachments', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$file_attachments = apply_filters(
				'wpsc_set_gs_file_attachments',
				array(
					'attachments-max-filesize' => isset( $_POST['attachments-max-filesize'] ) ? intval( $_POST['attachments-max-filesize'] ) : 20,
					'allowed-file-extensions'  => isset( $_POST['allowed-file-extensions'] ) ? sanitize_text_field( wp_unslash( $_POST['allowed-file-extensions'] ) ) : '',
					'image-download-behaviour' => isset( $_POST['image-download-behaviour'] ) ? sanitize_text_field( wp_unslash( $_POST['image-download-behaviour'] ) ) : 'open-browser',
				)
			);
			update_option( 'wpsc-gs-file-attachments', $file_attachments );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( check_ajax_referer( 'wpsc_reset_gs_file_attachments', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			self::reset();
			wp_die();
		}

		/**
		 * Print file upload JS function with reCaptcha compatibility
		 *
		 * @return void
		 */
		public static function js_upload_function() {

			$recaptcha = get_option( 'wpsc-recaptcha-settings' );
			?>

			/**
			 * Upload given file 
			 */
			function wpsc_file_upload(fileAttachment, uniqueId, slug, single = false) {

				var file_name_split = fileAttachment.name.split('.');
				var file_extension = file_name_split[file_name_split.length-1];
				file_extension = file_extension.toLowerCase();	
				<?php
				$file_settings = get_option( 'wpsc-gs-file-attachments' );
				?>
				if (jQuery.inArray(file_extension, supportcandy.allowed_file_extensions) < 0) {
					alert("<?php esc_attr_e( 'Attached file type not allowed!', 'supportcandy' ); ?>");
					return;
				}

				var current_filesize = fileAttachment.size/1000000;
				if (current_filesize > <?php echo intval( $file_settings['attachments-max-filesize'] ); ?>) {
					<?php /* translators: %1$s: attachment max file size in MB */ ?>
					alert("<?php printf( esc_attr__( 'File size exceeds allowed limit (%1$s MB)!', 'supportcandy' ), intval( $file_settings['attachments-max-filesize'] ) ); ?>");
					return;
				}

				var htmlSnippet = jQuery('.wpsc-page-snippets .wpsc-editor-attachment').first().prop('outerHTML');
				var attachmentContainer = jQuery('.wpsc-editor-attachment-container.' + uniqueId).append(htmlSnippet);
				var attachment = attachmentContainer.children().last();
				attachment.find('.attachment-label').text(fileAttachment.name);
				attachment.find('.attachment-waiting').circleProgress({
					startAngle: -Math.PI / 4 * 3,
					size: 20,
					value: 0.0,
					lineCap: 'round',
					fill: { gradient: ['#ff1e41', '#ff5f43'] }
				});
				attachment.find('.attachment-remove').data({ single, uniqueid: uniqueId });

				// Append form data
				var dataform = new FormData();
				dataform.append('wpscFileAttachment', fileAttachment);
				dataform.append('action', 'wpsc_file_upload');
				dataform.append('_ajax_nonce', '<?php echo esc_attr( wp_create_nonce( 'wpsc_file_upload' ) ); ?>');

				<?php
				if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 3 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
					?>
					grecaptcha.ready(function() {
						grecaptcha.execute('<?php echo esc_attr( $recaptcha['recaptcha-site-key'] ); ?>', {action: 'file_upload'}).then(function(token) {
							dataform.append('g-recaptcha-response', token);
							jQuery.ajax({
								url: supportcandy.ajax_url,
								type: 'POST',
								data: dataform,
								xhr: function () {
									var myXhr = jQuery.ajaxSettings.xhr();
									if (myXhr.upload) {
										myXhr.upload.addEventListener('progress', wpscAttachmentUploadProgress, false);
										myXhr.upload.attachment = attachment;
										myXhr.attachment = attachment;
										myXhr.uniqueId = uniqueId;
										myXhr.single = single;
									}
									return myXhr;
								},
								processData: false,
								contentType: false,
								error: function (data) {

									// Upload not accepted
									attachment.removeClass('upload-waiting');
									attachment.addClass('upload-error');
								},
								success: function (response, textStatus, xhr) {
									if (xhr.status === 200) {
										// Upload created
										var attachmentInput = '<input type="hidden" name="' + slug + '[]" value="' + response.id + '"/>';
										attachment.append(attachmentInput);
										attachment.removeClass('upload-waiting');
										attachment.addClass('upload-success');

										if(single) {
											jQuery( 'input.' + uniqueId ).hide();
										}
									} else {

										// Upload not accepted
										attachment.removeClass('upload-waiting');
										attachment.addClass('upload-error');
									}
								}
							});
						});
					});
					<?php

				} else {
					?>

					jQuery.ajax({
						url: supportcandy.ajax_url,
						type: 'POST',
						data: dataform,
						xhr: function () {
							var myXhr = jQuery.ajaxSettings.xhr();
							if (myXhr.upload) {
								myXhr.upload.addEventListener('progress', wpscAttachmentUploadProgress, false);
								myXhr.upload.attachment = attachment;
								myXhr.attachment = attachment;
								myXhr.uniqueId = uniqueId;
								myXhr.single = single;
							}
							return myXhr;
						},
						processData: false,
						contentType: false,
						error: function (data) {

							// Upload not accepted
							attachment.removeClass('upload-waiting');
							attachment.addClass('upload-error');
						},
						success: function (response, textStatus, xhr) {
							if (xhr.status === 200) {
								// Upload created
								var attachmentInput = '<input type="hidden" name="' + slug + '[]" value="' + response.id + '"/>';
								attachment.append(attachmentInput);
								attachment.removeClass('upload-waiting');
								attachment.addClass('upload-success');

								if(single) {
									jQuery( 'input.' + uniqueId ).hide();
								}
							} else {

								// Upload not accepted
								attachment.removeClass('upload-waiting');
								attachment.addClass('upload-error');
							}
						}
					});
					<?php
				}
				?>

			}
			<?php
		}
	}
endif;

WPSC_GS_File_Attachments::init();


