<?php
/**
 * The "Style 6" template to display the content of the single post or attachment:
 * featured image, title and meta are placed inside the content area
 *
 * @package PUBZINNE
 * @since PUBZINNE 1.75.0
 */
?>
<article id="post-<?php the_ID(); ?>"
	<?php
	post_class( 'post_item_single'
		. ' post_type_' . esc_attr( get_post_type() ) 
		. ' post_format_' . esc_attr( str_replace( 'post-format-', '', get_post_format() ) )
	);
	pubzinne_add_seo_itemprops();
	?>
>
<?php

	do_action( 'pubzinne_action_before_post_data' );

	pubzinne_add_seo_snippets();

	// Single post thumbnail and title
	if ( is_singular( 'post' ) || is_singular( 'attachment' ) ) {
		ob_start();
		?>
		<div class="post_header_wrap post_header_wrap_in_content post_header_wrap_style_<?php
			echo esc_attr( pubzinne_get_theme_option( 'single_style' ) );
			if ( has_post_thumbnail() || str_replace( 'post-format-', '', get_post_format() ) == 'image' ) {
				echo ' with_featured_image';
			}
		?>">
			<?php
			// Post title and meta
			pubzinne_show_post_title_and_meta( array( 
				'author_avatar' => false,
				'show_labels'   => false,
				'share_type'    => 'list',	// block - icons with bg, list - small icons without background
				'split_meta_by' => 'share',
				'add_spaces'    => true,
			) );
			// Featured image
			pubzinne_show_post_featured_image( array(
				'thumb_bg' => false,
				'class'    => 'alignwide'
			) );
			?>
		</div>
		<?php
		$pubzinne_post_header = ob_get_contents();
		ob_end_clean();
		if ( strpos( $pubzinne_post_header, 'post_featured' ) !== false || strpos( $pubzinne_post_header, 'post_title' ) !== false || strpos( $pubzinne_post_header, 'post_meta' ) !== false ) {
			do_action( 'pubzinne_action_before_post_header' );
			pubzinne_show_layout( $pubzinne_post_header );
			do_action( 'pubzinne_action_after_post_header' );
		}
	}

	do_action( 'pubzinne_action_before_post_content' );

	// Post content
	$pubzinne_share_position = pubzinne_array_get_keys_by_value( pubzinne_get_theme_option( 'share_position' ) );
	?>
	<div class="post_content post_content_single entry-content<?php
		if ( in_array( 'left', $pubzinne_share_position ) ) {
			echo ' post_info_vertical_present';
		}
	?>" itemprop="mainEntityOfPage">
		<?php
		if ( in_array( 'left', $pubzinne_share_position ) ) {
			?><div class="post_info_vertical"><?php
				pubzinne_show_post_meta(
					apply_filters(
						'pubzinne_filter_post_meta_args',
						array(
							'components'      => 'share',
							'class'           => 'post_share_vertical',
							'share_type'      => 'block',
							'share_direction' => 'vertical',
						),
						'single',
						1
					)
				);
			?></div><?php
		}
		the_content();
		?>
	</div><!-- .entry-content -->
	<?php
	do_action( 'pubzinne_action_after_post_content' );
	
	// Post footer: Tags, likes, share, author, prev/next links and comments
	do_action( 'pubzinne_action_before_post_footer' );
	?>
	<div class="post_footer post_footer_single entry-footer">
		<?php
		pubzinne_show_post_pagination();
		if ( is_single() && ! is_attachment() ) {
			pubzinne_show_post_footer();
		}
		?>
	</div>
	<?php
	do_action( 'pubzinne_action_after_post_footer' );
	?>
</article>
