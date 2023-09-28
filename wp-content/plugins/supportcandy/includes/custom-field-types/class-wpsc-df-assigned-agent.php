<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_Assigned_Agent' ) ) :

	final class WPSC_DF_Assigned_Agent {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_assigned_agent';

		/**
		 * Set whether this custom field type is of type date
		 *
		 * @var boolean
		 */
		public static $is_date = false;

		/**
		 * Set whether this custom field type has applicable to date range
		 *
		 * @var boolean
		 */
		public static $has_date_range = false;

		/**
		 * Set whether this custom field type has multiple values
		 *
		 * @var boolean
		 */
		public static $has_multiple_val = true;

		/**
		 * Data type for column created in tickets table
		 *
		 * @var string
		 */
		public static $data_type = 'TEXT NULL DEFAULT NULL';

		/**
		 * Set whether this custom field type has reference to other class
		 *
		 * @var boolean
		 */
		public static $has_ref = true;

		/**
		 * Reference class for this custom field type so that its value(s) return with object or array of objects automatically. Empty string indicate no reference.
		 *
		 * @var string
		 */
		public static $ref_class = 'wpsc_agent';

		/**
		 * Set whether this custom field field type is system default (no fields can be created from it).
		 *
		 * @var boolean
		 */
		public static $is_default = true;

		/**
		 * Set whether this field type has extra information that can be used in ticket form, edit custom fields, etc.
		 *
		 * @var boolean
		 */
		public static $has_extra_info = true;

		/**
		 * Set whether this custom field type can accept personal info.
		 *
		 * @var boolean
		 */
		public static $has_personal_info = false;

		/**
		 * Set whether fields created from this custom field type is allowed in create ticket form
		 *
		 * @var boolean
		 */
		public static $is_ctf = true;

		/**
		 * Set whether fields created from this custom field type is allowed in ticket list
		 *
		 * @var boolean
		 */
		public static $is_list = true;

		/**
		 * Set whether fields created from this custom field type is allowed in ticket filter
		 *
		 * @var boolean
		 */
		public static $is_filter = true;

		/**
		 * Set whether fields created from this custom field type can be given character limits
		 *
		 * @var boolean
		 */
		public static $has_char_limit = false;

		/**
		 * Set whether fields created from this custom field type has custom options set in options table
		 *
		 * @var boolean
		 */
		public static $has_options = false;

		/**
		 * Set whether fields created from this custom field type can be available for ticket list sorting
		 *
		 * @var boolean
		 */
		public static $is_sort = false;

		/**
		 * Set whether fields created from this custom field type can be auto-filled
		 *
		 * @var boolean
		 */
		public static $is_auto_fill = true;

		/**
		 * Set whether fields created from this custom field type can have placeholder
		 *
		 * @var boolean
		 */
		public static $is_placeholder = true;

		/**
		 * Set whether fields created from this custom field type is applicable for visibility conditions in create ticket form
		 *
		 * @var boolean
		 */
		public static $is_visibility_conditions = true;

		/**
		 * Set whether fields created from this custom field type is applicable for macros
		 *
		 * @var boolean
		 */
		public static $has_macro = true;

		/**
		 * Set whether fields of this custom field type is applicalbe for search on ticket list page.
		 *
		 * @var boolean
		 */
		public static $is_search = false;

		/**
		 * Initialize the class
		 *
		 * @return void
		 */
		public static function init() {

			// Get object of this class.
			add_filter( 'wpsc_load_ref_classes', array( __CLASS__, 'load_ref_class' ) );

			// TFF settings!
			add_action( 'wpsc_get_add_new_tff', array( __CLASS__, 'get_add_new_tff' ) );
			add_action( 'wpsc_jse_add_tff_change_field', array( __CLASS__, 'js_add_tff_change' ) );
			add_filter( 'wpsc_tff_add_new', array( __CLASS__, 'set_add_new_tff' ), 10, 2 );
			add_action( 'wpsc_get_edit_tff', array( __CLASS__, 'get_edit_tff' ), 10, 2 );
			add_filter( 'wpsc_set_edit_tff', array( __CLASS__, 'set_edit_tff' ), 10, 2 );

			// ticket form.
			add_action( 'wpsc_js_validate_ticket_form', array( __CLASS__, 'js_validate_ticket_form' ) );
			add_filter( 'wpsc_create_ticket_data', array( __CLASS__, 'set_create_ticket_data' ), 10, 3 );

			// create ticket data for rest api.
			add_filter( 'wpsc_rest_create_ticket', array( __CLASS__, 'set_rest_ticket_data' ), 10, 3 );

			// Assign default agent if not already assigned.
			add_action( 'wpsc_create_new_ticket', array( __CLASS__, 'assign_default_agent' ) );

			// agent autocomplete filter access only.
			add_action( 'wp_ajax_wpsc_agent_autocomplete_assigned_agent', array( __CLASS__, 'agent_autocomplete_assigned_agent' ) );
			add_action( 'wp_ajax_nopriv_wpsc_agent_autocomplete_assigned_agent', array( __CLASS__, 'agent_autocomplete_assigned_agent' ) );

			// autocomplete.
			add_action( 'wp_ajax_wpsc_agent_autocomplete_df_aa', array( __CLASS__, 'agent_autocomplete_df_aa' ) );
			add_action( 'wp_ajax_nopriv_wpsc_agent_autocomplete_df_aa', array( __CLASS__, 'agent_autocomplete_df_aa' ) );

			// rest api.
			add_filter( 'wpsc_rest_prevent_ticket_data', array( __CLASS__, 'rest_prevent_ticket_data' ) );
		}

		/**
		 * Load current class to reference classes
		 *
		 * @param array $classes - Associative array of class names indexed by its slug.
		 * @return array
		 */
		public static function load_ref_class( $classes ) {

			$classes[ self::$slug ] = array(
				'class'    => __CLASS__,
				'save-key' => 'id',
			);
			return $classes;
		}

		/**
		 * Print operators for ticket form filter
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param array             $filter - Existing filters (if any).
		 * @return void
		 */
		public static function get_operators( $cf, $filter = array() ) {?>

			<div class="item conditional">
				<select class="operator" onchange="wpsc_tc_get_operand(this, '<?php echo esc_attr( $cf->slug ); ?>', '<?php echo esc_attr( wp_create_nonce( 'wpsc_tc_get_operand' ) ); ?>');">                    
					<option value=""><?php esc_attr_e( 'Compare As', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '=' ); ?> value="="><?php esc_attr_e( 'Equals', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], 'IN' ); ?> value="IN"><?php esc_attr_e( 'Matches', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], 'NOT IN' ); ?> value="NOT IN"><?php esc_attr_e( 'Not Matches', 'supportcandy' ); ?></option>
				</select> 
			</div>
			<?php
		}

		/**
		 * Print operators for ticket form filter
		 *
		 * @param string            $operator - condition operator on which operands should be returned.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param array             $filter - Exising functions (if any).
		 * @return void
		 */
		public static function get_operands( $operator, $cf, $filter = array() ) {

			$is_multiple = $operator !== '=' ? true : false;
			$unique_id   = uniqid( 'wpsc_' );
			?>
			<div class="item conditional operand single">
				<select class="operand_val_1 <?php echo esc_attr( $unique_id ); ?>" <?php echo $is_multiple ? 'multiple' : ''; ?>>

					<?php
					if ( $is_multiple && isset( $filter['operand_val_1'] ) ) {

						foreach ( $filter['operand_val_1'] as $agent_id ) {

							if ( $agent_id == '0' ) {

								?>
								<option selected="selected" value="0"><?php esc_attr_e( 'None', 'supportcandy' ); ?></option>
								<?php

							} else {

								$agent = new WPSC_Agent( intval( $agent_id ) )
								?>
								<option selected="selected" value="<?php echo esc_attr( $agent->id ); ?>"><?php echo esc_attr( $agent->name ); ?></option>
								<?php
							}
						}
					}

					if ( ! $is_multiple && isset( $filter['operand_val_1'] ) ) {

						if ( $filter['operand_val_1'] == '0' ) {

							?>
							<option selected="selected" value="0"><?php esc_attr_e( 'None', 'supportcandy' ); ?></option>
							<?php

						} else {

							$agent = new WPSC_Agent( intval( $filter['operand_val_1'] ) )
							?>
							<option selected="selected" value="<?php echo esc_attr( $agent->id ); ?>"><?php echo esc_attr( $agent->name ); ?></option>
							<?php
						}
					}
					?>

				</select>
			</div>
			<script>
				jQuery('.operand_val_1.<?php echo esc_attr( $unique_id ); ?>').selectWoo({
					ajax: {
						url: supportcandy.ajax_url,
						dataType: 'json',
						delay: 250,
						data: function (params) {
							return {
								q: params.term, // search term
								page: params.page,
								action: 'wpsc_agent_autocomplete_assigned_agent',
								_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_agent_autocomplete_assigned_agent' ) ); ?>',
							};
						},
						processResults: function (data, params) {
							var terms = [];
							if ( data ) {
								jQuery.each( data, function( id, text ) {
									terms.push( { id: text.id, text: text.title } );
								});
							}
							return {
								results: terms
							};
						},
						cache: true
					},
					escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
					minimumInputLength: 0
				});
			</script>
			<?php
		}

		/**
		 * Check whether or not given condition is valid when it compared with given value.
		 *
		 * @param array             $condition - condition data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $assigned_agents - value to comapre.
		 * @return boolean
		 */
		public static function is_valid( $condition, $cf, $assigned_agents ) {

			$response = false;
			switch ( $condition['operator'] ) {

				case '=':
					if ( $condition['operand_val_1'] == 0 ) {
						$response = ! $assigned_agents ? true : false;
					} else {
						$response = in_array( $condition['operand_val_1'], $assigned_agents );
					}
					break;

				case 'IN':
					$flag = false;
					foreach ( $condition['operand_val_1'] as $agent ) {

						if ( ( $agent == 0 && ! $assigned_agents ) ||
							in_array( $agent, $assigned_agents )
						) {
							$flag = true;
							break;
						}
					}
					$response = $flag;
					break;

				case 'NOT IN':
					$flag = true;
					foreach ( $condition['operand_val_1'] as $agent ) {

						if ( ( $agent == 0 && ! $assigned_agents ) ||
							in_array( $agent, $assigned_agents )
						) {
							$flag = false;
							break;
						}
					}
					$response = $flag;
					break;
			}
			return $response;
		}

		/**
		 * Parse filter and return sql query to be merged in ticket model query builder
		 *
		 * @param WPSC_Custom_Field $cf - custom field of this type.
		 * @param mixed             $compare - comparison operator.
		 * @param mixed             $val - value to compare.
		 * @return string
		 */
		public static function parse_filter( $cf, $compare, $val ) {

			$str = '';

			switch ( $compare ) {

				case '=':
					if ( $val == '' || $val == '0' ) {
						$val = '^$';
					}
					$str = 't.' . $cf->slug . ' RLIKE \'(^|[|])' . esc_sql( $val ) . '($|[|])\'';
					break;

				case 'IN':
					foreach ( $val as $index => $value ) {
						if ( $value == '' || $value == '0' ) {
							$val[ $index ] = '^$';
						}
					}
					$str = 't.' . $cf->slug . ' RLIKE \'(^|[|])(' . implode( '|', esc_sql( $val ) ) . ')($|[|])\'';
					break;

				case 'NOT IN':
					foreach ( esc_sql( $val ) as $index => $value ) {
						if ( $value == '' || $value == '0' ) {
							$val[ $index ] = '^$';
						}
					}
					$str = 't.' . $cf->slug . ' NOT RLIKE \'(^|[|])(' . implode( '|', esc_sql( $val ) ) . ')($|[|])\'';
					break;

				default:
					$str = '1=1';
			}

			return $str;
		}

		/**
		 * Check ticket condition
		 *
		 * @param array             $condition - array with condition data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return boolean
		 */
		public static function is_valid_ticket_condition( $condition, $cf, $ticket ) {

			$flag = true;
			$agent_ids = array_filter(
				array_map(
					fn( $agent) => $agent->id,
					$ticket->{$cf->slug}
				)
			);

			switch ( $condition['operator'] ) {

				case '=':
					if ( $condition['operand_val_1'] == 0 ) {
						$flag = ! $agent_ids ? true : false;
					} else {
						$flag = in_array( $condition['operand_val_1'], $agent_ids ) ? true : false;
					}
					break;

				case 'IN':
					if ( ! $agent_ids ) {
						$flag = in_array( '0', $condition['operand_val_1'] ) ? true : false;
					} else {

						$flag = false;
						foreach ( $agent_ids as $id ) {
							if ( in_array( $id, $condition['operand_val_1'] ) ) {
								$flag = true;
								break;
							}
						}
					}
					break;

				case 'NOT IN':
					if ( ! $agent_ids ) {
						$flag = ! in_array( '0', $condition['operand_val_1'] ) ? true : false;
					} else {

						foreach ( $agent_ids as $id ) {
							if ( in_array( $id, $condition['operand_val_1'] ) ) {
								$flag = false;
								break;
							}
						}
					}
					break;

				default:
					$flag = true;
			}

			return $flag;
		}

		/**
		 * Print ticket form field
		 *
		 * @param WPSC_Custom_Field $cf - Custom field object.
		 * @param array             $tff - Array of ticket form field settings for this field.
		 * @return string
		 */
		public static function print_tff( $cf, $tff ) {

			$current_user = WPSC_Current_User::$current_user;
			$is_multiple  = $tff['is_multiple'] ? true : false;
			$allowed_user = $tff['allowed_user'];

			if ( ! (
				( $current_user->is_agent && in_array( $allowed_user, array( 'agent', 'both' ) ) ) ||
				( ! $current_user->is_agent && in_array( $allowed_user, array( 'customer', 'both' ) ) )
			) ) {
				return;
			}

			$unique_id   = uniqid( 'wpsc_' );
			$default_val = $cf->is_auto_fill && $cf->default_value ? $cf->default_value : array();

			ob_start();

			?>
			<div class="<?php echo esc_attr( WPSC_Functions::get_tff_classes( $cf, $tff ) ); ?>" data-cft="<?php echo esc_attr( self::$slug ); ?>">

				<div class="wpsc-tff-label">
					<span class="name"><?php echo esc_attr( $cf->name ); ?></span>

					<?php
					if ( $tff['is-required'] ) {
						?>
						<span class="required-indicator">*</span>
						<?php
					}
					?>

				</div>

				<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>

				<select class="<?php echo esc_attr( $unique_id ); ?>" name="<?php echo esc_attr( $cf->slug ); ?>[]" onchange="wpsc_check_tff_visibility()" <?php echo $is_multiple ? 'multiple' : ''; ?>>

					<?php
					if ( ! $tff['is_ajax'] ) {

						$agents = WPSC_Agent::find(
							array(
								'items_per_page' => 0,
								'meta_query'     => array(
									'relation' => 'AND',
									array(
										'slug'    => 'is_active',
										'compare' => '=',
										'val'     => 1,
									),
								),
							)
						)['results'];

						foreach ( $agents as $agent ) {

							$selected = in_array( $agent->id, $default_val ) ? 'selected' : ''

							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $agent->id ); ?>"><?php echo esc_attr( $agent->name ); ?></option>
							<?php

						}
					} else {

						foreach ( $default_val as $agent_id ) {

							$agent = new WPSC_Agent( $agent_id );

							?>
							<option selected value="<?php echo esc_attr( $agent->id ); ?>"><?php echo esc_attr( $agent->name ); ?></option>
							<?php

						}
					}
					?>
				</select>
				<script>

					jQuery('select.<?php echo esc_attr( $unique_id ); ?>').selectWoo(
						<?php
						if ( $tff['is_ajax'] ) :
							?>

							{
								ajax: {
									url: supportcandy.ajax_url,
									dataType: 'json',
									delay: 250,
									data: function (params) {
										return {
											q: params.term, // search term
											page: params.page,
											action: 'wpsc_agent_autocomplete_df_aa',
											_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_agent_autocomplete_df_aa' ) ); ?>',
											isMultiple: 1,
										};
									},
									processResults: function (data, params) {
										var terms = [];
										if ( data ) {
											jQuery.each( data, function( id, text ) {
												terms.push( { id: text.id, text: text.title } );
											});
										}
										return {
											results: terms
										};
									},
									cache: true
								},
								escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
								minimumInputLength: 0,
								allowClear: false,
								placeholder: "<?php echo esc_attr( $cf->placeholder_text ); ?>"
							}
							<?php
						endif;
						?>
					);
				</script>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Validate this type field in create ticket
		 *
		 * @return void
		 */
		public static function js_validate_ticket_form() {
			?>

			case '<?php echo esc_attr( self::$slug ); ?>':
				var val = customField.find('select').first().val();
				if (customField.hasClass('required') && !val) {
					isValid = false;
					alert(supportcandy.translations.req_fields_missing);
				}
				break;
			<?php
			echo PHP_EOL;
		}

		/**
		 * Return custom field value in $_POST
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @param string $slug - Custom field slug.
		 * @param mixed  $cf - Custom field object or false.
		 * @return mixed
		 */
		public static function get_tff_value( $slug, $cf = false ) {

			if ( isset( $_POST[ $slug ] ) ) { // phpcs:ignore
				$assignee = array_map( 'intval', $_POST[ $slug ] ); // phpcs:ignore
				return array_unique(
					array_filter(
						array_map(
							function( $id ) {
								$agent = new WPSC_Agent( $id );
								return $agent->id ? $agent->id : false;
							},
							$assignee
						)
					)
				);
			}

			return array();
		}

		/**
		 * Print tff fields related to assigned agent in add new tff
		 *
		 * @param string $unique_id - unique id for the tff form.
		 * @return void
		 */
		public static function get_add_new_tff( $unique_id ) {

			?>
			<div class="wpsc-input-group is-ajax <?php echo esc_attr( $unique_id ); ?>" style="display: none;">
				<div class="label-container">
					<label for=""><?php esc_attr_e( 'Ajax loading of results?', 'supportcandy' ); ?></label>
				</div>
				<select name="is-ajax">
					<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
				</select>
			</div>

			<div class="wpsc-input-group is-multiple <?php echo esc_attr( $unique_id ); ?>" style="display: none;">
				<div class="label-container">
					<label for=""><?php esc_attr_e( 'Is multiple?', 'supportcandy' ); ?></label>
				</div>
				<select name="is-multiple">
					<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
				</select>
			</div>

			<div class="wpsc-input-group allowed-user <?php echo esc_attr( $unique_id ); ?>" style="display: none;">
				<div class="label-container">
					<label for=""><?php esc_attr_e( 'Allowed user', 'supportcandy' ); ?></label>
				</div>
				<select name="allowed-user">
					<option value="agent"><?php esc_attr_e( 'Agent', 'supportcandy' ); ?></option>
					<option value="customer"><?php esc_attr_e( 'Customer', 'supportcandy' ); ?></option>
					<option value="both"><?php esc_attr_e( 'Both', 'supportcandy' ); ?></option>
				</select>
			</div>
			<?php
		}

		/**
		 * Change event callback extension for tff add new field
		 *
		 * @param string $unique_id - unique id for the tff form.
		 * @return void
		 */
		public static function js_add_tff_change( $unique_id ) {
			?>

			// Assigned agent
			if (slug == 'assigned_agent') {
				jQuery('div.allowed-user.<?php echo esc_attr( $unique_id ); ?>').show();
				jQuery('div.is-multiple.<?php echo esc_attr( $unique_id ); ?>').show();
				jQuery('div.is-ajax.<?php echo esc_attr( $unique_id ); ?>').show();
			} else {
				jQuery('div.allowed-user.<?php echo esc_attr( $unique_id ); ?>').hide();
				jQuery('div.is-multiple.<?php echo esc_attr( $unique_id ); ?>').hide();
				jQuery('div.is-ajax.<?php echo esc_attr( $unique_id ); ?>').hide();
			}
			<?php
		}

		/**
		 * Add options for assigned agent while saving field
		 *
		 * @param array             $field - custom field data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return array
		 */
		public static function set_add_new_tff( $field, $cf ) {

			// ignore phpcs nonce issue as we already checked where it is called from.
			if ( $cf->slug == 'assigned_agent' ) {
				$field['is_ajax']      = isset( $_POST['is-ajax'] ) ? intval( $_POST['is-ajax'] ) : 1; // phpcs:ignore
				$field['is_multiple']  = isset( $_POST['is-multiple'] ) ? intval( $_POST['is-multiple'] ) : 1; // phpcs:ignore
				$field['allowed_user'] = isset( $_POST['allowed-user'] ) ? sanitize_text_field( wp_unslash( $_POST['allowed-user'] ) ) : 'agent'; // phpcs:ignore
			}
			return $field;
		}

		/**
		 * Print tff fields related to assigned agent in edit tff form
		 *
		 * @param array             $field - custom field data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return void
		 */
		public static function get_edit_tff( $field, $cf ) {

			if ( $cf->slug == 'assigned_agent' ) :
				?>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Ajax loading of results?', 'supportcandy' ); ?></label>
					</div>
					<select name="is-ajax">
						<option <?php selected( $field['is_ajax'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $field['is_ajax'], '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Is multiple?', 'supportcandy' ); ?></label>
					</div>
					<select name="is-multiple">
						<option <?php selected( $field['is_multiple'], 1 ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $field['is_multiple'], 0 ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>

				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed user', 'supportcandy' ); ?></label>
					</div>
					<select name="allowed-user">
						<option <?php selected( $field['allowed_user'], 'agent' ); ?> value="agent"><?php esc_attr_e( 'Agent', 'supportcandy' ); ?></option>
						<option <?php selected( $field['allowed_user'], 'customer' ); ?> value="customer"><?php esc_attr_e( 'Customer', 'supportcandy' ); ?></option>
						<option <?php selected( $field['allowed_user'], 'both' ); ?> value="both"><?php esc_attr_e( 'Both', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php

			endif;
		}

		/**
		 * Set edit tff values for assigned agent
		 *
		 * @param array             $field - custom field data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return array
		 */
		public static function set_edit_tff( $field, $cf ) {

			// ignore phpcs nonce issue as we already checked where it is called from.
			if ( $cf->slug == 'assigned_agent' ) {
				$field['is_ajax']      = isset( $_POST['is-ajax'] ) ? intval( $_POST['is-ajax'] ) : 1; // phpcs:ignore
				$field['is_multiple']  = isset( $_POST['is-multiple'] ) ? intval( $_POST['is-multiple'] ) : 1; // phpcs:ignore
				$field['allowed_user'] = isset( $_POST['allowed-user'] ) ? sanitize_text_field( wp_unslash( $_POST['allowed-user'] ) ) : 'agent'; // phpcs:ignore
			}
			return $field;
		}

		/**
		 * Return default value for custom field of this type
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return mixed
		 */
		public static function get_default_value( $cf ) {

			return $cf->default_value ? implode( '|', $cf->default_value ) : '';
		}

		/**
		 * Check and return custom field value for new ticket to be created.
		 * This function is used by filter for set create ticket form and called directly by my-profile for each applicable custom fields.
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @param array   $data - Array of values to to stored in ticket in an insert function.
		 * @param array   $custom_fields - Array containing all applicable custom fields indexed by unique custom field types.
		 * @param boolean $is_my_profile - Whether it or not it is created from my-profile. This function is used by create ticket as well as my-profile. Due to customer fields handling is done same way, this flag gives apportunity to identify where it being called.
		 * @return array
		 */
		public static function set_create_ticket_data( $data, $custom_fields, $is_my_profile ) {

			if ( $is_my_profile ) {
				return;
			}

			$current_user = WPSC_Current_User::$current_user;
			$assignee = self::get_tff_value( 'assigned_agent' );
			$tff = get_option( 'wpsc-tff' );

			if (
				( ! isset( $tff['assigned_agent'] ) && $assignee ) ||
				(
					$assignee &&
					(
						( $current_user->is_agent && ! in_array( $tff['assigned_agent']['allowed_user'], array( 'agent', 'both' ) ) ) ||
						( ! $current_user->is_agent && $current_user->is_customer && ! in_array( $tff['assigned_agent']['allowed_user'], array( 'customer', 'both' ) ) )
					)
				)
			) {
				wp_send_json_error( new WP_Error( 'WPSC_DF_Assigned_Agent', 'Unauthorized!' ), 400 );
			}

			$data['assigned_agent'] = $assignee ? implode( '|', $assignee ) : '';
			return $data;
		}

		/**
		 * Set create ticket data for rest api request
		 *
		 * @param array           $data - create ticket data array.
		 * @param WP_REST_Request $request - rest request object.
		 * @param array           $custom_fields - custom field objects indexed by unique custom field types.
		 * @return array
		 */
		public static function set_rest_ticket_data( $data, $request, $custom_fields ) {

			$current_user = WPSC_Current_User::$current_user;
			$tff = get_option( 'wpsc-tff' );

			$assignee = array_unique(
				array_filter(
					array_map(
						function( $id ) {
							$agent = new WPSC_Agent( intval( $id ) );
							return $agent->id ? $agent->id : false;
						},
						explode( ',', sanitize_text_field( $request->get_param( 'assigned_agent' ) ) )
					)
				)
			);

			if (
				( ! isset( $tff['assigned_agent'] ) && $assignee ) ||
				(
					$assignee &&
					(
						( $current_user->is_agent && ! in_array( $tff['assigned_agent']['allowed_user'], array( 'agent', 'both' ) ) ) ||
						( ! $current_user->is_agent && $current_user->is_customer && ! in_array( $tff['assigned_agent']['allowed_user'], array( 'customer', 'both' ) ) )
					)
				)
			) {
				$data['assigned_agent'] = '';
				return $data;
			}

			$data['assigned_agent'] = $assignee ? implode( '|', $assignee ) : '';
			return $data;
		}

		/**
		 * Return val field for meta query of this type of custom field
		 *
		 * @param array $condition - condition data.
		 * @return mixed
		 */
		public static function get_meta_value( $condition ) {

			$operator = $condition['operator'];
			switch ( $operator ) {

				case '=':
				case 'IN':
				case 'NOT IN':
					return $condition['operand_val_1'];
			}
			return false;
		}

		/**
		 * Print edit custom field properties
		 *
		 * @param WPSC_Custom_Fields $cf - custom field object.
		 * @param string             $field_class - class name of field category.
		 * @return void
		 */
		public static function get_edit_custom_field_properties( $cf, $field_class ) {

			if ( in_array( 'extra_info', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="textfield" data-required="false" class="wpsc-input-group extra-info">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Extra info', 'supportcandy' ); ?></label>
					</div>
					<input name="extra_info" type="text" value="<?php echo esc_attr( $cf->extra_info ); ?>" autocomplete="off" />
				</div>
				<?php
			endif;

			if ( in_array( 'default_value', $field_class::$allowed_properties ) ) :
				?>

				<div data-type="textfield" data-required="false" class="wpsc-input-group default_value">

					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Default value', 'supportcandy' ); ?>
						</label>
					</div>

					<div class="ticket-fields-assigned-agent">
						<select multiple class="wpsc-default-assigned-agent" name="default_value[]">
							<?php
							$multi_dopt = $cf->default_value ? $cf->default_value : array();
							foreach ( $multi_dopt as $agent_id ) {
								$agent = new WPSC_Agent( intval( $agent_id ) );
								?>
								<option selected="selected" value="<?php echo esc_attr( $agent->id ); ?>"><?php echo esc_attr( $agent->name ); ?></option>
								<?php
							}
							?>
						</select>
					</div>

					<script>
						jQuery('.wpsc-default-assigned-agent').selectWoo({
							ajax: {
								url: supportcandy.ajax_url,
								dataType: 'json',
								delay: 250,
								data: function (params) {
									return {
										q: params.term, // search term
										page: params.page,
										action: 'wpsc_agent_autocomplete_admin_access',
										_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_agent_autocomplete_admin_access' ) ); ?>',
									};
								},
								processResults: function (data, params) {
									var terms = [];
									if ( data ) {
										jQuery.each( data, function( id, text ) {
											terms.push( { id: text.id, text: text.title } );
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
				<?php
			endif;

			if ( in_array( 'is_auto_fill', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group is_auto_fill">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Auto-fill in ticket form', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="is_auto_fill">
						<option <?php selected( $cf->is_auto_fill, '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option <?php selected( $cf->is_auto_fill, '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;

			if ( in_array( 'placeholder_text', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="textfield" data-required="false" class="wpsc-input-group placeholder_text">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Placeholder', 'supportcandy' ); ?>
						</label>
					</div>
					<input type="text" name="placeholder_text" value="<?php echo esc_attr( $cf->placeholder_text ); ?>" autocomplete="off">
				</div>
				<?php
			endif;

			if ( in_array( 'tl_width', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="number" data-required="false" class="wpsc-input-group tl_width">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Ticket list width (pixels)', 'supportcandy' ); ?>
						</label>
					</div>
					<input type="number" name="tl_width" value="<?php echo esc_attr( intval( $cf->tl_width ) ); ?>" autocomplete="off">
				</div>
				<?php
			endif;
		}

		/**
		 * Set custom field properties. Can be used by add/edit custom field.
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param string            $field_class - class of field category.
		 * @return void
		 */
		public static function set_cf_properties( $cf, $field_class ) {

			// extra info.
			if ( in_array( 'extra_info', $field_class::$allowed_properties ) ) {
				$cf->extra_info = isset( $_POST['extra_info'] ) ? sanitize_text_field( wp_unslash( $_POST['extra_info'] ) ) : ''; // phpcs:ignore
			}

			// default value.
			if ( in_array( 'default_value', $field_class::$allowed_properties ) ) {
				$cf->default_value = isset( $_POST['default_value'] ) ? array_filter( array_map( 'intval', $_POST['default_value'] ) ) : array(); // phpcs:ignore
			}

			// auto fill.
			if ( in_array( 'is_auto_fill', $field_class::$allowed_properties ) ) {
				$cf->is_auto_fill = isset( $_POST['is_auto_fill'] ) ? sanitize_text_field( wp_unslash( $_POST['is_auto_fill'] ) ) : ''; // phpcs:ignore
			}

			// placeholder!
			if ( in_array( 'placeholder_text', $field_class::$allowed_properties ) ) {
				$cf->placeholder_text = isset( $_POST['placeholder_text'] ) ? sanitize_text_field( wp_unslash( $_POST['placeholder_text'] ) ) : ''; // phpcs:ignore
			}

			// tl_width!
			if ( in_array( 'tl_width', $field_class::$allowed_properties ) ) {
				$tl_width     = isset( $_POST['tl_width'] ) ? intval( $_POST['tl_width'] ) : 0; // phpcs:ignore
				$cf->tl_width = $tl_width ? $tl_width : 100;
			}

			// save!
			$cf->save();

			// update widget title!
			$ticket_widgets                      = get_option( 'wpsc-ticket-widget', array() );
			$ticket_widgets['assignee']['title'] = $cf->name;
			update_option( 'wpsc-ticket-widget', $ticket_widgets );
		}

		/**
		 * Assign default agent to ticket if not already assigned
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return void
		 */
		public static function assign_default_agent( $ticket ) {

			if ( ! $ticket->assigned_agent ) {
				$agents                 = self::get_default_value( WPSC_Custom_Field::get_cf_by_slug( 'assigned_agent' ) );
				$agents                 = $agents ? explode( '|', $agents ) : array();
				$ticket->assigned_agent = $agents;
				$ticket->save();
			}
		}

		/**
		 * Returns printable ticket value for custom field. Can be used in export tickets, replace macros etc.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @param string            $module - module name.
		 * @return string
		 */
		public static function get_ticket_field_val( $cf, $ticket, $module = '' ) {

			$agents      = $ticket->{$cf->slug};
			$agent_names = array_filter(
				array_map(
					fn( $agent) => $agent->id ? $agent->name : '',
					$agents
				)
			);
			$value = $agent_names ? implode( ', ', $agent_names ) : esc_attr__( 'None', 'supportcandy' );

			return apply_filters( 'wpsc_ticket_field_val_assigned_agent', $value, $cf, $ticket, $module );
		}

		/**
		 * Print ticket value for given custom field on ticket list
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_tl_ticket_field_val( $cf, $ticket ) {

			echo esc_attr( self::get_ticket_field_val( $cf, $ticket ) );
		}

		/**
		 * Print ticket value for given custom field on widget
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_widget_ticket_field_val( $cf, $ticket ) {

			$agents = array_filter(
				array_map(
					fn( $agent) => $agent->id ? $agent : '',
					$ticket->{$cf->slug}
				)
			);

			if ( $agents ) {

				foreach ( $agents as $agent ) :
					?>
					<div class="user-list-item">
						<?php
						$email = ! $agent->is_agentgroup ? $agent->customer->email : '';
						echo get_avatar( $email, 40 );
						?>
						<div class="ul-body">
							<div class="ul-label"><?php echo esc_attr( $agent->name ); ?></div>
						</div>
					</div>
					<?php
				endforeach;

			} else {

				?>
				<div class="wpsc-widget-default"><?php esc_attr_e( 'None', 'supportcandy' ); ?></div>
				<?php
			}
		}

		/**
		 * Print given value for custom field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and print.
		 * @return void
		 */
		public static function print_val( $cf, $val ) {

			$val         = is_array( $val ) ? $val : array_filter( explode( '|', $val ) );
			$agents      = array_filter(
				array_map(
					function ( $agent ) {
						if ( is_object( $agent ) ) {
							return $agent->id ? $agent : '';
						} elseif ( $agent ) {
							$agent = new WPSC_Agent( $agent );
							return $agent->id ? $agent : '';
						} else {
							return '';
						}
					},
					$val
				)
			);
			$agent_names = array_filter(
				array_map(
					fn( $agent) => $agent->id ? $agent->name : '',
					$agents
				)
			);
			echo $agent_names ? esc_attr( implode( ', ', $agent_names ) ) : esc_attr__( 'None', 'supportcandy' );
		}

		/**
		 * Return printable value for history log macro
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and return.
		 * @return string
		 */
		public static function get_history_log_val( $cf, $val ) {

			ob_start();
			self::print_val( $cf, $val );
			return ob_get_clean();
		}

		/**
		 * Agent autocomplete for filter access
		 *
		 * @return void
		 */
		public static function agent_autocomplete_assigned_agent() {

			if ( check_ajax_referer( 'wpsc_agent_autocomplete_assigned_agent', '_ajax_nonce', false ) !== 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$current_user = WPSC_Current_User::$current_user;
			if ( $current_user->is_agent ) {
				$filter_items = get_option( 'wpsc-atl-filter-items', array() );
			} elseif ( $current_user->is_customer && ! $current_user->is_agent ) {
				$filter_items = get_option( 'wpsc-ctl-filter-items', array() );
			}

			if ( ! ( in_array( 'assigned_agent', $filter_items ) || WPSC_Functions::is_site_admin() ) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$filters = array(
				'term'       => isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
				'filter_by'  => 'all',
				'sort_by'    => 'name',
				'isMultiple' => 0,
			);

			$filters['isAgentgroup'] = 0;
			if ( class_exists( 'WPSC_Agentgroups' ) ) {
				$filters['isAgentgroup'] = isset( $_GET['isAgentgroup'] ) ? intval( $_GET['isAgentgroup'] ) : null;
			}

			$response = WPSC_Agent::agent_autocomplete( $filters );
			wp_send_json( $response );
		}

		/**
		 * Agent autocomplete for filter access
		 *
		 * @return void
		 */
		public static function agent_autocomplete_df_aa() {

			if ( check_ajax_referer( 'wpsc_agent_autocomplete_df_aa', '_ajax_nonce', false ) !== 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$tff = get_option( 'wpsc-tff', array() );

			$allowed_user = $tff['assigned_agent']['allowed_user'];

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				( $current_user->is_agent && in_array( $allowed_user, array( 'agent', 'both' ) ) ) ||
				( ! $current_user->is_agent && in_array( $allowed_user, array( 'customer', 'both' ) ) )
			) ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$filters = array(
				'term'       => isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '',
				'filter_by'  => 'all',
				'sort_by'    => 'name',
				'isMultiple' => 0,
			);

			$filters['isAgentgroup'] = 0;
			if ( class_exists( 'WPSC_Agentgroups' ) ) {
				$filters['isAgentgroup'] = isset( $_GET['isAgentgroup'] ) ? intval( $_GET['isAgentgroup'] ) : null;
			}

			$response = WPSC_Agent::agent_autocomplete( $filters );
			wp_send_json( $response );
		}

		/**
		 * Rest api filter for ticket data to prevent from sending it to client
		 *
		 * @param array $data - array of slugs to prevent from.
		 * @return array
		 */
		public static function rest_prevent_ticket_data( $data ) {

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				$tff = get_option( 'wpsc-tff' );
				$list_items = $current_user->get_tl_list_items();
				$widgets = get_option( 'wpsc-ticket-widget' );
				if ( ! (
					isset( $tff['assigned_agent'] ) ||
					in_array( 'assigned_agent', $list_items ) ||
					(
						$widgets['assignee']['is_enable'] &&
						$widgets['assignee']['allow-customer']
					)
				) ) {
					$data[] = 'assigned_agent';
				}
			}
			return $data;
		}
	}
endif;

WPSC_DF_Assigned_Agent::init();
