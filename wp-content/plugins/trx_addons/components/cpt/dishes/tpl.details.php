<?php
/**
 * The template to display dish's details page
 *
 * @package ThemeREX Addons
 * @since v1.6.09
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'dishes_page post_details_page' ); ?>>
	
	<section class="dishes_page_header post_details_page_header">	

		<?php
		// Get post meta: price, heat level, nutritions, ingredients, etc.
		$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
		
		// Image
		if ( has_post_thumbnail() ) {
			?><div class="dishes_page_featured post_details_page_featured">
				<?php
				the_post_thumbnail( trx_addons_get_thumb_size('huge'), trx_addons_seo_image_params(array(
							'alt' => get_the_title()
							))
						);
				?>
			</div>
			<?php
		}
		
		// Title
		?><h2 class="dishes_page_title post_details_page_title"><?php 
			the_title();
			// Price
			if (trim($meta['price']) != '') {
				?><span class="dishes_page_price post_details_page_price"><?php echo esc_html($meta['price']); ?></span><?php
			}
		?></h2><?php

		// Heat level
		if (trim($meta['spicy']) != '') {
			$meta['spicy'] = max(1, min(5, $meta['spicy']));
			?><span class="dishes_page_spicy dishes_page_spicy_<?php echo esc_html($meta['spicy']); ?>">
				<span class="dishes_page_spicy_label"><?php esc_html_e('Heat Level:', 'trx_addons'); ?></span>
				<span class="dishes_page_spicy_value"><?php echo esc_html($meta['spicy']); ?></span>
			</span><?php
		}
		?>
	</section>
	<?php

	// Post content
	if ( trim( get_the_content() ) != '' || trx_addons_is_preview( 'elementor' ) ) {
		?><section class="dishes_page_content post_details_page_content entry-content"<?php trx_addons_seo_snippets('articleBody'); ?>><?php
			the_content( );
		?></section><?php
	}

	// Post details
	if (trim($meta['nutritions'])!='' || trim($meta['ingredients'])!='') {
		
		?><section class="dishes_page_details post_details_page_details">
			<h3 class="dishes_page_details_title"><?php esc_html_e('Details', 'trx_addons'); ?></h3>
			<?php
			// Nutritions list
			if (trim($meta['nutritions'])!='') {
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
			if (trim($meta['ingredients'])!='') {
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

	// Buttons
	$add_to_cart_link = apply_filters( 'trx_addons_filter_cpt_add_to_cart_button', '', apply_filters( 'trx_addons_filter_sc_item_link_classes', 'sc_button', 'sc_dishes', array() ) );
	if ( comments_open() || get_comments_number() || $meta['product'] > 0 || ! empty( $add_to_cart_link ) ) {
		?><section class="dishes_page_button post_details_page_button sc_item_button"><?php
			if ( comments_open() || get_comments_number() ) {
				?><a href="<?php echo esc_url(get_comments_link()); ?>" class="sc_button"><?php esc_html_e('Comments', 'trx_addons'); ?></a><?php
			}
			if ($meta['product'] > 0) {
				?><a href="<?php echo esc_url(get_permalink($meta['product'])); ?>" class="sc_button"><?php esc_html_e('Order now', 'trx_addons'); ?></a><?php
			} else if ( ! empty( $add_to_cart_link ) ) {
				trx_addons_show_layout( $add_to_cart_link );
			}
		?></section><?php
	}
	?>

</article>