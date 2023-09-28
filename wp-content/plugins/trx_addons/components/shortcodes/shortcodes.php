<?php
/**
 * ThemeREX Shortcodes
 *
 * @package ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

// Define list with shortcodes
if (!function_exists('trx_addons_sc_setup')) {
	add_action( 'after_setup_theme', 'trx_addons_sc_setup', 2 );
	function trx_addons_sc_setup() {
		static $loaded = false;
		if ($loaded) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['sc_list'] = apply_filters('trx_addons_sc_list', array(
			'action' => array(
							'title' => __('Actions', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'simple' => esc_html__('Simple', 'trx_addons'),
								'event' => esc_html__('Event', 'trx_addons')
							)
						),
			'anchor' => array(
							'title' => __('Anchor', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons')
							)
						),
			'accordionposts' => array(
						'title' => __('Accordion of posts', 'trx_addons'),
						'layouts_sc' => array(
							'default' => esc_html__('Default', 'trx_addons')
						)
					),
			'blogger' => array(
							'title' => __('Blogger', 'trx_addons'),
							'post_loop'  => true,
							'layouts_sc' => array(

								'default' => esc_html__('Default', 'trx_addons'),
								'wide'    => esc_html__('Wide', 'trx_addons'),
								'list'    => esc_html__('List', 'trx_addons'),
								'news'    => esc_html__('News', 'trx_addons'),
								'panel'   => esc_html__('Panel', 'trx_addons'),
								'cards'   => esc_html__('Cards', 'trx_addons'),
/*
								'default' => trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/type-default.png'),
								'list' => trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/type-list.png'),
*/
							),
							// Templates for each shortcode's layout:
							// Zones: 'featured' - info inside (over) the featured image
							//                     Over positions: 'tl|tc|tr'
							//                                     'ml|mc|mr'
							//                                     'bl|bc|br'
							//        'content'  - info after (under) the featured image
							//        'header'   - info before (above) the post body (featured image and content)
							//        'footer'   - info after (below) the post body (featured image and content)
							// Components: 'title'    - post's title
							//             'excerpt'  - post's content (depends from post format)
							//             'readmore' - button (link) to the single post (with theme-specific styles)
							//             'meta'     - post's meta (categories, date, author, views, comments, likes, rating, edit)
							//             'meta_xxx' - separate post's meta item from the list above
							//             'price'    - post's price (if supported for the current post type)
							//             'rating'   - post's rating (if supported for the current post type) as text, icon and mark or stars
							//             'rating_text'
							//             'rating_icons'
							//             'rating_stars'
							'templates' => array(
								// Templates for layout "Default"
								'default' => array(
									'classic' => array(
										'title'  => __('Classic Grid', 'trx_addons'),
										'layout' => array(
											'featured' => array(
												//'br' => array( 'price' ),         // Show price in the bottom right corner of the featured image 
												//'tr' => array( 'rating_stars' )   // Show rating in the top right corner of the featured image
											),
											'content' => array(
												'meta_categories', 'title', 'meta', 'excerpt', 'readmore'
											)
										)
									),
									'classic_2' => array(
										'title'  => __('Classic with cats over image', 'trx_addons'),
										'layout' => array(
											'featured' => array(
												'bl' => array(
													'meta_categories'
												),
											),
											'content' => array(
												'title', 'meta', 'excerpt', 'readmore'
											)
										)
									),
									'classic_3' => array(
										'title'  => __('Classic with header above', 'trx_addons'),
										'layout' => array(
											'header' => array(
												'title', 'meta'
											),
											'featured' => array(
												'bl' => array(
													'meta_views'
												),
											),
											'content' => array(
												'excerpt', 'readmore'
											)
										)
									),
									'over_centered' => array(
										'title'  => __('Info over image', 'trx_addons'),
										'layout' => array(
											'featured' => array(
												'br' => array(
													'meta_date'
												),
												'mc' => array(
													'meta_categories', 'title', 'meta', 'readmore'
												),
												'tr' => array(
													'price'
												),
											),
										)
									),
									'over_bottom' => array(
										'title'  => __('Info over image (bottom)', 'trx_addons'),
										'layout' => array(
											'featured' => array(
												'bc' => array(
													'meta_categories', 'title', 'meta', 'readmore'
												),
												'tr' => array(
													'price'
												),
											),
										)
									),
								),
								// Templates for layout "Wide"
								'wide' => array(
									'default' => array(
										'title'  => __('Default', 'trx_addons'),
										'layout' => array(
											'header' => array(
												'title', 'meta'
											),
											'featured' => array(
											),
											'content' => array(
												'excerpt', 'readmore'
											)
										)
									),
								),
								// Templates for layout "List"
								'list' => array(
									'simple' => array(
										'title'  => __('Simple', 'trx_addons'),
										'layout' => array(
											'content' => array(
												'meta_categories', 'title', 'meta'
											)
										)
									),
									'with_image' => array(
										'title'  => __('With image', 'trx_addons'),
										'layout' => array(
											'featured' => array(
											),
											'content' => array(
												'meta_categories', 'title', 'meta'
											)
										)
									),
								),
								// Templates for layout "Panel"
								'panel' => array(
									'default' => array(
										'title'  => __('Default', 'trx_addons'),
										'layout' => array(
											'content' => array(
												'title', 'excerpt', 'readmore'
											)
										)
									),
								),
								// Templates for layout "Cards"
								'cards' => array(
									'default' => array(
										'title'  => __('Default', 'trx_addons'),
										'args' => array( 'columns' => 1, 'slider' => 0, 'video_in_popup' => 1,
														'pagination' => 'none', 'full_post' => ''
														),
										'layout' => array(
											'featured' => array(
											),
											'content' => array(
												'title', 'meta', 'excerpt'
											),
											'footer' => array(
												'meta_author'
											)
										)
									),
									'simple' => array(
										'title'  => __('Simple', 'trx_addons'),
										'args' => array( 'columns' => 1, 'slider' => 0, 'video_in_popup' => 1,
														'pagination' => 'none', 'full_post' => ''
														),
										'layout' => array(
											'header' => array(
												'meta'
											),
											'content' => array(
												'title', 'excerpt'
											),
											'footer' => array(
												'meta_author'
											)
										)
									),
									'featured' => array(
										'title'  => __('Featured', 'trx_addons'),
										'args' => array( 'columns' => 1, 'slider' => 0, 'video_in_popup' => 1,
														'pagination' => 'none', 'full_post' => '', 'image_ratio' => '3:4'
														),
										'layout' => array(
											'featured' => array(
												'br' => array(
													'meta_date'
												),
												'mc' => array(
													'meta_categories', 'title', 'meta', 'readmore'
												),
												'tr' => array(
													'price'
												),
											),
										)
									),
								),
								// Templates for layout "News"
								'news' => array(
									'announce' => array(
										'title' => __('Announcement', 'trx_addons'),
										'grid'  => array(
											// One post
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
												)
											),
											// Two posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
												)
											),
											// Three posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
												)
											),
											// Four posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
												)
											),
											// Five posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
												)
											),
											// Six posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
												)
											),
											// Seven posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
												)
											),
											// Eight posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
													array(
														'template' => 'default/over_centered',
														'args' => array( 'image_ratio' => '16:9', 'columns' => 1 )
													),
												)
											),
										)
									),
									'magazine' => array(
										'title' => __('Magazine', 'trx_addons'),
										'grid'  => array(
											// One post
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/classic'
													),
												)
											),
											// Two posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/classic',
														'args' => array( 'image_position' => 'top' )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
												)
											),
											// Three posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/classic',
														'args' => array( 'image_position' => 'top' )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
												)
											),
											// Four posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/classic',
														'args' => array( 'image_position' => 'top' )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
												)
											),
											// Five posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/classic',
														'args' => array( 'image_position' => 'top' )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
												)
											),
											// Six posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/classic',
														'args' => array( 'image_position' => 'top' )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
												)
											),
											// Seven posts
											array(
												'grid-layout' => array(
													array(
														'template' => 'default/classic',
														'args' => array( 'image_position' => 'top' )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
													array(
														'template' => 'list/with_image',
														'args' => array( 'image_position' => 'left', 'image_width' => 33 )
													),
												)
											),
										)
									)
								),
							),
						),
			'button' => array(
							'title' => __('Button', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'bordered' => esc_html__('Bordered', 'trx_addons'),
								'simple' => esc_html__('Simple', 'trx_addons')
							),
							// Always enabled!!!
							'std' => 1,
							'hidden' => false
						),
			'content' => array(
							'title' => __('Content', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
							),
							// Always enabled!!!
							'std' => 1,
							'hidden' => true
						),
			'countdown' => array(
							'title' => __('Countdown', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'circle' => esc_html__('Circle', 'trx_addons')
							)
						),
			'cover' => array(
							'title' => __('Cover or fixed link', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
							)
						),
			'form' => array(
							'title' => __('Forms', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'modern' => esc_html__('Modern', 'trx_addons'),
								'detailed' => esc_html__('Detailed', 'trx_addons')
							),
							// Always enabled!!!
							'std' => 1,
							'hidden' => false
						),
			'googlemap' => array(
							'title' => __('Google map', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'detailed' => esc_html__('Detailed', 'trx_addons')
							),
							// Always enabled!!!
							'std' => 1,
							'hidden' => false
						),
			'hotspot' => array(
							'title' => __('Hotspot', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons')
							)
						),
			'hscroll' => array(
							'title' => __('Horizontal Scroll', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons')
							)
						),
			'icompare' => array(
							'title' => __('Images Compare', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons')
							)
						),
			'icons' => array(
							'title' => __('Icons', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'modern' => esc_html__('Modern', 'trx_addons')
							)
						),
			'osmap' => array(
							'title' => __('OpenStreet map', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'detailed' => esc_html__('Detailed', 'trx_addons')
							),
						),
			'price' => array(
							'title' => __('Price block', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
							)
						),
			'promo' => array(
							'title' => __('Promo', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'modern' => esc_html__('Modern', 'trx_addons'),
								'blockquote' => esc_html__('Blockquote', 'trx_addons'),
							)
						),
			'skills' => array(
							'title' => __('Skills', 'trx_addons'),
							'layouts_sc' => array(
								'pie' => esc_html__('Pie', 'trx_addons'),
								'counter' => esc_html__('Counter', 'trx_addons'),
							)
						),
			'socials' => array(
							'title' => __('Socials', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Only icons', 'trx_addons'),
								'names' => esc_html__('Only names', 'trx_addons'),
								'icons_names' => esc_html__('Icon + name', 'trx_addons'),
							),
							// Always enabled!!!
							'std' => 1,
							'hidden' => false
						),
			'squeeze' => array(
							'title' => __('Squeeze images with titles', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
							)
						),
			'supertitle' => array(
							'title' => __('Super title', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
							)
						),
			'switcher' => array(
							'title' => __('Switch two (and more) blocks', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'tabs' => esc_html__('Tabs', 'trx_addons'),
							)
						),
			'table' => array(
							'title' => __('Table', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
							)
						),
			'title' => array(
							'title' => __('Title', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'shadow' => esc_html__('Shadow', 'trx_addons'),
								'accent' => esc_html__('Accent', 'trx_addons'),
								'gradient' => esc_html__('Gradient', 'trx_addons'),
							),
							// Always enabled!!!
							'std' => 1,
							'hidden' => false
						),
			'users' => array(
							'title' => __('Users list', 'trx_addons'),
							'layouts_sc' => array(
								'default' => esc_html__('Default', 'trx_addons'),
								'list' => esc_html__('List', 'trx_addons'),
							)
						),
			)
		);
	}
}

