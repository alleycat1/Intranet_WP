<?php
/**
 * The template to display end of the slider's wrap for some shortcodes
 *
 * @package ThemeREX Addons
 * @since v1.6.20
 */

extract(get_query_var('trx_addons_args_sc_show_slider_wrap'));

if (empty($args['slider_controls'])) $args['slider_controls'] = 'none';
if (empty($args['slider_pagination'])) $args['slider_pagination'] = 'none';
if (empty($args['slider_pagination_type'])) $args['slider_pagination_type'] = 'bullets';

$pagination = '<div class="slider_pagination_wrap swiper-pagination">'
				. (!empty($args['slider_pagination_buttons']) ? $args['slider_pagination_buttons'] : '')
			. '</div>';
if ( in_array($args['slider_pagination'], apply_filters('trx_addons_filter_slider_pagination_inside', array('left', 'right'), $args))
	&& $args['slider_pagination_type'] != 'fraction'
) {
	trx_addons_show_layout($pagination);
}	

$controls = '<div class="slider_controls_wrap">'
				. '<a class="slider_prev swiper-button-prev" href="#"></a>'
				. '<a class="slider_next swiper-button-next" href="#"></a>'
			. '</div>';
if ( in_array($args['slider_controls'], apply_filters('trx_addons_filter_slider_controls_inside', array('side'), $args)) ) {
	trx_addons_show_layout($controls);
}

?></div><?php	//slider-swiper

if ( in_array($args['slider_pagination'], apply_filters('trx_addons_filter_slider_pagination_outside', array('bottom', 'bottom_outside'), $args))
	|| $args['slider_pagination_type'] == 'fraction'
) {
	trx_addons_show_layout($pagination);
}

if ( in_array($args['slider_controls'], apply_filters('trx_addons_filter_slider_controls_outside', array('outside', 'top', 'bottom'), $args)) ) {
	trx_addons_show_layout($controls);
}

?></div><?php	//slider-swiper-outer

// Enable lazy load again
if ( apply_filters( 'trx_addons_filter_disable_wp_lazy_load_in_slider', true ) ) {
	if ( isset( $GLOBALS['TRX_ADDONS_STORAGE']['lazy_load_is_off'] ) && ! $GLOBALS['TRX_ADDONS_STORAGE']['lazy_load_is_off'] ) {
		unset( $GLOBALS['TRX_ADDONS_STORAGE']['lazy_load_is_off'] );
		trx_addons_lazy_load_on();
	}
}
