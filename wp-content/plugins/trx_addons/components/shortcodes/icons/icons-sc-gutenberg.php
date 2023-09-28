<?php
/**
 * Shortcode: Icons (Gutenberg support)
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
if ( ! function_exists( 'trx_addons_gutenberg_sc_icons_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_icons_editor_assets' );
	function trx_addons_gutenberg_sc_icons_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-icons',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/gutenberg/icons.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'icons/gutenberg/icons.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_icons_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_icons_add_in_gutenberg' );
	function trx_addons_sc_icons_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/icons',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'align'              => array(
								'type'    => 'string',
								'default' => 'center',
							),
							'size'               => array(
								'type'    => 'string',
								'default' => 'medium',
							),
							'color'              => array(
								'type'    => 'string',
								'default' => '',
							),
							'item_title_color'   => array(
								'type'    => 'string',
								'default' => '',
							),
							'item_text_color'    => array(
								'type'    => 'string',
								'default' => '',
							),
							'columns'            => array(
								'type'    => 'number',
								'default' => 1,
							),
							'icons_animation'    => array(
								'type'    => 'boolean',
								'default' => false,
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
						trx_addons_gutenberg_get_param_slider(),
						trx_addons_gutenberg_get_param_title(),
						trx_addons_gutenberg_get_param_button(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_icons_render_block',
				), 'trx-addons/icons' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_icons_render_block' ) ) {
	function trx_addons_gutenberg_sc_icons_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['icons'] ) ) {
			if ( is_string( $attributes['icons'] ) ) {
				$attributes['icons'] = json_decode( $attributes['icons'], true );
			}
			return trx_addons_sc_icons( $attributes );
		} else {			
			return esc_html__( 'Add at least one item', 'trx_addons' );
		}
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_icons_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_icons_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_icons_get_layouts( $array = array() ) {
		$array['sc_icons'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'sc', 'icons' ), 'trx_sc_icons' );
		return $array;
	}
}


// Add shortcode's specific lists to the JS storage
if ( ! function_exists( 'trx_addons_sc_icons_gutenberg_sc_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_sc_icons_gutenberg_sc_params' );
	function trx_addons_sc_icons_gutenberg_sc_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		// Icon position
		$vars['sc_icon_positions'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_icon_positions();
		
		// Return list of the icon's sizes
		$vars['sc_icon_sizes'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_icon_sizes();

		return $vars;
	}
}