// Include files with shortcodes
if (!function_exists('trx_addons_sc_load')) {
	add_action( 'after_setup_theme', 'trx_addons_sc_load', 6 );
	function trx_addons_sc_load() {
		static $loaded = false;
		if ($loaded) return;
		$loaded = true;
		global $TRX_ADDONS_STORAGE;
		if (is_array($TRX_ADDONS_STORAGE['sc_list']) && count($TRX_ADDONS_STORAGE['sc_list']) > 0) {
			foreach ($TRX_ADDONS_STORAGE['sc_list'] as $sc=>$params) {
				if (trx_addons_components_is_allowed('sc', $sc)
					&& ($fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_SHORTCODES . "{$sc}/{$sc}.php")) != '') { 
					include_once $fdir;
					trx_addons_sc_is_loaded($sc, true);
				}
			}
		}
	}
}

// Return true if component is loaded
if (!function_exists('trx_addons_sc_is_loaded')) {
	function trx_addons_sc_is_loaded($slug, $set=-1) {
		return trx_addons_components_is_loaded('sc', $slug, $set);
	}
}

// Add 'Shortcodes' block in the ThemeREX Addons Components
if (!function_exists('trx_addons_sc_components')) {
	add_filter( 'trx_addons_filter_components_blocks', 'trx_addons_sc_components');
	function trx_addons_sc_components($blocks=array()) {
		$blocks['sc'] = __('Shortcodes', 'trx_addons');
		return $blocks;
	}
}

	
// Load required styles and scripts for the frontend
if ( !function_exists( 'trx_addons_sc_load_scripts_front' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_load_scripts_front', TRX_ADDONS_ENQUEUE_SCRIPTS_PRIORITY);
	function trx_addons_sc_load_scripts_front() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 'trx_addons-sc', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'shortcodes.css'), array(), null );
			wp_enqueue_script( 'trx_addons-sc', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'shortcodes.js'), array('jquery'), null, true );
		}
	}
}

