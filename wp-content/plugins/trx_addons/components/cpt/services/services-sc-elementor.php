<?php
/**
 * ThemeREX Addons Custom post type: Services (Elementor support)
 *
 * @package ThemeREX Addons
 * @since v1.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_services_add_in_elementor')) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_sc_services_add_in_elementor' );
	function trx_addons_sc_services_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Services extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_services';
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
				return __( 'Services', 'trx_addons' );
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
				return 'eicon-info-box';
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
				// If open params in Elementor Editor
				$params = $this->get_sc_params();
				// Prepare lists
				$post_type = !empty($params['post_type']) ? $params['post_type'] : TRX_ADDONS_CPT_SERVICES_PT;
				$taxonomy = !empty($params['taxonomy']) ? $params['taxonomy'] : TRX_ADDONS_CPT_SERVICES_TAXONOMY;
				$tax_obj = get_taxonomy($taxonomy);
				
				$this->start_controls_section(
					'section_sc_services',
					[
						'label' => __( 'Services', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'services', 'sc'), 'trx_sc_services'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'featured',
					[
						'label' => __( 'Featured', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_services_featured(),
						'default' => 'image',
						'condition' => [
							'type' => ['default', 'callouts', 'hover', 'light', 'list', 'iconed', 'tabs', 'tabs_simple', 'timeline', 'panel']
						]
					]
				);

				$this->add_control(
					'featured_position',
					[
						'label' => __( 'Featured position', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Attention! Use 'Bottom' only with 'Callouts' or 'Timeline'", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_services_featured_positions(),
						'default' => 'top',
						'condition' => [
							'featured' => ['image', 'icon', 'number', 'pictogram']
						]
					]
				);

				$this->add_control(
					'thumb_size',
					[
						'type' => \Elementor\Controls_Manager::SELECT,
						'label' => __( 'Image size', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Leave 'Default' to use default size defined in the shortcode template or any registered size to override thumbnail size with the selected value.", 'trx_addons') ),
						'options' => ! $is_edit_mode ? array() : array_merge( array( '' => __( 'Default', 'trx_addons' ) ), trx_addons_get_list_thumbnail_sizes() ),
						'default' => '',
						'condition' => [
							'featured' => [ 'image' ],
						]
					]
				);

				$this->add_control(
					'pagination',
					[
						'label' => __( 'Pagination', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Add pagination links after posts. Attention! Pagination is not allowed if the slider layout is used.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_paginations(),
						'default' => 'none',
						'condition' => [
							'type!' => ['panel', 'tabs', 'tabs_simple']
						]
					]
				);

				$this->add_control(
					'post_type',
					[
						'label' => __( 'Post type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_posts_types(),
						'default' => TRX_ADDONS_CPT_SERVICES_PT
					]
				);

				$this->add_control(
					'taxonomy',
					[
						'label' => __( 'Taxonomy', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_taxonomies(false, $post_type),
						'default' => TRX_ADDONS_CPT_SERVICES_TAXONOMY
					]
				);

				$this->add_control(
					'cat',
					[
						'label' => __( 'Group', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode
										? array()
											// Make keys as string (add a space after the number) to preserve the order in the list
											// (otherwise the keys will be converted to numbers in the JS and the order will be broken)
										: trx_addons_array_make_string_keys(
												trx_addons_array_merge(
													array( 0 => trx_addons_get_not_selected_text( ! empty( $tax_obj->label ) ? $tax_obj->label : __( '- Not Selected -', 'trx_addons' ) ) ),
													$taxonomy == 'category' 
														? trx_addons_get_list_categories() 
														: trx_addons_get_list_terms(false, $taxonomy)
												)
											),
						'default' => '0'
					]
				);

				$this->add_query_param('', [
						'columns' => [ 
									'condition' => [
										'type' => ['default','callouts','light','list','iconed','hover','chess','timeline','panel']
									]
						]
					]);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_services_details',
					[
						'label' => __( 'Details', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
					]
				);

				$this->add_control(
					'tabs_effect',
					[
						'label' => __( 'Tabs change effect', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_services_tabs_effects(),
						'default' => 'fade',
						'condition' => [
							'type' => 'tabs'
						]
					]
				);

				$this->add_control(
					'hide_excerpt',
					[
						'label' => __( 'Excerpt', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Show', 'trx_addons' ),
						'label_on' => __( 'Hide', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'no_margin',
					[
						'label' => __( 'Remove margin', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'icons_animation',
					[
						'label' => __( 'Icons animation', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Attention! Animation is enabled only if there is an .SVG  icon in your theme with the same name as the selected icon.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'hide_bg_image',
					[
						'label' => __( 'Hide bg image', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Toggle to hide the background image on the front item.", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'popup',
					[
						'label' => __( 'Open in the popup', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Open details in the popup or navigate to the single post (default)", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'no_links',
					[
						'label' => __( 'Disable links', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'more_text',
					[
						'label' => __( "'More' text", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => esc_html__('Read more', 'trx_addons'),
						'condition' => [
							'no_links' => ''
						]
					]
				);

				$this->end_controls_section();
				
				$this->add_slider_param(false, [
					'slider' => [
								'condition' => [
									'type' => ['default','callouts','light','list','iconed','hover','chess','timeline']
								]
					]
				]);
				
				$this->add_title_param();
			}
		}
		
		// Register widget
		trx_addons_elm_register_widget( 'TRX_Addons_Elementor_Widget_Services' );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_sc_services_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_sc_services_black_list' );
	function trx_addons_sc_services_black_list($list) {
		$list[] = 'TRX_Addons_SOW_Widget_Services';
		return $list;
	}
}
