<?php
/**
 * Shortcode: Display site meta and/or title and/or breadcrumbs (Gutenberg support)
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
if ( ! function_exists( 'trx_addons_gutenberg_sc_layouts_title_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_layouts_title_editor_assets' );
	function trx_addons_gutenberg_sc_layouts_title_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-layout-title',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'title/gutenberg/layout-title.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'title/gutenberg/layout-title.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_layouts_title_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_layouts_title_add_in_gutenberg' );
	function trx_addons_sc_layouts_title_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/layouts-title',
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
							'use_featured_image' => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'height'             => array(
								'type'    => 'string',
								'default' => '',
							),
							'align'              => array(
								'type'    => 'string',
								'default' => '',
							),
							'meta'               => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'title'              => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'breadcrumbs'        => array(
								'type'    => 'boolean',
								'default' => false,
							),
						),
						trx_addons_gutenberg_get_param_hide(true),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_layouts_title_render_block',
				), 'trx-addons/layouts-title' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_layouts_title_render_block' ) ) {
	function trx_addons_gutenberg_sc_layouts_title_render_block( $attributes = array() ) {
		$output = trx_addons_sc_layouts_title( $attributes );
		if ( empty( $output ) && trx_addons_is_preview( 'gutenberg' ) ) {
			return TRX_ADDONS_GUTENBERG_EDITOR_MSG_BLOCK_IS_EMPTY;
		}
		return $output;
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_layouts_title_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_layouts_title_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_layouts_title_get_layouts( $array = array() ) {
		$array['sc_layouts_title'] = apply_filters(
			'trx_addons_sc_type', array(
				'default' => esc_html__( 'Default', 'trx_addons' ),
			), 'trx_sc_layouts_title'
		);
		return $array;
	}
}
