<?php
/**
 * Elementor extension: Add two groups of parameters: "Background Layers" and "Animations" (aka Parallax and Entrance)
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


//========================================================================
//  Background layers for Sections and Columns
//========================================================================


if ( ! function_exists( 'trx_addons_elm_parallax_preview_load_scripts' ) ) {
	add_action( "elementor/frontend/after_enqueue_scripts", 'trx_addons_elm_parallax_preview_load_scripts' );
	/**
	 * Load Elementor scripts and styles for the Elementor's preview mode
	 * 
	 * @hooked elementor/frontend/after_enqueue_scripts
	 * 
	 * @trigger trx_addons_action_pagebuilder_preview_scripts
	 */
	function trx_addons_elm_parallax_preview_load_scripts() {
		if ( trx_addons_is_on( trx_addons_get_option( 'debug_mode' ) ) ) {
			wp_enqueue_script( 'trx_addons-elementor-parallax', trx_addons_get_file_url( TRX_ADDONS_PLUGIN_API . 'elementor/elementor-parallax.js' ), array('jquery'), null, true );
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_parallax_merge_scripts' ) ) {
	add_action( "trx_addons_filter_merge_scripts", 'trx_addons_elm_parallax_merge_scripts' );
	/**
	 * Merge Elementor scripts to the single file to increase page upload speed
	 * 
	 * @hooked trx_addons_filter_merge_scripts
	 * 
	 * @param array $list  List of scripts to merge
	 * 
	 * @return array    List of scripts to merge
	 */
	function trx_addons_elm_parallax_merge_scripts( $list ) {
		if ( trx_addons_exists_elementor() ) {
			$list[ TRX_ADDONS_PLUGIN_API . 'elementor/elementor-parallax.js' ] = true;
		}
		return $list;
	}
}


if ( ! function_exists( 'trx_addons_elm_add_parallax_blocks' ) ) {
	add_action( 'elementor/element/before_section_start', 'trx_addons_elm_add_parallax_blocks', 10, 3 );
	/**
	 * Add a group of parameters "Background Layers" to the Elementor's sections and columns to the tab 'Style'
	 * 
	 * @hooked elementor/element/before_section_start
	 * 
	 * @param object $element  Element object
	 * @param string $section_id  Section ID
	 * @param array $args  Section arguments
	 */
	function trx_addons_elm_add_parallax_blocks( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}

		if ( in_array( $element->get_name(), array( 'section', 'column' ) ) && $section_id == 'section_border' ) {	//_section_responsive

			// Don't add a field of type 'REPEATER' to tabs other than TAB_CONTENT in any Elementor widget, section or column.
			// Because an error appears on action 'Paste style' executed.
			// Also an error message appears when saving the document after copying the styles of one widget to another.

			// This way with no errors, but a new tab 'Content' appears in sections and columns with single controls section.
			//'tab' => \Elementor\Controls_Manager::TAB_CONTENT,

			// This way may generate js-errors when a user copying styles from one section or column to another. But no new tab appears.
			//'tab' => ! empty( $args['tab'] ) ? $args['tab'] : \Elementor\Controls_Manager::TAB_ADVANCED,

			// Compromise way: add a new controls section 'Audio effects' to the tab 'Content' for widgets
			// and leave this controls section on the tab 'Advanced' for sections and columns

			// Partially fixed in the Elementor v.3.9.0, but if a field REPEATER have a parameter 'condition' - 
			// error still appear on save document after Paste Styles !
			
			$tab = ! empty( $args['tab'] ) ? $args['tab'] : \Elementor\Controls_Manager::TAB_STYLE;

			$element->start_controls_section( 'section_trx_parallax', array(
																		'tab' => $tab,
																		'label' => __( 'Background Layers', 'trx_addons' )
			) );

			$element->add_control( 'parallax_blocks', array(
				'label' => __( 'Layers', 'trx_addons' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => apply_filters('trx_addons_sc_param_group_params',
					array(
						array(
							'name' => 'type',
							'tab' => $tab,
							'label' => __( 'Layer handle', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => array(
								'none'   => __('None', 'trx_addons'),
								'mouse'  => __('Mouse events', 'trx_addons'),
								'scroll' => __('Scroll events', 'trx_addons'),
								'motion' => __('Permanent motion', 'trx_addons'),
							),
							'default' => 'none',
						),
						array(
							'name' => 'mouse_handler',
							'type' => \Elementor\Controls_Manager::SELECT,
							'label' => __( 'Mouse handler', 'trx_addons' ),
							'label_block' => false,
							'options' => array(
								'row'     => esc_html__( 'Current row', 'trx_addons' ),
								'content' => esc_html__( 'Content area', 'trx_addons' ),
								'window'  => esc_html__( 'Whole window', 'trx_addons' ),
							),
							'default' => 'row',
							'condition' => array(
								'type' => 'mouse',
							),
						),
						array(
							'name' => 'animation_prop',
							'tab' => $tab,
							'label' => __( 'Animation', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => array(
								'background'  => __('Background', 'trx_addons'),
								'transform'   => __('Transform', 'trx_addons'),
								'transform3d' => __('Transform3D (for mouse events only)', 'trx_addons'),
								'tilt'        => __('Tilt (for mouse events only)', 'trx_addons'),
							),
							'condition' => array(
								'type!' => 'none',
							),
							'default' => 'background',
						),
						array(
							'name' => 'image',
							'tab' => $tab,
							'label' => __( 'Background image', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::MEDIA,
							'default' => array(
								'url' => '',
							),
						),
						array(
							'name' => 'bg_size',
							'tab' => $tab,
							'label' => __( 'Background size', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => array(
								'auto'    => __('Auto', 'trx_addons'),
								'cover'   => __('Cover', 'trx_addons'),
								'contain' => __('Contain', 'trx_addons'),
							),
							'default' => 'cover',
						),
						array(
							'name' => 'left',
							'tab' => $tab,
							'label' => __( 'Left position (in %)', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => array(
								'size' => 0,
								'unit' => 'px'
							),
							'range' => array(
								'px' => array(
									'min' => 0,
									'max' => 100
								),
							),
							'size_units' => array( 'px' )
						),
						array(
							'name' => 'top',
							'tab' => $tab,
							'label' => __( 'Top position (in %)', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => array(
								'size' => 0,
								'unit' => 'px'
							),
							'range' => array(
								'px' => array(
									'min' => 0,
									'max' => 100
								),
							),
							'size_units' => array( 'px' ),
						),
						array(
							'name' => 'speed',
							'tab' => $tab,
							'label' => __( 'Shift speed', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => array(
								'size' => 50,
								'unit' => 'px'
							),
							'range' => array(
								'px' => array(
									'min' => -500,
									'max' => 500,
									'step' => 10
								),
							),
							'size_units' => array( 'px' ),
						),
						array(
							'name' => 'z_index',
							'tab' => $tab,
							'label' => __( 'Z-index', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => array(
								'size' => '',
								'unit' => 'px'
							),
							'range' => array(
								'px' => array(
									'min' => -1,
									'max' => 100
								),
							),
							'size_units' => array( 'px' ),
						),
						array(
							'name' => 'class',
							'tab' => $tab,
							'label' => __( 'CSS class', 'trx_addons' ),
							'description' => __( 'Class name to assign additional rules to this layer. For example: "hide_on_notebook", "hide_on_tablet", "hide_on_mobile" to hide block on the relative device', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::TEXT,
							'default' => '',
						),

						// Motion parameters
						array(
							'name' => 'motion_dir',
							'tab' => $tab,
							'type' => \Elementor\Controls_Manager::SELECT,
							'label' => __( 'Motion direction', 'trx_addons' ),
							'label_block' => false,
							'options' => array(
								'vertical' => __( 'Vertical', 'trx_addons'),
								'horizontal' => __( 'Horizontal', 'trx_addons'),
								'round' => __( 'Round', 'trx_addons'),
								'random' => __( 'Random', 'trx_addons'),
							),
							'default' => 'round',
							'condition' => array(
								'type' => 'motion'
							),
						),
						array(
							'name' => 'motion_time',
							'tab' => $tab,
							'label' => __( 'Motion time', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => array(
								'size' => 5,
								'unit' => 'px'
							),
							'size_units' => array( 'px' ),
							'range' => array(
								'px' => array(
									'min' => 0.1,
									'max' => 20,
									'step' => 0.1
								)
							),
							'condition' => array(
								'type' => 'motion'
							),
						),

						// Mouse parameters
						array(
							'name' => 'mouse_tilt_amount',
							'label' => __( 'Amount', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => array(
								'size' => 70,
								'unit' => 'px'
							),
							'range' => array(
								'px' => array(
									'min' => 10,
									'max' => 500
								),
							),
							'size_units' => array( 'px' ),
							'condition' => array(
								'type' => 'mouse',
								'animation_prop' => 'tilt',
							),
						),
					),
					'trx_sc_parallax_row'
				),
				'title_field' => '{{{ left.size }}}x{{{ top.size }}} / {{{ type }}} / {{{ animation_prop }}}',
			) );

			$element->end_controls_section();
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_parallax_blocks_data' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_elm_add_parallax_blocks_data', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render', 'trx_addons_elm_add_parallax_blocks_data', 10, 1 );
	add_action( 'elementor/frontend/column/before_render', 'trx_addons_elm_add_parallax_blocks_data', 10, 1 );
	/**
	 * Add an attribute "data-parallax-blocks" and classes 'sc_parallax' and 'sc_parallax_blocks'
	 * to wrappers of sections and columns if they have background layers
	 * 
	 * @hooked elementor/frontend/section/before_render  (after Elementor 2.1.0)
	 * @hooked elementor/frontend/column/before_render   (after Elementor 2.1.0)
	 * @hooked elementor/frontend/element/before_render  (before Elementor 2.1.0)
	 *
	 * @param object $element  Elementor element object
	 */
	function trx_addons_elm_add_parallax_blocks_data( $element ) {
		if ( is_object( $element ) && in_array( $element->get_name(), array( 'section', 'column' ) ) ) {
			//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
			$parallax_blocks = $element->get_settings( 'parallax_blocks' );
			if ( ! empty( $parallax_blocks ) 
				&& is_array( $parallax_blocks ) 
				&& count( $parallax_blocks ) > 0 
				&& ( $parallax_blocks[0]['type'] != 'none' || ! empty( $parallax_blocks[0]['image']['url'] ) )
			) {
				$element->add_render_attribute( '_wrapper', 'class', 'sc_parallax' );
				$element->add_render_attribute( '_wrapper', 'class', 'sc_parallax_blocks' );
				$element->add_render_attribute( '_wrapper', 'data-parallax-blocks', json_encode( $parallax_blocks ) );
			}
		}
	}
}



//========================================================================
//  Scrolling animation (was Parallax blocks) for all elements
//========================================================================

if ( ! function_exists('trx_addons_elm_add_parallax_params_to_widgets' ) ) {
	add_action( 'elementor/element/before_section_start', 'trx_addons_elm_add_parallax_params_to_widgets', 10, 3 );
	add_action( 'elementor/widget/before_section_start', 'trx_addons_elm_add_parallax_params_to_widgets', 10, 3 );
	/**
	 * Add 'Animation' (aka Parallax and Entrance) section to the Elementor's sections, columns and widgets
	 *
	 * @param object $element  Elementor element object
	 * @param string $section_id  Section ID
	 * @param array $args  Section arguments
	 */
	function trx_addons_elm_add_parallax_params_to_widgets($element, $section_id, $args) {

		if ( ! is_object( $element ) ) {
			return;
		}

		if ( in_array( $element->get_name(), array( 'section', 'column', 'common' ) ) && $section_id == '_section_responsive' ) {

			// Detect edit mode
			$is_edit_mode = trx_addons_elm_is_edit_mode();

			// Register controls
			$element->start_controls_section( 'section_trx_entrance', array(
																		'tab' => ! empty( $args['tab'] ) ? $args['tab'] : \Elementor\Controls_Manager::TAB_ADVANCED,
																		'label' => __( 'Animation', 'trx_addons' )
																	) );

			// Scrolling Animation
			//----------------------------------------------
			$element->add_control( 'parallax', array(
													'type' => \Elementor\Controls_Manager::SWITCHER,
													'render_type' => 'template',
													'label' => __( 'Scrolling Animation', 'trx_addons' ),
													'label_on' => __( 'On', 'trx_addons' ),
													'label_off' => __( 'Off', 'trx_addons' ),
													'return_value' => 'parallax',
													'prefix_class' => 'sc_',
									) );

			$element->add_control( 'parallax_flow', array(
													'type' => \Elementor\Controls_Manager::SELECT,
													'render_type' => 'template',
													'label' => __( 'Animation Flow', 'trx_addons' ),
													'label_block' => false,
													'description' => __( '1. <b>Default</b>: "Animate From" and "Animate To" values correspond to animation range points (in %).', 'trx_addons' )
																	. '<br>'
																	. __( '2. <b>In Out</b>: "Animate From" is the state of the object before it arrives at the "Start Point". "Animate To" is the state the object enters after crossing the "End Point".', 'trx_addons' )
																	. '<br>'
																	. __( '3. <b>Sticky</b>: Transition takes place in a fixed state.', 'trx_addons' )
																	. '<br>'
																	. __( 'For this effect, you need to adjust the following settings of the parent section:', 'trx_addons' )
																	. '<br>'
																	. __( '- Set "Height" to "Min. height" and specify the minimum height of the section (if it is not stretched by an image or text inserted into the adjacent column)', 'trx_addons' )
																	. '<br>'
																	. __( '- Set "Column Position" to "Stretch"', 'trx_addons' )
																	. '<br>'
																	. __( '4. <b>Entrance</b>: One-time transition triggered when element enters specified animation range.', 'trx_addons' ),
													'prefix_class' => 'sc_parallax_',
													'default' => 'default',
													'options' => array(
														'default' => __( 'Default', 'trx_addons' ),
														'in_out' => __( 'In Out', 'trx_addons' ),
														'sticky' => __( 'Sticky', 'trx_addons' ),
														'entrance' => __( 'Entrance', 'trx_addons' ),
													),
													'condition' => array(
														'parallax' => 'parallax'
													),
									) );


			$element->add_control( 'parallax_crop', array(
													'type' => \Elementor\Controls_Manager::SELECT,
													'render_type' => 'template',
													'label' => __( 'Crop object', 'trx_addons' ),
													'label_block' => false,
													'description' => __( 'Crop the visible area of ​​an object on scroll.', 'trx_addons' ),
													'default' => 'none',
													'options' => apply_filters( 'trx_addons_filter_parallax_crop_type', array(
														'none'                => __( 'None', 'trx_addons' ),
														'wipe_left_right'     => __( 'Wipe from Left to Right', 'trx_addons' ),
														'wipe_right_left'     => __( 'Wipe from Right to Left', 'trx_addons' ),
														'wipe_top_bottom'     => __( 'Wipe from Top to Bottom', 'trx_addons' ),
														'wipe_bottom_top'     => __( 'Wipe from Bottom to Top', 'trx_addons' ),
														'wipe_out_vertical'   => __( 'Wipe out Vertical', 'trx_addons' ),
														'wipe_out_horizontal' => __( 'Wipe out Horizontal', 'trx_addons' ),
														'corner_top_left'     => __( 'Corner from Top Left', 'trx_addons' ),
														'corner_top_right'    => __( 'Corner from Top Right', 'trx_addons' ),
														'corner_bottom_left'  => __( 'Corner from Bottom Left', 'trx_addons' ),
														'corner_bottom_right' => __( 'Corner from Bottom Right', 'trx_addons' ),
														'box_left'            => __( 'Box from Left', 'trx_addons' ),
														'box_right'           => __( 'Box from Right', 'trx_addons' ),
														'box_top'             => __( 'Box from Top', 'trx_addons' ),
														'box_bottom'          => __( 'Box from Bottom', 'trx_addons' ),
														'box_center'          => __( 'Box from Center', 'trx_addons' ),
														'circle'              => __( 'Circle', 'trx_addons' ),
														'ellipse_ver'         => __( 'Ellipse Vertical', 'trx_addons' ),
														'ellipse_hor'         => __( 'Ellipse Horizontal', 'trx_addons' ),
													) ),
													'condition' => array(
														'parallax' => 'parallax'
													),
									) );

			// Animation Range
			$element->add_control( 'parallax_range_heading', array(
													'label' => __( 'Animation Range', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::HEADING,
													'separator' => 'before',
													'condition' => array(
														'parallax' => 'parallax'
													),
									) );

			$element->add_control( 'parallax_range_start', array(
													'label' => __( 'Start point', 'trx_addons' ),
													'description' => __( 'The offset (as a percentage of the window height relative the window bottom) where the effect starts.', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => array(
														'size' => 0,
														'unit' => 'px'
													),
													'size_units' => array( 'px' ),
													'range' => array(
														'px' => array(
															'min' => -50,
															'max' => 150,
															'step' => 1
														)
													),
													'condition' => array(
														'parallax' => 'parallax'
													),
									) );

			$element->add_control( 'parallax_range_end', array(
													'label' => __( 'End point', 'trx_addons' ),
													'description' => __( 'End point of the effect. Must be greater than "Start point".', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => array(
														'size' => 40,
														'unit' => 'px'
													),
													'size_units' => array( 'px' ),
													'range' => array(
														'px' => array(
															'min' => -50,
															'max' => 150,
															'step' => 1
														)
													),
													'condition' => array(
														'parallax' => 'parallax',
														'parallax_flow!' => array( 'entrance', 'sticky' )
													),
									) );

			$element->add_control( 'parallax_sticky_offset', array(
										'label' => __( 'Sticky offset', 'trx_addons' ),
										'description' => __( 'Offset (in %) from the bottom of the parent container where the effect should end.', 'trx_addons' ),
										'type' => \Elementor\Controls_Manager::SLIDER,
										'default' => array(
											'size' => 0,
											'unit' => 'px'
										),
										'size_units' => array( 'px' ),
										'range' => array(
											'px' => array(
												'min' => 1,
												'max' => 100,
												'step' => 1
											)
										),
										'condition' => array(
											'parallax' => 'parallax',
											'parallax_flow' => 'sticky'
										),
						) );

			// Animate From & To
			$element->start_controls_tabs( 'parallax_params' );

			foreach ( array( 'start', 'end' ) as $tab ) {

				$element->start_controls_tab( "parallax_params_{$tab}", array(
														'label' => 'start' === $tab
																		? esc_html__( 'Animate From', 'trx_addons' )
																		: esc_html__( 'Animate To', 'trx_addons' ),
														'condition' => array(
															'parallax' => 'parallax'
														),
				) );


				$element->add_control( "parallax_x_{$tab}", array(
														'label' => __( 'The shift along the X-axis', 'trx_addons' ),
														'type' => \Elementor\Controls_Manager::SLIDER,
														'default' => array(
															'size' => 0,
															'unit' => 'px'
														),
														'size_units' => array( 'px', 'vw', '%' ),
														'range' => array(
															'px' => array(
																'min' => -1000,
																'max' => 1000
															),
															'vw' => array(
																'min' => -200,
																'max' => 200,
																'step' => 1
															),
															'%' => array(
																'min' => -500,
																'max' => 500
															)
														),
														'condition' => array(
															'parallax' => 'parallax'
														),
										) );

				$element->add_control( "parallax_y_{$tab}", array(
														'label' => __( 'The shift along the Y-axis', 'trx_addons' ),
														'type' => \Elementor\Controls_Manager::SLIDER,
														'default' => array(
															'size' => 0,
															'unit' => 'px'
														),
														'size_units' => array( 'px', 'vh', '%' ),
														'range' => array(
															'px' => array(
																'min' => -1000,
																'max' => 1000
															),
															'vh' => array(
																'min' => -200,
																'max' => 200,
																'step' => 1
															),
															'%' => array(
																'min' => -500,
																'max' => 500
															)
														),
														'condition' => array(
															'parallax' => 'parallax'
														),
										) );

				$element->add_control( "parallax_opacity_{$tab}", array(
														'label' => __( 'Opacity', 'trx_addons' ),
														'type' => \Elementor\Controls_Manager::SLIDER,
														'default' => array(
															'size' => 1,
															'unit' => 'px'
														),
														'size_units' => array( 'px' ),
														'range' => array(
															'px' => array(
																'min' => 0,
																'max' => 1,
																'step' => 0.05
															)
														),
														'condition' => array(
															'parallax' => 'parallax'
														),
										) );

				$element->add_control( "parallax_scale_{$tab}", array(
														'label' => __( 'Scale (in %)', 'trx_addons' ),
														'type' => \Elementor\Controls_Manager::SLIDER,
														'default' => array(
															'size' => 100,
															'unit' => 'px'
														),
														'size_units' => array( 'px' ),
														'range' => array(
															'px' => array(
																'min' => 0,
																'max' => 1000,
															)
														),
														'condition' => array(
															'parallax' => 'parallax'
														),
										) );

				$element->add_control( "parallax_rotate_{$tab}", array(
														'label' => __( 'Rotation (in deg)', 'trx_addons' ),
														'type' => \Elementor\Controls_Manager::SLIDER,
														'default' => array(
															'size' => 0,
															'unit' => 'px'
														),
														'size_units' => array( 'px' ),
														'range' => array(
															'px' => array(
																'min' => -360,
																'max' => 360,
																'step' => 1
															)
														),
														'condition' => array(
															'parallax' => 'parallax'
														),
										) );

				$element->add_control( "parallax_crop_{$tab}", array(
													'label' => __( 'Visible area', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => array(
														'size' => $tab == 'start' ? 0 : 100,
														'unit' => 'px'
													),
													'size_units' => array( 'px' ),
													'range' => array(
														'px' => array(
															'min' => 0,
															'max' => 100,
															'step' => 1
														)
													),
													'condition' => array(
														'parallax' => 'parallax',
														'parallax_crop!' => 'none',
													),
									) );

				$element->end_controls_tab();
			}

			$element->end_controls_tabs();

			// Anchor Point
			$element->add_control( 'parallax_x_anchor', array(
													'label' => esc_html__( 'X Anchor Point', 'trx_addons' ),
													'label_block' => false,
													'type' => \Elementor\Controls_Manager::CHOOSE,
													'options' => array(
														'left' => array(
															'title' => esc_html__( 'Left', 'trx_addons' ),
															'icon' => 'eicon-h-align-left',
														),
														'center' => array(
															'title' => esc_html__( 'Center', 'trx_addons' ),
															'icon' => 'eicon-h-align-center',
														),
														'right' => array(
															'title' => esc_html__( 'Right', 'trx_addons' ),
															'icon' => 'eicon-h-align-right',
														),
													),
													'default' => 'center',
													'condition' => array(
														'parallax' => 'parallax'
													),
													'separator' => 'before',
													'selectors' => array(
														'{{WRAPPER}}' => '--trx-addons-parallax-x-anchor: {{VALUE}}',
													),
			) );

			$element->add_control( 'parallax_y_anchor', array(
													'label' => esc_html__( 'Y Anchor Point', 'trx_addons' ),
													'label_block' => false,
													'type' => \Elementor\Controls_Manager::CHOOSE,
													'options' => array(
														'top' => array(
															'title' => esc_html__( 'Top', 'trx_addons' ),
															'icon' => 'eicon-v-align-top',
														),
														'center' => array(
															'title' => esc_html__( 'Center', 'trx_addons' ),
															'icon' => 'eicon-v-align-middle',
														),
														'bottom' => array(
															'title' => esc_html__( 'Bottom', 'trx_addons' ),
															'icon' => 'eicon-v-align-bottom',
														),
													),
													'default' => 'center',
													'condition' => array(
														'parallax' => 'parallax'
													),
													'selectors' => array(
														'{{WRAPPER}}' => '--trx-addons-parallax-y-anchor: {{VALUE}}',
													),
			) );

			// Easing and Duration
			$element->add_control( 'parallax_timing_heading', array(
													'label' => __( 'Animation Timing', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::HEADING,
													'separator' => 'before',
													'condition' => array(
														'parallax' => 'parallax'
													),
									) );

			$element->add_control( 'parallax_duration', array(
													'label' => __( 'Duration (in sec)', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => array(
														'size' => 1,
														'unit' => 'px'
													),
													//'separator' => 'before',
													'size_units' => array( 'px' ),
													'range' => array(
														'px' => array(
															'min' => 0.1,
															'max' => 10,
															'step' => 0.1
														)
													),
													'condition' => array(
														'parallax' => 'parallax'
													),
									) );

			$element->add_control( 'parallax_delay', array(
													'label' => __( 'Delay (in sec)', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => array(
														'size' => 0,
														'unit' => 'px'
													),
													//'separator' => 'before',
													'size_units' => array( 'px' ),
													'range' => array(
														'px' => array(
															'min' => 0,
															'max' => 10,
															'step' => 0.1
														)
													),
													'condition' => array(
														'parallax' => 'parallax',
														'parallax_flow' => 'entrance'
													),
									) );

			$element->add_control( 'parallax_squeeze', array(
													'label' => __( 'Squeeze interval', 'trx_addons' ),
													'description' => __( 'Ratio to shrink/stretch the interval between several items (blog posts, services, team members, words or chars in headings, etc.). Default 1.', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => array(
														'size' => 1,
														'unit' => 'px'
													),
													'size_units' => array( 'px' ),
													'range' => array(
														'px' => array(
															'min' => 0,
															'max' => 3,
															'step' => 0.05
														)
													),
													'condition' => array(
														'parallax' => 'parallax'
													),
									) );

			$element->add_control( 'parallax_ease', array(
													'type' => \Elementor\Controls_Manager::SELECT,
													'label' => __( 'Ease', 'trx_addons' ),
													'label_block' => false,
													'options' => ! $is_edit_mode ? array() : trx_addons_get_list_ease(),
													'default' => 'power2',
													'condition' => array(
														'parallax' => 'parallax',
														'parallax_flow' => 'entrance'
													),
									) );

			$element->add_control( 'parallax_lag', array(
													'label' => __( 'Scroll lag', 'trx_addons' ),
													'description' => __( 'Sets the lag of an element from the current scroll position.', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => array(
														'size' => 0,
														'unit' => 'px'
													),
													'size_units' => array( 'px' ),
													'range' => array(
														'px' => array(
															'min' => 0,
															'max' => 200,
															'step' => 1
														)
													),
													'condition' => array(
														'parallax' => 'parallax'
													),
									) );

			// Text Animation
			$element->add_control( 'parallax_text', array(
													'type' => \Elementor\Controls_Manager::SELECT,
													'label' => __( 'Text Animation', 'trx_addons' ),
													'label_block' => false,
													'description' => __( 'Applies only to text objects of type Heading and Title.', 'trx_addons' ),
													'separator' => 'before',
													'options' => array(
														'block' => __( 'Whole block', 'trx_addons'),
														'words' => __( 'Word by word', 'trx_addons'),
														'chars' => __( 'Char by char', 'trx_addons'),
													),
													'default' => 'block',
													'condition' => array(
														'parallax' => 'parallax'
													),
									) );
			$element->add_control( 'parallax_text_separate', array(
													'type' => \Elementor\Controls_Manager::SWITCHER,
													'label' => __( 'Separate Animation', 'trx_addons' ),
													'label_on' => __( 'On', 'trx_addons' ),
													'label_off' => __( 'Off', 'trx_addons' ),
													'description' => __( 'Animate each word (char) as a separate object.', 'trx_addons' ),
													'return_value' => 'on',
													'render_type' => 'template',
													'prefix_class' => 'sc_parallax_text_separate_',
													'condition' => array(
														'parallax' => 'parallax',
														'parallax_flow' => 'default',
														'parallax_text' => array('words', 'chars'),
													),
									) );
			$element->add_control( 'parallax_text_nowrap', array(
													'type' => \Elementor\Controls_Manager::SWITCHER,
													'label' => __( 'No wrap', 'trx_addons' ),
													'label_on' => __( 'On', 'trx_addons' ),
													'label_off' => __( 'Off', 'trx_addons' ),
													'return_value' => 'on',
													'render_type' => 'template',
													'prefix_class' => 'sc_parallax_text_nowrap_',
													'condition' => array(
														'parallax' => 'parallax',
														'parallax_text' => array('words', 'chars'),
													),
									) );

			// Mouse Animation
			$element->add_control( 'parallax_mouse', array(
													'type' => \Elementor\Controls_Manager::SWITCHER,
													'label' => __( 'Mouse Animation', 'trx_addons' ),
													'label_on' => __( 'On', 'trx_addons' ),
													'label_off' => __( 'Off', 'trx_addons' ),
													'separator' => 'before',
													'return_value' => 'mouse',
													'render_type' => 'template',
													'prefix_class' => 'sc_parallax_',
//													'condition' => array(
//														'parallax' => 'parallax'
//													),
									) );
			$element->add_control( 'parallax_mouse_type', array(
													'type' => \Elementor\Controls_Manager::SELECT,
													'label' => __( 'Transform type', 'trx_addons' ),
													'label_block' => false,
													'options' => array(
														'transform'   => esc_html__( 'Transform', 'trx_addons' ),
														'transform3d' => esc_html__( 'Transform 3D', 'trx_addons' ),
														'tilt'        => esc_html__( 'Tilt', 'trx_addons' ),
													),
													'default' => 'transform3d',
													'render_type' => 'template',
													'prefix_class' => 'sc_parallax_type_',
													'condition' => array(
//														'parallax' => 'parallax',
														'parallax_mouse' => 'mouse',
													),
									) );
			$element->add_control( 'parallax_mouse_tilt_amount', array(
													'label' => __( 'Amount', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => array(
														'size' => 70,
														'unit' => 'px'
													),
													'range' => array(
														'px' => array(
															'min' => 10,
															'max' => 500
														),
													),
													'size_units' => array( 'px' ),
													'condition' => array(
//														'parallax' => 'parallax',
														'parallax_mouse' => 'mouse',
														'parallax_mouse_type' => 'tilt',
													),
									) );
			$element->add_control( 'parallax_mouse_speed', array(
													'label' => __( 'Momentum', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => array(
														'size' => 10,
														'unit' => 'px'
													),
													'range' => array(
														'px' => array(
															'min' => -100,
															'max' => 100
														),
													),
													'size_units' => array( 'px' ),
													'condition' => array(
//														'parallax' => 'parallax',
														'parallax_mouse' => 'mouse',
														'parallax_mouse_type!' => 'tilt',
													),
									) );
			$element->add_control( 'parallax_mouse_z', array(
													'label' => __( 'Z-index', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => array(
														'size' => '',
														'unit' => 'px'
													),
													'range' => array(
														'px' => array(
															'min' => -1,
															'max' => 100
														),
													),
													'size_units' => array( 'px' ),
													'condition' => array(
//														'parallax' => 'parallax',
														'parallax_mouse' => 'mouse',
														'parallax_mouse_type' => array('tilt', 'transform3d'),
													),
									) );
			$element->add_control( 'parallax_mouse_handler', array(
													'type' => \Elementor\Controls_Manager::SELECT,
													'label' => __( 'Mouse handler', 'trx_addons' ),
													'label_block' => false,
													'options' => array(
														'self'    => esc_html__( 'Self', 'trx_addons' ),
														'parent'  => esc_html__( 'Parent', 'trx_addons' ),
														'column'  => esc_html__( 'Current column', 'trx_addons' ),
														'row'     => esc_html__( 'Current row', 'trx_addons' ),
														'content' => esc_html__( 'Content area', 'trx_addons' ),
														'window'  => esc_html__( 'Whole window', 'trx_addons' ),
													),
													'default' => 'row',
													'condition' => array(
//														'parallax' => 'parallax',
														'parallax_mouse' => 'mouse',
													),
									) );

			$element->end_controls_section();
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_parallax_data_to_widgets' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_elm_add_parallax_data_to_widgets', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render',  'trx_addons_elm_add_parallax_data_to_widgets', 10, 1 );
	add_action( 'elementor/frontend/column/before_render',  'trx_addons_elm_add_parallax_data_to_widgets', 10, 1 );
	add_action( 'elementor/frontend/widget/before_render',  'trx_addons_elm_add_parallax_data_to_widgets', 10, 1 );
	/**
	 * Add "data-parallax-params" to the wrapper of the section, column or widget
	 * if scroll animation (parallax) or mouse animation is enabled
	 * 
	 * @hooked elementor/frontend/section/before_render (after Elementor 2.1.0)
	 * @hooked elementor/frontend/column/before_render  (after Elementor 2.1.0)
	 * @hooked elementor/frontend/widget/before_render  (after Elementor 2.1.0)
	 * @hooked elementor/frontend/element/before_render (before Elementor 2.1.0)
	 *
	 * @param object $element  Elementor section, column or widget object
	 */
	function trx_addons_elm_add_parallax_data_to_widgets( $element ) {
		//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
		$parallax = $element->get_settings( 'parallax' );
		$parallax_mouse = $element->get_settings( 'parallax_mouse' );
		if ( ! empty( $parallax ) || ! empty( $parallax_mouse ) ) {
			$settings = $element->get_settings();
			// Set equal units for start and end values if one of them is zero
			$x_start      = ! empty( $parallax ) && ! empty( $settings['parallax_x_start'] ) ? $settings['parallax_x_start']['size'] : 0;
			$x_start_unit = ! empty( $parallax ) && ! empty( $settings['parallax_x_start'] ) ? $settings['parallax_x_start']['unit'] : 'px';
			$x_end        = ! empty( $parallax ) && ! empty( $settings['parallax_x_end'] ) ? $settings['parallax_x_end']['size'] : 0;
			$x_end_unit   = ! empty( $parallax ) && ! empty( $settings['parallax_x_end'] ) ? $settings['parallax_x_end']['unit'] : 'px';
			$y_start      = ! empty( $parallax ) && ! empty( $settings['parallax_y_start'] ) ? $settings['parallax_y_start']['size'] : 0;
			$y_start_unit = ! empty( $parallax ) && ! empty( $settings['parallax_y_start'] ) ? $settings['parallax_y_start']['unit'] : 'px';
			$y_end        = ! empty( $parallax ) && ! empty( $settings['parallax_y_end'] ) ? $settings['parallax_y_end']['size'] : 0;
			$y_end_unit   = ! empty( $parallax ) && ! empty( $settings['parallax_y_end'] ) ? $settings['parallax_y_end']['unit'] : 'px';
			if ( $x_end == 0 ) $x_end_unit = $x_start_unit;
			else if ( $x_start == 0 ) $x_start_unit = $x_end_unit;
			if ( $y_end == 0 ) $y_end_unit = $y_start_unit;
			else if ( $y_start == 0 ) $y_start_unit = $y_end_unit;
			// Add data
			$element->add_render_attribute( '_wrapper', 'data-parallax-params', json_encode( array(
				// Parallax settings
				'parallax'      => ! empty( $parallax ) ? 1 : 0,
				'flow'          => ! empty( $parallax ) && ! empty( $settings['parallax_flow'] ) ? $settings['parallax_flow'] : 'default',
				'crop'          => ! empty( $parallax ) && ! empty( $settings['parallax_crop'] ) ? $settings['parallax_crop'] : 'none',
				'range_start'   => ! empty( $parallax ) && ! empty( $settings['parallax_range_start'] ) ? $settings['parallax_range_start']['size'] : 0,
				'range_end'     => ! empty( $parallax ) && ! empty( $settings['parallax_range_end'] ) ? $settings['parallax_range_end']['size'] : 40,
				'sticky_offset' => ! empty( $parallax ) && ! empty( $settings['parallax_sticky_offset'] ) ? $settings['parallax_sticky_offset']['size'] : 0,
				'ease'          => ! empty( $parallax ) && ! empty( $settings['parallax_ease'] ) ? $settings['parallax_ease'] : 'power2',
				'duration'      => ! empty( $parallax ) && ! empty( $settings['parallax_duration'] ) ? $settings['parallax_duration']['size'] : 1,
				'delay'         => ! empty( $parallax ) && ! empty( $settings['parallax_delay'] ) ? $settings['parallax_delay']['size'] : 0,
				'squeeze'       => ! empty( $parallax ) && ! empty( $settings['parallax_squeeze'] ) ? $settings['parallax_squeeze']['size'] : 1,
				'lag'           => ! empty( $parallax ) && ! empty( $settings['parallax_lag'] ) ? $settings['parallax_lag']['size'] : 0,
				// From & To settings
				'x_start'       => $x_start,
				'x_start_unit'  => $x_start_unit,
				'x_end'         => $x_end,
				'x_end_unit'    => $x_end_unit,
				'y_start'       => $y_start,
				'y_start_unit'  => $y_start_unit,
				'y_end'         => $y_end,
				'y_end_unit'    => $y_end_unit,
				'scale_start'   => ! empty( $parallax ) && ! empty( $settings['parallax_scale_start'] ) ? $settings['parallax_scale_start']['size'] : 100,
				'scale_end'     => ! empty( $parallax ) && ! empty( $settings['parallax_scale_end'] ) ? $settings['parallax_scale_end']['size'] : 100,
				'rotate_start'  => ! empty( $parallax ) && ! empty( $settings['parallax_rotate_start'] ) ? $settings['parallax_rotate_start']['size'] : 0,
				'rotate_end'    => ! empty( $parallax ) && ! empty( $settings['parallax_rotate_end'] ) ? $settings['parallax_rotate_end']['size'] : 0,
				'opacity_start' => ! empty( $parallax ) && ! empty( $settings['parallax_opacity_start'] ) ? $settings['parallax_opacity_start']['size'] : 1,
				'opacity_end'   => ! empty( $parallax ) && ! empty( $settings['parallax_opacity_end'] ) ? $settings['parallax_opacity_end']['size'] : 1,
				'crop_start'    => ! empty( $parallax ) && ! empty( $settings['parallax_crop_start'] ) ? $settings['parallax_crop_start']['size'] : 0,
				'crop_end'      => ! empty( $parallax ) && ! empty( $settings['parallax_crop_end'] ) ? $settings['parallax_crop_end']['size'] : 100,
				// Text settings
				'text'          => ! empty( $parallax ) && ! empty( $settings['parallax_text'] ) ? $settings['parallax_text'] : 'block',
				'text_separate' => ! empty( $parallax ) && ! empty( $settings['parallax_text_separate'] ) ? 1 : 0,
				'text_wrap'     => ! empty( $parallax ) && ! empty( $settings['parallax_text_wrap'] ) ? 1 : 0,
				// Mouse settings
				'mouse'             => ! empty( $parallax_mouse ) ? 1 : 0,
				'mouse_type'        => ! empty( $parallax_mouse ) && ! empty( $settings['parallax_mouse_type'] ) ? $settings['parallax_mouse_type'] : 'transform3d',
				'mouse_tilt_amount' => ! empty( $parallax_mouse ) && ! empty( $settings['parallax_mouse_tilt_amount'] ) ? $settings['parallax_mouse_tilt_amount']['size'] : 70,
				'mouse_speed'       => ! empty( $parallax_mouse ) && ! empty( $settings['parallax_mouse_speed'] ) ? $settings['parallax_mouse_speed']['size'] : 10,
				'mouse_z'           => ! empty( $parallax_mouse ) && ! empty( $settings['parallax_mouse_z'] ) ? $settings['parallax_mouse_z']['size'] : '',
				'mouse_handler'     => ! empty( $parallax_mouse ) && ! empty( $settings['parallax_mouse_handler'] ) ? $settings['parallax_mouse_handler'] : 'row',
			) ) );
		}
	}
}



// Convert old parameters to the new format
//-----------------------------------------------------

if ( ! function_exists( 'trx_addons_elm_convert_params_parallax' ) ) {
	add_action( 'trx_addons_action_is_new_version_of_plugin', 'trx_addons_elm_convert_params_parallax', 10, 2 );
	add_action( 'trx_addons_action_importer_import_end', 'trx_addons_elm_convert_params_parallax' );
	/**
	 * Convert old parameters to the new format after update plugin ThemeREX Addons to the new version or after import demo data.
	 * Get all metadata '_elementor_data' and convert old parameters of Parallax to the new format
	 *
	 * @hooked trx_addons_action_is_new_version_of_plugin
	 * @hooked trx_addons_action_importer_import_end
	 * 
	 * @param string $new_version New version of the plugin.
	 * @param string $old_version Old version of the plugin.
	 */
	function trx_addons_elm_convert_params_parallax( $new_version = '', $old_version = '' ) {
		if ( empty( $old_version ) ) {
			$old_version = get_option( 'trx_addons_version', '1.0' );
		}
		if ( version_compare( $old_version, '2.18.0', '<' ) || current_action() == 'trx_addons_action_importer_import_end' ) {
			global $wpdb;
			$rows = $wpdb->get_results( "SELECT post_id, meta_id, meta_value
											FROM {$wpdb->postmeta}
											WHERE meta_key='_elementor_data' && meta_value!=''"
										);
			if ( is_array( $rows ) && count( $rows ) > 0 ) {
				foreach ( $rows as $row ) {
					$data = json_decode( $row->meta_value, true );
					if ( trx_addons_elm_convert_params_parallax_elements( $data ) ) {
						$wpdb->query( "UPDATE {$wpdb->postmeta} SET meta_value = '" . wp_slash( wp_json_encode( $data ) ) . "' WHERE meta_id = {$row->meta_id} LIMIT 1" );
					}
				}
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_convert_params_parallax_elements' ) ) {
	/**
	 * Convert old parameters of Parallax to the new format for each element in the Elementor data.
	 * Attention! The parameter $elements passed by reference and modified inside this function!
	 * Return true if $elements is modified (converted) and needs to be saved
	 *
	 * @param array $elements  Array of elements. Passed by reference and modified inside this function!
	 * 
	 * @return boolean True if parameters was changed and needs to be saved
	 */
	function trx_addons_elm_convert_params_parallax_elements( &$elements ) {
		$modified = false;
		if ( is_array( $elements ) ) {
			foreach( $elements as $k => $elm ) {
				// Convert parameters
				if ( ! empty( $elm['settings'] )
					&& is_array( $elm['settings'] )
					&& ! empty( $elm['settings']['parallax'] )
					&& $elm['settings']['parallax'] == 'parallax'
//					&& ! isset( $elm['settings']['parallax_flow'] )
				) {
					// Parallax flow
					if ( ! isset( $elm['settings']['parallax_flow'] ) ) {
						$elements[ $k ]['settings']['parallax_flow'] = 'default';
						$modified = true;
					}
					// Entrance
					if ( isset( $elm['settings']['parallax_entrance'] ) ) {
						if ( $elm['settings']['parallax_entrance'] == 'entrance' ) {
							$elements[ $k ]['settings']['parallax_flow'] = 'entrance';
						}
						unset( $elements[ $k ]['settings']['parallax_entrance'] );
						$modified = true;
					}
					// Animation Range Start
					if ( ! isset( $elm['settings']['parallax_range_start'] ) ) {
						$elements[ $k ]['settings']['parallax_range_start'] = 0;
						$modified = true;
					}
					if ( isset( $elm['settings']['parallax_offset'] ) ) {
						$elements[ $k ]['settings']['parallax_range_start'] = $elm['settings']['parallax_offset'];
						unset( $elements[ $k ]['settings']['parallax_offset'] );
						$modified = true;
					}
					// Animation Range End
					if ( ! isset( $elm['settings']['parallax_range_end'] ) ) {
						$elements[ $k ]['settings']['parallax_range_end'] = 0;
						$modified = true;
					}
					if ( isset( $elm['settings']['parallax_amplitude'] ) ) {
						$elements[ $k ]['settings']['parallax_range_end'] = $elm['settings']['parallax_amplitude'];
						unset( $elements[ $k ]['settings']['parallax_amplitude'] );
						$modified = true;
					}
					// Animate From & To: X, Y, Scale, Rotate, Opacity
					$atts = array( 'x' => 0, 'y' => 0, 'scale' => 100, 'rotate' => 0, 'opacity' => 1 );
					foreach( $atts as $att => $default_value ) {
						if ( ! isset( $elm['settings']["parallax_{$att}_start"] ) && isset( $elm['settings']["parallax_{$att}"] ) ) {
							$elements[ $k ]['settings']["parallax_{$att}_start"] = array(
								'size' => $default_value + ( ! empty( $elm['settings']['parallax_start'] )
																&& $elm['settings']['parallax_start'] == 'start'
																&& ! empty( $elm['settings']["parallax_{$att}"]['size'] )
																	? $elm['settings']["parallax_{$att}"]['size']
																	: 0
																),
								'unit' => $elm['settings']["parallax_{$att}"]['unit']
							);
							$elements[ $k ]['settings']["parallax_{$att}_end"] = array(
								'size' => $default_value + ( ! empty( $elm['settings']['parallax_start'] )
																&& $elm['settings']['parallax_start'] == 'start'
																|| empty( $elm['settings']["parallax_{$att}"]['size'] )
																	? 0
																	: $elm['settings']["parallax_{$att}"]['size']
																),
								'unit' => $elm['settings']["parallax_{$att}"]['unit']
							);
							unset( $elements[ $k ]['settings']["parallax_{$att}"] );
							$modified = true;
						}
					}
					if ( isset( $elm['settings']["parallax_start"] ) ) {
						unset( $elements[ $k ]['settings']["parallax_start"] );
						$modified = true;
					}
				}
				// Process inner elements
				if ( ! empty( $elm['elements'] ) && is_array( $elm['elements'] ) ) {
					$modified = trx_addons_elm_convert_params_parallax_elements( $elements[ $k ]['elements'] ) || $modified;
				}
			}
		}
		return $modified;
	}
}
