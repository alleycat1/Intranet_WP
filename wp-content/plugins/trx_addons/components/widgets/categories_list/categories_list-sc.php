<?php
/**
 * Widget: Categories list (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// trx_widget_categories_list
//-------------------------------------------------------------
/*
[trx_widget_categories_list id="unique_id" title="Widget title" style="1" number="4" columns="4" show_posts="0|1" show_children="0|1" cat_list="id1,id2,id3,..."]
*/
if ( !function_exists( 'trx_addons_sc_widget_categories_list' ) ) {
	function trx_addons_sc_widget_categories_list($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_categories_list', $atts, trx_addons_sc_common_atts('id,slider', array(
			// Individual params
			"title" => '',
			'style' => '1',
			'number' => 5,
			'columns' => 5,
			'columns_tablet' => '',
			'columns_mobile' => '',
			'show_thumbs' => 1,
			'show_posts' => 1,
			'show_children' => 0,
			'post_type' => 'post',
			'taxonomy' => 'category',
			'cat_list' => '',
			))
		);
		extract($atts);
		$type = 'trx_addons_widget_categories_list';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_categories_list' 
								. (trx_addons_exists_vc() ? ' vc_widget_categories_list wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_categories_list', 'widget_categories_list') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_categories_list', $atts, $content);
	}
}


// Add shortcode [trx_widget_categories_list]
if (!function_exists('trx_addons_sc_widget_categories_list_add_shortcode')) {
	function trx_addons_sc_widget_categories_list_add_shortcode() {
		add_shortcode("trx_widget_categories_list", "trx_addons_sc_widget_categories_list");
	}
	add_action('init', 'trx_addons_sc_widget_categories_list_add_shortcode', 20);
}
