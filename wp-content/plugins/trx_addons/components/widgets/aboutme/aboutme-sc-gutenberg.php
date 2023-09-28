<?php
/**
 * Widget: About Me (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_aboutme_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_aboutme_editor_assets' );
	function trx_addons_gutenberg_sc_aboutme_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-aboutme',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'aboutme/gutenberg/aboutme.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'aboutme/gutenberg/aboutme.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_aboutme_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_aboutme_add_in_gutenberg' );
	function trx_addons_sc_aboutme_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/aboutme',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'title'       => array(
								'type'    => 'string',
								'default' => esc_html__( 'About me', 'trx_addons' ),
							),
							'avatar'      => array(
								'type'    => 'number',
								'default' => 0,
							),
							'avatar_url'  => array(
								'type'    => 'string',
								'default' => '',
							),
							'username'    => array(
								'type'    => 'string',
								'default' => '',
							),
							'description' => array(
								'type'    => 'string',
								'default' => '',
							),
							// Rerender
							'reload'      => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_aboutme_render_block',
				), 'trx-addons/aboutme' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_aboutme_render_block' ) ) {
	function trx_addons_gutenberg_sc_aboutme_render_block( $attributes = array() ) {
		return trx_addons_sc_widget_aboutme( $attributes );
	}
}
