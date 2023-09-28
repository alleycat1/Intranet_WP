<?php
/**
 * Shortcode: Switcher (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v2.6.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }




// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_switcher_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_switcher_add_in_elementor' );
	function trx_addons_sc_switcher_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;	

		class TRX_Addons_Elementor_Widget_Switcher extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_switcher';
			}

			/**
			 * Retrieve widget title.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget title.
			 */
			public function get_title() {
				return __( 'Switcher', 'trx_addons' );
			}

			/**
			 * Retrieve widget icon.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget icon.
			 */
			public function get_icon() {
				return 'eicon-adjust';
			}

			/**
			 * Retrieve the list of categories the widget belongs to.
			 *
			 * Used to determine where to display the widget in the editor.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return array Widget categories.
			 */
			public function get_categories() {
				return ['trx_addons-elements'];
			}

			/**
			 * Widget preview refresh button.
			 *
			 * @since 2.6.0
			 * @access public
			 */
			/*
			public function is_reload_preview_required() {
				return true;
			}
			*/

			/**
			 * Register widget controls.
			 *
			 * Adds different input fields to allow the user to change and customize the widget settings.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function register_controls() {

				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				// If open params in Elementor Editor
				$params = $this->get_sc_params();

				// Prepare lists
				$layouts = ! $is_edit_mode ? array() : trx_addons_array_merge(	array(
														0 => trx_addons_get_not_selected_text( __( 'Not selected', 'trx_addons' ) )
														),
													trx_addons_get_list_layouts()
													);
				$templates = ! $is_edit_mode ? array() : trx_addons_array_merge(	array(
														0 => trx_addons_get_not_selected_text( __( 'Not selected', 'trx_addons' ) )
														),
													trx_addons_get_list_elementor_templates()
													);
				$layout = 0;

				// Register controls
				$this->start_controls_section(
					'section_sc_switcher',
					[
						'label' => __( 'Switcher', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'switcher'), 'trx_sc_switcher'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'effect',
					[
						'label' => __( 'Effect', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_switcher_effects(),
						'default' => 'swap'
					]
				);

				$this->add_control(
					'heading1',
					[
						'type' => \Elementor\Controls_Manager::HEADING,
						'label' => esc_html__( 'Slide 1', 'trx_addons' ),
						'separator' => 'before',
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'slide1_title',
					[
						'label' => __( 'Title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'slide1_title_color',
					[
						'label' => __( 'Title Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'slide1_switcher_color',
					[
						'label' => __( 'Switcher Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'slide1_type',
					[
						'label' => __("Content type", 'trx_addons'),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_content_types( true ),
						'default' => 'section',
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'slide1_content',
					[
						'label' => __( 'Content', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::WYSIWYG,
						//'separator' => 'none',
						'default' => '',
						'condition' => [
							'slide1_type' => 'content'
						]
					]
				);

				$this->add_control(
					'slide1_section',
					[
						'label' => __( 'Section ID', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => 'default',
							'slide1_type' => 'section'
						]
					]
				);

				$this->add_control(
					'slide1_layout',
					[
						'label' => __("Custom Layout", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses( __("Select any previously created layout to insert to this page", 'trx_addons')
														. '<br>'
														. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																	admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																	__("Open selected layout in a new tab to edit", 'trx_addons')
																),
													'trx_addons_kses_content'
													),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $layouts,
						'default' => 0,
						'condition' => [
							'type' => 'default',
							'slide1_type' => 'layout'
						]
					]
				);

				$this->add_control(
					'slide1_template',
					[
						'label' => __("Elementor's Template", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses( __("Select any previously created template to insert to this page", 'trx_addons')
														. '<br>'
														. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																	admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																	__("Open selected template in a new tab to edit", 'trx_addons')
																),
													'trx_addons_kses_content'
													),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $templates,
						'default' => 0,
						'condition' => [
							'type' => 'default',
							'slide1_type' => 'template'
						]
					]
				);

				$this->add_control(
					'heading2',
					[
						'type' => \Elementor\Controls_Manager::HEADING,
						'label' => esc_html__( 'Slide 2', 'trx_addons' ),
						'separator' => 'before',
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'slide2_title',
					[
						'label' => __( 'Title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'slide2_title_color',
					[
						'label' => __( 'Title Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'slide2_switcher_color',
					[
						'label' => __( 'Switcher Color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'slide2_type',
					[
						'label' => __("Content type", 'trx_addons'),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_content_types( true ),
						'default' => 'section',
						'condition' => [
							'type' => 'default'
						]
					]
				);

				$this->add_control(
					'slide2_content',
					[
						'label' => __( 'Content', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::WYSIWYG,
						//'separator' => 'none',
						'default' => '',
						'condition' => [
							'slide2_type' => 'content'
						]
					]
				);

				$this->add_control(
					'slide2_section',
					[
						'label' => __( 'Section ID', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => 'default',
							'slide2_type' => 'section'
						]
					]
				);

				$this->add_control(
					'slide2_layout',
					[
						'label' => __("Custom Layout", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses( __("Select any previously created layout to insert to this page", 'trx_addons')
														. '<br>'
														. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																	admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																	__("Open selected layout in a new tab to edit", 'trx_addons')
																),
													'trx_addons_kses_content'
													),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $layouts,
						'default' => 0,
						'condition' => [
							'type' => 'default',
							'slide2_type' => 'layout'
						]
					]
				);

				$this->add_control(
					'slide2_template',
					[
						'label' => __("Elementor's Template", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses( __("Select any previously created template to insert to this page", 'trx_addons')
														. '<br>'
														. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																	admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																	__("Open selected template in a new tab to edit", 'trx_addons')
																),
													'trx_addons_kses_content'
													),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $templates,
						'default' => 0,
						'condition' => [
							'type' => 'default',
							'slide2_type' => 'template'
						]
					]
				);

				$this->add_control(
					'slides',
					[
						'label' => esc_html__( 'Slides', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::REPEATER,
						'condition' => [
							'type' => 'tabs'
						],
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'slide_title' => esc_html__( 'First tab', 'trx_addons' ),
								'slide_type' => 'section',
								'slide_section' => '',
								'slide_layout' => 0,
								'slide_template' => 0,
							],
						], 'trx_sc_switcher'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', array_merge(
							[
								[
									'name' => 'slide_title',
									'label' => __( 'Title', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::WYSIWYG,
									//'separator' => 'none',
									'default' => ''
								],
								[
									'name' => 'slide_type',
									'label' => __("Content type", 'trx_addons'),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => trx_addons_get_list_content_types( true ),
									'default' => 'section',
								],
								[
									'name' => 'slide_content',
									'label' => __( 'Content', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::WYSIWYG,
									//'separator' => 'none',
									'default' => '',
									'condition' => [
										'slide_type' => 'content'
									]
								],
								[
									'name' => 'slide_section',
									'label' => __( 'Section ID', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'default' => '',
									'condition' => [
										'slide_type' => 'section'
									]
								],
								[
									'name' => 'slide_layout',
									'label' => __("Custom Layout", 'trx_addons'),
									'label_block' => false,
									'description' => wp_kses( __("Select any previously created layout to insert to this page", 'trx_addons')
																	. '<br>'
																	. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																				admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																				__("Open selected layout in a new tab to edit", 'trx_addons')
																			),
																'trx_addons_kses_content'
																),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => $layouts,
									'default' => 0,
									'condition' => [
										'slide_type' => 'layout'
									]
								],
								[
									'name' => 'slide_template',
									'label' => __("Elementor's Template", 'trx_addons'),
									'label_block' => false,
									'description' => wp_kses( __("Select any previously created template to insert to this page", 'trx_addons')
																	. '<br>'
																	. sprintf('<a href="%1$s" class="trx_addons_post_editor' . (intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																				admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																				__("Open selected template in a new tab to edit", 'trx_addons')
																			),
																'trx_addons_kses_content'
																),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => $templates,
									'default' => 0,
									'condition' => [
										'slide_type' => 'template'
									]
								]
							] ),
							'trx_sc_switcher' 
						),
						'title_field' => '{{{ slide_title }}}'
					]
				);

				$this->end_controls_section();

				$this->add_title_param();
			}

		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Switcher' );
	}
}


// Start buffering output of the sections with specified id
if ( ! function_exists( 'trx_addons_sc_switcher_start_catch_output' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_sc_switcher_start_catch_output', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render', 'trx_addons_sc_switcher_start_catch_output', 10, 1 );
	function trx_addons_sc_switcher_start_catch_output( $element ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_page'] ) && ! trx_addons_is_preview( 'elementor' ) ) {
			if ( is_object( $element ) && $element->get_name() == 'section' ) {
				$id = $element->get_settings( '_element_id' );
				if ( ! empty( $id ) && ! empty( $TRX_ADDONS_STORAGE['catch_output']['sc_switcher'][ $id ] ) ) {
					ob_start();
				}
			}
		}
	}
}


// End buffering output of the sections with specified id
if ( ! function_exists( 'trx_addons_sc_switcher_end_catch_output' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/after_render',  'trx_addons_sc_switcher_end_catch_output', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/after_render', 'trx_addons_sc_switcher_end_catch_output', 10, 1 );
	function trx_addons_sc_switcher_end_catch_output( $element ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['capture_page'] ) && ! trx_addons_is_preview( 'elementor' ) ) {
			if ( is_object( $element ) && $element->get_name() == 'section' ) {
				$id = $element->get_settings( '_element_id' );
				if ( ! empty( $id ) && ! empty( $TRX_ADDONS_STORAGE['catch_output']['sc_switcher'][ $id ] ) ) {
					$TRX_ADDONS_STORAGE['catch_output']['sc_switcher'][ $id ] = ob_get_contents();
					ob_end_clean();
				}
			}
		}
	}
}


// Paste buffer to the sections with specified id
if ( ! function_exists( 'trx_addons_sc_switcher_paste_catch_output' ) ) {
	add_filter( 'trx_addons_filter_page_content', 'trx_addons_sc_switcher_paste_catch_output', 10, 1 );
	function trx_addons_sc_switcher_paste_catch_output( $output ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! trx_addons_is_preview( 'elementor' ) ) {
			if ( ! empty( $TRX_ADDONS_STORAGE['catch_output']['sc_switcher'] ) && is_array( $TRX_ADDONS_STORAGE['catch_output']['sc_switcher'] ) ) {
				foreach( $TRX_ADDONS_STORAGE['catch_output']['sc_switcher'] as $id => $html ) {
					$output = preg_replace(
						'/(<div[^>]*class="sc_switcher_section[^>]*data-section="' . esc_attr( $id ) . '"[^>]*>)[\s]*<\/div>/',
						'${1}' . $html . '</div>',
						$output );
				}
			}
		}
		return $output;
	}
}
