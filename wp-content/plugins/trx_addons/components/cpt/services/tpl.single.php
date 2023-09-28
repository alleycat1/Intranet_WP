<?php
/**
 * The template to display the service's single page
 *
 * @package ThemeREX Addons
 * @since v1.4
 */

get_header();

while ( have_posts() ) { the_post();

	do_action('trx_addons_action_before_article', 'services.single');

	$show_image = ! trx_addons_sc_layouts_showed('featured') && has_post_thumbnail();
	$show_title = ! trx_addons_sc_layouts_showed('title');
	$price_showed = false;

	?><article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>" <?php post_class( 'services_single itemscope' ); trx_addons_seo_snippets('', 'Article'); ?>><?php
		
		do_action('trx_addons_action_article_start', 'services.single');

		if ( $show_image || $show_title ) {
			?>
			<section class="services_page_header">	
				<?php
				// Get post meta: price, icon, etc.
				$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);

				// Image
				if ( $show_image ) {
					?><div class="services_page_featured"><?php
						the_post_thumbnail( 
									//apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry-big'), 'services-single'),
									apply_filters('trx_addons_filter_thumb_size', 'full', 'services-single'),
									trx_addons_seo_image_params(array(
										'alt' => get_the_title()
									))
								);
						if (!empty($meta['price'])) {
							?><div class="sc_services_item_price"><?php echo esc_html($meta['price']); ?></div><?php
							$price_showed = true;
						}
					?></div><?php
				}

				// Title
				if ( $show_title ) {
					?><h2 class="services_page_title<?php if (!$price_showed && !empty($meta['price'])) echo ' with_price'; ?>"><?php
						the_title();
						// Price
						if ( ! $price_showed && !empty($meta['price'])) {
							?><div class="sc_services_item_price"><?php echo esc_html($meta['price']); ?></div><?php
							$price_showed = true;
						}
					?></h2><?php
				}
				?>
			</section>
			<?php
		}

		// Post content
		if ( trim( get_the_content() ) != '' || trx_addons_is_preview( 'elementor' ) ) {
			?><section class="services_page_content entry-content"<?php trx_addons_seo_snippets('articleBody'); ?>><?php
				// Price
				if ( ! $price_showed && ! empty( $meta['price'] ) ) {
					?><div class="sc_services_item_price"><?php echo esc_html($meta['price']); ?></div><?php
				}
				the_content( );
			?></section><?php
		}

		// Link to the product
		if (!empty($meta['product']) && (int) $meta['product'] > 0) {
			?><div class="services_page_buttons">
				<a href="<?php echo esc_url(get_permalink($meta['product'])); ?>" class="sc_button theme_button"><?php esc_html_e('Order now', 'trx_addons'); ?></a>
			</div><?php
		}

		do_action('trx_addons_action_article_end', 'services.single');

	?></article><?php

	do_action('trx_addons_action_after_article', 'services.single');

	// Open tabs wrapper
	$trx_addons_form_id = trx_addons_get_option('services_form');
	$trx_addons_add_contacts = (int) $trx_addons_form_id > 0 || ($trx_addons_form_id == 'default' && function_exists('trx_addons_sc_form'));
	$trx_addons_add_comments = comments_open() || get_comments_number();
	if ($trx_addons_add_contacts && $trx_addons_add_comments) {
		wp_enqueue_script('jquery-ui-tabs', false, array('jquery', 'jquery-ui-core'), null, true);
		?><div class="trx_addons_tabs services_page_tabs">
			<ul class="trx_addons_tabs_titles">
				<li data-active="true"><a href="<?php echo esc_url(trx_addons_get_hash_link('#services_page_tab_comments')); ?>"><?php
					esc_html_e('Comments', 'trx_addons');
				?></a></li><?php
				?><li><a href="<?php echo esc_url(trx_addons_get_hash_link('#services_page_tab_contacts')); ?>"><?php
					esc_html_e('Contact Us', 'trx_addons');
				?></a></li>
			</ul><?php
	}
	// If comments are open or we have at least one comment, load up the comment template.
	if ($trx_addons_add_comments) {
		?><section id="services_page_tab_comments" class="services_page_section services_page_section_comments"><?php
			comments_template();
		?></section><?php
	}
	
	// Contact form
	if ($trx_addons_add_contacts) {
		?><section id="services_page_tab_contacts" class="services_page_section services_page_section_contacts"><?php
			// Contact Form 7
			if ( (int) $trx_addons_form_id > 0 ) {
				// Add filter 'wpcf7_form_elements' before Contact Form 7 show form to add text
				if ( !function_exists( 'trx_addons_cpt_services_wpcf7_form_elements' ) ) {
					add_filter('wpcf7_form_elements', 'trx_addons_cpt_services_wpcf7_form_elements');
					function trx_addons_cpt_services_wpcf7_form_elements($elements) {
						$elements = str_replace('</textarea>',
												esc_html(sprintf(__("Hi.\nI'm interested in '%s'.\nPlease, get in touch with me.", 'trx_addons'), get_the_title()))
												. '</textarea>',
												$elements
												);
						return $elements;
					}
				}
				// Store data for the form for 4 hours
				set_transient(sprintf('trx_addons_cf7_%d_data', $trx_addons_form_id), array(
														'item'  => get_the_ID()
														), 4 * 60 * 60);
				// Display Contact Form 7
				trx_addons_show_layout(do_shortcode('[contact-form-7 id="'.esc_attr($trx_addons_form_id).'"]'));
				// Remove filter 'wpcf7_form_elements' after Contact Form 7 showed
				remove_filter('wpcf7_form_elements', 'trx_addons_cpt_services_wpcf7_form_elements');
			
			// Default form
			} else if ($trx_addons_form_id == 'default') {
				trx_addons_show_layout(trx_addons_sc_form(array()));
			}
		?></section><?php
	}

	// Close tabs wrapper
	if ($trx_addons_add_contacts && $trx_addons_add_comments) {
		?></div><?php
	}
}

get_footer();
