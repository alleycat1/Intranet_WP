<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_layouts_iconed_text'));
?><#
var id = settings._element_id ? settings._element_id + '_sc' : 'sc_layouts_iconed_text_'+(''+Math.random()).replace('.', '');

#><div id="{{ id }}" class="sc_layouts_iconed_text<?php	$element->sc_add_common_classes('sc_layouts_iconed_text'); ?>"><#

	// Open link
	if (settings.link.url != '') {
		#><a href="{{ settings.link.url }}" class="sc_layouts_item_link sc_layouts_iconed_text_link"><#
	}
	
	// Icon or Image
	var img = '', svg = '', type = 'icons',
		icon = trx_addons_get_settings_icon( settings.icon );
	if ( icon ) {
		if ( trx_addons_is_url( icon ) ) {
			if (icon.indexOf('.svg') >= 0) {
				svg = icon;
				type = 'svg';
			} else {
				img = icon;
				type = 'images';
			}
			icon = trx_addons_get_basename(icon);
		}
		#><span class="sc_layouts_item_icon sc_layouts_iconed_text_icon {{ icon }} sc_icon_type_{{ type }}"><#
			if (svg != '') {
				#><object type="image/svg+xml" data="{{ svg }}" border="0"></object><#
			} else if (img != '') {
				#><img class="sc_icon_as_image" src="{{ img }}" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"><#
			}
		#></span><#
	}
	if (settings.text1 != '' || settings.text2 != '') {
		#><span class="sc_layouts_item_details sc_layouts_iconed_text_details"><#
			if (settings.text1 != '') {
				#><span class="sc_layouts_item_details_line1 sc_layouts_iconed_text_line1">{{{ settings.text1 }}}</span><#
			}
			if (settings.text2 != '') {
				#><span class="sc_layouts_item_details_line2 sc_layouts_iconed_text_line2">{{{ settings.text2 }}}</span><#
			}
		#></span><#
	}

	// Close link
	if (settings.link.url != '') {
		#></a><#
	}
#></div>