// Load responsive styles for the frontend
if ( !function_exists( 'trx_addons_sc_load_responsive_styles' ) ) {
	add_action("wp_enqueue_scripts", 'trx_addons_sc_load_responsive_styles', TRX_ADDONS_ENQUEUE_RESPONSIVE_PRIORITY);
	function trx_addons_sc_load_responsive_styles() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_style( 
				'trx_addons-sc-responsive', 
				trx_addons_get_file_url(TRX_ADDONS_PLUGIN_SHORTCODES . 'shortcodes.responsive.css'), 
				array(), 
				null, 
				trx_addons_media_for_load_css_responsive( 'sc', 'xl' ) 
			);
		}
	}
}

// Merge shortcode's specific styles to the single stylesheet
if ( !function_exists( 'trx_addons_sc_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_merge_styles');
	function trx_addons_sc_merge_styles($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'shortcodes.css' ] = true;
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_sc_merge_styles_responsive');
	function trx_addons_sc_merge_styles_responsive($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'shortcodes.responsive.css' ] = true;
		return $list;
	}
}

	
// Merge shortcode's specific scripts to the single file
if ( !function_exists( 'trx_addons_sc_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_sc_merge_scripts');
	function trx_addons_sc_merge_scripts($list) {
		$list[ TRX_ADDONS_PLUGIN_SHORTCODES . 'shortcodes.js' ] = true;
		return $list;
	}
}


