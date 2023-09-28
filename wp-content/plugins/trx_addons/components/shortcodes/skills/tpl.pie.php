<?php
/**
 * The style "pie" of the Skills
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_skills');

$icon_present = '';
$legend = '';
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
	$icon = !empty($v['icon_type']) && !empty($v['icon_' . $v['icon_type']]) && $v['icon_' . $v['icon_type']] != 'empty' && ! trx_addons_is_off($v['icon_' . $v['icon_type']])
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
	$ed = $v['units'];		//substr($v['value'], -1)=='%' ? '%' : '';
	$value = (float) $v['value'];	//(float) str_replace('%', '', $v['value']);
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
	$item_color = !empty($v['color']) ? $v['color'] : ( !empty($args['color']) ? $args['color'] : apply_filters('trx_addons_filter_get_theme_accent_color', '#efa758') );
	$icon_color = ! empty($v['icon_color']) ? $v['icon_color'] : ( ! empty($args['icon_color']) ? $args['icon_color'] : '');
	$title_color = ! empty($v['item_title_color']) ? $v['item_title_color'] : ( ! empty($args['item_title_color']) ? $args['item_title_color'] : '');
	$bg_color = !empty($args['bg_color']) ? $args['bg_color'] : '#f7f7f7';
	$border_color = !empty($args['border_color']) ? $args['border_color'] : '';
	$cutout = empty($args['cutout']) ? 0 : $args['cutout'];
	if ($args['compact'] == 1) {
		$legend .= '<div class="' . apply_filters( 'trx_addons_filter_sc_item_classes', 'sc_skills_legend_item', 'sc_skills', $v, $args ) . '">'
						. '<span class="sc_skills_legend_marker" style="background-color:'.esc_attr($item_color).'"></span>'
						. '<span class="sc_skills_legend_title">' . esc_html($v['title']) . '</span>'
						. '<span class="sc_skills_legend_value">' . esc_html($v['value']) . '</span>'
					. '</div>';
		$data .= '<div class="pie"'
					. ' data-start="'.esc_attr($start).'"'
					. ' data-stop="'.esc_attr($stop).'"'
					. ' data-step="'.esc_attr($step).'"'
					. ' data-steps="'.esc_attr($steps).'"'
					. ' data-max="'.esc_attr($max).'"'
					. ' data-speed="'.esc_attr($speed).'"'
					. ' data-duration="'.esc_attr($animation).'"'
					. ' data-color="'.esc_attr($item_color).'"'
					. ' data-bg_color="'.esc_attr($bg_color).'"'
					. ' data-border_color="'.esc_attr($border_color).'"'
					. ' data-cutout="'.esc_attr($cutout).'"'
					. ' data-easing="easeOutCirc"'
					. ' data-ed="'.esc_attr($ed).'"'
			. '>'
				. '<input type="hidden" class="text" value="'.esc_attr($v['title']).'" />'
				. '<input type="hidden" class="percent" value="'.esc_attr($percent).'" />'
				. '<input type="hidden" class="color" value="'.esc_attr($item_color).'" />'
			. '</div>';
	} else {
		$item_id = trx_addons_generate_id( 'sc_skills_canvas_' );
		$data .= ($args['columns'] > 0 ? '<div class="sc_skills_column '.esc_attr(trx_addons_get_column_class(1, $args['columns'], !empty($args['columns_tablet']) ? $args['columns_tablet'] : '', !empty($args['columns_mobile']) ? $args['columns_mobile'] : '')).'">' : '')
				. '<div class="sc_skills_item_wrap">'
					. '<div class="' . apply_filters( 'trx_addons_filter_sc_item_classes', 'sc_skills_item', 'sc_skills', $v, $args ) . '">'
						. '<canvas id="'.esc_attr($item_id).'"></canvas>'
						. '<div class="sc_skills_total"'
							. ' data-start="'.esc_attr($start).'"'
							. ' data-stop="'.esc_attr($stop).'"'
							. ' data-step="'.esc_attr($step).'"'
							. ' data-steps="'.esc_attr($steps).'"'
							. ' data-max="'.esc_attr($max).'"'
							. ' data-speed="'.esc_attr($speed).'"'
							. ' data-duration="'.esc_attr($animation).'"'
							. ' data-color="'.esc_attr($item_color).'"'
							. ' data-bg_color="'.esc_attr($bg_color).'"'
							. ' data-border_color="'.esc_attr($border_color).'"'
							. ' data-cutout="'.esc_attr($cutout).'"'
							. ' data-easing="easeOutCirc"'
							. ' data-ed="'.esc_attr($ed).'"'
							. ' data-style="'.esc_attr($args['style']).'"'
							. ( $icon_color ? ' style="color:' . esc_attr( $icon_color ) . ';"' : '' )
						. '>'
							. ( $args['style'] == 'odometer' ? trx_addons_sc_skills_split_by_digits( $start, $stop, $ed ) : ( $start . $ed ) )
						. '</div>'
					. '</div>'
					. (!empty($v['title']) 
							? '<div class="sc_skills_item_title"'
								. ( ! empty( $title_color ) ? ' style="color: ' . esc_attr( $title_color ) . '"' : '' )
								. '>'
									. ( ! empty( $icon ) && ! trx_addons_is_off( $icon )
										? '<span class="sc_skills_icon sc_icon_type_' . esc_attr($v['icon_type']) . ' ' . esc_attr($icon) . '">'
												. ( ! empty($svg)
													? trx_addons_get_svg_from_file($svg)
													: '')
												. ( ! empty($img)
													? '<img class="sc_icon_as_image" src="'.esc_url($img).'" alt="'.esc_attr__('Icon', 'trx_addons').'">'
													: '')
												. '</span>'
										: '') 
									. nl2br(str_replace('|', "\n", esc_html($v['title'])))
								. '</div>' 
							: '')
				. '</div>'
			. ($args['columns'] > 0 ? '</div>' : '');
	}
}

?><div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
		class="sc_skills sc_skills_pie sc_skills_compact_<?php
				echo esc_attr($args['compact']>0 ? 'on' : 'off');
				echo ' sc_skills_counter_style_' . esc_attr( $args['style'] );
				echo ! empty($args['class']) ? ' '.esc_attr($args['class']) : '';
		?>"
		<?php echo !empty($args['css']) ? ' style="'.esc_attr($args['css']).'"' : ''; ?>
		data-type="pie"<?php
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
		if ($args['compact']==1) {
			?><div class="sc_item_content sc_skills_content">
				<div class="sc_skills_legend"><?php trx_addons_show_layout($legend); ?></div>
				<div<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'_pie_item"'; ?> class="sc_skills_item">
					<canvas<?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'_pie"'; ?> class="sc_skills_pie_canvas"></canvas>
					<div class="sc_skills_data" style="display:none;"><?php trx_addons_show_layout($data); ?></div>
				</div>
			</div><?php
		} else {
			trx_addons_show_layout($data);
		}

		if ($args['columns'] > 1) {
			?></div><?php
		}

		trx_addons_sc_show_links('sc_skills', $args);

?></div><?php

trx_addons_load_icons($icon_present);
