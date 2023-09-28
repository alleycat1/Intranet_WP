<?php
/**
 * Widget: Custom links (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.0.46
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_widget_custom_links_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_custom_links_add_in_elementor' );
	function trx_addons_sc_widget_custom_links_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Custom_Links extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_widget_custom_links';
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
				return __( 'Widget: Custom Links', 'trx_addons' );
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
				return 'eicon-toggle';
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
				return ['trx_addons-widgets'];
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
				$this->start_controls_section(
					'section_sc_custom_links',
					[
						'label' => __( 'Widget: Custom Links', 'trx_addons' ),
					]
				);
				
				$this->add_control(
					'title',
					[
						'label' => __( 'Title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Widget title", 'trx_addons' ),
						'default' => ''
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
					'links',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'title' => __( 'First link', 'trx_addons' ),
								'description' => $this->get_default_description(),
								'url' => ['url' => '#', 'is_external' => ''],
								'image' => ['url' => ''],
								'icon' => trx_addons_elementor_set_settings_icon( 'icon-star-empty' ),
								'caption' => '',
								'color' => '',
								'label' => '',
								'label_bg_color' => '',
								'label_on_hover' => '',
							],
							[
								'title' => __( 'Second link', 'trx_addons' ),
								'description' => $this->get_default_description(),
								'url' => ['url' => '#', 'is_external' => ''],
								'image' => ['url' => ''],
								'icon' => trx_addons_elementor_set_settings_icon( 'icon-heart-empty' ),
								'caption' => '',
								'color' => '',
								'label' => '',
								'label_bg_color' => '',
								'label_on_hover' => '',
							],
							[
								'title' => __( 'Third link', 'trx_addons' ),
								'description' => $this->get_default_description(),
								'url' => ['url' => '#', 'is_external' => ''],
								'image' => ['url' => ''],
								'icon' => trx_addons_elementor_set_settings_icon( 'icon-clock-empty' ),
								'caption' => '',
								'color' => '',
								'label' => '',
								'label_bg_color' => '',
								'label_on_hover' => '',
							]
						], 'trx_widget_custom_links'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', array_merge(
							[
								[
									'name' => 'title',
									'label' => __( 'Title', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Link's title", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'url',
									'label' => __( 'Link URL', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::URL,
									'placeholder' => __( '//your-link.com', 'trx_addons' ),
									'default' => [ 'url' => '' ]
								],
								[
									'name' => 'caption',
									'label' => __( 'Button caption', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Caption", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'image',
									'label' => __( 'Image', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
								]
							],
							$this->get_icon_param(),
							[
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
									'name' => 'color',
									'label' => __( 'Link color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
								],
								[
									'name' => 'label',
									'label' => __( 'Label', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Label", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'label_bg_color',
									'label' => __( 'Label bg Color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
								],
								[
									'name' => 'label_on_hover',
									'label' => __( 'Show label on hover', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label_off' => __( 'Off', 'trx_addons' ),
									'label_on' => __( 'On', 'trx_addons' ),
									'return_value' => '1'
								],
							] ),
							'trx_widget_custom_links'
						),
						'title_field' => '{{{ title }}}',
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . "custom_links/tpe.custom_links.php",
										'trx_addons_args_widget_custom_links',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Custom_Links' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_custom_links_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_custom_links_black_list' );
	function trx_addons_widget_custom_links_black_list($list) {
		$list[] = 'trx_addons_widget_custom_links';
		return $list;
	}
}
