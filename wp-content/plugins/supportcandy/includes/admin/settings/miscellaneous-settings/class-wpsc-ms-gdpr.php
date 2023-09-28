<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_MS_GDPR' ) ) :

	final class WPSC_MS_GDPR {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// User interface.
			add_action( 'wp_ajax_wpsc_get_ms_gdpr', array( __CLASS__, 'load_settings_ui' ) );
			add_action( 'wp_ajax_wpsc_set_ms_gdpr', array( __CLASS__, 'save_settings' ) );
			add_action( 'wp_ajax_wpsc_reset_ms_gdpr', array( __CLASS__, 'reset_settings' ) );

			// Print in create ticket form.
			add_action( 'wpsc_print_tff', array( __CLASS__, 'print_tff' ) );

			// TFF! validation.
			add_action( 'wpsc_js_validate_ticket_form', array( __CLASS__, 'js_validate_ticket_form' ) );

			// make tickets anonymous - gdpr setting.
			add_action( 'wpsc_cron_daily', array( __CLASS__, 'personal_data_eraser' ) );

			// GDPR.
			add_filter( 'wp_privacy_personal_data_exporters', array( __CLASS__, 'wpsc_register_privacy_exporters' ) );
			add_filter( 'wp_privacy_personal_data_erasers', array( __CLASS__, 'wpsc_register_privacy_erasers' ) );
		}

		/**
		 * Reset settings
		 *
		 * @return void
		 */
		public static function reset() {

			$gdpr_text = '<p>I understand my personal information like Name, Email address, IP address, etc. will be stored in database.</p>';
			$gdpr      = apply_filters(
				'wpsc_gdpr_settings',
				array(
					'allow-gdpr'                   => 1,
					'gdpr-text'                    => $gdpr_text,
					'personal-data-retention-time' => 0,
					'personal-data-retention-unit' => 'days',
					'editor'                       => 'html',
				)
			);
			update_option( 'wpsc-gdpr-settings', $gdpr );
			WPSC_Translations::remove( 'wpsc-gdpr', $gdpr_text );
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
			$settings = get_option( 'wpsc-gdpr-settings', array() );?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-ms-gdpr">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/gdpr/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-allow-gdpr" name="allow-gdpr">
						<option <?php selected( $settings['allow-gdpr'], 1 ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $settings['allow-gdpr'], 0 ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Checkbox text', 'supportcandy' ); ?></label>
					</div>
					<div class="textarea-container ">
						<div class="wpsc_tinymce_editor_btns">
							<div class="inner-container">
								<button class="visual wpsc-switch-editor <?php echo esc_attr( $settings['editor'] ) == 'html' ? 'active' : ''; ?>" type="button" onclick="wpsc_get_tinymce(this, 'gdpr-text','gdpr_body');"><?php esc_attr_e( 'Visual', 'supportcandy' ); ?></button>
								<button class="text wpsc-switch-editor <?php echo esc_attr( $settings['editor'] ) == 'text' ? 'active' : ''; ?>" type="button" onclick="wpsc_get_textarea(this, 'gdpr-text')"><?php esc_attr_e( 'Text', 'supportcandy' ); ?></button>
							</div>
						</div>
						<?php
						$gdpr_text = $settings['gdpr-text'] ? WPSC_Translations::get( 'wpsc-gdpr', stripslashes( $settings['gdpr-text'] ) ) : stripslashes( $settings['gdpr-text'] );
						?>
						<textarea name="gdpr-text" id="gdpr-text" class="wpsc_textarea"><?php echo wp_kses_post( $gdpr_text ); ?></textarea>
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
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Personal data retention', 'supportcandy' ); ?></label>
					</div>
					<div class="divide-bar">
						<input type="number" id="wpsc-personal-data-retention-time" name="personal-data-retention-time" value="<?php echo esc_attr( $settings['personal-data-retention-time'] ); ?>">
						<select id="wpsc-personal-data-retention-unit" name="personal-data-retention-unit">
							<option <?php selected( $settings['personal-data-retention-unit'], 'days' ); ?> value="days"><?php esc_attr_e( 'Day(s)', 'supportcandy' ); ?></option>
							<option <?php selected( $settings['personal-data-retention-unit'], 'month' ); ?> value="month"><?php esc_attr_e( 'Month(s)', 'supportcandy' ); ?></option>
							<option <?php selected( $settings['personal-data-retention-unit'], 'year' ); ?> value="year"><?php esc_attr_e( 'Year(s)', 'supportcandy' ); ?></option>
						</select>  
					</div>
				</div>    
				<?php do_action( 'wpsc_ms_gdpr' ); ?>
				<input type="hidden" name="action" value="wpsc_set_ms_gdpr">
				<input id="editor" type="hidden" name="editor" value="<?php echo esc_attr( $settings['editor'] ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_ms_gdpr' ) ); ?>">
			</form>
			<div class="setting-footer-actions">
				<button 
					class="wpsc-button normal primary margin-right"
					onclick="wpsc_set_ms_gdpr(this);">
					<?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
				<button 
					class="wpsc-button normal secondary"
					onclick="wpsc_reset_ms_gdpr(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_reset_ms_gdpr' ) ); ?>');">
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

			if ( check_ajax_referer( 'wpsc_set_ms_gdpr', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$settings = apply_filters(
				'wpsc_set_gdpr',
				array(
					'allow-gdpr'                   => isset( $_POST['allow-gdpr'] ) ? intval( $_POST['allow-gdpr'] ) : 1,
					'gdpr-text'                    => isset( $_POST ) && isset( $_POST['gdpr-text'] ) ? wp_kses_post( wp_unslash( $_POST['gdpr-text'] ) ) : '',
					'personal-data-retention-time' => isset( $_POST['personal-data-retention-time'] ) ? intval( $_POST['personal-data-retention-time'] ) : 0,
					'personal-data-retention-unit' => isset( $_POST['personal-data-retention-unit'] ) ? sanitize_text_field( wp_unslash( $_POST['personal-data-retention-unit'] ) ) : 'days',
					'editor'                       => isset( $_POST['editor'] ) ? sanitize_text_field( wp_unslash( $_POST['editor'] ) ) : 'html',
				)
			);
			update_option( 'wpsc-gdpr-settings', $settings );

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-gdpr' );

			// add string translations.
			WPSC_Translations::add( 'wpsc-gdpr', $settings['gdpr-text'] );
			wp_die();
		}

		/**
		 * Reset settings to default
		 *
		 * @return void
		 */
		public static function reset_settings() {

			if ( check_ajax_referer( 'wpsc_reset_ms_gdpr', '_ajax_nonce', false ) != 1 ) {
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

			$gdpr = get_option( 'wpsc-gdpr-settings' );
			if ( intval( $gdpr['allow-gdpr'] ) === 1 ) {
				?>
				<div class="wpsc-tff wpsc-gdpr wpsc-xs-12 wpsc-sm-12 wpsc-md-12 wpsc-lg-12 required wpsc-visible" data-cft="gdpr">
					<div class="checkbox-container">
						<?php $unique_id = uniqid( 'wpsc_' ); ?>
						<input id="<?php echo esc_attr( $unique_id ); ?>" type="checkbox" value="1"/>
						<?php
						$name = WPSC_Translations::get( 'wpsc-gdpr', stripslashes( $gdpr['gdpr-text'] ) );
						?>
						<label for="<?php echo esc_attr( $unique_id ); ?>"><?php echo wp_kses_post( $name ); ?></label>
					</div>
				</div>
				<?php
			}
		}

		/**
		 * Validate this type field in create ticket
		 *
		 * @return void
		 */
		public static function js_validate_ticket_form() {
			?>

			case 'gdpr':
				var checkbox = customField.find('input:checked');
				if (checkbox.length === 0) {
					isValid = false;
					alert(supportcandy.translations.req_gdpr);
				}
				break;
			<?php
			echo PHP_EOL;
		}

		/**
		 * Make tickets anonymous - gdpr setting - Personal data retention
		 *
		 * @return void
		 */
		public static function personal_data_eraser() {

			$settings = get_option( 'wpsc-gdpr-settings', array() );

			// return if age.
			if ( $settings['personal-data-retention-time'] === 0 ) {
				return;
			}

			$tz    = wp_timezone();
			$today = new DateTime( 'now', $tz );

			if ( $settings['personal-data-retention-unit'] == 'days' ) {
				$today->sub( new DateInterval( 'P' . $settings['personal-data-retention-time'] . 'D' ) );
			} elseif ( $settings['personal-data-retention-unit'] == 'month' ) {
				$today->sub( new DateInterval( 'P' . $settings['personal-data-retention-time'] . 'M' ) );
			} else {
				$today->sub( new DateInterval( 'P' . $settings['personal-data-retention-time'] . 'Y' ) );
			}

			$filters = array(
				'items_per_page' => 0,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'slug'    => 'date_updated',
						'compare' => '<',
						'val'     => $today->format( 'Y-m-d' ),
					),
					array(
						'slug'    => 'customer',
						'compare' => 'NOT IN',
						'val'     => array( 0 ),
					),
				),
			);

			$response = WPSC_Ticket::find( $filters );
			$tickets  = $response['results'];

			foreach ( $tickets as $ticket ) {

				self::anonymize_ticket( $ticket );
			}
		}

		/**
		 * Anonymize ticket & customer data
		 *
		 * @param WPSC_Ticket $ticket -  ticket object.
		 * @return void
		 */
		public static function anonymize_ticket( $ticket ) {

			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

				if ( in_array( $cf->field, array( 'ticket', 'agentonly' ) ) && $cf->is_personal_info ) {
					$val                 = $cf->type::$has_multiple_val ? array() : '';
					$ticket->{$cf->slug} = $val;
				}
			}

			$ticket->customer = 0;
			$ticket->save();

			do_action( 'wpsc_after_anonymizing_ticket', $ticket );
		}

		/**
		 * Register privacy exporters
		 *
		 * @param array $exporters - privacy expoters.
		 * @return array
		 */
		public static function wpsc_register_privacy_exporters( $exporters ) {

			$exporters['wpsc_ticket_cust'] = array(
				'exporter_friendly_name' => __( 'Ticket Customer', 'supportcandy' ),
				'callback'               => array( __CLASS__, 'wpsc_privacy_ticket_customer_exporter' ),
			);

			$exporters['wpsc_tickets'] = array(
				'exporter_friendly_name' => __( 'Tickets', 'supportcandy' ),
				'callback'               => array( __CLASS__, 'wpsc_privacy_ticket_exporter' ),
			);

			return $exporters;
		}

		/**
		 * Add customer data to privacy exporter
		 *
		 * @param string  $email_address - email address.
		 * @param integer $page - page numbers.
		 * @return array
		 */
		public static function wpsc_privacy_ticket_customer_exporter( $email_address = '', $page = 1 ) {

			$customer = WPSC_Customer::get_by_email( $email_address );
			if ( ! $customer->id ) {
				return;
			}

			$data = array(
				array(
					'name'  => __( 'Name', 'supportcandy' ),
					'value' => $customer->name,
				),
				array(
					'name'  => __( 'Email', 'supportcandy' ),
					'value' => $customer->email,
				),
			);

			$export_customer = array();
			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

				if ( $cf->field == 'customer' && $cf->is_personal_info && ! in_array( $cf->slug, WPSC_DF_Customer::$ignore_customer_info_cft ) && $customer->{$cf->slug} ) {

					$data[] = array(
						'name'  => stripslashes( $cf->name ),
						'value' => $cf->type::get_customer_field_val( $cf, $customer ),
					);
				}
			}

			$export_customer[] = array(
				'group_id'    => 'wpsc_ticket_cust',
				'group_label' => __( 'Ticket Customer', 'supportcandy' ),
				'item_id'     => "wpsc_cust_id-{$customer->id}",
				'data'        => $data,
			);

			return array(
				'data' => $export_customer,
				'done' => true,
			);
		}

		/**
		 * Add ticket data to privacy exporter
		 *
		 * @param string  $email_address - email address.
		 * @param integer $page - page numbers.
		 * @return array
		 */
		public static function wpsc_privacy_ticket_exporter( $email_address = '', $page = 1 ) {

			$customer = WPSC_Customer::get_by_email( $email_address );
			if ( ! $customer->id ) {
				return;
			}

			$filters = array(
				'items_per_page' => 0,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'slug'    => 'customer',
						'compare' => '=',
						'val'     => $customer->id,
					),
				),
			);
			$tickets = WPSC_Ticket::find( $filters )['results'];

			$export_tickets = array();
			foreach ( $tickets as $key => $ticket ) {

				$data = array(
					array(
						'name'  => __( 'Ticket ID', 'supportcandy' ),
						'value' => $ticket->id,
					),
					array(
						'name'  => __( 'Subject', 'supportcandy' ),
						'value' => $ticket->subject,
					),
					array(
						'name'  => __( 'IP Address', 'supportcandy' ),
						'value' => $ticket->ip_address,
					),
					array(
						'name'  => __( 'Browser', 'supportcandy' ),
						'value' => $ticket->browser,
					),
					array(
						'name'  => __( 'Operating System', 'supportcandy' ),
						'value' => $ticket->os,
					),
				);
				foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

					if ( $cf->is_personal_info && in_array( $cf->field, array( 'ticket', 'agentonly' ) ) && ! $cf->type::$is_default && $ticket->{$cf->slug} ) {
						$val    = in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ? $cf->type::get_ticket_field_val( $cf, $ticket ) : $cf->type::get_customer_field_val( $cf, $ticket->customer );
						$data[] = array(
							'name'  => stripslashes( $cf->name ),
							'value' => $val,
						);
					}
				}

				$export_tickets[] = array(
					'group_id'    => 'wpsc_tickets',
					'group_label' => __( 'User Tickets', 'supportcandy' ),
					'item_id'     => "wpsc_ticket_id-{$ticket->id}",
					'data'        => $data,
				);
			}

			return array(
				'data' => $export_tickets,
				'done' => true,
			);
		}

		/**
		 * Register privacy erasers
		 *
		 * @param array $erasers - rpivacy erasers.
		 * @return array
		 */
		public static function wpsc_register_privacy_erasers( $erasers = array() ) {

			$erasers[] = array(
				'eraser_friendly_name' => __( 'Ticket Records', 'supportcandy' ),
				'callback'             => array( __CLASS__, 'wpsc_privacy_customer_erasers' ),
			);

			return $erasers;
		}

		/**
		 * Erase customer ticket data
		 *
		 * @param string  $email_address - email address.
		 * @param integer $page - page numbers.
		 * @return array
		 */
		public static function wpsc_privacy_customer_erasers( $email_address, $page = 1 ) {

			$customer = WPSC_Customer::get_by_email( $email_address );
			if ( ! $customer->id ) {
				return;
			}

			$filters = array(
				'items_per_page' => 0,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'slug'    => 'customer',
						'compare' => '=',
						'val'     => $customer->id,
					),
				),
			);
			$tickets = WPSC_Ticket::find( $filters )['results'];

			foreach ( $tickets as $ticket ) {

				self::anonymize_ticket( $ticket );
			}

			WPSC_Customer::destroy( $customer );

			/* translators: %s: email address */
			$message = sprintf( __( 'Tickets of customer having email %s has been anonymized.', 'supportcandy' ), $email_address );
			return array(
				'items_removed'  => true,
				'items_retained' => false,
				'messages'       => array( $message ),
				'done'           => true,
			);
		}
	}
endif;

WPSC_MS_GDPR::init();
