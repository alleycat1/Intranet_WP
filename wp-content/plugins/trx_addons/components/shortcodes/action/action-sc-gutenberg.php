<?php
/**
 * Shortcode: Action (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_action_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_action_editor_assets' );
	function trx_addons_gutenberg_sc_action_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-action',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'action/gutenberg/action.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'action/gutenberg/action.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_action_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_action_add_in_gutenberg' );
	function trx_addons_sc_action_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/action',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'columns'            => array(
								'type'    => 'number',
								'default' => 1,
							),
							'full_height'        => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'min_height'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'actions'            => array(
								'type'    => 'string',
								'default' => '',
							),						
							// Rerender
							'reload'             => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_slider(),
						trx_addons_gutenberg_get_param_title(),
						trx_addons_gutenberg_get_param_button(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_action_render_block',
				), 'trx-addons/action' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_action_render_block' ) ) {
	function trx_addons_gutenberg_sc_action_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['actions'] ) ) {
			if ( is_string( $attributes['actions'] ) ) {
				$attributes['actions'] = json_decode( $attributes['actions'], true );
			}
			return trx_addons_sc_action( $attributes );
		} else {
			return esc_html__( 'Add at least one item', 'trx_addons' );
		}
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_action_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_action_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_action_get_layouts( $array = array() ) {
		$array['sc_action'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'sc', 'action' ), 'trx_sc_action' );
		return $array;
	}
}
