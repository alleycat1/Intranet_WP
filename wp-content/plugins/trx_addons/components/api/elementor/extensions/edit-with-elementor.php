<?php
/**
 * Elementor extension: Add custom layouts to the button "Edit with Elementor" on the admin bar.
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_elm_allow_submenu_add_options' ) ) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_elm_allow_submenu_add_options' );
	/**
	 * Add options to the ThemeREX Addons Options page
	 * 
	 * @hooked trx_addons_filter_options
	 *
	 * @param array $options  ThemeREX Addons Options array
	 * 
	 * @return array  Modified ThemeREX Addons Options array
	 */
	function trx_addons_elm_allow_submenu_add_options( $options ) {
		if ( trx_addons_exists_elementor() ) {// && defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.0.8.1', '<' ) ) {
			trx_addons_array_insert_after($options, 'layouts_info', array(
				'wp_admin_bar_render_to_the_footer' => array(
					"title" => esc_html__('Add Layouts to the button "Edit with Elementor"', 'trx_addons'),
					"desc" => wp_kses_data( __("Enable admin bar elements that depend on the content of the current page (e.g. Layouts)", 'trx_addons') ),
					"std" => "0",
					"type" => "switch"
				),
			) );
		}
		return $options;
	}
}

if ( ! function_exists( 'trx_addons_elm_allow_submenu_to_edit_layouts' ) ) {
	add_action( 'init', 'trx_addons_elm_allow_submenu_to_edit_layouts' );
	/**
	 * Move native 'wp_admin_bar_render' back to action 'wp_footer' to enable 'Edit with Elementor' for our layouts.
	 * Otherwise only current page link is available on this button.
	 * Fixed in Elementor 3.0.8.1
	 * 
	 * @hooked init
	 */
	function trx_addons_elm_allow_submenu_to_edit_layouts() {
		if ( trx_addons_exists_elementor() && defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '3.0.8.1', '<' ) ) {
			if ( trx_addons_is_on( trx_addons_get_option( 'wp_admin_bar_render_to_the_footer' ) ) ) {
				$priority = has_action( 'wp_body_open', 'wp_admin_bar_render' );
				if ( $priority !== false ) {
					remove_action( 'wp_body_open', 'wp_admin_bar_render', $priority );
					if ( ! has_action( 'wp_footer', 'wp_admin_bar_render' ) ) {
						add_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
					}
				}
			}
		}
	}
}
