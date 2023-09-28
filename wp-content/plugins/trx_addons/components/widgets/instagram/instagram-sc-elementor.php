<?php
/**
 * Widget: Instagram (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.47
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_widget_instagram_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_instagram_add_in_elementor' );
	function trx_addons_sc_widget_instagram_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Instagram extends TRX_Addons_Elementor_Widget {

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
					'count' => 'size',
					'columns' => 'size',
					'columns_gap' => 'size+unit'
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
				return 'trx_widget_instagram';
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
				return __( 'Widget: Instagram', 'trx_addons' );
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
				return 'eicon-image';
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
					'section_sc_instagram',
					[
						'label' => __( 'Widget: Instagram', 'trx_addons' ),
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
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('widgets', 'instagram'), 'trx_widget_instagram'),
						'default' => 'default',
					]
				);

				$this->add_control(
					'demo',
					[
						'label' => __( 'Demo mode', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'demo_thumb_size',
					[
						'label' => __( 'Thumb size', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_thumbnail_sizes(),
						'default' => apply_filters( 'trx_addons_filter_thumb_size',
													trx_addons_get_thumb_size( 'avatar' ),
													'trx_addons_widget_instagram',
													array()
												),
						'condition' => [
							'demo' => '1'
						],
					]
				);

				$this->add_control(
					'demo_files',
					[
						'label'   => __( 'Demo images', 'trx_addons' ),
						'type'    => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters(
							'trx_addons_sc_param_group_value', [
								[
									'image' => [ 'url' => '' ],
									'video' => '',
								],
							], 'trx_widget_instagram'
						),
						'fields'  => apply_filters( 'trx_addons_sc_param_group_params',
								[
									[
										'name'        => 'image',
										'label'       => __( 'Image', 'trx_addons' ),
										'label_block' => true,
										'type'        => \Elementor\Controls_Manager::MEDIA,
										'default'     => [
											'url' => '',
										],
									],
									[
										'name'        => 'video',
										'label'       => __( 'or Video URL', 'trx_addons' ),
										'label_block' => false,
										'type'        => \Elementor\Controls_Manager::TEXT,
										'default'     => '',
										'placeholder' => __( '//video.url', 'trx_addons' ),
									],
								],
								'trx_widget_instagram'
						),
						'condition' => [
							'demo' => '1'
						],
					]
				);

				$this->add_control(
					'hashtag',
					[
						'label' => __( 'Hashtag or Username', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "#hashtag", 'trx_addons' ),
						'condition' => [
							'demo' => ''
						],
						'default' => ''
					]
				);
				
				$this->add_control(
					'count',
					[
						'label' => __( 'Number of photos', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'condition' => [
							'demo' => ''
						],
						'default' => [
							'size' => 8
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 30
							]
						]
					]
				);
				
				$this->add_control(
					'columns',
					[
						'label' => __( 'Columns', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 4
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 12
							]
						],
						'selectors' => [
							'{{WRAPPER}} .widget_instagram_images .widget_instagram_images_item_wrap' => 'width:calc(100%/{{SIZE}});',
						]
					]
				);

				$this->add_control(
					'columns_gap',
					[
						'label' => __( 'Gap between columns', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'size_units' => ['px', 'em'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .widget_instagram_images' => 'margin-right: -{{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .widget_instagram_images .widget_instagram_images_item_wrap' => 'padding: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;',
						],
					]
				);
				
				$this->add_control(
					'links',
					[
						'label' => __( 'Links', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Where to send a visitor after clicking on the picture", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_instagram_redirects(),
						'default' => 'instagram'
					]
				);
				
				$this->add_control(
					'ratio',
					[
						'label' => __( 'Image ratio', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Select a ratio to show images. Default leave original ratio for each image", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_image_ratio( false ),
						'default' => 'none'
					]
				);

				$this->add_control(
					'follow',
					[
						'label' => __( 'Show button "Follow Me"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'follow_link',
					[
						'label' => __( 'Button link', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'follow' => '1'
						],
					]
				);

				$this->end_controls_section();
			}
		}

		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Instagram' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_instagram_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_instagram_black_list' );
	function trx_addons_widget_instagram_black_list($list) {
		$list[] = 'trx_addons_widget_instagram';
		return $list;
	}
}
