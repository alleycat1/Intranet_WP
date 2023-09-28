<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_Status' ) ) :

	final class WPSC_DF_Status {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_status';

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
		public static $data_type = 'INT NOT NULL';

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
		public static $ref_class = 'wpsc_status';

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
		public static $is_ctf = false;

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
		public static $is_sort = true;

		/**
		 * Set whether fields created from this custom field type can be auto-filled
		 *
		 * @var boolean
		 */
		public static $is_auto_fill = false;

		/**
		 * Set whether fields created from this custom field type can have placeholder
		 *
		 * @var boolean
		 */
		public static $is_placeholder = false;

		/**
		 * Set whether fields created from this custom field type is applicable for visibility conditions in create ticket form
		 *
		 * @var boolean
		 */
		public static $is_visibility_conditions = false;

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

			// ticket form.
			add_filter( 'wpsc_create_ticket_data', array( __CLASS__, 'set_create_ticket_data' ), 10, 3 );

			// create ticket data for rest api.
			add_filter( 'wpsc_rest_create_ticket', array( __CLASS__, 'set_rest_ticket_data' ), 10, 3 );

			// Ticket model.
			add_filter( 'wpsc_ticket_joins', array( __CLASS__, 'ticket_join' ), 10, 2 );

			// Individual ticket.
			add_action( 'wp_ajax_wpsc_it_close_ticket', array( __CLASS__, 'it_close_ticket' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_close_ticket', array( __CLASS__, 'it_close_ticket' ) );
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
		 * Check condition for this type
		 *
		 * @param array             $condition - condition data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $status - value to compare.
		 * @return boolean
		 */
		public static function is_valid( $condition, $cf, $status ) {

			$response = false;
			switch ( $condition['operator'] ) {

				case '=':
					$response = $condition['operand_val_1'] == $status ? true : false;
					break;

				case 'IN':
					$response = in_array( $status, $condition['operand_val_1'] ) ? true : false;
					break;

				case 'NOT IN':
					$response = ! in_array( $status, $condition['operand_val_1'] ) ? true : false;
					break;
			}
			return $response;
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
			$statuses    = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
			$unique_id   = uniqid( 'wpsc_' );
			?>
			<div class="item conditional operand single">
				<select class="operand_val_1 <?php echo esc_attr( $unique_id ); ?>" <?php echo $is_multiple ? 'multiple' : ''; ?>>
					<?php
					foreach ( $statuses as $status ) {
						$selected = '';
						if ( isset( $filter['operand_val_1'] ) && ( ( $is_multiple && in_array( $status->id, $filter['operand_val_1'] ) ) || ( ! $is_multiple && $status->id == $filter['operand_val_1'] ) ) ) {
							$selected = 'selected="selected"';
						}
						?>
						<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $status->id ); ?>"><?php echo esc_attr( $status->name ); ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<script>jQuery('.operand_val_1.<?php echo esc_attr( $unique_id ); ?>').selectWoo();</script>
			<?php
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
					$str = 't.' . $cf->slug . '=\'' . esc_sql( $val ) . '\'';
					break;

				case 'IN':
					$str = 'CONVERT(t.' . $cf->slug . ' USING utf8) IN(\'' . implode( '\', \'', esc_sql( $val ) ) . '\')';
					break;

				case 'NOT IN':
					$str = 'CONVERT(t.' . $cf->slug . ' USING utf8) NOT IN(\'' . implode( '\', \'', esc_sql( $val ) ) . '\')';
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
		 * Return default value for custom field of this type
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return mixed
		 */
		public static function get_default_value( $cf ) {

			return $cf->default_value[0];
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

			$data['status'] = self::get_default_value( $custom_fields[ self::$slug ][0] );
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

			$cf = WPSC_Custom_Field::get_cf_by_slug( 'category' );
			$data['status'] = self::get_default_value( $cf );
			return $data;
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
			if ( $filter['orderby_slug'] == 'status' ) {
				$joins[] = 'LEFT JOIN ' . $wpdb->prefix . 'psmsc_statuses st ON t.status = st.id';
			}
			return $joins;
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
		 * Close ticket
		 *
		 * @return void
		 */
		public static function it_close_ticket() {

			if ( check_ajax_referer( 'wpsc_it_close_ticket', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();
			$current_user = WPSC_Current_User::$current_user;
			$ticket       = WPSC_Individual_Ticket::$ticket;

			if ( ! $ticket->is_active ) {
				wp_send_json_error( 'Unauthorized!', 401 );
			}

			$gs          = get_option( 'wpsc-gs-general' );
			$tl_advanced = get_option( 'wpsc-tl-ms-advanced' );

			$close_flag = false;
			if (
				( $current_user->customer->id == $ticket->customer->id && in_array( 'customer', $gs['allow-close-ticket'] ) ) ||
				( $current_user->is_agent && WPSC_Individual_Ticket::has_ticket_cap( 'cs' ) && in_array( $current_user->agent->role, $gs['allow-close-ticket'] ) )
			) {
				$close_flag = true;
			}

			$close_flag = apply_filters( 'wpsc_it_action_close_flag', $close_flag, $ticket );

			if ( ! (
				$close_flag &&
				! (
					$ticket->status->id == $gs['close-ticket-status'] ||
					in_array( $ticket->status->id, $tl_advanced['closed-ticket-statuses'] )
				)
			) ) {
				wp_send_json_error( 'Unauthorized!', 401 );
			}

			WPSC_Individual_Ticket::change_status( $ticket->status->id, $gs['close-ticket-status'], $current_user->customer->id );
			wp_die();
		}

		/**
		 * Return data for this custom field while creating duplicate ticket
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return mixed
		 */
		public static function get_duplicate_ticket_data( $cf, $ticket ) {

			return $cf->default_value[0];
		}

		/**
		 * Print generic input field for this type.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Status       $value - optional. Input field will be printed with given value.
		 * @return void
		 */
		public static function print_cf_input( $cf, $value ) {

			if ( ! is_object( $value ) ) {

				$status = $value ? new WPSC_Status( $value ) : 0;
				if ( $status && $status->id ) {
					$value = $status;
				}
			}

			$statuses  = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
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
					foreach ( $statuses as $status ) {
						?>
						<option <?php $value && selected( $status->id, $value->id ); ?> value="<?php echo esc_attr( $status->id ); ?>"><?php echo esc_attr( $status->name ); ?></option>
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
		 * Get value for generic field from post.
		 * Ignore phpcs nonce issue as we already checked where it is called from.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return string
		 */
		public static function get_cf_input_val( $cf ) {

			$value  = isset( $_POST[ $cf->slug ] ) ? intval( $_POST[ $cf->slug ] ) : 0; // phpcs:ignore
			$status = $value ? new WPSC_Status( $value ) : 0;
			return $status && $status->id ? $value : '';
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
				$default_val = $cf->default_value ? $cf->default_value[0] : '';
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group default_value">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Default value', 'supportcandy' ); ?>
							<span class="required-char">*</span>
						</label>
					</div>
					<select id="wpsc-default-ts" name="default_value">
						<?php
						$statuses = WPSC_Status::find( array( 'items_per_page' => 0 ) )['results'];
						foreach ( $statuses as $status ) :
							?>
							<option <?php selected( $status->id, $default_val ); ?> value="<?php echo esc_attr( $status->id ); ?>"><?php echo esc_attr( $status->name ); ?></option>
							<?php
						endforeach;
						?>
					</select>
					<script>
						jQuery('#wpsc-default-ts').selectWoo({
							allowClear: true,
							placeholder: ""
						});
					</script>
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
				$default_value     = isset( $_POST['default_value'] ) ? intval( $_POST['default_value'] ) : 0; // phpcs:ignore
				if ( $default_value ) {
					$cf->default_value = array( $default_value );
				}
			}

			// tl_width!
			if ( in_array( 'tl_width', $field_class::$allowed_properties ) ) {
				$tl_width     = isset( $_POST['tl_width'] ) ? intval( $_POST['tl_width'] ) : 0; // phpcs:ignore
				$cf->tl_width = $tl_width ? $tl_width : 100;
			}

			// save!
			$cf->save();
		}

		/**
		 * Return orderby string
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return string
		 */
		public static function get_orderby_string( $cf ) {

			$statuses = implode(
				',',
				array_map(
					fn( $status ) => $status->id,
					WPSC_Status::find( array( 'items_per_page' => 0 ) )['results']
				)
			);
			return 'FIELD( t.status, ' . $statuses . ' )';
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

			return apply_filters( 'wpsc_ticket_field_val_status', $ticket->status->name, $cf, $ticket, $module );
		}

		/**
		 * Print ticket value for given custom field on ticket list
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_tl_ticket_field_val( $cf, $ticket ) {

			$status = $ticket->status;
			?>
			<div class="wpsc-tag" style="background-color: <?php echo esc_attr( $status->bg_color ); ?>; color:<?php echo esc_attr( $status->color ); ?>;"><?php echo esc_attr( $status->name ); ?></div>
			<?php
		}

		/**
		 * Print ticket value for given custom field on widget
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_widget_ticket_field_val( $cf, $ticket ) {

			self::print_tl_ticket_field_val( $cf, $ticket );
		}

		/**
		 * Print given value for custom field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and print.
		 * @return void
		 */
		public static function print_val( $cf, $val ) {

			$status = is_object( $val ) ? $val : new WPSC_Status( $val );
			if ( $status->id ) {
				?>
				<div class="wpsc-tag" style="background-color: <?php echo esc_attr( $status->bg_color ); ?>; color:<?php echo esc_attr( $status->color ); ?>;"><?php echo esc_attr( $status->name ); ?></div>
				<?php
			} else {
				esc_attr_e( 'None', 'supportcandy' );
			}
		}

		/**
		 * Return printable value for history log macro
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and return.
		 * @return string
		 */
		public static function get_history_log_val( $cf, $val ) {

			$status = is_object( $val ) ? $val : new WPSC_Status( $val );
			return $status->id ? $status->name : esc_attr__( 'None', 'supportcandy' );
		}
	}
endif;

WPSC_DF_Status::init();
