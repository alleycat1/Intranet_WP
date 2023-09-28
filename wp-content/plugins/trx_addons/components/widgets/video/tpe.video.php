<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract( get_query_var('trx_addons_args_widget_video') );

extract( trx_addons_prepare_widgets_args( trx_addons_generate_id( 'widget_video_' ), 'widget_video' ) );

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
?><#
if (settings.title != '') {
	#><?php trx_addons_show_layout($before_title); ?><#
	print(settings.title);
	#><?php trx_addons_show_layout($after_title); ?><#
}

// Widget body
var type = settings.type || 'default';

if ( type == 'default' ) {
	if ( settings.link != '' && settings.embed == '' ) {
	   settings.embed = trx_addons_get_embed_from_url(settings.link, settings.autoplay, settings.mute);
	}
	if ( settings.link != '' || settings.embed != '' ) {
		var id = settings._element_id ? settings._element_id + '_sc' : 'sc_video_' + ( '' + Math.random() ).replace( '.', '' );
		#><div id="{{ id }}" class="trx_addons_video_player <# print( settings.cover.url != '' ? 'with_cover hover_play' : 'without_cover' ); #>"><#
			if ( settings.cover.url != '' ) {
				#><img src="{{ settings.cover.url }}" alt="<?php esc_attr_e( "Video cover", 'trx_addons' ); ?>">
				<div class="video_mask"></div>
				<div class="video_hover" data-video="<# print( _.escape( settings.embed ) ); #>"></div><#
			}
			#>
			<div class="video_embed video_frame"><#
				if ( settings.cover.url == '' ) {
					print(settings.embed);
				}
			#></div>
		</div><#
	}

} else if ( type == 'hover' ) {
	if ( settings.link != '' ) {
		var id = settings._element_id ? settings._element_id + '_sc' : 'sc_video_' + ( '' + Math.random() ).replace( '.', '' );
		#><div id="{{ id }}" class="trx_addons_video_hover <# print( settings.cover.url != '' ? 'with_cover' : 'without_cover' ); #>">
			<div class="trx_addons_video_media" data-ratio="{{ settings.ratio }}">
				<#
				if ( settings.cover.url != '' ) {
					#><picture type="image" class="trx_addons_video_cover"><img src="{{ settings.cover.url }}" alt="<?php esc_attr_e("Video cover", 'trx_addons'); ?>"></picture><#
				}
				#>
				<video class="trx_addons_video_video inited trx_addons_noresize" playsinline disablepictureinpicture loop="loop"<#
					if ( settings.autoplay > 0 && settings.cover.url == '' ) print( ' autoplay="autoplay"' );
					if ( typeof settings.mute == 'undefined' || settings.mute > 0 ) print( ' muted="muted"' );
				#>>
					<source src="{{ settings.link }}" type="video/mp4" />
				</video>
			</div>
			<#
			if ( settings.subtitle != '' ) {
				#><p class="trx_addons_video_subtitle"><span class="trx_addons_video_subtitle_text"><# print( trx_addons_prepare_macros( settings.subtitle ) ); #></span></p><#
			}
		#></div><#
	}

} else {
	trx_addons_do_action( 'trx_addons_action_widget_video_layout', type, settings );
}
#><?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
