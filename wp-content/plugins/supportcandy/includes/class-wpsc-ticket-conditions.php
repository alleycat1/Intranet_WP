<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Ticket_Conditions' ) ) :

	final class WPSC_Ticket_Conditions {

		/**
		 * Ticket conditions
		 *
		 * @var array
		 */
		public static $conditions = array();

		/**
		 * Ignore custom field types to match for email piping rules
		 *
		 * @var array
		 */
		public static $ignore_cft = array();

		/**
		 * Initialize the class
		 *
		 * @return void
		 */
		public static function init() {

			add_action( 'init', array( __CLASS__, 'load_ticket_conditions' ) );

			add_action( 'wp_ajax_wpsc_tc_get_operators', array( __CLASS__, 'get_operators' ) );
			add_action( 'wp_ajax_nopriv_wpsc_tc_get_operators', array( __CLASS__, 'get_operators' ) );

			add_action( 'wp_ajax_wpsc_tc_get_operand', array( __CLASS__, 'get_operands' ) );
			add_action( 'wp_ajax_nopriv_wpsc_tc_get_operand', array( __CLASS__, 'get_operands' ) );

			// ignore custom field types.
			add_action( 'init', array( __CLASS__, 'set_ignore_cft_list' ) );
		}

		/**
		 * Set ignore custom field types for pipe rules
		 *
		 * @return void
		 */
		public static function set_ignore_cft_list() {

			self::$ignore_cft = apply_filters(
				'wpsc_ticket_condition_ignore_cft',
				array(
					'cf_woo_order',
					'cf_edd_order',
					'cf_tutor_order',
					'cf_learnpress_order',
					'cf_lifter_order',
				)
			);
		}

		/**
		 * Load ticket conditions along with user permitted to utilize them
		 *
		 * @return void
		 */
		public static function load_ticket_conditions() {

			$agent_filters = get_option( 'wpsc-atl-filter-items', array() );
			$customer_filters = get_option( 'wpsc-ctl-filter-items', array() );

			// custom fields.
			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

				if ( ! (
					class_exists( $cf->type ) &&
					$cf->type::$is_filter &&
					in_array( $cf->field, array( 'ticket', 'agentonly', 'customer' ) )
				) ) {
					continue;
				}

				$levels = array( 'admin' );

				if ( in_array( $cf->slug, $agent_filters ) ) {
					$levels[] = 'agent';
				}

				if ( in_array( $cf->slug, $customer_filters ) ) {
					$levels[] = 'customer';
				}

				self::$conditions[ $cf->slug ] = array(
					'name'   => $cf->name,
					'type'   => 'cf',
					'levels' => $levels,
				);
			}

			// custom conditions.
			$other_conditions = apply_filters(
				'wpsc_ticket_conditions',
				array(
					'submitted_by' => esc_attr__( 'Ticket submitted by', 'supportcandy' ),
					'user_role'    => esc_attr__( 'WP user role', 'supportcandy' ),
				)
			);

			foreach ( $other_conditions as $slug => $name ) {
				self::$conditions[ $slug ] = array(
					'name'   => $name,
					'type'   => 'other',
					'levels' => array( 'admin' ),
				);
			}
		}

		/**
		 * Print condition input in the form
		 *
		 * @param string  $name - form element name. Helpful to identify if multiple conditions need to be added.
		 * @param string  $hook - filter hook for conditions. Uniquely identify and filter conditions available in the form.
		 * @param string  $set_conditions - pre-defined conditions to print within input.
		 * @param boolean $is_required - whether or not to print required character for label.
		 * @param string  $label - whether or not to print required character for label.
		 * @return void
		 */
		public static function print( $name, $hook, $set_conditions = '', $is_required = false, $label = '' ) {

			$label = $label ? $label : __( 'Conditions', 'supportcandy' );
			$conditions = apply_filters( $hook, self::$conditions );
			$set_conditions = $set_conditions ? json_decode( html_entity_decode( $set_conditions ), true ) : array();
			$unique_id = wp_unique_id( 'wpsc_' );
			?>
			<div class="wpsc-input-group">
				<div class="label-container">
					<label for="">
						<?php
						echo esc_attr( $label );
						if ( $is_required ) {
							?>
							<span class="required-char">*</span>
							<?php
						}
						?>
					</label>
				</div>
				<div class="wpsc-form-filter-container <?php echo esc_attr( $name ); ?>">
					<div class="and-container">
						<?php
						if ( $set_conditions ) {
							foreach ( $set_conditions as $and_condition ) {
								?>
								<div class="and-item">
									<div class="or-container">
										<?php
										foreach ( $and_condition as $or_condition ) {
											if ( ! isset( $conditions[ $or_condition['slug'] ] ) ) {
												continue;
											}
											?>
											<div class="wpsc-form-filter-item">
												<div class="content">
													<div class="item">
														<select class="filter" onchange="wpsc_tc_get_operators(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_tc_get_operators' ) ); ?>');">
															<option value=""><?php esc_attr_e( 'Select field', 'supportcandy' ); ?></option>
															<?php
															foreach ( $conditions as $slug => $item ) {
																?>
																<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $or_condition['slug'], $slug, true ); ?>><?php echo esc_attr( $item['name'] ); ?></option>
																<?php
															}
															?>
														</select>
													</div>
													<?php
													if ( $conditions[ $or_condition['slug'] ]['type'] == 'cf' ) {
														$cf = WPSC_Custom_Field::get_cf_by_slug( $or_condition['slug'] );
														$cf->type::get_operators( $cf, $or_condition );
														$cf->type::get_operands( $or_condition['operator'], $cf, $or_condition );
													} else {
														self::print_operators( $or_condition['slug'], $or_condition );
														self::print_operands( $or_condition['slug'], $or_condition['operator'], $or_condition );
													}
													?>
												</div>
												<div class="remove-container">
													<span onclick="wpsc_remove_condition_item(this)"><?php WPSC_Icons::get( 'times-circle' ); ?></span>
												</div>
											</div>
											<?php
										}
										?>
									</div>
									<button class="wpsc-button small secondary" onclick="wpsc_add_or_condition( this, '<?php echo esc_attr( $unique_id ); ?>' );"><?php esc_html_e( '+ OR', 'supportcandy' ); ?></button>
								</div>
								<?php
							}
						}
						?>
					</div>
					<button class="wpsc-button small secondary" onclick="wpsc_add_and_condition( this, '<?php echo esc_attr( $unique_id ); ?>' );"><?php esc_html_e( '+ AND', 'supportcandy' ); ?></button>
				</div>
				<script>jQuery( '.wpsc-form-filter-container select.filter' ).selectWoo();</script>
				<div style="display: none;">
					<div class="and-template <?php echo esc_attr( $unique_id ); ?>">
						<div class="and-item">
							<div class="or-container"></div>
							<button class="wpsc-button small secondary" onclick="wpsc_add_or_condition( this, '<?php echo esc_attr( $unique_id ); ?>' );"><?php esc_html_e( '+ OR', 'supportcandy' ); ?></button>
						</div>
					</div>
					<div class="or-template <?php echo esc_attr( $unique_id ); ?>">
						<div class="wpsc-form-filter-item">
							<div class="content">
								<div class="item">
									<select class="filter" onchange="wpsc_tc_get_operators(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_tc_get_operators' ) ); ?>');">
										<option value=""><?php esc_attr_e( 'Select field', 'supportcandy' ); ?></option>
										<?php
										foreach ( $conditions as $slug => $item ) {
											?>
											<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_attr( $item['name'] ); ?></option>
											<?php
										}
										?>
									</select>
								</div>
							</div>
							<div class="remove-container">
								<span onclick="wpsc_remove_condition_item(this)"><?php WPSC_Icons::get( 'times-circle' ); ?></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Check whether or not conditions submitted by client are valid or not before inserting into the database or any further use
		 *
		 * @param string $hook - filter hook for conditions. Uniquely identify and filter conditions available in the form.
		 * @param string $conditions - conditions received from the client.
		 * @return boolean
		 */
		public static function is_valid_input_conditions( $hook, $conditions ) {

			// No conditions.
			if ( ! $conditions || $conditions == '[]' ) {
				return true;
			}

			$allowed_conditions = apply_filters( $hook, self::$conditions );
			$conditions = json_decode( html_entity_decode( $conditions ), true );

			foreach ( $conditions as $and_condition ) {
				foreach ( $and_condition as $or_condition ) {
					if ( ! isset( $allowed_conditions[ $or_condition['slug'] ] ) ) {
						return false;
					}
				}
			}

			return true;
		}

		/**
		 * Get condition operators ajax callback.
		 *
		 * @return void
		 */
		public static function get_operators() {

			$current_user = WPSC_Current_User::$current_user;

			if ( check_ajax_referer( 'wpsc_tc_get_operators', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
			if ( ! $slug || ! array_key_exists( $slug, self::$conditions ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			if ( ! in_array( $current_user->level, self::$conditions[ $slug ]['levels'] ) ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( self::$conditions[ $slug ]['type'] == 'cf' ) {

				$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
				$cf->type::get_operators( $cf );

			} else {

				self::print_operators( $slug );
			}

			wp_die();
		}

		/**
		 * Print condition operators
		 *
		 * @param string $slug - condition slug.
		 * @param array  $filter - predefined condition to set operator.
		 * @return void
		 */
		public static function print_operators( $slug, $filter = array() ) {

			switch ( $slug ) {

				case 'submitted_by':
					?>
					<div class="item conditional">
						<select class="operator" onchange="wpsc_tc_get_operand(this, '<?php echo esc_attr( $slug ); ?>', '<?php echo esc_attr( wp_create_nonce( 'wpsc_tc_get_operand' ) ); ?>');">
							<option value=""><?php esc_attr_e( 'Compare As', 'supportcandy' ); ?></option>
							<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '=' ); ?> value="="><?php esc_attr_e( 'Equals', 'supportcandy' ); ?></option>
						</select>
					</div>
					<?php
					break;

				case 'user_role':
					?>
					<div class="item conditional">
						<select class="operator" onchange="wpsc_tc_get_operand(this, '<?php echo esc_attr( $slug ); ?>', '<?php echo esc_attr( wp_create_nonce( 'wpsc_tc_get_operand' ) ); ?>');">
							<option value=""><?php esc_attr_e( 'Compare As', 'supportcandy' ); ?></option>
							<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '=' ); ?> value="="><?php esc_attr_e( 'Equals', 'supportcandy' ); ?></option>
							<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], 'IN' ); ?> value="IN"><?php esc_attr_e( 'Matches', 'supportcandy' ); ?></option>
							<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], 'NOT IN' ); ?> value="NOT IN"><?php esc_attr_e( 'Not Matches', 'supportcandy' ); ?></option>
						</select>
					</div>
					<?php
					break;

				default:
					do_action( 'wpsc_tc_print_operators', $slug, $filter );
			}
		}

		/**
		 * Get condition operands ajax callback.
		 *
		 * @return void
		 */
		public static function get_operands() {

			$current_user = WPSC_Current_User::$current_user;

			if ( check_ajax_referer( 'wpsc_tc_get_operand', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
			if ( ! $slug || ! array_key_exists( $slug, self::$conditions ) ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			if ( ! in_array( $current_user->level, self::$conditions[ $slug ]['levels'] ) ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$operator = isset( $_POST['operator'] ) ? sanitize_text_field( wp_unslash( $_POST['operator'] ) ) : '';
			if ( ! $operator ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			if ( self::$conditions[ $slug ]['type'] == 'cf' ) {

				$cf = WPSC_Custom_Field::get_cf_by_slug( $slug );
				$cf->type::get_operands( $operator, $cf );

			} else {

				self::print_operands( $slug, $operator );
			}

			wp_die();
		}

		/**
		 * Print condition operands
		 *
		 * @param string $slug - condition slug.
		 * @param string $operator - operator string.
		 * @param array  $filter - predefined condition to set operand.
		 * @return void
		 */
		public static function print_operands( $slug, $operator, $filter = array() ) {

			global $wp_roles;

			switch ( $slug ) {

				case 'submitted_by':
					?>
					<div class="item conditional operand single">
						<select 
							class="operand_val_1">
							<option <?php isset( $filter['operand_val_1'] ) && selected( $filter['operand_val_1'], 'user' ); ?> value="user"><?php esc_attr_e( 'User', 'supportcandy' ); ?></option>
							<option <?php isset( $filter['operand_val_1'] ) && selected( $filter['operand_val_1'], 'agent' ); ?> value="agent"><?php esc_attr_e( 'Agent', 'supportcandy' ); ?></option>
						</select>
					</div>
					<?php
					break;

				case 'user_role':
					$is_multiple = $operator !== '=' ? true : false;
					$unique_id   = uniqid( 'wpsc_' );
					?>
					<div class="item conditional operand single">
						<select 
							class="operand_val_1 <?php echo esc_attr( $unique_id ); ?>" <?php echo $is_multiple ? 'multiple' : ''; ?>>
							<?php
							foreach ( $wp_roles->roles as $key => $role ) {
								$selected = '';
								if ( isset( $filter['operand_val_1'] ) && ( ( $is_multiple && in_array( $key, $filter['operand_val_1'] ) ) || ( ! $is_multiple && $key == $filter['operand_val_1'] ) ) ) {
									$selected = 'selected="selected"';
								}
								?>
								<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $role['name'] ); ?></option>
								<?php
							}
							?>
						</select>
					</div>
					<script>jQuery('.operand_val_1.<?php echo esc_attr( $unique_id ); ?>').selectWoo();</script>
					<?php
					break;

				default:
					do_action( 'wpsc_tc_print_operand', $slug, $operator, $filter );
			}
		}

		/**
		 * Get meta query for the given conditions
		 *
		 * @param string $conditions - conditions json from database.
		 * @param string $is_user_query - check if it needs to check the permission of the filter of current user. e.g. custom filters, saved filters need to check permissions but default filters need not.
		 * @return array
		 */
		public static function get_meta_query( $conditions, $is_user_query = false ) {

			$current_user = WPSC_Current_User::$current_user;

			$meta_query = array();
			$conditions = $conditions ? json_decode( html_entity_decode( $conditions ), true ) : array();
			if ( ! $conditions ) {
				return $meta_query;
			}

			foreach ( $conditions as $and_condition ) {

				$temp = array();
				foreach ( $and_condition as $or_condition ) {

					if (
						! isset( self::$conditions[ $or_condition['slug'] ] ) ||
						( $is_user_query && ! in_array( $current_user->level, self::$conditions[ $or_condition['slug'] ]['levels'] ) )
					) {
						continue;
					}

					$cf = WPSC_Custom_Field::get_cf_by_slug( $or_condition['slug'] );
					$val = $cf->type::get_meta_value( $or_condition );
					if ( $val === false ) {
						continue;
					}

					$temp[] = array(
						'slug'    => $or_condition['slug'],
						'compare' => $or_condition['operator'],
						'val'     => $val,
					);
				}

				if ( $temp ) {
					$meta_query[] = array_merge( array( 'relation' => 'OR' ), $temp );
				}
			}

			return $meta_query;
		}

		/**
		 * Return whether or not given conditions are satisfied by the given ticket
		 *
		 * @param string      $conditions - conditions json from database.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return boolean
		 */
		public static function is_valid( $conditions, $ticket ) {

			$conditions = $conditions ? json_decode( html_entity_decode( $conditions ), true ) : array();
			if ( ! $conditions ) {
				return true;
			}

			foreach ( $conditions as $and_condition ) {

				$flag = false;
				foreach ( $and_condition as $or_condition ) {

					if ( ! isset( self::$conditions[ $or_condition['slug'] ] ) ) {
						continue;
					}

					if ( self::$conditions[ $or_condition['slug'] ]['type'] == 'cf' ) {

						$cf = WPSC_Custom_Field::get_cf_by_slug( $or_condition['slug'] );
						if ( in_array( $cf->type::$slug, self::$ignore_cft ) ) {
							continue;
						}
						if ( $cf->field == 'customer' ) {
							$flag = $cf->type::is_valid_customer_condition( $or_condition, $cf, $ticket->customer );
						} else {
							$flag = $cf->type::is_valid_ticket_condition( $or_condition, $cf, $ticket );
						}
					} else {

						switch ( $or_condition['slug'] ) {

							case 'submitted_by':
								if (
									(
										$or_condition['operand_val_1'] == 'agent' &&
										is_object( $ticket->agent_created )
									) ||
									(
										$or_condition['operand_val_1'] == 'user' &&
										! is_object( $ticket->agent_created )
									)
								) {
									$flag = true;
								}
								break;

							case 'user_role':
								$user_roles = $ticket->customer->user->roles;
								switch ( $or_condition['operator'] ) {

									case '=':
										$flag = in_array( $or_condition['operand_val_1'], $user_roles ) ? true : false;
										break;

									case 'IN':
										foreach ( $user_roles as $id ) {
											if ( in_array( $id, $or_condition['operand_val_1'] ) ) {
												$flag = true;
												break;
											}
										}
										break;

									case 'NOT IN':
										$temp = true;
										foreach ( $user_roles as $id ) {
											if ( in_array( $id, $or_condition['operand_val_1'] ) ) {
												$temp = false;
												break;
											}
										}
										$flag = $temp;
										break;
								}
								break;

							default:
								$flag = apply_filters( 'wpsc_tc_is_valid', $flag, $or_condition['slug'], $or_condition, $ticket );
								break;
						}
					}

					// no need to check further OR conditions if this condition is TRUE.
					if ( $flag ) {
						break;
					}
				}

				// no need to check further AND conditions if this condition is FALSE.
				if ( ! $flag ) {
					return false;
				}
			}

			// if we make upto this point, all conditions are satisfied.
			return true;
		}
	}
endif;

WPSC_Ticket_Conditions::init();
