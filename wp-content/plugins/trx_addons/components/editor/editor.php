<?php
/**
 * Add buttons in the TinyMCE
 *
 * @package ThemeREX Addons
 * @since v1.1
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Define component's subfolder
if ( !defined('TRX_ADDONS_PLUGIN_EDITOR') ) define('TRX_ADDONS_PLUGIN_EDITOR', TRX_ADDONS_PLUGIN_COMPONENTS . 'editor/');

// Add component to the global list
if (!function_exists('trx_addons_editor_add_to_components')) {
	add_filter( 'trx_addons_components_list', 'trx_addons_editor_add_to_components' );
	function trx_addons_editor_add_to_components($list=array()) {
		$list['editor'] = array(
					'title' => __('WP Editor extensions', 'trx_addons')
					);
		return $list;
	}
}

	
// Load required styles and scripts for admin mode
if ( !function_exists( 'trx_addons_editor_load_scripts_admin' ) ) {
	add_action("admin_enqueue_scripts", 'trx_addons_editor_load_scripts_admin');
	add_action("trx_addons_action_pagebuilder_admin_scripts", 'trx_addons_editor_load_scripts_admin');
	function trx_addons_editor_load_scripts_admin() {
		// Add styles in the WP text editor
		if ( ! is_rtl() || ! is_customize_preview() ) {
// Commented, because loading icons is moved down (under the condition)
//			add_editor_style( array(
//								trx_addons_get_file_url('css/font-icons/css/trx_addons_icons.css'),
//								)
//							 );	
			if ( is_admin() ) {
				if ( ! trx_addons_exists_gutenberg()
					|| ! function_exists('trx_addons_gutenberg_is_preview')
					|| ! trx_addons_gutenberg_is_preview()
				) {
					add_editor_style( array(
								trx_addons_get_file_url('css/font-icons/css/trx_addons_icons.css'),
								trx_addons_get_file_url(TRX_ADDONS_PLUGIN_EDITOR . 'css/trx_addons.editor.css')
								)
							 );	
				}
			}
		}

		
		// Add styles to the TrxAddons Options
		if ( isset($_REQUEST['page']) && $_REQUEST['page']=='trx_addons_options' ) {
			wp_enqueue_style( 'trx_addons-editor', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_EDITOR . 'css/trx_addons.editor.css'), array(), null );
		}
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_editor_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_editor_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_editor_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-editor', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_EDITOR . 'css/trx_addons.editor.css'), array(), null );
		}
	}
}

// Merge editor specific styles into single stylesheet
if ( !function_exists( 'trx_addons_editor_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_editor_merge_styles');
	function trx_addons_editor_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_EDITOR . 'css/trx_addons.editor.css' ] = true;
		return $list;
	}
}
	
// Add vars to the admin scripts
if ( !function_exists( 'trx_addons_editor_localize_script_admin' ) ) {
	add_filter("trx_addons_filter_localize_script_admin", 'trx_addons_editor_localize_script_admin');
	function trx_addons_editor_localize_script_admin($vars) {
		$vars['editor_author']				= esc_html__('ThemeREX', 'trx_addons');
		$vars['editor_description']			= esc_html__('ThemeREX Addons Buttons', 'trx_addons');
		$vars['editor_styleselect_title']	= esc_html__('Extra styles for the selected text', 'trx_addons');
		$vars['editor_tooltip_title']		= esc_html__('Add tooltip to the selected text', 'trx_addons');
		$vars['editor_tooltip_prompt']		= esc_html__('Enter tooltip text text', 'trx_addons');
		$vars['editor_icons_title']			= esc_html__('Insert icon to the caret position', 'trx_addons');
		$vars['editor_icons_list']			= trx_addons_get_list_icons_classes();
		$vars['editor_text_not_selected']	= esc_html__('First select the letter!', 'trx_addons');
		$vars['editor_empty_value']			= esc_html__('Text is empty!', 'trx_addons');
		return $vars;
	}
}



// Init TinyMCE
//--------------------------------------------------------------
if ( !function_exists( 'trx_addons_editor_init' ) ) {
	add_filter( 'tiny_mce_before_init', 'trx_addons_editor_init', 1000);
	function trx_addons_editor_init($opt) {
		$style_formats = array(
			array(
				'title' => esc_html__('Headers', 'trx_addons'),
				'items' => array(
					array(
						'title' => esc_html__('No margin', 'trx_addons'),
						'selector' => 'h1,h2,h3,h4,h5,h6',
						'classes' => 'trx_addons_no_margin'
					)
				)
			),
			array(
				'title' => esc_html__('Blockquotes', 'trx_addons'),
				'items' => array(
					array(
						'title' => esc_html__('Style 1', 'trx_addons'),
						'selector' => 'blockquote',
						'classes' => 'trx_addons_blockquote_style_1'
					),
					array(
						'title' => esc_html__('Style 2', 'trx_addons'),
						'selector' => 'blockquote',
						'classes' => 'trx_addons_blockquote_style_2'
					)
				)
			),
			array(
				'title' => esc_html__('List styles', 'trx_addons'),
				'items' => array(
					array(
						'title' => esc_html__('Dot', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_dot'
					),
					array(
						'title' => esc_html__('Custom', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_custom'
					),
					array(
						'title' => esc_html__('Parameters', 'trx_addons'),
						'selector' => 'ul,ol',
						'classes' => 'trx_addons_list_parameters'
					),

					array(
						'title' => esc_html__('Success', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_success'
					),
					array(
						'title' => esc_html__('Error', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_error'
					),
					array(
						'title' => esc_html__('Info', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_info'
					),
					array(
						'title' => esc_html__('Plus', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_plus'
					),
					array(
						'title' => esc_html__('Minus', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_minus'
					),
					array(
						'title' => esc_html__('Help', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_help'
					),
					array(
						'title' => esc_html__('Attention', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_attention'
					),


					array(
						'title' => esc_html__('Success (circled)', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_success_circled'
					),
					array(
						'title' => esc_html__('Error (circled)', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_error_circled'
					),
					array(
						'title' => esc_html__('Info (circled)', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_info_circled'
					),
					array(
						'title' => esc_html__('Plus (circled)', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_plus_circled'
					),
					array(
						'title' => esc_html__('Minus (circled)', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_minus_circled'
					),
					array(
						'title' => esc_html__('Help (circled)', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_help_circled'
					),
					array(
						'title' => esc_html__('Attention (circled)', 'trx_addons'),
						'selector' => 'ul',
						'classes' => 'trx_addons_list_attention_circled'
					),

				)
			),
			array(
				'title' => esc_html__('Inline', 'trx_addons'),
				'items' => array(
					array(
						'title' => esc_html__('Accent text', 'trx_addons'),
						'inline' => 'span',
						'classes' => 'trx_addons_accent'
					),
					array(
						'title' => esc_html__('Accent background', 'trx_addons'),
						'inline' => 'span',
						'classes' => 'trx_addons_accent_bg'
					),
					array(
						'title' => esc_html__('Dark text', 'trx_addons'),
						'inline' => 'span',
						'classes' => 'trx_addons_dark'
					),
					array(
						'title' => esc_html__('Inverse text', 'trx_addons'),
						'inline' => 'span',
						'classes' => 'trx_addons_inverse'
					),
					array(
						'title' => esc_html__('Big font', 'trx_addons'),
						'inline' => 'big'
					),
					array(
						'title' => esc_html__('Small font', 'trx_addons'),
						'inline' => 'small'
					),
					array(
						'title' => esc_html__('Tiny text', 'trx_addons'),
						'inline' => 'span',
						'classes' => 'trx_addons_tiny_text'
					),
					array(
						'title' => esc_html__('Dropcap 1', 'trx_addons'),
						'inline' => 'span',
						'classes' => 'trx_addons_dropcap trx_addons_dropcap_style_1'
					),
					array(
						'title' => esc_html__('Dropcap 2', 'trx_addons'),
						'inline' => 'span',
						'classes' => 'trx_addons_dropcap trx_addons_dropcap_style_2'
					),
				)
			)
		);
		/*
		array(
			'title' => 'Warning Box',
			'block' => 'div',
			'classes' => 'warning box',
			'wrapper' => true
		),
		array(
			'title' => 'Red Uppercase Text',
			'inline' => 'span',
			'styles' => array(
				'color' => '#ff0000',
				'fontWeight' => 'bold',
				'textTransform' => 'uppercase'
			)
		)
		*/
		$opt['style_formats'] = json_encode( apply_filters('trx_addons_filter_tiny_mce_style_formats', $style_formats) );
		return $opt;
	}
}

