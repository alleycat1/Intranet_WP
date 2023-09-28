<?php
/**
 * The style "chess" of the Services item
 *
 * @package ThemeREX Addons
 * @since v1.6.13
 */

$args = get_query_var('trx_addons_args_sc_services');
$number = get_query_var('trx_addons_args_item_number');
$meta = get_query_var('trx_addons_args_item_meta');
$link = empty($args['no_links'])
			? (!empty($meta['link']) ? $meta['link'] : get_permalink())
			: '';
$add_to_cart_link = ! empty( $meta['price'] ) 
						? apply_filters( 'trx_addons_filter_cpt_add_to_cart_link', '', 'sc_services_item_price_link', apply_filters( 'trx_addons_filter_cpt_add_to_cart_label', $meta['price'], 'services-tabs' ) )
						: '';
?>
<div data-post-id="<?php the_ID(); ?>" <?php post_class( apply_filters( 'trx_addons_filter_services_item_class',
			'sc_services_item sc_item_container post_container'
			. (empty($link) ? ' no_links' : '')
			. (!isset($args['hide_excerpt']) || (int)$args['hide_excerpt']==0 ? ' with_content' : ' without_content')
			. ($number-1 == $args['offset'] ? ' sc_services_item_active' : ''),
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
									apply_filters('trx_addons_filter_args_featured', array(
														'class' => 'sc_services_item_header',
														'show_no_image' => true,
														'no_links' => empty($link),
														'link' => $link,
														'thumb_bg' => true,
														'thumb_size' => ! empty( $args['thumb_size'] )
																			? $args['thumb_size']
																			: apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry-big'), 'services-tabs')
														),
													'services-tabs'
													)
								);
	do_action( 'trx_addons_action_services_item_after_featured', $args );
	?><div class="sc_services_item_content">
		<?php do_action( 'trx_addons_action_services_item_content_start', $args ); ?>
		<div class="sc_services_item_content_inner">
			<?php do_action( 'trx_addons_action_services_item_content_inner_start', $args ); ?>
			<h3 class="sc_services_item_title"><?php
				if (!empty($link)) {
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
			?></h3>
			<?php do_action( 'trx_addons_action_services_item_after_content_title', $args ); ?>
			<div class="sc_services_item_subtitle"><?php
				$terms = trx_addons_get_post_terms(', ', get_the_ID(), trx_addons_get_post_type_taxonomy());
				if (empty($link)) $terms = trx_addons_links_to_span($terms);
				trx_addons_show_layout($terms);
			?></div><?php
			do_action( 'trx_addons_action_services_item_after_content_subtitle', $args );
			if (!isset($args['hide_excerpt']) || (int)$args['hide_excerpt']==0) {
				do_action( 'trx_addons_action_services_item_before_text', $args );
				?><div class="sc_services_item_text"><?php the_excerpt(); ?></div><?php
				do_action( 'trx_addons_action_services_item_after_text', $args );
				if (!empty($link) && !empty($args['more_text'])) {
					?><div class="sc_services_item_button sc_item_button"><a href="<?php echo esc_url($link); ?>"<?php if (!empty($meta['link']) && trx_addons_is_external_url($meta['link'])) echo ' target="_blank"'; ?> class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button sc_button_simple', 'sc_services', $args)); ?>"><?php echo esc_html($args['more_text']); ?></a></div><?php
				}
			}
			do_action( 'trx_addons_action_services_item_content_inner_end', $args );
		?></div>
		<?php do_action( 'trx_addons_action_services_item_content_end', $args ); ?>
	</div>
</div>
