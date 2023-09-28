<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract( get_query_var('trx_addons_args_widget_banner') );

extract( trx_addons_prepare_widgets_args( trx_addons_generate_id( 'widget_banner_' ), 'widget_banner' ) );

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
if (settings.image.url == '') {
	if (settings.image.url == '' && settings.code == '') {
		settings.image.url = '<?php
								if (trx_addons_is_singular() && !trx_addons_sc_layouts_showed('featured')) {
									$image = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'full' );
									echo empty( $image[0] ) ? '' : addslashes($image[0]);
								}
							?>';
	}
}

if (settings.image.url != '') {
	print( (settings.link.url !=''
			? '<a href="' + settings.link.url + '"'
				+ ( settings.link.is_external == 'on' ? ' target="_blank"' : '' )
				+ ( settings.link.nofollow == 'on' ? ' rel="nofollow"' : '' )
			: '<span'
			)
			+ ' class="image_wrap">'
				+ '<img src="' + settings.image.url + '" alt="' + settings.title + '">'
			+ (settings.link.url !='' ? '</a>': '</span>')
		);
}

if (settings.code != '') print(settings.code);
	
#><?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
?>