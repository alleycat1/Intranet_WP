<?php
/**
 * The template to display the dish's single page
 *
 * @package ThemeREX Addons
 * @since v1.6.09
 */

get_header();

while ( have_posts() ) { the_post();
	do_action('trx_addons_action_before_article', 'dishes.single');
	?>
	<article id="post-<?php the_ID(); ?>" data-post-id="<?php the_ID(); ?>" <?php post_class( 'dishes_single itemscope' ); trx_addons_seo_snippets('', 'Article'); ?>>
		<?php
		do_action('trx_addons_action_article_start', 'dishes.single');

		// Get post meta: price, heat level, nutritions, ingredients, etc.
		$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);

		// Post header
		if ( ( ! trx_addons_sc_layouts_showed('featured') && has_post_thumbnail() ) || ! trx_addons_sc_layouts_showed('title') ) {
			?>
			<section class="dishes_page_header">	
				<?php
				// Image
				if ( ! trx_addons_sc_layouts_showed('featured') && has_post_thumbnail() ) {
					?><div class="dishes_page_featured">
						<?php
						do_action('trx_addons_action_before_featured');
						the_post_thumbnail( 
											apply_filters('trx_addons_filter_thumb_size', 'full', 'dishes-single'),
											trx_addons_seo_image_params(array(
																			'alt' => get_the_title()
																			))
											);

						// Heat level
						if (trim($meta['spicy']) != '') {
							$meta['spicy'] = max(1, min(5, $meta['spicy']));
							?><span class="dishes_page_spicy dishes_page_spicy_<?php echo esc_html($meta['spicy']); ?>">
								<span class="dishes_page_spicy_label"><?php esc_html_e('Heat Level:', 'trx_addons'); ?></span>
								<span class="dishes_page_spicy_value"><?php echo esc_html($meta['spicy']); ?></span>
							</span><?php
							trx_addons_sc_layouts_showed('spicy', true);
						}

						// Price
						if ( trx_addons_sc_layouts_showed('title') ) {
							if (trim($meta['price']) != '') {
								?><span class="dishes_page_price"><?php echo esc_html($meta['price']); ?></span><?php
							}
						}
						do_action('trx_addons_action_after_featured');
						?>
					</div>
					<?php
				}
				
				// Title
				if ( ! trx_addons_sc_layouts_showed('title') ) {
					?><h2 class="dishes_page_title<?php if (trim($meta['price']) != '') echo ' with_price'; ?>"><?php 
						the_title();
						// Price
						if (trim($meta['price']) != '') {
							?><span class="dishes_page_price"><?php echo esc_html($meta['price']); ?></span><?php
						}
					?></h2><?php
				}
				?>
			</section>
			<?php
		}

		// Post content
		if ( trim( get_the_content() ) != '' || trx_addons_is_preview( 'elementor' ) ) {
			?><section class="dishes_page_content entry-content"<?php trx_addons_seo_snippets('articleBody'); ?>><?php
				the_content( );
			?></section><?php
		}

		// Post details
		if ( !empty($meta['nutritions']) || !empty($meta['ingredients']) || ( ! trx_addons_sc_layouts_showed('spicy') && trim($meta['spicy']) != '' ) ) {
			
			?><section class="dishes_page_details">
				<h3 class="dishes_page_details_title"><?php esc_html_e('Details', 'trx_addons'); ?></h3>
				<?php
				// Heat level (if not showed)
				if ( ! trx_addons_sc_layouts_showed('spicy') && trim($meta['spicy']) != '' ) {
					$meta['spicy'] = max(1, min(5, $meta['spicy']));
					?>
					<div class="dishes_page_details_spicy">
						<span class="dishes_page_spicy dishes_page_spicy_<?php echo esc_html($meta['spicy']); ?>">
							<span class="dishes_page_spicy_label"><?php esc_html_e('Heat Level:', 'trx_addons'); ?></span>
							<span class="dishes_page_spicy_value"><?php echo esc_html($meta['spicy']); ?></span>
						</span>
					</div>
					<?php
					trx_addons_sc_layouts_showed('spicy', true);
				}
				// Nutritions list
				if ( !empty($meta['nutritions']) ) {
					$nutritions = explode("\n", $meta['nutritions']);
					?>
					<div class="dishes_page_details_nutritions">
						<h4 class="dishes_page_details_nutritions_title"><?php esc_html_e('Nutritions', 'trx_addons'); ?></h4>
						<ul class="dishes_page_details_nutritions_list">
							<?php
							foreach ($nutritions as $nutritions_item) {
								$nutritions_item = trim($nutritions_item);
								if (empty($nutritions_item)) continue;
								?><li><?php echo esc_html($nutritions_item); ?></li><?php
							}
							?>
						</ul>
					</div>
					<?php
				}
				// Ingredients list
				if ( !empty($meta['ingredients']) ) {
					$ingredients = explode("\n", $meta['ingredients']);
					?>
					<div class="dishes_page_details_ingredients">
						<h4 class="dishes_page_details_ingredients_title"><?php esc_html_e('Ingredients', 'trx_addons'); ?></h4>
						<ul class="dishes_page_details_ingredients_list">
							<?php
							foreach ($ingredients as $ingredients_item) {
								$ingredients_item = trim($ingredients_item);
								if (empty($ingredients_item)) continue;
								?><li><?php echo esc_html($ingredients_item); ?></li><?php
							}
							?>
						</ul>
					</div>
					<?php
				}
			?></section><?php
		}

		// Link to the product
		if ( ! empty( $meta['product'] ) && (int) $meta['product'] > 0 ) {
			?><div class="dishes_page_buttons">
				<a href="<?php echo esc_url(get_permalink($meta['product'])); ?>" class="sc_button theme_button"><?php esc_html_e('Order now', 'trx_addons'); ?></a>
			</div><?php
		} else {
			$add_to_cart_link = apply_filters( 'trx_addons_filter_cpt_add_to_cart_button', '', 'sc_button theme_button' );
			if ( ! empty( $add_to_cart_link ) ) {
				?><div class="dishes_page_buttons"><?php
					trx_addons_show_layout( $add_to_cart_link );
				?></div><?php
			}
		}

		do_action('trx_addons_action_article_end', 'dishes.single');
		
	?></article><?php

	do_action('trx_addons_action_after_article', 'dishes.single');

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
}

get_footer();
