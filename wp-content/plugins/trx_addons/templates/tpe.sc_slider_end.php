<?php
/**
 * The template to display end of the slider's wrap for some shortcodes
 * on the Elementor's preview page
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_show_slider_wrap'));
?><#

var slider_controls_html = '<div class="slider_controls_wrap">'
							+ '<a class="slider_prev swiper-button-prev" href="#"></a>'
							+ '<a class="slider_next swiper-button-next" href="#"></a>'
						+ '</div>';
var slider_pagination_html = '<div class="slider_pagination_wrap swiper-pagination">'
							+ (settings.slider_pagination_buttons ? settings.slider_pagination_buttons : '')
						+ '</div>';

if (['side'].indexOf(settings.slider_controls) >= 0)			print(slider_controls_html);
if (['left', 'right'].indexOf(settings.slider_pagination) >= 0)	print(slider_pagination_html);

#></div><#

if (['outside', 'top', 'bottom'].indexOf(settings.slider_controls) >= 0)	print(slider_controls_html);
if (['bottom', 'bottom_outside'].indexOf(settings.slider_pagination) >= 0)	print(slider_pagination_html);

#></div>