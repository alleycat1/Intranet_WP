<?php
/**
 * The Footer: widgets area, logo, footer menu and socials
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.0
 */

							// Widgets area inside page content
							pubzinne_create_widgets_area( 'widgets_below_content' );
						
							?>
						</div><!-- /.content -->
						<?php

						// Show main sidebar
						get_sidebar();
						?>
					</div><!-- /.content_wrap -->
					<?php

					// Widgets area below page content and related posts below page content
					$pubzinne_body_style = pubzinne_get_theme_option( 'body_style' );
					$pubzinne_widgets_name = pubzinne_get_theme_option( 'widgets_below_page' );
					$pubzinne_show_widgets = ! pubzinne_is_off( $pubzinne_widgets_name ) && is_active_sidebar( $pubzinne_widgets_name );
					$pubzinne_show_related = is_single() && pubzinne_get_theme_option( 'related_position' ) == 'below_page';
					if ( $pubzinne_show_widgets || $pubzinne_show_related ) {
						if ( 'fullscreen' != $pubzinne_body_style ) {
							?>
							<div class="content_wrap">
							<?php
						}
						// Show related posts before footer
						if ( $pubzinne_show_related ) {
							do_action( 'pubzinne_action_related_posts' );
						}

						// Widgets area below page content
						if ( $pubzinne_show_widgets ) {
							pubzinne_create_widgets_area( 'widgets_below_page' );
						}
						if ( 'fullscreen' != $pubzinne_body_style ) {
							?>
							</div><!-- /.content_wrap -->
							<?php
						}
					}
					?>
			</div><!-- /.page_content_wrap -->
			<?php

			// Don't display the footer elements while actions 'full_post_loading' and 'prev_post_loading'
			if ( ( ! is_singular( 'post' ) && ! is_singular( 'attachment' ) ) || ! in_array ( pubzinne_get_value_gp( 'action' ), array( 'full_post_loading', 'prev_post_loading' ) ) ) {
				
				// Skip link anchor to fast access to the footer from keyboard
				?>
				<a id="footer_skip_link_anchor" class="pubzinne_skip_link_anchor" href="#"></a>
				<?php

				do_action( 'pubzinne_action_before_footer' );

				// Footer
				$pubzinne_footer_type = pubzinne_get_theme_option( 'footer_type' );
				if ( 'custom' == $pubzinne_footer_type && ! pubzinne_is_layouts_available() ) {
					$pubzinne_footer_type = 'default';
				}
				get_template_part( apply_filters( 'pubzinne_filter_get_template_part', "templates/footer-{$pubzinne_footer_type}" ) );

				do_action( 'pubzinne_action_after_footer' );

			}
			?>

		</div><!-- /.page_wrap -->

	</div><!-- /.body_wrap -->

	<?php wp_footer(); ?>

</body>
</html>