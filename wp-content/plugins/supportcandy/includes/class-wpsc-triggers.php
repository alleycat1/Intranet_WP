<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Triggers' ) ) :

	final class WPSC_Triggers {

		/**
		 * Published triggers
		 *
		 * @var array
		 */
		public static $triggers = array();

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			add_action( 'init', array( __CLASS__, 'load_triggers' ) );
		}

		/**
		 * Load triggers
		 *
		 * @return void
		 */
		public static function load_triggers() {

			self::$triggers = apply_filters(
				'wpsc_triggers',
				array(
					'create-ticket'           => esc_attr__( 'Create new ticket', 'supportcandy' ),
					'reply-ticket'            => esc_attr__( 'Ticket reply', 'supportcandy' ),
					'submit-note'             => esc_attr__( 'Submit private note', 'supportcandy' ),
					'change-ticket-subject'   => esc_attr__( 'Change ticket subject', 'supportcandy' ),
					'change-ticket-status'    => esc_attr__( 'Change ticket status', 'supportcandy' ),
					'change-ticket-category'  => esc_attr__( 'Change ticket category', 'supportcandy' ),
					'change-ticket-priority'  => esc_attr__( 'Change ticket priority', 'supportcandy' ),
					'change-assignee'         => esc_attr__( 'Change assignee', 'supportcandy' ),
					'change-ticket-fields'    => esc_attr__( 'Change ticket fields', 'supportcandy' ),
					'change-agentonly-fields' => esc_attr__( 'Change agentonly fields', 'supportcandy' ),
					'delete-ticket'           => esc_attr__( 'Delete ticket', 'supportcandy' ),
				)
			);
		}

		/**
		 * Print trigger input field
		 *
		 * @param string  $name - form element name.
		 * @param string  $hook - hook for filtering the triggers.
		 * @param string  $value - pre-set value.
		 * @param boolean $is_required - set whether or not to print required character.
		 * @param string  $label - form element label.
		 * @param boolean $is_disabled - sometimes triggers can not be changed in an edit form e.g. ticket notifications. So, we just show disabled dropdown with pre-set value.
		 * @return void
		 */
		public static function print( $name, $hook, $value = '', $is_required = false, $label = '', $is_disabled = false ) {

			$label = $label ? $label : __( 'Trigger', 'supportcandy' );
			$triggers = apply_filters( $hook, self::$triggers );
			?>
			<div class="wpsc-input-group">
				<div class="label-container">
					<label for="">
						<?php
						echo esc_html( $label );
						if ( $is_required ) {
							?>
							<span class="required-char">*</span>
							<?php
						}
						?>
					</label>
				</div>
				<select name="<?php echo esc_html( $name ); ?>" <?php echo $is_disabled ? 'disabled' : ''; ?>>
					<?php
					foreach ( $triggers as $key => $val ) {
						?>
						<option <?php selected( $value, $key, true ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $val ); ?></option>
						<?php
					}
					?>
				</select>
			</div>
			<?php
		}

		/**
		 * Check whether or not trigger value is valid or not
		 *
		 * @param string $hook - hook for filtering the triggers.
		 * @param string $value - value to check valid or not.
		 * @return boolean
		 */
		public static function is_valid( $hook, $value ) {

			$triggers = apply_filters( $hook, self::$triggers );
			return isset( $triggers[ $value ] );
		}
	}

endif;

WPSC_Triggers::init();
