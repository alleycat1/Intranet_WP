<?php
/**
 * Shortcode: Display any previously created layout
 *
 * @package ThemeREX Addons
 * @since v1.6.06
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// trx_sc_layouts
//-------------------------------------------------------------
/*
[trx_sc_layouts layout="layout_id"]
*/
if ( ! function_exists( 'trx_addons_sc_layouts' ) ) {
	function trx_addons_sc_layouts($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"type" => "default",
			"layout" => "",
			"template" => "",
			"content" => "",		// Alternative content
			// Panels parameters
			"position" => "right",
			"size" => 300,
			"modal" => 0,
			"shift_page" => 0,
			"effect" => "slide",
			"show_on" => "none",
			"show_delay" => 0,
			"popup_id" => "",		// Alter name for id in Elementor ('id' is reserved by Elementor)
			))
		);

		$output = '';

		if (empty($atts['content']) && !empty($content)) {
			$atts['content'] = $content;
		}
		
		if (!empty($atts['popup_id'])) {
			$atts['id'] = $atts['popup_id'];
		}

		// If content specified and no layout selected
		if (!empty($atts['content']) && empty($atts['layout']) && empty($atts['template'])) {
			$atts['layout'] = '';
			$atts['template'] = '';
			// Remove tags p if content contain shortcodes
			if (strpos($atts['content'], '[') !== false) {
				$atts['content'] = shortcode_unautop($atts['content']);
			}
			// Do shortcodes inside content
			$atts['content'] = apply_filters('widget_text_content', $atts['content']);

		// Get translated version of specified layout
		} else if (!empty($atts['layout'])) {
			$atts['layout'] = apply_filters('trx_addons_filter_get_translated_post', $atts['layout'], TRX_ADDONS_CPT_LAYOUTS_PT);

		// Get translated version of specified template
		} else if (!empty($atts['template'])) {
			$atts['template'] = apply_filters('trx_addons_filter_get_translated_post', $atts['template'], 'elementor_library');

		}
		
		// Add 'size' as class
		if ($atts['type'] == 'panel') {
			if (empty($atts['size'])) {
				$atts['size'] = 'auto';
			} else {
				$atts['size'] = trx_addons_prepare_css_value( $atts['size'] );
			}
			$panel_class = trx_addons_add_inline_css_class(
									trx_addons_get_css_dimensions_from_values(
										in_array($atts['position'], array('left', 'right')) ? $atts['size'] : '',
										in_array($atts['position'], array('top', 'bottom')) ? $atts['size'] : ''
									)
								);
			$atts['class'] .= ( ! empty( $atts['class'] ) ? ' ' : '') . $panel_class;
			$atts['panel_class'] = $panel_class;
		}

		ob_start();
		trx_addons_get_template_part( array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'layouts/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'layouts/tpl.default.php'
										),
										'trx_addons_args_sc_layouts',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();

		// Prepare preview holder for PageBuilders
		$preview_holder = '';
		if ( trx_addons_is_preview() ) {
			$preview_holder = trx_addons_get_preview_placeholder( 'trx_sc_layouts', $atts );
		}

		// Output layout
		if ( in_array( $atts['type'], apply_filters( 'trx_addons_sc_layouts_popup_types',  array('popup', 'panel') ) ) ) {
			// Remove init classes from the output in the popup
			$output = str_replace(  'wp-audio-shortcode',
									'wp-audio-shortcode-noinit',
									$output
									);
			$output = apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_layouts', $atts, $content );
			if ( ! trx_addons_is_preview() ) {
				trx_addons_add_inline_html( $output );
			}
			return $preview_holder;
		} else {
			return apply_filters('trx_addons_sc_output', ! empty( $output ) ? $output : $preview_holder, 'trx_sc_layouts', $atts, $content);
		}
	}
}


// Add shortcode [trx_sc_layouts]
if ( ! function_exists( 'trx_addons_sc_layouts_add_shortcode' ) ) {
	function trx_addons_sc_layouts_add_shortcode() {
		add_shortcode("trx_sc_layouts", "trx_addons_sc_layouts");
	}
	add_action('init', 'trx_addons_sc_layouts_add_shortcode', 20);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'layouts/layouts-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'layouts/layouts-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'layouts/layouts-sc-vc.php';
}

// Create our widget
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'layouts/layouts-widget.php';
