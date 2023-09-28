<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v2.22.0
 */

extract( get_query_var( 'trx_addons_args_sc_tgenerator' ) );
?><#
settings = trx_addons_elm_prepare_global_params( settings );

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_tgenerator_' + ( '' + Math.random() ).replace( '.', '' );

#><div id="{{ id }}" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_tgenerator sc_tgenerator_' + settings.type, settings ) ); #>">

	<?php $element->sc_show_titles( 'sc_tgenerator' ); ?>

	<div class="sc_tgenerator_content sc_item_content">
		<div class="sc_tgenerator_form">
			<div class="sc_tgenerator_form_inner"<#
				if ( settings.prompt_width.size && settings.prompt_width.size < 100 ) {
					print( ' style="width:' + settings.prompt_width.size + '%"' );
				}
			#>">
				<div class="sc_tgenerator_form_field sc_tgenerator_form_field_prompt">
					<div class="sc_tgenerator_form_field_inner">
						<input type="text" value="{{ settings.prompt }}" class="sc_tgenerator_form_field_prompt_text" placeholder="<?php esc_attr_e('Describe what you want or select a "Text type" or a "Process text" below', 'trx_addons'); ?>">
						<a href="#" class="sc_tgenerator_form_field_prompt_button<# if ( ! settings.prompt ) print( ' sc_tgenerator_form_field_prompt_button_disabled' ); #>">{{{ settings.button_text || '<?php esc_html_e('Generate', 'trx_addons'); ?>' }}}</a>
					</div>
				</div>
				<div class="sc_tgenerator_form_field sc_tgenerator_form_field_tags">
					<span class="sc_tgenerator_form_field_tags_label"><?php esc_html_e( 'Write a', 'trx_addons' ); ?></span>
					<?php trx_addons_show_layout( trx_addons_sc_tgenerator_get_list_commands( 'write' ) ); ?>
					<span class="sc_tgenerator_form_field_tags_label"><?php esc_html_e( 'or', 'trx_addons' ); ?></span>
					<?php trx_addons_show_layout( trx_addons_sc_tgenerator_get_list_commands( 'process' ) ); ?>
					<span class="sc_tgenerator_form_field_tags_label sc_tgenerator_form_field_hidden"><?php esc_html_e( 'to', 'trx_addons' ); ?></span>
					<?php
					trx_addons_show_layout( trx_addons_sc_tgenerator_get_list_tones() );
					trx_addons_show_layout( trx_addons_sc_tgenerator_get_list_languages() );
					?>
				</div><#
				if ( settings.show_limits ) {
					#><div class="sc_tgenerator_limits">
						<span class="sc_tgenerator_limits_label"><?php
							esc_html_e( 'Limits per hour (day/week/month/year): XX requests.', 'trx_addons' );
						?></span>
						<span class="sc_tgenerator_limits_value"><?php
							esc_html_e( 'Used: YY requests.', 'trx_addons' );
						?></span>
					</div><#
				}
			#></div>
		</div>
		<textarea class="sc_tgenerator_text sc_tgenerator_form_field_hidden" placeholder="<?php esc_attr_e( 'Text to process...', 'trx_addons' ); ?>"></textarea>
		<div class="sc_tgenerator_result">
			<div class="sc_tgenerator_result_label"><?php esc_html_e( 'Result:', 'trx_addons' ); ?></div>
			<div class="sc_tgenerator_result_content"></div>
		</div>
	</div>

	<?php $element->sc_show_links('sc_tgenerator'); ?>

</div><#

settings = trx_addons_elm_restore_global_params( settings );
#>