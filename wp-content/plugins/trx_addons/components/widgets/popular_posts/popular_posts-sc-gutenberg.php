<?php
/**
 * Widget: Popular posts (Gutenberg support)
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
if ( ! function_exists( 'trx_addons_gutenberg_sc_popular_posts_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_popular_posts_editor_assets' );
	function trx_addons_gutenberg_sc_popular_posts_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-popular-posts',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'popular_posts/gutenberg/popular-posts.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'popular_posts/gutenberg/popular-posts.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_popular_posts_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_popular_posts_add_in_gutenberg' );
	function trx_addons_sc_popular_posts_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/popular-posts',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'title'           => array(
								'type'    => 'string',
								'default' => '',
							),
							'title_1'         => array(
								'type'    => 'string',
								'default' => esc_html__( 'Tab 1', 'trx_addons' ),
							),
							'title_2'         => array(
								'type'    => 'string',
								'default' => esc_html__( 'Tab 2', 'trx_addons' ),
							),
							'title_3'         => array(
								'type'    => 'string',
								'default' => esc_html__( 'Tab 3', 'trx_addons' ),
							),
							'orderby_1'       => array(
								'type'    => 'string',
								'default' => 'views',
							),
							'orderby_2'       => array(
								'type'    => 'string',
								'default' => 'comments',
							),
							'orderby_3'       => array(
								'type'    => 'string',
								'default' => 'likes',
							),
							'post_type_1'     => array(
								'type'    => 'string',
								'default' => 'post',
							),
							'post_type_2'     => array(
								'type'    => 'string',
								'default' => 'post',
							),
							'post_type_3'     => array(
								'type'    => 'string',
								'default' => 'post',
							),
							'taxonomy_1'      => array(
								'type'    => 'string',
								'default' => 'category',
							),
							'taxonomy_2'      => array(
								'type'    => 'string',
								'default' => 'category',
							),
							'taxonomy_3'      => array(
								'type'    => 'string',
								'default' => 'category',
							),
							'cat_1'           => array(
								'type'    => 'number',
								'default' => 0,
							),
							'cat_2'           => array(
								'type'    => 'number',
								'default' => 0,
							),
							'cat_3'           => array(
								'type'    => 'number',
								'default' => 0,
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
					'render_callback' => 'trx_addons_gutenberg_sc_popular_posts_render_block',
				), 'trx-addons/popular-posts' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_popular_posts_render_block' ) ) {
	function trx_addons_gutenberg_sc_popular_posts_render_block( $attributes = array() ) {
		return trx_addons_sc_widget_popular_posts( $attributes );
	}
}
