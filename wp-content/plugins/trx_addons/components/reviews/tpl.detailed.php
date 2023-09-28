<?php
/**
 * Detailed template to display the "post reviews" block on the single page
 *
 * @package ThemeREX Addons
 * @since v1.6.57
 */

$trx_addons_args = get_query_var('trx_addons_args_sc_reviews');
$gutenberg_preview = function_exists('trx_addons_gutenberg_is_preview') && trx_addons_gutenberg_is_preview() && !trx_addons_sc_stack_check('trx_sc_blogger');
$trx_addons_meta = $gutenberg_preview
						? array(
								'reviews_enable' => true,
								'reviews_mark' => 50,
								'reviews_title' => __('Review title', 'trx_addons'),
								'reviews_mark_text' => __('Mark title', 'trx_addons'),
								'reviews_summary' => __('Real data Review this post you will see in the frontend.', 'trx_addons'),
								)
						: get_post_meta( get_the_ID(), 'trx_addons_options', true );
if ( !empty($trx_addons_meta['reviews_enable']) && $trx_addons_meta['reviews_mark'] > 0 ) {
	?><div class="trx_addons_reviews_block trx_addons_reviews_block_detailed"><?php
		
		// Title
		if ( !empty($trx_addons_meta['reviews_title']) ) {
			?><h6 class="trx_addons_reviews_block_title"><?php echo esc_html($trx_addons_meta['reviews_title']); ?></h6><?php
		}

		// Mark and summary
		?><div class="trx_addons_reviews_block_info"><?php
			?><div class="trx_addons_reviews_block_mark_wrap"><?php
				$trx_addons_reviews_max = trx_addons_get_option('reviews_mark_max');
				$trx_addons_reviews_decimals = trx_addons_get_option('reviews_mark_decimals');
				$trx_addons_reviews_mark = trx_addons_reviews_mark2show( $trx_addons_meta['reviews_mark'], $trx_addons_reviews_max );
				trx_addons_reviews_show_round( 'p0', array(
					'mark' => $trx_addons_reviews_mark,
					'mark_max' => $trx_addons_reviews_max,
					'mark_decimals' => $trx_addons_reviews_decimals,
					'mark_text' => ! empty( $trx_addons_meta['reviews_mark_text'] ) ? $trx_addons_meta['reviews_mark_text'] : '',
				) );
			?></div><?php
			if ( !empty($trx_addons_meta['reviews_summary']) ) {
				?><div class="trx_addons_reviews_block_summary"><?php echo nl2br( wp_kses_data( $trx_addons_meta['reviews_summary'] ) ); ?></div><?php
			}
		?></div><?php

		// Pos & Neg
		if ( !empty($trx_addons_meta['reviews_positives']) || !empty($trx_addons_meta['reviews_negatives']) ) {
			?><div class="trx_addons_reviews_block_pn"><?php
				// Positive
				?><div class="trx_addons_reviews_block_positives">
					<p class="trx_addons_reviews_block_subtitle"><?php esc_html_e('Positives', 'trx_addons'); ?></p>
					<?php
					if (!empty($trx_addons_meta['reviews_positives'])) {
						$items = explode( "\n", str_replace("\r", '', $trx_addons_meta['reviews_positives']) );
						if (count($items) > 0) {
							?><ul class="trx_addons_reviews_block_list"><?php
							foreach($items as $item) {
								$item = trim($item);
								if (empty($item)) continue;
								?><li><?php echo esc_html($item); ?></li><?php
							}
						}
					}
				?></div><?php
				// Negative
				?><div class="trx_addons_reviews_block_negatives">
					<p class="trx_addons_reviews_block_subtitle"><?php esc_html_e('Negatives', 'trx_addons'); ?></p>
					<?php
					if (!empty($trx_addons_meta['reviews_negatives'])) {
						$items = explode( "\n", str_replace("\r", '', $trx_addons_meta['reviews_negatives']) );
						if (count($items) > 0) {
							?><ul class="trx_addons_reviews_block_list"><?php
							foreach($items as $item) {
								$item = trim($item);
								if (empty($item)) continue;
								?><li><?php echo esc_html($item); ?></li><?php
							}
							?></ul><?php
						}
					}
				?></div>
			</div><?php
		}

		// Criterias
		if ( !empty($trx_addons_meta['reviews_criterias']) && count($trx_addons_meta['reviews_criterias']) > 0 && $trx_addons_meta['reviews_criterias'][0]['mark'] > 0 ) {
			?><div class="trx_addons_reviews_block_criterias" data-mark-max="<?php echo esc_attr($trx_addons_reviews_max); ?>">
				<p class="trx_addons_reviews_block_subtitle"><?php esc_html_e('Breakdown', 'trx_addons'); ?></p>
				<ul class="trx_addons_reviews_block_list">
					<?php
					foreach($trx_addons_meta['reviews_criterias'] as $item) {
						if (empty($item['title']) || empty($item['mark'])) continue;
						$trx_addons_reviews_mark = trx_addons_reviews_mark2show( $item['mark'], $trx_addons_reviews_max );
						?><li>
							<span class="trx_addons_reviews_block_list_title"><?php echo esc_html($item['title']); ?></span>
							<?php
							if ( $trx_addons_reviews_max == 5 ) {
								?><span class="trx_addons_reviews_block_list_mark"><?php
									trx_addons_reviews_show_stars( 'p'.get_the_ID(), array(
										'mark' => $trx_addons_reviews_mark,
										'mark_max' => $trx_addons_reviews_max
									));
								?></span><?php
							} else {
								?>
								<span class="trx_addons_reviews_block_list_mark">
									<span class="trx_addons_reviews_block_list_mark_value"><?php echo esc_html($trx_addons_reviews_mark); ?></span>
									<span class="trx_addons_reviews_block_list_mark_line"></span>
									<span class="trx_addons_reviews_block_list_mark_line_hover" style="width:<?php echo esc_attr($item['mark']); ?>%;"></span>
								</span>
								<?php
							}
						?></li><?php
					}
					?>
				</ul>
			</div><?php
		}

		// Button
		if ( !empty($trx_addons_meta['reviews_link']) && !empty($trx_addons_meta['reviews_link_caption']) ) {
			?><div class="trx_addons_reviews_block_buttons"><?php
				if ( !empty($trx_addons_meta['reviews_link_title']) ) {
					?><p class="trx_addons_reviews_block_subtitle"><?php echo esc_html($trx_addons_meta['reviews_link_title']); ?></p><?php
				}
				?><a href="<?php echo esc_url($trx_addons_meta['reviews_link']); ?>" class="trx_addons_reviews_block_button sc_button theme_button"><?php echo esc_html($trx_addons_meta['reviews_link_caption']); ?></a>
			</div><?php
		}

	?></div><?php
}
