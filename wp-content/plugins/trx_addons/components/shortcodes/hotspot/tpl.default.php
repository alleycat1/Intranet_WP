<?php
/**
 * The style "default" of the Hotspot
 *
 * @package ThemeREX Addons
 * @since v1.94.0
 */

$args = get_query_var('trx_addons_args_sc_hotspot');

$icon_present = '';

if ( ! empty( $args['image'] ) ) {

	$image = trx_addons_get_attachment_img( $args['image'], 'full', array(
																		'filter' => 'hotspot-default',
																		'class'  => 'sc_hotspot_image',
																		'alt'    => __( 'Hotspot image', 'trx_addons' )
																		)
											);

	if ( ! empty( $image ) ) {

		?><div <?php if ( ! empty( $args['id'] ) ) echo ' id="' . esc_attr( $args['id'] ) . '"'; ?> 
			class="sc_hotspot sc_hotspot_<?php
				echo esc_attr( $args['type'] );
				if ( ! empty( $args['class'] ) ) echo ' ' . esc_attr( $args['class'] );
				?>"<?php
			if ( ! empty( $args['css'] ) ) echo ' style="' . esc_attr( $args['css'] ) . '"';
			trx_addons_sc_show_attributes('sc_hotspot', $args, 'sc_wrapper');
			?>><?php

			trx_addons_sc_show_titles('sc_hotspot', $args);

			?><div class="sc_hotspot_content sc_item_content"<?php trx_addons_sc_show_attributes('sc_hotspot', $args, 'sc_items_wrapper'); ?>><?php

				trx_addons_show_layout( $image );

				if ( ! empty( $args['image_link'] ) ) {
					?><a href="<?php echo esc_url( $args['image_link'] ); ?>" class="sc_hotspot_image_link"<?php
							if ( ! empty( $args['new_window'] ) || ! empty( $args['image_link_extra']['is_external'] ) ) echo ' target="_blank"';
							if ( ! empty( $args['nofollow'] ) || ! empty( $args['image_link_extra']['nofollow'] ) ) echo ' rel="nofollow"';
					?>></a><?php
				}

				$numbers = 0;

				foreach ( $args['spots'] as $item ) {

					// Dynamic content (from post)
					if ( $item['source'] != 'custom' && (int)$item['post'] > 0 ) {
						$post = get_post( (int)$item['post'] );
						if ( ! empty( $post->ID ) ) {
							$GLOBALS['post'] = $post;
							setup_postdata( $post );
							$item['image'] = ( ! isset( $item['post_parts'] ) || in_array( 'image', $item['post_parts'] ) )
												&& has_post_thumbnail()
													? get_post_thumbnail_id( get_the_ID() )
													: '';
							$item['subtitle'] = ! isset( $item['post_parts'] ) || in_array( 'category', $item['post_parts'] )
													? trx_addons_sc_show_post_meta( 'sc_hotspot', apply_filters( 'trx_addons_filter_post_meta_args', array(
															'components' => 'categories',
															'theme_specific' => false,
															'echo' => false
														), 'sc_hotspot_default' ) )
													: '';
							$item['title'] = ! isset( $item['post_parts'] ) || in_array( 'title', $item['post_parts'] )
													? get_the_title()
													: '';
							$item['price'] = ! isset( $item['post_parts'] ) || in_array( 'price', $item['post_parts'] )
													? apply_filters( 'trx_addons_filter_custom_meta_value', '', 'price' )
													: '';
							$item['description'] = ! isset( $item['post_parts'] ) || in_array( 'excerpt', $item['post_parts'] )
													? trx_addons_excerpt( get_the_excerpt(), apply_filters( 'trx_addons_filter_sc_hotspot_excerpt_length', 16 ) )
													: '';
							$item['link'] = get_permalink();
							wp_reset_postdata();
						}
					}

					$item['open'] = (int) $item['open'] > 0 ? 'click' : 'hover';
					$item['spot_visible'] = (int) $item['spot_visible'] > 0 ? 'always' : 'hover';
					?><div class="<?php
							echo apply_filters(
									'trx_addons_filter_sc_item_classes',
									'sc_hotspot_item'
										. " sc_hotspot_item_symbol_{$item['spot_symbol']}"
										. " sc_hotspot_item_open_{$item['open']}"
										. " sc_hotspot_item_visible_{$item['spot_visible']}"
										. ( (int)$item['opened'] > 0 ? ' sc_hotspot_item_opened' : '' ),
									'sc_hotspot',
									$item
								);
							?>"
							style="<?php
								echo 'left:' . esc_attr( $item['spot_x'] ) . ( strpos( $item['spot_x'], '%' ) === false ? '%' : '' ) . ';'
									. 'top:' . esc_attr( $item['spot_y'] ) . ( strpos( $item['spot_y'], '%' ) === false ? '%' : '' ) . ';';
							?>"
							tabindex="-1"<?php
							trx_addons_sc_show_attributes( 'sc_hotspot', $args, 'sc_item_wrapper' );
					?>>
						<span class="sc_hotspot_item_sonar"<?php
							if ( ! empty( $item['spot_sonar_color'] ) ) {
								echo ' style="background-color: ' . esc_attr( $item['spot_sonar_color'] ) . '"';
							}
						?>></span>
						<?php

						$icon = '';
						$icon_text = '';
						$img = '';
						$svg = '';

						if ( empty( $item['icon_type'] ) ) {
							$item['icon_type'] = '';
						}

						if ( trx_addons_is_off( $item['spot_symbol'] ) ) {
							$item['icon_type'] = 'none';

						} else if ( $item['spot_symbol'] == 'custom' ) {
							if ( ! empty( $item['spot_char'] ) ) {
								$item['spot_char'] = trim( $item['spot_char'] );
							}
							if ( ! empty( $item['spot_char'] ) ) {
								$item['icon_type'] = 'custom';
								$icon = sprintf( 'char-%s', $item['spot_char'] );
								$icon_text = $item['spot_char'];
							} else {
								$item['icon_type'] = 'none';
							}

						} else if ( $item['spot_symbol'] == 'number' ) {
							$numbers++;
							$item['icon_type'] = 'number';
							$icon = sprintf( 'number-%d', $numbers );
							$icon_text = $numbers;

						} else if ( $item['spot_symbol'] == 'icon' ) {
							$icon = ! empty( $item['icon_type'] ) && ! empty( $item['icon_' . $item['icon_type']] ) && $item['icon_' . $item['icon_type']] != 'empty'
											? $item['icon_' . $item['icon_type']] 
											: '';
							if ( ! empty( $icon ) ) {
								if ( strpos( $icon_present, $item['icon_type'] ) === false ) {
									$icon_present .= ( ! empty( $icon_present ) ? ',' : '') . $item['icon_type'];
								}
							} else {
								if ( ! empty( $item['icon'] ) && strtolower( $item['icon'] ) != 'none' ) {
									$icon = $item['icon'];
								}
							}
							if ( empty( $icon ) ) {
								$icon = 'none';
							}
							if ( trx_addons_is_url( $icon ) ) {
								if ( strpos( $icon, '.svg' ) !== false ) {
									$svg = $icon;
									$item['icon_type'] = 'svg';
								} else {
									$img = $icon;
									$item['icon_type'] = 'images';
								}
								$icon = basename( $icon );
							}

						} else if ( $item['spot_symbol'] == 'image' ) {
							if ( ! empty( $item['spot_image'] ) ) {
								$img = trx_addons_get_attachment_url( $item['spot_image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('tiny'), 'hotspot-default-spot-image') );
								$item['icon_type'] = 'images';
								$icon = basename( $img );
							}
						}
						// Icon
						echo empty( $item['link'] ) || $item['open'] == 'click'
								? '<span'
								: '<a href="' . esc_url( $item['link'] ) . '"';
						?> class="sc_hotspot_item_icon sc_hotspot_item_icon_type_<?php echo esc_attr( $item['icon_type'] ) . ' ' . esc_attr( $icon ); ?>"<?php
								if ( ! empty( $item['spot_bg_color'] ) ) {
									echo ' style="background-color:' . esc_attr( $item['spot_bg_color'] ) . ';"';
								}
						?>><?php
							if ( ! empty( $svg ) ) {
								?><span class="sc_icon_type_<?php echo esc_attr( $item['icon_type'] ) . ' ' . esc_attr($icon); ?>"><?php
									trx_addons_show_layout( trx_addons_get_svg_from_file( $svg ) );
								?></span><?php
							} else if ( ! empty( $img ) ) {
								$attr = trx_addons_getimagesize( $img );
								?><img class="sc_icon_as_image"
									src="<?php echo esc_url($img); ?>"
									alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php
									echo ( ! empty( $attr[3] ) ? ' ' . trim( $attr[3] ) : '');
								?>><?php
							} else {
								?><span class="sc_icon_type_<?php echo esc_attr( $item['icon_type'] ) . ' ' . esc_attr( $icon ); ?>"<?php
									if ( ! empty( $item['spot_color'] ) ) {
										echo ' style="color: ' . esc_attr( $item['spot_color'] ) . '"';
									}
								?>><?php
									if ( ! empty( $icon_text ) ) {
										echo esc_html( $icon_text );
									}
								?></span><?php
							}
						echo empty( $item['link'] ) || $item['open'] == 'click'
								? '</span>'
								: '</a>';
						?>

						<div class="sc_hotspot_item_popup <?php
							echo esc_attr( trx_addons_get_responsive_classes( 'sc_hotspot_item_popup_', $item, 'position', 'bc' ) );
						?> sc_hotspot_item_popup_align_<?php
							echo esc_attr( ! empty( $item['align'] ) ? $item['align'] : 'center' );
						?>"><?php
							// Add button 'Close' to the clickable items
							if ( $item['open'] == 'click' ) {
								?><span class="sc_hotspot_item_popup_close trx_addons_button_close"><span class="trx_addons_button_close_icon"></span></span><?php
							}
							if ( ! empty( $item['image'] ) ) {
								$image = '';
								if ( is_numeric( $item['image'] ) && (int) $item['image'] > 0 ) {
									$image = wp_get_attachment_image( $item['image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('small'), 'hotspot-default-item-image'), false );
								} else {
									$image = trx_addons_get_attachment_url( $item['image'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('small'), 'hotspot-default-item-image') );
									if ( ! empty( $image ) ) {
										$image = '<img src="' . esc_url( $image ) . '" alt="' . esc_attr__( 'Hotspot image', 'trx_addons' ) . '" />';
									}
								}
								if ( ! empty( $image ) ) {
									?><div class="sc_hotspot_item_image"><?php
										trx_addons_show_layout( $image );
									?></div><?php
								}
							}
							if ( ! empty( $item['subtitle'] ) ) {
								$item['subtitle'] = explode( '|', $item['subtitle'] );
								?><h6 class="sc_hotspot_item_subtitle"><?php
									foreach ( $item['subtitle'] as $str ) {
										?><span><?php echo wp_kses( $str, 'trx_addons_kses_content' ); ?></span><?php
									}
								?></h6><?php
							}
							if ( ! empty( $item['title'] ) ) {
								$item['title'] = explode( '|', $item['title'] );
								?><h5 class="sc_hotspot_item_title"><?php
									foreach ( $item['title'] as $str ) {
										?><span><?php echo wp_kses( $str, 'trx_addons_kses_content' ); ?></span><?php
									}
								?></h5><?php
							}
							if ( ! empty( $item['price'] ) ) {
								?><div class="sc_hotspot_item_price"><?php
									echo wp_kses( $item['price'], 'trx_addons_kses_content' );
								?></div><?php
							}
							if ( ! empty( $item['description'] ) ) {
								$item['description'] = explode('|', str_replace( "\n", '|', $item['description'] ) );
								?><div class="sc_hotspot_item_description"><?php
									foreach ( $item['description'] as $str ) {
										?><span><?php trx_addons_show_layout( $str ); ?></span><?php
									}
								?></div><?php
							}
							if ( ! empty( $item['link'] ) ) {
								?><a href="<?php echo esc_url( $item['link'] ); ?>" class="<?php
									if ( ! empty( $item['link_text'] ) ) {
										echo esc_attr( apply_filters( 'trx_addons_filter_sc_item_link_classes', 'sc_hotspot_item_link sc_button sc_button_size_small', 'sc_hotspot', $args, $item ) );
									} else {
										echo 'sc_hotspot_item_link_cover';
									}
									?>"<?php
										if ( ! empty( $item['new_window'] ) || ! empty( $item['link_extra']['is_external'] ) ) echo ' target="_blank"';
										if ( ! empty( $item['nofollow'] ) || ! empty( $item['link_extra']['nofollow'] ) ) echo ' rel="nofollow"';
								?>><?php 
									if ( ! empty( $item['link_text'] ) ) {
										echo esc_html( $item['link_text'] ); 
									}
								?></a><?php
							}
						?></div>

					</div><?php
				}

			?></div><?php

			trx_addons_sc_show_links('sc_hotspot', $args);

		?></div><?php

		trx_addons_load_icons($icon_present);
	}
}
