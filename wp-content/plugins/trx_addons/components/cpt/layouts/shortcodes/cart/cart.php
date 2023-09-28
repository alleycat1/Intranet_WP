<?php
/**
 * Shortcode: Display WooCommerce cart with items number and totals
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Load required styles for the frontend
if ( !function_exists( 'trx_addons_cpt_layouts_cart_load_styles_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_layouts_cart_load_styles_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_cpt_layouts_cart_load_styles_front() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_layouts-cart', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.css'), array(), null );
		}
	}
}

// Load responsive styles for the frontend
if ( !function_exists( 'trx_addons_cpt_layouts_cart_load_responsive_styles' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_layouts_cart_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY);
	function trx_addons_cpt_layouts_cart_load_responsive_styles() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 
				'trx_addons-sc_layouts-cart-responsive', 
				trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.responsive.css'), 
				array(), 
				null, 
				trx_addons_media_for_load_css_responsive( 'cpt-layouts-cart', 'md' ) 
			);
		}
	}
}

// Load required scripts for the frontend
if ( !function_exists( 'trx_addons_cpt_layouts_cart_load_scripts_front' ) ) {
	function trx_addons_cpt_layouts_cart_load_scripts_front() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_script( 'trx_addons-sc_layouts_cart', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.js'), array('jquery'), null, true );
		}
	}
}

// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_cart_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_cart_merge_styles');
	add_filter("trx_addons_filter_merge_styles_layouts", 'trx_addons_sc_layouts_cart_merge_styles');
	function trx_addons_sc_layouts_cart_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.css' ] = true;
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_layouts_cart_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_layouts_cart_merge_styles_responsive');
	add_filter("trx_addons_filter_merge_styles_responsive_layouts", 'trx_addons_sc_layouts_cart_merge_styles_responsive');
	function trx_addons_sc_layouts_cart_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.responsive.css' ] = true;
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_sc_layouts_cart_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_layouts_cart_merge_scripts');
	function trx_addons_sc_layouts_cart_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart.js' ] = true;
		return $list;
	}
}


// Load shortcode's specific scripts if current mode is Preview in the PageBuilder
if ( !function_exists( 'trx_addons_sc_layouts_cart_load_scripts' ) ) {
	add_action("trx_addons_action_pagebuilder_preview_scripts", 'trx_addons_sc_layouts_cart_load_scripts', 10, 1);
	function trx_addons_sc_layouts_cart_load_scripts( $editor = '', $force = false ) {
		if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) && $editor != 'gutenberg' ) {
			trx_addons_cpt_layouts_cart_load_styles_front();
			trx_addons_cpt_layouts_cart_load_responsive_styles();
			trx_addons_cpt_layouts_cart_load_scripts_front();
			do_action( 'trx_addons_action_load_scripts_front', $force, 'sc_layouts_cart' );
		}
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( ! function_exists( 'trx_addons_sc_layouts_cart_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_sc_layouts_cart_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_sc_layouts_cart_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_sc_layouts_cart_check_in_html_output', 10, 1 );
	function trx_addons_sc_layouts_cart_check_in_html_output( $content = '' ) {
		$args = array(
			'need' => trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ),
			'check' => array(
				'class=[\'"][^\'"]*sc_layouts_cart',
			)
		);
		if ( trx_addons_check_in_html_output( 'sc_layouts_cart', $content, $args ) ) {
			trx_addons_sc_layouts_cart_load_scripts( '', true );
		}
		return $content;
	}
}

// Add 'Cart' on action hook
if (!function_exists('trx_addons_add_cart')) {
	add_action('trx_addons_action_cart', 'trx_addons_add_cart');
	function trx_addons_add_cart($atts=array()) {
		trx_addons_show_layout(trx_addons_sc_layouts_cart($atts));
	}
}



// trx_sc_layouts_cart
//-------------------------------------------------------------
/*
[trx_sc_layouts_cart id="unique_id" text="Shopping cart"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_cart' ) ) {
	function trx_addons_sc_layouts_cart($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_cart', $atts, trx_addons_sc_common_atts('id,hide', array(
			// Individual params
			"type" => "default",
			"market" => "woocommerce",
			"text" => "",
			))
		);

		if ( $atts['type'] == 'panel' && ! function_exists( 'trx_addons_sc_layouts' ) ) {
			$atts['type'] = 'default';
		}

/*
		// Force enqueue WooCommerce scripts (if a cart is used on non-woocommerce pages)
		if ( $atts['market'] == 'woocommerce' && trx_addons_exists_woocommerce() ) {
			wp_enqueue_script( 'woocommerce' );
			wp_enqueue_script( 'wc-cart-fragments' );
		}
*/
		trx_addons_cpt_layouts_cart_load_scripts_front();

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_cart',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_cart', $atts, $content);
	}
}


// Add shortcode [trx_sc_layouts_cart]
if (!function_exists('trx_addons_sc_layouts_cart_add_shortcode')) {
	function trx_addons_sc_layouts_cart_add_shortcode() {
		
		if (!trx_addons_cpt_layouts_sc_required()) return;
		
		add_shortcode("trx_sc_layouts_cart", "trx_addons_sc_layouts_cart");

	}
	add_action('init', 'trx_addons_sc_layouts_cart_add_shortcode', 15);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'cart/cart-sc-vc.php';
}