// Add common atts like 'id', 'cls'', 'css', title params, etc. to the shortcode's atts
if (!function_exists('trx_addons_sc_common_atts')) {
	function trx_addons_sc_common_atts($common, $atts) {
		if (!is_array($common)) {
			$common = explode(',', $common);
		}
		if ( in_array('id', $common) ) {
			$atts = array_merge(array(
				"id" => "",
				"class" => "",
				"className" => "",	// Alter name for 'class' in Gutenberg
				"css" => ""
			), $atts);
		}
		if ( in_array('title', $common) ) {
			$atts = array_merge(array(
				"title" => "",
				"title_align" => "left",
				"title_style" => "default",
				"title_tag" => '',
				"title_color" => '',
				"title_color2" => '',
				"gradient_fill" => 'block',
				"gradient_direction" => '',
				"title_border_color" => '',
				"title_border_width" => '',
				"title_bg_image" => '',
				"title2" => '',
				"title2_color" => '',
				"title2_color2" => '',
				"gradient_fill2" => 'block',
				"gradient_direction2" => '',
				"title2_border_color" => '',
				"title2_border_width" => '',
				"title2_bg_image" => '',
				"subtitle" => "",
				"subtitle_align" => "none",
				"subtitle_position" => trx_addons_get_setting('subtitle_above_title') ? 'above' : 'below',
				"subtitle_color" => "",
				"description" => "",
				"description_color" => "",
				"link" => '',
				"link_style" => 'default',
				"link_size" => 'normal',
				"link_image" => '',
				"link_text" => esc_html__('Learn more', 'trx_addons'),
				"new_window" => 0,
				"typed" => 0,
				"typed_strings" => '',
				"typed_loop" => 1,
				"typed_cursor" => 1,
				"typed_cursor_char" => '|',
				"typed_color" => '',
				"typed_speed" => 6,
				"typed_delay" => 1
			), $atts);
		}
		if ( in_array('slider', $common) ) {
			$atts = array_merge(array(
				"slider" => 0,
				"slider_effect" => "slide",
				"slider_pagination" => "none",
				"slider_pagination_type" => "bullets",
				"slider_pagination_thumbs" => 0,
				"slider_controls" => "none",
				"slides_space" => 0,
				"slides_centered" => 0,
				"slides_overflow" => 0,
				"slider_mouse_wheel" => 0,
				"slider_autoplay" => 1,
				"slider_loop" => 1,
				"slider_free_mode" => 0,
			), $atts);
		}
		if ( in_array('query', $common) ) {
			$atts = array_merge(array(
				"cat" => "",
				"columns" => "",
				"columns_widescreen" => "",
				"columns_laptop" => "",
				"columns_tablet_extra" => "",
				"columns_tablet" => "",
				"columns_mobile_extra" => "",
				"columns_mobile" => "",
				"count" => 3,
				"offset" => 0,
				"orderby" => '',
				"order" => '',
				"ids" => '',
			), $atts);
		}
		if ( in_array('icon', $common) ) {
			$atts = array_merge(array(
				"icon_type" => '',
				"icon_fontawesome" => "",
				"icon_openiconic" => "",
				"icon_typicons" => "",
				"icon_entypo" => "",
				"icon_linecons" => "",
				"icon" => "",
			), $atts);
		}
		if ( in_array('hide', $common) ) {
			$atts = array_merge(array(
				"hide_on_wide" => "0",
				"hide_on_desktop" => "0",
				"hide_on_notebook" => "0",
				"hide_on_tablet" => "0",
				"hide_on_mobile" => "0",
				"hide_on_frontpage" => "0",
				"hide_on_singular" => "0",
				"hide_on_other" => "0",
			), $atts);
		}
		return apply_filters( 'trx_addons_filter_sc_common_atts', $atts, $common );
	}
}


// Prepare Id, custom CSS and other parameters in the shortcode's atts
if (!function_exists('trx_addons_sc_prepare_atts')) {
	function trx_addons_sc_prepare_atts($sc, $atts, $defa) {
		// Push shortcode name to the stack
		trx_addons_sc_stack_push($sc);
		// Add 'xxx_extra', 'xxx_object', '__globals__' to the default params (its original Elementor's params)
		if ( is_array( $atts ) ) {
			foreach( $atts as $k => $v ) {
				if ( ! isset( $defa[ $k ] ) && ( substr($k, -6) == '_extra' || substr($k, -7) == '_object' || substr($k, -11) == '__globals__' ) ) {
					$defa[$k] = $v;
				}
			}
		}
		// Merge atts with default values
		$atts = trx_addons_html_decode( shortcode_atts( apply_filters( 'trx_addons_sc_atts', $defa, $sc ), $atts ) );
		// Generate id (if empty)
		if ( empty($atts['id']) && apply_filters('trx_addons_filter_sc_generate_id', false) ) {
			$atts['id'] = trx_addons_generate_id( str_replace('trx_', '', $sc) . '_' );
		}
		// Copy className to class
		if (!empty($atts['className'])) {
			$atts['class'] = (!empty($atts['class']) ? $atts['class'] . ' ' : '') . $atts['className'];
		}
 		return apply_filters('trx_addons_filter_sc_prepare_atts', $atts, $sc);
	}
}


