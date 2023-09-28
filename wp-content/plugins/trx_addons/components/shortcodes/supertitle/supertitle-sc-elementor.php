<?php
/**
 * Shortcode: Super title (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.49
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------

if (!function_exists('trx_addons_sc_supertitle_add_in_trx_addons')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_supertitle_add_in_trx_addons' );
	function trx_addons_sc_supertitle_add_in_trx_addons() {

		if (!class_exists('TRX_Addons_Elementor_Widget')) return;

		class TRX_Addons_Elementor_Widget_Supertitle extends TRX_Addons_Elementor_Widget {


			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @param array			$data Widget data. Default is an empty array.
			 * @param array|null	$args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params([
					'size' => 'size+unit',
					'icon_size' => 'size+unit',
					'icon_column' => 'size',
					'header_column' => 'size',
					'gradient_direction' => 'size'
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
				return 'trx_sc_supertitle';
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
				return __( 'Super Title', 'trx_addons' );
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
				return 'eicon-t-letter';
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
					'section_sc_supertitle',
					[
						'label' => __( 'Super Title', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'supertitle'), 'trx_sc_supertitle'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'icon_column',
					[
						'label' => __( 'Icon column size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'description' => wp_kses_data( __("Specify the width of the icon (left) column from 0 (no left column) to 6.", 'trx_addons') ),
						'default' => [
							'size' => 1,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 6
							]
						],
					]
				);

				$this->add_control(
					'header_column',
					[
						'label' => __( 'Main (middle) column size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'description' => wp_kses_data( __("Specify the width of the main (middle) column from 0 (no middle column) to 12. Attention! The sum of values for the two columns (Icon and Main) must not exceed 12.", 'trx_addons') ),
						'default' => [
							'size' => 8,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 12
							]
						],
					]
				);

				$this->add_control(
					'image',
					[
						'label' => __( 'Choose media', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						]
					]
				);

				$this->add_icon_param();

				$this->add_control(
					'icon_color',
					[
						'label' => __( 'Color', 'elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .sc_supertitle_no_icon' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .sc_supertitle_icon_column .sc_icon_type_icons' => 'color: {{VALUE}};',
						]
					]
				);

				$this->add_control(
					'icon_bg_color',
					[
						'label' => __( 'Background color', 'elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .sc_supertitle_icon_column .sc_icon_type_icons' => 'background-color: {{VALUE}}; border-radius: 50%; padding: 20%;',
						]
					]
				);

				$this->add_control(
					'icon_size',
					[
						'label' => __( 'Icon size or Image width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3.3,
							'unit' => 'em'
						],
						'size_units' => [ 'em', 'px' ],
						'range' => [
							'em' => [
								'min' => 0,
								'max' => 20
							],
							'px' => [
								'min' => 0,
								'max' => 200
							]
						],
					]
				);

				$this->add_control(
					'items',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'text' => esc_html__( 'Main title', 'trx_addons' ),
								'align' => 'left',
								'tag' => 'h2',
								'item_type' => 'text',
								'color' => '#aa0000',
								'color2' => '',
							],
							[
								'text' => esc_html__( 'Subtitle left', 'trx_addons' ),
								'align' => 'left',
								'tag' => 'h6',
								'item_type' => 'text',
								'color' => '#0000aa',
								'color2' => '',
							],
							[
								'text' => esc_html__( 'Subtitle right', 'trx_addons' ),
								'align' => 'right',
								'tag' => 'h5',
								'item_type' => 'text',
								'color' => '#00aa00',
								'color2' => '',
							],
						], 'trx_sc_supertitle'),

						'fields' => apply_filters('trx_addons_sc_param_group_params',
							[
								[
									'name' => 'item_type',
									'label' => __( 'Item Type', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_supertitle_item_types(),
									'default' => 'text'
								],
								[
									'name' => 'text',
									'label' => __( 'Text', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXTAREA,
									'dynamic' => [
										'active' => true,
									],
									'placeholder' => __( 'Enter your text', 'trx_addons' ),
									'default' => __( 'Add Your Super Title Text Here', 'trx_addons' ),
									'condition' => [
										'item_type' => 'text'
									]
								],
								[
									'name' => 'link',
									'label' => __( 'Link', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::URL,
									'dynamic' => [
										'active' => true,
									],
									'placeholder' => __( '//your-link.com', 'trx_addons' ),
									'default' => [
										'url' => '',
									],
									'separator' => 'before',
									'condition' => [
										'item_type' => 'text'
									]
								],
								[
									'name' => 'tag',
									'label' => __( 'HTML Tag', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_title_tags('', true),
									'default' => 'h2',
									'condition' => [
										'item_type' => 'text'
									]
								],
								[
									'name' => 'media',
									'label' => __( 'Choose Image', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
									'condition' => [
										'item_type' => 'media'
									]
								],
								[
									'name' => 'item_icon',
									'label' =>  __('Icon', 'trx_addons'),
									'type' => 'trx_icons',
									'label_block' => false,
									'default' => '',
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_icons(trx_addons_get_setting('icons_type')),
									'condition' => [
										'item_type' => 'icon'
									],
								],
								[
									'name' => 'size',
									'label' => __( 'Icon Size', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SLIDER,
									'default' => [
										'size' => 5,
										'unit' => 'em'
									],
									'size_units' => [ 'em', 'px' ],
									'range' => [
										'em' => [
											'min' => 0,
											'max' => 100
										],
										'px' => [
											'min' => 0,
											'max' => 1000
										]
									],
									'condition' => [
										'item_type' => 'icon'
									],
								],
								[
									'name' => 'color',
									'label' => __( 'Color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
									'description' => '',
//									'global' => array(
//										'active' => false,
//									),
									'condition' => [
										'item_type' => ['text', 'icon']
									]
								],
								[
									'name' => 'color2',
									'label' => __( 'Color 2', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
									'description' => '',
//									'global' => array(
//										'active' => false,
//									),
									'condition' => [
										'item_type' => ['text']
									]
								],
								[
									'name' => 'gradient_direction',
									'label' => __( 'Gradient direction', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SLIDER,
									'default' => [
										'size' => 0,
										'unit' => 'px'
									],
									'size_units' => [ 'px' ],
									'range' => [
										'px' => [
											'min' => 0,
											'max' => 360
										]
									],
									'condition' => [
										'item_type' => 'text',
										'color2!' => ''
									],
								],
								[
									'name' => 'float_position',
									'label' => __( 'Float', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(false, false),
									'default' => 'left',
									'condition' => [
										'item_type' => ['media', 'icon']
									]
								],
								[
									'name' => 'align',
									'label' => __( 'Alignment', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::CHOOSE,
									'options' => [
										'left' => [
											'title' => __( 'Left', 'trx_addons' ),
											'icon' => 'fa fa-align-left',
										],
										'right' => [
											'title' => __( 'Right', 'trx_addons' ),
											'icon'  => 'fa fa-align-right',
										],
									],
									'default' => 'left',
								],
								[
									'name' => 'inline',
									'label' => __( 'Inline', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label_off' => __( 'Off', 'trx_addons' ),
									'label_on' => __( 'On', 'trx_addons' ),
									'default' => '',
									'return_value' => '1'
								],
							],
							'trx_sc_supertitle'
						),
						'title_field' => '{{{ item_type }}}: {{{ align }}}',
					]
				);

				$this->end_controls_section();
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'supertitle/tpe.default.php',
					'trx_addons_args_sc_supertitle',
					array('element' => $this)
				);
			}

		}

		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Supertitle' );
	}
}
