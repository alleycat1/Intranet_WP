<?php
/**
 * Widget: Posts or Revolution slider (Gutenberg support)
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
if ( ! function_exists( 'trx_addons_gutenberg_sc_slider_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_slider_editor_assets' );
	function trx_addons_gutenberg_sc_slider_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-slider',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_WIDGETS . 'slider/gutenberg/slider.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_WIDGETS . 'slider/gutenberg/slider.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}


// Slider
//------------------------------------------------------

// Block register
if ( ! function_exists( 'trx_addons_sc_slider_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_slider_add_in_gutenberg' );
	function trx_addons_sc_slider_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/slider',
				apply_filters('trx_addons_gb_map', array(
					'attributes'      => array_merge(
						array(
							'title'           => array(
								'type'    => 'string',
								'default' => '',
							),
							'engine'          => array(
								'type'    => 'string',
								'default' => 'swiper',
							),
							'slider_id'       => array(
								'type'    => 'string',
								'default' => '',
							),
							'slider_style'    => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'slides_per_view' => array(
								'type'    => 'number',
								'default' => 1,
							),
							'slides_space'    => array(
								'type'    => 'number',
								'default' => 0,
							),
							'slides_parallax' => array(
								'type'    => 'number',
								'default' => 0,
							),
							'slides_type'     => array(
								'type'    => 'string',
								'default' => 'bg',
							),
							'slides_ratio'    => array(
								'type'    => 'string',
								'default' => '16:9',
							),
							'slides_centered' => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'slides_overflow' => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'autoplay'        => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'loop'            => array(
								'type'    => 'boolean',
								'default' => true,
							),
							'speed'           => array(
								'type'    => 'number',
								'default' => 600,
							),
							'interval'        => array(
								'type'    => 'number',
								'default' => 7000,
							),
							'mouse_wheel'     => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'free_mode'       => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'noswipe'         => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'noresize'        => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'effect'          => array(
								'type'    => 'string',
								'default' => 'slide',
							),
							'height'          => array(
								'type'    => 'string',
								'default' => '',
							),
							'alias'           => array(
								'type'    => 'string',
								'default' => '',
							),
							'post_type'       => array(
								'type'    => 'string',
								'default' => 'post',
							),
							'taxonomy'        => array(
								'type'    => 'string',
								'default' => 'category',
							),
							'category'        => array(
								'type'    => 'string',
								'default' => '0',
							),
							'posts'           => array(
								'type'    => 'number',
								'default' => 5,
							),
							'titles'          => array(
								'type'    => 'string',
								'default' => 'center',
							),
							'large'           => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'controls'        => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'controls_pos'    => array(
								'type'    => 'string',
								'default' => 'side',
							),
							'label_prev'      => array(
								'type'    => 'string',
								'default' => esc_html__( 'Prev|PHOTO', 'trx_addons' ),
							),
							'label_next'      => array(
								'type'    => 'string',
								'default' => esc_html__( 'Next|PHOTO', 'trx_addons' ),
							),
							'pagination'      => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'pagination_type' => array(
								'type'    => 'string',
								'default' => 'bullets',
							),
							'pagination_pos'  => array(
								'type'    => 'string',
								'default' => 'bottom',
							),
							'direction'       => array(
								'type'    => 'string',
								'default' => 'horizontal',
							),
							'slides'          => array(
								'type'    => 'string',
								'default' => '',
							),
							'slave_id' => array(
								'type'    => 'string',
								'default' => '',
							),
							// Controller (TOC)
							'controller'      => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'controller_style' => array(
								'type'    => 'string',
								'default' => 'default',
							),
							'controller_pos' => array(
								'type'    => 'string',
								'default' => 'right',
							),
							'controller_controls' => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'controller_effect' => array(
								'type'    => 'string',
								'default' => 'slide',
							),
							'controller_per_view' => array(
								'type'    => 'number',
								'default' => 3,
							),
							'controller_space' => array(
								'type'    => 'number',
								'default' => 0,
							),
							'controller_height' => array(
								'type'    => 'string',
								'default' => '',
							),
							// Rerender
							'reload'          => array(
								'type'    => 'string',
								'default' => '',
							),
						),
						trx_addons_gutenberg_get_param_id()
					),
					'render_callback' => 'trx_addons_gutenberg_sc_slider_render_block',
				), 'trx-addons/slider' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_slider_render_block' ) ) {
	function trx_addons_gutenberg_sc_slider_render_block( $attributes = array() ) {
		if ( ! empty( $attributes['slides'] ) ) {
			if ( is_string( $attributes['slides'] ) ) {
				$attributes['slides'] = json_decode( $attributes['slides'], true );
			}
		}
		return trx_addons_sc_widget_slider( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_slider_get_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_slider_get_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_slider_get_layouts( $array = array() ) {
		$array['sc_slider'] = trx_addons_components_get_allowed_layouts('widgets', 'slider');
		return $array;
	}
}




// Slider Controller
//------------------------------------------------------

// Block register
if ( ! function_exists( 'trx_addons_sc_slider_controller_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_slider_controller_add_in_gutenberg' );
	function trx_addons_sc_slider_controller_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/slider-controller', array(
					'attributes'      => array(
						'slider_id'       => array(
							'type'    => 'string',
							'default' => '',
						),
						'height'      => array(
							'type'    => 'string',
							'default' => '',
						),
						'controls'        => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'controller_style'    => array(
							'type'    => 'string',
							'default' => 'thumbs',
						),
						'effect'          => array(
							'type'    => 'string',
							'default' => 'slide',
						),
						'direction'       => array(
							'type'    => 'string',
							'default' => 'horizontal',
						),
						'slides_per_view' => array(
							'type'    => 'number',
							'default' => 1,
						),
						'slides_space'    => array(
							'type'    => 'number',
							'default' => 0,
						),
						'interval'        => array(
							'type'    => 'number',
							'default' => 7000,
						),
						// ID, Class, CSS attributes
						'id'              => array(
							'type'    => 'string',
							'default' => '',
						),
						'class'           => array(
							'type'    => 'string',
							'default' => '',
						),
						'className'   => array(
							'type'    => 'string',
							'default' => '',
						),
						'css'             => array(
							'type'    => 'string',
							'default' => '',
						),
					),
					'render_callback' => 'trx_addons_gutenberg_sc_slider_controller_render_block',
				)
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_slider_controller_render_block' ) ) {
	function trx_addons_gutenberg_sc_slider_controller_render_block( $attributes = array() ) {
		return trx_addons_sc_slider_controller( $attributes );
	}
}




// Slider Controls
//------------------------------------------------------

// Block register
if ( ! function_exists( 'trx_addons_sc_slider_controls_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_slider_controls_add_in_gutenberg' );
	function trx_addons_sc_slider_controls_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			register_block_type(
				'trx-addons/slider-controls', array(
					'attributes'      => array(
						'slider_id'       => array(
							'type'    => 'string',
							'default' => '',
						),
						'controls_style'    => array(
							'type'    => 'string',
							'default' => 'default',
						),
						'align'      => array(
							'type'    => 'string',
							'default' => 'left',
						),
						'hide_prev'        => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'title_prev'       => array(
							'type'    => 'string',
							'default' => '',
						),
						'hide_next'        => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'title_next'       => array(
							'type'    => 'string',
							'default' => '',
						),
						'pagination_style'       => array(
							'type'    => 'string',
							'default' => 'none',
						),
						// ID, Class, CSS attributes
						'id'              => array(
							'type'    => 'string',
							'default' => '',
						),
						'class'           => array(
							'type'    => 'string',
							'default' => '',
						),
						'className'   => array(
							'type'    => 'string',
							'default' => '',
						),
						'css'             => array(
							'type'    => 'string',
							'default' => '',
						),
					),
					'render_callback' => 'trx_addons_gutenberg_sc_slider_controls_render_block',
				)
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_slider_controls_render_block' ) ) {
	function trx_addons_gutenberg_sc_slider_controls_render_block( $attributes = array() ) {
		return trx_addons_sc_slider_controls( $attributes );
	}
}


// Add shortcode's specific lists to the JS storage
if ( ! function_exists( 'trx_addons_sc_slider_gutenberg_sc_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_sc_slider_gutenberg_sc_params' );
	function trx_addons_sc_slider_gutenberg_sc_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		// Return list of the slider controls positions
		$vars['sc_slider_controls'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_slider_controls();

		// Return list of the slider pagination positions
		$vars['sc_slider_paginations'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_slider_paginations();

		// Prepare lists
		$vars['sliders_list'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_slider_engines();

		// Type of the slides content
		$vars['slides_type'] = array(
			'bg'     => esc_html__( 'Background', 'trx_addons' ),
			'images' => esc_html__( 'Image tag', 'trx_addons' ),
		);

		// Swiper effect
		$vars['sc_slider_effects'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_slider_effects();

		// Direction to change slides
		$vars['sc_slider_directions'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_slider_directions();

		// Paginations
		$vars['sc_slider_paginations_types'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_slider_paginations_types();

		// Paginations for slider controls
		$vars['sc_slider_controls_paginations_types'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_slider_controls_paginations_types();

		// Titles in the Swiper
		$vars['sc_slider_titles'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_slider_titles();

		// Styles of the slider TOC
		$vars['sc_slider_toc_styles'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_slider_toc_styles();

		// Positions of the slider TOC
		$vars['sc_slider_toc_positions'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_slider_toc_positions();

		// Styles of the slider controller
		$vars['sc_slider_controller_styles'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_slider_controller_styles();

		// Styles of the slider controls
		$vars['sc_slider_controls_styles'] = !$is_edit_mode ? array() : trx_addons_get_list_sc_slider_controls_styles();

		return $vars;
	}
}
