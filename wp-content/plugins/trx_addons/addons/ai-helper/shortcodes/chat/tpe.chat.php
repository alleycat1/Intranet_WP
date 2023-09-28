<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

extract( get_query_var( 'trx_addons_args_sc_chat' ) );
?><#
settings = trx_addons_elm_prepare_global_params( settings );

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_chat_' + ( '' + Math.random() ).replace( '.', '' );

#><div id="{{ id }}" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_chat sc_chat_' + settings.type, settings ) ); #>">

	<?php $element->sc_show_titles( 'sc_chat' ); ?>

	<div class="sc_chat_content sc_item_content">
		<div class="sc_chat_form">
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
						<input type="text" value="{{ settings.prompt }}" class="sc_chat_form_field_prompt_text" placeholder="<?php esc_attr_e('Type your message ...', 'trx_addons'); ?>">
						<a href="#" class="sc_chat_form_field_prompt_button<# if ( ! settings.prompt ) print( ' sc_chat_form_field_prompt_button_disabled' ); #>">{{{ settings.button_text || '<?php esc_html_e('Send', 'trx_addons'); ?>' }}}</a>
					</div>
				</div><#
				if ( settings.show_limits ) {
					#><div class="sc_chat_limits">
						<span class="sc_chat_limits_label"><?php
							esc_html_e( 'Limits per hour (day/week/month/year): XX requests.', 'trx_addons' );
						?></span>
						<span class="sc_chat_limits_value"><?php
							esc_html_e( 'Used: YY requests.', 'trx_addons' );
						?></span>
					</div><#
				}
			#></div>
		</div>
	</div>

	<?php $element->sc_show_links('sc_chat'); ?>

</div><#

settings = trx_addons_elm_restore_global_params( settings );
#>