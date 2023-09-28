<?php
/**
 * The style "default" of the Widget "Video"
 *
 * @package ThemeREX Addons
 * @since v1.6.10
 */

$args = get_query_var('trx_addons_args_widget_video');
extract($args);
		
// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
trx_addons_show_layout( trx_addons_get_video_layout( apply_filters( 'trx_addons_filter_get_video_layout_args', array(
	'link' => $link,
	'embed' => $embed,
	'cover' => empty( $autoplay ) ? $cover : '',
	'show_cover' => true,
	'popup' => empty( $autoplay ) && ! empty( $popup ),
	'autoplay' => ! empty( $autoplay ),
	'mute' => ! empty( $autoplay ) || ( isset( $mute ) && (int)$mute > 0 ),
	'loop' => ! empty( $autoplay ),
), 'video.default' ) ) );
	
// After widget (defined by themes)
trx_addons_show_layout($after_widget);
