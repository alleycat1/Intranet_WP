<?php
/**
 * Shortcode: Display Search form (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.6.08
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_layouts_search_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_layouts_search_add_in_elementor' );
	function trx_addons_sc_layouts_search_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Layouts_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Layouts_Search extends TRX_Addons_Elementor_Layouts_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_layouts_search';
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
				return __( 'Layouts: Search', 'trx_addons' );
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
				return 'eicon-search';
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
				return ['trx_addons-layouts'];
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
				$post_types = ! $is_edit_mode ? array() : trx_addons_get_list_posts_types();

				$this->start_controls_section(
					'section_sc_layouts_search',
					[
						'label' => __( 'Layouts: Search', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons'),
							), 'trx_sc_layouts_search'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'style',
					[
						'label' => __( 'Style', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : apply_filters('trx_addons_sc_style', trx_addons_get_list_sc_layouts_search(), 'trx_sc_layouts_search'),
						'default' => 'normal'
					]
				);

				$this->add_control(
					'ajax',
					[
						'label' => __( 'AJAX search', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Use incremental AJAX search", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'post_types',
					[
						'label' => __( 'Search in post types', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => $post_types,
						'multiple' => true,
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . "search/tpe.search.php",
										'trx_addons_args_sc_layouts_search',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Layouts_Search' );
	}
}
