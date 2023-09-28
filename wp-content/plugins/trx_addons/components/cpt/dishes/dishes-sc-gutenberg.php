<?php
/**
 * ThemeREX Addons Custom post type: Dishes (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.6.09
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_dishes_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_dishes_editor_assets' );
	function trx_addons_gutenberg_sc_dishes_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-dishes',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_CPT . 'dishes/gutenberg/dishes.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_CPT . 'dishes/gutenberg/dishes.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_dishes_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_dishes_add_in_gutenberg' );
	function trx_addons_sc_dishes_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/dishes', 
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'          => array(
								'type'    => 'string',
								'default' => 'default'
							),
							'featured_position'          => array(
								'type'    => 'string',
								'default' => 'top'
							),
							'no_margin'          => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'more_text'          => array(
								'type'    => 'string',
								'default' => esc_html__( 'Read more', 'trx_addons' ),
							),
							'pagination'          => array(
								'type'    => 'string',
								'default' => 'none'
							),
							'hide_excerpt'          => array(
								'type'    => 'boolean',
								'default' => false
							),
							'popup'          => array(
								'type'    => 'boolean',
								'default' => false
							),
							'cat'          => array(
								'type'    => 'string',
								'default' => '0'
							),
						),
						trx_addons_gutenberg_get_param_query(),
						trx_addons_gutenberg_get_param_slider(),
						trx_addons_gutenberg_get_param_title(),
						trx_addons_gutenberg_get_param_button(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_dishes_render_block',
				), 'trx-addons/dishes' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_dishes_render_block' ) ) {
	function trx_addons_gutenberg_sc_dishes_render_block( $attributes = array() ) {
		return trx_addons_sc_dishes( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_dishes_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_dishes_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_dishes_get_layouts( $array = array() ) {
		$array['trx_sc_dishes'] = apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'dishes', 'sc'), 'trx_sc_dishes');

		return $array;
	}
}

// Add shortcode's specific vars to the JS storage
if ( ! function_exists( 'trx_addons_gutenberg_sc_dishes_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_dishes_params' );
	function trx_addons_gutenberg_sc_dishes_params( $vars = array() ) {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {

			// If editor is active now
			$is_edit_mode = trx_addons_is_post_edit();

			// Dishes categories
			$vars['sc_dishes_cat']    = !$is_edit_mode ? array() : trx_addons_get_list_terms( false, TRX_ADDONS_CPT_DISHES_TAXONOMY );
			$vars['sc_dishes_cat'][0] = trx_addons_get_not_selected_text( esc_html__( 'Select category', 'trx_addons' ) );

			// Dishes positions
			$vars['sc_dishes_positions'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_dishes_positions();

			return $vars;
		}
	}
}
