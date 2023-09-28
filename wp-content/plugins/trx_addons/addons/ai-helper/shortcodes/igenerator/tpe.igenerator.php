<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v2.20.2
 */

 use TrxAddons\AiHelper\Lists;
 use TrxAddons\AiHelper\Utils;

extract( get_query_var( 'trx_addons_args_sc_igenerator' ) );
?><#
settings = trx_addons_elm_prepare_global_params( settings );

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_igenerator_' + ( '' + Math.random() ).replace( '.', '' );

var link_class = "<?php echo apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_igenerator_item_link sc_button sc_button_size_small', 'sc_igenerator'); ?>";
var link_class_over = "<?php echo apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_igenerator_item_link sc_igenerator_item_link_over', 'sc_igenerator'); ?>";

var models = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_ai_image_models() ) ); ?>' );
var styles = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_stability_ai_styles() ) ); ?>' );
var sizes  = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_ai_image_sizes() ) ); ?>' );
var openai_sizes  = JSON.parse( '<?php echo addslashes( json_encode( Lists::get_list_ai_image_sizes( 'openai' ) ) ); ?>' );


#><div id="{{ id }}" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_igenerator sc_igenerator_' + settings.type, settings ) ); #>">

	<?php $element->sc_show_titles( 'sc_igenerator' ); ?>

	<div class="sc_igenerator_content sc_item_content">
		<div class="sc_igenerator_form <#
			print( trx_addons_get_responsive_classes( 'sc_igenerator_form_align_', settings, 'align', '' ).replace( /flex-start|flex-end/g, function( match ) {
				return match == 'flex-start' ? 'left' : 'right';
			} ) );
		#>">
			<div class="sc_igenerator_form_inner">
				<div class="sc_igenerator_form_field sc_igenerator_form_field_prompt<#
					if ( settings.show_settings ) {
						print( ' sc_igenerator_form_field_prompt_with_settings' );
					}
				#>">
					<div class="sc_igenerator_form_field_inner">
						<input type="text" value="{{ settings.prompt }}" class="sc_igenerator_form_field_prompt_text" placeholder="<?php esc_attr_e('Describe what you want or hit a tag below', 'trx_addons'); ?>">
						<a href="#" class="sc_igenerator_form_field_prompt_button<# if ( ! settings.prompt ) print( ' sc_igenerator_form_field_prompt_button_disabled' ); #>">{{{ settings.button_text || '<?php esc_html_e('Generate', 'trx_addons'); ?>' }}}</a>
					</div><#
					if ( settings.show_settings ) {
						var settings_mode = settings.show_settings_size ? 'full' : 'light';
						#>
						<a href="#" class="sc_igenerator_form_settings_button trx_addons_icon-sliders"></a>
						<div class="sc_igenerator_form_settings sc_igenerator_form_settings_{{ settings_mode }}"><#
							// Settings mode 'full' - visitors can change settings 'size', 'width' and 'height'
							if ( settings_mode == 'full' ) {
								// Model
								#><div class="sc_igenerator_form_settings_field">
									<label for="sc_igenerator_form_settings_field_model"><?php esc_html_e('Model:', 'trx_addons'); ?></label>
									<select name="sc_igenerator_form_settings_field_model" id="sc_igenerator_form_settings_field_model"><#
										for ( var model in models ) {
											#><option value="{{ model }}"<# if ( settings.model == model ) print( ' selected="selected"' ); #>>{{ models[model] }}</option><#
										}
									#></select>
		   						</div><#
								// Style
								#><div class="sc_igenerator_form_settings_field<# if ( ! settings.model || settings.model.indexOf( 'stability-ai/' ) < 0 ) print( ' trx_addons_hidden' ); #>">
									<label for="sc_igenerator_form_settings_field_style"><?php esc_html_e('Style:', 'trx_addons'); ?></label>
									<select name="sc_igenerator_form_settings_field_style" id="sc_igenerator_form_settings_field_style"><#
										for ( var style in styles ) {
											#><option value="{{ style }}"<# if ( settings.style == style ) print( ' selected="selected"' ); #>>{{ styles[style] }}</option><#
										}
									#></select>
		   						</div><#
								// Size
								#><div class="sc_igenerator_form_settings_field">
									<label for="sc_igenerator_form_settings_field_size"><?php esc_html_e('Size (px):', 'trx_addons'); ?></label>
									<select name="sc_igenerator_form_settings_field_size" id="sc_igenerator_form_settings_field_size"><#
										for ( var size in sizes ) {
											#><option value="{{ size }}"<#
												if ( settings.size == size ) print( ' selected="selected"' );
												if ( settings.model && settings.model.indexOf( 'openai/' ) >= 0 && ! open_sizes[size] ) print( ' class="trx_addons_hidden"' );
											#>>{{ sizes[size] }}</option><#
										}
									#></select>
		   						</div><#
								// Width (numeric field)
								#><div class="sc_igenerator_form_settings_field<# if ( settings.size != 'custom' ) print( ' trx_addons_hidden' ); #>">
									<label for="sc_igenerator_form_settings_field_width"><?php esc_html_e('Width (px):', 'trx_addons'); ?></label>
									<div class="sc_igenerator_form_settings_field_numeric_wrap">
										<input
											type="number"
											name="sc_igenerator_form_settings_field_width"
											id="sc_igenerator_form_settings_field_width"
											min="0"
											max="<?php echo esc_attr( Utils::get_max_image_width() ); ?>"
											step="8"
											value="{{ settings.width }}"
										>
										<div class="sc_igenerator_form_settings_field_numeric_wrap_buttons">
											<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_inc"></a>
											<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_dec"></a>
										</div>
									</div>
								</div><#
								// Height (numeric field)
								#><div class="sc_igenerator_form_settings_field<# if ( settings.size != 'custom' ) print( ' trx_addons_hidden' ); #>">
									<label for="sc_igenerator_form_settings_field_height"><?php esc_html_e('Height (px):', 'trx_addons'); ?></label>
									<div class="sc_igenerator_form_settings_field_numeric_wrap">
										<input
											type="number"
											name="sc_igenerator_form_settings_field_height"
											id="sc_igenerator_form_settings_field_height"
											min="0"
											max="<?php echo esc_attr( Utils::get_max_image_height() ); ?>"
											step="8"
											value="{{ settings.height }}"
										>
										<div class="sc_igenerator_form_settings_field_numeric_wrap_buttons">
											<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_inc"></a>
											<a href="#" class="sc_igenerator_form_settings_field_numeric_wrap_button sc_igenerator_form_settings_field_numeric_wrap_button_dec"></a>
										</div>
									</div>
								</div><#

							// Free mode settings
							} else {

								for ( var model in models ) {
									var id = 'sc_igenerator_form_settings_field_model_' + settings.model.replace( '/', '-' );
									#><div class="sc_igenerator_form_settings_field">
										<input type="radio" id="{{ id }}" name="sc_igenerator_form_settings_field_model" value="{{ model }}"<# if ( settings.model == model ) print( ' checked="checked"' ); #>><label for="{{ id }}">{{ models[model] }}</label>
									</div><#
								}
							}
						#></div><#
					}
				#></div>
				<div class="sc_igenerator_form_field sc_igenerator_form_field_tags"><#
					if ( settings.tags_label ) {
						#><span class="sc_igenerator_form_field_tags_label">{{ settings.tags_label }}</span><#
					}
					if ( settings.tags && settings.tags.length ) {
						#><span class="sc_igenerator_form_field_tags_list"><#
							_.each( settings.tags, function( tag ) {
								#><a href="#" class="sc_igenerator_form_field_tags_item" data-tag-prompt="{{ tag.prompt }}">{{ tag.title }}</a><#
							} );
						#></span><#
					}
				#></div>
			</div><#
			if ( settings.show_limits ) {
				#><div class="sc_igenerator_limits">
					<span class="sc_igenerator_limits_label"><?php
						esc_html_e( 'Limits per hour (day/week/month/year): XX images.', 'trx_addons' );
					?></span>
					<span class="sc_igenerator_limits_value"><?php
						esc_html_e( 'Used: YY images.', 'trx_addons' );
					?></span>
				</div><#
			}
		#></div>
		<div class="sc_igenerator_images sc_igenerator_images_columns_{{ settings.number.size }} sc_igenerator_images_size_{{ settings.size.size }}"></div>
	</div>

	<?php $element->sc_show_links('sc_igenerator'); ?>

</div><#

settings = trx_addons_elm_restore_global_params( settings );
#>