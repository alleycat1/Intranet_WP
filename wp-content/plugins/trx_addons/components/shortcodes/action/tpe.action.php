<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_action'));
?><#
settings = trx_addons_elm_prepare_global_params( settings );

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_action_'+(''+Math.random()).replace('.', '');

if (settings.columns.size < 1) settings.columns.size = settings.actions.length;
settings.columns.size = Math.max(1, Math.min(settings.actions.length, settings.columns.size));
if (settings.columns_tablet.size > 0) settings.columns_tablet.size = Math.max(1, Math.min(settings.actions.length, settings.columns_tablet.size));
if (settings.columns_mobile.size > 0) settings.columns_mobile.size = Math.max(1, Math.min(settings.actions.length, settings.columns_mobile.size));
settings.slider = settings.slider > 0 && settings.actions.length > settings.columns.size;
settings.slides_space.size = Math.max(0, settings.slides_space.size);
if (settings.slider > 0 && settings.slider_pagination > 0) settings.slider_pagination = 'bottom';

var column_class = "<?php echo esc_attr(trx_addons_get_column_class(1, '##')); ?>";
var link_class = "<?php echo apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_action_item_link sc_button sc_button_size_small', 'sc_action'); ?>";
var link_class_over = "<?php echo apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_action_item_link sc_action_item_link_over', 'sc_action'); ?>";

