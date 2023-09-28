<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Agent_Settings' ) ) :

	final class WPSC_Agent_Settings {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Get agent list ajax register.
			add_action( 'wp_ajax_wpsc_get_agent_list', array( __CLASS__, 'get_agent_list' ) );

			// Get add agent modal.
			add_action( 'wp_ajax_wpsc_get_add_agent', array( __CLASS__, 'get_add_agent' ) );
			add_action( 'wp_ajax_wpsc_set_add_agent', array( __CLASS__, 'set_add_agent' ) );

			// Get edit agent modal.
			add_action( 'wp_ajax_wpsc_get_edit_agent', array( __CLASS__, 'get_edit_agent' ) );
			add_action( 'wp_ajax_wpsc_set_edit_agent', array( __CLASS__, 'set_edit_agent' ) );

			// Get delete agent modal.
			add_action( 'wp_ajax_wpsc_delete_agent', array( __CLASS__, 'delete_agent' ) );

			// WP user autocomplete.
			add_action( 'wp_ajax_wpsc_search_wp_users', array( __CLASS__, 'search_wp_users' ) );

			// WP User ADD/Delete.
			add_action( 'delete_user', array( __CLASS__, 'wp_user_delete' ), 10, 3 );
			add_action( 'user_register', array( __CLASS__, 'wp_user_register' ), 11, 2 );
		}

		/**
		 * Get agent list ajax callback function
		 *
		 * @return void
		 */
		public static function get_agent_list() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$args      = array(
				'items_per_page' => 0,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => 1,
					),
					array(
						'slug'    => 'is_agentgroup',
						'compare' => '=',
						'val'     => 0,
					),
				),
			);
			$agents    = WPSC_Agent::find( $args )['results'];
			$roles     = get_option( 'wpsc-agent-roles', array() );
			$unique_id = uniqid( 'wpsc_' );?>

			<div class="wpsc-setting-header">
				<h2><?php esc_attr_e( 'Agents', 'supportcandy' ); ?></h2>
			</div>

			<div class="wpsc-setting-section-body">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/add-new-agent/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-setting-cards-container">
					<div style="width: 100%;">
						<table class="agent-role-table wpsc-setting-tbl">
							<thead>
								<tr>
									<th><?php esc_attr_e( 'Agent name', 'supportcandy' ); ?></th>
									<th><?php esc_attr_e( 'Agent Email', 'supportcandy' ); ?></th>
									<th><?php esc_attr_e( 'Role', 'supportcandy' ); ?></th>
									<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								if ( $agents ) :
									foreach ( $agents as $agent ) {
										?>
										<tr>
											<td><?php echo esc_attr( $agent->name ); ?></td>
											<td><?php echo esc_attr( $agent->customer->email ); ?></td>
											<td><?php echo esc_attr( $roles[ $agent->role ]['label'] ); ?></td>
											<td>
												<a href="javascript:wpsc_get_edit_agent(<?php echo esc_attr( $agent->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_agent' ) ); ?>')"><?php esc_attr_e( 'Edit', 'supportcandy' ); ?></a> | 
												<a href="javascript:wpsc_get_delete_agent(<?php echo esc_attr( $agent->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_delete_agent' ) ); ?>' )"><?php esc_attr_e( 'Delete', 'supportcandy' ); ?></a>
											</td>
										</tr>
										<?php
									}
								endif;
								?>
							</tbody>
						</table>
						<script>
							jQuery('table.agent-role-table').DataTable({
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
											var data = { action: 'wpsc_get_add_agent' };
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
					</div>
				</div>
			</div>
			<?php
			wp_die();
		}

		/**
		 * Add agent modal popup UI
		 *
		 * @return void
		 */
		public static function get_add_agent() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$title = esc_attr__( 'Add new', 'supportcandy' );
			$roles = get_option( 'wpsc-agent-roles', array() );
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-add-agent">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Select users', 'supportcandy' ); ?></label>
					</div>
					<select id="wpsc-select-user-input" name="users[]" multiple></select>
					<script>
						jQuery('#wpsc-select-user-input').selectWoo({
							ajax: {
								url: supportcandy.ajax_url,
								dataType: 'json',
								delay: 250,
								data: function (params) {
									return {
										q: params.term, // search term.
										page: params.page,
										action: 'wpsc_search_wp_users',
										_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_search_wp_users' ) ); ?>',
									};
								},
								processResults: function (data, params) {
									var terms = [];
									if ( data ) {
										jQuery.each( data, function( id, text ) {
											terms.push( { id: text.id, text: text.title+' ('+text.email+')' } );
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
							allowClear: false,
							placeholder: ""
						});
					</script>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Select role', 'supportcandy' ); ?></label>
					</div>
					<select name="role">
					<?php
					foreach ( $roles as $key => $role ) {
						?>
							<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $role['label'] ); ?></option>
						<?php
					}
					?>
					</select>
				</div>
				<?php do_action( 'wpsc_get_add_agent_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_add_agent">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_add_agent' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_add_agent(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_add_agent_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Create an agent
		 *
		 * @return void
		 */
		public static function set_add_agent() {

			if ( check_ajax_referer( 'wpsc_set_add_agent', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$users = isset( $_POST['users'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['users'] ) ) ) : array();
			if ( ! $users ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$roles = get_option( 'wpsc-agent-roles', array() );

			$role = isset( $_POST['role'] ) ? intval( $_POST['role'] ) : 0;
			if ( ! $role || ! isset( $roles[ $role ] ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			foreach ( $users as $user_id ) {

				$user = new WP_User( $user_id );
				if ( ! $user->ID ) {
					continue;
				}

				$agent = WPSC_Agent::get_by_user_id( $user->ID );

				// skip if agent is already active.
				if ( $agent->id && $agent->is_active ) {
					continue;
				}

				if ( $agent->id ) {

					$agent->is_active = 1;
					$agent->role      = $role;
					$agent->save();
					$agent->user->add_cap( 'wpsc_agent' );

				} elseif ( ! $agent->id ) {

					$agent = self::insert_new_record( $user, $role );
				}

				// reset agent counts.
				$agent->reset_workload();
				$agent->reset_unresolved_count();
			}

			do_action( 'after_set_add_agent', $agent );

			wp_die();
		}

		/**
		 * Edit agent modal popup UI
		 *
		 * @return void
		 */
		public static function get_edit_agent() {

			if ( check_ajax_referer( 'wpsc_get_edit_agent', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Unauthorized access', 'supportcandy' ), 401 );
			}

			$agent = new WPSC_Agent( $id );
			if ( ! $agent->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$title = $agent->name;
			$roles = get_option( 'wpsc-agent-roles', array() );
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-agent">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Role', 'supportcandy' ); ?></label>
					</div>
					<select name="role">
					<?php
					foreach ( $roles as $key => $role ) {
						?>
							<option <?php selected( $key, $agent->role ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $role['label'] ); ?></option>
						<?php
					}
					?>
					</select>
				</div>
				<?php do_action( 'wpsc_get_edit_agent_body', $agent ); ?>
				<input type="hidden" name="action" value="wpsc_set_edit_agent">
				<input type="hidden" name="id" value="<?php echo esc_attr( $agent->id ); ?>">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_agent' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_edit_agent(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_edit_agent_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Update an agent
		 */
		public static function set_edit_agent() {

			if ( check_ajax_referer( 'wpsc_set_edit_agent', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( __( 'Unauthorized access', 'supportcandy' ), 401 );
			}

			$roles = get_option( 'wpsc-agent-roles', array() );

			$role = isset( $_POST['role'] ) ? intval( $_POST['role'] ) : 0;
			if ( ! $role || ! isset( $roles[ $role ] ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$agent = new WPSC_Agent( $id );
			if ( ! $agent->id ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			if ( $agent->role != $role ) {
				$agent->role = $role;
				if ( $agent->has_cap( 'backend-access' ) ) {
					$agent->user->add_cap( 'wpsc_agent' );
				} else {
					$agent->user->remove_cap( 'wpsc_agent' );
				}
			}

			$agent = apply_filters( 'wpsc_set_edit_agent', $agent );
			$agent->save();
			wp_die();
		}

		/**
		 * Delete an agent
		 *
		 * @return void
		 */
		public static function delete_agent() {

			if ( check_ajax_referer( 'wpsc_get_delete_agent', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( 'Unauthorized!', 401 );
			}

			$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
			if ( ! $id ) {
				wp_send_json_error( 'Bad Request', 400 );
			}

			$agent = new WPSC_Agent( $id );
			if ( ! $agent->id ) {
				wp_send_json_error( 'Bad Request', 400 );
			}

			$agent->is_active = 0;
			$success          = $agent->save();

			// Remove agent capability.
			if ( $agent->has_cap( 'backend-access' ) ) {
				$agent->user->remove_cap( 'wpsc_agent' );
			}

			do_action( 'wpsc_delete_agent', $agent );

			wp_die();
		}

		/**
		 * Search WordPress users for autocomplete purposes
		 *
		 * @return void
		 */
		public static function search_wp_users() {

			if ( check_ajax_referer( 'wpsc_search_wp_users', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorized request!', 400 );
			}

			global $wpdb;

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}
			$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

			// search in wp user table.
			$users = ( new WP_User_Query(
				array(
					'search'         => '*' . esc_attr( $term ) . '*',
					'search_columns' => array(
						'user_login',
						'user_nicename',
						'user_email',
						'display_name',
					),
					'number'         => 10,
				)
			) )->get_results();

			$response = array();
			foreach ( $users as $user ) {
				$response[] = array(
					'id'    => $user->ID,
					'title' => $user->display_name,
					'email' => $user->user_email,
				);
			}
			wp_send_json( $response );
		}

		/**
		 * Create new agent
		 *
		 * @param object  $user - user name.
		 * @param integer $role - role id.
		 * @return WPSC_Agent
		 */
		private static function insert_new_record( $user, $role ) {

			$customer = WPSC_Customer::get_by_user_id( $user->ID );

			// Create agent record.
			$data  = array(
				'user'      => $user->ID,
				'customer'  => $customer->id,
				'role'      => $role,
				'name'      => $user->display_name,
				'is_active' => 1,
			);
			$agent = WPSC_Agent::insert( $data );

			// Give agent capability to this WP user.
			if ( $agent->has_cap( 'backend-access' ) ) {
				$user->add_cap( 'wpsc_agent' );
			}

			// working hrs.
			$company_whs = WPSC_Working_Hour::get();
			for ( $i = 1; $i <= 7; $i++ ) {
				$data = array(
					'agent'      => $agent->id,
					'day'        => $i,
					'start_time' => $company_whs[ $i ]->start_time,
					'end_time'   => $company_whs[ $i ]->end_time,
				);
				WPSC_Working_Hour::insert( $data );
			}

			return $agent;
		}

		/**
		 * After user deleted
		 *
		 * @param integer $user_id - user id.
		 * @param integer $reassign - reassign.
		 * @param object  $user -user info.
		 * @return void
		 */
		public static function wp_user_delete( $user_id, $reassign, $user ) {

			global $wpdb;
			$agent = WPSC_Agent::get_by_user_id( $user_id );
			if ( ! $agent->id || ! $agent->is_active ) {
				return;
			}

			$agent->is_active = 0;
			$agent->user      = 0;
			$agent->save();

			do_action( 'wpsc_delete_agent', $agent );
		}

		/**
		 * After new user added
		 *
		 * @param integer $user_id - user id.
		 * @param array   $user_data - user data.
		 * @return void
		 */
		public static function wp_user_register( $user_id, $user_data ) {

			$customer = WPSC_Customer::get_by_email( $user_data['user_email'] );
			if ( ! $customer->id ) {
				return;
			}

			$agent = WPSC_Agent::get_by_customer( $customer );
			if ( ! $agent->id ) {
				return;
			}

			$name = $user_data['first_name'] . ' ' . $user_data['last_name'];
			$name = trim( $name ) ? $name : $user_data['user_login'];

			$agent->user = $user_id;
			$agent->name = $name;
			$agent->save();
		}
	}
endif;

WPSC_Agent_Settings::init();
