<?php
/**
 * Plugin support: WooCommerce
 *
 * Check if theme need additional layouts for products - register shortcode 'trx_sc_extended_products' with attribute 'style'
 * 
 * @package ThemeREX Addons
 * @since v1.85.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	exit;
}

if ( ! function_exists( 'trx_addons_woocommerce_extended_products_get_layouts' ) ) {
	/**
	 * Get a filtered list of layouts for the shortcode "Extended products"
	 * 
	 * @trigger trx_addons_filter_woocommerce_products_layouts
	 *
	 * @return array  list of layouts
	 */
	function trx_addons_woocommerce_extended_products_get_layouts() {
		static $list = false;
		if ( $list === false ) {
			$list = apply_filters( 'trx_addons_filter_woocommerce_products_layouts', array(
				'default' => array(
					'title' => __( 'Default', 'trx_addons' ),
					'template' => ''
				)
			) );
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_extended_products_get_list_styles' ) ) {
	/**
	 * Get a list of styles for the shortcode "Extended products" based on the layouts list
	 *
	 * @return array  List of styles
	 */
	function trx_addons_woocommerce_extended_products_get_list_styles() {
		$layouts = trx_addons_woocommerce_extended_products_get_layouts();
		$list = array();
		if ( is_array( $layouts ) ) {
			foreach ( $layouts as $k => $v ) {
				$list[$k] = $v['title'];
			}
		}
		return $list;
	}
}

if ( ! function_exists( 'trx_addons_woocommerce_extended_products_get_list_types' ) ) {
	/**
	 * Get a list of types for the shortcode "Extended products"
	 * 
	 * @trigger trx_addons_filter_extended_products_types
	 *
	 * @return array  List of types
	 */
	function trx_addons_woocommerce_extended_products_get_list_types() {
		return apply_filters( 'trx_addons_filter_extended_products_types', array(
			'products'     => __( 'Products', 'trx_addons' ),
			'on_sale'      => __( 'On sale', 'trx_addons' ),
			'best_selling' => __( 'Best selling', 'trx_addons' ),
			'top_rated'    => __( 'Top rated', 'trx_addons' ),
		) );
	}
}


// Register shortcode if theme need additional layouts
//-------------------------------------------------------------
if ( ! function_exists( 'trx_addons_sc_extended_products' ) ) {
	/**
	 * Shortcode [trx_sc_extended_products]
	 * 
	 * @trigger trx_addons_sc_output
	 * @trigger trx_addons_filter_woocommerce_products_slides_min_width
	 * 
	 * @param array $atts  Shortcode attributes
	 * @param string $content  Shortcode content
	 * 
	 * @return string  Shortcode output
	 */
	function trx_addons_sc_extended_products( $atts, $content = null ) {	

		// Exit to prevent recursion
		if ( trx_addons_sc_stack_check( 'trx_sc_extended_products' ) ) {
			return '';
		}

		$atts = trx_addons_sc_prepare_atts( 'trx_sc_extended_products', $atts, trx_addons_sc_common_atts( 'id,title,slider', array(
			// Individual params
			"type" => "default",
			"style" => "products",
			"per_page" => 3,
			"columns" => 3,
			"category" => "",
			"ids" => "",
			"orderby" => "date",
			"order" => "desc"
		) ) );

		if ( is_array( $atts['category'] ) ) {
			$atts['category'] = implode( ',', $atts['category'] );
		}
		if ( is_array( $atts['ids'] ) ) {
			$atts['ids'] = implode( ',', $atts['ids'] );
		}
		if ( ! empty( $atts['style'] ) ) {
			$atts[ $atts['style'] ] = '1';
		}

		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) {
			$atts['slider_pagination'] = 'bottom';
		}

		$atts['slides_min_width'] = apply_filters( 'trx_addons_filter_woocommerce_products_slides_min_width', 225 );

		$layouts = trx_addons_woocommerce_extended_products_get_layouts();

		// Store atts for the filter
		global $TRX_ADDONS_STORAGE;
		$TRX_ADDONS_STORAGE['sc_extended_products_args'] = $atts;
		$TRX_ADDONS_STORAGE['sc_extended_products_template_args'] = ! empty( $layouts[ $atts[ 'type' ] ] ) ? $layouts[ $atts[ 'type' ] ] : array();

		// Add filters
		add_filter( 'wc_get_template_part', 'trx_addons_sc_extended_products_wc_get_template_part', 1000, 3 );
		add_filter( 'woocommerce_product_loop_start', 'trx_addons_sc_extended_products_add_product_style_to_products_wrap', 1000 );
		add_filter( 'woocommerce_post_class', 'trx_addons_sc_extended_products_add_product_style_to_product_items', 1000, 2 );
		add_filter( 'woocommerce_product_loop_start', 'trx_addons_sc_extended_products_woocommerce_product_loop_start', 1000 );
		add_filter( 'woocommerce_product_loop_end', 'trx_addons_sc_extended_products_woocommerce_product_loop_end', 1000 );

		// Show template
		ob_start();
		trx_addons_get_template_part( array(
											TRX_ADDONS_PLUGIN_API . 'woocommerce/tpl.extended-products.'.trx_addons_esc($atts['type']).'.php',
											TRX_ADDONS_PLUGIN_API . 'woocommerce/tpl.extended-products.default.php'
										),
										'trx_addons_args_sc_extended_products',
										$atts
									);
		$output = ob_get_contents();
		ob_end_clean();

		// Convert each li to slide if slider is present
		if ( $atts['slider'] > 0 ) {
			$output = preg_replace(
								array(
									// Case 1: Make each li with product as slide
									'/(<li[^>]*class=")/',
									// Case 2: Wrap each li with product to the <div class="slider-slide">
									//'/(<li[^>]*class=")/',
									//'/(<\/li>)/'
								),
								array(
									// Case 1: Make each li with product as slide
									'$1slider-slide swiper-slide ',
									// Case 2: Wrap each li with product to the <div class="slider-slide">
									//'<div class="slider-slide swiper-slide">$1',
									//'$1</div>'
								),
								$output
								);
		}

		// Remove filter
		remove_filter( 'wc_get_template_part', 'trx_addons_sc_extended_products_wc_get_template_part', 1000 );
		remove_filter( 'woocommerce_product_loop_start', 'trx_addons_sc_extended_products_add_product_style_to_products_wrap', 1000 );
		remove_filter( 'woocommerce_post_class', 'trx_addons_sc_extended_products_add_product_style_to_product_items', 1000 );
		remove_filter( 'woocommerce_product_loop_start', 'trx_addons_sc_extended_products_woocommerce_product_loop_start', 1000 );
		remove_filter( 'woocommerce_product_loop_end', 'trx_addons_sc_extended_products_woocommerce_product_loop_end', 1000 );

		// Remove atts
		unset( $TRX_ADDONS_STORAGE['sc_extended_products_args'] );
		unset( $TRX_ADDONS_STORAGE['sc_extended_products_template_args'] );

		return apply_filters( 'trx_addons_sc_output', $output, 'trx_sc_extended_products', $atts, $content );
	}
}

