<#
/**
 * Template to represent shortcode as Widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and used to generate the live preview.
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

var button_classes = "<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button', 'sc_button')); ?>";

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_button_'+(''+Math.random()).replace('.', '');

#><div id="{{ id }}" class="sc_item_button sc_button_wrap sc_align_{{ settings.align }}"><#

	_.each(settings.buttons, function(item) {

		var icon = trx_addons_get_settings_icon( item.icon );
		
		item.css += item.bg_image.url != '' ? 'background-image:url(' + item.bg_image.url + ');' : '';
		
		#><a href="{{ item.link.url }}"<#
			if (item.item_id != '') print(' id="'+item.item_id+'"');
			#> class="<# print(button_classes
								+ ' sc_button_' + item.type
								+ (item.class != '' ? ' ' + item.class : '')
	       	                    + (item.size != '' ? ' sc_button_size_' + item.size : '')
	           	                + (item.bg_image.url != '' ? ' sc_button_bg_image' : '')
	               	            + (item.image.url != '' ? ' sc_button_with_image' : '')
	                   	        + (icon != '' ? ' sc_button_with_icon' : '')
	                       	    + (item.icon_position != '' ? ' sc_button_icon_' + item.icon_position : '')
								+ (typeof item.color_style != 'undefined' && item.color_style != '' && item.color_style != 'default' ? ' color_style_' + item.color_style : '')
	                            ); #>"<#
			if (item.link.is_external == 'on') print(' target="_blank"');
			if (item.css != '') print(' style="' + item.css + '"');
		#>><#
		
			// Icon or Image
			if (!trx_addons_is_off(item.image.url) || !trx_addons_is_off(icon)) {
				#><span class="sc_button_icon"><#
					if (item.image.url != '') {
						#><img class="sc_icon_as_image" src="{{ item.image.url }}" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"><#
					} else if ( trx_addons_is_url( icon ) ) {
						if (icon.indexOf('.svg') >= 0) {
							#><object type="image/svg+xml" data="{{ icon }}" border="0"></object><#
						} else {
							#><img class="sc_icon_as_image" src="{{ icon }}" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"><#
						}
					} else {
						#><span class="{{ icon }}"></span><#
					}
				#></span><#
			}
			if (item.title != '' || item.subtitle != '') {
				#><span class="sc_button_text<# if (!trx_addons_is_off(item.text_align)) print(' sc_align_'+item.text_align); #>"><#
					if (item.subtitle != '') {
						#><span class="sc_button_subtitle">{{{ trx_addons_prepare_macros( item.subtitle ) }}}</span><#
					}
					if (item.title != '') {
						#><span class="sc_button_title">{{{ trx_addons_prepare_macros( item.title ) }}}</span><#
					}
				#></span><#
			}
		#></a><#
	});
#></div>