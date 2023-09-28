<?php
/**
 * The style "counter" of the Skills
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_skills');

$icon_present = '';
$data = '';
$max = max(0, (float) $args['max']);

foreach ($args['values'] as $v) {
	$value = (float) $v['value'];
	if ( $max < $value ) {
		$max = $value;
	}
}

foreach ($args['values'] as $v) {
	if (empty($v['icon_type'])) $v['icon_type'] = '';
	$icon = ! empty($v['icon_type']) && ! empty($v['icon_' . $v['icon_type']]) && $v['icon_' . $v['icon_type']] != 'empty' && ! trx_addons_is_off($v['icon_' . $v['icon_type']])
				? $v['icon_' . $v['icon_type']] 
				: '';
	if (!empty($icon)) {
		if (strpos($icon_present, $v['icon_type'])===false)
			$icon_present .= (!empty($icon_present) ? ',' : '') . $v['icon_type'];
	} else {
		if (!empty($v['icon']) && strtolower($v['icon'])!='none') $icon = $v['icon'];
	}
	$img = $svg = '';
	if (!empty($icon) && trx_addons_is_url($icon)) {
		if (strpos($icon, '.svg') !== false) {
			$svg = $icon;
			$v['icon_type'] = 'svg';
		} else {
			$img = $icon;
			$v['icon_type'] = 'images';
		}
		$icon = basename($icon);
	}
	$ed = $v['units'];				// substr($v['value'], -1)=='%' ? '%' : '';
	$value = (float) $v['value'];	// (float) str_replace('%', '', $v['value']);
	$percent = round($value / $max * 100);
	$start = 0;
	$stop = $value;
	$steps = 100;
	$step = $value / $steps;
	$animation = max(0, isset( $args['duration'] ) ? $args['duration'] : apply_filters( 'trx_addons_filter_skills_duration', 1500 ));
	if ( $animation > 0 ) {
		// All counters have an equal duration of the animation
		$speed = round( $animation / $steps  );
	} else {
		// Counters have a different (random) duration of the animation
		$speed = mt_rand(5, 20);
		$animation = round($steps * $speed);	
	}
	$item_color = ! empty($v['color']) ? $v['color'] : ( ! empty($args['color']) ? $args['color'] : '');
	$icon_color = ! empty($v['icon_color']) ? $v['icon_color'] : ( ! empty($args['icon_color']) ? $args['icon_color'] : '');
	$icon_color_class = ! empty( $icon_color )
							? ' ' . trx_addons_add_inline_css_class( 'color:' . esc_attr( $icon_color ), ':before' )
							: '';
	$title_color = ! empty($v['item_title_color']) ? $v['item_title_color'] : ( ! empty($args['item_title_color']) ? $args['item_title_color'] : '');
	if ( ! empty( $v['image'] ) ) {
		$v['image'] = trx_addons_get_attachment_url( $v['image'], apply_filters( 'trx_addons_filter_thumb_size', trx_addons_get_thumb_size('tiny'), 'skills-default' ) );
		$attr = trx_addons_getimagesize( $v['image'] );
	}

	$data .= ( $args['columns'] > 0
				? '<div class="sc_skills_column ' . esc_attr( trx_addons_get_column_class( 1, $args['columns'], ! empty( $args['columns_tablet'] ) ? $args['columns_tablet'] : '', ! empty( $args['columns_mobile'] ) ? $args['columns_mobile'] : '' ) ) . '">'
				: ''
				)
			. '<div class="sc_skills_item_wrap' . ( ! empty( $args['icon_position'] ) ? ' sc_skills_item_icon_position_' . esc_attr( $args['icon_position'] ) : '' ) . '">'
				. '<div class="' . apply_filters( 'trx_addons_filter_sc_item_classes', 'sc_skills_item', 'sc_skills', $v, $args ) . '">'
					. ( ! empty( $v['char'] )
						? '<div class="sc_skills_icon sc_skills_char" data-char="' . esc_attr( $v['char'] ) . '"'
								. ( ! empty( $icon_color ) ? ' style="color: ' . esc_attr( $icon_color ) . '"' : '' )
							. '>'
							. '<span data-char="' . esc_attr( $v['char'] ) . '"'
								. ( ! empty( $icon_color ) ? ' style="color: ' . esc_attr( $icon_color ) . '"' : '' )
							. '></span>'
							. '</div>'
						: ''
						)
					. ( ! empty( $v['image'] )
						? '<div class="sc_skills_image">'
							. '<img src="' . esc_url( $v['image'] ) . '" alt="' . esc_attr__('Icon', 'trx_addons') . '"'
								. ( ! empty( $attr[3] ) ? ' ' . trim( $attr[3] ) : '' ) . '>'
							. '</div>'
						: ''
						)
					. ( ! empty( $icon ) && ! trx_addons_is_off( $icon )
						? '<div class="sc_skills_icon sc_icon_type_' . esc_attr($v['icon_type']) . ' ' . esc_attr($icon) . esc_attr($icon_color_class) . '">'
								. ( ! empty($svg)
									? trx_addons_get_svg_from_file($svg)
									: '')
								. ( ! empty($img)
									? '<img class="sc_icon_as_image" src="'.esc_url($img).'" alt="'.esc_attr__('Icon', 'trx_addons').'">'
									: '')
								. '</div>'
						: ''
						)
					. '<div class="sc_skills_total"'
						. ' data-start="'.esc_attr($start).'"'
						. ' data-stop="'.esc_attr($stop).'"'
						. ' data-step="'.esc_attr($step).'"'
						. ' data-max="'.esc_attr($max).'"'
						. ' data-speed="'.esc_attr($speed).'"'
						. ' data-duration="'.esc_attr($animation).'"'
						. ' data-ed="'.esc_attr($ed).'"'
						. ' data-style="'.esc_attr($args['style']).'"'
						. ( ! empty($item_color) ? ' style="color: ' . esc_attr($item_color) . '"' : '')
						. '>'
						. ( $args['style'] == 'odometer' ? trx_addons_sc_skills_split_by_digits( $start, $stop, $ed ) : ( $start . $ed ) )
					. '</div>'
				. '</div>'
				. ( ! empty($v['title'])
					? '<div class="sc_skills_item_title"'
						. ( ! empty( $title_color ) ? ' style="color: ' . esc_attr( $title_color ) . '"' : '' )
						. '>'
							. nl2br( str_replace( '|', "\n", esc_html( $v['title'] ) ) )
						. '</div>'
					: ''
					)
			. '</div>'
		. ( $args['columns'] > 0 ? '</div>' : '' );
}

?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
		class="sc_skills sc_skills_counter sc_skills_counter_style_<?php
			echo esc_attr( $args['style'] );
			echo ! empty($args['class']) ? ' '.esc_attr($args['class']) : '';
		?>"
		<?php echo !empty($args['css']) ? ' style="'.esc_attr($args['css']).'"' : ''; ?>
		data-type="counter"<?php
		trx_addons_sc_show_attributes('sc_skills', $args, 'sc_wrapper');
?>><?php

		trx_addons_sc_show_titles('sc_skills', $args);

		if ($args['columns'] > 1) {
			?><div class="sc_skills_columns sc_item_columns <?php
				echo esc_attr(trx_addons_get_columns_wrap_class())
					. ' columns_padding_bottom'
					. esc_attr( trx_addons_add_columns_in_single_row( $args['columns'], $args['values'] ) );
			?>"><?php
		}

		trx_addons_show_layout($data);

		if ($args['columns'] > 1) {
			?></div><?php
		}

		trx_addons_sc_show_links('sc_skills', $args);
		
?></div><?php

trx_addons_load_icons($icon_present);
