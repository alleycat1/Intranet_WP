<?php
/* Woocommerce support functions
------------------------------------------------------------------------------- */

// Theme init priorities:
// 1 - register filters, that add/remove lists items for the Theme Options
if ( ! function_exists( 'pubzinne_woocommerce_theme_setup1' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_woocommerce_theme_setup1', 1 );
	function pubzinne_woocommerce_theme_setup1() {

		// Theme-specific parameters for WooCommerce
		add_theme_support(
			'woocommerce', array(
				// Image width for thumbnails gallery
				'gallery_thumbnail_image_width' => 150,

				// Image width for the catalog images: 'thumbnail_image_width' => 300,
				// Attention! If you set this parameter - WooCommerce hide relative control from Customizer

				// Image width for the single product image: 'single_image_width' => 600,
				// Attention! If you set this parameter - WooCommerce hide relative control from Customizer
			)
		);

		// Next setting from the WooCommerce 3.0+ enable built-in image zoom on the single product page
		add_theme_support( 'wc-product-gallery-zoom' );

		// Next setting from the WooCommerce 3.0+ enable built-in image slider on the single product page
		add_theme_support( 'wc-product-gallery-slider' );

		// Next setting from the WooCommerce 3.0+ enable built-in image lightbox on the single product page
		add_theme_support( 'wc-product-gallery-lightbox' );

		// Disable WooCommerce ads
		add_filter( 'woocommerce_allow_marketplace_suggestions', '__return_false' );

		// Add new sidebar for WooCommerce
		add_filter( 'pubzinne_filter_list_sidebars', 'pubzinne_woocommerce_list_sidebars' );

		// Add 'product' to the list with supported post types
		add_filter( 'pubzinne_filter_list_posts_types', 'pubzinne_woocommerce_list_post_types' );

		// Detect if WooCommerce support 'Product Grid' feature
		$product_grid = pubzinne_exists_woocommerce() && function_exists( 'wc_get_theme_support' ) ? wc_get_theme_support( 'product_grid' ) : false;
		add_theme_support( 'wc-product-grid-enable', isset( $product_grid['min_columns'] ) && isset( $product_grid['max_columns'] ) );
	}
}

// Theme init priorities:
// 3 - add/remove Theme Options elements
if ( ! function_exists( 'pubzinne_woocommerce_theme_setup3' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_woocommerce_theme_setup3', 3 );
	function pubzinne_woocommerce_theme_setup3() {
		if ( pubzinne_exists_woocommerce() ) {

			// Section 'WooCommerce'
			pubzinne_storage_set_array_before(
				'options', 'fonts', array_merge(
					array(
						'shop'               => array(
							'title'      => esc_html__( 'Shop', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'Select theme-specific parameters to display the shop pages', 'pubzinne' ) ),
							'priority'   => 80,
							'expand_url' => esc_url( pubzinne_woocommerce_get_shop_page_link() ),
							'icon'       => 'icon-cart-2',
							'type'       => 'section',
						),

						'products_info_shop' => array(
							'title'  => esc_html__( 'Products list', 'pubzinne' ),
							'desc'   => '',
							'qsetup' => esc_html__( 'General', 'pubzinne' ),
							'type'   => 'info',
						),
						'shop_mode'          => array(
							'title'   => esc_html__( 'Shop style', 'pubzinne' ),
							'desc'    => wp_kses_data( __( 'Select style for the products list. Attention! If the visitor has already selected the list type at the top of the page - his choice is remembered and has priority over this option', 'pubzinne' ) ),
							'std'     => 'thumbs',
							'options' => array(
								'thumbs' => array(
												'title' => esc_html__( 'Grid', 'pubzinne' ),
												'icon'  => 'images/theme-options/shop/grid.png',
												),
								'list'   => array(
												'title' => esc_html__( 'List', 'pubzinne' ),
												'icon'  => 'images/theme-options/shop/list.png',
												),
							),
							'qsetup'  => esc_html__( 'General', 'pubzinne' ),
							'type'    => 'choice',
						),
					),
					! get_theme_support( 'wc-product-grid-enable' )
					? array(
						'posts_per_page_shop' => array(
							'title' => esc_html__( 'Products per page', 'pubzinne' ),
							'desc'  => wp_kses_data( __( 'How many products should be displayed on the shop page. If empty - use global value from the menu Settings - Reading', 'pubzinne' ) ),
							'std'   => '',
							'type'  => 'text',
						),
						'blog_columns_shop'   => array(
							'title'      => esc_html__( 'Grid columns', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many columns should be used for the shop products in the grid view (from 2 to 4)?', 'pubzinne' ) ),
							'dependency' => array(
								'shop_mode' => array( 'thumbs' ),
							),
							'std'        => 2,
							'options'    => pubzinne_get_list_range( 2, 4 ),
							'type'       => 'select',
						),
					)
					: array(),
					array(
						'blog_animation_shop' => array(
							'title' => esc_html__( 'Products animation on the shop page', 'pubzinne' ),
							'desc' => wp_kses_data( __( 'Select animation to show products on the shop page. Attention! Do not use any animation on pages with the "wheel to the anchor" behaviour!', 'pubzinne' ) ),
							'std' => 'none',
							'options' => array(),
							'type' => 'select',
						),
						'shop_hover'              => array(
							'title'   => esc_html__( 'Hover style', 'pubzinne' ),
							'desc'    => wp_kses_data( __( 'Hover style on the products in the shop archive', 'pubzinne' ) ),
							'std'     => 'shop_buttons',
							'options' => apply_filters(
								'pubzinne_filter_shop_hover',
								array(
									'none'         => esc_html__( 'None', 'pubzinne' ),
									'shop'         => esc_html__( 'Icons', 'pubzinne' ),
									'shop_buttons' => esc_html__( 'Buttons', 'pubzinne' ),
								)
							),
							'qsetup'  => esc_html__( 'General', 'pubzinne' ),
							'type'    => 'select',
						),
						'shop_pagination'         => array(
							'title'   => esc_html__( 'Pagination style', 'pubzinne' ),
							'desc'    => wp_kses_data( __( 'Pagination style in the shop archive', 'pubzinne' ) ),
							'std'     => 'numbers',
							'options' => apply_filters(
								'pubzinne_filter_shop_pagination',
								array(
									'pages'    => array(
														'title' => esc_html__( 'Page numbers', 'pubzinne' ),
														'icon'  => 'images/theme-options/pagination/page-numbers.png',
														),
									'more'     => array(
														'title' => esc_html__( 'Load more', 'pubzinne' ),
														'icon'  => 'images/theme-options/pagination/load-more.png',
														),
									'infinite' => array(
														'title' => esc_html__( 'Infinite scroll', 'pubzinne' ),
														'icon'  => 'images/theme-options/pagination/infinite-scroll.png',
														),
								)
							),
							'qsetup'  => esc_html__( 'General', 'pubzinne' ),
							'type'    => 'choice',
						),

						'single_info_shop'        => array(
							'title' => esc_html__( 'Single product', 'pubzinne' ),
							'desc'  => '',
							'type'  => 'info',
						),
						'single_product_layout'      => array(
							'title'      => esc_html__( 'Layout of the single product', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'Select layout of the single products page', 'pubzinne' ) ),
							'std'        => 'default',
							'override' => array(
								'mode'    => 'product',
								'section' => esc_html__( 'Content', 'pubzinne' ),
							),
							'options'    => apply_filters(
															'pubzinne_filter_woocommerce_single_product_layouts',
															array(
																'default'   => esc_html__( 'Default', 'pubzinne' ),
																'stretched' => esc_html__( 'Stretched', 'pubzinne' ),
															)
														),
							'type'       => 'select',
						),
						'show_related_posts_shop' => array(
							'title'  => esc_html__( 'Show related products', 'pubzinne' ),
							'desc'   => wp_kses_data( __( "Show section 'Related products' on the single product page", 'pubzinne' ) ),
							'std'    => 1,
							'type'   => 'switch',
						),
						'related_posts_shop'      => array(
							'title'      => esc_html__( 'Related products', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many related products should be displayed on the single product page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_shop' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 9 ),
							'type'       => 'select',
						),
						'related_columns_shop'    => array(
							'title'      => esc_html__( 'Related columns', 'pubzinne' ),
							'desc'       => wp_kses_data( __( 'How many columns should be used to output related products on the single product page?', 'pubzinne' ) ),
							'dependency' => array(
								'show_related_posts_shop' => array( 1 ),
							),
							'std'        => 3,
							'options'    => pubzinne_get_list_range( 1, 4 ),
							'type'       => 'select',
						),
					),
					pubzinne_options_get_list_cpt_options( 'shop' )
				)
			);
			// Set 'WooCommerce Sidebar' as default for all shop pages
			pubzinne_storage_set_array2( 'options', 'sidebar_widgets_shop', 'std', 'woocommerce_widgets' );
		}
	}
}


// Move section 'Shop' inside the section 'WooCommerce' in the Customizer (if WooCommerce 3.3+ is installed)
if ( ! function_exists( 'pubzinne_woocommerce_customizer_register_controls' ) ) {
	add_action( 'customize_register', 'pubzinne_woocommerce_customizer_register_controls', 100 );
	function pubzinne_woocommerce_customizer_register_controls( $wp_customize ) {
		if ( pubzinne_exists_woocommerce() ) {
			$panel = $wp_customize->get_panel( 'woocommerce' );
			$sec   = $wp_customize->get_section( 'shop' );
			if ( is_object( $panel ) && is_object( $sec ) ) {
				$sec->panel    = 'woocommerce';
				$sec->title    = esc_html__( 'Theme-specific options', 'pubzinne' );
				$sec->priority = 100;
			}
		}
	}
}


// Set theme-specific default columns number in the new WooCommerce after switch theme
if ( ! function_exists( 'pubzinne_woocommerce_action_switch_theme' ) ) {
	add_action( 'after_switch_theme', 'pubzinne_woocommerce_action_switch_theme' );
	function pubzinne_woocommerce_action_switch_theme() {
		if ( pubzinne_exists_woocommerce() ) {
			update_option( 'woocommerce_catalog_columns', apply_filters( 'pubzinne_filter_woocommerce_columns', 3 ) );
		}
	}
}

// Set theme-specific default columns number in the new WooCommerce after plugin activation
if ( ! function_exists( 'pubzinne_woocommerce_action_activated_plugin' ) ) {
	add_action( 'activated_plugin', 'pubzinne_woocommerce_action_activated_plugin', 10, 2 );
	function pubzinne_woocommerce_action_activated_plugin( $plugin, $network_activation ) {
		if ( 'woocommerce/woocommerce.php' == $plugin ) {
			update_option( 'woocommerce_catalog_columns', apply_filters( 'pubzinne_filter_woocommerce_columns', 3 ) );
		}
	}
}



// Check if meta box is allowed
if ( ! function_exists( 'pubzinne_woocommerce_allow_override_options' ) ) {
	if ( ! PUBZINNE_THEME_FREE ) {
		add_filter( 'pubzinne_filter_allow_override_options', 'pubzinne_woocommerce_allow_override_options', 10, 2);
	}
	function pubzinne_woocommerce_allow_override_options( $allow, $post_type ) {
		return $allow || 'product' == $post_type;
	}
}


// Add section 'Products' to the Front Page option
if ( ! function_exists( 'pubzinne_woocommerce_front_page_options' ) ) {
	if ( ! PUBZINNE_THEME_FREE ) {
		add_filter( 'pubzinne_filter_front_page_options', 'pubzinne_woocommerce_front_page_options' );
	}
	function pubzinne_woocommerce_front_page_options( $options ) {
		if ( pubzinne_exists_woocommerce() ) {

			$options['front_page_sections']['std']    .= ( ! empty( $options['front_page_sections']['std'] ) ? '|' : '' ) . 'woocommerce=1';
			$options['front_page_sections']['options'] = array_merge(
				$options['front_page_sections']['options'],
				array(
					'woocommerce' => esc_html__( 'Products', 'pubzinne' ),
				)
			);
			$options                                   = array_merge(
				$options, array(

					// Front Page Sections - WooCommerce
					'front_page_woocommerce'               => array(
						'title'    => esc_html__( 'Products', 'pubzinne' ),
						'desc'     => '',
						'priority' => 200,
						'type'     => 'section',
					),
					'front_page_woocommerce_layout_info'   => array(
						'title' => esc_html__( 'Layout', 'pubzinne' ),
						'desc'  => '',
						'type'  => 'info',
					),
					'front_page_woocommerce_fullheight'    => array(
						'title'   => esc_html__( 'Full height', 'pubzinne' ),
						'desc'    => wp_kses_data( __( 'Stretch this section to the window height', 'pubzinne' ) ),
						'std'     => 0,
						'refresh' => false,
						'type'    => 'switch',
					),
					'front_page_woocommerce_stack'         => array(
						'title'      => esc_html__( 'Stack this section', 'pubzinne' ),
						'desc'       => wp_kses_data( __( 'Add the behavior of "a stack" for this section to fix it when you scroll to the top of the screen.', 'pubzinne' ) ),
						'std'        => 0,
						'refresh'    => false,
						'dependency' => array(
							'front_page_woocommerce_fullheight' => array( 1 ),
						),
						'type'       => 'switch',
					),
					'front_page_woocommerce_paddings'      => array(
						'title'   => esc_html__( 'Paddings', 'pubzinne' ),
						'desc'    => wp_kses_data( __( 'Select paddings inside this section', 'pubzinne' ) ),
						'std'     => 'medium',
						'options' => pubzinne_get_list_paddings(),
						'refresh' => false,
						'type'    => 'choice',
					),
					'front_page_woocommerce_heading_info'  => array(
						'title' => esc_html__( 'Title', 'pubzinne' ),
						'desc'  => '',
						'type'  => 'info',
					),
					'front_page_woocommerce_caption'       => array(
						'title'   => esc_html__( 'Section title', 'pubzinne' ),
						'desc'    => '',
						'refresh' => false, // To refresh part of the page: '.front_page_section_woocommerce .front_page_section_woocommerce_caption',
						'std'     => wp_kses_data( __( 'This text can be changed in the section "Products"', 'pubzinne' ) ),
						'type'    => 'text',
					),
					'front_page_woocommerce_description'   => array(
						'title'   => esc_html__( 'Description', 'pubzinne' ),
						'desc'    => wp_kses_data( __( "Short description after the section's title", 'pubzinne' ) ),
						'refresh' => false, // To refresh part of the page: '.front_page_section_woocommerce .front_page_section_woocommerce_description',
						'std'     => wp_kses_data( __( 'This text can be changed in the section "Products"', 'pubzinne' ) ),
						'type'    => 'textarea',
					),
					'front_page_woocommerce_products_info' => array(
						'title' => esc_html__( 'Products parameters', 'pubzinne' ),
						'desc'  => '',
						'type'  => 'info',
					),
					'front_page_woocommerce_products'      => array(
						'title'   => esc_html__( 'Type of the products', 'pubzinne' ),
						'desc'    => '',
						'std'     => 'products',
						'options' => array(
							'recent_products'       => esc_html__( 'Recent products', 'pubzinne' ),
							'featured_products'     => esc_html__( 'Featured products', 'pubzinne' ),
							'top_rated_products'    => esc_html__( 'Top rated products', 'pubzinne' ),
							'sale_products'         => esc_html__( 'Sale products', 'pubzinne' ),
							'best_selling_products' => esc_html__( 'Best selling products', 'pubzinne' ),
							'product_category'      => esc_html__( 'Products from categories', 'pubzinne' ),
							'products'              => esc_html__( 'Products by IDs', 'pubzinne' ),
						),
						'type'    => 'select',
					),
					'front_page_woocommerce_products_categories' => array(
						'title'      => esc_html__( 'Categories', 'pubzinne' ),
						'desc'       => esc_html__( 'Comma separated category slugs. Used only with "Products from categories"', 'pubzinne' ),
						'dependency' => array(
							'front_page_woocommerce_products' => array( 'product_category' ),
						),
						'std'        => '',
						'type'       => 'text',
					),
					'front_page_woocommerce_products_per_page' => array(
						'title' => esc_html__( 'Per page', 'pubzinne' ),
						'desc'  => wp_kses_data( __( 'How many products will be displayed on the page. Attention! For "Products by IDs" specify comma separated list of the IDs', 'pubzinne' ) ),
						'std'   => 3,
						'type'  => 'text',
					),
					'front_page_woocommerce_products_columns' => array(
						'title' => esc_html__( 'Columns', 'pubzinne' ),
						'desc'  => wp_kses_data( __( 'How many columns will be used', 'pubzinne' ) ),
						'std'   => 3,
						'type'  => 'text',
					),
					'front_page_woocommerce_products_orderby' => array(
						'title'   => esc_html__( 'Order by', 'pubzinne' ),
						'desc'    => wp_kses_data( __( 'Not used with Best selling products', 'pubzinne' ) ),
						'std'     => 'date',
						'options' => array(
							'date'  => esc_html__( 'Date', 'pubzinne' ),
							'title' => esc_html__( 'Title', 'pubzinne' ),
						),
						'type'    => 'radio',
					),
					'front_page_woocommerce_products_order' => array(
						'title'   => esc_html__( 'Order', 'pubzinne' ),
						'desc'    => wp_kses_data( __( 'Not used with Best selling products', 'pubzinne' ) ),
						'std'     => 'desc',
						'options' => array(
							'asc'  => esc_html__( 'Ascending', 'pubzinne' ),
							'desc' => esc_html__( 'Descending', 'pubzinne' ),
						),
						'type'    => 'radio',
					),
					'front_page_woocommerce_color_info'    => array(
						'title' => esc_html__( 'Colors and images', 'pubzinne' ),
						'desc'  => '',
						'type'  => 'info',
					),
					'front_page_woocommerce_scheme'        => array(
						'title'   => esc_html__( 'Color scheme', 'pubzinne' ),
						'desc'    => wp_kses_data( __( 'Color scheme for this section', 'pubzinne' ) ),
						'std'     => 'inherit',
						'options' => array(),
						'refresh' => false,
						'type'    => 'radio',
					),
					'front_page_woocommerce_bg_image'      => array(
						'title'           => esc_html__( 'Background image', 'pubzinne' ),
						'desc'            => wp_kses_data( __( 'Select or upload background image for this section', 'pubzinne' ) ),
						'refresh'         => '.front_page_section_woocommerce',
						'refresh_wrapper' => true,
						'std'             => '',
						'type'            => 'image',
					),
					'front_page_woocommerce_bg_color_type'  => array(
						'title'   => esc_html__( 'Background color', 'pubzinne' ),
						'desc'    => wp_kses_data( __( 'Background color for this section', 'pubzinne' ) ),
						'std'     => PUBZINNE_THEME_FREE ? 'custom' : 'none',
						'refresh' => false,
						'options' => array(
							'none'            => esc_html__( 'None', 'pubzinne' ),
							'scheme_bg_color' => esc_html__( 'Scheme bg color', 'pubzinne' ),
							'custom'          => esc_html__( 'Custom', 'pubzinne' ),
						),
						'type'    => 'radio',
					),
					'front_page_woocommerce_bg_color'       => array(
						'title'      => esc_html__( 'Custom color', 'pubzinne' ),
						'desc'       => wp_kses_data( __( 'Custom background color for this section', 'pubzinne' ) ),
						'std'        => PUBZINNE_THEME_FREE ? '#000' : '',
						'refresh'    => false,
						'dependency' => array(
							'front_page_woocommerce_bg_color_type' => array( 'custom' ),
						),
						'type'       => 'color',
					),
					'front_page_woocommerce_bg_mask'       => array(
						'title'   => esc_html__( 'Background mask', 'pubzinne' ),
						'desc'    => wp_kses_data( __( 'Use Background color as section mask with specified opacity. If 0 - mask is not used', 'pubzinne' ) ),
						'std'     => 1,
						'max'     => 1,
						'step'    => 0.1,
						'refresh' => false,
						'type'    => 'slider',
					),
					'front_page_woocommerce_anchor_info'   => array(
						'title' => esc_html__( 'Anchor', 'pubzinne' ),
						'desc'  => wp_kses_data( __( 'You can select icon and/or specify a text to create anchor for this section and show it in the side menu (if selected in the section "Header - Menu".', 'pubzinne' ) )
									. '<br>'
									. wp_kses_data( __( 'Attention! Anchors available only if plugin "ThemeREX Addons is installed and activated!', 'pubzinne' ) ),
						'type'  => 'info',
					),
					'front_page_woocommerce_anchor_icon'   => array(
						'title' => esc_html__( 'Anchor icon', 'pubzinne' ),
						'desc'  => '',
						'std'   => '',
						'type'  => 'icon',
					),
					'front_page_woocommerce_anchor_text'   => array(
						'title' => esc_html__( 'Anchor text', 'pubzinne' ),
						'desc'  => '',
						'std'   => '',
						'type'  => 'text',
					),
				)
			);
		}
		return $options;
	}
}

// Theme init priorities:
// 9 - register other filters (for installer, etc.)
if ( ! function_exists( 'pubzinne_woocommerce_theme_setup9' ) ) {
	add_action( 'after_setup_theme', 'pubzinne_woocommerce_theme_setup9', 9 );
	function pubzinne_woocommerce_theme_setup9() {
		if ( pubzinne_exists_woocommerce() ) {
			add_action( 'wp_enqueue_scripts', 'pubzinne_woocommerce_frontend_scripts', 1100 );
			add_action( 'wp_enqueue_scripts', 'pubzinne_woocommerce_responsive_styles', 2000 );
			add_filter( 'pubzinne_filter_merge_styles', 'pubzinne_woocommerce_merge_styles' );
			add_filter( 'pubzinne_filter_merge_styles_responsive', 'pubzinne_woocommerce_merge_styles_responsive' );
			add_filter( 'pubzinne_filter_merge_scripts', 'pubzinne_woocommerce_merge_scripts' );
			add_filter( 'pubzinne_filter_get_post_info', 'pubzinne_woocommerce_get_post_info' );
			add_filter( 'pubzinne_filter_post_type_taxonomy', 'pubzinne_woocommerce_post_type_taxonomy', 10, 2 );
			add_action( 'pubzinne_action_override_theme_options', 'pubzinne_woocommerce_override_theme_options' );
			add_filter( 'pubzinne_filter_detect_blog_mode', 'pubzinne_woocommerce_detect_blog_mode' );
			add_filter( 'pubzinne_filter_get_post_categories', 'pubzinne_woocommerce_get_post_categories' );
			add_filter( 'pubzinne_filter_allow_override_header_image', 'pubzinne_woocommerce_allow_override_header_image' );
			add_filter( 'pubzinne_filter_get_blog_title', 'pubzinne_woocommerce_get_blog_title' );
			add_action( 'pubzinne_action_before_post_meta', 'pubzinne_woocommerce_action_before_post_meta' );
			if ( ! is_admin() ) {
				add_action( 'pre_get_posts', 'pubzinne_woocommerce_pre_get_posts' );
				add_filter( 'pubzinne_filter_sidebar_control_text', 'pubzinne_woocommerce_sidebar_control_text' );
				add_filter( 'pubzinne_filter_sidebar_control_title', 'pubzinne_woocommerce_sidebar_control_title' );
			}
		}
		if ( is_admin() ) {
			add_filter( 'pubzinne_filter_tgmpa_required_plugins', 'pubzinne_woocommerce_tgmpa_required_plugins' );
		}

		// Add wrappers and classes to the standard WooCommerce output
		if ( pubzinne_exists_woocommerce() ) {

			// Remove WOOC sidebar
			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

			// Remove link around product item
			remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

			// Add Wishlist button
			if ( get_option('yith_woocompare_compare_button_in_products_list') == 'yes' ) {
				add_action( 'woocommerce_after_shop_loop_item', 'pubzinne_woocommerce_add_wishlist_wrap', 19 );
				add_action( 'woocommerce_after_shop_loop_item', 'pubzinne_woocommerce_add_wishlist_link', 20 );
			}

			// Remove link around product category
			remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
			remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );

			// Detect we are inside subcategory
			add_action( 'woocommerce_before_subcategory', 'pubzinne_woocommerce_before_subcategory', 1 );
			add_action( 'woocommerce_after_subcategory', 'pubzinne_woocommerce_after_subcategory', 9999 );

			// Open main content wrapper - <article>
			remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
			add_action( 'woocommerce_before_main_content', 'pubzinne_woocommerce_wrapper_start', 10 );
			// Close main content wrapper - </article>
			remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
			add_action( 'woocommerce_after_main_content', 'pubzinne_woocommerce_wrapper_end', 10 );

			// Close header section
			add_action( 'woocommerce_after_main_content', 'pubzinne_woocommerce_archive_description', 1 );
			add_action( 'woocommerce_before_shop_loop', 'pubzinne_woocommerce_archive_description', 5 );
			add_action( 'woocommerce_no_products_found', 'pubzinne_woocommerce_archive_description', 5 );

			// Add theme specific search form
			add_filter( 'get_product_search_form', 'pubzinne_woocommerce_get_product_search_form' );

			// Change text on 'Add to cart' button
			add_filter( 'woocommerce_product_add_to_cart_text', 'pubzinne_woocommerce_add_to_cart_text' );
			add_filter( 'woocommerce_product_single_add_to_cart_text', 'pubzinne_woocommerce_add_to_cart_text' );

			// Wrap 'Add to cart' button
			add_filter( 'woocommerce_loop_add_to_cart_link', 'pubzinne_woocommerce_add_to_cart_link', 10, 3 );

			// Add list mode buttons
			add_action( 'woocommerce_before_shop_loop', 'pubzinne_woocommerce_before_shop_loop', 10 );

			// Show 'No products' if no search results and display mode 'both'
			add_action( 'woocommerce_after_shop_loop', 'pubzinne_woocommerce_after_shop_loop', 1 );

			// Set columns number for the products loop
			if ( ! get_theme_support( 'wc-product-grid-enable' ) ) {
				add_filter( 'loop_shop_columns', 'pubzinne_woocommerce_loop_shop_columns' );
				add_filter( 'post_class', 'pubzinne_woocommerce_loop_shop_columns_class' );
				add_filter( 'product_cat_class', 'pubzinne_woocommerce_loop_shop_columns_class', 10, 3 );
			}

			// Set size of the thumbnail in the products loop
			add_filter( 'single_product_archive_thumbnail_size', 'pubzinne_woocommerce_single_product_archive_thumbnail_size' );

			// Open product/category item wrapper
			add_action( 'woocommerce_before_subcategory_title', 'pubzinne_woocommerce_item_wrapper_start', 9 );
			add_action( 'woocommerce_before_shop_loop_item_title', 'pubzinne_woocommerce_item_wrapper_start', 9 );
			// Close featured image wrapper and open title wrapper
			add_action( 'woocommerce_before_subcategory_title', 'pubzinne_woocommerce_title_wrapper_start', 20 );
			add_action( 'woocommerce_before_shop_loop_item_title', 'pubzinne_woocommerce_title_wrapper_start', 20 );

			// Add tags before title
			add_action( 'woocommerce_before_shop_loop_item_title', 'pubzinne_woocommerce_title_tags', 30 );

			// Wrap product title to the link
			add_filter( 'the_title', 'pubzinne_woocommerce_the_title' );
			// Wrap category title to the link (from WooCommerce 3.2.2+ )
			add_action( 'woocommerce_before_subcategory_title', 'pubzinne_woocommerce_before_subcategory_title', 22, 1 );
			add_action( 'woocommerce_after_subcategory_title', 'pubzinne_woocommerce_after_subcategory_title', 2, 1 );

			// Close title wrapper and add description in the list mode
			add_action( 'woocommerce_after_shop_loop_item_title', 'pubzinne_woocommerce_title_wrapper_end', 7 );
			add_action( 'woocommerce_after_subcategory_title', 'pubzinne_woocommerce_title_wrapper_end2', 10 );
			// Close product/category item wrapper
			add_action( 'woocommerce_after_subcategory', 'pubzinne_woocommerce_item_wrapper_end', 20 );
			add_action( 'woocommerce_after_shop_loop_item', 'pubzinne_woocommerce_item_wrapper_end', 20 );

			// Add product ID into product meta section (after categories and tags)
			add_action( 'woocommerce_product_meta_end', 'pubzinne_woocommerce_show_product_id', 10 );

			// Set columns number for the product's thumbnails
			add_filter( 'woocommerce_product_thumbnails_columns', 'pubzinne_woocommerce_product_thumbnails_columns' );

			// Wrap price (WooCommerce use priority 10 to output price)
			add_action( 'woocommerce_after_shop_loop_item_title', 'pubzinne_woocommerce_price_wrapper_start', 9 );
			add_action( 'woocommerce_after_shop_loop_item_title', 'pubzinne_woocommerce_price_wrapper_end', 11 );

			// Decorate price
			add_filter( 'woocommerce_get_price_html', 'pubzinne_woocommerce_get_price_html' );

			// Add 'Out of stock' label
			add_action( 'pubzinne_action_woocommerce_item_featured_link_start', 'pubzinne_woocommerce_add_out_of_stock_label' );

			// Decorate 'Sale' label
			add_filter( 'woocommerce_sale_flash', 'pubzinne_woocommerce_add_sale_percent', 10, 3 );

			// Add custom paginations to the loop
			add_action( 'woocommerce_after_shop_loop', 'pubzinne_woocommerce_pagination', 9 );

			// Add WooCommerce-specific classes
			add_filter( 'body_class', 'pubzinne_woocommerce_add_body_classes' );

			// Detect current shop mode
			if ( ! is_admin() ) {
				$shop_mode = pubzinne_get_value_gp( 'shop_mode' );
				if ( empty( $shop_mode ) ) {
					$shop_mode = pubzinne_get_value_gpc( 'pubzinne_shop_mode' );
				}
				if ( empty( $shop_mode ) && pubzinne_check_theme_option( 'shop_mode' ) ) {
					$shop_mode = pubzinne_get_theme_option( 'shop_mode' );
				}
				if ( empty( $shop_mode ) ) {
					$shop_mode = 'thumbs';
				}
				pubzinne_storage_set( 'shop_mode', $shop_mode );
			}
		}
	}
}

// Theme init priorities:
// Action 'wp'
// 1 - detect override mode. Attention! Only after this step you can use overriden options (separate values for the shop, courses, etc.)
if ( ! function_exists( 'pubzinne_woocommerce_theme_setup_wp' ) ) {
	add_action( 'wp', 'pubzinne_woocommerce_theme_setup_wp' );
	function pubzinne_woocommerce_theme_setup_wp() {
		if ( pubzinne_exists_woocommerce() ) {
			// Set columns number for the related products
			if ( (int) pubzinne_get_theme_option( 'show_related_posts' ) == 0 || (int) pubzinne_get_theme_option( 'related_posts' ) == 0 ) {
				remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			} else {
				add_filter( 'woocommerce_output_related_products_args', 'pubzinne_woocommerce_output_related_products_args' );
				add_filter( 'woocommerce_related_products_columns', 'pubzinne_woocommerce_related_products_columns' );
				add_filter( 'woocommerce_cross_sells_columns', 'pubzinne_woocommerce_related_products_columns' );
				add_filter( 'woocommerce_upsells_columns', 'pubzinne_woocommerce_related_products_columns' );
			}
			// Decorate breadcrumbs
			add_filter( 'woocommerce_breadcrumb_defaults', 'pubzinne_woocommerce_breadcrumb_defaults' );
			if ( is_product() && pubzinne_get_theme_option( 'single_product_layout' ) == 'stretched' ) {
				remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
				add_action( 'woocommerce_single_product_summary', 'woocommerce_breadcrumb', 3 );
			}
		}
	}
}

// Filter to add in the required plugins list
if ( ! function_exists( 'pubzinne_woocommerce_tgmpa_required_plugins' ) ) {
	//Handler of the add_filter('pubzinne_filter_tgmpa_required_plugins',	'pubzinne_woocommerce_tgmpa_required_plugins');
	function pubzinne_woocommerce_tgmpa_required_plugins( $list = array() ) {
		if ( pubzinne_storage_isset( 'required_plugins', 'woocommerce' ) && pubzinne_storage_get_array( 'required_plugins', 'woocommerce', 'install' ) !== false ) {
			$list[] = array(
				'name'     => pubzinne_storage_get_array( 'required_plugins', 'woocommerce', 'title' ),
				'slug'     => 'woocommerce',
				'required' => false,
			);
		}
		return $list;
	}
}


// Check if WooCommerce installed and activated
if ( ! function_exists( 'pubzinne_exists_woocommerce' ) ) {
	function pubzinne_exists_woocommerce() {
		return class_exists( 'Woocommerce' );
	}
}

// Return true, if current page is any woocommerce page
if ( ! function_exists( 'pubzinne_is_woocommerce_page' ) ) {
	function pubzinne_is_woocommerce_page() {
		$rez = false;
		if ( pubzinne_exists_woocommerce() ) {
			$rez = is_woocommerce() || is_shop() || is_product() || is_product_category() || is_product_tag() || is_product_taxonomy() || is_cart() || is_checkout() || is_account_page();
		}
		return $rez;
	}
}

// Detect current blog mode
if ( ! function_exists( 'pubzinne_woocommerce_detect_blog_mode' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_detect_blog_mode', 'pubzinne_woocommerce_detect_blog_mode' );
	function pubzinne_woocommerce_detect_blog_mode( $mode = '' ) {
		if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
			$mode = 'shop';
		} elseif ( is_product() || is_cart() || is_checkout() || is_account_page() ) {
			$mode = 'shop'; // 'shop_single' if using separate parameters for shop and product pages
		}
		return $mode;
	}
}

// Override options with stored page meta on 'Shop' pages
if ( ! function_exists( 'pubzinne_woocommerce_override_theme_options' ) ) {
	//Handler of the add_action( 'pubzinne_action_override_theme_options', 'pubzinne_woocommerce_override_theme_options');
	function pubzinne_woocommerce_override_theme_options() {
		// Remove ' || is_product()' from the condition in the next row
		// if you don't need to override theme options from the page 'Shop' on single products
		if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() || is_product() ) {
			$id = pubzinne_woocommerce_get_shop_page_id();
			if ( 0 < $id ) {
				// Get Theme Options from the shop page
				$shop_meta = get_post_meta( $id, 'pubzinne_options', true );
				if ( ! is_array( $shop_meta ) ) {
					$shop_meta = array();
				}
				// Add (override) with current post (product) options
				if ( pubzinne_storage_isset( 'options_meta' ) ) {
					$options_meta = pubzinne_storage_get( 'options_meta' );
					if ( is_array( $options_meta ) ) {
						$shop_meta = array_merge( $shop_meta, $options_meta );
					}
				}
				pubzinne_storage_set( 'options_meta', $shop_meta );
			}
		}
	}
}

