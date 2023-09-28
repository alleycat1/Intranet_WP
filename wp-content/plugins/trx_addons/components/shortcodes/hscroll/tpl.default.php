<?php
/**
 * The style "default" of the HScroll
 *
 * @package ThemeREX Addons
 * @since v2.6.0
 */

$args  = get_query_var('trx_addons_args_sc_hscroll');
$total = ! empty( $args['slides'] ) && is_array( $args['slides'] ) ? count( $args['slides'] ) : 0;
?>
<div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
	class="sc_hscroll sc_hscroll_<?php
		echo esc_attr( $args['type'] );
		if ( ! empty( $args['disable_on_mobile'] ) ) echo ' sc_hscroll_disable_on_mobile';
		if ( ! empty( $args['reverse'] ) ) echo ' sc_hscroll_reverse';
		if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
		?>"<?php
	if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
	trx_addons_sc_show_attributes('sc_hscroll', $args, 'sc_wrapper');
	?>
	data-total="<?php echo esc_attr( $total ); ?>"
>
	<div class="sc_hscroll_content sc_item_content"<?php
		trx_addons_sc_show_attributes('sc_hscroll', $args, 'sc_content');
	?>>
		<div class="sc_hscroll_spacer <?php
			echo esc_attr( trx_addons_add_inline_css_class( 'height: calc( ' . ( $total * $args['speed'] ) . ' * ( 100vh - var(--fixed-rows-height) ) );' ) );
		?>"></div>
		<div class="sc_hscroll_wrap<?php
			if ( $total > 0 ) {
				$css = '';
				if ( ! empty( $args['bg_color'] ) ) {
					$css .= 'background-color:' . esc_attr( $args['bg_color'] ) . ';';
				}
				if ( ! empty( $args['bg_image'] ) ) {
					$css .= 'background-image: url(' . esc_url( $args['bg_image'] ) . ');';
				}
				if ( ! empty( $css ) ) {
					echo ' ' . esc_attr( trx_addons_add_inline_css_class( $css ) );
				}
			}
		?>">
			<?php
			// Layers with backgrounds
			if ( $total > 0 ) {
				$first = true;
				for ( $i = 0; $i < $total; $i++ ) {
					$k = ! empty( $args['reverse'] ) ? $total - 1 - $i : $i;
					if ( ! empty( $args['slides'][ $k ]['bg_image'] ) ) {
						?><div class="sc_hscroll_background sc_hscroll_background_<?php
							echo esc_attr( $k );
							echo ' ' . esc_attr( trx_addons_add_inline_css_class( 'background-image: url(' . esc_url( $args['slides'][ $k ]['bg_image'] ) . ');' ) );
							if ( $first ) {
								echo ' sc_hscroll_background_active';
							}
						?>"></div><?php
						$first = false;
					}
				}
			}
			// Arrows
			if ( ! empty( $args['arrows'] ) ) {
				?><div class="sc_hscroll_arrow_left"></div><?php
			}
			?>
			<div class="sc_hscroll_slider <?php echo esc_attr( trx_addons_add_inline_css_class( 'width: ' . ( $total * 100 ) . '%;' ) ); ?>">
				<div class="sc_hscroll_scroller"<?php trx_addons_sc_show_attributes('sc_hscroll', $args, 'sc_scroller'); ?>>
					<div class="sc_hscroll_sections"<?php trx_addons_sc_show_attributes('sc_hscroll', $args, 'sc_items'); ?>>
						<?php
						if ( $total > 0 ) {
							$bg_image = '';
							foreach ( $args['slides'] as $k => $slide ) {
								?>
								<div class="sc_hscroll_section <?php
										$css = 'width: ' . ( 100 / $total ) . '%;';
										if ( ! empty( $slide['bg_image'] ) ) {
											$bg_image = $slide['bg_image'];
										}
										if ( ! empty( $bg_image ) ) {
											$css .= 'background-image: url(' . esc_url( $bg_image ) . ');';
										}
										echo esc_attr( trx_addons_add_inline_css_class( $css ) );
										if ( ! empty( $bg_image ) ) {
											?> sc_hscroll_section_with_bg_image<?php
										}
									?>"<?php
									if ( $slide['type'] == 'section' && ! empty( $slide['section'] ) ) {
										?> data-section="<?php echo esc_attr( $slide['section'] ); ?>"<?php
									}
								?>>
									<?php
									/*
									if ( 'template' === $section['template_type'] ) {
										$template_title = $section['section_template'];
										echo $this->getTemplateInstance()->get_template_content( $template_title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
									*/
									// Prepare layout
									if ( $slide['type'] == 'layout' && ! empty( $slide['layout'] ) ) {
										$slide['content'] = trx_addons_cpt_layouts_show_layout( $slide['layout'], 0, false );

									// Prepare template
									} else if ( $slide['type'] == 'template' && ! empty( $slide['template'] ) ) {
										$slide['content'] = trx_addons_cpt_layouts_show_layout( $slide['template'], 0, false );

									// Put the section id to the catch-list
									} else if ( $slide['type'] == 'section' && ! empty( $slide['section'] ) ) {
										if ( ! isset( $GLOBALS['TRX_ADDONS_STORAGE']['catch_output']['sc_hscroll'] ) ) {
											$GLOBALS['TRX_ADDONS_STORAGE']['catch_output']['sc_hscroll'] = array();
										}
										$GLOBALS['TRX_ADDONS_STORAGE']['catch_output']['sc_hscroll'][ trim( $slide['section'] ) ] = 1;
									}
									// Output the content
									if ( ! empty( $slide['content'] ) ) {
										trx_addons_show_layout( $slide['content'] );
									}
									?>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
			</div>
			<?php
			// Arrows
			if ( ! empty( $args['arrows'] ) ) {
				?><div class="sc_hscroll_arrow_right"></div><?php
			}
			// Bullets (dots)
			if ( ! empty( $args['bullets'] ) ) {
				?><div class="sc_hscroll_bullets sc_hscroll_bullets_position_<?php echo esc_attr( $args['bullets_position'] ); ?>"><?php
					for ( $i = 0; $i < $total; $i++ ) {
						?><span class="sc_hscroll_bullet<?php if ( $i == 0 ) echo ' sc_hscroll_bullet_active'; ?>"></span><?php
					}
				?></div><?php
			}
			// Page numbers
			if ( ! empty( $args['numbers'] ) ) {
				?><div class="sc_hscroll_numbers sc_hscroll_numbers_position_<?php echo esc_attr( $args['numbers_position'] ); ?>"><?php
					?><span class="sc_hscroll_number_active">1</span><?php
					?><span class="sc_hscroll_number_delimiter"></span><?php
					?><span class="sc_hscroll_number_total"><?php echo (int)$total; ?></span><?php
				?></div><?php
			}
			// Progress bar
			if ( ! empty( $args['progress'] ) ) {
				?><div class="sc_hscroll_progress sc_hscroll_progress_position_<?php echo esc_attr( $args['progress_position'] ); ?>">
					<div class="sc_hscroll_progress_value"></div>
				</div><?php
			}
			?>
		</div>
	</div>
</div>