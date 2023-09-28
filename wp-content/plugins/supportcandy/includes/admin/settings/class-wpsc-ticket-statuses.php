<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Ticket_Statuses' ) ) :

	final class WPSC_Ticket_Statuses {

		/**
		 * Initialize this class
		 */
		public static function init() {

			// List.
			add_action( 'wp_ajax_wpsc_get_ticket_statuses', array( __CLASS__, 'get_statuses' ) );

			// Add new.
			add_action( 'wp_ajax_wpsc_get_add_new_status', array( __CLASS__, 'get_add_new_status' ) );
			add_action( 'wp_ajax_wpsc_set_add_status', array( __CLASS__, 'set_add_status' ) );

			// Edit.
			add_action( 'wp_ajax_wpsc_get_edit_status', array( __CLASS__, 'get_edit_status' ) );
			add_action( 'wp_ajax_wpsc_set_edit_status', array( __CLASS__, 'set_edit_status' ) );

			// Delete.
			add_action( 'wp_ajax_wpsc_get_delete_status', array( __CLASS__, 'get_delete_status' ) );
			add_action( 'wp_ajax_wpsc_set_delete_status', array( __CLASS__, 'set_delete_status' ) );
		}

		/**
		 * Get status settings
		 *
		 * @return void
		 */
		public static function get_statuses() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$statuses         = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			$cf               = WPSC_Custom_Field::get_cf_by_slug( 'status' );
			$general_settings = get_option( 'wpsc-gs-general' );
			$default_statuses = array( 1, 2, 3, 4, $cf->default_value[0], $general_settings['ticket-status-after-customer-reply'], $general_settings['ticket-status-after-agent-reply'], $general_settings['close-ticket-status'] );
			$default_statuses = array_unique( $default_statuses );?>

			<div class="wpsc-setting-header">
				<h2><?php esc_attr_e( 'Ticket Statuses', 'supportcandy' ); ?></h2>
			</div>
			<div class="wpsc-setting-section-body">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/ticket-statuses/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<table class="wpsc-ticket-statuses wpsc-setting-tbl">
					<thead>
						<tr>
							<th><?php esc_attr_e( 'Name', 'supportcandy' ); ?></th>
							<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $statuses as $status ) {
							?>
							<tr>
								<td><span class="wpsc-tag" style="color: <?php echo esc_attr( $status->color ); ?>; background-color: <?php echo esc_attr( $status->bg_color ); ?>;" ><?php echo esc_attr( $status->name ); ?></span></td>
								<td>
									<div class="actions">
										<a class="wpsc-link" href="javascript:wpsc_get_edit_status(<?php echo esc_attr( $status->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_status' ) ); ?>');" ><?php echo esc_attr__( 'Edit', 'supportcandy' ); ?></a>
										<?php if ( ! in_array( $status->id, $default_statuses ) ) : ?>
											| <a class="wpsc-link" href="javascript:wpsc_get_delete_status(<?php echo esc_attr( $status->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_delete_status' ) ); ?>');" ><?php echo esc_attr__( 'Delete', 'supportcandy' ); ?></a>
											<?php
										endif;
										?>
									</div>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
			<script>
				jQuery('table.wpsc-ticket-statuses').DataTable({
					ordering: false,
					pageLength: 20,
					bLengthChange: false,
					columnDefs: [ 
						{ targets: -1, searchable: false },
						{ targets: '_all', className: 'dt-left' }
					],
					dom: 'Bfrtip',
					buttons: [
						{
							text: '<?php esc_attr_e( 'Add new', 'supportcandy' ); ?>',
							className: 'wpsc-button small primary',
							action: function ( e, dt, node, config ) {
								wpsc_show_modal();
								var data = { action: 'wpsc_get_add_new_status' };
								jQuery.post(
									supportcandy.ajax_url,
									data,
									function (response) {

										// Set to modal.
										jQuery( '.wpsc-modal-header' ).text( response.title );
										jQuery( '.wpsc-modal-body' ).html( response.body );
										jQuery( '.wpsc-modal-footer' ).html( response.footer );
										// Display modal.
										wpsc_show_modal_inner_container();
									}
								);
							}
						}
					],
					language: supportcandy.translations.datatables
				});
			</script>
			<?php
			wp_die();
		}

		/**
		 * Get add new status
		 */
		public static function get_add_new_status() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$title = esc_attr__( 'Add new', 'supportcandy' );

			$statuses = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-add-status">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Name', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<input name="name" type="text" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" name="color" value="#ffffff" />
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" name="bg-color" value="#1E90FF" />
				</div>

				<div data-type="" data-required="true" class="wpsc-input-group load-after">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Load after', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="load-after" class="load-after">
						<option value="__TOP__">-- <?php esc_attr_e( 'TOP', 'supportcandy' ); ?> --</option>
						<?php
						foreach ( $statuses as $status ) {
							?>
							<option value="<?php echo esc_attr( $status->id ); ?>"><?php echo esc_attr( $status->name ); ?></option>
							<?php
						}
						?>
						<option selected value="__END__">-- <?php esc_attr_e( 'END', 'supportcandy' ); ?> --</option>
					</select>
					<script>jQuery('select.load-after').selectWoo();</script>
				</div>


				<script>jQuery('.wpsc-color-picker').wpColorPicker();</script>
				<?php do_action( 'wpsc_get_add_status_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_add_status">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_status' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_add_status(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_add_status' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set add new status
		 */
		public static function set_add_status() {

			if ( check_ajax_referer( 'wpsc_set_add_status', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
			if ( ! $name ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$color = isset( $_POST['color'] ) ? sanitize_text_field( wp_unslash( $_POST['color'] ) ) : '';
			if ( ! $color ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$bgcolor = isset( $_POST['bg-color'] ) ? sanitize_text_field( wp_unslash( $_POST['bg-color'] ) ) : '';
			if ( ! $bgcolor ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$data   = array(
				'name'     => $name,
				'color'    => $color,
				'bg_color' => $bgcolor,
			);
			$status = WPSC_Status::insert( $data );

			// set laod order.
			$statuses_list = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			$load_after    = isset( $_POST['load-after'] ) ? sanitize_text_field( wp_unslash( $_POST['load-after'] ) ) : '__END__';

			if ( $load_after != '__END__' ) {

				$count = 1;

				if ( $load_after == '__TOP__' ) {
					$status->load_order = $count++;
					$status->save();
				}

				foreach ( $statuses_list as $stat ) {

					if ( $stat->id == $status->id ) {
						continue;
					}

					$stat->load_order = $count++;
					$stat->save();

					if ( $stat->id == $load_after ) {
						$status->load_order = $count++;
						$status->save();
					}
				}
			}

			wp_die();
		}

		/**
		 *  Get edit status
		 */
		public static function get_edit_status() {

			global $wpdb;

			if ( check_ajax_referer( 'wpsc_get_edit_status', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$status = new WPSC_Status( $id );
			if ( ! $status->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$title = esc_attr( $status->name );

			$statuses = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-status">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Name', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<input name="name" type="text" value="<?php echo esc_attr( $status->name ); ?>" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" name="color" value="<?php echo esc_attr( $status->color ); ?>" />
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" name="bg-color" value="<?php echo esc_attr( $status->bg_color ); ?>" />
				</div>

				<div data-type="" data-required="true" class="wpsc-input-group load-after">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Load after', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="load-after" class="load-after">
						<option value="__TOP__">-- <?php esc_attr_e( 'TOP', 'supportcandy' ); ?> --</option>
						<?php
						$load_after = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_statuses WHERE load_order < {$status->load_order} ORDER BY load_order DESC LIMIT 1" );
						foreach ( $statuses as $stat ) {
							if ( $status == $stat ) {
								continue;
							}
							?>
							<option <?php selected( $load_after, $stat->id ); ?> value="<?php echo esc_attr( $stat->id ); ?>"><?php echo esc_attr( $stat->name ); ?></option>
							<?php
						}
						?>
						<option value="__END__">-- <?php esc_attr_e( 'END', 'supportcandy' ); ?> --</option>
					</select>
					<script>jQuery('select.load-after').selectWoo();</script>
				</div>


				<script>jQuery('.wpsc-color-picker').wpColorPicker();</script>
				<?php do_action( 'wpsc_get_edit_status_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_edit_status">
				<input type="hidden" name= "id" value= "<?php echo esc_attr( $status->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_status' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_edit_status(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_edit_status' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set edit status
		 */
		public static function set_edit_status() {

			global $wpdb;

			if ( check_ajax_referer( 'wpsc_set_edit_status', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
			if ( ! $name ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$color = isset( $_POST['color'] ) ? sanitize_text_field( wp_unslash( $_POST['color'] ) ) : '';
			if ( ! $color ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$bgcolor = isset( $_POST['bg-color'] ) ? sanitize_text_field( wp_unslash( $_POST['bg-color'] ) ) : '';
			if ( ! $bgcolor ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$status = new WPSC_Status( $id );
			if ( ! $status->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$status->name     = $name;
			$status->color    = $color;
			$status->bg_color = $bgcolor;
			$status->save();

			// set load order.
			$statuses_list = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			$load_after    = isset( $_POST['load-after'] ) ? sanitize_text_field( wp_unslash( $_POST['load-after'] ) ) : '__END__';

			if ( $load_after != '__END__' ) {

				$count = 1;

				if ( $load_after == '__TOP__' ) {
					$status->load_order = $count++;
					$status->save();
				}

				foreach ( $statuses_list as $cat ) {

					if ( $cat->id == $status->id ) {
						continue;
					}

					$cat->load_order = $count++;
					$cat->save();

					if ( $cat->id == $load_after ) {
						$status->load_order = $count++;
						$status->save();
					}
				}
			} else {

				$max_load_order     = (int) $wpdb->get_var( "SELECT max(load_order) FROM {$wpdb->prefix}psmsc_statuses" );
				$status->load_order = ++$max_load_order;
				$status->save();
			}

			wp_die();
		}

		/**
		 * Delete status modal
		 *
		 * @return void
		 */
		public static function get_delete_status() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			if ( check_ajax_referer( 'wpsc_get_delete_status', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$title = esc_attr__( 'Delete status', 'supportcandy' );

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$status = new WPSC_Status( $id );
			if ( ! $status->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$statuses = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-delete-status">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Replace with', 'supportcandy' ); ?></label>
					</div>
					<select name="replace_id">
						<?php
						foreach ( $statuses as $st ) {
							if ( $st->id == $status->id ) {
								continue;
							}
							?>
							<option value="<?php echo esc_attr( $st->id ); ?>"><?php echo esc_attr( $st->name ); ?></option>
							<?php
						}
						?>
					</select>
				</div>
				<input type="hidden" name="id" value="<?php echo esc_attr( $status->id ); ?>">
				<input type="hidden" name="action" value="wpsc_set_delete_status">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_delete_status' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_delete_status(this);">
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
		 * Delete status
		 */
		public static function set_delete_status() {

			global $wpdb;

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			if ( check_ajax_referer( 'wpsc_set_delete_status', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$status = new WPSC_Status( $id );
			if ( ! $status->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$replace_id = isset( $_POST['replace_id'] ) ? intval( $_POST['replace_id'] ) : 0;
			if ( ! $replace_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$replace = new WPSC_Status( $replace_id );
			if ( ! $replace->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$cf               = WPSC_Custom_Field::get_cf_by_slug( 'status' );
			$general_settings = get_option( 'wpsc-gs-general' );

			$statuses = array( 1, 2, 3, 4, $cf->default_value[0], $general_settings['ticket-status-after-customer-reply'], $general_settings['ticket-status-after-agent-reply'], $general_settings['close-ticket-status'] );
			$statuses = array_unique( $statuses );
			if ( in_array( $id, $statuses ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			// replace in ticket table.
			$success = $wpdb->update(
				$wpdb->prefix . 'psmsc_tickets',
				array( 'status' => $replace->id ),
				array( 'status' => $status->id )
			);

			// replace in logs.
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}psmsc_threads WHERE type='log' AND body RLIKE '^\{\"slug\":\"status\",.*[\"|:]" . $status->id . "[\"|}]'" );
			foreach ( $results as $log ) {
				$body       = json_decode( $log->body );
				$body->prev = $body->prev == $status->id ? $replace->id : $body->prev;
				$body->new  = $body->new == $status->id ? $replace->id : $body->new;
				$body       = wp_json_encode( $body );
				$wpdb->update(
					$wpdb->prefix . 'psmsc_threads',
					array( 'body' => $body ),
					array( 'id' => $log->id )
				);
			}

			$status->destroy( $status );
			wp_die();
		}
	}
endif;

WPSC_Ticket_Statuses::init();
