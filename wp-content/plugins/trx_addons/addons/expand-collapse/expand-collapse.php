<?php
/**
 * Expand / Collapse any Elementor's module
 *
 * @addon expand-collapse
 * @version 1.1
 *
 * @package ThemeREX Addons
 * @since v2.6.1
 */


// Load required styles and scripts for the frontend
if ( ! function_exists( 'trx_addons_expand_collapse_load_scripts_front' ) ) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_expand_collapse_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY );
	add_action( 'trx_addons_action_pagebuilder_preview_scripts', 'trx_addons_expand_collapse_load_scripts_front', 10, 1 );
	function trx_addons_expand_collapse_load_scripts_front( $force = false ) {
		trx_addons_enqueue_optimized( 'expand_collapse', $force, array(
			'css'  => array(
				'trx_addons-expand-collapse' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'expand-collapse/expand-collapse.css' ),
			),
			'js' => array(
				'trx_addons-expand-collapse' => array( 'src' => TRX_ADDONS_PLUGIN_ADDONS . 'expand-collapse/expand-collapse.js', 'deps' => 'jquery' ),
			)
		) );
	}
}

	
// Merge styles to the single stylesheet
if ( ! function_exists( 'trx_addons_expand_collapse_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_expand_collapse_merge_styles');
	function trx_addons_expand_collapse_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'expand-collapse/expand-collapse.css' ] = false;
		return $list;
	}
}

	
// Merge specific scripts into single file
if ( ! function_exists( 'trx_addons_expand_collapse_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_expand_collapse_merge_scripts');
	function trx_addons_expand_collapse_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_ADDONS . 'expand-collapse/expand-collapse.js' ] = false;
		return $list;
	}
}

// Load styles and scripts if present in the cache of the menu or layouts or finally in the whole page output
if ( ! function_exists( 'trx_addons_expand_collapse_check_in_html_output' ) ) {
//	add_filter( 'trx_addons_filter_get_menu_cache_html', 'trx_addons_audio_effects_check_in_html_output', 10, 1 );
//	add_action( 'trx_addons_action_show_layout_from_cache', 'trx_addons_audio_effects_check_in_html_output', 10, 1 );
	add_action( 'trx_addons_action_check_page_content', 'trx_addons_expand_collapse_check_in_html_output', 10, 1 );
	function trx_addons_expand_collapse_check_in_html_output( $content = '' ) {
		$args = array(
			'check' => array(
				'trx_expcol_on'
			)
		);
		if ( trx_addons_check_in_html_output( 'expand_collapse', $content, $args ) ) {
			trx_addons_expand_collapse_load_scripts_front( true );
		}
		return $content;
	}
}

