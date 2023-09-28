<?php
/**
 * Image hover effects, based on curtain.js library
 *
 * @addon image-effects
 * @version 1.7
 *
 * @package ThemeREX Addons
 * @since v1.85.0
 */


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_image_effects_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_image_effects_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_image_effects_load_scripts_front', 10, 1 );
	function trx_addons_image_effects_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'image_effects', $force, array(
			'lib' => array(
				'js' => array(
					'curtains' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'image-effects/curtains/curtains.min.js' ),
				)
			),
			'css'  => array(
				'trx_addons-image-effects' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'image-effects/image-effects.css' ),
			),
			'js' => array(
				'trx_addons-image-effects' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'image-effects/image-effects.js', 'deps' => 'jquery' ),
			),
		) );
	}
}

// Enqueue responsive styles for frontend
if ( ! function_exists( 'trx_addons_image_effects_load_scripts_front_responsive' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_image_effects_load_scripts_front_responsive', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY );
	add_action( 'trx_addons_action_load_scripts_front_image_effects', 'trx_addons_image_effects_load_scripts_front_responsive', 10, 1 );
	function trx_addons_image_effects_load_scripts_front_responsive( $force = false ) {
		trx_addons_enqueue_optimized_responsive( 'image_effects', $force, array(
			'css'  => array(
				'trx_addons-image-effects-responsive' => array(
					'src' => TRX_ADDONS_PLUGIN_ADDONS . 'image-effects/image-effects.responsive.css',
					'media' => 'lg'
				),
			),
		) );
	}
}


// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_image_effects_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_image_effects_merge_styles');
	function trx_addons_image_effects_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'image-effects/image-effects.css' ] = false;
		return $list;
	}
}

// Merge styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_image_effects_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_image_effects_merge_styles_responsive');
	function trx_addons_image_effects_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'image-effects/image-effects.responsive.css' ] = false;
		return $list;
	}
}

	
// Merge specific scripts to the single file
if ( !function_exists( 'trx_addons_image_effects_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_image_effects_merge_scripts');
	function trx_addons_image_effects_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'image-effects/image-effects.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( !function_exists( 'trx_addons_image_effects_check_in_html_output' ) ) {
	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_image_effects_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_image_effects_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_image_effects_check_in_html_output', 10, 1 );
	function trx_addons_image_effects_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'data-image-effect'
			)
		);
		if ( trx_addons_check_in_html_output( 'image_effects', $content, $args ) ) {
			trx_addons_image_effects_load_scripts_front( true );
		}
		// Load TweenMax if image effects present on the page
		if ( strpos( $content, 'data-image-effect="on_' ) !== false ) {
			trx_addons_enqueue_tweenmax();
		}
		return $content;
	}
}


// Load TweenMax always to use it for ease change values
// If it's not loaded - internal easing is used
//add_filter( 'trx_addons_filter_load_tweenmax', '__return_true' );


// Return list of image effects
if ( ! function_exists( 'trx_addons_image_effects_list' ) ) {
	function trx_addons_image_effects_list( $add_none ) {
		$list = apply_filters( 'trx_addons_filter_image_effects', array(
										'on_ripple'  => esc_html__( 'Ripple', 'trx_addons' ),
										'on_ripple2' => esc_html__( 'Ripple 2', 'trx_addons' ),
										'on_waves'   => esc_html__( 'Waves', 'trx_addons' ),
										'on_waves2'  => esc_html__( 'Waves 2', 'trx_addons' ),
										'on_smudge'  => esc_html__( 'Smudge', 'trx_addons' ),
										'on_swap'    => esc_html__( 'Swap', 'trx_addons' ),
										'on_fish'    => esc_html__( 'Fish', 'trx_addons' ),
										'on_blot'    => esc_html__( 'Blot', 'trx_addons' ),
										'on_tint'    => esc_html__( 'Tint', 'trx_addons' ),
										'on_scroller'=> esc_html__( 'Scroller', 'trx_addons' ),
									) );
		return $add_none ? trx_addons_array_merge( array('' => esc_html__('None', 'trx_addons')), $list ) : $list;
	}
}

// Return url of the displacement image by the method number
if ( ! function_exists( 'trx_addons_image_effects_displacement_url' ) ) {
	function trx_addons_image_effects_displacement_url( $method ) {
		if ( $method == 'random' ) {
			$method = mt_rand( 0, 16 );
		}
		return $method === 'none' ? '' : esc_url( trx_addons_get_file_url( TRX_ADDONS_PLUGIN_ADDONS . 'image-effects/images/' . max( 0, min( 16, $method ) ) . '.jpg' ) );
	}
}


//========================================================================
//  Add hover effects to the theme's hovers list
//========================================================================

