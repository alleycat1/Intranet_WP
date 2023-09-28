<?php
/**
 * ThemeREX Addons Layouts: Elementor utilities
 *
 * @package ThemeREX Addons
 * @since v1.6.41
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Set Elementor's options at once
//--------------------------------------------------------
if (!function_exists('trx_addons_cpt_layouts_elm_add_support')) {
	add_action( 'trx_addons_action_set_elementor_options', 'trx_addons_cpt_layouts_elm_add_support' );
	function trx_addons_cpt_layouts_elm_add_support() {
		// Add 'Layouts' to the Elementor's Editor support
		if (($cpt = get_option('elementor_cpt_support', false)) === false || !is_array($cpt)) {
			$cpt = ['post', 'page'];
		}
		global $TRX_ADDONS_STORAGE;
		if (is_array($TRX_ADDONS_STORAGE['cpt_list']) 
			&& !empty($TRX_ADDONS_STORAGE['cpt_list']['layouts']['post_type'])
			&& trx_addons_components_is_allowed('cpt', 'layouts')
			&& !in_array($TRX_ADDONS_STORAGE['cpt_list']['layouts']['post_type'], $cpt)
		)	$cpt[] = $TRX_ADDONS_STORAGE['cpt_list']['layouts']['post_type'];
		update_option('elementor_cpt_support', $cpt);
		// Set CSS method to 'internal' (embed CSS instead using external file)
		update_option('elementor_css_print_method', 'internal');
	}
}


// Init Elementor's support
//--------------------------------------------------------

// Add CPT 'Layouts' support to Elementor
if (!function_exists('trx_addons_cpt_layouts_elm_init')) {
	add_action( 'elementor/init', 'trx_addons_cpt_layouts_elm_init' );
	function trx_addons_cpt_layouts_elm_init() {

		// Add CPT 'Layouts' to the Elementor Editor default post_types
		add_post_type_support( TRX_ADDONS_CPT_LAYOUTS_PT, 'elementor' );
		
		// Add a custom category for ThemeREX Layouts shortcodes
		trx_addons_cpt_layouts_elm_add_categories();

		// Template to create our classes with widgets
		if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "layouts/elementor/class.widget.php")) != '') { 
			include_once $fdir;
		}
	}
}

// Set apropriate document type on post creation
if (!function_exists('trx_addons_cpt_layouts_elm_set_document_type')) {
	add_action( 'wp_insert_post', 'trx_addons_cpt_layouts_elm_set_document_type', 10, 2 );
	function trx_addons_cpt_layouts_elm_set_document_type( $post_id, $post ) {
		$post_type = ! empty( $post->post_type ) ? $post->post_type : '';
		if ( $post_type !== TRX_ADDONS_CPT_LAYOUTS_PT ) {
			return;
		}
		// Set apropriate document type
		$documents = Elementor\Plugin::instance()->documents;
		$doc_type  = $documents->get_document_type( $post_type );
		update_post_meta( $post_id, $doc_type::TYPE_META_KEY, $post_type );
		// Set default layout type
		if ( get_post_meta( $post_id, 'trx_addons_layout_type', true ) == '' ) {
			update_post_meta( $post_id, 'trx_addons_layout_type', 'custom' );
		}
	}
}

// Register apropriate document type for 'Layouts' post type
if (!function_exists('trx_addons_cpt_layouts_elm_register_document_type')) {
	add_action( 'elementor/documents/register', 'trx_addons_cpt_layouts_elm_register_document_type' );
	function trx_addons_cpt_layouts_elm_register_document_type( $documents_manager ) {
		if ( ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "layouts/elementor/class.document.php")) != '') { 
			include_once $fdir;
		}
		$documents_manager->register_document_type( TRX_ADDONS_CPT_LAYOUTS_PT, 'TRX_Addons_Elementor_Layouts_Document' );
	}
}

// Set apropriate document template for 'Layouts' post type
if (!function_exists('trx_addons_cpt_layouts_elm_set_post_type_template')) {
	add_action( 'template_include', 'trx_addons_cpt_layouts_elm_set_post_type_template', 9999 );
	function trx_addons_cpt_layouts_elm_set_post_type_template( $template ) {
		if ( trx_addons_is_singular( TRX_ADDONS_CPT_LAYOUTS_PT )
			|| ( apply_filters( 'trx_addons_filter_replace_elementor_template', true )
					&& class_exists( '\Elementor\TemplateLibrary\Source_Local' )
					&& trx_addons_is_singular( \Elementor\TemplateLibrary\Source_Local::CPT )
					&& in_array( get_post_meta( get_the_ID(), '_elementor_template_type', true ), array( 'section' ) )
			)
		) {
			$template = trx_addons_elm_is_preview()
							? trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "layouts/elementor/tpl.editor.php")
							: trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . "layouts/elementor/tpl.single.php");
		}
		return $template;
	}
}

// Return a list of bofy classes for the layouts in the preview mode and in the editor mode
if (!function_exists('trx_addons_cpt_layouts_elm_get_body_classes')) {
	function trx_addons_cpt_layouts_elm_get_body_classes( $id = 0, $echo = false ) {
		if ( empty( $id ) ) {
			$id = get_the_ID();
		}
		$body_classes = get_body_class();
		$layout_type = get_post_meta( $id, 'trx_addons_layout_type', true );
		if ( is_array( $body_classes ) ) {
			$body_classes = implode( ' ', $body_classes );
		}
		$body_classes .= ' ' . esc_attr( TRX_ADDONS_CPT_LAYOUTS_PT ) . '-type-' . esc_attr( $layout_type );
		if ( in_array( $layout_type, array( 'header', 'footer' ) ) ) {
			$body_classes = str_replace( 'body_style_wide', '', $body_classes );
			$body_classes = str_replace( 'body_style_boxed', '', $body_classes );
		}
		if ( $echo ) {
			echo 'class="' . esc_attr( $body_classes ) . '"';
		}
		return $body_classes;
	}
}

// Add category for Layouts
if (!function_exists('trx_addons_cpt_layouts_elm_add_categories')) {
	add_action( 'elementor/elements/categories_registered', 'trx_addons_cpt_layouts_elm_add_categories' );
	function trx_addons_cpt_layouts_elm_add_categories($mgr = null) {

		static $added = false;

		if (!$added) {

			if ($mgr == null) $mgr = \Elementor\Plugin::instance()->elements_manager;
			
			// Add a custom category for ThemeREX Layouts shortcodes
			$mgr->add_category( 
				'trx_addons-layouts',
				array(
					'title' => __( 'ThemeREX Addons Layouts', 'trx_addons' ),
					'icon' => '	eicon-inner-section', //default icon
					'active' => false,
				)
			);

			$added = true;
		}
	}
}


// Add standard elements params in the new controls section: 'Custom Layouts'
if (!function_exists('trx_addons_cpt_layouts_elm_add_params_in_standard_elements')) {
	add_action( 'elementor/element/after_section_end', 'trx_addons_cpt_layouts_elm_add_params_in_standard_elements', 10, 3 );
	function trx_addons_cpt_layouts_elm_add_params_in_standard_elements($element, $section_id, $args) {

		if ( !is_object($element) ) return;
		
		if ( in_array($element->get_name(), array('section')) && $section_id == 'section_layout' ) {
			
			// Detect edit mode
			$is_edit_mode = trx_addons_elm_is_edit_mode();

			// Register controls
			$element->start_controls_section( 'section_custom_layout',	array(
																		'label' => __( 'Custom Layout', 'trx_addons' ),
																		'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
																	) );
			
			$tmp = ! $is_edit_mode ? array() : trx_addons_get_list_sc_layouts_row_types();
			$row_types = array();
			foreach ($tmp as $k=>$v) {
				$row_types[$k=='inherit' ? '' : 'row sc_layouts_row_type_'.esc_attr($k)] = $v;
			}

			$element->add_control( 'row_type', array(
									'label' => __("Row type", 'trx_addons'),
									'label_block' => false,
									'description' => wp_kses_data( __("Select row type to decorate header widgets. Attention! Use this parameter to decorate custom layouts only!", 'trx_addons') ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => $row_types,
									'default' => '',
									'prefix_class' => 'sc_layouts_'
									) );

			$element->add_control( 'row_delimiter', array(
									'label' => __( 'Delimiter', 'trx_addons' ),
									'label_block' => false,
									'label_off' => __( 'Hide', 'trx_addons' ),
									'label_on' => __( 'Show', 'trx_addons' ),
									'description' => wp_kses_data( __("Show delimiter after this row", 'trx_addons') ),
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'return_value' => 'delimiter',
									'prefix_class' => 'sc_layouts_row_',
									) );

			$element->add_control( 'row_fixed', array(
									'label' => __("Fix this row when scroll", 'trx_addons'),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => array(
										"" => esc_html__("Don't fix", 'trx_addons'),
										"fixed" => esc_html__("Fix on large screen ", 'trx_addons'),
										"fixed sc_layouts_row_fixed_always" => esc_html__("Fix always", 'trx_addons')
									),
									'default' => '',
									'prefix_class' => 'sc_layouts_row_'
									) );

			$element->add_control( 'row_fixed_delay', array(
									'label' => __("Delay before fix this row", 'trx_addons'),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'condition' => array(
										'row_fixed!' => ''
									),
									'default' => '',
									'return_value' => 'delay_fixed',
									'prefix_class' => 'sc_layouts_row_'
									) );

			$element->add_control( 'row_hide_unfixed', array(
									'label' => __("Hide this row on unfix", 'trx_addons'),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label_off' => __( 'Show', 'trx_addons' ),
									'label_on' => __( 'Hide', 'trx_addons' ),
									'condition' => array(
										'row_fixed!' => ''
									),
									'default' => '',
									'return_value' => 'hide_unfixed',
									'prefix_class' => 'sc_layouts_row_'
									) );

			$element->add_control( 'hide_on_frontpage', array(
									'label' => __( 'Hide on Frontpage', 'trx_addons' ),
									'label_block' => false,
									'label_off' => __( 'Show', 'trx_addons' ),
									'label_on' => __( 'Hide', 'trx_addons' ),
									'description' => wp_kses_data( __("Hide this row on Frontpage", 'trx_addons') ),
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'return_value' => 'hide_on_frontpage',
									'prefix_class' => 'sc_layouts_',
									) );

			$element->add_control( 'hide_on_singular', array(
									'label' => __( 'Hide on single posts and pages', 'trx_addons' ),
									'label_block' => false,
									'label_off' => __( 'Show', 'trx_addons' ),
									'label_on' => __( 'Hide', 'trx_addons' ),
									'description' => wp_kses_data( __("Hide this row on single posts and pages", 'trx_addons') ),
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'return_value' => 'hide_on_singular',
									'prefix_class' => 'sc_layouts_',
									) );

			$element->add_control( 'hide_on_other', array(
									'label' => __( 'Hide on other pages', 'trx_addons' ),
									'label_block' => false,
									'label_off' => __( 'Show', 'trx_addons' ),
									'label_on' => __( 'Hide', 'trx_addons' ),
									'description' => wp_kses_data( __("Hide this row on other pages (posts archive, category or taxonomy posts, author's posts, etc.)", 'trx_addons') ),
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'return_value' => 'hide_on_other',
									'prefix_class' => 'sc_layouts_',
									) );

			$element->end_controls_section();
		}
	}
}



// Add standard elements params in the existing controls sections
if (!function_exists('trx_addons_cpt_layouts_elm_append_params_in_standard_elements')) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_cpt_layouts_elm_append_params_in_standard_elements', 10, 3 );
	function trx_addons_cpt_layouts_elm_append_params_in_standard_elements($element, $section_id, $args) {

		if ( !is_object($element) ) return;
		
		if ( in_array($element->get_name(), array('column')) && $section_id == 'layout' ) {
			// Detect edit mode
			$is_edit_mode = trx_addons_elm_is_edit_mode();
			// Register controls
			$tmp = ! $is_edit_mode ? array() : trx_addons_get_list_sc_aligns(true, false);
			$col_aligns = array();
			foreach ($tmp as $k=>$v) {
				$col_aligns[$k=='inherit' ? '' : esc_attr($k) . ' sc_layouts_column'] = $v;
			}

			$element->add_responsive_control( 'column_align', array(
									'label' => __("Text alignment", 'trx_addons'),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => $col_aligns,
									'default' => '',
									'prefix_class' => 'sc%s_layouts_column_align_'
									) );

			$element->add_control( 'icons_position', array(
									'label' => __("Icons position", 'trx_addons'),
									'label_block' => false,
									'description' => wp_kses_data( __("Select icons position of the inner widgets 'Layouts: xxx' in this column. Attention! Use this parameter to decorate custom layouts only!", 'trx_addons') ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => ! $is_edit_mode ? array() : trx_addons_get_list_sc_layouts_icons_positions(),
									'default' => 'left',
									'prefix_class' => 'sc_layouts_column_icons_position_'
									) );
		}
	}
}



// PRE-RENDER PROCESSING
// ------------------------------------------------------------------------

// Add 'sc_layouts_item' to the classes list of widgets
if ( !function_exists( 'trx_addons_cpt_layouts_elm_sc_wrap' ) ) {
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_cpt_layouts_elm_sc_wrap', 10, 1 );
	function trx_addons_cpt_layouts_elm_sc_wrap($widget) {
		if ( ( trx_addons_sc_stack_check('show_layout')						// Wrap shortcodes in the headers and footers
				|| trx_addons_is_singular( TRX_ADDONS_CPT_LAYOUTS_PT )		// or if it's a preview mode for layout
			)
			&& ! trx_addons_sc_stack_check('trx_sc_layouts') 				// Don't wrap shortcodes inside content
		) {
			$widget->add_render_attribute( '_wrapper', 'class', 'sc_layouts_item' );
		}
	}
}



// AFTER-RENDER PROCESSING
// ------------------------------------------------------------------------

// Remove empty and inherit classes 'sc_layouts_row' from the 'inherit' rows
if (!function_exists('trx_addons_cpt_layouts_elm_remove_inherit_classes')) {
	add_filter( 'elementor/frontend/the_content', 'trx_addons_cpt_layouts_elm_remove_inherit_classes' );
	function trx_addons_cpt_layouts_elm_remove_inherit_classes($content) {
		return str_replace(
					array(
						'sc_layouts_column_ ',
						'sc_layouts_row_ ',
						'sc_layouts_ ',
					),
					'',
					$content);
	}
}



// Generate content to show layout
//------------------------------------------------------------------------
if ( !function_exists( 'trx_addons_cpt_layouts_elm_layout_content' ) ) {
	add_filter( 'trx_addons_filter_sc_layout_content', 'trx_addons_cpt_layouts_elm_layout_content', 11, 3 );
	function trx_addons_cpt_layouts_elm_layout_content($content, $post_id = 0, $force_styles = false) {
		static $styles_included = array();
		$allow_recursive_layouts = trx_addons_get_setting( 'allow_recursive_layouts', false );
		// Check if this post built with Elementor
		if ( trx_addons_exists_elementor() ) {
			if ( $post_id == 0 ) $post_id = trx_addons_get_the_ID();
			$cur_page_built_with_elementor = trx_addons_is_built_with_elementor( $post_id );
			if ( $cur_page_built_with_elementor ) {
				// If a caller is a show_layout
				if ( trx_addons_sc_stack_check('show_layout') ) {
					global $TRX_ADDONS_STORAGE;

					if ( ! $allow_recursive_layouts ) {
						// A global array with ids of inner layouts to generate its after a main layout is generated
						if ( ! isset( $TRX_ADDONS_STORAGE['elementor_recursive_layouts'] ) ) {
							$TRX_ADDONS_STORAGE['elementor_recursive_layouts'] = array();
						}
						$elementor_recursive_layouts_pointer = count( $TRX_ADDONS_STORAGE['elementor_recursive_layouts'] );
					}

					// Check if we not have a recursion call
					if ( $allow_recursive_layouts || ! trx_addons_sc_stack_check('show_layout_elementor') ) {

						trx_addons_sc_stack_push('show_layout_elementor');

						// Add inline css to the output
						$inline_css = $force_styles
										|| ( empty( $TRX_ADDONS_STORAGE['cur_page_built_with_elementor'] )
											&& empty( $styles_included[ $post_id ] )
											&& ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, '3.0.0', '<' ) )
											);

						// Attention! Recommended method get_builder_content_for_display() is damage sliders with custom layouts inside
						// because $post_id is equal to get_the_ID() - an empty string is returned
						//$post_content = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $post_id, $inline_css );

						// Use get_builder_content() instead (is an internal method, but it is not check post ID)
						// and turn off edit mode before build content and restore it after ( not need for get_builder_content_for_display() )

						// Set edit mode as false, so don't render settings and etc. ( not need for get_builder_content_for_display() )
						$is_edit_mode = \Elementor\Plugin::instance()->editor->is_edit_mode();
						\Elementor\Plugin::instance()->editor->set_edit_mode( false );

						$post_content = \Elementor\Plugin::instance()->frontend->get_builder_content( $post_id, $inline_css ); // || $is_edit_mode

						// Restore edit mode ( not need for get_builder_content_for_display() )
						\Elementor\Plugin::instance()->editor->set_edit_mode( $is_edit_mode );
						
						trx_addons_sc_stack_pop();

						$styles_included[$post_id] = $inline_css;	// true

						if ( ! empty($post_content) ) {
							$content = apply_filters( 'trx_addons_filter_sc_layout_content_from_builder', $post_content, $post_id, 'elementor' );
						}

						// Check if a last call contains a recursive calls
						if ( ! $allow_recursive_layouts ) {
							if ( count( $TRX_ADDONS_STORAGE['elementor_recursive_layouts'] ) > $elementor_recursive_layouts_pointer ) {
								for ( $i = $elementor_recursive_layouts_pointer; $i < count( $TRX_ADDONS_STORAGE['elementor_recursive_layouts'] ); $i++ ) {
									$inner_post_id = $TRX_ADDONS_STORAGE['elementor_recursive_layouts'][ $i ];
									// To prevent an output be wrapped with sc_layouts_item
									trx_addons_sc_stack_push('trx_sc_layouts');
									// Generate an inner layout
									$inner_content = trx_addons_cpt_layouts_show_layout( $inner_post_id, 0, false );
									// Cancel prevention
									trx_addons_sc_stack_pop();
									// Replace a placeholder in a main layout with a generated content
									$content = str_replace(
													sprintf( '{{ELEMENTOR-LAYOUT-%d}}', $inner_post_id ),
													$inner_content,
													$content
												);
									// Replace a placeholder in an inline html (panels and popups) with a generated content
									trx_addons_set_inline_html( str_replace(
																	sprintf( '{{ELEMENTOR-LAYOUT-%d}}', $inner_post_id ),
																	$inner_content,
																	trx_addons_get_inline_html()
									) );
								}
								array_splice( $TRX_ADDONS_STORAGE['elementor_recursive_layouts'], $elementor_recursive_layouts_pointer );
							}
						}
					} else {
						$content = sprintf( '{{ELEMENTOR-LAYOUT-%d}}', $post_id );
						array_push( $TRX_ADDONS_STORAGE['elementor_recursive_layouts'], $post_id );
					}

				// Else - a caller is a content filter - just change a previously generated content
				} else {
					$content .= ' ';
				}
			}
		}
		return $content;
	}
}

// Check if specified post built with Elementor
if ( !function_exists( 'trx_addons_cpt_layouts_elm_post_built_in' ) ) {
	add_filter( 'trx_addons_filter_post_built_in', 'trx_addons_cpt_layouts_elm_post_built_in', 10, 2 );
	function trx_addons_cpt_layouts_elm_post_built_in($builder, $post_id=0) {
		if ( $post_id == 0 ) {
			$post_id = get_the_ID();
		}
		if ( $post_id > 0 && trx_addons_is_built_with_elementor( $post_id ) ) {
			$builder = 'elementor';
		}
		return $builder;
	}
}


// Load required styles and scripts for the frontend
//-----------------------------------------------------------------
if ( !function_exists( 'trx_addons_cpt_layouts_elm_load_styles_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_cpt_layouts_elm_load_styles_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_cpt_layouts_elm_load_styles_front() {
		if ( trx_addons_exists_elementor() ) {
			global $TRX_ADDONS_STORAGE;
			$TRX_ADDONS_STORAGE['cur_page_built_with_elementor'] = trx_addons_is_singular() && trx_addons_is_built_with_elementor( get_the_ID() );
			$TRX_ADDONS_STORAGE['force_load_elementor_styles'] = apply_filters('trx_addons_filter_force_load_elementor_styles', false );
			if ( !empty($TRX_ADDONS_STORAGE['force_load_elementor_styles']) ) {
				\Elementor\Plugin::instance()->frontend->enqueue_styles();
				\Elementor\Plugin::instance()->frontend->print_fonts_links();
			}
		}
	}
}
if ( !function_exists( 'trx_addons_cpt_layouts_elm_load_scripts_front' ) ) {
	add_action("wp_footer", 'trx_addons_cpt_layouts_elm_load_scripts_front');
	function trx_addons_cpt_layouts_elm_load_scripts_front() {
		if ( trx_addons_exists_elementor() ) {
			global $TRX_ADDONS_STORAGE;
			if ( !empty($TRX_ADDONS_STORAGE['force_load_elementor_styles']) ) {
				\Elementor\Plugin::instance()->frontend->enqueue_scripts();
			}
		}
	}
}


// Create layouts
//------------------------------------------------------------------------

// Replace post_id in CSS styles
if ( ! function_exists( 'trx_addons_cpt_layouts_elm_create_layout_post_meta' ) ) {
	add_filter( 'trx_addons_filter_create_layout_post_meta', 'trx_addons_cpt_layouts_elm_create_layout_post_meta', 10, 4 );
	function trx_addons_cpt_layouts_elm_create_layout_post_meta( $meta_val, $meta_key, $layout_slug, $new_id ) {
		if ( $meta_key == '_elementor_css' && ! empty( $new_id ) && ! empty( $layout_slug ) ) {
			$parts  = explode( '_', $layout_slug );
			$type   = $parts[0];
			$old_id = ! empty( $parts[1] ) ? (int) $parts[1] : 0;
			if ( $old_id > 0 && $old_id != $new_id ) {
				$meta_val = trx_addons_str_replace( $old_id, $new_id, $meta_val );
			}
		}
		return $meta_val;
	}
}


// One-click import support
//------------------------------------------------------------------------

// Export custom layouts
if ( !function_exists( 'trx_addons_cpt_layouts_elm_export_meta' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_cpt_layouts_export_meta', 'trx_addons_cpt_layouts_elm_export_meta', 10, 2 );
	function trx_addons_cpt_layouts_elm_export_meta($meta, $post) {
		$tpl = get_post_meta( $post->ID, '_wp_page_template', true );
		if (!empty($tpl)) $meta['_wp_page_template'] = $tpl;
		$data = get_post_meta( $post->ID, '_elementor_data', true );
		if (!empty($data)) $meta['_elementor_data'] = $data;
		$mode = get_post_meta( $post->ID, '_elementor_edit_mode', true );
		if (!empty($mode)) $meta['_elementor_edit_mode'] = $mode;
		$css = get_post_meta( $post->ID, '_elementor_css', true );
		if (!empty($css)) $meta['_elementor_css'] = serialize($css);
		$ver = get_post_meta( $post->ID, '_elementor_version', true );
		if (!empty($ver)) $meta['_elementor_version'] = $ver;
		return $meta;
	}
}
