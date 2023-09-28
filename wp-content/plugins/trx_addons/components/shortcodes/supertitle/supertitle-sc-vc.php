<?php
/**
 * Shortcode: Super title (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.6.49
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_sc_supertitle] in the VC shortcodes list
if (!function_exists('trx_addons_sc_supertitle_add_in_vc')) {
	function trx_addons_sc_supertitle_add_in_vc() {

		if (!trx_addons_exists_vc()) return;

		vc_lean_map('trx_sc_supertitle', 'trx_addons_sc_supertitle_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Supertitle extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_supertitle_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_supertitle_add_in_vc_params')) {
	function trx_addons_sc_supertitle_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				'base' => 'trx_sc_supertitle',
				'name' => esc_html__('Super Title', 'trx_addons'),
				'description' => wp_kses_data( __("Insert 'Super Title' element", 'trx_addons') ),
				'category' => esc_html__('ThemeREX', 'trx_addons'),
				'icon' => 'icon_trx_sc_supertitle',
				'class' => 'trx_sc_supertitle',
				'content_element' => true,
				'is_container' => false,
				'show_settings_on_create' => true,
				'params' => array_merge(
					array(
						array(
							'param_name' => 'type',
							'heading' => esc_html__('Layout', 'trx_addons'),
							'description' => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							'admin_label' => true,
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-6',
							'std' => 'default',
							'value' => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'supertitle'), 'trx_sc_supertitle')),
							'type' => 'dropdown'
						),
						array(
							'param_name' => 'icon_column',
							'heading' => esc_html__('Icon column size', 'trx_addons'),
							'description' => wp_kses_data( __("Specify the width of the icon (left) column from 0 (no left column) to 6.", 'trx_addons') ),
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-6',
							'std' => '1',
							'type' => 'textfield'
						),
						array(
							'param_name' => 'header_column',
							'heading' => esc_html__('Left column size', 'trx_addons'),
							'description' => wp_kses_data( __("Specify the width of the main (middle) column from 0 (no middle column) to 12. Attention! The sum of values for the two columns (Icon and Main) must not exceed 12.", 'trx_addons') ),
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-6',
							'std' => '8',
							'type' => 'textfield'
						),
						array(
							'param_name' => 'image',
							'heading' => esc_html__('Choose media', 'trx_addons'),
							'description' => wp_kses_data( __('Select or upload image or specify URL from other site to use it as icon', 'trx_addons') ),
							'type' => 'attach_image'
						),
					),
					trx_addons_vc_add_icon_param(''),
					array(
						array(
							'param_name' => 'icon_color',
							'heading' => esc_html__( 'Color', 'trx_addons' ),
							'description' => esc_html__( 'Selected color will be applied to the Super Title icon or border (if no icon selected)', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '',
							'type' => 'colorpicker'
						),
						array(
							'param_name' => 'icon_bg_color',
							'heading' => esc_html__( 'Background color', 'trx_addons' ),
							'description' => esc_html__( 'Selected background color will be applied to the Super Title icon', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '',
							'type' => 'colorpicker'
						),
						array(
							'param_name' => 'icon_size',
							'heading' => esc_html__( 'Icon size or image width', 'trx_addons' ),
							'description' => esc_html__( 'For example, use 14px or 1em.', 'trx_addons' ),
							'admin_label' => true,
							'edit_field_class' => 'vc_col-sm-4',
							'type' => 'textfield',
						),
						array(
							'type' => 'param_group',
							'param_name' => 'items',
							'heading' => esc_html__( 'Items', 'trx_addons' ),
							'description' => wp_kses_data( __('Select icons, specify title and/or description for each item', 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
								array(
									'item_type' => 'text',
									'text' => esc_html__( 'Main title', 'trx_addons' ),
									'align' => 'left',
									'item_icon' => '',
									'color' => '',
									'color2' => '',
									'gradient_direction' => '0',
								),
							), 'trx_sc_supertitle') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array_merge(array(
								array(
									'param_name' => 'item_type',
									'heading' => esc_html__('Item Type', 'trx_addons'),
									'description' => wp_kses_data( __('Select type of the item', 'trx_addons') ),
									'admin_label' => true,
									'edit_field_class' => 'vc_col-sm-6',
									'std' => 'text',
									'value' => array_flip(trx_addons_get_list_sc_supertitle_item_types()),
									'type' => 'dropdown'
								),

								/*
								* Title
								*/
								array(
									'param_name' => 'text',
									'heading' => esc_html__('Text', 'trx_addons'),
									'description' => '',
									'admin_label' => true,
									'edit_field_class' => 'vc_col-sm-12',
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'text'
									),
									'type' => 'textarea_safe'
								),
								array(
									'param_name' => 'link',
									'heading' => esc_html__( 'Link text', 'trx_addons' ),
									'description' => esc_html__( 'Specify link for the text', 'trx_addons' ),
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'text'
									),
									'edit_field_class' => 'vc_col-sm-12',
									'admin_label' => true,
									'type' => 'textfield',
								),
								array(
									'param_name' => 'new_window',
									'heading' => esc_html__('Open in the new tab', 'trx_addons'),
									'description' => wp_kses_data( __("Open this link in the new browser's tab", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-12',
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'text'
									),
									'std' => 0,
									'value' => array(esc_html__('Open in the new tab', 'trx_addons') => 1 ),
									'type' => 'checkbox'
								),
								array(
									'param_name' => 'tag',
									'heading' => esc_html__('HTML Tag', 'trx_addons'),
									'description' => wp_kses_data( __('Select HTML wrapper of the item', 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-6',
									'std' => 'h2',
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'text'
									),
									'value' => array_flip(trx_addons_get_list_sc_title_tags('', true)),
									'type' => 'dropdown'
								),

								/*
								* Media
								*/
								array(
									'param_name' => 'media',
									'heading' => esc_html__('Choose media', 'trx_addons'),
									'description' => wp_kses_data( __('Select or upload image or specify URL from other site to use it as icon', 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-12',
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'media'
									),
									'type' => 'attach_image'
								),

								/*
								* Icon
								*/
								array(
									'param_name' => 'item_icon',
									'heading' => esc_html__('Icon', 'trx_addons'),
									'description' => wp_kses_data( __('Select icon', 'trx_addons') ),
									'value' => trx_addons_get_list_icons(trx_addons_get_setting('icons_type')),
									'edit_field_class' => 'vc_col-sm-12',
									'std' => '',
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'icon'
									),
									'style' => trx_addons_get_setting('icons_type'),
									'type' => 'icons'
								),
								array(
									'param_name' => 'size',
									'heading' => esc_html__( 'Size', 'trx_addons' ),
									'description' => esc_html__( 'For example, use 14px or 1em.', 'trx_addons' ),
									'admin_label' => true,
									'edit_field_class' => 'vc_col-sm-12',
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'icon'
									),
									'type' => 'textfield',
								),

								array(
									'param_name' => 'float_position',
									'heading' => esc_html__('Position', 'trx_addons'),
									'description' => '',
									'edit_field_class' => 'vc_col-sm-6',
									'std' => 'left',
									'dependency' => array(
											'element' => 'item_type',
											'value' => array('icon', 'media')
									),
									'value' => array_flip(trx_addons_get_list_sc_aligns(false, false)),
									'type' => 'dropdown'
								),

								/*
								* Common
								*/
								array(
									'param_name' => 'align',
									'heading' => esc_html__('Alignment', 'trx_addons'),
									'description' => '',
									'edit_field_class' => 'vc_col-sm-6',
									'std' => 'left',
									'value' => apply_filters('trx_addons_sc_supertitle_item_type', array(
										__( 'Left', 'trx_addons' ) => 'left',
										__( 'Right', 'trx_addons' ) => 'right',
									)),
									'type' => 'dropdown'
								),
								array(
									'param_name' => 'inline',
									'heading' => esc_html__('Inline', 'trx_addons'),
									'description' => '',
									'edit_field_class' => 'vc_col-sm-12',
									'std' => 0,
									'value' => array(esc_html__('Make it inline', 'trx_addons') => 1 ),
									'type' => 'checkbox'
								),
								array(
									'param_name' => 'color',
									'heading' => esc_html__( 'Color', 'trx_addons' ),
									'description' => esc_html__( 'Selected color will also be applied to the subtitle.', 'trx_addons' ),
									'edit_field_class' => 'vc_col-sm-4',
									'std' => '',
									'dependency' => array(
										'element' => 'item_type',
										'value' => array('icon', 'text')
									),
									'type' => 'colorpicker'
								),
								array(
									'param_name' => 'color2',
									'heading' => esc_html__( 'Color 2', 'trx_addons' ),
									'description' => esc_html__( 'If not empty - used for gradient.', 'trx_addons' ),
									'edit_field_class' => 'vc_col-sm-4',
									'std' => '',
									'dependency' => array(
										'element' => 'item_type',
										'value' => array('text')
									),
									'type' => 'colorpicker'
								),
								array(
									'param_name' => 'gradient_direction',
									'heading' => esc_html__( 'Gradient direction', 'trx_addons' ),
									'description' => esc_html__( 'Gradient direction in degress (0 - 360)', 'trx_addons' ),
									'admin_label' => true,
									'edit_field_class' => 'vc_col-sm-4',
									'std' => '',
									'dependency' => array(
										'element' => 'color2',
										'not_empty' => true
									),
									'type' => 'textfield',
								),
							) ), 'trx_sc_supertitle')
						)
					),
						trx_addons_vc_add_id_param()
					)
				), 'trx_sc_supertitle'
			);
	}
}
