<?php
/**
 * ThemeREX Addons Custom post type: Cars (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.6.25
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Prepare slides with Cars data
//----------------------------------------------------------------------------
if (!function_exists('trx_addons_cpt_cars_slider_content')) {
	add_filter('trx_addons_filter_slider_content', 'trx_addons_cpt_cars_slider_content', 10, 3);
	function trx_addons_cpt_cars_slider_content($image, $args, $data='') {
		if (get_post_type() == TRX_ADDONS_CPT_CARS_PT) {
			$image['content'] = trx_addons_get_template_part_as_string(TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.slider-slide.php',
											'trx_addons_args_cars_slider_slide',
											compact('image', 'args')
										);
			$image['image'] = $image['link'] = $image['url'] = '';
		}
		return $image;
	}
}


// trx_sc_cars
//-------------------------------------------------------------
/*
[trx_sc_cars id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_cars' ) ) {
	function trx_addons_sc_cars($atts, $content=null) {	

		// Exit to prevent recursion
		if (trx_addons_sc_stack_check('trx_sc_cars')) return '';

		$atts = trx_addons_sc_prepare_atts('trx_sc_cars', $atts, trx_addons_sc_common_atts('id,title,slider,query', array(
			// Individual params
			"type" => "default",
			"cars_maker" => '',
			"cars_model" => '',
			"cars_city" => '',
			"cars_type" => '',
			"cars_status" => '',
			"cars_labels" => '',
			"cars_price" => '',
			"cars_produced" => '',
			"cars_mileage" => '',
			"cars_engine_size" => '',
			"cars_fuel" => '',
			"cars_type_drive" => '',
			"cars_transmission" => '',
			"pagination" => "none",
			"page" => 1,
			'posts_exclude' => '',	// comma-separated list of IDs to exclude from output
			"more_text" => esc_html__('Read more', 'trx_addons'),
			))
		);
		if (!empty($atts['ids'])) {
			if ( is_array( $atts['ids'] ) ) {
				$atts['ids'] = join(',', $atts['ids']);
			}
			$atts['ids'] = str_replace(array(';', ' '), array(',', ''), $atts['ids']);
			$ids_count = count( explode( ',', $atts['ids'] ) );
			if ( empty( $atts['count'] ) || $atts['count'] >= $ids_count || empty( $atts['pagination'] ) || trx_addons_is_off( $atts['pagination'] ) ) {
				$atts['count'] = $ids_count;
			}
		}
		$atts['count'] = max(1, (int) $atts['count']);
		$atts['offset'] = max(0, (int) $atts['offset']);
		if (empty($atts['orderby'])) $atts['orderby'] = 'title';
		if (empty($atts['order'])) $atts['order'] = 'asc';
		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';
		if ($atts['slider'] > 0) $atts['pagination'] = 'none';
		// Load CPT-specific scripts and styles
		trx_addons_cpt_cars_load_scripts_front( true );
		// Load template
		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT . 'cars/tpl.cars.default.php'
										),
                                        'trx_addons_args_sc_cars',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_cars', $atts, $content);
	}
}


// Add shortcode [trx_sc_cars]
if (!function_exists('trx_addons_sc_cars_add_shortcode')) {
	function trx_addons_sc_cars_add_shortcode() {
		add_shortcode("trx_sc_cars", "trx_addons_sc_cars");
	}
	add_action('init', 'trx_addons_sc_cars_add_shortcode', 20);
}
