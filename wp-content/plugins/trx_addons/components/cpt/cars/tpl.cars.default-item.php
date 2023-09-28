<?php
/**
 * The style "default" of the Cars
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

$args = get_query_var('trx_addons_args_sc_cars');
if (empty($args['slider'])) $args['slider'] = 0;

$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = get_permalink();

if ($args['slider']) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
}
?><div data-post-id="<?php the_ID(); ?>" class="sc_cars_item sc_item_container post_container with_image<?php
	echo isset($args['hide_excerpt']) && (int)$args['hide_excerpt'] > 0 ? ' without_content' : ' with_content';
?>"<?php trx_addons_add_blog_animation('cars', $args); ?>>
	<?php
	// Featured image
	if ( has_post_thumbnail() ) {
		$price_block = trx_addons_get_template_part_as_string(
							TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.parts.price.php',
							'trx_addons_args_cars_price',
							array(
								'meta' => $meta
							)
						);
		$add_to_cart_icon = apply_filters( 'trx_addons_filter_cpt_add_to_cart_icon', '' );
		$add_to_cart_link = str_replace(
								array( 'cpt_to_cart_link', $add_to_cart_icon, 'class="cars_price"' ),
								array( '',                 '',                'class="cars_price' . ( ! empty( $add_to_cart_icon ) ? ' ' . $add_to_cart_icon : '' )  . '"' ),
								apply_filters(
									'trx_addons_filter_cpt_add_to_cart_link',
									'',
									'sc_cars_item_price_link',
									apply_filters( 'trx_addons_filter_cpt_add_to_cart_label', $price_block, 'cars-default' )
								)
							);
		trx_addons_get_template_part( 'templates/tpl.featured.php',
										'trx_addons_args_featured',
										apply_filters(
											'trx_addons_filter_args_featured',
											array(
												'class' => 'sc_cars_item_thumb',
												'hover' => 'zoomin',
												'thumb_size' => apply_filters('trx_addons_filter_thumb_size',
																				trx_addons_get_thumb_size( $args['columns'] == 1 ? 'avatar' : ( $args['columns'] > 2 ? 'medium' : 'big' ) ),
																				'cars-default', $args ),
												'show_no_image' => true,
												'post_info' => apply_filters(
																'trx_addons_filter_post_info',
																'<div class="sc_cars_item_labels">'
																	. trx_addons_get_post_terms('', get_the_ID(), TRX_ADDONS_CPT_CARS_TAXONOMY_LABELS)
																. '</div>'
																. '<div class="sc_cars_item_price' . ( ! empty( $add_to_cart_link ) ? ' sc_cars_item_price_with_link' : '' ) . '">'
																	. ( ! empty( $add_to_cart_link ) ? $add_to_cart_link : $price_block )
																. '</div>',
																'cars-default', $args )
											), 'cars-default', $args
										)
									);
	}
	?><div class="sc_cars_item_info">
		<div class="sc_cars_item_header"><?php
			// Title
			?><h5 class="sc_cars_item_title entry-title"><a href="<?php echo esc_url($link); ?>"><?php the_title(); ?></a></h5><?php
			// Type
			?><span class="sc_cars_item_type"><?php
				trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_CARS_TAXONOMY_TYPE));
			?></span><?php
			// Year built
			if (!empty($meta['produced'])) {
				?> <span class="sc_cars_item_produced"><?php trx_addons_show_layout($meta['produced']); ?></span><?php
			}
			// Add to Compare
			$list = urldecode(trx_addons_get_value_gpc('trx_addons_cars_compare_list', ''));
			$list = !empty($list) ? json_decode($list, true) : array();
			?><span class="sc_cars_item_compare trx_addons_icon-balance-scale<?php
					if (!empty($list['id_'.intval(get_the_ID())])) echo ' in_compare_list';
					?>" 
					data-car-id="<?php echo esc_attr(get_the_ID()); ?>" 
					title="<?php esc_attr_e('Add to compare list', 'trx_addons'); ?>"></span>
		</div>

		<div class="sc_cars_item_params"><?php
			// Mileage
			?><span class="sc_cars_item_param sc_cars_item_param_mileage" title="<?php esc_attr_e('Mileage', 'trx_addons'); ?>">
				<span class="sc_cars_item_param_icon trx_addons_icon-gauge"></span>
				<span class="sc_cars_item_param_text"><?php
					trx_addons_show_layout(trx_addons_num2kilo($meta['mileage'])
											. ($meta['mileage_prefix'] 
													? ' ' . trx_addons_prepare_macros($meta['mileage_prefix'])
													: '')
											);
				?></span>
			</span><?php
			// Engine
			?><span class="sc_cars_item_param sc_cars_item_param_engine" title="<?php esc_attr_e('Engine', 'trx_addons'); ?>">
				<span class="sc_cars_item_param_icon trx_addons_icon-cogs"></span>
				<span class="sc_cars_item_param_text"><?php
					trx_addons_show_layout($meta['engine_size']
											 . ($meta['engine_size_prefix'] 
														? ' ' . trx_addons_prepare_macros($meta['engine_size_prefix'])
														: '')
											 . ($meta['engine_type'] 
														? ' ' . trx_addons_prepare_macros($meta['engine_type'])
														: '')
											);
				?></span>
			</span><?php
			// Fuel
			?><span class="sc_cars_item_param sc_cars_item_param_fuel" title="<?php esc_attr_e('Fuel', 'trx_addons'); ?>">
				<span class="sc_cars_item_param_icon trx_addons_icon-fuel"></span>
				<span class="sc_cars_item_param_text"><?php
					trx_addons_show_layout(trx_addons_get_option_title(TRX_ADDONS_CPT_CARS_PT, 'fuel', $meta['fuel']));
				?></span>
			</span><?php
			// Transmission
			?><span class="sc_cars_item_param sc_cars_item_param_transmission" title="<?php esc_attr_e('Transmission', 'trx_addons'); ?>">
				<span class="sc_cars_item_param_icon trx_addons_icon-flow-tree"></span>
				<span class="sc_cars_item_param_text"><?php
					trx_addons_show_layout(trx_addons_get_option_title(TRX_ADDONS_CPT_CARS_PT, 'transmission', $meta['transmission']));
				?></span>
			</span><?php
		?></div>

		<div class="sc_cars_item_options sc_cars_item_footer"><?php

			// City
			?><span class="sc_cars_item_option sc_cars_item_address" title="<?php esc_attr_e('City', 'trx_addons'); ?>">
				<span class="sc_cars_item_option_label">
					<span class="sc_cars_item_option_label_icon trx_addons_icon-home"></span>
					<span class="sc_cars_item_option_label_text"><?php esc_html_e('City:', 'trx_addons'); ?></span>
				</span>
				<span class="sc_cars_item_option_data"><?php trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_CARS_TAXONOMY_CITY)); ?></span>
			</span><?php

			// Agent
			$trx_addons_agent = trx_addons_cars_get_agent_data($meta);
			if (!empty($trx_addons_agent['name'])) {
				?><span class="sc_cars_item_option sc_cars_item_author" title="<?php echo esc_attr($trx_addons_agent['title']); ?>">
					<span class="sc_cars_item_option_label">
						<span class="sc_cars_item_option_label_icon trx_addons_icon-user-alt"></span>
						<span class="sc_cars_item_option_label_text"><?php echo esc_html($trx_addons_agent['title']); ?></span>
					</span>
					<span class="sc_cars_item_option_data"><?php
						echo (!empty($trx_addons_agent['posts_link'])
								? '<a href="'.esc_url($trx_addons_agent['posts_link']).'">'
								: ''
								)
							. esc_html($trx_addons_agent['name'])
							. (!empty($trx_addons_agent['posts_link'])
								? '</a>'
								: ''
								);
					?></span>
				</span><?php
			}
				
			// Publish date
			?><span class="sc_cars_item_option sc_cars_item_date" title="<?php esc_attr_e('Publish date', 'trx_addons'); ?>">
				<span class="sc_cars_item_option_label">
					<span class="sc_cars_item_option_label_icon trx_addons_icon-calendar"></span>
					<span class="sc_cars_item_option_label_text"><?php esc_html_e('Added:', 'trx_addons'); ?></span>
				</span>
				<span class="sc_cars_item_option_data"><?php
					echo wp_kses_data(apply_filters('trx_addons_filter_get_post_date', date('d.m.y', get_the_date('U'))));
				?></span>
			</span><?php

			if (!empty($args['more_text'])) {
				?><div class="sc_cars_item_button sc_item_button"><a href="<?php echo esc_url($link); ?>" class="sc_button<?php
					if ($args['columns'] == 1) echo ' sc_button_simple';
					?>"><?php
						echo esc_html($args['more_text']);
					?></a>
				</div><?php
			}
		?></div>
	</div>
</div>
<?php
if ($args['slider'] || $args['columns'] > 1) {
	?></div><?php
}
