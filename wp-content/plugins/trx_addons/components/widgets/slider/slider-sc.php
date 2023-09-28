<?php
/**
 * Widget: Posts or Revolution slider (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// trx_widget_slider
//-------------------------------------------------------------
/*
[trx_widget_slider id="unique_id" title="Widget title" engine="revo" alias="home_slider_1"]
	[trx_slide title="Slide title" subtitle="Slide subtitle" link="" video_url="URL to video" video_embed="or HTML-code with iframe"]Slide content[/trx_slide]
	...
[/trx_widget_slider]
*/
if ( !function_exists( 'trx_addons_sc_widget_slider' ) ) {
	function trx_addons_sc_widget_slider($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_slider', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			'title' => '',
			'engine' => 'swiper',
			'slider_id' => '',
			'slider_style' => 'default',
			'slides_per_view' => '1',
			'slides_space' => '0',
			'slides_parallax' => '0',
			'slides_type' => 'bg',
			'slides_ratio' => '16:9',
			"slides_centered" => '0',
			"slides_overflow" => '0',
			"mouse_wheel" => '0',
			"loop" => '1',
			"autoplay" => '1',
			"free_mode" => '0',
			'noresize' => '0',
			'effect' => 'slide',
			'height' => '',
			'alias' => '',
			'post_type' => 'post',
			'taxonomy' => 'category',
			'category' => '0',
			'posts' => '5',
			'speed' => '600',
			'interval' => '7000',
			'titles' => 'center',
			'large' => 0,
			'noswipe' => 0,
			'controls' => 0,
			'controls_pos' => 'side',
			'label_prev' => esc_html__('Prev|PHOTO', 'trx_addons'),				// Label of the 'Prev Slide' button (Modern style)
			'label_next' => esc_html__('Next|PHOTO', 'trx_addons'),				// Label of the 'Next Slide' button (Modern style)
			'pagination' => 0,
			'pagination_type' => 'bullets',
			'pagination_pos' => 'bottom',
			'direction' => 'horizontal',
			'slides' => '',
			'slave_id' => '',
			'controller' => 0,				// Show controller with slides images and title
			'controller_style' => 'default',// Style of controller
			'controller_pos' => 'right',	// left | right | bottom - position of the slider controller
			'controller_controls' => 0, 	// Show arrows in the controller
			'controller_effect' => 'slide',	// slide | fade | cube | coverflow | flip - change slides effect for the controller
			'controller_per_view' => 3, 	// Slides per view in the controller
			'controller_space' => 0, 		// Space between slides in the controller
			'controller_height' => '', 		// Height of the the controller
			))
		);

		global $wp_widget_factory, $TRX_ADDONS_STORAGE;

		if (!is_array($atts['slides']) && function_exists('vc_param_group_parse_atts')) {
			$atts['slides'] = (array) vc_param_group_parse_atts( $atts['slides'] );
		}
		if (is_array($atts['slides'])) {
			if ( count($atts['slides']) == 0
				|| count($atts['slides'][0]) == 0
				|| ( empty($atts['slides'][0]['image'])
					&& empty($atts['slides'][0]['video_url'])
					&& empty($atts['slides'][0]['video_embed']) 
					&& empty($atts['slides'][0]['content'])
					)
			) {
				$atts['slides'] = $TRX_ADDONS_STORAGE['trx_slide_data'] = array();
				$content = do_shortcode($content);
				if (count($TRX_ADDONS_STORAGE['trx_slide_data']) > 0) {
					$atts['slides'] = $TRX_ADDONS_STORAGE['trx_slide_data'];
				}
			}
		}
		if (!empty($atts['slider_id'])) {
			$atts['id'] = $atts['slider_id'];
//		} else if (empty($atts['id'])) {
//			$atts['id'] = trx_addons_generate_id( 'sc_slider_' );
		}
		$type = 'trx_addons_widget_slider';
		$output = '';
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . (!empty($atts['id']) ? ' id="'.esc_attr($atts['id']).'"' : '')
							. ' class="widget_area sc_widget_slider' 
								. (trx_addons_exists_vc() ? ' vc_widget_slider wpb_content_element' : '') 
								. (!empty($atts['class']) ? ' ' . esc_attr($atts['class']) : '') 
								. '"'
							. ($atts['css'] ? ' style="'.esc_attr($atts['css']).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args(!empty($atts['id']) ? $atts['id'].'_widget' : 'widget_slider', 'widget_slider') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_slider', $atts, $content);
	}
}


