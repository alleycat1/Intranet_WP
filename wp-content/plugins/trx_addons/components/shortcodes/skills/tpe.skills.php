<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_skills'));
?><#
settings = trx_addons_elm_prepare_global_params( settings );

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_skills_'+(''+Math.random()).replace('.', ''),
	column_class = "<?php echo esc_attr(trx_addons_get_column_class(1, '##')); ?>",
	columns = 0,
	legend = '',
	data = '',
	max = Math.max( 0, parseFloat( settings.max ) ),
	compact = 0,
	cutout = 0,
	matches = '',
	reg = /([+\-]?[\.0-9]+)(.*)/;

for (var i in settings.values) {
	matches = ('' + settings.values[i].value).match(reg);
	if (matches && matches.length) {
		settings.values[i].value = parseFloat(matches[1]);
		settings.values[i].units = matches[2];
	} else {
		if ( settings.values[i].value == '' ) {
			settings.values[i].value = '0';
		} else {
			settings.values[i].value = parseFloat(('' + settings.values[i].value).replace('%', ''));
		}
		settings.values[i].units = '';
	}
	if (max < settings.values[i].value) max = settings.values[i].value;
}

matches = ('' + max).match(reg);
if (matches && matches.length) {
	max = matches[1];
} else {
	max = ('' + max).replace('%', '');
}
_.each( settings.values, function(item) {
	if ( max < item.value ) {
		max = item.value;
	}
} );

columns = settings.compact == 0 
			? (settings.columns.size < 1 
				? settings.values.length
				: Math.min(settings.columns.size, settings.values.length)
				)
			: 1;
if (settings.columns_tablet.size > 0 && settings.compact == 0) settings.columns_tablet.size = Math.max(1, Math.min(settings.values.length, settings.columns_tablet.size));
if (settings.columns_mobile.size > 0 && settings.compact == 0) settings.columns_mobile.size = Math.max(1, Math.min(settings.values.length, settings.columns_mobile.size));

cutout = Math.min(100, Math.max(0, settings.cutout.size));
compact = settings.compact < 1 ? 0 : 1;

