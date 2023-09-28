<?php
/**
 * The style "default" of the Chat
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

 use TrxAddons\AiHelper\Lists;

 $args = get_query_var('trx_addons_args_sc_chat');

do_action( 'trx_addons_action_sc_chat_before', $args );

?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
	class="sc_chat sc_chat_<?php
		echo esc_attr( $args['type'] );
		if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
		?>"<?php
	if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
	trx_addons_sc_show_attributes( 'sc_chat', $args, 'sc_wrapper' );
	?>><?php

	trx_addons_sc_show_titles('sc_chat', $args);

	do_action( 'trx_addons_action_sc_chat_before_content', $args );

	?><div class="sc_chat_content sc_item_content"<?php trx_addons_sc_show_attributes( 'sc_chat', $args, 'sc_items_wrapper' ); ?>>
		<div class="sc_chat_form"
			data-chat-limit-exceed="<?php echo esc_attr( trx_addons_get_option( "ai_helper_sc_chat_limit_alert" . ( ! empty( $args['premium'] ) ? '_premium' : '' ) ) ); ?>"
			data-chat-settings="<?php
				echo esc_attr( trx_addons_encode_settings( array(
					'premium' => ! empty( $args['premium'] ) ? 1 : 0,
				) ) );
		?>">
			<div class="sc_chat_form_inner">
				<?php
				$trx_addons_ai_helper_prompt_id = 'sc_chat_form_field_prompt_' . mt_rand();
				?>
				<label for="<?php echo esc_attr( $trx_addons_ai_helper_prompt_id ); ?>" class="sc_chat_form_field_prompt_label"><?php
					esc_attr_e('How can I help you?', 'trx_addons');
					?><a href="#" class="sc_chat_form_start_new trx_addons_hidden"><?php
						esc_html_e('New chat', 'trx_addons');
				?></a></label>
				<div class="sc_chat_result">
					<ul class="sc_chat_list"></ul>
				</div>
				<div class="sc_chat_form_field sc_chat_form_field_prompt">
					<div class="sc_chat_form_field_inner">
						<input id="<?php echo esc_attr( $trx_addons_ai_helper_prompt_id ); ?>" type="text" value="<?php echo esc_attr( $args['prompt'] ); ?>" class="sc_chat_form_field_prompt_text" placeholder="<?php esc_attr_e('Type your message ...', 'trx_addons'); ?>">
						<a href="#" class="sc_chat_form_field_prompt_button<?php if ( empty( $args['prompt'] ) ) echo ' sc_chat_form_field_prompt_button_disabled'; ?>"><?php
							if ( ! empty( $args['button_text'] ) ) {
								echo esc_html( $args['button_text'] );
							} else {
								esc_html_e('Send', 'trx_addons');
							}
						?></a>
					</div>
				</div><?php
				if ( ! empty( $args['show_limits'] ) ) {
					$premium = ! empty( $args['premium'] ) && (int)$args['premium'] == 1;
					$suffix = $premium ? '_premium' : '';
					$limits = (int)trx_addons_get_option( "ai_helper_sc_chat_limits{$suffix}" ) > 0;
					if ( $limits ) {
						$generated = 0;
						if ( $premium ) {
							$user_id = get_current_user_id();
							$user_level = apply_filters( 'trx_addons_filter_sc_chat_user_level', $user_id > 0 ? 'default' : '', $user_id );
							if ( ! empty( $user_level ) ) {
								$levels = trx_addons_get_option( "ai_helper_sc_chat_levels_premium" );
								$level_idx = trx_addons_array_search( $levels, 'level', $user_level );
								$user_limit = $level_idx !== false ? $levels[ $level_idx ] : false;
								if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
									$generated = trx_addons_sc_chat_get_total_generated( $user_limit['per'], $suffix, $user_id );
								}
							}
						}
						if ( ! $premium || empty( $user_level ) || ! isset( $user_limit['limit'] ) || trim( $user_limit['limit'] ) === '' ) {
							$generated = trx_addons_sc_chat_get_total_generated( 'hour', $suffix );
							$user_limit = array(
								'limit' => (int)trx_addons_get_option( "ai_helper_sc_chat_limit_per_hour{$suffix}" ),
								'requests' => (int)trx_addons_get_option( "ai_helper_sc_chat_limit_per_visitor{$suffix}" ),
								'per' => 'hour'
							);
						}
						if ( isset( $user_limit['limit'] ) && trim( $user_limit['limit'] ) !== '' ) {
							?><div class="sc_chat_limits">
								<span class="sc_chat_limits_total"><?php
									$periods = Lists::get_list_periods();
									echo wp_kses( sprintf(
														__( 'Limits%s: %s%s.', 'trx_addons' ),
														! empty( $periods[ $user_limit['per'] ] ) ? ' ' . sprintf( __( 'per %s', 'trx_addons' ), strtolower( $periods[ $user_limit['per'] ] ) ) : '',
														sprintf( __( '%s requests', 'trx_addons' ), '<span class="sc_chat_limits_total_value">' . (int)$user_limit['limit'] . '</span>' ),
														! empty( $user_limit['requests'] ) ? ' ' . sprintf( __( ' for all visitors and up to %s requests from a single visitor', 'trx_addons' ), '<span class="sc_chat_limits_total_requests">' . (int)$user_limit['requests'] . '</span>' ) : '',
													),
													'trx_addons_kses_content'
												);
								?></span>
								<span class="sc_chat_limits_used"><?php
									echo wp_kses( sprintf(
														__( 'Used: %s requests%s.', 'trx_addons' ),
														'<span class="sc_chat_limits_used_value">' . min( $generated, (int)$user_limit['limit'] )  . '</span>',
														! empty( $user_limit['requests'] ) ? ' ' . sprintf( __( 'from all visitors and %s requests from the current user', 'trx_addons' ), '<span class="sc_chat_limits_used_requests">' . (int)trx_addons_get_value_gpc( 'trx_addons_ai_helper_chat_count' ) . '</span>' ) : '',
													),
													'trx_addons_kses_content'
												);
								?></span>
							</div><?php
						}
					}
				}
				?><div class="sc_chat_message">
					<div class="sc_chat_message_inner"></div>
					<a href="#" class="sc_chat_message_close trx_addons_button_close" title="<?php esc_html_e( 'Close', 'trx_addons' ); ?>"><span class="trx_addons_button_close_icon"></span></a>
				</div>
			</div>
		</div>
	</div>

	<?php
	do_action( 'trx_addons_action_sc_chat_after_content', $args );

	trx_addons_sc_show_links('sc_chat', $args);
	?>

</div><?php

do_action( 'trx_addons_action_sc_chat_after', $args );
