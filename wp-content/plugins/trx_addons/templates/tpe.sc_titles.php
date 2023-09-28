<?php
/**
 * The template to display shortcode's title, subtitle and description
 * on the Elementor's preview page
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_show_titles'));
if (empty($size)) $size = 'large';
?><#
var title_text = settings.title;
var title_align = !trx_addons_is_off(settings.title_align) ? ' sc_align_'+settings.title_align : '';
var title_style = !trx_addons_is_off(settings.title_style) ? ' sc_item_title_style_'+settings.title_style : '';
var title_class = "<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_title_class', 'sc_item_title '.$sc.'_title', $sc)); ?>";

var subtitle_align = !trx_addons_is_off(settings.subtitle_align) ? ' sc_align_'+settings.subtitle_align : title_align;
var subtitle_position = !trx_addons_is_off(settings.subtitle_position) ? settings.subtitle_position : 'above';
var subtitle_class = "<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_subtitle_class', 'sc_item_subtitle '.$sc.'_subtitle', $sc)); ?>";

var description_class = "<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_description_class', 'sc_item_descr '.$sc.'_descr', $sc)); ?>";

var title_html  = '';
var subtitle_html = '';

if (settings.subtitle) {
	subtitle_html += '<span class="'
						+ subtitle_class
						+ subtitle_align
						+ ' sc_item_subtitle_' + subtitle_position
						+ title_style
						+ ( settings.subtitle_color ? ' sc_item_subtitle_with_custom_color' : '')
					+ '">'
						+ trx_addons_prepare_macros(settings.subtitle)
					+ '</span>';
}
if (settings.subtitle_position == 'above' && (settings.title == '' || trx_addons_is_off(settings.subtitle_align) || settings.subtitle_align == settings.title_align) ) {
	title_html += subtitle_html;
}
if (settings.title) {
	// Dual title
	var dual_open  = trx_addons_apply_filters( 'trx_addons_filter_dual_title_open',  '[[' ),
		dual_close = trx_addons_apply_filters( 'trx_addons_filter_dual_title_close', ']]' );
	// Append 'title2' to 'title'
	if ( settings.title2 ) {
		title_text += dual_open  + settings.title2 + dual_close;
	}

	// Prepare a 'typed' part
	if ( settings.typed > 0 && settings.typed_strings != '' ) {
		// Don't process strings with 'trim' to enable single type behaviour
		var typed_strings = settings.typed_strings.split("\n"),
			typed_strings_json = JSON.stringify(typed_strings).replace(/"/g, '&quot;');
		title_text = title_text.replace(
							typed_strings[0],
							'<span class="sc_typed_entry"'
								+ ' data-strings="' + typed_strings_json + '"'
								+ ' data-loop="' + (settings.typed_loop ? 1 : 0 ) + '"'
								+ ' data-cursor="' + ( settings.typed_cursor ? 1 : 0 ) + '"'
								+ ' data-cursor-char="|"'
								+ ' data-speed="' + settings.typed_speed.size + '"'
								+ ' data-delay="' + settings.typed_delay.size + '"'
								+ ( settings.typed_color != '' ? ' style="color:' + settings.typed_color + '"' : '')
								+ '>' + typed_strings[0] + '</span>'
						);
	}
	var title_tag = !trx_addons_is_off(settings.title_tag)
					? settings.title_tag
					: "<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_title_tag', 'large' == $size ? 'h2' : ('tiny' == $size ? 'h4' : 'h3'))); ?>";
	var title_tag_class = ( ! trx_addons_is_off(settings.title_tag)
							? ' sc_item_title_tag'
							: ''
							)
						+ ( settings.typed > 0
							? ' sc_typed'
							: ''
							);

//---!!!							
//	var title_tag_style = settings.title_color != '' && settings.title_style != 'gradient'
//							? 'color:' + settings.title_color
//							: '';

	<?php do_action( 'trx_addons_action_tpe_item_title_tag' ); ?>

	// Open a title tag
	title_html += '<' + title_tag
					+ ' class="'
						+ title_class
						+ title_tag_class
						+ title_align
						+ title_style
						+ '"'
//---!!!
//					+ (title_tag_style != ''
//						? ' style="' + title_tag_style + '"'
//						: '')

					<?php do_action( 'trx_addons_action_tpe_item_title_data' ); ?>

				+ '>'
					+ ( !trx_addons_is_off(settings.subtitle_align) && subtitle_align != title_align
							? '<span class="sc_item_title_inner">'
								+ ( subtitle_position == 'above'
									? subtitle_html
									: ''
									)
							: ''
							);

	// Decorate gradient
	var gradient_fill = 'block', gradient_direction = 0;
	var add_class = '', add_style = '';
	if ( settings.title_style == 'gradient' ) {
		if ( settings.gradient_fill ) {
			gradient_fill = settings.gradient_fill;
		}
		if ( settings.gradient_direction ) {
			gradient_direction = settings.gradient_direction.size;
		}
		add_class = 'trx_addons_text_gradient trx_addons_text_gradient_fill_' + gradient_fill;
		add_style = settings.title_color != ''
								? ' style="'
									+ 'color:' + settings.title_color + ';'
									+ 'background:linear-gradient(' 
										+ Math.max(0, Math.min(360, gradient_direction)) + 'deg,'
										+ (settings.title_color2 ? settings.title_color2 : 'transparent') + ','
										+ settings.title_color
										+ ');'
									+ '"'
								: '';
	}

	// Decorate 'title2' parts
	var add_class2 = '', add_style2 = '';
	if ( title_text.indexOf( dual_open ) >= 0 ) {
		// Decorate gradient
		if ( settings.title_style == 'gradient' ) {
			if ( settings.gradient_fill2 ) {
				gradient_fill = settings.gradient_fill2;
			}
			if ( settings.gradient_direction2 ) {
				gradient_direction = settings.gradient_direction2.size;
			}
			add_class2 = 'trx_addons_text_gradient trx_addons_text_gradient_fill_' + gradient_fill;
			add_style2 = settings.title2_color
									? ' style="'
										+ 'color:' + settings.title2_color + ';'
										+ 'background:linear-gradient(' 
											+ Math.max(0, Math.min(360, gradient_direction)) + 'deg,'
											+ (settings.title2_color2 ? settings.title2_color2 : 'transparent') + ','
											+ settings.title2_color
											+ ');'
										+ '"'
									: '';
		}
		title_text = title_text
						// Replace open and close macros with tags
						.split( dual_open ).join( '</span><span class="sc_item_title_text2' + ( add_class2 ? ' ' + add_class2 : '' ) + '"' + add_style2 + '>' )
						.split( dual_close ).join( '</span><span class="sc_item_title_text' + ( add_class ? ' ' + add_class : '' ) + '"' + add_style + '>' );
	}

	// Wrap text to the span
	title_text = '<span class="sc_item_title_text' + ( add_class ? ' ' + add_class : '' ) + '"' + add_style + '>' + title_text + '</span>';

	// Remove empty tags
	title_text = title_text
						.split( '<span class="sc_item_title_text2' + ( add_class2 ? ' ' + add_class2 : '' ) + '"' + add_style2 + '></span>' ).join( '' )
						.split( '<span class="sc_item_title_text' + ( add_class ? ' ' + add_class : '' ) + '"' + add_style + '></span>' ).join( '' );

	// Add title
	title_html += trx_addons_prepare_macros( title_text );

	// Add subtitle
	title_html += ! trx_addons_is_off(settings.subtitle_align) && subtitle_align != title_align
					? (subtitle_position != 'above'
						? subtitle_html
						: ''
						)
						+ '</span>'
					: '';

	// Close a title tag
	title_html += '</' + title_tag + '>';
}

if (settings.subtitle_position != 'above' && (trx_addons_is_off(settings.subtitle_align) || settings.subtitle_align == settings.title_align) ) {
	title_html += subtitle_html;
}

if (settings.description) {
	title_html += '<div class="' + description_class + title_align + ( settings.description_color ? ' sc_item_descr_with_custom_color' : '') + '">'
					+ trx_addons_prepare_macros( settings.description )
					+ '</div>';
}
print(title_html);
#>