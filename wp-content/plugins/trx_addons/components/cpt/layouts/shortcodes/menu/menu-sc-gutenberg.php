<?php
/**
 * Shortcode: Display menu in the Layouts Builder (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}
	


// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_menu_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_menu_editor_assets' );
	function trx_addons_gutenberg_sc_menu_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-menu',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/gutenberg/menu.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'menu/gutenberg/menu.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_menu_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_menu_add_in_gutenberg' );
	function trx_addons_sc_menu_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/layouts-menu',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'           => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'direction'      => array(
								'type'    => 'string',
								'default' => 'horizontal',
							),
							'location'       => array(
								'type'    => 'string',
								'default' => 'menu_main',
							),
							'menu'           => array(
								'type'    => 'string',
								'default' => '',
							),
							'mobile_menu'    => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'mobile_button'  => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'animation_in'   => array(
								'type'    => 'string',
								'default' => '',
							),
							'animation_out'  => array(
								'type'    => 'string',
								'default' => '',
							),
							'hover'          => array(
								'type'    => 'string',
								'default' => 'fade',
							),
							'hide_on_mobile' => array(
								'type'    => 'boolean',
								'default' => false,
							),
						),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_menu_render_block',
				), 'trx-addons/layouts-menu' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_menu_render_block' ) ) {
	function trx_addons_gutenberg_sc_menu_render_block( $attributes = array() ) {
		$output = trx_addons_sc_layouts_menu( $attributes );
		if ( empty( $output ) && trx_addons_is_preview( 'gutenberg' ) ) {
			return TRX_ADDONS_GUTENBERG_EDITOR_MSG_BLOCK_IS_EMPTY;
		}
		return $output;
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_menu_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_menu_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_menu_get_layouts( $array = array() ) {
		$array['sc_menu'] = apply_filters( 'trx_addons_sc_type', trx_addons_get_list_sc_layouts_menu(), 'trx_sc_layouts_menu' );
		return $array;
	}
}

// Add shortcode's specific vars to the JS storage
if ( ! function_exists( 'trx_addons_gutenberg_sc_menu_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_menu_params' );
	function trx_addons_gutenberg_sc_menu_params( $vars = array() ) {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {

			// If editor is active now
			$is_edit_mode = trx_addons_is_post_edit();

			// Menu locations
			$vars['menu_locations'] = !$is_edit_mode ? array() : trx_addons_get_list_menu_locations();

			// Menus
			$vars['menus'] = !$is_edit_mode ? array() : trx_addons_get_list_menus();

			// Menu hover
			$vars['menu_hover'] = !$is_edit_mode ? array() : trx_addons_get_list_menu_hover();

			return $vars;
		}
	}
}