// Prepare atts before output a shortcode layout:
// if pagination is 'rand' - get all posts IDs and put its to the 'ids' argument
if ( ! function_exists('trx_addons_sc_prepare_atts_before_output')) {
	add_filter( 'trx_addons_filter_sc_prepare_atts_before_output', 'trx_addons_sc_prepare_atts_before_output', 10, 4 );
	function trx_addons_sc_prepare_atts_before_output( $args, $query_args, $query, $sc = '' ) {
		if ( $args['orderby'] == 'rand'
			&& ( isset( $args['pagination'] ) && ! trx_addons_is_off( $args['pagination'] ) )
			&& $args['count'] > 0
			&& $query->found_posts > $args['count']
			&& $query->found_posts < apply_filters( 'trx_addons_filter_max_posts_for_random_pagination', 100 )
		) {
			$qa = $query_args;
			$qa['posts_per_page'] = -1;
			$qa['fields'] = 'ids';
			$q = new WP_Query( $qa );
			if ( ! empty( $q->posts ) && is_array( $q->posts ) && ! empty( $query->posts ) && is_array( $query->posts ) ) {
				$posts = array_flip( $q->posts );
				$ids = array();
				foreach( $query->posts as $post ) {
					$ids[] = $post->ID;
					if ( isset( $posts[ $post->ID ] ) ) {
						unset( $posts[ $post->ID ] );
					}
				}
				$args['ids'] = join(',', array_merge( $ids, array_keys( $posts ) ) );
				$args['orderby'] = 'none';
			}
		}
		return $args;
	}
}


// After all handlers are finished - pop sc from the stack
if (!function_exists('trx_addons_sc_output_finish')) {
	add_filter('trx_addons_sc_output', 'trx_addons_sc_output_finish', 9999, 4);
	function trx_addons_sc_output_finish($output='', $sc='', $atts='', $content='') {
		trx_addons_sc_stack_pop($sc);
		return $output;
	}
}

// Push shortcode name to the stack
if (!function_exists('trx_addons_sc_stack_push')) {
	function trx_addons_sc_stack_push($sc) {
		global $TRX_ADDONS_STORAGE;
		array_push($TRX_ADDONS_STORAGE['sc_stack'], $sc);
	}
}

// Pop shortcode name from the stack
if (!function_exists('trx_addons_sc_stack_pop')) {
	function trx_addons_sc_stack_pop() {
		global $TRX_ADDONS_STORAGE;
		return array_pop($TRX_ADDONS_STORAGE['sc_stack']);
	}
}

// Check if shortcode name is in the stack
if (!function_exists('trx_addons_sc_stack_check')) {
	function trx_addons_sc_stack_check($sc=false, $last=false) {
		global $TRX_ADDONS_STORAGE;
		return is_array( $TRX_ADDONS_STORAGE['sc_stack'] )
				? ( ! empty( $sc )
					? ( $last
						? ( count( $TRX_ADDONS_STORAGE['sc_stack'] ) > 0
							? $TRX_ADDONS_STORAGE['sc_stack'][count( $TRX_ADDONS_STORAGE['sc_stack'] ) - 1]
							: false
							)
						: in_array( $sc, $TRX_ADDONS_STORAGE['sc_stack'] )
						)
					: count( $TRX_ADDONS_STORAGE['sc_stack'] ) > 0
					)
				: false;
	}
}

// Make shortcode string
if ( !function_exists('trx_addons_sc_make_string') ) {
	function trx_addons_sc_make_string($sc, $atts) {
		$str = '';
		foreach ($atts as $k => $v) {
			$str .= sprintf( ' %s="%s"', esc_attr($k), esc_attr($v) );
		}
		return "[{$sc}{$str}]";
	}
}


