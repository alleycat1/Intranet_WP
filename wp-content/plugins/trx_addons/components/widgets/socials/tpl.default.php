<?php
/**
 * The style "default" of the Widget "Socials"
 *
 * @package ThemeREX Addons
 * @since v1.6.10
 */

$args = get_query_var('trx_addons_args_widget_socials');
extract($args);

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);

// Widget body
if ($description) {
	?><div class="socials_description"><?php echo do_shortcode($description); ?></div><?php
}

// Display widget body
$output = $type == 'socials'
			? trx_addons_get_socials_links()
			: trx_addons_get_share_links(array(
					'type' => 'block',
					'caption' => '',
					'echo' => false
				));
if ( !empty($output) ) {
	?><div class="<?php echo esc_attr($type); ?>_wrap sc_align_<?php echo esc_attr($align); ?>"><?php trx_addons_show_layout($output); ?></div><?php
}
	
// After widget (defined by themes)
trx_addons_show_layout($after_widget);
