<?php
/**
 * Template of the custom slide content with Property's data for the Swiper
 *
 * @package ThemeREX Addons
 * @since v1.6.23
 */

$args = get_query_var('trx_addons_args_properties_slider_slide');

$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = get_permalink();

?><div class="sc_properties_slider_columns <?php echo esc_attr(trx_addons_get_columns_wrap_class()); ?>"><?php
	
	// First column with data
	?><div class="sc_properties_slider_column <?php echo esc_attr(trx_addons_get_column_class(2, 5)); ?>">
		<h4 class="sc_properties_slider_title"><?php the_title(); ?></h4>
		<ul class="sc_properties_slider_data trx_addons_list trx_addons_list_parameters"><?php
			if ( !empty($meta['id']) ) {
				?>
				<li class="sc_properties_slider_data_id">
					<strong><?php esc_html_e('Property ID', 'trx_addons'); ?></strong>
					<em><?php echo esc_html($meta['id']); ?></em>
				</li>
				<?php
			}
			if ( !empty($meta['bedrooms']) ) {
				?>
				<li class="sc_properties_slider_data_beds">
					<strong><?php esc_html_e('Bedrooms', 'trx_addons'); ?></strong>
					<em><?php echo esc_html($meta['bedrooms']); ?></em>
				</li>
				<?php
			}
			if ( !empty($meta['bathrooms']) ) {
				?>
				<li class="sc_properties_slider_data_baths">
					<strong><?php esc_html_e('Bathrooms', 'trx_addons'); ?></strong>
					<em><?php echo esc_html($meta['bathrooms']); ?></em>
				</li>
				<?php
			}
			if ( !empty($meta['area_size']) ) {
				?>
				<li class="sc_properties_slider_data_area">
					<strong><?php esc_html_e('Area size', 'trx_addons'); ?></strong>
					<em><?php trx_addons_show_layout($meta['area_size']
													. ($meta['area_size_prefix'] 
															? ' ' . trx_addons_prepare_macros($meta['area_size_prefix'])
															: ''
														));
					?></em>
				</li>
				<?php
			}
			if ( !empty($meta['garages']) ) {
				?>
				<li class="sc_properties_slider_data_garages">
					<strong><?php esc_html_e('Garages', 'trx_addons'); ?></strong>
					<em><?php trx_addons_show_layout($meta['garages']
													. ($meta['garage_size'] 
															? ' (' . trx_addons_prepare_macros($meta['garage_size']) . ')'
															: ''
														)
													);
					?></em>
				</li>
				<?php
			}
			if ( !empty($meta['price']) || !empty($meta['price2']) ) {
				?>
				<li class="sc_properties_slider_data_price">
					<strong><?php
						if (!empty($meta['before_price']))
							trx_addons_show_layout(trx_addons_prepare_macros($meta['before_price']));
						else
							esc_html_e('Price', 'trx_addons');
					?></strong>
					<em><?php
						if (!empty($meta['price']))
							echo esc_html(trx_addons_format_price($meta['price']));
						if (!empty($meta['price']) && !empty($meta['price2'])) {
							?><br><?php
						}
						if (!empty($meta['price2']))
							echo esc_html(trx_addons_format_price($meta['price2']));
						if (!empty($meta['after_price']))
							trx_addons_show_layout(' '.trx_addons_prepare_macros($meta['after_price']));
					?></em>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
		trx_addons_show_layout(trx_addons_sc_button(array(
										'link' => $link,
										'title' => esc_html__('Learn more', 'trx_addons'),
										'class' => esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_properties_slider_data_button', 'sc_properties', $args))
										)
								));
		?>
	</div><?php

	// Second column with image
	?><div class="sc_properties_slider_column <?php echo esc_attr(trx_addons_get_column_class(3, 5)); ?>">
		<figure class="sc_properties_slider_image">
			<?php the_post_thumbnail(trx_addons_get_thumb_size('big'), array('alt' => get_the_title())); ?>
		</figure>
	</div>
</div>