<?php
/**
 * The template to display start of the slider's wrap for some shortcodes
 *
 * @package ThemeREX Addons
 * @since v1.6.20
 */

extract(get_query_var('trx_addons_args_sc_show_slider_wrap'));

if (!isset($args['slider_effect'])) $args['slider_effect'] = 'slide';
if (empty($args['slider_controls'])) $args['slider_controls'] = 'none';
if (empty($args['slider_pagination'])) $args['slider_pagination'] = 'none';
if (empty($args['slider_pagination_type'])) $args['slider_pagination_type'] = 'bullets';
if (empty($args['slides_space'])) $args['slides_space'] = 0;
if (empty($args['slides_centered'])) $args['slides_centered'] = '';
if (empty($args['slides_overflow'])) $args['slides_overflow'] = '';
if (empty($args['slider_mouse_wheel'])) $args['slider_mouse_wheel'] = '';
if (!isset($args['slider_autoplay'])) $args['slider_autoplay'] = '1';
if (!isset($args['slider_loop'])) $args['slider_loop'] = '1';
if (empty($args['slider_free_mode'])) $args['slider_free_mode'] = '';
if (empty($args['columns'])) $args['columns'] = 1;

// Load slider scripts
trx_addons_enqueue_slider();

// Disable lazy load in slider
if ( apply_filters( 'trx_addons_filter_disable_wp_lazy_load_in_slider', true ) ) {
	$GLOBALS['TRX_ADDONS_STORAGE']['lazy_load_is_off'] = trx_addons_lazy_load_is_off();
	if ( ! $GLOBALS['TRX_ADDONS_STORAGE']['lazy_load_is_off'] ) {
		trx_addons_lazy_load_off();
	}
}

?><div<?php
	   if (!empty($args['id'])) echo ' id="' . esc_attr($args['id']) . '_outer"';
	   trx_addons_sc_show_attributes( $sc, $args, 'sc_slider_outer_wrapper');
	   ?> class="<?php echo esc_attr($sc); ?>_slider sc_item_slider slider_swiper_outer slider_outer<?php
			echo ' slider_outer_' . esc_attr(trx_addons_is_off($args['slider_controls']) 
						? 'nocontrols'
						: 'controls slider_outer_controls_' . esc_attr($args['slider_controls']))
				. ' slider_outer_' . esc_attr(trx_addons_is_off($args['slider_pagination']) 
						? 'nopagination'
						: 'pagination slider_outer_pagination_'.esc_attr($args['slider_pagination_type']).' slider_outer_pagination_pos_'.esc_attr($args['slider_pagination']))
				. ' slider_outer_' . esc_attr(trx_addons_is_off($args['slides_centered']) 
						? 'nocentered'
						: 'centered')
				. ' slider_outer_overflow_' . esc_attr(trx_addons_is_off($args['slides_overflow']) 
						? 'hidden'
						: 'visible')
				. ' slider_outer_' . esc_attr($args['columns']>1 
						? 'multi' 
						: 'one');
		?>">
	<div<?php
		if (!empty($args['id'])) echo ' id="' . esc_attr($args['id']) . '_swiper"';
		trx_addons_sc_show_attributes( $sc, $args, 'sc_slider_wrapper');
		?> class="slider_container swiper-slider-container slider_swiper slider_noresize<?php
			echo ' slider_' . esc_attr(trx_addons_is_off($args['slider_controls']) 
						? 'nocontrols'
						: 'controls slider_controls_' . esc_attr($args['slider_controls']))
				. ' slider_' . esc_attr(trx_addons_is_off($args['slider_pagination']) 
						? 'nopagination'
						: 'pagination slider_pagination_'.esc_attr($args['slider_pagination_type']).' slider_pagination_pos_'.esc_attr($args['slider_pagination']))
				. ' slider_' . esc_attr(trx_addons_is_off($args['slides_centered']) 
						? 'nocentered'
						: 'centered')
				. ' slider_overflow_' . esc_attr(trx_addons_is_off($args['slides_overflow']) 
						? 'hidden'
						: 'visible')
				. ' slider_' . esc_attr($args['columns']>1 
						? 'multi' 
						: 'one');
			?>"<?php
			echo ($args['columns'] > 1 
					? ' data-slides-per-view="' . esc_attr($args['columns']) . '"' 
					: '')
				. ($args['slides_space'] > 0 
					? ' data-slides-space="' . esc_attr($args['slides_space']) . '"' 
					: '')
				. ' data-effect="'.esc_attr($args['slider_effect']).'"'
				. ' data-slides-min-width="' . (!empty($args['slides_min_width']) ? $args['slides_min_width'] : apply_filters( 'trx_addons_filter_slider_slide_width', 150 ) ) . '"'
				. ' data-pagination="'.esc_attr($args['slider_pagination_type']).'"'
				. ' data-direction="'.( ! empty($args['slider_direction']) ? esc_attr($args['slider_direction']) : 'horizontal').'"'
				. ' data-mouse-wheel="'.esc_attr(!empty($args['slider_mouse_wheel']) ? '1' : '0').'"'
				. ' data-autoplay="'.esc_attr(!empty($args['slider_autoplay']) ? '1' : '0').'"'
				. ' data-loop="'.esc_attr(!empty($args['slider_loop']) ? '1' : '0').'"'
				. ' data-free-mode="'.esc_attr(!empty($args['slider_free_mode']) ? '1' : '0').'"'
				. ' data-slides-centered="'.esc_attr(!empty($args['slides_centered']) ? '1' : '0').'"'
				. ' data-slides-overflow="'.esc_attr(!empty($args['slides_overflow']) ? '1' : '0').'"';
		?>>
		<div class="slides slider-wrapper swiper-wrapper sc_item_columns_<?php echo esc_attr($args['columns']); ?>">