<?php
/**
 * The style "default" of the Images Compare
 *
 * @package ThemeREX Addons
 * @since v1.97.0
 */

$args = get_query_var('trx_addons_args_sc_icompare');

$icon_present = '';

if ( ! empty( $args['image1'] ) && ! empty( $args['image2'] ) ) {

	$image1 = trx_addons_get_attachment_img( $args['image1'], 'full', array(
																		'filter' => 'icompare-default',
																		'class'  => 'sc_icompare_image sc_icompare_image1',
																		'alt'    => __( 'Before', 'trx_addons' )
																		)
											);
	$image2 = trx_addons_get_attachment_img( $args['image2'], 'full', array(
																		'filter' => 'icompare-default',
																		'class'  => 'sc_icompare_image sc_icompare_image2',
																		'alt'    => __( 'After', 'trx_addons' )
																		)
											);
	if ( ! empty( $image1 ) && ! empty( $image2 ) ) {

		?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
			class="sc_icompare sc_icompare_<?php echo esc_attr( $args['type'] );
				echo ' sc_icompare_direction_' . esc_attr( $args['direction'] )
					. ' sc_icompare_event_' . esc_attr( $args['event'] );
				if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
				?>"<?php
			if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
			trx_addons_sc_show_attributes( 'sc_icompare', $args, 'sc_wrapper' );
		?>><?php

			trx_addons_sc_show_titles( 'sc_icompare', $args );

			?><div class="sc_icompare_content sc_item_content"<?php trx_addons_sc_show_attributes('sc_icompare', $args, 'sc_items_wrapper'); ?>><?php

				trx_addons_show_layout( str_replace( 'sc_icompare_image1', 'sc_icompare_image0', $image1 ) );

				trx_addons_show_layout( $image1 );

				trx_addons_show_layout( $image2 );

				if ( ! empty( $args['before_text'] ) || ! empty( $args['after_text'] ) ) {
					?><div class="sc_icompare_overlay"<?php trx_addons_sc_show_attributes('sc_icompare', $args, 'sc_items_overlay'); ?>><?php
						if ( ! empty( $args['before_text'] ) ) {
							?><span class="sc_icompare_text_before sc_icompare_text_pos_<?php echo esc_attr( $args['before_pos'] ); ?>"><?php echo wp_kses( $args['before_text'], 'trx_addons_kses_content' ); ?></span><?php
						}
						if ( ! empty( $args['after_text'] ) ) {
							?><span class="sc_icompare_text_after sc_icompare_text_pos_<?php echo esc_attr( $args['after_pos'] ); ?>"><?php echo wp_kses( $args['after_text'], 'trx_addons_kses_content' ); ?></span><?php
						}
					?></div><?php
				}

				?><div class="sc_icompare_handler sc_icompare_handler_style_<?php echo esc_attr( $args['handler'] ); ?>"
					data-handler-pos="<?php echo esc_attr( $args['handler_pos'] ); ?>"
				><?php

					// Separator
					if ( ! empty( $args['handler_separator'] ) ) {
						?>
						<span class="sc_icompare_handler_separator sc_icompare_handler_separator1"></span>
						<span class="sc_icompare_handler_separator sc_icompare_handler_separator2"></span>
						<?php
					}

					$shown = false;
					// Handler image
					if ( ! empty( $args['handler_image'] ) ) {
						$img = trx_addons_get_attachment_img( $args['handler_image'], trx_addons_get_thumb_size( 'tiny' ), array(
																		'filter' => 'icompare-handler',
																		'class'  => 'sc_icompare_handler_image',
																		'alt'    => __( 'Handler image', 'trx_addons' )
																		)
											);
						if ( ! empty( $img ) ) {
							trx_addons_show_layout( $img );
							$shown = true;
						}

					// Handler icon
					} else {
						$icon_type = ! empty( $args['icon_type'] ) ? $args['icon_type'] : 'icon';
						$icon = ! empty( $icon_type ) && ! empty( $args['icon_' . $icon_type] ) && $args['icon_' . $icon_type] != 'empty'
									? $args['icon_' . $icon_type] 
									: '';
						$svg = '';
						$img = '';
						if ( ! empty( $icon ) ) {
							if ( strpos( $icon_present, $icon_type ) === false ) {
								$icon_present .= ( ! empty( $icon_present ) ? ',' : '') . $icon_type;
							}
						} else if ( ! empty( $args['icon'] ) && strtolower( $args['icon'] ) != 'none' ) {
							$icon = $args['icon'];
						}
						if ( ! empty( $icon ) ) {
							if ( trx_addons_is_url( $icon ) ) {
								if ( strpos( $icon, '.svg' ) !== false ) {
									$svg = $icon;
									$icon_type = 'svg';
								} else {
									$img = $icon;
									$icon_type = 'images';
								}
								$icon = basename( $icon );
							}
							if ( ! empty( $svg ) ) {
								?><span class="sc_icompare_handler_icon sc_icon_type_svg"><?php
									trx_addons_show_layout( trx_addons_get_svg_from_file( $svg ) );
								?></span><?php
							} else if ( ! empty( $img ) ) {
								$attr = trx_addons_getimagesize( $img );
								?><img class="sc_icompare_handler_icon sc_icon_type_images sc_icon_as_image"
									src="<?php echo esc_url($img); ?>"
									alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php
									echo ( ! empty( $attr[3] ) ? ' ' . trim( $attr[3] ) : '');
								?>><?php
							} else {
								?><span class="sc_icompare_handler_icon sc_icon_type_icon <?php echo esc_attr( $icon ); ?>"></span><?php
							}
							$shown = true;
						}
					}

					// Default arrows
					if ( ! $shown ) {
						?><span class="sc_icompare_handler_arrows"></span><?php
					}

				?></div><?php

			?></div><?php

			trx_addons_sc_show_links('sc_icompare', $args);

		?></div><?php

		trx_addons_load_icons($icon_present);
	}
}