// Add shortcode [trx_widget_slider]
if (!function_exists('trx_addons_sc_widget_slider_add_shortcode')) {
	function trx_addons_sc_widget_slider_add_shortcode() {
		add_shortcode("trx_widget_slider", "trx_addons_sc_widget_slider");
	}
	add_action('init', 'trx_addons_sc_widget_slider_add_shortcode', 20);
}



// trx_slide
//-------------------------------------------------------------
/*
[trx_slide title="Slide title" subtitle="Slide subtitle" link="" video_url="URL to video" video_embed="or HTML-code with iframe"]Slide content[/trx_slide]
*/
if ( !function_exists( 'trx_addons_sc_slide' ) ) {
	function trx_addons_sc_slide($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_slide', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			'title' => '',
			'subtitle' => '',
			'link' => '',
			'image' => '',
			'video_url' => '',
			'video_embed' => '',
			))
		);

		global $TRX_ADDONS_STORAGE;

		$atts['content'] = do_shortcode($content);
		$TRX_ADDONS_STORAGE['trx_slide_data'][] = $atts;

		return '';
	}
}


// Add shortcode [trx_slide]
if (!function_exists('trx_addons_sc_slide_add_shortcode')) {
	function trx_addons_sc_slide_add_shortcode() {
		add_shortcode("trx_slide", "trx_addons_sc_slide");
	}
	add_action('init', 'trx_addons_sc_slide_add_shortcode', 20);
}



// trx_slider_controller
//-------------------------------------------------------------
/*
[trx_slider_controller id="unique_id" slider_id="controlled_slider_id"]
*/
if ( !function_exists( 'trx_addons_sc_slider_controller' ) ) {
	function trx_addons_sc_slider_controller($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_slider_controller', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			'controller_style' => 'thumbs',
			'slider_id' => '',
			'slides_per_view' => '3',
			'slides_space' => '0',
			'effect' => 'slide',
			'direction' => 'horizontal',
			'height' => '',
			'interval' => '7000',
			'controls' => 0,
			))
		);
		$atts['slides_per_view'] = empty($atts['slides_per_view']) ? 1 : max(1, min(8, (int) $atts['slides_per_view']));
		if ( in_array( $atts['effect'], array( 'fade', 'flip', 'cube' ) ) ) {
			$atts['slides_per_view'] = 1;	
		}
		$output = '<div' . (!empty($atts['id']) ? ' id="'.esc_attr($atts['id']).'"' : '')
						. ' class="sc_slider_controller'
							. ' sc_slider_controller_'.esc_attr($atts['controller_style']) 
							. ' sc_slider_controller_'.esc_attr($atts['direction']) 
							. ' sc_slider_controller_height_' . ((int)$atts['height']>0 ? 'fixed' : 'auto')
							. ( ! empty($atts['height']) && $atts['direction'] == 'horizontal'
									? ' ' . trx_addons_add_inline_css_class( '--sc-slider-controller-height:' . esc_attr( trx_addons_prepare_css_value( $atts['height'] ) ) )
									: ''
								)
							. (!empty($atts['class']) ? ' ' . esc_attr($atts['class']) : '') 
							. '"'
						. ' data-slider-id="'.esc_attr($atts['slider_id']).'"'
						. ' data-style="'.esc_attr($atts['controller_style']).'"'
						. ' data-controls="' . esc_attr($atts['controls']>0 ? 1 : 0) . '"'
						. ' data-interval="'.esc_attr($atts['interval']).'"'
						. ' data-effect="'.esc_attr($atts['effect']).'"'
						. ' data-direction="'.esc_attr($atts['direction']=='vertical' ? 'vertical' : 'horizontal').'"'
						. ' data-slides-per-view="'.esc_attr($atts['slides_per_view']).'"'
						. ' data-slides-space="'.esc_attr($atts['slides_space']).'"'