// Check if shortcode is present in the content of the current page
if ( ! function_exists( 'trx_addons_sc_check_in_content')) {
	function trx_addons_sc_check_in_content( $args, $post_id=-1 ) {
		static $posts = array();
		$sc_wrappers = array(
			'sc' => array( '[', ']' ),
			'gutenberg' => array( '<!-- ', ' -->' )
		);
		if ( $post_id < 0 ) {
			$post_id = trx_addons_get_the_ID();
		}
		if ( $post_id > 0 ) {
			if ( ! isset( $posts[ $post_id ] ) ) {
				$posts[ $post_id ] = array(
					'post'  => get_post( $post_id ),
					'meta'  => array(),
					'check' => array()
				);
			}
			// Apply filters to allow theme add/remove specific entries to be checked
			$args = apply_filters( 'trx_addons_filter_sc_check_in_content_args', $args, $post_id );
			// If not checked yet
			if ( ! isset( $posts[ $post_id ]['check'][ $args['sc'] ] ) ) {
				$posts[ $post_id ]['check'][ $args['sc'] ] = false;
				// Check entries
				if ( ! empty( $args['entries'] ) ) {
					foreach( $args['entries'] as $entry ) {
						// Check in the content for shortcodes and gutenberg blocks
						if ( empty( $entry['type'] ) || ! in_array( $entry['type'], array( 'elm', 'elementor' ) ) ) {
							$content = ! empty( $posts[ $post_id ]['post']->post_content ) ? $posts[ $post_id ]['post']->post_content : '';
							if ( ! empty( $content ) ) {
								$sc    = $entry['sc'];
								$type  = str_replace(
											array( 'gb', 'elm' ),
											array( 'gutenberg', 'elementor' ),
											empty( $entry['type'] ) ? 'sc' : $entry['type']
										);
								$start = isset( $sc_wrappers[ $type ] ) ? $sc_wrappers[ $type ][0] : '';
								$end   = isset( $sc_wrappers[ $type ] ) ? $sc_wrappers[ $type ][1] : '';
								$param = ! empty( $entry['param'] ) ? $entry['param'] : '';
								$posts[ $post_id ]['check'][ $args['sc'] ] = preg_match( "#" . ( ! empty( $start ) ? "\\{$start}" : '' ) . "{$sc} " . ( ! empty( $param ) ? "[^\\" . substr( $end, -1 ) . "]*{$param}" : '' ) . "#", $content );
							}
						// Check in the meta for Elementor
						} else if ( trx_addons_exists_elementor() ) {
							$key = '_elementor_data';
							if ( ! isset( $posts[ $post_id ]['meta'][ $key ] ) ) {
								$posts[ $post_id ]['meta'][ $key ] = get_post_meta( $post_id, $key, true );
							}
							if ( is_string( $posts[ $post_id ]['meta'][ $key ] ) ) {
								$sc    = $entry['sc'];
								$param = ! empty( $entry['param'] ) ? $entry['param'] : '';
								$posts[ $post_id ]['check'][ $args['sc'] ] = strpos( $posts[ $post_id ]['meta'][ $key ], $sc ) !== false
																			&& ( empty( $param ) || strpos( $posts[ $post_id ]['meta'][ $key ], $param ) !== false );
							}
						}
						if ( $posts[ $post_id ]['check'][ $args['sc'] ] ) {
							break;
						}
					}
				}
			}
			return $posts[ $post_id ]['check'][ $args['sc'] ];
		} else {
			return false;
		}
	}
}



// Shortcodes parts
//---------------------------------------

// Enqueue iconed fonts
if (!function_exists('trx_addons_load_icons')) {
	function trx_addons_load_icons($list='') {
		if (!empty($list) && function_exists('vc_icon_element_fonts_enqueue')) {
			$list = explode(',', $list);
			foreach ($list as $icon_type)
				vc_icon_element_fonts_enqueue($icon_type);
		}
	}
}

// Display title, subtitle and description for some shortcodes
if (!function_exists('trx_addons_sc_show_titles')) {
	function trx_addons_sc_show_titles($sc, $args, $size='') {
		trx_addons_get_template_part('templates/tpl.sc_titles.php',
										'trx_addons_args_sc_show_titles',
										compact('sc', 'args', 'size')
									);
	}
}

// Add a class with a custom color (if specified) to the subtitle and description
// (if a template 'templates/tpl.sc_titles.php' copied to the theme/skin)
if ( ! function_exists( 'trx_addons_sc_add_color_to_subtitle' ) ) {
	add_filter( 'trx_addons_filter_sc_item_subtitle_class', 'trx_addons_sc_add_color_to_subtitle', 10, 3 );
	add_filter( 'trx_addons_filter_sc_item_description_class', 'trx_addons_sc_add_color_to_subtitle', 10, 3 );
	function trx_addons_sc_add_color_to_subtitle( $classes, $sc = '', $sc_args = array() ) {
		$slug = current_filter() == 'trx_addons_filter_sc_item_subtitle_class' ? 'subtitle' : 'description';
		$short_slug = current_filter() == 'trx_addons_filter_sc_item_subtitle_class' ? 'subtitle' : 'descr';
		if ( strpos( $classes, "sc_item_{$short_slug}_with_custom_color" ) === false ) {
			$q_args = get_query_var('trx_addons_args_sc_show_titles');
			$color = ! empty( $sc_args["{$slug}_color"] )
								? $sc_args["{$slug}_color"]
								: ( ! empty( $q_args['args']["{$slug}_color"] )
									? $q_args['args']["{$slug}_color"]
									: ''
									);
			if ( ! empty( $color ) ) {
				$classes .= " sc_item_{$short_slug}_with_custom_color "
							. trx_addons_add_inline_css_class( 'color: ' . esc_attr( $color ) . ( trx_addons_is_preview('gb') ? ' !important' : '' ) . ';' );
			}
		}
		return $classes;
	}
}

