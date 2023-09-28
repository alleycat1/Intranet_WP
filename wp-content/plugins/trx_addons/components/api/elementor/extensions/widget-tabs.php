<?php
/**
 * Elementor extension: Improve core widget "Tabs"
 *
 * @package ThemeREX Addons
 * @since v2.18.4
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_elm_add_params_tabs_open_on_hover' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_tabs_open_on_hover', 10, 3 );
	/**
	 * Add a parameter 'Open on hover' to the Elementor tabs to add the ability to open tabs on mouse hover
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element     Elementor element
	 * @param string $section_id  Section ID
	 * @param array $args         Section arguments
	 */
	function trx_addons_elm_add_params_tabs_open_on_hover( $element, $section_id, $args ) {

		if ( ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();

		// Add 'Open on hover' to the tabs
		if ( $el_name == 'tabs' && $section_id == 'section_tabs' ) {
			$element->add_control( 'open_on_hover', array(
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label' => __("Open on hover", 'trx_addons'),
									'label_on' => __( 'On', 'trx_addons' ),
									'label_off' => __( 'Off', 'trx_addons' ),
									'return_value' => 'on',
									'render_type' => 'template',
									'prefix_class' => 'sc_tabs_open_on_hover_',
								) );
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_params_tabs_icon_position' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_tabs_icon_position', 10, 3 );
	/**
	 * Add a parameter 'Icon position' to the Elementor tabs to add the ability to change icon position in tabs
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element     Elementor element
	 * @param string $section_id  Section ID
	 * @param array $args         Section arguments
	 */
	function trx_addons_elm_add_params_tabs_icon_position( $element, $section_id, $args ) {

		if ( ! trx_addons_get_option( 'sc_tabs_layouts' ) || ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();

		// Add 'Icon position' to the tabs
		if ( $el_name == 'tabs' && $section_id == 'section_tabs' ) {
			$element->add_control( 'icon_position', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Icon position", 'trx_addons'),
									'label_block' => false,
									'options' => array(
										'left'  => __( 'Left', 'trx_addons' ),
										'top' => __( 'Top', 'trx_addons' ),
									),
									'default' => 'left',
									'prefix_class' => 'sc_tabs_icon_position_',
								) );
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_add_params_tab_template' ) ) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_tab_template', 10, 3 );
	/**
	 * Add a parameters 'Template' and 'Layout' to the Elementor tabs to add the ability
	 * to use Elementor templates and/or our custom layouts as the tabs content
	 * 
	 * @hooked elementor/element/before_section_end
	 *
	 * @param object $element     Elementor element
	 * @param string $section_id  Section ID
	 * @param array $args         Section arguments
	 */
	function trx_addons_elm_add_params_tab_template( $element, $section_id, $args ) {

		if ( ! trx_addons_get_option( 'sc_tabs_layouts' ) || ! is_object( $element ) ) {
			return;
		}
		
		$el_name = $element->get_name();
		
		// Add template selector
		if ( $el_name == 'tabs' && $section_id == 'section_tabs' ) {

			// Detect edit mode
			$is_edit_mode = trx_addons_elm_is_edit_mode();

			$control   = $element->get_controls( 'tabs' );
			$fields    = $control['fields'];
			$default   = $control['default'];
			$templates = ! $is_edit_mode ? array() : trx_addons_get_list_elementor_templates();
			$layouts   = ! $is_edit_mode ? array() : trx_addons_get_list_layouts();
			if ( count( $templates ) > 1 || count( $layouts ) > 1 ) {
				if ( ! isset( $fields['tab_content']['condition'] ) ) {
					$fields['tab_content']['condition'] = array();
				}
				$fields['tab_content']['condition']['tab_content_type!'] = array( 'layout', 'template' );
				if ( is_array( $default ) ) {
					for( $i=0; $i < count( $default ); $i++ ) {
						$default[$i]['tab_content_type'] = 'content';
						$default[$i]['tab_template'] = 0;
						$default[$i]['tab_layout'] = 0;
						$default[$i]['tab_icon'] = '';
					}
				}
				$fields['tab_title']['label_block'] = false;
				$fields['tab_title']['label'] = __( 'Title', 'trx_addons' );
				trx_addons_array_insert_before( $fields, 'tab_title', trx_addons_get_icon_param('tab_icon') );
				trx_addons_array_insert_after( $fields, 'tab_title', array(
					'tab_content_type' => array(
						'type' => \Elementor\Controls_Manager::SELECT,
						'label' => __("Content type", 'trx_addons'),
						'label_block' => false,
						'options' => array(
							'content'  => __( 'Content', 'trx_addons' ),
							'template' => __( 'Saved Template', 'trx_addons' ),
							'layout'   => __( 'Saved Layout', 'trx_addons' ),
						),
						'default' => 'content',
						'name' => 'tab_content_type'
					),					
					'tab_template' => array(
						'type' => \Elementor\Controls_Manager::SELECT,
						'label' => __("Template", 'trx_addons'),
						'label_block' => false,
						'options' => $templates,
						'default' => 0,
						'name' => 'tab_template',
						'condition' => array(
							'tab_content_type' => 'template'
						)
					),
					'tab_layout' => array(
						'type' => \Elementor\Controls_Manager::SELECT,
						'label' => __("Layout", 'trx_addons'),
						'label_block' => false,
						'options' => $layouts,
						'default' => 0,
						'name' => 'tab_layout',
						'condition' => array(
							'tab_content_type' => 'layout'
						)
					),
				) );
				$element->update_control( 'tabs', array(
								'default' => $default,
								'fields' => $fields
							) );
			}
		}
	}
}

if ( ! function_exists( 'trx_addons_elm_tab_template_add_layout' ) ) {
	add_filter( 'elementor/widget/render_content', 'trx_addons_elm_tab_template_add_layout', 10, 2 );
	/**
	 * Replace the tab's content with a selected template's or layout's content
	 * 
	 * @hooked elementor/widget/render_content
	 * 
	 * @param string $html  The tab's HTML content.
	 * @param object $element The tab's element object.
	 * 
	 * @return string  Modified tab's HTML content.
	 */
	function trx_addons_elm_tab_template_add_layout( $html, $element ) {
		if ( trx_addons_get_option( 'sc_tabs_layouts' ) > 0 && is_object( $element ) ) {
			$el_name = $element->get_name();
			if ( $el_name == 'tabs' ) {
				//$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
				$tabs = $element->get_settings( 'tabs' );
				if ( is_array( $tabs ) ) {
					foreach( $tabs as $k => $tab ) {
						$layout = '';
						if ( ! empty( $tab['tab_content_type'] ) && $tab['tab_content_type'] == 'template' && ! empty( $tab['tab_template'] ) ) {
							$layout = trx_addons_cpt_layouts_show_layout($tab['tab_template'], 0, false);
						} else if ( ! empty( $tab['tab_content_type'] ) && $tab['tab_content_type'] == 'layout' && ! empty( $tab['tab_layout'] ) ) {
							$layout = trx_addons_cpt_layouts_show_layout($tab['tab_layout'], 0, false);
						}
						if ( ! empty( $layout ) ) {
							// Old way: preg_replace is broke a layout (if price $XX is present in the layout)
							/*
							$html = preg_replace(
										'~(<div[^>]*class="elementor-tab-content[^>]*data-tab="'.($k+1).'"[^>]*>)([\s\S]*)(</div>)~U',
										'$1' . trim( $layout ) . '$3',
										$html
									);
							*/
							// New way: use preg_match and str_replace instead preg_replace
							if ( preg_match(
										'~(<div[^>]*class="elementor-tab-content[^>]*data-tab="'.($k+1).'"[^>]*>)([\s\S]*)(</div>)~U',
										$html,
										$matches
									)
							) {
								$html = str_replace( $matches[0], $matches[1] . trim( $layout ) . $matches[3], $html );
							}
						}
						if ( ! empty( $tab['tab_icon'] ) && ! trx_addons_is_off( $tab['tab_icon'] ) ) {
							$html = preg_replace(
										'~(<div[^>]*class="elementor-tab-title[^>]*data-tab="'.($k+1).'"[^>]*>[\s]*)(<a href="">)~U',
										'$1' . apply_filters('trx_addons_filter_tab_link',
													'<a href="" class="' . esc_attr( $tab['tab_icon'] ) . '">',
													$k,
													$tab
												),
										$html
									);							
						}
					}
				}
			}
		}
		return $html;
	}
}

if ( ! function_exists( 'trx_addons_elm_modify_tabs' ) ) {
	add_action( trx_addons_elementor_get_action_for_widgets_registration(), 'trx_addons_elm_modify_tabs' );
	/**
	 * Modify the tabs widget class to use our own to disable js template for the editor.
	 * Need to reload the tabs content from the server in the Elementor Editor
	 * after any settings change (for example, after change the template or layout in the tab)
	 * 
	 * @param object $widgets_manager  Elementor widgets manager object
	 */
	function trx_addons_elm_modify_tabs( $widgets_manager ) {
		if ( trx_addons_get_option('sc_tabs_layouts') > 0
			&& class_exists('\Elementor\Widget_Tabs') 
			&& ! class_exists('TRX_Addons_Elementor_Widget_Tabs') 
			&& ( method_exists( $widgets_manager, 'unregister' )
					? $widgets_manager->unregister( 'tabs' )				// Use $widgets_manager->unregister() instead
					: $widgets_manager->unregister_widget_type( 'tabs' )	// Method $widgets_manager->unregister_widget_type()
											 								// is soft deprecated since 3.5.0
				)
		) {
			class TRX_Addons_Elementor_Widget_Tabs extends \Elementor\Widget_Tabs {
				// Disable js-template - widget need reload on any parameter change
				protected function content_template() { return ''; }
			}
			// Method $widgets_manager->register_widget_type() is soft deprecated since 3.5.0
			// Use $widgets_manager->register() instead
			if ( method_exists( $widgets_manager, 'register' ) ) {
				$widgets_manager->register( new TRX_Addons_Elementor_Widget_Tabs() );
			} else {
				$widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Tabs() );
			}
		}
	}
}
