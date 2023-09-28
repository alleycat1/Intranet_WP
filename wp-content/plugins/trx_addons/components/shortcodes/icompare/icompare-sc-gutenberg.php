<?php
/**
 * Shortcode: Images Compare (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.97.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_icompare_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_icompare_editor_assets' );
	function trx_addons_gutenberg_sc_icompare_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-icompare',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'icompare/gutenberg/icompare.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'icompare/gutenberg/icompare.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_icompare_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_icompare_add_in_gutenberg' );
	function trx_addons_sc_icompare_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/icompare',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'image1'             => array(
								'type'    => 'number',
								'default' => 0,
							),
							'image1_url'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'image2'             => array(
								'type'    => 'number',
								'default' => 0,
							),
							'image2_url'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'direction'          => array(
								'type'    => 'string',
								'default' => 'vertical',
							),
							'event'              => array(
								'type'    => 'string',
								'default' => 'drag',
							),						
							'handler'            => array(
								'type'    => 'string',
								'default' => 'round',
							),						
							'handler_separator'  => array(
								'type'    => 'boolean',
								'default' => false,
							),						
							'handler_pos'        => array(
								'type'    => 'number',
								'default' => 50,
							),						
							'icon'               => array(
								'type'    => 'string',
								'default' => '',
							),						
							'handler_image'      => array(
								'type'    => 'number',
								'default' => 0,
							),						
							'handler_image_url'  => array(
								'type'    => 'string',
								'default' => '',
							),						
							'before_text'        => array(
								'type'    => 'string',
								'default' => '',
							),						
							'before_pos'         => array(
								'type'    => 'string',
								'default' => 'tl',
							),						
							'after_text'         => array(
								'type'    => 'string',
								'default' => '',
							),						
							'after_pos'          => array(
								'type'    => 'string',
								'default' => 'br',
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
					'render_callback' => 'trx_addons_gutenberg_sc_icompare_render_block',
				), 'trx-addons/icompare' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_icompare_render_block' ) ) {
	function trx_addons_gutenberg_sc_icompare_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['image1'] ) && ! empty( $attributes['image2'] ) ) {
			return trx_addons_sc_icompare( $attributes );
		} else {
			return esc_html__( 'Select images for states before and after', 'trx_addons' );
		}
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenerg_sc_icompare_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_icompare_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_icompare_get_layouts( $array = array() ) {
		$array['sc_icompare'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'sc', 'icompare' ), 'trx_sc_icompare' );
		return $array;
	}
}

// Add shortcode-specific lists to the js vars
if ( ! function_exists( 'trx_addons_gutenberg_sc_icompare_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_icompare_params', 10, 1 );
	function trx_addons_gutenberg_sc_icompare_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		$vars['sc_icompare_directions']   = ! $is_edit_mode ? array() : trx_addons_get_list_sc_directions();
		$vars['sc_icompare_mouse_events'] = ! $is_edit_mode ? array() : trx_addons_get_list_sc_mouse_events();
		$vars['sc_icompare_handlers']     = ! $is_edit_mode ? array() : trx_addons_get_list_sc_icompare_handlers();
		$vars['sc_icompare_positions']    = ! $is_edit_mode ? array() : trx_addons_get_list_sc_positions();
		return $vars;
	}
}
