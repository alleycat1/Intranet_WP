<?php
/**
 * The style "default" of the Expand/Collapse Button
 *
 * @package ThemeREX Addons
 * @since v2.6.1
 */

$args = get_query_var('trx_addons_args_trx_expcol_button');

?><a href="#" class="trx_expcol_button<?php
	if ( ! empty( $args['trx_expcol_collapsed_bg_color'] ) || ! empty( $args['trx_expcol_expanded_bg_color'] ) ) {
		?> trx_expcol_button_with_bg<?php
	}
	?>"
	data-trx-expcol-collapsed-title="<?php echo esc_attr( $args['trx_expcol_collapsed_title'] ); ?>"
	data-trx-expcol-expanded-title="<?php echo esc_attr( $args['trx_expcol_expanded_title'] ); ?>"
><?php

	$icon_present = '';

	foreach ( array( 'collapsed', 'expanded' ) as $state ) {
		if ( empty( $args["trx_expcol_{$state}_icon_type"] ) ) $args["trx_expcol_{$state}_icon_type"] = '';
		$icon = ! empty( $args["trx_expcol_{$state}_icon_type"] ) && ! empty( $args["trx_expcol_{$state}_icon_" . $args["trx_expcol_{$state}_icon_type"]] ) && $args["trx_expcol_{$state}_icon_" . $args["trx_expcol_{$state}_icon_type"]] != 'empty' 
					? $args["trx_expcol_{$state}_icon_" . $args["trx_expcol_{$state}_icon_type"]] 
					: '';
		if ( ! empty( $icon ) ) {
			if ( strpos( $icon_present, $args["trx_expcol_{$state}_icon_type"] ) === false )
				$icon_present .= ( ! empty( $icon_present ) ? ',' : '') . $args["trx_expcol_{$state}_icon_type"];
		} else {
			if ( ! empty( $args["trx_expcol_{$state}_icon"] ) && strtolower( $args["trx_expcol_{$state}_icon"] ) != 'none' ) {
				$icon = $args["trx_expcol_{$state}_icon"];
			}
		}
		$args["trx_expcol_{$state}_icon"] = $icon;

		if ( ! empty( $icon ) ) {
			?><span class="trx_expcol_button_icon trx_expcol_button_icon_<?php echo esc_attr( $state ); ?>"><?php

				if ( trx_addons_is_url( $icon ) ) {
					if ( strpos( $icon, '.svg') !== false ) {
						trx_addons_show_layout( trx_addons_get_svg_from_file( $icon ) );
					} else {
						$attr = trx_addons_getimagesize( $icon );
						?><img class="sc_icon_as_image" src="<?php echo esc_url( $icon ); ?>" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php echo ( ! empty( $attr[3] ) ? ' ' . trim( $attr[3] ) : '' ); ?>><?php
					}
				} else {
					?><span class="<?php echo esc_attr( $icon ); ?>"></span><?php
				}

			?></span><?php
		}
	}
	
	if ( ! empty( $args["trx_expcol_expanded_title"] ) || ! empty( $args["trx_expcol_collapsed_title"] ) ) {
		?><span class="trx_expcol_button_title"><?php
			$state = $args["trx_expcol_state"];
			if ( ! empty( $args["trx_expcol_{$state}_title"] ) ) {
				echo esc_html( $args["trx_expcol_{$state}_title"] );
			}
		?></span><?php
	}

?></a><?php

trx_addons_load_icons( $icon_present );
