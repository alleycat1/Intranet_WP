<?php
/**
 * ThemeREX Addons Posts and Comments Reviews (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.57
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_reviews_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_reviews_editor_assets' );
	function trx_addons_gutenberg_sc_reviews_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-reviews',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_REVIEWS . 'gutenberg/reviews.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_REVIEWS . 'gutenberg/reviews.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_reviews_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_reviews_add_in_gutenberg' );
	function trx_addons_reviews_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/reviews',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'          => array(
								'type'    => 'string',
								'default' => 'short'
							),
							'align'         => array(
								'type'    => 'string',
								'default' => 'right'
							),
						),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_reviews_render_block',
				), 'trx-addons/reviews' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_reviews_render_block' ) ) {
	function trx_addons_gutenberg_sc_reviews_render_block( $attributes = array() ) {
		return trx_addons_sc_reviews( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_reviews_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_reviews_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_reviews_get_layouts( $array = array() ) {
		$array['trx_sc_reviews'] = apply_filters('trx_addons_sc_type', trx_addons_reviews_sc_type_list(), 'trx_sc_reviews');
		return $array;
	}
}
