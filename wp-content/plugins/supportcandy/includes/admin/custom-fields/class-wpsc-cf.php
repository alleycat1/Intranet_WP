<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_CF' ) ) :

	final class WPSC_CF {

		/**
		 * Allowed custom field properties
		 *
		 * @var array
		 */
		public static $allowed_properties = array(
			'extra_info',
			'placeholder_text',
			'char_limit',
			'date_format',
			'date_range',
			'start_range',
			'end_range',
			'time_format',
			'tl_width',
			'allow_ticket_form',
			'allow_my_profile',
			'is_personal_info',
		);

		/**
		 * Initialize this class
		 */
		public static function init() {

			// Register this field category (ticket).
			add_filter( 'wpsc_custom_field_categories', array( __CLASS__, 'register_field' ) );

			// List.
			add_action( 'wp_ajax_wpsc_get_customer_fields', array( __CLASS__, 'get_customer_fields' ) );

			// Filter custom field types for new custom field.
			add_filter( 'wpsc_add_new_custom_field_cf_types', array( __CLASS__, 'filter_cf_types' ), 10, 2 );
		}

		/**
		 * Register field category
		 *
		 * @param array $fields - customer fields.
		 * @return array
		 */
		public static function register_field( $fields ) {

			$fields['customer'] = __CLASS__;
			return $fields;
		}

		/**
		 * Get customer fields
		 *
		 * @return void
		 */
		public static function get_customer_fields() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}?>

			<div class="wpsc-setting-header">
				<h2><?php esc_attr_e( 'Customer Fields', 'supportcandy' ); ?></h2>
			</div>
			<div class="wpsc-setting-section-body">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/customer-fields/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<table class="ticket-fields wpsc-setting-tbl">
					<thead>
						<tr>
							<th><?php esc_attr_e( 'Field', 'supportcandy' ); ?></th>
							<th><?php esc_attr_e( 'Extra info', 'supportcandy' ); ?></th>
							<th><?php esc_attr_e( 'Actions', 'supportcandy' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( WPSC_Custom_Field::$custom_fields as $cf ) {
							if ( $cf->field != 'customer' ) {
								continue;
							}
							?>
							<tr>
								<td><?php echo esc_attr( $cf->name ); ?></td>
								<td><?php echo esc_attr( $cf->extra_info ); ?></td>
								<td>
									<a href="javascript:wpsc_get_edit_custom_field(<?php echo esc_attr( $cf->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_edit_custom_field' ) ); ?>');" class="wpsc-link"><?php esc_attr_e( 'Edit', 'supportcandy' ); ?></a>
									<?php
									if ( ! $cf->type::$is_default ) {
										echo esc_attr( ' | ' );
										?>
										<a href="javascript:wpsc_delete_custom_field(<?php echo esc_attr( $cf->id ); ?>, '<?php echo esc_attr( wp_create_nonce( 'wpsc_delete_custom_field' ) ); ?>');" class="wpsc-link"><?php esc_attr_e( 'Delete', 'supportcandy' ); ?></a>
										<?php
									}
									?>
								</td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
				<script>
					jQuery('table.ticket-fields').DataTable({
						ordering: false,
						pageLength: 20,
						bLengthChange: false,
						columnDefs: [ 
							{ targets: -1, searchable: false },
							{ targets: '_all', className: 'dt-left' }
						],
						dom: 'Bfrtip',
						buttons: [
							{
								text: '<?php esc_attr_e( 'Add new', 'supportcandy' ); ?>',
								className: 'wpsc-button small primary',
								action: function ( e, dt, node, config ) {
									wpsc_get_add_new_custom_field('customer', '<?php echo esc_attr( wp_create_nonce( 'wpsc_get_add_new_custom_field' ) ); ?>');
								}
							}
						],
						language: supportcandy.translations.datatables
					});
				</script>
			</div>
			<?php
			wp_die();
		}

		/**
		 * Filter out unwanted custom field types from add new custom field section for customer fields
		 *
		 * @param array  $cf_types - cf_types.
		 * @param string $field - custom field category (ticket, agentonly, customer, etc.).
		 * @return array
		 */
		public static function filter_cf_types( $cf_types, $field ) {

			if ( $field == 'customer' && in_array( 'cf_html', array_keys( $cf_types ) ) ) {
				unset( $cf_types['cf_html'] );
			}

			return $cf_types;
		}
	}
endif;

WPSC_CF::init();
