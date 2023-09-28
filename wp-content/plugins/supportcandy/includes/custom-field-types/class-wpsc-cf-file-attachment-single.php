<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_CF_File_Attachment_Single' ) ) :

	final class WPSC_CF_File_Attachment_Single {

		/**
		 * Slug for this custom field type
		 *
		 * @var string
		 */
		public static $slug = 'cf_file_attachment_single';

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
		public static $data_type = 'INT NULL DEFAULT NULL';

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
		public static $ref_class = 'wpsc_attachment';

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
			add_filter( 'wpsc_cf_types', array( __CLASS__, 'add_cf_type' ), 13 );

			// ticket form.
			add_action( 'wpsc_js_validate_ticket_form', array( __CLASS__, 'js_validate_ticket_form' ) );
			add_filter( 'wpsc_create_ticket_data', array( __CLASS__, 'set_create_ticket_data' ), 10, 3 );

			// create ticket data for rest api.
			add_filter( 'wpsc_rest_create_ticket', array( __CLASS__, 'set_rest_ticket_data' ), 10, 3 );

			// Add ticket id to attachmets after create ticket.
			add_action( 'wpsc_create_new_ticket', array( __CLASS__, 'add_attachment_ticket_id' ), 1 );

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
				'label' => esc_attr__( 'File Attachment (Single)', 'supportcandy' ),
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

			$current_user = WPSC_Current_User::$current_user;
			$unique_id    = uniqid( 'wpsc_' );
			$attachment   = new WPSC_Attachment();
			if ( $cf->field == 'customer' ) {
				$attachment = $current_user->is_customer && $current_user->customer->{$cf->slug} ? $current_user->customer->{$cf->slug} : new WPSC_Attachment();
			}

			ob_start();?>
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
					class="<?php echo esc_attr( $unique_id ); ?>" 
					type="file" 
					onchange="wpsc_set_attach_single(this, '<?php echo esc_attr( $unique_id ); ?>', '<?php echo esc_attr( $cf->slug ); ?>')"/>
				<div class="<?php echo esc_attr( $unique_id ); ?> wpsc-editor-attachment-container">
					<?php
					if ( $attachment->id ) {
						?>
						<div class="wpsc-editor-attachment upload-success">
							<div class="attachment-label"><?php echo esc_attr( $attachment->name ); ?></div>
							<div 
								class="attachment-remove" 
								onclick="wpsc_remove_attachment(this)"
								data-single="true"
								data-uniqueid="<?php echo esc_attr( $unique_id ); ?>">
											<?php WPSC_Icons::get( 'times' ); ?>
							</div>
							<input type="hidden" name="<?php echo esc_attr( $cf->slug ); ?>[]" value="<?php echo esc_attr( $attachment->id ); ?>"/>
						</div>
						<?php
					}
					?>
				</div>
				<?php
				if ( $attachment->id ) :
					?>
					<script>jQuery('input.<?php echo esc_attr( $unique_id ); ?>').hide();</script>
					<?php
				endif;
				?>
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
				var count = customField.find('input[type=hidden]').length;
				if (customField.hasClass('required') && count === 0) {
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

			return isset( $_POST[ $slug ] ) && isset( $_POST[ $slug ][0] ) ? intval( $_POST[ $slug ][0] ) : 0; // phpcs:ignore
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

						if ( $value ) {
							$attachment            = new WPSC_Attachment( $value );
							$attachment->is_active = 1;
							$attachment->save();
						}
						$data[ $cf->slug ] = $value ? $value : '';

					} elseif ( $cf->field == 'agentonly' ) {

						$data[ $cf->slug ] = '';

					} elseif ( $cf->field == 'customer' && $data['customer'] != 0 ) {

						$tff = get_option( 'wpsc-tff' );
						if ( ! $is_my_profile && ! isset( $tff[ $cf->slug ] ) ) {
							continue;
						}
						$customer     = new WPSC_Customer( $data['customer'] );
						$existing_val = $customer->{$cf->slug} ? $customer->{$cf->slug}->id : null;
						if ( $existing_val != $value ) {

							$new_attach_id = '';
							if ( $value ) {
								$attachment              = new WPSC_Attachment( $value );
								$attachment->is_active   = 1;
								$attachment->source      = 'cf';
								$attachment->source_id   = $cf->id;
								$attachment->customer_id = $customer->id;
								$attachment->save();

								$new_attach_id = $attachment->id;
							}

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
											'new'  => $new_attach_id,
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

					$attachment = WPSC_Functions::sanitize_attachment( intval( $request->get_param( $cf->slug ) ) );

					if ( in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ) {

						$data[ $cf->slug ] = $cf->field == 'ticket' && $attachment ? $attachment : '';

					} else {

						$customer = new WPSC_Customer( $data['customer'] );
						$existing_val = $customer->{$cf->slug};
						$existing_val = $existing_val && $existing_val->id ? $existing_val->id : '';

						if ( $attachment && $attachment != $existing_val ) {

							$customer->{$cf->slug} = $attachment;
							$customer->save();

							$attachment = new WPSC_Attachment( $attachment );
							$attachment->is_active   = 1;
							$attachment->source      = 'cf';
							$attachment->source_id   = $cf->id;
							$attachment->customer_id = $customer->id;
							$attachment->save();

							// Set log for this change.
							WPSC_Log::insert(
								array(
									'type'         => 'customer',
									'ref_id'       => $customer->id,
									'modified_by'  => $current_user->customer->id,
									'body'         => wp_json_encode(
										array(
											'slug' => $cf->slug,
											'prev' => $existing_val,
											'new'  => $attachment->id,
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
							$search_items = WPSC_Attachment::get_tl_search_string( $search );
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
		 * Print edit ticket custom field in individual ticket
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_edit_ticket_cf( $cf, $ticket ) {

			$unique_id  = uniqid( 'wpsc_' );
			$attachment = $ticket->{$cf->slug} ? $ticket->{$cf->slug} : new WPSC_Attachment();
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
				<input 
					class="<?php echo esc_attr( $unique_id ); ?>" 
					type="file" 
					onchange="wpsc_set_attach_single(this, '<?php echo esc_attr( $unique_id ); ?>', '<?php echo esc_attr( $cf->slug ); ?>')"/>
				<div class="<?php echo esc_attr( $unique_id ); ?> wpsc-editor-attachment-container">
					<?php
					if ( $attachment->id ) {
						?>
						<div class="wpsc-editor-attachment upload-success">
							<div class="attachment-label"><?php echo esc_attr( $attachment->name ); ?></div>
							<div 
								class="attachment-remove" 
								onclick="wpsc_remove_attachment(this)"
								data-single="true"
								data-uniqueid="<?php echo esc_attr( $unique_id ); ?>">
											<?php WPSC_Icons::get( 'times' ); ?>
							</div>
							<input type="hidden" name="<?php echo esc_attr( $cf->slug ); ?>[]" value="<?php echo esc_attr( $attachment->id ); ?>"/>
						</div>
						<?php
					}
					?>
				</div>
				<?php
				if ( $attachment->id ) :
					?>
					<script>jQuery('input.<?php echo esc_attr( $unique_id ); ?>').hide();</script>
					<?php
				endif;
				?>
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
			$new  = isset( $_POST[ $cf->slug ] ) && isset( $_POST[ $cf->slug ][0] ) ? intval( $_POST[ $cf->slug ][0] ) : ''; // phpcs:ignore
			if ( $new === 0 ) {
				return $ticket;
			}

			// Exit if there is no change.
			if ( $prev == $new ) {
				return $ticket;
			}

			$new            = new WPSC_Attachment( $new );
			$new->is_active = 1;
			$new->ticket_id = $ticket->id;
			$new->source    = 'cf';
			$new->source_id = $cf->id;
			$new->save();

			// Change value.
			$ticket->{$cf->slug} = $new->id;
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

			$id = WPSC_Functions::sanitize_attachment( intval( $value ) );
			if ( ! $id ) {
				return;
			}

			$attachment = new WPSC_Attachment( $id );

			if ( is_object( $ticket->{$cf->slug} ) && $ticket->{$cf->slug}->id != $attachment->id ) {

				$ticket->{$cf->slug} = $attachment->id;

				$attachment->is_active   = 1;
				$attachment->source      = 'cf';
				$attachment->source_id   = $cf->id;
				$attachment->ticket_id   = $ticket->id;
				$attachment->save();
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

			$unique_id  = uniqid( 'wpsc_' );
			$attachment = $customer->id && $customer->{$cf->slug} ? $customer->{$cf->slug} : new WPSC_Attachment();

			ob_start();
			?>
			<div class="<?php echo esc_attr( WPSC_Functions::get_tff_classes( $cf, $tff ) ); ?>" data-cft="<?php echo esc_attr( self::$slug ); ?>">
				<div class="wpsc-tff-label">
					<span class="name"><?php echo esc_attr( $cf->name ); ?></span>
				</div>
				<span class="extra-info"><?php echo esc_attr( $cf->extra_info ); ?></span>
				<input 
					class="<?php echo esc_attr( $unique_id ); ?>" 
					type="file" 
					onchange="wpsc_set_attach_single(this, '<?php echo esc_attr( $unique_id ); ?>', '<?php echo esc_attr( $cf->slug ); ?>')"/>
				<div class="<?php echo esc_attr( $unique_id ); ?> wpsc-editor-attachment-container">
					<?php
					if ( $attachment->id ) {
						?>
						<div class="wpsc-editor-attachment upload-success">
							<div class="attachment-label"><?php echo esc_attr( $attachment->name ); ?></div>
							<div 
								class="attachment-remove" 
								onclick="wpsc_remove_attachment(this)"
								data-single="true"
								data-uniqueid="<?php echo esc_attr( $unique_id ); ?>">
								<?php WPSC_Icons::get( 'times' ); ?>
							</div>
							<input type="hidden" name="<?php echo esc_attr( $cf->slug ); ?>[]" value="<?php echo esc_attr( $attachment->id ); ?>"/>
						</div>
						<?php
					}
					?>
				</div>
				<?php
				if ( $attachment->id ) {
					?>
					<script>jQuery('input.<?php echo esc_attr( $unique_id ); ?>').hide();</script>
					<?php
				}
				?>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Add ticket id to attachments after create ticket
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return void
		 */
		public static function add_attachment_ticket_id( $ticket ) {

			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

				if (
					$cf->type::$slug != self::$slug ||
					$cf->field != 'ticket'
				) {
					continue;
				}

				$attachment = $ticket->{$cf->slug};
				if ( $attachment ) {
					$attachment->ticket_id = $ticket->id;
					$attachment->source    = 'cf';
					$attachment->source_id = $cf->id;
					$attachment->save();
				}
			}
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

			$attachment = $ticket->{$cf->slug};

			$name = '';
			if ( $attachment && $attachment->id ) {
				WPSC_Macros::$attachments = array_merge( WPSC_Macros::$attachments, array( $attachment ) );

				if ( $module == 'email-notification' ) {
					$link = site_url( '/' ) . '?wpsc_attachment=' . $attachment->id . '&auth_code=' . $ticket->auth_code;
					$name = '<a href= ' . $link . ' >' . $attachment->name . '</a>';
				} else {
					$name = $attachment->name;
				}
			}

			return apply_filters( 'wpsc_ticket_field_val_attach_single', $name, $cf, $ticket, $module );
		}

		/**
		 * Print ticket value for given custom field on widget
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Ticket       $ticket - ticket object.
		 * @return void
		 */
		public static function print_widget_ticket_field_val( $cf, $ticket ) {

			$attachment = $ticket->{$cf->slug};
			if ( ! $attachment->id ) {
				return;
			}
			$unique_id = uniqid( 'wpsc_' );
			?>
			<div class="<?php echo esc_attr( $unique_id ); ?>">
				<div class="wpsc-attachment-item">
					<?php
					$download_url = site_url( '/' ) . '?wpsc_attachment=' . $attachment->id . '&auth_code=' . $ticket->auth_code;
					?>
					<a class="wpsc-link" href="<?php echo esc_url( $download_url ); ?>" target="_blank">
					<span class="wpsc-attachment-name"><?php echo esc_attr( $attachment->name ); ?></span></a>
				</div>
			</div>
			<script>jQuery('.<?php echo esc_attr( $unique_id ); ?>').parent().addClass('fullwidth');</script>
			<?php
		}

		/**
		 * Returns printable customer value for custom field. Can be used in export tickets, replace macros etc.
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @return string
		 */
		public static function get_customer_field_val( $cf, $customer ) {

			$attachment = $customer->{$cf->slug};
			return $attachment && $attachment->id ? $attachment->name : '';
		}

		/**
		 * Print customer value for given custom field on widget
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param WPSC_Customer     $customer - customer object.
		 * @return void
		 */
		public static function print_widget_customer_field_val( $cf, $customer ) {

			$attachment = $customer->{$cf->slug};
			if ( ! is_object( $attachment ) ) {
				return;
			}
			$unique_id = uniqid( 'wpsc_' );
			?>
			<div class="<?php echo esc_attr( $unique_id ); ?>">
				<div class="wpsc-attachment-item">
					<?php
					$download_url = site_url( '/' ) . '?wpsc_attachment=' . $attachment->id;
					?>
					<a class="wpsc-link" href="<?php echo esc_url( $download_url ); ?>" target="_blank">
					<span class="wpsc-attachment-name"><?php echo esc_attr( $attachment->name ); ?></span></a>
				</div>
			</div>
			<script>jQuery('.<?php echo esc_attr( $unique_id ); ?>').parent().addClass('fullwidth');</script>
			<?php
		}

		/**
		 * Print given value for custom field
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and print.
		 * @return void
		 */
		public static function print_val( $cf, $val ) {

			$attachment = is_object( $val ) ? $val : new WPSC_Attachment( $val );
			if ( ! $attachment->id ) {
				esc_attr_e( 'None', 'supportcandy' );
				return;
			}
			$ticket = new WPSC_Ticket( $attachment->ticket_id );
			?>
			<div class="wpsc-attachment-item">
				<?php
				$download_url = site_url( '/' ) . '?wpsc_attachment=' . $attachment->id . '&auth_code=' . $ticket->auth_code;
				?>
				<a class="wpsc-link" href="<?php echo esc_url( $download_url ); ?>" target="_blank">
					<span class="wpsc-attachment-name"><?php echo esc_attr( $attachment->name ); ?></span>
				</a>
			</div>
			<?php
		}

		/**
		 * Return printable value for history log macro
		 *
		 * @param WPSC_Custom_Field $cf - custom field object.
		 * @param mixed             $val - value to convert and return.
		 * @return string
		 */
		public static function get_history_log_val( $cf, $val ) {

			$attachment = is_object( $val ) ? $val : new WPSC_Attachment( $val );
			return $attachment->id ? $attachment->name : esc_attr__( 'None', 'supportcandy' );
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
	}
endif;

WPSC_CF_File_Attachment_Single::init();
