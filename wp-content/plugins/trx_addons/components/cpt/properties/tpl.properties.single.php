<?php
/**
 * The template to display the property's single page
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

wp_enqueue_script('jquery-ui-accordion', false, array('jquery', 'jquery-ui-core'), null, true);
if (trx_addons_get_option('properties_single_style') == 'tabs')
	wp_enqueue_script('jquery-ui-tabs', false, array('jquery', 'jquery-ui-core'), null, true);

get_header();

while ( have_posts() ) { the_post();
	$trx_addons_meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
	
	do_action( 'trx_addons_action_before_article', 'properties.single' );
	
	?>
	<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>" <?php post_class( 'properties_page itemscope' ); trx_addons_seo_snippets('', 'Article'); ?>>

		<?php do_action( 'trx_addons_action_article_start', 'properties.single' ); ?>
		
		<section id="properties_page_header" class="properties_page_section properties_page_header"><?php
			// Image
			if ( ! trx_addons_sc_layouts_showed('featured') && has_post_thumbnail() ) {
				?><div class="properties_page_featured"><?php
					the_post_thumbnail(
										apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('huge'), 'properties-single'),
										trx_addons_seo_image_params(array(
																		'alt' => get_the_title()
																		))
										);
				?></div><?php
				if ( ! empty($trx_addons_meta['gallery'])) {
					$trx_addons_gallery = explode('|', $trx_addons_meta['gallery']);
					if (is_array($trx_addons_gallery)) {
						?><div class="properties_page_gallery"><?php
							array_unshift($trx_addons_gallery, get_post_thumbnail_id($id));
							$i = 0;
							foreach($trx_addons_gallery as $trx_addons_image) {
								$i++;
								if ($trx_addons_image != '') {
									$trx_addons_thumb = trx_addons_get_attachment_url($trx_addons_image, trx_addons_get_thumb_size('tiny'));
									$trx_addons_image = trx_addons_get_attachment_url($trx_addons_image, apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('huge'), 'properties-single'));
									if (!empty($trx_addons_thumb)) {
										$attr = trx_addons_getimagesize($trx_addons_thumb);
										?><span class="properties_page_gallery_item<?php if ($i==1) echo " properties_page_gallery_item_active"; ?>" data-image="<?php echo esc_url($trx_addons_image); ?>"><?php
											?><img src="<?php echo esc_url($trx_addons_thumb); ?>" alt="<?php esc_attr_e('Gallery item', 'trx_addons'); ?>"<?php
												if (!empty($attr[3])) echo ' '.trim($attr[3]);
											?>><?php
										?></span><?php
									}
								}
							}
						?></div><?php
					}
				}
			}
			
			// Title
			if ( true || !trx_addons_sc_layouts_showed('title') ) {
				?><div id="properties_page_title" class="properties_page_title_wrap">
					<h2 class="properties_page_title">
						<?php the_title(); ?>
						<span class="properties_page_status"><?php
							trx_addons_show_layout(trx_addons_get_post_terms('', get_the_ID(), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_STATUS));
						?></span>
					</h2>
					<?php
					// Address
					if (!empty($trx_addons_meta['address'])) {
						?><div class="properties_page_title_address"><?php
							trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.parts.address.php',
															'trx_addons_args_properties_address',
															array('meta' => $trx_addons_meta)
														);
						?></div><?php
					}
					// Meta
					?><div class="properties_page_title_meta"><?php
						// Price
						if (!empty($trx_addons_meta['price']) || !empty($trx_addons_meta['price2'])) {
							$price_block = trx_addons_get_template_part_as_string(
												TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.parts.price.php',
												'trx_addons_args_properties_price',
												array(
													'meta' => $trx_addons_meta
												)
											);
							$add_to_cart_icon = apply_filters( 'trx_addons_filter_cpt_add_to_cart_icon', '' );
							$add_to_cart_link = str_replace(
													array( 'cpt_to_cart_link', $add_to_cart_icon ),
													array( '', '' ),
													apply_filters(
														'trx_addons_filter_cpt_add_to_cart_link',
														'',
														'sc_properties_item_price_link',
														apply_filters( 'trx_addons_filter_cpt_add_to_cart_label', $price_block, 'properties-single' )
													)
												);
							?><div class="properties_page_title_price"><?php
								trx_addons_show_layout( ! empty( $add_to_cart_link ) ? $add_to_cart_link : $price_block );
							?></div><?php
						}
						// Meta
						trx_addons_sc_show_post_meta('properties_single', apply_filters('trx_addons_filter_post_meta_args', array(
									'components' => 'views,comments,likes,share',
									'seo' => false
									), 'properties_single', 1)
								);
					?></div><?php
					trx_addons_sc_layouts_showed('postmeta', true);
				?></div><?php
			}
		?></section><?php

		
		// Section's titles
		$trx_addons_section_titles = apply_filters( 'trx_addons_filter_single_property_tabs', array(
			'description' => __('Description', 'trx_addons'),
			'details' => __('Details', 'trx_addons'),
			'features' => __('Features', 'trx_addons'),
			'floor_plans' => __('Floor plans', 'trx_addons'),
			'attachments' => __('Attachments', 'trx_addons'),
			'video' => __('Video', 'trx_addons'),
			'virtual_tour' => __('Virtual tour', 'trx_addons'),
			'contacts' => __('Contacts', 'trx_addons'),
			'map' => __('Map', 'trx_addons'),
		), $trx_addons_meta );
		$trx_addons_tabs_id = 'properties_page_tabs';

		// Tabs
		if ( trx_addons_get_option('properties_single_style') == 'tabs' ) {
			if ( empty($trx_addons_meta['floor_plans_enable'])
				|| empty($trx_addons_meta['floor_plans']) 
				|| !is_array($trx_addons_meta['floor_plans'])
				|| empty($trx_addons_meta['floor_plans'][0]['title'])
			) {
				unset($trx_addons_section_titles['floor_plans']);
			}
			if ( empty($trx_addons_meta['attachments']) ) {
				unset($trx_addons_section_titles['attachments']);
			}
			if ( empty($trx_addons_meta['video']) ) {
				unset($trx_addons_section_titles['video']);
			}
			if ( empty($trx_addons_meta['virtual_tour']) ) {
				unset($trx_addons_section_titles['virtual_tour']);
			}
			if ( empty($trx_addons_meta['show_map'])
				|| empty($trx_addons_meta['location'])
			) {
				unset($trx_addons_section_titles['map']);
			}
			if ( empty($trx_addons_meta['agent_type']) 
				|| $trx_addons_meta['agent_type']=='none' 
				|| ($trx_addons_meta['agent_type']!='author' && $trx_addons_meta['agent']==0)
			) {
				unset($trx_addons_section_titles['contacts']);
			}
			?><div class="trx_addons_tabs properties_page_tabs"><?php

				do_action( 'trx_addons_action_before_single_property_tabs', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

				?><ul class="trx_addons_tabs_titles"><?php
					foreach ($trx_addons_section_titles as $trx_addons_section_slug => $trx_addons_section_title) {
						$trx_addons_tab_id = $trx_addons_tabs_id.'_'.$trx_addons_section_slug;
						$trx_addons_tab_active = trx_addons_get_value_gp('tab')==$trx_addons_section_slug
										? ' data-active="true"' 
										: '';
						?><li<?php
							if (trx_addons_get_value_gp('tab')==$trx_addons_section_slug)
								echo ' data-active="true"';
							?>><a href="<?php echo esc_url(trx_addons_get_hash_link('#'.$trx_addons_tab_id.'_content')); ?>"><?php
								echo esc_html($trx_addons_section_title);
							?></a></li><?php
					}
				?></ul><?php

				do_action( 'trx_addons_action_after_single_property_tabs', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );
		}


		do_action( 'trx_addons_action_before_single_property_section', 'all', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		do_action( 'trx_addons_action_before_single_property_section', 'description', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		// Post content
		if ( trim( get_the_content() ) != '' || trx_addons_is_preview( 'elementor' ) ) {
			?><section id="<?php echo esc_attr($trx_addons_tabs_id.'_description'); ?>_content" class="properties_page_section properties_page_content entry-content"<?php trx_addons_seo_snippets('articleBody'); ?>><?php
				if (trx_addons_get_option('properties_single_style') == 'tabs') {
					?><h4 class="properties_page_section_title"><?php echo esc_html($trx_addons_section_titles['description']); ?></h4><?php
				}
				the_content( );
			?></section><?php
		}

		do_action( 'trx_addons_action_after_single_property_section', 'description', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		do_action( 'trx_addons_action_before_single_property_section', 'details', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		// Details
		?><section id="<?php echo esc_attr($trx_addons_tabs_id.'_details'); ?>_content" class="properties_page_section properties_page_details">
			<h4 class="properties_page_section_title"><?php echo esc_html($trx_addons_section_titles['details']); ?></h4>
			<?php
			// ID
			if (!empty($trx_addons_meta['id'])) {
				?><span class="properties_page_section_item">
					<span class="properties_page_label"><?php esc_html_e('Property ID:', 'trx_addons'); ?></span>
					<span class="properties_page_data"><?php trx_addons_show_layout($trx_addons_meta['id']); ?></span>
				</span><?php
			}
			// Area size
			if (!empty($trx_addons_meta['area_size'])) {
				?><span class="properties_page_section_item">
					<span class="properties_page_label"><?php esc_html_e('Area size:', 'trx_addons'); ?></span>
					<span class="properties_page_data"><?php
						trx_addons_show_layout($trx_addons_meta['area_size']
												. ($trx_addons_meta['area_size_prefix'] 
														? ' ' . trx_addons_prepare_macros($trx_addons_meta['area_size_prefix'])
														: ''
													)
												);
					?></span>
				</span><?php
			}
			// Land size
			if (!empty($trx_addons_meta['land_size'])) {
				?><span class="properties_page_section_item">
					<span class="properties_page_label"><?php esc_html_e('Land size:', 'trx_addons'); ?></span>
					<span class="properties_page_data"><?php
						trx_addons_show_layout($trx_addons_meta['land_size']
												. ($trx_addons_meta['land_size_prefix'] 
														? ' ' . trx_addons_prepare_macros($trx_addons_meta['land_size_prefix'])
														: ''
													)
												);
					?></span>
				</span><?php
			}
			// Bedrooms
			if (!empty($trx_addons_meta['bedrooms'])) {
				?><span class="properties_page_section_item">
					<span class="properties_page_label"><?php esc_html_e('Bedrooms:', 'trx_addons'); ?></span>
					<span class="properties_page_data"><?php echo esc_html($trx_addons_meta['bedrooms']); ?></span>
				</span><?php
			}
			// Bathrooms
			if (!empty($trx_addons_meta['bathrooms'])) {
				?><span class="properties_page_section_item">
					<span class="properties_page_label"><?php esc_html_e('Bathrooms:', 'trx_addons'); ?></span>
					<span class="properties_page_data"><?php echo esc_html($trx_addons_meta['bathrooms']); ?></span>
				</span><?php
			}
			// Garages
			if (!empty($trx_addons_meta['garages'])) {
				?><span class="properties_page_section_item">
					<span class="properties_page_label"><?php esc_html_e('Garages:', 'trx_addons'); ?></span>
					<span class="properties_page_data"><?php
						trx_addons_show_layout($trx_addons_meta['garages']
												. ($trx_addons_meta['garage_size'] 
														? ' (' . trx_addons_prepare_macros($trx_addons_meta['garage_size']) . ')'
														: ''
													)
												);
					?></span>
				</span><?php
			}
			// Year built
			if (!empty($trx_addons_meta['built'])) {
				?><span class="properties_page_section_item">
					<span class="properties_page_label"><?php esc_html_e('Year built:', 'trx_addons'); ?></span>
					<span class="properties_page_data"><?php trx_addons_show_layout($trx_addons_meta['built']); ?></span>
				</span><?php
			}
			// Additional details
			if (!empty($trx_addons_meta['details_enable']) && !empty($trx_addons_meta['details']) && is_array($trx_addons_meta['details'])) {
				foreach ($trx_addons_meta['details'] as $detail) {
					if (!empty($detail['title'])) {
						?><span class="properties_page_section_item">
							<span class="properties_page_label"><?php
								trx_addons_show_layout(trx_addons_prepare_macros($detail['title'])); 
							?>:</span>
							<span class="properties_page_data"><?php 
								trx_addons_show_layout(trx_addons_prepare_macros($detail['value'])); 
							?></span>
						</span><?php
					}
				}
			}
		?></section><?php

		do_action( 'trx_addons_action_after_single_property_section', 'details', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		do_action( 'trx_addons_action_before_single_property_section', 'features', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		// Features
		?><section id="<?php echo esc_attr($trx_addons_tabs_id.'_features'); ?>_content" class="properties_page_section properties_page_features">
			<h4 class="properties_page_section_title"><?php echo esc_html($trx_addons_section_titles['features']); ?></h4>
			<div class="properties_page_features_list">
				<?php trx_addons_show_layout(trx_addons_get_post_terms('', get_the_ID(), TRX_ADDONS_CPT_PROPERTIES_TAXONOMY_FEATURES)); ?>
			</div>
		</section><?php

		do_action( 'trx_addons_action_after_single_property_section', 'features', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		do_action( 'trx_addons_action_before_single_property_section', 'floor_plans', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		// Floor plans
		if (!empty($trx_addons_meta['floor_plans_enable']) && !empty($trx_addons_meta['floor_plans']) 
			&& is_array($trx_addons_meta['floor_plans']) && !empty($trx_addons_meta['floor_plans'][0]['title'])) {
			?><section id="<?php echo esc_attr($trx_addons_tabs_id.'_floor_plans'); ?>_content" class="properties_page_section properties_page_floor_plans">
				<h4 class="properties_page_section_title"><?php echo esc_html($trx_addons_section_titles['floor_plans']); ?></h4>
				<div class="properties_page_floor_plans_list trx_addons_accordion" data-collapsible="true"><?php
					foreach ($trx_addons_meta['floor_plans'] as $plan) {
						?><div class="properties_page_floor_plans_list_item">
							<h5 class="properties_page_floor_plans_list_item_title"><?php
								// Plan's title
								?><span class="properties_page_floor_plans_list_item_title_part">
									<span class="properties_page_data"><?php echo esc_html($plan['title']); ?></span>
								</span><?php
								// Floor size
								if (!empty($plan['area'])) {
									?><span class="properties_page_floor_plans_list_item_title_part">
										<span class="properties_page_label"><?php esc_html_e('Size:', 'trx_addons'); ?></span>
										<span class="properties_page_data"><?php echo esc_html($plan['area']); ?></span>
									</span><?php
								}
								// Bedrooms
								if (!empty($plan['bedrooms'])) {
									?><span class="properties_page_floor_plans_list_item_title_part">
										<span class="properties_page_label"><?php esc_html_e('Bedrooms:', 'trx_addons'); ?></span>
										<span class="properties_page_data"><?php echo esc_html($plan['bedrooms']); ?></span>
									</span><?php
								}
								// Bathrooms
								if (!empty($plan['bathrooms'])) {
									?><span class="properties_page_floor_plans_list_item_title_part">
										<span class="properties_page_label"><?php esc_html_e('Bathrooms:', 'trx_addons'); ?></span>
										<span class="properties_page_data"><?php echo esc_html($plan['bathrooms']); ?></span>
									</span><?php
								}
							?></h5>
							<div class="properties_page_floor_plans_list_item_content"><?php
								// Image
								if (!empty($plan['image'])) {
									$trx_addons_image = trx_addons_get_attachment_url($plan['image'], trx_addons_get_thumb_size('huge'));
									if (!empty($trx_addons_image)) {
										?><div class="properties_page_floor_plans_list_item_image"><?php
											$attr = trx_addons_getimagesize($trx_addons_image);
											?><img src="<?php echo esc_url($trx_addons_image); ?>" alt="<?php esc_attr_e('Floor plan', 'trx_addons'); ?>"<?php
												if (!empty($attr[3])) echo ' '.trim($attr[3]);
											?>><?php
										?></div><?php
									}
								}
								// Description
								if (!empty($plan['description'])) {
									?><div class="properties_page_floor_plans_list_item_description"><?php
										echo wp_kses($plan['description'], 'trx_addons_kses_content');
									?></div><?php
								}
							?></div>
						</div><?php
					}
				?></div>
			</section><?php
		}

		do_action( 'trx_addons_action_after_single_property_section', 'floor_plans', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		// Attachments
		if ( ! empty( $trx_addons_meta['attachments'] ) ) {
			$trx_addons_meta['attachments'] = explode('|', $trx_addons_meta['attachments']);
			if ( is_array( $trx_addons_meta['attachments']) && count( $trx_addons_meta['attachments'] ) > 0 ) {
		
				do_action( 'trx_addons_action_before_single_property_section', 'attachments', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );
		
				?><section id="<?php echo esc_attr($trx_addons_tabs_id.'_attachments'); ?>_content" class="properties_page_section properties_page_attachments">
					<h4 class="properties_page_section_title"><?php echo esc_html($trx_addons_section_titles['attachments']); ?></h4><?php
					if (!empty($trx_addons_meta['attachments_description'])) {
						?><div class="properties_page_section_description"><?php
							echo wp_kses(nl2br($trx_addons_meta['attachments_description']), 'trx_addons_kses_content');
						?></div><?php
					}
					?><div class="properties_page_attachments_list"><?php
						foreach ($trx_addons_meta['attachments'] as $file) {
							?><a href="<?php echo esc_url($file); ?>" target="_blank" download="<?php echo(esc_attr(basename($file))); ?>"><?php echo esc_html(basename($file));	?></a><?php
						}
					?></div>
				</section><?php

				do_action( 'trx_addons_action_after_single_property_section', 'attachments', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );
			}
		}


		// Video promo
		if ( ! empty( $trx_addons_meta['video'] ) ) {

			do_action( 'trx_addons_action_before_single_property_section', 'video', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

			?><section id="<?php echo esc_attr($trx_addons_tabs_id.'_video'); ?>_content" class="properties_page_section properties_page_video">
				<h4 class="properties_page_section_title"><?php echo esc_html($trx_addons_section_titles['video']); ?></h4><?php
					if (!empty($trx_addons_meta['video_description'])) {
						?><div class="properties_page_section_description"><?php
							echo wp_kses(nl2br($trx_addons_meta['video_description']), 'trx_addons_kses_content');
						?></div><?php
					}
				?><div class="properties_page_video_wrap"><?php
					trx_addons_show_layout( trx_addons_get_video_layout( apply_filters( 'trx_addons_filter_get_video_layout_args', array(
																			'link' => $trx_addons_meta['video']
																		), 'properties.single' ) ) );
				?></div>
			</section><?php

			do_action( 'trx_addons_action_after_single_property_section', 'video', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		}


		// Virtual tour
		if ( ! empty( $trx_addons_meta['virtual_tour'] ) ) {

			do_action( 'trx_addons_action_before_single_property_section', 'virtual_tour', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

			?><section id="<?php echo esc_attr($trx_addons_tabs_id.'_virtual_tour'); ?>_content" class="properties_page_section properties_page_virtual_tour">
				<h4 class="properties_page_section_title"><?php echo esc_html($trx_addons_section_titles['virtual_tour']); ?></h4><?php
					if (!empty($trx_addons_meta['virtual_tour_description'])) {
						?><div class="properties_page_section_description"><?php
							echo wp_kses(nl2br($trx_addons_meta['virtual_tour_description']), 'trx_addons_kses_content');
						?></div><?php
					}
				?><div class="properties_page_virtual_tour_wrap"><?php
					if (strpos($trx_addons_meta['virtual_tour'], '<')===false) {
						?><iframe src="<?php
							echo esc_url($trx_addons_meta['virtual_tour']);
							?>" width="1170" height="658"></iframe><?php
					} else
						trx_addons_show_layout($trx_addons_meta['virtual_tour']);
				?></div>
			</section><?php

			do_action( 'trx_addons_action_after_single_property_section', 'virtual_tour', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		}


		// Map
		if ( ! empty( $trx_addons_meta['show_map'] ) && ! empty( $trx_addons_meta['location'] ) ) {

			do_action( 'trx_addons_action_before_single_property_section', 'map', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

			?><section id="<?php echo esc_attr($trx_addons_tabs_id.'_map'); ?>_content" class="properties_page_section properties_page_map">
				<h4 class="properties_page_section_title"><?php echo esc_html($trx_addons_section_titles['map']); ?></h4><?php
				?><div class="properties_page_map"><?php
					trx_addons_show_layout(trx_addons_sc_properties(array(
																		'type' => 'map',
																		'ids' => get_the_ID(),
																		'count' => 1
																		)));
				?></div>
			</section><?php

			do_action( 'trx_addons_action_after_single_property_section', 'map', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		}

		// Agent info
		if ( ! empty($trx_addons_meta['agent_type']) 
			&& $trx_addons_meta['agent_type']!='none' 
			&& ( $trx_addons_meta['agent_type']=='author' || $trx_addons_meta['agent'] != 0 )
		) {

			do_action( 'trx_addons_action_before_single_property_section', 'contacts', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

			?><section id="<?php echo esc_attr($trx_addons_tabs_id.'_contacts'); ?>_content" class="properties_page_section properties_page_agent">
				<h4 class="properties_page_section_title"><?php echo esc_html($trx_addons_section_titles['contacts']); ?></h4>
				<div class="properties_page_agent_wrap"<?php trx_addons_seo_snippets('author', 'Person'); ?>><?php
					trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.parts.agent.php',
													'trx_addons_args_properties_agent',
													array('meta' => $trx_addons_meta)
												);
				?></div>
			</section><?php

			do_action( 'trx_addons_action_after_single_property_section', 'contacts', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		}

		do_action( 'trx_addons_action_after_single_property_section', 'all', $trx_addons_tabs_id, $trx_addons_meta, $trx_addons_section_titles );

		// Close tabs wrapper
		if (trx_addons_get_option('properties_single_style') == 'tabs') {
			?></div><?php
		}

		do_action( 'trx_addons_action_article_end', 'properties.single' );

	?></article><?php

	do_action( 'trx_addons_action_after_article', 'properties.single' );

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
}

get_footer();
