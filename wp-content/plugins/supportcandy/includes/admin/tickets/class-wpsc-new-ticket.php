<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_New_Ticket' ) ) :

	final class WPSC_New_Ticket {

		/**
		 * Set whether or not ticket form has customer fields
		 *
		 * @var boolean
		 */
		public static $has_customer_fields = false;

		/**
		 * Initialize this class
		 */
		public static function init() {

			// ticket form.
			add_action( 'wp_ajax_wpsc_get_ticket_form', array( __CLASS__, 'get_ticket_form' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_ticket_form', array( __CLASS__, 'get_ticket_form' ) );
			add_action( 'wp_ajax_wpsc_set_ticket_form', array( __CLASS__, 'set_ticket_form' ) );
			add_action( 'wp_ajax_nopriv_wpsc_set_ticket_form', array( __CLASS__, 'set_ticket_form' ) );

			// Create as.
			add_action( 'wp_ajax_wpsc_get_change_create_as', array( __CLASS__, 'get_change_create_as' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_change_create_as', array( __CLASS__, 'get_change_create_as' ) );
			add_action( 'wp_ajax_wpsc_create_as_autocomplete', array( __CLASS__, 'create_as_autocomplete' ) );
			add_action( 'wp_ajax_nopriv_wpsc_create_as_autocomplete', array( __CLASS__, 'create_as_autocomplete' ) );
			add_action( 'wp_ajax_wpsc_add_new_create_as', array( __CLASS__, 'add_new_create_as' ) );
			add_action( 'wp_ajax_nopriv_wpsc_add_new_create_as', array( __CLASS__, 'add_new_create_as' ) );
			add_action( 'wp_ajax_wpsc_get_create_as_customer_fields', array( __CLASS__, 'get_create_as_customer_fields' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_create_as_customer_fields', array( __CLASS__, 'get_create_as_customer_fields' ) );

			// Visibility.
			add_action( 'wp_ajax_wpsc_check_tff_visibility', array( __CLASS__, 'check_tff_visibility' ) );
			add_action( 'wp_ajax_nopriv_wpsc_check_tff_visibility', array( __CLASS__, 'check_tff_visibility' ) );
		}

		/**
		 * Ticket form ajax callback
		 *
		 * @return void
		 */
		public static function get_ticket_form() {

			if ( check_ajax_referer( 'general', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user  = WPSC_Current_User::$current_user;
			$gs            = get_option( 'wpsc-gs-general' );
			$is_created_as = false;

			if ( isset( $_POST['create_as_email'] ) && isset( $_POST['create_as_name'] ) ) :

				$email = isset( $_POST['create_as_email'] ) ? sanitize_text_field( wp_unslash( $_POST['create_as_email'] ) ) : '';
				$name  = isset( $_POST['create_as_name'] ) ? sanitize_text_field( wp_unslash( $_POST['create_as_name'] ) ) : '';
				if ( ! $email || ! $name ) {
					return;
				}

				$customer = WPSC_Customer::get_by_email( $email );
				if ( ! $customer->id ) {
					$customer = WPSC_Customer::insert(
						array(
							'user'  => 0,
							'name'  => $name,
							'email' => $email,
						)
					);
				}

				$is_created_as = true;

			endif;

			if ( ! (
				( ! $current_user->user->ID && in_array( 'guest', $gs['allow-create-ticket'] ) ) ||
				( $current_user->is_agent && in_array( $current_user->agent->role, $gs['allow-create-ticket'] ) ) ||
				( ! $current_user->is_agent && $current_user->user->ID && in_array( 'registered-user', $gs['allow-create-ticket'] ) )
			) ) {
				?>
				<div style="align-item:center;" ><h6><?php esc_attr_e( 'Unathorized access!' ); ?></h6></div>
				<?php
				wp_die();
			}

			// Create ticket on behalf.
			if ( $current_user->is_agent && in_array( $current_user->agent->role, $gs['allow-create-ticket'] ) && $current_user->agent->has_cap( 'create-as' ) ) :
				$cf = WPSC_Custom_Field::get_cf_by_slug( 'customer' );
				?>
				<div style="width: 100%;">
					<div class="wpsc-tff customer wpsc-xs-12 wpsc-sm-12 wpsc-md-6 wpsc-lg-4">
						<div class="wpsc-tff-label">
							<div class="wpsc-tff-label">
								<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
								<span class="required-indicator">*</span>
							</div>
						</div>
						<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>
						<div style="display:flex; flex-direction:column; width:100%;">
							<select class="create-as">
								<option value="<?php $current_user->customer->id; ?>">
									<?php
									/* translators: %1$s: Name, %2$s: Email Address */
									printf( esc_attr__( '%1$s (%2$s)', 'supportcandy' ), esc_attr( $current_user->customer->name ), esc_attr( $current_user->customer->email ) );
									?>
								</option>
							</select>
							<a class="wpsc-link" href="javascript:wpsc_get_change_create_as('<?php echo esc_attr( wp_create_nonce( 'wpsc_get_change_create_as' ) ); ?>');" style="margin-top:3px; width:fit-content;"><?php esc_attr_e( 'Add new', 'supportcandy' ); ?></a>
						</div>
						<script>
							jQuery('select.create-as').selectWoo({
								ajax: {
									url: supportcandy.ajax_url,
									dataType: 'json',
									delay: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											action: 'wpsc_create_as_autocomplete',
											_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_create_as_autocomplete' ) ); ?>'
										};
									},
									processResults: function (data, params) {
										var terms = [];
										if ( data ) {
											jQuery.each( data, function( id, customer ) {
												terms.push({ 
													id: customer.id, 
													text: customer.text,
													email: customer.email,
													name: customer.name
												});
											});
										}
										return {
											results: terms
										};
									},
									cache: true
								},
								escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
								minimumInputLength: 1,
							});
							jQuery('select.create-as').on('select2:select', function (e) {
								var data = e.params.data;
								jQuery('input.name').val(data.name);
								jQuery('input.email').val(data.email);
								wpsc_after_change_create_as();
							});
						</script>
					</div>
				</div>
				<?php
			endif;
			?>

			<form class="wpsc-create-ticket" onsubmit="return false;" action="#">
				<?php
				// Load form fields.
				$tff = get_option( 'wpsc-tff', array() );
				foreach ( $tff as $slug => $field ) :
					$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
					if ( ! $cf ) {
						continue;
					}
					if ( $cf->field === 'customer' && ! self::$has_customer_fields ) {
						self::$has_customer_fields = true;
					}
					echo $cf->type::print_tff( $cf, $field ); // phpcs:ignore
				endforeach;
				do_action( 'wpsc_print_tff' );
				?>
				<input type="hidden" name="action" value="wpsc_set_ticket_form"/>
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_ticket_form' ) ); ?>">
			</form>

			<div class="wpsc-tff wpsc-xs-12 wpsc-sm-12 wpsc-md-12 wpsc-lg-12">
				<div class="submit-container">
					<button class="wpsc-button normal primary margin-right" onclick="wpsc_submit_ticket_form(this);"><?php esc_attr_e( 'Submit', 'supportcandy' ); ?></button>
					<button class="wpsc-button normal secondary" onclick="wpsc_get_ticket_form();"><?php esc_attr_e( 'Reset Form', 'supportcandy' ); ?></button>
				</div>
			</div>

			<script>

				jQuery(document).ready(function() {
					wpsc_check_tff_visibility();
				});

				var hasCustomerFields = <?php echo self::$has_customer_fields ? 'true' : 'false'; ?>;
				var prevCustomer = '<?php echo $current_user->is_customer ? esc_attr( $current_user->customer->email ) : ''; ?>';

				/**
				 * Submit create ticket form
				 */
				function wpsc_submit_ticket_form(el) {

					if (!wpsc_validate_ticket_form()) return;

					var is_editor = (typeof isWPSCEditor !== 'undefined')  ? isWPSCEditor : 0;

					var dataform = new FormData(jQuery('form.wpsc-create-ticket')[0]);
					dataform.append('is_frontend', supportcandy.is_frontend);
					dataform.append('is_editor', is_editor);
					<?php do_action( 'wpsc_js_create_ticket_formdata' ); ?>
					jQuery('.wpsc-body').html(supportcandy.loader_html);
					if (supportcandy.is_frontend === '0') {
						wpsc_scroll_top();
					}

					<?php
					$recaptcha = get_option( 'wpsc-recaptcha-settings' );
					if ( $recaptcha['allow-recaptcha'] === 1 && $recaptcha['recaptcha-version'] == 3 && $recaptcha['recaptcha-site-key'] && $recaptcha['recaptcha-secret-key'] ) {
						?>
						grecaptcha.ready(function() {
							grecaptcha.execute('<?php echo esc_attr( $recaptcha['recaptcha-site-key'] ); ?>', {action: 'submit_ticket'}).then(function(token) {
								dataform.append('g-recaptcha-response', token);
								wpsc_post_ticket_form(dataform);
							});
						});
						<?php
					} else {
						?>
						wpsc_post_ticket_form(dataform);
						<?php
					}
					?>
				}

				/**
				 * Post ticket for to server
				 *
				 * @return void
				 */
				function wpsc_post_ticket_form(dataform) {

					var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
					jQuery.ajax({
						url: supportcandy.ajax_url,
						type: 'POST',
						data: dataform,
						processData: false,
						contentType: false,
						error: function (response) {
							alert(response.responseJSON.data);
							if (is_tinymce) {
								tinyMCE.get('description').setContent('');
							}
							wpsc_get_ticket_form();
						},
						success: function (response, textStatus, xhr) {
							if( response.action == 'ticket' ) {
								wpsc_get_individual_ticket( response.id );
							}else if( response.action == 'url' ) {
								window.location.href = response.redirectURL;
							}else if( response.action == 'text' ) {
								var classes = 'wpsc-xs-12 wpsc-sm-12 wpsc-md-12 wpsc-lg-12';
								jQuery('.wpsc-body').html('<div class="' + classes + '">' + response.thankyouText + '</div>');
								if (is_tinymce) {
									tinyMCE.get('description').setContent('');
								}
							}
							wpsc_run_ajax_background_process();
						}
					});
				}

				/**
				 * Check validations for create ticket form
				 *
				 * @return Boolean
				 */
				function wpsc_validate_ticket_form() {

					var customFields = jQuery('.wpsc-tff.wpsc-visible');
					var flag = true;
					jQuery.each(customFields, function(index, customField){

						customField = jQuery(customField);
						var customFieldType = customField.data('cft');
						var isValid = true;
						switch (customFieldType) {
							<?php do_action( 'wpsc_js_validate_ticket_form' ); ?>
						}
						if (!isValid) {
							flag = false;
							return false;
						}
					});
					return flag;
				}

				function wpsc_get_create_as_customer_fields(nonce) {

					if (!hasCustomerFields) return;

					var curCustomer = jQuery('.wpsc-create-ticket input.email').val().trim();
					if (curCustomer == prevCustomer) return;
					prevCustomer = curCustomer;

					var data = {
						action: 'wpsc_get_create_as_customer_fields',
						email: curCustomer,
						_ajax_nonce: nonce
					};
					jQuery.post(supportcandy.ajax_url, data, function (response) {

						jQuery.each( response, function( key, field ) {
							var currentEl = jQuery('.wpsc-tff.' + field.slug);
							var nextEl = currentEl.next();
							currentEl.remove();
							nextEl.before(field.html);
						});
					});
				}
				<?php

				if ( $is_created_as ) {
					?>
					jQuery(document).ready(function() {
						<?php /* translators: %1$s: Name, %2$s: Email Address */ ?>
						var label = '<?php printf( esc_attr__( '%1$s (%2$s)', 'supportcandy' ), esc_attr( $customer->name ), esc_attr( $customer->email ) ); ?>';
						var newOption = new Option(label, <?php echo esc_attr( $customer->id ); ?>, false, false);
						jQuery('select.create-as').append(newOption);
						jQuery('select.create-as').val(<?php echo esc_attr( $customer->id ); ?>).trigger('change');
						jQuery('input.name').val('<?php echo esc_attr( $customer->name ); ?>');
						jQuery('input.email').val('<?php echo esc_attr( $customer->email ); ?>');
						wpsc_after_change_create_as();
					});
					<?php
				}
				?>

				<?php do_action( 'wpsc_js_ticket_form_functions' ); ?>
			</script>
			<?php

			wp_die();
		}

		/**
		 * Submit ticket
		 *
		 * @return void
		 */
		public static function set_ticket_form() {

			if ( check_ajax_referer( 'wpsc_set_ticket_form', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_MS_Recaptcha::validate( 'submit_ticket' );

			$current_user = WPSC_Current_User::$current_user;
			$gs           = get_option( 'wpsc-gs-general' );
			$advanced     = get_option( 'wpsc-ms-advanced-settings' );

			if ( ! (
				( ! $current_user->user->ID && in_array( 'guest', $gs['allow-create-ticket'] ) ) ||
				( $current_user->is_agent && in_array( $current_user->agent->role, $gs['allow-create-ticket'] ) ) ||
				( ! $current_user->is_agent && $current_user->user->ID && in_array( 'registered-user', $gs['allow-create-ticket'] ) )
			) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			// group by custom field type.
			$cfs = array();
			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {
				$cfs[ $cf->type::$slug ][] = $cf;
			}

			$data = apply_filters( 'wpsc_create_ticket_data', array(), $cfs, false );

			// Seperate description from $data.
			$description = $data['description'];
			unset( $data['description'] );

			// Seperate description attachments from $data.
			$description_attachments = $data['description_attachments'];
			unset( $data['description_attachments'] );

			$data['last_reply_on'] = ( new DateTime() )->format( 'Y-m-d H:i:s' );

			// insert ticket data.
			$ticket = WPSC_Ticket::insert( $data );

			if ( ! $ticket ) {
				wp_send_json_error( new WP_Error( '001', 'Something went wrong!' ), 500 );
			}

			$thread_customer = $current_user->is_agent && $current_user->customer->id != $ticket->customer->id && $advanced['raised-by-user'] == 'agent' ? $current_user->customer : $ticket->customer;
			$ticket->last_reply_by = $thread_customer->id;
			$ticket->save();

			// replace macros only when current user is an agent.
			$description = $current_user->is_agent ? WPSC_Macros::replace( $description, $ticket ) : $description;

			// set signature if agent.
			$signature = $current_user->is_agent && $current_user->customer->email == $ticket->customer->email ? $current_user->agent->get_signature() : '';
			if ( $signature ) {
				$description .= '<br>' . $signature;
			}

			// Create report thread.
			$thread = WPSC_Thread::insert(
				array(
					'ticket'      => $ticket->id,
					'customer'    => $thread_customer->id,
					'type'        => 'report',
					'body'        => $description,
					'attachments' => $description_attachments,
					'ip_address'  => $ticket->ip_address,
					'source'      => $ticket->source,
					'os'          => $ticket->os,
					'browser'     => $ticket->browser,
				)
			);

			do_action( 'wpsc_create_new_ticket', $ticket );

			// tinymce img attachments.
			if ( preg_match_all( '/' . preg_quote( home_url( '/' ), '/' ) . '\?wpsc_attachment=(\d*)/', $description, $matches ) ) {
				foreach ( $matches[1] as $id ) {
					$attachment            = new WPSC_Attachment( $id );
					$attachment->is_active = 1;
					$attachment->source_id = $thread->id;
					$attachment->ticket_id = $ticket->id;
					$attachment->save();
				}
			}

			$response = array( 'id' => $ticket->id );

			$is_frontend = isset( $_POST['is_frontend'] ) ? intval( $_POST['is_frontend'] ) : 0;

			$thankyou = get_option( 'wpsc-gs-thankyou-page-settings' );

			if ( $current_user->is_agent ) {

				$response['action'] = $thankyou['action-agent'];
				if ( $thankyou['action-agent'] == 'url' && $thankyou['page-url-agent'] && filter_var( $thankyou['page-url-agent'], FILTER_VALIDATE_URL ) ) {
					$response['isRedirect']  = 1;
					$response['redirectURL'] = $thankyou['page-url-agent'];
				} elseif ( $thankyou['action-agent'] == 'text' ) {
					$thank_you                = $thankyou['html-agent'] ? WPSC_Translations::get( 'wpsc-thankyou-html-agent', $thankyou['html-agent'] ) : $thankyou['html-agent'];
					$thankyou_text            = WPSC_Macros::replace( $thank_you, $ticket );
					$thankyou_text            = apply_filters( 'wpsc_after_thankyou_text', $thankyou_text, $ticket );
					$response['isRedirect']   = 0;
					$response['thankyouText'] = $thankyou_text;
				}
			} else {

				$response['action'] = $thankyou['action-customer'];
				if ( $thankyou['action-customer'] == 'url' && $thankyou['page-url-customer'] && filter_var( $thankyou['page-url-customer'], FILTER_VALIDATE_URL ) ) {
					$response['isRedirect']  = 1;
					$response['redirectURL'] = $thankyou['page-url-customer'];
				} elseif ( $thankyou['action-customer'] == 'text' ) {
					$thank_you                = $thankyou['html-customer'] ? WPSC_Translations::get( 'wpsc-thankyou-html', $thankyou['html-customer'] ) : $thankyou['html-customer'];
					$thankyou_text            = WPSC_Macros::replace( $thank_you, $ticket );
					$thankyou_text            = apply_filters( 'wpsc_after_thankyou_text', $thankyou_text, $ticket );
					$response['isRedirect']   = 0;
					$response['thankyouText'] = $thankyou_text;
				}
			}

			wp_send_json( $response );
		}

		/**
		 * Change create as or raised by modal
		 *
		 * @return void
		 */
		public static function get_change_create_as() {

			if ( check_ajax_referer( 'wpsc_get_change_create_as', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;

			if ( ! ( $current_user->is_agent && $current_user->agent->has_cap( 'create-as' ) ) ) {
				wp_send_json_error( new WP_Error( '001', 'Unauthorized access!' ), 401 );
			}

			$title = esc_attr__( 'Add new', 'supportcandy' );

			ob_start();
			$unique_id = uniqid( 'wpsc_' );
			?>
			<form action="#" onsubmit="return false;" class="frm-add-new-ticket-form-field">

				<div class="wpsc-input-group label">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Name', 'supportcandy' ); ?> 
							<span class="required-char">*</span>
						</label>
					</div>
					<input class="name <?php echo esc_attr( $unique_id ); ?>" type="text" autocomplete="off"/>
				</div>

				<div class="wpsc-input-group label">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Email Address', 'supportcandy' ); ?> 
							<span class="required-char">*</span>
						</label>
					</div>
					<input class="email <?php echo esc_attr( $unique_id ); ?>" type="text" autocomplete="off"/>
				</div>

			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_change_create_as('<?php echo esc_attr( $unique_id ); ?>', '<?php echo esc_attr( wp_create_nonce( 'wpsc_add_new_create_as' ) ); ?>');">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_add_new_tff' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Create as auto-complete
		 *
		 * @return void
		 */
		public static function create_as_autocomplete() {

			if ( check_ajax_referer( 'wpsc_create_as_autocomplete', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;

			if ( ! ( $current_user->is_agent && $current_user->agent->has_cap( 'create-as' ) ) ) {
				wp_send_json_error( new WP_Error( '001', 'Unauthorized access!' ), 401 );
			}

			$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
			if ( ! $term ) {
				wp_send_json_error( new WP_Error( '002', 'Search term should have at least one character!' ), 400 );
			}

			wp_send_json(
				array_map(
					fn( $customer) => array(
						'id'    => $customer->id,
						'text'  => sprintf(
							/* translators: %1$s: Name, %2$s: Email Address */
							esc_attr__( '%1$s (%2$s)', 'supportcandy' ),
							$customer->name,
							$customer->email
						),
						'email' => $customer->email,
						'name'  => $customer->name,
					),
					WPSC_Customer::customer_search( $term )
				)
			);
		}

		/**
		 * Create as add new customer
		 *
		 * @return void
		 */
		public static function add_new_create_as() {

			if ( check_ajax_referer( 'wpsc_add_new_create_as', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;

			if ( ! ( $current_user->is_agent && $current_user->agent->has_cap( 'create-as' ) ) ) {
				wp_send_json_error( new WP_Error( '001', 'Unauthorized access!' ), 401 );
			}

			$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
			if ( ! $name ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$email = isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';
			if ( ! $email || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$customer = WPSC_Customer::get_by_email( $email );
			if ( ! $customer->id ) {
				$customer = WPSC_Customer::insert(
					array(
						'user'  => 0,
						'name'  => $name,
						'email' => $email,
					)
				);
			}

			$response = array(
				'id'    => $customer->id,
				'name'  => $customer->name,
				'email' => $customer->email,
				/* translators: %1$s: Name, %2$s: Email Address */
				'label' => sprintf( esc_attr__( '%1$s (%2$s)', 'supportcandy' ), $customer->name, $customer->email ),
			);

			wp_send_json( $response, 200 );
		}

		/**
		 * Return visibility condition results
		 *
		 * @return void
		 */
		public static function check_tff_visibility() {

			$response = array();
			$tff      = get_option( 'wpsc-tff', array() );
			foreach ( $tff as $slug => $settings ) {

				$visibility = WPSC_TFF::get_visibility( $settings, true );
				if ( ! count( $visibility ) ) {
					continue;
				}

				$current_user = WPSC_Current_User::$current_user;

				if (
					isset( $settings['allowed_user'] ) &&
					(
						( $current_user->is_agent && $settings['allowed_user'] == 'customer' ) ||
						( ! $current_user->is_agent && $settings['allowed_user'] == 'agent' )
					)
				) {
					continue;
				}

				$flag = true;
				foreach ( $visibility as $and_condition ) {

					$temp = false;
					foreach ( $and_condition as $or_condition ) {

						if ( ! isset( WPSC_Ticket_Conditions::$conditions[ $or_condition['slug'] ] ) ) {
							continue;
						}

						if ( isset( $response[ $or_condition['slug'] ] ) && ! $response[ $or_condition['slug'] ] ) {
							continue;
						}

						$cf = WPSC_Custom_Field::get_cf_by_slug( $or_condition['slug'] );
						$value = $cf->type::get_tff_value( $cf->slug, $cf );
						if ( $cf->type::is_valid( $or_condition, $cf, $value ) ) {
							$temp = true;
							break;
						}
					}

					if ( ! $temp ) {
						$flag = false;
						break;
					}
				}

				$response[ $slug ] = $flag ? 1 : 0;
			}
			wp_send_json( $response );
		}

		/**
		 * Create as customer field values
		 *
		 * @return void
		 */
		public static function get_create_as_customer_fields() {

			if ( check_ajax_referer( 'wpsc_get_create_as_customer_fields', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && $current_user->agent->has_cap( 'create-as' ) ) ) {
				wp_send_json_error( new WP_Error( '001', 'Unauthorized!' ), 400 );
			}

			$email = isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';
			if ( ! $email ) {
				wp_send_json_error( new WP_Error( '002', 'Something went wrong!' ), 400 );
			}

			WPSC_Current_User::change_current_user( $email );

			$tff      = get_option( 'wpsc-tff' );
			$response = array();
			foreach ( $tff as $slug => $properties ) {

				$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
				if ( ! $cf ) {
					continue;
				}
				if ( $cf->field === 'customer' && ! $cf->type::$is_default ) {
					$response[] = array(
						'slug' => $slug,
						'html' => $cf->type::print_tff( $cf, $properties ),
					);
				}
			}

			wp_send_json( $response );
		}
	}
endif;

WPSC_New_Ticket::init();
