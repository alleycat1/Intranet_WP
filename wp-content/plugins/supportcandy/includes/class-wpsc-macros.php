<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Macros' ) ) :

	final class WPSC_Macros {

		/**
		 * All macros
		 *
		 * @var array
		 */
		public static $macros;

		/**
		 * Custom field type attachments macros
		 *
		 * @var array
		 */
		public static $attachments = array();

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// Load macros.
			add_action( 'init', array( __CLASS__, 'load_macros' ), 12 );

			// get macros modal.
			add_action( 'wp_ajax_wpsc_get_macros', array( __CLASS__, 'get_macros' ) );
			add_action( 'wp_ajax_nopriv_wpsc_get_macros', array( __CLASS__, 'get_macros' ) );
		}

		/**
		 * Load maros on init wp event
		 *
		 * @return void
		 */
		public static function load_macros() {

			$macros = array(

				array(
					'tag'   => '{{ticket_id}}',
					'title' => esc_attr__( 'Ticket id', 'supportcandy' ),
				),
				array(
					'tag'   => '{{customer_name}}',
					'title' => esc_attr__( 'Customer name', 'supportcandy' ),
				),
				array(
					'tag'   => '{{customer_first_name}}',
					'title' => esc_attr__( 'Customer first name', 'supportcandy' ),
				),
				array(
					'tag'   => '{{customer_email}}',
					'title' => esc_attr__( 'Customer email addrees', 'supportcandy' ),
				),
				array(
					'tag'   => '{{last_reply}}',
					'title' => esc_attr__( 'Last reply', 'supportcandy' ),
				),
				array(
					'tag'   => '{{last_reply_user_name}}',
					'title' => esc_attr__( 'Last reply user name', 'supportcandy' ),
				),
				array(
					'tag'   => '{{last_reply_user_email}}',
					'title' => esc_attr__( 'Last reply user email', 'supportcandy' ),
				),
				array(
					'tag'   => '{{last_reply_user_first_name}}',
					'title' => esc_attr__( 'Last reply user first name', 'supportcandy' ),
				),
				array(
					'tag'   => '{{last_note}}',
					'title' => esc_attr__( 'Last note', 'supportcandy' ),
				),
				array(
					'tag'   => '{{last_note_user_name}}',
					'title' => esc_attr__( 'Last note user name', 'supportcandy' ),
				),
				array(
					'tag'   => '{{last_note_user_email}}',
					'title' => esc_attr__( 'Last note user email', 'supportcandy' ),
				),
				array(
					'tag'   => '{{last_note_user_first_name}}',
					'title' => esc_attr__( 'Last note user first name', 'supportcandy' ),
				),
				array(
					'tag'   => '{{current_user_name}}',
					'title' => esc_attr__( 'Current user name', 'supportcandy' ),
				),
				array(
					'tag'   => '{{current_user_email}}',
					'title' => esc_attr__( 'Current user email', 'supportcandy' ),
				),
				array(
					'tag'   => '{{current_user_first_name}}',
					'title' => esc_attr__( 'Current user first name', 'supportcandy' ),
				),
				array(
					'tag'   => '{{ticket_history}}',
					'title' => esc_attr__( 'Ticket history (last few report & reply excluding last reply)', 'supportcandy' ),
				),
				array(
					'tag'   => '{{ticket_history_all}}',
					'title' => esc_attr__( 'Ticket history (report & reply)', 'supportcandy' ),
				),
				array(
					'tag'   => '{{ticket_notes_history}}',
					'title' => esc_attr__( 'Ticket history (note)', 'supportcandy' ),
				),
				array(
					'tag'   => '{{ticket_history_all_with_notes}}',
					'title' => esc_attr__( 'Ticket history (report, reply & note)', 'supportcandy' ),
				),
				array(
					'tag'   => '{{ticket_history_all_with_logs}}',
					'title' => esc_attr__( 'Ticket history (report, reply & log)', 'supportcandy' ),
				),
				array(
					'tag'   => '{{ticket_history_all_with_notes_and_logs}}',
					'title' => esc_attr__( 'Ticket history (report, reply, note & log)', 'supportcandy' ),
				),
				array(
					'tag'   => '{{ticket_notes_history_with_logs}}',
					'title' => esc_attr__( 'Ticket history (note & log)', 'supportcandy' ),
				),
				array(
					'tag'   => '{{ticket_url}}',
					'title' => esc_attr__( 'Ticket URL', 'supportcandy' ),
				),
			);

			// Add custom fields.
			foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

				if ( ! (
					class_exists( $cf->type ) &&
					in_array( $cf->field, WPSC_CF_Settings::$allowed_modules['ticket-macro'] ) &&
					$cf->type::$has_macro
				) ) {
					continue;
				}

				$macros[] = array(
					'tag'   => '{{' . $cf->slug . '}}',
					'title' => $cf->name,
				);
			}

			self::$macros = apply_filters( 'wpsc_macros', $macros );
		}

		/**
		 * Get macros
		 *
		 * @return void
		 */
		public static function get_macros() {

			$current_user = WPSC_Current_User::$current_user;
			if ( ! $current_user->is_agent ) {
				wp_send_json_error( new WP_Error( '001', 'Bad request!' ), 400 );
			}

			$gs    = get_option( 'wpsc-gs-general' );
			$title = esc_attr__( 'Insert macro', 'supportcandy' );

			// Sort macros.
			$macros = self::$macros;
			$macros = WPSC_Functions::array_sort( $macros, 'title', SORT_ASC );

			// Unique ID.
			$unique_id = uniqid( 'wpsc_' );

			ob_start();?>

			<div style="width: 100%;">
				<table class="wpsc-setting-tbl wpscMacros">
					<thead>
						<tr>
							<th><?php esc_attr_e( 'Title', 'supportcandy' ); ?></th>
							<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $macros as $macro ) {

							if ( $macro['tag'] == '{{customer_email}}' && ! in_array( $current_user->agent->role, $gs['allow-ar-thread-email'] ) ) {
								continue;
							}
							?>
							<tr>
								<td class="insert-tag lable" data-label="<?php echo esc_attr( $macro['title'] ); ?>" data-tag="<?php echo esc_attr( $macro['tag'] ); ?>"><?php echo esc_attr( $macro['title'] ); ?></td>
								<td>
									<a class="copy-tag wpsc-link" title="<?php echo esc_attr_e( 'Copy Tag' ); ?>"><?php esc_attr_e( 'Copy', 'supportcandy' ); ?></a> |
									<a class="insert-tag wpsc-link" title="<?php echo esc_attr_e( 'Insert Tag' ); ?>"><?php esc_attr_e( 'Insert', 'supportcandy' ); ?></a>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>

			<script>
				var macroTable = jQuery('table.wpscMacros').DataTable({
					ordering: false,
					paging: false, 
					info: false,
					order: [[1, "desc"]],
					columnDefs: [ 
						{ targets: -1, searchable: false, orderable: false },
						{ targets: '_all', className: 'dt-left' }
					],
					language: supportcandy.translations.datatables
				});

				jQuery(document).ready(function() {
					jQuery('div.dataTables_filter input', macroTable.table().container()).focus();
				})

				// Insert tag directly into editor
				jQuery('.insert-tag').click(function(){

					var text_to_insert = jQuery(this).closest('tr').find('td:first-child').data('tag');
					var is_tinymce = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
					if (is_tinymce) {
						tinymce.activeEditor.execCommand('mceInsertContent', false, text_to_insert);
					} else {
						var $txt = jQuery(".wpsc_textarea");
						var caretPos = $txt[0].selectionStart;
						var textAreaTxt = $txt.val();
						$txt.val(textAreaTxt.substring(0, caretPos) + text_to_insert + textAreaTxt.substring(caretPos));
					}
					wpsc_close_modal();
				});
				// Copy tag to clipboard
				jQuery('.copy-tag').click(function(){

					var text_to_copy = jQuery(this).closest('tr').find('td:first-child').data('tag');
					var temp = jQuery("<input>");
					jQuery("body").append(temp);
					temp.val(text_to_copy).select();
					document.execCommand("copy");
					temp.remove();
					wpsc_close_modal();
				});
			</script>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Close', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_after_macro' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);
			wp_send_json( $response );
		}

		/**
		 * Replace macros in string
		 *
		 * @param string      $str - String to replace macros in.
		 * @param WPSC_ticket $ticket - ticket object.
		 * @param string      $module - module name.
		 * @return string
		 */
		public static function replace( $str, $ticket, $module = '' ) {

			// validate ticket object.
			if ( is_object( $ticket ) && ! intval( $ticket->id ) ) {
				return $str;
			}

			// get all macros within string so that will replace only matched.
			preg_match_all( '/{(\w*)}/', $str, $matches );
			$matches = isset( $matches[1] ) ? array_unique( $matches[1] ) : array();

			// replace matched tags.
			foreach ( $matches as $macro ) {

				switch ( $macro ) {

					case 'ticket_id':
						$cf  = WPSC_Custom_Field::get_cf_by_slug( 'id' );
						$str = str_replace( '{{ticket_id}}', $cf->type::get_ticket_field_val( $cf, $ticket, $module ), $str );
						break;

					case 'ticket_status':
						$cf  = WPSC_Custom_Field::get_cf_by_slug( 'status' );
						$str = str_replace( '{{ticket_status}}', $cf->type::get_ticket_field_val( $cf, $ticket, $module ), $str );
						break;

					case 'ticket_category':
						$cf  = WPSC_Custom_Field::get_cf_by_slug( 'category' );
						$str = str_replace( '{{ticket_category}}', $cf->type::get_ticket_field_val( $cf, $ticket, $module ), $str );
						break;

					case 'ticket_priority':
						$cf  = WPSC_Custom_Field::get_cf_by_slug( 'priority' );
						$str = str_replace( '{{ticket_priority}}', $cf->type::get_ticket_field_val( $cf, $ticket, $module ), $str );
						break;

					case 'ticket_subject':
						$cf  = WPSC_Custom_Field::get_cf_by_slug( 'subject' );
						$str = str_replace( '{{ticket_subject}}', $cf->type::get_ticket_field_val( $cf, $ticket, $module ), $str );
						break;

					case 'customer_name':
						$cf  = WPSC_Custom_Field::get_cf_by_slug( 'customer' );
						$str = str_replace( '{{customer_name}}', $cf->type::get_ticket_field_val( $cf, $ticket, $module ), $str );
						break;

					case 'customer_email':
						$cf  = WPSC_Custom_Field::get_cf_by_slug( 'email' );
						$str = str_replace( '{{customer_email}}', $cf->type::get_customer_field_val( $cf, $ticket->customer ), $str );
						break;

					case 'customer_first_name':
						$str = self::replace_customer_first_name( $str, $ticket );
						break;

					case 'ticket_description':
						$cf  = WPSC_Custom_Field::get_cf_by_slug( 'description' );
						$str = str_replace( '{{ticket_description}}', $cf->type::get_ticket_field_val( $cf, $ticket, $module ), $str );
						break;

					case 'last_reply':
						$str = self::replace_last_reply( $str, $ticket );
						break;

					case 'last_reply_user_name':
						$str = self::replace_last_reply_user_name( $str, $ticket );
						break;

					case 'last_reply_user_email':
						$str = self::replace_last_reply_user_email( $str, $ticket );
						break;

					case 'last_reply_user_first_name':
						$str = self::replace_last_reply_user_first_name( $str, $ticket );
						break;

					case 'last_note':
						$str = self::replace_last_note( $str, $ticket );
						break;

					case 'last_note_user_name':
						$str = self::replace_last_note_user_name( $str, $ticket );
						break;

					case 'last_note_user_email':
						$str = self::replace_last_note_user_email( $str, $ticket );
						break;

					case 'last_note_user_first_name':
						$str = self::replace_last_note_user_first_name( $str, $ticket );
						break;

					case 'current_user_name':
						$str = self::replace_current_user_name( $str, $ticket );
						break;

					case 'current_user_email':
						$str = self::replace_current_user_email( $str, $ticket );
						break;

					case 'current_user_first_name':
						$str = self::replace_current_user_first_name( $str, $ticket );
						break;

					case 'previously_assigned_agent':
						$cf  = WPSC_Custom_Field::get_cf_by_slug( 'prev_assignee' );
						$str = str_replace( '{{' . $cf->slug . '}}', $cf->type::get_ticket_field_val( $cf, $ticket, $module ), $str );
						break;

					case 'ticket_history':
						$str = self::replace_ticket_history( $str, $ticket );
						break;

					case 'ticket_history_all':
						$str = self::replace_ticket_history_all( $str, $ticket );
						break;

					case 'ticket_notes_history':
						$str = self::replace_ticket_notes_history( $str, $ticket );
						break;

					case 'ticket_history_all_with_notes':
						$str = self::replace_ticket_history_all_with_notes( $str, $ticket );
						break;

					case 'ticket_history_all_with_logs':
						$str = self::replace_ticket_history_all_with_logs( $str, $ticket );
						break;

					case 'ticket_history_all_with_notes_and_logs':
						$str = self::replace_ticket_history_all_with_notes_and_logs( $str, $ticket );
						break;

					case 'ticket_notes_history_with_logs':
						$str = self::replace_ticket_notes_history_with_logs( $str, $ticket );
						break;

					case 'ticket_url':
						$str = self::replace_ticket_url( $str, $ticket );
						break;

					default:
						// custom fields.
						foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {

							if (
								$cf->type::$slug == 'cf_html' ||
								$cf->slug != $macro
							) {
								continue;
							}

							$val = in_array( $cf->field, array( 'ticket', 'agentonly' ) ) ? $cf->type::get_ticket_field_val( $cf, $ticket, $module ) : $cf->type::get_customer_field_val( $cf, $ticket->customer );
							$str = str_replace( '{{' . $cf->slug . '}}', $val, $str );
						}

						// filter tags.
						$str = apply_filters( 'wpsc_replace_macros', $str, $ticket, $macro );
				}
			}

			// return string after replacing found tags.
			return $str;
		}

		/**
		 * Replace ticket customer first name
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_customer_first_name( $str, $ticket ) {

			$first_name = $ticket->customer->name;
			if ( $ticket->customer->user ) {
				$first_name = get_user_meta( $ticket->customer->user->ID, 'first_name', true );
				if ( ! $first_name ) {
					$first_name = $ticket->customer->name;
				}
			}

			$str = str_replace( '{{customer_first_name}}', $first_name, $str );
			return $str;
		}

		/**
		 * Replace ticket history
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_ticket_history( $str, $ticket ) {

			$advanced = get_option( 'wpsc-ms-advanced-settings' );
			$filters  = array(
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'slug'    => 'ticket',
						'compare' => '=',
						'val'     => $ticket->id,
					),
					array(
						'slug'    => 'type',
						'compare' => 'IN',
						'val'     => array( 'report', 'reply' ),
					),
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => 1,
					),
				),
				'orderby'        => 'id',
				'order'          => 'DESC',
				'items_per_page' => intval( $advanced['ticket-history-macro-threads'] ) + 1,
			);

			$threads = WPSC_Thread::find( $filters );

			if ( $threads['total_items'] ) {

				array_shift( $threads['results'] );
				$history = array_filter(
					array_map(
						fn ( $thread) => $thread->get_history_macro(),
						$threads['results']
					)
				);
				$history = implode( '<br><hr><br>', $history );
				return str_replace( '{{ticket_history}}', $history, $str );

			} else {

				$history = esc_attr__( 'Not Applicable', 'supportcandy' );
				return str_replace( '{{ticket_history}}', $history, $str );
			}
		}

		/**
		 * Replace ticket history all
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_ticket_history_all( $str, $ticket ) {

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
						'compare' => 'IN',
						'val'     => array( 'report', 'reply' ),
					),
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => 1,
					),
				),
				'orderby'    => 'id',
				'order'      => 'DESC',
			);

			$threads = WPSC_Thread::find( $filters );

			if ( $threads['total_items'] ) {

				$history = array_filter(
					array_map(
						fn ( $thread) => $thread->get_history_macro(),
						$threads['results']
					)
				);
				$history = implode( '<br><hr><br>', $history );
				return str_replace( '{{ticket_history_all}}', $history, $str );

			} else {

				$history = esc_attr__( 'Not Applicable', 'supportcandy' );
				return str_replace( '{{ticket_history_all}}', $history, $str );
			}
		}

		/**
		 * Replace ticket notes history
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_ticket_notes_history( $str, $ticket ) {

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
						'val'     => 'note',
					),
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => 1,
					),
				),
				'orderby'    => 'id',
				'order'      => 'DESC',
			);

			$threads = WPSC_Thread::find( $filters );

			if ( $threads['total_items'] ) {

				$history = array_filter(
					array_map(
						fn ( $thread) => $thread->get_history_macro(),
						$threads['results']
					)
				);
				$history = implode( '<br><hr><br>', $history );
				return str_replace( '{{ticket_notes_history}}', $history, $str );

			} else {

				$history = esc_attr__( 'Not Applicable', 'supportcandy' );
				return str_replace( '{{ticket_notes_history}}', $history, $str );
			}
		}

		/**
		 * Replace ticket history all with notes
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_ticket_history_all_with_notes( $str, $ticket ) {

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
						'compare' => 'IN',
						'val'     => array( 'report', 'reply', 'note' ),
					),
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => 1,
					),
				),
				'orderby'    => 'id',
				'order'      => 'DESC',
			);

			$threads = WPSC_Thread::find( $filters );

			if ( $threads['total_items'] ) {

				$history = array_filter(
					array_map(
						fn ( $thread) => $thread->get_history_macro(),
						$threads['results']
					)
				);
				$history = implode( '<div></div><br><hr><br><div></div>', $history );
				return str_replace( '{{ticket_history_all_with_notes}}', $history, $str );

			} else {

				$history = esc_attr__( 'Not Applicable', 'supportcandy' );
				return str_replace( '{{ticket_history_all_with_notes}}', $history, $str );
			}
		}

		/**
		 * Replace ticket history all with logs
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_ticket_history_all_with_logs( $str, $ticket ) {

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
						'compare' => 'IN',
						'val'     => array( 'report', 'reply', 'log' ),
					),
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => 1,
					),
				),
				'orderby'    => 'id',
				'order'      => 'DESC',
			);

			$threads = WPSC_Thread::find( $filters );

			if ( $threads['total_items'] ) {

				$history = array_filter(
					array_map(
						fn ( $thread) => $thread->get_history_macro(),
						$threads['results']
					)
				);
				$history = implode( '<br><hr><br>', $history );
				return str_replace( '{{ticket_history_all_with_logs}}', $history, $str );

			} else {

				$history = esc_attr__( 'Not Applicable', 'supportcandy' );
				return str_replace( '{{ticket_history_all_with_logs}}', $history, $str );
			}
		}

		/**
		 * Replace ticket history all with logs and notes
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_ticket_history_all_with_notes_and_logs( $str, $ticket ) {

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
						'compare' => 'IN',
						'val'     => array( 'report', 'reply', 'note', 'log' ),
					),
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => 1,
					),
				),
				'orderby'    => 'id',
				'order'      => 'DESC',
			);

			$threads = WPSC_Thread::find( $filters );

			if ( $threads['total_items'] ) {

				$history = array_filter(
					array_map(
						fn ( $thread) => $thread->get_history_macro(),
						$threads['results']
					)
				);
				$history = implode( '<br><hr><br>', $history );
				return str_replace( '{{ticket_history_all_with_notes_and_logs}}', $history, $str );

			} else {

				$history = esc_attr__( 'Not Applicable', 'supportcandy' );
				return str_replace( '{{ticket_history_all_with_notes_and_logs}}', $history, $str );
			}
		}

		/**
		 * Replace ticket notes history with logs
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_ticket_notes_history_with_logs( $str, $ticket ) {

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
						'compare' => 'IN',
						'val'     => array( 'note', 'log' ),
					),
					array(
						'slug'    => 'is_active',
						'compare' => '=',
						'val'     => 1,
					),
				),
				'orderby'    => 'id',
				'order'      => 'DESC',
			);

			$threads = WPSC_Thread::find( $filters );

			if ( $threads['total_items'] ) {

				$history = array_filter(
					array_map(
						fn ( $thread) => $thread->get_history_macro(),
						$threads['results']
					)
				);
				$history = implode( '<br><hr><br>', $history );
				return str_replace( '{{ticket_notes_history_with_logs}}', $history, $str );

			} else {

				$history = esc_attr__( 'Not Applicable', 'supportcandy' );
				return str_replace( '{{ticket_notes_history_with_logs}}', $history, $str );
			}
		}

		/**
		 * Replace ticket URL
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_ticket_url( $str, $ticket ) {

			$ticket_url = '<a class="wpsc_link" href="' . $ticket->get_url() . '" target="_blank">' . $ticket->get_url() . '</a>';
			$str        = str_replace( '{{ticket_url}}', $ticket_url, $str );
			return $str;
		}

		/**
		 * Replace ticket last reply
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_last_reply( $str, $ticket ) {

			$thread = $ticket->get_last_reply();
			if ( $thread ) {
				$str = str_replace( '{{last_reply}}', $thread->get_printable_string(), $str );
			} else {
				$str = str_replace(
					'{{last_reply}}',
					esc_attr__( 'Not Applicable', 'supportcandy' ),
					$str
				);
			}

			return $str;
		}

		/**
		 * Replace ticket last reply user name
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_last_reply_user_name( $str, $ticket ) {

			$thread = $ticket->get_last_reply();
			if ( ! $thread ) {
				return str_replace( '{{last_reply_user_name}}', '', $str );
			}

			$str = str_replace( '{{last_reply_user_name}}', $thread->customer->name, $str );
			return $str;
		}

		/**
		 * Replace ticket last reply user email
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_last_reply_user_email( $str, $ticket ) {

			$thread = $ticket->get_last_reply();
			if ( ! $thread ) {
				return str_replace( '{{last_reply_user_email}}', '', $str );
			}

			$str = str_replace( '{{last_reply_user_email}}', $thread->customer->email, $str );
			return $str;
		}

		/**
		 * Replace ticket last reply user first name
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_last_reply_user_first_name( $str, $ticket ) {

			$thread = $ticket->get_last_reply();
			if ( ! $thread ) {
				return str_replace( '{{last_reply_user_first_name}}', '', $str );
			}

			$first_name = $thread->customer->name;
			if ( $thread->customer->user ) {
				$first_name = get_user_meta( $thread->customer->user->ID, 'first_name', true );
				if ( ! $first_name ) {
					$first_name = $thread->customer->name;
				}
			}

			$str = str_replace( '{{last_reply_user_first_name}}', $first_name, $str );
			return $str;
		}

		/**
		 * Replace ticket last note
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_last_note( $str, $ticket ) {

			$thread = $ticket->get_last_note();
			if ( $thread ) {
				$str = str_replace( '{{last_note}}', $thread->get_printable_string(), $str );
			} else {
				$str = str_replace(
					'{{last_note}}',
					esc_attr__( 'Not Applicable', 'supportcandy' ),
					$str
				);
			}

			return $str;
		}

		/**
		 * Replace ticket last note user name
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_last_note_user_name( $str, $ticket ) {

			$thread = $ticket->get_last_note();
			if ( ! $thread ) {
				return str_replace( '{{last_note_user_name}}', '', $str );
			}

			$str = str_replace( '{{last_note_user_name}}', $thread->customer->name, $str );
			return $str;
		}

		/**
		 * Replace ticket last note user email
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_last_note_user_email( $str, $ticket ) {

			$thread = $ticket->get_last_note();
			if ( ! $thread ) {
				return str_replace( '{{last_note_user_email}}', '', $str );
			}

			$str = str_replace( '{{last_note_user_email}}', $thread->customer->email, $str );
			return $str;
		}

		/**
		 * Replace ticket last note user first name
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_last_note_user_first_name( $str, $ticket ) {

			$thread = $ticket->get_last_note();
			if ( ! $thread ) {
				return str_replace( '{{last_note_user_first_name}}', '', $str );
			}

			$first_name = $thread->customer->name;
			if ( $thread->customer->user ) {
				$first_name = get_user_meta( $thread->customer->user->ID, 'first_name', true );
				if ( ! $first_name ) {
					$first_name = $thread->customer->name;
				}
			}

			$str = str_replace( '{{last_note_user_first_name}}', $first_name, $str );
			return $str;
		}

		/**
		 * Replace current user name
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_current_user_name( $str, $ticket ) {

			$current_user = WPSC_Current_User::$current_user;
			if ( $current_user->is_customer ) {
				$str = str_replace( '{{current_user_name}}', $current_user->customer->name, $str );
			} else {
				$str = str_replace( '{{current_user_name}}', esc_attr__( 'Not Applicable', 'supportcandy' ), $str );
			}

			return $str;
		}

		/**
		 * Replace current user email
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_current_user_email( $str, $ticket ) {

			$current_user = WPSC_Current_User::$current_user;
			if ( $current_user->is_customer ) {
				$str = str_replace( '{{current_user_email}}', $current_user->customer->email, $str );
			} else {
				$str = str_replace( '{{current_user_email}}', esc_attr__( 'Not Applicable', 'supportcandy' ), $str );
			}

			return $str;
		}

		/**
		 * Replace current user first name
		 *
		 * @param string      $str - string to replace tags in.
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @return string
		 */
		public static function replace_current_user_first_name( $str, $ticket ) {

			$current_user = WPSC_Current_User::$current_user;

			if ( $current_user->is_customer ) {
				$first_name = $current_user->customer->name;
				if ( $current_user->customer->user ) {
					$first_name = get_user_meta( $current_user->user->ID, 'first_name', true );
					if ( ! $first_name ) {
						$first_name = $current_user->customer->name;
					}
				}
				$str = str_replace( '{{current_user_first_name}}', $first_name, $str );
			} else {
				$str = str_replace( '{{current_user_first_name}}', esc_attr__( 'Not Applicable', 'supportcandy' ), $str );
			}

			return $str;
		}
	}
endif;

WPSC_Macros::init();