if ( ! function_exists( 'trx_addons_sc_extended_products_add_shortcode' ) ) {
	add_action( 'init', 'trx_addons_sc_extended_products_add_shortcode', 20 );
	/**
	 * Add shortcode [trx_sc_extended_products]
	 * 
	 * @hooked init, 20
	 */
	function trx_addons_sc_extended_products_add_shortcode() {
		$layouts = trx_addons_woocommerce_extended_products_get_layouts();
		if ( count( $layouts ) > 0 && trx_addons_exists_woocommerce() ) {
			add_shortcode( "trx_sc_extended_products", "trx_addons_sc_extended_products" );
		}
	}
}

if ( ! function_exists( 'trx_addons_sc_extended_products_remove_extra_atts' ) ) {
	add_filter( 'trx_addons_filter_sc_extended_products_args_to_woocommerce', 'trx_addons_sc_extended_products_remove_extra_atts' );
	/**
	 * Remove '_extra' attributes from the shortcode attributes
	 *
	 * @param array $atts  Shortcode attributes
	 * 
	 * @return array     Shortcode attributes without '_extra' attributes
	 */
	function trx_addons_sc_extended_products_remove_extra_atts( $atts ) {
		if ( is_array( $atts ) ) {
			foreach( $atts as $k => $v ) {
				if ( substr( $k, -6 ) == '_extra' || is_array( $v ) || is_object( $v ) ) {
					unset( $atts[ $k ] );
				}
			}
		}
		return $atts;
	}
}

if ( ! function_exists( 'trx_addons_sc_extended_products_wc_get_template_part' ) ) {
	/**
	 * Change template for the product item in the extended products list
	 *
	 * @param string $template  Template path
	 * @param string $slug      Template slug
	 * @param string $name      Template name
	 * 
	 * @return string           Template path
	 */
	function trx_addons_sc_extended_products_wc_get_template_part( $template, $slug, $name ) {
		global $TRX_ADDONS_STORAGE;
		if ( $slug == 'content' && $name == 'product' && ! empty( $TRX_ADDONS_STORAGE['sc_extended_products_template_args']['template'] ) ) {
			$template = $TRX_ADDONS_STORAGE['sc_extended_products_template_args']['template'];
		}
		return $template;
	}
}

