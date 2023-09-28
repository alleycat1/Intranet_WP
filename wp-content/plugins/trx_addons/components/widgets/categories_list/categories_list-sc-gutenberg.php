<?php
/**
 * Widget: Categories list (Gutenberg support)
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
if ( ! function_exists( 'trx_addons_gutenberg_sc_categories_list_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_categories_list_editor_assets' );
	function trx_addons_gutenberg_sc_categories_list_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-categories-list',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'categories_list/gutenberg/categories-list.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'categories_list/gutenberg/categories-list.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_categories_list_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_categories_list_add_in_gutenberg' );
	function trx_addons_sc_categories_list_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/categories-list',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'title'         => array(
								'type'    => 'string',
								'default' => esc_html__( 'Categories List', 'trx_addons' )
							),
							'style'         => array(
								'type'    => 'string',
								'default' => '1',
							),
							'number'        => array(
								'type'    => 'number',
								'default' => 5,
							),
							'columns'       => array(
								'type'    => 'number',
								'default' => 5,
							),
							'show_thumbs'   => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'show_posts'    => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'show_children' => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'post_type'     => array(
								'type'    => 'string',
								'default' => 'post',
							),
							'taxonomy'      => array(
								'type'    => 'string',
								'default' => 'category',
							),
							'cat_list'      => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_slider(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_categories_list_render_block',
				), 'trx-addons/categories-list' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_categories_list_render_block' ) ) {
	function trx_addons_gutenberg_sc_categories_list_render_block( $attributes = array() ) {
		return trx_addons_sc_widget_categories_list( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_categories_list_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_categories_list_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_categories_list_get_layouts( $array = array() ) {
		$array['sc_categories_list'] = apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('widgets', 'categories_list'), 'trx_widget_categories_list');
		return $array;
	}
}
