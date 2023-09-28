<?php
/**
 * Widget: Custom links (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.0.46
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_custom_links_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_custom_links_editor_assets' );
	function trx_addons_gutenberg_sc_custom_links_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-custom-links',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'custom_links/gutenberg/custom-links.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'custom_links/gutenberg/custom-links.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_custom_links_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_custom_links_add_in_gutenberg' );
	function trx_addons_sc_custom_links_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/custom-links',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'title'       => array(
								'type'    => 'string',
								'default' => esc_html__( 'Custom Links', 'trx_addons' ),
							),
							'icons_animation'      => array(
								'type'    => 'boolean',
								'default' => 0,
							),
							'links'       => array(
								'type'    => 'string',
								'default' => ''
							),
							// Reload block - hidden option
							'reload'         => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_custom_links_render_block',
				), 'trx-addons/custom-links' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_custom_links_render_block' ) ) {
	function trx_addons_gutenberg_sc_custom_links_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['links'] ) ) {
			if ( is_string( $attributes['links'] ) ) {
				$attributes['links'] = json_decode( $attributes['links'], true );
			}
			return trx_addons_sc_widget_custom_links( $attributes );
		} else {
			return esc_html__( 'Add at least one link', 'trx_addons' );
		}
	}
}
