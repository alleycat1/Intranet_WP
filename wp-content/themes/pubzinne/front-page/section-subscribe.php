<div class="front_page_section front_page_section_subscribe<?php
	$pubzinne_scheme = pubzinne_get_theme_option( 'front_page_subscribe_scheme' );
	if ( ! empty( $pubzinne_scheme ) && ! pubzinne_is_inherit( $pubzinne_scheme ) ) {
		echo ' scheme_' . esc_attr( $pubzinne_scheme );
	}
	echo ' front_page_section_paddings_' . esc_attr( pubzinne_get_theme_option( 'front_page_subscribe_paddings' ) );
	if ( pubzinne_get_theme_option( 'front_page_subscribe_stack' ) ) {
		echo ' sc_stack_section_on';
	}
?>"
		<?php
		$pubzinne_css      = '';
		$pubzinne_bg_image = pubzinne_get_theme_option( 'front_page_subscribe_bg_image' );
		if ( ! empty( $pubzinne_bg_image ) ) {
			$pubzinne_css .= 'background-image: url(' . esc_url( pubzinne_get_attachment_url( $pubzinne_bg_image ) ) . ');';
		}
		if ( ! empty( $pubzinne_css ) ) {
			echo ' style="' . esc_attr( $pubzinne_css ) . '"';
		}
		?>
>
<?php
	// Add anchor
	$pubzinne_anchor_icon = pubzinne_get_theme_option( 'front_page_subscribe_anchor_icon' );
	$pubzinne_anchor_text = pubzinne_get_theme_option( 'front_page_subscribe_anchor_text' );
if ( ( ! empty( $pubzinne_anchor_icon ) || ! empty( $pubzinne_anchor_text ) ) && shortcode_exists( 'trx_sc_anchor' ) ) {
	echo do_shortcode(
		'[trx_sc_anchor id="front_page_section_subscribe"'
									. ( ! empty( $pubzinne_anchor_icon ) ? ' icon="' . esc_attr( $pubzinne_anchor_icon ) . '"' : '' )
									. ( ! empty( $pubzinne_anchor_text ) ? ' title="' . esc_attr( $pubzinne_anchor_text ) . '"' : '' )
									. ']'
	);
}
?>
	<div class="front_page_section_inner front_page_section_subscribe_inner
	<?php
	if ( pubzinne_get_theme_option( 'front_page_subscribe_fullheight' ) ) {
		echo ' pubzinne-full-height sc_layouts_flex sc_layouts_columns_middle';
	}
	?>
			"
			<?php
			$pubzinne_css      = '';
			$pubzinne_bg_mask  = pubzinne_get_theme_option( 'front_page_subscribe_bg_mask' );
			$pubzinne_bg_color_type = pubzinne_get_theme_option( 'front_page_subscribe_bg_color_type' );
			if ( 'custom' == $pubzinne_bg_color_type ) {
				$pubzinne_bg_color = pubzinne_get_theme_option( 'front_page_subscribe_bg_color' );
			} elseif ( 'scheme_bg_color' == $pubzinne_bg_color_type ) {
				$pubzinne_bg_color = pubzinne_get_scheme_color( 'bg_color', $pubzinne_scheme );
			} else {
				$pubzinne_bg_color = '';
			}
			if ( ! empty( $pubzinne_bg_color ) && $pubzinne_bg_mask > 0 ) {
				$pubzinne_css .= 'background-color: ' . esc_attr(
					1 == $pubzinne_bg_mask ? $pubzinne_bg_color : pubzinne_hex2rgba( $pubzinne_bg_color, $pubzinne_bg_mask )
				) . ';';
			}
			if ( ! empty( $pubzinne_css ) ) {
				echo ' style="' . esc_attr( $pubzinne_css ) . '"';
			}
			?>
	>
		<div class="front_page_section_content_wrap front_page_section_subscribe_content_wrap content_wrap">
			<?php
			// Caption
			$pubzinne_caption = pubzinne_get_theme_option( 'front_page_subscribe_caption' );
			if ( ! empty( $pubzinne_caption ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<h2 class="front_page_section_caption front_page_section_subscribe_caption front_page_block_<?php echo ! empty( $pubzinne_caption ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( $pubzinne_caption, 'pubzinne_kses_content' ); ?></h2>
				<?php
			}

			// Description (text)
			$pubzinne_description = pubzinne_get_theme_option( 'front_page_subscribe_description' );
			if ( ! empty( $pubzinne_description ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_description front_page_section_subscribe_description front_page_block_<?php echo ! empty( $pubzinne_description ) ? 'filled' : 'empty'; ?>"><?php echo wp_kses( wpautop( $pubzinne_description ), 'pubzinne_kses_content' ); ?></div>
				<?php
			}

			// Content
			$pubzinne_sc = pubzinne_get_theme_option( 'front_page_subscribe_shortcode' );
			if ( ! empty( $pubzinne_sc ) || ( current_user_can( 'edit_theme_options' ) && is_customize_preview() ) ) {
				?>
				<div class="front_page_section_output front_page_section_subscribe_output front_page_block_<?php echo ! empty( $pubzinne_sc ) ? 'filled' : 'empty'; ?>">
				<?php
					pubzinne_show_layout( do_shortcode( $pubzinne_sc ) );
				?>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
