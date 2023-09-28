<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_CF_Single_Select' ) ) :

	final class WPSC_CF_Single_Select {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'cf_single_select';

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
		public static $has_multiple_val = false;

		/**
		 * Data type for column created in tickets table
		 *
		 * @var string
		 */
		public static $data_type = 'TINYTEXT NULL DEFAULT NULL';

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
		public static $ref_class = 'wpsc_option';

		/**
		 * Set whether this custom field field type is system default (no fields can be created from it).
		 *
		 * @var boolean
		 */
		public static $is_default = false;

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
		public static $has_personal_info = true;

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
		public static $has_options = true;

		/**
		 * Set whether fields created from this custom field type can be available for ticket list sorting
		 *
		 * @var boolean
		 */
		public static $is_sort = true;

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
		public static $is_search = true;

		/**
		 * Initialize the class
		 *
		 * @return void
		 */
		public static function init() {

			// Get object of this class.
			add_filter( 'wpsc_load_ref_classes', array( __CLASS__, 'load_ref_class' ) );

			// Set custom field type.
			add_filter( 'wpsc_cf_types', array( __CLASS__, 'add_cf_type' ), 2 );

			// ticket form.
			add_action( 'wpsc_js_validate_ticket_form', array( __CLASS__, 'js_validate_ticket_form' ) );
			add_filter( 'wpsc_create_ticket_data', array( __CLASS__, 'set_create_ticket_data' ), 10, 3 );

			// create ticket data for rest api.
			add_filter( 'wpsc_rest_create_ticket', array( __CLASS__, 'set_rest_ticket_data' ), 10, 3 );

			// Ticket model.
			add_filter( 'wpsc_ticket_joins', array( __CLASS__, 'ticket_join' ), 9, 2 );

			// ticket search query.
			add_filter( 'wpsc_ticket_search', array( __CLASS__, 'ticket_search' ), 10, 5 );
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
		 * Add custom field type to list
		 *
		 * @param array $cf_types - custom field types array.
		 * @return array
		 */
		public static function add_cf_type( $cf_types ) {

			$cf_types[ self::$slug ] = array(
				'label' => esc_attr__( 'Dropdown (single-select)', 'supportcandy' ),
				'class' => __CLASS__,
			);
			return $cf_types;
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
			$options     = $cf->get_options();
			$unique_id   = uniqid( 'wpsc_' );
			?>
			<div class="item conditional operand single">
				<select class="operand_val_1 <?php echo esc_attr( $unique_id ); ?>" <?php echo $is_multiple ? 'multiple' : ''; ?>>
					<?php
					foreach ( $options as $option ) {
						$selected = '';
						if ( isset( $filter['operand_val_1'] ) && ( ( $is_multiple && in_array( $option->id, $filter['operand_val_1'] ) ) || ( ! $is_multiple && $option->id == $filter['operand_val_1'] ) ) ) {
							$selected = 'selected="selected"';
						}
						?>
						<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $option->id ); ?>"><?php echo esc_attr( $option->name ); ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<script>jQuery('.operand_val_1.<?php echo esc_attr( $unique_id ); ?>').selectWoo();</script>
			<?php
		}

		/**
		 * Check condition for this type
		 *
		 * @param array             $condition - condition data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $option - value to compare.
		 * @return boolean
		 */
		public static function is_valid( $condition, $cf, $option ) {

			$response = false;
			switch ( $condition['operator'] ) {

				case '=':
					$response = $condition['operand_val_1'] == $option ? true : false;
					break;

				case 'IN':
					$response = in_array( $option, $condition['operand_val_1'] ) ? true : false;
					break;

				case 'NOT IN':
					$response = ! in_array( $option, $condition['operand_val_1'] ) ? true : false;
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
					$str = self::get_sql_slug( $cf ) . '=\'' . esc_sql( $val ) . '\'';
					break;

				case 'IN':
					$str = 'CONVERT(' . self::get_sql_slug( $cf ) . ' USING utf8) IN(\'' . implode( '\', \'', esc_sql( $val ) ) . '\')';
					break;

				case 'NOT IN':
					$str = 'CONVERT(' . self::get_sql_slug( $cf ) . ' USING utf8) NOT IN(\'' . implode( '\', \'', esc_sql( $val ) ) . '\')';
					break;

				default:
					$str = '1=1';
			}

			return $str;
		}

		/**
		 * Return orderby string
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return string
		 */
		public static function get_orderby_string( $cf ) {

			return 'op.name';
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

			if ( ! is_object( $ticket->{$cf->slug} ) ) {
				return false;
			}

			$flag = true;

			switch ( $condition['operator'] ) {

				case '=':
					$flag = $ticket->{$cf->slug}->id == $condition['operand_val_1'] ? true : false;
					break;

				case 'IN':
					$flag = in_array( $ticket->{$cf->slug}->id, $condition['operand_val_1'] );
					break;

				case 'NOT IN':
					$flag = ! in_array( $ticket->{$cf->slug}->id, $condition['operand_val_1'] );
					break;

				default:
					$flag = true;
			}

			return $flag;
		}

		/**
		 * Check customer condition
		 *
		 * @param array             $condition - array with condition data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @return boolean
		 */
		public static function is_valid_customer_condition( $condition, $cf, $customer ) {

			if ( ! is_object( $customer->{$cf->slug} ) ) {
				return false;
			}

			$flag = true;

			switch ( $condition['operator'] ) {

				case '=':
					$flag = $customer->{$cf->slug}->id == $condition['operand_val_1'] ? true : false;
					break;

				case 'IN':
					$flag = in_array( $customer->{$cf->slug}->id, $condition['operand_val_1'] );
					break;

				case 'NOT IN':
					$flag = ! in_array( $customer->{$cf->slug}->id, $condition['operand_val_1'] );
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

			$options = $cf->get_options();
			$val     = '';
			if ( $cf->field == 'ticket' ) {
				$val = $cf->is_auto_fill && $cf->default_value ? stripslashes( htmlspecialchars( $cf->default_value[0] ) ) : '';
			} else {
				$current_user = WPSC_Current_User::$current_user;
				$val          = $current_user->is_customer && $current_user->customer->{$cf->slug} ? intval( $current_user->customer->{$cf->slug}->id ) : 0;
			}
			$unique_id = uniqid( 'wpsc_' );

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
				<select class="<?php echo esc_attr( $unique_id ); ?>" name="<?php echo esc_attr( $cf->slug ); ?>" onchange="wpsc_check_tff_visibility()">
					<option value=""></option>
					<?php
					foreach ( $options as $option ) :
						?>
						<option <?php selected( $option->id, $val ); ?> value="<?php echo esc_attr( $option->id ); ?>"><?php echo esc_attr( $option->name ); ?></option>
						<?php
					endforeach;
					?>
				</select>
				<script>
					jQuery('select.<?php echo esc_attr( $unique_id ); ?>').selectWoo({
						allowClear: true,
						placeholder: "<?php echo esc_attr( $cf->placeholder_text ); ?>"    
					});
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

			return isset( $_POST[ $slug ] ) ? intval( $_POST[ $slug ] ) : 0; // phpcs:ignore
		}

		/**
		 * Return default value for custom field of this type
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return mixed
		 */
		public static function get_default_value( $cf ) {

			return $cf->default_value ? $cf->default_value[0] : '';
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

			if ( isset( $custom_fields[ self::$slug ] ) ) {
				foreach ( $custom_fields[ self::$slug ] as $cf ) {
					$value = self::get_tff_value( $cf->slug );
					if ( $cf->field == 'ticket' ) {

						$default_val       = self::get_default_value( $cf );
						$data[ $cf->slug ] = $value ? $value : $default_val;

					} elseif ( $cf->field == 'agentonly' ) {

						$data[ $cf->slug ] = self::get_default_value( $cf );

					} elseif ( $cf->field == 'customer' && $data['customer'] != 0 ) {

						$tff = get_option( 'wpsc-tff' );
						if ( ! $is_my_profile && ! isset( $tff[ $cf->slug ] ) ) {
							continue;
						}
						$customer     = new WPSC_Customer( $data['customer'] );
						$existing_val = $customer->{$cf->slug} ? $customer->{$cf->slug}->id : '';
						$value        = $value === 0 ? '' : $value;
						if ( $existing_val != $value ) {
							$customer->{$cf->slug} = $value;
							$customer->save();

							// Set log for this change.
							WPSC_Log::insert(
								array(
									'type'         => 'customer',
									'ref_id'       => $customer->id,
									'modified_by'  => WPSC_Current_User::$current_user->customer->id,
									'body'         => wp_json_encode(
										array(
											'slug' => $cf->slug,
											'prev' => $existing_val,
											'new'  => $value,
										)
									),
									'date_created' => ( new DateTime() )->format( 'Y-m-d H:i:s' ),
								)
							);
						}
					}
				}
			}
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

			if ( isset( $custom_fields[ self::$slug ] ) ) {
				foreach ( $custom_fields[ self::$slug ] as $cf ) {

					if (
						! in_array( $cf->field, array( 'ticket', 'agentonly', 'customer' ) ) ||
						( $cf->field == 'customer' && ! isset( $tff[ $cf->slug ] ) )
					) {
						continue;
					}

					$option = new WPSC_Option( intval( $request->get_param( $cf->slug ) ) );

					if ( in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ) {

						$data[ $cf->slug ] = $cf->field == 'ticket' && $option->id ? $option->id : self::get_default_value( $cf );

					} else {

						$customer = new WPSC_Customer( $data['customer'] );
						$existing_val = $customer->{$cf->slug};

						if ( $option->id && $option->id != $existing_val->id ) {

							$customer->{$cf->slug} = $option;
							$customer->save();

							// Set log for this change..
							WPSC_Log::insert(
								array(
									'type'         => 'customer',
									'ref_id'       => $customer->id,
									'modified_by'  => $current_user->customer->id,
									'body'         => wp_json_encode(
										array(
											'slug' => $cf->slug,
											'prev' => $existing_val->id,
											'new'  => $option->id,
										)
									),
									'date_created' => ( new DateTime() )->format( 'Y-m-d H:i:s' ),
								)
							);
						}
					}
				}
			}

			return $data;
		}

		/**
		 * Return slug string to be used in where condition of ticket model for this type of field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return string
		 */
		public static function get_sql_slug( $cf ) {

			$join_char = in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ? 't.' : 'c.';
			return $join_char . $cf->slug;
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
		 * Print generic input field for this type.
		 *
		 * @param WPSC_Custom_Field $cf - Custom field object.
		 * @param mixed             $value - optional. Input field will be printed with given value if given.
		 * @return void
		 */
		public static function print_cf_input( $cf, $value = '' ) {

			if ( $value && ! is_object( $value ) ) {

				$option = new WPSC_Option( $value );
				if ( $option && $option->id ) {
					$value = $option;
				}
			}

			$options   = $cf->get_options();
			$unique_id = uniqid( 'wpsc_' );
			?>
			<div class="wpsc-tff wpsc-sm-12 wpsc-md-12 wpsc-lg-12 wpsc-visible wpsc-xs-12" data-cft="<?php echo esc_attr( self::$slug ); ?>">
				<div class="wpsc-tff-label">
					<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
				</div>
				<?php
				$extra_info = stripslashes( $cf->extra_info );
				if ( $extra_info ) :
					?>
					<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>
					<?php
				endif
				?>
				<select class="<?php echo esc_attr( $unique_id ); ?>" name="<?php echo esc_attr( $cf->slug ); ?>">
					<option value=""></option>
					<?php
					foreach ( $options as $option ) {
						?>
						<option <?php $value && selected( $option->id, $value->id ); ?> value="<?php echo esc_attr( $option->id ); ?>"><?php echo esc_attr( $option->name ); ?></option>
						<?php
					}
					?>
				</select>
				<script>
					jQuery('select.<?php echo esc_attr( $unique_id ); ?>').selectWoo({
						allowClear: true,
						placeholder: ""
					});
				</script>
			</div>
			<?php
		}

		/**
		 * Get value for generic field from post.
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return string
		 */
		public static function get_cf_input_val( $cf ) {

			return isset( $_POST[ $cf->slug ] ) ? intval( $_POST[ $cf->slug ] ) : ''; // phpcs:ignore
		}

		/**
		 * Print edit ticket custom field in individual ticket
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_edit_ticket_cf( $cf, $ticket ) {

			$options   = $cf->get_options();
			$unique_id = uniqid( 'wpsc_' );
			?>
			<div class="wpsc-tff wpsc-sm-12 wpsc-md-12 wpsc-lg-12 wpsc-visible wpsc-xs-12" data-cft="<?php echo esc_attr( self::$slug ); ?>">
				<div class="wpsc-tff-label">
					<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
				</div>
				<?php
				$extra_info = stripslashes( $cf->extra_info );
				if ( $extra_info ) {
					?>
					<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>
					<?php
				}
				?>
				<select class="<?php echo esc_attr( $unique_id ); ?>" name="<?php echo esc_attr( $cf->slug ); ?>">
					<option value=""></option>
					<?php
					foreach ( $options as $option ) {
						?>
						<option <?php $ticket->{$cf->slug} && selected( $option->id, $ticket->{$cf->slug}->id ); ?> value="<?php echo esc_attr( $option->id ); ?>"><?php echo esc_attr( $option->name ); ?></option>
						<?php
					}
					?>
				</select>
				<script>
					jQuery('select.<?php echo esc_attr( $unique_id ); ?>').selectWoo({
						allowClear: true,
						placeholder: "<?php echo esc_attr( $cf->placeholder_text ); ?>"
					});
				</script>
			</div>
			<?php
		}

		/**
		 * Set edit individual ticket for this custom field type.
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return WPSC_Ticket
		 */
		public static function set_edit_ticket_cf( $cf, $ticket ) {

			$prev = $ticket->{$cf->slug} ? $ticket->{$cf->slug}->id : '';
			$new  = isset( $_POST[ $cf->slug ] ) ? intval( $_POST[ $cf->slug ] ) : ''; // phpcs:ignore
			$new  = $new === 0 ? '' : $new;

			// Exit if there is no change.
			if ( $prev == $new ) {
				return $ticket;
			}

			// Change value.
			$ticket->{$cf->slug} = $new;
			$ticket->save();

			return $ticket;
		}

		/**
		 * Modify ticket field value of this custom field type using rest api
		 *
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @param WPSC_Custom_Field $cf - custom field.
		 * @param mixed             $value - value to be set.
		 * @return void
		 */
		public static function set_rest_edit_ticket_cf( $ticket, $cf, $value ) {

			$id = WPSC_Functions::sanitize_option( intval( $value ), $cf, $cf->get_options() );
			$prev = is_object( $ticket->{$cf->slug} ) ? $ticket->{$cf->slug}->id : 0;
			if ( $prev != $id ) {
				$ticket->{$cf->slug} = $id ? $id : '';
			}
		}

		/**
		 * Insert log thread for this custom field type change
		 *
		 * @param WPSC_Custom_Field $cf - current custom field of this type.
		 * @param WPSC_Ticket       $prev - ticket object before making any changes.
		 * @param WPSC_Ticket       $new - ticket object after making changes.
		 * @param string            $current_date - date string to be stored as create time.
		 * @param int               $customer_id - current user customer id for blame.
		 * @return void
		 */
		public static function insert_ticket_log( $cf, $prev, $new, $current_date, $customer_id ) {

			// Exit if there is no change.
			if ( $prev->{$cf->slug} == $new->{$cf->slug} ) {
				return;
			}

			$prev_val = $prev->{$cf->slug} ? $prev->{$cf->slug}->id : '';
			$new_val  = $new->{$cf->slug} ? $new->{$cf->slug}->id : '';

			$thread = WPSC_Thread::insert(
				array(
					'ticket'       => $prev->id,
					'customer'     => $customer_id,
					'type'         => 'log',
					'body'         => wp_json_encode(
						array(
							'slug' => $cf->slug,
							'prev' => $prev_val,
							'new'  => $new_val,
						)
					),
					'date_created' => $current_date,
					'date_updated' => $current_date,
				)
			);
		}

		/**
		 * Return data for this custom field while creating duplicate ticket
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return mixed
		 */
		public static function get_duplicate_ticket_data( $cf, $ticket ) {

			return $ticket->{$cf->slug} ? $ticket->{$cf->slug}->id : '';
		}

		/**
		 * Print edit field for this type in edit customer info
		 *
		 * @param WPSC_Custom_field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @param array             $tff - ticket form field data.
		 * @return string
		 */
		public static function print_edit_customer_info( $cf, $customer, $tff ) {

			$options   = $cf->get_options();
			$val       = $customer->id && $customer->{$cf->slug} ? intval( $customer->{$cf->slug}->id ) : 0;
			$unique_id = uniqid( 'wpsc_' );

			ob_start();
			?>
			<div class="<?php echo esc_attr( WPSC_Functions::get_tff_classes( $cf, $tff ) ); ?>" data-cft="<?php echo esc_attr( self::$slug ); ?>">
				<div class="wpsc-tff-label">
					<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
				</div>
				<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>
				<select class="<?php echo esc_attr( $unique_id ); ?>" name="<?php echo esc_attr( $cf->slug ); ?>">
					<option value=""></option>
					<?php
					foreach ( $options as $option ) :
						?>
						<option <?php selected( $option->id, $val ); ?> value="<?php echo esc_attr( $option->id ); ?>"><?php echo esc_attr( $option->name ); ?></option>
						<?php
					endforeach;
					?>
				</select>
				<script>
					jQuery('select.<?php echo esc_attr( $unique_id ); ?>').selectWoo({
						allowClear: false,
						placeholder: "<?php echo esc_attr( $cf->placeholder_text ); ?>"
					});
				</script>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Print add new custom field setting properties
		 *
		 * @param string $field_class - Class name of the field.
		 * @return void
		 */
		public static function get_add_new_custom_field_properties( $field_class ) {

			if ( in_array( 'extra_info', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="textfield" data-required="false" class="wpsc-input-group extra-info">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Extra info', 'supportcandy' ); ?></label>
					</div>
					<input name="extra_info" type="text" autocomplete="off" />
				</div>
				<?php
			endif;
			?>

			<div data-type="select-options" data-required="true" class="wpsc-input-group options">
				<div class="label-container">
					<label for="">
						<?php esc_attr_e( 'Field options', 'supportcandy' ); ?> 
						<span class="required-char">*</span>
					</label>
				</div>
				<div class="wpsc-options-container ui-sortable"></div>
				<script>jQuery(".wpsc-options-container").sortable({ handle: '.wpsc-sort-handle' });</script>
				<button class="wpsc-button small secondary" onclick="wpsc_get_add_new_option();">
					<?php esc_attr_e( 'Add new', 'supportcandy' ); ?>
				</button>
			</div>
			<?php

			if ( in_array( 'default_value', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group default-val-option-single">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Default value', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="default-val-option-single[]" id="wpsc-default-val-option-single">
						<option value=""></option>
					</select>
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
						<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
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
					<input type="text" name="placeholder_text" autocomplete="off">
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
					<input type="number" name="tl_width" autocomplete="off">
				</div>
				<?php
			endif;

			if ( in_array( 'is_personal_info', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group is_personal_info">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Has personal info', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="is_personal_info">
						<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;

			if ( in_array( 'allow_my_profile', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group allow_my_profile">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Allow in my profile?', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="allow_my_profile">
						<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;

			if ( in_array( 'allow_ticket_form', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group allow_ticket_form">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Allow in ticket form?', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="allow_ticket_form">
						<option value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;
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
			?>

			<div data-type="select-options" data-required="true" class="wpsc-input-group options">
				<div class="label-container">
					<label for="">
						<?php esc_attr_e( 'Field options', 'supportcandy' ); ?> 
						<span class="required-char">*</span>
					</label>
				</div>
				<div class="wpsc-options-container ui-sortable">
				<?php
					$options = $cf->get_options();
				foreach ( $options as $option ) :
					?>
						<div class="wpsc-option-item">
							<div class="content">
								<div class="wpsc-edit-option-container" style="display: none;">
									<input class="edit-option-text" type="text" value="" autocomplete="off" />
									<button onclick="wpsc_set_edit_option(this, '<?php echo esc_attr( wp_create_nonce( 'wpsc_set_edit_option' ) ); ?>');">
										<?php WPSC_Icons::get( 'check' ); ?>
									</button>
									<button class="cancel" onclick="wpsc_edit_option_cancel(this);">
										<?php WPSC_Icons::get( 'times' ); ?>
									</button>
								</div>
								<div class="wpsc-option-listing-container">
									<span class="sort wpsc-sort-handle"><?php WPSC_Icons::get( 'sort' ); ?></span>
									<div class="text"><?php echo esc_attr( $option->name ); ?></div>
									<span class="edit" onclick="wpsc_edit_option(this);"><?php WPSC_Icons::get( 'edit' ); ?></span>
								</div>
							</div>
							<div class="remove-container">
								<span onclick="wpsc_remove_option_item(this)"><?php WPSC_Icons::get( 'times-circle' ); ?></span>
							</div>
							<input type="hidden" class="option_id" name="options[]" value="<?php echo esc_attr( $option->id ); ?>">
						</div>
						<?php
					endforeach;
				?>
				</div>
				<script>jQuery(".wpsc-options-container").sortable({ handle: '.wpsc-sort-handle' });</script>
				<button class="wpsc-button small secondary" onclick="wpsc_get_add_new_option();">
					<?php esc_attr_e( 'Add new', 'supportcandy' ); ?>
				</button>
			</div>
			<?php

			if ( in_array( 'default_value', $field_class::$allowed_properties ) ) :
				$default_val = $cf->default_value ? $cf->default_value[0] : '';
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group default_value">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Default value', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="default-val-option-single[]" id="wpsc-default-val-option-single">
						<option value=""></option>
						<?php
						$options = $cf->get_options();
						foreach ( $options as $option ) :
							echo '<option ' . selected( $default_val, $option->id ) . ' value=' . esc_attr( $option->id ) . '>' . esc_attr( $option->name ) . '</option>';
						endforeach;
						?>
					</select>
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
					<input type="number" name="tl_width" value="<?php echo intval( $cf->tl_width ); ?>" autocomplete="off">
				</div>
				<?php
			endif;

			if ( in_array( 'is_personal_info', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group is_personal_info">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Has personal info', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="is_personal_info">
						<option <?php selected( $cf->is_personal_info, '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option <?php selected( $cf->is_personal_info, '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;

			if ( in_array( 'allow_my_profile', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group allow_my_profile">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Allow in my profile?', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="allow_my_profile">
						<option <?php selected( $cf->allow_my_profile, '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option <?php selected( $cf->allow_my_profile, '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
				</div>
				<?php
			endif;

			if ( in_array( 'allow_ticket_form', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group allow_ticket_form">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Allow in ticket form?', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="allow_ticket_form">
						<option <?php selected( $cf->allow_ticket_form, '0' ); ?> value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
						<option <?php selected( $cf->allow_ticket_form, '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
					</select>
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

			// options.
			$options = isset( $_POST['options'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash($_POST['options']) ) ) : array(); // phpcs:ignore
			if ( ! $options ) {
				wp_send_json_error( new WP_Error( '001', 'Bad request!' ), 400 );
			}

			// default value.
			if ( in_array( 'default_value', $field_class::$allowed_properties ) ) {
				$cf->default_value = isset( $_POST['default-val-option-single'] ) ? array_filter( array_map( 'intval', $_POST['default-val-option-single'] ) ) : array(); // phpcs:ignore
			}

			// auto fill.
			if ( in_array( 'is_auto_fill', $field_class::$allowed_properties ) ) {
				$cf->is_auto_fill = isset( $_POST['is_auto_fill'] ) ? sanitize_text_field( wp_unslash( $_POST['is_auto_fill'] ) ) : ''; // phpcs:ignore
			}

			// placeholder!
			if ( in_array( 'placeholder_text', $field_class::$allowed_properties ) ) {
				$cf->placeholder_text = isset( $_POST['placeholder_text'] ) ? sanitize_text_field( wp_unslash( $_POST['placeholder_text'] ) ) : ''; // phpcs:ignore
			}

			// personal info.
			if ( in_array( 'is_personal_info', $field_class::$allowed_properties ) ) {
				$cf->is_personal_info = isset( $_POST['is_personal_info'] ) ? intval( $_POST['is_personal_info'] ) : 0; // phpcs:ignore
			}

			// my-profile.
			if ( in_array( 'allow_my_profile', $field_class::$allowed_properties ) ) {
				$cf->allow_my_profile = isset( $_POST['allow_my_profile'] ) ? intval( $_POST['allow_my_profile'] ) : 0; // phpcs:ignore
			}

			// ticket form.
			if ( in_array( 'allow_ticket_form', $field_class::$allowed_properties ) ) {
				$cf->allow_ticket_form = isset( $_POST['allow_ticket_form'] ) ? intval( $_POST['allow_ticket_form'] ) : 0; // phpcs:ignore
			}

			// tl_width!
			if ( in_array( 'tl_width', $field_class::$allowed_properties ) ) {
				$tl_width     = isset( $_POST['tl_width'] ) ? intval( $_POST['tl_width'] ) : 0; // phpcs:ignore
				$cf->tl_width = $tl_width ? $tl_width : 100;
			}

			// save!
			$cf->save();
			$cf->set_options( $options, true );
		}

		/**
		 * Add SQL joins to ticket model for this field type
		 *
		 * @param array $joins - array of join string that can be imploded later.
		 * @param array $filter - user filter.
		 * @return array
		 */
		public static function ticket_join( $joins, $filter ) {

			global $wpdb;

			$cf = WPSC_Custom_Field::get_cf_by_slug( $filter['orderby_slug'] );

			if ( $cf && $cf->type::$slug == 'cf_single_select' ) {
				$join_str = self::get_sql_slug( $cf );
				$joins[]  = 'LEFT JOIN ' . $wpdb->prefix . 'psmsc_options op ON ' . $join_str . ' = op.id';
			}

			return $joins;
		}

		/**
		 * Add ticket search compatibility for fields of this custom field type.
		 *
		 * @param array  $sql - Array of sql peices that can be joined later.
		 * @param array  $filter - User filter.
		 * @param array  $custom_fields - Custom fields array applicable for search.
		 * @param string $search - search string.
		 * @param array  $allowed_search_fields - Allowed search fields.
		 * @return array
		 */
		public static function ticket_search( $sql, $filter, $custom_fields, $search, $allowed_search_fields ) {

			if ( isset( $custom_fields[ self::$slug ] ) ) {

				$search_items = false;
				foreach ( $custom_fields[ self::$slug ] as $cf ) {
					if ( in_array( $cf->slug, $allowed_search_fields ) ) {
						if ( ! $search_items ) {
							$search_items = WPSC_Option::get_tl_search_string( $search );
							if ( ! $search_items ) {
								break;
							}
						}
						$join_char = in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ? 't.' : 'c.';
						$sql[]     = $join_char . $cf->slug . ' IN(' . implode( ', ', $search_items ) . ')';
					}
				}
			}

			return $sql;
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

			$option = $ticket->{$cf->slug};
			$value = is_object( $option ) && $option->id ? $option->name : '';
			return apply_filters( 'wpsc_ticket_field_val_single_select', $value, $cf, $ticket, $module );
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

			echo esc_attr( self::get_ticket_field_val( $cf, $ticket ) );
		}

		/**
		 * Returns printable customer value for custom field. Can be used in export tickets, replace macros etc.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @return string
		 */
		public static function get_customer_field_val( $cf, $customer ) {

			$option = $customer->{$cf->slug};
			return is_object( $option ) && $option->id ? $option->name : '';
		}

		/**
		 * Print customer value for given custom field on ticket list
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @return void
		 */
		public static function print_tl_customer_field_val( $cf, $customer ) {

			echo esc_attr( self::get_customer_field_val( $cf, $customer ) );
		}

		/**
		 * Print customer value for given custom field on widget
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @return void
		 */
		public static function print_widget_customer_field_val( $cf, $customer ) {

			echo esc_attr( self::get_customer_field_val( $cf, $customer ) );
		}

		/**
		 * Print given value for custom field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and print.
		 * @return void
		 */
		public static function print_val( $cf, $val ) {

			$option = is_object( $val ) ? $val : new WPSC_Option( $val );
			echo $option->id ? esc_attr( $option->name ) : esc_attr__( 'None', 'supportcandy' );
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
	}
endif;

WPSC_CF_Single_Select::init();
