<?php
/**
 * Shortcode: Socials (Elementor support)
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
if (!function_exists('trx_addons_sc_socials_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_socials_add_in_elementor' );
	function trx_addons_sc_socials_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Socials extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_socials';
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
				return __( 'Socials', 'trx_addons' );
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
				return 'eicon-social-icons';
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
					'section_sc_socials',
					[
						'label' => __( 'Socials', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'socials'), 'trx_sc_socials'),
						'default' => 'default',
					]
				);

				$this->add_control(
					'icons_type',
					[
						'label' => __( 'Icons type', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_socials_types(),
						'default' => 'socials',
					]
				);

				$this->add_control(
					'align',
					[
						'label' => __( 'Icons alignment', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(),
						'default' => 'none',
					]
				);
				
				$this->add_control(
					'icons',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'title' => '',
								'link'  => '',
								'icon'  => ''
							]
						], 'trx_sc_socials'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', array_merge(
								[
									[
										'name' => 'link',
										'label' => __( "Link to profile", 'trx_addons' ),
										'label_block' => false,
										'type' => \Elementor\Controls_Manager::TEXT,
										'placeholder' => __( "Your profile URL", 'trx_addons' ),
										'default' => ''
									],
									[
										'name' => 'title',
										'label' => __( 'Title', 'trx_addons' ),
										'label_block' => false,
										'type' => \Elementor\Controls_Manager::TEXT,
										'label_block' => true,
										'placeholder' => __( "Icon's title", 'trx_addons' ),
										'default' => ''
									]
								],
								$this->get_icon_param(true)
							),
							'trx_sc_socials'
						),
						'title_field' => '{{{ title }}}',
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . "socials/tpe.socials.php",
										'trx_addons_args_sc_socials',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Socials' );
	}
}
