<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Text_Editor' ) ) :

	final class WPSC_Text_Editor {

		/**
		 * Tabs for this section
		 *
		 * @var array
		 */
		private static $tabs;

		/**
		 * Current tab
		 *
		 * @var string
		 */
		public static $current_tab;

		/**
		 * Rich text editor toolbar
		 *
		 * @var array
		 */
		public static $toolbar = array();

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Load settings.
			add_action( 'init', array( __CLASS__, 'toolbar_options' ) );

			// Load tabs for this section.
			add_action( 'admin_init', array( __CLASS__, 'load_tabs' ) );

			// Add current tab to admin localization data.
			add_filter( 'wpsc_admin_localizations', array( __CLASS__, 'localizations' ) );

			// Load section tab layout.
			add_action( 'wp_ajax_wpsc_get_rich_text_editor', array( __CLASS__, 'get_rich_text_editor' ) );
		}

		/**
		 * Load toolbar settings for global use
		 *
		 * @return void
		 */
		public static function toolbar_options() {

			$toolbar = array(
				array(
					'name'  => esc_attr__( 'Bold', 'supportcandy' ),
					'value' => 'bold',
				),
				array(
					'name'  => esc_attr__( 'Italic', 'supportcandy' ),
					'value' => 'italic',
				),
				array(
					'name'  => esc_attr__( 'Underline', 'supportcandy' ),
					'value' => 'underline',
				),
				array(
					'name'  => esc_attr__( 'Blockquote', 'supportcandy' ),
					'value' => 'blockquote',
				),
				array(
					'name'  => esc_attr__( 'Align', 'supportcandy' ),
					'value' => 'alignleft aligncenter alignright',
				),
				array(
					'name'  => esc_attr__( 'Bulleted list', 'supportcandy' ),
					'value' => 'bullist',
				),
				array(
					'name'  => esc_attr__( 'Numbered list', 'supportcandy' ),
					'value' => 'numlist',
				),
				array(
					'name'  => esc_attr__( 'Right to left', 'supportcandy' ),
					'value' => 'rtl',
				),
				array(
					'name'  => esc_attr__( 'Link', 'supportcandy' ),
					'value' => 'link',
				),
				array(
					'name'  => esc_attr__( 'Image', 'supportcandy' ),
					'value' => 'wpsc_insert_editor_img',
				),
				array(
					'name'  => esc_attr__( 'Text Color', 'supportcandy' ),
					'value' => 'forecolor',
				),
				array(
					'name'  => esc_attr__( 'Text Background Color', 'supportcandy' ),
					'value' => 'backcolor',
				),
				array(
					'name'  => esc_attr__( 'Strikethrough', 'supportcandy' ),
					'value' => 'strikethrough',
				),
			);
			self::$toolbar = $toolbar;
		}

		/**
		 * Load tabs for this section
		 */
		public static function load_tabs() {

			self::$tabs        = apply_filters(
				'wpsc_te_tabs',
				array(
					'agent'           => array(
						'slug'     => 'agent',
						'label'    => esc_attr__( 'Agent', 'supportcandy' ),
						'callback' => 'wpsc_get_te_agent',
					),
					'registered-user' => array(
						'slug'     => 'registered_user',
						'label'    => esc_attr__( 'Registered User', 'supportcandy' ),
						'callback' => 'wpsc_get_te_registered_user',
					),
					'guest-user'      => array(
						'slug'     => 'guest_user',
						'label'    => esc_attr__( 'Guest User', 'supportcandy' ),
						'callback' => 'wpsc_get_te_guest_user',
					),
					'advanced'        => array(
						'slug'     => 'advanced',
						'label'    => esc_attr__( 'Advanced', 'supportcandy' ),
						'callback' => 'wpsc_get_te_advanced',
					),
				)
			);
			self::$current_tab = isset( $_REQUEST['tab'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tab'] ) ) : 'agent'; //phpcs:ignore
		}


		/**
		 * Add localizations to local JS
		 *
		 * @param array $localizations - localization list.
		 * @return array
		 */
		public static function localizations( $localizations ) {

			if ( ! ( WPSC_Settings::$is_current_page && WPSC_Settings::$current_section === 'rich-text-editor' ) ) {
				return $localizations;
			}

			// Current section.
			$localizations['current_tab'] = self::$current_tab;

			return $localizations;
		}

		/**
		 * General setion body layout
		 *
		 * @return void
		 */
		public static function get_rich_text_editor() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}?>

			<div class="wpsc-setting-tab-container">
				<?php
				foreach ( self::$tabs as $key => $tab ) :
					$active = self::$current_tab === $key ? 'active' : ''
					?>
						<button 
							class="<?php echo esc_attr( $key ) . ' ' . esc_attr( $active ); ?>"
							onclick="<?php echo esc_attr( $tab['callback'] ) . '();'; ?>">
							<?php echo esc_attr( $tab['label'] ); ?>
							</button>
							<?php
					endforeach;
				?>
			</div>
			<div class="wpsc-setting-section-body"></div>
			<?php
			wp_die();
		}

		/**
		 * Print editor (TinyMCE) init scripts
		 *
		 * @param string $id - textarea id.
		 * @param string $body_id - body id.
		 * @return void
		 */
		public static function print_editor_init_scripts( $id, $body_id ) {

			$toolbox      = array();
			$is_editor    = false;
			$current_user = WPSC_Current_User::$current_user;
			$advanced     = get_option( 'wpsc-te-advanced' );
			$rich_editing = get_user_meta( $current_user->user->ID, 'rich_editing', true );
			$rich_editing = filter_var( $rich_editing, FILTER_VALIDATE_BOOLEAN );

			if ( $current_user->is_agent ) {

				$agent = get_option( 'wpsc-te-agent' );
				if ( $agent['enable'] ) {
					$is_editor = true;
					foreach ( $agent['toolbar'] as $key => $value ) {
						$toolbox[] = $value;
						if ( in_array( $value, array( 'blockquote', 'alignright', 'numlist', 'rtl', 'wpsc_insert_editor_img' ) ) ) {
							$toolbox[] = '|';
						}
					}
				}
			} elseif ( $current_user->is_customer && $current_user->user->ID ) {

				$reg_user = get_option( 'wpsc-te-registered-user' );
				if ( $reg_user['enable'] ) {
					$is_editor = true;
					foreach ( $reg_user['toolbar'] as $key => $value ) {
						$toolbox[] = $value;
						if ( in_array( $value, array( 'blockquote', 'alignright', 'numlist', 'rtl', 'wpsc_insert_editor_img' ) ) ) {
							$toolbox[] = '|';
						}
					}
				}
			} else {

				$guest = get_option( 'wpsc-te-guest-user' );
				if ( $guest['enable'] ) {
					$is_editor = true;
					foreach ( $guest['toolbar'] as $key => $value ) {
						$toolbox[] = $value;
						if ( in_array( $value, array( 'blockquote', 'alignright', 'numlist', 'rtl', 'wpsc_insert_editor_img' ) ) ) {
							$toolbox[] = '|';
						}
					}
				}
				$rich_editing = true;
			}
			$toolbox = implode( ' ', $toolbox );
			?>

			var isWPSCEditor = <?php echo esc_attr( $is_editor ) && esc_attr( $rich_editing ) ? '1' : '0'; ?>;
			<?php
			if ( $is_editor && $rich_editing ) :
				?>
				tinymce.remove('#<?php echo esc_attr( $id ); ?>');
				tinymce.init({ 
					selector:'#<?php echo esc_attr( $id ); ?>',
					body_id: '<?php echo esc_attr( $body_id ); ?>',
					menubar: false,
					statusbar: false,
					autoresize_min_height: 150,
					wp_autoresize_on: true,
					plugins: [
					'lists link directionality wpautoresize textcolor paste'
					],
					<?php echo $advanced['html-pasting'] == 0 ? 'paste_as_text: true,' : ''; ?>
					image_advtab: true,
					toolbar: '<?php echo esc_attr( $toolbox ); ?>',
					directionality: '<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>',
					branding: false,
					autoresize_bottom_margin: 20,
					browser_spellcheck : true,
					relative_urls : false,
					remove_script_host : false,
					convert_urls : true,
					paste_data_images: true,
					images_upload_handler: function (blobInfo, success, failure) {

						form_data = new FormData();
						form_data.append('file', blobInfo.blob(), blobInfo.filename());
						form_data.append('action','wpsc_tinymce_upload_file');
						form_data.append('_ajax_nonce', '<?php echo esc_attr( wp_create_nonce( 'wpsc_tinymce_upload_file' ) ); ?>');

						jQuery.ajax({
							type : 'post',
							url : supportcandy.ajax_url,
							cache: false,
							contentType: false,
							processData: false,
							data: form_data,
							success: function(res){
								success(res.imgURL);
							}
						});
					},
					setup: function (editor) {
						editor.on('blur', function(e) {
							jQuery( '#<?php echo esc_attr( $id ); ?>' ).text( editor.getContent() );
							wpsc_check_tff_visibility();
						});
						// Add a custom button
						editor.addButton('wpsc_insert_editor_img', {
							title : 'Insert/edit image',
							onclick : function() {
								// Add you own code to execute something on click
								wpsc_add_custom_image_tinymce(editor, '<?php echo esc_attr( wp_create_nonce( 'wpsc_add_custom_image_tinymce' ) ); ?>');
							}
						});
						editor.on('click', function (e) {
							if(e.target.nodeName === "IMG"){
								wpsc_edit_custom_image_tinymce(editor, e.target, '<?php echo esc_attr( wp_create_nonce( 'wpsc_edit_custom_image_tinymce' ) ); ?>');
							}
						});
						editor.on('KeyUp', function (e) {
							if( tinyMCE.activeEditor.getContent() == '' ) {
								ticket_id = jQuery('#wpsc-current-ticket').val();
								wpsc_clear_saved_draft_reply( ticket_id );
							}
						});
					}
				});
				<?php
			endif;
		}

		/**
		 * Check attachment are allowed to user
		 *
		 * @return boolean
		 */
		public static function is_allow_attachments() {

			$current_user = WPSC_Current_User::$current_user;
			$flag         = false;

			if ( $current_user->is_agent ) {

				$agent = get_option( 'wpsc-te-agent' );
				if ( $agent['allow-attachments'] ) {
					$flag = true;
				}
			} elseif ( $current_user->is_customer && $current_user->user->ID ) {

				$reg_user = get_option( 'wpsc-te-registered-user' );
				if ( $reg_user['allow-attachments'] ) {
					$flag = true;
				}
			} else {

				$guest = get_option( 'wpsc-te-guest-user' );
				if ( $guest['allow-attachments'] ) {
					$flag = true;
				}
			}

			return $flag;
		}

		/**
		 * Check attachment notice are allowed to user
		 *
		 * @return boolean
		 */
		public static function is_attachment_notice() {

			$current_user = WPSC_Current_User::$current_user;
			$flag         = false;

			if ( $current_user->is_agent ) {

				$agent = get_option( 'wpsc-te-agent' );
				if ( $agent['file-attachment-notice'] ) {
					$flag = true;
				}
			} elseif ( $current_user->is_customer && $current_user->user->ID ) {

				$reg_user = get_option( 'wpsc-te-registered-user' );
				if ( $reg_user['file-attachment-notice'] ) {
					$flag = true;
				}
			} else {

				$guest = get_option( 'wpsc-te-guest-user' );
				if ( $guest['file-attachment-notice'] ) {
					$flag = true;
				}
			}
			return $flag;
		}

		/**
		 * File attachment notice text
		 *
		 * @return string
		 */
		public static function file_attachment_notice_text() {

			$current_user = WPSC_Current_User::$current_user;
			$notice_text  = '';

			if ( $current_user->is_agent ) {

				$agent       = get_option( 'wpsc-te-agent' );
				$notice_text = $agent['file-attachment-notice-text'];

			} elseif ( $current_user->is_customer && $current_user->user->ID ) {

				$reg_user    = get_option( 'wpsc-te-registered-user' );
				$notice_text = $reg_user['file-attachment-notice-text'];

			} else {

				$guest       = get_option( 'wpsc-te-guest-user' );
				$notice_text = $guest['file-attachment-notice-text'];

			}
			return $notice_text;
		}
	}
endif;

WPSC_Text_Editor::init();
