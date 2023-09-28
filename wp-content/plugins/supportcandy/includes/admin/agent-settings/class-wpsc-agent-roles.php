<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Agent_Roles' ) ) :

	final class WPSC_Agent_Roles {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Get agent roles ajax register.
			add_action( 'wp_ajax_wpsc_get_agent_roles', array( __CLASS__, 'get_agent_roles' ) );

			// Add agent role.
			add_action( 'wp_ajax_wpsc_get_add_agent_role', array( __CLASS__, 'get_add_agent_role' ) );
			add_action( 'wp_ajax_wpsc_set_add_agent_role', array( __CLASS__, 'set_add_agent_role' ) );

			// clone agent role.
			add_action( 'wp_ajax_wpsc_get_clone_agent_role', array( __CLASS__, 'get_clone_agent_role' ) );

			// Edit agent role.
			add_action( 'wp_ajax_wpsc_get_edit_agent_role', array( __CLASS__, 'get_edit_agent_role' ) );
			add_action( 'wp_ajax_wpsc_set_edit_agent_role', array( __CLASS__, 'set_edit_agent_role' ) );

			// Delete agent role.
			add_action( 'wp_ajax_wpsc_delete_agent_role', array( __CLASS__, 'delete_agent_role' ) );
		}

		/**
		 * Get agent roles ajax callback function
		 *
		 * @return void
		 */
		public static function get_agent_roles() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$roles = get_option( 'wpsc-agent-roles', array() );?>

			<div class="wpsc-setting-header">
				<h2><?php esc_attr_e( 'Agent Roles', 'supportcandy' ); ?></h2>
			</div>
			<div class="wpsc-setting-section-body">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/add-new-agent-role/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-setting-cards-container">
					<div style="width: 100%;">
						<table class="agent-role-list-table wpsc-setting-tbl">
							<thead>
								<tr>
									<th><?php esc_attr_e( 'Role', 'supportcandy' ); ?></th>
									<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ( $roles ) :
									foreach ( $roles as $key => $role ) :
										?>
										<tr>
											<td> <span class="title"><?php echo esc_attr( $role['label'] ); ?></span> </td>
											<td>
												<a href="javascript:wpsc_get_edit_agent_role(<?php echo esc_attr( $key ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_agent_role' ) ); ?>');"><?php esc_attr_e( 'Edit', 'supportcandy' ); ?></a> |
												<a href="javascript:wpsc_get_clone_agent_role(<?php echo esc_attr( $key ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_clone_agent_role' ) ); ?>');"><?php esc_attr_e( 'Clone', 'supportcandy' ); ?></a> 
												<?php
												if ( intval( $key ) > 2 ) :
													?>
													| <a href="javascript:wpsc_delete_agent_role(<?php echo esc_attr( $key ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_delete_agent_role' ) ); ?>');"><?php esc_attr_e( 'Delete', 'supportcandy' ); ?></a>
													<?php
												endif
												?>
											</td>
										</tr>
										<?php
									endforeach;
								endif;
								?>
							</tbody>
						</table>
						<script>
							jQuery('table.agent-role-list-table').DataTable({
								order: [[1, "asc"]],
								pageLength: 20,
								bLengthChange: false,
								columnDefs: [ 
									{ targets: [0,-1], orderable: false },
									{ targets: -1, searchable: false },
									{ targets: '_all', className: 'dt-left' }
								],
								dom: 'Bfrtip',
								buttons: [
									{
										text: '<?php esc_attr_e( 'Add new', 'supportcandy' ); ?>',
										className: 'wpsc-button small primary',
										action: function ( e, dt, node, config ) {
											jQuery( '.wpsc-setting-section-body' ).html( supportcandy.loader_html );
											var data = { action: 'wpsc_get_add_agent_role' , _ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_add_agent_role' ) ); ?>' };
											jQuery.post(
												supportcandy.ajax_url,
												data,
												function (response) {
													jQuery( '.wpsc-setting-section-body' ).html( response );
												}
											);
										}
									}
								],
								language: supportcandy.translations.datatables
							});
						</script>
					</div>
				</div>
			</div>
			<?php
			wp_die();
		}

		/**
		 * Add agent role UI
		 *
		 * @return void
		 */
		public static function get_add_agent_role() {

			if ( check_ajax_referer( 'wpsc_get_add_agent_role', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$title = esc_attr__( 'Add new', 'supportcandy' );

			?>
			<form action="#" onsubmit="return false;" class="frm-add-agent-role">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Label', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<input id="label" type="text" name="label" autocomplete="off">
				</div>

				<table id="wpsc_add_agent_role">
					<thead>
						<tr>
							<th><?php esc_attr_e( 'Capability', 'supportcandy' ); ?></th>
							<th style="padding: 10px;"><input type="checkbox" id="wpsc-all-una"><?php esc_attr_e( 'Unassigned', 'supportcandy' ); ?></th>
							<th style="padding: 10px;"><input type="checkbox" id="wpsc-all-ame"><?php esc_attr_e( 'Assigned to me', 'supportcandy' ); ?></th>
							<th style="padding: 10px;"><input type="checkbox" id="wpsc-all-ao"><?php esc_attr_e( 'Assigned to others', 'supportcandy' ); ?></th>
						</tr>
					</thead>

					<tbody>
						<tr>
							<td><label for=""><?php esc_attr_e( 'View Tickets', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="view-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="view-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="view-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Reply Tickets', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="reply-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="reply-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="reply-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Private Notes', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="pn-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="pn-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="pn-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Duplicate Ticket', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="dt-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="dt-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="dt-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Assign Agents', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="aa-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="aa-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="aa-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Change Status', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="cs-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="cs-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="cs-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Change Ticket Fields', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="ctf-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="ctf-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="ctf-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Change Agent Only Fields', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="caof-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="caof-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="caof-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Change Raised By', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="crb-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="crb-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="crb-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Modify Additional Recipients', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="ar-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="ar-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="ar-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Edit Threads', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="eth-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="eth-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="eth-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Delete Threads', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="dth-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="dth-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="dth-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'View Logs', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="vl-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="vl-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="vl-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Delete Ticket', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="dtt-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="dtt-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="dtt-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Modify Tags', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" value="tt-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" value="tt-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" value="tt-assigned-others" class="wpsc-ao"></td>
						</tr>

						<?php do_action( 'wpsc_add_agent_role_ticket_permissions' ); ?>
					</tbody>
				</table>

				<div class="wpsc-input-group" style="margin-top: 20px;">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Other permissions', 'supportcandy' ); ?></label>
					</div>
					<div class="checkbox-group">
						<div>
							<input name="caps[]" type="checkbox" value="backend-access">
							<span><?php esc_attr_e( 'WP dashboard access', 'supportcandy' ); ?></span>
						</div>
						<div>
							<input name="caps[]" type="checkbox" value="create-as">
							<span><?php esc_attr_e( 'Create ticket on others behalf', 'supportcandy' ); ?></span>
						</div>
						<div>
							<input name="caps[]" type="checkbox" value="dtt-access">
							<span><?php esc_attr_e( 'Deleted filter access', 'supportcandy' ); ?></span>
						</div>
						<div>
							<input name="caps[]" type="checkbox" value="eci-access">
							<span><?php esc_attr_e( 'Edit customer info', 'supportcandy' ); ?></span>
						</div>
						<?php do_action( 'wpsc_add_agent_role_other_permissions' ); ?>
					</div>
				</div>
				<input type="hidden" name="action" value="wpsc_set_add_agent_role">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_agent_role' ) ); ?>">
			</form>

			<button class="wpsc-button small primary" onclick="wpsc_set_add_agent_role(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_get_agent_roles();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>

			<script>
				// check all unassigned action.
				jQuery('#wpsc-all-una').change(function () {
					if( jQuery(this).is(':checked') ) {
						jQuery(".wpsc-una").prop('checked','checked');
					} else {
						jQuery(".wpsc-una").removeAttr('checked');
					}
				});
				jQuery(".wpsc-una").change(function() {
					if( jQuery('.wpsc-una:checked').length == jQuery('.wpsc-una').length ) {
						jQuery('#wpsc-all-una').prop('checked','checked');
					}else {
						jQuery('#wpsc-all-una').removeAttr('checked');
					}
				})

				// check all assigned me action.
				jQuery('#wpsc-all-ame').change(function () {
					if( jQuery(this).is(':checked') ) {
						jQuery(".wpsc-ame").prop('checked','checked');
					} else {
						jQuery(".wpsc-ame").removeAttr('checked');
					}
				});
				jQuery(".wpsc-ame").change(function() {
					if( jQuery('.wpsc-ame:checked').length == jQuery('.wpsc-ame').length ) {
						jQuery('#wpsc-all-ame').prop('checked','checked');
					}else {
						jQuery('#wpsc-all-ame').removeAttr('checked');
					}
				})

				// check all assigned to other action.
				jQuery('#wpsc-all-ao').change(function () {
					if( jQuery(this).is(':checked') ) {
						jQuery(".wpsc-ao").prop('checked','checked');
					} else {
						jQuery(".wpsc-ao").removeAttr('checked');
					}
				});
				jQuery(".wpsc-ao").change(function() {
					if( jQuery('.wpsc-ao:checked').length == jQuery('.wpsc-ao').length ) {
						jQuery('#wpsc-all-ao').prop('checked','checked');
					}else {
						jQuery('#wpsc-all-ao').removeAttr('checked');
					}
				})

				jQuery('#wpsc_add_agent_role').DataTable({
					ordering: false,
					pageLength: 20,
					bLengthChange: false,
					columnDefs: [ 
						{ targets: -1, searchable: false },
						{ targets: '_all', className: 'dt-left' }
					],
					dom: 'Bfrtip',
					language: supportcandy.translations.datatables,
					bPaginate: false,
					bInfo: false,
				});
			</script>	
			<?php
			wp_die();
		}

		/**
		 * Set add agent role
		 *
		 * @return void
		 */
		public static function set_add_agent_role() {

			if ( check_ajax_referer( 'wpsc_set_add_agent_role', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $label ) {
				wp_die( 'Label not available' );
			}

			$caps = isset( $_POST['caps'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['caps'] ) ) ) : array();
			if ( ! $caps ) {
				wp_die( 'caps not found' );
			}

			$roles = get_option( 'wpsc-agent-roles' );

			$roles[] = apply_filters(
				'wpsc_set_add_agent_role',
				array(
					'label' => $label,
					'caps'  => array(
						'backend-access'        => in_array( 'backend-access', $caps ) ? true : false,
						'create-as'             => in_array( 'create-as', $caps ) ? true : false,
						'view-unassigned'       => in_array( 'view-unassigned', $caps ) ? true : false,
						'view-assigned-me'      => in_array( 'view-assigned-me', $caps ) ? true : false,
						'view-assigned-others'  => in_array( 'view-assigned-others', $caps ) ? true : false,
						'reply-unassigned'      => in_array( 'reply-unassigned', $caps ) ? true : false,
						'reply-assigned-me'     => in_array( 'reply-assigned-me', $caps ) ? true : false,
						'reply-assigned-others' => in_array( 'reply-assigned-others', $caps ) ? true : false,
						'pn-unassigned'         => in_array( 'pn-unassigned', $caps ) ? true : false,
						'pn-assigned-me'        => in_array( 'pn-assigned-me', $caps ) ? true : false,
						'pn-assigned-others'    => in_array( 'pn-assigned-others', $caps ) ? true : false,
						'aa-unassigned'         => in_array( 'aa-unassigned', $caps ) ? true : false,
						'aa-assigned-me'        => in_array( 'aa-assigned-me', $caps ) ? true : false,
						'aa-assigned-others'    => in_array( 'aa-assigned-others', $caps ) ? true : false,
						'cs-unassigned'         => in_array( 'cs-unassigned', $caps ) ? true : false,
						'cs-assigned-me'        => in_array( 'cs-assigned-me', $caps ) ? true : false,
						'cs-assigned-others'    => in_array( 'cs-assigned-others', $caps ) ? true : false,
						'ctf-unassigned'        => in_array( 'ctf-unassigned', $caps ) ? true : false,
						'ctf-assigned-me'       => in_array( 'ctf-assigned-me', $caps ) ? true : false,
						'ctf-assigned-others'   => in_array( 'ctf-assigned-others', $caps ) ? true : false,
						'caof-unassigned'       => in_array( 'caof-unassigned', $caps ) ? true : false,
						'caof-assigned-me'      => in_array( 'caof-assigned-me', $caps ) ? true : false,
						'caof-assigned-others'  => in_array( 'caof-assigned-others', $caps ) ? true : false,
						'crb-unassigned'        => in_array( 'crb-unassigned', $caps ) ? true : false,
						'crb-assigned-me'       => in_array( 'crb-assigned-me', $caps ) ? true : false,
						'crb-assigned-others'   => in_array( 'crb-assigned-others', $caps ) ? true : false,
						'eth-unassigned'        => in_array( 'eth-unassigned', $caps ) ? true : false,
						'eth-assigned-me'       => in_array( 'eth-assigned-me', $caps ) ? true : false,
						'eth-assigned-others'   => in_array( 'eth-assigned-others', $caps ) ? true : false,
						'dth-unassigned'        => in_array( 'dth-unassigned', $caps ) ? true : false,
						'dth-assigned-me'       => in_array( 'dth-assigned-me', $caps ) ? true : false,
						'dth-assigned-others'   => in_array( 'dth-assigned-others', $caps ) ? true : false,
						'vl-unassigned'         => in_array( 'vl-unassigned', $caps ) ? true : false,
						'vl-assigned-me'        => in_array( 'vl-assigned-me', $caps ) ? true : false,
						'vl-assigned-others'    => in_array( 'vl-assigned-others', $caps ) ? true : false,
						'dtt-unassigned'        => in_array( 'dtt-unassigned', $caps ) ? true : false,
						'dtt-assigned-me'       => in_array( 'dtt-assigned-me', $caps ) ? true : false,
						'dtt-assigned-others'   => in_array( 'dtt-assigned-others', $caps ) ? true : false,
						'dtt-access'            => in_array( 'dtt-access', $caps ) ? true : false,
						'dt-unassigned'         => in_array( 'dt-unassigned', $caps ) ? true : false,
						'dt-assigned-me'        => in_array( 'dt-assigned-me', $caps ) ? true : false,
						'dt-assigned-others'    => in_array( 'dt-assigned-others', $caps ) ? true : false,
						'ar-unassigned'         => in_array( 'ar-unassigned', $caps ) ? true : false,
						'ar-assigned-me'        => in_array( 'ar-assigned-me', $caps ) ? true : false,
						'ar-assigned-others'    => in_array( 'ar-assigned-others', $caps ) ? true : false,
						'eci-access'            => in_array( 'eci-access', $caps ) ? true : false,
						'tt-unassigned'         => in_array( 'tt-unassigned', $caps ) ? true : false,
						'tt-assigned-me'        => in_array( 'tt-assigned-me', $caps ) ? true : false,
						'tt-assigned-others'    => in_array( 'tt-assigned-others', $caps ) ? true : false,

					),
				),
				$caps
			);

			update_option( 'wpsc-agent-roles', $roles );

			do_action( 'wpsc_after_add_agent_role', array_key_last( $roles ) );
			wp_die();
		}

		/**
		 * Clone existing agent role.
		 *
		 * @return void
		 */
		public static function get_clone_agent_role() {

			if ( check_ajax_referer( 'wpsc_get_clone_agent_role', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorized request!', 400 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( 'Incorrect request!', 400 );
			}

			$roles = get_option( 'wpsc-agent-roles', array() );
			$roles[] = array(
				'label' => $roles[ $id ]['label'] . ' clone',
				'caps'  => $roles[ $id ]['caps'],
			);

			update_option( 'wpsc-agent-roles', $roles );

			wp_die();
		}

		/**
		 * Edit agent role UI
		 *
		 * @return void
		 */
		public static function get_edit_agent_role() {

			if ( check_ajax_referer( 'wpsc_get_edit_agent_role', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$role_id = isset( $_POST['role_id'] ) ? intval( $_POST['role_id'] ) : 0;
			if ( ! $role_id ) {
				wp_die( 'role_id not found!' );
			}

			$roles = get_option( 'wpsc-agent-roles', array() );
			$role  = isset( $roles[ $role_id ] ) ? $roles[ $role_id ] : array();
			if ( ! $role ) {
				wp_die( 'Role not available!' );
			}

			?>
			<form action="#" onsubmit="return false;" class="frm-edit-agent-role">

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Label', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<input id="label" type="text" name="label" autocomplete="off" value="<?php echo esc_attr( $role['label'] ); ?>">
				</div>

				<table id="wpsc_edit_agent_role">
					<thead>
						<tr>
							<th><?php esc_attr_e( 'Capability', 'supportcandy' ); ?></th>
							<th style="padding: 10px;"><input type="checkbox" id="wpsc-all-una"><?php esc_attr_e( 'Unassigned', 'supportcandy' ); ?></th>
							<th style="padding: 10px;"><input type="checkbox" id="wpsc-all-ame"><?php esc_attr_e( 'Assigned to me', 'supportcandy' ); ?></th>
							<th style="padding: 10px;"><input type="checkbox" id="wpsc-all-ao"><?php esc_attr_e( 'Assigned to others', 'supportcandy' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><label for=""><?php esc_attr_e( 'View Tickets', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['view-unassigned'], 1 ); ?> value="view-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['view-assigned-me'], 1 ); ?> value="view-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['view-assigned-others'], 1 ); ?> value="view-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Reply Tickets', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['reply-unassigned'], 1 ); ?> value="reply-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['reply-assigned-me'], 1 ); ?> value="reply-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['reply-assigned-others'], 1 ); ?> value="reply-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Private Notes', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['pn-unassigned'], 1 ); ?> value="pn-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['pn-assigned-me'], 1 ); ?> value="pn-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['pn-assigned-others'], 1 ); ?> value="pn-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Duplicate Ticket', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['dt-unassigned'], 1 ); ?> value="dt-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['dt-assigned-me'], 1 ); ?> value="dt-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['dt-assigned-others'], 1 ); ?> value="dt-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Assign Agents', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['aa-unassigned'], 1 ); ?> value="aa-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['aa-assigned-me'], 1 ); ?> value="aa-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['aa-assigned-others'], 1 ); ?> value="aa-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Change Status', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['cs-unassigned'], 1 ); ?> value="cs-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['cs-assigned-me'], 1 ); ?> value="cs-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['cs-assigned-others'], 1 ); ?> value="cs-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Change Ticket Fields', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['ctf-unassigned'], 1 ); ?> value="ctf-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['ctf-assigned-me'], 1 ); ?> value="ctf-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['ctf-assigned-others'], 1 ); ?> value="ctf-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Change Agent Only Fields', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['caof-unassigned'], 1 ); ?> value="caof-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['caof-assigned-me'], 1 ); ?> value="caof-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['caof-assigned-others'], 1 ); ?> value="caof-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Change Raised By', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['crb-unassigned'], 1 ); ?> value="crb-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['crb-assigned-me'], 1 ); ?> value="crb-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['crb-assigned-others'], 1 ); ?> value="crb-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Modify Additional Recipients', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['ar-unassigned'], 1 ); ?> value="ar-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['ar-assigned-me'], 1 ); ?> value="ar-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['ar-assigned-others'], 1 ); ?> value="ar-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Edit Threads', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['eth-unassigned'], 1 ); ?> value="eth-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['eth-assigned-me'], 1 ); ?> value="eth-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['eth-assigned-others'], 1 ); ?> value="eth-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Delete Threads', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['dth-unassigned'], 1 ); ?> value="dth-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['dth-assigned-me'], 1 ); ?> value="dth-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['dth-assigned-others'], 1 ); ?> value="dth-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'View Logs', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['vl-unassigned'], 1 ); ?> value="vl-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['vl-assigned-me'], 1 ); ?> value="vl-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['vl-assigned-others'], 1 ); ?> value="vl-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Delete Ticket', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['dtt-unassigned'], 1 ); ?> value="dtt-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['dtt-assigned-me'], 1 ); ?> value="dtt-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['dtt-assigned-others'], 1 ); ?> value="dtt-assigned-others" class="wpsc-ao"></td>
						</tr>

						<tr>
							<td><label for=""><?php esc_attr_e( 'Modify Tags', 'supportcandy' ); ?></label></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['tt-unassigned'], 1 ); ?> value="tt-unassigned" class="wpsc-una"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['tt-assigned-me'], 1 ); ?> value="tt-assigned-me" class="wpsc-ame"></td>
							<td><input name="caps[]" type="checkbox" <?php checked( $role['caps']['tt-assigned-others'], 1 ); ?> value="tt-assigned-others" class="wpsc-ao"></td>
						</tr>
						<?php do_action( 'wpsc_edit_agent_role_ticket_permissions', $role ); ?>
					</tbody>
				</table>
				<div class="wpsc-input-group" style="margin-top: 20px;">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Other permissions', 'supportcandy' ); ?></label>
					</div>
					<div class="checkbox-group">
						<div>
							<input name="caps[]" type="checkbox" <?php checked( $role['caps']['backend-access'], 1 ); ?> value="backend-access">
							<span><?php esc_attr_e( 'WP dashboard access', 'supportcandy' ); ?></span>
						</div>
						<div>
							<input name="caps[]" type="checkbox" <?php checked( $role['caps']['create-as'], 1 ); ?> value="create-as">
							<span><?php esc_attr_e( 'Create ticket on others behalf', 'supportcandy' ); ?></span>
						</div>
						<div>
							<input name="caps[]" type="checkbox" <?php checked( $role['caps']['dtt-access'], 1 ); ?> value="dtt-access">
							<span><?php esc_attr_e( 'Deleted filter access', 'supportcandy' ); ?></span>
						</div>
						<div>
							<input name="caps[]" type="checkbox" <?php checked( $role['caps']['eci-access'], 1 ); ?> value="eci-access">
							<span><?php esc_attr_e( 'Edit customer info', 'supportcandy' ); ?></span>
						</div>
						<?php do_action( 'wpsc_edit_agent_role_other_permissions', $role ); ?>
					</div>
				</div>
				<input type="hidden" name="role_id" value="<?php echo esc_attr( $role_id ); ?>">
				<input type="hidden" name="action" value="wpsc_set_edit_agent_role">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_agent_role' ) ); ?>">
			</form>
			<button class="wpsc-button small primary" onclick="wpsc_set_edit_agent_role();">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_get_agent_roles();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>

			<script>
				// check all unassigned action.
				jQuery('#wpsc-all-una').change(function () {
					if( jQuery(this).is(':checked') ) {
						jQuery(".wpsc-una").prop('checked','checked');
					} else {
						jQuery(".wpsc-una").removeAttr('checked');
					}
				});
				jQuery(".wpsc-una").change(function() {
					if( jQuery('.wpsc-una:checked').length == jQuery('.wpsc-una').length ) {
						jQuery('#wpsc-all-una').prop('checked','checked');
					}else {
						jQuery('#wpsc-all-una').removeAttr('checked');
					}
				});
				jQuery(".wpsc-una").trigger("change");

				// check all assigned me action.
				jQuery('#wpsc-all-ame').change(function () {
					if( jQuery(this).is(':checked') ) {
						jQuery(".wpsc-ame").prop('checked','checked');
					} else {
						jQuery(".wpsc-ame").removeAttr('checked');
					}
				});
				jQuery(".wpsc-ame").change(function() {
					if( jQuery('.wpsc-ame:checked').length == jQuery('.wpsc-ame').length ) {
						jQuery('#wpsc-all-ame').prop('checked','checked');
					}else {
						jQuery('#wpsc-all-ame').removeAttr('checked');
					}
				});
				jQuery(".wpsc-ame").trigger('change');

				// check all assigned to other action.
				jQuery('#wpsc-all-ao').change(function () {
					if( jQuery(this).is(':checked') ) {
						jQuery(".wpsc-ao").prop('checked','checked');
					} else {
						jQuery(".wpsc-ao").removeAttr('checked');
					}
				});
				jQuery(".wpsc-ao").change(function() {
					if( jQuery('.wpsc-ao:checked').length == jQuery('.wpsc-ao').length ) {
						jQuery('#wpsc-all-ao').prop('checked','checked');
					}else {
						jQuery('#wpsc-all-ao').removeAttr('checked');
					}
				});
				jQuery(".wpsc-ao").trigger('change');

				jQuery('#wpsc_edit_agent_role').DataTable({
					ordering: false,
					pageLength: 20,
					bLengthChange: false,
					columnDefs: [ 
						{ targets: -1, searchable: false },
						{ targets: '_all', className: 'dt-left' }
					],
					dom: 'Bfrtip',
					language: supportcandy.translations.datatables,
					bPaginate: false,
					bInfo: false,
				});
			</script>

			<?php
			wp_die();
		}

		/**
		 * Set edit agent role
		 *
		 * @return void
		 */
		public static function set_edit_agent_role() {

			if ( check_ajax_referer( 'wpsc_set_edit_agent_role', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$role_id = isset( $_POST['role_id'] ) ? intval( $_POST['role_id'] ) : 0;
			if ( ! $role_id ) {
				wp_die( 'role_id not found!' );
			}

			$roles = get_option( 'wpsc-agent-roles', array() );
			$role  = isset( $roles[ $role_id ] ) ? $roles[ $role_id ] : array();
			if ( ! $role ) {
				wp_die( 'Role not available!' );
			}

			$label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $label ) {
				wp_die( 'Label not available' );
			}

			$caps = isset( $_POST['caps'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['caps'] ) ) ) : array();
			if ( ! $caps ) {
				wp_die( 'caps not found' );
			}

			$roles[ $role_id ] = apply_filters(
				'wpsc_set_edit_agent_role',
				array(
					'label' => $label,
					'caps'  => array(
						'backend-access'        => in_array( 'backend-access', $caps ) ? true : false,
						'create-as'             => in_array( 'create-as', $caps ) ? true : false,
						'view-unassigned'       => in_array( 'view-unassigned', $caps ) ? true : false,
						'view-assigned-me'      => in_array( 'view-assigned-me', $caps ) ? true : false,
						'view-assigned-others'  => in_array( 'view-assigned-others', $caps ) ? true : false,
						'reply-unassigned'      => in_array( 'reply-unassigned', $caps ) ? true : false,
						'reply-assigned-me'     => in_array( 'reply-assigned-me', $caps ) ? true : false,
						'reply-assigned-others' => in_array( 'reply-assigned-others', $caps ) ? true : false,
						'pn-unassigned'         => in_array( 'pn-unassigned', $caps ) ? true : false,
						'pn-assigned-me'        => in_array( 'pn-assigned-me', $caps ) ? true : false,
						'pn-assigned-others'    => in_array( 'pn-assigned-others', $caps ) ? true : false,
						'aa-unassigned'         => in_array( 'aa-unassigned', $caps ) ? true : false,
						'aa-assigned-me'        => in_array( 'aa-assigned-me', $caps ) ? true : false,
						'aa-assigned-others'    => in_array( 'aa-assigned-others', $caps ) ? true : false,
						'cs-unassigned'         => in_array( 'cs-unassigned', $caps ) ? true : false,
						'cs-assigned-me'        => in_array( 'cs-assigned-me', $caps ) ? true : false,
						'cs-assigned-others'    => in_array( 'cs-assigned-others', $caps ) ? true : false,
						'ctf-unassigned'        => in_array( 'ctf-unassigned', $caps ) ? true : false,
						'ctf-assigned-me'       => in_array( 'ctf-assigned-me', $caps ) ? true : false,
						'ctf-assigned-others'   => in_array( 'ctf-assigned-others', $caps ) ? true : false,
						'caof-unassigned'       => in_array( 'caof-unassigned', $caps ) ? true : false,
						'caof-assigned-me'      => in_array( 'caof-assigned-me', $caps ) ? true : false,
						'caof-assigned-others'  => in_array( 'caof-assigned-others', $caps ) ? true : false,
						'crb-unassigned'        => in_array( 'crb-unassigned', $caps ) ? true : false,
						'crb-assigned-me'       => in_array( 'crb-assigned-me', $caps ) ? true : false,
						'crb-assigned-others'   => in_array( 'crb-assigned-others', $caps ) ? true : false,
						'eth-unassigned'        => in_array( 'eth-unassigned', $caps ) ? true : false,
						'eth-assigned-me'       => in_array( 'eth-assigned-me', $caps ) ? true : false,
						'eth-assigned-others'   => in_array( 'eth-assigned-others', $caps ) ? true : false,
						'dth-unassigned'        => in_array( 'dth-unassigned', $caps ) ? true : false,
						'dth-assigned-me'       => in_array( 'dth-assigned-me', $caps ) ? true : false,
						'dth-assigned-others'   => in_array( 'dth-assigned-others', $caps ) ? true : false,
						'vl-unassigned'         => in_array( 'vl-unassigned', $caps ) ? true : false,
						'vl-assigned-me'        => in_array( 'vl-assigned-me', $caps ) ? true : false,
						'vl-assigned-others'    => in_array( 'vl-assigned-others', $caps ) ? true : false,
						'dtt-unassigned'        => in_array( 'dtt-unassigned', $caps ) ? true : false,
						'dtt-assigned-me'       => in_array( 'dtt-assigned-me', $caps ) ? true : false,
						'dtt-assigned-others'   => in_array( 'dtt-assigned-others', $caps ) ? true : false,
						'dtt-access'            => in_array( 'dtt-access', $caps ) ? true : false,
						'dt-unassigned'         => in_array( 'dt-unassigned', $caps ) ? true : false,
						'dt-assigned-me'        => in_array( 'dt-assigned-me', $caps ) ? true : false,
						'dt-assigned-others'    => in_array( 'dt-assigned-others', $caps ) ? true : false,
						'ar-unassigned'         => in_array( 'ar-unassigned', $caps ) ? true : false,
						'ar-assigned-me'        => in_array( 'ar-assigned-me', $caps ) ? true : false,
						'ar-assigned-others'    => in_array( 'ar-assigned-others', $caps ) ? true : false,
						'eci-access'            => in_array( 'eci-access', $caps ) ? true : false,
						'tt-unassigned'         => in_array( 'tt-unassigned', $caps ) ? true : false,
						'tt-assigned-me'        => in_array( 'tt-assigned-me', $caps ) ? true : false,
						'tt-assigned-others'    => in_array( 'tt-assigned-others', $caps ) ? true : false,
					),
				),
				$role,
				$caps
			);

			// Update backend access user capabilities.
			if ( $role['caps']['backend-access'] !== $roles[ $role_id ]['caps']['backend-access'] ) {

				$response = WPSC_Agent::find(
					array(
						'items_per_page' => 0,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'slug'    => 'role',
								'compare' => '=',
								'val'     => $role_id,
							),
							array(
								'slug'    => 'is_active',
								'compare' => '=',
								'val'     => 1,
							),
						),
					)
				);
				foreach ( $response['results'] as $agent ) {

					if ( $roles[ $role_id ]['caps']['backend-access'] && ! $agent->user->has_cap( 'wpsc_agent' ) ) {
						$agent->user->add_cap( 'wpsc_agent' );
					}

					if ( ! $roles[ $role_id ]['caps']['backend-access'] && $agent->user->has_cap( 'wpsc_agent' ) ) {
						$agent->user->remove_cap( 'wpsc_agent' );
					}
				}
			}

			update_option( 'wpsc-agent-roles', $roles );
			do_action( 'wpsc_agent_role_update', $role_id );
			wp_die();
		}

		/**
		 * Delete agent role
		 */
		public static function delete_agent_role() {

			if ( check_ajax_referer( 'wpsc_delete_agent_role', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$role_id = isset( $_POST['role_id'] ) ? intval( $_POST['role_id'] ) : 0;
			if ( ! $role_id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$roles = get_option( 'wpsc-agent-roles', array() );
			$role  = isset( $roles[ $role_id ] ) ? $roles[ $role_id ] : array();
			if ( ! $role ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			if ( $role_id <= 2 ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$args   = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'slug'    => 'role',
						'compare' => '=',
						'val'     => $role_id,
					),
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => 1,
					),
				),
			);
			$agents = WPSC_Agent::find( $args );
			if ( $agents['total_items'] ) {
				wp_send_json_error( 'The role can not be deleted if existing agents are associated with it!', 400 );
			}

			unset( $roles[ $role_id ] );
			do_action( 'wpsc_after_delete_agent_role', $role_id );
			update_option( 'wpsc-agent-roles', $roles );

			wp_die();
		}
	}
endif;

WPSC_Agent_Roles::init();