#><div id="{{ id }}" class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_action sc_action_'+settings.type, settings ) ); #>">

	<?php $element->sc_show_titles('sc_action'); ?>

	<#
	if (settings.slider) {
		settings.slides_min_width = 250;
		#><?php $element->sc_show_slider_wrap_start('sc_action'); ?><#
	} else if (settings.columns.size > 1) {
		#><div class="sc_action_columns_wrap sc_item_columns
			<?php echo esc_attr(trx_addons_get_columns_wrap_class()); ?>
			columns_padding_bottom<#
			if (settings.columns.size >= settings.actions.length ) {
				#> columns_in_single_row<#
			}
		#>"><#
	} else {
		#><div class="sc_action_content sc_item_content"><#
	}	

	_.each(settings.actions, function(item) {
		if (item.position == '') item.position = 'mc';
		if (settings.slider == 1) {
			#><div class="slider-slide swiper-slide"><#
		} else if (settings.columns.size > 1) {
			#><div class="<#
				var classes = column_class.replace('##', settings.columns.size);
				if (settings.columns_tablet.size > 0) classes += ' ' + column_class.replace('##', settings.columns_tablet.size) + '-tablet';
				if (settings.columns_mobile.size > 0) classes += ' ' + column_class.replace('##', settings.columns_mobile.size) + '-mobile';
				print(classes);
			#>"><#
		}
		#><div class="<#
			print( trx_addons_apply_filters(
					'trx_addons_filter_sc_item_classes',
					'sc_action_item sc_action_item_' + settings.type
						+ ( item.bg_image.url != '' || item.bg_color != '' ? ' with_image' : '' )
						+ ( item.bg_color != '' ? ' with_bg_color' : '' )
						+ ( settings.full_height == 1 ? ' trx_addons_stretch_height' : '' )
						+ ( settings.min_height.size > 0 && ! settings.full_height ? ' sc_action_fixed_height' : '' )
						+ ( item.position ? ' sc_action_item_' + item.position : '' ),
					'sc_action',
					item
				) );
			#>"<#
			if (item.bg_image.url != '' || item.bg_color != '') {
				print(' style="'
							+ (item.bg_color != '' ? 'background-color:'+item.bg_color+';' : '')
							+ (item.bg_image.url != '' ? 'background-image:url('+item.bg_image.url+');' : '')
							+ '"');
			}
		#>><#
			if (item.bg_image.url != '' || item.bg_color != '') {
				#>
				<div class="sc_action_item_mask"></div>
				<div class="sc_action_item_inner">
				<#
			}
			if (item.image.url != '') {
				#><div class="sc_action_item_image"><img src="{{ item.image.url }}" alt="<?php esc_attr_e("Action's image", 'trx_addons'); ?>"></div><#
			} else {
				var icon = trx_addons_get_settings_icon( item.icon );
				if ( typeof item.icon_type == 'undefined' ) item.icon_type = '';
				if ( icon != '' && icon != 'none' ) {
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
					#><div class="sc_action_item_icon sc_icon_type_{{ item.icon_type }} {{ icon }}"
						<#
						if (item.color != '') print(' style="color: ' + item.color + '"');
						#>><#
						if (svg != '') {
							#><span class="sc_icon_type_{{ item.icon_type }} {{ icon }}"><object type="image/svg+xml" data="{{ svg }}" border="0"></object></span><#
						} else if (img != '') {
							#><img class="sc_icon_as_image" src="{{ img }}" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"><#
						} else {
							#><span class="sc_icon_type_{{ item.icon_type }} {{ icon }}"
								<# if (item.color != '') print(' style="color: '+item.color+';border-color: ' + item.color + '"'); #>
							></span><#
						}
					#></div><#
				}
			}
			if (item.subtitle != '') {
				item.subtitle = item.subtitle.split('|');
				#><h6 class="sc_action_item_subtitle"<# if (item.color_2 != '') print(' style="color: ' + item.color_2 + '"'); #>><#
					_.each(item.subtitle, function(str) {
						print('<span>'+str+'</span>');
					});
				#></h6><#
			}
			if (item.title != '') {
				item.title = item.title.split('|');
				#><h3 class="sc_action_item_title"<# if (item.color != '') print(' style="color: '+item.color+'"'); #>><#
					_.each(item.title, function(str) {
						print('<span>'+str+'</span>');
					});
				#></h3><#
			}
			if (item.date != '') {
				#><div class="sc_action_item_date"<#
					if (item.color != '' || item.color_4 != '') {
						print(' style="'
								+ ( item.color != '' ? 'color: '+item.color+';' : '' )
								+ ( item.color_4 != '' ? 'border-color: '+item.color_4+';' : '' )
								+ '"'
						);
					}
				#>>{{ item.date }}</div><#
			}
			if (item.description != '') {
				item.description = item.description
										.replace(/\[(.*)\]/g, '<b>$1</b>')
										.replace(/\n/g, '|')
										.split('|');
				#><div class="sc_action_item_description"<# if (item.color_3 != '') print(' style="color: '+item.color_3+'"'); #>><#
					_.each(item.description, function(str) {
						print('<span>'+str+'</span>');
					});
				#></div><#
			}
			if (item.link.url != '' && item.link_text != '') {
				#><a href="{{ item.link.url }}" class="{{ link_class }}">{{ item.link_text }}</a><#
			}
			if (item.info != '') {
				item.info = item.info.split('|');
				#><div class="sc_action_item_info"<#
					if (item.color != '' || item.color_4 != '') {
						print(' style="'
								+ ( item.color != '' ? 'color: '+item.color+';' : '' )
								+ ( item.color_4 != '' ? 'border-color: '+item.color_4+';' : '' )
								+ '"'
						);
					}
				#>><#
					_.each(item.info, function(str) {
						print('<span>'+str+'</span>');
					});
				#></div><#
			}
			if (item.bg_image.url != '' || item.bg_color != '') {
				#></div><#
				if (item.link.url != '' && item.link_text == '') {
					#><a href="{{ item.link.url }}" class="{{ link_class_over }}"></a><#
				}
			}
		#></div><#

		if (settings.slider || settings.columns.size > 1) {
			#></div><#
		}
	});

	#></div><#

	if (settings.slider) {
		#><?php $element->sc_show_slider_wrap_end('sc_action'); ?><#
	}

	#><?php $element->sc_show_links('sc_action'); ?>

</div><#

settings = trx_addons_elm_restore_global_params( settings );
#>