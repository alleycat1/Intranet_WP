<?php
/**
 * Widget: Socials (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_widget_socials_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_socials_add_in_elementor' );
	function trx_addons_sc_widget_socials_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Socials_Widget extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_widget_socials';
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
				return __( 'Widget: Socials', 'trx_addons' );
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
				// Detect edit mode
				$is_edit_mode = trx_addons_elm_is_edit_mode();

				// Register controls
				$this->start_controls_section(
					'section_sc_socials',
					[
						'label' => __( 'Widget: Socials', 'trx_addons' ),
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
					'type',
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
						'label' => __( 'Icons align', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(),
						'default' => 'left',
					]
				);
				
				$this->add_control(
					'description',
					[
						'label' => '',
						'label_block' => true,
						'show_label' => false,
						'type' => \Elementor\Controls_Manager::WYSIWYG,
						'default' => ''
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . "socials/tpe.socials.php",
										'trx_addons_args_widget_socials',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Socials_Widget' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_socials_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_socials_black_list' );
	function trx_addons_widget_socials_black_list($list) {
		$list[] = 'trx_addons_widget_socials';
		return $list;
	}
}
