<?php
/**
 * The template to display the user's avatar, bio and socials on the Author page
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.71.0
 */
?>

<div class="author_page author vcard" itemprop="author" itemscope="itemscope" itemtype="<?php echo esc_attr( pubzinne_get_protocol( true ) ); ?>//schema.org/Person">

	<div class="author_avatar" itemprop="image">
		<?php
		$pubzinne_mult = pubzinne_get_retina_multiplier();
		echo get_avatar( get_the_author_meta( 'user_email' ), 120 * $pubzinne_mult );
		?>
	</div><!-- .author_avatar -->

	<h4 class="author_title" itemprop="name"><span class="fn"><?php the_author(); ?></span></h4>

	<div class="author_bio" itemprop="description"><?php echo wp_kses( wpautop( get_the_author_meta( 'description' ) ), 'pubzinne_kses_content' ); ?></div>

	<div class="author_details">
		<span class="author_posts_total">
			<?php
			$pubzinne_posts_total = count_user_posts( get_the_author_meta('ID'), 'post' );
			if ( $pubzinne_posts_total > 0 ) {
				echo wp_kses( sprintf( _n( '%s article published', '%s articles published', $pubzinne_posts_total, 'pubzinne' ),
										'<span class="author_posts_total_value">' . number_format_i18n( $pubzinne_posts_total ) . '</span>'
								 		),
							'pubzinne_kses_content'
							);
			} else {
				esc_html_e( 'No posts published.', 'pubzinne' );
			}
			?>
		</span><?php
			ob_start();
			do_action( 'pubzinne_action_user_meta', 'author-page' );
			$pubzinne_socials = ob_get_contents();
			ob_end_clean();
			pubzinne_show_layout( $pubzinne_socials,
				'<span class="author_socials"><span class="author_socials_caption">' . esc_html__( 'Follow:', 'pubzinne' ) . '</span>',
				'</span>'
			);
		?>
	</div><!-- .author_details -->

</div><!-- .author_page -->