// Add buttons in array
if ( !function_exists( 'trx_addons_editor_add_buttons' ) ) {
	add_filter( 'mce_external_plugins', 'trx_addons_editor_add_buttons', 20 );	// Elementor remove all filters with priority 10 
	function trx_addons_editor_add_buttons($buttons) {
		$buttons['trx_addons'] = trx_addons_get_file_url(TRX_ADDONS_PLUGIN_EDITOR . 'js/trx_addons.editor.js');
		return $buttons;
	}
}

// Register buttons in TinyMCE
if ( !function_exists( 'trx_addons_editor_register_buttons' ) ) {
	add_filter( 'mce_buttons', 'trx_addons_editor_register_buttons', 20 );		// Elementor remove all filters with priority 10 
	function trx_addons_editor_register_buttons($buttons) {
		array_push( $buttons, 'styleselect', 'trx_addons_tooltip', 'trx_addons_icons' );
		return $buttons;
	}
}

// Register buttons 2 in TinyMCE
if ( !function_exists( 'trx_addons_editor_register_buttons_2' ) ) {
	add_filter( 'mce_buttons_2', 'trx_addons_editor_register_buttons_2' );
	function trx_addons_editor_register_buttons_2($buttons) {
		array_splice( $buttons, 1, 0, array('sub', 'sup') );
		return $buttons;
	}
}