// Filter to return image effects
if ( ! function_exists( 'trx_addons_image_effects_custom_hover_list' ) ) {
	add_filter( 'trx_addons_filter_custom_hover_list', 'trx_addons_image_effects_custom_hover_list' );
	function trx_addons_image_effects_custom_hover_list( $list ) {
		return trx_addons_array_merge( $list, trx_addons_image_effects_list(false) );
	}
}

// Add class to the 'featured_image' block
if ( ! function_exists( 'trx_addons_image_effects_custom_hover_featured_classes' ) ) {
	add_filter( 'trx_addons_filter_post_featured_classes', 'trx_addons_image_effects_custom_hover_featured_classes', 10, 3 );
	function trx_addons_image_effects_custom_hover_featured_classes( $classes, $args, $mode ) {
		if ( $mode != 'singular' && ! empty( $args['hover'] ) && in_array( $args['hover'], array_keys( trx_addons_image_effects_list(false) ) ) ) {
			$classes .= ' trx_addons_image_effects_' . esc_attr( $args['hover'] );
		}
		return $classes;
	}
}

// Add data-parameters to the 'featured_image' block
if ( ! function_exists( 'trx_addons_image_effects_custom_hover_featured_data' ) ) {
	add_filter( 'trx_addons_filter_post_featured_data', 'trx_addons_image_effects_custom_hover_featured_data', 10, 3 );
	function trx_addons_image_effects_custom_hover_featured_data( $data, $args, $mode ) {
		if ( $mode != 'singular' && ! empty( $args['hover'] ) && in_array( $args['hover'], array_keys( trx_addons_image_effects_list(false) ) ) && is_array( $data ) ) {
			// Load scripts and styles
			trx_addons_image_effects_load_scripts_front( true );
			// Disable lazy-load if image effects are used
			trx_addons_lazy_load_off();
			// Add an image effect to the data attributes
			$data['image-effect'] = $args['hover'];
			$data['image-effect-scale'] = apply_filters( 'trx_addons_filter_image_effects_scale_featured', 1 );
			$data['image-effect-strength'] = apply_filters( 'trx_addons_filter_image_effects_strength_featured', 30 );
			if ( in_array( $args['hover'], array('on_ripple', 'on_ripple2', 'on_blot', 'on_scroller') ) ) {
				$data['image-effect-waves-direction'] = apply_filters( 'trx_addons_filter_image_effects_waves_direction', $args['hover'] == 'on_ripple' ? 1 : 0 );
			}
			// Add a displacement image
			if ( in_array( $args['hover'], array('on_ripple2', 'on_swap', 'on_fish') ) ) {
				$displacements = array(
					'on_ripple2' => 0,
					'on_swap'    => 'random',
					'on_fish'    => 'none'
				);
				$data['image-effect-displacement'] = trx_addons_image_effects_displacement_url( $displacements[ $args['hover'] ] );
			}
			// Add a replacement (secondary) image
			if ( in_array( $args['hover'], array('on_swap', 'on_fish', 'on_blot') ) ) {
				$image_url = '';
				if ( function_exists('trx_addons_get_secondary_image_url') ) {
					$image_url = trx_addons_get_secondary_image_url( $args['thumb_size'] );
				}
				if ( empty( $image_url ) ) {
					if ( empty( $args['thumb_id'] ) ) {
						$args['thumb_id'] = get_post_thumbnail_id( get_the_ID() );
					}
					if ( ! empty( $args['thumb_id'] ) ) {
						$image = wp_get_attachment_image_src( $args['thumb_id'], $args['thumb_size'] );
						$image_url = empty( $image[0] ) ? '' : $image[0];
					}
				}
				$data['image-effect-swap-image'] = $image_url;
			}
			if ( in_array( $args['hover'], array('on_tint', 'on_blot') ) ) {
				$data['image-effect-tint-color'] = empty( $args['hover_tint_color'] )
																? apply_filters('trx_addons_filter_get_theme_accent_color', '#efa758', 'hover_tint_color')
																: $args['hover_tint_color'];
			}
		}
		return $data;
	}
}

// Add images to the 'featured_image' block
if ( ! function_exists( 'trx_addons_image_effects_custom_hover_icons' ) ) {
	add_action( 'trx_addons_action_custom_hover_icons', 'trx_addons_image_effects_custom_hover_icons', 10, 2 );
	function trx_addons_image_effects_custom_hover_icons( $args, $hover ) {
		if ( in_array( $hover, array_keys( trx_addons_image_effects_list(false) ) ) && ! empty( $args['thumb_bg'] ) ) {
			if ( ! empty( $args['image'] ) ) {
				?><img src="<?php echo esc_url( $args['image'] ); ?>" class="trx_addons_image_effect_original_image" /><?php
			} else {
				echo wp_get_attachment_image( get_post_thumbnail_id( get_the_ID() ), $args['thumb_size'], false, array( 'class' => 'trx_addons_image_effect_original_image' ) );
			}
		}
	}
}



