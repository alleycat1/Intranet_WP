<?php
/**
 * Shortcode: Hotspot (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.94.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_hotspot_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_hotspot_editor_assets' );
	function trx_addons_gutenberg_sc_hotspot_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-hotspot',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'hotspot/gutenberg/hotspot.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'hotspot/gutenberg/hotspot.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_hotspot_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_hotspot_add_in_gutenberg' );
	function trx_addons_sc_hotspot_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/hotspot',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'image'              => array(
								'type'    => 'number',
								'default' => 0,
							),
							'image_url'          => array(
								'type'    => 'string',
								'default' => '',
							),
							'image_link'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'spots'              => array(
								'type'    => 'string',
								'default' => '',
							),						
							// Rerender
							'reload'             => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_title(),
						trx_addons_gutenberg_get_param_button(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_hotspot_render_block',
				), 'trx-addons/hotspot' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_hotspot_render_block' ) ) {
	function trx_addons_gutenberg_sc_hotspot_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['image'] ) ) {
			if ( empty( $attributes['spots'] ) ) {
				$attributes['spots'] = array();
			}
			if ( is_string( $attributes['spots'] ) ) {
				$attributes['spots'] = json_decode( $attributes['spots'], true );
			}
			return trx_addons_sc_hotspot( $attributes );
		} else {
			return esc_html__( 'Select a main image', 'trx_addons' );
		}
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenerg_sc_hotspot_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_hotspot_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_hotspot_get_layouts( $array = array() ) {
		$array['sc_hotspot'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'sc', 'hotspot' ), 'trx_sc_hotspot' );
		return $array;
	}
}

// Add shortcode-specific lists to the js vars
if ( ! function_exists( 'trx_addons_gutenberg_sc_hotspot_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_hotspot_params', 10, 1 );
	function trx_addons_gutenberg_sc_hotspot_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		$vars['sc_hotspot_symbols'] = ! $is_edit_mode ? array() : trx_addons_get_list_sc_hotspot_symbols();
		$vars['sc_hotspot_sources'] = ! $is_edit_mode ? array() : trx_addons_get_list_sc_hotspot_sources();
		$vars['sc_hotspot_post_parts'] = ! $is_edit_mode ? array() : trx_addons_get_list_sc_hotspot_post_parts();
		$vars['sc_hotspot_posts']   = ! $is_edit_mode ? array() : trx_addons_get_list_posts( false, array(
																						'post_type' => 'any',
																						'order' => 'asc',
																						'orderby' => 'title'
																						)
																					);
		return $vars;
	}
}
