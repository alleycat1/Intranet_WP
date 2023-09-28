<?php
/**
 * The style "default" of the Widget "Contacts"
 *
 * @package ThemeREX Addons
 * @since v1.6.10
 */

$args = get_query_var('trx_addons_args_widget_contacts');
extract($args);
	
// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);
	
// Widget body
?><div class="contacts_wrap"><?php

if (!empty($logo)) {
	?><div class="contacts_logo"><?php
		trx_addons_show_layout($logo);
	?></div><?php
}
if (!empty($description)) {
	?><div class="contacts_description"><?php echo do_shortcode($description); ?></div><?php
}
$show_info = !empty($address) || !empty($phone) || !empty($email);
if ( ! $show_info ) {
	$map_position = 'top';
}
if ( $show_info || !empty($map) ) {
	if ( $show_info && !empty($map) ) {
		?><div class="contacts_place contacts_map_<?php echo esc_attr($map_position); ?>"><?php
	}
	$map_type = empty( $map )
				? ''
				: ( function_exists('trx_addons_sc_googlemap')
					? 'google'
					: ( function_exists('trx_addons_sc_osmap')
						? 'openstreet' 
						: ''
						)
					);
	if ( ! empty($map_type) && ! empty($address) ) {
		$map_args = apply_filters( 'trx_addons_filter_contacts_map_args', array(
						'height' => $map_height,
						'zoom' => 13,
						'style' => $map_type == 'openstreet' ? 'streets' : 'default',
						'markers' => array(
							array(
								'address' => $address,
								)
							)
						)
					);
		if ( $map_type == 'google' ) {
			trx_addons_show_layout( trx_addons_sc_googlemap($map_args), '<div class="contacts_map">', '</div>' );
		} else if ($map_type == 'openstreet' ) {
			trx_addons_show_layout( trx_addons_sc_osmap($map_args), '<div class="contacts_map">', '</div>' );
		}
	}
	if ($show_info) {
		?><div class="contacts_info"><?php
			if (!empty($address)) {
				if ($columns) {
					?><div class="contacts_left"><?php
				}
				?><span class="contacts_address"><?php echo str_replace('|', "<br>", $address); ?></span><?php
				if ($columns) {
					?></div><?php
				}
			}
			if (!empty($phone) || !empty($email)) {
				if ($columns) {
					?><div class="contacts_right"><?php
				}
				if (!empty($email)) {
					?><span class="contacts_email"><a href="mailto:<?php echo antispambot($email); ?>"><?php echo antispambot($email); ?></a></span><?php
				}
				if (!empty($phone)) {
					?><a href="<?php echo esc_attr(trx_addons_get_phone_link($phone)); ?>" class="contacts_phone"><?php echo wp_kses_data($phone); ?></a><?php
				}
				if ($columns) {
					?></div><?php
				}
			}
		?></div><?php
	}
	if ($show_info && !empty($map)) {
		?></div><?php
	}
}

// Social icons
if ( $socials && ($output = trx_addons_get_socials_links()) != '') {
	?><div class="contacts_socials socials_wrap"><?php trx_addons_show_layout($output); ?></div><?php
}

// Custom content
if ( !empty($content) ) {
	?><div class="contacts_content"><?php trx_addons_show_layout($content); ?></div><?php
}

?></div><?php
	
// After widget (defined by themes)
trx_addons_show_layout($after_widget);
