<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.97.0
 */

extract( get_query_var( 'trx_addons_args_sc_icompare' ) );
?><#
var id = settings._element_id ? settings._element_id + '_sc' : 'sc_icompare_' + ('' + Math.random()).replace('.', '');

var image1 = settings.image1.url,
	image2 = settings.image2.url;

if ( image1 && image2 ) {

	#><div id="{{ id }}"
		class="<#
			print( trx_addons_apply_filters( 'trx_addons_filter_sc_classes', 'sc_icompare sc_icompare' + settings.type
						+ ' sc_icompare_direction_' + settings.direction
						+ ' sc_icompare_event_' + settings.event
						+ ( settings.class ? ' ' + settings.class : '' ),
					settings ) );
		#>">

		<?php $element->sc_show_titles('sc_icompare'); ?>

		<div class="sc_icompare_content sc_item_content"><#

			#><img src="{{ image1 }}" class="sc_icompare_image sc_icompare_image0"><#
			#><img src="{{ image1 }}" class="sc_icompare_image sc_icompare_image1"><#
			#><img src="{{ image2 }}" class="sc_icompare_image sc_icompare_image2"><#

			if ( settings.before_text || settings.after_text ) {
				#><div class="sc_icompare_overlay"><#
					if ( settings.before_text ) {
						#><span class="sc_icompare_text_before sc_icompare_text_pos_{{ settings.before_pos }}">{{ settings.before_text }}</span><#
					}
					if ( settings.after_text ) {
						#><span class="sc_icompare_text_after sc_icompare_text_pos_{{ settings.after_pos }}">{{ settings.after_text }}</span><#
					}
				#></div><#
			}

			#><div class="sc_icompare_handler sc_icompare_handler_style_{{ settings.handler }}" data-handler-pos="{{ settings.handler_pos.size }}"><#
				// Separator
				if ( settings.handler_separator ) {
					#>
					<span class="sc_icompare_handler_separator sc_icompare_handler_separator1"></span>
					<span class="sc_icompare_handler_separator sc_icompare_handler_separator2"></span>
					<#
				}

				var shown = false,
					icon = '',
					svg = '',
					img = '';

				// Handler image
				if ( settings.handler_image.url ) {
					#><img src="{{ settings.handler_image.url }}" class="sc_icompare_handler_image"><#
					shown = true;

				// Handler icon
				} else {
					var icon = trx_addons_get_settings_icon( settings.icon );
					if ( icon ) {
						if ( trx_addons_is_url( icon ) ) {
							if ( icon.indexOf( '.svg' ) >= 0 ) {
								svg = icon;
								item.icon_type = 'svg';
							} else {
								img = icon;
								item.icon_type = 'images';
							}
							icon = trx_addons_get_basename( icon );
						}

						if ( svg != '' ) {
							#><span class="sc_icompare_handler_icon sc_icon_type_svg"><object type="image/svg+xml" data="{{ svg }}" border="0"></object></span><#
						} else if ( img != '' ) {
							#><img class="sc_icompare_handler_icon sc_icon_type_images sc_icon_as_image" src="{{ img }}" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"><#
						} else {
							#><span class="sc_icompare_handler_icon sc_icon_type_icon {{ icon }}"></span><#
						}
						shown = true;
					}
				}

				// Default arrows
				if ( ! shown ) {
					#><span class="sc_icompare_handler_arrows"></span><#
				}

			#></div><#

		#></div>

		<?php $element->sc_show_links('sc_icompare'); ?>

	</div><#
}
#>