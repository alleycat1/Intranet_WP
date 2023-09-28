<?php
/**
 * Shortcode: Blogger (Gutenberg support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}



// Gutenberg Block
//------------------------------------------------------

// Add scripts and styles for the editor
if ( ! function_exists( 'trx_addons_gutenberg_sc_blogger_editor_assets' ) ) {
	add_action( 'enqueue_block_editor_assets', 'trx_addons_gutenberg_sc_blogger_editor_assets' );
	function trx_addons_gutenberg_sc_blogger_editor_assets() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			// Scripts
			wp_enqueue_script(
				'trx-addons-gutenberg-editor-block-blogger',
				trx_addons_get_file_url( TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/gutenberg/blogger.gutenberg-editor.js' ),
				trx_addons_block_editor_dependencis(),
				filemtime( trx_addons_get_file_dir( TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/gutenberg/blogger.gutenberg-editor.js' ) ),
				true
			);
		}
	}
}

// Block register
if ( ! function_exists( 'trx_addons_sc_blogger_add_in_gutenberg' ) ) {
	add_action( 'init', 'trx_addons_sc_blogger_add_in_gutenberg' );
	function trx_addons_sc_blogger_add_in_gutenberg() {
		if ( trx_addons_exists_gutenberg() && trx_addons_get_setting( 'allow_gutenberg_blocks' ) ) {
			$atts = array_merge(
					array(
						'type'               => array(
							'type'    => 'string',
							'default' => 'default',
						),
						'post_type'			=> array(
							'type'    => 'string',
							'default' => 'post',
						),
						'taxonomy'			=> array(
							'type'    => 'string',
							'default' => 'category',
						),
						'cat'				=> array(
							'type'    => 'string',
							'default' => '',
						),
						'pagination'		=> array(
							'type'    => 'string',
							'default' => 'none',
						),
						// Details
						'meta_parts'		=> array(
							'type'    => 'string',
							'default' => '',
						),
						'hide_excerpt'	=> array(
							'type'    => 'boolean',
							'default' => false,
						),
						'excerpt_length'=> array(
							'type'    => 'string',
							'default' => '',
						),
						'full_post'		=> array(
							'type'    => 'boolean',
							'default' => false,
						),
						'no_margin'     => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'no_links'		=> array(
							'type'    => 'boolean',
							'default' => false,
						),
						'more_button'	=> array(
							'type'    => 'boolean',
							'default' => true,
						),
						'more_text'		=> array(
							'type'    => 'string',
							'default' => esc_html__('Read more', 'trx_addons'),
						),
						'image_position'	=> array(
							'type'    => 'string',
							'default' => 'top',
						),
						'image_width'	=> array(
							'type'    => 'number',
							'default' => 40,
						),
						'image_ratio'	=> array(
							'type'    => 'string',
							'default' => 'none',
						),
						'thumb_size'	=> array(
							'type'    => 'string',
							'default' => '',
						),
						'hover'			=> array(
							'type'    => 'string',
							'default' => 'inherit',
						),
						'date_format'	=> array(
							'type'    => 'string',
							'default' => '',
						),
						'text_align'	=> array(
							'type'    => 'string',
							'default' => 'left',
						),
						'on_plate'		=> array(
							'type'    => 'boolean',
							'default' => false,
						),
						'video_in_popup'     => array(
							'type'    => 'boolean',
							'default' => false,
						),
						'numbers'		=> array(
							'type'    => 'boolean',
							'default' => false,
						),
						'align'		=> array(
							'type'    => 'string',
							//'enum'    => array( 'left', 'center', 'right', 'wide', 'full' ),
							'default' => '',
						),
						// Rerender
						'reload'             => array(
							'type'    => 'string',
							'default' => '',
						),
					),
					trx_addons_gutenberg_get_param_filters(),
					trx_addons_gutenberg_get_param_query(),
					trx_addons_gutenberg_get_param_slider(),
					trx_addons_gutenberg_get_param_title(),
					trx_addons_gutenberg_get_param_button(),
					trx_addons_gutenberg_get_param_id()
			);
			// If editor is active now
			$is_edit_mode = trx_addons_is_post_edit();
			// Templates
			if ( $is_edit_mode ) {
				$layouts = apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), 'trx_sc_blogger' );
				$templates = trx_addons_components_get_allowed_templates('sc', 'blogger', $layouts);
				if ( is_array($templates) ) {
					foreach ($templates as $k => $v) {
						$atts['template_' . $k] = array(
							'type' => 'string',
							'default' => is_array($v) ? trx_addons_array_get_first($v) : ''
						);
					}
				}
			}
			register_block_type(
				'trx-addons/blogger',
				apply_filters('trx_addons_gb_map', array(
					'attributes' => $atts,
					'supports' => array(
						'align' => array( 'left', 'center', 'right', 'wide', 'full' ),
						'html' => false,
					),
					'render_callback' => 'trx_addons_gutenberg_sc_blogger_render_block',
				), 'trx-addons/blogger' )
			);
		}
	}
}

// Block render
if ( ! function_exists( 'trx_addons_gutenberg_sc_blogger_render_block' ) ) {
	function trx_addons_gutenberg_sc_blogger_render_block( $attributes = array() ) {
		return trx_addons_sc_blogger( $attributes );
	}
}

// Return list of allowed layouts
if ( ! function_exists( 'trx_addons_gutenberg_sc_blogger_layouts' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_layouts', 'trx_addons_gutenberg_sc_blogger_layouts', 10, 1 );
	function trx_addons_gutenberg_sc_blogger_layouts( $list = array() ) {
		$list['sc_blogger'] = apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), 'trx_sc_blogger' );
		return $list;
	}
}

// Add shortcode-specific lists to the js vars
if ( ! function_exists( 'trx_addons_gutenberg_sc_blogger_params' ) ) {
	add_filter( 'trx_addons_filter_gutenberg_sc_params', 'trx_addons_gutenberg_sc_blogger_params', 10, 1 );
	function trx_addons_gutenberg_sc_blogger_params( $vars = array() ) {

		// If editor is active now
		$is_edit_mode = trx_addons_is_post_edit();

		// Templates
		$vars['sc_blogger_templates'] = array();
		if ($is_edit_mode) {
			$layouts = apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'blogger'), 'trx_sc_blogger' );
			$templates = trx_addons_components_get_allowed_templates('sc', 'blogger', $layouts);
			$vars['sc_blogger_templates'] = $templates;
			if ( is_array($templates) ) {
				foreach ($templates as $k => $v) {
					$options = array();
					$default = '';
					if (is_array($v)) {
						foreach($v as $k1 => $v1) {
							$options[$k1] = !empty($v1['title']) ? $v1['title'] : ucfirst( str_replace( array('_', '-'), ' ', $k1 ) );
							if (empty($default)) $default = $k1;
						}
					}
					$vars['sc_blogger_template_' . $k] = $options;
				}
			}
		}
		$vars['sc_blogger_tabs_positions']  = ! $is_edit_mode ? array() : trx_addons_get_list_sc_tabs_positions();
		$vars['sc_blogger_image_positions'] = ! $is_edit_mode ? array() : trx_addons_get_list_sc_blogger_image_positions();
		$vars['sc_blogger_image_ratio']     = ! $is_edit_mode ? array() : trx_addons_get_list_sc_image_ratio();
		$vars['sc_blogger_image_hover']     = ! $is_edit_mode ? array() : trx_addons_get_list_sc_image_hover();
		$vars['sc_blogger_thumb_sizes']     = ! $is_edit_mode ? array() : array_merge( array( '' => __( 'Default', 'trx_addons' ) ), trx_addons_get_list_thumbnail_sizes() );

		return $vars;
	}
}
