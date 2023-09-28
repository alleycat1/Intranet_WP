<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_Date_Closed' ) ) :

	final class WPSC_DF_Date_Closed {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_date_closed';

		/**
		 * Set whether this custom field type is of type date
		 *
		 * @var boolean
		 */
		public static $is_date = true;

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
		public static $data_type = 'DATETIME NULL DEFAULT NULL';

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
		public static $ref_class = 'datetime';

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
		public static $is_sort = true;

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
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '<' ); ?> value="<"><?php esc_attr_e( 'Less than', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '<=' ); ?> value="<="><?php esc_attr_e( 'Less than or equals', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '>' ); ?> value=">"><?php esc_attr_e( 'Greater than', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], '>=' ); ?> value=">="><?php esc_attr_e( 'Greater than or equals', 'supportcandy' ); ?></option>
					<option <?php isset( $filter['operator'] ) && selected( $filter['operator'], 'BETWEEN' ); ?> value="BETWEEN"><?php esc_attr_e( 'Between', 'supportcandy' ); ?></option>
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

			$unique_id     = uniqid( 'wpsc_' );
			$is_between    = $operator == 'BETWEEN' ? true : false;
			$operand_val_1 = isset( $filter['operand_val_1'] ) ? $filter['operand_val_1'] : '';
			$operand_val_2 = isset( $filter['operand_val_2'] ) ? $filter['operand_val_2'] : '';
			?>

			<div class="item conditional operand <?php echo ! $is_between ? 'single' : ''; ?>">
				<input type="text" class="operand_val_1 <?php echo esc_attr( $unique_id ); ?>" placeholder="<?php echo $is_between ? esc_attr__( 'From date', 'supportcandy' ) : ''; ?>" value="<?php echo esc_attr( $operand_val_1 ); ?>">
			</div>
			<script>
				jQuery('.operand_val_1.<?php echo esc_attr( $unique_id ); ?>').flatpickr({
					locale: "<?php echo esc_attr( WPSC_Functions::get_locale_iso() ); ?>",
				});
			</script>
			<?php

			if ( $is_between ) {
				?>
				<div class="item conditional operand">
					<input type="text" class="operand_val_2 <?php echo esc_attr( $unique_id ); ?>" placeholder="<?php esc_attr_e( 'To date', 'supportcandy' ); ?>" value="">
				</div>
				<script>
					jQuery('.operand_val_1.<?php echo esc_attr( $unique_id ); ?>').change(function() {
						var fromDate = jQuery(this).val().trim();
						jQuery('.operand_val_2.<?php echo esc_attr( $unique_id ); ?>').flatpickr({
							minDate: fromDate,
							defaultDate: fromDate,
							locale: "<?php echo esc_attr( WPSC_Functions::get_locale_iso() ); ?>",
						});
					});
					<?php
					if ( $operand_val_2 ) {
						?>
						jQuery('.operand_val_2.<?php echo esc_attr( $unique_id ); ?>').flatpickr({
							minDate: '<?php echo esc_attr( $operand_val_1 ); ?>',
							defaultDate: '<?php echo esc_attr( $operand_val_2 ); ?>',
							locale: "<?php echo esc_attr( WPSC_Functions::get_locale_iso() ); ?>",
						});
						<?php
					}
					?>
				</script>
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
					$from = WPSC_Functions::get_utc_date_str( $val . ' 00:00:00' );
					$to   = WPSC_Functions::get_utc_date_str( $val . ' 23:59:59' );
					$str  = 't.' . $cf->slug . ' BETWEEN \'' . esc_sql( $from ) . '\' AND \'' . esc_sql( $to ) . '\'';
					break;

				case '<':
					$from = WPSC_Functions::get_utc_date_str( $val . ' 00:00:00' );
					$str  = 't.' . $cf->slug . $compare . '\'' . esc_sql( $from ) . '\'';
					break;

				case '>':
					$to  = WPSC_Functions::get_utc_date_str( esc_sql( $val ) . ' 23:59:59' );
					$str = 't.' . $cf->slug . $compare . '\'' . esc_sql( $to ) . '\'';
					break;

				case '<=':
					$from = WPSC_Functions::get_utc_date_str( $val . ' 00:00:00' );
					$to   = WPSC_Functions::get_utc_date_str( $val . ' 23:59:59' );
					$arr  = array(
						't.' . $cf->slug . $compare . '\'' . $from . '\'',
						't.' . $cf->slug . ' BETWEEN \'' . $from . '\' AND \'' . $to . '\'',
					);
					$str  = '(' . implode( ' OR ', esc_sql( $arr ) ) . ')';
					break;

				case '>=':
					$from = WPSC_Functions::get_utc_date_str( esc_sql( $val ) . ' 00:00:00' );
					$to   = WPSC_Functions::get_utc_date_str( esc_sql( $val ) . ' 23:59:59' );
					$arr  = array(
						't.' . $cf->slug . $compare . '\'' . $to . '\'',
						't.' . $cf->slug . ' BETWEEN \'' . $from . '\' AND \'' . $to . '\'',
					);
					$str  = '(' . implode( ' OR ', esc_sql( $arr ) ) . ')';
					break;

				case 'BETWEEN':
					$from = $val['operand_val_1'];
					$to   = $val['operand_val_2'];
					if ( preg_match( '/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/', $from ) ) {
						$from = WPSC_Functions::get_utc_date_str( $val['operand_val_1'] );
						$to   = WPSC_Functions::get_utc_date_str( $val['operand_val_2'] );
					} else {
						$from = WPSC_Functions::get_utc_date_str( $val['operand_val_1'] . ' 00:00:00' );
						$to   = WPSC_Functions::get_utc_date_str( $val['operand_val_2'] . ' 23:59:59' );
					}
					$str = 't.' . $cf->slug . ' BETWEEN \'' . esc_sql( $from ) . '\' AND \'' . esc_sql( $to ) . '\'';
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

			return 't.' . $cf->slug;
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
			$date = $ticket->{$cf->slug};
			if ( ! is_object( $date ) ) {
				return false;
			}

			$date->setTimezone( wp_timezone() );

			switch ( $condition['operator'] ) {

				case '=':
					$flag = $date->format( 'Y-m-d' ) == $condition['operand_val_1'] ? true : false;
					break;

				case '<':
					$operand = new DateTime( $condition['operand_val_1'] );
					$flag    = $operand < $date ? true : false;
					break;

				case '>':
					$operand = new DateTime( $condition['operand_val_1'] );
					$flag    = $operand > $date ? true : false;
					break;

				case '<=':
					$operand = new DateTime( $condition['operand_val_1'] );
					$flag    = $operand <= $date ? true : false;
					break;

				case '>=':
					$operand = new DateTime( $condition['operand_val_1'] );
					$flag    = $operand >= $date ? true : false;
					break;

				case 'BETWEEN':
					$operand1 = new DateTime( $condition['operand_val_1'] );
					$operand2 = new DateTime( $condition['operand_val_2'] );
					$flag     = $operand1 <= $date && $operand2 >= $date ? true : false;
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

			return '';
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
				case '<':
				case '<=':
				case '>':
				case '>=':
					return $condition['operand_val_1'];

				case 'BETWEEN':
					if (
						! ( isset( $condition['operand_val_1'] ) && preg_match( '/^(\d{4})-\d{2}-(\d{2})$/', $condition['operand_val_1'] ) ) ||
						! ( isset( $condition['operand_val_2'] ) && preg_match( '/^(\d{4})-\d{2}-(\d{2})$/', $condition['operand_val_2'] ) )
					) {
						return false;
					}

					return array(
						'operand_val_1' => $condition['operand_val_1'],
						'operand_val_2' => $condition['operand_val_2'],
					);
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
			?>

			<div data-type="date-format" data-required="false" class="wpsc-input-group date-format">
				<div class="label-container">
					<label for=""><?php esc_attr_e( 'Display as', 'supportcandy' ); ?></label>
				</div>
				<select name="date_display_as">
					<option <?php selected( $cf->date_display_as, 'date' ); ?> value="date"><?php esc_attr_e( 'Date Format', 'supportcandy' ); ?></option>
					<option <?php selected( $cf->date_display_as, 'diff' ); ?> value="diff"><?php esc_attr_e( 'Date Difference (e.g. 1 hour ago)', 'supportcandy' ); ?></option>
				</select>
			</div>

			<div data-type="date-format" data-required="false" class="wpsc-input-group date-format">
				<div class="label-container">
					<label for=""><?php esc_attr_e( 'Date format', 'supportcandy' ); ?></label>
				</div>
				<input type="text" name="date_format" value="<?php echo esc_attr( $cf->date_format ); ?>" autocomplete="off" />
			</div>
			<?php

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

			// date display as.
			$cf->date_display_as = isset( $_POST['date_display_as'] ) ? sanitize_text_field( wp_unslash( $_POST['date_display_as'] ) ) : 'date'; // phpcs:ignore

			// date format.
			$cf->date_format = isset( $_POST['date_format'] ) ? sanitize_text_field( wp_unslash( $_POST['date_format'] ) ) : ''; // phpcs:ignore

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

			$general_settings = get_option( 'wpsc-gs-general' );
			$format           = $cf->date_format ? $cf->date_format : $general_settings['default-date-format'];
			$date             = $ticket->{$cf->slug};
			$value = is_object( $date ) ? wp_date( $format, $date->setTimezone( wp_timezone() )->getTimestamp() ) : '';

			return apply_filters( 'wpsc_ticket_field_val_date_closed', $value, $cf, $ticket, $module );
		}

		/**
		 * Print ticket value for given custom field on ticket list
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_tl_ticket_field_val( $cf, $ticket ) {

			$date = $ticket->{$cf->slug};
			if ( ! is_object( $date ) ) {
				return;
			}

			$now      = new DateTime();
			$date_str = self::get_ticket_field_val( $cf, $ticket );
			$diff_str = WPSC_Functions::date_interval_highest_unit_ago( $date->diff( $now ) );
			if ( $cf->date_display_as == 'date' ) {
				?>
				<span title="<?php echo esc_attr( $diff_str ); ?>"><?php echo esc_attr( $date_str ); ?></span>
				<?php
			} else {
				?>
				<span title="<?php echo esc_attr( $date_str ); ?>"><?php echo esc_attr( $diff_str ); ?></span>
				<?php
			}
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
	}
endif;

WPSC_DF_Date_Closed::init();