_.each(settings.values, function(item) {
	var icon = trx_addons_get_settings_icon( item.icon ),
		img = '',
		svg = '';
	if ( icon == 'none' || icon == 'empty' ) {
		icon = '';
	}
	if (typeof item.icon_type == 'undefined') {
		item.icon_type = '';
	}
	if (icon != '') {
		if ( trx_addons_is_url( icon ) ) {
			if ( icon.indexOf('.svg') >= 0 ) {
				svg = icon;
				item.icon_type = 'svg';
			} else {
				img = icon;
				item.icon_type = 'images';
			}
			icon = trx_addons_get_basename(icon);
		}
	}
	var ed = item.units,	//(''+item.value).substr(-1)=='%' ? '%' : '',
		value = item.value,	//(''+item.value).replace('%', ''),
		percent = Math.round(value / max * 100),
		start = 0,
		stop = value,
		steps = 100,
		step = value / steps,
		speed = Math.round(5 + Math.random() * 20),
		animation = typeof settings.duration != 'undefined' 
						? Math.max(0, settings.duration.size)
						: <?php echo max(0, apply_filters( 'trx_addons_filter_skills_duration', 1500 ) ); ?>,
		item_color = item.color != '' 
						? item.color 
						: (settings.color!='' 
							? settings.color 
							: (settings.type == 'pie' 
								? '<?php echo apply_filters('trx_addons_filter_get_theme_accent_color', '#efa758'); ?>' 
								: ''
								)
							),
		icon_color = typeof item.icon_color != 'undefined' && item.icon_color != '' 
						? item.icon_color 
						: (settings.icon_color!='' 
							? settings.icon_color 
							: (settings.type == 'pie' 
								? '<?php echo apply_filters('trx_addons_filter_get_theme_accent_color', '#efa758'); ?>' 
								: ''
								)
							),
		title_color = typeof item.item_title_color != 'undefined' && item.item_title_color != '' 
						? item.item_title_color 
						: (settings.item_title_color!='' 
							? settings.item_title_color 
							: ''
							),
		bg_color = settings.bg_color != '' 
						? settings.bg_color 
						: '#f7f7f7',
		border_color = settings.border_color != '' 
						? settings.border_color 
						: '';
	if ( animation > 0 ) {
		// All counters have an equal duration of the animation
		speed = Math.round( animation / steps  );
	} else {
		// Counters have a different (random) duration of the animation
		animation = Math.round( steps * speed );
	}
	
	if (settings.type == 'pie') {

		if (compact == 1) {
			legend += '<div class="'
							+ trx_addons_apply_filters( 'trx_addons_filter_sc_item_classes', 'sc_skills_legend_item', 'sc_skills', item, settings )
						+ '">'
							+ '<span class="sc_skills_legend_marker" style="background-color:' + item_color + '"></span>'
							+ '<span class="sc_skills_legend_title">' + item.title + '</span>'
							+ '<span class="sc_skills_legend_value">' + item.value + '</span>'
						+ '</div>';
			data += '<div class="pie"'
						+ ' data-start="' + start + '"'
						+ ' data-stop="' + stop + '"'
						+ ' data-step="' + step + '"'
						+ ' data-steps="' + steps + '"'
						+ ' data-max="' + max + '"'
						+ ' data-speed="' + speed + '"'
						+ ' data-duration="' + animation + '"'
						+ ' data-color="' + item_color + '"'
						+ ' data-bg_color="' + bg_color + '"'
						+ ' data-border_color="' + border_color + '"'
						+ ' data-cutout="' + cutout + '"'
						+ ' data-easing="easeOutCirc"'
						+ ' data-ed="' + ed + '"'
				+ '>'
					+ '<input type="hidden" class="text" value="' + item.title + '" />'
					+ '<input type="hidden" class="percent" value="' + percent + '" />'
					+ '<input type="hidden" class="color" value="' + item_color + '" />'
				+ '</div>';

		} else {
		
			var item_id = 'sc_skills_canvas_' + (''+Math.random()).replace('.','');
			data += (columns > 0
						? '<div class="sc_skills_column '
								+ column_class.replace('##', columns)
								+ (settings.columns_tablet.size > 0 ? ' ' + column_class.replace('##', settings.columns_tablet.size) + '-tablet' : '')
								+ (settings.columns_mobile.size > 0 ? ' ' + column_class.replace('##', settings.columns_mobile.size) + '-mobile' : '')
							+ '">'
						: ''
						)
					+ '<div class="sc_skills_item_wrap">'
						+ '<div class="'
							+ trx_addons_apply_filters( 'trx_addons_filter_sc_item_classes', 'sc_skills_item', 'sc_skills', item, settings )
						+ '">'
							+ '<canvas id="' + item_id + '"></canvas>'
							+ '<div class="sc_skills_total"'
								+ ' data-start="' + start + '"'
								+ ' data-stop="' + stop + '"'
								+ ' data-step="' + step + '"'
								+ ' data-steps="' + steps + '"'
								+ ' data-max="' + max + '"'
								+ ' data-speed="' + speed + '"'
								+ ' data-duration="' + animation + '"'
								+ ' data-color="' + item_color + '"'
								+ ' data-bg_color="' + bg_color + '"'
								+ ' data-border_color="' + border_color + '"'
								+ ' data-cutout="' + cutout + '"'
								+ ' data-easing="easeOutCirc"'
								+ ' data-ed="' + ed + '"'
								+ ' data-style="' + settings.style + '"'
								+ ( icon_color ? ' style="color:' + icon_color + ';"' : '' )
							+ '>'
								+ ( settings.style == 'odometer' ? trx_addons_sc_skills_split_by_digits( start, stop, ed ) : ( start + ed ) )
							+ '</div>'
						+ '</div>'
						+ (item.title != '' 
								? '<div class="sc_skills_item_title"' + ( title_color ? ' style="color:' + title_color + '"' : '' ) + '>'
										+ ( icon != '' && icon != 'none'
											? '<span class="sc_skills_icon sc_icon_type_' + item.icon_type + ' ' + icon + '">'
												+ (svg != ''
													? '<object type="image/svg+xml" data="' + svg + '" border="0"></object>'
													: '')
												+ (img != ''
													? '<img class="sc_icon_as_image" src="' + img + '" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>">'
													: '')
												+ '</span>'
											: '') 
										+ item.title.replace(/\|/g, '\n').replace(/\n/g, '<br>')
									+ '</div>' 
								: '')
					+ '</div>'
				+ (columns > 0 ? '</div>' : '');
		}

	} else {

		data += (columns > 0
					? '<div class="sc_skills_column '
							+ column_class.replace('##', columns)
							+ (settings.columns_tablet.size > 0 ? ' ' + column_class.replace('##', settings.columns_tablet.size) + '-tablet' : '')
							+ (settings.columns_mobile.size > 0 ? ' ' + column_class.replace('##', settings.columns_mobile.size) + '-mobile' : '')
						+ '">'
					: '')
				+ '<div class="sc_skills_item_wrap' + ( typeof settings.icon_position != 'undefined' ? ' sc_skills_item_icon_position_' + settings.icon_position : '' ) + '">'
					+ '<div class="'
							+ trx_addons_apply_filters( 'trx_addons_filter_sc_item_classes', 'sc_skills_item', 'sc_skills', item, settings )
						+ '">'
						+ ( item.char != ''
							? '<div class="sc_skills_icon sc_skills_char" data-char="' + item.char + '"'
									+ ( item_color != '' ? ' style="color: ' + icon_color + '"' : '' )
								+ '>'
									+ '<span data-char="' + item.char + '"'
										+ ( item_color != '' ? ' style="color: ' + icon_color + '"' : '' )
									+ '></span>'
								+ '</div>'
							: ''
							)
						+ ( item.image.url != ''
							? '<div class="sc_skills_image">'
									+ '<img src="' + item.image.url + '" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>">'
								+ '</div>'
							: ''
							)
						+ ( icon != '' && icon != 'none'
							? '<span class="sc_skills_icon sc_icon_type_' + item.icon_type + ' ' + icon + '"'
									+ ( icon_color ? ' style="color:' + icon_color + '"' : '' )
								+ '>'
									+ (svg != ''
										? '<object type="image/svg+xml" data="' + svg + '" border="0"></object>'
										: '')
									+ (img != ''
										? '<img class="sc_icon_as_image" src="' + img + '" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>">'
										: '')
								+ '</span>'
							: '') 
						+ '<div class="sc_skills_total"'
							+ ' data-start="' + start + '"'
							+ ' data-stop="' + stop + '"'
							+ ' data-step="' + step + '"'
							+ ' data-max="' + max + '"'
							+ ' data-speed="' + speed + '"'
							+ ' data-duration="' + animation + '"'
							+ ' data-ed="' + ed + '"'
							+ ' data-style="' + settings.style + '"'
							+ (item_color != '' ? ' style="color: ' + item_color + ';"' : '')
						+ '>'
							+ ( settings.style == 'odometer' ? trx_addons_sc_skills_split_by_digits( start, stop, ed ) : ( start + ed ) )
						+ '</div>'
					+ '</div>'
					+ ( item.title != ''
						? '<div class="sc_skills_item_title"' + ( title_color ? ' style="color:' + title_color + '"' : '' ) + '>'
							+ item.title.replace(/\|/g, '\n').replace(/\n/g, '<br>')
							+ '</div>'
						: ''
						)
				+ '</div>'
			+ (columns > 0 ? '</div>' : '');
	}
});

