<?php
/**
 * Shortcode: OpenStreet Map (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.63
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_osmap_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_osmap_add_in_elementor' );
	function trx_addons_sc_osmap_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Osmap extends TRX_Addons_Elementor_Widget {

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
					'width' => 'size+unit',
					'height' => 'size',
					'zoom' => 'size',
					'cluster' => 'url',
					'icon' => 'url',
					'icon_retina' => 'url',
					'icon_width' => 'size',
					'icon_height' => 'size',
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
				return 'trx_sc_osmap';
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
				return __( 'OpenStreet Map', 'trx_addons' );
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
				return 'eicon-image-hotspot';
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
				$styles = ! $is_edit_mode ? array() : trx_addons_get_list_sc_osmap_styles();
				$style_default = count( $styles ) > 0 ? trx_addons_array_get_first( $styles ) : '';

				$this->start_controls_section(
					'section_sc_osmap',
					[
						'label' => __( 'OpenStreet Map', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'osmap'), 'trx_sc_osmap'),
						'default' => 'default',
					]
				);

				$this->add_control(
					'style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $styles,
						'default' => $style_default,
					]
				);

				$this->add_control(
					'prevent_scroll',
					[
						'label' => __( 'Prevent scroll', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'address',
					[
						'label' => __( 'Address or Lat,Lng', 'trx_addons' ),
						'label_block' => true,
						'description' => wp_kses_data( __("Specify the address (or comma separated LatLng) if you don't need a unique marker, title or LatLng coordinates. Otherwise, leave this field empty and specify the markers below.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Address or Lat,Lng", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'center',
					[
						'label' => __( 'Center of the map', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Comma separated coordinates of the map's center. If left empty, the coordinates of the first marker will be used.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Center", 'trx_addons' ),
						'default' => ''
					]
				);				
				
				$this->add_control(
					'zoom',
					[
						'label' => __( 'Zoom', 'trx_addons' ),
						'description' => wp_kses_data( __("Map zoom factor on a scale from 1 to 18. If assigned the value '0' or left empty, fit the bounds to markers.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 16
						],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 18
							]
						]
					]
				);

				$this->add_control(
					'width',
					[
						'label' => __( 'Width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 100,
							'unit' => '%'
						],
						'range' => [
							'%' => [
								'min' => 10,
								'max' => 100
							],
							'px' => [
								'min' => 50,
								'max' => 1920
							]
						],
						'size_units' => ['%', 'px'],
						'selectors' => [
							'{{WRAPPER}} .sc_osmap' => 'width: {{SIZE}}{{UNIT}};',
						],
					]
				);
				
				$this->add_control(
					'height',
					[
						'label' => __( 'Height', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 350
						],
						'range' => [
							'px' => [
								'min' => 50,
								'max' => 1000
							]
						],
						'selectors' => [
							'{{WRAPPER}} .sc_osmap' => 'height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_osmap_markers',
					[
						'label' => __( 'Markers', 'trx_addons' ),
					]
				);
				
				$this->add_control(
					'markers',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'address' => __('51.50343,-0.12048', 'trx_addons'),
								'icon' => ['url' => ''],
								'icon_retina' => ['url' => ''],
								'icon_width' => ['size' => 0, 'unit' => 'px'],
								'icon_height' => ['size' => 0, 'unit' => 'px'],
								'title' => __( 'One', 'trx_addons' ),
								'description' => ''
							]
						], 'trx_sc_osmap'),
						'fields' => apply_filters('trx_addons_sc_param_group_params',
							[
								[
									'name' => 'address',
									'label' => __( "Address or Lat,Lng", 'trx_addons' ),
									'label_block' => true,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Address or Lat,Lng", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'icon',
									'label' => __( 'Icon', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
								],
								[
									'name' => 'icon_retina',
									'label' => __( 'Icon for Retina', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
								],
								[
									'name' => 'icon_width',
									'label' => __( "Icon's width", 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SLIDER,
									'default' => [
										'size' => 0,
										'unit' => 'px'
									],
									'range' => [
										'px' => [
											'min' => 0,
											'max' => 128
										]
									]
								],
								[
									'name' => 'icon_height',
									'label' => __( "Icon's height", 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SLIDER,
									'default' => [
										'size' => 0,
										'unit' => 'px'
									],
									'range' => [
										'px' => [
											'min' => 0,
											'max' => 128
										]
									]
								],
								[
									'name' => 'title',
									'label' => __( 'Title', 'trx_addons' ),
									'label_block' => true,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Marker's title", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'description',
									'label' => __( 'Description', 'trx_addons' ),
									'label_block' => true,
									'type' => \Elementor\Controls_Manager::WYSIWYG,
									'default' => '',
									'separator' => 'none'
								],
							],
							'trx_sc_osmap'
						),
						'title_field' => '{{{ title }}}',
					]
				);

				$this->add_control(
					'cluster',
					[
						'label' => __( 'Cluster icon', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => ''
						]
					]
				);

				$this->end_controls_section();
				
				$this->start_controls_section(
					'section_sc_osmap_content',
					[
						'label' => __( 'Additional content', 'trx_addons' ),
					]
				);

				$this->add_control(
					'content',
					[
						'label' => __( 'Content', 'trx_addons' ),
						'label_block' => true,
						'description' => wp_kses_data(__( "Content to place over the map", 'trx_addons' )),
						'type' => \Elementor\Controls_Manager::WYSIWYG,
						'default' => '',
						'separator' => 'none'
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . "osmap/tpe.osmap.php",
										'trx_addons_args_sc_osmap',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Osmap' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_sc_osmap_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_sc_osmap_black_list' );
	function trx_addons_sc_osmap_black_list($list) {
		$list[] = 'TRX_Addons_SOW_Widget_Osmap';
		return $list;
	}
}
