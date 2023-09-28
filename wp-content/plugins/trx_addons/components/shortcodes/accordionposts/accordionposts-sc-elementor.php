<?php
/**
 * Shortcode: Accordion posts (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }



// Elementor Widget
//------------------------------------------------------

if (!function_exists('trx_addons_sc_accordionposts_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_accordionposts_add_in_elementor' );
	function trx_addons_sc_accordionposts_add_in_elementor() {

		if (!class_exists('TRX_Addons_Elementor_Widget')) return;

		class TRX_Addons_Elementor_Widget_Accordionposts extends TRX_Addons_Elementor_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @param array			$data Widget data. Default is an empty array.
			 * @param array|null	$args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params([
					'height' => 'size+unit'
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
				return 'trx_sc_accordionposts';
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
				return __( 'Accordion of posts', 'trx_addons' );
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
				return 'eicon-call-to-action';
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

				// If open params in Elementor Editor
				$params = $this->get_sc_params();

				// Prepare list of pages
				$posts = ! $is_edit_mode ? array() : trx_addons_get_list_posts(false, array(
													'post_type' => 'page',
													'not_selected' => false
													)
												);
				$default = trx_addons_array_get_first($posts);
				$post = !empty($params['post_id']) ? $params['post_id'] : $default;

				// Prepare list of layouts
				$layouts = ! $is_edit_mode ? array() : trx_addons_get_list_layouts();
				$default = trx_addons_array_get_first($layouts);
				$layout  = ! empty($params['layout_id']) ? $params['layout_id'] : $default;

				unset($posts[ get_the_ID() ]); // To avoid recursion, we prevent adding $current page in accordion

				$this->start_controls_section(
					'section_sc_accordionposts',
					[
						'label' => __( 'Accordion posts', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'accordionposts'), 'trx_sc_accordionposts'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'accordions',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'title' => esc_html__( 'First accordions post', 'trx_addons' ),
								'subtitle' => esc_html__( 'Network', 'trx_addons' ),
								'post_id' => '0',
								'advanced_rolled_content' => 0,
								'icon' => trx_addons_elementor_set_settings_icon( 'icon-users-group' ),
								'color' => '#ffffff',
								'bg_color' => '#aa0000',
							],
							[
								'title' => esc_html__( 'Second accordion post', 'trx_addons' ),
								'subtitle' => esc_html__( 'Study', 'trx_addons' ),
								'post_id' => '0',
								'advanced_rolled_content' => 0,
								'icon' => trx_addons_elementor_set_settings_icon( 'icon-graduation-light' ),
								'color' => '#ffffff',
								'bg_color' => '#00aa00',
							],
							[
								'title' => esc_html__( 'Third accordion post', 'trx_addons' ),
								'subtitle' => esc_html__( 'Time', 'trx_addons' ),
								'post_id' => '0',
								'advanced_rolled_content' => 0,
								'icon' => trx_addons_elementor_set_settings_icon( 'icon-clock-empty' ),
								'color' => '#ffffff',
								'bg_color' => '#0000aa',
							]
						], 'trx_sc_accordionposts'),
						'fields' => apply_filters('trx_addons_sc_param_group_params',
							[
								[
									'name' => 'title',
									'label' => __( 'Title', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Item's title", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'subtitle',
									'label' => __( 'Subtitle', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => $this->get_default_subtitle(),
									'default' =>  __( 'Description', 'trx_addons' )
								],
								[
									'name' => 'icon',
									'type' => 'trx_icons',
									'label' => __( 'Icon', 'trx_addons' ),
									'label_block' => false,
									'default' => '',
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_icons( trx_addons_get_setting('icons_type')),
									'style' =>  trx_addons_get_setting('icons_type')
								],
								[
									'name' => 'color',
									'label' => __( 'Icon Color', 'trx_addons' ),
									'description' => wp_kses_data( __("Selected color will also be applied to the subtitle. ", 'trx_addons')),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
								],
								[
									'name' => 'bg_color',
									'label' => __( 'Icon Background Color', 'trx_addons' ),
									'description' => wp_kses_data( __("Selected color will also be applied to the subtitle. ", 'trx_addons')),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
//									'global' => array(
//										'active' => false,
//									),
								],
								[
									'name' => 'content_source',
									'label' => __( 'Select content source', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' =>  [
										'text' => __( 'Use content', 'trx_addons' ),
										'page' => __( 'Pages', 'trx_addons' ),
										'layout' => __( 'Layouts', 'trx_addons' ),
									],
									'default' => 'text'
								],
								[
									'name' => 'post_id',
									'label' => __( 'Page ID', 'trx_addons' ),
									'description' => wp_kses_data( __("'Use Content' option allows you to create custom content for the selected content area, otherwise you will be prompted to choose an existing page to import content from it. ", 'trx_addons')
										. '<br>'
										. sprintf('<a href="%1$s" class="trx_addons_post_editor" target="_blank">%2$s</a>',
											admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $post ) ),
											__("Open selected layout in a new tab to edit", 'trx_addons')
										)
									),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => $posts,
									'default' => $post,
									'condition' => ['content_source' => 'page']
								],
								[
									'name' => 'layout_id',
									'label' => __( 'Layout ID', 'trx_addons' ),
									'description' => wp_kses_data( __("'Use Content' option allows you to create custom content for the selected content area, otherwise you will be prompted to choose an existing page to import content from it. ", 'trx_addons')
										. '<br>'
										. sprintf('<a href="%1$s" class="trx_addons_post_editor" target="_blank">%2$s</a>',
											admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
											__("Open selected layout in a new tab to edit", 'trx_addons')
										)
									),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => $layouts,
									'default' => $layout,
									'condition' => ['content_source' => 'layout']
								],
								[
									'name' => 'inner_content',
									'label' => __( 'Inner content', 'trx_addons' ),
									'default' => '',
									'placeholder' =>  '',
									'type' => \Elementor\Controls_Manager::WYSIWYG,
									'show_label' => true,
									'condition' => ['content_source' => 'text']
								],
								[
									"name" => "advanced_rolled_content",
									'label' => __( 'Advanced Header Options', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label_off' => __( 'Off', 'trx_addons' ),
									'label_on' => __( 'On', 'trx_addons' ),
									'default' => '',
									'return_value' => '1'
								],
								[
									'name' => 'rolled_content',
									'label' => '',
									'default' => '',
									'placeholder' =>  $this->get_default_subtitle(),
									'type' => \Elementor\Controls_Manager::WYSIWYG,
									'show_label' => true,
									'condition' => ['advanced_rolled_content' => '1']
								],
							],
							'trx_sc_accordionposts'
						),
						'title_field' => '{{{ title }}}',
					]
				);

				$this->end_controls_section();
			}

		}

		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Accordionposts' );
	}
}
