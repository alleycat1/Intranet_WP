<?php
/**
 * Plugin support: WPML for Elementor
 *
 * @package ThemeREX Addons
 * @since v1.88.6
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}


if ( ! function_exists( 'trx_addons_wpml_elementor_widgets_to_translate' ) ) {
	add_filter( 'wpml_elementor_widgets_to_translate', 'trx_addons_wpml_elementor_widgets_to_translate' );
	/**
	 * Add Elementor's widgets to WPML's string translation
	 * 
	 * @hooked wpml_elementor_widgets_to_translate
	 * 
	 * @param array $nodes  List of widgets (shortcodes) and their params
	 * 
	 * @return array        Modified list of widgets (shortcodes) and their params
	 */
	function trx_addons_wpml_elementor_widgets_to_translate( $nodes ) {

		// Shortcodes
		//----------------------------------

		// Shortcode 'Action'
		$sc = __( 'Action', 'trx_addons' );
		$nodes['trx_sc_action'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_action' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							),
			'integration-class' => 'WPML_Elementor_Trx_Sc_Action',
		);

		// Shortcode 'Anchor'
		$sc = __( 'Anchor', 'trx_addons' );
		$nodes['trx_sc_anchor'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_anchor' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								'url' => array(
									'field'       => 'url',
									'type'        => sprintf( __( '%s: link URL', 'trx_addons' ), $sc ),
									'editor_type' => 'LINK'
								),
							),
		);

		// Shortcode 'Blogger'
		$sc = __( 'Blogger', 'trx_addons' );
		$nodes['trx_sc_blogger'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_blogger' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'filters_title',
										'type'        => sprintf( __( '%s: filters title', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'filters_subtitle',
										'type'        => sprintf( __( '%s: filters subtitle', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'filters_all_text',
										'type'        => sprintf( __( '%s: filters "All" tab', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'filters_more_text',
										'type'        => sprintf( __( '%s: filters "More posts"', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'more_text',
										'type'        => sprintf( __( '%s: button "Read More"', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'date_format',
										'type'        => sprintf( __( '%s: date format', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							),
		);

		// Shortcode 'Button'
		$nodes['trx_sc_button'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_button' ),
			'fields'     => array(
							),
			'integration-class' => 'WPML_Elementor_Trx_Sc_Button',
		);

		// Shortcode 'Cover'
		$sc = __( 'Cover', 'trx_addons' );
		$nodes['trx_sc_cover'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_cover' ),
			'fields'     => array(
								'url' => array(
									'field'       => 'url',
									'type'        => sprintf( __( '%s: link URL', 'trx_addons' ), $sc ),
									'editor_type' => 'LINK'
								),
							),
		);

		// Shortcode 'Form'
		$sc = __( 'Form', 'trx_addons' );
		$nodes['trx_sc_form'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_form' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'email',
										'type'        => sprintf( __( '%s: e-mail', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'phone',
										'type'        => sprintf( __( '%s: phone', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'address',
										'type'        => sprintf( __( '%s: address', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'button_caption',
										'type'        => sprintf( __( '%s: button caption', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							),
		);

		// Shortcode 'Google map'
		$sc = __( 'Google map', 'trx_addons' );
		$nodes['trx_sc_googlemap'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_googlemap' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'address',
										'type'        => sprintf( __( '%s: address or Lat,Lng', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'center',
										'type'        => sprintf( __( '%s: center of the map', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'content',
										'type'        => sprintf( __( '%s: content', 'trx_addons' ), $sc ),
										'editor_type' => 'VISUAL'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							),
			'integration-class' => 'WPML_Elementor_Trx_Sc_Googlemap',
		);

		// Shortcode 'Icons'
		$sc = __( 'Icons', 'trx_addons' );
		$nodes['trx_sc_icons'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_icons' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							),
			'integration-class' => 'WPML_Elementor_Trx_Sc_Icons',
		);

		// Shortcode 'OpenStreet map'
		$sc = __( 'OpenStreet map', 'trx_addons' );
		$nodes['trx_sc_osmap'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_osmap' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'address',
										'type'        => sprintf( __( '%s: address or Lat,Lng', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'center',
										'type'        => sprintf( __( '%s: center of the map', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'content',
										'type'        => sprintf( __( '%s: content', 'trx_addons' ), $sc ),
										'editor_type' => 'VISUAL'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							),
			'integration-class' => 'WPML_Elementor_Trx_Sc_Osmap',
		);

		// Shortcode 'Price'
		$sc = __( 'Price', 'trx_addons' );
		$nodes['trx_sc_price'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_price' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							),
			'integration-class' => 'WPML_Elementor_Trx_Sc_Price',
		);

		// Shortcode 'Promo'
		$sc = __( 'Promo', 'trx_addons' );
		$nodes['trx_sc_promo'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_promo' ),
			'fields'     => array_merge(
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc ),
								// Shortcode-specific params
								array(
									'link2' => array(
										'field'       => 'url',
										'field_id'    => 'link2_url',
										'type'        => sprintf( __( '%s: button 2 URL', 'trx_addons' ), $sc ),
										'editor_type' => 'LINK'
									),
									array(
										'field'       => 'link2_text',
										'type'        => sprintf( __( '%s: button 2 text', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'images',
										'type'        => sprintf( __( '%s: image URL', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'video_url',
										'type'        => sprintf( __( '%s: video URL', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
									array(
										'field'       => 'video_embed',
										'type'        => sprintf( __( '%s: video embed', 'trx_addons' ), $sc ),
										'editor_type' => 'AREA'
									),
									array(
										'field'       => 'content',
										'type'        => sprintf( __( '%s: content', 'trx_addons' ), $sc ),
										'editor_type' => 'VISUAL'
									),
								)
							),
		);

		// Shortcode 'Skills'
		$sc = __( 'Skills', 'trx_addons' );
		$nodes['trx_sc_skills'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_skills' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							),
			'integration-class' => 'WPML_Elementor_Trx_Sc_Skills',
		);

		// Shortcode 'Socials'
		$sc = __( 'Socials', 'trx_addons' );
		$nodes['trx_sc_socials'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_socials' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							),
			'integration-class' => 'WPML_Elementor_Trx_Sc_Socials',
		);

		// Shortcode 'Super title'
		$sc = __( 'Super title', 'trx_addons' );
		$nodes['trx_sc_supertitle'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_supertitle' ),
			'fields'     => array(
							),
			'integration-class' => 'WPML_Elementor_Trx_Sc_Supertitle',
		);

		// Shortcode 'Table'
		$sc = __( 'Table', 'trx_addons' );
		$nodes['trx_sc_table'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_table' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'content',
										'type'        => sprintf( __( '%s: content', 'trx_addons' ), $sc ),
										'editor_type' => 'AREA'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							),
		);

		// Shortcode 'Title'
		$sc = __( 'Title', 'trx_addons' );
		$nodes['trx_sc_title'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_title' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							)
		);

		// Shortcode 'Users'
		$sc = __( 'Users', 'trx_addons' );
		$nodes['trx_sc_users'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_users' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							)
		);

		// Shortcode 'WooCommerce Extended Products'
		$sc = __( 'WooCommerce Extended Products', 'trx_addons' );
		$nodes['trx_sc_extended_products'] = array(
				'conditions' => array( 'widgetType' => 'trx_sc_extended_products' ),
				'fields'     => array_merge(
						// Shortcode-specific params
						array(
						),
						// Common params
						trx_addons_wpml_elementor_get_title_params( $sc )
				)
		);


		// Widgets
		//-----------------------------------------

		// Widget 'About me'
		$sc = __( 'Widget About me', 'trx_addons' );
		$nodes['trx_widget_aboutme'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_aboutme' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								'avatar' => array(
									'field'       => 'url',
									'type'        => sprintf( __( '%s: avatar URL', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'username',
									'type'        => sprintf( __( '%s: user name', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'description',
									'type'        => sprintf( __( '%s: description', 'trx_addons' ), $sc ),
									'editor_type' => 'VISUAL'
								),
							),
		);

		// Widget 'Audio'
		$sc = __( 'Widget Audio', 'trx_addons' );
		$nodes['trx_widget_audio'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_audio' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'subtitle',
									'type'        => sprintf( __( '%s: subtitle', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'next_text',
									'type'        => sprintf( __( '%s: "Next" button', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'prev_text',
									'type'        => sprintf( __( '%s: "Prev" button', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'now_text',
									'type'        => sprintf( __( '%s: "Now playing" text', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
			'integration-class' => 'WPML_Elementor_Trx_Widget_Audio',
		);

		// Widget 'Banner'
		$sc = __( 'Widget Banner', 'trx_addons' );
		$nodes['trx_widget_banner'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_banner' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								'link' => array(
									'field'       => 'url',
									'type'        => sprintf( __( '%s: link URL', 'trx_addons' ), $sc ),
									'editor_type' => 'LINK'
								),
								array(
									'field'       => 'code',
									'type'        => sprintf( __( '%s: or HTML code', 'trx_addons' ), $sc ),
									'editor_type' => 'AREA'
								),
							),
		);

		// Widget 'Calendar'
		$sc = __( 'Widget Calendar', 'trx_addons' );
		$nodes['trx_widget_calendar'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_calendar' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
		);

		// Widget 'Categories list'
		$sc = __( 'Widget Categories list', 'trx_addons' );
		$nodes['trx_widget_categories_list'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_categories_list' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
		);

		// Widget 'Contacts'
		$sc = __( 'Widget Contacts', 'trx_addons' );
		$nodes['trx_widget_contacts'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_contacts' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'description',
									'type'        => sprintf( __( '%s: description', 'trx_addons' ), $sc ),
									'editor_type' => 'VISUAL'
								),
								array(
									'field'       => 'address',
									'type'        => sprintf( __( '%s: address', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'phone',
									'type'        => sprintf( __( '%s: phone', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'email',
									'type'        => sprintf( __( '%s: e-mail', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
		);

		// Widget 'Custom links'
		$sc = __( 'Widget Custom links', 'trx_addons' );
		$nodes['trx_widget_custom_links'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_custom_links' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
			'integration-class' => 'WPML_Elementor_Trx_Widget_Custom_Links',
		);

		// Widget 'Flickr'
		$sc = __( 'Widget Flickr', 'trx_addons' );
		$nodes['trx_widget_flickr'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_flickr' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'flickr_api_key',
									'type'        => sprintf( __( '%s: API key', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'flickr_username',
									'type'        => sprintf( __( '%s: user name', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'flickr_count',
									'type'        => sprintf( __( '%s: number of photos', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'flickr_columns',
									'type'        => sprintf( __( '%s: columns', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
		);

		// Widget 'Instagram'
		$sc = __( 'Widget Instagram', 'trx_addons' );
		$nodes['trx_widget_instagram'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_instagram' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'hashtag',
									'type'        => sprintf( __( '%s: API key', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'count',
									'type'        => sprintf( __( '%s: number of photos', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'columns',
									'type'        => sprintf( __( '%s: columns', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
		);

		// Widget 'Popular posts'
		$sc = __( 'Widget Popular posts', 'trx_addons' );
		$nodes['trx_widget_popular_posts'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_popular_posts' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'title_1',
									'type'        => sprintf( __( '%s: tab 1 title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'title_2',
									'type'        => sprintf( __( '%s: tab 2 title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'title_3',
									'type'        => sprintf( __( '%s: tab 3 title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
		);

		// Widget 'Recent news'
		$sc = __( 'Widget Recent news', 'trx_addons' );
		$nodes['trx_sc_recent_news'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_recent_news' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'subtitle',
									'type'        => sprintf( __( '%s: subtitle', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'ids',
									'type'        => sprintf( __( '%s: list IDs', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
		);

		// Widget 'Recent posts'
		$sc = __( 'Widget Recent posts', 'trx_addons' );
		$nodes['trx_widget_recent_posts'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_recent_posts' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
		);

		// Widget 'Slider'
		$sc = __( 'Widget Slider', 'trx_addons' );
		$nodes['trx_widget_slider'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_slider' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'alias',
									'type'        => sprintf( __( '%s: RevSlider alias', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
			'integration-class' => 'WPML_Elementor_Trx_Widget_Slider',
		);

		// Widget 'Socials'
		$sc = __( 'Widget Socials', 'trx_addons' );
		$nodes['trx_widget_socials'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_socials' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'description',
									'type'        => sprintf( __( '%s: description', 'trx_addons' ), $sc ),
									'editor_type' => 'VISUAL'
								),
							),
		);

		// Widget 'Twitter'
		$sc = __( 'Widget Twitter', 'trx_addons' );
		$nodes['trx_widget_twitter'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_twitter' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'username',
									'type'        => sprintf( __( '%s: user name', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'consumer_key',
									'type'        => sprintf( __( '%s: consumer key', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'consumer_secret',
									'type'        => sprintf( __( '%s: consumer secret', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'token_key',
									'type'        => sprintf( __( '%s: token key', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'token_secret',
									'type'        => sprintf( __( '%s: token secret', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
		);

		// Widget 'Video'
		$sc = __( 'Widget Video', 'trx_addons' );
		$nodes['trx_widget_video'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_video' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'link',
									'type'        => sprintf( __( '%s: link to the video', 'trx_addons' ), $sc ),
									'editor_type' => 'LINK'
								),
								array(
									'field'       => 'embed',
									'type'        => sprintf( __( '%s: video embed code', 'trx_addons' ), $sc ),
									'editor_type' => 'AREA'
								),
							),
		);

		// Widget 'Video list'
		$sc = __( 'Widget Video list', 'trx_addons' );
		$nodes['trx_widget_video_list'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_video_list' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
			'integration-class' => 'WPML_Elementor_Trx_Widget_Video_List',
		);


		// CPT: Custom post types
		//------------------------------------------

		// CPT 'Cars'
		$sc = __( 'Cars', 'trx_addons' );
		$nodes['trx_sc_cars'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_cars' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'more_text',
										'type'        => sprintf( __( '%s: "More" text', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							)
		);

		// CPT 'Courses'
		$sc = __( 'Courses', 'trx_addons' );
		$nodes['trx_sc_courses'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_courses' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'more_text',
										'type'        => sprintf( __( '%s: "More" text', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							)
		);

		// CPT 'Dishes'
		$sc = __( 'Dishes', 'trx_addons' );
		$nodes['trx_sc_dishes'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_dishes' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'more_text',
										'type'        => sprintf( __( '%s: "More" text', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							)
		);

		// CPT 'Layouts': Blog itam
		$sc = __( 'Layouts - Blog item', 'trx_addons' );
		$nodes['trx_sc_layouts_blog_item'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_layouts_blog_item' ),
			'fields'     => array(
								array(
									'field'       => 'button_text',
									'type'        => sprintf( __( '%s: Button text', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							)
		);

		// CPT 'Layouts': Cart
		$sc = __( 'Layouts - Cart', 'trx_addons' );
		$nodes['trx_sc_layouts_cart'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_layouts_cart' ),
			'fields'     => array(
								array(
									'field'       => 'text',
									'type'        => sprintf( __( '%s: Cart text', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							)
		);

		// CPT 'Layouts': Iconed text
		$sc = __( 'Layouts - Iconed text', 'trx_addons' );
		$nodes['trx_sc_layouts_iconed_text'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_layouts_iconed_text' ),
			'fields'     => array(
								array(
									'field'       => 'text1',
									'type'        => sprintf( __( '%s: Text line 1', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'text2',
									'type'        => sprintf( __( '%s: Text line 2', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							)
		);

		// CPT 'Layouts': Layout
		$sc = __( 'Layouts', 'trx_addons' );
		$nodes['trx_sc_layouts'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_layouts' ),
			'fields'     => array(
								array(
									'field'       => 'popup_id',
									'type'        => sprintf( __( '%s: Popup (panel) ID', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'content',
									'type'        => sprintf( __( '%s: Popup (panel) content', 'trx_addons' ), $sc ),
									'editor_type' => 'VISUAL'
								),
							)
		);

		// CPT 'Layouts': Login
		$sc = __( 'Layouts - Login', 'trx_addons' );
		$nodes['trx_sc_layouts_login'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_layouts_login' ),
			'fields'     => array(
								array(
									'field'       => 'text_login',
									'type'        => sprintf( __( '%s: Login text', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'text_logout',
									'type'        => sprintf( __( '%s: Logout text', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							)
		);

		// CPT 'Layouts': Logo
		$sc = __( 'Layouts - Logo', 'trx_addons' );
		$nodes['trx_sc_layouts_logo'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_layouts_logo' ),
			'fields'     => array(
								array(
									'field'       => 'logo_text',
									'type'        => sprintf( __( '%s: Logo text', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'logo_slogan',
									'type'        => sprintf( __( '%s: Logo slogan', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								'logo' => array(
									'field'       => 'url',
									'field_id'    => 'logo',
									'type'        => sprintf( __( '%s: Logo image', 'trx_addons' ), $sc ),
									'editor_type' => 'LINK'
								),
								'logo_retina' => array(
									'field'       => 'url',
									'field_id'    => 'logo_retina',
									'type'        => sprintf( __( '%s: Logo Retina', 'trx_addons' ), $sc ),
									'editor_type' => 'LINK'
								),
							)
		);

		// CPT 'Layouts': Title
		$sc = __( 'Layouts - Title', 'trx_addons' );
		$nodes['trx_sc_layouts_title'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_layouts_title' ),
			'fields'     => array(
								'image' => array(
									'field'       => 'url',
									'field_id'    => 'image',
									'type'        => sprintf( __( '%s: image URL', 'trx_addons' ), $sc ),
									'editor_type' => 'LINK'
								),
							)
		);

		// CPT 'Portfolio'
		$sc = __( 'Portfolio', 'trx_addons' );
		$nodes['trx_sc_portfolio'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_portfolio' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'more_text',
										'type'        => sprintf( __( '%s: "More" text', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							)
		);

		// CPT 'Properties'
		$sc = __( 'Properties', 'trx_addons' );
		$nodes['trx_sc_properties'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_properties' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'more_text',
										'type'        => sprintf( __( '%s: "More" text', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							)
		);

		// CPT 'Services'
		$sc = __( 'Services', 'trx_addons' );
		$nodes['trx_sc_services'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_services' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'more_text',
										'type'        => sprintf( __( '%s: "More" text', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							)
		);

		// CPT 'Sport'
		$sc = __( 'Sport - Matches', 'trx_addons' );
		$nodes['trx_sc_matches'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_matches' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'more_text',
										'type'        => sprintf( __( '%s: "More" text', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							)
		);

		// CPT 'Team'
		$sc = __( 'Team', 'trx_addons' );
		$nodes['trx_sc_team'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_team' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
									array(
										'field'       => 'more_text',
										'type'        => sprintf( __( '%s: "More" text', 'trx_addons' ), $sc ),
										'editor_type' => 'LINE'
									),
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							)
		);

		// CPT 'Testimonials'
		$sc = __( 'Testimonials', 'trx_addons' );
		$nodes['trx_sc_testimonials'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_testimonials' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							)
		);


		// Third-party plugins
		//-------------------------------

		// Widget 'EDD Search'
		$sc = __( 'Widget EDD Search', 'trx_addons' );
		$nodes['trx_widget_edd_search'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_edd_search' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
		);

		// Widget 'LearnPress Course info'
		$sc = __( 'Widget LearnPress Course info', 'trx_addons' );
		$nodes['trx_sc_widget_lp_course_info'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_widget_lp_course_info' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
		);

		// Shortcode 'MP Time table'
		$sc = __( 'Widget MP Time table', 'trx_addons' );
		$nodes['trx_sc_mptt'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_widget_lp_course_info' ),
			'fields'     => array(
								array(
									'field'       => 'label',
									'type'        => sprintf( __( '%s: filter label', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'mptt_id',
									'type'        => sprintf( __( '%s: timetable ID', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
		);

		// Shortcode 'Events'
		$sc = __( 'Events', 'trx_addons' );
		$nodes['trx_sc_events'] = array(
			'conditions' => array( 'widgetType' => 'trx_sc_events' ),
			'fields'     => array_merge(
								// Shortcode-specific params
								array(
								),
								// Common params
								trx_addons_wpml_elementor_get_title_params( $sc )
							)
		);

		// Shortcode 'ThemeREX Donations form'
		$sc = __( 'Donations form', 'trx_addons' );
		$nodes['trx_donations_form'] = array(
			'conditions' => array( 'widgetType' => 'trx_donations_form' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'subtitle',
									'type'        => sprintf( __( '%s: subtitle', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'description',
									'type'        => sprintf( __( '%s: description', 'trx_addons' ), $sc ),
									'editor_type' => 'VISUAL'
								),
								array(
									'field'       => 'client_id',
									'type'        => sprintf( __( '%s: PayPal client ID', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'amount',
									'type'        => sprintf( __( '%s: default amount', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							)
		);

		// Shortcode 'ThemeREX Donations list'
		$sc = __( 'Donations list', 'trx_addons' );
		$nodes['trx_donations_list'] = array(
			'conditions' => array( 'widgetType' => 'trx_donations_list' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'subtitle',
									'type'        => sprintf( __( '%s: subtitle', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'description',
									'type'        => sprintf( __( '%s: description', 'trx_addons' ), $sc ),
									'editor_type' => 'VISUAL'
								),
								'link' => array(
									'field'       => 'url',
									'type'        => sprintf( __( '%s: link URL', 'trx_addons' ), $sc ),
									'editor_type' => 'LINK'
								),
								array(
									'field'       => 'link_caption',
									'type'        => sprintf( __( '%s: link text', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							)
		);

		// Shortcode 'ThemeREX Donations info'
		$sc = __( 'Donations info', 'trx_addons' );
		$nodes['trx_donations_info'] = array(
			'conditions' => array( 'widgetType' => 'trx_donations_info' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'subtitle',
									'type'        => sprintf( __( '%s: subtitle', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'description',
									'type'        => sprintf( __( '%s: description', 'trx_addons' ), $sc ),
									'editor_type' => 'VISUAL'
								),
								'link' => array(
									'field'       => 'url',
									'type'        => sprintf( __( '%s: link URL', 'trx_addons' ), $sc ),
									'editor_type' => 'LINK'
								),
								array(
									'field'       => 'link_caption',
									'type'        => sprintf( __( '%s: link text', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							)
		);

		// Widget 'WooCommerce Search'
		$sc = __( 'Widget WooCommerce search', 'trx_addons' );
		$nodes['trx_widget_woocommerce_search'] = array(
			'conditions' => array( 'widgetType' => 'trx_widget_woocommerce_search' ),
			'fields'     => array(
								array(
									'field'       => 'title',
									'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'last_text',
									'type'        => sprintf( __( '%s: text after last field', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
								array(
									'field'       => 'button_text',
									'type'        => sprintf( __( '%s: button text', 'trx_addons' ), $sc ),
									'editor_type' => 'LINE'
								),
							),
			'integration-class' => 'WPML_Elementor_Trx_Widget_Woocommerce_Search',
		);
		
		return $nodes;

	}
}

if ( ! function_exists( 'trx_addons_wpml_elementor_get_title_params' ) ) {
	/**
	 * Return array with title parameters for WPML translation
	 *
	 * @param string $sc  Shortcode name
	 * 
	 * @return array      Array with title parameters
	 */
	function trx_addons_wpml_elementor_get_title_params( $sc ) {
		return array(
					array(
						'field'       => 'title',
						'type'        => sprintf( __( '%s: title', 'trx_addons' ), $sc ),
						'editor_type' => 'LINE'
					),
					array(
						'field'       => 'subtitle',
						'type'        => sprintf( __( '%s: subtitle', 'trx_addons' ), $sc ),
						'editor_type' => 'LINE'
					),
					array(
						'field'       => 'description',
						'type'        => sprintf( __( '%s: description', 'trx_addons' ), $sc ),
						'editor_type' => 'AREA'
					),
					'link' => array(
						'field'       => 'url',
						'type'        => sprintf( __( '%s: link URL', 'trx_addons' ), $sc ),
						'editor_type' => 'LINK'
					),
					array(
						'field'       => 'link_text',
						'type'        => sprintf( __( '%s: link text', 'trx_addons' ), $sc ),
						'editor_type' => 'LINE'
					),
				);
	}
}

if ( ! function_exists( 'trx_addons_wpml_elementor_autoload_classes' ) ) {
	/**
	 * Autoload required classes for WPML translation
	 *
	 * @param string $class  Class name
	 */
	function trx_addons_wpml_elementor_autoload_classes( $class ) {
		if (   0 !== strpos( $class, 'WPML_Elementor_Trx_Module_' )
			&& 0 !== strpos( $class, 'WPML_Elementor_Trx_Widget_' )
			&& 0 !== strpos( $class, 'WPML_Elementor_Trx_Sc_' )
		) {
			return;
		}
		$file = TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'sitepress-multilingual-cms/ate/class-' . trx_addons_esc( str_replace( '_', '-', strtolower( $class ) ) ) . '.php';
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
	// Register autoloader
	spl_autoload_register( 'trx_addons_wpml_elementor_autoload_classes' );
}
