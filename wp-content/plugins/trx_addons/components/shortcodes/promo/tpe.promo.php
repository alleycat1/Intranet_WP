<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_promo'));
?><#
settings = trx_addons_elm_prepare_global_params( settings );

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_promo_'+(''+Math.random()).replace('.', ''),
	icon = trx_addons_get_settings_icon( settings.icon );

if ( trx_addons_is_off(icon) ) icon = '';

if (settings.images.length == 0) {
	settings.text_width = '100%';
	settings.image_width = '0%';
	settings.gap = '';
} else if ( settings.title == '' && settings.subtitle == '' && settings.description == '' && settings.content == '' 
		&& (settings.link.url == '' || settings.link_text == '') ) {
	settings.image_width = '100%';
	settings.text_width = '0%';
	settings.gap = '';
} else {
	if (settings.image_width.size == 0)
		settings.image_width = {'size': 50, 'unit': '%'};
	if (settings.gap.size > 0) {
		if (settings.image_width.unit == settings.gap.unit) {
			settings.text_width = settings.image_width.unit == '%'
									? (100 - settings.gap.size/2 - settings.image_width.size) + '%'
									: 'calc(100% - ' + settings.gap.size+settings.gap.unit + '/2 - ' + settings.image_width.size + settings.image_width.unit + ')';
			settings.image_width = (settings.image_width.size - settings.gap.size/2) + settings.image_width.unit;
		} else {
			settings.text_width = 'calc(100% - ' + settings.gap.size + settings.gap.unit + '/2 - ' + settings.image_width.size + settings.image_width.unit + ')';
			settings.image_width = 'calc(' + settings.image_width.size + settings.image_width.unit + ' - ' + settings.gap.size + settings.gap.unit + '/2)';
		}
	} else {
		settings.text_width = settings.image_width.unit == '%' 
						? (100 - settings.image_width.size) + '%'
						: 'calc(100% - ' + settings.image_width.size + settings.image_width.unit + ')';
		settings.image_width = settings.image_width.size + settings.image_width.unit;
	}
	settings.gap = settings.gap.size > 0 ? settings.gap.size + settings.gap.unit : '';
}

var css_image_wrap = (settings.images.length > 0 ? 'width:'+settings.image_width + ';' : '')
			+ (!trx_addons_is_off(settings.image_position) ? settings.image_position+':0;' : '');

var css_image = (settings.type != 'modern' ? css_image_wrap : '')
			+ (settings.image_bg_color != '' ? 'background-color:' + settings.image_bg_color + ';' : '')
			+ (settings.images.length == 1 && settings.images[0].url !='' ? 'background-image:url(' + settings.images[0].url + ');' : '');

var css_text = 'width: ' + settings.text_width + ';'
			+ (settings.images.length > 0 ? 'float:' + (settings.image_position=='left' ? 'right' : 'left') + ';' : '');

#><div id="{{ id }}" class="<# print( trx_addons_apply_filters( 'trx_addons_filter_sc_classes',
	'sc_promo sc_promo_' + settings.type + ' sc_promo_size_' + settings.size
	+ ( settings.text_paddings != 1 ? ' sc_promo_no_paddings' : '' )
	+ ( settings.images.length > 0
		? ( settings.image_cover == 0 ? ' sc_promo_image_fit' : '' ) + ' sc_promo_image_position_' + settings.image_position
		: ' sc_promo_no_image'
		),
	settings ) );
#>"><#
	
	// Image or Gallery (Slider) or Video
	if (settings.images.length > 0) {

		if (settings.type == 'modern') {
			#><div class="sc_promo_image_wrap" style="{{ css_image_wrap }}"><#
		}

		#><div class="sc_promo_image" style="{{ css_image }}"><#
			if (settings.video_url != '' || settings.video_embed != '') {
				if (settings.video_url != '' && settings.video_embed == '') {
				   settings.video_embed = trx_addons_get_embed_from_url(settings.video_url);
				}
				#><div id="{{ id }}_video" class="trx_addons_video_player with_cover hover_play">
					<img src="{{ settings.images[0].url }}" alt="<?php esc_attr_e('Video cover', 'trx_addons'); ?>">
					<div class="video_mask"></div>
					<div class="video_hover" data-video="{{ settings.video_embed }}"></div>
					<div class="video_embed video_frame"></div>
				</div><#
			} else if (settings.images.length > 1) {
				settings.slider = 1;
				settings.slider_effect = 'slide';
				settings.slider_controls = 'side';
				settings.slider_pagination = 'bottom';
				settings.slides_space = {'size': 0 };
				settings.slides_centered = 0;
				settings.slides_overflow = 0;
				settings.slider_mouse_wheel = 0;
				settings.slider_autoplay = 1;
				settings.slider_loop = 1;
				settings.slider_free_mode = 0;
				settings.slides_min_width = 250;
				#>
				<?php $element->sc_show_slider_wrap_start('sc_promo'); ?>
				<#
					_.each(settings.images, function(item) {
						#><div class="slider-slide swiper-slide" style="background-image: url({{ item.url }});"></div><#
					});
					#>
				</div>
				<?php $element->sc_show_slider_wrap_end('sc_promo'); ?>
				<#
			}
		#></div><#

		if (settings.type == 'modern') {
			if (settings.link2 != '' && settings.link2_text != '') {
				#><a class="sc_promo_link2" href="{{ settings.link2.url }}"><#
					var text = settings.link2_text.split('|');
					_.each(text, function(str) {
						#><span>{{ str }}</span><#
					});
				#></a><#
			}
			#></div><#
		}
	}

	// Text
	if (settings.title != '' || settings.subtitle != '' || settings.description != '' || settings.content !='' 
		|| (settings.link.url != '' && settings.link_text != '') || settings.link_image.url != '') {
		if (settings.type == 'blockquote') {
			#><blockquote class="sc_promo_text trx_addons_blockquote_style_1 sc_align_{{ settings.text_align }}" style="{{ css_text }}"><#
				if (settings.description != '') {
					#><p><# print(trx_addons_prepare_macros(settings.description)); #></p><#
				}
				if (settings.content != '' && 'tiny' != settings.size) {
					print(settings.content);
				}
				if (settings.link.url !='' && settings.link_text != '') {
					#><p><a href="{{ settings.link.url }}">{{ settings.link_text }}</a></p><#
				}
			#></blockquote><#
		} else {
			#><div class="sc_promo_text<#
				if (!trx_addons_is_off(settings.text_float)) print(' sc_float_' + settings.text_float);
				if (settings.full_height == 1) print(' trx_addons_stretch_height');
				#>" style="{{ css_text }}">
				<div class="sc_promo_text_inner sc_align_{{ settings.text_align }}"><#
					if (icon != '') {
						#><div class="sc_promo_icon" data-icon="{{ icon }}"><span class="{{ icon }}"></span></div><#
					}
					#><?php $element->sc_show_titles('sc_promo'); ?><#
					if (settings.content != '' && 'tiny' != settings.size) {
						#><div class="sc_promo_content sc_item_content">{{{ settings.content }}}</div><#
					}
					if ('tiny' != settings.size) {
						if (settings.type == 'modern' && settings.link_style == '') settings.link_style = 'simple';
						#><?php $element->sc_show_links('sc_promo'); ?><#
					}
				#></div>
			</div><#
		}
	}
	if ( 'tiny' == settings.size ) {
		if (settings.link.url != '') {
			#><a href="{{ settings.link.url }}" class="sc_promo_link"></a><#
		}
	}
#></div><#

settings = trx_addons_elm_restore_global_params( settings );
#>