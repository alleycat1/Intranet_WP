<?php
/**
 * Widget: Twitter (Elementor support)
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
if (!function_exists('trx_addons_sc_widget_twitter_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_twitter_add_in_elementor' );
	function trx_addons_sc_widget_twitter_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Twitter extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_widget_twitter';
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
				return __( 'Widget: Twitter', 'trx_addons' );
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
				return 'eicon-twitter-feed';
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
					'section_sc_twitter_account',
					[
						'label' => __( 'Twitter API Keys', 'trx_addons' ),
						'description' => wp_kses_data( __("To get API keys you need to create an application in your Twitter account", 'trx_addons') ),
					]
				);

				$this->add_control(
					'twitter_api',
					[
						'label' => __( 'Twitter API', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_twitter_api(),
						'default' => 'token'
					]
				);

				$this->add_control(
					'username',
					[
						'label' => __( 'Twitter Username', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Username", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'consumer_key',
					[
						'label' => __( 'Consumer Key', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Consumer Key", 'trx_addons' ),
						'default' => '',
						'condition' => array(
							'twitter_api' => array( 'token' )
						)
					]
				);

				$this->add_control(
					'consumer_secret',
					[
						'label' => __( 'Consumer Secret', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Consumer Secret", 'trx_addons' ),
						'default' => '',
						'condition' => array(
							'twitter_api' => array( 'token' )
						)
					]
				);

				$this->add_control(
					'token_key',
					[
						'label' => __( 'Token Key', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Token Key", 'trx_addons' ),
						'default' => '',
						'condition' => array(
							'twitter_api' => array( 'token' )
						)
					]
				);

				$this->add_control(
					'token_secret',
					[
						'label' => __( 'Token Secret', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Token Secret", 'trx_addons' ),
						'default' => '',
						'condition' => array(
							'twitter_api' => array( 'token' )
						)
					]
				);

				$this->add_control(
					'bearer',
					[
						'label' => __( 'Bearer', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Bearer", 'trx_addons' ),
						'default' => '',
						'condition' => array(
							'twitter_api' => array( 'bearer' )
						)
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('widgets', 'twitter'), 'trx_widget_twitter'),
						'default' => 'list',
						'condition' => array(
							'twitter_api!' => array( 'embed' )
						)
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
					'count',
					[
						'label' => __( 'Count', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 2
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 30
							]
						]
					]
				);
				
				$this->add_responsive_control(
					'columns',
					[
						'label' => __( 'Columns', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 1
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 12
							]
						],
						'condition' => [
							'type' => [ 'default' ],
							'twitter_api!' => [ 'embed' ]
						]
					]
				);

				$this->add_control(
					'embed_header',
					[
						'label' => __( 'Show embed header', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => [
							'twitter_api' => [ 'embed' ]
						],
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'embed_footer',
					[
						'label' => __( 'Show embed footer', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => [
							'twitter_api' => [ 'embed' ]
						],
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'embed_borders',
					[
						'label' => __( 'Show embed borders', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => [
							'twitter_api' => [ 'embed' ]
						],
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'embed_scrollbar',
					[
						'label' => __( 'Show embed scrollbar', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => [
							'twitter_api' => [ 'embed' ]
						],
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'embed_transparent',
					[
						'label' => __( 'Make embed bg transparent', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => [
							'twitter_api' => [ 'embed' ]
						],
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'follow',
					[
						'label' => __( 'Show Follow Us', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '1',
						'return_value' => '1'
					]
				);

				$this->add_control(
					'bg_image',
					[
						'label' => __( 'Background Image', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						],
					]
				);

				$this->add_slider_param('', [
					'slider' => [
									'condition' => ['type' => 'default']
								]
					]);

				$this->end_controls_section();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Twitter' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_twitter_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_twitter_black_list' );
	function trx_addons_widget_twitter_black_list($list) {
		$list[] = 'trx_addons_widget_twitter';
		return $list;
	}
}
