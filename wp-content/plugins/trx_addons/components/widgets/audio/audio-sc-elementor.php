<?php
/**
 * Widget: Audio player for Local hosted audio and Soundcloud and other embeded audio (Elementor support)
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
if ( ! function_exists( 'trx_addons_sc_widget_audio_add_in_elementor' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_widget_audio_add_in_elementor' );
	function trx_addons_sc_widget_audio_add_in_elementor() {

		if ( ! class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			return;
		}

		class TRX_Addons_Elementor_Widget_Audio extends TRX_Addons_Elementor_Widget {

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
				$this->add_plain_params(
					[
						'cover' => 'url'
					]
				);
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
				return 'trx_widget_audio';
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
				return __( 'Widget: Audio', 'trx_addons' );
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
				return 'eicon-posts-ticker';
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
				return [ 'trx_addons-widgets' ];
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
					'section_sc_audio',
					[
						'label' => __( 'Widget: Audio', 'trx_addons' ),
					]
				);

				$this->add_control(
					'title',
					[
						'label'       => __( 'Title', 'trx_addons' ),
						'label_block' => false,
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( 'Widget title', 'trx_addons' ),
						'default'     => '',
					]
				);

				$this->add_control(
					'subtitle',
					[
						'label'       => __( 'Subtitle', 'trx_addons' ),
						'label_block' => false,
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( 'Widget subtitle', 'trx_addons' ),
						'default'     => '',
					]
				);

				$this->add_control(
					'media_from_post',
					[
						'label'        => __( 'Get audio from post', 'trx_addons' ),
						'label_block'  => false,
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_off'    => __( 'No', 'trx_addons' ),
						'label_on'     => __( 'Yes', 'trx_addons' ),
						'return_value' => '1',
					]
				);

				$this->add_control(
					'next_btn',
					[
						'label'        => __( 'Show "NEXT" button', 'trx_addons' ),
						'label_block'  => false,
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_off'    => __( 'Hide', 'trx_addons' ),
						'label_on'     => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'media_from_post' => ''
						]
					]
				);

				$this->add_control(
					'prev_btn',
					[
						'label'        => __( 'Show "PREV" button', 'trx_addons' ),
						'label_block'  => false,
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_off'    => __( 'Hide', 'trx_addons' ),
						'label_on'     => __( 'Show', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'media_from_post' => ''
						]
					]
				);

				$this->add_control(
					'next_text',
					[
						'label'       => __( '"NEXT" button caption', 'trx_addons' ),
						'label_block' => false,
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( 'Next', 'trx_addons' ),
						'default'     => '',
						'condition' => [
							'media_from_post' => ''
						]
					]
				);

				$this->add_control(
					'prev_text',
					[
						'label'       => __( '"PREV" button caption', 'trx_addons' ),
						'label_block' => false,
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( 'Prev', 'trx_addons' ),
						'default'     => '',
						'condition' => [
							'media_from_post' => ''
						]
					]
				);

				$this->add_control(
					'now_text',
					[
						'label'       => __( '"Now Playing" text', 'trx_addons' ),
						'label_block' => false,
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( 'Now Playing', 'trx_addons' ),
						'default'     => '',
					]
				);

				$this->add_control(
					'track_time',
					[
						'label'        => __( 'Track time', 'trx_addons' ),
						'label_block'  => false,
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_off'    => __( 'Hide', 'trx_addons' ),
						'label_on'     => __( 'Show', 'trx_addons' ),
						'default'      => '1',
						'return_value' => '1',
					]
				);

				$this->add_control(
					'track_scroll',
					[
						'label'        => __( 'Track scroll bar', 'trx_addons' ),
						'label_block'  => false,
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_off'    => __( 'Hide', 'trx_addons' ),
						'label_on'     => __( 'Show', 'trx_addons' ),
						'default'      => '1',
						'return_value' => '1',
					]
				);

				$this->add_control(
					'track_volume',
					[
						'label'        => __( 'Track volume bar', 'trx_addons' ),
						'label_block'  => false,
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'label_off'    => __( 'Hide', 'trx_addons' ),
						'label_on'     => __( 'Show', 'trx_addons' ),
						'default'      => '1',
						'return_value' => '1',
					]
				);

				$this->add_control(
					'media',
					[
						'label'   => '',
						'type'    => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters(
							'trx_addons_sc_param_group_value', [
								[
									'url'         => '',
									'embed'       => '',
									'caption'     => __( 'Song', 'trx_addons' ),
									'author'      => __( 'Author', 'trx_addons' ),
									'description' => $this->get_default_description(),
									'cover'       => [ 'url' => '' ],
								],
							], 'trx_widget_audio'
						),
						'fields'  => apply_filters( 'trx_addons_sc_param_group_params',
								[
									[
										'name'        => 'url',
										'label'       => __( 'URL', 'trx_addons' ),
										'label_block' => false,
										'type'        => \Elementor\Controls_Manager::TEXT,
										'default'     => '',
										'placeholder' => __( '//audio.url', 'trx_addons' ),
									],
									[
										'name'        => 'embed',
										'label'       => __( 'Embed code', 'trx_addons' ),
										'label_block' => true,
										'description' => wp_kses_data( __( 'Paste HTML code to embed audio (to use it instead URL from the field above)', 'trx_addons' ) ),
										'type'        => \Elementor\Controls_Manager::TEXTAREA,
										'default'     => '',
										'rows'        => 10,
									],
									[
										'name'        => 'caption',
										'label'       => __( 'Audio caption', 'trx_addons' ),
										'label_block' => false,
										'type'        => \Elementor\Controls_Manager::TEXT,
										'placeholder' => __( 'Caption', 'trx_addons' ),
										'default'     => '',
									],
									[
										'name'        => 'author',
										'label'       => __( 'Author', 'trx_addons' ),
										'label_block' => false,
										'type'        => \Elementor\Controls_Manager::TEXT,
										'placeholder' => __( 'Author name', 'trx_addons' ),
										'default'     => '',
									],
									[
										'name'        => 'description',
										'label'       => __( 'Description', 'trx_addons' ),
										'label_block' => true,
										'description' => wp_kses_data( __( 'Short description', 'trx_addons' ) ),
										'type'        => \Elementor\Controls_Manager::TEXTAREA,
										'default'     => '',
										'rows'        => 10,
									],
									[
										'name'        => 'cover',
										'label'       => __( 'Cover image', 'trx_addons' ),
										'label_block' => true,
										'type'        => \Elementor\Controls_Manager::MEDIA,
										'default'     => [
											'url' => '',
										],
									],
								],
								'trx_widget_audio'
						),
						'condition' => [
							'media_from_post' => ''
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
				trx_addons_get_template_part(
					TRX_ADDONS_PLUGIN_WIDGETS . 'audio/tpe.audio.php',
					'trx_addons_args_widget_audio',
					array( 'element' => $this )
				);
			}
		}

		/* Register widget */
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Audio' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if ( ! function_exists( 'trx_addons_widget_audio_black_list' ) ) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_audio_black_list' );
	function trx_addons_widget_audio_black_list( $list ) {
		$list[] = 'trx_addons_widget_audio';
		return $list;
	}
}
