<?php
/**
 * Shortcode: OpenStreen Map
 *
 * @package ThemeREX Addons
 * @since v1.6.63
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Load shortcode's specific scripts if current mode is Preview in the PageBuilder
if ( !function_exists( 'trx_addons_sc_osmap_load_scripts' ) ) {
	add_action("trx_addons_action_pagebuilder_preview_scripts", 'trx_addons_sc_osmap_load_scripts', 10, 1);
	function trx_addons_sc_osmap_load_scripts( $force = false ) {
		trx_addons_enqueue_optimized( 'sc_osmap', $force, array(
			'lib' => array(
				'callback' => function() {
					trx_addons_enqueue_osmap();
				}
			),
			'css'  => array(
				'trx_addons-sc_osmap' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/osmap.css' ),
			),
			'js' => array(
				'trx_addons-sc_osmap' => array( 'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/osmap.js', 'deps' => 'jquery' ),
			),
			'check' => array(
				array( 'type' => 'sc',  'sc' => 'trx_sc_osmap' ),
				array( 'type' => 'gb',  'sc' => 'wp:trx-addons/osmap' ),
				array( 'type' => 'elm', 'sc' => '"widgetType":"trx_sc_osmap"' ),
				array( 'type' => 'elm', 'sc' => '"shortcode":"[trx_sc_osmap' ),
			)
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_sc_osmap_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_sc_osmap_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_sc_osmap', 'trx_addons_sc_osmap_load_scripts_front_responsive', 10, 1 );
	function trx_addons_sc_osmap_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'sc_osmap', $force, array(
			'css'  => array(
				'trx_addons-sc_osmap-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/osmap.responsive.css',
					'media' => 'md'
				),
			),
		) );
	}
}
	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_osmap_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_osmap_merge_styles');
	function trx_addons_sc_osmap_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/osmap.css' ] = false;
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_osmap_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_osmap_merge_styles_responsive');
	function trx_addons_sc_osmap_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/osmap.responsive.css' ] = false;
		return $list;
	}
}

// Merge osmap specific scripts to the single file
if ( !function_exists( 'trx_addons_sc_osmap_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_osmap_merge_scripts');
	function trx_addons_sc_osmap_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/osmap.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu
if ( !function_exists( 'trx_addons_sc_osmap_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_osmap_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_osmap_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_osmap_check_in_html_output', 10, 1 );
	function trx_addons_sc_osmap_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'class=[\'"][^\'"]*sc_osmap'
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_osmap', $content, $args ) ) {
			trx_addons_sc_osmap_load_scripts( true );
		}
		return $content;
	}
}

	
// Add messages for JS
if ( !function_exists( 'trx_addons_sc_osmap_localize_script' ) ) {
	add_filter("trx_addons_filter_localize_script", 'trx_addons_sc_osmap_localize_script');
	function trx_addons_sc_osmap_localize_script($vars) {
		if ( trx_addons_components_is_allowed('sc', 'osmap') ) {
			$vars['msg_sc_osmap_not_avail'] = esc_html__('OpenStreetMap service is not available', 'trx_addons');
			$vars['msg_sc_osmap_geocoder_error'] = esc_html__('Error while geocoding address', 'trx_addons');
			$vars['osmap_tiler'] = trx_addons_get_option('api_openstreet_tiler');
			$vars['osmap_tiler_styles'] = trx_addons_get_list_sc_osmap_styles( true );
			$vars['osmap_attribution'] = wp_kses_data( __( 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors', 'trx_addons' ) );
		}
		return $vars;
	}
}

// Add vars to the admin
if ( !function_exists( 'trx_addons_sc_osmap_localize_admin_script' ) ) {
	add_filter( 'trx_addons_filter_localize_script_admin',	'trx_addons_sc_osmap_localize_admin_script');
	function trx_addons_sc_osmap_localize_admin_script($vars = array()) {
		if ( trx_addons_components_is_allowed('sc', 'osmap') ) {
			$vars['osmap_tiler'] = trx_addons_get_option('api_openstreet_tiler');
			$vars['osmap_tiler_styles'] = trx_addons_get_list_sc_osmap_styles( true );
			$vars['osmap_attribution'] = wp_kses_data( __( 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors', 'trx_addons' ) );
		}
		return $vars;
	}
}


// trx_sc_osmap
//-------------------------------------------------------------
/*
[trx_sc_osmap id="unique_id" style="grey" zoom="16" markers="encoded json data"]
*/
if ( !function_exists( 'trx_addons_sc_osmap' ) ) {
	function trx_addons_sc_osmap($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_osmap', $atts, trx_addons_sc_common_atts('id,title', array(
			// Individual params
			"type" => "default",
			"zoom" => 16,
			"center" => '',
			"style" => '',
			"address" => '',
			"markers" => '',
			"cluster" => '',
			"width" => "100%",
			"height" => "400",
			"prevent_scroll" => 0,
			// Content from non-containers PageBuilder
			"content" => ""
			))
		);

		if (empty($atts['id'])) {
			$atts['id'] = trx_addons_generate_id( 'sc_osmap_' );
		}
		if (empty($atts['style'])) {
			$atts['style'] = trx_addons_array_get_first( trx_addons_get_list_sc_osmap_styles() );
		}
		if (!is_array($atts['markers']) && function_exists('vc_param_group_parse_atts')) {
			$atts['markers'] = (array) vc_param_group_parse_atts( $atts['markers'] );
		}

		$output = '';
		if ( (is_array($atts['markers']) && count($atts['markers']) > 0) || !empty($atts['address'])) {
			if (!empty($atts['address'])) {
				$atts['markers'] = array(
										array(
											'title' => '',
											'description' => '',
											'address' => $atts['address'],
											'icon' => trx_addons_remove_protocol(trx_addons_get_option('api_openstreet_marker')),
											'icon_width' => '',
											'icon_height' => ''
										)
									);
			} else {
				foreach ($atts['markers'] as $k=>$v) {
					if (!empty($v['description']) && function_exists('vc_value_from_safe')) {
						$atts['markers'][$k]['description'] = trim( vc_value_from_safe( $v['description'] ) );
					}
					if (!empty($v['icon'])) {
						$atts['markers'][$k]['icon'] = trx_addons_get_attachment_url($v['icon'], 'full');
						if (empty($v['icon_width']) || empty($v['icon_height'])) {
							$attr = trx_addons_getimagesize($atts['markers'][$k]['icon']);
							$atts['markers'][$k]['icon_width'] = ! empty( $attr[0] ) ? $attr[0] : '';
							$atts['markers'][$k]['icon_height'] = ! empty( $attr[1] ) ? $attr[1] : '';
						}
					} else {
						$v['icon'] = trx_addons_remove_protocol(trx_addons_get_option('api_openstreet_marker'));
					}
					if (!empty($v['icon_retina']) && trx_addons_get_retina_multiplier() > 1) {
						$atts['markers'][$k]['icon'] = trx_addons_get_attachment_url($v['icon_retina'], 'full');
					}
				}
			}

			$atts['zoom'] = max(0, min(21, $atts['zoom']));
			$atts['center'] = trim($atts['center']);
	
			if (count($atts['markers']) > 1) {
				if (empty($atts['cluster'])) {
					$atts['cluster'] = trx_addons_remove_protocol(trx_addons_get_option('api_openstreet_cluster'));
				}
				if (empty($atts['cluster'])) {
					$atts['cluster'] = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/cluster/cluster-icon.png');
				} else if ((int) $atts['cluster'] > 0) {
					$atts['cluster'] = trx_addons_get_attachment_url($atts['cluster'], apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size('masonry'), 'osmap-cluster'));
				}
			} else if ($atts['zoom'] == 0) {
				$atts['zoom'] = 16;
			}
	
			$atts['class'] .= (!empty($atts['class']) ? ' ' : '') 
							. trx_addons_add_inline_css_class(trx_addons_get_css_dimensions_from_values($atts['width'], $atts['height']));
	
			if ( empty( $atts['style'] ) ) {
				$output = esc_html__( 'Please, specify correct style for the shortcode "OpenStreet map"', 'trx_addons' );
			} else {
				$atts['content'] = do_shortcode(empty($atts['content']) ? $content : $atts['content']);
			
				// Load shortcode-specific scripts and styles
				trx_addons_sc_osmap_load_scripts( true );

				// Load template
				ob_start();
				trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/tpl.'.trx_addons_esc($atts['type']).'.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/tpl.default.php'
											),
											'trx_addons_args_sc_osmap', 
											$atts
										);
				$output = ob_get_contents();
				ob_end_clean();
			}
		}
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_osmap', $atts, $content);
	}
}


// Add shortcode [trx_sc_osmap]
if (!function_exists('trx_addons_sc_osmap_add_shortcode')) {
	function trx_addons_sc_osmap_add_shortcode() {
		add_shortcode("trx_sc_osmap", "trx_addons_sc_osmap");
	}
	add_action('init', 'trx_addons_sc_osmap_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/osmap-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/osmap-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/osmap-sc-vc.php';
}

// Create our widget
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'osmap/osmap-widget.php';
