<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract( get_query_var('trx_addons_args_widget_contacts') );

extract( trx_addons_prepare_widgets_args( trx_addons_generate_id( 'widget_contacts_' ), 'widget_contacts' ) );

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
?><#
if ( settings.title != '' ) {
	#><?php trx_addons_show_layout($before_title); ?><#
	print(settings.title);
	#><?php trx_addons_show_layout($after_title); ?><#
}

// Widget body
#><div class="contacts_wrap"><#
	if ( settings.logo.url != '' ) {
		var mult = <?php echo trx_addons_get_retina_multiplier(); ?>;
		if ( settings.logo_retina.url != '' && mult > 1 ) {
			settings.logo.url = settings.logo_retina.url;
		}
		#><div class="contacts_logo"><img src="{{ settings.logo.url }}" alt="<?php esc_attr_e("Contact's logo", 'trx_addons'); ?>"></div><#
	}
	if ( settings.description != '' ) {
		#><div class="contacts_description">{{{ settings.description }}}</div><#
	}
	var show_info = settings.address != '' || settings.phone != '' || settings.email != '';
	if ( ! show_info ) {
		settings.map_position = 'top';
	}
	if ( show_info || settings.map > 0 ) {
		if ( show_info && settings.map > 0 ) {
			#><div class="contacts_place contacts_map_{{ settings.map_position }}"><#
		}
		if ( settings.map > 0 && settings.address != '' ) {
			<?php
			if ( function_exists('trx_addons_sc_googlemap') ) {
				$map_type = $map_class = 'google';
			} else if ( function_exists('trx_addons_sc_osmap') ) {
				$map_type = 'openstreet';
				$map_class = 'os';
			}
			?>
			var map_type = '<?php echo esc_html( $map_type ); ?>';
			var id = settings._element_id ? settings._element_id + '_sc' : 'sc_contacts_map_'+(''+Math.random()).replace('.', '');
			var icon = "<?php
				if ( ! empty( $map_type ) ) {
					echo addslashes(trx_addons_remove_protocol(trx_addons_get_option('api_' . $map_type . '_marker')));
				}
			?>";
			#><div class="contacts_map">
				<div id="{{ id }}_wrap" class="sc_<?php echo esc_attr( $map_class ); ?>map_wrap">
					<div id="{{ id }}_map" class="sc_<?php echo esc_attr( $map_class ); ?>map sc_<?php echo esc_attr( $map_class ); ?>map_default"
						data-zoom="13"
						data-style="<# print( map_type == 'openstreet' ? 'streets' : 'default' ); #>"
					>
						<div id="{{ id }}_marker"
							class="sc_<?php echo esc_attr( $map_class ); ?>map_marker"
							data-address="<# print(_.escape(trx_addons_remove_macros(settings.address))); #>"></div>
					</div>
				</div>
			</div><#
		}
		if ( show_info ) {
			#><div class="contacts_info"><#
				if ( settings.address != '' ) {
					if ( settings.columns > 0 ) {
						#><div class="contacts_left"><#
					}
					#><span class="contacts_address"><# print(trx_addons_prepare_macros(settings.address)); #></span><#
					if ( settings.columns > 0 ) {
						#></div><#
					}
				}
				if ( settings.phone != '' || settings.email != '' ) {
					if ( settings.columns > 0 ) {
						#><div class="contacts_right"><#
					}
					if ( settings.email != '' ) {
						#><span class="contacts_email"><a href="mailto:{{ settings.email }}">{{ settings.email }}</a></span><#
					}
					if ( settings.phone != '' ) {
						#><a href="tel:{{ settings.phone }}" class="contacts_phone">{{ settings.phone }}</a><#
					}
					if ( settings.columns > 0 ) {
						#></div><#
					}
				}
			#></div><#
		}
		if ( show_info && settings.map > 0 ) {
			#></div><#
		}
	}

	// Social icons
	if ( settings.socials > 0) {
		#><div class="contacts_socials socials_wrap"><?php trx_addons_show_layout(trx_addons_get_socials_links()); ?></div><#
	}

#></div><?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
