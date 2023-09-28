<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.94.0
 */

extract( get_query_var( 'trx_addons_args_sc_hotspot' ) );
?><#
settings = trx_addons_elm_prepare_global_params( settings );

var id = settings._element_id ? settings._element_id + '_sc' : 'sc_hotspot_' + ('' + Math.random()).replace('.', '');

var link_class = "<?php echo apply_filters( 'trx_addons_filter_sc_item_link_classes', 'sc_hotspot_item_link sc_button sc_button_size_small', 'sc_hotspot' ); ?>";

var image = settings.image.url;

if ( image ) {

	#><div id="{{ id }}" class="<# print( trx_addons_apply_filters( 'trx_addons_filter_sc_classes', 'sc_hotspot sc_hotspot_' + settings.type + ( settings.class ? ' ' + settings.class : '' ), settings ) ); #>">

		<?php $element->sc_show_titles('sc_hotspot'); ?>

		<div class="sc_hotspot_content sc_item_content">

			<img src="{{ image }}" class="sc_hotspot_image"><#

			var numbers = 0;

			_.each( settings.spots, function( item ) {
				item.open = item.open > 0 ? 'click' : 'hover';
				item.opened = item.opened > 0 ? 'sc_hotspot_item_opened' : '';
				item.spot_visible = item.spot_visible > 0 ? 'always' : 'hover';
				#><div class="<#
						print( trx_addons_apply_filters(
								'trx_addons_filter_sc_item_classes',
								'sc_hotspot_item sc_hotspot_item_visible_' + item.spot_visible + ' sc_hotspot_item_open_' + item.open + ' ' + item.opened,
								'sc_hotspot',
								item
							) );
				#>" style="left:{{ item.spot_x.size }}%; top:{{ item.spot_y.size }}%;">
					<span class="sc_hotspot_item_sonar"<#
						if ( item.spot_sonar_color ) {
							print( ' style="background-color:' + item.spot_sonar_color + '"' );
						}
					#>></span><#

					var icon = '',
						icon_text = '',
						svg = '',
						img = '';

					if ( typeof item.icon_type == 'undefined' ) {
						item.icon_type = '';
					}

					if ( trx_addons_is_off( item.spot_symbol ) ) {
						item.icon_type = 'none';

					} else if ( item.spot_symbol == 'custom' ) {
						if ( item.spot_char ) {
							item.spot_char = ('' + item.spot_char).trim();	//.charAt(0);
						}
						if ( item.spot_char ) {
							item.icon_type = 'custom';
							icon = 'char-' + item.spot_char;
							icon_text = item.spot_char;
						} else {
							item.icon_type = 'none';
						}

					} else if ( item.spot_symbol == 'number' ) {
						numbers++;
						item.icon_type = 'number';
						icon = 'number-' + numbers;
						icon_text = numbers;

					} else if ( item.spot_symbol == 'icon' ) {
						icon = trx_addons_get_settings_icon( item.icon );
						if ( ! icon ) icon = 'none';
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

					} else if ( item.spot_symbol == 'image' ) {
						img = item.spot_image.url;
						item.icon_type = 'images';
						icon = trx_addons_get_basename( img );
					}
					// Icon
					print( item.link.url == '' || item.open == 'click'
							? '<span'
							: '<a href="' + item.link.url + '"' );
					#> class="sc_hotspot_item_icon sc_hotspot_item_icon_type_{{ item.icon_type }} {{ icon }}"<#
						if ( item.spot_bg_color != '' ) {
							print(' style="background-color:' + item.spot_bg_color + ';"');
						}
					#>><#
						if ( svg != '' ) {
							#><span class="sc_icon_type_{{ item.icon_type }} {{ icon }}"><object type="image/svg+xml" data="{{ svg }}" border="0"></object></span><#
						} else if ( img != '' ) {
							#><img class="sc_icon_as_image" src="{{ img }}" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"><#
						} else {
							#><span class="sc_icon_type_{{ item.icon_type }} {{ icon }}"<#
								if ( item.spot_color != '' ) print(' style="color: ' + item.spot_color + ';"');
							#>>{{ icon_text }}</span><#
						}
					print( item.link.url == '' || item.open == 'click'
							? '</span>'
							: '</a>'
					);
					#>

					<div class="sc_hotspot_item_popup <#
						print( trx_addons_get_responsive_classes( 'sc_hotspot_item_popup_', item, 'position', 'bc' ) );
					#> sc_hotspot_item_popup_align_<#
						print( item.align ? item.align : 'center' );
					#>"><#
						// Add button 'Close' to the clickable items
						if ( item.open == 'click' ) {
							#><span class="sc_hotspot_item_popup_close trx_addons_button_close"><span class="trx_addons_button_close_icon"></span></span><#
						}
						// Dynamic content (from post) can't be shown in the edit mode
						if ( item.source != 'custom' ) {
							item.image = { url: typeof item['post_parts'] == 'undefined' || item['post_parts'].indexOf( 'image' ) >= 0
													? '<?php echo trx_addons_get_no_image( '', true ) ?>'
													: '' };
							item.subtitle = typeof item['post_parts'] == 'undefined' || item['post_parts'].indexOf( 'category' ) >= 0
													? "<?php esc_html_e( 'Attention', 'trx_addons' ); ?>"
													: '';
							item.title = typeof item['post_parts'] == 'undefined' || item['post_parts'].indexOf( 'title' ) >= 0
													? "<?php esc_html_e( 'Dynamic content', 'trx_addons' ); ?>"
													: '';
							item.description = typeof item['post_parts'] == 'undefined' || item['post_parts'].indexOf( 'excerpt' ) >= 0
													? "<?php esc_html_e( 'Dynamic content is not available in the edit mode!', 'trx_addons' ); ?>"
													: '';
							item.price = typeof item['post_parts'] == 'undefined' || item['post_parts'].indexOf( 'price' ) >= 0
													? '99.99'
													: '';
							item.link = { url: '' };
							item.link_text = '';
						}

						// Prepare content
						if ( item.image.url != '' ) {
							#><div class="sc_hotspot_item_image"><img src="{{ item.image.url }}" alt="<?php esc_attr_e("Hotspot item image", 'trx_addons'); ?>"></div><#
						}
						if ( item.subtitle != '' ) {
							item.subtitle = item.subtitle.split( '|' );
							#><h6 class="sc_hotspot_item_subtitle"><#
								_.each( item.subtitle, function( str ) {
									print( '<span>' + str + '</span>' );
								} );
							#></h6><#
						}
						if ( item.title != '' ) {
							item.title = item.title.split( '|' );
							#><h5 class="sc_hotspot_item_title"><#
								_.each( item.title, function( str ) {
									print( '<span>' + str + '</span>' );
								} );
							#></h5><#
						}
						if ( item.price != '' ) {
							#><div class="sc_hotspot_item_price">{{ item.price }}</div><#
						}
						if ( item.description != '' ) {
							item.description = item.description
													.replace(/\[(.*)\]/g, '<b>$1</b>')
													.replace(/\n/g, '|')
													.split('|');
							#><div class="sc_hotspot_item_description"><#
								_.each( item.description, function( str ) {
									print( '<span>' + str + '</span>' );
								} );
							#></div><#
						}
						if ( item.link.url != '' ) {
							#><a href="{{ item.link.url }}" class="<#
								if ( item.link_text != '' ) {
									print( link_class );
								} else {
									print( 'sc_hotspot_item_link_cover' );
								}
							#>"><#
								if ( item.link_text != '' ) print( item.link_text );
							#></a><#
						}
					#></div>
				</div><#
			} );

			if ( settings.image_link.url ) {
				#><a href="{{ settings.image_link.url }}" class="sc_hotspot_link_cover"></a><#
			}

		#></div>

		<?php $element->sc_show_links('sc_hotspot'); ?>

	</div><#
}

settings = trx_addons_elm_restore_global_params( settings );
#>