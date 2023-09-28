<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract( get_query_var( 'trx_addons_args_widget_audio' ) );

extract( trx_addons_prepare_widgets_args( trx_addons_generate_id( 'widget_audio_' ), 'widget_audio' ) );

/* Before widget (defined by themes) */
trx_addons_show_layout( $before_widget );

/* Widget title if one was input (before and after defined by themes) */
?><#
if (settings.title != '') {
	#><?php trx_addons_show_layout( $before_title ); ?><#
	print(settings.title);
	#><?php trx_addons_show_layout( $after_title ); ?><#
}

if ( settings.subtitle != '' ) { 
	#><div class="widget_subtitle"><# print(settings.subtitle); #></div><#
}

if (settings.media.length > 0) {
	var wrap_track_time = settings.track_time != '1' ? ' hide_time' : '';
	var wrap_track_scroll = settings.track_scroll != '1' ? ' hide_scroll' : '';
	var wrap_track_volume = settings.track_volume != '1' ? ' hide_volume' : '';
	var wrap_list = settings.media.length > 1 ? ' list' : '';
	var wrap_class = wrap_track_time + wrap_track_scroll + wrap_track_volume + wrap_list;
	#><div class="trx_addons_audio_wrap{{ wrap_class }}">
		<div class="trx_addons_audio_list"><#
			_.each(settings.media, function(item) {
				#><div class="trx_addons_audio_player <# print(item.cover.url != '' ? 'with_cover' : 'without_cover'); #>"<#
					if (item.cover.url != '') print(' style="background-image:url(' + item.cover.url + ');"');
				#>>
					<div class="trx_addons_audio_player_wrap"><#
						if (item.author != '' || item.caption != '') {
							#><div class="audio_info"><#
								var now_text = settings.now_text !== "" ? settings.now_text : "<?php esc_html_e( 'Now Playing', 'trx_addons' ); ?>";
								if ( now_text != "#" && settings.media.length > 1) {
									#><h5 class="audio_now_playing">{{ now_text }}</h5><#
								}
								if (item.author != '') {
									#><h6 class="audio_author">{{ item.author }}</h6><#
								}
								if (item.caption != '') {
									#><h5 class="audio_caption">{{ item.caption }}</h5><#
								}
								if (item.description != '') {
									#><div class="audio_description">{{ item.description }}</div><#
								}
							#></div><#
						}

						#><div class="audio_frame audio_<# print(item.embed != '' ? 'embed' : 'local'); #>"><#
							if (item.embed != '')
								print(item.embed);
							else if (item.url != '') {
								#><audio src="{{ item.url }}">
									<source type="audio/mpeg" src="{{ item.url }}">
								</audio><#
							}
						#></div>
					</div>
				</div><#
			});
			#></div><#
			if (settings.media.length > 1) {
				if (settings.prev_btn == '1' || settings.next_btn == '1'){
					#><div class="trx_addons_audio_navigation"><#
						if ( settings.prev_btn == '1')
						{
							#><span class="nav_btn prev"><span class="trx_addons_icon-slider-left"></span><#
							if ( settings.prev_text != '')
								print(settings.prev_text);
							#></span><#
						}
						if ( settings.next_btn == '1')
						{
							#><span class="nav_btn next"><#
							if ( settings.next_text != '')
								print(settings.next_text);
							#><span class="trx_addons_icon-slider-right"></span></span><#
						}
					#></div><#
				}
			} 
		#>
	</div><#
}
#><?php

/* After widget (defined by themes) */
trx_addons_show_layout( $after_widget );
