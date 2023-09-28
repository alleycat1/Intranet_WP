<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract( get_query_var('trx_addons_args_widget_aboutme') );

extract( trx_addons_prepare_widgets_args( trx_addons_generate_id( 'widget_aboutme_' ), 'widget_aboutme' ) );

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
#>
<?php $blogusers = get_users( 'role=administrator' ); ?>
<#
var avatar = settings.avatar.url == ''
					? '<?php
						if (count($blogusers) > 0) {
							$mult = trx_addons_get_retina_multiplier();
							echo addslashes(get_avatar($blogusers[0]->user_email, 220*$mult));
						}
						?>'
					: '<img src="' + settings.avatar.url + '" alt="' + username + '">';
var description = settings.username == '' && settings.description == '' 
					? '<?php if ( count( $blogusers ) > 0 ) echo addslashes( str_replace( array( "\r", "\n" ), array( '', '<br>'), $blogusers[0]->description ) ); ?>'
					: settings.description;
var username = settings.username == '' 
					? '<?php if ( count( $blogusers ) > 0 ) echo addslashes( $blogusers[0]->display_name ); ?>'
					: settings.username;
if (avatar != '') {
	#><div class="aboutme_avatar">{{{ avatar }}}</div><#
}
if (username != '' && username != '#') {
	#><h5 class="aboutme_username">{{ username }}</h5><#
}
if (description != '' && description != '#') {
	#><div class="aboutme_description">{{{ description }}}</div><#
}
#><?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
?>