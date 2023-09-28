<?php
/**
 * Widget: Display Contacts info (Elementor support)
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
if (!function_exists('trx_addons_sc_widget_contacts_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_contacts_add_in_elementor' );
	function trx_addons_sc_widget_contacts_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Contacts extends TRX_Addons_Elementor_Widget {

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
					'logo' => 'url',
					'logo_retina' => 'url',
					'map_height' => 'size+unit'
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
				return 'trx_widget_contacts';
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
				return __( 'Widget: Contacts', 'trx_addons' );
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
				return 'eicon-image-box';
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
					'section_sc_contacts',
					[
						'label' => __( 'Widget: Contacts', 'trx_addons' ),
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
					'logo',
					[
						'label' => __( 'Logo', 'trx_addons' ),
						'label_block' => true,
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						],
					]
				);

				$this->add_control(
					'logo_retina',
					[
						'label' => __( 'Logo for Retina', 'trx_addons' ),
						'label_block' => true,
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						],
					]
				);

				$this->add_control(
					'columns',
					[
						'label' => __( 'Break into columns', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Break contact information into two columns with the address being displayed on the left hand side and phone/email - on the right.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'heading_description',
					[
						'label' => __( 'Description', 'elementor' ),
						'description' => wp_kses_data( __("Short description", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
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

				$this->start_controls_section(
					'section_sc_contacts_address',
					[
						'label' => __( 'Contacts', 'trx_addons' ),
					]
				);

				$this->add_control(
					'address',
					[
						'label' => __( 'Address', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Address", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'phone',
					[
						'label' => __( 'Phone', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Phone", 'trx_addons' ),
						'default' => ''
					]
				);
				
				$this->add_control(
					'email',
					[
						'label' => __( 'E-mail', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "E-mail", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'socials',
					[
						'label' => __( 'Show social icons', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_contacts_map',
					[
						'label' => __( 'Map', 'trx_addons' ),
					]
				);

				$this->add_control(
					'map',
					[
						'label' => __( 'Show map', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'map_height',
					[
						'label' => __( 'Height', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 130,
							'unit' => 'px'
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 500
							],
							'em' => [
								'min' => 0,
								'max' => 50
							],
						],
						'size_units' => [ 'px', 'em' ],
						'condition' => [
							'map' => '1'
						],
						'selectors' => [
							'{{WRAPPER}} .sc_googlemap,{{WRAPPER}} .sc_osmap' => 'height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'map_position',
					[
						'label' => __( 'Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => array(
								'top' => __('Top', 'trx_addons'),
								'left' => __('Left', 'trx_addons'),
								'right' => __('Right', 'trx_addons')
							),
						'default' => 'top',
						'condition' => [
							'map' => '1'
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
			protected function content_template() {
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . "contacts/tpe.contacts.php",
										'trx_addons_args_widget_contacts',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Contacts' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_contacts_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_contacts_black_list' );
	function trx_addons_widget_contacts_black_list($list) {
		$list[] = 'trx_addons_widget_contacts';
		return $list;
	}
}
