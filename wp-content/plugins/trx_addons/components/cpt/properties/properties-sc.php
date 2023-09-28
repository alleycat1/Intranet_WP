<?php
/**
 * ThemeREX Addons Custom post type: Properties (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.6.22
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Prepare slides with Properties data
//----------------------------------------------------------------------------
if (!function_exists('trx_addons_cpt_properties_slider_content')) {
	add_filter('trx_addons_filter_slider_content', 'trx_addons_cpt_properties_slider_content', 10, 3);
	function trx_addons_cpt_properties_slider_content($image, $args, $data='') {
		if (get_post_type() == TRX_ADDONS_CPT_PROPERTIES_PT) {
			$image['content'] = trx_addons_get_template_part_as_string(TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.slider-slide.php',
											'trx_addons_args_properties_slider_slide',
											compact('image', 'args')
										);
			$image['image'] = $image['link'] = $image['url'] = '';
		}
		return $image;
	}
}


// trx_sc_properties
//-------------------------------------------------------------
/*
[trx_sc_properties id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_properties' ) ) {
	function trx_addons_sc_properties($atts, $content=null) {	

		// Exit to prevent recursion
		if (trx_addons_sc_stack_check('trx_sc_properties')) return '';

		$atts = trx_addons_sc_prepare_atts('trx_sc_properties', $atts, trx_addons_sc_common_atts('id,title,slider,query', array(
			// Individual params
			"type" => "default",
			"properties_type" => '',
			"properties_status" => '',
			"properties_labels" => '',
			"properties_country" => '',
			"properties_state" => '',
			"properties_city" => '',
			"properties_neighborhood" => '',
			"map_height" => 350,
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
		trx_addons_cpt_properties_load_scripts_front( true );

		// Load template
		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT . 'properties/tpl.properties.default.php'
										),
                                        'trx_addons_args_sc_properties',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_properties', $atts, $content);
	}
}


// Add shortcode [trx_sc_properties]
if (!function_exists('trx_addons_sc_properties_add_shortcode')) {
	function trx_addons_sc_properties_add_shortcode() {
		add_shortcode("trx_sc_properties", "trx_addons_sc_properties");
	}
	add_action('init', 'trx_addons_sc_properties_add_shortcode', 20);
}
