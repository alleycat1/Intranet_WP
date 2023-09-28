<?php
/**
 * ThemeREX Addons Custom post type: Services (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// trx_sc_services
//-------------------------------------------------------------
/*
[trx_sc_services id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_services' ) ) {
	function trx_addons_sc_services($atts, $content=null) {	

		// Exit to prevent recursion
		if (trx_addons_sc_stack_check('trx_sc_services')) return '';

		$atts = trx_addons_sc_prepare_atts('trx_sc_services', $atts, trx_addons_sc_common_atts('id,title,slider,query', array(
			// Individual params
			"type" => "default",
			"featured" => "image",
			"featured_position" => "top",
			"thumb_size" => '',
			"tabs_effect" => "fade",
			"hide_excerpt" => 0,
			"hide_bg_image" => 0,
			"icons_animation" => 0,
			"no_margin" => 0,
			'no_links' => false,
			"pagination" => "none",
			"page" => 1,
			'posts_exclude' => '',	// comma-separated list of IDs to exclude from output
			'post_type' => TRX_ADDONS_CPT_SERVICES_PT,
			'taxonomy' => TRX_ADDONS_CPT_SERVICES_TAXONOMY,
			"popup" => 0,
			"more_text" => esc_html__('Read more', 'trx_addons'),
			))
		);

		if ( in_array( $atts['type'], array( 'tabs', 'tabs_simple' ) ) ) {
			wp_enqueue_script( 'trx_addons-cpt_services', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT . 'services/services.js'), array('jquery'), null, true );
		}

		if ($atts['type'] == 'chess') {
			$atts['columns'] = max(1, min(3, (int) $atts['columns']));
		} else if ($atts['type'] == 'timeline') {
			$atts['no_margin'] = 1;
			if ($atts['featured']!='none' && in_array($atts['featured_position'], array('left', 'right'))) {
				$atts['columns'] = 1;
			}
		}
		if ($atts['featured_position'] == 'bottom' && !in_array($atts['type'], array('callouts', 'timeline'))) {
			$atts['featured_position'] = 'top';
		}
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
		$atts['popup'] = max(0, (int) $atts['popup']);
		if ($atts['popup']) $atts['class'] .= (!empty($atts['class']) ? ' ' : '') . 'sc_services_popup sc_post_details_popup';
		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';
		if ($atts['slider'] > 0) $atts['pagination'] = 'none';

		// Load CPT-specific scripts and styles
		trx_addons_cpt_services_load_scripts_front( true );

		// Load template
		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT . 'services/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT . 'services/tpl.default.php'
										),
                                        'trx_addons_args_sc_services',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_services', $atts, $content);
	}
}


// Add shortcode [trx_sc_services]
if (!function_exists('trx_addons_sc_services_add_shortcode')) {
	function trx_addons_sc_services_add_shortcode() {
		add_shortcode("trx_sc_services", "trx_addons_sc_services");
	}
	add_action('init', 'trx_addons_sc_services_add_shortcode', 20);
}
