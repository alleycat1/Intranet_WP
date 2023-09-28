<?php
/**
 * Shortcode: Display any previously created layout (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.06
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_layouts_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_layouts_add_in_elementor' );
	function trx_addons_sc_layouts_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Layouts_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Layouts extends TRX_Addons_Elementor_Layouts_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.44
			 * @access public
			 *
			 * @param array      $data Widget data. Default is an empty array.
			 * @param array|null $args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params([
					'size' => 'size+unit',
					'show_delay' => 'size'
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
				return 'trx_sc_layouts';
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
				return __( 'Layouts', 'trx_addons' );
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
				return 'eicon-gallery-masonry';
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
				return ['trx_addons-layouts'];
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
				$default = 0;
				$layout = !empty($params['layout']) ? $params['layout'] : $default;

				$this->start_controls_section(
					'section_sc_layouts',
					[
						'label' => __( 'Layouts', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : apply_filters('trx_addons_sc_type', trx_addons_get_list_sc_layouts_type(), 'trx_sc_layouts'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'popup_id',
					[
						'label' => __( "Popup (panel) ID", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Popup (panel) ID is required!", 'trx_addons' ),
						'default' => '',
						'condition' => [
							'type' => ['popup', 'panel']
						]
					]
				);

				$this->add_control(
					'layout', 
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
						'default' => $default
					]
				);

				$this->add_control(
					'template', 
					[
						'label' => __("or Elementor's Template", 'trx_addons'),
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
						'default' => $default,
						'condition' => [
							'layout' => [ 0, '0' ]
						]
					]
				);

				$this->add_control(
					'content',
					[
						'label' => __( 'or text content', 'trx_addons' ),
						'label_block' => true,
						"description" => wp_kses_data( __("Alternative content to be used instead layouts and templates", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::WYSIWYG,
						'default' => '',
						'separator' => 'none',
						'condition' => [
							'layout' => [ 0, '0' ],
							'template' => [ 0, '0' ]
						]
					]
				);

				$this->add_control(
					'position', 
					[
						'label' => __("Panel position", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses_data( __("Dock the panel to the specified side of the window", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_layouts_panel_positions(),
						'default' => 'right',
						'condition' => ['type' => 'panel']
					]
				);

				$this->add_control(
					'effect', 
					[
						'label' => __("Display effect", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses_data( __("Effect to display this panel", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_layouts_panel_effects(),
						'default' => 'slide',
						'condition' => ['type' => 'panel']
					]
				);

				$this->add_control(
					'size',
					[
						'label' => __( 'Size', 'trx_addons' ),
						'description' => wp_kses_data( __("Size (width or height) of the panel", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 300,
							'unit' => 'px'
						],
						'range' => [
							'%' => [
								'min' => 5,
								'max' => 100
							],
							'px' => [
								'min' => 30,
								'max' => 1920
							],
							'em' => [
								'min' => 3,
								'max' => 300
							]
						],
						'size_units' => ['%', 'px', 'em'],
						'condition' => ['type' => 'panel']
					]
				);

				$this->add_control(
					'modal',
					[
						'label' => __( 'Modal', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Disable clicks on the rest window area", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => ['type' => 'panel']
					]
				);

				$this->add_control(
					'shift_page',
					[
						'label' => __( 'Shift page', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Shift page content when panel is opened", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => ['type' => 'panel']
					]
				);

				$this->add_control(
					'show_on', 
					[
						'label' => __("Show on", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses_data( __("The event by which the popup/panel should be displayed", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_layouts_show_on(),
						'default' => 'none',
						'condition' => [
							'type' => ['popup', 'panel']
						]
					]
				);

				$this->add_control(
					'show_delay',
					[
						'label' => __( 'Show delay', 'trx_addons' ),
						'description' => wp_kses_data( __("How many seconds to wait before the popup appears", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 120
							]
						],
						'size_units' => ['px'],
						'condition' => [
							'type' => ['popup', 'panel'],
							'show_on' => ['on_page_load', 'on_page_load_once']
						]
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
			// Commented, because when the 'type' is 'default' -
			// we need to load a custom layout from a server
			// protected function content_template() {
			// 	$this->sc_show_placeholder( array(
			// 		'title' => 'type'
			// 	) );
			// }
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Layouts' );
	}
}

// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_sc_layouts_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_sc_layouts_black_list' );
	function trx_addons_sc_layouts_black_list($list) {
		$list[] = 'TRX_Addons_SOW_Widget_Layouts';
		return $list;
	}
}