//========================================================================
//  Add hover effects to the shortcode 'Image' from Elementor
//========================================================================

// Add 'image_effect' to the 'Image' and 'Image Box' params
if ( ! function_exists( 'trx_addons_image_effects_add_image_param_in_elementor' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_image_effects_add_image_param_in_elementor', 10, 3 );
	function trx_addons_image_effects_add_image_param_in_elementor( $element, $section_id, $args ) {

		if ( ! is_object($element) ) return;

		$el_name = $element->get_name();

		if ( in_array( $el_name, array( 'image', 'image-box' ) ) && 'section_image' === $section_id ) {
			$element->add_control( 'trx_addons_image_effects_heading', array(
									'type' => \Elementor\Controls_Manager::HEADING,
									'label' => esc_html__( 'Image effects', 'trx_addons' ),
									'separator' => 'before',
			) );
			$element->add_control( 'trx_addons_image_effect', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Effect on mouse hover", 'trx_addons'),
									'options' => apply_filters( 'trx_addons_filter_image_effects', trx_addons_image_effects_list(true) ),
									'prefix_class' => 'trx_addons_image_effects_',
									'default' => '',
			) );

			$element->add_control( 'trx_addons_image_effect_scale', array(
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label' => __("Scale on hover", 'trx_addons'),
				'label_on' => __( 'On', 'trx_addons' ),
				'label_off' => __( 'Off', 'trx_addons' ),
				'return_value' => '1',
				'default' => '',
				'condition' => array(
					'trx_addons_image_effect!' => '',
				),
			) );

			$element->add_control( 'trx_addons_image_effect_strength', array(
				'label' => __( 'Strength', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => 20,
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 5,
						'max' => 50
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'trx_addons_image_effect' => array('on_waves', 'on_waves2', 'on_ripple', 'on_ripple2', 'on_smudge', 'on_blot', 'on_scroller')
				),
			) );

			$element->add_control( 'trx_addons_image_effect_waves_factor', array(
				'label' => __( 'Waves frequency', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => 4,
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 1,
						'max' => 10
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'trx_addons_image_effect' => array('on_waves', 'on_waves2')
				),
			) );

			$element->add_control( 'trx_addons_image_effect_waves_direction', array(
				'label' => __( 'Direction', 'trx_addons' ),
				'description' => __( 'Ripple direction: 0 - horizontal, 100 - vertical', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => 100,
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'trx_addons_image_effect' => array('on_ripple', 'on_ripple2', 'on_blot', 'on_scroller')
				),
			) );

			$element->add_control( 'trx_addons_image_effect_tint_color', array(
				'label' => __( 'Tint color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				// Not used, because global colors are not compatible with tint
				'global' => array(
					'active' => false,
				),
				'condition' => array(
					'trx_addons_image_effect' => array( 'on_tint', 'on_blot' ),
				),
			) );

			$element->add_control( 'trx_addons_image_effect_swap_image', array(
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => __( 'Swap image', 'trx_addons' ),
				'default' => array(
					'url' => '',
				),
				'condition' => array(
					'trx_addons_image_effect' => array('on_swap', 'on_fish', 'on_blot')
				),
			) );

			$element->add_control( 'trx_addons_image_effect_displacement', array(
				'type' => \Elementor\Controls_Manager::MEDIA,
				'label' => __( 'Displacement texture', 'trx_addons' ),
				'default' => array(
					'url' => '',
				),
				'condition' => array(
					'trx_addons_image_effect' => array( 'on_ripple2', 'on_swap', 'on_fish' )
				),
			) );

			$element->add_control( 'trx_addons_image_effect_displacement_method', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Displacement method', 'trx_addons' ),
				'default' => 0,
				'options' => apply_filters( 'trx_addons_filter_image_effect_displacement_methods',
											trx_addons_array_merge(
												array(
													'none' => __( 'None', 'trx_addons' ),
													'random' => __( 'Random', 'trx_addons' )
												),
												trx_addons_get_list_range( 0, 16 )
											) ),
				'condition' => array(
					'trx_addons_image_effect' => array( 'on_ripple2', 'on_swap', 'on_fish' ),
					'trx_addons_image_effect_displacement[url]' => ''
				),
			) );

		}
	}
}

