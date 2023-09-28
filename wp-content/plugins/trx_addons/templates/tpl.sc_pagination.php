<?php
/**
 * The template to display shortcode's pagination
 *
 * @package ThemeREX Addons
 * @since v1.6.42
 */

extract(get_query_var('trx_addons_args_sc_pagination'));

$max_page = ! empty( $query )
				? ( ! empty( $args['offset'] )
					? ( ceil( $query->found_posts - $args['offset'] ) / max( 1, $query->query_vars['posts_per_page'] ) )
					: ( ! empty($query->max_num_pages)
						? $query->max_num_pages
						: 1
						)
					)
				: 1;

if ( ! trx_addons_is_off($args['pagination']) && $max_page > 1 ) {

	$args['sc'] = $sc;
	
	$align = !empty($args['title_align']) ? ' sc_align_'.trim($args['title_align']) : '';
	
	// Old style: links 'Prev' & 'Next'
	if ($args['pagination'] == 'prev_next') {
		?><nav class="<?php echo esc_attr($sc); ?>_pagination sc_item_pagination sc_item_pagination_<?php echo esc_attr($args['pagination']); ?> nav-links-old <?php echo esc_attr($align); ?>" data-params="<?php echo esc_attr(serialize(apply_filters('trx_addons_filter_sc_args_to_serialize', $args))); ?>" role="navigation"><?php
			?><span class="nav-prev<?php if ($args['page'] == 1) echo ' nav-disabled'; ?>"><a href="#" data-page="<?php echo esc_attr($args['page'] - 1); ?>"><span class="nav-prev-label"><?php esc_html_e('Previous', 'trx_addons'); ?></span></a></span><?php
			?><span class="nav-next<?php if ($args['page'] >= $max_page) echo ' nav-disabled'; ?>"><a href="#" data-page="<?php echo esc_attr($args['page'] + 1); ?>"><span class="nav-next-label"><?php esc_html_e('Next', 'trx_addons'); ?></span></a></span><?php
		?></nav><?php
	
	// Page numbers
	} else if ( in_array($args['pagination'], array('pages', 'advanced_pages')) ) {
		?><nav class="<?php echo esc_attr($sc); ?>_pagination sc_item_pagination sc_item_pagination_<?php echo esc_attr($args['pagination']); ?> navigation pagination <?php echo esc_attr($align); ?>" data-params="<?php echo esc_attr(serialize(apply_filters('trx_addons_filter_sc_args_to_serialize', $args))); ?>" role="navigation">
			<div class="nav-links"><?php
				$total = 5;
				$start = max(1, $args['page'] - floor($total/2));
				$end = min($max_page, $start + $total - 1);
				if ($args['page'] > 1) {
					?><a href="#" class="page-numbers first" data-page="1" title="<?php esc_attr_e('First page', 'trx_addons'); ?>"><?php esc_html_e('First', 'trx_addons'); ?></a><?php
					?><a href="#" class="page-numbers prev" data-page="<?php echo esc_attr($args['page'] - 1); ?>" title="<?php esc_attr_e('Previous page', 'trx_addons'); ?>"><?php esc_html_e('Previous', 'trx_addons'); ?></a><?php
				}
				for ($i = $start; $i <= $end; $i++) {
					if ($i == $args['page']) {
						?><span class="page-numbers current"><?php echo esc_html($i); ?></span><?php
					} else {
						?><a href="#" class="page-numbers" data-page="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></a><?php
					}
				}
				if ($args['page'] < $max_page) {
					?><a href="#" class="page-numbers next" data-page="<?php echo esc_attr($args['page'] + 1); ?>" title="<?php esc_attr_e('Next page', 'trx_addons'); ?>"><?php esc_html_e('Next', 'trx_addons'); ?></a><?php
					?><a href="#" class="page-numbers last" data-page="<?php echo esc_attr($max_page); ?>" title="<?php esc_attr_e('Last page', 'trx_addons'); ?>"><?php esc_html_e('Last', 'trx_addons'); ?></a><?php
				}
			?></div><?php
			if ( $args['pagination'] == 'advanced_pages' ) {
				?><span class="page-numbers page-count"><?php echo sprintf(esc_html__('Page %d of %d', 'trx_addons'), $args['page'], $max_page); ?></span><?php
			}
		?></nav><?php

	// Load more
	} else if ( in_array( $args['pagination'], array('load_more', 'infinite') ) ) {
		if ($args['page'] < $max_page) {
			?><nav class="<?php echo esc_attr($sc); ?>_pagination sc_item_pagination sc_item_pagination_load_more nav-links-more <?php
				echo esc_attr($align);
				if ( $args['pagination'] == 'infinite' ) {
					echo ' sc_item_pagination_infinite';
				}
			?>" data-params="<?php echo esc_attr(serialize(apply_filters('trx_addons_filter_sc_args_to_serialize', $args))); ?>">
				<a class="nav-links" data-page="<?php echo esc_attr($args['page']+1); ?>" data-max-page="<?php echo esc_attr($max_page); ?>"><span><?php
					esc_html_e( 'Load more', 'trx_addons' );
				?></span></a>
			</nav><?php
		}
	}
}