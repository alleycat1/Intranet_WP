<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Ticket_Categories' ) ) :

	final class WPSC_Ticket_Categories {

		/**
		 * Initialize this class
		 */
		public static function init() {

			// List.
			add_action( 'wp_ajax_wpsc_get_ticket_categories', array( __CLASS__, 'get_ticket_categories' ) );

			// Add new.
			add_action( 'wp_ajax_wpsc_get_add_new_category', array( __CLASS__, 'get_add_new_category' ) );
			add_action( 'wp_ajax_wpsc_set_add_category', array( __CLASS__, 'wpsc_set_add_category' ) );

			// Edit.
			add_action( 'wp_ajax_wpsc_get_edit_category', array( __CLASS__, 'get_edit_category' ) );
			add_action( 'wp_ajax_wpsc_set_edit_category', array( __CLASS__, 'set_edit_category' ) );

			// Delete.
			add_action( 'wp_ajax_wpsc_get_delete_category', array( __CLASS__, 'get_delete_category' ) );
			add_action( 'wp_ajax_wpsc_set_delete_category', array( __CLASS__, 'set_delete_category' ) );
		}

		/**
		 * Load ticket category list
		 */
		public static function get_ticket_categories() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$categories = WPSC_Category::find( array( 'items_per_page' => 0 ) )['results'];

			$cf = WPSC_Custom_Field::get_cf_by_slug( 'category' );?>

			<div class="wpsc-setting-header">
				<h2><?php esc_attr_e( 'Ticket Categories', 'supportcandy' ); ?></h2>
			</div>
			<div class="wpsc-setting-section-body">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/ticket-categories/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<table class="wpsc-ticket-categories wpsc-setting-tbl">
					<thead>
						<tr>
							<th><?php esc_attr_e( 'Name', 'supportcandy' ); ?></th>
							<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $categories as $category ) {
							?>
							<tr>
								<td><span class="title"><?php echo esc_attr( $category->name ); ?></span></td>
								<td>
									<div class="actions">
										<a class="wpsc-link" href="javascript:wpsc_get_edit_category(<?php echo esc_attr( $category->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_category' ) ); ?>');" ><?php echo esc_attr__( 'Edit', 'supportcandy' ); ?></a>
										<?php
										if ( $cf->default_value[0] != $category->id ) :
											?>
											| <a class="wpsc-link" href="javascript:wpsc_get_delete_category(<?php echo esc_attr( $category->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_delete_category' ) ); ?>');" ><?php echo esc_attr__( 'Delete', 'supportcandy' ); ?></a>
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
				jQuery('table.wpsc-ticket-categories').DataTable({
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
								var data = { action: 'wpsc_get_add_new_category' };
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
		 * Get add new category
		 *
		 * @return void
		 */
		public static function get_add_new_category() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$title = esc_attr__( 'Add new', 'supportcandy' );

			$categories = WPSC_Category::find( array( 'items_per_page' => 0 ) )['results'];
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-add-category">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Name', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<input name="label" type="text" autocomplete="off">
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
						foreach ( $categories as $category ) {
							?>
							<option value="<?php echo esc_attr( $category->id ); ?>"><?php echo esc_attr( $category->name ); ?></option>
							<?php
						}
						?>
						<option selected value="__END__">-- <?php esc_attr_e( 'END', 'supportcandy' ); ?> --</option>
					</select>
					<script>jQuery('select.load-after').selectWoo();</script>
				</div>

				<?php do_action( 'wpsc_get_add_category_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_add_category">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_category' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_add_category(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_add_category_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Insert new category
		 *
		 * @return void
		 */
		public static function wpsc_set_add_category() {

			if ( check_ajax_referer( 'wpsc_set_add_category', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $label ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$data     = array( 'name' => $label );
			$category = WPSC_Category::insert( $data );

			// set laod order.
			$categories = WPSC_Category::find( array( 'items_per_page' => 0 ) )['results'];
			$load_after = isset( $_POST['load-after'] ) ? sanitize_text_field( wp_unslash( $_POST['load-after'] ) ) : '__END__';

			if ( $load_after != '__END__' ) {

				$count = 1;

				if ( $load_after == '__TOP__' ) {
					$category->load_order = $count++;
					$category->save();
				}

				foreach ( $categories as $cat ) {

					if ( $cat->id == $category->id ) {
						continue;
					}

					$cat->load_order = $count++;
					$cat->save();

					if ( $cat->id == $load_after ) {
						$category->load_order = $count++;
						$category->save();
					}
				}
			}

			wp_die();
		}

		/**
		 * Edit category modal
		 *
		 * @return void
		 */
		public static function get_edit_category() {

			global $wpdb;

			if ( check_ajax_referer( 'wpsc_get_edit_category', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$category = new WPSC_Category( $id );
			if ( ! $category->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$title = esc_attr( $category->name );

			$categories = WPSC_Category::find( array( 'items_per_page' => 0 ) )['results'];
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-category">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Name', 'supportcandy' ); ?></label>
						<span class="required-char">*</span>
					</div>
					<input name="label" type="text" value="<?php echo esc_attr( $category->name ); ?>" autocomplete="off">
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
						$load_after = $wpdb->get_var( "SELECT id FROM {$wpdb->prefix}psmsc_categories WHERE load_order < {$category->load_order} ORDER BY load_order DESC LIMIT 1" );
						foreach ( $categories as $cat ) {
							if ( $cat->id == $category->id ) {
								continue;
							}
							?>
							<option <?php selected( $load_after, $cat->id ); ?> value="<?php echo esc_attr( $cat->id ); ?>"><?php echo esc_attr( $cat->name ); ?></option>
							<?php
						}
						?>
						<option value="__END__">-- <?php esc_attr_e( 'END', 'supportcandy' ); ?> --</option>
					</select>
					<script>jQuery('select.load-after').selectWoo();</script>
				</div>

				<?php do_action( 'wpsc_get_edit_category_body' ); ?>
				<input type="hidden" name="id" value="<?php echo esc_attr( $category->id ); ?>">
				<input type="hidden" name="action" value="wpsc_set_edit_category">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_category' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_edit_category(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_edit_category_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Set edit category
		 *
		 * @return void
		 */
		public static function set_edit_category() {

			global $wpdb;

			if ( check_ajax_referer( 'wpsc_set_edit_category', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $label ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$category = new WPSC_Category( $id );
			if ( ! $category->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$category->name = $label;
			$category->save();

			// set laod order.
			$categories = WPSC_Category::find( array( 'items_per_page' => 0 ) )['results'];
			$load_after = isset( $_POST['load-after'] ) ? sanitize_text_field( wp_unslash( $_POST['load-after'] ) ) : '__END__';

			if ( $load_after != '__END__' ) {

				$count = 1;

				if ( $load_after == '__TOP__' ) {
					$category->load_order = $count++;
					$category->save();
				}

				foreach ( $categories as $cat ) {

					if ( $cat->id == $category->id ) {
						continue;
					}

					$cat->load_order = $count++;
					$cat->save();

					if ( $cat->id == $load_after ) {
						$category->load_order = $count++;
						$category->save();
					}
				}
			} else {

				$max_load_order       = (int) $wpdb->get_var( "SELECT max(load_order) FROM {$wpdb->prefix}psmsc_categories" );
				$category->load_order = ++$max_load_order;
				$category->save();
			}

			wp_die();
		}

		/**
		 * Delete category modal
		 *
		 * @return void
		 */
		public static function get_delete_category() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			if ( check_ajax_referer( 'wpsc_get_delete_category', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$title = esc_attr__( 'Delete category', 'supportcandy' );

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$category = new WPSC_Category( $id );
			if ( ! $category->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$categories = WPSC_Category::find( array( 'items_per_page' => 0 ) )['results'];
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-delete-category">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Replace with', 'supportcandy' ); ?></label>
					</div>
					<select name="replace_id">
						<?php
						foreach ( $categories as $cat ) {
							if ( $cat->id == $category->id ) {
								continue;
							}
							?>
							<option value="<?php echo esc_attr( $cat->id ); ?>"><?php echo esc_attr( $cat->name ); ?></option>
							<?php
						}
						?>
					</select>
				</div>
				<input type="hidden" name="id" value="<?php echo esc_attr( $category->id ); ?>">
				<input type="hidden" name="action" value="wpsc_set_delete_category">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_delete_category' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_delete_category(this);">
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
		 * Delete Category
		 */
		public static function set_delete_category() {

			global $wpdb;

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			if ( check_ajax_referer( 'wpsc_set_delete_category', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$category = new WPSC_Category( $id );
			if ( ! $category->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$replace_id = isset( $_POST['replace_id'] ) ? intval( $_POST['replace_id'] ) : 0;
			if ( ! $replace_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$replace = new WPSC_Category( $replace_id );
			if ( ! $replace->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$cf = WPSC_Custom_Field::get_cf_by_slug( 'category' );
			if ( $id == $cf->default_value[0] ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			// replace in ticket table.
			$success = $wpdb->update(
				$wpdb->prefix . 'psmsc_tickets',
				array( 'category' => $replace->id ),
				array( 'category' => $category->id )
			);

			// replace in logs.
			$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}psmsc_threads WHERE type='log' AND body RLIKE '^\{\"slug\":\"category\",.*[\"|:]" . $category->id . "[\"|}]'" );
			foreach ( $results as $log ) {
				$body       = json_decode( $log->body );
				$body->prev = $body->prev == $category->id ? $replace->id : $body->prev;
				$body->new  = $body->new == $category->id ? $replace->id : $body->new;
				$body       = wp_json_encode( $body );
				$wpdb->update(
					$wpdb->prefix . 'psmsc_threads',
					array( 'body' => $body ),
					array( 'id' => $log->id )
				);
			}

			$category->destroy( $category );

			wp_die();
		}
	}
endif;

WPSC_Ticket_Categories::init();