// Return tabs for the filters header for some shortcodes
// Attention! Array $args passed by reference because it can be modified in this function
if (!function_exists('trx_addons_sc_get_filters_tabs')) {
	function trx_addons_sc_get_filters_tabs($sc, &$args) {
		$tabs = array();
		if ( !empty($args['show_filters']) ) {
			if (!empty($args['filters_ids']) && count($args['filters_ids']) > 0) {
				foreach ($args['filters_ids'] as $ids_filter) {
					$term = get_term_by( is_numeric( $ids_filter ) && (int) $ids_filter > 0 ? 'id' : 'name', $ids_filter, $args['filters_taxonomy'] );
					if ($term) {
						$tabs[$term->term_id] = apply_filters('trx_addons_extended_taxonomy_name', $term->name, $term);
					}
				}
			} else {
				$only_children = $args['filters_taxonomy'] == $args['taxonomy'];	// && !empty($args['cat'])
				$tabs = array();
				$cats = is_array( $args['cat'] ) ? $args['cat'] : explode( ',', $args['cat'] );
				foreach( $cats as $cat ) {
					$tabs = trx_addons_array_merge(
								$tabs,
								$args['filters_taxonomy'] == 'category' && !$only_children
									? trx_addons_get_list_categories()
									: trx_addons_get_list_terms(false, $args['filters_taxonomy'], $only_children ? array('parent' => $cat) : array())
							);
				}
			}

			if (count($tabs) > 0) {
				if (empty($args['filters_active'])) {
					$args['filters_active'] = !empty($args['filters_all']) ? 0 : trx_addons_array_get_first($tabs);
				}
			}
		}
		return $tabs;
	}
}

// Display filters header (title, subtitle and tabs) for some shortcodes
if (!function_exists('trx_addons_sc_show_filters')) {
	function trx_addons_sc_show_filters($sc, $args, $tabs) {
		trx_addons_get_template_part('templates/tpl.sc_filters.php',
										'trx_addons_args_sc_show_filters',
										compact('sc', 'args', 'tabs')
									);
	}
}

// Display pagination buttons for some shortcodes
if (!function_exists('trx_addons_sc_show_pagination')) {
	function trx_addons_sc_show_pagination($sc, $args, $query) {
		trx_addons_get_template_part('templates/tpl.sc_pagination.php',
										'trx_addons_args_sc_pagination',
										compact('sc', 'args', 'query')
									);
	}
}

// Display link button or image for some shortcodes
if (!function_exists('trx_addons_sc_show_links')) {
	function trx_addons_sc_show_links($sc, $args) {
		trx_addons_get_template_part('templates/tpl.sc_links.php',
										'trx_addons_args_sc_show_links',
										compact('sc', 'args')
									);
	}
}

// Add a parameter 'size' to the link args if it's not present
// (if a template 'templates/tpl.sc_links.php' copied to the theme/skin)
if ( ! function_exists( 'trx_addons_sc_add_size_to_links_args' ) ) {
	add_filter( 'trx_addons_filter_sc_item_button_args', 'trx_addons_sc_add_size_to_links_args', 10, 3 );
	function trx_addons_sc_add_size_to_links_args( $args, $sc = '', $sc_args = array() ) {
		if ( empty( $args['size'] ) ) {
			$q_args = get_query_var('trx_addons_args_sc_show_links');
			$args['size'] = ! empty( $sc_args['link_size'] )
								? $sc_args['link_size']
								: ( ! empty( $q_args['args']['link_size'] )
									? $q_args['args']['link_size']
									: 'normal'
									);
		}
		return $args;
	}
}

// Display additional attributes for some shortcodes
if (!function_exists('trx_addons_sc_show_attributes')) {
	function trx_addons_sc_show_attributes($sc, $args, $area) {
		do_action( 'trx_addons_action_sc_show_attributes', $sc, $args, $area );
	}
}

// Show post meta block: post date, author, categories, views, comments, likes, rating, etc.
if ( !function_exists('trx_addons_sc_show_post_meta') ) {
	function trx_addons_sc_show_post_meta($sc, $args=array()) {
		$args = array_merge(array(
			'tag' => 'div',
			'components' => '',	//categories,tags,date,author,views,comments,likes,rating,share,edit
			'share_type' => 'drop',
			'seo' => false,
			'date_format' => '',
			'theme_specific' => true,
			'class' => '',
			'echo' => true
			), $args);
		if ( ( $meta = apply_filters( 'trx_addons_filter_post_meta', '', array_merge( $args, array( 'sc' => $sc, 'echo' => false ) ) ) ) != '' ) {
			if ( ! empty( $args['echo'] ) ) {
				trx_addons_show_layout($meta);
			} else {
				return trim( $meta );
			}
		} else {
			if ( empty( $args['echo'] ) ) {
				ob_start();
			}
			trx_addons_get_template_part( 'templates/tpl.sc_post_meta.php',
											'trx_addons_args_sc_show_post_meta',
											compact( 'sc', 'args' )
										);
			if ( empty( $args['echo'] ) ) {
				$meta = ob_get_contents();
				ob_end_clean();
				return $meta;
			}
		}
	}
}

