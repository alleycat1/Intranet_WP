<?php
/**
 * Shortcode: Button (Elementor support)
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
if (!function_exists('trx_addons_sc_button_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_button_add_in_elementor' );
	function trx_addons_sc_button_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Button extends TRX_Addons_Elementor_Widget {

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
					'height' => 'size'
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
				return 'trx_sc_button';
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
				return __( 'Button', 'trx_addons' );
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
				return 'eicon-button';
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
					'section_sc_button',
					[
						'label' => __( 'Button', 'trx_addons' ),
					]
				);
				
				$this->add_control(
					'align',
					[
						'label' => __( 'Button alignment', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(),
						'default' => 'none',
					]
				);

				$this->add_control(
					'buttons',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'type' => 'default',
								'link' => ['url' => '#', 'is_external' => ''],
								'title' => esc_html__( 'Button', 'trx_addons' ),
								'subtitle' => '',
								'size' => 'normal',
								'text_align' => 'none',
								'bg_image' => '',
								'icon_position' => 'left',
								'image' => ['url' => ''],
								'icon' => '',
								'item_id' => '',	// 'id' not work in Elementor
								'class' => '',
								'css' => ''
							]
						], 'trx_sc_button'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', array_merge(
							[
								[
									'name' => 'type',
									'label' => __( 'Layout', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'button'), 'trx_sc_button'),
									'default' => 'default',
								],
								[
									'name' => 'size',
									'label' => __( 'Size', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_button_sizes(),
									'default' => 'normal',
								],
								[
									'name' => 'link',
									'label' => __( 'Button URL', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::URL,
									'label_block' => false,
									'placeholder' => __( '//your-link.com', 'trx_addons' ),
									'default' => [
										'url' => '#',
										'is_external' => ''
									]
								],
								[
									'name' => 'title',
									'label' => __( 'Title', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXT,
									'label_block' => false,
									'placeholder' => __( "Title", 'trx_addons' ),
									'default' => __('Button', 'trx_addons')
								],
								[
									'name' => 'subtitle',
									'label' => __( 'Subtitle', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXT,
									'label_block' => false,
									'placeholder' => __( "Subtitle", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'text_align',
									'label' => __( 'Text alignment', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(),
									'default' => 'none',
								],
								[
									'name' => 'bg_image',
									'label' => __( 'Background Image', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
									'condition' => ['type' => 'default']
								]
							],
							$this->get_icon_param(),
							[
								[
									'name' => 'image',
									'label' => __( 'or select an image', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
								],
								[
									'name' => 'icon_position',
									'label' => __( 'Icon position', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_icon_positions(),
									'default' => 'left',
								],
								[
									'name' => 'item_id',	// 'id' not work in Elementor
									'label' => __( 'CSS ID', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXT,
									'label_block' => false,
									'default' => ''
								],
								[
									'name' => 'class',
									'label' => __( 'CSS Class', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXT,
									'label_block' => false,
									'default' => ''
								],
								[
									'name' => 'css',
									'label' => __( 'CSS Rules', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXTAREA,
									'label_block' => true,
									'default' => ''
								],
							]
						),
						'trx_sc_button' ),
						'title_field' => '{{{ title }}}'
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . "button/tpe.button.php",
										'trx_addons_args_sc_button',
										array('element' => $this)
									);
			}

		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Button' );
	}
}
