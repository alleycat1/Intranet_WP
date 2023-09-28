<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Ticket_Priorities' ) ) :

	final class WPSC_Ticket_Priorities {

		/**
		 * Initialize this class
		 */
		public static function init() {

			// List.
			add_action( 'wp_ajax_wpsc_get_ticket_priorities', array( __CLASS__, 'get_priorities' ) );

			// Add new.
			add_action( 'wp_ajax_wpsc_get_add_new_priority', array( __CLASS__, 'get_add_new_priority' ) );
			add_action( 'wp_ajax_wpsc_set_add_priority', array( __CLASS__, 'set_add_priority' ) );

			// Edit.
			add_action( 'wp_ajax_wpsc_get_edit_priority', array( __CLASS__, 'get_edit_priority' ) );
			add_action( 'wp_ajax_wpsc_set_edit_priority', array( __CLASS__, 'set_edit_priority' ) );

			// Delete.
			add_action( 'wp_ajax_wpsc_get_delete_priority', array( __CLASS__, 'get_delete_priority' ) );
			add_action( 'wp_ajax_wpsc_set_delete_priority', array( __CLASS__, 'set_delete_priority' ) );
		}

		/**
		 * Get priority settings
		 *
		 * @return void
		 */
		public static function get_priorities() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$priorities = WPSC_Priority::find( array( 'items_per_page' => 0 ) )['results'];
			$cf         = WPSC_Custom_Field::get_cf_by_slug( 'priority' );?>

			<div class="wpsc-setting-header">
				<h2><?php esc_attr_e( 'Ticket Priorities', 'supportcandy' ); ?></h2>
			</div>
			<div class="wpsc-setting-section-body">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/ticket-priorities/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<table class="wpsc-ticket-priorities wpsc-setting-tbl">
					<thead>
						<tr>
							<th><?php esc_attr_e( 'Name', 'supportcandy' ); ?></th>
							<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $priorities  as  $priority ) {
							?>
							<tr>
								<td><span class="wpsc-tag" style="color: <?php echo esc_attr( $priority->color ); ?>; background-color: <?php echo esc_attr( $priority->bg_color ); ?>;" ><?php echo esc_attr( $priority->name ); ?></span></td>
								<td>
									<div class="actions">
										<a class="wpsc-link" href="javascript:wpsc_get_edit_priority(<?php echo esc_attr( $priority->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_priority' ) ); ?>');" ><?php echo esc_attr__( 'Edit', 'supportcandy' ); ?></a>
										<?php
										if ( $cf->default_value[0] != $priority->id ) :
											?>
											| <a class="wpsc-link" href="javascript:wpsc_get_delete_priority(<?php echo esc_attr( $priority->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_delete_priority' ) ); ?>');" ><?php echo esc_attr__( 'Delete', 'supportcandy' ); ?></a>
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
				jQuery('table.wpsc-ticket-priorities').DataTable({
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
								var data = { action: 'wpsc_get_add_new_priority' };
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
		 * Get add new priority
		 */
		public static function get_add_new_priority() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$title = esc_attr__( 'Add new', 'supportcandy' );

			$priorities = WPSC_Priority::find( array( 'items_per_page' => 0 ) )['results'];
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-add-priority">
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
						foreach ( $priorities as $priority ) {
							?>
							<option value="<?php echo esc_attr( $priority->id ); ?>"><?php echo esc_attr( $priority->name ); ?></option>
							<?php
						}
						?>
						<option selected value="__END__">-- <?php esc_attr_e( 'END', 'supportcandy' ); ?> --</option>
					</select>
					<script>jQuery('select.load-after').selectWoo();</script>
				</div>
				<script>jQuery('.wpsc-color-picker').wpColorPicker();</script>
				<?php do_action( 'wpsc_get_add_priority_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_add_priority">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_priority' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_add_priority(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_add_priority' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set add priority
		 */
		public static function set_add_priority() {

			if ( check_ajax_referer( 'wpsc_set_add_priority', '_ajax_nonce', false ) != 1 ) {
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

			$data     = array(
				'name'     => $name,
				'color'    => $color,
				'bg_color' => $bgcolor,
			);
			$priority = WPSC_Priority::insert( $data );

			// set laod order.
			$priorities = WPSC_Priority::find( array( 'items_per_page' => 0 ) )['results'];
			$load_after = isset( $_POST['load-after'] ) ? sanitize_text_field( wp_unslash( $_POST['load-after'] ) ) : '__END__';

			if ( $load_after != '__END__' ) {

				$count = 1;

				if ( $load_after == '__TOP__' ) {
					$priority->load_order = $count++;
					$priority->save();
				}

				foreach ( $priorities as $prio ) {

					if ( $prio->id == $priority->id ) {
						continue;
					}

					$prio->load_order = $count++;
					$prio->save();

					if ( $prio->id == $load_after ) {
						$priority->load_order = $count++;
						$priority->save();
					}
				}
			}

			wp_die();
		}

		/**
		 *  Get edit priority
		 */
		public static function get_edit_priority() {

			global $wpdb;

			if ( check_ajax_referer( 'wpsc_get_edit_priority', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$priority = new WPSC_Priority( $id );
			if ( ! $priority->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$title = esc_attr( $priority->name );

			$priorities = WPSC_Priority::find( array( 'items_per_page' => 0 ) )['results'];
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-priority">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Name', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<input name="name" type="text" value="<?php echo esc_attr( $priority->name ); ?>" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" name="color" value="<?php echo esc_attr( $priority->color ); ?>" />
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" name="bg-color" value="<?php echo esc_attr( $priority->bg_color ); ?>" />
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
						$load_after = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_priorities WHERE load_order < {$priority->load_order} ORDER BY load_order DESC LIMIT 1" );
						foreach ( $priorities as $prio ) {
							if ( $prio->id == $priority->id ) {
								continue;
							}
							?>
							<option <?php selected( $load_after, $prio->id ); ?> value="<?php echo esc_attr( $prio->id ); ?>"><?php echo esc_attr( $prio->name ); ?></option>
							<?php
						}
						?>
						<option value="__END__">-- <?php esc_attr_e( 'END', 'supportcandy' ); ?> --</option>
					</select>
					<script>jQuery('select.load-after').selectWoo();</script>
				</div>
				<script>jQuery('.wpsc-color-picker').wpColorPicker();</script>
				<?php do_action( 'wpsc_get_edit_priority_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_edit_priority">
				<input type="hidden" name= "id" value="<?php echo esc_attr( $id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_priority' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_edit_priority(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_edit_priority' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set edit priority
		 */
		public static function set_edit_priority() {

			global $wpdb;

			if ( check_ajax_referer( 'wpsc_set_edit_priority', '_ajax_nonce', false ) != 1 ) {
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

			$priority = new WPSC_Priority( $id );
			if ( ! $priority->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$priority->name     = $name;
			$priority->color    = $color;
			$priority->bg_color = $bgcolor;
			$priority->save();

			// set laod order.
			$priorities = WPSC_Priority::find( array( 'items_per_page' => 0 ) )['results'];
			$load_after = isset( $_POST['load-after'] ) ? sanitize_text_field( wp_unslash( $_POST['load-after'] ) ) : '__END__';

			if ( $load_after != '__END__' ) {

				$count = 1;

				if ( $load_after == '__TOP__' ) {
					$priority->load_order = $count++;
					$priority->save();
				}

				foreach ( $priorities as $cat ) {

					if ( $cat->id == $priority->id ) {
						continue;
					}

					$cat->load_order = $count++;
					$cat->save();

					if ( $cat->id == $load_after ) {
						$priority->load_order = $count++;
						$priority->save();
					}
				}
			} else {

				$max_load_order       = (int) $wpdb->get_var( "SELECT max(load_order) FROM {$wpdb->prefix}psmsc_priorities" );
				$priority->load_order = ++$max_load_order;
				$priority->save();
			}

			wp_die();
		}

		/**
		 * Delete priority modal
		 *
		 * @return void
		 */
		public static function get_delete_priority() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			if ( check_ajax_referer( 'wpsc_get_delete_priority', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$title = esc_attr__( 'Delete priority', 'supportcandy' );

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$priority = new WPSC_Priority( $id );
			if ( ! $priority->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$priorities = WPSC_Priority::find( array( 'items_per_page' => 0 ) )['results'];
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-delete-priority">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Replace with', 'supportcandy' ); ?></label>
					</div>
					<select name="replace_id">
						<?php
						foreach ( $priorities as $prt ) {
							if ( $prt->id == $priority->id ) {
								continue;
							}
							?>
								<option value="<?php echo esc_attr( $prt->id ); ?>"><?php echo esc_attr( $prt->name ); ?></option>
							<?php
						}
						?>
					</select>
				</div>
				<input type="hidden" name="id" value="<?php echo esc_attr( $priority->id ); ?>">
				<input type="hidden" name="action" value="wpsc_set_delete_priority">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_delete_priority' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_delete_priority(this);">
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
		 * Delete priority
		 */
		public static function set_delete_priority() {

			global $wpdb;

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			if ( check_ajax_referer( 'wpsc_set_delete_priority', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$priority = new WPSC_Priority( $id );
			if ( ! $priority->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$replace_id = isset( $_POST['replace_id'] ) ? intval( $_POST['replace_id'] ) : 0;
			if ( ! $replace_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$replace = new WPSC_Priority( $replace_id );
			if ( ! $replace->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$cf = WPSC_Custom_Field::get_cf_by_slug( 'priority' );
			if ( $id == $cf->default_value[0] || $id == '1' ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			// replace in ticket table.
			$success = $wpdb->update(
				$wpdb->prefix . 'psmsc_tickets',
				array( 'priority' => $replace->id ),
				array( 'priority' => $priority->id )
			);

			// replace in logs.
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}psmsc_threads WHERE type='log' AND body RLIKE '^\{\"slug\":\"priority\",.*[\"|:]" . $priority->id . "[\"|}]'" );
			foreach ( $results as $log ) {
				$body       = json_decode( $log->body );
				$body->prev = $body->prev == $priority->id ? $replace->id : $body->prev;
				$body->new  = $body->new == $priority->id ? $replace->id : $body->new;
				$body       = wp_json_encode( $body );
				$wpdb->update(
					$wpdb->prefix . 'psmsc_threads',
					array( 'body' => $body ),
					array( 'id' => $log->id )
				);
			}

			$priority->destroy( $priority );

			wp_die();
		}
	}
endif;

WPSC_Ticket_Priorities::init();
