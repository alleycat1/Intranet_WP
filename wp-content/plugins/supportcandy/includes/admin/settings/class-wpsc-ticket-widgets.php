<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_Ticket_Widgets' ) ) :

	final class WPSC_Ticket_Widgets {

		/**
		 * Initialize this class
		 */
		public static function init() {

			// Ticket widget.
			add_action( 'wp_ajax_wpsc_get_ticket_widget', array( __CLASS__, 'get_ticket_widget' ) );
			add_action( 'wp_ajax_wpsc_set_tw_load_order', array( __CLASS__, 'set_tw_load_order' ) );

			// allow access to new agent role.
			add_action( 'wpsc_after_add_agent_role', array( __CLASS__, 'after_add_agent_role' ) );
		}

		/**
		 * Load ticket widgets
		 */
		public static function get_ticket_widget() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );
			ob_start(); ?>

			<div class="wpsc-setting-header">
				<h2><?php esc_attr_e( 'Ticket Widgets', 'supportcandy' ); ?></h2>
			</div>
			<div class="wpsc-setting-section-body">
				<div class="wpsc-dock-container">
					<?php
					printf(
						/* translators: Click here to see the documentation */
						esc_attr__( '%s to see the documentation!', 'supportcandy' ),
						'<a href="https://supportcandy.net/docs/ticket-widget-settings/" target="_blank">' . esc_attr__( 'Click here', 'supportcandy' ) . '</a>'
					);
					?>
				</div>
				<div class="wpsc-setting-cards-container ui-sortable">
					<?php
					foreach ( $ticket_widgets as $key => $ticket_widget ) {
						if ( ! class_exists( $ticket_widget['class'] ) ) {
							continue;
						}
						$style = ! $ticket_widget['is_enable'] ? 'background-color:#eec7ca;color:#dc2222' : '';
						?>
						<div class="wpsc-setting-card" data-id="<?php echo esc_attr( $key ); ?>" style="<?php echo esc_attr( $style ); ?>">
							<span class="wpsc-sort-handle action-btn"><?php WPSC_Icons::get( 'sort' ); ?></span>
							<span class="title">
								<?php
								$ticket_widget_title = $ticket_widget['title'] ? WPSC_Translations::get( 'wpsc-twt-' . $key, stripslashes( htmlspecialchars( $ticket_widget['title'] ) ) ) : stripslashes( htmlspecialchars( $ticket_widget['title'] ) );
								echo esc_attr( $ticket_widget_title );
								?>
							</span>
							<div class="actions">
								<span class="action-btn"  onclick="<?php echo esc_attr( $ticket_widget['callback'] ); ?>"><?php WPSC_Icons::get( 'edit' ); ?></span>
							</div>
						</div>
						<?php
					}
					?>
				</div>
				<div class="setting-footer-actions">
					<button class="wpsc-button normal secondary wpsc-save-sort-order"><?php esc_attr_e( 'Save Order', 'supportcandy' ); ?></button>
				</div>
			</div>
			<script>
				var items = jQuery( ".wpsc-setting-cards-container" ).sortable({ handle: '.wpsc-sort-handle' });
				jQuery(".wpsc-save-sort-order").click(function(){
					var slugs = items.sortable( "toArray", {attribute: 'data-id'} );
					wpsc_set_tw_load_order(slugs, '<?php echo esc_attr( wp_create_nonce( 'wpsc_set_tw_load_order' ) ); ?>');
				});
			</script>
			<?php
			wp_die();
		}

		/**
		 * Set ticket widgets order
		 *
		 * @return void
		 */
		public static function set_tw_load_order() {

			if ( check_ajax_referer( 'wpsc_set_tw_load_order', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$slugs = isset( $_POST['slugs'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['slugs'] ) ) ) : array();
			if ( ! $slugs ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}
			$sorted_widgets = array();

			$ticket_widgets      = get_option( 'wpsc-ticket-widget', array() );
			$ticket_widgets_keys = array_keys( $ticket_widgets );
			// Verifying if slug is present in list item.
			foreach ( $slugs as $slug ) {
				if ( ! in_array( $slug, $ticket_widgets_keys ) ) {
					wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
				}
			}

			foreach ( $slugs as $slug ) :
				$sorted_widgets[ $slug ] = $ticket_widgets[ $slug ];
			endforeach;
			update_option( 'wpsc-ticket-widget', $sorted_widgets );
			wp_die();
		}

		/**
		 * After new agent role added add that role in ticket widgets
		 *
		 * @param integer $role_id - agent role id.
		 * @return void
		 */
		public static function after_add_agent_role( $role_id ) {

			$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );
			foreach ( $ticket_widgets as $key => $widget ) {

				$widget['allowed-agent-roles'][] = $role_id;
				$ticket_widgets[ $key ]          = $widget;
			}
			update_option( 'wpsc-ticket-widget', $ticket_widgets );
		}
	}

endif;

WPSC_Ticket_Widgets::init();