// Display begin of the slider layout for some shortcodes
if (!function_exists('trx_addons_sc_show_slider_wrap_start')) {
	function trx_addons_sc_show_slider_wrap_start($sc, $args) {
		trx_addons_get_template_part('templates/tpl.sc_slider_start.php',
										'trx_addons_args_sc_show_slider_wrap',
										apply_filters('trx_addons_filter_sc_show_slider_args', compact('sc', 'args'))
									);
	}
}

// Display end of the slider layout for some shortcodes
if (!function_exists('trx_addons_sc_show_slider_wrap_end')) {
	function trx_addons_sc_show_slider_wrap_end($sc, $args) {
		trx_addons_get_template_part('templates/tpl.sc_slider_end.php',
										'trx_addons_args_sc_show_slider_wrap', 
										apply_filters('trx_addons_filter_sc_show_slider_args', compact('sc', 'args'))
									);
	}
}


// AJAX Pagination in the shortcodes
//------------------------------------------
if ( !function_exists( 'trx_addons_ajax_sc_pagination' ) ) {
	add_action('wp_ajax_trx_addons_item_pagination',		'trx_addons_ajax_sc_pagination');
	add_action('wp_ajax_nopriv_trx_addons_item_pagination',	'trx_addons_ajax_sc_pagination');
	function trx_addons_ajax_sc_pagination() {

		trx_addons_verify_nonce();
	
		$response = array('error'=>'', 'data'=>'', 'css' => '');

		$params = trx_addons_unserialize(wp_unslash($_POST['params']));
		$params['page'] = $_POST['page'];
		if (!empty($_POST['filters_active'])) {
			$params['filters_active'] = $_POST['filters_active'];
		}

		$func_name = 'trx_addons_' . $params['sc'];

		if ( (
				trx_addons_components_is_allowed('sc', str_replace('sc_', '', $params['sc']))
				||
				trx_addons_components_is_allowed('cpt', str_replace('sc_', '', $params['sc']))
				||
				trx_addons_components_is_allowed('widgets', str_replace('sc_widget_', '', $params['sc']))
			)
			&& function_exists($func_name)
		) {
			$response['data'] = call_user_func($func_name, $params);
			$response['css'] = apply_filters('trx_addons_filter_inline_css', trx_addons_get_inline_css());
		} else {
			$response['error'] = esc_html__('Unknown shortcode!', 'trx_addons');
		}

		trx_addons_ajax_response( $response );
	}
}


// AJAX incremental search
//-------------------------------------------------------
if ( !function_exists( 'trx_addons_callback_ajax_sc_posts_search' ) ) {
	add_action('wp_ajax_ajax_sc_posts_search', 'trx_addons_callback_ajax_sc_posts_search');
	function trx_addons_callback_ajax_sc_posts_search() {

		trx_addons_verify_nonce();

		$response = array( 'results' => array(), 'pagination' => array( 'more' => false ) );

		$s = trx_addons_get_value_gp('term');
	
		if ( ! empty($s) ) {
			$post_type = trx_addons_get_value_gp('post_type');
			$taxonomy  = trx_addons_get_value_gp('taxonomy');
			$terms     = trx_addons_get_value_gp('terms');
			if ( is_array($terms) ) {
				$terms = join( ',', $terms );
			}
			$terms = str_replace(' ', '', $terms);
			
			$args = array(
				'post_status' => 'publish',
				'post_type' => ! empty($post_type) ? $post_type : 'any',
				'orderby' => 'title',
				'order' => 'asc', 
				'posts_per_page' => 10,
				'title_filter' => $s,
			);
			if ( ! empty($terms) ) {
				$args = trx_addons_query_add_posts_and_cats( $args, '', '', $terms, $taxonomy );
			}

			add_filter( 'posts_where', 'trx_addons_ajax_sc_posts_search_query_title_filter', 10, 2 );
			
			$args = apply_filters( 'trx_addons_filter_query_args', $args, 'sc_posts_search' );
			
			$query = new WP_Query( apply_filters( 'trx_addons_ajax_sc_posts_search_query', $args ) );
			
			remove_filter( 'posts_where', 'trx_addons_ajax_sc_posts_search_query_title_filter', 10, 2 );

			$post_number = 0;
			while ( $query->have_posts() ) { $query->the_post();
				$response['results'][] = array(
					'id' => get_the_ID(),
					'text' => get_the_title()
				);
			}
		}
		
		trx_addons_ajax_response( $response );
	}
}

// Add title to WHERE clause
if ( !function_exists( 'trx_addons_ajax_sc_posts_search_query_title_filter' ) ) {
	function trx_addons_ajax_sc_posts_search_query_title_filter( $where, $wp_query ) {
		global $wpdb;
		$title = $wp_query->get( 'title_filter' );
		if ( ! empty( $title )  ) {
			$where .= " AND {$wpdb->posts}.post_title LIKE '%" . esc_sql( like_escape( $title ) ) . "%'";
		}
		return $where;
	}
}



// Universal add parameters to our shortcodes
//--------------------------------------------
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'shortcodes-params.php';

// Add Gutenberg support
//--------------------------------------------
if ( trx_addons_exists_gutenberg() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_SHORTCODES . 'shortcodes-gutenberg.php';
}
