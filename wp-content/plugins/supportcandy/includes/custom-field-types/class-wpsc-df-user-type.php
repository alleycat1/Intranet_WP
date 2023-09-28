<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_User_Type' ) ) :

	final class WPSC_DF_User_Type {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_user_type';

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
		public static $data_type = 'VARCHAR(100) NOT NULL';

		/**
		 * Set whether this custom field type has reference to other class
		 *
		 * @var boolean
		 */
		public static $has_ref = false;

		/**
		 * Reference class for this custom field type so that its value(s) return with object or array of objects automatically. Empty string indicate no reference.
		 *
		 * @var string
		 */
		public static $ref_class = '';

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
		public static $has_extra_info = false;

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
		public static $is_sort = false;

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

			// WP user chagnes (add/delete).
			add_action( 'user_register', array( __CLASS__, 'wp_user_register' ), 11, 2 );
			add_action( 'delete_user', array( __CLASS__, 'wp_user_delete' ), 10, 3 );
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

			$unique_id = uniqid( 'wpsc_' );
			?>
			<div class="item conditional operand single">
				<select 
					class="operand_val_1 <?php echo esc_attr( $unique_id ); ?>">
					<option <?php isset( $filter['operand_val_1'] ) && selected( $filter['operand_val_1'], 'registered' ); ?> value="registered"><?php esc_attr_e( 'Registered', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operand_val_1'] ) && selected( $filter['operand_val_1'], 'guest' ); ?>  value="guest"><?php esc_attr_e( 'Guest', 'supportcandy' ); ?></option>
				</select>
			</div>
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

			switch ( $condition['operator'] ) {

				case '=':
					$flag = $condition['operand_val_1'] == $ticket->{$cf->slug} ? true : false;
					break;

				default:
					$flag = true;
			}

			return $flag;
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

			$customer          = new WPSC_Customer( $data['customer'] );
			$data['user_type'] = $customer->user ? 'registered' : 'guest';
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

			$customer          = new WPSC_Customer( $data['customer'] );
			$data['user_type'] = $customer->user ? 'registered' : 'guest';
			return $data;
		}

		/**
		 * Return val field for meta query of this type of custom field
		 *
		 * @param array $condition - condition data.
		 * @return mixed
		 */
		public static function get_meta_value( $condition ) {

			return $condition['operand_val_1'];
		}

		/**
		 * Change user type from "guest" to "registered" for all his exising guest tickets (if any)
		 *
		 * @param int   $user_id - user id of user being registered.
		 * @param array $user_data - user data such as name, email, etc.
		 * @return void
		 */
		public static function wp_user_register( $user_id, $user_data ) {

			global $wpdb;

			$customer = WPSC_Customer::get_by_user_id( $user_id );

			// update customer_type in all of his tickets.
			$success = $wpdb->update(
				$wpdb->prefix . 'psmsc_tickets',
				array( 'user_type' => 'registered' ),
				array( 'customer' => $customer->id )
			);
		}

		/**
		 * Change user type from "registered" to "guest" for all his exising guest tickets (if any)
		 *
		 * @param int     $user_id - user id of user being deleted.
		 * @param int     $reassign - user id of user this user being reassigned to.
		 * @param WP_User $user - wp user object.
		 * @return void
		 */
		public static function wp_user_delete( $user_id, $reassign, $user ) {

			global $wpdb;

			$customer = WPSC_Customer::get_by_email( $user->user_email );
			if ( $customer->id ) {

				$customer->user = 0;
				$customer->save();

				// update customer_type in all of his tickets.
				$success = $wpdb->update(
					$wpdb->prefix . 'psmsc_tickets',
					array( 'user_type' => 'guest' ),
					array( 'customer' => $customer->id )
				);
			}
		}

		/**
		 * Return data for this custom field while creating duplicate ticket
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return mixed
		 */
		public static function get_duplicate_ticket_data( $cf, $ticket ) {

			return $ticket->user_type;
		}

		/**
		 * Print edit custom field properties
		 *
		 * @param WPSC_Custom_Fields $cf - custom field object.
		 * @param string             $field_class - class name of field category.
		 * @return void
		 */
		public static function get_edit_custom_field_properties( $cf, $field_class ) {

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

			// tl_width!
			if ( in_array( 'tl_width', $field_class::$allowed_properties ) ) {
				$tl_width     = isset( $_POST['tl_width'] ) ? intval( $_POST['tl_width'] ) : 0; // phpcs:ignore
				$cf->tl_width = $tl_width ? $tl_width : 100;
			}

			// save!
			$cf->save();
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

			$value = $ticket->{$cf->slug} == 'registered' ? esc_attr__( 'Registered', 'supportcandy' ) : esc_attr__( 'Guest', 'supportcandy' );
			return apply_filters( 'wpsc_ticket_field_val_user_type', $value, $cf, $ticket, $module );
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
	}
endif;

WPSC_DF_User_Type::init();
