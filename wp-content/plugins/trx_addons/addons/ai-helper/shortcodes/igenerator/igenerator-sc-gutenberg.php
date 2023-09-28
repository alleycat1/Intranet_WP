<?php
/**
 * Shortcode: IGenerator (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v2.20.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

use TrxAddons\AiHelper\Lists;
use TrxAddons\AiHelper\Utils;

// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_igenerator_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_igenerator_editor_assets' );
	function trx_addons_gutenberg_sc_igenerator_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-igenerator',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/gutenberg/igenerator.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_ADDONS . 'ai-helper/shortcodes/igenerator/gutenberg/igenerator.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_igenerator_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_igenerator_add_in_gutenberg' );
	function trx_addons_sc_igenerator_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/igenerator',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'prompt'             => array(
								'type'    => 'string',
								'default' => '',
							),						
							'prompt_width'       => array(
								'type'    => 'number',
								'default' => 100,
							),
							'show_prompt_translated' => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'align'              => array(
								'type'    => 'string',
								'default' => '',
							),						
							'button_text'        => array(
								'type'    => 'string',
								'default' => '',
							),						
							'tags_label'         => array(
								'type'    => 'string',
								'default' => '',
							),						
							'tags'               => array(
								'type'    => 'string',
								'default' => '',
							),						
							'premium'            => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'model'               => array(
								'type'    => 'string',
								'default' => Utils::get_default_image_model(),
							),
							'show_settings'       => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'show_settings_size'  => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'show_limits'        => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'show_download'       => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'show_popup'          => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'number'             => array(
								'type'    => 'number',
								'default' => 3,
							),
							'columns'            => array(
								'type'    => 'number',
								'default' => 3,
							),
							'size'               => array(
								'type'    => 'string',
								'default' => Utils::get_default_image_size( 'sc_igenerator' ),
							),						
							'width'             => array(
								'type'    => 'number',
								'default' => 0,
							),
							'height'            => array(
								'type'    => 'number',
								'default' => 0,
							),
							'demo_thumb_size'  => array(
								'type'    => 'string',
								'default' => apply_filters( 'trx_addons_filter_thumb_size',
													trx_addons_get_thumb_size( 'avatar' ),
													'trx_addons_sc_igenerator',
													array()
												),
							),
							'demo_images'  => array(
								'type'    => 'string',
								'default' => '',
							),
							'demo_images_url'  => array(
								'type'    => 'string',
								'default' => '',
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
					'render_callback' => 'trx_addons_gutenberg_sc_igenerator_render_block',
				), 'trx-addons/igenerator' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_igenerator_render_block' ) ) {
	function trx_addons_gutenberg_sc_igenerator_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['tags'] ) && is_string( $attributes['tags'] ) ) {
			$attributes['tags'] = json_decode( $attributes['tags'], true );
		}
		$attributes['demo_images'] = $attributes['demo_images_url'];
		return trx_addons_sc_igenerator( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_igenerator_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_igenerator_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_igenerator_get_layouts( $array = array() ) {
		$array['sc_igenerator'] = apply_filters( 'trx_addons_sc_type', array( 'default' => __( 'Default', 'trx_addons' ) ), 'trx_sc_igenerator' );
		return $array;
	}
}

// Add shortcode-specific lists to the js vars
if ( ! function_exists( 'trx_addons_gutenberg_sc_igenerator_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_igenerator_params', 10, 1 );
	function trx_addons_gutenberg_sc_igenerator_params( $vars = array() ) {
		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		$vars['sc_igenerator_image_sizes'] = ! $is_edit_mode ? array() : Lists::get_list_ai_image_sizes();
		$vars['sc_igenerator_default_image_size'] = Utils::get_default_image_size( 'sc_igenerator' );
		$vars['sc_igenerator_demo_thumb_size'] = apply_filters( 'trx_addons_filter_thumb_size',
													trx_addons_get_thumb_size( 'avatar' ),
													'trx_addons_sc_igenerator',
													array()
												);
		$vars['sc_igenerator_thumb_sizes'] = ! $is_edit_mode ? array() : trx_addons_get_list_thumbnail_sizes();
		$vars['sc_igenerator_aligns'] = ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns();
		$vars['sc_igenerator_models'] = ! $is_edit_mode ? array() : Lists::get_list_ai_image_models();
		$vars['sc_igenerator_models_sd'] = ! $is_edit_mode ? array() : array_values( array_filter( array_keys( $vars['sc_igenerator_models'] ), function( $key ) { return Utils::is_model_support_image_dimensions( $key ); } ) );
		return $vars;
	}
}
