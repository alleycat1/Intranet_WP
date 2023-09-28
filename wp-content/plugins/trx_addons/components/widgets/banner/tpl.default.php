<?php
/**
 * The style "default" of the Widget "Banner"
 *
 * @package ThemeREX Addons
 * @since v1.6.10
 */

$args = get_query_var('trx_addons_args_widget_banner');
extract($args);
		
// Before widget (defined by themes)
if ( trx_addons_is_on($fullwidth) ) {
	$before_widget = str_replace('class="widget ', 'class="widget widget_fullwidth ', $before_widget);
}
if ( ! is_admin() ) {
	$before_widget = str_replace('class="widget ', 'class="widget trx_addons_show_on_' . esc_attr( $banner_show ) . ' ' , $before_widget);
}

trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
if ($banner_image!='') {
	$attr = trx_addons_getimagesize($banner_image);
	echo ( ! empty($banner_link)
			? '<a href="' . esc_url($banner_link) . '"'
				. ( ! empty( $args['new_window'] ) || ! empty( $args['link_extra']['is_external'] )
					? ' target="_blank"'
					: '' )
				. ( ! empty( $args['nofollow'] ) || ! empty( $args['link_extra']['nofollow'] )
					? ' rel="nofollow"'
					: '' )
			: '<span'
			)
		. ' class="image_wrap">'
			. '<img src="' . esc_url($banner_image) . '" alt="' . esc_attr($title) . '"' . (!empty($attr[3]) ? ' '.trim($attr[3]) : '')	. '>'
		. ( ! empty( $banner_link )
			? '</a>'
			: '</span>'
			);
}
if ($banner_code!='') {
	trx_addons_show_layout( do_shortcode( $banner_code ) );
}
	
// After widget (defined by themes)
trx_addons_show_layout($after_widget);
