<?php
/**
 * Shortcode: Promo block (Gutenberg support)
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
if ( ! function_exists( 'trx_addons_gutenberg_sc_promo_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_promo_editor_assets' );
	function trx_addons_gutenberg_sc_promo_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-promo',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'promo/gutenberg/promo.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'promo/gutenberg/promo.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_promo_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_promo_add_in_gutenberg' );
	function trx_addons_sc_promo_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/promo',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'icon'               => array(
								'type'    => 'string',
								'default' => '',
							),
							'icon_color'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'text_bg_color'      => array(
								'type'    => 'string',
								'default' => '',
							),
							'image'              => array(
								'type'    => 'number',
								'default' => 0,
							),
							'image_url'          => array(
								'type'		=> 'string',
								'default'	=> '',
							),
							'image_bg_color'     => array(
								'type'    => 'string',
								'default' => '',
							),
							'image_cover'        => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'image_position'     => array(
								'type'    => 'string',
								'default' => 'left',
							),
							'image_width'        => array(
								'type'    => 'string',
								'default' => '50%',
							),
							'video_url'          => array(
								'type'    => 'string',
								'default' => '',
							),
							'video_embed'        => array(
								'type'    => 'string',
								'default' => '',
							),
							'video_in_popup'     => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'size'               => array(
								'type'    => 'string',
								'default' => 'normal',
							),
							'full_height'        => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'text_width'         => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'text_float'         => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'text_align'         => array(
								'type'    => 'string',
								'default' => 'none',
							),
							'text_paddings'      => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'text_margins'       => array(
								'type'    => 'string',
								'default' => '',
							),
							'gap'                => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_title(),
						trx_addons_gutenberg_get_param_button(),
						trx_addons_gutenberg_get_param_button2(),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_promo_render_block',
				), 'trx-addons/promo' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_promo_render_block' ) ) {
	function trx_addons_gutenberg_sc_promo_render_block( $attributes = array() ) {
		return trx_addons_sc_promo( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_promo_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_promo_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_promo_get_layouts( $array = array() ) {
		$array['sc_promo'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'sc', 'promo' ), 'trx_sc_promo' );
		return $array;
	}
}


// Add shortcode's specific lists to the JS storage
if ( ! function_exists( 'trx_addons_sc_promo_gutenberg_sc_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_sc_promo_gutenberg_sc_params' );
	function trx_addons_sc_promo_gutenberg_sc_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		// Return list of the image positions
		$vars['sc_promo_positions'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_promo_positions();

		// Return list of the promo's sizes
		$vars['sc_promo_sizes'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_promo_sizes();

		// Return list of the promo text area's widths
		$vars['sc_promo_widths'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_promo_widths();

		return $vars;
	}
}