if ( ! function_exists( 'trx_addons_sc_extended_products_add_product_style_to_products_wrap' ) ) {
	/**
	 * Add a class with a product style to the wrap ul.products
	 *
	 * @param string $template  Template content
	 * 
	 * @return string           Template content
	 */
	function trx_addons_sc_extended_products_add_product_style_to_products_wrap( $template ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['sc_extended_products_args']['type'] ) ) {
			$new_classes = array(
				sprintf( 'products_style_%s', $TRX_ADDONS_STORAGE['sc_extended_products_args']['type'] )
			);
			if ( ! empty( $TRX_ADDONS_STORAGE['sc_extended_products_template_args']['products_classes'] ) ) {
				$new_classes = array_merge(
									$new_classes, 
									is_array( $TRX_ADDONS_STORAGE['sc_extended_products_template_args']['products_classes'] )
										? $TRX_ADDONS_STORAGE['sc_extended_products_template_args']['products_classes']
										: explode( ' ', $TRX_ADDONS_STORAGE['sc_extended_products_template_args']['products_classes'] )
									);
			}
			$template = preg_replace( 
									'/(<ul[^>]*class="products )/',
									'$1' . esc_attr( join( ' ', $new_classes ) ) . ' ',
									$template
									);
		}
		return $template;
	}
}

if ( ! function_exists( 'trx_addons_sc_extended_products_add_product_style_to_product_items' ) ) {
	/**
	 * Add a class with a product style to the product item
	 *
	 * @param array $classes   Product classes
	 * @param object $product  Product object
	 * 
	 * @return array           Product classes
	 */
	function trx_addons_sc_extended_products_add_product_style_to_product_items( $classes, $product ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['sc_extended_products_args']['type'] ) && is_array( $classes ) ) {
			$new_classes = array(
				sprintf( 'product_style_%s', esc_attr( $TRX_ADDONS_STORAGE['sc_extended_products_args']['type'] ) )
			);
			if ( ! empty( $TRX_ADDONS_STORAGE['sc_extended_products_template_args']['product_classes'] ) ) {
				$new_classes = array_merge(
									$new_classes, 
									is_array( $TRX_ADDONS_STORAGE['sc_extended_products_template_args']['product_classes'] )
										? $TRX_ADDONS_STORAGE['sc_extended_products_template_args']['product_classes']
										: explode( ' ', $TRX_ADDONS_STORAGE['sc_extended_products_template_args']['product_classes'] )
									);
			}
			foreach( $new_classes as $c ) {
				$c = trim( $c );
				if ( ! empty( $c ) && ! in_array( $c, $classes ) ) {
					$classes[] = $c;
				}
			}
		}
		return $classes;
	}
}

if ( ! function_exists( 'trx_addons_sc_extended_products_woocommerce_product_loop_start' ) ) {
	/**
	 * Substitute a products loop start with the slider wrapper start
	 *
	 * @param string $template  Template content
	 * 
	 * @return string           Template content
	 */
	function trx_addons_sc_extended_products_woocommerce_product_loop_start( $template ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['sc_extended_products_args']['slider'] )
			&& (int) $TRX_ADDONS_STORAGE['sc_extended_products_args']['slider'] > 0
			&& strpos( $template, 'slider_container' ) === false
		) {
			$wrap = trx_addons_get_template_part_as_string(
							'templates/tpl.sc_slider_start.php',
							'trx_addons_args_sc_show_slider_wrap',
							apply_filters( 'trx_addons_filter_sc_show_slider_args', array(
																						'sc' => 'sc_extended_products',
																						'args' => $TRX_ADDONS_STORAGE['sc_extended_products_args']
																						) )
						);
			if ( preg_match( '/<ul class="([^"]*)"/', $template, $matches) && ! empty( $matches[1] ) ) {
				$wrap = str_replace('<div class="slides', '<ul class="slides ' . $matches[1], $wrap);
			}
			$template = $wrap;
		}
		return $template;
	}
}

if ( ! function_exists( 'trx_addons_sc_extended_products_woocommerce_product_loop_end' ) ) {
	/**
	 * Substitute a products loop end with the slider wrapper end
	 *
	 * @param string $template  Template content
	 * 
	 * @return string           Template content
	 */
	function trx_addons_sc_extended_products_woocommerce_product_loop_end( $template ) {
		global $TRX_ADDONS_STORAGE;
		if ( ! empty( $TRX_ADDONS_STORAGE['sc_extended_products_args']['slider'] ) && (int) $TRX_ADDONS_STORAGE['sc_extended_products_args']['slider'] > 0 ) {
			$template = '</ul>'
						. trx_addons_get_template_part_as_string(
							'templates/tpl.sc_slider_end.php',
							'trx_addons_args_sc_show_slider_wrap',
							apply_filters( 'trx_addons_filter_sc_show_slider_args', array(
																						'sc' => 'sc_extended_products',
																						'args' => $TRX_ADDONS_STORAGE['sc_extended_products_args']
																						) )
						);
		}
		return $template;
	}
}



// Add shortcodes
//----------------------------------------------------------------------------

// Add shortcodes to Elementor
if ( trx_addons_exists_elementor() && function_exists('trx_addons_elm_init') && trx_addons_exists_woocommerce() ) {
	require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . 'woocommerce/woocommerce-extended-products-sc-elementor.php';
}
