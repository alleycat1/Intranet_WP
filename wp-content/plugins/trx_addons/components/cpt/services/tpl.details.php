<?php
/**
 * The template to display service's details page
 *
 * @package ThemeREX Addons
 * @since v1.6.35
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'services_page post_details_page' ); ?>>
	
	<section class="services_page_header post_details_page_header">	

		<?php
		// Get post meta: price, icon, etc.
		$meta = (array)get_post_meta(get_the_ID(), 'trx_addons_options', true);
		$meta['price'] = apply_filters( 'trx_addons_filter_custom_meta_value', !empty($meta['price']) ? $meta['price'] : '', 'price' );
		
		// Image
		if ( has_post_thumbnail() ) {
			?><div class="services_page_featured post_details_page_featured">
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
		?><h2 class="services_page_title post_details_page_title"><?php 
			the_title();
			// Price
			if (trim($meta['price']) != '') {
				?><span class="services_page_price post_details_page_price"><?php trx_addons_show_layout($meta['price']); ?></span><?php
			}
		?></h2>
	</section>
	<?php

	// Post content
	if ( trim( get_the_content() ) != '' || trx_addons_is_preview( 'elementor' ) ) {
		?><section class="services_page_content post_details_page_content entry-content"<?php trx_addons_seo_snippets('articleBody'); ?>><?php
			the_content( );
		?></section><?php
	}

	// Buttons
	if ( comments_open() || get_comments_number() || $meta['product'] > 0) {
		?><section class="services_page_button post_details_page_button sc_item_button"><?php
			if ( comments_open() || get_comments_number() ) {
				?><a href="<?php echo esc_url(get_comments_link()); ?>" class="sc_button"><?php esc_html_e('Comments', 'trx_addons'); ?></a><?php
			}
			if ($meta['product'] > 0) {
				?><a href="<?php echo esc_url(get_permalink($meta['product'])); ?>" class="sc_button"><?php esc_html_e('Order now', 'trx_addons'); ?></a><?php
			}
		?></section><?php
	}
	?>

</article>