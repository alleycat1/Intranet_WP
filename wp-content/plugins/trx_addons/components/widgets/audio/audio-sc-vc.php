<?php
/**
 * Widget: Audio player for Local hosted audio and Soundcloud and other embeded audio (WPBakery support)
 *
 * @package ThemeREX Addons
 * @since v1.2
 */


// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


// Add [trx_widget_audio] in the VC shortcodes list
if ( ! function_exists( 'trx_addons_sc_widget_audio_add_in_vc' ) ) {
	function trx_addons_sc_widget_audio_add_in_vc() {

		if ( ! trx_addons_exists_vc() ) return;

		vc_lean_map( 'trx_widget_audio', 'trx_addons_sc_widget_audio_add_in_vc_params' );
		class WPBakeryShortCode_Trx_Widget_Audio extends WPBakeryShortCode {}
	}
	add_action( 'init', 'trx_addons_sc_widget_audio_add_in_vc', 20 );
}


// Return params
if ( ! function_exists( 'trx_addons_sc_widget_audio_add_in_vc_params' ) ) {
	function trx_addons_sc_widget_audio_add_in_vc_params() {
		return apply_filters(
			'trx_addons_sc_map', array(
				'base'                    => 'trx_widget_audio',
				'name'                    => esc_html__( 'Widget: Audio', 'trx_addons' ),
				'description'             => wp_kses_data( __( 'Insert widget with embedded audio from popular audio hosting: SoundCloud, etc. or with local hosted audio', 'trx_addons' ) ),
				'category'                => esc_html__( 'ThemeREX', 'trx_addons' ),
				'icon'                    => 'icon_trx_widget_audio',
				'class'                   => 'trx_widget_audio',
				'content_element'         => true,
				'is_container'            => false,
				'show_settings_on_create' => true,
				'params'                  => array_merge(
					array(
						array(
							'param_name'  => 'title',
							'heading'     => esc_html__( 'Widget title', 'trx_addons' ),
							'description' => wp_kses_data( __( 'Title of the widget', 'trx_addons' ) ),
							'admin_label' => true,
							'type'        => 'textfield',
						),
						array(
							'param_name'  => 'subtitle',
							'heading'     => esc_html__( 'Widget subtitle', 'trx_addons' ),
							'description' => wp_kses_data( __( 'Subtitle of the widget', 'trx_addons' ) ),
							'admin_label' => true,
							'type'        => 'textfield',
						),
						array(
							'param_name'       => 'media_from_post',
							'heading'          => esc_html__( 'Get audio from post', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Get URL of the audio from the current post', 'trx_addons' ) ),
							'admin_label'      => true,
							'std'              => '0',
							'value'            => array( esc_html__( 'Get from post', 'trx_addons' ) => '1' ),
							'type'             => 'checkbox',
						),
						array(
							'param_name'       => 'next_btn',
							'heading'          => esc_html__( 'Next button', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Show next button', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label'      => true,
							'std'              => '1',
							'value'            => array( esc_html__( 'Show', 'trx_addons' ) => '1' ),
							'type'             => 'checkbox',
							'dependency'       => array(
								'element'  => 'media_from_post',
								'is_empty' => true,
							),
						),
						array(
							'param_name'       => 'prev_btn',
							'heading'          => esc_html__( 'Prev button', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Show prev button', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label'      => true,
							'std'              => '1',
							'value'            => array( esc_html__( 'Show', 'trx_addons' ) => '1' ),
							'type'             => 'checkbox',
							'dependency'       => array(
								'element'  => 'media_from_post',
								'is_empty' => true,
							),
						),
						array(
							'param_name'       => 'next_text',
							'heading'          => esc_html__( 'Next button caption', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Insert button caption', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-6',
							'dependency'       => array(
								'element' => 'next_btn',
								'value'   => '1',
							),
							'admin_label'      => true,
							'type'             => 'textfield',
							'dependency'       => array(
								'element'  => 'media_from_post',
								'is_empty' => true,
							),
						),
						array(
							'param_name'       => 'prev_text',
							'heading'          => esc_html__( 'Prev button caption', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Insert button caption', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-6',
							'dependency'       => array(
								'element' => 'prev_btn',
								'value'   => '1',
							),
							'admin_label'      => true,
							'type'             => 'textfield',
							'dependency'       => array(
								'element'  => 'media_from_post',
								'is_empty' => true,
							),
						),
						array(
							'param_name'  => 'now_text',
							'heading'     => esc_html__( "'Now Playing' text", 'trx_addons' ),
							'description' => wp_kses_data( __( "Change text of 'Now Playing' label. Write # if you want to hide label.", 'trx_addons' ) ),
							'admin_label' => true,
							'type'        => 'textfield',
						),
						array(
							'param_name'       => 'track_time',
							'heading'          => esc_html__( 'Track time', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Show track time', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-4',
							'admin_label'      => true,
							'std'              => '1',
							'value'            => array( esc_html__( 'Show', 'trx_addons' ) => '1' ),
							'type'             => 'checkbox',
						),
						array(
							'param_name'       => 'track_scroll',
							'heading'          => esc_html__( 'Track scroll bar', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Show track scroll bar', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-4',
							'admin_label'      => true,
							'std'              => '1',
							'value'            => array( esc_html__( 'Show', 'trx_addons' ) => '1' ),
							'type'             => 'checkbox',
						),
						array(
							'param_name'       => 'track_volume',
							'heading'          => esc_html__( 'Track volume bar', 'trx_addons' ),
							'description'      => wp_kses_data( __( 'Show track volume bar', 'trx_addons' ) ),
							'edit_field_class' => 'vc_col-sm-4',
							'admin_label'      => true,
							'std'              => '1',
							'value'            => array( esc_html__( 'Show', 'trx_addons' ) => '1' ),
							'type'             => 'checkbox',
						),
						array(
							'type'        => 'param_group',
							'param_name'  => 'media',
							'heading'     => esc_html__( 'Media', 'trx_addons' ),
							'description' => wp_kses_data( __( 'Specify values for each media item', 'trx_addons' ) ),
							'value'       => urlencode(
								json_encode(
									apply_filters(
										'trx_addons_sc_param_group_value', array(
											array(
												'url'     => '',
												'embed'   => '',
												'caption' => '',
												'author'  => '',
												'description' => '',
												'cover'   => '',
											),
										), 'trx_widget_audio'
									)
								)
							),
							'params'      => apply_filters(
								'trx_addons_sc_param_group_params', array_merge(
									array(
										array(
											'param_name'  => 'url',
											'heading'     => esc_html__( 'Audio URL', 'trx_addons' ),
											'description' => wp_kses_data( __( 'URL for local hosted audio file', 'trx_addons' ) ),
											'admin_label' => true,
											'type'        => 'textfield',
										),
										array(
											'param_name'  => 'embed',
											'heading'     => esc_html__( 'Embed code', 'trx_addons' ),
											'description' => wp_kses_data( __( 'or paste HTML code to embed audio', 'trx_addons' ) ),
											'type'        => 'textarea_safe',
										),
										array(
											'param_name'  => 'caption',
											'heading'     => esc_html__( 'Audio caption', 'trx_addons' ),
											'description' => wp_kses_data( __( 'Caption of this audio', 'trx_addons' ) ),
											'edit_field_class' => 'vc_col-sm-6',
											'admin_label' => true,
											'type'        => 'textfield',
										),
										array(
											'param_name'  => 'author',
											'heading'     => esc_html__( 'Author name', 'trx_addons' ),
											'description' => wp_kses_data( __( 'Name of the author', 'trx_addons' ) ),
											'edit_field_class' => 'vc_col-sm-6',
											'type'        => 'textfield',
										),
										array(
											'param_name'  => 'description',
											'heading'     => esc_html__( 'Description', 'trx_addons' ),
											'description' => wp_kses_data( __( 'Short description', 'trx_addons' ) ),
											'edit_field_class' => 'vc_col-sm-6',
											'type'        => 'textarea_safe',
										),
										array(
											'param_name'  => 'cover',
											'heading'     => esc_html__( 'Cover image', 'trx_addons' ),
											'description' => wp_kses_data( __( 'Select or upload cover image or write URL from other site', 'trx_addons' ) ),
											'edit_field_class' => 'vc_col-sm-6',
											'type'        => 'attach_image',
										),
									)
								), 'trx_widget_audio'
							),
							'dependency'       => array(
								'element'  => 'media_from_post',
								'is_empty' => true,
							),
						),
					),
					trx_addons_vc_add_id_param()
				),
			), 'trx_widget_audio'
		);
	}
}
