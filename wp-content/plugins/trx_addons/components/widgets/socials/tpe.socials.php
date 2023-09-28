<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract( get_query_var('trx_addons_args_widget_socials') );

extract( trx_addons_prepare_widgets_args( trx_addons_generate_id( 'widget_socials_' ), 'widget_socials' ) );

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
if (settings.description != '') {
	#><div class="socials_description">{{{ settings.description }}}</div><#
}
var socials = settings.type == 'socials'
				? '<?php echo str_replace(array("'", "\r", "\n"), array("\\'", "%13", "%10"), trx_addons_get_socials_links()); ?>'
				: '<?php echo str_replace(array("'", "\r", "\n"), array("\\'", "%13", "%10"), trx_addons_get_share_links(array(
						'type' => 'block',
						'caption' => '',
						'echo' => false
					))); ?>';
if ( socials != '') {
	#><div class="{{ settings.type }}_wrap sc_align_{{ settings.align }}"><# print(socials); #></div><#
}
#><?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