// Add WooCommerce-specific classes to the body
if ( ! function_exists( 'pubzinne_woocommerce_add_body_classes' ) ) {
	//Handler of the add_filter( 'body_class', 'pubzinne_woocommerce_add_body_classes' );
	function pubzinne_woocommerce_add_body_classes( $classes ) {
		if ( is_product() ) {
			$classes[] = 'single_product_layout_' . pubzinne_get_theme_option( 'single_product_layout' );
		}
		return $classes;
	}
}


// Return current page title
if ( ! function_exists( 'pubzinne_woocommerce_get_blog_title' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_get_blog_title', 'pubzinne_woocommerce_get_blog_title');
	function pubzinne_woocommerce_get_blog_title( $title = '' ) {
		if ( ! pubzinne_exists_trx_addons() && pubzinne_exists_woocommerce() && pubzinne_is_woocommerce_page() && is_shop() ) {
			$id    = pubzinne_woocommerce_get_shop_page_id();
			$title = $id ? get_the_title( $id ) : esc_html__( 'Shop', 'pubzinne' );
		}
		return $title;
	}
}


// Return taxonomy for current post type
if ( ! function_exists( 'pubzinne_woocommerce_post_type_taxonomy' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_post_type_taxonomy',	'pubzinne_woocommerce_post_type_taxonomy', 10, 2 );
	function pubzinne_woocommerce_post_type_taxonomy( $tax = '', $post_type = '' ) {
		if ( 'product' == $post_type ) {
			$tax = 'product_cat';
		}
		return $tax;
	}
}

// Return true if page title section is allowed
if ( ! function_exists( 'pubzinne_woocommerce_allow_override_header_image' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_allow_override_header_image', 'pubzinne_woocommerce_allow_override_header_image' );
	function pubzinne_woocommerce_allow_override_header_image( $allow = true ) {
		return is_product() ? false : $allow;
	}
}

// Return shop page ID
if ( ! function_exists( 'pubzinne_woocommerce_get_shop_page_id' ) ) {
	function pubzinne_woocommerce_get_shop_page_id() {
		return get_option( 'woocommerce_shop_page_id' );
	}
}

// Return shop page link
if ( ! function_exists( 'pubzinne_woocommerce_get_shop_page_link' ) ) {
	function pubzinne_woocommerce_get_shop_page_link() {
		$url = '';
		$id  = pubzinne_woocommerce_get_shop_page_id();
		if ( $id ) {
			$url = get_permalink( $id );
		}
		return $url;
	}
}

// Show categories of the current product
if ( ! function_exists( 'pubzinne_woocommerce_get_post_categories' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_get_post_categories', 		'pubzinne_woocommerce_get_post_categories');
	function pubzinne_woocommerce_get_post_categories( $cats = '' ) {
		if ( get_post_type() == 'product' ) {
			$cats = pubzinne_get_post_terms( ', ', get_the_ID(), 'product_cat' );
		}
		return $cats;
	}
}

// Add 'product' to the list of the supported post-types
if ( ! function_exists( 'pubzinne_woocommerce_list_post_types' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_list_posts_types', 'pubzinne_woocommerce_list_post_types');
	function pubzinne_woocommerce_list_post_types( $list = array() ) {
		$list['product'] = esc_html__( 'Products', 'pubzinne' );
		return $list;
	}
}

// Show price of the current product in the widgets and search results
if ( ! function_exists( 'pubzinne_woocommerce_get_post_info' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_get_post_info', 'pubzinne_woocommerce_get_post_info');
	function pubzinne_woocommerce_get_post_info( $post_info = '' ) {
		if ( get_post_type() == 'product' ) {
			global $product;
			$price_html = $product->get_price_html();
			if ( ! empty( $price_html ) ) {
				$post_info = '<div class="post_price product_price price">' . trim( $price_html ) . '</div>' . $post_info;
			}
		}
		return $post_info;
	}
}

// Show price of the current product in the search results streampage
if ( ! function_exists( 'pubzinne_woocommerce_action_before_post_meta' ) ) {
	//Handler of the add_action( 'pubzinne_action_before_post_meta', 'pubzinne_woocommerce_action_before_post_meta');
	function pubzinne_woocommerce_action_before_post_meta() {
		if ( ! is_single() && get_post_type() == 'product' ) {
			global $product;
			$price_html = $product->get_price_html();
			if ( ! empty( $price_html ) ) {
				?><div class="post_price product_price price"><?php pubzinne_show_layout( $price_html ); ?></div>
				<?php
			}
		}
	}
}

// Change text of the sidebar control
if ( ! function_exists( 'pubzinne_woocommerce_sidebar_control_text' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_sidebar_control_text', 'pubzinne_woocommerce_sidebar_control_text' );
	function pubzinne_woocommerce_sidebar_control_text( $text ) {
		if ( ! empty( $text ) && pubzinne_exists_woocommerce() && pubzinne_is_woocommerce_page() ) {
			$text = wp_kses_data( __( 'Filters', 'pubzinne' ) );
		}
		return $text;
	}
}

// Change title of the sidebar control
if ( ! function_exists( 'pubzinne_woocommerce_sidebar_control_title' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_sidebar_control_title', 'pubzinne_woocommerce_sidebar_control_title' );
	function pubzinne_woocommerce_sidebar_control_title( $title ) {
		if ( ! empty( $title ) && pubzinne_exists_woocommerce() && pubzinne_is_woocommerce_page() ) {
			$title = wp_kses_data( __( 'Filters', 'pubzinne' ) );
		}
		return $title;
	}
}

// Enqueue WooCommerce custom styles
if ( ! function_exists( 'pubzinne_woocommerce_frontend_scripts' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'pubzinne_woocommerce_frontend_scripts', 1100 );
	function pubzinne_woocommerce_frontend_scripts() {
		if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
			$pubzinne_url = pubzinne_get_file_url( 'plugins/woocommerce/woocommerce.css' );
			if ( '' != $pubzinne_url ) {
				wp_enqueue_style( 'pubzinne-woocommerce', $pubzinne_url, array(), null );
			}
			$pubzinne_url = pubzinne_get_file_url( 'plugins/woocommerce/woocommerce.js' );
			if ( '' != $pubzinne_url ) {
				wp_enqueue_script( 'pubzinne-woocommerce', $pubzinne_url, array( 'jquery' ), null, true );
			}
		}
	}
}

// Enqueue WooCommerce responsive styles
if ( ! function_exists( 'pubzinne_woocommerce_responsive_styles' ) ) {
	//Handler of the add_action( 'wp_enqueue_scripts', 'pubzinne_woocommerce_responsive_styles', 2000 );
	function pubzinne_woocommerce_responsive_styles() {
		if ( pubzinne_is_on( pubzinne_get_theme_option( 'debug_mode' ) ) ) {
			$pubzinne_url = pubzinne_get_file_url( 'plugins/woocommerce/woocommerce-responsive.css' );
			if ( '' != $pubzinne_url ) {
				wp_enqueue_style( 'pubzinne-woocommerce-responsive', $pubzinne_url, array(), null );
			}
		}
	}
}

// Merge custom styles
if ( ! function_exists( 'pubzinne_woocommerce_merge_styles' ) ) {
	//Handler of the add_filter('pubzinne_filter_merge_styles', 'pubzinne_woocommerce_merge_styles');
	function pubzinne_woocommerce_merge_styles( $list ) {
		$list[] = 'plugins/woocommerce/woocommerce.css';
		return $list;
	}
}


// Merge responsive styles
if ( ! function_exists( 'pubzinne_woocommerce_merge_styles_responsive' ) ) {
	//Handler of the add_filter('pubzinne_filter_merge_styles_responsive', 'pubzinne_woocommerce_merge_styles_responsive');
	function pubzinne_woocommerce_merge_styles_responsive( $list ) {
		$list[] = 'plugins/woocommerce/woocommerce-responsive.css';
		return $list;
	}
}

// Merge custom scripts
if ( ! function_exists( 'pubzinne_woocommerce_merge_scripts' ) ) {
	//Handler of the add_filter('pubzinne_filter_merge_scripts', 'pubzinne_woocommerce_merge_scripts');
	function pubzinne_woocommerce_merge_scripts( $list ) {
		$list[] = 'plugins/woocommerce/woocommerce.js';
		return $list;
	}
}



// Add WooCommerce specific items into lists
//------------------------------------------------------------------------

// Add sidebar
if ( ! function_exists( 'pubzinne_woocommerce_list_sidebars' ) ) {
	//Handler of the add_filter( 'pubzinne_filter_list_sidebars', 'pubzinne_woocommerce_list_sidebars' );
	function pubzinne_woocommerce_list_sidebars( $list = array() ) {
		$list['woocommerce_widgets'] = array(
			'name'        => esc_html__( 'WooCommerce Widgets', 'pubzinne' ),
			'description' => esc_html__( 'Widgets to be shown on the WooCommerce pages', 'pubzinne' ),
		);
		return $list;
	}
}




// Decorate WooCommerce output: Loop
//------------------------------------------------------------------------

// Add query vars to set products per page
if ( ! function_exists( 'pubzinne_woocommerce_pre_get_posts' ) ) {
	//Handler of the add_action( 'pre_get_posts', 'pubzinne_woocommerce_pre_get_posts' );
	function pubzinne_woocommerce_pre_get_posts( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}
		if ( $query->get( 'wc_query' ) == 'product_query' ) {
			$ppp = get_theme_mod( 'posts_per_page_shop', 0 );
			if ( $ppp > 0 ) {
				$query->set( 'posts_per_page', $ppp );
			}
		}
	}
}


// Add custom paginations to the loop
if ( ! function_exists( 'pubzinne_woocommerce_pagination' ) ) {
	//Handler of the add_action( 'woocommerce_after_shop_loop', 'pubzinne_woocommerce_pagination', 9 );
	function pubzinne_woocommerce_pagination() {
		$pagination = pubzinne_get_theme_option('shop_pagination');
		if ( in_array( $pagination, array( 'more', 'infinite' ) ) ) {
			pubzinne_show_pagination(
				array(
					'pagination'   => $pagination,
					'class_prefix' => 'woocommerce'
				)
			);
		}
	}
}


// Before main content
if ( ! function_exists( 'pubzinne_woocommerce_wrapper_start' ) ) {
	//Handler of the add_action('woocommerce_before_main_content', 'pubzinne_woocommerce_wrapper_start', 10);
	function pubzinne_woocommerce_wrapper_start() {
		if ( is_product() || is_cart() || is_checkout() || is_account_page() ) {
			?>
			<article class="post_item_single post_type_product">
			<?php
		} else {
			?>
			<div class="list_products shop_mode_<?php echo esc_attr( ! pubzinne_storage_empty( 'shop_mode' ) ? pubzinne_storage_get( 'shop_mode' ) : 'thumbs' ); ?>">
				<div class="list_products_header">
			<?php
			pubzinne_storage_set( 'woocommerce_list_products_header', true );
		}
	}
}

// After main content
if ( ! function_exists( 'pubzinne_woocommerce_wrapper_end' ) ) {
	//Handler of the add_action('woocommerce_after_main_content', 'pubzinne_woocommerce_wrapper_end', 10);
	function pubzinne_woocommerce_wrapper_end() {
		if ( is_product() || is_cart() || is_checkout() || is_account_page() ) {
			?>
			</article><!-- /.post_item_single -->
			<?php
		} else {
			?>
			</div><!-- /.list_products -->
			<?php
		}
	}
}

// Close header section
if ( ! function_exists( 'pubzinne_woocommerce_archive_description' ) ) {
	//Handler of the add_action('woocommerce_after_main_content', 'pubzinne_woocommerce_archive_description', 1);
	//Handler of the add_action( 'woocommerce_before_shop_loop',	'pubzinne_woocommerce_archive_description', 5 );
	//Handler of the add_action( 'woocommerce_no_products_found',	'pubzinne_woocommerce_archive_description', 5 );
	function pubzinne_woocommerce_archive_description() {
		if ( pubzinne_storage_get( 'woocommerce_list_products_header' ) ) {
			?>
			</div><!-- /.list_products_header -->
			<?php
			pubzinne_storage_set( 'woocommerce_list_products_header', false );
			remove_action( 'woocommerce_after_main_content', 'pubzinne_woocommerce_archive_description', 1 );
		} elseif ( ! is_singular() ) {
			get_template_part( apply_filters( 'pubzinne_filter_get_template_part', 'templates/content', 'none-search' ), 'none-search' );
		}
	}
}

// Add list mode buttons
if ( ! function_exists( 'pubzinne_woocommerce_before_shop_loop' ) ) {
	//Handler of the add_action( 'woocommerce_before_shop_loop', 'pubzinne_woocommerce_before_shop_loop', 10 );
	function pubzinne_woocommerce_before_shop_loop() {
		?>
		<div class="pubzinne_shop_mode_buttons"><form action="<?php echo esc_url( pubzinne_get_current_url() ); ?>" method="post"><input type="hidden" name="pubzinne_shop_mode" value="<?php echo esc_attr( pubzinne_storage_get( 'shop_mode' ) ); ?>" /><a href="#" class="woocommerce_thumbs icon-th" title="<?php esc_attr_e( 'Show products as thumbs', 'pubzinne' ); ?>"></a><a href="#" class="woocommerce_list icon-th-list" title="<?php esc_attr_e( 'Show products as list', 'pubzinne' ); ?>"></a></form></div><!-- /.pubzinne_shop_mode_buttons -->
		<?php
	}
}

// Show 'No products' if no search results and display mode 'both'
if ( ! function_exists( 'pubzinne_woocommerce_after_shop_loop' ) ) {
	//Handler of the add_action( 'woocommerce_after_shop_loop', 'pubzinne_woocommerce_after_shop_loop', 1 );
	function pubzinne_woocommerce_after_shop_loop() {
		if ( ! have_posts() && 'products' != woocommerce_get_loop_display_mode() ) {
			wc_get_template( 'loop/no-products-found.php' );
		}
	}
}

// Number of columns for the shop streampage
if ( ! function_exists( 'pubzinne_woocommerce_loop_shop_columns' ) ) {
	//Handler of the add_filter( 'loop_shop_columns', 'pubzinne_woocommerce_loop_shop_columns' );
	function pubzinne_woocommerce_loop_shop_columns( $cols ) {
		return max( 2, min( 4, pubzinne_get_theme_option( 'blog_columns' ) ) );
	}
}

// Add column class into product item in shop streampage
if ( ! function_exists( 'pubzinne_woocommerce_loop_shop_columns_class' ) ) {
	//Handler of the add_filter( 'post_class', 'pubzinne_woocommerce_loop_shop_columns_class' );
	//Handler of the add_filter( 'product_cat_class', 'pubzinne_woocommerce_loop_shop_columns_class', 10, 3 );
	function pubzinne_woocommerce_loop_shop_columns_class( $classes, $class = '', $cat = '' ) {
		global $woocommerce_loop;
		if ( is_product() ) {
			if ( ! empty( $woocommerce_loop['columns'] ) ) {
				$classes[] = ' column-1_' . esc_attr( $woocommerce_loop['columns'] );
			}
		} elseif ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {
			$classes[] = ' column-1_' . esc_attr( max( 2, min( 4, pubzinne_get_theme_option( 'blog_columns' ) ) ) );
		}
		return $classes;
	}
}

// Set size of the thumbnail in the products loop
if ( ! function_exists( 'pubzinne_woocommerce_single_product_archive_thumbnail_size' ) ) {
	//Handler of the add_filter( 'single_product_archive_thumbnail_size', 'pubzinne_woocommerce_single_product_archive_thumbnail_size' );
	function pubzinne_woocommerce_single_product_archive_thumbnail_size( $size = 'woocommerce_thumbnail' ) {
		static $subst = array();
		if ( isset( $subst[ $size ] ) ) {
			$size = $subst[ $size ];
		} else {
			$subst[ $size ] = $size;
			$cols           = max( 1, wc_get_loop_prop( 'columns' ) );
			$cols_gap       = 30;
			$page_width     = apply_filters( 'pubzinne_filter_content_width', pubzinne_get_theme_option( 'page_width' ) );
			$sb_width       = 0;
			if ( pubzinne_sidebar_present() ) {
				$pubzinne_sidebar_type = pubzinne_get_theme_option( 'sidebar_type' );
				if ( 'custom' == $pubzinne_sidebar_type && ! pubzinne_is_layouts_available() ) {
					$pubzinne_sidebar_type = 'default';
				}
				if ( 'default' == $pubzinne_sidebar_type ) {
					$pubzinne_sidebar_name = pubzinne_get_theme_option( 'sidebar_widgets' );
					if ( is_active_sidebar( $pubzinne_sidebar_name ) ) {
						$sb_width = pubzinne_get_theme_option( 'sidebar_width' );
					}
				}
			}
			$w = round( ( $page_width - ( $sb_width > 0 ? $sb_width + $cols_gap : 0 ) - ( $cols - 1 ) * $cols_gap ) / $cols );
			$new_size = pubzinne_get_closest_thumb_size( $size, array( 'width' => $w, 'height' => $w ) );
			if ( ! empty( $new_size ) ) {
				$subst[ $size ] = $new_size;
				$size           = $new_size;
			}
		}
		return $size;
	}
}

// Detect when we are in a subcategory: start category
if ( ! function_exists( 'pubzinne_woocommerce_before_subcategory' ) ) {
	//Handler of the add_action( 'woocommerce_before_subcategory', 'pubzinne_woocommerce_before_subcategory', 1 );
	function pubzinne_woocommerce_before_subcategory( $cat = '' ) {
		pubzinne_storage_set( 'in_product_category', $cat );
	}
}

// Detect when we are in a subcategory: end category
if ( ! function_exists( 'pubzinne_woocommerce_after_subcategory' ) ) {
	//Handler of the add_action( 'woocommerce_after_subcategory', 'pubzinne_woocommerce_after_subcategory', 9999 );
	function pubzinne_woocommerce_after_subcategory( $cat = '' ) {
		pubzinne_storage_set( 'in_product_category', false );
	}
}
	

// Open item wrapper for categories and products
if ( ! function_exists( 'pubzinne_woocommerce_item_wrapper_start' ) ) {
	//Handler of the add_action( 'woocommerce_before_subcategory_title', 'pubzinne_woocommerce_item_wrapper_start', 9 );
	//Handler of the add_action( 'woocommerce_before_shop_loop_item_title', 'pubzinne_woocommerce_item_wrapper_start', 9 );
	function pubzinne_woocommerce_item_wrapper_start( $cat = '' ) {
		pubzinne_storage_set( 'in_product_item', true );
		$hover = pubzinne_get_theme_option( 'shop_hover' );
		?>
		<div class="post_item post_layout_<?php
			echo esc_attr( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy()
				? pubzinne_storage_get( 'shop_mode' )
				: 'thumbs'
			);
			?>"
			<?php pubzinne_add_blog_animation(); ?>
		>
			<div class="post_featured hover_<?php echo esc_attr( $hover ); ?>">
				<?php do_action( 'pubzinne_action_woocommerce_item_featured_start' ); ?>
				<a href="<?php echo esc_url( is_object( $cat ) ? get_term_link( $cat->slug, 'product_cat' ) : get_permalink() ); ?>">
				<?php
				do_action( 'pubzinne_action_woocommerce_item_featured_link_start' );
	}
}

// Open item wrapper for categories and products
if ( ! function_exists( 'pubzinne_woocommerce_open_item_wrapper' ) ) {
	//Handler of the add_action( 'woocommerce_before_subcategory_title', 'pubzinne_woocommerce_title_wrapper_start', 20 );
	//Handler of the add_action( 'woocommerce_before_shop_loop_item_title', 'pubzinne_woocommerce_title_wrapper_start', 20 );
	function pubzinne_woocommerce_title_wrapper_start( $cat = '' ) {
				do_action( 'pubzinne_action_woocommerce_item_featured_link_end' );
				?>
				</a>
				<?php
				$hover = pubzinne_get_theme_option( 'shop_hover' );
				if ( 'none' != $hover ) {
					?>
					<div class="mask"></div>
					<?php
					pubzinne_hovers_add_icons( $hover, array( 'cat' => $cat ) );
				}
				do_action( 'pubzinne_action_woocommerce_item_featured_end' );
				?>
			</div><!-- /.post_featured -->
			<div class="post_data">
				<div class="post_data_inner">
					<div class="post_header entry-header">
					<?php
					do_action( 'pubzinne_action_woocommerce_item_header_start' );
	}
}


// Display product's tags before the title
if ( ! function_exists( 'pubzinne_woocommerce_title_tags' ) ) {
	//Handler of the add_action( 'woocommerce_before_shop_loop_item_title', 'pubzinne_woocommerce_title_tags', 30 );
	function pubzinne_woocommerce_title_tags() {
		global $product;
		pubzinne_show_layout( wc_get_product_tag_list( $product->get_id(), ', ', '<div class="post_tags product_tags">', '</div>' ) );
	}
}

// Wrap product title to the link
if ( ! function_exists( 'pubzinne_woocommerce_the_title' ) ) {
	//Handler of the add_filter( 'the_title', 'pubzinne_woocommerce_the_title' );
	function pubzinne_woocommerce_the_title( $title ) {
		if ( pubzinne_storage_get( 'in_product_item' ) && get_post_type() == 'product' ) {
			$title = '<a href="' . esc_url( get_permalink() ) . '">' . esc_html( $title ) . '</a>';
		}
		return $title;
	}
}

// Wrap category title to the link: open tag
if ( ! function_exists( 'pubzinne_woocommerce_before_subcategory_title' ) ) {
	//Handler of the add_action( 'woocommerce_before_subcategory_title', 'pubzinne_woocommerce_before_subcategory_title', 10, 1 );
	function pubzinne_woocommerce_before_subcategory_title( $cat ) {
		if ( pubzinne_storage_get( 'in_product_item' ) && is_object( $cat ) ) {
			?>
			<a href="<?php echo esc_url( get_term_link( $cat->slug, 'product_cat' ) ); ?>">
			<?php
		}
	}
}

// Wrap category title to the link: close tag
if ( ! function_exists( 'pubzinne_woocommerce_after_subcategory_title' ) ) {
	//Handler of the add_action( 'woocommerce_after_subcategory_title', 'pubzinne_woocommerce_after_subcategory_title', 10, 1 );
	function pubzinne_woocommerce_after_subcategory_title( $cat ) {
		if ( pubzinne_storage_get( 'in_product_item' ) && is_object( $cat ) ) {
			?>
			</a>
			<?php
		}
	}
}

// Add excerpt in output for the product in the list mode
if ( ! function_exists( 'pubzinne_woocommerce_title_wrapper_end' ) ) {
	//Handler of the add_action( 'woocommerce_after_shop_loop_item_title', 'pubzinne_woocommerce_title_wrapper_end', 7);
	function pubzinne_woocommerce_title_wrapper_end() {
			do_action( 'pubzinne_action_woocommerce_item_header_end' );
			?>
			</div><!-- /.post_header -->
		<?php
		if ( pubzinne_storage_get( 'shop_mode' ) == 'list' && ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) && ! is_product() ) {
			?>
			<div class="post_content entry-content"><?php pubzinne_show_layout( get_the_excerpt() ); ?></div>
			<?php
		}
	}
}

// Add excerpt in output for the product in the list mode
if ( ! function_exists( 'pubzinne_woocommerce_title_wrapper_end2' ) ) {
	//Handler of the add_action( 'woocommerce_after_subcategory_title', 'pubzinne_woocommerce_title_wrapper_end2', 10 );
	function pubzinne_woocommerce_title_wrapper_end2( $category ) {
			do_action( 'pubzinne_action_woocommerce_item_header_end' );
			?>
			</div><!-- /.post_header -->
		<?php
		if ( pubzinne_storage_get( 'shop_mode' ) == 'list' && is_shop() && ! is_product() ) {
			?>
			<div class="post_content entry-content"><?php pubzinne_show_layout( $category->description ); ?></div><!-- /.post_content -->
			<?php
		}
	}
}

// Close item wrapper for categories and products
if ( ! function_exists( 'pubzinne_woocommerce_close_item_wrapper' ) ) {
	//Handler of the add_action( 'woocommerce_after_subcategory', 'pubzinne_woocommerce_item_wrapper_end', 20 );
	//Handler of the add_action( 'woocommerce_after_shop_loop_item', 'pubzinne_woocommerce_item_wrapper_end', 20 );
	function pubzinne_woocommerce_item_wrapper_end( $cat = '' ) {
				?>
				</div><!-- /.post_data_inner -->
			</div><!-- /.post_data -->
		</div><!-- /.post_item -->
		<?php
		pubzinne_storage_set( 'in_product_item', false );
	}
}

// Change text on 'Add to cart' button to 'Buy now'
if ( ! function_exists( 'pubzinne_woocommerce_add_to_cart_text' ) ) {
	//Handler of the add_filter( 'woocommerce_product_add_to_cart_text',       'pubzinne_woocommerce_add_to_cart_text' );
	//Handler of the add_filter( 'woocommerce_product_single_add_to_cart_text','pubzinne_woocommerce_add_to_cart_text' );
	function pubzinne_woocommerce_add_to_cart_text( $text = '' ) {
		global $product;
		return is_object( $product ) && $product->is_in_stock()
				&& 'grouped' !== $product->get_type()
				&& ( 'external' !== $product->get_type() || $product->get_button_text() == '' )
					? esc_html__( 'Buy now', 'pubzinne' )
					: $text;
	}
}

// Wrap 'Add to cart' button
if ( ! function_exists( 'pubzinne_woocommerce_add_to_cart_link' ) ) {
	//Handler of the add_filter( 'woocommerce_loop_add_to_cart_link', 'pubzinne_woocommerce_add_to_cart_link', 10, 2 );
	function pubzinne_woocommerce_add_to_cart_link( $html, $product = false, $args = array() ) {
		return pubzinne_is_off( pubzinne_get_theme_option( 'shop_hover' ) ) ? sprintf( '<div class="add_to_cart_wrap">%s</div>', $html ) : $html;
	}
}


// Add wrap for buttons 'Compare' and 'Add to wishlist'
if ( ! function_exists( 'pubzinne_woocommerce_add_wishlist_wrap' ) ) {
	//Handler of the add_action( 'woocommerce_after_shop_loop_item', 'pubzinne_woocommerce_add_wishlist_wrap' ), 19 );
	function pubzinne_woocommerce_add_wishlist_wrap() {
		if ( function_exists( 'YITH_WCWL' ) ) {
			?><div class="yith_buttons_wrap"><?php
		}
	}
}

// Add button 'Add to wishlist'
if ( ! function_exists( 'pubzinne_woocommerce_add_wishlist_link' ) ) {
	//Handler of the add_action( 'woocommerce_after_shop_loop_item', 'pubzinne_woocommerce_add_wishlist_link' ), 20 );
	function pubzinne_woocommerce_add_wishlist_link() {
		if ( function_exists( 'YITH_WCWL' ) ) {
			$enabled_on_loop = 'yes' == get_option( 'yith_wcwl_show_on_loop' );
			$position = get_option( 'yith_wcwl_loop_position' );
			if ( $enabled_on_loop && 'before_image' == $position ) {
				pubzinne_show_layout( do_shortcode( '[yith_wcwl_add_to_wishlist]' ) );
			}
			?></div><?php
		}
	}
}


// Add label 'out of stock'
if ( ! function_exists( 'pubzinne_woocommerce_add_out_of_stock_label' ) ) {
	//Handler of the add_action( 'pubzinne_action_woocommerce_item_featured_link_start', 'pubzinne_woocommerce_add_out_of_stock_label' );
	function pubzinne_woocommerce_add_out_of_stock_label() {
		global $product;
		$cat = pubzinne_storage_get( 'in_product_category' );
		if ( empty($cat) || ! is_object($cat) ) {
			if ( is_object( $product ) && ! $product->is_in_stock() ) {
				?>
				<span class="outofstock_label"><?php esc_html_e( 'Out of stock', 'pubzinne' ); ?></span>
				<?php
			}
		}
	}
}


// Add label with sale percent instead standard 'Sale'
if ( ! function_exists( 'pubzinne_woocommerce_add_sale_percent' ) ) {
	//Handler of the add_filter( 'woocommerce_sale_flash', 'pubzinne_woocommerce_add_sale_percent', 10, 3 );
	function pubzinne_woocommerce_add_sale_percent( $label, $post = '', $product = '' ) {
		$percent = '';
		if ( is_object( $product ) ) {
			if ( 'variable' === $product->get_type() ) {
				$prices  = $product->get_variation_prices();
				if ( ! is_array( $prices['regular_price'] ) && ! is_array( $prices['sale_price'] ) && $prices['regular_price'] > $prices['sale_price'] ) {
					$percent = round( ( $prices['regular_price'] - $prices['sale_price'] ) / $prices['regular_price'] * 100 );
				}
			} else {
				$price = $product->get_price_html();
				$prices = explode( '<ins', $price );
				if ( count( $prices ) > 1 ) {
					$prices[1] = '<ins' . $prices[1];
					$price_old = pubzinne_parse_num( $prices[0] );
					$price_new = pubzinne_parse_num( $prices[1] );
					if ( $price_old > 0 && $price_old > $price_new ) {
						$percent = round( ( $price_old - $price_new ) / $price_old * 100 );
					}
				}
			}
		}
		return ! empty( $percent ) ? '<span class="onsale">-' . esc_html( $percent ) . '%</span>' : $label;
	}
}


// Wrap price - start (WooCommerce use priority 10 to output price)
if ( ! function_exists( 'pubzinne_woocommerce_price_wrapper_start' ) ) {
	//Handler of the add_action( 'woocommerce_after_shop_loop_item_title',	'pubzinne_woocommerce_price_wrapper_start', 9);
	function pubzinne_woocommerce_price_wrapper_start() {
		if ( pubzinne_storage_get( 'shop_mode' ) == 'thumbs' && ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) && ! is_product() ) {
			global $product;
			$price_html = $product->get_price_html();
			if ( '' != $price_html ) {
				?>
				<div class="price_wrap">
				<?php
			}
		}
	}
}


// Wrap price - start (WooCommerce use priority 10 to output price)
if ( ! function_exists( 'pubzinne_woocommerce_price_wrapper_end' ) ) {
	//Handler of the add_action( 'woocommerce_after_shop_loop_item_title',	'pubzinne_woocommerce_price_wrapper_end', 11);
	function pubzinne_woocommerce_price_wrapper_end() {
		if ( pubzinne_storage_get( 'shop_mode' ) == 'thumbs' && ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) && ! is_product() ) {
			global $product;
			$price_html = $product->get_price_html();
			if ( '' != $price_html ) {
				?>
				</div><!-- /.price_wrap -->
				<?php
			}
		}
	}
}


// Decorate price
if ( ! function_exists( 'pubzinne_woocommerce_get_price_html' ) ) {
	//Handler of the add_filter(    'woocommerce_get_price_html',	'pubzinne_woocommerce_get_price_html' );
	function pubzinne_woocommerce_get_price_html( $price = '' ) {
		if ( ! is_admin() && ! empty( $price ) ) {
			$sep = get_option( 'woocommerce_price_decimal_sep' );
			if ( empty( $sep ) ) {
				$sep = '.';
			}
			$price = preg_replace( '/([0-9,]+)(\\' . trim( $sep ) . ')([0-9]{2})/', '\\1<span class="decimals_separator">\\2</span><span class="decimals">\\3</span>', $price );
		}
		return $price;
	}
}


// Decorate breadcrumbs
if ( ! function_exists( 'pubzinne_woocommerce_breadcrumb_defaults' ) ) {
	//Handler of the add_filter( 'woocommerce_breadcrumb_defaults', 'pubzinne_woocommerce_breadcrumb_defaults' );
	function pubzinne_woocommerce_breadcrumb_defaults( $args ) {
		$args['delimiter'] = '<span class="woocommerce-breadcrumb-delimiter"></span>';
		$args['before']    = '<span class="woocommerce-breadcrumb-item">';
		$args['after']     = '</span>';
		return $args;
	}
}

		



// Decorate WooCommerce output: Single product
//------------------------------------------------------------------------

// Add Product ID for the single product
if ( ! function_exists( 'pubzinne_woocommerce_show_product_id' ) ) {
	//Handler of the add_action( 'woocommerce_product_meta_end', 'pubzinne_woocommerce_show_product_id', 10);
	function pubzinne_woocommerce_show_product_id() {
		$authors = wp_get_post_terms( get_the_ID(), 'pa_product_author' );
		if ( is_array( $authors ) && count( $authors ) > 0 ) {
			echo '<span class="product_author">' . esc_html__( 'Author: ', 'pubzinne' );
			$delim = '';
			foreach ( $authors as $author ) {
				echo  esc_html( $delim ) . '<span>' . esc_html( $author->name ) . '</span>';
				$delim = ', ';
			}
			echo '</span>';
		}
		echo '<span class="product_id">' . esc_html__( 'Product ID: ', 'pubzinne' ) . '<span>' . get_the_ID() . '</span></span>';
	}
}

// Number columns for the product's thumbnails
if ( ! function_exists( 'pubzinne_woocommerce_product_thumbnails_columns' ) ) {
	//Handler of the add_filter( 'woocommerce_product_thumbnails_columns', 'pubzinne_woocommerce_product_thumbnails_columns' );
	function pubzinne_woocommerce_product_thumbnails_columns( $cols ) {
		return 4;
	}
}

// Set products number for the related products
if ( ! function_exists( 'pubzinne_woocommerce_output_related_products_args' ) ) {
	//Handler of the add_filter( 'woocommerce_output_related_products_args', 'pubzinne_woocommerce_output_related_products_args' );
	function pubzinne_woocommerce_output_related_products_args( $args ) {
		$args['posts_per_page'] = (int) pubzinne_get_theme_option( 'show_related_posts' )
										? max( 0, min( 9, pubzinne_get_theme_option( 'related_posts' ) ) )
										: 0;
		$args['columns']        = max( 1, min( 4, pubzinne_get_theme_option( 'related_columns' ) ) );
		return $args;
	}
}

// Set columns number for the related products
if ( ! function_exists( 'pubzinne_woocommerce_related_products_columns' ) ) {
	//Handler of the add_filter( 'woocommerce_related_products_columns', 'pubzinne_woocommerce_related_products_columns' );
	//Handler of the add_filter( 'woocommerce_cross_sells_columns', 'pubzinne_woocommerce_related_products_columns' );
	//Handler of the add_filter( 'woocommerce_upsells_columns', 'pubzinne_woocommerce_related_products_columns' );
	function pubzinne_woocommerce_related_products_columns( $columns = 0 ) {
		$columns = max( 1, min( 4, pubzinne_get_theme_option( 'related_columns' ) ) );
		return $columns;
	}
}



// Decorate WooCommerce output: Widgets
//------------------------------------------------------------------------

// Search form
if ( ! function_exists( 'pubzinne_woocommerce_get_product_search_form' ) ) {
	//Handler of the add_filter( 'get_product_search_form', 'pubzinne_woocommerce_get_product_search_form' );
	function pubzinne_woocommerce_get_product_search_form( $form ) {
		return '
		<form role="search" method="get" class="search_form" action="' . esc_url( home_url( '/' ) ) . '">
			<input type="text" class="search_field" placeholder="' . esc_attr__( 'Search for products &hellip;', 'pubzinne' ) . '" value="' . get_search_query() . '" name="s" /><button class="search_button" type="submit">' . esc_html__( 'Search', 'pubzinne' ) . '</button>
			<input type="hidden" name="post_type" value="product" />
		</form>
		';
	}
}


// Add plugin-specific colors and fonts to the custom CSS
if ( pubzinne_exists_woocommerce() ) {
	require_once pubzinne_get_file_dir( 'plugins/woocommerce/woocommerce-style.php' );
}
