<?php
/**
 * Shortcode: Icons (Elementor support)
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
if (!function_exists('trx_addons_sc_icons_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_icons_add_in_elementor' );
	function trx_addons_sc_icons_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Icons extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_icons';
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
				return __( 'Icons', 'trx_addons' );
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
				return 'eicon-info-box';
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
					'section_sc_icons',
					[
						'label' => __( 'Icons', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'icons'), 'trx_sc_icons'),
						'default' => 'default',
					]
				);

				$this->add_control(
					'align',
					[
						'label' => __( 'Align', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(),
						'default' => 'center',
					]
				);

				$this->add_control(
					'size',
					[
						'label' => __( 'Icon size', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_icon_sizes(),
						'default' => 'medium',
					]
				);

				$this->add_control(
					'color',
					[
						'label' => __( 'Icon color', 'trx_addons' ),
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
						'label' => __( 'Item title color', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
					]
				);

				$this->add_control(
					'item_text_color',
					[
						'label' => __( 'Item text color', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
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
					'icons_animation',
					[
						'label' => __( 'Icons animation', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Toggle on if you want to animate icons. Attention! Animation is enabled only if there is an .SVG  icon in your theme with the same name as the selected icon.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);
				
				$this->add_control(
					'icons',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'title' => __( 'First icon', 'trx_addons' ),
								'link' => ['url' => ''],
								'description' => $this->get_default_description(),
								'color' => '',
								'item_title_color' => '',
								'item_text_color' => '',
								'char' => '',
								'image' => ['url' => ''],
								'icon' => trx_addons_elementor_set_settings_icon( 'icon-star-empty' )
							],
							[
								'title' => __( 'Second icon', 'trx_addons' ),
								'link' => ['url' => ''],
								'description' => $this->get_default_description(),
								'color' => '',
								'item_title_color' => '',
								'item_text_color' => '',
								'char' => '',
								'image' => ['url' => ''],
								'icon' => trx_addons_elementor_set_settings_icon( 'icon-heart-empty' )
							],
							[
								'title' => __( 'Third icon', 'trx_addons' ),
								'link' => ['url' => ''],
								'description' => $this->get_default_description(),
								'color' => '',
								'item_title_color' => '',
								'item_text_color' => '',
								'char' => '',
								'image' => ['url' => ''],
								'icon' => trx_addons_elementor_set_settings_icon( 'icon-clock-empty' )
							]
						], 'trx_sc_icons'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', array_merge(
							$this->get_icon_param(),
							[
								[
									'name' => 'svg',
									'label' => __( 'or SVG-icon', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'media_types' => [ 'svg' ],
									'default' => [
										'url' => '',
									],
									'condition' => [
										'icon' => ['', 'none'],
										'char' => '',
										'image[url]' => '',
									]
								],
								[
									'name' => 'char',
									'label' => __( 'or character', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Single character", 'trx_addons' ),
									'default' => '',
									'condition' => [
										'icon' => ['', 'none'],
										'svg[url]' => '',
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
										'icon' => ['', 'none'],
										'svg[url]' => '',
										'char' => ''
									]
								],
								[
									'name' => 'color',
									'label' => __( 'Icon color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
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
//									'global' => array(
//										'active' => false,
//									),
								],
								[
									'name' => 'link',
									'label' => __( 'Link', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::URL,
									'default' => ['url' => ''],
									'placeholder' => __( '//your-link.com', 'trx_addons' ),
								],
								[
									'name' => 'description',
									'label' => __( 'Description', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXTAREA,
									'placeholder' => __( "Short description of this item", 'trx_addons' ),
									'default' => '',
									'separator' => 'none',
									'rows' => 10,
									'show_label' => false,
								],
								[
									'name' => 'item_text_color',
									'label' => __( 'Text color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
								],
							] ),
							'trx_sc_icons' ),
						'title_field' => '{{{ title }}}',
					]
				);

				$this->end_controls_section();

				$this->add_slider_param();
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . "icons/tpe.icons.php",
										'trx_addons_args_sc_icons',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Icons' );
	}
}
