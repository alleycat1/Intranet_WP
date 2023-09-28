<?php
/**
 * The template to display the page title and breadcrumbs
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

// Page (category, tag, archive, author) title

if ( pubzinne_need_page_title() ) {
	pubzinne_sc_layouts_showed( 'title', true );
	pubzinne_sc_layouts_showed( 'postmeta', true );
	?>
	<div class="top_panel_title sc_layouts_row sc_layouts_row_type_normal">
		<div class="content_wrap">
			<div class="sc_layouts_column sc_layouts_column_align_center">
				<div class="sc_layouts_item">
					<div class="sc_layouts_title sc_align_center">
						<?php
						// Post meta on the single post
						if ( is_single() ) {
							?>
							<div class="sc_layouts_title_meta">
							<?php
								pubzinne_show_post_meta(
									apply_filters(
										'pubzinne_filter_post_meta_args', array(
											'components' => join( ',', pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'meta_parts' ) ) ),
											'counters'   => join( ',', pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'counters' ) ) ),
											'seo'        => pubzinne_is_on( pubzinne_get_theme_option( 'seo_snippets' ) ),
										), 'header', 1
									)
								);
							?>
							</div>
							<?php
						}

						// Blog/Post title
						?>
						<div class="sc_layouts_title_title">
							<?php
							$pubzinne_blog_title           = pubzinne_get_blog_title();
							$pubzinne_blog_title_text      = '';
							$pubzinne_blog_title_class     = '';
							$pubzinne_blog_title_link      = '';
							$pubzinne_blog_title_link_text = '';
							if ( is_array( $pubzinne_blog_title ) ) {
								$pubzinne_blog_title_text      = $pubzinne_blog_title['text'];
								$pubzinne_blog_title_class     = ! empty( $pubzinne_blog_title['class'] ) ? ' ' . $pubzinne_blog_title['class'] : '';
								$pubzinne_blog_title_link      = ! empty( $pubzinne_blog_title['link'] ) ? $pubzinne_blog_title['link'] : '';
								$pubzinne_blog_title_link_text = ! empty( $pubzinne_blog_title['link_text'] ) ? $pubzinne_blog_title['link_text'] : '';
							} else {
								$pubzinne_blog_title_text = $pubzinne_blog_title;
							}
							?>
							<h1 itemprop="headline" class="sc_layouts_title_caption<?php echo esc_attr( $pubzinne_blog_title_class ); ?>">
								<?php
								$pubzinne_top_icon = pubzinne_get_term_image_small();
								if ( ! empty( $pubzinne_top_icon ) ) {
									$pubzinne_attr = pubzinne_getimagesize( $pubzinne_top_icon );
									?>
									<img src="<?php echo esc_url( $pubzinne_top_icon ); ?>" alt="<?php esc_attr_e( 'Site icon', 'pubzinne' ); ?>"
										<?php
										if ( ! empty( $pubzinne_attr[3] ) ) {
											pubzinne_show_layout( $pubzinne_attr[3] );
										}
										?>
									>
									<?php
								}
								echo wp_kses_data( $pubzinne_blog_title_text );
								?>
							</h1>
							<?php
							if ( ! empty( $pubzinne_blog_title_link ) && ! empty( $pubzinne_blog_title_link_text ) ) {
								?>
								<a href="<?php echo esc_url( $pubzinne_blog_title_link ); ?>" class="theme_button theme_button_small sc_layouts_title_link"><?php echo esc_html( $pubzinne_blog_title_link_text ); ?></a>
								<?php
							}

							// Category/Tag description
							if ( ! is_paged() && ( is_category() || is_tag() || is_tax() ) ) {
								the_archive_description( '<div class="sc_layouts_title_description">', '</div>' );
							}

							?>
						</div>
						<?php

						// Breadcrumbs
						ob_start();
						do_action( 'pubzinne_action_breadcrumbs' );
						$pubzinne_breadcrumbs = ob_get_contents();
						ob_end_clean();
						pubzinne_show_layout( $pubzinne_breadcrumbs, '<div class="sc_layouts_title_breadcrumbs">', '</div>' );
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
