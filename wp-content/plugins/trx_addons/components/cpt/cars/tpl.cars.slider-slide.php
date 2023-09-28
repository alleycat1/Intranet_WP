<?php
/**
 * Template of the custom slide content with Car's data for the Swiper
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

$args = get_query_var('trx_addons_args_cars_slider_slide');

$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = get_permalink();

?><div class="sc_cars_slider_columns <?php echo esc_attr(trx_addons_get_columns_wrap_class()); ?>"><?php
	
	// First column with data
	?><div class="sc_cars_slider_column <?php echo esc_attr(trx_addons_get_column_class(2, 5)); ?>">
		<h4 class="sc_cars_slider_title"><?php the_title(); ?></h4>
		<ul class="sc_cars_slider_data trx_addons_list trx_addons_list_parameters"><?php
			if (!empty($meta['id'])) {
				?><li class="sc_cars_slider_data_id">
					<strong><?php esc_html_e('Car ID', 'trx_addons'); ?></strong>
					<em><?php echo esc_html($meta['id']); ?></em>
				</li><?php
			}
			if (!empty($meta['produced'])) {
				?><li class="sc_cars_slider_data_year">
					<strong><?php esc_html_e('Produced', 'trx_addons'); ?></strong>
					<em><?php echo esc_html($meta['produced']); ?></em>
				</li><?php
			}
			if (!empty($meta['mileage'])) {
				?><li class="sc_cars_slider_data_year">
					<strong><?php esc_html_e('Mileage', 'trx_addons'); ?></strong>
					<em><?php 
						echo esc_html(trx_addons_num2kilo($meta['mileage'])
									. ($meta['mileage_prefix'] 
											? ' ' . trx_addons_prepare_macros($meta['mileage_prefix'])
											: '')
									);
					?></em>
				</li><?php
			}
			if (!empty($meta['engine_size']) || !empty($meta['engine_type'])) {
				?><li class="sc_cars_slider_data_engine">
					<strong><?php esc_html_e('Engine', 'trx_addons'); ?></strong>
					<em><?php
						echo esc_html($meta['engine_size']
									 . ($meta['engine_size_prefix'] 
												? ' ' . trx_addons_prepare_macros($meta['engine_size_prefix'])
												: '')
									 . ($meta['engine_type'] 
												? ' ' . trx_addons_prepare_macros($meta['engine_type'])
												: '')
									);
					?></em>
				</li><?php
			}
			if (!empty($meta['fuel'])) {
				?><li class="sc_cars_slider_data_fuel">
					<strong><?php esc_html_e('Fuel', 'trx_addons'); ?></strong>
					<em><?php echo esc_html(trx_addons_get_option_title(TRX_ADDONS_CPT_CARS_PT, 'fuel', $meta['fuel'])); ?></em>
				</li><?php
			}
			if (!empty($meta['transmission'])) {
				?><li class="sc_cars_slider_data_fuel">
					<strong><?php esc_html_e('Transmission', 'trx_addons'); ?></strong>
					<em><?php echo esc_html(trx_addons_get_option_title(TRX_ADDONS_CPT_CARS_PT, 'transmission', $meta['transmission'])); ?></em>
				</li><?php
			}
			if (!empty($meta['price']) || !empty($meta['price2'])) {
				?><li class="sc_cars_slider_data_price">
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
				</li><?php
			}
		?></ul><?php
		if (!empty($args['more_text'])) {
			trx_addons_show_layout(trx_addons_sc_button(array(
										'link' => $link,
										'title' => $args['more_text'],
										'class' => esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_cars_slider_data_button', 'sc_cars', $args))
										)
								));
		}
		?>
	</div><?php

	// Second column with image
	?><div class="sc_cars_slider_column <?php echo esc_attr(trx_addons_get_column_class(3, 5)); ?>">
		<figure class="sc_cars_slider_image"><?php
			the_post_thumbnail(
							apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('big'), 'cars-slide'),
							array('alt' => get_the_title())
							);
		?></figure>
	</div>
</div>