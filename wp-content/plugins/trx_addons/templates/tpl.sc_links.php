<?php
/**
 * The template to display shortcode's link
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

extract(get_query_var('trx_addons_args_sc_show_links'));

$align = !empty($args['title_align']) ? ' sc_align_'.trim($args['title_align']) : '';
if (!empty($args['link_image']) && ($args['link_image'] = trx_addons_get_attachment_url($args['link_image'], trx_addons_get_thumb_size('medium')))!='') {
	$attr = trx_addons_getimagesize($args['link_image']);
	?><div class="<?php echo esc_attr($sc); ?>_button_image sc_item_button_image<?php echo esc_attr($align); ?>"><?php
		if (!empty($args['link'])) {
			?><a href="<?php echo esc_url($args['link']); ?>"<?php
				if (!empty($args['new_window']) || !empty($args['link_extra']['is_external'])) echo ' target="_blank"';
				if (!empty($args['nofollow']) || !empty($args['link_extra']['nofollow'])) echo ' rel="nofollow"';
			?>><?php
		}
		?><img src="<?php echo esc_url($args['link_image']); ?>" alt="<?php esc_attr_e("Button's image", 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
		if (!empty($args['link'])) {
			?></a><?php
		}
	?></div><?php
} else if (!empty($args['link']) && !empty($args['link_text'])) {
	if (empty($args['link_style'])) $args['link_style'] = 'default';
	if (empty($args['link_size'])) $args['link_size'] = 'normal';
	trx_addons_show_layout( trx_addons_sc_button( apply_filters( 'trx_addons_filter_sc_item_button_args',
																	array(
																		'type' => $args['link_style'],
																		'size' => $args['link_size'],
																		'title' => $args['link_text'],
																		'link' => $args['link'],
																		'link_extra' => ! empty( $args['link_extra'] ) ? $args['link_extra'] : array('link' => $args['link'], 'is_external' => ''),
																		'class' => 'sc_item_button sc_item_button_' . esc_attr( $args['link_style'] ) . ' sc_item_button_size_' . esc_attr( $args['link_size'] ) . ' ' . esc_attr( $sc ) . '_button',
																		'align' => ! empty( $args['title_align'] ) ? $args['title_align'] : 'none'
																	),
																	$sc,
																	$args
	) ) );
}
