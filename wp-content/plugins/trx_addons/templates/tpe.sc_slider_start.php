<?php
/**
 * The template to display start of the slider's wrap for some shortcodes
 * on the Elementor's preview page
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_show_slider_wrap'));
?><#
// Default values
settings = trx_addons_array_merge({
									'slider_effect':			'slide',
									'slider_controls':			'none',
									'slider_pagination':		'none',
									'slider_pagination_type':	'bullets',
									'slides_space':				{'size': 0},
									'slides_centered':			'',
									'slides_overflow':			'',
                                    'slider_mouse_wheel':		'',
                                    'slider_free_mode':			'',
                                    'slider_autoplay':			'1',
                                    'slider_loop':				'1',
									'columns': 					{'size': 1}
									}, settings);
// Check values
if (settings.slides_space.size == '')	settings.slides_space.size = 0;
if (settings.columns.size < 1)			settings.columns.size = 1;

print('<div' + (settings._element_id!='' ? ' id="' + settings._element_id + '_outer"' : '')
			+ ' class="<?php echo esc_attr($sc); ?>_slider sc_item_slider slider_swiper_outer slider_outer'
					+ ' slider_outer_' + (trx_addons_is_off(settings.slider_controls) 
											? 'nocontrols'
											: 'controls slider_outer_controls_' + settings.slider_controls)
					+ ' slider_outer_' + (trx_addons_is_off(settings.slider_pagination) 
											? 'nopagination'
											: 'pagination slider_outer_pagination_' + settings.slider_pagination_type + ' slider_outer_pagination_pos_' + settings.slider_pagination)
					+ ' slider_outer_' + (trx_addons_is_off(settings.slides_centered) 
											? 'nocentered'
											: 'centered')
					+ ' slider_outer_overflow_' + (trx_addons_is_off(settings.slides_overflow) 
											? 'hidden'
											: 'visible')
					+ ' slider_outer_' + (settings.columns.size > 1 
											? 'multi' 
											: 'one')
					+ '">'
						+ '<div' + (settings._element_id!='' ? ' id="' + settings._element_id + '_swiper"' : '')
							+ ' class="slider_container swiper-slider-container slider_swiper slider_noresize'
								+ ' slider_' + (trx_addons_is_off(settings.slider_controls)
												? 'nocontrols'
												: 'controls slider_controls_' + settings.slider_controls)
								+ ' slider_' +  (trx_addons_is_off(settings.slider_pagination) 
												? 'nopagination'
												: 'pagination slider_pagination_' + settings.slider_pagination_type + ' slider_pagination_pos_' + settings.slider_pagination)
								+ ' slider_' + (trx_addons_is_off(settings.slides_centered) 
												? 'nocentered'
												: 'centered')
								+ ' slider_overflow_' + (trx_addons_is_off(settings.slides_overflow) 
												? 'hidden'
												: 'visible')
								+ ' slider_' + (settings.columns.size > 1 
												? 'multi' 
												: 'one')
								+ '"'
							+ (settings.slider_effect
								? ' data-effect="' + settings.slider_effect + '"' 
								: '')
							+ (settings.columns.size > 1
								? ' data-slides-per-view="' + settings.columns.size + '"' 
								: '')
							+ (settings.slides_space.size > 0 
								? ' data-slides-space="' + settings.slides_space.size + '"' 
								: '')
							+ ' data-slides-centered="' + (settings.slides_centered > 0 ? '1' : '0') + '"'
							+ ' data-slides-overflow="' + (settings.slides_overflow > 0 ? '1' : '0') + '"'
							+ ' data-mouse-wheel="' + (settings.slider_mouse_wheel > 0 ? '1' : '0') + '"'
							+ ' data-autoplay="' + (settings.slider_autoplay > 0 ? '1' : '0') + '"'
							+ ' data-loop="' + (settings.slider_loop > 0 ? '1' : '0') + '"'
							+ ' data-free-mode="' + (settings.slider_free_mode > 0 ? '1' : '0') + '"'
							+ ' data-slides-min-width="' + (settings.slides_min_width > 0 ? settings.slides_min_width : trx_addons_apply_filters( 'trx_addons_filter_slider_slide_width', 150 )) + '"'
							+ ' data-pagination="' + settings.slider_pagination_type + '"'
							+ '>'
								+ '<div class="slides slider-wrapper swiper-wrapper sc_item_columns_' + settings.columns.size + '">');
#>