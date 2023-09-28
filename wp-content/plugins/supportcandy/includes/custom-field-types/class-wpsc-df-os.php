<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_OS' ) ) :

	final class WPSC_DF_OS {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_os';

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
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], 'LIKE' ); ?> value="LIKE"><?php esc_attr_e( 'Has Words', 'supportcandy' ); ?></option>
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

			$value = isset( $filter['operand_val_1'] ) ? stripslashes( $filter['operand_val_1'] ) : '';
			if ( in_array( $operator, array( 'IN', 'NOT IN', 'LIKE' ) ) ) {
				?>

				<div class="item conditional operand single">
					<textarea class="operand_val_1" placeholder="<?php esc_attr_e( 'One condition per line!', 'supportcandy' ); ?>" style="width: 100%;"><?php echo esc_attr( $value ); ?></textarea>
				</div>
				<?php

			} else {
				?>

				<div class="item conditional operand single">
					<input 
						type="text" 
						class="operand_val_1"
						value="<?php echo esc_attr( $value ); ?>"
						autocomplete="off"/>
				</div>
				<?php
			}
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
					$str = 'CONVERT(t.' . $cf->slug . ' USING utf8) IN(\'' . implode( '\', \'', esc_sql( $val ) ) . '\')';
					break;

				case 'NOT IN':
					$str = 'CONVERT(t.' . $cf->slug . ' USING utf8) NOT IN(\'' . implode( '\', \'', esc_sql( $val ) ) . '\')';
					break;

				case 'LIKE':
					$arr = array();
					$val = explode( PHP_EOL, $val );
					foreach ( $val as $term ) {
						$term  = str_replace( '*', '%', trim( $term ) );
						$arr[] = 'CONVERT(t.' . $cf->slug . ' USING utf8) LIKE \'%' . esc_sql( $term ) . '%\'';
					}
					$str = '(' . implode( ' OR ', $arr ) . ')';
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

			$flag  = true;
			$value = stripslashes( $ticket->{$cf->slug} );
			$terms = explode( PHP_EOL, $condition['operand_val_1'] );

			switch ( $condition['operator'] ) {

				case '=':
					$flag = $condition['operand_val_1'] == $value ? true : false;
					break;

				case 'IN':
					$flag = false;
					foreach ( $terms as $term ) {
						$term = trim( $term );
						if ( $term == $value ) {
							$flag = true;
							break;
						}
					}
					break;

				case 'NOT IN':
					foreach ( $terms as $term ) {
						$term = trim( $term );
						if ( $term == $value ) {
							$flag = false;
							break;
						}
					}
					break;

				case 'LIKE':
					$flag = false;
					foreach ( $terms as $term ) {
						$index = strpos( $value, trim( stripslashes( $term ) ) );
						if ( is_numeric( $index ) ) {
							$flag = true;
							break;
						}
					}
					break;

				default:
					$flag = true;
			}

			return $flag;
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

			$data['os'] = self::get_user_platform();
			return $data;
		}

		/**
		 * Get operating system from user agent
		 *
		 * @return String
		 */
		public static function get_user_platform() {

			$os_platform = '';

			$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';

			$os_array = array(
				'/windows nt 10/i'      => 'Windows 10',
				'/windows nt 6.3/i'     => 'Windows 8.1',
				'/windows nt 6.2/i'     => 'Windows 8',
				'/windows nt 6.1/i'     => 'Windows 7',
				'/windows nt 6.0/i'     => 'Windows Vista',
				'/windows nt 5.2/i'     => 'Windows Server 2003/XP x64',
				'/windows nt 5.1/i'     => 'Windows XP',
				'/windows xp/i'         => 'Windows XP',
				'/windows nt 5.0/i'     => 'Windows 2000',
				'/windows me/i'         => 'Windows ME',
				'/win98/i'              => 'Windows 98',
				'/win95/i'              => 'Windows 95',
				'/win16/i'              => 'Windows 3.11',
				'/macintosh|mac os x/i' => 'Mac OS X',
				'/mac_powerpc/i'        => 'Mac OS 9',
				'/linux/i'              => 'Linux',
				'/ubuntu/i'             => 'Ubuntu',
				'/iphone/i'             => 'iPhone',
				'/ipod/i'               => 'iPod',
				'/ipad/i'               => 'iPad',
				'/android/i'            => 'Android',
				'/blackberry/i'         => 'BlackBerry',
				'/webos/i'              => 'Mobile',
			);

			foreach ( $os_array as $regex => $value ) {
				if ( preg_match( $regex, $user_agent ) ) {
					$os_platform = $value;
					break;
				}
			}

			return $os_platform;
		}

		/**
		 * Return slug string to be used in where condition of ticket model for this type of field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return string
		 */
		public static function get_sql_slug( $cf ) {

			return 't.' . $cf->slug;
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
					return $condition['operand_val_1'];

				case 'IN':
				case 'NOT IN':
					$val      = explode( PHP_EOL, $condition['operand_val_1'] );
					$temp_val = array();
					foreach ( $val as $value ) {
						$value = trim( $value );
						if ( $value ) {
							$temp_val[] = $value;
						}
					}
					return $temp_val ? $temp_val : false;

				case 'LIKE':
					return trim( $condition['operand_val_1'] );
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

			$os = $ticket->{$cf->slug};
			echo $os ? esc_attr( $os ) : esc_attr__( 'Not Applicable', 'supportcandy' );
		}
	}
endif;

WPSC_DF_OS::init();
