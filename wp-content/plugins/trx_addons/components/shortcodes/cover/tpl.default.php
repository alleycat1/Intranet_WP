<?php
/**
 * The style "default" of the Cover link
 *
 * @package ThemeREX Addons
 * @since v1.86.0
 */

$args = get_query_var('trx_addons_args_sc_cover');

?><a href="<?php echo empty( $args['url'] ) ? '#' : esc_url( $args['url'] ); ?>"
	class="sc_cover sc_cover_<?php echo esc_attr($args['type']); ?>"
	data-place="<?php echo esc_attr($args['place']); ?>"<?php
	if ( ! empty($args['new_window']) || ! empty($args['url_extra']['is_external']) ) echo ' target="_blank"';
	if ( ! empty($args['url_extra']['nofollow']) ) echo ' rel="nofollow"';
	if ( ! empty($args['css']) ) echo ' style="' . esc_attr($args['css']) . '"';
	trx_addons_sc_show_attributes('sc_cover', $args, 'sc_item_wrapper');
?>></a>