<?php
/**
 * Widget: Recent posts (Gutenberg support)
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
if ( ! function_exists( 'trx_addons_gutenberg_sc_recent_posts_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_recent_posts_editor_assets' );
	function trx_addons_gutenberg_sc_recent_posts_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-recent-posts',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'recent_posts/gutenberg/recent-posts.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'recent_posts/gutenberg/recent-posts.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_recent_posts_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_recent_posts_add_in_gutenberg' );
	function trx_addons_sc_recent_posts_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/recent-posts',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'title'           => array(
								'type'    => 'string',
								'default' => '',
							),
							'number'          => array(
								'type'    => 'number',
								'default' => 4,
							),
							'show_date'       => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'show_image'      => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'show_author'     => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'show_counters'   => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'show_categories' => array(
								'type'    => 'boolean',
								'default' => true,
							),
						),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_recent_posts_render_block',
				), 'trx-addons/recent-posts' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_recent_posts_render_block' ) ) {
	function trx_addons_gutenberg_sc_recent_posts_render_block( $attributes = array() ) {
		return trx_addons_sc_widget_recent_posts( $attributes );
	}
}
