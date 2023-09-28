<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_CF_HTML' ) ) :

	final class WPSC_CF_HTML {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'cf_html';

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
		public static $data_type = 'TEXT NULL DEFAULT NULL';

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
		public static $is_default = false;

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
		public static $is_ctf = true;

		/**
		 * Set whether fields created from this custom field type is allowed in ticket list
		 *
		 * @var boolean
		 */
		public static $is_list = false;

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

			// Set custom field type.
			add_filter( 'wpsc_cf_types', array( __CLASS__, 'add_cf_type' ), 12 );

			// Avoid being listed on ticket list or agentonly widgets.
			add_filter( 'wpsc_it_widget_exclude_cft', array( __CLASS__, 'exclude_wdget_cft' ) );
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
				'label' => esc_attr__( 'HTML', 'supportcandy' ),
				'class' => __CLASS__,
			);
			return $cf_types;
		}

		/**
		 * Print ticket form field
		 *
		 * @param WPSC_Custom_Field $cf - Custom field object.
		 * @param array             $tff - Array of ticket form field settings for this field.
		 * @return string
		 */
		public static function print_tff( $cf, $tff ) {

			if ( ! $cf->default_value ) {
				return '';
			}

			ob_start();?>
			<div class="<?php echo esc_attr( WPSC_Functions::get_tff_classes( $cf, $tff ) ); ?>" data-cft="<?php echo esc_attr( self::$slug ); ?>">
				<?php
				echo do_shortcode( wp_kses_post( $cf->default_value[0] ) );
				?>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Exclude this custom field type being listed in ticket fields or agentonly widgets
		 *
		 * @param array $slug_arr - exclude list.
		 * @return array
		 */
		public static function exclude_wdget_cft( $slug_arr ) {

			$slug_arr[] = self::$slug;
			return $slug_arr;
		}

		/**
		 * Print add new custom field setting properties
		 *
		 * @param string $field_class - Class name of the field.
		 * @return void
		 */
		public static function get_add_new_custom_field_properties( $field_class ) {
			?>

			<div data-type="textarea" data-required="true" class="wpsc-input-group html_text">
				<div class="label-container">
					<label for="">
						<?php esc_html_e( 'HTML Content', 'supportcandy' ); ?> 
						<span class="required-char">*</span>
					</label>
				</div>
				<textarea name="html_text" rows="5"></textarea>
			</div>
			<?php
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

			<div class="wpsc-input-group html_text">
				<div class="label-container">
					<label for="">
						<?php esc_html_e( 'HTML Content', 'supportcandy' ); ?> 
						<span class="required-char">*</span>
					</label>
				</div>
				<textarea name="html_text" rows="5"><?php echo esc_attr( $cf->default_value[0] ); ?></textarea>
			</div>
			<?php
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

			// default value.
			if ( in_array( 'default_value', $field_class::$allowed_properties ) ) {
				$default_value     = isset( $_POST['html_text'] ) ? wp_kses_post( wp_unslash( $_POST['html_text'] ) ) : ''; // phpcs:ignore
				$cf->default_value = $default_value ? array( $default_value ) : array();
			}

			// save!
			$cf->save();
		}

	}
endif;

WPSC_CF_HTML::init();
