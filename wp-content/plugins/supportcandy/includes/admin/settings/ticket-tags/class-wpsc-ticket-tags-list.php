<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Ticket_Tags_List' ) ) :

	final class WPSC_Ticket_Tags_List {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// List.
			add_action( 'wp_ajax_wpsc_get_ticket_tags', array( __CLASS__, 'get_ticket_tags' ) );

			// Add new.
			add_action( 'wp_ajax_wpsc_get_add_new_tags', array( __CLASS__, 'get_add_new_tags' ) );
			add_action( 'wp_ajax_wpsc_set_add_ticket_tags', array( __CLASS__, 'set_add_ticket_tags' ) );

			// Edit.
			add_action( 'wp_ajax_wpsc_get_edit_ticket_tags', array( __CLASS__, 'get_edit_ticket_tags' ) );
			add_action( 'wp_ajax_wpsc_set_edit_ticket_tags', array( __CLASS__, 'set_edit_ticket_tags' ) );

			// Delete.
			add_action( 'wp_ajax_wpsc_set_delete_ticket_tags', array( __CLASS__, 'set_get_delete_ticket_tags' ) );
		}

		/**
		 * Load ticket tags list
		 */
		public static function get_ticket_tags() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$tags = WPSC_Ticket_Tags::find( array( 'items_per_page' => 0 ) )['results'];
			?>
			<div class="wpsc-dock-container">
				<?php
				printf(
				/* translators: Click here to see the documentation */
					esc_attr__( '%s to see the documentation!', 'supportcandy' ),
					'<a href="https://supportcandy.net/docs/tags/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
				);
				?>
			</div>
			<table class="wpsc-ticket-tags wpsc-setting-tbl">
				<thead>
					<tr>
						<th><?php esc_attr_e( 'Name', 'supportcandy' ); ?></th>
						<th><?php esc_attr_e( 'Description', 'supportcandy' ); ?></th>
						<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $tags as $tag ) {
						?>
						<tr>
							<td><span class="wpsc-tag" style="color: <?php echo esc_attr( $tag->color ); ?>; background-color: <?php echo esc_attr( $tag->bg_color ); ?>;" ><?php echo esc_attr( $tag->name ); ?></span></td>
							<td><?php echo strlen( $tag->description ) > 20 ? esc_attr( substr( $tag->description, 0, 20 ) . '...' ) : esc_attr( $tag->description ); ?></td>
							<td>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpsc-tickets&section=ticket-list&wpsc_tag=' . $tag->id ) ); ?>" class="wpsc-link" target="__blank"><?php echo esc_attr( wpsc__( 'View tickets', 'supportcandy' ) ); ?></a> | 
								<a href="javascript:wpsc_get_edit_ticket_tags(<?php echo esc_attr( $tag->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_ticket_tags' ) ); ?>');" class="wpsc-link"><?php echo esc_attr( wpsc__( 'Edit', 'supportcandy' ) ); ?></a> | 
								<a href="javascript:wpsc_set_delete_ticket_tags(<?php echo esc_attr( $tag->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_set_delete_ticket_tags' ) ); ?>');" class="wpsc-link"><?php echo esc_attr( wpsc__( 'Delete', 'supportcandy' ) ); ?></a>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<script>
				jQuery('table.wpsc-ticket-tags').DataTable({
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
								var data = { action: 'wpsc_get_add_new_tags' };
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
		 * Get add new tags
		 *
		 * @return void
		 */
		public static function get_add_new_tags() {

			$title = esc_attr( wpsc__( 'Add new', 'supportcandy' ) );
			$general = get_option( 'wpsc-ticket-tags-general-settings' );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-add-ms-ticket-tags">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php echo esc_attr( wpsc__( 'Name', 'supportcandy' ) ); ?></label>
						<span class="required-char">*</span>
					</div>
					<input name="name" type="text" autocomplete="off">
				</div>
				<div data-type="textfield" data-required="false" class="wpsc-input-group description">
					<div class="label-container">
						<label for="">
							<?php echo esc_attr( wpsc__( 'Description', 'wpsc-usergroup' ) ); ?>
						</label>
					</div>
					<input name="description" type="text" id="description" class="wpsc_textfield" autocomplete="off"/>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" name="color" value=<?php echo esc_attr( $general['color'] ); ?> />
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" name="bg-color" value=<?php echo esc_attr( $general['bg-color'] ); ?> />
				</div>
				<script>jQuery('.wpsc-color-picker').wpColorPicker();</script>
				<input type="hidden" name="action" value="wpsc_set_add_ticket_tags">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_ticket_tags' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_add_ticket_tags(this);">
				<?php echo esc_attr( wpsc__( 'Submit', 'supportcandy' ) ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php echo esc_attr( wpsc__( 'Cancel', 'supportcandy' ) ); ?>
			</button>
			<?php
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response, 200 );
		}

		/**
		 * Get set new tags
		 *
		 * @return void
		 */
		public static function set_add_ticket_tags() {

			$general = get_option( 'wpsc-ticket-tags-general-settings' );

			if ( check_ajax_referer( 'wpsc_set_add_ticket_tags', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorized request', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
			if ( ! $name ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$description = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';

			$color = isset( $_POST['color'] ) ? sanitize_text_field( wp_unslash( $_POST['color'] ) ) : $general['color'];
			if ( ! $color ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$bgcolor = isset( $_POST['bg-color'] ) ? sanitize_text_field( wp_unslash( $_POST['bg-color'] ) ) : $general['bg-color'];
			if ( ! $bgcolor ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$data = array(
				'name'        => $name,
				'description' => $description,
				'color'       => $color,
				'bg_color'    => $bgcolor,
			);

			WPSC_Ticket_Tags::insert( $data );

			wp_die();
		}

		/**
		 * Edit ticket tag modal
		 *
		 * @return void
		 */
		public static function get_edit_ticket_tags() {

			if ( check_ajax_referer( 'wpsc_get_edit_ticket_tags', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorized request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$title = esc_attr( wpsc__( 'Edit', 'supportcandy' ) );

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$tag = new WPSC_Ticket_Tags( $id );
			if ( ! $tag->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-ticket-tag">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php echo esc_attr( wpsc__( 'Name', 'supportcandy' ) ); ?></label>
						<span class="required-char">*</span>
					</div>
					<input name="name" type="text" value="<?php echo esc_attr( $tag->name ); ?>" autocomplete="off">
				</div>
				</div>
				<div data-type="textfield" data-required="false" class="wpsc-input-group description">
					<div class="label-container">
						<label for="">
							<?php echo esc_attr( wpsc__( 'Description', 'supportcandy' ) ); ?>
						</label>
					</div>
					<input name="description" type="text" id="description" class="wpsc_textfield" autocomplete="off"  value="<?php echo esc_attr( $tag->description ); ?>"/>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" name="color" value="<?php echo esc_attr( $tag->color ); ?>" />
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Background color', 'supportcandy' ); ?></label>
					</div>
					<input class="wpsc-color-picker" name="bg-color" value="<?php echo esc_attr( $tag->bg_color ); ?>" />
				</div>

				<script>jQuery('.wpsc-color-picker').wpColorPicker();</script>
				<?php do_action( 'wpsc_get_edit_ticket_tag_body', $id ); ?>
				<input type="hidden" name="id" value="<?php echo esc_attr( $tag->id ); ?>">
				<input type="hidden" name="action" value="wpsc_set_edit_ticket_tags">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_ticket_tags' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_edit_ticket_tags(this);">
				<?php echo esc_attr( wpsc__( 'Submit', 'supportcandy' ) ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php echo esc_attr( wpsc__( 'Cancel', 'supportcandy' ) ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_edit_ticket_tag_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response, 200 );
		}

		/**
		 * Set edit tag
		 *
		 * @return void
		 */
		public static function set_edit_ticket_tags() {

			if ( check_ajax_referer( 'wpsc_set_edit_ticket_tags', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorized request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$tag = new WPSC_Ticket_Tags( $id );
			if ( ! $tag->id ) {
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

			$description = isset( $_POST['description'] ) ? sanitize_textarea_field( wp_unslash( $_POST['description'] ) ) : '';

			$tag->name = $name;
			$tag->description = $description;
			$tag->color    = $color;
			$tag->bg_color = $bgcolor;
			$tag->save();
			wp_die();
		}

		/**
		 * Delete Tags
		 */
		public static function set_get_delete_ticket_tags() {

			if ( check_ajax_referer( 'wpsc_set_delete_ticket_tags', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorized request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$tag = new WPSC_Ticket_Tags( $id );
			if ( ! $tag->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}
			$tag->destroy( $tag );
			wp_die();
		}
	}
endif;

WPSC_Ticket_Tags_List::init();