// Add "Expand-Collapse" params to all elements
if ( ! function_exists( 'trx_addons_elm_add_params_expand_collapse' ) ) {
	add_action( 'elementor/element/before_section_start', 'trx_addons_elm_add_params_expand_collapse', 10, 3 );
	add_action( 'elementor/widget/before_section_start', 'trx_addons_elm_add_params_expand_collapse', 10, 3 );
	function trx_addons_elm_add_params_expand_collapse( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) return;

		if ( in_array( $element->get_name(), array( 'section', 'column', 'common' ) ) && $section_id == '_section_responsive' ) {
			
			$element->start_controls_section( 'section_trx_expand_collapse', array(
																		'tab' => !empty($args['tab']) ? $args['tab'] : \Elementor\Controls_Manager::TAB_ADVANCED,
																		'label' => __( 'Expand / Collapse', 'trx_addons' )
																	) );

			$element->add_control( 'trx_expcol_allow', array(
				'label' => __( 'Allow Expand/Collapse', 'trx_addons' ),
				'label_on' => __( 'On', 'trx_addons' ),
				'label_off' => __( 'Off', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'return_value' => 'on',
				'prefix_class' => 'trx_expcol_',
			) );

			$element->add_control( 'trx_expcol_state', array(
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => __( 'Initial State', 'trx_addons' ),
				'label_block' => false,
				'options' => apply_filters( 'trx_addons_filter_expand_collapse_states', array(
					'collapsed' => esc_html__( 'Collapsed', 'trx_addons' ),
					'expanded'  => esc_html__( 'Expanded', 'trx_addons' ),
				) ),
				'default' => 'collapsed',
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
				'prefix_class' => 'trx_expcol_state_',
			) );

			$element->add_control( 'trx_expcol_gradient', array(
				'label' => __( 'Gradient', 'trx_addons' ),
				'label_on' => __( 'Show', 'trx_addons' ),
				'label_off' => __( 'Hide', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
				'default' => 'on',
				'return_value' => 'on',
				'prefix_class' => 'trx_expcol_gradient_',
			) );

			$element->add_control( 'trx_expcol_gradient_color', array(
				'label' => __( 'Gradient Color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'condition' => array(
					'trx_expcol_allow' => 'on',
					'trx_expcol_gradient' => 'on'
				),
				'selectors' => array(
					'{{WRAPPER}}:after' => 'background: -webkit-gradient(linear,left top,left bottom,color-stop(0,{{VALUE}}00),to({{VALUE}}));'
											. 'background: -webkit-linear-gradient(top,{{VALUE}}00 0,{{VALUE}} 100%);'
											. 'background: linear-gradient(to bottom,{{VALUE}}00 0,{{VALUE}} 100%);',
					'{{WRAPPER}} .trx_expcol_button' => 'background-color: {{VALUE}}E6;'
				),
				'global' => array(
					'active' => false,
				),
			) );

			$element->add_control( 'trx_expcol_gradient_size', array(
				'label' => __( 'Gradient Size', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => '50',
					'unit' => '%'
				),
				'range' => array(
					'%' => array(
						'min' => 0,
						'max' => 100,
						'step' => 1
					),
				),
				'size_units' => array( '%' ),
				'condition' => array(
					'trx_expcol_allow' => 'on',
					'trx_expcol_gradient' => 'on'
				),
				'selectors' => array(
					'{{WRAPPER}}.trx_expcol_gradient_on:after' => 'height: {{SIZE}}%;'
				),
			) );

			$element->add_responsive_control( 'trx_expcol_collapsed_height', array(
				'label' => __( 'Collapsed Height', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => '',
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 50,
						'max' => 1000,
						'step' => 1
					),
					'em' => array(
						'min' => 5,
						'max' => 100,
						'step' => 0.1
					),
					'vh' => array(
						'min' => 1,
						'max' => 100,
						'step' => 0.1
					),
				),
				'size_units' => array( 'px', 'em', 'vh' ),
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
				'selectors' => array(
					'{{WRAPPER}}.trx_expcol_state_collapsed' => 'max-height: {{SIZE}}{{UNIT}};'
				),
			) );

			$element->add_control( 'trx_expcol_button_border_size', array(
				'label' => __( 'Button Border Size', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0,
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
						'step' => 1
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
				'selectors' => array(
					'{{WRAPPER}} .trx_expcol_button' => 'border-width: {{SIZE}}px;'
				),
			) );

			$element->add_control( 'trx_expcol_button_border_radius', array(
				'label' => __( 'Button Border Radius', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0,
					'unit' => 'px'
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
						'step' => 1
					),
				),
				'size_units' => array( 'px' ),
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
				'selectors' => array(
					'{{WRAPPER}} .trx_expcol_button' => 'border-radius: {{SIZE}}px;'
				),
			) );

			// Collapsed state
			$element->add_control( 'trx_expcol_collapsed_heading', array(
				'type' => \Elementor\Controls_Manager::HEADING,
				'label' => esc_html__( 'Collapsed Button', 'trx_addons' ),
				'separator' => 'before',
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
			) );

			$element->add_control( 'trx_expcol_collapsed_title', array(
				'label' => __( 'Title', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
			) );

			$element->add_control( 'trx_expcol_collapsed_title_color', array(
				'label' => __( 'Title Color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
				'selectors' => array(
					'{{WRAPPER}}.trx_expcol_state_collapsed .trx_expcol_button' => 'color: {{VALUE}};'
				),
//					'global' => array(
//						'active' => false,
//					),
			) );

			$element->add_control( 'trx_expcol_collapsed_bg_color', array(
				'label' => __( 'Background Color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
				'selectors' => array(
					'{{WRAPPER}}.trx_expcol_state_collapsed .trx_expcol_button' => 'background-color: {{VALUE}};'
				),
//					'global' => array(
//						'active' => false,
//					),
			) );

			$element->add_control( 'trx_expcol_collapsed_bd_color', array(
				'label' => __( 'Border Color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
				'selectors' => array(
					'{{WRAPPER}}.trx_expcol_state_collapsed .trx_expcol_button' => 'border-color: {{VALUE}};'
				),
//					'global' => array(
//						'active' => false,
//					),
			) );

			$params = trx_addons_get_icon_param();
			$params = trx_addons_array_get_first_value( $params );
			unset( $params['name'] );
			$params['condition'] = array(
				'trx_expcol_allow' => 'on'
			);
			$element->add_control( 'trx_expcol_collapsed_icon', $params );

			// Expanded state
			$element->add_control( 'trx_expcol_expanded_heading', array(
				'type' => \Elementor\Controls_Manager::HEADING,
				'label' => esc_html__( 'Expanded Button', 'trx_addons' ),
				'separator' => 'before',
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
			) );

			$element->add_control( 'trx_expcol_expanded_title', array(
				'label' => __( 'Title', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => '',
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
			) );

			$element->add_control( 'trx_expcol_expanded_title_color', array(
				'label' => __( 'Title Color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
				'selectors' => array(
					'{{WRAPPER}}.trx_expcol_state_expanded .trx_expcol_button' => 'color: {{VALUE}};'
				),
//					'global' => array(
//						'active' => false,
//					),
			) );

			$element->add_control( 'trx_expcol_expanded_bg_color', array(
				'label' => __( 'Background Color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
				'selectors' => array(
					'{{WRAPPER}}.trx_expcol_state_expanded .trx_expcol_button' => 'background-color: {{VALUE}};'
				),
//					'global' => array(
//						'active' => false,
//					),
			) );

			$element->add_control( 'trx_expcol_expanded_bd_color', array(
				'label' => __( 'Border Color', 'trx_addons' ),
				'label_block' => false,
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '',
				'condition' => array(
					'trx_expcol_allow' => 'on',
				),
				'selectors' => array(
					'{{WRAPPER}}.trx_expcol_state_expanded .trx_expcol_button' => 'border-color: {{VALUE}};'
				),
//					'global' => array(
//						'active' => false,
//					),
			) );

			$params = trx_addons_get_icon_param();
			$params = trx_addons_array_get_first_value( $params );
			unset( $params['name'] );
			$params['condition'] = array(
				'trx_expcol_allow' => 'on'
			);
			$element->add_control( 'trx_expcol_expanded_icon', $params );

			$element->end_controls_section();
		}
	}
}

// Add "data-expand-collapse" to the wrapper of the row
if ( !function_exists( 'trx_addons_elm_add_expand_collapse_data' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_elm_add_expand_collapse_data', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render', 'trx_addons_elm_add_expand_collapse_data', 10, 1 );
	add_action( 'elementor/frontend/column/before_render', 'trx_addons_elm_add_expand_collapse_data', 10, 1 );
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_elm_add_expand_collapse_data', 10, 1 );
	function trx_addons_elm_add_expand_collapse_data( $element ) {
		if ( is_object( $element ) ) {
			//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
			$trx_expcol_allow = $element->get_settings( 'trx_expcol_allow' );
			if ( ! empty( $trx_expcol_allow ) && ! trx_addons_is_off( $trx_expcol_allow ) ) {
				// Load scripts and styles
				trx_addons_expand_collapse_load_scripts_front( true );
				// Generate a button layout
				$settings = $element->get_settings();
				ob_start();
				trx_addons_get_template_part( array(
												//TRX_ADDONS_PLUGIN_SHORTCODES . 'button/tpl.'.trx_addons_esc($atts['type']).'.php',
												TRX_ADDONS_PLUGIN_ADDONS . 'expand-collapse/tpl.expand-collapse.default.php'
												),
												'trx_addons_args_trx_expcol_button', 
												$settings
											);
				$output = ob_get_contents();
				ob_end_clean();
				// Add data-parameters to the element wrapper
				$element->add_render_attribute( '_wrapper', array(
					'data-trx-expcol-collapsed-height' => $settings['trx_expcol_collapsed_height']['size'] . $settings['trx_expcol_collapsed_height']['unit'],
					'data-trx-expcol-button' => apply_filters( 'trx_addons_filter_expcol_button_layout', $output ),
				) );
			}
		}
	}
}
