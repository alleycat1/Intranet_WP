<?php
/**
 * The style "default" of the Supertitle
 *
 * @package ThemeREX Addons
 * @since v1.6.49
 */

?><#
settings = trx_addons_elm_prepare_global_params( settings );

var icon_column = Math.max(0, Math.min(6, settings.icon_column.size));
var header_column = Math.max(0, Math.min(12, settings.header_column.size));
if ( icon_column + header_column > 12 )	icon_column = 12 - header_column;
var right_column = Math.max(0, 12 - header_column - icon_column);
var column_class = "<?php echo esc_attr(trx_addons_get_column_class('##', 12)); ?>";
var icon = trx_addons_get_settings_icon( settings.icon );
var icon_type = settings.image !== '' &&  settings.image.url !== '' 
					? 'image' 
					: (icon !== '' && icon !== 'none' 
						? 'icon' 
						: 'no_icon');
var has_side = false;
_.each(settings.items, function(item) {
	if (item.align === 'right') has_side = true;
});
if (!has_side) {
	header_column = 12 - icon_column;
	right_column = 0;
}
#><div class="sc_supertitle sc_supertitle_{{ settings.type }}">
	<div class="sc_supertitle_columns_wrap sc_item_columns
		<?php echo esc_attr(trx_addons_get_columns_wrap_class()); ?>
		columns_padding_bottom columns_in_single_row"><#
		if (icon_column > 0) {
			#>
			<div class="sc_supertitle_icon_column <# print(column_class.replace('##', icon_column)); if (icon_type ==='no_icon' ) print(' sc_supertitle_icon_empty_column'); #>">
				<div class="sc_supertitle_media_block">
					<#
					if (icon_type === 'image') {
						#><img class="sc_icon_as_image" src="{{ settings.image.url }}" ><#
					} else if (icon_type === 'icon') {
						#><span <#
							if (icon !== '') print(' class="sc_icon_type_icons ' + icon +'"');
							if (settings.icon_size.size !== '') print(' style="font-size:' + settings.icon_size.size + settings.icon_size.unit + ';"');
						#>></span><#
					} else {
						#><span class="sc_supertitle_no_icon"></span><#
					}
					#>
				</div>
			</div><#
		}
		if (header_column > 0) {
			#><div class="sc_supertitle_left_column <# print(column_class.replace('##', header_column)); #>"><#
				trx_addons_sc_supertitle_show_items('left');
			#></div><#
		}
		if (has_side && right_column > 0) {
			#><div class="sc_supertitle_right_column <# print(column_class.replace('##', right_column)); #>"><#
				trx_addons_sc_supertitle_show_items('right');
			#></div><#
		}
		#>
	</div>
</div><#
// Display items in the left and right columns
function trx_addons_sc_supertitle_show_items(side) {
	_.each(settings.items, function(item) {
		if (item.align != side) return;
		if (item.item_type === 'text') {
			if (item.text !== '') {
				var tag = trx_addons_is_off(item.tag) ? 'h2' : item.tag;
				#><{{ tag }} class="sc_supertitle_text<#
					if (item.inline == '1') print(' sc_supertitle_display_inline');
				#>"<#
					if (item.color !== '' && item.color2 == '') print( ' style="color: ' + item.color + '"');
				#>><#
					if (item.link.url !== '') {
						#><a href="{{ item.link.url }}" <# if (item.color !== '' && item.color2 == '') print( ' style="color: '+ item.color +'"'); #>><#
					}
					if (item.color !== '' && item.color2 !== '') {
						print('<span class="trx_addons_text_gradient" style="color:' + item.color + ';background:' + item.color + ';background:linear-gradient(' + Math.max(0, Math.min(360, item.gradient_direction.size)) + 'deg,' + (item.color2 ? item.color2 : 'transparent')  + ',' + item.color + ');">'
									+ item.text
								+ '</span>');
					} else {
						print(item.text);
					}
					if (item.link.url !== '') {
						#></a><#
					}
				#></{{ tag }}><#
			}
		} else if (item.item_type === 'media') {
			if (item.media !== '') {
				#><div class="sc_supertitle_media sc_supertitle_position_{{ item.float_position }} <# if (item.inline == '1') print('sc_supertitle_display_inline'); #>">
					<img src="{{ item.media.url }}">
				</div><#
			}
		} else if (item.item_type === 'icon') {
			if (item.item_icon !== '') {
				#><div class="sc_supertitle_icon sc_supertitle_position_{{ item.float_position }} <# if (item.inline == '1') print('sc_supertitle_display_inline'); #>">
					<span class="sc_icon_type_icons {{ item.item_icon }}"  style="<#
						if (item.color !== '') print( 'color:' + item.color + ';');
						if (item.size.size !== '') print( 'font-size:' + item.size.size + item.size.unit + ';');
					#>"></span>
				</div><#
			}
		}
	});
}

settings = trx_addons_elm_restore_global_params( settings );
#>