// Moved to CSS var
//						. ((int)$atts['height'] > 0 ? ' data-height="'.esc_attr(trx_addons_prepare_css_value($atts['height'])).'"' : '')
						. ($atts['css'] ? ' style="'.esc_attr($atts['css']).'"' : '')
					. '>'
						. ( empty($atts['slider_id']) ? __( 'Controlled slider ID is not specified', 'trx_addons' ) : '' )
					. '</div>';
		return apply_filters('trx_addons_sc_output', $output, 'trx_slider_controller', $atts, $content);
	}
}


// Add shortcode [trx_slider_controller]
if (!function_exists('trx_addons_sc_slider_controller_add_shortcode')) {
	function trx_addons_sc_slider_controller_add_shortcode() {
		add_shortcode("trx_slider_controller", "trx_addons_sc_slider_controller");
	}
	add_action('init', 'trx_addons_sc_slider_controller_add_shortcode', 20);
}


// trx_slider_controls
//-------------------------------------------------------------
/*
[trx_slider_controls id="unique_id" slider_id="controller_slider_id"]
*/
if ( !function_exists( 'trx_addons_sc_slider_controls' ) ) {
	function trx_addons_sc_slider_controls($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_slider_controls', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			'slider_id' => '',
			'controls_style' => 'default',
			'align' => 'left',
			'hide_prev' => 0,
			'title_prev' => '',
			'hide_next' => 0,
			'title_next' => '',
			'pagination_style' => 'none',
			))
		);
		
		$output = '<div' . (!empty($atts['id']) ? ' id="'.esc_attr($atts['id']).'"' : '')
						. ' class="sc_slider_controls'
							. ' sc_slider_controls_'.esc_attr($atts['controls_style'])
							. ' slider_pagination_style_'.esc_attr($atts['pagination_style'])
							. (!empty($atts['align']) ? ' sc_align_' . esc_attr($atts['align']) : '') 
							. (!empty($atts['class']) ? ' ' . esc_attr($atts['class']) : '') 
							. '"'
						. ' data-slider-id="'.esc_attr($atts['slider_id']).'"'
						. ' data-style="'.esc_attr($atts['controls_style']).'"'
						. ' data-pagination-style="'.esc_attr($atts['pagination_style']).'"'
						. ($atts['css'] ? ' style="'.esc_attr($atts['css']).'"' : '')
					. '>'
						. '<div class="slider_controls_wrap'
							. (empty($atts['hide_prev']) ? ' with_prev' : '')
							. (empty($atts['hide_next']) ? ' with_next' : '')
						.'">'
							. ( empty($atts['slider_id'])
								? __( 'Controlled slider ID is not specified', 'trx_addons' )
								: ( (empty($atts['hide_prev']) 
										? '<a class="slider_prev'.(!empty($atts['title_prev']) ? ' with_title' : '').'" href="#">'
											. (!empty($atts['title_prev']) ? esc_html($atts['title_prev']) : '')
											. '</a>' 
										: ''
										)
									. (empty($atts['hide_next']) 
										? '<a class="slider_next'.(!empty($atts['title_next']) ? ' with_title' : '').'" href="#">'
											. (!empty($atts['title_next']) ? esc_html($atts['title_next']) : '')
											. '</a>' 
										: ''
										)
									. (!trx_addons_is_off($atts['pagination_style'])
										? '<div class="slider_pagination_wrap">'
											. ($atts['pagination_style'] == 'progressbar'
												? '<span class="slider_progress_bar"></span>'
												: '')
											. '</div>'
										: '')
									)
								)
						. '</div>'
					. '</div>';
		return apply_filters('trx_addons_sc_output', $output, 'trx_slider_controls', $atts, $content);
	}
}


// Add shortcode [trx_slider_controls]
if (!function_exists('trx_addons_sc_slider_controls_add_shortcode')) {
	function trx_addons_sc_slider_controls_add_shortcode() {
		add_shortcode("trx_slider_controls", "trx_addons_sc_slider_controls");
	}
	add_action('init', 'trx_addons_sc_slider_controls_add_shortcode', 20);
}
