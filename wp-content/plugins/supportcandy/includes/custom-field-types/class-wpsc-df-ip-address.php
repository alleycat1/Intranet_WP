<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_IP_Address' ) ) :

	final class WPSC_DF_IP_Address {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_ip_address';

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
		public static $data_type = 'VARCHAR(50) NULL';

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
		public static $is_filter = false;

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
		 * Set whether fields created from this custom field type can be auto-filled
		 *
		 * @var boolean
		 */
		public static $is_auto_fill = false;

		/**
		 * Set whether fields created from this custom field type can be available for ticket list sorting
		 *
		 * @var boolean
		 */
		public static $is_sort = false;

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
		 * Return default value for custom field of this type
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return mixed
		 */
		public static function get_default_value( $cf ) {

			return '';
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

			$data['ip_address'] = self::get_current_user_ip();
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

			$data['ip_address'] = self::get_current_user_ip();
			return $data;
		}

		/**
		 * Get current user IP address
		 *
		 * @return string
		 */
		public static function get_current_user_ip() {

			$ip_address = isset( $_SERVER ) && isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
			if ( strlen( $ip_address ) > 28 || $ip_address == '::1' ) {
				$ip_address = '';
			}
			return $ip_address;
		}

		/**
		 * Print edit custom field properties
		 *
		 * @param WPSC_Custom_Fields $cf - custom field object.
		 * @param string             $field_class - class name of field category.
		 * @return void
		 */
		public static function get_edit_custom_field_properties( $cf, $field_class ) {

			if ( in_array( 'tl_width', $field_class::$allowed_properties ) ) :?>
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

			return $ticket->{$cf->slug};
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

			$ip = $ticket->{$cf->slug};
			if ( $ip ) {
				echo '<a class="wpsc-link" href="https://ipinfo.io/' . esc_attr( $ip ) . '" target="_blank">' . esc_attr( $ip ) . '</a>';
			} else {
				esc_attr_e( 'Not Applicable', 'supportcandy' );
			}
		}
	}
endif;

WPSC_DF_IP_Address::init();
