<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_TFF' ) ) :

	final class WPSC_TFF {

		/**
		 * Ticket form fields
		 *
		 * @var array
		 */
		private static $tff = array();

		/**
		 * Initialize this class
		 */
		public static function init() {

			// List.
			add_action( 'wp_ajax_wpsc_get_tff', array( __CLASS__, 'get_tff' ) );

			// Add new.
			add_action( 'wp_ajax_wpsc_get_add_new_tff', array( __CLASS__, 'get_add_new_tff' ) );
			add_action( 'wp_ajax_wpsc_set_add_new_tff', array( __CLASS__, 'set_add_new_tff' ) );

			// Edit.
			add_action( 'wp_ajax_wpsc_get_edit_tff', array( __CLASS__, 'get_edit_tff' ) );
			add_action( 'wp_ajax_wpsc_set_edit_tff', array( __CLASS__, 'set_edit_tff' ) );

			// Delete.
			add_action( 'wp_ajax_wpsc_delete_tff', array( __CLASS__, 'delete_tff' ) );

			// Remove if custom field deleted.
			add_action( 'wpsc_delete_custom_field', array( __CLASS__, 'delete_custom_field' ), 10, 1 );

			// visibility conditions filter.
			add_filter( 'wpsc_visibility_conditions', array( __CLASS__, 'visibility_conditions' ) );
		}

		/**
		 * Get ticket form fields
		 *
		 * @return void
		 */
		public static function get_tff() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$tff = get_option( 'wpsc-tff', array() );
			?>

			<div class="wpsc-setting-header">
				<h2><?php esc_attr_e( 'Ticket Form Fields', 'supportcandy' ); ?></h2>
			</div>
			<div class="wpsc-setting-section-body">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/ticket-form-fields/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<table class="form-fields wpsc-setting-tbl">
					<thead>
						<tr>
							<th><?php esc_attr_e( 'Field', 'supportcandy' ); ?></th>
							<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $tff as $slug => $settings ) :
							$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
							if ( ! $cf ) {
								continue;
							}
							?>
							<tr>
								<td><?php echo esc_attr( $cf->name ); ?></td>
								<td>
									<a href="javascript:wpsc_get_edit_tff(<?php echo esc_attr( $cf->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_tff' ) ); ?>');" class="wpsc-link"><?php esc_attr_e( 'Edit', 'supportcandy' ); ?></a>
									<?php
									if ( ! ( $cf->slug == 'name' || $cf->slug == 'email' ) ) :
										echo esc_attr( ' | ' );
										?>
										<a href="javascript:wpsc_delete_tff(<?php echo esc_attr( $cf->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_delete_tff' ) ); ?>');" class="wpsc-link"><?php esc_attr_e( 'Delete', 'supportcandy' ); ?></a>
										<?php
									endif;
									?>
								</td>
							</tr>
							<?php
						endforeach;
						?>
					</tbody>
				</table>
			</div>
			<script>
				jQuery('table.form-fields').DataTable({
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
								var data = { action: 'wpsc_get_add_new_tff' };
								jQuery.post(
									supportcandy.ajax_url,
									data,
									function (response) {
										jQuery( '.wpsc-modal-header' ).text( response.title );
										jQuery( '.wpsc-modal-body' ).html( response.body );
										jQuery( '.wpsc-modal-footer' ).html( response.footer );
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
		 * Add new ticket form field modal popup UI
		 *
		 * @return void
		 */
		public static function get_add_new_tff() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$title         = esc_attr__( 'Add new field', 'supportcandy' );
			$custom_fields = WPSC_Custom_Field::$custom_fields;
			$tff           = get_option( 'wpsc-tff', array() );
			$unique_id     = uniqid( 'wpsc_' );

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="frm-add-new-ticket-form-field">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Select field', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<select id="wpsc-select-ticket-form-field" name="id" onchange="<?php echo esc_attr( $unique_id ); ?>(this)">
						<option value=""></option>
						<?php
						foreach ( $custom_fields as $cf ) {

							if (
								class_exists( $cf->type ) &&
								in_array( $cf->field, WPSC_CF_Settings::$allowed_modules['ticket-form'] ) &&
								$cf->type::$is_ctf &&
								! isset( $tff[ $cf->slug ] )
							) {
								if ( $cf->field == 'customer' && ! $cf->allow_ticket_form ) {
									continue;
								}
								?>
								<option class="field_<?php echo esc_attr( $cf->id ); ?>" data-fieldtype="<?php echo esc_attr( $cf->type ); ?>" data-slug="<?php echo esc_attr( $cf->slug ); ?>" value="<?php echo esc_attr( $cf->id ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
								<?php
							}
						}
						?>
					</select>
					<script>
						jQuery('#wpsc-select-ticket-form-field').selectWoo({
							allowClear: true,
							placeholder: ""
						});
						function <?php echo esc_attr( $unique_id ); ?>(el) {

							var id = jQuery(el).val();
							var option = jQuery('option.field_'+id);
							var slug = option.data('slug');
							<?php
							do_action( 'wpsc_jse_add_tff_change_field', $unique_id );
							?>
						}
						jQuery(document).ready(function() {	   
							jQuery('#wpsc-select-ticket-form-field').change(function() {
								var slug = jQuery('option:selected', this).data('fieldtype');
								if(slug =='WPSC_CF_HTML'){			 
									jQuery('#wpsc-create-ticket-required').hide();
								}
							});
						});
					</script>
				</div>
				<div class="wpsc-input-group" id="wpsc-create-ticket-required">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Is required?', 'supportcandy' ); ?></label>
					</div>
					<select name="is-required">
						<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Width', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-select-ticket-form-field-width" name="width">
						<option value="1/3"><?php esc_attr_e( '1/3rd of row', 'supportcandy' ); ?></option>
						<option value="half"><?php esc_attr_e( 'Half-width of row', 'supportcandy' ); ?></option>
						<option value="full"><?php esc_attr_e( 'Full-width of row', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Load after', 'supportcandy' ); ?></label>
					</div>
					<select name="load-after" class="load-after">
						<option value="__TOP__">-- <?php esc_attr_e( 'TOP', 'supportcandy' ); ?> --</option>
						<?php
						foreach ( $tff as $slug => $settings ) {
							$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
							if ( ! $cf ) {
								continue;
							}
							?>
							<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_attr( $cf->name ); ?></option>
							<?php
						}
						?>
						<option value="__END__" selected>-- <?php esc_attr_e( 'END', 'supportcandy' ); ?> --</option>
					</select>
					<script>jQuery('select.load-after').selectWoo();</script>
				</div>
				<?php do_action( 'wpsc_get_add_new_tff', $unique_id ); ?>
				<?php WPSC_Ticket_Conditions::print( 'visibility', 'wpsc_visibility_conditions', '', false, __( 'Visibility conditions', 'supportcandy' ) ); ?>
				<input type="hidden" name="action" value="wpsc_set_add_new_tff">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_new_tff' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_add_new_tff(this);">
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
		 * Insert form field
		 *
		 * @return void
		 */
		public static function set_add_new_tff() {

			if ( check_ajax_referer( 'wpsc_set_add_new_tff', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$tff = get_option( 'wpsc-tff', array() );

			$cf = new WPSC_Custom_Field( $id );
			if (
				! $cf->id ||
				! $cf->type::$is_ctf ||
				! in_array( $cf->field, WPSC_CF_Settings::$allowed_modules['ticket-form'] ) ||
				isset( $tff[ $cf->slug ] )
			) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$is_required = isset( $_POST['is-required'] ) ? intval( $_POST['is-required'] ) : 1;
			$width       = isset( $_POST['width'] ) ? sanitize_text_field( wp_unslash( $_POST['width'] ) ) : 'full';
			$load_after  = isset( $_POST['load-after'] ) ? sanitize_text_field( wp_unslash( $_POST['load-after'] ) ) : '__END__';

			$visibility = isset( $_POST['visibility'] ) ? sanitize_text_field( wp_unslash( $_POST['visibility'] ) ) : '';
			if ( ! WPSC_Ticket_Conditions::is_valid_input_conditions( 'wpsc_visibility_conditions', $visibility ) ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$field = apply_filters(
				'wpsc_tff_add_new',
				array(
					'is-required' => $is_required,
					'width'       => $width,
					'visibility'  => $visibility,
				),
				$cf
			);

			switch ( $load_after ) {

				case '__TOP__':
					$tff = array_merge( array( $cf->slug => $field ), $tff );
					break;

				case '__END__':
					$tff = array_merge( $tff, array( $cf->slug => $field ) );
					break;

				default:
					$load_after = WPSC_Custom_Field::get_cf_by_slug( $load_after );
					if ( ! $load_after || ! isset( $tff[ $load_after->slug ] ) ) {
						wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
					}

					if ( count( $tff ) == 0 ) {

						$tff = array_merge( $tff, array( $cf->slug => $field ) );

					} else {

						$offset = array_search( $load_after->slug, array_keys( $tff ) ) + 1;
						$arr1   = array_slice( $tff, 0, $offset );
						$arr2   = array_slice( $tff, $offset );
						$tff    = array_merge( $arr1, array( $cf->slug => $field ), $arr2 );
					}
					break;
			}

			update_option( 'wpsc-tff', $tff );
			wp_die();
		}

		/**
		 * Edit ticket form field modal
		 *
		 * @return void
		 */
		public static function get_edit_tff() {

			if ( check_ajax_referer( 'wpsc_get_edit_tff', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$tff = get_option( 'wpsc-tff', array() );
			$cf  = new WPSC_Custom_Field( $id );

			if (
				! $cf->id ||
				! $cf->type::$is_ctf ||
				! isset( $tff[ $cf->slug ] )
			) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$field     = $tff[ $cf->slug ];
			$title     = $cf->name;
			$unique_id = uniqid( 'wpsc_' );

			// calculate load order.
			$tff_keys   = array_keys( $tff );
			$offset     = array_search( $cf->slug, $tff_keys );
			$load_after = $offset == 0 ? '__TOP__' : $tff_keys[ $offset - 1 ];

			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="frm-edit-ticket-form-field">

				<?php
				if ( ! ( $cf->slug == 'name' || $cf->slug == 'email' ) ) :
					?>
					<div class="wpsc-input-group" style="<?php echo esc_attr( $cf->type ) === 'WPSC_CF_HTML' ? 'display:none;' : ''; ?>" >
						<div class="label-container">
							<label for=""><?php esc_attr_e( 'Is required?', 'supportcandy' ); ?></label>
						</div>
						<select name="is-required">
							<option <?php selected( $field['is-required'], 1 ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
							<option <?php selected( $field['is-required'], 0 ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						</select>
					</div>
					<?php
				endif;
				?>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Width', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-select-ticket-form-field-width" name="width">
						<option <?php selected( $field['width'], '1/3' ); ?> value="1/3"><?php esc_attr_e( '1/3rd of row', 'supportcandy' ); ?></option>
						<option <?php selected( $field['width'], 'half' ); ?> value="half"><?php esc_attr_e( 'Half-width of row', 'supportcandy' ); ?></option>
						<option <?php selected( $field['width'], 'full' ); ?> value="full"><?php esc_attr_e( 'Full-width of row', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Load after', 'supportcandy' ); ?></label>
					</div>
					<select name="load-after" class="load-after">
						<option <?php selected( $load_after, '__TOP__', true ); ?> value="__TOP__">-- <?php esc_attr_e( 'TOP', 'supportcandy' ); ?> --</option>
						<?php
						foreach ( $tff as $slug => $settings ) {
							$cff = WPSC_Custom_Field::get_cf_by_slug( $slug );
							if ( ! $cff || $cff == $cf ) {
								continue;
							}
							?>
							<option <?php selected( $load_after, $cff->slug, true ); ?> value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_attr( $cff->name ); ?></option>
							<?php
						}
						?>
						<option value="__END__">-- <?php esc_attr_e( 'END', 'supportcandy' ); ?> --</option>
					</select>
					<script>jQuery('select.load-after').selectWoo();</script>
				</div>
				<?php
				do_action( 'wpsc_get_edit_tff', $field, $cf );

				if ( ! ( $cf->slug == 'name' || $cf->slug == 'email' ) ) {
					WPSC_Ticket_Conditions::print( 'visibility', 'wpsc_visibility_conditions', $field['visibility'], false, __( 'Visibility conditions', 'supportcandy' ) );
				}
				?>
				<input type="hidden" name="id" value="<?php echo esc_attr( $cf->id ); ?>">
				<input type="hidden" name="action" value="wpsc_set_edit_tff">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_tff' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_edit_tff(this);">
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
		 * Set edit data
		 *
		 * @return void
		 */
		public static function set_edit_tff() {

			if ( check_ajax_referer( 'wpsc_set_edit_tff', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$tff        = get_option( 'wpsc-tff', array() );
			$cf         = new WPSC_Custom_Field( $id );
			$load_after = isset( $_POST['load-after'] ) ? sanitize_text_field( wp_unslash( $_POST['load-after'] ) ) : '__END__';

			if (
				! $cf->id ||
				! $cf->type::$is_ctf ||
				! isset( $tff[ $cf->slug ] )
			) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$visibility = isset( $_POST['visibility'] ) ? sanitize_text_field( wp_unslash( $_POST['visibility'] ) ) : '';
			if ( ! WPSC_Ticket_Conditions::is_valid_input_conditions( 'wpsc_visibility_conditions', $visibility ) ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			// unset from tff so that load after should work.
			unset( $tff[ $cf->slug ] );

			$field = apply_filters(
				'wpsc_set_edit_tff',
				array(
					'is-required' => isset( $_POST['is-required'] ) ? intval( $_POST['is-required'] ) : 1,
					'width'       => isset( $_POST['width'] ) ? sanitize_text_field( wp_unslash( $_POST['width'] ) ) : 'full',
					'visibility'  => $visibility,
				),
				$cf
			);

			// set load after.
			switch ( $load_after ) {

				case '__TOP__':
					$tff = array_merge( array( $cf->slug => $field ), $tff );
					break;

				case '__END__':
					$tff = array_merge( $tff, array( $cf->slug => $field ) );
					break;

				default:
					$load_after = WPSC_Custom_Field::get_cf_by_slug( $load_after );
					if ( ! $load_after || ! isset( $tff[ $load_after->slug ] ) ) {
						wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
					}
					$offset = array_search( $load_after->slug, array_keys( $tff ) ) + 1;
					$arr1   = array_slice( $tff, 0, $offset );
					$arr2   = array_slice( $tff, $offset );
					$tff    = array_merge( $arr1, array( $cf->slug => $field ), $arr2 );
					break;
			}

			update_option( 'wpsc-tff', $tff );
			wp_die();
		}

		/**
		 * Delete form field
		 *
		 * @return void
		 */
		public static function delete_tff() {

			if ( check_ajax_referer( 'wpsc_delete_tff', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$tff = get_option( 'wpsc-tff', array() );

			$cf = new WPSC_Custom_Field( $id );
			if ( ! $cf->id || ! $cf->type::$is_ctf || ! in_array( $cf->field, array( 'customer', 'ticket' ) ) || ! isset( $tff[ $cf->slug ] ) ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			unset( $tff[ $cf->slug ] );
			update_option( 'wpsc-tff', $tff );
			wp_die();
		}

		/**
		 * Return JSON object for tff
		 *
		 * @param WPSC_TFF $tff - ticket form field.
		 * @param boolean  $is_array - is array.
		 * @return array
		 */
		public static function get_visibility( $tff, $is_array = false ) {

			$visibility = $tff['visibility'] ? html_entity_decode( $tff['visibility'] ) : '';
			if ( $is_array ) {
				return $visibility ? json_decode( $visibility, true ) : array();
			} else {
				return $visibility ? json_decode( $visibility ) : new stdClass();
			}
		}

		/**
		 * Remove tff if related custom field got deleted
		 *
		 * @param WPSC_CF $cf - tff custom field.
		 * @return void
		 */
		public static function delete_custom_field( $cf ) {

			$tff = get_option( 'wpsc-tff', array() );
			if ( array_key_exists( $cf->slug, $tff ) ) {
				unset( $tff[ $cf->slug ] );
				update_option( 'wpsc-tff', $tff );
			}
		}

		/**
		 * Filter conditions for visibility
		 *
		 * @param array $conditions - conditions to filter.
		 * @return array
		 */
		public static function visibility_conditions( $conditions ) {

			foreach ( $conditions as $slug => $item ) {

				if ( $item['type'] == 'cf' ) {

					$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
					if (
						! $cf->type::$is_visibility_conditions ||
						! in_array( $cf->field, array( 'ticket', 'customer' ) )
					) {
						unset( $conditions[ $slug ] );
					}
				} else { // not custom field type.

					unset( $conditions[ $slug ] );
				}
			}

			return $conditions;
		}
	}
endif;

WPSC_TFF::init();
