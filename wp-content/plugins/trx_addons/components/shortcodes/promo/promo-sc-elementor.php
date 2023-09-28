<?php
/**
 * Shortcode: Promo block (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_promo_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_promo_add_in_elementor' );
	function trx_addons_sc_promo_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Promo extends TRX_Addons_Elementor_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @param array      $data Widget data. Default is an empty array.
			 * @param array|null $args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params([
					'gap' => 'size+unit',
					'image_width' => 'size+unit',
					'link2' => 'url'
				]);
			}

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_promo';
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
				return __( 'Promo', 'trx_addons' );
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
				return 'eicon-image-before-after';
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

				// Register controls
				$this->start_controls_section(
					'section_sc_promo',
					[
						'label' => __( 'Promo', 'trx_addons' ),
					]
				);

					$this->add_control(
						'type',
						[
							'label' => __( 'Layout', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'promo'), 'trx_sc_promo'),
							'default' => 'default',
						]
					);

					$this->add_icon_param();

					$this->add_control(
						'icon_color',
						[
							'label' => __( 'Icon color', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '',
							'selectors' => [
								'{{WRAPPER}} .sc_promo_icon' => 'color: {{VALUE}};',
							]
						]
					);

					$this->add_control(
						'text_bg_color',
						[
							'label' => __( 'Text bg color', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '',
							'selectors' => [
								'{{WRAPPER}} .sc_promo_text_inner' => 'background-color: {{VALUE}};',
							]
						]
					);

					$this->add_title_param('', [
						'title' => ['default' => __('Promo block', 'trx_addons')],
						'subtitle' => ['default' => $this->get_default_subtitle()],
						'description' => ['default' => $this->get_default_description()],
					]);

					$this->add_control(
						'link2',
						[
							'label' => __( 'Button 2 URL', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::URL,
							'default' => [
								'url' => ''
							],
							'condition' => [
								'type' => ['modern']
							]
						]
					);

					$this->add_control(
						'link2_text',
						[
							'label' => __( 'Button 2 text', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::TEXT,
							'default' => '',
							'condition' => [
								'type' => ['modern']
							]
						]
					);

					$this->add_control(
						'link2_style',
						[
							'label' => __( 'Button 2 style', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'button'), 'trx_sc_button'),
							'default' => 'default',
							'condition' => [
								'type' => ['modern']
							]
						]
					);

				$this->end_controls_section();
				
				$this->start_controls_section(
					'section_sc_promo_image',
					[
						'label' => __( 'Image & Video', 'trx_addons' ),
					]
				);
				
					$this->add_control(
						'images',
						[
							'label' => __( 'Image', 'trx_addons' ),
							'description' => wp_kses_data( __("Select the promo image from the library for this section. Show slider if you select 2+ images", 'trx_addons') ),
							'type' => \Elementor\Controls_Manager::GALLERY,
							'default' => []
						]
					);

					$this->add_control(
						'image_bg_color',
						[
							'label' => __( 'Image bg color', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '',
//							'global' => array(
//								'active' => false,
//							),
						]
					);

					$this->add_control(
						'image_cover',
						[
							'label' => __( 'Image cover area', 'trx_addons' ),
							'label_block' => false,
							"description" => wp_kses_data( __("Fit an image into the area or cover it.", 'trx_addons') ),
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'default' => '1',
							'return_value' => '1',
						]
					);

					$this->add_control(
						'image_position',
						[
							'label' => __( 'Image position', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_promo_positions(),
							'default' => 'left',
						]
					);

					$this->add_control(
						'image_width',
						[
							'label' => __( 'Image width', 'trx_addons' ),
							'description' => wp_kses_data( __("Specify width of the image. If left empty or assigned the value '0', the columns will be equal.", 'trx_addons') ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => [
								'size' => 50,
								'unit' => '%'
							],
							'size_units' => [ '%', 'px' ],
							'range' => [
								'%' => [
									'min' => 0,
									'max' => 100
								],
								'px' => [
									'min' => 0,
									'max' => 1920
								]
							],
						]
					);

					$this->add_control(
						'video_url',
						[
							'label' => __( 'Video URL', 'trx_addons' ),
							'label_block' => false,
							'description' => __( 'Enter link to the video (Note: read more about available formats at WordPress Codex page)', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::TEXT,
							'default' => '',
						]
					);

					$this->add_control(
						'video_embed',
						[
							'label' => __( 'Video embed code', 'trx_addons' ),
							'label_block' => true,
							'description' => __( 'or paste the HTML code to embed video in this block', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::TEXTAREA,
							'rows' => 10,
							'separator' => 'none',
							'default' => '',
						]
					);

					$this->add_control(
						'video_in_popup',
						[
							'label' => __( 'Video in the popup', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1',
						]
					);

				$this->end_controls_section();
				
				$this->start_controls_section(
					'section_sc_promo_content',
					[
						'label' => __( 'Additional content', 'trx_addons' )
					]
				);

					$this->add_control(
						'content',
						[
							'label' => __( 'Content', 'trx_addons' ),
							'label_block' => true,
							'description' => wp_kses_data(__( "Custom content (html and shortcodes are allowed)", 'trx_addons' )),
							'type' => \Elementor\Controls_Manager::WYSIWYG,
							'default' => '',
							'separator' => 'none'
						]
					);

				$this->end_controls_section();
				
				$this->start_controls_section(
					'section_sc_promo_dimensions',
					[
						'label' => __( 'Dimensions', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
					]
				);

					$this->add_control(
						'size',
						[
							'label' => __( 'Size', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_promo_sizes(),
							'default' => 'normal'
						]
					);

					$this->add_control(
						'full_height',
						[
							'label' => __( 'Full height', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1'
						]
					);

					$this->add_control(
						'text_width',
						[
							'label' => __( 'Text width', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_promo_widths(),
							'default' => 'none'
						]
					);

					$this->add_control(
						'text_float',
						[
							'label' => __( 'Text block floating', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(),
							'default' => 'none'
						]
					);

					$this->add_control(
						'text_align',
						[
							'label' => __( 'Text alignment', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SELECT,
							'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(),
							'default' => 'none'
						]
					);

					$this->add_control(
						'text_paddings',
						[
							'label' => __( 'Text paddings', 'trx_addons' ),
							'label_block' => false,
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'default' => '1',
							'return_value' => '1'
						]
					);

					$this->add_control(
						'text_margins',
						[
							'label' => __( 'Text margins', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ '%', 'em', 'px' ],
							'selectors' => [
								'{{WRAPPER}} .sc_promo_text_inner' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							]
						]
					);

					$this->add_control(
						'gap',
						[
							'label' => __( 'Gap', 'trx_addons' ),
							"description" => wp_kses_data( __("Gap between text and image (in percent)", 'trx_addons') ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => [
								'size' => 0
							],
							'size_units' => [ '%', 'em', 'px' ],
							'range' => [
								'%' => [
									'min' => 0,
									'max' => 50
								],
								'em' => [
									'min' => 0,
									'max' => 20
								],
								'px' => [
									'min' => 0,
									'max' => 300
								]
							]
						]
					);
				
				$this->end_controls_section();
			}

			// Prepare specific params for this shortcode
			protected function sc_prepare_atts($atts, $sc='', $level=0) {
				if ( ! empty($atts['text_margins']) && is_array($atts['text_margins']) ) {
					if ( ! empty($atts['text_margins']['top']) || ! empty($atts['text_margins']['right']) || ! empty($atts['text_margins']['bottom']) || ! empty($atts['text_margins']['left']) ) {
						$atts['text_margins'] = (float) $atts['text_margins']['top'] . $atts['text_margins']['unit']
												. ' ' . (float) $atts['text_margins']['right'] . $atts['text_margins']['unit']
												. ' ' . (float) $atts['text_margins']['bottom'] . $atts['text_margins']['unit']
												. ' ' . (float) $atts['text_margins']['left'] . $atts['text_margins']['unit'];
					} else {
						$atts['text_margins'] = '';
					}
						
				}
				return parent::sc_prepare_atts($atts, $sc, $level);
			}

			/**
			 * Render widget's template for the editor.
			 *
			 * Written as a Backbone JavaScript template and used to generate the live preview.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function content_template() {
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . "promo/tpe.promo.php",
										'trx_addons_args_sc_promo',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Promo' );
	}
}
