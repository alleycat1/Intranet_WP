<?php
/**
 * Shortcode: Display menu in the Layouts Builder
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}
	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_layouts_menu_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_layouts_menu_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_sc_layouts_menu_load_scripts_front() {
		// Superfish Menu
		// Attention! To prevent duplicate this script in the plugin and in the menu, don't merge it!
		wp_enqueue_script( 'superfish', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/superfish.js'), array('jquery'), null, true );
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc_layouts-menu', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/menu.css'), array(), null );
			wp_enqueue_script( 'trx_addons-sc_layouts_menu', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/menu.js'), array('jquery'), null, true );
		}
	}
}

// Load responsive styles for the frontend
if ( !function_exists( 'trx_addons_cpt_layouts_menu_load_responsive_styles' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_layouts_menu_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY);
	function trx_addons_cpt_layouts_menu_load_responsive_styles() {
		if (trx_addons_exists_page_builder() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 
				'trx_addons-sc_layouts-menu-responsive', 
				trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/menu.responsive.css'), 
				array(), 
				null, 
				trx_addons_media_for_load_css_responsive( 'cpt-layouts-menu', 'lg' ) 
			);
		}
	}
}
	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_menu_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_menu_merge_styles');
	add_filter("trx_addons_filter_merge_styles_layouts", 'trx_addons_sc_layouts_menu_merge_styles');
	function trx_addons_sc_layouts_menu_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/menu.css' ] = true;
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_layouts_menu_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_layouts_menu_merge_styles_responsive');
	add_filter("trx_addons_filter_merge_styles_responsive_layouts", 'trx_addons_sc_layouts_menu_merge_styles_responsive');
	function trx_addons_sc_layouts_menu_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/menu.responsive.css' ] = true;
		return $list;
	}
}

	
// Merge shortcode's specific scripts into single file
if ( !function_exists( 'trx_addons_sc_layouts_menu_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_layouts_menu_merge_scripts');
	function trx_addons_sc_layouts_menu_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/menu.js' ] = true;
		$list[ TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/jquery.slidemenu.js' ] = true;
		return $list;
	}
}


// Load shortcode's specific scripts if current mode is Preview in the PageBuilder
if ( !function_exists( 'trx_addons_sc_layouts_menu_load_scripts' ) ) {
	add_action("trx_addons_action_pagebuilder_preview_scripts", 'trx_addons_sc_layouts_menu_load_scripts', 10, 1);
	function trx_addons_sc_layouts_menu_load_scripts($editor='') {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode')) && $editor!='gutenberg') {
			wp_enqueue_style( 'trx_addons-sc_layouts-menu', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/menu.css'), array(), null );
			wp_enqueue_script( 'slidemenu', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/jquery.slidemenu.js'), array('jquery'), null, true );
		}
	}
}


// Add menu layout to the mobile menu
if ( !function_exists( 'trx_addons_sc_layouts_menu_add_to_mobile_menu' ) ) {
	function trx_addons_sc_layouts_menu_add_to_mobile_menu($menu) {
		global $TRX_ADDONS_STORAGE;
		// Get menu items
		$tmp_pos1 = strpos($menu, '<ul');
		$tmp_pos1 = strpos($menu, '>', $tmp_pos1) + 1;
		$tmp_pos2 = strrpos($menu, '</ul>');
		$menu = substr($menu, $tmp_pos1, $tmp_pos2 - $tmp_pos1);
		// Add to the mobile menu
		if (!isset($TRX_ADDONS_STORAGE['menu_mobile'])) $TRX_ADDONS_STORAGE['menu_mobile'] = '';
		$TRX_ADDONS_STORAGE['menu_mobile'] .= $menu;
	}
}
	
// Return stored items as mobile menu
if ( !function_exists( 'trx_addons_sc_layouts_menu_get_mobile_menu' ) ) {
	add_filter("trx_addons_filter_get_mobile_menu", 'trx_addons_sc_layouts_menu_get_mobile_menu');
	function trx_addons_sc_layouts_menu_get_mobile_menu($menu) {
		global $TRX_ADDONS_STORAGE;
		return empty($TRX_ADDONS_STORAGE['menu_mobile']) 
					? '' 
					: '<ul id="' . esc_attr( trx_addons_generate_id( 'menu_mobile_' ) ) . "\">{$TRX_ADDONS_STORAGE['menu_mobile']}</ul>";
	}
}

// Add description to the menu item
if (!function_exists('trx_addons_sc_layouts_menu_add_menu_item_description')) {
	add_filter( 'nav_menu_item_title', 'trx_addons_sc_layouts_menu_add_menu_item_description', 10, 4 );
	function trx_addons_sc_layouts_menu_add_menu_item_description($title, $item, $args, $depth) {
		if (!empty($item->description)) {
			$title .= '<span class="sc_layouts_menu_item_description">' . trim($item->description) . '</span>';
		}
		return $title;
	}
}


// trx_sc_layouts_menu
//-------------------------------------------------------------
/*
[trx_sc_layouts_menu id="unique_id" menu="menu_id" location="menu_location" burger="0|1" mobile="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_menu' ) ) {
	function trx_addons_sc_layouts_menu($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_menu', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"type" => "default",
			"direction" => "horizontal",
			"submenu_style" => "popup",
			"location" => "",
			"menu" => "",
			"mobile_menu" => "0",
			"mobile_button" => "0",
			"animation_in" => "",
			"animation_out" => "",
			"hover" => "fade",
			"hide_on_mobile" => "0",
			))
		);

		if (trx_addons_is_off($atts['menu'])) $atts['menu'] = '';
		if (trx_addons_is_off($atts['location'])) $atts['location'] = '';
		$atts['direction'] = $atts['direction'] == 'vertical' ? 'vertical' : 'horizontal';

		// Slide menu support
		if (trx_addons_is_on(trx_addons_get_option('debug_mode')) && in_array($atts['hover'], array('slide_line', 'slide_box')) ) {
			wp_enqueue_script( 'slidemenu', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/jquery.slidemenu.js'), array('jquery'), null, true );
		}

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/tpl.default.php'
										),
										'trx_addons_args_sc_layouts_menu',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_menu', $atts, $content);
	}
}


// Add shortcode [trx_sc_layouts_menu]
if (!function_exists('trx_addons_sc_layouts_menu_add_shortcode')) {
	function trx_addons_sc_layouts_menu_add_shortcode() {
		
		//if (!trx_addons_cpt_layouts_sc_required()) return;

		add_shortcode("trx_sc_layouts_menu", "trx_addons_sc_layouts_menu");
	}
	add_action('init', 'trx_addons_sc_layouts_menu_add_shortcode', 15);
}


// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/menu-sc-elementor.php';
}

// Add shortcodes to Gutenberg
if ( trx_addons_exists_gutenberg() && function_exists( 'trx_addons_gutenberg_get_param_id' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/menu-sc-gutenberg.php';
}

// Add shortcodes to VC
if ( trx_addons_exists_vc() && function_exists( 'trx_addons_vc_add_id_param' ) ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/menu-sc-vc.php';
}
