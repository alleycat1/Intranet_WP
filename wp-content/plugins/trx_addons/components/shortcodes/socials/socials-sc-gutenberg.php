<?php
/**
 * Shortcode: Socials (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_socials_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_socials_editor_assets' );
	function trx_addons_gutenberg_sc_socials_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-socials',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/gutenberg/socials.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'socials/gutenberg/socials.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_socials_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_socials_add_in_gutenberg' );
	function trx_addons_sc_socials_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/socials',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'icons_type'         => array(
								'type'    => 'string',
								'default' => 'socials',
							),
							'align'              => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'icons'              => array(
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
					'render_callback' => 'trx_addons_gutenberg_sc_socials_render_block',
				), 'trx-addons/socials' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_socials_render_block' ) ) {
	function trx_addons_gutenberg_sc_socials_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['icons'] ) ) {
			if ( is_string( $attributes['icons'] ) ) {
				$attributes['icons'] = json_decode( $attributes['icons'], true );
			}
		}
		return trx_addons_sc_socials( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_socials_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_socials_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_socials_get_layouts( $array = array() ) {
		$array['sc_socials'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'sc', 'socials' ), 'trx_sc_socials' );
		if (!isset($array['sc_socials_types'])) $array['sc_socials_types'] = trx_addons_get_list_sc_socials_types();
		return $array;
	}
}