// Split value by digits, wrapper to the span
function trx_addons_sc_skills_split_by_digits(value, max, unit='') {
	var output = '<span class="sc_skills_digits">';
	if ( value == '' ) value = 0;
	var sm = '' + max,
		sv = ('' + value).padStart( sm.length, '0' );
	for ( var i = 0; i < sv.length; i++ ) {
		var digit = sv.substring(i, i+1);
		output += '<span class="sc_skills_digit">'
					+ '<span class="sc_skills_digit_placeholder">8</span>'
					+ '<span class="sc_skills_digit_wrap">'
						+ '<span class="sc_skills_digit_ribbon">'
							+ '<span class="sc_skills_digit_value">'
								+ digit
							+ '</span>'
						+ '</span>'
					+ '</span>'
				+ '</span>';
	}
	if ( unit ) {
		output += '<span class="sc_skills_unit">'
						+ unit
					+ '</span>';
	}
	output += '</span>';
	return output;
};


if (settings.type == 'pie') {
	#><div id="{{ id }}"
		class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_skills sc_skills_pie sc_skills_compact_' + (compact > 0 ? 'on' : 'off')+' sc_skills_counter_style_' + settings.style, settings ) ); #>"
		data-type="pie"><#
} else {
	#><div id="{{ id }}"
		class="<# print( trx_addons_apply_filters('trx_addons_filter_sc_classes', 'sc_skills sc_skills_counter sc_skills_counter_style_' + settings.style, settings ) ); #>"
		data-type="counter"><#
}

	#><?php $element->sc_show_titles('sc_skills'); ?><#

	if (columns > 1) {
		#><div class="sc_skills_columns sc_item_columns
			<?php echo esc_attr(trx_addons_get_columns_wrap_class()); ?>
			columns_padding_bottom<#
			if (columns >= settings.values.length ) {
				#> columns_in_single_row<#
			}
		#>"><#
	}
	if (settings.type == 'pie' && compact == 1) {
		#><div class="sc_item_content sc_skills_content">
			<div class="sc_skills_legend">{{{ legend }}}</div>
			<div id="{{ id }}_pie_item" class="sc_skills_item">
				<canvas id="{{ id }}_pie" class="sc_skills_pie_canvas"></canvas>
				<div class="sc_skills_data" style="display:none;">{{{ data }}}</div>
			</div>
		</div><#
	} else {
		print(data);
	}

	if (columns > 1) {
		#></div><#
	}

	#><?php $element->sc_show_links('sc_skills'); ?>

</div><#

settings = trx_addons_elm_restore_global_params( settings );
#>