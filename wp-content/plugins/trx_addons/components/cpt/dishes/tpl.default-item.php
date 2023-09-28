<?php
/**
 * The style "default" of the Dishes
 *
 * @package ThemeREX Addons
 * @since v1.6.09
 */

$args = get_query_var('trx_addons_args_sc_dishes');

$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = get_permalink();
$featured_position = !empty($args['featured_position']) ? $args['featured_position'] : 'top';

if (!empty($args['slider'])) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
}
?>
<div data-post-id="<?php the_ID(); ?>" class="sc_dishes_item sc_item_container post_container with_image<?php
	if (isset($args['hide_excerpt']) && (int)$args['hide_excerpt'] > 0) echo ' without_content';
	echo ' sc_dishes_item_featured_'.esc_attr($featured_position);
	?>"<?php
	trx_addons_add_blog_animation('dishes', $args);
	if (!empty($args['popup'])) {
		?> data-post_id="<?php echo esc_attr(get_the_ID()); ?>"<?php
		?> data-post_type="<?php echo esc_attr(TRX_ADDONS_CPT_DISHES_PT); ?>"<?php
	}
	?>>
	<?php
	$spicy_showed = false;
	$price_showed = false;
	$add_to_cart_link = ! empty( $meta['price'] ) && ( empty( $meta['product'] ) || trx_addons_is_off( $meta['product'] ) ) 
							? apply_filters( 'trx_addons_filter_cpt_add_to_cart_link', '', 'sc_dishes_item_price_link', apply_filters( 'trx_addons_filter_cpt_add_to_cart_label', $meta['price'], 'dishes-default' ) )
							: '';
	// Featured image
	if ( has_post_thumbnail() && (empty($args['featured']) || $args['featured']=='image')) {
		?><div class="sc_dishes_item_image"><?php
			trx_addons_get_template_part('templates/tpl.featured.php',
										'trx_addons_args_featured',
										apply_filters('trx_addons_filter_args_featured', array(
														'class' => 'sc_dishes_item_thumb',
														'hover' => 'zoomin',
														'thumb_size' => apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size($args['columns'] > 2 ? 'medium' : 'big'), 'dishes-default')
														),
														'dishes-default'
													)
										);
			// Heat level
			if (trim($meta['spicy']) != '') {
				$meta['spicy'] = max(1, min(5, $meta['spicy']));
				?><span class="dishes_page_spicy dishes_page_spicy_<?php echo esc_html($meta['spicy']); ?>">
					<span class="dishes_page_spicy_label"><?php esc_html_e('Heat Level:', 'trx_addons'); ?></span>
					<span class="dishes_page_spicy_value"><?php echo esc_html($meta['spicy']); ?></span>
				</span><?php
			}
			$spicy_showed = true;
			// Price
			if (trim($meta['price']) != '') {
				?><div class="sc_dishes_item_price<?php echo ! empty( $add_to_cart_link ) ? ' sc_dishes_item_price_with_link' : ''; ?>"><?php
					trx_addons_show_layout( ! empty( $add_to_cart_link ) ? $add_to_cart_link : trim( $meta['price'] ) );
				?></div><?php
			}
			$price_showed = true;
		?></div><?php
	}
	?><div class="sc_dishes_item_info">
		<div class="sc_dishes_item_header">
			<?php
			// Heat level
			if ( ! $spicy_showed && trim($meta['spicy']) != '' ) {
				$meta['spicy'] = max(1, min(5, $meta['spicy']));
				?><span class="dishes_page_spicy dishes_page_spicy_<?php echo esc_html($meta['spicy']); ?>">
					<span class="dishes_page_spicy_label"><?php esc_html_e('Heat Level:', 'trx_addons'); ?></span>
					<span class="dishes_page_spicy_value"><?php echo esc_html($meta['spicy']); ?></span>
				</span><?php
			}
			?>
			<h4 class="sc_dishes_item_title entry-title"><a href="<?php echo esc_url($link); ?>"><?php the_title(); ?></a></h4>
			<div class="sc_dishes_item_subtitle"><?php trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_DISHES_TAXONOMY));?></div>
			<?php
			// Price
			if ( ! $price_showed && trim($meta['price']) != '' ) {
				?><div class="sc_dishes_item_price<?php echo ! empty( $add_to_cart_link ) ? ' sc_dishes_item_price_with_link' : ''; ?>"><?php
					trx_addons_show_layout( ! empty( $add_to_cart_link ) ? $add_to_cart_link : trim( $meta['price'] ) );
				?></div><?php
			}
			?>
		</div><?php
		if ( ! isset( $args['hide_excerpt'] ) || (int) $args['hide_excerpt'] == 0 ) {
			?><div class="sc_dishes_item_content"><?php the_excerpt(); ?></div><?php
			$add_to_cart_link = apply_filters( 'trx_addons_filter_cpt_add_to_cart_link', '', apply_filters( 'trx_addons_filter_sc_item_link_classes', 'sc_button sc_button_simple sc_dishes_button_order', 'sc_dishes', $args ) );
			if ( ! empty( $args['more_text'] ) || $meta['product'] > 0 || ! empty( $add_to_cart_link ) ) {
				?><div class="sc_dishes_item_button sc_item_button"><?php
					if ( ! empty( $args['more_text'] ) ) {
						?><a href="<?php echo esc_url($link); ?>" class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button sc_button_simple sc_dishes_button_more', 'sc_dishes', $args)); ?>"><?php echo esc_html($args['more_text']); ?></a><?php
					}
					if ( $meta['product'] > 0 ) {
						?><a href="<?php echo esc_url(get_permalink($meta['product'])); ?>" class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button sc_button_simple sc_dishes_button_order', 'sc_dishes', $args)); ?>"><?php esc_html_e('Order now', 'trx_addons'); ?></a><?php
					} else if ( ! empty( $add_to_cart_link ) ) {
						trx_addons_show_layout( $add_to_cart_link );
					}
				?></div><?php
			}
		}
	?></div>
</div><?php
if (!empty($args['slider']) || $args['columns'] > 1) {
	?></div><?php
}
