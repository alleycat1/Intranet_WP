<?php
/**
 * Shortcode: Skills (Elementor support)
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
if (!function_exists('trx_addons_sc_skills_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_skills_add_in_elementor' );
	function trx_addons_sc_skills_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Skills extends TRX_Addons_Elementor_Widget {

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
					'duration' => 'size',
					'cutout' => 'size'
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
				return 'trx_sc_skills';
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
				return __( 'Skills', 'trx_addons' );
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
				return 'eicon-skill-bar';
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
					'section_sc_skills',
					[
						'label' => __( 'Skills', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'skills'), 'trx_sc_skills'),
						'default' => 'counter',
					]
				);

				$this->add_control(
					'style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : apply_filters('trx_addons_sc_style', trx_addons_get_list_sc_skills_counter_styles(), 'trx_sc_skills'),
						'default' => 'counter'
					]
				);

				$this->add_control(
					'icon_position',
					[
						'label' => __( 'Icon position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : apply_filters('trx_addons_sc_skills_icon_positions', trx_addons_get_list_sc_skills_counter_icon_positions()),
						'default' => 'top',
						'condition' => [
							'type' => ['counter']
						]
					]
				);

				$this->add_control(
					'cutout',
					[
						'label' => __( 'Cutout', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify the pie cutout radius. Border width = 100% - cutout value.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100
							]
						],
						'condition' => [
							'type' => ['pie']
						]
					]
				);

				$this->add_control(
					'compact',
					[
						'label' => __( 'Compact pie', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'type' => ['pie']
						]
					]
				);

				$this->add_control(
					'icon_color',
					[
						'label' => __( 'Icon color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
					]
				);

				$this->add_control(
					'color',
					[
						'label' => __( 'Value color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
					]
				);

				$this->add_control(
					'item_title_color',
					[
						'label' => __( 'Title color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
					]
				);

				$this->add_control(
					'bg_color',
					[
						'label' => __( 'Background color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['pie']
						]
					]
				);

				$this->add_control(
					'border_color',
					[
						'label' => __( 'Border color', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['pie']
						]
					]
				);

				$this->add_control(
					'max',
					[
						'label' => __( 'Max. value', 'trx_addons' ),
						'label_block' => false,
						'placeholder' => __( 'Max. value', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '100'
					]
				);

				$this->add_control(
					'duration',
					[
						'label' => __( 'Duration', 'trx_addons' ),
						'description' => wp_kses_data( __("Duration of the animation. If 0 - random duration is used.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 1500
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 3000
							]
						]
					]
				);

				$this->add_responsive_control(
					'columns',
					[
						'label' => __( 'Columns', 'trx_addons' ),
						'description' => wp_kses_data( __("Specify the number of columns. If left empty or assigned the value '0' - auto detect by the number of items.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 12
							]
						]
					]
				);

				$this->add_control(
					'values',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'title' => esc_html__( 'First item', 'trx_addons' ),
								'value' => '60',
								'color' => '',
								'icon_color' => '',
								'item_title_color' => '',
								'char' => '',
								'image' => ['url' => ''],
								'icon'  => trx_addons_elementor_set_settings_icon( 'icon-star-empty' ),
							],
							[
								'title' => esc_html__( 'Second item', 'trx_addons' ),
								'value' => '80',
								'color' => '',
								'icon_color' => '',
								'item_title_color' => '',
								'char' => '',
								'image' => ['url' => ''],
								'icon'  => trx_addons_elementor_set_settings_icon( 'icon-heart-empty' ),
							],
							[
								'title' => esc_html__( 'Third item', 'trx_addons' ),
								'value' => '75',
								'color' => '',
								'icon_color' => '',
								'item_title_color' => '',
								'char' => '',
								'image' => ['url' => ''],
								'icon'  => trx_addons_elementor_set_settings_icon( 'icon-clock-empty' ),
							]
						], 'trx_sc_skills'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', array_merge(
								$this->get_icon_param(),
								[
									[
										'name' => 'icon_color',
										'label' => __( 'Icon color', 'trx_addons' ),
										'type' => \Elementor\Controls_Manager::COLOR,
										'default' => '',
//										'global' => array(
//											'active' => false,
//										),
									],
									[
										'name' => 'char',
										'label' => __( 'or character', 'trx_addons' ),
										'label_block' => false,
										'type' => \Elementor\Controls_Manager::TEXT,
										'placeholder' => __( "Single character", 'trx_addons' ),
										'default' => '',
										'condition' => [
											//'type' => 'counter',
											'icon' => ['', 'none'],
											'image[url]' => '',
										]
									],
									[
										'name' => 'image',
										'label' => __( 'or image', 'trx_addons' ),
										'type' => \Elementor\Controls_Manager::MEDIA,
										'default' => [
											'url' => '',
										],
										'condition' => [
											//'type' => 'counter',
											'icon' => ['', 'none'],
											'char' => ''
										]
									],
									[
										'name' => 'value',
										'label' => __( "Item's value", 'trx_addons' ),
										'label_block' => false,
										'type' => \Elementor\Controls_Manager::TEXT,
										'placeholder' => __( "Item's value", 'trx_addons' ),
										'default' => ''
									],
									[
										'name' => 'color',
										'label' => __( 'Value color', 'trx_addons' ),
										'type' => \Elementor\Controls_Manager::COLOR,
										'default' => '',
//										'global' => array(
//											'active' => false,
//										),
									],
									[
										'name' => 'title',
										'label' => __( 'Title', 'trx_addons' ),
										'label_block' => false,
										'type' => \Elementor\Controls_Manager::TEXT,
										'placeholder' => __( "Item's title", 'trx_addons' ),
										'default' => ''
									],
									[
										'name' => 'item_title_color',
										'label' => __( 'Title color', 'trx_addons' ),
										'type' => \Elementor\Controls_Manager::COLOR,
										'default' => '',
//										'global' => array(
//											'active' => false,
//										),
									],
								]
							),
							'trx_sc_skills'
						),
						'title_field' => '{{{ title }}}: {{{ value }}}',
					]
				);

				$this->end_controls_section();

				$this->add_title_param();
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . "skills/tpe.skills.php",
										'trx_addons_args_sc_skills',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Skills' );
	}
}
