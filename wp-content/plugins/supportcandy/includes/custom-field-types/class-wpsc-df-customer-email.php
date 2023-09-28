<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_Customer_Email' ) ) :

	final class WPSC_DF_Customer_Email {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_customer_email';

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
		public static $has_personal_info = true;

		/**
		 * Set whether this custom field type has multiple values
		 *
		 * @var boolean
		 */
		public static $has_multiple_val = false;

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
		 * Data type for column created in customer table
		 *
		 * @var string
		 */
		public static $data_type = 'VARCHAR(200) NOT NULL';

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
		public static $is_sort = true;

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
		public static $is_visibility_conditions = false;

		/**
		 * Set whether fields created from this custom field type is applicable for macros.
		 * Set false in order to avoid {{email}} to be listed. Customer email can be achived by {{customer_email}} macro.
		 *
		 * @var boolean
		 */
		public static $has_macro = false;

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

			// TFF!
			add_action( 'wpsc_js_validate_ticket_form', array( __CLASS__, 'js_validate_ticket_form' ) );
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
		 * Print ticket form field
		 *
		 * @param WPSC_Custom_Field $cf - Custom field object.
		 * @param array             $tff - Array of ticket form field settings for this field.
		 * @return string
		 */
		public static function print_tff( $cf, $tff ) {

			$current_user = WPSC_Current_User::$current_user;
			ob_start();
			if ( ! $current_user->is_customer ) {
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
					<input 
						type="text" 
						class="<?php echo esc_attr( $cf->slug ); ?>"
						name="<?php echo esc_attr( $cf->slug ); ?>" 
						placeholder="<?php echo esc_attr( $cf->placeholder_text ); ?>"
						autocomplete="off"/>
				</div>
				<?php

			} else {
				?>

				<input 
					type="hidden" 
					class="<?php echo esc_attr( $cf->slug ); ?>" 
					name="<?php echo esc_attr( $cf->slug ); ?>" 
					value="<?php echo esc_attr( $current_user->customer->email ); ?>"/>
				<?php
			}

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
				var val = customField.find('input').first().val().trim();
				if (customField.hasClass('required') && !val) {
					isValid = false;
					alert(supportcandy.translations.req_fields_missing);
					break;
				}
				if (val && !validateEmail(val)) {
					isValid = false;
					alert('<?php esc_attr_e( 'Invalid email address!', 'supportcandy' ); ?>');
					break;
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

			$email = isset( $_POST[ $slug ] ) ? sanitize_text_field( wp_unslash( $_POST[ $slug ] ) ) : ''; // phpcs:ignore
			if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				$email = '';
			}
			return $email;
		}

		/**
		 * Return slug string to be used in where condition of ticket model for this type of field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return string
		 */
		public static function get_sql_slug( $cf ) {

			return 'c.' . $cf->slug;
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
		}

		/**
		 * Return orderby string
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return string
		 */
		public static function get_orderby_string( $cf ) {

			return 'c.' . $cf->slug;
		}

		/**
		 * Returns printable customer value for custom field. Can be used in export tickets, replace macros etc.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @return string
		 */
		public static function get_customer_field_val( $cf, $customer ) {

			return $customer->email;
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
	}
endif;

WPSC_DF_Customer_Email::init();
