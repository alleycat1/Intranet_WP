<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Customers' ) ) :

	final class WPSC_Customers {

		/**
		 * Initialize the class
		 *
		 * @return void
		 */
		public static function init() {

			// get customer list.
			add_action( 'wp_ajax_wpsc_get_customer_list', array( __CLASS__, 'get_customer_list' ) );

			// view customer info.
			add_action( 'wp_ajax_wpsc_view_customer_info', array( __CLASS__, 'view_customer_info' ) );

			// edit customer info.
			add_action( 'wp_ajax_wpsc_get_edit_customer_info', array( __CLASS__, 'get_edit_customer_info' ) );
			add_action( 'wp_ajax_wpsc_set_edit_customer_info', array( __CLASS__, 'set_edit_customer_info' ) );

			// view customer logs.
			add_action( 'wp_ajax_wpsc_view_customer_logs', array( __CLASS__, 'view_customer_logs' ) );

			// calculate customer ticket count.
			add_action( 'wpsc_create_new_ticket', array( __CLASS__, 'customer_ticket_count' ) );
			add_action( 'wpsc_delete_ticket', array( __CLASS__, 'customer_ticket_count' ) );
			add_action( 'wpsc_ticket_restore', array( __CLASS__, 'customer_ticket_count' ) );
			add_action( 'wpsc_change_raised_by', array( __CLASS__, 'customer_ticket_count' ), 200, 4 );
		}

		/**
		 * Admin submenu layout
		 *
		 * @return void
		 */
		public static function layout() {?>

			<div class="wrap">
				<hr class="wp-header-end">
				<div id="wpsc-container">
					<div class="wpsc-setting-header">
						<h2><?php esc_attr_e( 'Customers', 'supportcandy' ); ?></h2>
					</div>
					<div class="wpsc-setting-section-body">
						<?php
						self::load_customer_list();
						WPSC_Tickets::load_html_snippets();
						?>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * List customer list
		 *
		 * @return void
		 */
		public static function load_customer_list() {

			$customers = WPSC_Customer::find( array( 'items_per_page' => 0 ) )['results'];
			?>
			<table class="wpsc_customer_list wpsc-setting-tbl">
				<thead>
					<tr>
						<th><?php esc_attr_e( 'Name', 'supportcandy' ); ?></th>
						<th><?php esc_attr_e( 'Email address', 'supportcandy' ); ?></th>
						<th><?php esc_attr_e( 'Number of tickets', 'supportcandy' ); ?></th>
						<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
					</tr>
				</thead>
			</table>

			<script>
				jQuery(document).ready(function() {

					jQuery('.wpsc_customer_list').dataTable({
						processing: true,
						serverSide: true,
						serverMethod: 'post',
						ajax: { 
							url: supportcandy.ajax_url,
							data: {
								'action': 'wpsc_get_customer_list',
								'_ajax_nonce': '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_customer_list' ) ); ?>'
							}
						},
						'columns': [
							{ data: 'name' },
							{ data: 'email' },
							{ data: 'tickets' },
							{ data: 'actions' },
						],
						'bDestroy': true,
						'searching': true,
						'ordering': false,
						'bLengthChange': false,
						pageLength: 20,
						columnDefs: [ 
							{ targets: '_all', className: 'dt-left' },
						],
						language: supportcandy.translations.datatables
					});
				});

				<?php do_action( 'wpsc_js_customer_list_functions' ); ?>
			</script>
			<?php
		}

		/**
		 * Get list of all customers
		 *
		 * @return void
		 */
		public static function get_customer_list() {

			if ( check_ajax_referer( 'wpsc_get_customer_list', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorized request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorized request!', 401 );
			}

			$search     = isset( $_POST['search'] ) && isset( $_POST['search']['value'] ) ? sanitize_text_field( wp_unslash( $_POST['search']['value'] ) ) : '';
			$draw       = isset( $_POST['draw'] ) ? intval( $_POST['draw'] ) : 1;
			$start      = isset( $_POST['start'] ) ? intval( $_POST['start'] ) : 1;
			$rowperpage = isset( $_POST['length'] ) ? intval( $_POST['length'] ) : 20;
			$page_no    = ( $start / $rowperpage ) + 1;

			$response  = WPSC_Customer::find(
				array(
					'search'         => $search,
					'items_per_page' => $rowperpage,
					'page_no'        => $page_no,
					'orderby'        => 'ticket_count',
					'order'          => 'DESC',
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'slug'    => 'ticket_count',
							'compare' => '>',
							'val'     => 0,
						),
					),
				)
			);
			$customers = $response['results'];

			$data = array();
			foreach ( $customers as $customer ) {

				ob_start();
				?>
				<a href="javascript:wpsc_view_customer_info(<?php echo esc_attr( $customer->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_view_customer_info' ) ); ?>')" class="wpsc-link">
					<?php esc_attr_e( 'View', 'supportcandy' ); ?>
				</a> |
				<a href="javascript:wpsc_get_edit_customer_info(<?php echo esc_attr( $customer->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_customer_info' ) ); ?>')" class="wpsc-link">
					<?php esc_attr_e( 'Edit', 'supportcandy' ); ?>
				</a> |
				<a href="javascript:wpsc_view_customer_logs(<?php echo esc_attr( $customer->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_view_customer_logs' ) ); ?>')" class="wpsc-link">
					<?php esc_attr_e( 'Logs', 'supportcandy' ); ?>
				</a>
				<?php
				$actions = ob_get_clean();

				$data[] = array(
					'name'    => $customer->name,
					'email'   => $customer->email,
					'tickets' => $customer->ticket_count,
					'actions' => $actions,
				);
			}

			$response = array(
				'draw'                 => intval( $draw ),
				'iTotalRecords'        => $response['total_items'],
				'iTotalDisplayRecords' => $response['total_items'],
				'data'                 => $data,
			);

			wp_send_json( $response );
		}

		/**
		 * View customer info
		 *
		 * @return void
		 */
		public static function view_customer_info() {

			if ( check_ajax_referer( 'wpsc_view_customer_info', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$customer_id = isset( $_POST['customer_id'] ) ? intval( $_POST['customer_id'] ) : 0;
			if ( ! $customer_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$customer = new WPSC_Customer( $customer_id );
			if ( ! $customer->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$title     = esc_attr__( 'Customer info', 'supportcandy' );
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
							<tr>
								<td><?php esc_attr_e( 'Email Address', 'supportcandy' ); ?>:</td>
								<td><?php echo esc_attr( $customer->email ); ?></td>
							</tr>
							<?php
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
							searching: false,
							paging:	   false,
							ordering:  false,
							info:      false
						});
					</script>
				</div>
				<?php do_action( 'wpsc_view_customer_info', $customer ); ?>
			</div>
			<?php
			$body = ob_get_clean();

			ob_start();

			?>
			<button class="wpsc-button small primary" onclick="wpsc_get_edit_customer_info(<?php echo esc_attr( $customer->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_customer_info' ) ); ?>');">
				<?php esc_attr_e( 'Edit Info', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Close', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_view_customer_info_footer', $customer );
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
		public static function get_edit_customer_info() {

			if ( check_ajax_referer( 'wpsc_get_edit_customer_info', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$customer_id = isset( $_POST['customer_id'] ) ? intval( $_POST['customer_id'] ) : 0;
			if ( ! $customer_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$customer = new WPSC_Customer( $customer_id );
			if ( ! $customer->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$title = esc_attr__( 'Edit customer info', 'supportcandy' );
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="frm-edit-customer-info">
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
				do_action( 'wpsc_get_edit_customer_info_body', $customer );
				?>
				<input type="hidden" name="action" value="wpsc_set_edit_customer_info"/>
				<input type="hidden" name="id" value="<?php echo esc_attr( $customer->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_customer_info' ) ); ?>">
			</form>
			<?php
			do_action( 'wpsc_get_edit_customer_info_footer', $customer );
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_edit_customer_info(this, <?php echo esc_attr( $customer->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_view_customer_info' ) ); ?>');">
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
		 * Save customer info
		 *
		 * @return void
		 */
		public static function set_edit_customer_info() {

			if ( check_ajax_referer( 'wpsc_set_edit_customer_info', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$customer_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $customer_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$customer = new WPSC_Customer( $customer_id );
			if ( ! $customer->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

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

		/**
		 * View customer logs
		 *
		 * @return void
		 */
		public static function view_customer_logs() {

			if ( check_ajax_referer( 'wpsc_view_customer_logs', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$customer_id = isset( $_POST['customer_id'] ) ? intval( $_POST['customer_id'] ) : 0;
			if ( ! $customer_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$customer = new WPSC_Customer( $customer_id );
			if ( ! $customer->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$title     = esc_attr__( 'Customer logs', 'supportcandy' );
			$unique_id = uniqid();

			ob_start();
			$logs = WPSC_Log::find(
				array(
					'meta_query' => array(
						'relation' => 'AND',
						array(
							'slug'    => 'type',
							'compare' => '=',
							'val'     => 'customer',
						),
						array(
							'slug'    => 'ref_id',
							'compare' => '=',
							'val'     => $customer->id,
						),
					),
				)
			)['results'];
			?>
			<div style="width: 100%;">
				<?php
				foreach ( $logs as $log ) {
					self::print_log( $log );
				}
				?>
			</div>
			<?php

			do_action( 'wpsc_view_customer_info', $customer );

			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Close', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_view_customer_logs_footer', $customer );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Print customer log
		 *
		 * @param WPSC_Log $log - log object.
		 * @return void
		 */
		public static function print_log( $log ) {

			$advanced      = get_option( 'wpsc-ms-advanced-settings', array() );
			$now           = new DateTime();
			$date          = $log->date_created->setTimezone( wp_timezone() );
			$time_date_str = $date->format( $advanced['thread-date-format'] );
			$time_diff_str = WPSC_Functions::date_interval_highest_unit_ago( $date->diff( $now ) );
			$title         = $advanced['thread-date-display-as'] == 'date' ? $time_diff_str : $time_date_str;
			$time_str      = $advanced['thread-date-display-as'] == 'date' ? $time_date_str : $time_diff_str;

			$body     = json_decode( $log->body );
			$customer = new WPSC_Customer( $log->modified_by );

			$cf = WPSC_Custom_Field::get_cf_by_slug( $body->slug );
			if ( ! $cf ) {
				return;
			}
			?>
			<div class="wpsc-thread log">
				<div class="thread-avatar">
					<?php
					if ( $log->modified_by ) {
						echo get_avatar( $customer->email, 32 );
					} else {
						WPSC_Icons::get( 'system' );
					}
					?>
				</div>
				<div class="thread-body">
					<div class="thread-header">
						<div class="user-info">
							<div>
								<?php
								if ( $log->modified_by ) {
									printf(
										/* translators: %1$s: User Name, %2$s: Field Name */
										esc_attr__( '%1$s changed the %2$s', 'supportcandy' ),
										'<strong>' . esc_attr( $customer->name ) . '</strong>',
										'<strong>' . esc_attr( $cf->name ) . '</strong>'
									);
								} else {
									printf(
										/* translators: %1$s: Field Name */
										esc_attr__( 'The %1$s has been changed', 'supportcandy' ),
										'<strong>' . esc_attr( $cf->name ) . '</strong>'
									);
								}

								?>
							</div>
							<span class="thread-time" title="<?php echo esc_attr( $title ); ?>"><?php echo esc_attr( $time_str ); ?></span>
						</div>
					</div>
					<div class="wpsc-log-diff">
						<div class="lhs"><?php $cf->type::print_val( $cf, $body->prev ); ?></div>
						<div class="transform-icon">
							<?php
							if ( is_rtl() ) {
								WPSC_Icons::get( 'arrow-left' );
							} else {
								WPSC_Icons::get( 'arrow-right' );
							}
							?>
						</div>
						<div class="rhs"><?php $cf->type::print_val( $cf, $body->new ); ?></div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Count customer tickets after create/delete/restore ticket
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return void
		 */
		public static function customer_ticket_count( $ticket ) {

			$ticket->customer->update_ticket_count();
		}
	}
endif;

WPSC_Customers::init();
