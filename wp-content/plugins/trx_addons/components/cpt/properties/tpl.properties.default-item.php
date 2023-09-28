<?php
/**
 * The style "default" of the Properties
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

$args = get_query_var('trx_addons_args_sc_properties');
if (empty($args['slider'])) $args['slider'] = 0;

$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
$link = get_permalink();

if ($args['slider']) {
	?><div class="slider-slide swiper-slide"><?php
} else if ($args['columns'] > 1) {
	?><div class="<?php echo esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')); ?>"><?php
}
?><div data-post-id="<?php the_ID(); ?>" class="sc_properties_item sc_item_container post_container with_image<?php
	echo isset($args['hide_excerpt']) && (int)$args['hide_excerpt'] > 0 ? ' without_content' : ' with_content';
?>"<?php trx_addons_add_blog_animation('properties', $args); ?>>
	<?php
	$price_block = trx_addons_get_template_part_as_string(
						TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.parts.price.php',
						'trx_addons_args_properties_price',
						array(
							'meta' => $meta
						)
					);
	$add_to_cart_icon = apply_filters( 'trx_addons_filter_cpt_add_to_cart_icon', '' );
	$add_to_cart_link = str_replace(
							array( 'cpt_to_cart_link', $add_to_cart_icon, 'class="properties_price"' ),
							array( '',                 '',                'class="properties_price' . ( ! empty( $add_to_cart_icon ) ? ' ' . $add_to_cart_icon : '' )  . '"' ),
							apply_filters(
								'trx_addons_filter_cpt_add_to_cart_link',
								'',
								'sc_properties_item_price_link',
								apply_filters( 'trx_addons_filter_cpt_add_to_cart_label', $price_block, 'properties-default' )
							)
						);
	// Featured image
	trx_addons_get_template_part('templates/tpl.featured.php',
										'trx_addons_args_featured',
										apply_filters( 'trx_addons_filter_args_featured', array(
															'class' => 'sc_properties_item_thumb',
															'hover' => 'zoomin',
															'thumb_size' => apply_filters('trx_addons_filter_thumb_size',
																							trx_addons_get_thumb_size( $args['columns'] == 1 ? 'avatar' : ( $args['columns'] > 2 ? 'medium' : 'big' ) ),
																							'properties-default', $args ),
															'show_no_image' => true,
															'post_info' => apply_filters('trx_addons_filter_post_info',
																			'<div class="sc_properties_item_labels">'
																				. trx_addons_get_post_terms('', get_the_ID(), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_LABELS)
																			. '</div>'
																			. '<div class="sc_properties_item_price' . ( ! empty( $add_to_cart_link ) ? ' sc_properties_item_price_with_link' : '' ) . '">'
																				. ( ! empty( $add_to_cart_link ) ? $add_to_cart_link : $price_block )
																			. '</div>',
																			'properties-default', $args )
															), 'properties-default', $args
														)
									);
	?><div class="sc_properties_item_info">
		<div class="sc_properties_item_header"><?php
			// Title
			?><h5 class="sc_properties_item_title entry-title"><a href="<?php echo esc_url($link); ?>"><?php the_title(); ?></a></h5><?php
			// Status
			?><div class="sc_properties_item_status"><?php
				trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS));
			?></div><?php
			// Type
			?><div class="sc_properties_item_type"><?php
				trx_addons_show_layout(trx_addons_get_post_terms(', ', get_the_ID(), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_TYPE));
			?></div><?php
			// Add to Compare
			$list = urldecode(trx_addons_get_value_gpc('trx_addons_properties_compare_list', ''));
			$list = !empty($list) ? json_decode($list, true) : array();
			?><span class="sc_properties_item_compare trx_addons_icon-balance-scale<?php
					if (!empty($list['id_'.intval(get_the_ID())])) echo ' in_compare_list';
					?>" 
					data-property-id="<?php echo esc_attr(get_the_ID()); ?>" 
					title="<?php esc_attr_e('Add to compare list', 'trx_addons'); ?>"></span>
		</div>
		<div class="sc_properties_item_options">
			<div class="sc_properties_item_row sc_properties_item_row_info"><?php
				// ID
				if (!empty($meta['id'])) {
					?><span class="sc_properties_item_option sc_properties_item_id" title="<?php esc_attr_e('Property ID', 'trx_addons'); ?>">
						<span class="sc_properties_item_option_label">
							<span class="sc_properties_item_option_label_icon trx_addons_icon-map"></span>
							<span class="sc_properties_item_option_label_text"><?php esc_html_e('ID:', 'trx_addons'); ?></span>
						</span>
						<span class="sc_properties_item_option_data"><?php trx_addons_show_layout($meta['id']); ?></span>
					</span><?php
				}
				// Year built
				if (!empty($meta['built'])) {
					?><span class="sc_properties_item_option sc_properties_item_built" title="<?php esc_attr_e('Year built', 'trx_addons'); ?>">
						<span class="sc_properties_item_option_label">
							<span class="sc_properties_item_option_label_icon trx_addons_icon-building"></span>
							<span class="sc_properties_item_option_label_text"><?php esc_html_e('Built:', 'trx_addons'); ?></span>
						</span>
						<span class="sc_properties_item_option_data"><?php trx_addons_show_layout($meta['built']); ?></span>
					</span><?php
				}
				// Area size
				if (!empty($meta['area_size'])) {
					?><span class="sc_properties_item_option sc_properties_item_area" title="<?php esc_attr_e('Area size', 'trx_addons'); ?>">
						<span class="sc_properties_item_option_label">
							<span class="sc_properties_item_option_label_icon trx_addons_icon-resize-full-alt"></span>
							<span class="sc_properties_item_option_label_text"><?php esc_html_e('Area:', 'trx_addons'); ?></span>
						</span>
						<span class="sc_properties_item_option_data"><?php
							trx_addons_show_layout($meta['area_size']
													. ($meta['area_size_prefix'] 
															? ' ' . trx_addons_prepare_macros($meta['area_size_prefix'])
															: ''
														)
													);
						?></span>
					</span><?php
				}
			?></div>
			<div class="sc_properties_item_row sc_properties_item_row_info"><?php
				// Bedrooms
				if (!empty($meta['bedrooms'])) {
					?><span class="sc_properties_item_option sc_properties_item_bedrooms" title="<?php esc_attr_e('Bedrooms number', 'trx_addons'); ?>">
						<span class="sc_properties_item_option_label">
							<span class="sc_properties_item_option_label_icon trx_addons_icon-bed"></span>
							<span class="sc_properties_item_option_label_text"><?php esc_html_e('Beds:', 'trx_addons'); ?></span>
						</span>
						<span class="sc_properties_item_option_data"><?php echo esc_html($meta['bedrooms']); ?></span>
					</span><?php
				}
				// Bathrooms
				if (!empty($meta['bathrooms'])) {
					?><span class="sc_properties_item_option sc_properties_item_bathrooms" title="<?php esc_attr_e('Bathrooms number', 'trx_addons'); ?>">
						<span class="sc_properties_item_option_label">
							<span class="sc_properties_item_option_label_icon trx_addons_icon-water"></span>
							<span class="sc_properties_item_option_label_text"><?php esc_html_e('Baths:', 'trx_addons'); ?></span>
						</span>
						<span class="sc_properties_item_option_data"><?php echo esc_html($meta['bathrooms']); ?></span>
					</span><?php
				}
				// Garages
				if (!empty($meta['garages'])) {
					?><span class="sc_properties_item_option sc_properties_item_garages" title="<?php esc_attr_e('Garages number and size', 'trx_addons'); ?>">
						<span class="sc_properties_item_option_label">
							<span class="sc_properties_item_option_label_icon trx_addons_icon-car"></span>
							<span class="sc_properties_item_option_label_text"><?php esc_html_e('Garages:', 'trx_addons'); ?></span>
						</span>
						<span class="sc_properties_item_option_data"><?php
							trx_addons_show_layout($meta['garages']
													. ($meta['garage_size'] 
															? ' (' . trx_addons_prepare_macros($meta['garage_size']) . ')'
															: ''
														)
													);
						?></span>
					</span><?php
				}
			?></div>
			<div class="sc_properties_item_row sc_properties_item_row_address"><?php
				// Address
				?><span class="sc_properties_item_option sc_properties_item_address" title="<?php esc_attr_e('Address', 'trx_addons'); ?>">
					<span class="sc_properties_item_option_label">
						<span class="sc_properties_item_option_label_icon trx_addons_icon-home"></span>
						<span class="sc_properties_item_option_label_text"><?php esc_html_e('Address:', 'trx_addons'); ?></span>
					</span>
					<span class="sc_properties_item_option_data"><?php 
						trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.parts.address.php',
													'trx_addons_args_properties_address',
													array(
														'meta' => array(
															'address' => $meta['address'],
															'neighborhood' => $meta['neighborhood'],
															'city' => $meta['city']
															)
													));
					?></span>
				</span><?php
			?></div>
			<div class="sc_properties_item_row sc_properties_item_row_meta"><?php
				// Agent
				if ($meta['agent_type']!='none' && ($meta['agent_type']=='author' || $meta['agent']!=0)) {
					$trx_addons_agent = trx_addons_properties_get_agent_data($meta);
					?><span class="sc_properties_item_option sc_properties_item_author" title="<?php echo esc_attr($trx_addons_agent['title']); ?>">
						<span class="sc_properties_item_option_label">
							<span class="sc_properties_item_option_label_icon trx_addons_icon-user-alt"></span>
							<span class="sc_properties_item_option_label_text"><?php echo esc_html($trx_addons_agent['title']); ?></span>
						</span>
						<span class="sc_properties_item_option_data"><?php
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
				?><span class="sc_properties_item_option sc_properties_item_date" title="<?php esc_attr_e('Publish date', 'trx_addons'); ?>">
					<span class="sc_properties_item_option_label">
						<span class="sc_properties_item_option_label_icon trx_addons_icon-calendar"></span>
						<span class="sc_properties_item_option_label_text"><?php esc_html_e('Added:', 'trx_addons'); ?></span>
					</span>
					<span class="sc_properties_item_option_data"><?php
						echo wp_kses_data(apply_filters('trx_addons_filter_get_post_date', date('d.m.y', get_the_date('U'))));
					?></span>
				</span><?php
			?></div><?php
			if (!empty($args['more_text'])) {
				?><div class="sc_properties_item_button sc_item_button"><a href="<?php echo esc_url($link); ?>" class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button', 'sc_properties', $args)); ?>"><?php echo esc_html($args['more_text']); ?></a></div><?php
			}
		?></div>
	</div>
</div>
<?php
if ($args['slider'] || $args['columns'] > 1) {
	?></div><?php
}
