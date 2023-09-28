<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly!
}

if ( ! class_exists( 'WPSC_ITW_Ticket_Tags' ) ) :

	final class WPSC_ITW_Ticket_Tags {

		/**
		 * Initialize this class
		 *
		 * @return void
		 */
		public static function init() {

			// get edit ticket tags.
			add_action( 'wp_ajax_wpsc_it_set_edit_ticket_tags', array( __CLASS__, 'update_edit_ticket_tags' ) );
			add_action( 'wp_ajax_nopriv_wpsc_it_set_edit_ticket_tags', array( __CLASS__, 'update_edit_ticket_tags' ) );

			// Ticket tags.
			add_action( 'wp_ajax_wpsc_get_tw_ticket_tags', array( __CLASS__, 'get_tw_ticket_tags' ) );
			add_action( 'wp_ajax_wpsc_set_tw_ticket_tags', array( __CLASS__, 'set_tw_ticket_tags' ) );

			// tags autocomplete.
			add_action( 'wp_ajax_wpsc_tag_autocomplete', array( __CLASS__, 'tag_autocomplete' ) );
			add_action( 'wp_ajax_nopriv_wpsc_tag_autocomplete', array( __CLASS__, 'tag_autocomplete' ) );
		}

		/**
		 * Prints body of current widget
		 *
		 * @param WPSC_Ticket $ticket - ticket object.
		 * @param array       $settings - widget settings.
		 * @return void
		 */
		public static function print_widget( $ticket, $settings ) {

			$current_user = WPSC_Current_User::$current_user;
			if ( ! (
				(
					(
						WPSC_Individual_Ticket::$view_profile == 'customer' ||
						$ticket->customer->id == $current_user->customer->id
					) &&
					$settings['allow-customer']
				) ||
				( WPSC_Individual_Ticket::$view_profile == 'agent' && in_array( $current_user->agent->role, $settings['allowed-agent-roles'] ) )
			) ) {
				return;
			}

			?>

			<div class="wpsc-it-widget wpsc-itw-ticket-tags">
				<div class="wpsc-widget-header">
					<h2>
						<?php
						$settings_title = $settings['title'] ? WPSC_Translations::get( 'wpsc-twt-tags', stripslashes( $settings['title'] ) ) : stripslashes( $settings['title'] );
						echo esc_attr( $settings_title )
						?>
					</h2>
					<?php
					if ( $ticket->is_active && WPSC_Individual_Ticket::$view_profile == 'agent' && WPSC_Individual_Ticket::has_ticket_cap( 'tt' ) ) :
						?>
						<span class="wpsc-add-tag-trigger"><?php WPSC_Icons::get( 'edit' ); ?></span>
						<?php
					endif
					?>
				</div>
				<div class="wpsc-widget-body">
					<div class="wpsc-it-tag-body">
						<?php
						if ( $ticket->tags ) {
							?>
							<div class="wpsc-tag-list wpsc-add-tag-trigger">
								<?php
								foreach ( $ticket->tags as $tag ) {
									if ( ! $tag->id ) {
										continue;
									}
									?>
									<div class="wpsc-ticket-tag" style="background-color: <?php echo esc_attr( $tag->bg_color ); ?>; color: <?php echo esc_attr( $tag->color ); ?>;">
										<?php echo esc_attr( $tag->name ); ?>
									</div>
									<?php
								}
								?>
							</div>
							<?php
						} else {
							?>
							<div class="wpsc-widget-default wpsc-add-tag-trigger"><?php esc_attr_e( 'Not Applicable', 'supportcandy' ); ?></div>
							<?php
						}
						?>
					</div>
					<?php
					if ( $ticket->is_active ) {
						?>
						<div class="wpsc-select-ticket-tags" style="display: none;">
							<select id="wpsc-tags" class="wpsc-tags" multiple name="tags[]">
								<?php
								foreach ( $ticket->tags as $tag ) {
									if ( ! $tag->id ) {
										continue;
									}
									?>
									<option selected="selected" value="<?php echo esc_attr( $tag->id ); ?>"><?php echo esc_attr( $tag->name ); ?></option>
									<?php
								}
								?>
							</select>
							<div class="wpsc-ticket-tags-action">
								<span class="wpsc-close-ticket-tag"><?php WPSC_Icons::get( 'times' ); ?></span>
								<span class="wpsc-add-ticket-tag"><?php WPSC_Icons::get( 'check' ); ?></span>
							</div>
							<?php
							if ( $ticket->is_active && WPSC_Individual_Ticket::$view_profile == 'agent' && WPSC_Individual_Ticket::has_ticket_cap( 'tt' ) ) :
								?>
								<script>
									jQuery(document).ready(function(){
										jQuery(".wpsc-add-tag-trigger").click(function(){
											jQuery(".wpsc-it-tag-body").hide();
											jQuery(".wpsc-select-ticket-tags").show();

											if (!jQuery('select.wpsc-tags').hasClass("select2-hidden-accessible")) {
												jQuery('select.wpsc-tags').selectWoo({
													tags: true,
													ajax: {
														url: supportcandy.ajax_url,
														dataType: 'json',
														delay: 250,
														data: function (params) {
															return {
																q: params.term, // search term
																page: params.page,
																action: 'wpsc_tag_autocomplete',
																tags: jQuery( this ).val(),
																_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_tag_autocomplete' ) ); ?>'
															};
														},
														processResults: function (data, params) {
															var terms = [];
															if ( data ) {
																jQuery.each( data, function( id, text ) {
																	terms.push( { id: text.id, text: text.title } );
																});
															}
															return {
																results: terms
															};
														},
														cache: true
													},
													escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
													minimumInputLength: 0,
													allowClear: false,
													placeholder: ""
												});
											}
										});

										jQuery(".wpsc-add-ticket-tag").click(function(){
											var tags = jQuery( 'select.wpsc-tags:visible' ).val();
											if( ! tags ) {
												return;
											}
											ticket_id = jQuery('#wpsc-current-ticket').val();
											var data = {
												action: 'wpsc_it_set_edit_ticket_tags',
												tags,
												ticket_id,
												_ajax_nonce: '<?php echo esc_attr( wp_create_nonce( 'wpsc_it_set_edit_ticket_tags' ) ); ?>'
											};
											jQuery.post(
												supportcandy.ajax_url,
												data,
												function (response) {

												}
											).done(
												function (res) {
													wpsc_get_individual_ticket( ticket_id );
												}
											);
											jQuery(".wpsc-it-tag-body").show();
											jQuery(".wpsc-select-ticket-tags").hide();
										});

										jQuery(".wpsc-close-ticket-tag").click(function(){
											ticket_id = jQuery('#wpsc-current-ticket').val();
											wpsc_get_individual_ticket(<?php echo intval( $ticket->id ); ?>);
										});
									});
								</script>
								<?php
							endif
							?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		}

		/**
		 * Tag autocomplete
		 *
		 * @return void
		 */
		public static function tag_autocomplete() {

			if ( check_ajax_referer( 'wpsc_tag_autocomplete', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';

			$tags = isset( $_GET['tags'] ) ? array_filter( array_map( 'intval', $_GET['tags'] ) ) : array();

			$args = array(
				'items_per_page' => 5,
				'search'         => $term,
				'order'          => 'ASC',
				'orderby'        => 'id',
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'slug'    => 'id',
						'compare' => 'NOT IN',
						'val'     => $tags,
					),
				),
			);

			$tags = WPSC_Ticket_Tags::find( $args )['results'];
			$response = array();
			foreach ( $tags as $tag ) {
				$response[] = array(
					'id'    => $tag->id,
					'title' => $tag->name,
				);
			}

			wp_send_json( $response );
		}

		/**
		 *  Set ticket  tags
		 *
		 * @return void
		 */
		public static function update_edit_ticket_tags() {

			if ( check_ajax_referer( 'wpsc_it_set_edit_ticket_tags', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			WPSC_Individual_Ticket::load_current_ticket();

			$current_user = WPSC_Current_User::$current_user;
			if ( ! ( WPSC_Individual_Ticket::$view_profile == 'agent' && WPSC_Individual_Ticket::has_ticket_cap( 'tt' ) ) ) {
				wp_send_json_error( 'Bad request', 400 );
			}

			$ticket = WPSC_Individual_Ticket::$ticket;
			if ( ! $ticket->is_active ) {
				wp_send_json_error( 'Something went wrong!', 400 );
			}

			$tags = isset( $_POST['tags'] ) ? array_filter( array_map( 'sanitize_text_field', wp_unslash( $_POST['tags'] ) ) ) : array();

			$general = get_option( 'wpsc-ticket-tags-general-settings' );

			$temp = array();
			foreach ( $ticket->tags as $tem ) {
				$temp[] = $tem->id;
			}

			$ticket_tags = array();
			foreach ( $tags as $key => $tag_id ) {

				$tag = new WPSC_Ticket_Tags( $tag_id );
				if ( ! $tag->id ) {

					$data = array(
						'name'        => $tag_id,
						'description' => '',
						'color'       => $general['color'],
						'bg_color'    => $general['bg-color'],
					);
					$new_tag = WPSC_Ticket_Tags::insert( $data );
					$tags[ $key ] = $new_tag->id;
				}
				$ticket_tags[] = $tags[ $key ];
			}
			$ticket_tags = array_unique( $ticket_tags );
			$ticket->tags = $ticket_tags;
			$ticket->date_updated = new DateTime();
			$ticket->save();
			wp_die();
		}

		/**
		 * Get Ticket tags
		 */
		public static function get_tw_ticket_tags() {

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$ticket_widgets    = get_option( 'wpsc-ticket-widget', array() );
			$ticket_tags = $ticket_widgets['tags'];
			$title             = $ticket_tags['title'];
			$roles             = get_option( 'wpsc-agent-roles', array() );
			ob_start();
			?>
			<form action="#" onsubmit="return false;" class="wpsc-frm-edit-tags">
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Title', 'supportcandy' ); ?></label>
					</div>
					<input name="label" type="text" value="<?php echo esc_attr( $ticket_tags['title'] ); ?>" autocomplete="off">
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Enable', 'supportcandy' ); ?></label>
					</div>
					<select name="is_enable">
						<option <?php selected( $ticket_tags['is_enable'], '1' ); ?> value="1"><?php esc_attr_e( 'Yes', 'supportcandy' ); ?></option>
						<option <?php selected( $ticket_tags['is_enable'], '0' ); ?>  value="0"><?php esc_attr_e( 'No', 'supportcandy' ); ?></option>
					</select>
				</div>
				<div class="wpsc-input-group">
					<div class="label-container">
						<label for=""><?php esc_attr_e( 'Allowed agent roles', 'supportcandy' ); ?></label>
					</div>
					<select  multiple id="wpsc-select-agents" name="agents[]" placeholder="search agent...">
						<?php
						foreach ( $roles as $key => $role ) :
							$selected = in_array( $key, $ticket_tags['allowed-agent-roles'] ) ? 'selected="selected"' : ''
							?>
							<option <?php echo esc_attr( $selected ); ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $role['label'] ); ?></option>
							<?php
							endforeach;
						?>
					</select>
				</div>
				<script>
					jQuery('#wpsc-select-agents').selectWoo({
						allowClear: false,
						placeholder: ""
					});
				</script>
				<?php do_action( 'wpsc_get_ticket_tags_body' ); ?>
				<input type="hidden" name="action" value="wpsc_set_tw_ticket_tags">
				<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( wp_create_nonce( 'wpsc_set_tw_ticket_tags' ) ); ?>">
			</form>
			<?php
			$body = ob_get_clean();

			ob_start();
			?>
			<button class="wpsc-button small primary" onclick="wpsc_set_tw_ticket_tags(this);">
				<?php esc_attr_e( 'Submit', 'supportcandy' ); ?>
			</button>
			<button class="wpsc-button small secondary" onclick="wpsc_close_modal();">
				<?php esc_attr_e( 'Cancel', 'supportcandy' ); ?>
			</button>
			<?php
			do_action( 'wpsc_get_tw_ticket_tags_widget_footer' );
			$footer = ob_get_clean();

			$response = array(
				'title'  => $title,
				'body'   => $body,
				'footer' => $footer,
			);

			wp_send_json( $response );
		}

		/**
		 * Set ticket tags
		 */
		public static function set_tw_ticket_tags() {

			if ( check_ajax_referer( 'wpsc_set_tw_ticket_tags', '_ajax_nonce', false ) != 1 ) {
				wp_send_json_error( 'Unauthorised request!', 401 );
			}

			if ( ! WPSC_Functions::is_site_admin() ) {
				wp_send_json_error( __( 'Unauthorized access!', 'supportcandy' ), 401 );
			}

			$label = isset( $_POST['label'] ) ? sanitize_text_field( wp_unslash( $_POST['label'] ) ) : '';
			if ( ! $label ) {
				wp_send_json_error( __( 'Bad request!', 'supportcandy' ), 400 );
			}

			$is_enable = isset( $_POST['is_enable'] ) ? intval( $_POST['is_enable'] ) : 0;
			$agents    = isset( $_POST['agents'] ) ? array_filter( array_map( 'intval', $_POST['agents'] ) ) : array();

			$ticket_widgets = get_option( 'wpsc-ticket-widget', array() );

			$ticket_widgets['tags']['title']               = $label;
			$ticket_widgets['tags']['is_enable']           = $is_enable;
			$ticket_widgets['tags']['allowed-agent-roles'] = $agents;
			update_option( 'wpsc-ticket-widget', $ticket_widgets );

			// remove string translations.
			WPSC_Translations::remove( 'wpsc-twt-tags' );
			WPSC_Translations::add( 'wpsc-twt-tags', stripslashes( $label ) );
			wp_die();
		}
	}

endif;

WPSC_ITW_Ticket_Tags::init();
