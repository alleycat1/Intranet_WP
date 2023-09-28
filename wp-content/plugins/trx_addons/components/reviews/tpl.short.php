<?php
/**
 * Default template to display the "post reviews" block on the single page
 *
 * @package ThemeREX Addons
 * @since v1.6.34 (redesined in 1.6.57)
 */

$trx_addons_args = get_query_var('trx_addons_args_sc_reviews');
$gutenberg_preview = function_exists('trx_addons_gutenberg_is_preview') && trx_addons_gutenberg_is_preview() && !trx_addons_sc_stack_check('trx_sc_blogger');
$trx_addons_meta = $gutenberg_preview
						? array(
								'reviews_enable' => true,
								'reviews_mark' => 50,
								'reviews_title' => __('Review title', 'trx_addons'),
								'reviews_mark_text' => __('Mark title', 'trx_addons'),
								)
						: get_post_meta( get_the_ID(), 'trx_addons_options', true );
if ( !empty($trx_addons_meta['reviews_enable']) && $trx_addons_meta['reviews_mark'] > 0 ) {
	?><div class="trx_addons_reviews_block trx_addons_reviews_block_short sc_float_<?php
		echo esc_attr( $trx_addons_args['align'] );
		if ( in_array( $trx_addons_args['align'], array( 'left', 'right' ) ) ) {
			echo ' align' . esc_attr( $trx_addons_args['align'] );
		}
	?>"><?php
		if ( !empty($trx_addons_meta['reviews_title']) ) {
			?><h6 class="trx_addons_reviews_block_title"><?php echo esc_html($trx_addons_meta['reviews_title']); ?></h6><?php
		}
		if ( !empty($trx_addons_meta['reviews_image']) ) {
			$image = trx_addons_get_attachment_url($trx_addons_meta['reviews_image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry'), 'reviews'));
			if (!empty($image)) {
				$attr = trx_addons_getimagesize($image);
				?><div class="trx_addons_reviews_block_image"><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($trx_addons_meta['reviews_title']); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>></div><?php
			}
		}
		?><div class="trx_addons_reviews_block_info"><?php
			$trx_addons_reviews_max = trx_addons_get_option('reviews_mark_max');
			$trx_addons_reviews_decimals = trx_addons_get_option('reviews_mark_decimals');
			$trx_addons_reviews_mark = trx_addons_reviews_mark2show( $trx_addons_meta['reviews_mark'], $trx_addons_reviews_max );
			trx_addons_reviews_show_round( 'p0', array(
				'mark' => $trx_addons_reviews_mark,
				'mark_max' => $trx_addons_reviews_max,
				'mark_decimals' => $trx_addons_reviews_decimals,
				'mark_text' => ! empty( $trx_addons_meta['reviews_mark_text'] ) ? $trx_addons_meta['reviews_mark_text'] : '',
			) );
			if ( !empty($trx_addons_meta['reviews_attributes']) && count($trx_addons_meta['reviews_attributes']) > 0 && !empty($trx_addons_meta['reviews_attributes'][0]['title']) ) {
				?><div class="trx_addons_reviews_block_attributes"><?php
					foreach($trx_addons_meta['reviews_attributes'] as $attr) {
						if ( empty($attr['title']) && empty($attr['value']) ) continue;
						?><div class="trx_addons_reviews_block_attributes_row trx_addons_reviews_block_attributes_row_type_<?php echo esc_attr($attr['type']); ?>"><?php
							if ( !empty($attr['link']) ) {
								?><a href="<?php echo esc_url($attr['link']); ?>" class="trx_addons_reviews_block_attributes_<?php echo esc_attr($attr['type'] == 'text' ? 'link' : 'button sc_button theme_button'); ?>"><?php
							}
							if ( ! empty($attr['title']) ) {
								?><span class="trx_addons_reviews_block_attributes_title"><?php echo esc_html($attr['title']); ?></span><?php
							}
							if ( ! empty($attr['value']) ) {
								?><span class="trx_addons_reviews_block_attributes_value"><?php echo esc_html($attr['value']); ?></span><?php
							}
							if ( !empty($attr['link']) ) {
								?></a><?php
							}
						?></div><?php
					}
				?></div><?php
			}
		?></div><?php
	?></div><?php
}
