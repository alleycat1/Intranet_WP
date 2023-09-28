<?php
/**
 * The style "chess" of the Services item
 *
 * @package ThemeREX Addons
 * @since v1.6.13
 */

$args = get_query_var('trx_addons_args_sc_services');

$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
if (!is_array($meta)) $meta = array();
$meta['price'] = apply_filters( 'trx_addons_filter_custom_meta_value', !empty($meta['price']) ? $meta['price'] : '', 'price' );

$link = empty($args['no_links'])
			? (!empty($meta['link']) ? $meta['link'] : get_permalink())
			: '';

$add_to_cart_link = ! empty( $meta['price'] ) 
						? apply_filters( 'trx_addons_filter_cpt_add_to_cart_link', '', 'sc_services_item_price_link', apply_filters( 'trx_addons_filter_cpt_add_to_cart_label', $meta['price'], 'services-chess' ) )
						: '';

if (!empty($args['slider'])) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?> "><?php
}
?>
<div data-post-id="<?php the_ID(); ?>" <?php post_class( apply_filters( 'trx_addons_filter_services_item_class',
			'sc_services_item sc_item_container post_container'
			. (empty($link) ? ' no_links' : '')
			. (isset($args['hide_excerpt']) && (int)$args['hide_excerpt']>0 ? ' without_content' : ' with_content'),
			$args )
			);
	trx_addons_add_blog_animation('services', $args);
	if (!empty($args['popup'])) {
		?> data-post_id="<?php echo esc_attr(get_the_ID()); ?>"<?php
		?> data-post_type="<?php echo esc_attr(TRX_ADDONS_CPT_SERVICES_PT); ?>"<?php
	}
?>><?php
	do_action( 'trx_addons_action_services_item_start', $args );
	trx_addons_get_template_part('templates/tpl.featured.php',
									'trx_addons_args_featured',
									apply_filters( 'trx_addons_filter_args_featured', array(
														'class' => 'sc_services_item_header',
														'show_no_image' => true,
														'no_links' => empty($link),
														'link' => $link,
														'thumb_bg' => true,
														'thumb_size' => ! empty( $args['thumb_size'] )
																			? $args['thumb_size']
																			: apply_filters( 'trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry-big'), 'services-chess', $args ),
														'post_info' => apply_filters('trx_addons_filter_post_info',
																		! empty( $link )
																			? '<a class="post_link sc_services_item_link" href="' . esc_url( $link ) . '"' . ( ! empty( $meta['link'] ) && trx_addons_is_external_url( $meta['link'] ) ? ' target="_blank"' : '') . '></a>'
																			: '',
																		'services-chess', $args )
													),
													'services-chess', $args
												)
								);
	do_action( 'trx_addons_action_services_item_after_featured', $args );
	?>
	<div class="sc_services_item_content">
		<?php
		do_action( 'trx_addons_action_services_item_content_start', $args );
		$title_tag = 'h6';
		if ($args['columns'] == 1) $title_tag = 'h4';
		?>
		<<?php echo esc_attr($title_tag); ?> class="sc_services_item_title<?php if (!empty($meta['price'])) echo ' with_price'; ?>"><?php
			if ( ! empty( $link ) ) {
				?><a href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?>><?php
			}
			the_title();
			// Price
			if ( ! empty( $meta['price'] ) ) {
				?><div class="sc_services_item_price"><?php
					trx_addons_show_layout( ! empty( $add_to_cart_link ) ? $add_to_cart_link : trim( $meta['price'] ) );
				?></div><?php
			}
			if (!empty($link)) {
				?></a><?php
			}
		?></<?php echo esc_attr($title_tag); ?>>
		<?php do_action( 'trx_addons_action_services_item_after_title', $args ); ?>
		<div class="sc_services_item_subtitle"><?php
			$terms = trx_addons_get_post_terms(', ', get_the_ID(), trx_addons_get_post_type_taxonomy());
			if (empty($link)) $terms = trx_addons_links_to_span($terms);
			trx_addons_show_layout($terms);
		?></div>
		<?php
		do_action( 'trx_addons_action_services_item_after_subtitle', $args );
		if (!isset($args['hide_excerpt']) || (int)$args['hide_excerpt']==0) {
			?>
			<div class="sc_services_item_text"><?php the_excerpt(); ?></div>
			<?php
			if (!empty($link) && !empty($args['more_text'])) {
				?><div class="sc_services_item_button sc_item_button"><a href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?> class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button sc_button_simple', 'sc_services', $args)); ?>"><?php echo esc_html($args['more_text']); ?></a></div><?php
			}
		}
		do_action( 'trx_addons_action_services_item_content_end', $args );
		?>
	</div>
</div>
<?php
if (!empty($args['slider']) || $args['columns'] > 1) {
	?></div><?php
}
