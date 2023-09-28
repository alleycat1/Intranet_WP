<?php
/**
 * ThemeREX Addons Custom post type: Testimonials (Elementor support)
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
if (!function_exists('trx_addons_sc_testimonials_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_testimonials_add_in_elementor' );
	function trx_addons_sc_testimonials_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Testimonials extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_testimonials';
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
				return __( 'Testimonials', 'trx_addons' );
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
				return 'eicon-testimonial';
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
				return ['trx_addons-cpt'];
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
					'section_sc_testimonials',
					[
						'label' => __( 'Testimonials', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'testimonials', 'sc'), 'trx_sc_testimonials'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'rating',
					[
						'label' => __( 'Show rating', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'No', 'trx_addons' ),
						'label_on' => __( 'Yes', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'use_initials',
					[
						'label' => __( 'Use initials', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'If no avatar is present, the initials derived from the available username will be used.', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'No', 'trx_addons' ),
						'label_on' => __( 'Yes', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'type' => ['default']
						]
					]
				);

				$this->add_control(
					'post_type',
					[
						'type' => \Elementor\Controls_Manager::HIDDEN,
						'render_type' => 'none',
						'default' => TRX_ADDONS_CPT_TESTIMONIALS_PT
					]
				);
				$this->add_control(
					'taxonomy',
					[
						'type' => \Elementor\Controls_Manager::HIDDEN,
						'render_type' => 'none',
						'default' => TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY
					]
				);

				$this->add_control(
					'cat',
					[
						'label' => __( 'Group', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_array_merge( array( 0 => trx_addons_get_not_selected_text( esc_html__( 'Select category', 'trx_addons' ) ) ), trx_addons_get_list_terms( false, TRX_ADDONS_CPT_TESTIMONIALS_TAXONOMY ) ),
						'default' => '0'
					]
				);
				
				$this->add_query_param('', array(), TRX_ADDONS_CPT_TESTIMONIALS_PT);

				$this->end_controls_section();
				
				$this->add_slider_param(false, [
					'slider_pagination_thumbs' => [
						'label' => __( 'Pagination thumbs', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'slider_pagination' => ['left', 'right', 'bottom', 'bottom_outside']
						]
					]
				]);
				
				$this->add_title_param();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Testimonials' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_sc_testimonials_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_sc_testimonials_black_list' );
	function trx_addons_sc_testimonials_black_list($list) {
		$list[] = 'TRX_Addons_SOW_Widget_Testimonials';
		return $list;
	}
}
