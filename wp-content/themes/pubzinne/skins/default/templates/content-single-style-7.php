<?php
/**
 * The "Style 7" template to display the content of the single post or attachment:
 * featured image and title are placed in the fullscreen post header, meta is inside the content
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

	// Single post meta
	if ( is_singular( 'post' ) || is_singular( 'attachment' ) ) {
		ob_start();
		?>
		<div class="post_header_wrap post_header_wrap_in_content post_header_wrap_style_<?php
			echo esc_attr( pubzinne_get_theme_option( 'single_style' ) );
		?>">
			<?php
			// Post meta
			pubzinne_show_post_title_and_meta( array( 
				'show_title'    => false,
				'author_avatar' => false,
				'show_labels'   => false,
				'share_type'    => 'list',	// block - icons with bg, list - small icons without background
				'split_meta_by' => 'share',
			) );
			?>
		</div>
		<?php
		$pubzinne_post_header = ob_get_contents();
		ob_end_clean();
		if ( strpos( $pubzinne_post_header, 'post_meta' ) !== false ) {
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
