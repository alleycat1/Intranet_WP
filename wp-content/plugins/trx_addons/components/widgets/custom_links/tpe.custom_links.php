<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.46
 */

extract( get_query_var('trx_addons_args_widget_custom_links') );

extract( trx_addons_prepare_widgets_args( trx_addons_generate_id( 'widget_custom_links_' ), 'widget_custom_links' ) );

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
?><#
settings = trx_addons_elm_prepare_global_params( settings );

if (settings.title != '') {
	#><?php trx_addons_show_layout($before_title); ?><#
	print(settings.title);
	#><?php trx_addons_show_layout($after_title); ?><#
}

// Widget body
if (settings.links.length > 0) {
	#><ul class="custom_links_list"><#
		_.each(settings.links, function(item) {
			var target = item.url.is_external ? ' target="_blank"' : '';
			var icon = trx_addons_get_settings_icon( item.icon );
			#><li class="custom_links_list_item<#
				if (item.label_on_hover) print(' custom_links_list_item_label_hover');
				if ((icon!='' && !trx_addons_is_off(icon))
					||
					(item.image.url!='' && !trx_addons_is_off(item.image.url))
				) print(' with_icon');
			#>"><#
				// Open link
				if (item.url.url == '') {
					#><span class="custom_links_list_item_link"<#
						if (item.color!='') print(' style="color:'+item.color+' !important;"');
					#>><#
				} else {
					#><a class="custom_links_list_item_link" href="{{ item.url.url }}"{{ target }}<#
						if (item.color!='') print(' style="color:'+item.color+' !important;"');
					#>><#
				}
				// Image or Icon
				if (item.image.url != '' && !trx_addons_is_off(item.image.url)) {
					#><img class="custom_links_list_item_image" src="{{ item.image.url }}" alt="<?php esc_attr_e("Icon", 'trx_addons'); ?>"><#
				} else if (icon != '' && !trx_addons_is_off(icon)) {
					var icon = trx_addons_is_off(icon) ? '' : icon;
					if (typeof item.icon_type == 'undefined') item.icon_type = 'icons';
					if (icon != '') {
						var img = '', svg = '';
						if ( trx_addons_is_url( icon ) ) {
							if (icon.indexOf('.svg') >= 0) {
								svg = icon;
								item.icon_type = 'svg';
							} else {
								img = icon;
								item.icon_type = 'images';
							}
							icon = trx_addons_get_basename(icon);
						}
						#><span id="{{ id }}_{{ icon }}" class="custom_links_list_item_icon sc_icon_type_{{ item.icon_type }} {{ icon }}"><#
							if (svg != '') {
								#><object type="image/svg+xml" data="{{ svg }}" border="0"></object><#
							} else if (img != '') {
								#><img class="sc_icon_as_image" src="{{ img }}" alt="<?php esc_attr_e("Icon", 'trx_addons'); ?>"><#
							}
						#></span><#
					}
				}
				// Title
				if (item.title != '') {
					item.title = item.title.split('|');
					#><span class="custom_links_list_item_title"><#
						_.each(item.title, function(str) {
							#><span><# print(str); #></span><#
						});
					#></span><#
				}
				// Label
				if (item.label != '') {
					var css = '';
					if (item.label_bg_color != '') {
						css = ' style="'
									+ 'border-color:' + item.label_bg_color + ';'
									+ 'background-color:' + item.label_bg_color + ';'
									+ '"';
					}
					#><span class="custom_links_list_item_label"<# if (css) print(css); #>>{{ item.label }}</span><#
				}
				// Close link (or button)
				if (item.url.url == '') {
					#></span><#
				} else {
					#></a><#
				}
				// Description
				if (item.description != '') {
					#><span class="custom_links_list_item_description"><#
						if (item.description.indexOf('<p>') < 0) {
							item.description = item.description
													.replace(/\[(.*)\]/g, '<b>$1</b>')
													.replace(/\n/g, '|')
													.split('|');
							_.each(item.description, function(str) {
								#><span><# print(str); #></span><#
							});
						} else
							print(item.description);
					#></span><#
				}
				// Button
				if (item.caption != '') {
					#><a class="custom_links_list_item_button sc_button sc_button_simple" href="{{ item.url.url }}"{{ target }}><#
						print(item.caption); 
					#></a><#
				}
			#></li><#
		});
	#></ul><#
}
	
settings = trx_addons_elm_restore_global_params( settings );
#><?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
