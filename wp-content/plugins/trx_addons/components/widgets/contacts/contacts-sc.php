<?php
/**
 * Widget: Display Contacts info (Shortcodes)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// trx_widget_contacts
//-------------------------------------------------------------
/*
[trx_widget_contacts id="unique_id" title="Widget title" logo="image_url" logo_retina="image_url" description="short description" address="Address string" phone="Phone" email="Email" socials="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_widget_contacts' ) ) {
	function trx_addons_sc_widget_contacts($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_contacts', $atts, trx_addons_sc_common_atts('id', array(
			// Individual params
			"title" => "",
			"logo" => "",
			"logo_retina" => "",
			"description" => "",
			"map" => 0,
			"map_height" => 140,
			"map_position" => "top",
			"address" => "",
			"phone" => "",
			"email" => "",
			"columns" => 0,
			"socials" => 0,
			))
		);
		if ($atts['columns']=='') $atts['columns'] = 0;
		if ($atts['socials']=='') $atts['socials'] = 0;
		if ($atts['map']=='') $atts['map'] = 0;
		extract($atts);
		$atts['content'] = do_shortcode($content);
		$type = 'trx_addons_widget_contacts';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_contacts' 
								. (trx_addons_exists_vc() ? ' vc_widget_contacts wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_contacts', 'widget_contacts') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_contacts', $atts, $content);
	}
}


// Add shortcode [trx_widget_contacts]
if (!function_exists('trx_addons_sc_widget_contacts_add_shortcode')) {
	function trx_addons_sc_widget_contacts_add_shortcode() {
		add_shortcode("trx_widget_contacts", "trx_addons_sc_widget_contacts");
	}
	add_action('init', 'trx_addons_sc_widget_contacts_add_shortcode', 20);
}
