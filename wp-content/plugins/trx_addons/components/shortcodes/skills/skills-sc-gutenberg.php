<?php
/**
 * Shortcode: Skills (Gutenberg support)
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
if ( ! function_exists( 'trx_addons_gutenberg_sc_skills_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_skills_editor_assets' );
	function trx_addons_gutenberg_sc_skills_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-skills',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/gutenberg/skills.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'skills/gutenberg/skills.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_skills_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_skills_add_in_gutenberg' );
	function trx_addons_sc_skills_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/skills',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'type'               => array(
								'type'    => 'string',
								'default' => 'counter',
							),
							'style'              => array(
								'type'    => 'string',
								'default' => 'replace',
							),
							'icon_position'      => array(
								'type'    => 'string',
								'default' => 'top',
							),
							'cutout'             => array(
								'type'    => 'number',
								'default' => 92,
							),
							'compact'            => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'color'              => array(
								'type'    => 'string',
								'default' => '',
							),
							'icon_color'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'item_title_color'   => array(
								'type'    => 'string',
								'default' => '',
							),
							'back_color'         => array(
								'type'    => 'string',
								'default' => '',
							),
							'border_color'       => array(
								'type'    => 'string',
								'default' => '',
							),
							'max'                => array(
								'type'    => 'number',
								'default' => 100,
							),
							'columns'            => array(
								'type'    => 'number',
								'default' => 1,
							),
							'values'             => array(
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
					'render_callback' => 'trx_addons_gutenberg_sc_skills_render_block',
				), 'trx-addons/skills' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_skills_render_block' ) ) {
	function trx_addons_gutenberg_sc_skills_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['values'] ) ) {
			if ( is_string( $attributes['values'] ) ) {
				$attributes['values'] = json_decode( $attributes['values'], true );
			}
			return trx_addons_sc_skills( $attributes );
		} else {
			return esc_html__( 'Add at least one item', 'trx_addons' );
		}
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_skills_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_skills_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_skills_get_layouts( $array = array() ) {
		$array['sc_skills'] = apply_filters( 'trx_addons_sc_type', trx_addons_components_get_allowed_layouts( 'sc', 'skills' ), 'trx_sc_skills' );
		$array['sc_skills_counter_styles'] = apply_filters( 'trx_addons_sc_style', trx_addons_get_list_sc_skills_counter_styles(), 'trx_sc_skills' );
		$array['sc_skills_counter_icon_positions'] = apply_filters( 'trx_addons_sc_skills_icon_positions', trx_addons_get_list_sc_skills_counter_icon_positions() );
		return $array;
	}
}
