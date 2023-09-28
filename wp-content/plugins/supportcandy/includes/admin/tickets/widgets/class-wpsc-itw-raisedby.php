<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_ITW_Raisedby' ) ) :

	final class WPSC_ITW_Raisedby {

		/**
		 * Actions for raised by
		 *
		 * @var array
		 */
		public static $actions;

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Edit Raised by widget.
			add_action( 'wp_ajax_wpsc_get_tw_raised_by', array( __CLASS__, 'get_tw_raised_by' ) );
			add_action( 'wp_ajax_wpsc_set_tw_raised_by', array( __CLASS__, 'set_tw_raised_by' ) );

			// Edit ticket raised by.
			add_action( 'wp_ajax_wpsc_it_get_edit_raised_by', array( __CLASS__, 'get_edit_raised_by' ) );
			add_action( 'wp_ajax_wpsc_raisedby_autocomplete', array( __CLASS__, 'get_raisedby_autocomplete' ) );
			add_action( 'wp_ajax_wpsc_it_set_edit_raised_by', array( __CLASS__, 'set_edit_raised_by' ) );

			// Customer other tickets.
			add_action( 'wp_ajax_wpsc_get_rb_other_tickets', array( __CLASS__, 'get_rb_other_tickets' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_rb_other_tickets', array( __CLASS__, 'get_rb_other_tickets' ) );

			// Raised by info.
			add_action( 'wp_ajax_wpsc_get_rb_info', array( __CLASS__, 'get_rb_info' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_rb_info', array( __CLASS__, 'get_rb_info' ) );

			// Edit raised by info.
			add_action( 'wp_ajax_wpsc_get_edit_rb_info', array( __CLASS__, 'get_edit_rb_info' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_edit_rb_info', array( __CLASS__, 'get_edit_rb_info' ) );
			add_action( 'wp_ajax_wpsc_set_edit_rb_info', array( __CLASS__, 'set_edit_rb_info' ) );
			add_action( 'wp_ajax_nopriv_wpsc_set_edit_rb_info', array( __CLASS__, 'set_edit_rb_info' ) );
		}

		/**
		 * Prints body of current widget
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @param array       $settings - widget settings.
		 * @return void
		 */
		public static function print_widget( $ticket, $settings ) {

			$actions = self::load_actions();

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				(
					(
						WPSC_Individual_Ticket::$view_profile == 'customer' ||
						$ticket->customer->id == $current_user->customer->id
					) &&
					$settings['allow-customer']
				) ||
				( WPSC_Individual_Ticket::$view_profile == 'agent' && in_array( $current_user->agent->role, $settings['allowed-agent-roles'] ) )
			) ) {
				return;
			}?>
			<div class="wpsc-it-widget wpsc-itw-raised-by">
				<div class="wpsc-widget-header">
					<h2>
						<?php
							$settings_title = $settings['title'] ? WPSC_Translations::get( 'wpsc-twt-raised-by', stripslashes( $settings['title'] ) ) : stripslashes( $settings['title'] );
							echo esc_attr( $settings_title )
						?>
					</h2>
					<?php
					if ( $ticket->is_active && WPSC_Individual_Ticket::$view_profile == 'agent' && WPSC_Individual_Ticket::has_ticket_cap( 'crb' ) ) :
						?>
						<span onclick="wpsc_it_get_edit_raised_by(<?php echo esc_attr( $ticket->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_it_get_edit_raised_by' ) ); ?>')"><?php WPSC_Icons::get( 'edit' ); ?></span>
						<?php
					endif
					?>
				</div>
				<div class="wpsc-widget-body">
					<div class="user-list-item raised-by">
						<?php echo get_avatar( $ticket->customer->email, 40 ); ?>
						<div class="ul-body">
							<div class="ul-label"><?php echo esc_attr( $ticket->customer->name ); ?></div>
							<?php
							if ( WPSC_Individual_Ticket::$view_profile == 'agent' ) :
								?>
								<div class="ul-actions">
									<?php
									foreach ( $actions as $action ) :
										?>
										<span 
											onclick="<?php echo esc_attr( $action['callback'] ) . '(this, ' . esc_attr( $ticket->id ) . ', \'' . esc_attr( wp_create_nonce( $action['callback'] ) ) . '\')'; ?>"
											title="<?php echo esc_attr( $action['label'] ); ?>">
											<?php WPSC_Icons::get( $action['icon'] ); ?>
										</span>
										<?php
									endforeach;
									?>
								</div>
								<?php
							endif;
							?>
						</div>
					</div>
					<?php do_action( 'wpsc_itw_raisedby', $ticket ); ?>
				</div>
			</div>
			<?php
		}

		/**
		 * Load actions for raised by user
		 *
		 * @return array
		 */
		public static function load_actions() {

			$actions = array(
				'info'          => array(
					'label'    => esc_attr__( 'Info', 'supportcandy' ),
					'icon'     => 'info-circle',
					'callback' => 'wpsc_get_rb_info',
				),
				'other-tickets' => array(
					'label'    => esc_attr__( 'All other tickets of this user', 'supportcandy' ),
					'icon'     => 'ticket-alt',
					'callback' => 'wpsc_get_rb_other_tickets',
				),
			);
			return apply_filters( 'wpsc_itw_raisedby_actions', $actions );
		}

		/**
		 * Get Raised By
		 */
		public static function get_tw_raised_by() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );
			$raised_by      = $ticket_widgets['raised-by'];
			$title          = $raised_by['title'];
			$roles          = get_option( 'wpsc-agent-roles', array() );
			ob_start();
			?>

			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-ticket-raised-by">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Title', 'supportcandy' ); ?></label>
					</div>
					<input name="label" type="text" value="<?php echo esc_attr( $raised_by['title'] ); ?>" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select name="is_enable">
						<option <?php selected( $raised_by['is_enable'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $raised_by['is_enable'], '0' ); ?>  value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed for customer', 'supportcandy' ); ?></label>
					</div>
					<select id="allow-customer" name="allow-customer">
						<option <?php selected( $raised_by['allow-customer'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $raised_by['allow-customer'], '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed agent roles', 'supportcandy' ); ?></label>
					</div>
					<select  multiple id="wpsc-select-agents" name="agents[]" placeholder="search agent...">
						<?php
						foreach ( $roles as $key => $role ) :
							$selected = in_array( $key, $raised_by['allowed-agent-roles'] ) ? 'selected="selected"' : ''
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $role['label'] ); ?></option>
							<?php
						endforeach;
						?>
					</select>
				</div>
				<script>
					jQuery('#wpsc-select-agents').selectWoo({
						allowClear: false,
						placeholder: ""
					});
				</script>
				<?php do_action( 'wpsc_get_raised_by_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_tw_raised_by">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_tw_raised_by' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_tw_raised_by(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_tw_raised_by_widget_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set Raised By
		 */
		public static function set_tw_raised_by() {

			if ( check_ajax_referer( 'wpsc_set_tw_raised_by', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $label ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			// Also change customer cf name.
			$cf       = WPSC_Custom_Field::get_cf_by_slug( 'customer' );
			$cf->name = $label;
			$cf->save();

			$is_enable          = isset( $_POST['is_enable'] ) ? intval( $_POST['is_enable'] ) : 0;
			$allow_for_customer = isset( $_POST['allow-customer'] ) ? intval( $_POST['allow-customer'] ) : 0;
			$agents             = isset( $_POST['agents'] ) ? array_filter( array_map( 'intval', $_POST['agents'] ) ) : array();

			$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );

			$ticket_widgets['raised-by']['title']               = $label;
			$ticket_widgets['raised-by']['is_enable']           = $is_enable;
			$ticket_widgets['raised-by']['allow-customer']      = $allow_for_customer;
			$ticket_widgets['raised-by']['allowed-agent-roles'] = $agents;
			update_option( 'wpsc-ticket-widget', $ticket_widgets );

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-twt-raised-by' );
			WPSC_Translations::add( 'wpsc-twt-raised-by', stripslashes( $label ) );
			wp_die();
		}

		/**
		 * Change create as or raised by modal
		 *
		 * @return void
		 */
		public static function get_edit_raised_by() {

			if ( check_ajax_referer( 'wpsc_it_get_edit_raised_by', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'crb' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$ticket = WPSC_Individual_Ticket::$ticket;

			$widgets = get_option( 'wpsc-ticket-widget' );
			$title   = $widgets['raised-by']['title'];

			ob_start();
			$unique_id = uniqid( 'wpsc_' );
			?>
			<form action="#" onsubmit="return false;" class="change-raised-by <?php echo esc_attr( $unique_id ); ?>">
				<div class="wpsc-input-group label">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'User type', 'supportcandy' ); ?>
						</label>
					</div>
					<select class="type <?php echo esc_attr( $unique_id ); ?>" name="type">
						<option value="existing" <?php echo $ticket->customer->id ? 'selected' : ''; ?>><?php esc_attr_e( 'Existing Customer / Registered User', 'supportcandy' ); ?></option>
						<option value="new" <?php echo ! $ticket->customer->id ? 'selected' : ''; ?>><?php esc_attr_e( 'New Customer', 'supportcandy' ); ?></option>
					</select>
					<script>
						jQuery('select.type.<?php echo esc_attr( $unique_id ); ?>').change(function(){
							var type = jQuery(this).val().trim();
							if (type == 'existing') {
								jQuery('.wpsc-input-group.new').hide();
								jQuery('.wpsc-input-group.existing').show();
							} else {
								jQuery('.wpsc-input-group.existing').hide();
								jQuery('.wpsc-input-group.new').show();
							}
						});
					</script>
				</div>
				<div class="wpsc-input-group existing" style="<?php echo ! $ticket->customer->id ? 'display:none;' : ''; ?>">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Existing Customer / Registered User', 'supportcandy' ); ?>
						</label>
					</div>
					<select class="existing-user <?php echo esc_attr( $unique_id ); ?>" name="customer_email">
						<?php
						if ( $ticket->customer->id ) :
							?>
							<option value="<?php echo esc_attr( $ticket->customer->id ); ?>"><?php echo esc_attr( $ticket->customer->name ) . ' (' . esc_attr( $ticket->customer->email ) . ')'; ?></option>
							<?php
						endif
						?>
					</select>
					<script>
						jQuery('select.existing-user.<?php echo esc_attr( $unique_id ); ?>').selectWoo({
							ajax: {
								url: supportcandy.ajax_url,
								type: 'POST',
								dataType: 'json',
								delay: 250,
								data: function (params) {
									return {
										q: params.term, // search term
										ticket_id: <?php echo esc_attr( $ticket->id ); ?>,
										action: 'wpsc_raisedby_autocomplete',
										_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_raisedby_autocomplete' ) ); ?>',
									};
								},
								processResults: function (data, params) {
									var terms = [];
									if ( data ) {
										jQuery.each( data, function( id, customer ) {
											terms.push({ 
												id: customer.email, 
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
							allowClear: true,
							placeholder: ""
						});
						supportcandy.translations.raisedByEditWidget = {
							invalidEmail: '<?php esc_attr_e( 'Invalid email address!', 'supportcandy' ); ?>'
						}
					</script>
				</div>
				<div class="wpsc-input-group label new" style="<?php echo $ticket->customer->id ? 'display:none;' : ''; ?>">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Name', 'supportcandy' ); ?> 
							<span class="required-char">*</span>
						</label>
					</div>
					<input class="name <?php echo esc_attr( $unique_id ); ?>" type="text" name="name" autocomplete="off"/>
				</div>
				<div class="wpsc-input-group label new" style="<?php echo $ticket->customer->id ? 'display:none;' : ''; ?>">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Email Address', 'supportcandy' ); ?> 
							<span class="required-char">*</span>
						</label>
					</div>
					<input class="email <?php echo esc_attr( $unique_id ); ?>" type="text" name="email" autocomplete="off"/>
				</div>
				<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket->id ); ?>">
				<input type="hidden" name="action" value="wpsc_it_set_edit_raised_by">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_it_set_edit_raised_by' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_it_set_edit_raised_by(this, <?php echo esc_attr( $ticket->id ); ?>, '<?php echo esc_attr( $unique_id ); ?>');">
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
		 * Set raised by
		 *
		 * @return void
		 */
		public static function set_edit_raised_by() {

			if ( check_ajax_referer( 'wpsc_it_set_edit_raised_by', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'crb' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$ticket = WPSC_Individual_Ticket::$ticket;
			$clone = clone $ticket;
			if ( ! $ticket->is_active ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
			if ( ! $type ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$customer = new WPSC_Customer();
			if ( $type == 'new' ) {

				$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
				if ( ! $type ) {
					wp_send_json_error( 'Something went wrong!', 400 );
				}

				$email = isset( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';
				if ( ! $email || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					wp_send_json_error( 'Something went wrong!', 400 );
				}

				// Check if this email address is already present in customer model.
				$customer = WPSC_Customer::get_by_email( $email );

				// Check if this email address is already a registered user and if yes, create a customer record.
				if ( ! $customer->id ) {

					$user = get_user_by( 'email', $email );
					if ( $user ) {

						$customer = WPSC_Customer::insert(
							array(
								'user'  => $user->ID,
								'name'  => $user->display_name,
								'email' => $email,
							)
						);

					} else {

						$customer = WPSC_Customer::insert(
							array(
								'user'  => 0,
								'name'  => $name,
								'email' => $email,
							)
						);
					}
				}
			} else {

				$email = isset( $_POST['customer_email'] ) ? sanitize_text_field( wp_unslash( $_POST['customer_email'] ) ) : '';
				if ( ! $email || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					wp_die();
				}

				// Check if this email address is already present in customer model.
				$customer = WPSC_Customer::get_by_email( $email );
				if ( ! $customer->id ) {

					$user = get_user_by( 'email', $email );
					if ( ! $user ) {
						wp_send_json_error( 'Something went wrong!', 400 ); // We are expecting either existing customer record or registered user.
					}
					$customer = WPSC_Customer::insert(
						array(
							'user'  => $user->ID,
							'name'  => $user->display_name,
							'email' => $email,
						)
					);
				}
			}

			// Change raised by if it has been changed.
			if ( $customer->id && $ticket->customer->id != $customer->id ) {

				$current_user = WPSC_Current_User::$current_user;
				$prev         = $ticket->customer;
				WPSC_Individual_Ticket::change_raised_by( $prev, $customer, $current_user->customer->id );
			}

			// Count prev customer ticket after change raised by.
			WPSC_Customers::customer_ticket_count( $clone );
			wp_die();
		}

		/**
		 * Get raised by autocomplete
		 *
		 * @return void
		 */
		public static function get_raisedby_autocomplete() {

			if ( check_ajax_referer( 'wpsc_raisedby_autocomplete', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'crb' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$term = isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '';
			if ( ! $term ) {
				wp_send_json_error( new WP_Error( '005', 'Search term should have at least one character!' ), 400 );
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
		 * Get customer other tickets
		 *
		 * @return void
		 */
		public static function get_rb_other_tickets() {

			if ( check_ajax_referer( 'wpsc_get_rb_other_tickets', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( WPSC_Individual_Ticket::$view_profile == 'agent' && WPSC_Individual_Ticket::has_ticket_cap( 'view' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$ticket = WPSC_Individual_Ticket::$ticket;

			$title = esc_attr( $ticket->customer->name );

			// filters.
			$filters = array(
				'filterSlug'     => 'all',
				'orderby'        => 'date_created',
				'order'          => 'DESC',
				'items_per_page' => 0,
				'is_active'      => 1,
			);

			// system query.
			$filters['system_query'] = $current_user->get_tl_system_query( $filters );

			// meta query.
			$filters['meta_query'] = array(
				'relation' => 'AND',
				array(
					'slug'    => 'customer',
					'compare' => '=',
					'val'     => $ticket->customer->id,
				),
			);

			$tickets    = WPSC_Ticket::find( $filters )['results'];
			$list_items = get_option( 'wpsc-atl-list-items' );
			$unique_id  = uniqid();

			ob_start();
			?>
			<div style="overflow-x:auto; width:100%;">
				<table class="wpsc-cust-all-tickets wpsc-setting-tbl">
					<thead>
						<tr>
							<?php
							foreach ( $list_items as $slug ) :
								$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
								if ( ! $cf ) {
									continue;
								}
								?>
								<th style="min-width: <?php echo esc_attr( $cf->tl_width ); ?>px;"><?php echo esc_attr( $cf->name ); ?></th>
								<?php
							endforeach;
							?>
						</tr>
					</thead>	
					<tbody>
						<?php
						foreach ( $tickets as $ticket ) :
							?>
							<tr class="wpsc-raised-by-ticket" onclick="if(link) wpsc_open_customer_ticket(<?php echo esc_attr( $ticket->id ); ?>)">
								<?php
								foreach ( $list_items as $slug ) :
									$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
									if ( ! $cf ) {
										continue;
									}
									?>
									<td onmouseover="link=true;">
										<?php
										if ( in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ) {
											$cf->type::print_tl_ticket_field_val( $cf, $ticket );
										} else {
											$cf->type::print_tl_customer_field_val( $cf, $ticket->customer );
										}
										?>
									</td>
									<?php
								endforeach;
								?>
							</tr>
							<?php
						endforeach;
						?>
					</tbody>
				</table>
				<script>
					jQuery('table.wpsc-cust-all-tickets').DataTable({
						ordering: false,
						pageLength: 20,
						bLengthChange: false,
						columnDefs: [ 
							{ targets: -1, searchable: false },
							{ targets: '_all', className: 'dt-left' }
						],
						language: supportcandy.translations.datatables
					});

					function wpsc_open_customer_ticket( id ) {

						if ( wpsc_is_description_text() ) {
							if ( confirm( supportcandy.translations.warning_message ) ) {
								wpsc_close_modal(); 
								ticket_id = jQuery('#wpsc-current-ticket').val();
								wpsc_clear_saved_draft_reply( ticket_id );
								wpsc_get_individual_ticket( id );
							} else {
								return;
							}
						}else{
							wpsc_close_modal(); 
							wpsc_get_individual_ticket( id );
						}
					}
				</script>
			</div>
			<?php

			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Close', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Get raised by info
		 *
		 * @return void
		 */
		public static function get_rb_info() {

			if ( check_ajax_referer( 'wpsc_get_rb_info', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();
			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( WPSC_Individual_Ticket::$view_profile == 'agent' && WPSC_Individual_Ticket::has_ticket_cap( 'view' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$ticket    = WPSC_Individual_Ticket::$ticket;
			$customer  = $ticket->customer;
			$title     = esc_attr__( 'Customer info', 'supportcandy' );
			$gs        = get_option( 'wpsc-gs-general', array() );
			$unique_id = uniqid();

			ob_start();
			?>
			<div class="wpsc-thread-info">

				<div style="width: 100%;">

					<table class="wpsc-setting-tbl <?php echo esc_attr( $unique_id ); ?>" style="margin-bottom: 15px;">
						<thead>
							<tr>
								<th><?php esc_attr_e( 'Field', 'supportcandy' ); ?></th>
								<th><?php esc_attr_e( 'Value', 'supportcandy' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><?php esc_attr_e( 'Name', 'supportcandy' ); ?>:</td>
								<td><?php echo esc_attr( $customer->name ); ?></td>
							</tr>
							<?php
							if ( $current_user->is_agent && in_array( $current_user->agent->role, $gs['allow-ar-thread-email'] ) ) {
								?>
								<tr>
									<td><?php esc_attr_e( 'Email Address', 'supportcandy' ); ?>:</td>
									<td><?php echo esc_attr( $customer->email ); ?></td>
								</tr>
								<?php
							}

							foreach ( WPSC_Custom_Field::$custom_fields as $cf ) :
								if ( $cf->field !== 'customer' || in_array( $cf->slug, WPSC_DF_Customer::$ignore_customer_info_cft ) ) {
									continue;
								}
								?>
								<tr>
									<td><?php echo esc_attr( $cf->name ); ?>:</td>
									<td><?php $cf->type::print_widget_customer_field_val( $cf, $customer ); ?></td>
								</tr>
								<?php
							endforeach;
							?>
						</tbody>
					</table>
					<script>
						jQuery('.<?php echo esc_attr( $unique_id ); ?>').DataTable({
							ordering: false,
							pageLength: 20,
							bLengthChange: false,
							columnDefs: [ 
								{ targets: -1, searchable: false },
								{ targets: '_all', className: 'dt-left' }
							],
							language: supportcandy.translations.datatables
						});
					</script>
				</div>
				<?php do_action( 'wpsc_get_rb_info', $customer ); ?>
			</div>
			<?php
			$body = ob_get_clean();

			ob_start();
			if ( $current_user->agent->has_cap( 'eci-access' ) ) {
				?>
				<button class="wpsc-button small primary" onclick="wpsc_get_edit_rb_info(this, <?php echo esc_attr( $ticket->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_rb_info' ) ); ?>');">
					<?php esc_attr_e( 'Edit Info', 'supportcandy' ); ?>
				</button>
				<?php
			}
			?>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Close', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_rb_info_footer', $customer );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Get edit customer info
		 *
		 * @return void
		 */
		public static function get_edit_rb_info() {

			if ( check_ajax_referer( 'wpsc_get_edit_rb_info', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();
			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( WPSC_Individual_Ticket::$view_profile == 'agent' && $current_user->agent->has_cap( 'eci-access' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$ticket   = WPSC_Individual_Ticket::$ticket;
			$customer = $ticket->customer;

			$title = esc_attr__( 'Edit customer info', 'supportcandy' );
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="frm-edit-rb-info">

				<div class="wpsc-input-group" style="align-items:flex-end;">
					<input type="text" id="rb-search" placeholder="<?php esc_attr_e( 'Search...', 'supportcandy' ); ?>" autocomplete="off" style="max-width:200px;">
				</div>

				<?php

				$cf = WPSC_Custom_Field::get_cf_by_slug( 'name' )
				?>
				<div class="wpsc-tff">
					<div class="wpsc-tff-label">
						<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
					</div>
					<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>
					<input 
						type="text" 
						name="<?php echo esc_attr( $cf->slug ); ?>" 
						value="<?php echo esc_attr( $customer->name ); ?>"
						placeholder="<?php echo esc_attr( $cf->placeholder_text ); ?>"
						autocomplete="off"/>
				</div>
				<?php
				foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {
					if ( $cf->field !== 'customer' || in_array( $cf->slug, WPSC_DF_Customer::$ignore_customer_info_cft ) ) {
						continue;
					}
					$properties = array(
						'is-required' => 0,
						'width'       => 'full',
						'visibility'  => '',
					);
					echo $cf->type::print_edit_customer_info( $cf, $customer, $properties ); // phpcs:ignore
				}
				do_action( 'wpsc_get_edit_rb_info_body', $customer );
				?>
				<input type="hidden" name="action" value="wpsc_set_edit_rb_info"/>
				<input type="hidden" name="ticket_id" value="<?php echo esc_attr( $ticket->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_rb_info' ) ); ?>">

				<script>
					jQuery('#rb-search').keyup(function(e) {
						var val = jQuery(this).val().trim();
						var regex = new RegExp(val, "i");
						jQuery('.frm-edit-rb-info .wpsc-tff').each(function(){
							var name 	= jQuery(this).find('.wpsc-tff-label').text();
							if (name.search(regex) < 0) {
								jQuery(this).hide();
							} else {
								jQuery(this).show();
							}
						})
					});
				</script>

			</form>
			<?php
			do_action( 'wpsc_get_edit_rb_info_footer', $customer );
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_edit_rb_info(this, <?php echo esc_attr( $ticket->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_rb_info' ) ); ?>');">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Save raised by info
		 *
		 * @return void
		 */
		public static function set_edit_rb_info() {

			if ( check_ajax_referer( 'wpsc_set_edit_rb_info', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();
			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( WPSC_Individual_Ticket::$view_profile == 'agent' && $current_user->agent->has_cap( 'eci-access' ) ) ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$ticket   = WPSC_Individual_Ticket::$ticket;
			$customer = $ticket->customer;

			$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
			if ( ! $name ) {
				wp_send_json_error( new WP_Error( '002', 'Bad request!' ), 400 );
			}

			if ( $customer->name != $name ) {
				$customer->name = $name;
				$customer->save();
				// Update WP User if available.
				if ( $customer->user ) {
					wp_update_user(
						array(
							'ID'           => $customer->user->ID,
							'display_name' => $name,
						)
					);
				}
			}

			$cfs = array();
			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

				if ( $cf->field !== 'customer' || $cf->type::$is_default ) {
					continue;
				}
				$cfs[ $cf->type::$slug ][] = $cf;
			}

			foreach ( $cfs as $slug => $fields ) {
				WPSC_Functions::$ref_classes[ $slug ]['class']::set_create_ticket_data( array( 'customer' => $customer->id ), $cfs, true );
			}
			wp_die();
		}
	}
endif;

WPSC_ITW_Raisedby::init();