// Add "data-image-effects" to the wrapper of the widget "Image"
if ( !function_exists( 'trx_addons_image_effects_before_render_elements' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_image_effects_before_render_elements', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_image_effects_before_render_elements', 10, 1 );
	function trx_addons_image_effects_before_render_elements($element) {
		if ( is_object( $element ) ) {
			$el_name = $element->get_name();
			if ( in_array( $el_name, array( 'image', 'image-box' ) ) ) {
				//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
				$image_effect = $element->get_settings( 'trx_addons_image_effect' );
				if ( ! empty( $image_effect ) ) {
					$settings = $element->get_settings();
					// Load scripts and styles
					trx_addons_image_effects_load_scripts_front( true );
					// Add data-parameters to the image wrapper
					$data = array(
						'data-image-effect' => $settings['trx_addons_image_effect'],
						'data-image-effect-scale' => (int) $settings['trx_addons_image_effect_scale'],
						'data-image-effect-strength' => 60 - max( 5, min( 50, $settings['trx_addons_image_effect_strength']['size'] ) ),
					);
					if ( in_array( $settings['trx_addons_image_effect'], array('on_waves', 'on_waves2') ) ) {
						$data['data-image-effect-waves-factor'] = isset( $settings['trx_addons_image_effect_waves_factor']['size'] )
																		? max( 1, min( 10, $settings['trx_addons_image_effect_waves_factor']['size'] ) )
																		: 4;
					}
					if ( in_array( $settings['trx_addons_image_effect'], array('on_ripple', 'on_ripple2', 'on_blot', 'on_scroller') ) ) {
						$data['data-image-effect-waves-direction'] = isset( $settings['trx_addons_image_effect_waves_direction']['size'] )
																		? $settings['trx_addons_image_effect_waves_direction']['size'] / 100
																		: 1;
					}
					if ( in_array( $settings['trx_addons_image_effect'], array('on_ripple2', 'on_swap', 'on_fish') ) ) {
						if ( ! empty( $settings['trx_addons_image_effect_displacement']['url'] ) ) {
							$data['data-image-effect-displacement'] = $settings['trx_addons_image_effect_displacement']['url'];
						} else if ( isset( $settings['trx_addons_image_effect_displacement_method'] ) ) {
							$data['data-image-effect-displacement'] = trx_addons_image_effects_displacement_url( $settings['trx_addons_image_effect_displacement_method'] );
						} else {
							$data['data-image-effect-displacement'] = '';
						}
					}
					if ( in_array( $settings['trx_addons_image_effect'], array('on_swap', 'on_fish', 'on_blot') ) ) {
						$data['data-image-effect-swap-image'] = $settings['trx_addons_image_effect_swap_image']['url'];
					}
					if ( in_array( $settings['trx_addons_image_effect'], array('on_tint', 'on_blot') ) ) {
						$data['data-image-effect-tint-color'] = empty( $settings['trx_addons_image_effect_tint_color'] )
																	? ''
																	: $settings['trx_addons_image_effect_tint_color'];
					}
					$element->add_render_attribute( '_wrapper', apply_filters( 'trx_addons_filter_image_effect_data', $data, $element ) );
				}
			}
		}
	}
}

// Replace loading="lazy" with loading="eager" for an image tags inside an Elementor's widget "Image" if the image effect is enabled.
// This image tag is generated by Elementor (not by core WP functions)
if ( ! function_exists( 'trx_addons_image_effects_change_image_loading' ) ) {
	add_filter( 'elementor/image_size/get_attachment_image_html', 'trx_addons_image_effects_change_image_loading', 10, 4 );
	function trx_addons_image_effects_change_image_loading( $html, $settings, $image_size_key='', $image_key='' ) {
		if ( ! empty( $settings['trx_addons_image_effect'] ) ) {
			$html = str_replace( 'loading="lazy"', 'loading="eager"', $html );
		}
		return $html;
	}
}

// Remove loading="lazy" from the image tag if the image effect is enabled after the page is generated.
// Needs to remove lazy load added to the image tag by 3rd party plugins or WordPress 6.1+ (add lazy load
// to all images in the content if the image tag is not contain attribute loading)
if ( ! function_exists( 'trx_addons_image_effects_remove_lazy_load_from_page_content' ) ) {
	add_action( 'after_setup_theme', 'trx_addons_image_effects_remove_lazy_load_from_page_content', 10 );
	function trx_addons_image_effects_remove_lazy_load_from_page_content() {
		ob_start( function( $html ) {
			return preg_replace( '/(<div[^>]*data-image-effect[^>]*>[\s]*(<div[^>]*>[\s]*<div[^>]*>[\s]*)?<img[^>]*)(loading="lazy")/i', '${1}loading="eager"', $html );
		} );
	}
}
