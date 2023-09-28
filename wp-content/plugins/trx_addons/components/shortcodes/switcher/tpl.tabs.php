<?php
/**
 * The style "default" of the Switcher
 *
 * @package ThemeREX Addons
 * @since v2.6.0
 */

$args = get_query_var('trx_addons_args_sc_switcher');
if ( ! empty( $args['slides'] ) && is_array( $args['slides'] ) && count( $args['slides'] ) > 0 ) {
	if ( empty( $args['effect'] ) ) $args['effect'] = 'swap';
	?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?>
		class="sc_switcher sc_switcher_<?php
			echo esc_attr( $args['type'] );
			echo ' sc_switcher_effect_' . esc_attr( $args['effect'] );
			if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
			?>"<?php
		if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
		trx_addons_sc_show_attributes('sc_switcher', $args, 'sc_wrapper');
	?>><?php

		trx_addons_sc_show_titles( 'sc_switcher', $args );

		?><div class="sc_switcher_content sc_item_content"<?php
			trx_addons_sc_show_attributes('sc_switcher', $args, 'sc_content');
		?>>
			<div class="sc_switcher_tabs_controls"><?php
				$i = 0;
				foreach ( $args['slides'] as $slide ) {
					?><div class="sc_switcher_tab<?php if ( $i++ == 0 ) echo ' sc_switcher_tab_active'; ?>" data-tab="<?php echo esc_attr( $i ); ?>">
						<?php echo wp_kses( trx_addons_prepare_macros( $slide['slide_title'] ), 'trx_addons_kses_content' ); ?>
						<a href="#" class="sc_switcher_tab_link"></a>
					</div><?php
				}
			?></div>
			<div class="sc_switcher_sections"<?php trx_addons_sc_show_attributes('sc_switcher', $args, 'sc_items'); ?>>
				<div class="sc_switcher_slider sc_switcher_slider_<?php echo count( $args['slides'] ); ?>">
					<?php
					$i = 0;
					foreach ( $args['slides'] as $slide ) {
						?><div class="sc_switcher_section<?php if ( $i++ == 0 ) echo ' sc_switcher_section_active'; ?>"<?php
							if ( $slide['slide_type'] == 'section' && ! empty( $slide['slide_section'] ) ) {
								?> data-section="<?php echo esc_attr( $slide['slide_section'] ); ?>"<?php
							}
						?>>
							<?php
							// Prepare layout
							if ( $slide['slide_type'] == 'layout' && ! empty( $slide['slide_layout'] ) ) {
								$slide['slide_content'] = trx_addons_cpt_layouts_show_layout( $slide['slide_layout'], 0, false );

							// Prepare template
							} else if ( $slide['slide_type'] == 'template' && ! empty( $slide['slide_template'] ) ) {
								$slide['slide_content'] = trx_addons_cpt_layouts_show_layout( $slide['slide_template'], 0, false );

							// Put the section id to the catch-list
							} else if ( $slide['slide_type'] == 'section' && ! empty( $slide['slide_section'] ) ) {
								if ( ! isset( $GLOBALS['TRX_ADDONS_STORAGE']['catch_output']['sc_switcher'] ) ) {
									$GLOBALS['TRX_ADDONS_STORAGE']['catch_output']['sc_switcher'] = array();
								}
								$GLOBALS['TRX_ADDONS_STORAGE']['catch_output']['sc_switcher'][ trim( $slide['slide_section'] ) ] = 1;

							// Content
							} else if ( $slide['slide_type'] == 'content' && ! empty( $slide['slide_content'] ) ) {
								$slide['slide_content'] = do_shortcode( str_replace( array( '<p>[', ']</p>' ), array( '[', ']' ),  $slide['slide_content'] ) );
							}

							// Output the content
							if ( ! empty( $slide['slide_content'] ) ) {
								trx_addons_show_layout( $slide['slide_content'] );
							}
							?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div><?php
		
		trx_addons_sc_show_links('sc_switcher', $args);

	?></div><?php
}
