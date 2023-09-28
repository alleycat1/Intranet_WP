<?php
/**
 * Shortcode: Anchor (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_anchor_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_anchor_editor_assets' );
	function trx_addons_gutenberg_sc_anchor_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-anchor',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'anchor/gutenberg/anchor.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'anchor/gutenberg/anchor.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_anchor_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_anchor_add_in_gutenberg' );
	function trx_addons_sc_anchor_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/anchor',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array(
						'id'    => array(
							'type'    => 'string',
							'default' => '',
						),
						'className'          => array(
							'type'    => 'string',
							'default' => '',
						),
						'title' => array(
							'type'    => 'string',
							'default' =>  esc_html__("Title", 'trx_addons'),
						),
						'url'   => array(
							'type'    => 'string',
							'default' => '',
						),
						'icon'  => array(
							'type'    => 'string',
							'default' => '',
						),
					),
					'render_callback' => 'trx_addons_gutenberg_sc_anchor_render_block',
				), 'trx-addons/anchor' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_anchor_render_block' ) ) {
	function trx_addons_gutenberg_sc_anchor_render_block( $attributes = array() ) {
		return trx_addons_sc_anchor( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_anchor_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_anchor_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_anchor_get_layouts( $array = array() ) {
		$array['sc_anchor'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'sc', 'anchor' ), 'trx_sc_anchor' );
		return $array;
	}
}
