<?php
/**
 * Widget: Video player for Youtube, Vimeo, etc. embeded video (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.1
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_widget_video_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_video_add_in_elementor' );
	function trx_addons_sc_widget_video_add_in_elementor() {
		
		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) return;

		class TRX_Addons_Elementor_Widget_Video extends TRX_Addons_Elementor_Widget {

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
				$this->add_plain_params( [
					'cover' => 'url'
				] );
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
				return 'trx_widget_video';
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
				return __( 'Widget: Video', 'trx_addons' );
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
				return 'eicon-youtube';
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
					'section_sc_video',
					[
						'label' => __( 'Widget: Video', 'trx_addons' ),
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
						'label' => __( 'Type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_widget_video_layouts(),
						'default' => 'default'
					]
				);

				$this->add_control(
					'ratio',
					[
						'label' => __( 'Ratio', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_image_ratio( false, false ),
						'condition' => [
							'type' => 'hover',
						],
						'default' => '16:9'
					]
				);
				
				$this->add_control(
					'subtitle',
					[
						'label' => __( 'Subtitle', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Subtitle under the video", 'trx_addons' ),
						'condition' => [
							'type' => 'hover',
						],
						'default' => ''
					]
				);

				$this->add_control(
					'media_from_post',
					[
						'label' => __( 'Get video from post', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'No', 'trx_addons' ),
						'label_on' => __( 'Yes', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'link',
					[
						'label' => __( 'Video URL', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'Enter an URL of the video (Note: read more about available formats at WordPress Codex page)', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'media_from_post' => ''
						]
					]
				);

				$this->add_control(
					'embed',
					[
						'label' => __( 'Video embed code', 'trx_addons' ),
						'label_block' => true,
						'description' => __( 'or paste the HTML code to embed video in this block', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXTAREA,
						'rows' => 10,
						'separator' => 'none',
						'default' => '',
						'condition' => [
							'type!' => 'hover',
							'media_from_post' => ''
						]
					]
				);

				$this->add_control(
					'cover',
					[
						'label' => __( 'Cover image', 'trx_addons' ),
						'label_block' => true,
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						],
					]
				);

				$this->add_control(
					'autoplay',
					[
						'label' => __( 'Autoplay on load', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'condition' => [
//							'type!' => 'hover',
							'cover[url]' => '',
						],
						'return_value' => '1',
					]
				);

				$this->add_control(
					'mute',
					[
						'label' => __( 'Mute', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
//						'condition' => [
//							'autoplay' => ''
//						],
						'default' => '',
						'return_value' => '1',
					]
				);

				$this->add_control(
					'popup',
					[
						'label' => __( 'Open in the popup', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'type!' => 'hover',
							'cover[url]!' => '',
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . "video/tpe.video.php",
										'trx_addons_args_widget_video',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Video' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_video_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_video_black_list' );
	function trx_addons_widget_video_black_list($list) {
		$list[] = 'trx_addons_widget_video';
		return $list;
	}
}
