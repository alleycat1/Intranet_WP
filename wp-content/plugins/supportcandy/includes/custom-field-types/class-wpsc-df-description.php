<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_DF_Description' ) ) :

	final class WPSC_DF_Description {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'df_description';

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
		public static $is_list = false;

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
		public static $is_auto_fill = true;

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
		public static $is_visibility_conditions = true;

		/**
		 * Set whether fields created from this custom field type is applicable for macros
		 *
		 * @var boolean
		 */
		public static $has_macro = true;

		/**
		 * Set whether fields of this custom field type is applicalbe for search on ticket list page.
		 * threads type is already available, no need to use this specific type.
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

			// JS events.
			add_action( 'wpsc_js_validate_ticket_form', array( __CLASS__, 'js_validate_ticket_form' ) );
			add_action( 'wpsc_js_create_ticket_formdata', array( __CLASS__, 'js_create_ticket_formdata' ) );

			// create ticket form.
			add_filter( 'wpsc_create_ticket_data', array( __CLASS__, 'set_create_ticket_data' ), 10, 3 );

			// create ticket data for rest api.
			add_filter( 'wpsc_rest_create_ticket', array( __CLASS__, 'set_rest_ticket_data' ), 10, 3 );

			// Add ticket id to description attachments.
			add_action( 'wpsc_create_new_ticket', array( __CLASS__, 'add_attachment_ticket_id' ), 1 );
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

			$value = isset( $filter['operand_val_1'] ) ? stripslashes( $filter['operand_val_1'] ) : ''
			?>
			<div class="item conditional operand single">
				<textarea class="operand_val_1" placeholder="<?php esc_attr_e( 'One condition per line!', 'supportcandy' ); ?>" style="width: 100%;"><?php echo esc_attr( $value ); ?></textarea>
			</div>
			<?php
		}

		/**
		 * Check condition for this type
		 *
		 * @param array             $condition - condition data.
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $value - value to compare.
		 * @return boolean
		 */
		public static function is_valid( $condition, $cf, $value ) {

			$response = false;
			$value    = stripslashes( $value );
			$terms    = explode( PHP_EOL, $condition['operand_val_1'] );
			$response = false;
			foreach ( $terms as $term ) {
				$index = strpos( $value, trim( stripslashes( $term ) ) );
				if ( is_numeric( $index ) ) {
					$response = true;
					break;
				}
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

				case 'LIKE':
					$arr = array();
					$val = explode( PHP_EOL, $val );
					foreach ( $val as $term ) {
						$term  = str_replace( '*', '%', trim( $term ) );
						$arr[] = 'CONVERT(th.body USING utf8) LIKE \'%' . $term . '%\'';
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

			$filters = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'slug'    => 'ticket',
						'compare' => '=',
						'val'     => $ticket->id,
					),
					array(
						'slug'    => 'type',
						'compare' => '=',
						'val'     => 'report',
					),
				),
			);
			$threads = WPSC_Thread::find( $filters );
			$thread  = isset( $threads['results'][0] ) ? $threads['results'][0] : array();

			if ( $thread && $thread->is_active ) {

				$value    = stripslashes( $thread->body );
				$terms    = explode( PHP_EOL, $condition['operand_val_1'] );
				$response = false;
				foreach ( $terms as $term ) {
					$index = strpos( $value, trim( stripslashes( $term ) ) );
					if ( is_numeric( $index ) ) {
						$response = true;
						break;
					}
				}
				return $response;

			} else {

				return false;
			}
		}

		/**
		 * Print ticket form field
		 *
		 * @param WPSC_Custom_Field $cf - Custom field object.
		 * @param array             $tff - Array of ticket form field settings for this field.
		 * @return string
		 */
		public static function print_tff( $cf, $tff ) {

			$unique_id    = uniqid( 'wpsc_' );
			$val          = $cf->is_auto_fill && $cf->default_value ? stripslashes( htmlspecialchars( $cf->default_value[0] ) ) : '';
			$current_user = WPSC_Current_User::$current_user;

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
				<textarea name="<?php echo esc_attr( $cf->slug ); ?>" id="description" class="wpsc_textarea" onchange="wpsc_check_tff_visibility();"><?php echo esc_attr( $val ); ?></textarea>
				<input 
					class="<?php echo esc_attr( $unique_id ); ?>" 
					type="file" 
					onchange="wpsc_set_attach_multiple(this, '<?php echo esc_attr( $unique_id ); ?>', 'description_attachments')" 
					multiple
					style="display: none;"/>
				<div class="wpsc-it-editor-action-container">
					<div class="actions">
						<div class="wpsc-editor-actions">
							<?php

							if ( WPSC_Text_Editor::is_allow_attachments() ) {
								?>
								<span class="wpsc-link" onclick="wpsc_trigger_desc_attachments('<?php echo esc_attr( $unique_id ); ?>');"><?php esc_attr_e( 'Attach Files', 'supportcandy' ); ?></span>
								<?php
							}

							if ( $current_user->is_agent ) {
								?>
								<span class="wpsc-link" onclick="wpsc_get_macros()"><?php esc_attr_e( 'Insert Macro', 'supportcandy' ); ?></span>
								<?php
							}

							do_action( 'wpsc_tff_editor_actions' );

							?>
						</div>

						<?php
						if ( WPSC_Text_Editor::is_attachment_notice() && WPSC_Text_Editor::is_allow_attachments() ) {
							?>
							<div class="wpsc-file-attachment-notice"><?php echo esc_html( WPSC_Text_Editor::file_attachment_notice_text() ); ?></div>
							<?php
						}
						?>

						<div class="<?php echo esc_attr( $unique_id ); ?> wpsc-editor-attachment-container"></div>

					</div>
				</div>
				<script>
					<?php WPSC_Text_Editor::print_editor_init_scripts( 'description', 'wpsc-description' ); ?>
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
				var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
				if (is_tinymce && tinymce.get('description')){
					var val = tinyMCE.get('description').getContent();
				} else {
					var val = jQuery('#description').val().trim();
				}
				if (customField.hasClass('required') && !val) {
					isValid = false;
					alert(supportcandy.translations.req_fields_missing);
				}
				break;
			<?php
			echo PHP_EOL;
		}

		/**
		 * Submit ticket FormData append of description
		 *
		 * @return void
		 */
		public static function js_create_ticket_formdata() {

			$cf = WPSC_Custom_Field::get_cf_by_slug( 'description' );

			$tff = get_option( 'wpsc-tff', array() );
			if ( array_key_exists( $cf->slug, $tff ) ) {
				?>
				var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
				if (is_tinymce && tinymce.get('description')){
					var description = tinyMCE.get('description').getContent();
				} else {
					var description = jQuery('#description').val().trim();
				}
				dataform.append('description', description);
				<?php
				echo PHP_EOL;
			}
		}

		/**
		 * Return default value for custom field of this type
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @return mixed
		 */
		public static function get_default_value( $cf ) {

			return $cf->default_value ? stripslashes( $cf->default_value[0] ) : '';
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

			$cf          = WPSC_Custom_Field::get_cf_by_slug( $slug );
			$default_val = self::get_default_value( $cf );
			$description = isset( $_POST[ $slug ] ) ? wp_kses_post( wp_unslash( $_POST[ $slug ] ) ) : ''; // phpcs:ignore

			// replace new line with br if no text editor.
			$is_editor   = isset( $_POST['is_editor'] ) ? intval( $_POST['is_editor'] ) : 0; // phpcs:ignore
			if ( $description && ! $is_editor ) {
				$description = nl2br( $description );
			}

			return $description ? $description : $default_val;
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

			$data['description']     = self::get_tff_value( 'description' );
			$description_attachments = isset( $_POST['description_attachments'] ) ? array_filter( array_map( 'intval', $_POST['description_attachments'] ) ) : array(); // phpcs:ignore
			foreach ( $description_attachments as $id ) {
				$attachment            = new WPSC_Attachment( $id );
				$attachment->is_active = 1;
				$attachment->save();
			}
			$data['description_attachments'] = $description_attachments ? implode( '|', $description_attachments ) : '';
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

			$cf = WPSC_Custom_Field::get_cf_by_slug( 'description' );
			$description = $request->get_param( 'description' );
			$is_html = $description != wp_strip_all_tags( $description ) ? true : false;
			$description = $is_html ? wp_kses_post( $description ) : str_replace( PHP_EOL, '<br />', $description );
			$description = $description ? $description : self::get_default_value( $cf );

			// check description is present.
			if ( ! $description ) {
				$data['errors']->add( 'req_fields_missing', 'description is missing!', 'description' );
				return $data;
			}

			$data['description'] = $description;

			// description attachments.
			$attachments = sanitize_text_field( $request->get_param( 'description_attachments' ) );
			if ( $attachments ) {
				$attachments = implode(
					'|',
					array_filter(
						array_map(
							function( $id ) {
								$attachment = new WPSC_Attachment( intval( $id ) );
								if ( $attachment->id ) {
									$attachment->is_active = 1;
									$attachment->save();
									return $attachment->id;
								} else {
									return false;
								}
							},
							explode( ',', $attachments )
						)
					)
				);
			}

			$data['description_attachments'] = $attachments;
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

				case 'LIKE':
					return $condition['operand_val_1'];
			}
			return false;
		}

		/**
		 * Add ticket id to description attachments
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return void
		 */
		public static function add_attachment_ticket_id( $ticket ) {

			$thread = $ticket->get_description_thread();
			foreach ( $thread->attachments as $attachment ) {
				$attachment->ticket_id = $ticket->id;
				$attachment->source    = 'report';
				$attachment->source_id = $thread->id;
				$attachment->save();
			}
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
				<div data-type="textarea" data-required="false" class="wpsc-input-group default_value">
					<div class="label-container">
						<label for="">
							<?php esc_attr_e( 'Default value', 'supportcandy' ); ?>
						</label>
					</div>
					<textarea name="default_value" rows="5"><?php echo esc_attr( $default_val ); ?></textarea>
				</div>
				<?php
			endif;

			if ( in_array( 'is_auto_fill', $field_class::$allowed_properties ) ) :
				?>
				<div data-type="single-select" data-required="false" class="wpsc-input-group is_auto_fill">
					<div class="label-container">
						<label for="">
							<?php esc_html_e( 'Auto-fill in ticket form', 'supportcandy' ); ?>
						</label>
					</div>
					<select name="is_auto_fill">
						<option <?php selected( $cf->is_auto_fill, '0' ); ?> value="0"><?php esc_html_e( 'No', 'supportcandy' ); ?></option>
						<option <?php selected( $cf->is_auto_fill, '1' ); ?> value="1"><?php esc_html_e( 'Yes', 'supportcandy' ); ?></option>
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

			// default value.
			if ( in_array( 'default_value', $field_class::$allowed_properties ) ) {
				$default_value     = isset( $_POST['default_value'] ) ? wp_kses_post( wp_unslash( $_POST['default_value'] ) ) : ''; // phpcs:ignore
				$cf->default_value = $default_value ? array( $default_value ) : array();
			}

			// auto fill.
			if ( in_array( 'is_auto_fill', $field_class::$allowed_properties ) ) {
				$cf->is_auto_fill = isset( $_POST['is_auto_fill'] ) ? sanitize_text_field( wp_unslash( $_POST['is_auto_fill'] ) ) : ''; // phpcs:ignore
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

			$filters = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'slug'    => 'ticket',
						'compare' => '=',
						'val'     => $ticket->id,
					),
					array(
						'slug'    => 'type',
						'compare' => '=',
						'val'     => 'report',
					),
				),
			);
			$threads = WPSC_Thread::find( $filters );
			$thread  = isset( $threads['results'][0] ) ? $threads['results'][0] : array();
			return $thread && $thread->is_active ? $thread->get_printable_string() : '';
		}
	}
endif;

WPSC_DF_Description::init();
